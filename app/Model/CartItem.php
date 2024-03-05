<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model {
	protected $table = 'c_cart_items';

	public function product_items() {
		return $this->hasOne(Product::class, 'id', 'product_id');
	}

	public function product_variation() {
		return $this->hasOne(ProductVariation::class, 'id', 'product_variation_id');
	}

	public function delivery_slot() {
		return $this->hasOne(DeliverySlot::class, 'id', 'delivery_slot_id');
	}

	public function offers() {
		return $this->hasOne(Offer::class, 'id');
	}

	public function cart() {
		return $this->belongsTo(Cart::class);
	}

	public function coupon_code() {
		return $this->belongsTo(CouponRange::class);
	}
}
