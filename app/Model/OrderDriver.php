<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderDriver extends Model {
	protected $table = 'order_drivers';

	public function order() {
		return $this->belongsTo(Order::class);
	}

	public function driver() {
		return $this->belongsTo(User::class);
	}
}
