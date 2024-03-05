<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
	protected $table = "settings";
	protected $fillable = ['contact_email', 'contact_address', 'contact_no'];
}
