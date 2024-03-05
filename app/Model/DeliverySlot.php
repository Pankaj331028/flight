<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DeliverySlot extends Model {
	protected $table = 'c_delivery_slots';

	public function product_orders() {
		return $this->hasMany(OrderItem::class);
	}

	public function product_carts() {
		return $this->hasMany(CartItem::class);
	}

	public function fetchSlots($request, $columns) {
		$query = DeliverySlot::where('status', '!=', 'DL');

		if (isset($request->from_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
		}
		if (isset($request->end_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
		}
		if (isset($request->search)) {
			$query->where(function ($q) use ($request) {
				$q->where('from_time', 'like', '%' . $request->search . '%');
				$q->orWhere('to_time', 'like', '%' . $request->search . '%');
			});
		}
		if (isset($request->status)) {
			$query->where('status', $request->status);
		}
		if (isset($request->slot_type)) {
			$query->where('type', $request->slot_type);
		}
		if (isset($request->slot_day)) {
			$query->where('day', $request->slot_day);
		}
		if (isset($request->order_column)) {
			$slots = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$slots = $query->orderBy('day', 'asc')->orderBy('from_time', 'asc');
		}
		return $slots;
	}
}
