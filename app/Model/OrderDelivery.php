<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderDelivery extends Model {
	protected $table = 'order_schedules';

	public function order() {
		return $this->belongsTo(Order::class);
	}

	public function order_item() {
		return $this->belongsTo(OrderItem::class);
	}
}
