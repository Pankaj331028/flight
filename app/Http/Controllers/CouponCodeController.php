<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Model\BusRuleRef;
use App\Model\Category;
use App\Model\CouponCode;
use App\Model\CouponRange;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CouponCodeController extends Controller
{
    public $code;
    public $columns;
    public $restrict;
    public $category;
    public $currency;
    public $subcategories;
    public $existing_categories;

    public function __construct()
    {
        $this->code = new CouponCode;
        $this->subcategories = [];
        $this->restrict = BusRuleRef::where('rule_name', 'category_restrict')->first()->rule_value;
        $this->category = new Category;
        $this->currency = BusRuleRef::where('rule_name', 'currency')->first()->rule_value;
        $this->columns = [
            "select", "coupon_code", "categories", "discount", "max_use", "max_use_total", "start_date", "end_date", "status", "activate", "action",
        ];
        $this->existing_categories = [];

        $data = CouponCode::where('status', 'AC')->pluck('category_id');
        foreach ($data as $value) {
            $cat = explode(',', $value);
            foreach ($cat as $val) {
                if (!in_array($val, $this->existing_categories)) {
                    $this->existing_categories[] = $val;
                }

            }
        }

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $list = $this->category->fetchCategories()->get();

        $categories = [];
        $i = 1;
        $level = 1;
        foreach ($list as $key => $value) {
            $categories[$value->id] = $i . ". " . $value->name;
            $root = $value;
            $categories = $this->setList($root, $categories, $i, $level);
            $i++;
        }
        return view('coupon_codes.index', compact('categories'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function couponCodesAjax(Request $request)
    {
        if(isset($request->search['value']))
        {
            $request->search = $request->search['value'];
        }
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->code->fetchCouponCodes($request, $this->columns);
        $total = $records->get();
        if (isset($request->start)) {
            $coupon_codes = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $coupon_codes = $records->offset($request->start)->limit(count($total))->get();
        }
        // echo $total;
        $result = [];
        $no = 1;
        foreach ($coupon_codes as $code) {
            $data = [];
            $data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $code->id . '"><i class="input-helper"></i></label></div>';
            $data['sno'] = $no++;
            $data['coupon_code'] = $code->code;
            $data['categories'] = $this->formatCategory($code->category_id);
            $data['max_use'] = $code->max_use;
            $data['max_use_total'] = $code->max_use_total;
            $data['start_date'] = date('Y M, d', strtotime($code->start_date));
            $data['end_date'] = date('Y M, d', strtotime($code->end_date));
            $data['status'] = ucfirst(config('constants.STATUS.' . $code->status));

            $discounts = '';
            $count = 1;
            foreach ($code->coupon_range as $key => $value) {
                $discounts .= "Range " . $count++ . ": \n";
                $discounts .= "Minimum Price: " . $this->currency . " " . $value->min_price . "\n";
                $discounts .= "Maximum Price: " . $this->currency . " " . $value->max_price . "\n";
                $discounts .= "Discount: " . $value->value . "%" . "\n\n";
            }
            $data['discounts'] = $discounts;

            // check active coupon_codes of similar categories
            $cat = explode(',', $code->category_id);
            $count = 0;
            foreach ($cat as $key => $value) {
                if (CouponCode::whereRaw('find_in_set(?,category_id)', [$value])->where('status', 'AC')->count() > 0) {
                    $count++;
                }

            }

            if (Helper::checkAccess(route('changeStatusCouponCode'))) {
                $data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($code->status == 'AC' ? ' checked' : '') . ' data-id="' . $code->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusCouponCode" data-count="' . $count . '"></div></div>';
            } else {
                $data['activate'] = ucfirst(config('constants.STATUS.' . $code->status));
            }
            $action = '';

            if (Helper::checkAccess(route('editCouponCode'))) {
                $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editCouponCode', ['id' => $code->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            }
            if (Helper::checkAccess(route('viewCouponCode'))) {
                $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewCouponCode', ['id' => $code->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
            }
            $data['action'] = $action;

            $result[] = $data;
        }
        $data = json_encode([
            'data' => $result,
            'recordsTotal' => count($total),
            'recordsFiltered' => count($total),
        ]);

        echo $data;

    }

    // function to create comma separated string of categories from id.
    public function formatCategory($ids)
    {
        $ids = explode(',', $ids);
        $categories = implode(',', Category::whereIN('id', $ids)->pluck('name')->toArray());
        return $categories;
    }

    //recursive function to create array of categories and subcategories together using nested loop along with indented serial number.
    public function setList($root, $categories, $i, $level)
    {
        $child = $root->childCat;
        $j = 1;
        if (($this->restrict == null) || ($this->restrict != null && $level <= $this->restrict)) {
            if (!isset($this->subcategories[$root->id]) && count($child) > 0) {
                $this->subcategories[$root->id] = array();
            }

            foreach ($child as $ch) {
                if ($ch->status != 'DL') {
                    //ordering for each subcategory along with nesting.
                    $k = "&nbsp;&nbsp;" . $i . "." . $j;
                    $categories[$ch->id] = $k . '. ' . $ch->name;
                    array_push($this->subcategories[$root->id], $ch->id);
                    //calling function recursively to loop through subcategories of each category.
                    $categories = $this->setList($ch, $categories, $k, ++$level);
                    $level = 2;
                    $j++;
                }
            }
            //once all subcategories are done looping, move to next main subcategory.
            $root = $child;
        }
        return $categories;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $type = 'add';
        $url = route('addCouponCode');
        $code = new CouponCode;
        $list = $this->category->fetchCategories()->get();

        $categories = [];
        $i = 1;
        $level = 1;
        foreach ($list as $key => $value) {
            $categories[$value->id] = $i . ". " . $value->name;
            $root = $value;
            $categories = $this->setList($root, $categories, $i, $level);
            $i++;
        }
        $subcategories = $this->subcategories;
        $existing = $this->existing_categories;
        return view('coupon_codes.create', compact('type', 'url', 'code', 'categories', 'subcategories', 'existing'));
    }

    /**
     * check for unique name during adding new code
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkCouponCode(Request $request, $id = null)
    {
        if (isset($request->coupon_code)) {
            $check = CouponCode::where('code', $request->coupon_code);

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
            'max_use' => 'required',
            'max_use_total' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'categories.*' => 'required',

        ]);

        $attr = [
            'max_use' => 'Max Use (Per User)',
            'max_use_total' => 'Max Use Total',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'categories.*' => 'Categories',
        ];

        $validate->setAttributeNames($attr);

        if ($validate->fails()) {
            return redirect()->route('createCouponCode')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $code = new CouponCode;
                $code->code = strtoupper($request->post('coupon_code'));

                // remove subcategories from the list if parent category is selected.
                $code_cat = $request->post('coupon_categories');
                foreach ($code_cat as $value) {
                    if (isset($this->subcategories[$value])) {
                        foreach ($this->subcategories[$value] as $key) {
                            unset($code_cat[array_search($key, $code_cat)]);
                        }
                    }
                }
                $code->category_id = implode(',', $code_cat);
                $code->title = $request->post('coupon_title');
                $code->max_use = $request->post('max_use');
                $code->max_use_total = $request->post('max_use_total');
                $code->description = $request->post('coupon_description') ? $request->post('coupon_description') : null;
                $code->start_date = date('Y-m-d', strtotime($request->post('start_date')));
                $code->end_date = date('Y-m-d', strtotime($request->post('end_date')));
                $code->status = trim($request->post('status'));
                $code->created_at = date('Y-m-d H:i:s');

                $count = 0;
                foreach ($request->all() as $key => $value) {
                    if (stripos($key, 'coderange') !== false) {
                        $count++;
                    }

                }
                if ($count > 0) {
                    if ($code->save()) {

                        CouponRange::saveCouponRange($count, $request->all(), $code->id);
                        $request->session()->flash('success', 'Coupon Code added successfully');
                        return redirect()->route('couponCodes');
                    } else {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('couponCodes');
                    }
                } else {
                    $request->session()->flash('error', 'Please add ranges.');
                    return redirect()->route('couponCodes');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('couponCodes');
            }

        }
    }

    //recursive function to create array of categories and subcategories together using nested loop along with indented serial number for view page
    public function setListOrder($root, $categories, $level)
    {
        $child = $root->childCat;
        if (($this->restrict == null) || ($this->restrict != null && $level <= $this->restrict)) {
            foreach ($child as $ch) {
                if ($ch->status != 'DL') {
                    //ordering for each subcategory along with nesting.
                    if (!array_key_exists($root->id, $categories)) {
                        $categories[$root->id] = array();
                    }
                    $categories[$root->id][$ch->id] = $ch->name;

                    //calling function recursively to loop through subcategories of each category.
                    $categories = $this->setListOrder($ch, $categories, ++$level);
                    $level = 2;
                }
            }
            //once all subcategories are done looping, move to next main subcategory.
            $root = $child;
        }
        return $categories;
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
            $code = CouponCode::where('id', $id)->first();
            if (isset($code->id)) {
                $list = Category::with(['childCat' => function ($q) {
                    $q->where('c_categories.status', '!=', 'DL');
                    // exclude those subcategories where separate coupon code is applied
                    $q->whereRaw('not exists(select category_id from c_coupon_codes where find_in_set(c_categories.id,c_coupon_codes.category_id))');
                }])->where('status', '!=', 'DL')->whereIN('id', explode(',', $code->category_id))->orderBy('created_at', 'asc')->get();
                $categories = [];
                $i = 1;
                $level = 1;
                foreach ($list as $key => $value) {
                    $categories[$value->id] = $i . ". " . $value->name;
                    $root = $value;
                    $categories = $this->setList($root, $categories, $i, $level);
                    $i++;
                }

                $subcategories = $this->subcategories;
                return view('coupon_codes.view', compact('code', 'categories'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('couponCodes');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('couponCodes');
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
            $code = CouponCode::where('id', $id)->first();
            if (isset($code->id)) {
                $cartcount = 0;
                $ordercount = 0;

                if (count($code->coupon_range) > 0) {

                    foreach ($code->coupon_range as $key => $value) {
                        $cartcount += count($value->code_cartitems);
                        $ordercount += count($value->code_orderitems);
                    }
                }
                if ($cartcount <= 0) {
                    if ($ordercount <= 0) {
                        $type = 'edit';
                        $url = route('updateCouponCode', ['id' => $code->id]);

                        $list = $this->category->fetchCategories()->get();

                        $categories = [];
                        $i = 1;
                        $level = 1;
                        foreach ($list as $key => $value) {
                            $categories[$value->id] = $i . ". " . $value->name;
                            $root = $value;
                            $categories = $this->setList($root, $categories, $i, $level);
                            $i++;
                        }
                        $subcategories = $this->subcategories;
                        $existing = $this->existing_categories;
                        return view('coupon_codes.create', compact('code', 'type', 'url', 'categories', 'subcategories', 'existing'));
                    } else {
                        $request->session()->flash('error', 'This code cannot be edited. Orders have been placed using this coupon code.');
                        return redirect()->route('couponCodes');
                    }
                } else {
                    $request->session()->flash('error', 'This code cannot be edited. Cart has been created using this coupon code.');
                    return redirect()->route('couponCodes');
                }

            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('couponCodes');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('couponCodes');
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
            $code = CouponCode::where('id', $id)->first();
            if (isset($code->id)) {
                $validate = Validator($request->all(), [
                    'max_use' => 'required',
                    'max_use_total' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'categories.*' => 'required',

                ]);

                $attr = [
                    'max_use' => 'Max Use (Per User)',
                    'max_use_total' => 'Max Use Total',
                    'start_date' => 'Start Date',
                    'end_date' => 'End Date',
                    'categories.*' => 'Categories',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('editCouponCode', ['id' => $code->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        $code->code = strtoupper($request->post('coupon_code'));

                        // remove subcategories from the list if parent category is selected.
                        $code_cat = $request->post('coupon_categories');
                        foreach ($code_cat as $value) {
                            if (isset($this->subcategories[$value])) {
                                foreach ($this->subcategories[$value] as $key) {
                                    unset($code_cat[array_search($key, $code_cat)]);
                                }
                            }
                        }
                        $code->category_id = implode(',', $code_cat);
                        $code->title = $request->post('coupon_title');
                        $code->max_use = $request->post('max_use');
                        $code->max_use_total = $request->post('max_use_total');
                        $code->description = $request->post('coupon_description') ? $request->post('coupon_description') : null;
                        $code->start_date = date('Y-m-d', strtotime($request->post('start_date')));
                        $code->end_date = date('Y-m-d', strtotime($request->post('end_date')));
                        $code->status = trim($request->post('status'));

                        $count = 0;
                        foreach ($request->all() as $key => $value) {
                            if (stripos($key, 'coderangeid') !== false) {
                                $count++;
                            }

                        }
                        if ($count > 0) {
                            if ($code->save()) {
                                CouponRange::saveCouponRange($count, $request->all(), $code->id);
                                $request->session()->flash('success', 'Coupon Code updated successfully');
                                return redirect()->route('couponCodes');
                            } else {
                                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                                return redirect()->route('couponCodes');
                            }
                        } else {
                            $request->session()->flash('error', 'Please add ranges.');
                            return redirect()->route('couponCodes');
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('couponCodes');
                    }

                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('coupon_codes');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('coupon_codes');
        }

    }

    // activate/deactivate coupon code
    public function updateStatus(Request $request)
    {

        if (isset($request->statusid) && $request->statusid != null) {
            $code = CouponCode::find($request->statusid);

            if (isset($code->id)) {
                $code->status = $request->status;
                if ($code->save()) {

                    // check active coupon_codes of similar categories
                    $cat = explode(',', $code->category_id);
                    foreach ($cat as $key => $value) {
                        $others = CouponCode::whereRaw('find_in_set(?,category_id)', [$value])->where('status', 'AC')->where('id', '!=', $code->id)->get();
                        foreach ($others as $k => $v) {
                            $cats = str_replace(',' . $value, '', $v->category_id);
                            $cats = str_replace($value . ',', '', $cats);
                            $v->category_id = $cats;
                            $v->save();
                        }

                    }
                    $request->session()->flash('success', 'CouponCode updated successfully.');
                    return redirect()->back();
                } else {
                    $request->session()->flash('error', 'Unable to update coupon code. Please try again later.');
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

    // activate/deactivate coupon code
    public function updateStatusAjax(Request $request)
    {

        if (isset($request->statusid) && $request->statusid != null) {
            $code = CouponCode::find($request->statusid);

            if (isset($code->id)) {
                $code->status = $request->status;

                if ($code->save()) {

                    // check active coupon_codes of similar categories
                    $cat = explode(',', $code->category_id);
                    foreach ($cat as $key => $value) {
                        $others = CouponCode::whereRaw('find_in_set(?,category_id)', [$value])->where('status', 'AC')->where('id', '!=', $code->id)->get();
                        foreach ($others as $k => $v) {
                            $cats = str_replace(',' . $value, '', $v->category_id);
                            $cats = str_replace($value . ',', '', $cats);
                            $v->category_id = $cats;
                            $v->save();
                        }

                    }
                    echo json_encode(['status' => 1, 'message' => 'Coupon Code updated successfully.']);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Unable to update coupon code. Please try again later.']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid Coupon Code']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid Coupon Code']);
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
                $code = CouponCode::find($id);

                if (isset($code->id)) {
                    if ($code->status == 'AC') {
                        $code->status = 'IN';
                    } elseif ($code->status == 'IN') {
                        $code->status = 'AC';

                        // check active coupon_codes of similar categories
                        $cat = explode(',', $code->category_id);
                        foreach ($cat as $key => $value) {
                            $others = CouponCode::whereRaw('find_in_set(?,category_id)', [$value])->where('id', '!=', $code->id)->where('status', 'AC')->get();
                            foreach ($others as $k => $v) {
                                $cats = str_replace(',' . $value, '', $v->category_id);
                                $cats = str_replace($value . ',', '', $cats);
                                $v->category_id = $cats;
                                $v->save();
                            }

                        }
                    }

                    if ($code->save()) {
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Coupon Codes updated successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all coupon codes were updated. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
    }
}
