<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model {
	protected $table = 'order_items';

	public function product_items() {
		return $this->hasOne(Product::class, 'id', 'product_id');
	}

	public function product_variation() {
		return $this->hasOne(ProductVariation::class, 'id', 'product_variation_id');
	}

	public function delivery_slot() {
		return $this->hasOne(DeliverySlot::class, 'id', 'delivery_slot_id');
	}

	public function coupon_range() {
		return $this->hasOne(CouponRange::class, 'id', 'coupon_code_id');
	}

	public function offers() {
		return $this->hasOne(Offer::class, 'id');
	}

	public function order() {
		return $this->belongsTo(Order::class);
	}
	public function order_schedule() {
		return $this->hasMany(OrderDelivery::class);
	}

}
