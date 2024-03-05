<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {
	protected $table = 't_transactions';

	public function user() {
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function order() {
		return $this->belongsTo(Order::class, 'order_id', 'id');
	}

	public function fetchPayments($request, $columns) {
		$query = Transaction::with(['user', 'order'])->where('status', '!=', 'DL');

		if (isset($request->from_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
		}
		if (isset($request->end_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
		}
		if (isset($request->search)) {
			$query->whereHas('user', function ($q) use ($request) {
				$q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'like', '%' . $request->search . '%');
			});
			$query->orWhereHas('order', function ($q) use ($request) {
				$q->orWhere('order_no', 'like', '%' . $request->search . '%');
				$q->orWhere('order_code', 'like', '%' . $request->search . '%');
			});
		}
		if (isset($request->status)) {
			$query->where('status', $request->status);
		}
		if (isset($request->order_column)) {
			$payments = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$payments = $query->orderBy('created_at', 'desc');
		}

		return $payments;
	}

}
