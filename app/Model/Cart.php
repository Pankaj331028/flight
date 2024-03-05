<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model {
	protected $table = 'c_carts';

	protected $fillable = ['address_id'];

	public function cart_items() {
		return $this->hasMany(CartItem::class, "cart_id", "id");
	}

	public function user_address() {
		return $this->belongsTo(UserAddress::class, "address_id", "id");
	}

	public function coupon_range() {
		return $this->belongsToMany(CouponRange::class, "c_cart_items", "cart_id", "coupon_code_id");
	}
}
