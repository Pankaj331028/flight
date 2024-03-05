<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Country extends Model {
	protected $table = 'g_countries';
	public $timestamps = false;
	// protected $with = ['state'];

	public function state() {
		return $this->hasMany(State::class);
	}

	public static function getStates($country) {
		$states = State::whereHas('country', function ($query) use ($country) {
			$query->where('g_countries.name', $country);
		})->where('status', '=', 'AC')->pluck('name');
		return $states;
	}

	public function fetchCountries($request, $columns) {
		$query = Country::with('unit')->where('status', '=', 'AC');

		if (isset($request->search)) {
			$query->where(function ($q) use ($request) {
				$q->orWhere('name', 'like', '%' . $request->search . '%');
			});
		}

		if (isset($request->unit)) {
			$query->where('distance_unit', $request->unit);
		}

		if (isset($request->order_column)) {
			$units = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$units = $query->orderBy('name', 'asc');
		}
		return $units;
	}
}
