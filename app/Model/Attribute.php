<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $table = 'attributes';

   protected $with = ['options'];

/* public function brand() {
return $this->hasMany(CollectionVariation::class, "attribute_id")->where('status', '!=', 'DL');
}*/
    public function options()
    {
        return $this->hasMany(AttributeValue::class, "attribute_id");
    }
    public function products()
    {
        return $this->belongsToMany('Product', 'product_attribute_options');
    }
    public function fetchAttribute($request, $columns)
    {
        $query = Attribute::where('status', '!=','DL');
        if (isset($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->orWhere('name', 'like', '%' . $request->search . '%');
                $q->orWhere('type', 'like', '%' . $request->search . '%');
            });
        }

        if (isset($request->order_column)) {
            $Attribute = $query->orderBy($columns[$request->order_column], $request->order_dir);
        } else {
            $Attribute = $query->orderBy('id', 'desc');
        }
        return $Attribute;
    }
}
