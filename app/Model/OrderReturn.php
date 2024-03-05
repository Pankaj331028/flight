<?php

namespace App\Model;
use DB;
use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model {
	protected $table = 'order_returns';

	public function order_item() {
		return $this->belongsTo(OrderItem::class);
	}

	public function order() {
		return $this->belongsTo(Order::class);
	}

	public function order_schedule() {
		return $this->belongsTo(OrderDelivery::class);
	}

	public function fetchReturns($request, $columns) {

		$query = OrderReturn::with(['order_item', 'order', 'order_schedule'])->where('status', '!=', 'DL');

		$query = $query->select('*', DB::raw("CASE WHEN type = 'order' THEN GROUP_CONCAT(DISTINCT order_item_id) ELSE order_item_id END as products"));

		if (isset($request->from_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
		}
		if (isset($request->end_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
		}
		if (isset($request->search)) {
			$query->where(function ($q) use ($request) {
				$q->orWhereHas('order', function ($qu) use ($request) {
					$qu->where('order_code', 'like', '%' . $request->search . '%');
					$qu->orWhere('order_no', 'like', '%' . $request->search . '%');
				});
			});
		}
		if (isset($request->status)) {
			$query->where('status', $request->status);
		}
		if (isset($request->return_type)) {
			$query->where('type', $request->return_type);
		}

		$query = $query->groupBy(DB::raw("CASE WHEN type = 'order' THEN order_id ELSE order_schedule_id END"));

		if (isset($request->order_column)) {
			$plans = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$plans = $query->orderBy('created_at', 'desc');
		}

		return $plans;
	}

}
