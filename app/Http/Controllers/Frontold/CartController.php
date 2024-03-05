<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\ApiController as ApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MyController as MyController;
use App\Model\BusRuleRef;
use App\Model\Country;
use App\Model\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{

    public function __construct(Request $request)
    {
        $this->api = (new ApiController($request));
        $this->function = (new MyController($request));
    }

    public function index(Request $request)
    {

        // CouponCode::where(DB::raw("strftime('%Y-%m', created_at) = '2010-1'"))->get();

        $data = Auth::guard('front')->user()->load('user_addresses');
        $requestData = $request->merge(['user_id' => $data->id]); //converting into request for api

        $countryRef = BusRuleRef::where('rule_name', 'app_country')->first();

        $countries = Country::where('name', $countryRef->rule_value)->get();

        $stateList = $this->api->stateList($requestData);

        $stateList = isset($stateList['data']) ? $stateList['data'] : null;

        $currency = $this->function->currency;

        $items = json_decode($this->api->getCartItems($requestData));

        // dd($items);

        if ($items->status == 200) {
            $user = $data;
            $usercartsitem = $items->data;
            $wallet = $items->wallet;
            $total_amount = $items->total_amount;
            $coupon_discount = $items->coupon_discount;
            $shipping_fee = $items->shipping_fee;
            $grand_total = $items->grand_total;
            $savings = $items->savings;
            $credit_usage = $items->credit_usage;
            $credits_used = $items->credits_used;
            $payment_key = $items->payment_key;
            $code = isset($items->code) ? $items->code : null;
            $cart_address = isset($items->address) ? $items->address : null;
            return view('front.cart.index', compact('currency', 'usercartsitem', 'wallet', 'total_amount', 'coupon_discount', 'shipping_fee', 'grand_total', 'savings', 'credit_usage', 'credits_used', 'payment_key', 'countries', 'stateList', 'code', 'cart_address', 'user'));
        } else {
            $user = $data;
            return view('front.cart.index', compact('user'));
        }
    }

    public function setAddress(Request $request)
    {

        $result = $this->api->setAddress($request);
        $request->session()->flash('success', $result['message']);
        return redirect()->back();
    }

    public function setCartWallet(Request $request)
    {
        $cart = $this->api->setCartWallet($request);
        $message = $cart['message'];
        $status = $cart['status'];

        if ($status == 200) {
            $items = json_decode($this->api->getCartItems($request));
            if ($items->status == 200) {
                $currency = $items->currency;
                $total_amount = $items->total_amount;
                $shipping_fee = $items->shipping_fee;
                $coupon_discount = $items->coupon_discount;
                $credits_used = $items->credits_used;
                $grand_total = $items->grand_total;
                $savings = $items->savings;
            }

            $view = view("front.cart.partial-cart_details", compact('currency', 'total_amount', 'shipping_fee', 'coupon_discount', 'credits_used', 'grand_total', 'savings'))->render();
            return response()->json(['status' => $status, 'message' => $message, 'html' => $view]);
        } else {
            return response()->json(['status' => $status, 'message' => $message]);
        }
    }

    public function addToCartProduct(Request $request)
    {
        $cart = $this->api->addToCart($request);
        $message = $cart['message'];
        $status = $cart['status'];
        if ($status == 200) {
            $items = json_decode($this->api->getCartItems($request));
            if ($items->status == 200) {
                $currency = $items->currency;
                $total_amount = $items->total_amount;
                $shipping_fee = $items->shipping_fee;
                $coupon_discount = $items->coupon_discount;
                $credits_used = $items->credits_used;
                $grand_total = $items->grand_total;
                $savings = $items->savings;
            }

            $view = view("front.cart.partial-cart_details", compact('currency', 'total_amount', 'shipping_fee', 'coupon_discount', 'credits_used', 'grand_total', 'savings'))->render();
            return response()->json(['status' => $status, 'message' => $message, 'html' => $view]);
        } else {
            return response()->json(['status' => $status, 'message' => $message]);
        }
    }

    public function applyCouponCode(Request $request)
    {

        $coupon = $this->api->applyCouponCode($request);
        $message = $coupon['message'];
        $status = $coupon['status'];

        if ($status == 200) {
            // $code = $coupon['code'];
            $items = json_decode($this->api->getCartItems($request));
            if ($items->status == 200) {
                $currency = $items->currency;
                $total_amount = $items->total_amount;
                $shipping_fee = $items->shipping_fee;
                $coupon_discount = $items->coupon_discount;
                $credits_used = $items->credits_used;
                $grand_total = $items->grand_total;
                $savings = $items->savings;
                $code = isset($items->code) ? $items->code : null;
            }
            $view = view("front.cart.partial-cart_details", compact('currency', 'total_amount', 'shipping_fee', 'coupon_discount', 'credits_used', 'grand_total', 'savings'))->render();

            return response()->json(['status' => $status, 'message' => $message, 'html' => $view, 'code' => $code]);
        } else {
            $code = null;
            return response()->json(['status' => $status, 'message' => $message, 'code' => $code]);
        }
    }

    public function getCartItem_ajax(Request $request)
    {
        $user_id = isset(auth()->guard('front')->user()->id) ? auth()->guard('front')->user()->id : 0;
        $usercartsitem = [];
        $requestData = $request->merge(['user_id' => $user_id]);
        $items = json_decode($this->api->getCartItems($requestData));
        $usercartsitem = $items->data;
        return view('front.cart.ajaxCart', compact('usercartsitem'));
    }

}
