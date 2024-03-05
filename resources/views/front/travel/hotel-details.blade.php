@extends('front.travel.layouts.master')
@section('template_title','Book Hotel - '.$hotel->name.' '.$hotel->type)
@section('content')
	<style type="text/css">
		.invalid-feedback{
			color: white;
		    font-size: 14px;
		    margin-top: 0;
		    background: #ff00009e;
		    padding: 1px 10px;
		}
		input[type="number"] {
		  -webkit-appearance: textfield;
		     -moz-appearance: textfield;
		          appearance: textfield;
		}
		input[type=number]::-webkit-inner-spin-button,
		input[type=number]::-webkit-outer-spin-button {
		  -webkit-appearance: auto;
		  appearance: auto;
		  -moz-appearance: auto;
		}

		.filter_btn:hover{
			color:#3ab54b !important;
			border-color: #3ab54b;
		}
		li.disabled{
			background: transparent !important;
		}
		button:disabled,
		button[disabled]{
			cursor: not-allowed;
		}
	</style>
	<div id="page-wrapper">
		<section class="wrapper1 render-QG quickGrab bgf7 py-3 py-md-4">
			<div class="container">
		        <div class="row explore-hotels">
		        	<div class="col-md-12">
		        		<div class="">
		        			<div class="row"><h2 class="px-3 py-2">Book Hotel - {{$hotel->name}} {{$hotel->type}}</h2></div>
				            <form action="submit" method="POST">
				            	<div class="form-in filters-results">
					            	<div class="row px-3">
						            	<div class="inout-control col-md-3">
						            		<div class="input-group">
						            			<span class="input-group-addon"><i class="fa fa-map-marker mr-3 location" aria-hidden="true"></i></span>
								            	<select name="state" id="state" class="form-control selectpiker" readonly>
											        <option value="{{Request::get('state')}}">{{Request::get('state')}}</option>
											    </select>
											</div>
										</div>
						            	<div class="inout-control col-md-3">
						            		<div class="input-group">
						            			<span class="input-group-addon"><i class="fa fa-map-marker mr-3 location" aria-hidden="true"></i></span>
								            	<select name="city" id="city" class="form-control selectpiker" readonly>
											        <option value="{{Request::get('city')}}">{{Request::get('city')}}</option>
											    </select>
											</div>
										</div>
										@if(Request::get('room_type')!=null)
										<div class="inout-control col-md-4">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-bed bed" aria-hidden="true"></i></span>
								            	<select name="room_type" id="room_type" class="form-control selectpiker" readonly>
											        <option value="{{implode(',',Request::get('room_type'))}}">{{implode(',',Request::get('room_type'))}}</option>
											    </select>
											</div>
										</div>
										<div class="inout-control col-md-2">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-bed gate" aria-hidden="true"></i></span>
								            	<select name="no_rooms" id="no_rooms" class="form-control selectpiker" readonly>
											        <option value="{{Request::get('no_rooms')}}">{{Config::get('constants.COUNTING.'.Request::get('no_rooms'))}}</option>
											    </select>
											</div>
										</div>
										@else
										<div class="inout-control col-md-3">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-bed profile" aria-hidden="true"></i></span>
							            		<input class="form-control" type="text" placeholder="No. of Adult" value="{{Config::get('constants.COUNTING.'.Request::get('no_adults'))}}" name="no_adults" id="no_adults" readonly>
							            	</div>
										</div>
										<div class="inout-control col-md-3">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-bed gate" aria-hidden="true"></i></span>
								            	<select name="no_rooms" id="no_rooms" class="form-control selectpiker" readonly>
											        <option value="{{Request::get('no_rooms')}}">{{Config::get('constants.COUNTING.'.Request::get('no_rooms'))}}</option>
											    </select>
											</div>
										</div>
										@endif
									</div>

									<div class="row px-3">
										@if(Request::get('room_type')!=null)
										<div class="inout-control col-md-3">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-bed profile" aria-hidden="true"></i></span>
							            		<input class="form-control" type="text" placeholder="No. of Adult" value="{{Config::get('constants.COUNTING.'.Request::get('no_adults'))}}" name="no_adults" id="no_adults" readonly>
							            	</div>
										</div>
										@endif
										<div class="inout-control col-md-3">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-bed children" aria-hidden="true"></i></span>
								            	<input class="form-control" name="no_child" id="no_child" type="text" placeholder="No. of Children" value="{{Request::get('no_child')??'No Child'}}" readonly>
								            </div>
										</div>
						            	<div class="inout-control col-md-3">
									        <div class="input-group" data-date-format="mm-dd-yyyy">
									        	<span class="input-group-addon"><i class="fa fa-heart calendar" aria-hidden="true"></i></span>
											    <input class="form-control" name="check_in" id="check_in" type="text" placeholder="Check-In --/--/--" value="{{Request::get('check_in')}}" readonly />
											</div>
										</div>
										<div class="inout-control col-md-3">
							            	<div class="input-group" data-date-format="mm-dd-yyyy">
									        	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar calendar"></i></span>
											    <input class="form-control" name="check_out" id="check_out" type="text" placeholder="Check-Out --/--/--" value="{{Request::get('check_out')}}" readonly />
											</div>
										</div>
									</div>
								</div>
						    </form>
		        		</div>
		        	</div>
		        </div>
			</div>
		</section>
		<section class="wrapper2 render-QG quickGrab bgf7 pb-5">
			<div class="container mobile-space-none">
				<div class="row listingProdGrid hotel-filter-details">
					<div class="col-lg-12 near-by-hotels">
						<div class="fliter_box_inner">
						    <div class="row align-items-center">
						        <div class="col-md-3">
						        	<img src="{{$hotel->image}}">
						        </div>
						        <div class="col-md-5 hotel-suites">
						        	<h5>{{$hotel->name}} {{$hotel->type}}</h5>
						        	<div class="row align-items-center mb-3">
							        	<div class="reating mr-3 ml-3">
							        		<span class="@if($hotel->rating>=1) close @else open @endif"></span>
							        		<span class="@if($hotel->rating>=2) close @else open @endif"></span>
							        		<span class="@if($hotel->rating>=3) close @else open @endif"></span>
							        		<span class="@if($hotel->rating>=4) close @else open @endif"></span>
							        		<span class="@if($hotel->rating==5) close @else open @endif"></span>
							        	</div>
							        	<span class="reviews mr-2">{{$hotel->rating_count}} Reviews</span>
							        	<span class="location"><i class="fa fa-map-marker" aria-hidden="true"></i>{{$hotel->city}}</span>
							        </div>
							        <div class="popular">
							        	<i class="fa fa-map-marker" aria-hidden="true"></i>
							        	<span>Popular! Last Booked {{intval($hotel->last_booked)}} Hour Ago</span>
							        </div>
							        <div class="heighly-review">
							        	<i class="fa fa-map-marker" aria-hidden="true"></i>
							        	<span>Highly Reviewed By Couples</span>
							        </div>
							        <div class="near-by">
							        	<i class="fa fa-map-marker" aria-hidden="true"></i>
							        	<span>{{$hotel->distance}} kms from {{$hotel->city}} City</span>
							        </div>
						        </div>
						        <div class="col-md-4">
						        	<div class="prize">
						        		<h2>{{$currency}} {{number_format($hotel->price)}}</h2>
						        		<p>+ {{Config::get('constants.tax')}}% taxes & fees Per Night</p>
						        		<span>No Cost EMI</span>
						        		<strong class="total-prize">{{$currency}} {{number_format($hotel->total)}}</strong>
						        	</div>
						        </div>
						    </div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<section class="py-5">
			<div class="container">
				<div class="row">
			        <div class="wizard">
			            <div class="wizard-inner">
			                <div class="connecting-line"></div>
		                	<ul class="nav nav-pills nav-wizard" id="tabs">
						    	<li>
						    		<a href="#step-1" class="active show" id="step1" data-toggle="tab"><span>1</span>Guest Details</a>
						    	</li>
						    	<li class="disabled">
						    		<a href="#step-2" id="step2" data-toggle="tab"><span>2</span>Special Request</a>
						    	</li>
						    	<li class="disabled">
						    		<a href="#step-3" id="step3" class="" data-toggle="tab"><span>3</span>Payment</a>
						    	</li>
							</ul>

							<form method="POST" action="{{route('book-hotel')}}" class="checkout-form">
								{{csrf_field()}}
								<input type="hidden" name="check_in" value="{{Request::get('check_in')}}">
								<input type="hidden" name="check_out" value="{{Request::get('check_out')}}">
								<input type="hidden" name="no_rooms" value="{{Request::get('no_rooms')}}">
								<input type="hidden" name="no_adults" value="{{Request::get('no_adults')}}">
								<input type="hidden" name="no_child" value="{{Request::get('no_child')}}">
								<input type="hidden" name="hotel_id" value="{{$hotel->id}}">
								<div class="tab-content">
									<div class="tab-pane active" id="step-1">
										<div class="row explore-hotels mt-5">
											<div class="col-md-12">
								        		<div class="">
									            	<div class="form-in filters-results">
										            	<div class="row px-3">
										            		<div class="form-group inout-control col-md-12 radio-button">
										            			<p>I’m Booking for <span class="text-danger"> *</span></p>
																<input type="radio" id="own" name="booking_for" value="own">
																<label for="own">Myself</label>
																<input type="radio" id="oth" name="booking_for" value="oth">
																<label for="oth">Someone Else</label><br>
										            		</div>
															<div class="inout-control col-md-2">
																<div class="form-group input-group">
												            		<select class="form-control" name="title" id="user_title">
												            			<option value="">Select Salutation <span class="text-danger"> *</span></option>
												            			<option value="Mr">Mr.</option>
												            			<option value="Ms">Ms.</option>
												            		</select>
												            	</div>
															</div>
															<div class="inout-control col-md-5">
																<div class="form-group input-group">
												            		<input class="form-control" type="text" placeholder="First Name *" name="first_name" id="first_name">
												            	</div>
															</div>
															<div class="inout-control col-md-5">
																<div class="form-group input-group">
												            		<input class="form-control" type="text" placeholder="Last Name *" name="last_name" id="last_name">
												            	</div>
															</div>
															<div class="inout-control col-md-6">
																<div class="form-group input-group">
												            		<input class="form-control numberInput" type="text" placeholder="Mobile No. *" name="phone" id="user_phone" minlength="10" maxlength="10">
												            	</div>
															</div>
															<div class="inout-control col-md-6">
																<div class="form-group input-group">
												            		<input class="form-control" type="email" placeholder="Email *" name="email" id="user_email">
												            	</div>
															</div>
															<div class="inout-control col-md-12 mt-2">
																<input type="Checkbox" id="gst" name="gst">
																<label for="gst">Enter GST Details (Optional)</label>
										            		</div>
										            		<div id="gstrow" class="d-none w-100" style="flex-flow: wrap;">
											            		<div class="inout-control col-md-6">
																	<div class="form-group input-group">
													            		<input class="form-control" type="text" placeholder="Enter Registration No. *" id="gst_registration" name="registration" maxlength="100">
													            	</div>
																</div>
																<div class="inout-control col-md-6">
																	<div class="form-group input-group">
													            		<input class="form-control" type="text" placeholder="Enter Company Name *" name="company_name" id="company_name" maxlength="100">
													            	</div>
																</div>
																<div class="inout-control col-md-12">
																	<div class="form-group input-group">
													            		<input class="form-control" type="text" placeholder="Enter Company Address *" name="company_address" id="company_address" maxlength="250">
													            	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="row px-3 mt-3 checkout-next">
														<button type="button" id="step1next" class="stepbtn btn filter_btn">Next</button>
													</div>
								        		</div>
								        	</div>
								        </div>
								    </div>

								    <div class="tab-pane" id="step-2">
								    	<div class="row explore-hotels mt-5">
											<div class="col-md-12">
								        		<div class="">
									            	<div class="form-in filters-results">
										            	<div class="row px-3">
										            		<div class="inout-control col-md-12 mt-2">
																<input type="Checkbox" id="smoking" name="special_request[]" value="Smoking Room">
																<label for="smoking">Smoking Room</label>
																<input type="Checkbox" id="late" name="special_request[]" value="Late Check-in">
																<label for="late">Late Check-in</label>
																<input type="Checkbox" id="early" name="special_request[]" value="Early Check-in">
																<label for="early">Early Check-in</label>
																<input type="Checkbox" id="high" name="special_request[]" value="Room on a high floor">
																<label for="high">Room on a high floor</label>
																<input type="Checkbox" id="bed" name="special_request[]" value="Large bed">
																<label for="bed">Large bed</label>
										            		</div>
															<div class="inout-control col-md-12 mt-5 radio-button">
																<p>Any Other Request?</p>
															</div>
															<div class="inout-control col-md-12">
																<div class="input-group">
												            		<textarea class="form-control" name="other_request" id="other_request"></textarea>
												            	</div>
															</div>
														</div>
													</div>
													<div class="row px-3 mt-3 checkout-next">
														<button type="button" id="step2next" class="stepbtn btn filter_btn">Next</button>
													</div>
								        		</div>
								        	</div>
								        </div>
								    </div>

								    <div class="tab-pane" id="step-3">
								    	<div class="row explore-hotels mt-5">
											<div class="col-md-12">
								        		<div class="">
									            	<div class="form-in filters-results payments-option">
										            	<div class="row px-3">
															<div class="inout-control col-md-12 ml-4 radio-button">
																<p>Payment Options <span class="text-danger"> *</span></p>
															</div>
															<div class="col-md-4">
																<div class="fliter_box_inner">
																	<div class="upi pm">
																		<h5>UPI</h5>
																		<p>Pay Directly From Your Bank Account</p>
																	</div>
																</div>
															</div>
															<div class="col-md-4">
																<div class="fliter_box_inner">
																	<div class="credit-card pm">
																		<h5>Credit/Debit/ATM Card</h5>
																		<p>Visa, MasterCard, Amex, Discover</p>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="form-group input-group">
														<input type="hidden" class="form-control" id="payment_method" name="payment_method">
													</div>
													<div class="col-md-12">
														<div class="row align-items-center justify-content-between mt-3 mb-3 checkout-prise">
															<h5>Total Payment</h5>
															<h2>{{$currency}} {{number_format($hotel->total)}}</h2>
														</div>
													</div>

													<div class="col-md-12 d-none" id="card">
														<div class="row">
															<div class="col-md-12 p-0"><h5>Fill Card Details <span class="text-danger"> *</span></h5></div>
															<div class="inout-control col-md-4 checkbox-upi-input">
																<div class="form-group input-group pl-2">
													            	<select name="payment_type" class="form-control selectpiker" id="payment_type">
																        <option value="">Select Card Type *</option>
																        <option value="debit_card">Debit Card</option>
																        <option value="credit_card">Credit Card</option>
																    </select>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="inout-control col-md-4 checkbox-upi-input">
																<div class="form-group input-group pl-2">
													            	<select name="card_type" id="card_type" class="form-control selectpiker">
													            		<option value="">Select Card</option>
																        <option value="visa">Visa</option>
																        <option value="amex">Amex</option>
																        <option value="master">Mastercard</option>
																        <option value="discover">Discover</option>
																    </select>
																</div>
															</div>
															<div class="inout-control col-md-4 checkbox-upi-input">
																<div class="form-group input-group">
													            	<input class="form-control" type="text" placeholder="Enter Card Number *" name="card_no" id="card_no" maxlength="16" minlength="16">
													            </div>
															</div>
															<div class="inout-control col-md-4 checkbox-upi-input">
																<div class="form-group input-group">
													            	<input class="form-control" type="text" placeholder="Enter Your Name On Card *" name="card_name" id="card_name" maxlength="100">
													            </div>
															</div>
														</div>
														<div class="row">
															<div class="inout-control col-md-4">
														        <div class="input-group form-group">
																    <select class="form-control" name="card_month" id="card_month">
																    	<option value="">Select Month *</option>
																    	<option value="01">January</option>
																    	<option value="02">February</option>
																    	<option value="03">March</option>
																    	<option value="04">April</option>
																    	<option value="05">May</option>
																    	<option value="06">June</option>
																    	<option value="07">July</option>
																    	<option value="08">August</option>
																    	<option value="09">September</option>
																    	<option value="10">October</option>
																    	<option value="11">November</option>
																    	<option value="12">December</option>
																    </select>
																</div>
															</div>
															<div class="inout-control col-md-4">
														        <div class="input-group form-group">
																    <select class="form-control" name="card_year" id="card_year">
																    	@for($y=date('Y',strtotime('+1 month')); $y < date('Y')+20;$y++)
																    		<option value="{{$y}}">{{$y}}</option>
																    	@endfor
																    </select>
																</div>
															</div>
															<div class="inout-control col-md-4 checkbox-upi-input">
																<div class="input-group form-group">
													            	<input class="form-control" name="cvv" id="cvv" type="password" placeholder="Enter Card CVV *" maxlength="3" minlength="3">
													            </div>
															</div>
														</div>
													</div>


													<div class="inout-control col-md-12 checkbox-upi-input d-none" id="upi">
														<h5>Enter UPI ID <span class="text-danger"> *</span></h5>
														<div class="input-group form-group">
											            	<input class="form-control" type="text" placeholder="Enter UPI ID" name="upi" id="upi_id">
															<span class="input-group-addon">
																<img src="{{asset('front/images/travel/upi-logos.png')}}" alt="upi-logos">
															</span>
											            </div>
													</div>

													<div class="row px-3 mt-3 checkout-next">
														<button type="submit" id="submitBtn" class="btn filter_btn">Submit</button>
													</div>
								        		</div>
								        	</div>
								        </div>
								    </div>
								</div>
							</form>
						</div>
			        </div>
			   </div>
			</div>
		</section>
	</div>


	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="{{URL::asset('/js/jquery.validate.min.js')}}"></script>
    <script src="{{URL::asset('/js/jquery.validate-init.js')}}"></script>
    <script type="text/javascript">

		function nextTab(elem) {
		    $(elem).next().find('a[data-toggle="tab"]').click();
		}
		function prevTab(elem) {
		    $(elem).prev().find('a[data-toggle="tab"]').click();
		}
    	$(document).ready(function(){


		    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {

		        var $target = $(e.target);

		        if ($target.parent().hasClass('disabled')) {
		            return false;
		        }
		    });

		    $(".next-step").click(function (e) {

		        var $active = $('.wizard .nav-tabs li.active');
		        $active.next().removeClass('disabled');
		        nextTab($active);

		    });
		    $(".prev-step").click(function (e) {

		        var $active = $('.wizard .nav-tabs li.active');
		        prevTab($active);

		    });

		    $('#tabs').tabs();


			function showErrors(errorMessage, errormap, errorlist) {
			    var val = this;
			    errormap.forEach(function(error, index) {
			        val.settings.highlight.call(val, error.element, val.settings.errorClass, val.settings.validClass);
			        $(error.element).siblings("span.field-validation-valid, span.field-validation-error").html($("<span></span>").html(error.message)).addClass("field-validation-error").removeClass("field-validation-valid").show();
			    });
			}

			jQuery.validator.addMethod("invalidUpi", function(value, element, param) {
			    return ($.inArray(value, param) >= 0);
			}, function() {
			    return "Invalid UPI ID"
			});

			jQuery.validator.addMethod("invalidCard", function(value, element, param) {
			    return ($.inArray(value, param[0][param[1]][param[2]]) >= 0);
			}, function() {
			    return "Invalid Card Details"
			});

			jQuery.validator.addMethod("invalidgst", function(value, element, param) {
			    return value.trim()==param;
			}, function() {
			    return "Invalid GST Details"
			});

    		var validator = $(".checkout-form").validate({
	            ignore: '.tab-pane:not(.active) :input',
	            errorClass: "invalid-feedback animated fadeInDown",
	            errorElement: "div",
	            errorPlacement: function(e, a) {
	                if (jQuery(a).closest(".form-group").find('.invalid-feedback').length > 0)
	                	jQuery(a).closest(".form-group").find('.invalid-feedback').remove();

	                jQuery(a).closest(".form-group").append(e)

	                var name = jQuery(a).attr('name');
	                jQuery(e).attr('id', 'invalid-' + name);
	            },
	            highlight: function(e) {
	                jQuery(e).closest(".form-group").removeClass("is-invalid").addClass("is-invalid")
	            },
	            success: function(e) {
	                jQuery(e).closest(".form-group").removeClass("is-invalid"), jQuery(e).remove()
	            },
	            rules: {
	            	booking_for:{
	            		required:!0
	            	},
	            	title:{
	            		required:!0
	            	},
	            	first_name:{
	            		required:!0
	            	},
	            	last_name:{
	            		required:!0
	            	},
	            	phone:{
	            		required:!0,
	            		digits:!0
	            	},
	            	email:{
	            		required:!0,
	            		email:!0
	            	},
	            	payment_method:{
	            		required:!0
	            	}
	            },
	            messages: {
	            	booking_for:{
	            		required:"Let us know for whom you are booking the hotel"
	            	},
	            	title:{
	            		required:"Please select Salutation of the user"
	            	},
	            	first_name:{
	            		required:"Please provide First Name of the user"
	            	},
	            	last_name:{
	            		required:"Please provide Last Name of the user"
	            	},
	            	phone:{
	            		required:"Please provide Contact Number of the user to ease the communication (if required)",
	            		digits:"Contact Number is invalid"
	            	},
	            	email:{
	            		required:"Please provide Email ID of the user to ease the communication (if required)",
	            		email:"Email ID is invalid"
	            	},
	            	payment_method:{
	            		required:"Please select your payment method"
	            	},
	            	registration:{
	            		required:"Please provide your GST Registration No."
	            	},
	            	company_name:{
	            		required:"Please provide your registered Company Name"
	            	},
	            	company_address:{
	            		required:"Please provide your registered Company Address"
	            	},
	            	upi:{
	            		required:"Please provide your UPI ID"
	            	},
	            	card_year:{
	            		required:"Please provide your Card Expiry Date"
	            	},
	            	card_month:{
	            		required:"Please provide your Card Expiry Date"
	            	},
	            	payment_type:{
	            		required:"Please select your card type"
	            	},
	            	card_type:{
	            		required:"Please select your card"
	            	},
	            	card_no:{
	            		required:"Please provide your card number"
	            	},
	            	card_name:{
	            		required:"Please provide name on your card"
	            	},
	            	cvv:{
	            		required:"Please provide your Card's security code"
	            	}
	            }
	        })

	        $('[name=gst]').click(function(){
	        	if($(this).is(":checked")){
	        		$('#gstrow').removeClass('d-none').addClass('d-flex');
	        		$('[name=registration]').rules('add','required');
	        		$('[name=registration]').rules('add',{invalidgst: "{{Config::get('constants.gst.registration_no')}}"});
	        		$('[name=company_name]').rules('add','required');
	        		$('[name=company_address]').rules('add','required');
	        		$('[name=company_name]').rules('add',{invalidgst: "{{Config::get('constants.gst.name')}}"});
	        		$('[name=company_address]').rules('add',{invalidgst: "{{Config::get('constants.gst.address')}}"});
	        	}else{
	        		$('#gstrow').addClass('d-none').removeClass('d-flex');
	        		$('[name=registration]').rules('remove','required');
	        		$('[name=company_name]').rules('remove','required');
	        		$('[name=company_address]').rules('remove','required');
	        		$('[name=registration]').rules('remove','invalidgst');
	        		$('[name=company_name]').rules('remove','invalidgst');
	        		$('[name=company_address]').rules('remove','invalidgst');
	        	}
	        })

	        $('.stepbtn').click(function(){
	        	if($('.checkout-form').valid()){

		        	var $active = $('.wizard .nav li a[href="#' + $(this).closest('.tab-pane').attr('id') + '"]');

		        	$active.closest('li').next().removeClass('disabled')
		        	nextTab($active.closest('li'));
	        	}
	        })

	        $('.pm').click(function(){
	        	var upis = <?php echo json_encode(Config::get('constants.upi')) ?>;


	        	if($(this).hasClass('upi')){
	        		$('[name=payment_method]').val('upi');
	        		$('#card').addClass('d-none');
	        		$('#upi').removeClass('d-none');
	        		$('[name=upi]').rules('add','required');
	        		$('[name=upi]').rules('add', {invalidUpi: upis});
	        		$('[name=card_year]').rules('remove','required');
	        		$('[name=card_month]').rules('remove','required');
	        		$('[name=payment_type]').rules('remove','required');
	        		$('[name=card_type]').rules('remove','required');
	        		$('[name=card_no]').rules('remove','required');
	        		$('[name=card_no]').rules('remove','invalidCard');
	        		$('[name=card_name]').rules('remove','required');
	        		$('[name=cvv]').rules('remove','required');
	        	}
	        	else{

	        		$('[name=payment_method]').val('card');
	        		$('#card').removeClass('d-none');
	        		$('#upi').addClass('d-none');
	        		$('[name=upi]').rules('remove','required');
	        		$('[name=upi]').rules('remove','invalidUpi');
	        		$('[name=card_year]').rules('add','required');
	        		$('[name=card_month]').rules('add','required');
	        		$('[name=payment_type]').rules('add','required');
	        		$('[name=card_type]').rules('add','required');
	        		$('[name=card_no]').rules('add','required');
	        		$('[name=card_name]').rules('add','required');
	        		$('[name=cvv]').rules('add','required');
	        	}
				// $('.filter_btn').attr('disabled',true);
	        	$('[name=payment_method]').valid();
	        })

	        /*$('[name=upi]').on('change input', function(){
	        	var values = <?php echo json_encode(Config::get('constants.upi')) ?>;

	        	if($.inArray($(this).val(),values) < 0)
	        	{
				    $('.filter_btn').attr('disabled',true);
	        	}else{
				    $('.filter_btn').attr('disabled',false);
	        	}
	        })*/

	        $('[name=card_no]').on('change input', function(){
	        	var values = <?php echo json_encode(Config::get('constants')) ?>;
	        	var payment_type=$('[name=payment_type]').val();
	        	var card_type=$('[name=card_type]').val();

	        	if($('[name=payment_type]').valid() && $('[name=card_type]').valid())
	        		$('[name=card_no]').rules('add',{invalidCard: [values, payment_type, card_type]});

	        	/*if($(this).val()!='' && $(this).valid() && $.inArray($(this).val(),values[payment_type][card_type]) < 0)
	        	{
				    $('.filter_btn').attr('disabled',true);
	        	}
	        	else{
				    $('.filter_btn').attr('disabled',false);
	        	}*/
	        })

	        $('[name=payment_type]').change(function(){
	        	var values = <?php echo json_encode(Config::get('constants')) ?>;
	        	var payment_type=$('[name=payment_type]').val();
	        	var card_type=$('[name=card_type]').val();

	        	if($('[name=payment_type]').valid() && $(this).valid())
	        		$('[name=card_no]').rules('add',{invalidCard: [values, payment_type, card_type]});

	        	/*if($('[name=card_no]').val()!='' && $('[name=card_no]').valid() && $.inArray($('[name=card_no]').val(),values[payment_type][card_type]) < 0)
	        	{
				    $('.filter_btn').attr('disabled',true);
	        	}
	        	else{
				    $('.filter_btn').attr('disabled',false);
	        	}*/
	        })

	        $('[name=card_type]').change(function(){
	        	var values = <?php echo json_encode(Config::get('constants')) ?>;
	        	var payment_type=$('[name=payment_type]').val();
	        	var card_type=$('[name=card_type]').val();

	        	if($('[name=payment_type]').valid() && $(this).valid())
	        		$('[name=card_no]').rules('add',{invalidCard: [values, payment_type, card_type]});

	        	/*if($('[name=card_no]').val()!='' && $('[name=card_no]').valid() && $.inArray($('[name=card_no]').val(),values[payment_type][card_type]) < 0)
	        	{
				    $('.filter_btn').attr('disabled',true);
	        	}
	        	else{
				    $('.filter_btn').attr('disabled',false);
	        	}*/
	        })

	        $('[name=card_year]').change(function(){

        		$('[name=card_month]').valid();
        		$('[name=card_year]').valid();
	        	if($(this).val() != '' && $('[name=card_month]').val() != ''){
	        		var year = $(this).val();
	        		var month = $('[name=card_month]').val();

	        		if((year+'-'+month) < "{{date('Y-m')}}"){

		        		validator.showErrors({
					        "card_month": "Invalid Expiry Date"
					    });
				    	$('.filter_btn').attr('disabled',true);
	        		}else{
	        			$('[name=card_month]').closest('.input-group').removeClass('is-invalid');
	        			$('[name=card_month]').closest('.input-group').find('.invalid-feedback').remove();
				    	$('.filter_btn').attr('disabled',false);
	        		}
	        	}
	        })
	        $('[name=card_month]').change(function(){

        		$('[name=card_month]').valid();
        		$('[name=card_year]').valid();
	        	if($(this).val() != '' && $('[name=card_year]').val() != ''){
	        		var month = $(this).val();
	        		var year = $('[name=card_year]').val();

	        		if((year+'-'+month) < "{{date('Y-m')}}"){

		        		validator.showErrors({
					        "card_month": "Invalid Expiry Date"
					    });
				    	$('.filter_btn').attr('disabled',true);
	        		}else{
	        			$('[name=card_month]').closest('.input-group').removeClass('is-invalid');
	        			$('[name=card_month]').closest('.input-group').find('.invalid-feedback').remove();
				    	$('.filter_btn').attr('disabled',false);
	        		}
	        	}
	        })
    	})

    </script>
@endsection