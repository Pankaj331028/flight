@extends('front.flight.layouts.master')
@section('template_title','Flight Booking')
@section('content')
<div id="page-wrapper">
	<section class="wrapper2 render-QG quickGrab bgf7 py-5">
		<div class="container mobile-space-none">
			<div class="row listingProdGrid hotel-filter-details">
				<div class="col-lg-3 mb-4">
					<div class="fliter_box_inner">
					    <div class="row">
					        <div class="col-12">
					            <div class="filter_box box_css booking-filter">
					            	<ul>
					            		<li>
					            			<a href="#" class="active">Confirmed Booking</a>
					            		</li>
					            		<li>
					            			<a href="#">My Bookings</a>
					            		</li>
					            		<li>
					            			<a href="#">Account Settings</a>
					            		</li>
					            		<li>
					            			<a href="#">Change Password</a>
					            		</li>
					            		<li>
					            			<a href="#">Logout</a>
					            		</li>
					            	</ul>
					            </div>
					        </div>
					    </div>
					</div>
				</div>
				<div class="col-lg-9 mb-4 near-by-hotels">
					<h4>My Bookings (3)</h4>
					<div class="fliter_box_inner mb-4">
					    <div class="row align-items-center">
					    	<div class="col-md-9 col-sm-8 hotel-suites">
					    		<div class="row align-items-center">
							        <div class="col-md-5 mobile-space-none">
							        	<img src="{{asset('front/images/hotel-filter-pic.png')}}">
							        </div>
							        <div class="col-md-7 mobile-space-none">
							        	<div class="room-code">
							        		<span>#TYUYG21234</span>
							        	</div>
							        	<h5>Hotel S-Presso Inn Suites</h5>
							        	<div class="row align-items-center mb-3">
								        	<div class="reating mr-3 ml-3">
								        		<span></span>
								        		<span></span>
								        		<span></span>
								        		<span></span>
								        		<span></span>
								        	</div>
								        	<span class="reviews mr-2">600 Reviews</span>
								        	<span class="location"><i class="fa fa-map-marker" aria-hidden="true"></i>Jaipur</span>
								        </div>
								        <div class="popular">
								        	<i class="fa fa-map-marker" aria-hidden="true"></i>
								        	<span>Popular! Last Booked 1 Houe Ago</span>
								        </div>
								        <div class="heighly-review">
								        	<i class="fa fa-map-marker" aria-hidden="true"></i>
								        	<span>Heighly Reviewed By Couples</span>
								        </div>
								        <div class="near-by">
								        	<i class="fa fa-map-marker" aria-hidden="true"></i>
								        	<span>1.5kms from Jaipur City</span>
								        </div>
							        </div>
							    </div>
							    <div class="row explore-hotels booking-date">
							    	<div class="inout-control col-md-6 mobile-space-none">
								        <div id="datepicker" class="input-group date" data-date-format="mm-dd-yyyy">
								        	<span class="input-group-addon"><i class="fa fa-heart calendar" aria-hidden="true"></i></span>
										    <input class="form-control" type="text" placeholder="Check-In - 9 Dec 2022" readonly />
										</div>
									</div>
									<div class="inout-control col-md-6 mobile-space-none">
								        <div id="datepicker" class="input-group date" data-date-format="mm-dd-yyyy">
								        	<span class="input-group-addon"><i class="fa fa-heart calendar" aria-hidden="true"></i></span>
										    <input class="form-control" type="text" placeholder="Check-Out - 12 Dec 2022" readonly />
										</div>
									</div>
							    </div>
						    </div>

					        <div class="col-md-3 col-sm-4">
					        	<div class="my-booking prize">
					        		<p>Total Amount Paid</p>
					        		<strong class="total-prize">Rs 3,500.00</strong>
					        		<span>Payment By Credit Card</span>
					        		<button type="submit" class="edit btn filter_btn">Edit</button>
					        		<button type="submit" class="cancle btn filter_btn">Cancel</button>
					        	</div>
					        </div>
					    </div>
					</div>
					<div class="fliter_box_inner mb-4">
					    <div class="row align-items-center">
					    	<div class="col-md-8 hotel-suites">
					    		<div class="row align-items-center">
							        <div class="col-md-5 mobile-space-none">
                                    <img src="{{asset('front/images/hotel-filter-pic.png')}}">
							        </div>
							        <div class="col-md-7 mobile-space-none">
							        	<div class="room-code">
							        		<span>#TYUYG21234</span>
							        	</div>
							        	<h5>Hotel S-Presso Inn Suites</h5>
							        	<div class="row align-items-center mb-3">
								        	<div class="reating mr-3 ml-3">
								        		<span></span>
								        		<span></span>
								        		<span></span>
								        		<span></span>
								        		<span></span>
								        	</div>
								        	<span class="reviews mr-2">600 Reviews</span>
								        	<span class="location"><i class="fa fa-map-marker" aria-hidden="true"></i>Jaipur</span>
								        </div>
								        <div class="popular">
								        	<i class="fa fa-map-marker" aria-hidden="true"></i>
								        	<span>Popular! Last Booked 1 Houe Ago</span>
								        </div>
								        <div class="heighly-review">
								        	<i class="fa fa-map-marker" aria-hidden="true"></i>
								        	<span>Heighly Reviewed By Couples</span>
								        </div>
								        <div class="near-by">
								        	<i class="fa fa-map-marker" aria-hidden="true"></i>
								        	<span>1.5kms from Jaipur City</span>
								        </div>
							        </div>
							    </div>
							    <div class="row explore-hotels booking-date">
							    	<div class="inout-control col-md-6 mobile-space-none">
								        <div id="datepicker" class="input-group date" data-date-format="mm-dd-yyyy">
								        	<span class="input-group-addon"><i class="fa fa-heart calendar" aria-hidden="true"></i></span>
										    <input class="form-control" type="text" placeholder="Check-In - 9 Dec 2022" readonly />
										</div>
									</div>
									<div class="inout-control col-md-6 mobile-space-none">
								        <div id="datepicker" class="input-group date" data-date-format="mm-dd-yyyy">
								        	<span class="input-group-addon"><i class="fa fa-heart calendar" aria-hidden="true"></i></span>
										    <input class="form-control" type="text" placeholder="Check-Out - 12 Dec 2022" readonly />
										</div>
									</div>
							    </div>
						    </div>

					        <div class="col-md-4">
					        	<div class="my-booking cancled prize">
					        		<button type="submit" class="cancle btn filter_btn mb-3">Cancelled</button>
					        		<p>Total Amount Paid</p>
					        		<strong class="total-prize">Rs 3,500.00</strong>
					        		<span>Payment By Credit Card</span>
					        	</div>
					        </div>
					    </div>
					</div>
					<div class="fliter_box_inner mb-4">
					    <div class="row align-items-center">
					    	<div class="col-md-8 hotel-suites">
					    		<div class="row align-items-center">
							        <div class="col-md-5 mobile-space-none">
                                    <img src="{{asset('front/images/hotel-filter-pic.png')}}">
							        </div>
							        <div class="col-md-7 mobile-space-none">
							        	<div class="room-code">
							        		<span>#TYUYG21234</span>
							        	</div>
							        	<h5>Hotel S-Presso Inn Suites</h5>
							        	<div class="row align-items-center mb-3">
								        	<div class="reating mr-3 ml-3">
								        		<span></span>
								        		<span></span>
								        		<span></span>
								        		<span></span>
								        		<span></span>
								        	</div>
								        	<span class="reviews mr-2">600 Reviews</span>
								        	<span class="location"><i class="fa fa-map-marker" aria-hidden="true"></i>Jaipur</span>
								        </div>
								        <div class="popular">
								        	<i class="fa fa-map-marker" aria-hidden="true"></i>
								        	<span>Popular! Last Booked 1 Houe Ago</span>
								        </div>
								        <div class="heighly-review">
								        	<i class="fa fa-map-marker" aria-hidden="true"></i>
								        	<span>Heighly Reviewed By Couples</span>
								        </div>
								        <div class="near-by">
								        	<i class="fa fa-map-marker" aria-hidden="true"></i>
								        	<span>1.5kms from Jaipur City</span>
								        </div>
							        </div>
							    </div>
							    <div class="row explore-hotels booking-date">
							    	<div class="inout-control col-md-6 mobile-space-none">
								        <div id="datepicker" class="input-group date" data-date-format="mm-dd-yyyy">
								        	<span class="input-group-addon"><i class="fa fa-heart calendar" aria-hidden="true"></i></span>
										    <input class="form-control" type="text" placeholder="Check-In - 9 Dec 2022" readonly />
										</div>
									</div>
									<div class="inout-control col-md-6 mobile-space-none">
								        <div id="datepicker" class="input-group date" data-date-format="mm-dd-yyyy">
								        	<span class="input-group-addon"><i class="fa fa-heart calendar" aria-hidden="true"></i></span>
										    <input class="form-control" type="text" placeholder="Check-Out - 12 Dec 2022" readonly />
										</div>
									</div>
							    </div>
						    </div>

					        <div class="col-md-4">
					        	<div class="my-booking success prize">
					        		<button type="submit" class="mb-3 edit btn filter_btn">Confirmed</button>
					        		<p>Total Amount Paid</p>
					        		<strong class="total-prize">Rs 3,500.00</strong>
					        		<span>Payment By Credit Card</span>
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