<?php

namespace App\Http\Controllers;

use App\Model\BusRuleRef;
use Illuminate\Http\Request;

class BusRuleRefController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $setting = BusRuleRef::select("rule_name", "rule_value")->whereIn("rule_name", array("ios_update_user", "ios_url_user", "ios_version_user", "android_update_user", "android_url_user", "android_version_user", "app_update_msg", "customer_care_number", "referrer_amount","mobile_app_status"))->where("sts_cd", 'AC')->get();
      
        return view('setting.index', compact('setting'));
    }
// 
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'android_version_user' => 'required',
            'android_url_user' => 'required',
            'ios_version_user' => 'required',
            'ios_url_user' => 'required',
            'app_update_msg' => 'required',
            "customer_care_number" => 'required',
            "referrer_amount" => 'required',
            "mobile_app_status" => '',
        
        ]);

        $attr = [
            'android_version_user' => 'Android User App Version',
            'android_url_user' => 'Android User App URL',
            'android_update_user' => 'Android User App Force Update',
            'ios_version_user' => 'iOS User App Version',
            'ios_url_user' => 'iOS User App URL',
            'ios_update_user' => 'iOS User App Force Update',
            'app_update_msg' => 'Application Update Message',
            'customer_care_number' => 'Customer Care Number',
            'referrer_amount' => 'Referral Amount',
            'contact_email' => 'Email Address',
            'contact_address' => 'Contact Address',
            'mobile_app_status' => 'Mobile App Status',
        ];

        $validate->setAttributeNames($attr);
        if ($validate->fails()) {
            return redirect()->route("setting")->withInput($request->all())->withErrors($validate);
        } else {
            BusRuleRef::where("rule_name", "android_version_user")->update(['rule_value' => $request->android_version_user]);
            BusRuleRef::where("rule_name", "android_url_user")->update(['rule_value' => $request->android_url_user]);
            BusRuleRef::where("rule_name", "android_update_user")->update(['rule_value' => $request->android_update_user]);
            BusRuleRef::where("rule_name", "ios_version_user")->update(['rule_value' => $request->ios_version_user]);
            BusRuleRef::where("rule_name", "ios_url_user")->update(['rule_value' => $request->ios_url_user]);
            BusRuleRef::where("rule_name", "ios_update_user")->update(['rule_value' => $request->ios_update_user]);
            BusRuleRef::where("rule_name", "app_update_msg")->update(['rule_value' => $request->app_update_msg]);
            BusRuleRef::where("rule_name", "customer_care_number")->update(['rule_value' => $request->customer_care_number]);
            BusRuleRef::where("rule_name", "referrer_amount")->update(['rule_value' => $request->referrer_amount]);
            $mobileAppStatus = $request->has('mobile_app_status') ? 'AC' : 'IN';
            BusRuleRef::where("rule_name", "mobile_app_status")->update(['rule_value' => $mobileAppStatus]);

            $request->session()->flash('success', 'Setting successfully saved');
            return redirect()->route('setting');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}