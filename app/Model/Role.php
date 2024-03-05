<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {
	protected $table = 'g_roles';

	public function role() {
		return $this->belongsToMany('App\Model\User', 'g_user_roles');
	}

	public function permission() {
		return $this->hasMany('App\Model\Permission');
	}

	public function assignModule($module, $operations, $id) {

		$permit = Permission::where('role_id', $id)->update(['status' => 'IN']);
		// dd($module);
		//allow list permission to each module selected
		foreach ($module as $value) {
			$action = Action::where('name', 'list')->first()->id;
			$action_edit = Action::where('name', 'edit')->first()->id;

			$module_action = ModuleAction::where('module_id', $value)->where('action_id', $action)->first();

			if (!$module_action) {
				$module_action = ModuleAction::where('module_id', $value)->where('action_id', $action_edit)->first();
			}

			if (isset($module_action->id)) {
				$permission = new Permission;
				$permission->role_id = $id;
				$permission->module_action_id = $module_action->id;
				$permission->save();
			}

		}

		if ($operations != null && count($operations) > 0) {
			foreach ($operations as $value) {
				$permission = new Permission;
				$permission->role_id = $id;
				$permission->module_action_id = $value;
				$permission->save();
			}
		}

	}

	public function fetchRoles($request, $columns) {
		$query = Role::with(['permission', 'role'])->whereNotIn('role', ['admin', 'driver', 'user'])->where('status', '!=', 'DL');

		if (isset($request->from_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
		}
		if (isset($request->end_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
		}
		if (isset($request->search)) {
			$query->where(function ($q) use ($request) {
				$q->where('name', 'like', '%' . $request->search . '%');
				$q->orWhere('role', 'like', '%' . $request->search . '%');
			});
		}
		if (isset($request->status)) {
			$query->where('status', $request->status);
		}

		if (isset($request->order_column)) {
			$roles = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$roles = $query->orderBy('created_at', 'desc');
		}
		return $roles;
	}
}
