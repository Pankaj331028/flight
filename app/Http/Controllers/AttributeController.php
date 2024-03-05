<?php

namespace App\Http\Controllers;

use App\Model\Attribute;
use App\Model\AttributeValue;
use App\Model\Cart;
use App\Model\CartItem;
use App\Model\ProductAttributeVariation;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AttributeController extends Controller
{
    public $attribute;
    public $columns;

    public function __construct()
    {
        $this->attribute = new Attribute;
        $this->columns = [
            "select", "slug", "type", "name", "status", "activate", "action",
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('attributes.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function AttributeAjax(Request $request)
    {
        if (isset($request->search)) {
            $request->search = $request->search['value'];
        }
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->attribute->fetchAttribute($request, $this->columns);
        $total = $records->count();
        if (isset($request->start)) {
            $attributes = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $attributes = $records->offset(0)->limit($total)->get();
        }
        // echo $total;
        $result = [];
        foreach ($attributes as $attribute) {
            $data = [];
            $data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="attribute_id[]" value="' . $attribute->id . '"><i class="input-helper"></i></label></div>';
            $data['name'] = ucfirst($attribute->name);
            $data['type'] = ucfirst($attribute->type);
            $data['status'] = ucfirst(config('constants.STATUS.' . $attribute->status));
            $tmp = '';
            if (isset($attribute->options) && !empty($attribute->options)) {
                foreach ($attribute->options as $k => $val) {
                    $tmp .= ucfirst($val->value) . ',';
                }
                $tmp = trim($tmp, ',');
            }
            $data['value'] = $tmp;

            $data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($attribute->status == 'AC' ? ' checked' : '') . ' data-id="' . $attribute->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusattribute"></div></div>';

            $action = '';
            $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editAttribute', ['id' => $attribute->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';

            $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewAttribute', ['id' => $attribute->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

            $action .= '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="toolTip deleteAttribute" data-toggle="tooltip" data-placement="bottom" data-id="' . $attribute->id . '" title="Delete"><i class="fa fa-times"></i></a>';

            $data['action'] = $action;
            $result[] = $data;
        }
        $data = json_encode([
            'data' => $result,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
        ]);
        echo $data;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $type = 'add';
        $url = route('addAttribute');
        $AttributeVariation = array();
        $attribute = new Attribute;

        return view('attributes.create', compact('type', 'url', 'attribute', 'AttributeVariation'));
    }

    /**
     * check for unique email during adding attribute
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkAttribute(Request $request, $id = null)
    {
        if (isset($request->title)) {
            $check = Attribute::where('title', $request->title);
            if (isset($id) && $id != null) {
                $check = $check->where('id', '!=', $id);
            }
            $check = $check->where('status', '!=', 'DL')->count();
            if ($check > 0) {
                return "false";
            } else {
                return "true";
            }

        } else {
            return "true";
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validate = Validator($request->all(), [
            'name' => 'required',
            'type' => 'required',

        ]);

        $attr = [
            'name' => 'Name',
            'type' => 'Type',
        ];

        $validate->setAttributeNames($attr);

        if ($validate->fails()) {
            return redirect()->route('createAttribute')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $data = $request->all();
                $attribute = new Attribute;
                $attribute->type = $request->post('type');
                $attribute->name = $request->post('name');
                $attribute->slug = str_slug($request->post('name'));
                $attribute->status = trim($request->post('status'));
                if ($attribute->save()) {
                    if ($request->post('type') == 'text') {
                        $attributev = new AttributeValue;
                        $attributev->attribute_id = $attribute->id;
                        $attributev->value = $request->post('text');
                        $attributev->save();
                    } else if ($request->post('type') == 'textarea') {
                        $attributev = new AttributeValue;
                        $attributev->attribute_id = $attribute->id;
                        $attributev->value = $request->post('textarea');
                        $attributev->save();
                    } else if ($request->post('type') == 'date') {
                        $attributev = new AttributeValue;
                        $attributev->attribute_id = $attribute->id;
                        $attributev->value = $request->post('date');
                        $attributev->save();
                    } else if ($request->post('type') == 'multiple_select') {
                        if (!empty($request->post('option'))) {
                            foreach ($request->post('option') as $key => $value) {
                                $attributev = new AttributeValue;
                                $attributev->attribute_id = $attribute->id;
                                $attributev->value = $value;
                                $attributev->save();
                            }
                        }
                    } else {
                        if (!empty($request->post('option'))) {
                            foreach ($request->post('option') as $key => $value) {
                                $attributev = new AttributeValue;
                                $attributev->attribute_id = $attribute->id;
                                $attributev->value = $value;
                                $attributev->save();
                            }
                        }
                    }
                    $request->session()->flash('success', 'Attribute added successfully');
                    return redirect()->route('attributes');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('attributes');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('attributes');
            }

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id = null)
    {
        if (isset($id) && $id != null) {
            $attribute = Attribute::where('id', $id)->first();
            if (isset($attribute->id)) {
                $AttributeVariation = AttributeValue::where('attribute_id', $attribute->id)->get();
                return view('attributes.view', compact('attribute', 'AttributeVariation'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('attributes');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('attributes');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id = null)
    {
        if (isset($id) && $id != null) {
            $attribute = Attribute::where('id', $id)->first();
            //echo '<pre>'; print_r($collection); die;
            if (isset($attribute->id)) {
                $AttributeVariation = AttributeValue::where('attribute_id', $attribute->id)->get();
                //echo '<pre>'; print_r($AttributeVariation); die;
                $type = 'edit';
                $url = route('updateAttribute', ['id' => $attribute->id]);
                return view('attributes.create', compact('attribute', 'type', 'url', 'AttributeVariation'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('attributes');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('attributes');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null)
    {
        if (isset($id) && $id != null) {
            $attribute = Attribute::where('id', $id)->first();
            if (isset($attribute->id)) {

                $validate = Validator($request->all(), [
                    'name' => 'required',
                    'type' => 'required',

                ]);

                $attr = [
                    'name' => 'Name',
                    'type' => 'Description',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('editAttribute', ['id' => $attribute->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        $attribute->name = $request->post('name');
                        $attribute->type = $request->post('type');
                        $attribute->slug = str_slug($request->post('name'));
                        $attribute->status = trim($request->post('status'));
                        if ($attribute->save()) {
                            DB::table('attribute_values')->where('attribute_id', $id)->delete();
                            if ($request->post('type') == 'text') {
                                $attributev = new AttributeValue;
                                $attributev->attribute_id = $attribute->id;
                                $attributev->value = $request->post('text');
                                $attributev->save();
                            } else if ($request->post('type') == 'textarea') {
                                $attributev = new AttributeValue;
                                $attributev->attribute_id = $attribute->id;
                                $attributev->value = $request->post('textarea');
                                $attributev->save();
                            } else if ($request->post('type') == 'date') {
                                $attributev = new AttributeValue;
                                $attributev->attribute_id = $attribute->id;
                                $attributev->value = $request->post('date');
                                $attributev->save();
                            } else if ($request->post('type') == 'multiple_select') {
                                if (!empty($request->post('option'))) {
                                    foreach ($request->post('option') as $key => $value) {
                                        $attributev = new AttributeValue;
                                        $attributev->attribute_id = $attribute->id;
                                        $attributev->value = $value;
                                        $attributev->save();
                                    }
                                }
                            } else {
                                if (!empty($request->post('option'))) {
                                    foreach ($request->post('option') as $key => $value) {
                                        $attributev = new AttributeValue;
                                        $attributev->attribute_id = $attribute->id;
                                        $attributev->value = $value;
                                        $attributev->save();
                                    }
                                }
                            }
                            $request->session()->flash('success', 'Attribute updated successfully');
                            return redirect()->route('attributes');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('attributes');
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('attributes');
                    }

                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('attributes');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('attributes');
        }

    }

    // activate/deactivate Attribute
    public function updateStatus(Request $request)
    {

        if (isset($request->ids) && $request->ids != null) {
            $attribute = Attribute::find($request->ids);

            if (isset($attribute->id)) {
                $attribute->status = $request->status;
                
                if ($attribute->save()) {
                    if($attribute->options)
                    {
                        AttributeValue::where('attribute_id', $attribute->id)->update(['status' => $request->status]);
                    }
                    echo json_encode(['status' => 1, 'message' => 'Attribute updated successfully.']);
                    //$request->session()->flash('success', 'Attribute updated successfully.');
                    //return redirect()->back();
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Unable to update Attribute. Please try again later.']);
                    //$request->session()->flash('error', 'Unable to update Attribute. Please try again later.');
                    //return redirect()->back();
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid Slider']);
                //$request->session()->flash('error', 'Invalid Data');
                //return redirect()->back();
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid Slider']);
            //$request->session()->flash('error', 'Invalid Data');
            //return redirect()->back();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (isset($request->deleteid) && $request->deleteid != null) {
            $attribute = Attribute::find($request->deleteid);

            if (isset($attribute->id)) {
                $attribute->status = 'DL';
                if ($attribute->save()) {
                    $request->session()->flash('success', 'Attribute deleted successfully.');
                    return redirect()->back();
                } else {
                    $request->session()->flash('error', 'Unable to delete Attribute. Please try again later.');
                    return redirect()->back();
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->back();
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->back();
        }
    }
    /**
     * Remove multiple resource from storage.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function bulkdelete(Request $request)
    {

        if (isset($request->ids) && $request->ids != null) {
            $ids = count($request->ids);
            $count = 0;
            foreach ($request->ids as $id) {
                $attribute = Attribute::find($id);

                if (isset($attribute->id)) {
                    $attribute->status = 'DL';
                    if ($attribute->save()) {
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Attribute deleted successfully.']);
                //return redirect()->back();
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all Attribute were deleted. Please try again later.']);
                //return redirect()->back();
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
            //return redirect()->back();
        }
    }
    /**
     * activate/deactivate multiple resource from storage.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function bulkchangeStatus(Request $request)
    {

        if (isset($request->ids) && $request->ids != null) {
            $ids = count($request->ids);
            $count = 0;
            foreach ($request->ids as $id) {
                $attribute = Attribute::find($id);

                if (isset($attribute->id)) {
                    if ($attribute->status == 'AC') {
                        $attribute->status = 'IN';
                    } elseif ($attribute->status == 'IN') {
                        $attribute->status = 'AC';
                    }

                    if ($attribute->save()) {
                        if($attribute->options)
                        {
                            AttributeValue::where('attribute_id', $attribute->id)->update(['status' => $attribute->status]);
                        }
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Attribute updated successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all Attribute were updated. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
    }

    public function variationSend(Request $request)
    {
        $ids = array_unique($request->ids);
        $data = ProductAttributeVariation::where('product_id', $request->product_id)->whereIn('attribute_value_id', $ids)->get();

        if (isset($data) && count($data) == count($ids) && $data->unique('variation_id')->count() == 1) {
            // if (isset($data) && $data->unique('variation_id')->count() == 1) {
            $varid = $data->unique('variation_id')->first()->variation_id;
            $maxqty = $data->unique('variation_id')->first()->variation->max_qty;

            $user_id = Auth::guard('front')->user()->id;

            $cart = Cart::where(['user_id' => $user_id, 'status' => 'AC'])->first();
            $cartcount = 0;
            if ($cart) {
                $query = CartItem::selectRaw('sum(qty) as total')->where('cart_id', $cart->id)->where('product_id', $request->product_id)->where('product_variation_id', $varid)->where('status', 'AC')->first();

                if ($query) {
                    $cartcount = $query->total;
                }
            }
            return response()->json(['status' => true, 'variation_id' => $varid, 'cartcount' => $cartcount, 'max_qty' => $maxqty]);
        } else {
            return response()->json(['status' => false, 'message' => 'Selected product not avaiable']);
        }
    }

}
