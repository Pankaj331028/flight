<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model {

	const CREATED_AT = 'Created_date';
	const UPDATED_AT = 'Updated_Date';

	protected $casts = ['Created_date', 'Updated_Date'];

	public $fillable = ['flightName', 'Country', 'Destinations', 'URL'];
}
