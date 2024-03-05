<?php

namespace App\Http\Controllers\Front;

use Auth;
use DB;
use URL;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController as ApiController;
use App\Http\Controllers\MyController as MyController;


class FavouriteController extends Controller
{
    public function __construct(Request $request) {
		$this->api = (new ApiController($request));
		$this->function = (new MyController($request));
    }
    
    public function index(Request $request)
    {
        $user_id            = Auth::guard('front')->user()->id;
        $requestData        = $request->merge(['user_id' => $user_id]);
        $favouriteProducts  = $this->api->getUserFavourites($requestData);
        $currency		        = isset($favouriteProducts['data']) ? $favouriteProducts['currency'] : null;
        $products           = isset($favouriteProducts['data']) ? $favouriteProducts['data'] : [];

        return view('front.favourite',compact('user_id','products','currency'));
    }
}
