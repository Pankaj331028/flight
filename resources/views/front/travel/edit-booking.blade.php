@extends('front.travel.layouts.master')
@section('template_title','Edit Booking - #'.$book->booking_code)
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
	</style>
	<div id="page-wrapper">
		<section class="wrapper1 render-QG quickGrab bgf7 py-3 py-md-4">
			<div class="container">
		        <div class="row explore-hotels">
		        	<div class="col-md-12">
		        		<div class="">
		        			<div class="row"><h2 class="px-3 py-2">Edit Booking #{{$book->booking_code}}</h2></div>
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
						    <div class="row">
						        <div class="col-md-3">
						        	<img src="{{$book->hotel->image}}">
						        </div>
						        <div class="col-md-5 hotel-suites">
						        	<h5>{{$book->hotel->name}} {{$book->hotel->type}}</h5>
						        	<div class="row align-items-center mb-3">
							        	<div class="reating mr-3 ml-3">
							        		<span class="@if($book->hotel->rating>=1) close @else open @endif"></span>
							        		<span class="@if($book->hotel->rating>=2) close @else open @endif"></span>
							        		<span class="@if($book->hotel->rating>=3) close @else open @endif"></span>
							        		<span class="@if($book->hotel->rating>=4) close @else open @endif"></span>
							        		<span class="@if($book->hotel->rating==5) close @else open @endif"></span>
							        	</div>
							        	<span class="reviews mr-2">{{$book->hotel->rating_count}} Reviews</span>
							        	<span class="location"><i class="fa fa-map-marker" aria-hidden="true"></i>{{$book->hotel->city}}</span>
							        </div>
							        <div class="popular">
							        	<i class="fa fa-map-marker" aria-hidden="true"></i>
							        	<span>Popular! Last Booked {{intval($book->hotel->last_booked)}} Hour Ago</span>
							        </div>
							        <div class="heighly-review">
							        	<i class="fa fa-map-marker" aria-hidden="true"></i>
							        	<span>Highly Reviewed By Couples</span>
							        </div>
							        <div class="near-by">
							        	<i class="fa fa-map-marker" aria-hidden="true"></i>
							        	<span>{{$book->hotel->distance}} kms from {{$book->hotel->city}} City</span>
							        </div>
							        <form class="form-valide" action="{{route('update-booking',['id'=>$book->id])}}" method="POST">
							        	{{csrf_field()}}
						            	<div class="form-in py-3 filters-results">
						            		<div class="row px-4">
						            			<h5>Update Booking</h5>
						            		</div>
							            	<div class="row px-3">
							            		<input type="hidden" name="booking_no" value="{{$book->booking_code}}">
								            	<div class="inout-control col-md-6">
											        <div class="input-group form-group date" data-date-format="mm-dd-yyyy">
											        	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar calendar" aria-hidden="true"></i></span>
													    <input class="form-control from" name="check_in" type="text" placeholder="Check-In --/--/--" value="{{$book->check_in}}" required />
													</div>
												</div>
												<div class="inout-control col-md-6">
									            	<div class="input-group form-group date" data-date-format="mm-dd-yyyy">
											        	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar calendar"></i></span>
													    <input class="form-control to" name="check_out" type="text" placeholder="Check-Out --/--/--" value="{{$book->check_out}}" required />
													</div>
												</div>
											</div>
											<div class="row px-3 py-2">
												<button type="submit" class="edit btn filter_btn">Confirm</button>
											</div>
										</div>
								    </form>
						        </div>
						        <div class="col-md-4">
						        	<div class="prize">
						        		<h2>{{$currency}} {{number_format($book->price)}}</h2>
						        		<p>+ {{Config::get('constants.tax')}}% taxes & fees Per Night</p>
						        		<span>No Cost EMI</span>
						        		<strong class="total-prize">{{$currency}} {{number_format($book->price_with_tax)}}</strong>
						        	</div>
						        </div>
						    </div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
    <script src="{{URL::asset('/js/jquery.validate.min.js')}}"></script>
    <script src="{{URL::asset('/js/jquery.validate-init.js')}}"></script>
    <script type="text/javascript">
    	$(document).ready(function(){

    		@php

				$checkin = date_create($book->check_in);
				$checkout = date_create($book->check_out);

				$days = date_diff($checkin, $checkout);

    		@endphp

            $('.from').datepicker({
                dateFormat: "yy-mm-dd",
                minDate: new Date() ,
                onClose: function (selectedDate) {
                    var date = new Date(selectedDate);
                    var date1 = new Date(date.setDate(date.getDate()));
                    var date = new Date(date.setDate(date.getDate() + Number("{{$days->days}}")));

                    var month = (date.getMonth()+1);
                    var day = date.getDate();
                    month = month < 10 ? '0'+month : month;
                    day = day < 10 ? '0'+day : day;
                    var nextdate = date.getFullYear() + '-' + month + '-' + day;

                    var month1 = (date1.getMonth()+1);
                    var day1 = date1.getDate();
                    month1 = month1 < 10 ? '0'+month1 : month1;
                    day1 = day1 < 10 ? '0'+day1 : day1;
                    var nextdate1 = date1.getFullYear() + '-' + month1 + '-' + day1;

                    $('.to').datepicker( "option", "minDate", nextdate1 );
                    $('.to').val(nextdate);
                    $(this).valid();
                }
            });

            $('.to').datepicker({
                dateFormat: "yy-mm-dd",
                minDate: new Date() ,
                onClose: function (selectedDate) {
                    var date = new Date(selectedDate);
                    var date = new Date(date.setDate(date.getDate() - Number("{{$days->days}}")));

                    var month = (date.getMonth()+1);
                    var day = date.getDate();
                    month = month < 10 ? '0'+month : month;
                    day = day < 10 ? '0'+day : day;
                    var nextdate = date.getFullYear() + '-' + month + '-' + day;

                    // $('.to').datepicker( "option", "minDate", nextdate );
                    $('.from').val(nextdate);
                    $(this).valid();
                }
            });
    	})
    </script>
@endsection