<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CMS extends Model {
	protected $table = 'c_cms';

	public function child() {
		return $this->hasMany(CMS::class, "parent_id", "id");
	}

	public function fetchCMS($request, $columns) {
		$query = CMS::whereNull('parent_id');
		if (isset($request->search)) {
			$query->where(function ($q) use ($request) {
				$q->orWhere('name', 'like', '%' . $request->search . '%');
			});
		}

		if (isset($request->order_column)) {
			$cms = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$cms = $query->orderBy('name', 'asc');
		}
		return $cms;
	}
}
