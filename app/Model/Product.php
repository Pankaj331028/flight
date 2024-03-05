<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $table = 'products';
    protected $with = ['variations'];

    public function variations()
    {
        return $this->hasMany(ProductVariation::class, "product_id")->where('status', '!=', 'DL');
    }

    public function variationsAttribute()
    {
        return $this->hasMany(ProductAttributeVariation::class, "product_id")->where('status', '!=', 'DL');
    }

    public function prod_attr()
    {
        return $this->belongsToMany(AttributeValue::class, "product_attribute_variations")->where('attribute_values.status', 'AC');
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class, "product_id");
    }

    public function cart_items()
    {
        return $this->hasMany(CartItem::class, "product_id");
    }

    public function favourites()
    {
        return $this->hasMany(ProductFavourite::class, "product_id");
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'parent_id')->where('c_categories.parent_id', null);
    }

    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'parent_id')->where('c_categories.parent_id', '!=', null);
    }

    public function active_favs()
    {
        return $this->hasMany(ProductFavourite::class, "product_id")->where('status', 'AC');
    }

    public function inactive_favs()
    {
        return $this->hasMany(ProductFavourite::class, "product_id")->where('status', 'IN');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }

    public function get_products_name($id)
    {
        return Product::where(['id' => $id, 'status' => 'AC'])->pluck('name');
    }

    public function fetchProducts($request, $columns)
    {
        $query = Product::with(['category', 'subcategory'])->whereHas('prod_attr')->where('status', '!=', 'DL');

        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        if (isset($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
                $q->orWhere('product_code', 'like', '%' . $request->search . '%');
                $q->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }
        if (isset($request->manage_stock)) {
            $query->where('manage_stock', $request->manage_stock);
        }
        if (isset($request->quick_grab)) {
            $query->where('quick_grab', $request->quick_grab);
        }
        if (isset($request->is_exclusive)) {
            $query->where('is_exclusive', $request->is_exclusive);
        }
        if (isset($request->categoryFilter)) {
            $query->where(function ($qu) use ($request) {
                $qu->whereHas('category', function ($q) use ($request) {
                    $q->where('id', $request->categoryFilter);
                })->orWhereHas('subcategory', function ($q) use ($request) {
                    $q->where('id', $request->categoryFilter);
                });
            });
        }
        if (isset($request->order_column)) {
            $plans = $query->orderBy($columns[$request->order_column], $request->order_dir);
        } else {
            $plans = $query->orderBy('created_at', 'desc');
        }
        return $plans;
    }

}
