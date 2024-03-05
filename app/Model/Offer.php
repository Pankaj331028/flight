<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Offer extends Model {
	protected $table = 'c_offers';

	public function offer_orderitems() {
		return $this->hasMany(OrderItem::class, 'offer_id', 'id');
	}

	public function offer_cartitems() {
		return $this->hasMany(CartItem::class, 'offer_id', 'id');
	}

	public function fetchOffers(Request $request, $columns) {
		$query = Offer::where('status', '!=', 'DL');

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
				$q->where('offer_code', 'like', '%' . $request->search . '%');
				$q->orWhere('value', 'like', '%' . $request->search . '%');
				$q->orWhere('start_date', 'like', '%' . $request->search . '%');
				$q->orWhere('end_date', 'like', '%' . $request->search . '%');
				$q->orWhereRaw('DATE_FORMAT(start_date, "%M") like ?', ["%" . $request->search . "%"]);
				$q->orWhereRaw('DATE_FORMAT(end_date, "%M") like ?', ["%" . $request->search . "%"]);
			});
		}
		if (isset($request->status)) {
			$query->where('status', $request->status);
		}
		if (isset($request->is_top_offer)) {
			$query->where('is_top_offer', $request->is_top_offer);
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
