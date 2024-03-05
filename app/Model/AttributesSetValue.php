<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AttributesSetValue extends Model {
	protected $table = 'attribute_attribute_set';
	//protected $fillable = ['product_id', 'unit_id', 'weight'];

	/*public function collection_brand() {
		return $this->belongsTo(Brand::class, 'brand_id');
	}*/
}
