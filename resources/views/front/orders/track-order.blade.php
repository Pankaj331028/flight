@extends('front.layouts.master')
@section('template_title','Track Order')
@section('content')
<section class="trackOrderWrapper1 mt-5">
    <div class="container">
        <h5 class="font35 font-weight-bold color11 text-center pageTitle">Track Order</h5>
    </div>
</section>

@if(isset($item['order']))
<section class="trackOrderWrapper2 mt-sm-5 mt-3">
    <div class="container">
        <p class="font20 color20 fontSemiBold">Order No : <span class="font18 color36">{{ $item['order']['order_no'] }}</span></p>

        <div class="trackProcess my-sm-5 my-3">
            <ul class="progressbar">
                @foreach ($item['order_status'] as $order)
                    <li class="text-center font18 fontBold @if($order['status']==1) active @endif">{{ $order['title'] }}<br><span
                    class="font16 fontNormal color20 d-none d-md-flex justify-content-center">{{ $order['message'] }}</span></li>
                @endforeach
            </ul>
        </div>
</section>

<section class="trackOrderWrapper2 mb-5">
    <div class="container borderTop1 mt-sm-5 mt-3 pt-sm-5 pt-3 px-md-0">
        <div class="row">
            <div class="col-md-6">
                <div class="paymentMethod borderRight1">
                    <div class="font20 fontSemiBold color20 mb-4">Delivery Address</div>
                    @php($address = $item['order']['delivery_address'])
                    <p class="font18 color36">{{ $address['apartment'].' '.$address['address'] }} <br> {{ $address['city'].', '.$address['state'].' ('.$address['zipcode'].')'}}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="deliverTime">
                    <div class="font20 fontSemiBold color20 mb-4">Delivery Time</div>
                    <p class="font18 color36">{{ $item['order']['delivery_time'] }}</p> 
                </div>
            </div>
        </div>
    </div>
</section>

<section class="trackOrderWrapper3">
    <div class="container borderTop1 my-sm-5 mt-3 mb-3 pt-sm-5 pt-3 px-md-0">
        <div class="trackOrderBtn d-flex justify-content-center">
            <form id="cancelProduct" action="{{ url('api/cancelOrder') }}" method="post">
                <input type="hidden" name="user_id" value="{{ $auth_user->id }}">
                @if(isset($item_id))
                <input type="hidden" name="item_id" value="{{ $item_id }}">
                <input type="hidden" name="date" value="{{ $item['order']['current_date'] }}">
                <input type="hidden" name="order_id" value="{{ isset($item['order']['order_id']) ? $item['order']['order_id'] : $order_id  }}">
                @else
                <input type="hidden" name="order_id" value="{{ isset($item['order']['order_id']) ? $item['order']['order_id'] : $order_id  }}">
                @endif
                @if($item['order']['status']=='delivered')
                    <a href="{{ url('/chat-support') }}" class="cancelOrder font16 btn_trans colorTheme border1 radius50 px-4 py-2 d-flex align-items-center">Return</a>
                @else
                    <button type="submit" class="cancelOrder font16 btn_trans colorTheme border1 radius50 px-4 py-2 d-flex align-items-center">Cancel</button>
                @endif
            </form>
        </div>
    </div>
</section>
@include('front.orders.script')
@else
<div class="d-flex justify-content-center my-5">
    <span class="font20">Order number is invalid. Please try again !</span>
</div>
@endif
@endsection