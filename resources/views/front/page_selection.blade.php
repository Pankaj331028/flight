@extends('front.travel.layouts.master')
@section('template_title','Select Portal')
@section('content')
	<style type="text/css">
		.fliter_box_inner{
			cursor: pointer;
		}
	</style>
	<div id="page-wrapper">
		<section class="wrapper1 render-QG quickGrab bgf7 py-3 py-md-5">
			<div class="container mt-5 mb-5">
				<div class="text-center mb-5">
		            <h5 class="font35 font-weight-bold color11 welcome-heading">Welcome {{env('APP_NAME')}}, Please Choose ...</h5>
		            {{-- <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry</p> --}}
		        </div>
		        <div class="row justify-content-center welcome">
		        	@if (in_array('grocery', Session::get('access')))
		        	<div class="col-lg-4">
		        		<div class="fliter_box_inner text-center" data-href="{{route('grocery')}}">
		        			<img src="{{asset('front/images/travel/grocery.png')}}" alt="Grocery">
		        			<h3 class="mt-4">Grocery</h3>
		        		</div>
		        	</div>
		        	@endif
		        	@if (in_array('travel', Session::get('access')))
		        	<div class="col-lg-4">
		        		<div class="fliter_box_inner text-center" data-href="{{route('travel')}}">
		        			<img src="{{asset('front/images/travel/hotelbooking.png')}}" alt="hotel booking">
		        			<h3 class="mt-4">Hotel Booking</h3>
		        		</div>
		        	</div>
		        	@endif
		        	@if (in_array('flight', Session::get('access')))
					<div class="col-lg-4">
		        		<div class="fliter_box_inner text-center" data-href="{{route('flight')}}">
		        			<img src="{{asset('front/images/flight.jpg')}}" alt="hotel booking" width="250" height="174">
		        			<h3 class="mt-4">Flight Booking</h3>
		        		</div>
		        	</div>
		        	@endif
	        	</div>
			</div>
		</section>
	</div>

<script type="text/javascript">
    $(document).on('click','.fliter_box_inner',function(){
        $('.fliter_box_inner').removeClass('active');
        $(this).addClass('active');
        window.location=$(this).data('href');
    })
</script>
@endsection