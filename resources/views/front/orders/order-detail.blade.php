@extends('front.layouts.master')
@section('template_title','Order Detail')
@push('css')
<style>
    #calTbody .a-date.event {
        background-color:yellow;
        color:black;
    }
    #calTbody .a-date.focused, #calTbody .a-date:active{
        border: 1px solid;
    }
    #calTbody .a-date.current {
        border:0;
    }
</style>
@endpush
@section('content')
<section class="wrapperorderDetail1 mt-5">
    <div class="container">
        <h5 class="font35 font-weight-bold color11 text-center pageTitle">Order Details</h5>
    </div>
    <section class="wrapperOrderDetail2">
        <input type="hidden" id="order_id" value="{{ $item['data']['id'] }}">
        <input type="hidden" id="user_id" value="{{ $auth_user->id }}">
        <?php
            $end   =  end($item['calendar']);
        ?>
        <input type="hidden" id="end_date" value="{{ $end['date'] }}">
        
        <div class="container">
            <div class="d-flex justify-content-between py-sm-4 py-2">
                <div class="orderStatus">
                    <p class="font18 color20 fontSemiBold mb-0">Order No: <span class="font16 fontNormal color36">{{ $item['data']['order_no'] }}</span></p>
                    <p class="font18 color18 fontSemiBold mb-0">Schedule: <span class="font16 fontNormal color36">{{ $item['data']['duration'] }}</span></p>
                    <p class="font18 color20 fontSemiBold mb-0">Date: <span class="font16 fontNormal color36">{{ Carbon\Carbon::parse($item['current_date'])->format('d M, Y') }}</span></p>
                </div>
                <div class="trackOrderBtn ">
                    <a class="font16 colorTheme border1 radius50 px-4 py-2 d-flex align-items-center"
                        href="{{ url('order-status/'.$item['data']['id']) }}">Status</a>
                </div>
            </div>
            <div class="d-flex border-bottom borderTop1 py-sm-4 py-2 mb-4">
                <div class="calendar_wrapper">
                   <div id="calendar"></div>
                </div>
            </div>
        </div>
    </section>   
    {{-- Ordered products detail --}}
    @include('front.orders.partial-ordered-product')
    @include('front.modals.re-schedule')
    
    <section class="container text-center calendarProduct mb-5">
        <span class="font-weight-bold font18">No product found</span>
    </section>
</section>
@endsection
@include('front.orders.script')