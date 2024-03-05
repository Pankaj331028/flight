<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\ApiController as ApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MyController as MyController;
use Illuminate\Http\Request;
use View;

class OfferController extends Controller
{
    public function __construct(Request $request)
    {
        $this->api = (new ApiController($request));
        $this->function = (new MyController($request));
    }

    //list of offers redirected from home page
    public function details(Request $request, $type, $id)
    {
        if ($type == 'top_offer') {
            $user_id = isset(auth()->guard('front')->user()->id) ? auth()->guard('front')->user()->id : 0;
            $requestData = $request->merge(['offer_id' => $id, 'page_no' => 0, 'user_id' => $user_id]);

            $products = $this->api->getOfferDetails($requestData);

            $currency = isset($products['currency']) ? $products['currency'] : null;
            $products = isset($products['data']) ? $products['data'] : [];
        } else if ($type == 'offer') {
            $user_id = isset(auth()->guard('front')->user()->id) ? auth()->guard('front')->user()->id : 0;
            $requestData = $request->merge(['top_offer' => $id, 'page_no' => 0, 'user_id' => $user_id]);

            $products = $this->api->getAllOffers($requestData);

            $currency = isset($products['currency']) ? $products['currency'] : null;
            $products = isset($products['products']) ? $products['products'] : [];
        } else {
            $user_id = isset(auth()->guard('front')->user()->id) ? auth()->guard('front')->user()->id : 0;
            $requestData = $request->merge(['type' => $type, 'page_no' => 0, 'user_id' => $user_id]);

            $products = $this->api->getExcProducts($requestData);

            $currency = isset($products['currency']) ? $products['currency'] : null;
            $products = isset($products['data']) ? $products['data'] : [];
        }

        $offerid = $id;
        return view('front.listing.view-offer', compact('products', 'currency', 'user_id', 'type', 'offerid'));
    }
}
