@extends('front.layouts.master')
@section('template_title')
 {{ $pageTitle }}
@endsection
@section('content')
<section class="wrapperabout1 my-5">
    <div class="container">
        <div class="font35 font-weight-bold color11 text-center pageTitle">Contact Us</div>
        <div class="row borderBottom1 mt-sm-5 mt-3 pb-sm-4 pb-3">
            <div class="col-md-4">
                <div class="contAddBlock addresBlock">
                    <p class="font18 fontSemiBold color20 d-flex align-items-center"><span class="mr-3 theme_icon"><i class="fa fa-map-marker " aria-hidden="true"></i></span>Address</p>
                    <p class="font16 color36">{{config('settings.CONFIG_CONTACT_ADDRESS')}}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contAddBlock emailBlock">
                    <p class="font18 fontSemiBold color20 d-flex align-items-center"><span class="mr-3 theme_icon"><i class="fa fa-envelope" aria-hidden="true"></i></span>Email Address</p>
                    <a href="mailto:{{config('settings.CONFIG_CONTACT_EMAIL')}}" class="font16 color36">{{config('settings.CONFIG_CONTACT_EMAIL')}}</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contactBlock">
                    <p class="font18 fontSemiBold color20 d-flex align-items-center"><span class="mr-3 theme_icon"><i class="fa fa-phone" aria-hidden="true"></i></span>Contact No.</p>
                    <a href="tel:1800415648956" class="font16 color36">{{config('settings.CONFIG_CONTACT_NO')}}</a>
                </div>
            </div>
        </div>

        <div class="getintouch">
            <p class="font20 color20 fontSemiBold my-sm-4 my-2">Get In Touch</p>
            <form action="{{ url('contact/store') }}" method="post">
                {{ csrf_field() }}
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <input type="text" name="name" class="form-control colorb7 font16 height50" placeholder="Name" value="{{ old('name') }}">
                         <div class="error help-block text-danger">{{ $errors->first('name') }}</div>
                    </div>
                    <div class="form-group col-md-4">
                        <input type="email" name="email" class="form-control colorb7 font16 height50" placeholder="Email Address" value="{{ old('email') }}">
                         <div class="error help-block text-danger">{{ $errors->first('email') }}</div>
                    </div>
                    <div class="form-group col-md-4">
                        <input type="text" name="phone_no" class="form-control colorb7 font16 height50" placeholder="Contact No." value="{{ old('phone_no') }}">
                         <div class="error help-block text-danger">{{ $errors->first('phone_no') }}</div>
                    </div>
                </div>
                <div class="form-group">
                    <textarea class="form-control colorb7 font16 height50" name="message" rows="3" placeholder="Type your message here..">{{ old('message') }}</textarea>
                     <div class="error help-block text-danger">{{ $errors->first('message') }}</div>
                </div>
                <div>
                    <button type="submit" class="font16 fontSemiBold colorWhite bgTheme radius50 borderNone px-5 py-2 hover1">Submit</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection