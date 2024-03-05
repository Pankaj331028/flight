<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="{{URL::asset('/front/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{URL::asset('/front/css/style.css') }}" rel="stylesheet">
    <link href="{{URL::asset('/front/css/main.css') }}" rel="stylesheet">
    <link href="{{URL::asset('/front/css/responsive.css') }}" rel="stylesheet">
    <link href="{{URL::asset('/css/toastr/toastr.min.css')}}" rel="stylesheet">
    <title>Thoraipakkam OMR Branch</title>
    <style>
    .send_otp_div {
        display: none;
    }

    .error
    {
        display: flex;
        flex-wrap: wrap;
    }
    </style>
  </head>
  <body>
    <div class="form-bg dialogBOx">
      <div class="container">
          <div class="row signLoginBox">
            <div class="col-md-7 Automation mb-2">
              <div class="AutomationCourse">
                <img src="{{ asset('front/images/logo.svg') }}" width="150" data-testid="omrlogo">
                <h1 data-testid="join-automation">For Joining Automation Course</h1>
                <p> Please Contact-<span>Velmurugan <br>9944152058</span></p>
              </div>
            </div>
              <div class="col-md-5  tabssignup">
                  <div class="tab loginBox" role="tabpanel">
                      <!-- Nav tabs -->
                      <!-- Tab panes -->
                      <div class="tab-content tabs">
                          <div role="tabpanel" class="tab-pane fade in active show logintab" id="Section1">
                              <form class="form-horizontal loginForm" id="userLogin" action="{{ url('weblogin') }}" method="post">
                                  <div class="form-group login">
                                      <input class="form-control" id="email" type="email" placeholder="Email Address*" name="email" required>
                                      <small class="help-block error text-danger email"></small>
                                  </div>
                                  <div class="form-group login">
                                      <input class="form-control" id="pass" type="password" placeholder="Password*" name="pass">
                                      <small class="help-block error text-danger pass"></small>
                                  </div>
                                    <div id="errorMessage" class="d-none">
                                      <b style="color: #dc3545;">

                                      </b>
                                    </div>
                                  <div class="from row terms">
                                    <div class="form-check defaultCheck ">
                                    <input class="form-check-input defaultCheck2" type="checkbox" name="remember_me" id="">
                                    <label class="form-check-label defaultCheck2label" for="defaultCheck2">
                                      Remember Me
                                    </label>
                                  </div>
                                  <!-- Button trigger modal -->
                                    <a type="text" data-toggle="modal" data-target="#exampleModal12" class="forgetPassword">
                                      Forgot password?
                                    </a>
                                  </div>
                                  <div class="form-group text-center mt-3 loginBtnHover commonBtn">
                                      <button type="submit" value="login">Login</button>
                                  </div>
                                  <div class="form-group forgot-pass">
                                      <span>Don't have an account?<a type="text" class="signupanchor showSignup" data-target="#opensignup" data-toggle="modal">
                                          &nbsp;Signup here
                                      </a>
                                      <!-- Modal -->
                                      </span>
                                  </div>
                              </form>
                          </div>
                          <!-- Button trigger modal -->
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <div class="modal fade sign" id="opensignup" tabindex="-1" role="dialog" aria-labelledby="signup" aria-hidden="true" style="display:none;">
        <div class="modal-dialog sign" role="document">
          <div class="modal-content sign">
            <div class="modal-header sign">
              <h5 class="modal-title sign" id="signup">Sign up</h5>
              <button type="button" class="close signupClose" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="closeBtnSign">&times;</span>
              </button>
            </div>
            <div class="modal-body sign loadSignup">

            </div>
          </div>
        </div>
      </div>
      <!-- Modal -->
      <div class="modal fade forget" id="exampleModal12" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog box" role="document">
          <div class="modal-content forget">
            <div class="modal-header forget">
              <h5 class="modal-title forget" id="exampleModalLabelforget">Forget Password</h5>
              <button type="button" class="close forget" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
              <div class="modal-body forget">
                <form class="modal_form" id="resetPassword" action="{{ url('api/forgotPassword') }}" method="post">
                      {{ csrf_field() }}
                    <div class="form-group login">
                        <input class="form-control" id="forgot_email" type="email" placeholder="Email Address*" name="forgot_email" required>
                    </div>
                  </div>
                  <div class="form-group text-center mt-3 commonBtn loginBtnHover">
                      <button type="submit" class="btn btn-default">Submit</button>
                  </div>
                </form>
              </div>
          </div>
      </div>
    </div>
     @include('front.layouts.partials.scripts')
     <script>
      function myFunction() {
        $("#myDIVyy").removeClass("myDIVyy");
        $('#myDIVyy').toggleClass("mystyledd");
      }

      $(document).on('click', '#send_otp' , function(){
          var email = $('#signupEmail').val();
          var mobile_number = $('#signupMobile').val();
          $.ajax({
          url: "{{route('sendOtp')}}",
          type: "Post",
          data: { mobile_number : mobile_number, email : email },
          success: function(result){
              $('#send_otp').addClass('disabled');
              $('#signupEmail').prop('readonly', true);
              $('#signupMobile').prop('readonly', true);
              $('#sub_button').prop('disabled', false);
              $('#send_otp').prop('disabled', true);
              $(".timer_div").removeClass('d-none');
              $('.js-timeout').text("0:30");
              $("#send_otp").html('Resend OTP');
              countdown();
              $("input[name='otp']").val('');
              $("input[name='otp']").val(result.otp);
              //  $("input[name='otp']").prop('readonly', true);
              toastr.success(result.message);
              $("#send_otp_id").removeClass('send_otp_div');
              $('#sub_button').removeClass('disabled');

          },
          error: function(error) {
          var d = error.responseJSON;
              if(d.errors.email != undefined){
                  toastr.error(d.errors.email);
              }else if(d.errors.mobile_number != undefined){
                  toastr.error(d.errors.mobile_number);
              }else{
                toastr.error(d.message);
              }
          }
        })

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
              $(".timer_div").addClass('d-none');
              $('#send_otp').removeClass('disabled');
              $('#send_otp').prop('disabled', false);
            }
          }, 1000);
        }
      });

      $('.showSignup').on('click', function(){
        setTimeout(() => {
            $('.loadSignup').html('<form class="form-horizontal" id="signup" action="{{ url('api/userRegister') }}" method="POST"><input type="hidden" name="device_token" value="{{ csrf_token() }}"><input type="hidden" name="device_id" value=""><input type="hidden" name="device_type" value="web"><div class="form row"><div class="form-group col-md-6"><input class="form-control" type="text" placeholder="First Name*" name="first_name"><small class="help-block error text-danger first_name"></small></div><div class="form-group col-md-6"><input class="form-control" type="text" placeholder="Last Name*" name="last_name"><small class="help-block error text-danger last_name"></small></div></div><div class="form row"><div class="form-group col-md-4"><select class="form-control" aria-label="Default select example" name="day"><option selected value=""> Day </option>@for($i =1; $i <= 31; $i++)<option value="{{$i}}"> {{$i}} </option>@endfor</select> <small class="help-block error text-danger day"></small></select></div><div class="form-group col-md-4"><select class="form-control" aria-label="Default select example" name="month"><option selected value=""> Month</option><option value="January">January</option><option value="February">February</option><option value="March">March</option><option value="April">April</option><option value="May">May</option><option value="June">June</option><option value="July">July</option><option value="August">August</option><option value="September">September</option><option value="Octomber">Octomber</option><option value="November">November</option><option value="December">December</option></select><small class="help-block error text-danger month"></small></div><div class="form-group col-md-4"><select class="form-control" aria-label="Default select example" name="year"><option selected value=""> Year </option>@for ($i = date('Y', strtotime('-50 year')); $i <= date('Y', strtotime('-10 year')); $i++)<option value="{{$i}}"> {{$i}} </option>@endfor</select><small class="help-block error text-danger year"></small></div></div><div class="row mb-4 "><label for="" class="radiochek">Gender</label><br><div class="form-froup col-md-12 d-flex"><div class="form-check genders"><input class="form-check-input" type="radio" name="gender" id="male" value="male" checked><label class="form-check-label" for="male">Male</label></div><div class="form-check genders"><input class="form-check-input" type="radio" name="gender" id="female" value="female"><label class="form-check-label" for="female">Female</label></div> <div class="form-check genders "><input class="form-check-input" type="radio" name="gender" id="other" value="other" ><label class="form-check-label" for="other">Other</label><small class="help-block error text-danger gender"></small></div></div></div><div class="row mb-4 "><label for="" class="radiochek">Select hobbies</label><br><div class="form-froup col-md-12 "><div class="form-check hobby"><input class="form-check-input" type="checkbox" type="checkbox" name="hobbies[]" value="dance" id="dance"><label class="form-check-label" for="dance">Dance</label></div><div class="form-check hobby"><input class="form-check-input" type="checkbox" name="hobbies[]"  value="writing"  id="writing"><label class="form-check-label" for="writing">Writing</label></div><div class="form-check hobby"><input class="form-check-input" type="checkbox" name="hobbies[]"  value="cooking"  id="cooking"><label class="form-check-label" for="cooking">Cooking</label></div><div class="form-check hobby"><input class="form-check-input" type="checkbox" name="hobbies[]"  value="learning"  id="learning"><label class="form-check-label" for="learning">Learning</label></div><div class="form-check hobby"><input class="form-check-input" type="checkbox" name="hobbies[]"  value="painting"  id="painting"><label class="form-check-label" for="painting">Painting</label></div><div class="form-check hobby"><input class="form-check-input" type="checkbox" name="hobbies[]"  value="photography"  id="photography"><label class="form-check-label" for="photography">Photography</label></div><small class="help-block error text-danger hobbies"></small></div></div><div class="form-group mt-3"><input class="form-control" id="signupEmail" name="signup_email" type="email" placeholder="Email Address*"><small class="help-block error text-danger email"></small></div><div class="form row  der"><div class="form-group col-md-6"><input class="form-control" id="signupMobile" type="text" placeholder="Contact No." name="mobile_number" maxlength="10"></div><div class="form-group col-md-6 sendOtp"><button type="button" id="send_otp"> Send OTP</button></div></div><p class="timer_div d-none">Resend OTP in <span class="js-timeout">0:30 Seconds </span></p><div class="mt-3 send_otp_div" id="send_otp_id"><div class="form-group col-md-12 otp_div" style="padding:0;"><input name="otp" type="text" class="form-control colorb7 font16 height50" maxlength="4" placeholder="OTP." id="number_type" ><small class="help-block error text-danger otp"></small></div></div><div class="form-group mt-3"><input class="form-control" type="password" name="signup_password" placeholder="Password*" minlength="7" maxlength="15"><small class="help-block error text-danger password "></small></div><div class="form-group mt-3"><input class="form-control" id="signup_pwd" type="password" placeholder="Confirm Password*" name="confirm_password"><small class="help-block error text-danger confirm_password"></small></div><div class="from row terms"><a><div class="form-check referal" onclick="myFunction()"><input class="form-check-input" type="checkbox" value="" id="defaultCheck3" name="referBox"><label class="form-check-label" for="defaultCheck3" onclick="myFunction()">Referral Code</label></div></a><div id="myDIVyy"><div class="form-group toggle mt-3"><input class="form-control" type="text" placeholder="Enter Referal Code*" name="referral_code"></div></div></div><div class="form-group text-center mt-3"><button type="submit" id="sub_button" class="font16 fontSemiBold colorWhite bgTheme radius50 borderNone px-5 py-2 hover1">Sign up</button></form>')
        }, 5000);
        // $('#sub_button').addClass('disabled');
        // $("#opensignup").modal('show');
        // $("#opensignup").css('display','');
      });

      $('.signupClose').click(function()
      {
          $('.loadSignup').empty();
      });

      function randomStr(len, arr) {
          var ans = '';
          for (var i = len; i > 0; i--) {
              ans +=
                arr[Math.floor(Math.random() * arr.length)];
          }
          return ans;
      }

      $('.defaultCheck2').attr('id', 'remember_'+randomStr(5, '12345abcde'));
      $('.defaultCheck2label').attr('for', $('.defaultCheck2').attr('id'));
    </script>
  </body>
</html>