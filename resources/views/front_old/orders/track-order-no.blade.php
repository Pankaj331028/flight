@extends('front.layouts.master')
@section('template_title','Track Order')
@section('content')
<section class="trackOrderWrapper1 mt-5">
    <div class="container">
        <div class="font35 font-weight-bold color11 text-center pageTitle">Track Order</div>
    </div>
</section>

<section class="trackOrderWrapper2 mt-sm-5 mt-3">
    <div class="container">
        <form action="{{ url('track-order') }}" method="POST">
            {{ csrf_field() }}
        <div class="flex justify-content-center my-sm-5 my-3">
           <div>
               <input class="form-control" name="order_no" name="order_id" type="text" placeholder="Enter Order ID">
            </div>
           <div class="text-center mt-5">
               <button type="submit" class="track hover1 font16 fontsemibold colorWhite bgTheme px-4 py-1">Track</button>
            </div>
        </div>
        </form>
</section>
@endsection