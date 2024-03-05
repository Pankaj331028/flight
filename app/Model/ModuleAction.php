<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ModuleAction extends Model {
	protected $table = 'g_module_actions';
	protected $with = ['action'];

	public function module_permission() {
		return $this->belongsToMany('App\Model\Role', 'g_permissions');
	}

	public function action() {
		return $this->belongsTo('App\Model\Action');
	}

	public function module() {
		return $this->belongsTo('App\Model\Module');
	}

}
