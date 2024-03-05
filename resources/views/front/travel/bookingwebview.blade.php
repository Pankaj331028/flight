@extends('front.travel.layouts.withoutheader')
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
	.h2, h2 {
    margin-right: 10px;
    margin-bottom: 10px;
}
</style>
		<section class="">
			<div class="container">
				<div class="row">
			        <div class="wizard">
			            <div class="wizard-inner">
			                <div class="connecting-line"></div>
							<form method="POST" action="{{route('updateBookingwebview',['user_id' => $user_id,'id'=>$booking->id])}}" method="POST" class="checkout-form">
								{{csrf_field()}}
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
															<h2>{{$currency}} {{number_format($booking->price_with_tax)}}</h2>
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
																    <select class="form-control" name="month" id="card_month">
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
																    <select class="form-control" name="year" id="card_year">
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

{{--  --}}
													<div class="inout-control col-md-12 checkbox-upi-input d-none" id="upi">
														<h5>Enter UPI ID <span class="text-danger"> *</span></h5>
														<div class="input-group form-group">
											            	<input class="form-control" type="text" placeholder="Enter UPI ID" name="upi" id="upi_id">
															<span class="input-group-addon">
																<img src="{{asset('front/images/travel/upi-logos.png')}}" alt="upi-logos">
															</span>
											            </div>
													</div>

													<div class="row px-3 mt-3 mb-5 checkout-next">
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
	            ignore: '',
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

	            	payment_method:{
	            		required:"Please select your payment method"
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
	        		$('[name=year]').rules('remove','required');
	        		$('[name=month]').rules('remove','required');
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
	        		$('[name=year]').rules('add','required');
	        		$('[name=month]').rules('add','required');
	        		$('[name=payment_type]').rules('add','required');
	        		$('[name=card_type]').rules('add','required');
	        		$('[name=card_no]').rules('add','required');
	        		$('[name=card_name]').rules('add','required');
	        		$('[name=cvv]').rules('add','required');
	        	}
				// $('.filter_btn').attr('disabled',true);
	        	$('[name=payment_method]').valid();
	        })

	        $('[name=card_no]').on('change input', function(){
	        	var values = <?php echo json_encode(Config::get('constants')) ?>;
	        	var payment_type=$('[name=payment_type]').val();
	        	var card_type=$('[name=card_type]').val();

	        	if($('[name=payment_type]').valid() && $('[name=card_type]').valid())
	        		$('[name=card_no]').rules('add',{invalidCard: [values, payment_type, card_type]});
	        })

	        $('[name=payment_type]').change(function(){
	        	var values = <?php echo json_encode(Config::get('constants')) ?>;
	        	var payment_type=$('[name=payment_type]').val();
	        	var card_type=$('[name=card_type]').val();

	        	if($('[name=payment_type]').valid() && $(this).valid())
	        		$('[name=card_no]').rules('add',{invalidCard: [values, payment_type, card_type]});

	        })

	        $('[name=card_type]').change(function(){
	        	var values = <?php echo json_encode(Config::get('constants')) ?>;
	        	var payment_type=$('[name=payment_type]').val();
	        	var card_type=$('[name=card_type]').val();

	        	if($('[name=payment_type]').valid() && $(this).valid())
	        		$('[name=card_no]').rules('add',{invalidCard: [values, payment_type, card_type]});

	        })

	        $('[name=year]').change(function(){

        		$('[name=month]').valid();
        		$('[name=year]').valid();
	        	if($(this).val() != '' && $('[name=month]').val() != ''){
	        		var year = $(this).val();
	        		var month = $('[name=month]').val();

	        		if((year+'-'+month) < "{{date('Y-m')}}"){

		        		validator.showErrors({
					        "month": "Invalid Expiry Date"
					    });
				    	$('.filter_btn').attr('disabled',true);
	        		}else{
	        			$('[name=month]').closest('.input-group').removeClass('is-invalid');
	        			$('[name=month]').closest('.input-group').find('.invalid-feedback').remove();
				    	$('.filter_btn').attr('disabled',false);
	        		}
	        	}
	        })
	        $('[name=month]').change(function(){

        		$('[name=month]').valid();
        		$('[name=year]').valid();
	        	if($(this).val() != '' && $('[name=year]').val() != ''){
	        		var month = $(this).val();
	        		var year = $('[name=year]').val();

	        		if((year+'-'+month) < "{{date('Y-m')}}"){

		        		validator.showErrors({
					        "month": "Invalid Expiry Date"
					    });
				    	$('.filter_btn').attr('disabled',true);
	        		}else{
	        			$('[name=month]').closest('.input-group').removeClass('is-invalid');
	        			$('[name=month]').closest('.input-group').find('.invalid-feedback').remove();
				    	$('.filter_btn').attr('disabled',false);
	        		}
	        	}
	        })
    	})

    </script>
	@endsection