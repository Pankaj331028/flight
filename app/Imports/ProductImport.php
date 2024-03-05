<?php

namespace App\Imports;

use App\Library\Helper;
use App\Model\Category;
use App\Model\Product;
use App\Model\ProductUnit;
use App\Model\ProductVariation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Request;
use Session;

class ProductImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        if ($rows->count()) {
            $i = 1;
            $arr = [];
            $error = 0;
            if (isset($rows[0])) {
                foreach ($rows as $key => $value) {
                    // validate fields properly
                    // if(!empty($value['category']) && !empty($value['name']) && !empty($value['image']) && !empty($value['description']) && !empty($value['weight']) && !empty($value['unit']) && !empty($value['quantity']) && !empty($value['price']) && !empty($value['maximum_quantity_user_can_purchase_at_a_time']))
                    // {
                    if ($value['category'] != null) {
                        $cat = Category::where('name', $value['category'])->first();
                        if (!isset($cat->id)) {
                            Request::session()->flash('error', 'Category doesnot exists at line ' . $i);
                            return redirect()->back();
                        }
                    } else {
                        if ($value['name'] != null) {
                            Request::session()->flash('error', 'Category is blank at line ' . $i);
                            return redirect()->back();
                        }
                    }

                    if ($value['image'] == null) {
                        if ($value['name'] != null) {
                            Request::session()->flash('error', 'Image is blank at line ' . $i);
                            return redirect()->back();
                        }
                    }
                    if ($value['name'] == null) {
                        if ($value['category'] != null) {
                            Request::session()->flash('error', 'Product Name is blank at line ' . $i);
                            return redirect()->back();
                        }
                    } else {
                        $prod = Product::where('name', $value['name'])->first();
                        if (isset($prod->id)) {
                            Request::session()->flash('error', 'Product with name at line ' . $i . ' already exists');
                            return redirect()->back();
                        }
                    }

                    if ($value['description'] == null) {
                        if ($value['name'] != null) {
                            Request::session()->flash('error', 'Product Description is blank at line ' . $i);
                            return redirect()->back();
                        }
                    }
                    // if ($value['weight'] == null) {
                    //     Request::session()->flash('error', 'Variation weight is blank at line ' . $i);
                    //     return redirect()->back();
                    // }
                    // if ($value['unit'] == null) {
                    //     Request::session()->flash('error', 'Variation unit is blank at line ' . $i);
                    //     return redirect()->back();
                    // }
                    if ($value['quantity'] <= 0) {
                        Request::session()->flash('error', 'Variation quantity should be greather than 0 at line ' . $i);
                        return redirect()->back();
                    }
                    if ($value['price'] == null) {
                        Request::session()->flash('error', 'Variation price is blank at line ' . $i);
                        return redirect()->back();
                    }
                    if ($value['maximum_quantity_user_can_purchase_at_a_time'] <= 0) {
                        Request::session()->flash('error', 'Variation max quantity should be greather than 0 at line ' . $i);
                        return redirect()->back();
                    }
                    $i++;
                    // }
                    // else{
                    //     $error ++;
                    // }
                }
                // if($error > 0){
                //     Request::session()->flash('error', 'Unable to import complete data. Please verify and import again.');
                //     return redirect()->route('importProducts');
                // }
                $i = 1;
                foreach ($rows as $key => $value) {
                    if ($value['name'] != null) {
                        $arr[$i] = array(
                            'cat' => $cat->id,
                            'image' => $value['image'],
                            'name' => $value['name'],
                            'description' => $value['description'],
                            'quick_grab' => $value['quick_grab'] != null ? strval((int) $value['quick_grab']) : '0',
                            'is_exclusive' => $value['is_exclusive'] != null ? strval((int) $value['is_exclusive']) : '0',
                            'variation' => array(
                                array(
                                    // 'weight' => $value['weight'],
                                    'unit' => $value['unit'],
                                    'quantity' => (int) $value['quantity'],
                                    'price' => (int) $value['price'],
                                    'special_price' => $value['special_price'] != null ? (int) $value['special_price'] : 0,
                                    'max_qty' => (int) $value['maximum_quantity_user_can_purchase_at_a_time'],
                                ),
                            ),
                        );
                    } else {
                        end($arr);
                        $key = key($arr);
                        $temp = array(
                            // 'weight' => $value['weight'],
                            'unit' => $value['unit'],
                            'quantity' => (int) $value['quantity'],
                            'price' => (int) $value['price'],
                            'special_price' => $value['special_price'] != null ? (int) $value['special_price'] : 0,
                            'max_qty' => (int) $value['maximum_quantity_user_can_purchase_at_a_time'],
                        );
                        array_push($arr[$key]['variation'], $temp);
                    }
                    $i++;
                }
                $count = 0;
                foreach ($arr as $key => $value) {
                    $p = Product::where('name', $value['name'])->first();
                    if (isset($p->id)) {
                        Request::session()->flash('error', 'Product with name at line ' . $count++ . ' already exists');
                        return redirect()->back();
                    }
                    $prod = new Product;
                    $prod->product_code = Helper::generateNumber('products', 'product_code');
                    $prod->parent_id = $value['cat'];
                    $prod->image = $value['image'];
                    $prod->name = $value['name'];
                    $prod->description = $value['description'];
                    $prod->quick_grab = $value['quick_grab'];
                    $prod->is_exclusive = $value['is_exclusive'];
                    $prod->save();
                    $count++;

                    foreach ($value['variation'] as $k => $v) {
                        $unit = $v['unit'];
                        $names = ($unit[strlen($unit) - 1] == 's') ? [$unit, rtrim($unit, 's')] : [$unit, $unit . 's'];
                        $unit = ProductUnit::whereIN('unit', $names)->first();
                        if (!isset($unit->id)) {
                            $unit = new ProductUnit;
                            $unit->unit = $v['unit'];
                            $unit->fullname = $v['unit'];
                            $unit->save();
                        }

                        $var = new ProductVariation;
                        // $var->weight = $v['weight'];
                        $var->product_id = $prod->id;
                        $var->unit_id = $unit->id;
                        $var->qty = $v['quantity'];
                        $var->price = $v['price'];
                        $var->special_price = $v['special_price'];
                        $var->max_qty = $v['max_qty'];
                        $var->save();
                    }
                }
                if ($count == count($arr)) {
                    Request::session()->flash('success', 'Data imported successfully');
                    return redirect()->back();
                } else {
                    Request::session()->flash('error', 'Unable to import complete data. Please verify and import again.');
                    return redirect()->back();
                }
            } else {
                Request::session()->flash('error', 'Blank data or unable to read data from file.');
                return redirect()->back();
            }
        } else {
            Request::session()->flash('error', 'Blank data or unable to read data from file.');
            return redirect()->back();
        }
    }
}
