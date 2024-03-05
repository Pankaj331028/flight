<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CouponRange extends Model {
	protected $table = 'c_coupon_range';

	public function coupon_code() {
		return $this->belongsTo(CouponCode::class);
	}

	public function code_orderitems() {
		return $this->hasMany(OrderItem::class, 'coupon_code_id', 'id');
	}

	public function code_cartitems() {
		return $this->hasMany(CartItem::class, 'coupon_code_id', 'id');
	}

	public static function saveCouponRange($count, $req, $id) {
		for ($i = 1; $i <= $count; $i++) {
			$exists = $req["coderangeid_" . $i];
			$range = CouponRange::find($exists);
			if (isset($range->id)) {
				$discount = "codedisc_" . $i;
				$minprice = "codemin_" . $i;
				$maxprice = "codemax_" . $i;
				if (isset($req[$discount])) {

					$range->value = $req[$discount];
					$range->min_price = $req[$minprice];
					$range->max_price = $req[$maxprice];
				}
				$range->status = trim($req["codestatus_" . $i]);
				$range->save();
			} else {
				$range = new CouponRange;
				// $type = "codetype_" . $i;
				$discount = "codedisc_" . $i;
				$minprice = "codemin_" . $i;
				$maxprice = "codemax_" . $i;
				// $range->type = $req[$type];
				$range->coupon_code_id = $id;
				$range->value = $req[$discount];
				$range->min_price = $req[$minprice];
				$range->max_price = $req[$maxprice];
				$range->status = trim($req["codestatus_" . $i]);
				$range->save();
			}
		}
	}

	public function fetchRanges($request, $columns) {
		$query = CouponRange::where('coupon_code_id', $request->code_id)->where('status', '!=', 'DL');

		if (isset($request->search)) {
			$query->where(function ($q) use ($request) {
				$q->orWhere('min_price', 'like', '%' . $request->search . '%');
				$q->orWhere('max_price', 'like', '%' . $request->search . '%');
				$q->orWhere('value', 'like', '%' . $request->search . '%');
			});
		}

		if (isset($request->order_column)) {
			$variations = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$variations = $query->orderBy('created_at', 'desc');
		}
		return $variations;
	}

}
