<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AttributesSet extends Model {
	protected $table = 'attribute_sets';

/*	protected $with = ['brand'];

	public function brand() {
		return $this->hasMany(CollectionVariation::class, "attribute_id")->where('status', '!=', 'DL');
	}*/

	public function fetchAttributesSet($request, $columns) {
		$query = AttributesSet::where('status', 'AC');
		if (isset($request->search)) {
			$query->where(function ($q) use ($request) {
				$q->orWhere('name', 'like', '%' . $request->search . '%');
			});
		}

		if (isset($request->order_column)) {
			$AttributesSet = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$AttributesSet = $query->orderBy('id', 'desc');
		}
		return $AttributesSet;
	}
}
