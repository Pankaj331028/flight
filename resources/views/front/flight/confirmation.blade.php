@extends('front.travel.layouts.master')
@section('template_title','Booking Confirmed')
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
	</style>
	<div id="page-wrapper">
		<section class="wrapper2 render-QG quickGrab bgf7 py-5">
			<div class="container mobile-space-none">
				<div class="row listingProdGrid hotel-filter-details">
					<div class="col-lg-12 mb-4">
						<div class="fliter_box_inner successful">
						    <div class="row justify-content-center">
					            <div class="seccess-box text-center">
					            	<img src="{{asset('front/images/travel/successful.png')}}">
					            	<h2 class="couppon-code" name="booking-code"><strong>#{{$booking_no}}</strong> Booking is Confirmed </h2>
					            	<p>Hotel <strong>{{$hotel}}</strong> is booked!</p>
					            	<div class="text-center sucess-pop-button mt-5">
						            	<button onclick='window.location="{{route('travel')}}"'  id="goToHome" class="btn filter_btn">Go To Home</button>
						            	<button onclick='window.location="{{route('my-bookings')}}"' id="myBooking" class="btn filter_btn">My Booking</button>
						            </div>
					            </div>
						    </div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>

@endsection