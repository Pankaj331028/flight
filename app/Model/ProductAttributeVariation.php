<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeVariation extends Model
{
    protected $table = 'product_attribute_variations';
    protected $fillable = ['product_id'];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }

    public function attribute_option()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id', 'id');
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id', 'id')->where('status', 'AC');
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

    public static function saveVariations($count, $req, $id)
    {
        $all = ProductVariation::where('product_id', $id)->get();
        $allids = array_column($all->toArray(), 'id');
        $presentids = [];
        $ids = [];

        //print_r($req); die;

        if (count($all) > 0) {

            foreach ($all as $key => $value) {
                for ($i = 1; $i <= $count; $i++) {

                    $exists = $req["prodvarid_" . $i];
                    $presentids[] = $exists;
                    if ($exists != 0) {
                        if ($value->id == $exists) {
                            // $weight = "prodweight_" . $i;
                            // $unit = "produnit_" . $i;
                            $qty = "prodqty_" . $i;
                            $maxqty = "prodmaxqty_" . $i;
                            // $mrp = "prodmrp_" . $i;
                            // $special = "prodspecial_" . $i;

                            $mrp_inr = "prodmrp_inr_" . $i;
                            $special_inr = "prodspecial_inr_" . $i;

                            $mrp_jpy = "prodmrp_jpy_" . $i;

                            //print_r($mrp_jpy); die;

                            $mrp_usd = "prodmrp_usd_" . $i;
                            $special_usd = "prodspecial_usd_" . $i;

                            $special_jpy = "prodspecial_jpy_" . $i;
                            $mrp_cad = "prodmrp_cad_" . $i;
                            $special_cad = "prodspecial_cad_" . $i;
                            $mrp_gbp = "prodmrp_gbp_" . $i;
                            $special_gbp = "prodspecial_gbp_" . $i;
                            // $value->weight = $req[$weight];
                            // $value->unit_id = $req[$unit];
                            $value->qty = $req[$qty];
                            $value->max_qty = $req[$maxqty];
                            // $value->price = $req[$mrp];
                            // $value->special_price = isset($req[$special]) ? $req[$special] : null;

                            $value->price = $req[$mrp_inr];
                            $value->special_price = isset($req[$special_inr]) ? $req[$special_inr] : null;
                            $value->usd_price = $req[$mrp_usd];
                            $value->usd_special_price = isset($req[$special_usd]) ? $req[$special_usd] : null;
                            $value->jpy_price = $req[$mrp_jpy];
                            $value->jpy_special_price = isset($req[$special_jpy]) ? $req[$special_jpy] : null;
                            $value->gbp_price = $req[$mrp_gbp];
                            $value->gbp_special_price = isset($req[$special_gbp]) ? $req[$special_gbp] : null;
                            $value->cad_price = $req[$mrp_inr];
                            $value->cad_special_price = isset($req[$special_cad]) ? $req[$special_cad] : null;

                            $value->status = trim($req["prodstatus_" . $i]);
                            $value->save();
                        }
                    } else {
                        // for ($i = 1; $i <= $count; $i++) {
                        $variant = new ProductVariation;
                        $variant->product_id = $id;
                        //$weight = "prodweight_" . $i;
                        $unit = "produnit_" . $i;
                        $qty = "prodqty_" . $i;
                        $maxqty = "prodmaxqty_" . $i;
                        // $mrp = "prodmrp_" . $i;
                        // $special = "prodspecial_" . $i;

                        $mrp_inr = "prodmrp_inr_" . $i;
                        $special_inr = "prodspecial_inr_" . $i;
                        $mrp_usd = "prodmrp_usd_" . $i;
                        $special_usd = "prodspecial_usd_" . $i;
                        $mrp_jpy = "prodmrp_jpy_" . $i;
                        $special_jpy = "prodspecial_jpy_" . $i;
                        $mrp_cad = "prodmrp_cad_" . $i;
                        $special_cad = "prodspecial_cad_" . $i;
                        $mrp_gbp = "prodmrp_gbp_" . $i;
                        $special_gbp = "prodspecial_gbp_" . $i;

                        //print_r($mrp_usd); die;

                        //$variant->weight = $req[$weight];
                        $variant->unit_id = $req[$unit];
                        $variant->qty = $req[$qty];
                        $variant->max_qty = $req[$maxqty];
                        $variant->price = $req[$mrp_inr];
                        $variant->special_price = isset($req[$special_inr]) ? $req[$special_inr] : null;
                        $variant->usd_price = $req[$mrp_usd];
                        $variant->usd_special_price = isset($req[$special_usd]) ? $req[$special_usd] : null;
                        $variant->jpy_price = $req[$mrp_jpy];
                        $variant->jpy_special_price = isset($req[$special_jpy]) ? $req[$special_jpy] : null;
                        $variant->gbp_price = $req[$mrp_gbp];
                        $variant->gbp_special_price = isset($req[$special_gbp]) ? $req[$special_gbp] : null;
                        $variant->cad_price = $req[$mrp_inr];
                        $variant->cad_special_price = isset($req[$special_cad]) ? $req[$special_cad] : null;

                        $variant->status = trim($req["prodstatus_" . $i]);
                        $variant->save();
                        // }
                    }
                }
            }
            $ids = array_diff($allids, $presentids);
        } else {
            for ($i = 1; $i <= $count; $i++) {
                $variant = new ProductVariation;
                $variant->product_id = $id;
                //$weight = "prodweight_" . $i;
                $unit = "produnit_" . $i;
                $qty = "prodqty_" . $i;
                $maxqty = "prodmaxqty_" . $i;
                // $mrp = "prodmrp_" . $i;
                // $special = "prodspecial_" . $i;
                $mrp_inr = "prodmrp_inr_" . $i;
                $special_inr = "prodspecial_inr_" . $i;
                $mrp_usd = "prodmrp_usd_" . $i;
                $special_usd = "prodspecial_usd_" . $i;
                $mrp_jpy = "prodmrp_jpy_" . $i;
                $special_jpy = "prodspecial_jpy_" . $i;
                $mrp_cad = "prodmrp_cad_" . $i;
                $special_cad = "prodspecial_cad_" . $i;
                $mrp_gbp = "prodmrp_gbp_" . $i;
                $special_gbp = "prodspecial_gbp_" . $i;

                //$variant->weight = $req[$weight];
                $variant->unit_id = $req[$unit];
                $variant->qty = $req[$qty];
                $variant->max_qty = $req[$maxqty];
                // $variant->price = $req[$mrp];
                // $variant->special_price = isset($req[$special]) ? $req[$special] : null;

                $variant->price = $req[$mrp_inr];
                $variant->special_price = isset($req[$special_inr]) ? $req[$special_inr] : null;
                $variant->usd_price = $req[$mrp_usd];
                $variant->usd_special_price = isset($req[$special_usd]) ? $req[$special_usd] : null;
                $variant->jpy_price = $req[$mrp_jpy];
                $variant->jpy_special_price = isset($req[$special_jpy]) ? $req[$special_jpy] : null;
                $variant->gbp_price = $req[$mrp_gbp];
                $variant->gbp_special_price = isset($req[$special_gbp]) ? $req[$special_gbp] : null;
                $variant->cad_price = $req[$mrp_inr];
                $variant->cad_special_price = isset($req[$special_cad]) ? $req[$special_cad] : null;

                $variant->status = trim($req["prodstatus_" . $i]);
                if ($variant->save()) {
                    $selected_all_attribute = Attribute::whereIn('id', $req["attribute_id"])->select('slug')->get();
                    if (count($selected_all_attribute) > 0) {
                        foreach ($selected_all_attribute as $k => $v) {
                            $attribute_option = "prod" . $v->slug . "_" . $i;
                            if (isset($req[$attribute_option])) {
                                $product_attribute_variations = new ProductAttributeVariation;
                                $product_attribute_variations->product_id = $id;
                                $product_attribute_variations->variation_id = $variant->id;
                                $product_attribute_variations->attribute_value_id = isset($req[$attribute_option]) ? $req[$attribute_option] : 0;
                                $product_attribute_variations->status = 'AC';
                                $product_attribute_variations->save();
                            }
                        }
                    }
                }

            }
        }
        //delete all those variations which user deleted in case of edit
        foreach ($ids as $value) {
            ProductVariation::where('id', $value)->update(['status' => 'DL']);
        }
    }

    public function fetchVariations($request, $columns)
    {
        $query = ProductVariation::select('product_variations.*', 'pu.unit', 'pu.fullname')->join('c_product_units as pu', 'pu.id', '=', 'product_variations.unit_id')->where('product_id', $request->product_id)->where('product_variations.status', '!=', 'DL');

        if (isset($request->search)) {
            $query->where(function ($q) use ($request) {
                // $q->where('weight', 'like', '%' . $request->search . '%');
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
            if ($columns[$request->order_column] == 'unit') {
                $variations = $query->orderBy('pu.fullname', $request->order_dir);
            } else {
                $variations = $query->orderBy('product_variations.' . $columns[$request->order_column], $request->order_dir);
            }

        } else {
            $variations = $query->orderBy('product_variations.created_at', 'desc');
        }
        return $variations;
    }

}
