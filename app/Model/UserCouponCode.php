<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserCouponCode extends Model {
	protected $table = "user_coupon_codes";

	public function order() {
		return $this->belongsTo("App\Model\Order", "order_id");
	}

	public function coupon_code() {
		return $this->belongsTo("App\Model\CouponRange", "coupon_code_id");
	}

	public function user() {
		return $this->belongsTo("App\Model\User", "user_id");
	}
}
