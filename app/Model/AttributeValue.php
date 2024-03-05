<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    protected $table = 'attribute_values';
    protected $fillable = ['id', 'value', 'status'];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id')->where('status', '!=', 'DL');
    }
}
