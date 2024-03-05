<?php

namespace App\Model;
use Auth;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
	protected $table = 'orders';

	public function order_items() {
		return $this->hasMany(OrderItem::class);
	}

	public function order_schedule() {
		return $this->hasMany(OrderDelivery::class);
	}

	public function order_return() {
		return $this->hasMany(OrderReturn::class);
	}

	public function user_address() {
		return $this->belongsTo(UserAddress::class, "address_id", "id");
	}

	public function user() {
		return $this->belongsTo(User::class, "user_id", "id");
	}

	public function order_drivers() {
		return $this->hasMany(OrderDriver::class);
	}

	public function fetchOrders($request, $columns) {
		$query = Order::with(['order_items', 'user_address'])->where('status', '!=', 'DL')->whereHas('order_items');

		if (Auth::guard('admin')->user()->user_role[0]->role == 'user') {
			$query->whereUserId(Auth::guard('admin')->id());
		}

		if (isset($request->from_date)) {
			$query->where(function ($que) use ($request) {
				$que->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
				$que->orWhereHas('order_items', function ($q) use ($request) {
					$q->whereRaw('DATE_FORMAT(start_date, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
					$q->orWhereRaw('DATE_FORMAT(end_date, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
				});
			});
		}
		if (isset($request->end_date)) {
			$query->where(function ($que) use ($request) {
				$que->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
				$que->orWhereHas('order_items', function ($q) use ($request) {
					$q->whereRaw('DATE_FORMAT(start_date, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
					$q->orWhereRaw('DATE_FORMAT(end_date, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
				});
			});
		}
		if (isset($request->search)) {
			$query->where(function ($q) use ($request) {
				$q->orWhere('order_code', 'like', '%' . $request->search . '%');
				$q->orWhere('order_no', 'like', '%' . $request->search . '%');
				$q->orWhereHas('user',function($qu)use($request){
					$qu->whereRaw('CONCAT(first_name," ",last_name) like ?',['%'.$request->search.'%']);
				});
			});
		}
		if (isset($request->status)) {
			$query->where(function ($que) use ($request) {
				$que->where('status', $request->status);
				$que->orWhereHas('order_items', function ($q) use ($request) {
					$q->where('status', $request->status);
					$q->orWhereHas('order_schedule', function ($qu) use ($request) {
						$qu->where('status', $request->status);
					});
				});
			});
		}
		if (isset($request->order_column)) {
			$plans = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$plans = $query->orderBy('created_at', 'desc');
		}
		return $plans;
	}
}
