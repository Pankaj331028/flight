@extends('front.travel.layouts.master')
@section('template_title','Edit Booking - #'.$book['bookingNumber'])
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
		        			<div class="row"><h2 class="px-3 py-2">Edit Booking {{$book['bookingNumber']}}</h2></div>
		        		</div>
		        	</div>
		        </div>
			</div>
		</section>
		<section class="wrapper2 render-QG quickGrab bgf7 pb-5">
			<div class="container mobile-space-none">
				<div class="row listingProdGrid hotel-filter-details">
					<div class="col-lg-12 near-by-hotels">
						<div class="fliter_box_inner mb-4">
							<div class="align-items-center">
								<form class="  form-valide" action="{{route('flight-update-booking',['id'=>$book['bookingNumber']])}}" method="POST">
							        	{{csrf_field()}}
									<div class="row mx-auto">
										<div class="col-md-8 hotel-suites">
											<div class="row align-items-center">
												<div class="col-md-5 mobile-space-none">
													@if ($getAirlineDetails !== null && isset($book['airlineNames'][0]))
													<img src="data:image/png;base64,{{ $getAirlineDetails[$book['airlineNames'][0]]['airlineLogo'] }}" alt="" class="airlineImg">
													@endif
												</div>
												<div class="col-md-7 mobile-space-none">
													<a href="{{route('flight-view-booking',['no'=>$book['bookingNumber']])}}" target="_blank">
														<div class="room-code">
															<span>{{$book['bookingNumber']}}</span>
														</div>
														@if ($flightNameArray !== null && isset($book['fromlocations'][0]))
														<h5>{{$flightNameArray[$book['fromlocations'][0]]}} - {{$flightNameArray[$book['tolocations'][0]]}}</h5>
														@endif
													</a>
												</div>
											</div>
											<div class="row explore-hotels booking-date">
												<div class="inout-control col-md-12 mobile-space-none">
													@if (!empty($book['depatureTimes'][0]))

													<div class="row mx-auto">
														<div class="col-md-5 mobile-space-none input-group" data-date-format="mm-dd-yyyy">  
														<input class="form-control" type="text" placeholder="depatureTimes : {{\Carbon\Carbon::createFromFormat('H:i', $book['arrivalTimes'][0])->format('h:i A')}}" readonly />
														</div>
														<div class="col-md-7 mobile-space-none">
															<input class="form-control" type="text" placeholder="ArrivalTimes : {{\Carbon\Carbon::createFromFormat('H:i', $book['arrivalTimes'][0])->format('h:i A')}}" readonly />
														</div>
													</div>
													@endif
													@if ( isset($book['deptDates'][0]))
													<div class="input-group form-group date" data-date-format="mm-dd-yyyy">
														<span class="input-group-addon"><i class="glyphicon glyphicon-calendar calendar" aria-hidden="true"></i></span>
														<input class="form-control from" name="DeptDate" type="text" placeholder="DeptDate --/--/--" value="{{$book['deptDates'][0]}}" required />
													</div>
													@endif
												</div>
												@php 
													$deptDateTimestamp = null;
													$currentTimestamp = null;
													if ( isset($book['deptDates'][0])){
														
														$deptDateTimestamp = strtotime($book['deptDates'][0] . ' ' . $book['depatureTimes'][0]);
														$currentTimestamp = time();
														
													}
												@endphp
												<div class="flightOuterBox">
													@if ( $book['webCheckin'] == false )
													@if($book['bookingStatus'] !='Cancelled')
														@if ( $deptDateTimestamp > $currentTimestamp && isset($book['deptDates'][0]))
														<a href="{{route('flight-web-checkin',['id'=>$book['bookingNumber']])}}"  class="cancle btn filter_btn">Web-Checkin</a>
														@endif
													@endif
													@else
													<a href="javascript:void(0)"  class=" btn filter_btn" disabled>Checked</a>
													@endif
												</div>
											</div>
										</div>
									
										<div class="col-md-4">
											<div class="my-booking @if($book['bookingStatus']=='confirmed') success @endif prize">
												@if($book['bookingStatus'] =='Cancelled')
													<button type="submit" class="cancle btn filter_btn mb-3">Cancelled</button>
												@endif
												<p>Total Amount Paid</p>
												<strong class="total-prize">Rs {{number_format($book['totalPrice'])}}</strong>
												<span>Payment By {{$book['payment_method'] == 'upi' ? 'UPI' : Config::get('constants.PAYMENT_METHOD.' . $book['payment_type'])}}</span>
												
												@if ( $deptDateTimestamp > $currentTimestamp && isset($book['deptDates'][0]))
												
												@if($book['bookingStatus'] =='Confirmed')
												<button type="submit" class="edit btn filter_btn" >Update</button>
												<a href="{{route('flight-cancel-booking',['id'=>$book['bookingNumber']])}}" onclick="return confirm('Are you sure you want to cancel this booking?')" class="cancle btn filter_btn">Cancel</a>
												@endif
												@endif
											</div>
										</div>
									</div>
								</from>
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
            $('.from').datepicker({
                dateFormat: "yy-mm-dd",
                minDate: new Date() ,
               
            });
    	})
    </script>
@endsection