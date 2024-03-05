<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CouponCode extends Model {
	protected $table = 'c_coupon_codes';

	public function coupon_range() {
		return $this->hasMany(CouponRange::class, 'coupon_code_id', 'id')->where('status', '!=', 'DL');
	}

	public function fetchCouponCodes(Request $request, $columns) {
		$query = CouponCode::where('status', '!=', 'DL');

		if (isset($request->from_date)) {
			$query->where(function ($q) use ($request) {
				$q->whereRaw('DATE_FORMAT(start_date, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '" or DATE_FORMAT(end_date, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
			});
		}
		if (isset($request->end_date)) {
			$query->where(function ($q) use ($request) {
				$q->whereRaw('DATE_FORMAT(end_date, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '" or "' . date("Y-m-d", strtotime($request->end_date)) . '" between DATE_FORMAT(start_date, "%Y-%m-%d") and DATE_FORMAT(end_date, "%Y-%m-%d")');
			});
		}
		if (isset($request->search)) {
			$query->where(function ($q) use ($request) {
				$q->where('code', 'like', '%' . $request->search . '%');
				$q->orWhereHas('coupon_range', function ($qu) use ($request) {
					$qu->where('value', 'like', '%' . $request->search . '%');
					$qu->orWhere('min_price', 'like', '%' . $request->search . '%');
					$qu->orWhere('max_price', 'like', '%' . $request->search . '%');
				});
			});
		}
		if (isset($request->status)) {
			$query->where('status', $request->status);
		}
		if (isset($request->categoryFilter)) {
			$query->whereRaw("FIND_IN_SET(" . $request->categoryFilter . ",category_id)");
		}
		if (isset($request->order_column)) {
			$offers = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$offers = $query->orderBy('created_at', 'desc');
		}
		return $offers;
	}
}
