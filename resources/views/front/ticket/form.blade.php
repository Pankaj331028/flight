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

   <style>
        span.select2.select2-container.select2-container--default {
            width: 100% !important;
        }

        .disabled{
            background: #c5c5c5;
            cursor: not-allowed;
        }

        .send_otp_div {
            display: none;
        }
        label {
            margin-right: 15px !important;
        }

    </style>
</head>
<body>
<div id="wrapper">
    <div class="page-wrapper">

    <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header d-flex justify-content-center">
            <h5 class="modal-title font28 font-weight-bold color11 text-center" id="addaddressTitle"> Ticket Generate
            </h5>

        </div>
        <div class="modal-body">
            <form method="POST" action="{{ url('api/ticketSubmit') }}" id="form" class="ticketGenerate">
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <input type="text" name="order_no" class="form-control height50" placeholder="Order No*" required>
                                <small class="help-block error text-danger order_no"></small>
                    </div>

                </div>

                <div class="row">
                      <div class="form-group col-md-12">
                          <input name="mobile" type="text" class="form-control colorb7 font16 height50" placeholder="Contact No." minlength="10" maxlength="10" id="mobile"  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                          <small class="help-block error text-danger mobile"></small>
                        </div>

                        <div class="form-group col-md-8">
                            <input type="email" name="email" class="form-control height50" placeholder="Email*" id="email" required>
                                 <small class="help-block error text-danger email"></small>
                        </div>


                        <div class="col-md-4" style="margin: auto; text-align: right;">
                          <button type="button" class="font16 fontSemiBold colorWhite bgTheme radius50 borderNone px-3 py-2 hover1" id="send_otp" required> Send OTP </button>
                        </div>
                    </div>
                    <p class="timer_div">Resend OTP in <span class="js-timeout">0:30 Seconds </span>.</p>



                <div class="send_otp_div" id="send_otp_div">
                    <iframe src="{{URL::asset('ticketGenerateOtp')}}" title="" id="ticket-otp">
                    </iframe>
                </div>

                    <input type="hidden" name="otp" id="otp">


                    <div class="row">
                    <div class="form-group col-md-12">
                        <input type="text" name="subject" class="form-control height50" placeholder="subject*" id="subject" required>
                        <small class="help-block error text-danger subject"></small>
                    </div>
                    </div>

                <div class="form-row">
                    <textarea  name="message" class="form-control rows="4" cols="50"  placeholder="Message" required></textarea>
                    <small class="help-block error text-danger message"></small>
                </div>

                <div class="modal-footer border-top-0 d-flex justify-content-center">
                    <button type="submit"  id="sub_button" class="font18 fontSemiBold colorWhite bgTheme radius50 borderNone px-5 py-2 hover1"> Submit </button>
                </div>
            </form>
        </div>
    </div>
</div>
    </div>
</div>

@include('front.layouts.partials.iframe_script')


<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script>

$(document).ready(function(){
    $(".timer_div").hide();
    $('#sub_button').addClass('disabled');
    $("#sub_button").attr("disabled", true);

$('#send_otp').on('click', function(){
    $("#form").validate();
    var email = $('#email').val();
    var mobile = $('#mobile').val();
    $.ajax({
    url: "{{route('ticketSendOtp')}}",
    type: "Post",
    data: { mobile : mobile, email : email },
    success: function(result){
        $('#send_otp').addClass('disabled');
        $('#email').prop('readonly', true);
        $('#sub_button').prop('disabled', false);
        $('#send_otp').prop('disabled', true);
        $(".timer_div").show();
        $('.js-timeout').text("0:30");
        $("#send_otp").html('Resend OTP');
        countdown();

        $( "#ticket-otp" ).contents().find("input[name='otp_data']").val('');
        $( "#ticket-otp" ).contents().find("input[name='otp_data']").val(result.otp);
        var otp = $( "#ticket-otp" ).contents().find("input[name='otp_data']").val();
        $("input[name='otp']").val('');
        $("input[name='otp']").val(otp);

        toastr.success(result.message);
        $(document).find('#send_otp_div').removeClass('send_otp_div');
        $('#sub_button').removeClass('disabled');

    },
    error: function(error) {
    var d = error.responseJSON;
        if(d.errors.email != undefined){
            toastr.error(d.errors.email);
        }
        if(d.errors.mobile != undefined){
            toastr.error(d.errors.mobile);
        }
    }
    });
});

var interval;
function countdown() {
  clearInterval(interval);
  interval = setInterval( function() {
      var timer = $('.js-timeout').html();
      timer = timer.split(':');
      var minutes = timer[0];
      var seconds = timer[1];
      seconds -= 1;
      if (minutes < 0) return;
      else if (seconds < 0 && minutes != 0) {
          minutes -= 1;
          seconds = 59;
      }
      else if (seconds < 10 && length.seconds != 2) seconds = '0' + seconds;
      $('.js-timeout').html(minutes + ':' + seconds);
      if (minutes == 0 && seconds == 0){
      clearInterval(interval);
      $(".timer_div").hide();
      $('#send_otp').removeClass('disabled');
      $('#send_otp').prop('disabled', false);
    }
  }, 1000);
}


$('#ticket-otp').contents().change(function(){
    var otp = $( "#ticket-otp" ).contents().find("input[name='otp_data']").val()
    $("input[name='otp']").val('');
    $("input[name='otp']").val(otp);
  });

 //register user
 $(document).on('submit', '.ticketGenerate', function(e) {
        e.preventDefault();
        $('#sub_button').html('Please wait...');
        $('#sub_button').attr('disabled',true);
        $('small').text('');
        $('input').parent().removeClass('has-error');

        $.ajax({
            method: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: "json",
        })
        .done(function(data) {
                if(data.status==true){
                    toastr.success(data.message,"Status",{
                    timeOut: 5000,
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": true,
                    "tapToDismiss": true
                    });
                    setTimeout(function() {
                        location.reload();
                        window.top.location.reload();
                    }, 3000);
                }else{
                    $('#sub_button').html('Submit');
                     $('#sub_button').attr('disabled',false);
                    toastr.success(data.message,"Status",{
                    timeOut: 5000,
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": true,
                    "tapToDismiss": true
                    });
                }
            })
            .fail(function(data) {
                    toastr.error(data.responseJSON.message,"Status",{
                    timeOut: 5000,
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": true,
                    "tapToDismiss": true
                    });

            });
        });




});
</script>



</body>
</html>
