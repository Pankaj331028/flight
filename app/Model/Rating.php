<?php

namespace App\Model;

use DB;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model {
	protected $table = 'c_app_ratings';

	public function user() {
		return $this->belongsTo(User::class, 'user_id');
	}

	public function fetchRatings($request, $columns) {
		$query = Rating::with('user')->where('status', '!=', 'DL');

		if (isset($request->from_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
		}
		if (isset($request->end_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
		}
		if (isset($request->search)) {
			$query->whereHas('user', function ($q) use ($request) {
				$q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'like', '%' . $request->search . '%');
			});
		}
		if (isset($request->status)) {
			$query->where('status', $request->status);
		}
		if (isset($request->order_column)) {
			$ratings = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$ratings = $query->orderBy('created_at', 'desc');
		}

		return $ratings;
	}
}
