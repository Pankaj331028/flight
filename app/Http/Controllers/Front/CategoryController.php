<?php

namespace App\Http\Controllers\Front;

use App\Model\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController as ApiController;
use App\Http\Controllers\MyController as MyController;

class CategoryController extends Controller
{
	public function __construct(Request $request) {
		$this->api = (new ApiController($request));
		$this->function = (new MyController($request));
	}
	
    public function index(Request $request,$id)
	{
		if($id=='list'){
			$id = null;
		}
		$requestData = $request->merge(['category_id'=>$id]);
		$categories = $this->api->categoryList($request);
		$categories = ($categories['status']=='200' ? $categories['data'] : []);
		// if(empty($categories)){
		// 	return app('App\Http\Controllers\Front\ProductController')->index($id);
		// }
		return view('front.category',compact('categories','id'));
	}
}