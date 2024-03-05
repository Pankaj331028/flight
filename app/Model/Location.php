<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Location extends Model {
	protected $table = 'c_areas';

	public function city() {
		return $this->belongsTo(City::class);
	}

	public function fetchLocations($city, $state, $request, $columns) {
		$query = Location::where('status', '!=', 'DL');

		if ($city != 'all') {
			$query->whereHas('city', function ($q) use ($city) {
				$q->where('g_cities.name', $city);
			});
		} elseif ($state != 'all') {
			$query->whereHas('city', function ($q) use ($state) {
				$q->whereHas('state', function ($qu) use ($state) {
					$qu->where('g_states.name', $state);
				});
			});
		}

		if (isset($request->from_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
		}
		if (isset($request->end_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
		}
		if (isset($request->search)) {
			$query->where(function ($q) use ($request) {
				$q->where('area', 'like', '%' . $request->search . '%');
				$q->orWhere('area_code', 'like', '%' . $request->search . '%');
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
