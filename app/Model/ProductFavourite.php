<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductFavourite extends Model {
	protected $table = 'product_favourites';

	public function product() {
		return $this->belongsTo(Product::class, "product_id", "id");
	}

	public function product_variation() {
		return $this->belongsTo(ProductVariation::class, "product_variation_id", "id");
	}

	public function user() {
		return $this->belongsTo(User::class, "user_id", "id");
	}

	public function fetchUnits($request, $columns) {
		$query = ProductUnit::where('status', '!=', 'DL');

		if (isset($request->from_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
		}
		if (isset($request->end_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
		}
		if (isset($request->search)) {
			$query->where(function ($q) use ($request) {
				$q->where('unit', 'like', '%' . $request->search . '%');
				$q->orWhere('fullname', 'like', '%' . $request->search . '%');
			});
		}
		if (isset($request->status)) {
			$query->where('status', $request->status);
		}
		if (isset($request->order_column)) {
			$plans = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$plans = $query->orderBy('created_at', 'desc');
		}
		return $plans;
	}
}
