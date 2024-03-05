
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
<div class="modal fade" id="loginSignupModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal_header border-bottom-0">
          <!-- Nav tabs -->
            <ul class="nav nav-tabs modal_tabs text-center border-bottom-0 d-flex justify-content-center" role="tablist">
              <li role="presentation" class="active mr-3 login_dyna"><a href="#login" aria-controls="login" role="tab" data-toggle="tab" class="font24 font-weight-bold" id="pr_login">Login</a></li>
              <li role="presentation" class="sign_dyna mr-3"><a href="#register" aria-controls="register" role="tab" data-toggle="tab" class="font24 font-weight-bold" id="pr_register">Sign up</a></li>
            </ul>

            @if(isset($auth_user->id))
            <button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
             <img src="{{ URL::asset('/front/images/close-icon.png') }}">
            </button>
            @endif
        </div>
        <!-- Tab panes -->
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane fade in active" id="login">
            <!--Login form-->
            <form class="modal_form" id="userLogin" action="{{ url('weblogin') }}" method="post">
              <div class="modal-body">
                  <div class="form-row">
                      <div class="form-group col-md-12">
                          <input type="email" class="form-control colorb7 font16 height50" name="email" placeholder="Email Address*" required>
                          <small class="help-block error text-danger email"></small>
                      </div>
                  </div>
                  <div class="form-row">
                      <div class="form-group col-md-12">
                          <input type="password" id="password" class="form-control colorb7 font16 height50" name="password" placeholder="Password*">
                          <small class="help-block error text-danger password "></small>
                      </div>
                  </div>
                  <div class="form-row">
                      <div class="form-group col-md-12 d-flex justify-content-between mb-0">
                            <div class="custom-control custom-checkbox mb-3">
                                <input type="checkbox" class="custom-control-input" name="remember_me" id="rememberMe">
                                <label class="custom-control-label font-weight-bold colorTheme" for="rememberMe"> Remember Me</label>
                          </div>
                          <p class="mb-0"><a href="#" class="reset-pwd colorTheme mb-0">Forgot password ?</a></p>
                      </div>
                  </div>
              </div>

              <div class="modal-footer border-top-0 d-flex justify-content-center">
                <button type="submit" class="s-login font16 fontSemiBold colorWhite bgTheme radius50 borderNone px-5 py-2 hover1">Login</button>
              </div>
              <!-- <h2 class="background font14 font-weight-bold text-center "><span>Login With</span></h2>
              <div class="d-flex justify-content-between my-4 flex-column flex-sm-row">
                    <a href="{{ url('social-login/facebook') }}" type="submit" class="loginBtn loginBtn--facebook">
                        Login with Facebook
                    </a>
                    <a href="{{ url('social-login/google') }}" type="submit" class="loginBtn loginBtn--google">
                        Login with Google
                    </a>
              </div> -->
              <div class="form-row">
                  <div class="form-group col-md-12 text-center mb-0 pb-3">
                      <p class="mb-0 font14">Don't have an account? <a href="javascript:void(0)" class="colorTheme mb-0 sign_active" id="opensignup" >Signup here</a></p>
                  </div>
              </div>
            </form>
          </div>

          <div role="tabpanel" class="tab-pane fade" id="register">
            <!--Registeration form-->
            <form class="modal_form" id="signup" action="{{ url('api/userRegister') }}" method="POST">
            <input type="hidden" name="device_token" value="{{ csrf_token() }}">
            <input type="hidden" name="device_id" value="">
            <input type="hidden" name="device_type" value="web">
              <div class="modal-body">

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <input name="first_name" type="text" class="form-control colorb7 font16 height50" placeholder="First Name*">
                          <small class="help-block error text-danger first_name"></small>
                      </div>
                      <div class="form-group col-md-6">
                          <input name="last_name" type="text" class="form-control colorb7 font16 height50" placeholder="Last Name*">
                          <small class="help-block error text-danger last_name"></small>
                      </div>
                  </div>



    <!-- <div class="form-group col-md-6">
                          <input name="dob" type="text" class="form-control colorb7 font16 height50" id="datepicker" placeholder="Date Of Birth">
                          <small class="help-block error text-danger"></small>
                      </div> -->

                    <p> Date Of Birth.</p>
                  <div class="form-row">
                      <div class="form-group col-md-4">
                      <select  class="form-control colorb7 font16 height50" aria-label="Default select example" name="day">
                          <option selected value=""> Day </option>
                       @for($i =1; $i <= 31; $i++)
                        <option value="{{$i}}"> {{$i}} </option>
                        @endfor
                        </select>
                          <small class="help-block error text-danger day"></small>
                      </div>

                      <div class="form-group col-md-4">
                      <select  class="form-control colorb7 font16 height50" aria-label="Default select example" name="month">
                        <option selected value=""> Select Month </option>
                        <option value="January"> January </option>
                        <option value="February"> February </option>
                        <option value="March"> March </option>
                        <option value="April"> April </option>
                        <option value="May"> May </option>
                        <option value="June"> June </option>
                        <option value="July"> July </option>
                        <option value="August"> August </option>
                        <option value="September"> September </option>
                        <option value="October"> October </option>
                        <option value="November"> November</option>
                        <option value="December"> December</option>
                        </select>
                          <small class="help-block error text-danger month"></small>
                      </div>



                      <div class="form-group col-md-4">
                      <select  class="form-control colorb7 font16 height50" aria-label="Default select example" name="year">
                          <option selected value=""> Year </option>
                          @for ($i = date('Y', strtotime('-50 year')); $i <= date('Y', strtotime('-10 year')); $i++)
                                <option value="{{$i}}"> {{$i}} </option>
                            @endfor
                        </select>
                          <small class="help-block error text-danger year"></small>
                      </div>

                  </div>




                  <!-- <div class="form-row">
                      <div class="form-group col-md-12">
                      <select  class="form-control colorb7 font16 height50" aria-label="Default select example" name="gender">
                        <option selected value=""> Gender </option>
                        <option value="male"> Male </option>
                        <option value="female"> Female </option>
                        <option value="other"> Other  </option>
                        </select>
                          <small class="help-block error text-danger"></small>
                      </div>
                  </div> -->


                  <div class="form-row">
                  <p> Gender.</p>
                    <div class="form-group col-md-12">
                    <input type="radio" name="gender" id="male" value="male">
                    <label for="male"> Male </label>

                    <input type="radio" name="gender" id="female" value="female">
                    <label for="female"> Female </label>

                    <input type="radio" name="gender" id="other" value="other" >
                    <label for="other"> Other </label>
                    <small class="help-block error text-danger gender"></small>
                </div>
            </div>
<!--
                  <div class="form-row">
                      <div class="form-group col-md-12">
                      <select  class="form-control colorb7 font16 height50 select_2" name="hobbies[]" aria-label="Default select example" multiple="multiple" placeholder="Select Hobbies">
                        <option value="dance"> Dance </option>
                        <option value="writing"> Writing </option>
                        <option value="cooking"> Cooking   </option>
                        <option value="learning"> Learning   </option>
                        <option value="painting"> Painting   </option>
                        <option value="photography"> Photography   </option>
                        </select>
                          <small class="help-block error text-danger"></small>
                      </div>
                  </div> -->
                  <div class="form">
                  <p> Select hobbies.</p>
                      <div class="form-group col-md-12">
                      <input type="checkbox" name="hobbies[]" value="dance" id="dance">
                      <label for="dance"> Dance </label><br>

                      <input type="checkbox" name="hobbies[]"  value="writing"  id="writing">
                      <label for="writing"> Writing </label><br>

                      <input type="checkbox" name="hobbies[]"  value="cooking"  id="cooking">
                      <label for="cooking"> cooking </label><br>

                      <input type="checkbox" name="hobbies[]"  value="learning"  id="learning">
                      <label for="learning"> learning </label><br>

                      <input type="checkbox" name="hobbies[]"  value="painting"  id="painting">
                      <label for="painting"> Painting </label><br>

                      <input type="checkbox" name="hobbies[]"  value="photography"  id="photography">
                      <label for="photography"> photography </label><br>
                          <small class="help-block error text-danger hobbies"></small>
                      </div>
                  </div>


                  <div class="form-row">
                      <div class="form-group col-md-12">
                          <input type="email" name="email"  class="form-control colorb7 font16 height50" placeholder="Email Address*" id="signupEmail">
                          <small class="help-block error text-danger email"></small>
                      </div>
                  </div>

             <div id="errors_show"></div>
                <div class="form-row">
                  <div class="row">
                      <div class="form-group col-md-12">
                          <input name="mobile_number" type="text" class="form-control colorb7 font16 height50" placeholder="Contact No." minlength="10" maxlength="10" id="signupMobile"  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">

                        </div>
                    </div>


                    <div class="col-md-5" style="margin: auto; text-align: right;">
                          <button type="button" class="font16 fontSemiBold colorWhite bgTheme radius50 borderNone px-3 py-2 hover1" id="send_otp"> Send OTP </button>
                        </div>
                    </div>

                    <p class="timer_div">Resend OTP in <span class="js-timeout">0:30 Seconds </span>.</p>


                    <div class="send_otp_div" id="send_otp_id">
                    <div class="form-group col-md-12 otp_div" style="padding:0;">
                          <input name="otp" type="text" class="form-control colorb7 font16 height50" maxlength="4" placeholder="OTP." id="number_type" >
                          <small class="help-block error text-danger otp"></small>
                        </div>
                    </div>


                  <div class="form-row">
                      <div class="form-group col-md-12">
                          <input name="password" type="password" class="form-control colorb7 font16 height50" placeholder="Password*" minlength="7" maxlength="15">
                          <small class="help-block error text-danger password "></small>
                      </div>
                  </div>

                  <div class="form-row">
                      <div class="form-group col-md-12">
                          <input name="confirm_password" type="password" class="form-control colorb7 font16 height50" placeholder="Confirm Password*">
                          <small class="help-block error text-danger confirm_password"></small>
                      </div>
                  </div>

                  <div class="form-row">
                    <div class="form-group col-md-12">
                        <div class="custom-control custom-checkbox mb-3">
                          <input type="checkbox" class="custom-control-input check_refer" id="customCheck" name="referBox">
                          <label class="custom-control-label font-weight-bold colorTheme" for="customCheck">Referral Code</label>
                        </div>
                        <input name="referral_code" type="text" class="form-control colorb7 font16 height50 control_refer" placeholder="Enter Referral Code">
                    </div>

                  </div>
              </div>
              <div class="modal-footer border-top-0 d-flex justify-content-center">
              <button type="submit" id="sub_button" class="font16 fontSemiBold colorWhite bgTheme radius50 borderNone px-5 py-2 hover1 disabled_button" disabled  >Sign up</button>
                <!-- <button type="submit" id="sub_button" class="font16 fontSemiBold colorWhite bgTheme radius50 borderNone px-5 py-2 hover1">Sign up</button> -->
              </div>
              <div class="form-row">
                  <div class="form-group col-md-12 text-center mb-0 pb-3">
                      <p class="mb-0 font14">Already have an account ? <a href="#login" class="colorTheme mb-0 login_active" aria-controls="login" role="tab" data-toggle="tab">Login here</a></p>
                  </div>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
</div>






<!-- Rate us Modal  -->
<div class="modal fade" id="rateusModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal_header border-bottom-0">
                <h5 class="modal-title text-center font18 font-weight-bold modal_title_rel" id="exampleModalLabel">
                <span>We want your feedback</span>
                </h5>
                <button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
                <img src="{{ URL::asset('/front/images/close-icon.png') }}">
                </button>
            </div>
            <form class="modal_form" action="{{ url('api/submitRating') }}" method="POST" id="rateStar">
            <input type="hidden" id="star">
            <div class="modal-body">
                <div class="vector_img text-center">
                    <img src="{{ URL::asset('/front/images/vector.png') }}">
                </div>
                <div class="rate_content text-center">
                    <h5><b>Enjoy The App? Rate Us</b></h5>
                </div>
                    <div class="star-rating">
                        <input type="radio" onclick="getRating(this)" id="5-stars" name="rating" value="5" />
                        <label for="5-stars" class="star"><i class="fa fa-star" aria-hidden="true"></i></label>
                        <input type="radio" onclick="getRating(this)" id="4-stars" name="rating" value="4" />
                        <label for="4-stars" class="star"><i class="fa fa-star" aria-hidden="true"></i></label>
                        <input type="radio" onclick="getRating(this)" id="3-stars" name="rating" value="3" />
                        <label for="3-stars" class="star"><i class="fa fa-star" aria-hidden="true"></i></label>
                        <input type="radio" onclick="getRating(this)" id="2-stars" name="rating" value="2" />
                        <label for="2-stars" class="star"><i class="fa fa-star" aria-hidden="true"></i></label>
                        <input type="radio"onclick="getRating(this)"  id="1-star" name="rating" value="1" />
                        <label for="1-star" class="star"><i class="fa fa-star" aria-hidden="true"></i></label>
                    </div>
            </div>
            <div class="modal-footer border-top-0 d-flex justify-content-center">
            <button type="submit" class="font25 fontSemiBold colorWhite bgTheme radius50 borderNone px-5 py-2 hover1">Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!---- Referral Modal --->
<div class="modal fade" id="referModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal_header border-bottom-0">
          <h5 class="modal-title text-center font20 font-weight-bold modal_title_rel" id="exampleModalLabel">
          <span>Refer & Earn</span>
          </h5>
          <button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
            <img src="{{ asset('/front/images/close-icon.png') }}">
          </button>
        </div>
        {{-- <form class="modal_form"> --}}
          <div class="modal-body">
             <div class="vector_img text-center">
                 <img src="{{ asset('/front/images/refer_vector.jpg') }}">
             </div>
             <div class="refer_content text-center">
                 <p>{{ \App\Library\Notify::getBusRuleRef('refer_screen')}} {{ \App\Library\Notify::getBusRuleRef('referrer_amount')}}</p>
             </div>
             <button class="refer_code_wrapper text-center" data-clipboard-target="#referalCode">
                 <h2 class="font-weight-bold font26" id="referalCode">@if(Auth::guard('front')->check()) {{ $auth_user->referral_code }} @endif</h2>
                 <P class="dark_table_text font22">Tap to copy code</P>
             </button>
          </div>
          <div class="modal-footer border-top-0 d-flex justify-content-center">
            <button class="socialShare font16 fontSemiBold colorWhite bgTheme radius50 borderNone px-5 py-2 hover1">Share Now</button>
          </div>
        {{-- </form> --}}
      </div>
    </div>
</div>
<!----- Reset Password Modal --->
<!-- <div class="modal fade" id="forgot-password" tabindex="-1" role="dialog"> -->
<div class="modal fade" id="forgot-password" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal_header border-bottom-0">
            <h5 class="modal-title text-center font18 font-weight-bold modal_title_rel" id="exampleModalLabel">
                <span>Forgot Password</span>
            </h5>
            @if(isset($auth_user->id))
          <button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
           <img src="{{ URL::asset('/front/images/close-icon.png') }}">
          </button>
          @endif
      </div>
      <div class="modal-body">
        <form class="modal_form" id="resetPassword" action="{{ url('api/forgotPassword') }}" method="post">
            {{ csrf_field() }}
            <div class="form-row">
                <div class="form-group col-md-12">
                    <input type="email" class="form-control colorb7 font16 height50" name="email" placeholder="Email Address*" required>
                </div>
            </div>
            <div class="modal-footer border-top-0 d-flex justify-content-center">
                <button type="submit" id="resetSbt" class="reset font16 fontSemiBold colorWhite bgTheme radius50 borderNone px-5 py-2 hover1 disable_button">Submit</button>
            </div>
        </form>
        </div>
    </div>
</div>

<!--- Social email signup --->
<div class="modal fade" id="social-signup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal_header border-bottom-0">
            <h5 class="modal-title text-center font18 font-weight-bold modal_title_rel" id="exampleModalLabel">
                <span>Signup</span>
            </h5>
            @if(isset($auth_user->id))
            <button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
            <img src="{{ URL::asset('/front/images/close-icon.png') }}">
           </button>
          @endif
      </div>
      <div class="modal-body">
        <form class="modal_form" id="socialSignup" action="{{ url('social-signup') }}" method="post">
            {{ csrf_field() }}
            <div class="form-row">
                <div class="form-group col-md-12">
                    <span class="text-danger">Enter your email id to register with our site</span>
                    <input type="email" class="form-control colorb7 font16 height50" required name="email" placeholder="Email Address*">
                </div>
            </div>
            <div class="modal-footer border-top-0 d-flex justify-content-center">
                <button type="submit" class="reset font16 fontSemiBold colorWhite bgTheme radius50 borderNone px-5 py-2 hover1">Submit</button>
            </div>
        </form>
        </div>
    </div>
    </div>
</div>
@include('front.share',['url'=> "http://127.0.0.1:8000/"])
@push('js')
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script>
$(document).ready(function(){
    $(".timer_div").hide();
    $('#sub_button').addClass('disabled');

$('#send_otp').on('click', function(){
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
        $(".timer_div").show();
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
        }

        if(d.errors.mobile_number != undefined){
            toastr.error(d.errors.mobile_number);
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



$('#opensignup').on('click', function(){

        setTimeout(function () {
            $("#pr_login").removeClass('active');
        $("#login").removeClass('active show');

        $("#pr_register").addClass('active');
        $("#register").addClass('active show');
        }, 5000);

});




});
</script>

@endpush
