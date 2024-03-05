@extends('front.layouts.master')
@section('template_title','Detail')
@section('content')
<section class="wrappernotification1 my-5 ">
    <div class="container">
        <h5 class="font35 font-weight-bold color11 text-center pageTitle">Status </h5>
        <div class="row mt-4 py-4 border-bottom">
            <div class="col-md-6">
                <p class="font18" data-testid="order_no">
                    <b>Order No : </b> {{ $order['order_no'] }}
                </p>
                <p class="font18">
                    <b>Schedule : </b>  {{ $order['duration'] }}
                </p>
                <p class="font18">
                    <b>Delivery Slot : </b>  {{ $order['delivery_slot'] }}
                </p>
                {{-- <p class="font18">
                    <b>Date : </b> Feb 3 , 2020 
                </p> --}}
            </div>
            <div class="col-md-6 text-md-right">
                <p class="font22 d-flex justify-content-md-end">
                    @if($product->scheduled == 0)
                        @if($schedules[0]->status=='pending')
                        <a href="{{ url('track-order/'.$id.'/'.$product->id) }}" class="font16 btn_trans colorTheme border1 radius50 px-5 py-2 d-flex align-items-center">Track</a>
                        @endif
                    @endif
                </p>
            </div>
        </div>
        <div class="row border-bottom mt-4" >
            <div class="col-md-6">
                <div class="orderDetailProd  pb-4 ">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="orderDetailProd">
                                <img src="{{ $product->medium_image }}" alt="" style="width: 70px;">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="orderDetailDesc">
                                <div class="cartProdDetail border-right-0">
                                <p class="font18 fontSemiBold color20 mb-1">{{ $product->name }}</p>
                                <p class="font16 color20 fontsemibold mb-1 d-flex align-items-center">
                                    @if($product->special_price == $product->price || $product->special_price=='0')
                                        <span>{{ $product['currency'] }}{{ $product->price }}</span>
                                    @else
                                    <span>{{ $product->currency }}{{ $product->special_price }}</span>
                                    <strike class="colorb7 ml-2">{{ $product->currency }}{{ $product->price }}</strike>
                                    @endif
                                </p>
                                <p class="font16 color36 mb-1">{{ $product->weight }}</p>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('front.orders.partial-re-schedule')
@include('front.modals.re-schedule')
@endsection
@include('front.orders.script')