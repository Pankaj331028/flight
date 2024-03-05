<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\ApiController as ApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MyController as MyController;
use App\Model\BusRuleRef;
use App\Model\Category;
use App\Model\Product;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB as FacadesDB;
use URL;

class ProductController extends Controller
{
    public function __construct(Request $request)
    {
        $this->api = (new ApiController($request));
        $this->function = (new MyController($request));
    }

    public function index(Request $request, $id)
    {
        $pagelength = BusRuleRef::where('rule_name', 'page_length')->first()->rule_value;
        $currency = BusRuleRef::where('rule_name', 'currency')->first()->rule_value;
        $products = [];
        $category = Category::find($id);

        if (Auth::guard('front')->user()) {
            $user_id = Auth::guard('front')->user()->id;

        } else {
            $user_id = '';

        }

        $usercartsitem = [];
        $requestData = $request->merge(['user_id' => $user_id]);
        if (Auth::guard('front')->user()) {
            $items = json_decode($this->api->getCartItems($requestData));
            if ($items->status == 200) {
                $usercartsitem = $items->data;
            }
        }

        if (isset($category)) {
            $products = Product::whereHas('prod_attr')->with(['variations' => function ($q) {
                $q->where('status', 'AC');
                $q->orderBy('price', 'asc');
            }])->select('*', DB::raw("CONCAT('" . URL::asset("uploads/products/medium") . "/medium-', image) as medium_image, image"),
                DB::raw("CONCAT('" . URL::asset("uploads/products") . "/', image) image"))
                ->where('status', 'AC')->whereHas('variations', function ($q) {
                $q->where('status', 'AC');
            })->where(function ($q) use ($category, $id) {
                $q->where('parent_id', $id);
                if (count($category->childCat) > 0) {

                    $q->orWhereIN('parent_id', array_column($category->childCat->toArray(), 'id'));
                }
            })->where('status', 'AC')->orderBy('created_at', 'desc')->paginate($pagelength);

            $user_id = isset(auth()->guard('front')->user()->id) ? auth()->guard('front')->user()->id : 0;
            $products = $this->function->formatProducts($products, $user_id);

            $banner = URL::asset('/uploads/categories/' . $category->banner);
            $cart_count = $this->function->getCartCount($user_id);

            if ($products->count() > 0) {
                return view('front.products.index', compact('banner', 'currency', 'cart_count', 'products', 'category', 'user_id', 'usercartsitem'));
            } else {
                return view('front.products.index', compact('banner', 'cart_count', 'category', 'user_id', 'products', 'usercartsitem'));
            }
        } else {
            $banner = '';
            return view('front.products.index', compact('banner', 'products', 'usercartsitem'));
        }
    }

    public function show(Request $request, $type, $id)
    {
        $user_id = isset(auth()->guard('front')->user()->id) ? auth()->guard('front')->user()->id : 0;
        $requestData = $request->merge(['type' => $type, 'id' => $id, 'user_id' => $user_id]);
        $item = $this->api->getProductDescription($requestData);
        if($item['status'] == 200)
        {
            $product = $item['data'];
            $currency = $item['currency'];
            $offer = $this->api->homePageData($requestData);
            $quick_grabs = isset($offer['quick_grabs']) ? $offer['quick_grabs'] : null;
            return view('front.products.view', compact('product', 'currency', 'id', 'type', 'quick_grabs'));
        }else{
            session()->flash('error', 'Product not found');
            return redirect('/');
        }
    }
        
    public function productFilter(Request $request)
    {
        FacadesDB::enableQueryLog();
        $query = Product::where('status', 'AC');
        $query = Product::whereHas('prod_attr')->select('products.*', DB::raw("CONCAT('" . URL::asset("uploads/products/medium") . "/medium-', image) as medium_image, image"),
            DB::raw("CONCAT('" . URL::asset("uploads/products") . "/', image) image"))->with(["variations" => function ($q) use ($request) {
            $q->where('special_price', '>=', $request->min_price)->where('special_price', '<=', $request->max_price);
        }])->Join('product_variations', function ($join) {
            $join->on('products.id', '=', 'product_variations.product_id');
        });

        if (isset($request->type) && $request->type != '' && $request->type == 'is_exclusive') {
            $query->where('products.is_exclusive', '1');
        }

        if (isset($request->keyword) && $request->keyword != '') {
            $query->where(function ($q) use ($request) {
                $q->where('products.name', 'like', '%' . $request->keyword . '%');
                $q->where('products.description', 'like', '%' . $request->keyword . '%');
            });
        }

        if (isset($request->type) && $request->type != '' && $request->type == 'quick_grab') {
            $query->where('products.quick_grab', '1');
        }

        if (isset($request->categories_id) && count($request->categories_id) > 0) {
            $query->whereIn('products.parent_id', $request->categories_id);
        }

        if (isset($request->attributesArray) && count($request->attributesArray) > 0) {
            $query->wherehas('prod_attr', function ($q) use ($request) {
                $q->whereIn('attribute_value_id', $request->attributesArray);
            });
        }

        if (isset($request->min_price) && isset($request->max_price)) {
            $query->wherehas('variations', function ($q) use ($request) {
                $q->where('special_price', '>=', $request->min_price)->where('special_price', '<=', $request->max_price);
            });
        }

        //sorting data
        if (isset($request->order_by) && $request->order_by == 'price_low_to_high') {
            $query->orderBy('product_variations.special_price', 'asc');

        } elseif (isset($request->order_by) && $request->order_by == 'price_high_to_low') {
            $query->orderBy('product_variations.special_price', 'desc');

        } elseif (isset($request->order_by) && $request->order_by == 'name_asc') {
            $query->orderBy('products.name', 'asc');

        } elseif (isset($request->order_by) && $request->order_by == 'name_desc') {
            $query->orderBy('products.name', 'desc');
        } else {
            $query->orderBy('products.created_at', 'desc');
        }

        $product = $query->groupBy('products.id')->get();

        // dd(FacadesDB::getQueryLog());

        $user_id = isset(auth()->guard('front')->user()->id) ? auth()->guard('front')->user()->id : 0;
        $products = $this->function->formatProducts($product, $user_id);

        // dd($products);

        return view('front.products.productResult', compact('products'));
    }

}
