@extends('front.layouts.master')
@section('template_title','Order Status')
@section('content')
<section class="wrappernotification1 my-5 ">
    <div class="container">
        <div class="font35 font-weight-bold color11 text-center pageTitle">Status </div>
        <div class="row mt-4 py-4 border-bottom">
            <div class="col-md-6">
                <p class="font18">
                    <b>Order No : </b> {{ $item['order']['order_no'] }}
                </p>
            </div>
            <div class="col-md-6 text-md-right">
                <p class="font22">
                    <b>{{ $item['currency'] }} {{ $item['order']['amount'] }}</b>
                </p>
            </div>
        </div>
        @foreach($item['products'] as $product)
        <div class="row border-bottom mt-4 pb-md-0 pb-3" >
            <div class="col-md-6">
                <div class="orderDetailProd  pb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="orderDetailProd">
                                <img src="{{ $product->medium_image }}" alt="" style="width: 70px;">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="orderDetailDesc">
                                <div class="cartProdDetail">
                                <p class="font18 fontSemiBold color20 mb-1">{{ $product->name }}</p>
                                <p class="font16 color20 fontsemibold mb-1 d-flex align-items-center">
                                    @if($product->special_price == $product->price || $product->special_price=='0')
                                        <span>{{ $product['currency'] }}{{ $product->price }}</span>
                                    @else
                                    <span>{{ $item['currency'] }}{{ $product->special_price }}</span>
                                    <strike class="colorb7 ml-2">{{ $item['currency'] }}{{ $product->price }}</strike>
                                    @endif
                                </p>
                                <p class="font16 color36 mb-1">{{ $product->weight }}</p>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="orderDetailCalender d-flex align-items-center justify-content-md-end">
                  <div class="btn_or_1">
                    <a href="{{ url('order-status-detail/'.$item['order']['id'].'/'.$product->id) }}" class="font16 btn_trans colorTheme border1 radius50 px-5 py-2 d-flex align-items-center">More Details</a>
                  </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endsection