<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class State extends Model {
	protected $table = 'g_states';
	protected $with = ['country'];

	public function city() {
		return $this->hasMany(City::class);
	}

	public function country() {
		return $this->belongsTo(Country::class, "country_id");
	}

	public static function getCities($state) {
		$cities = City::whereHas('state', function ($query) use ($state) {
			$query->where('g_states.name', $state);
		})->where('status', '=', 'AC')->pluck('name');
		return $cities;
	}

}
