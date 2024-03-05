<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Action extends Model {
	protected $table = 'g_actions';

	public function module_action() {
		return $this->belongsToMany('App\Model\Module', 'g_module_actions');
	}

}
