<!doctype html>
<html lang="en">
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
    
        <!-- ===== Plugins CSS ===== -->
        <link href="{{URL::asset('/front/css/magicscroll.css') }}" rel="stylesheet" type='text/css' media='all'/>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    
        <!-- SELECT 2  -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
        @stack('css')
    </head>
  <body>
    <div id="wrapper">
        <a id="button" class=""><img src="{{ asset('/front/images/arrow-up.png') }}" alt=""></i></a>
        <div class="preloader">
            <div class="cssload-speeding-wheel"></div>
        </div>
    
        <div id="overlay">
            <div class="cv-spinner">
                <span class="spinner"></span>
        </div>
        </div>
    
        <!-- ===== Top-Navigation ===== -->
        @include('front.automation.navbar')
        @yield('automation-content')
        <!-- ===== Top-Navigation-End ===== -->
    
        <!-- ===== Page-Content ===== -->
        <div class="page-wrapper">
            @yield('content')
        </div>
    </div>
    <script src="{{ asset('js/all.min.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @stack('js')
  </body>
  @include('front.layouts.partials.footer')
</html>