<!DOCTYPE html>
<html lang="en">
<head>
<title>omrbranch</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<link href="{{URL::asset('/front/css/bootstrap.min.css') }}" rel="stylesheet">
<body style="background-color:#fff; font-family:arial; font-size:14px;">

@php

if(!isset($email_support)){
    $email_support = '';
}

@endphp

<center>
    <table width="100%"  cellspacing="0" cellpadding="20" style="margin-bottom: 40px;">
        <thead align="center">
            <tr>
                <th style="width: 50%;">
                    <img src="{{ asset('/front/images/logo.png') }}" width="20%" height="auto">
                </th>
            </tr>
        </thead>
    </table>
	<table width="800" cellspacing="0" style="border:1px solid #40b851; border-radius:5px; overflow:hidden">
		<tr>
			<td bgcolor="#40b851" style="padding:10px 20px;">
				<table style="width:100%;">
					<tr>
						<td width="40%" style="color:#fff;">
							omrbranch
						</td>
						<td style="color:#fff">
							&nbsp;
						</td>
					</tr>
				</table>
			</td>
		</tr>
    <tr>
        <td>
            <div style="padding:25px;">
            <p style="font-size: 30px;font-weight: bold;margin-bottom: 0px;">{{$currency}} {{$order->grand_total}}</p>
            <p style="color: #5f5e5e;">Thanks for choosing omrbranch, {{$first_name." ".$last_name}}</p>
            <p style="color: #5f5e5e;">{{$order->created_at}} | omrbranch</p>
            </div>
        </td>
    </tr>

    <tr style="background-color: #e8e8e8; padding: 15px 25px;">
        <td>
            <table style="width:100%;">
                <tr>
                    <td style="padding : 8px 25px;">
                        <div style="display: inline-block;width: 10%;">
                            <img src="{{URL::asset('/images/tmplate-mail-icon.png')}}" alt="">
                        </div>
                        <div style="display: inline-block;width: 89%;color: #5f5e5e;">
                            <p>Here`s your receipt from omrbranch.
                            </p>
                        </div>
                    </td>
                </tr>
                <tr style="background-color: #e8e8e8;">
                    <td style="padding: 10px 25px;display: block;border-top: 1px solid #ccc;">
                        <table width="100%">
                        <tr>
                            <td width="50%">
                                <h2 style="color: #262626; font-weight: normal;">Your Order Items</h2>
                            </td>
                            <td align="right">
                                <h2 style="color: #262626; font-weight: normal;">#{{$order->order_no}}</h2>
                            </td>
                        </tr>
                        </table>
                        <div style="display: inline-block;width: 100%;">
                            <p style="float: left;width: 20%; font-size: 14px;color: #5f5e5e;">Item Name</p>
                            <p style="float: left;width: 20%; font-size: 14px;color: #5f5e5e;">Delivery Time</p>
                            <p style="float: left;width: 20%;text-align: right; font-size: 14px;color: #5f5e5e;">Quantity</p>
                            <p style="float: right;width: 20%;text-align: right; font-size: 14px;color: #5f5e5e;">Price</p>
                        </div>
                    </td>
                </tr>
                @foreach($items as $item)
                <tr style="background-color: #e8e8e8;">
                    <td style="padding: 10px 25px;display: block;border-top: 1px solid #ccc;">
                        <div style="display: inline-block;width: 100%;">
                            <p style="float: left;width: 20%; font-size: 14px;color: #5f5e5e;">{{App\Model\Product::where(["id" => $item->product_id])->pluck("name")[0]}}</p>
                            <p style="float: left;width: 20%;text-align: right; font-size: 14px;color: #5f5e5e;">
                                @if($item->scheduled=='1')
                                    {{date('d M, Y',strtotime($item->start_date)) .' - '.date('d M, Y',strtotime($item->end_date)) . (isset($item->delivery_slot->id)?('('.date('h:i A', strtotime($item->delivery_slot->from_time)) . ' to ' . date('h:i A', strtotime($item->delivery_slot->to_time)).')'):'-')}}
                                @else
                                    {{date('d M, Y',strtotime($item->start_date)) . (isset($item->delivery_slot->id)?('('.date('h:i A', strtotime($item->delivery_slot->from_time)) . ' to ' . date('h:i A', strtotime($item->delivery_slot->to_time)).')'):'-')}}
                                @endif
                            </p>
                            <p style="float: left;width: 20%;text-align: right; font-size: 14px;color: #5f5e5e;">{{$item->qty}}</p>
                            <p style="float: right;width: 20%;text-align: right; font-size: 14px;color: #5f5e5e;">{{$currency}}{{$item->amount}}</p>
                        </div>

                    </td>
                </tr>
                @endforeach

                @if(sprintf('%.2f', $order->shipping_fee)!=0.00)
                    <tr style="background-color: #e8e8e8;">
                        <td style="padding: 10px 25px;display: block;border-top: 1px solid #ccc;">
                            <p style="float: left;width: 50%; font-size: 14px;color: #5f5e5e;">Shipping Price</p>
                            <p style="margin-top:0px;float: right;width: 50%;text-align: right;font-size: 30px;font-weight: bold;color: #262626;">{{$currency}} {{$order->shipping_fee}}</p>

                        </td>
                    </tr>
                @endif

                @if(sprintf('%.2f', $order->credits_used)!=0.00)
                    <tr style="background-color: #e8e8e8;">
                        <td style="padding: 10px 25px;display: block;border-top: 1px solid #ccc;">
                            <p style="float: left;width: 50%; font-size: 14px;color: #5f5e5e;">Credits Used</p>
                            <p style="margin-top:0px;float: right;width: 50%;text-align: right;font-size: 30px;font-weight: bold;color: #262626;">{{$currency}} {{$order->credits_used}}</p>

                        </td>
                    </tr>
                @endif

                @if(sprintf('%.2f', $order->coupon_discount)!=0.00)
                    <tr style="background-color: #e8e8e8;">
                        <td style="padding: 10px 25px;display: block;border-top: 1px solid #ccc;">
                            <p style="float: left;width: 50%; font-size: 14px;color: #5f5e5e;">Coupon Discount</p>
                            <p style="margin-top:0px;float: right;width: 50%;text-align: right;font-size: 30px;font-weight: bold;color: #262626;">{{$currency}} {{$order->coupon_discount}}</p>

                        </td>
                    </tr>
                @endif


                <tr style="background-color: #e8e8e8;">
                    <td style="padding: 10px 25px;display: block;border-top: 1px solid #ccc;">
                        <div style="display: inline-block;width: 100%;">
                            <p style="float: left;width: 50%; font-size: 14px;color: #5f5e5e; margin-bottom: 0px;">COLLECTED</p>
                        </div>
                        <div style="display: inline-block;width: 100%;">
                            <p style="float: left;width: 50%; font-size: 14px;color: #5f5e5e;">
                                <img src="{{URL::asset('/images/template-cash-icon.png')}}" alt="">
                                <span style="margin-left: 15px;margin-top: -25px;display: inline-block;"> {{Config::get('constants.PAYMENT_METHOD'.$order->payment_method)}} </span>
                            </p>
                            <p style="margin-top:0px;float: right;width: 50%;text-align: right;font-size: 26px;font-weight: bold;color: #262626;">{{$currency}} {{$order->grand_total}}</p>
                        </div>
                    </td>
                    @if(sprintf('%.2f', $order->savings)!=0.00)
                        <td style="padding: 10px 25px;display: block;border-top: 1px solid #ccc;">
                            <div style="display:flex;width: 100%;">
                                <div style="width: 50%;">
                                    <p style="font-size: 14px;color: #5f5e5e;">Total Savings</p>
                                </div>
                                <div style="width: 50%; text-align: right">
                                    <p style="font-size: 14px;font-weight: bold;color: #262626;">{{ $currency }}{{ $order->savings }}</p>
                                </div>
                            </div>
                        </td>
                    @endif
                    <td style="padding: 10px 25px;display: block;border-top: 1px solid #ccc;">
                        <div style="display: inline-block;width: 100%;">
                            <p style="float: left;width: 30%; font-size: 14px;color: #5f5e5e;">
                                Delivery Address
                            </p>
                            <p style="margin-top:0px;float: right;width: 70%;text-align: right;font-size: 14px;font-weight: bold;color: #262626;"> {{$order->user_address->apartment.','.$order->user_address->address.','.$order->user_address->acity->name.','.$order->user_address->astate->name.','.$order->user_address->acountry->name.'-'.$order->user_address->zipcode}}
                            </p>

                        </div>
                        <div style="display: inline-block;width: 100%;">
                            <p style="float: left;width: 30%; font-size: 14px;color: #5f5e5e;">Mobile Number</p>
                            <p style="margin-top:0px;float: right;width: 70%;text-align: right;font-size: 14px;font-weight: bold;color: #262626;"> {{ $order->user_address->mobile }}</p>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="text-align:left; padding:10px 0px 20px 20px; border-bottom:1px solid #e1e1e1;" bgcolor="#f9f9f9">
            <div style="font-size:14px;"><b>For Joining Automation course -</b></div>
            <div style="font-size:14px;">Please contact - Velmurugan<br><a href="tel:9944152058">99441 52058</a>
                <p>Thanks,<br>
                Velmurugan</p>
            </div>
        </td>
    </tr>

    <tr style="">
        <td style="background-color: #4d4d4d;">
            <table style="width:100%">
                <tr>
                    <td style="background-color: #4d4d4d;padding: 15px 25px;text-align: center;">
                        <p style="color: #fff; font-size: 22px;">Need help?</p>
                        <p style="color: #a2a1a1;font-size:14px;">Tap Help in your app to contact support with</p>
                        <p style="color: #a2a1a1;font-size:14px;"><b>Help Line:</b> 24 X 7: {{$customer_care}}</p>
                        <p style="color: #a2a1a1;font-size:14px;"><b>24 X 7:</b> <a href="mailto:{{$email_support}}" style="color:#fff;">{{$email_support}}</a></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="text-align:center; padding:20px 10px 20px; font-size:15px; color:#666;">Copyright @ {{ date('Y') }} omrbranch. All rights reserved.</td>
    </tr>
	</table>
</center>
</body>
</html>
