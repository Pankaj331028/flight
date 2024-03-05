<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\ApiController as ApiController;
use App\Http\Controllers\Controller;
use App\Model\BusRuleRef;
use App\Model\Country;
use App\Model\User;
use App\Model\UserAddress;
use Auth;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(Request $request)
    {
        $this->api = (new ApiController($request));
    }

    public function index(Request $request)
    {

        $user = User::find(Auth::guard('front')->user()->id);
        $requestData = $request->merge(['user_id' => $user->id]);
        $items = json_decode($this->api->getAllOrders($requestData));
        $currency = isset($items->data) ? $items->currency : null;
        $items = isset($items->data) ? $items->data : [];

        $user_addresses = $this->api->getUserAddress($requestData);

        $user_addresses = isset($user_addresses['data']) ? $user_addresses['data'] : null;
        $countryRef = BusRuleRef::where('rule_name', 'app_country')->first();
        $countries = Country::where('name', $countryRef->rule_value)->get();
        $stateList = $this->api->stateList($requestData);
        $stateList = isset($stateList['data']) ? $stateList['data'] : null;
        $wallet = $this->api->getUserWallet($requestData);
        $user_wallet = ($wallet['status'] == 200) ? $wallet['data'] : [];
        $wallet_balance = ($wallet['status'] == 200) ? $currency . ' ' . $wallet['balance'] : $currency . ' 0';

        return view('front.account', compact('user', 'items', 'user_addresses', 'countries', 'stateList', 'user_wallet', 'wallet_balance'));
    }

    public function removeAddress(Request $request)
    {
        $data = UserAddress::whereIn('id', $request->id)->update(['status' => 'DL']);
        if (isset($data)) {
            session()->put('address_update', 1);
            return response()->json(['status' => true, 'message' => 'Address successfully removed']);
        } else {
            return response()->json(['status' => false, 'message' => 'Something went wrong']);
        }

    }

}
