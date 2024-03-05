@if(count($confirmed) > 0)
	@foreach($confirmed as $book)
								<div class="fliter_box_inner mb-4">
								    <div class="row align-items-center">
								    	<div class="col-md-8 hotel-suites">
								    		<div class="row align-items-center">
										        <div class="col-md-5 mobile-space-none">
										        	<img src="{{$book->hotel->image}}">
										        </div>
										        <div class="col-md-7 mobile-space-none">
										        	<a href="{{route('view-booking',['no'=>$book->booking_code])}}" target="_blank">
											        	<div class="room-code">
											        		<span>#{{$book->booking_code}}</span>
											        	</div>
											        	<h5>{{$book->hotel->name}} {{$book->hotel->type}}</h5>
											        </a>
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
										        </div>
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
								        	<div class="my-booking success prize">
								        		<button type="submit" class="mb-3 edit btn filter_btn">Confirmed</button>
								        		<p>Total Amount Paid</p>
								        		<strong class="total-prize">{{$currency}} {{number_format($book->price_with_tax)}}</strong>
								        		<span>Payment By {{$book->payment_method == 'upi' ? 'UPI' : Config::get('constants.PAYMENT_METHOD.' . $book->payment_type)}}</span>
								        	</div>
								        </div>
								    </div>
								</div>
								@endforeach
@else
	<h4 class="error text-danger">No Confirmed Bookings</h4>
@endif