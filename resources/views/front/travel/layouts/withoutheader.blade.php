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
    <link href="{{ asset('/plugins/jssocials-master/dist/jssocials.css') }}" rel="Stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

<!-- SELECT 2  -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


<!-- DATEPICKER  -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
   <link rel="stylesheet" href="/resources/demos/style.css">
   <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
   <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>

    @stack('css')
</head>

<body>

<div class="overlay"></div>
<!-- ===== Main-Wrapper ===== -->
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

{{--  --}}
    <!-- ===== Top-Navigation ===== -->
    @include('front.travel.layouts.partials.withoutheader')
    <!-- ===== Top-Navigation-End ===== -->

    <!-- ===== Page-Content ===== -->
    <div class="page-wrapper">
        @yield('content')

        <!--- Modals --->
        {{-- @include('front.travel.layouts.partials.modals') --}}
        <!-- Modals end --->
    </div>
    <!-- ===== Page-Content-End ===== -->
</div>
<!-- ===== Main-Wrapper-End ===== -->

    <!--=======Required JS Files===== -->
    @include('front.travel.layouts.partials.withouticon')

    <!--=======Required JS Files End===== -->
@stack('js')
</body>
    <!-- ===== Footer ===== -->
    {{-- @include('front.travel.layouts.partials.footer') --}}
    <!-- ===== Footer-End ===== -->




<script type="text/javascript">
    $(document).ready(function() {
        $( function() {
            $( "#datepicker" ).datepicker({
                changeMonth: true,
                changeYear: true
                // yearRange: "1950:2022"
            });
            $( "#datepicker1" ).datepicker({
                changeMonth: true,
                changeYear: true
                // yearRange: "1950:2022"
            });

            var toMinDate =  $('.from').val();

            $('.from').datepicker({
                dateFormat: "yy-mm-dd",
                minDate: new Date() ,
                onClose: function (selectedDate) {

                    var date = new Date(selectedDate);
                    var date = new Date(date.setDate(date.getDate() + 1));

                    var month = (date.getMonth()+1);
                    var day = date.getDate();
                    month = month < 10 ? '0'+month : month;
                    day = day < 10 ? '0'+day : day;
                    var nextdate = date.getFullYear() + '-' + month + '-' + day;

                    $('.to').datepicker( "option", "minDate", nextdate );
                    $('.to').val('');
                    $(this).valid();
                }
            });

            $('.to').datepicker({
                dateFormat: "yy-mm-dd",
                minDate: toMinDate ,
                onClose: function(selectedDate){
                    $(this).valid();
                }
            });
        });


    $('.select_2').select2({
        placeholder: "Select Hobbies",
        closeOnSelect: false
    });

    /*$('.form-control').on('input change', function(){
        $(this).valid();
    })*/

    $(".cart_btn").click(function () {
            $(".cart_innerbox").toggleClass('active');
            $('.overlay').toggleClass('active');
         });

         if ( $('.cart_innerbox').hasClass('active') ) {
            $("body").click(function () {
               $(".cart_innerbox").removeClass('active');
            });
         };


    $(document).on('click','.cart_btn_remove',function () {
        //   alert('here');
        $(".cart_innerbox").removeClass("active");
        if ($('.overlay').hasClass('active')) {
            $('.overlay').removeClass('active')
        }
    });

    hide = true;
         $('body').on("click", function () {
            if (hide) $('.filter_dropdown_box').removeClass('active');
            hide = true;
         });
         $('body').on('click', '.filter_btn', function () {
            var self = $(".filter_dropdown_box");
            if (self.hasClass('active')) {
               $('.filter_dropdown_box').removeClass('active');
               return false;
            }
            $('.filter_dropdown_box').removeClass('active');
               self.toggleClass('active');
            hide = false;
         });

});
</script>




</html>
