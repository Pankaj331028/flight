<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('plugins/images/favicon.png')}}">
    <title>@yield('template_title') | {{ config('app.name') }}</title>
    <!-- ===== Bootstrap CSS ===== -->
    <link href="{{asset('bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">

    <!-- ===== Animation CSS ===== -->
    <link href="{{asset('css/animate.css')}}" rel="stylesheet">
    <!-- ===== Custom CSS ===== -->
    <link href="{{asset('css/style.unminified.css')}}" rel="stylesheet">
    <!-- ===== Color CSS ===== -->
    <link href="{{asset('css/colors/default.css')}}" id="theme" rel="stylesheet">

    @stack('css')
</head>
<body class="fix-header">
<!-- ===== Main-Wrapper ===== -->
@yield('content')

<!-- ===== Main-Wrapper-End ===== -->
<!-- ==============================
    Required JS Files
=============================== -->
<!-- ===== jQuery ===== -->
<script src="{{asset('plugins/components/jquery/dist/jquery.min.js')}}"></script>
<!-- ===== Bootstrap JavaScript ===== -->
<script src="{{asset('bootstrap/dist/js/bootstrap.min.js')}}"></script>
<!-- ===== Custom JavaScript ===== -->
<script src="{{asset('js/custom-normal.js')}}"></script>
<!-- ===== Plugin JS ===== -->
@stack('js')
</body>

</html>