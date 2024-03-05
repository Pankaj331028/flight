<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\ApiController as ApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MyController as MyController;
use Auth;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(Request $request)
    {
        $this->api = (new ApiController($request));
        $this->function = (new MyController($request));
    }

    public function index(Request $request, $id)
    {
        $requestData = $request->merge(['user_id' => Auth::guard('front')->user()->id, 'order_id' => $id]);
        $item = $this->api->getOrderDetails($requestData);
        return view('front.orders.order-detail', compact('item'));
    }

    public function trackOrderByItem(Request $request, $order_id, $item_id = null)
    {
        $requestData = $request->merge(['user_id' => Auth::guard('front')->user()->id, 'order_id' => $order_id, 'item_id' => $item_id]);
        $item = $this->api->trackOrder($requestData, $order_id, $item_id);
        return view('front.orders.track-order', compact('item', 'order_id', 'item_id'));
    }

    //trak order by order_no
    public function trackOrderByNo(Request $request)
    {
        return view('front.orders.track-order-no');
    }

    public function trackOrder(Request $request)
    {
        $validate = Validator($request->all(), [
            'order_no' => 'required',
        ]);

        $attr = [
            'order_no' => 'Order Number',
        ];
        $validate->setAttributeNames($attr);
        if ($validate->fails()) {
            $errors = $validate->errors();
            $request->session()->flash('error', $errors->first());
            return redirect()->back();
        } else {
            $item = $this->api->trackOrderNo($request, $request->order_id, $request->item_id);
            if ($item['status'] == 200) {
                $item = ($item['status'] == 200) ? $item : null;
                return view('front.orders.track-order', compact('item'));
            } else {
                $request->session()->flash('error', $item['message']);
                return redirect()->back();
            }
        }

    }

    public function getStatus(Request $request, $id)
    {
        $requestData = $request->merge(['order_id' => $id, 'user_id' => Auth::guard('front')->user()->id]);
        $item = $this->api->getOrderStatus($requestData);
        return view('front.orders.order-status', compact('item'));
    }

    public function getStatusDetail(Request $request, $id, $item_id)
    {
        $requestData = $request->merge(['order_id' => $id, 'user_id' => Auth::guard('front')->user()->id, 'item_id' => $item_id]);
        $item = $this->api->getOrderItemStatus($requestData);
        if ($item['status'] == 200) {
            $currency = $item['currency'];
            $product = $item['products'];
            $schedules = $item['schedules'];
            $order = $item['order'];
        }
        return view('front.orders.order-status-detail', compact('currency', 'product', 'schedules', 'order', 'id'));
    }

    public function getProductDateWise(Request $request)
    {
        $item = $this->api->getDateProducts($request);
        $view = view("front.orders.partial-ordered-product", compact('item'))->render();
        return response()->json(['message' => $item['status'], 'status' => $item['status'], 'html' => $view]);
    }
}
