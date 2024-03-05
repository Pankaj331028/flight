@extends('front.layouts.master')
@section('content')
<section id="wrapper" class="error-page my-5">
    <div class="error-box">
        <div class="error-body text-center">
            <h1 class="font-weight-bold">400</h1>
            <h3 class="font-weight-bold text-uppercase my-3">Page Not Found !</h3>
            <p class="text-muted m-t-30 m-b-30">YOU SEEM TO BE TRYING TO FIND HIS WAY HOME</p> <a href="{{'/'}}" class="btn colorWhite bgTheme radius50 borderNone px-5 py-2 hover1">Back to home</a> </div>
    </div>
</section>
@endsection