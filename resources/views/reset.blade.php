
@extends('front.layouts.master')
@section('template_title','Reset Password')
@section('content')
<section class="wrapper2 py-3 py-md-5">
    <div class="container">
    <div class="col-lg-6 col-md-6 col-sm-6 col-6 mx-auto">
        <h2 class="text-center mb-4">Reset Password</h2>
        @include('layouts.message')
        <div class="auto-form-wrapper">
            <form class="form-valide" method="post" action="{{$url}}">
                {{csrf_field()}}
                <input type="hidden" name="email" value="{{$email}}">
                <div class="form-group my-4">
                    <label class="label">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="pass" placeholder="*********" name="pass">
                    </div>
                </div>
                <div class="form-group mb-5">
                    <label class="label">Confirm Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" placeholder="*********" name="confirm-pass">
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="submit" class="font16 fontSemiBold colorWhite bgTheme radius50 borderNone px-5 py-2 hover1">Confirm</button>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{URL::asset('/plugins/jquery/jquery.min.js')}}"></script>
<script src="{{URL::asset('/js/vendor.bundle.base.js')}}"></script>
<script src="{{URL::asset('/js/vendor.bundle.addons.js')}}"></script>
<script src="{{URL::asset('/js/misc.js')}}"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
<script src="{{URL::asset('/js/jquery.validate.min.js')}}"></script>
<script src="{{URL::asset('/js/jquery.validate-init.js')}}"></script>
<script type="text/javascript">
    $('div.alert').delay(10000).slideUp(500);
    $(".preloader").fadeOut();

</script>
@endpush