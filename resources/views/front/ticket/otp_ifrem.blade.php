<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="Description" content="@yield('description')" />
    <meta name="robots" content="index, follow" />
    <meta name="Language" content="English" />
    <meta property="og:title" content="@yield('og_title')" />
    <meta property="og:url" content="@yield('og_url')" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="@yield('og_description')" />
    <meta property="og:image" content="@yield('og_image')" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="@yield('twitter_title')" />
    <meta name="twitter:description" content="@yield('twitter_description')" />
    <meta name="twitter:url" content="@yield('twitter_url')" />
    <meta name="twitter:image" content="@yield('twitter_image')" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{URL::asset('/images/favicon.png')}}">
    <title>@yield('template_title') - {{ config('app.name') }}</title>

    <!-- ===== Bootstrap CSS ===== -->
    <link href="{{URL::asset('/front/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- ===== Custom CSS ===== -->
    <link href="{{URL::asset('/front/css/style.css') }}" rel="stylesheet">
    <link href="{{URL::asset('/front/css/main.css') }}" rel="stylesheet">
    <link href="{{URL::asset('/front/css/responsive.css') }}" rel="stylesheet">
    <link href="{{URL::asset('/front/css/font-awesome.min.css') }}" rel="stylesheet">

    <!-- ===== Plugins CSS ===== -->
    <link href="{{URL::asset('/front/css/magiczoomplus.css') }}" rel="stylesheet" type="text/css" media="screen" />
    <link href="{{URL::asset('/front/css/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{URL::asset('/front/css/magicscroll.css') }}" rel="stylesheet" type='text/css' media='all'/>
    <link href="{{URL::asset('/front/css/jquery.noty.css') }}" rel="stylesheet" type='text/css'>
    <link href="{{URL::asset('/front/css/noty_theme_default.css') }}" rel="stylesheet" type='text/css'>
    <link href="{{URL::asset('/css/toastr/toastr.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('/front/css/mini-event-calendar.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('/plugins/jqueryui/jquery-custom-ui.css')}}" rel="Stylesheet">
    <link href="{{URL::asset('/plugins/sweetalert/sweetalert.css')}}" rel="Stylesheet">
    <link href="{{ asset('/plugins/jssocials-master/dist/jssocials.css') }} rel="Stylesheet">


<!-- SELECT 2  -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


<!-- DATEPICKER  -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
   <link rel="stylesheet" href="/resources/demos/style.css">
   <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
   <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>


</head>
<body>
<div id="wrapper">
    <div class="page-wrapper">

    <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

        <div class="modal-body">
                    <div class="row">
                      <div class="form-group col-md-12">

                          <div class="form-group col-md-12 otp_div" style="padding:0;">
                            <input name="otp_data" type="text" class="form-control colorb7 font16 height50" maxlength="4" placeholder="OTP." id="number_type" >
                            <small class="help-block error text-danger otp"></small>
                          </div>
                        </div>
                    </div>
                    </div>


        </div>
    </div>
</div>
    </div>
</div>
</body>
</html>
