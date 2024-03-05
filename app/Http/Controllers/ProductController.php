<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use App\Imports\ProductImport;
use App\Imports\ProductUpdate;
use App\Library\Helper;
use App\Library\Notify;
use App\Model\Attribute as ModelAttribute;
use App\Model\Brand;
use App\Model\BusRuleRef;
use App\Model\Category;
use App\Model\Product;
use App\Model\ProductUnit;
use App\Model\ProductVariation;
use App\Model\Tax;
use Illuminate\Http\Request;
use Image;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use URL;

class ProductController extends Controller
{
    public $product;
    public $category;
    public $categories;
    public $brands;
    public $taxes;
    public $units;
    public $columns;

    public function __construct()
    {
        $this->product = new Product;
        $this->category = new Category;
        $this->brands = Brand::where('status', '!=', 'DL')->pluck('name', 'id');
        $this->taxes = Tax::where('status', '!=', 'DL')->select('value', 'name', 'id')->get();
        $this->units = ProductUnit::where('status', '!=', 'DL')->select('fullname', 'unit', 'id')->get();

        $records = $this->category->fetchCategories();
        $this->restrict = BusRuleRef::where('rule_name', 'category_restrict')->first()->rule_value;
        $list = $records->get();
        // echo $total;
        $result = [];
        $categories = [];
        $i = 1;

        foreach ($list as $key => $value) {
            $categories[$value->id] = $i . ". " . $value->name;
            $root = $value;
            $categories = $this->setList($root, $categories, $i, 1);
            $i++;
        }
        $this->categories = $categories;
        $this->columns = [
            "select", "product_code", "product_id", "category_id", "subcategory_id", "name", "description", "image", "variations", "manage_stock", "quick_grab", "is_exclusive", "tax_id", "type", "status", "activate", "action", "fav_count",
        ];
    }

    //recursive function to create array of categories and subcategories together using nested loop along with indented serial number.
    public function setListIndex($root, $categories, $i, $level)
    {
        $child = $root->childCat;
        $j = 1;
        if (($this->restrict == null) || ($this->restrict != null && $level <= $this->restrict)) {
            foreach ($child as $ch) {
                if ($ch->status != 'DL') {
                    //ordering for each subcategory along with nesting.
                    $k = "&nbsp;&nbsp;" . $i . "." . $j;
                    $categories[$ch->id] = $k . '. ' . $ch->name;

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

    //recursive function to create array of categories and subcategories together using nested loop along with indented serial number.
    public function setList($root, $categories, $i, $level)
    {
        $child = $root->childCat;
        $j = 1;
        if (($this->restrict == null) || ($this->restrict != null && $level <= $this->restrict)) {
            foreach ($child as $ch) {
                if ($ch->status != 'DL') {
                    //ordering for each subcategory along with nesting.
                    $k = "&nbsp;&nbsp;" . $i . "." . $j;
                    $categories[$root->id . '-' . $ch->id] = $k . '. ' . $ch->name;

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
    public function index()
    {
        $list = $this->category->fetchCategories()->get();
        // echo $total;
        $result = [];
        $categories = [];
        $i = 1;

        foreach ($list as $key => $value) {
            $categories[$value->id] = $i . ". " . $value->name;
            $root = $value;
            $categories = $this->setListIndex($root, $categories, $i, 1);
            $i++;
        }
        return view('products.index', compact('categories'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function productsAjax(Request $request)
    {
        if (isset($request->search)) {
            $request->search = $request->search['value'];
        }
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->product->fetchProducts($request, $this->columns);
        $total = $records->count();
        if (isset($request->start)) {
            $products = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $products = $records->offset($request->start)->limit($total)->get();
        }
        $result = [];
        $no = 1;
        // print_r($products);die();
        foreach ($products as $product) {
            $data = [];
            $data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $product->id . '"><i class="input-helper"></i></label></div>';
            $data['brand_id'] = $product->brand->name ?? '-';
            $data['product_code'] = $product->product_code;
            $data['category_id'] = ($product->category != null) ? $product->category->name : $product->subcategory->parentCat->name;
            $data['subcategory_id'] = ($product->subcategory != null) ? $product->subcategory->name : '-';
            $data['tax_id'] = ($product->tax_id != null) ? $product->tax->name . "(" . $product->tax->value . ")" : '-';
            $data['name'] = $product->name;
            $data['image'] = ($product->image != null) ? URL::asset('/uploads/products/' . $product->image) : '-';
            $data['description'] = ($product->description != null) ? $product->description : '-';
            $data['manage_stock'] = ucfirst(config('constants.CONFIRM.' . $product->manage_stock));
            $data['quick_grab'] = ucfirst(config('constants.CONFIRM.' . $product->quick_grab));
            $data['is_exclusive'] = ucfirst(config('constants.CONFIRM.' . $product->is_exclusive));
            $data['type'] = ucfirst(config('constants.PRODUCT_TYPE.' . $product->type));
            $data['status'] = ucfirst(config('constants.STATUS.' . $product->status));
            $data['sno'] = $no++;

            $variations = '';
            $count = 1;
            foreach ($product->variations as $key => $value) {
                $variations .= "Variation " . $count++ . ": \n";
                $variations .= "Quantity: " . $value->qty . "\n";
                $variations .= "Maximum Quantity: " . $value->max_qty . "\n";
                $variations .= "Price: " . Session::get('currency') . $value->price . "\n";
                $variations .= "Special Price: " . (($value->special_price != null) ? Session::get('currency') . $value->special_price : '-') . "\n\n";
            }
            
            $data['variations'] = $variations;
            $data['fav_count'] = $product->active_favs != null ? count($product->active_favs) : 0;

            if (Helper::checkAccess(route('changeStatusProduct'))) {
                $data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($product->status == 'AC' ? ' checked' : '') . ' data-id="' . $product->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusProduct"></div></div>';
            } else {
                $data['activate'] = ucfirst(config('constants.STATUS.' . $product->status));
            }
            $action = '';

            if (Helper::checkAccess(route('editProduct'))) {
                $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editProduct', ['id' => $product->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            }
            if (Helper::checkAccess(route('viewProduct'))) {
                $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewProduct', ['id' => $product->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
            }
            if (Helper::checkAccess(route('deleteProduct'))) {
                $action .= '&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="toolTip deleteProduct" data-toggle="tooltip" data-id="' . $product->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
            }
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
        $url = route('addProduct');
        $product = new Product;
        $categories = $this->categories;
        $brands = $this->brands;
        $taxes = $this->taxes;
        $units = $this->units;
        $attributes = ModelAttribute::where('name', '!=', '')->where('status', 'AC')->get();
        return view('products.create', compact('type', 'url', 'product', 'categories', 'brands', 'taxes', 'units', 'attributes'));
    }

    /**
     * check for unique name during adding new product
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function attributeOptionGet(Request $request)
    {
        // dd($request->all());
        $attr_html = '';
        $html = '';
        $dummy_html = '';
        if (isset($request->option) && $request->option != null) {

            $attributes = ModelAttribute::whereIn('id', $request->option)->where('status', 'AC')->get();
            if (count($attributes) > 0) {
                foreach ($attributes as $key => $value) {
                    $attr_html .= '<div class="form-group col-md-4 m-t-20 ">
							<label>' . ucfirst($value->name) . '<sup class="text-reddit"> *</sup></label>
							<select class="form-control" data-rule-required="true" name="prod' . strtolower($value->slug) . '_1" id="prod' . strtolower($value->slug) . '_1">';
                    if (isset($value->options) && !empty($value->options)) {
                        $attr_html .= '<option value="">Select ' . ucfirst($value->name) . '</option>';
                        foreach ($value->options as $k => $v) {
                            $attr_html .= '<option value="' . $v->id . '">' . ($v->value) . '</option>';
                        }
                    }
                    $attr_html .= '</select>
						</div>';
                }

                if ($request->type == 'edit') {
                    $variations = ProductVariation::where('product_id', $request->product_id)->get();

                    foreach ($variations as $key => $variant) {
                        $attribute_html = '';
                        $key = $key + 1;

                        $all_attributes = ModelAttribute::where('status', 'AC')->get();

                        if ($request->option) {
                            // $pre_attributes = $all_attributes->merge(Attribute::whereIn('id', $request->option)->get());
                            $pre_attributes = ModelAttribute::whereIn('id', $request->option)->where('status', 'AC')->get();
                        } else {
                            $pre_attributes = ModelAttribute::where('status', 'AC')->get();
                        }

                        foreach ($pre_attributes as $value) {
                            $attribute_html .= '<div class="form-group col-md-4 m-t-20 ">
							<label>' . ucfirst($value->name) . '<sup class="text-reddit"> *</sup></label>
							<select class="form-control" data-rule-required="true" name="prod' . strtolower($value->name) . '_' . $key . '" id="prod' . strtolower($value->slug) . '_' . $key . '">';

                            if ($variant->options != null) {
                                if (in_array($value->id, $variant->options->pluck('attribute_id')->toArray())) {
                                    if (isset($value->options) && !empty($value->options)) {
                                        $attribute_html .= '<option value="">Select ' . ucfirst($value->name) . '</option>';
                                        foreach ($value->options as $k => $v) {
                                            if (in_array($v->id, $variant->options->pluck('attribute_value_id')->toArray())) {
                                                $attribute_html .= '<option value="' . $v->id . '" selected>' . ($v->value) . '</option>';
                                            } else {
                                                $attribute_html .= '<option value="' . $v->id . '">' . ($v->value) . '</option>';
                                            }
                                        }
                                    }
                                } else {
                                    if (isset($value->options) && !empty($value->options)) {
                                        $attribute_html .= '<option value="">Select ' . ucfirst($value->name) . '</option>';
                                        foreach ($value->options as $k => $v) {
                                            $attribute_html .= '<option value="' . $v->id . '">' . ($v->value) . '</option>';
                                        }
                                    }
                                }
                            } else {
                                if (isset($value->options) && !empty($value->options)) {
                                    $attribute_html .= '<option value="">Select ' . ucfirst($value->name) . '</option>';
                                    foreach ($value->options as $k => $v) {
                                        $attribute_html .= '<option value="' . $v->id . '">' . ($v->value) . '</option>';
                                    }
                                }
                            }
                            $attribute_html .= '</select>
							</div>';
                        }

                        if (count($variations) == 1) {
                            $display = 'style=display:none';
                        } else {
                            $display = '';
                        }
                        $html .= '<div class="variantRow" id="row_' . $key . '">
							<h6 class="text-danger" id="proderror_' . $key . '" style="display: none;">Size for this variant cannnot be edited/deleted. Cart/Order has been placed.</h6>
							<div class="float-right" ' . $display . '>
								<a href="javascript:void(0)" class="deleteRow" id="deleterow_' . $key . '" ><i class="fa fa-trash" aria-hidden="true"></i></a>
							</div><br>
							<input type="hidden" name="prodvarid_' . $key . '" value="' . $variant->id . '">
							<div class="row">' . $attribute_html . '

								<div class="form-group col-md-4 m-t-20 ">
									<label>Quantity</label><sup class="text-reddit"> *</sup>
									<input type="text" class="form-control form-control-line numberInput" name="prodqty_' . $key . '" value="' . $variant->qty . '" placeholder="Please enter quantity" maxlength="10">
								</div>
								<div class="form-group col-md-4 m-t-20 ">
									<label>Maximum Quantity</label><sup class="text-reddit"> *</sup>
									<input type="text" class="form-control form-control-line numberInput" name="prodmaxqty_' . $key . '" value="' . $variant->max_qty . '" placeholder="Please enter max quantity" maxlength="10">
								</div>

								<div class="form-group col-md-4 m-t-20 ">
									<label>MRP</label><sup class="text-reddit"> *</sup>
									<input type="text" class="form-control form-control-line numberInput" name="prodmrp_' . $key . '" value="' . $variant->price . '" placeholder="Please enter mrp" maxlength="10">
								</div>
								<div class="form-group col-md-4 m-t-20 ">
									<label>Special Price</label>
									<input type="text" class="form-control form-control-line numberInput" name="prodspecial_' . $key . '" value="' . $variant->special_price . '" maxlength="10" placeholder="Please enter special price">
								</div>

								<input type="hidden" name="prodstatus_' . $key . '" value="' . $variant->status . '">
								<div class="form-group bt-switch col-md-6 m-t-20 p-0">
									<label class="col-md-4">Status</label>
									<div class="col-md-3" style="float: right;">
										<input type="checkbox" data-on-color="success" checked data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-prodstatus_' . $key . '" class="prodStatus" id="prodstatus_' . $key . '">
									</div>
								</div>
							</div>
						</div>';
                    }
                } else {
                    $html .= '<div class="variantRow" id="row_1">
						<h6 class="text-danger" id="proderror_1" style="display: none;">Size for this variant cannnot be edited/deleted. Cart/Order has been placed.</h6>
						<div class="float-right">
							<a href="javascript:void(0)" class="deleteRow" id="deleterow_1"><i class="fa fa-trash" aria-hidden="true"></i></a>
						</div><br>
						<input type="hidden" name="prodvarid_1" value="0">
						<div class="row">' . $attr_html . '

							<div class="form-group col-md-4 m-t-20 ">
								<label>Quantity</label><sup class="text-reddit"> *</sup>
								<input type="text" class="form-control form-control-line numberInput" name="prodqty_1" value="" placeholder="Please enter quantity" maxlength="10">
							</div>
							<div class="form-group col-md-4 m-t-20 ">
								<label>Maximum Quantity</label><sup class="text-reddit"> *</sup>
								<input type="text" class="form-control form-control-line numberInput" name="prodmaxqty_1" value="" placeholder="Please enter max quantity" maxlength="10">
							</div>

							<div class="form-group col-md-4 m-t-20 ">
								<label>MRP</label><sup class="text-reddit"> *</sup>
								<input type="text" class="form-control form-control-line numberInput" name="prodmrp_1" value="" placeholder="Please enter mrp" maxlength="10">
							</div>
							<div class="form-group col-md-4 m-t-20 ">
								<label>Special Price</label>
								<input type="text" class="form-control form-control-line numberInput" name="prodspecial_1" value="" maxlength="10" placeholder="Please enter special price">
							</div>

							<input type="hidden" name="prodstatus_1" value="AC">

							<div class="form-group bt-switch col-md-6 m-t-20 p-0">
								<label class="col-md-4">Status</label>
								<div class="col-md-3" style="float: right;">
									<input type="checkbox" data-on-color="success" checked data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-prodstatus_1" class="prodStatus" id="prodstatus_1">
								</div>
							</div>
						</div>
					</div>';
                }
                $dummy_html = '<div class="variantRow" id="row_1">
                        <h6 class="text-danger" id="proderror_1" style="display: none;">Size for this variant cannnot be edited/deleted. Cart/Order has been placed.</h6>
                        <div class="float-right">
                            <a href="javascript:void(0)" class="deleteRow" id="deleterow_1"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </div><br>
                        <input type="hidden" name="prodvarid_1" value="0">
                        <div class="row">' . $attr_html . '

                            <div class="form-group col-md-4 m-t-20 ">
                                <label>Quantity</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line numberInput" name="prodqty_1" value="" placeholder="Please enter quantity" maxlength="10">
                            </div>
                            <div class="form-group col-md-4 m-t-20 ">
                                <label>Maximum Quantity</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line numberInput" name="prodmaxqty_1" value="" placeholder="Please enter max quantity" maxlength="10">
                            </div>

                            <div class="form-group col-md-4 m-t-20 ">
                                <label>MRP</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line numberInput" name="prodmrp_1" value="" placeholder="Please enter mrp" maxlength="10">
                            </div>
                            <div class="form-group col-md-4 m-t-20 ">
                                <label>Special Price</label>
                                <input type="text" class="form-control form-control-line numberInput" name="prodspecial_1" value="" maxlength="10" placeholder="Please enter special price">
                            </div>

                            <input type="hidden" name="prodstatus_1" value="AC">

                            <div class="form-group bt-switch col-md-6 m-t-20 p-0">
                                <label class="col-md-4">Status</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" data-on-color="success" checked data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-prodstatus_1" class="prodStatus" id="prodstatus_1">
                                </div>
                            </div>
                        </div>
                    </div>';
            }
        }

        return ['html' => $html, 'dummy_html' => $dummy_html];

    }

    public function checkProduct(Request $request, $id = null)
    {
        if (isset($request->product_name)) {
            $check = Product::where('name', $request->product_name);
            if (isset($id) && $id != null) {
                $check = $check->where('id', '!=', $id);
            }
            if (isset($request->category) && $request->category != null) {
                $check = $check->where('parent_id', $request->category);
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
        // dd($request->all());
        $validate = Validator($request->all(), [
            'product_name' => 'required',
        ]);
        $attr = [
            'product_name' => 'Product Name',
            'product_image' => 'Product Image',
            'category_id' => 'Category',
            'prod_description' => 'Description',
        ];

        $validate->setAttributeNames($attr);

        if ($validate->fails()) {
            return redirect()->route('createProduct')->withInput($request->all())->withErrors($validate);
        } else {
            try {

                $product = new Product;
                $product->product_code = Helper::generateNumber('products', 'product_code');
                $product->name = $request->post('product_name');
                $product->description = $request->post('prod_description');
                $filename = "";
                // check profile_picture key exist or not
                if ($request->hasfile('product_image')) {
                    $file = $request->file('product_image');
                    $filename = time() . $file->getClientOriginalName();
                    $filename = str_replace(' ', '', $filename);
                    $filename = str_replace('.jpeg', '.jpg', $filename);
                    $file->move(public_path('uploads/products'), $filename);

                    //store image thumbnails
                    $path = 'uploads/products/';
                    $filePath = public_path('uploads/products/' . $filename);
                    $thumbnail_name = 'small-' . $filename;
                    $img = Image::make($filePath)->resize(150, 100, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $img->save(public_path($path . 'small/' . $thumbnail_name));

                    // medium
                    $thumbnail_name = 'medium-' . $filename;
                    $img = Image::make($filePath)->resize(300, 185, function ($constraint) {
                        $constraint->aspectRatio();
                    });

                    $img->save(public_path($path . 'medium/' . $thumbnail_name));

                    Helper::compress_image(public_path('uploads/products/' . $filename), Notify::getBusRuleRef('image_quality'));
                }
                if ($filename != "") {
                    $product->image = $filename;
                }
                $category = explode('-', $request->category_id);
                $product->parent_id = count($category) > 1 ? $category[1] : $category[0];
                $product->status = trim($request->post('status'));
                $product->quick_grab = trim($request->post('quick_grab'));
                $product->is_exclusive = trim($request->post('is_exclusive'));
                $product->created_at = date('Y-m-d H:i:s');

                $count = 0;
                foreach ($request->all() as $key => $value) {
                    if (stripos($key, 'prodvar') !== false) {
                        $count++;
                    }

                }

                if ($count > 0) {
                    //     dd('1');

                    if ($product->save()) {
                        ProductVariation::saveVariations($count, $request->all(), $product->id);
                        $request->session()->flash('success', 'Product added successfully');
                        return redirect()->route('products');
                    } else {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('products');
                    }
                } else {
                    $request->session()->flash('error', 'Please add variations.');
                    return redirect()->route('products');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('products');
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
            $product = Product::where('id', $id)->first();
            if (isset($product->id)) {

                $attributes = ModelAttribute::where('status', 'AC')->whereIn('id', $product->prod_attr()->groupBy('attribute_id')->pluck('product_attribute_variations.attribute_id')->toArray())->pluck('name', 'id')->toArray();
                return view('products.view', compact('product', 'attributes'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('products');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('products');
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
            $product = Product::where('id', $id)->first();
            if (isset($product->id)) {
                // if (count($product->cart_items) <= 0) {
                // if (count($product->order_items) <= 0) {
                $type = 'edit';
                $url = route('updateProduct', ['id' => $product->id]);
                $categories = $this->categories;
                $brands = $this->brands;
                $taxes = $this->taxes;
                $units = $this->units;
                $attr_html = '';
                // $units = $this->units;
                $attributes = ModelAttribute::where('name', '!=', '')->where('status', 'AC')->get();
                $option = $product->prod_attr()->groupBy('attribute_id')->pluck('product_attribute_variations.attribute_id')->toArray();

                $attributes1 = ModelAttribute::whereIn('id', $option)->where('status', 'AC')->get();

                if (count($attributes1) > 0) {
                    foreach ($attributes1 as $key => $value) {
                        $attr_html .= '<div class="form-group col-md-4 m-t-20 ">
                               <label>' . ucfirst($value->name) . '</label><sup class="text-reddit"> *</sup>
                               <select class="form-control" name="prod' . strtolower($value->slug) . '_1" id="prod' . strtolower($value->slug) . '_1">';
                        if (isset($value->options) && !empty($value->options)) {
                            $attr_html .= '<option value="">Select ' . ucfirst($value->name) . '</option>';
                            foreach ($value->options as $k => $v) {
                                $attr_html .= '<option value="' . $v->id . '">' . ($v->value) . '</option>';
                            }
                        }
                        $attr_html .= '</select>
                           </div>';
                    }
                }

                return view('products.create', compact('product', 'type', 'url', 'categories', 'brands', 'taxes', 'units', 'attributes', 'attr_html', 'attributes1'));
                //     } else {
                //         $request->session()->flash('error', 'This product cannot be edited. Orders have been placed.');
                //         return redirect()->route('products');
                //     }
                // } else {
                //     $request->session()->flash('error', 'This product cannot be edited. Cart has been created.');
                //     return redirect()->route('products');
                // }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('products');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('products');
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
            $product = Product::where('id', $id)->first();
            if (isset($product->id)) {
                $validate = Validator($request->all(), [
                    'product_name' => 'required',
                    'category_id' => 'required',
                    'prod_description' => 'required',
                ]);

                $attr = [
                    'product_name' => 'Product Name',
                    'category_id' => 'Category',
                    'prod_description' => 'Description',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('editProduct', ['id' => $product->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        $product->name = $request->post('product_name');
                        $product->description = $request->post('prod_description');
                        $filename = "";
                        // check profile_picture key exist or not
                        if ($request->hasfile('product_image')) {
                            $file = $request->file('product_image');
                            $filename = time() . $file->getClientOriginalName();
                            $filename = str_replace(' ', '', $filename);
                            $filename = str_replace('.jpeg', '.jpg', $filename);
                            $file->move(public_path('uploads/products'), $filename);

                            //store image thumbnails
                            $path = 'uploads/products/';
                            $filePath = 'uploads/products/' . $filename;
                            $thumbnail_name = 'small-' . $filename;
                            $img = Image::make($filePath)->resize(150, 100, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                            $img->save($path . 'small/' . $thumbnail_name);

                            // // medium
                            // $thumbnail_name = 'medium-' . $filename;
                            // $img = Image::make($filePath)->resize(300, 185, function ($constraint) {
                            //     $constraint->aspectRatio();
                            // });
                            // $img->save($path . 'medium/' . $thumbnail_name);
                            // Helper::compress_image(public_path('uploads/products/' . $filename), Notify::getBusRuleRef('image_quality'));
                            // if ($product->image != null && file_exists(public_path('uploads/products/' . $product->image))) {
                            //     unlink(public_path('uploads/products/' . $product->image));
                            // }
                        }
                        if ($filename != "") {
                            $product->image = $filename;
                        }

                        $category = explode('-', $request->category_id);
                        $product->parent_id = count($category) > 1 ? $category[1] : $category[0];
                        $product->status = trim($request->post('status'));
                        $product->quick_grab = trim($request->post('quick_grab'));
                        $product->is_exclusive = trim($request->post('is_exclusive'));
                        $count = 0;
                        foreach ($request->all() as $key => $value) {
                            if (stripos($key, 'prodvar') !== false) {
                                $count++;
                            }

                        }
                        if ($count > 0) {
                            if ($product->save()) {
                                ProductVariation::saveVariations($count, $request->all(), $product->id);
                                $request->session()->flash('success', 'Product updated successfully');
                                return redirect()->route('products');
                            } else {
                                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                                return redirect()->route('products');
                            }
                        } else {
                            $request->session()->flash('error', 'Please add variations.');
                            return redirect()->route('products');
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('products');
                    }

                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('products');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('products');
        }

    }

    // activate/deactivate product
    public function updateStatus(Request $request)
    {

        if (isset($request->statusid) && $request->statusid != null) {
            $product = Product::find($request->statusid);

            if (isset($product->id)) {
                $product->status = $request->status;
                if (isset($request->quick_grab)) {
                    $product->quick_grab = $request->quick_grab;
                }

                if (isset($request->is_exclusive)) {
                    $product->is_exclusive = $request->is_exclusive;
                }

                if ($product->save()) {
                    $request->session()->flash('success', 'Product updated successfully.');
                    return redirect()->back();
                } else {
                    $request->session()->flash('error', 'Unable to update product. Please try again later.');
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

    // activate/deactivate product
    public function updateStatusAjax(Request $request)
    {

        if (isset($request->statusid) && $request->statusid != null) {
            $product = Product::find($request->statusid);

            if (isset($product->id)) {
                $product->status = $request->status;
                if ($product->save()) {
                    echo json_encode(['status' => 1, 'message' => 'Product updated successfully.']);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Unable to update product. Please try again later.']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid Product']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid Product']);
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
            $product = Product::find($request->deleteid);

            if (isset($product->id)) {
                $product->status = 'DL';
                if ($product->save()) {
                    ProductVariation::where('product_id', $product->id)->update(['status' => 'DL']);
                    echo json_encode(['status' => 1, 'message' => 'Product deleted successfully.']);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Unable to delete product. Please try again later.']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid Product']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid Product']);
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

        if (isset($request->deleteid) && $request->deleteid != null) {
            $deleteid = explode(',', $request->deleteid);
            $ids = count($deleteid);
            $count = 0;
            foreach ($deleteid as $id) {
                $product = Product::find($id);

                if (isset($product->id)) {
                    $product->status = 'DL';
                    if ($product->save()) {
                        ProductVariation::where('product_id', $product->id)->update(['status' => 'DL']);
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Products deleted successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all products were deleted. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
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
                $product = Product::find($id);

                if (isset($product->id)) {
                    if ($product->status == 'AC') {
                        $product->status = 'IN';
                    } elseif ($product->status == 'IN') {
                        $product->status = 'AC';
                    }

                    if ($product->save()) {
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Products updated successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all products were updated. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
    }

    /**
     * Show the form for importing product sheet
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $url = route('importProducts');
        if (strtolower($request->method()) == 'post') {
            $validate = Validator($request->all(), [
                'product_import' => 'required',
            ]);

            $attr = [
                'product_import' => 'File',
            ];

            $validate->setAttributeNames($attr);

            if ($validate->fails()) {
                return redirect()->route('importProducts')->withInput($request->all())->withErrors($validate);
            } else {
                Excel::import(new ProductImport, request()->file('product_import'));
            }

        }

        return view('products.import', compact('url'));
    }

    public function exportView()
    {
        return view('products.export');
    }

    public function export(Request $request)
    {
        if ($request->id == null) {
            $id = ['all'];
        } else {
            $id = explode(',', $request->id);
        }
        return Excel::download(new ProductExport($id), 'products.xlsx');
    }

    public function bulkUpdate(Request $request)
    {
        $validate = Validator($request->all(), [
            'product_update' => 'required',
        ]);

        $attr = [
            'product_update' => 'File',
        ];

        $validate->setAttributeNames($attr);

        if ($validate->fails()) {
            return redirect()->route('exportView')->withInput($request->all())->withErrors($validate);
        } else {
            Excel::import(new ProductUpdate, request()->file('product_update'));
            return redirect()->route('exportView');
        }
    }
}
