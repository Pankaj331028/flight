<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model {
	protected $table = 'g_permissions';
	protected $with = ['module_action'];

	public function role() {
		return $this->belongsTo('App\Model\Role');
	}
	public function module_action() {
		return $this->belongsTo('App\Model\ModuleAction');
	}

}
