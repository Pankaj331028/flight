<?php

namespace App\Http\Controllers\Front;

use Auth;
use URL;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController as ApiController;
use App\Http\Controllers\MyController as MyController;

class NotificationController extends Controller
{
	public function __construct(Request $request) {
		$this->api = (new ApiController($request));
		$this->function = (new MyController($request));
	}

    public function index(Request $request)
	{
		$requestData   = $request->merge(['user_id' => Auth::guard('front')->user()->id]);
		$notifications = $this->api->getNotifications($requestData);
		$notifications = isset($notifications['data']) ? collect($notifications['data']) : null;
		$notifications = $notifications->groupBy('date');
		return view('front.notifications',compact('notifications'));
	}
}
