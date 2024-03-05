@extends('front.flight.layouts.master')
@section('template_title','Flight Booking')
@section('content')
<div id="page-wrapper">
	<section class="wrapper2 render-QG quickGrab bgf7 py-5">
		<div class="container mobile-space-none">
			<div class="row listingProdGrid hotel-filter-details">
				<div class="col-lg-12 mb-4">
					<div class="fliter_box_inner successful">
					    <div class="row justify-content-center">
				            <div class="seccess-box text-center">

				            	<img src="{{asset('front/images/successful.png')}}">
				            	<h2 class="couppon-code">Flight <strong>{{$createBooking['bookingNumber']}}</strong> Booking is Confirmed </h2>
				            	<!-- <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry</p> -->
				            	<div class="text-center sucess-pop-button mt-5">
									<button onclick='window.location="{{route('flight')}}"' class="btn filter_btn">Go To Home</button>
					            	<button onclick='window.location="{{route('flight-my-bookings')}}"' class="btn filter_btn">My Booking</button>
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