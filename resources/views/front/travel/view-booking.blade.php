@extends('front.travel.layouts.master')
@section('template_title','Booking Details - #'.$book->booking_code)
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
		        			<div class="row"><h2 class="px-3 py-2">Booking #{{$book->booking_code}}</h2></div>
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
								    <div class="row explore-hotels booking-date">
								    	<div class="inout-control col-md-6 mobile-space-none">
									        <div class="input-group" data-date-format="mm-dd-yyyy">
									        	<span class="input-group-addon"><i class="fa fa-heart calendar" aria-hidden="true"></i></span>
											    <input class="form-control" type="text" placeholder="Check-In - {{date('d M, Y',strtotime($book->check_in))}}" readonly />
											</div>
										</div>
										<div class="inout-control col-md-6 mobile-space-none">
									        <div class="input-group" data-date-format="mm-dd-yyyy">
									        	<span class="input-group-addon"><i class="fa fa-heart calendar" aria-hidden="true"></i></span>
											    <input class="form-control" type="text" placeholder="Check-Out - {{date('d M, Y',strtotime($book->check_out))}}" readonly />
											</div>
										</div>
								    </div>
						        </div>
						        <div class="col-md-4">
						        	<div class="my-booking @if($book->status=='confirmed') success @endif prize">
						        		@if($book->status=='cancelled')
								        	<button type="submit" class="cancle btn filter_btn mb-3">Cancelled</button>
						        		@elseif($book->status=='confirmed')
						        			<button type="submit" class="mb-3 edit btn filter_btn">Confirmed</button>
						        		@endif
						        		<h2>{{$currency}} {{number_format($book->price)}}</h2>
						        		<p>+ {{Config::get('constants.tax')}}% taxes & fees Per Night</p>
						        		<span>No Cost EMI</span>
						        		<strong class="total-prize">{{$currency}} {{number_format($book->price_with_tax)}}</strong>
						        		@if($book->status=='pending')
						        		<button type="submit" class="edit btn filter_btn" onclick="window.location='{{route('edit-booking',['id'=>$book->id])}}'">Edit</button>
						        		<a href="{{route('cancel-booking',['id'=>$book->id])}}" onclick="return confirm('Are you sure you want to cancel this booking?')" class="cancle btn filter_btn">Cancel</a>
						        		@endif
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
@endsection