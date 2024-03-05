<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ShippingCharge extends Model {
	protected $table = "c_shipping_charges";

	public function fetchShippingCharges($request, $columns) {
		$query = ShippingCharge::where('status', '!=', 'DL');

		if (isset($request->from_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
		}
		if (isset($request->end_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
		}
		if (isset($request->search)) {
			$query->where(function ($q) use ($request) {
				$q->where('min_price', 'like', '%' . $request->search . '%');
				$q->orWhere('shipping_code', 'like', '%' . $request->search . '%');
				$q->orWhere('max_price', 'like', '%' . $request->search . '%');
				$q->orWhere('shipping_charge', 'like', '%' . $request->search . '%');
			});
		}
		if (isset($request->order_column)) {
			$offers = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$offers = $query->orderBy('created_at', 'desc');
		}
		return $offers;
	}
}
