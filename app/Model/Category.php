<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {
	protected $table = 'c_categories';
	protected $with = ['childCatList'];

	public function parentCat() { 
		return $this->belongsTo(Category::class, "parent_id", "id");
	}

	public function childCatList() {
		return $this->hasMany(Category::class, "parent_id", "id");
	}

	public function childCat() {
		return $this->hasMany(Category::class, "parent_id", "id")->where('status', 'AC');
	}

	public function products() {
		return $this->hasMany(Product::class, "parent_id", 'id')->where('status', 'AC');
	}

	public function fetchCategories($id = null)
	{
        $query = Category::with(['childCat' => function ($q) {
            $q->where('status', '!=', 'DL');
        }])->where('status', '!=', 'DL')->whereNull('parent_id');

        if ($id != null) {
            $query->where('id', '!=', $id);
        }

        $categories = $query->orderBy('created_at', 'desc');

        return $categories;
    }

	public function scopeActive($query) 
	{
		return $query->where('status','AC');
	}
}
