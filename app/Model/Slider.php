<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model {
	protected $table = 'c_sliders';

	public function category() {
		return $this->belongsTo(Category::class);
	}

	public function fetchSliders($request, $columns) {
		$query = Slider::where('status', '!=', 'DL');

		if (isset($request->from_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
		}
		if (isset($request->end_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
		}
		if (isset($request->status)) {
			$query->where('status', $request->status);
		}
		if (isset($request->category_filter)) {
			$query->where('category_id', $request->category_filter);
		}
		if (isset($request->order_column)) {
			$plans = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$plans = $query->orderBy('created_at', 'desc');
		}
		return $plans;
	}

	public function scopeActive($query) {
        return $query->where('status', '=', 'AC');
	}
	
	public function scopeType($query,$type) {
        return $query->where('type', '=', $type);
    }
}
