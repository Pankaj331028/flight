<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model {
	protected $table = 'user_address';

	protected $fillable = ['status'];

	public function acountry() {
		return $this->belongsTo(Country::class, "country_id", 'id');
	}
	public function astate() {
		return $this->belongsTo(State::class, "state_id", 'id');
	}
	public function acity() {
		return $this->belongsTo(City::class, "city_id", 'id');
	}
}
