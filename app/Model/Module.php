<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Module extends Model {
	protected $table = 'g_modules';

	public function action() {
		return $this->belongsToMany('App\Model\Action', 'g_module_actions');
	}

	public function module_action() {
		return $this->hasMany('App\Model\ModuleAction')->orderBy('action_id');
	}

}
