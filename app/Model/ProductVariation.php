<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $table = 'product_variations';
    protected $fillable = ['product_id', 'unit_id', 'weight'];
    protected $appends = ['specifications'];

    public function options()
    {
        return $this->hasMany(ProductAttributeVariation::class, "variation_id");
    }

    public function product_units()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function product_orders()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function product_carts()
    {
        return $this->hasMany(CartItem::class);
    }

    public function get_all_variations()
    {
        return ProductVariation::where('status', '!=', 'DL')->get();
    }

    public function variationOption()
    {
        return $this->hasMany(ProductAttributeVariation::class, 'variation_id');
    }

    public static function saveVariations($count, $req, $id)
    {

        for ($i = 1; $i <= $count; $i++) {
            $exists = $req["prodvarid_" . $i];
            $variant = ProductVariation::find($exists);
            if (isset($variant->id)) {
                $qty = "prodqty_" . $i;
                $maxqty = "prodmaxqty_" . $i;
                $mrp = "prodmrp_" . $i;
                $special = "prodspecial_" . $i;
                $variant->qty = $req[$qty] ?? '';
                $variant->max_qty = $req[$maxqty];
                $variant->price = $req[$mrp];
                $variant->special_price = isset($req[$special]) ? $req[$special] : null;
                $variant->status = trim($req["prodstatus_" . $i]);

                if ($variant->save()) {

                    $selected_all_attribute = Attribute::whereIn('id', $req["attribute_id"] ?? [])->select('slug', 'id')->get();
                    if (count($selected_all_attribute) > 0) {
                        foreach ($selected_all_attribute as $k => $v) {
                            $attribute_option = "prod" . $v->slug . "_" . $i;
                            $attr_exist = ProductAttributeVariation::where('variation_id', $variant->id)->where('product_id', $id)->where('attribute_id', $v->id)->first();

                            if (isset($req[$attribute_option])) {
                                //update if found
                                if ($attr_exist) {
                                    $product_attribute_variations = $attr_exist;
                                } else {
                                    $product_attribute_variations = new ProductAttributeVariation;
                                    $product_attribute_variations->product_id = $id;
                                    $product_attribute_variations->variation_id = $variant->id;

                                }
                                $product_attribute_variations->attribute_id = $v->id;
                                $product_attribute_variations->attribute_value_id = isset($req[$attribute_option]) ? $req[$attribute_option] : 0;
                                $product_attribute_variations->status = 'AC';
                                // dd($product_attribute_variations);
                                $product_attribute_variations->save();
                            }
                        }
                    }
                }
            } else {
                $variant = new ProductVariation;
                $variant->product_id = $id;
                // $weight = "prodweight_" . $i;
                // $unit = "produnit_" . $i;
                $qty = "prodqty_" . $i;
                $maxqty = "prodmaxqty_" . $i;
                $mrp = "prodmrp_" . $i;
                $special = "prodspecial_" . $i;
                // $variant->weight = $req[$weight];
                // $variant->unit_id = $req[$unit];
                $variant->qty = $req[$qty];
                $variant->max_qty = $req[$maxqty];
                $variant->price = $req[$mrp];
                $variant->special_price = isset($req[$special]) ? $req[$special] : null;
                $variant->status = trim($req["prodstatus_" . $i]);

                if ($variant->save()) {
                    $selected_all_attribute = Attribute::whereIn('id', $req["attribute_id"])->select('slug', 'id')->get();
                    if (count($selected_all_attribute) > 0) {
                        foreach ($selected_all_attribute as $k => $v) {
                            $attribute_option = "prod" . $v->slug . "_" . $i;
                            // $attr_exist = ProductAttributeVariation::where('variation_id', $variant->id)->where('product_id', $id)->where('attribute_id',$v->id)->first();

                            // if (isset($req[$attribute_option])) {
                            //update if found
                            // if($attr_exist){
                            //     $product_attribute_variations = $attr_exist;
                            // }else{
                            $product_attribute_variations = new ProductAttributeVariation;
                            $product_attribute_variations->product_id = $id;
                            $product_attribute_variations->variation_id = $variant->id;
                            // }
                            $product_attribute_variations->attribute_id = $v->id;
                            $product_attribute_variations->attribute_value_id = isset($req[$attribute_option]) ? $req[$attribute_option] : 0;
                            $product_attribute_variations->status = 'AC';
                            $product_attribute_variations->save();
                            // }
                        }
                    }
                }
            }
        }
    }

    public function fetchVariations($request, $columns)
    {
        $query = ProductVariation::where('product_id', $request->product_id)->where('status', '!=', 'DL');
        if (isset($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('weight', 'like', '%' . $request->search . '%');
                $q->orWhereHas('product_units', function ($q) use ($request) {
                    $q->where('unit', 'like', '%' . $request->search . '%');
                    $q->orWhere('fullname', 'like', '%' . $request->search . '%');
                });
                $q->orWhere('qty', 'like', '%' . $request->search . '%');
                $q->orWhere('max_qty', 'like', '%' . $request->search . '%');
                $q->orWhere('price', 'like', '%' . $request->search . '%');
                $q->orWhere('special_price', 'like', '%' . $request->search . '%');
            });
        }

        if (isset($request->order_column)) {
            $variations = $query->orderBy($columns[$request->order_column], $request->order_dir);
        } else {
            $variations = $query->orderBy('created_at', 'desc');
        }
        return $variations;
    }

    public function getSpecificationsAttribute()
    {
        $result = [];
        if ($this->options) {
            $options = $this->options;
            foreach ($options as $option) {
                if (isset($option->attribute_option)) {
                    if($option->attribute_option->attribute()->whereStatus('AC')->exists())
                    {
                        $result[] = $option->attribute_option->value;
                    }
                }
            }
            return implode(', ', $result);
        } else {
            return null;
        }
    }

}
