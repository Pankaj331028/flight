@extends('front.flight.layouts.master')

@section('template_title','Booking Details bookingcode')
@section('content')
<div id="page-wrapper">
	<section class="wrapper2 render-QG quickGrab bgf7 py-5">
		<div class="container mobile-space-none">
			<div class="row listingProdGrid hotel-filter-details">
				<!-- <div class="col-lg-3 mb-4">
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
				</div> -->
				<div class="col-lg-12 mb-4 near-by-hotels">
					<!-- <h4>My Bookings (3)</h4> -->
					<div class="fliter_box_inner mb-4">
						<div class="row align-items-center">
							<div class="col-md-12 col-sm-8 hotel-suites border-right-0">
								<div class="row align-items-center">
									<div class="col-md-12 mobile-space-none flightBooking">
										<div class="room-code font16">
											
										<span class="text-secondary font14 font700">
											@switch(count($flightDetails))
												@case(1)
													One-Way
													@break

												@case(2)
													Round Trip
													@break

												@default
													Multi Trip
											@endswitch
										</span><br>
											<span>{{$bookings['bookingNumber']}}</span>
										</div>
										<section class="pb-4">
											@foreach($flightDetails as $flightData)
											
											<div class="row mx-auto position-relative border-bottom pb-3">
												<div class="py-2 float-start w-50">
													<span class="text-secondary">From</span><br>
													<label class="font18 mb-0 font700">{{$flightNameArray[$flightData['fromlocations']]}}</label><br>
													<span class="text-secondary">{{ \Carbon\Carbon::createFromFormat('H:i', $flightData['depatureTimes'])->format('h:i A') }} - {{ \Carbon\Carbon::parse($flightData['deptDates'])->format('D, d M') }}</span>
												</div>
												<div class="swap position-absolute" style="display: none;">
													<img src="images/swap.png" alt="Swap"class="shadow">
												</div>
												<div class="py-2 float-start w-50">
													<span class="text-secondary">To</span><br>
													<label class="font18 mb-0 font700">{{$flightNameArray[$flightData['tolocations']]}}</label><br>
													<span class="text-secondary">{{ \Carbon\Carbon::createFromFormat('H:i', $flightData['arrivalTimes'])->format('h:i A') }} - {{ \Carbon\Carbon::parse($flightData['deptDates'])->format('D, d M') }}</span>
												</div>
												<ul>
													<li class="font16 text-secondary" style="color: #4e4e4e;">
														<i class="fa-solid fa-users"></i> &nbsp;
														{{$bookings['noOfAirlineAdults']}} adult 
													</li>
													<!-- <li class="font16 text-secondary" style="color: #4e4e4e;">
														<i class="fa-solid fa-couch"></i> &nbsp;
														Premium
													</li> -->
												</ul>
											</div>
											<div class="row mx-auto position-relative pb-3">
												
											</div>
											@endforeach
										</section>

										<div id="accordion" class="passengerCollaps">
											@foreach($passengerDetails as $key => $passengerData)
											
											<div class="card border-0">
												<div class="card-header border-bottom bg-transparent p-0" id="heading_{{$key}}">
													<div class="row">
														<div class="col-3">
															<label class="my-2 font500 font16">{{$passengerData['salutation'].' '.$passengerData['firstName']}}</label>
														</div>
														<div class="col">
															<label class="my-2 font500 font16">{{$passengerData['mobileNo']}}</label>
														</div>
														<div class="col">
															<label class="my-2 font500 font16">{{$passengerData['emailId']}}</label>
														</div>
														<div class="col pt-1" style="padding-top: 5px !important;">
															<button type="button" class="btn filter_btn viewMore" data-toggle="collapse" data-target="#collapse_{{$key}}" aria-expanded="true" aria-controls="collapse_{{$key}}">
																View Detail
															</button>
															<!-- <button class="btn btn-link p-0 mb-2 text-dark w-100 mt-1" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
																&#8685;
															</button> -->
														</div>
													</div>
												</div>

												<div id="collapse_{{$key}}" class="collapse @if ($loop->first) show @endif" aria-labelledby="heading_{{$key}}" data-parent="#accordion">
													<div class="card-body p-0 pt-3">
														<div class="row">
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Gender</label><br>
																<label class="text-secondary font16">{{$passengerData['gender']}}</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Date of Birth</label><br>
																<label class="text-secondary font16">{{ \Carbon\Carbon::parse($passengerData['dob'])->format('D, d M') }}</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Passport</label><br>
																<label class="text-secondary font16">{{$passengerData['passport']}}</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Visa</label><br>
																<label class="text-secondary font16">{{$passengerData['visaNo']}}</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Valid Date</label><br>
																<label class="text-secondary font16">{{ \Carbon\Carbon::parse($passengerData['validDate'])->format('D, d M') }}</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Occupation</label><br>
																<label class="text-secondary font16">{{$passengerData['occupation']}}</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Graduation</label><br>
																<label class="text-secondary font16">{{$passengerData['graduation']}}</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Passes Out</label><br>
																<label class="text-secondary font16">{{$passengerData['passedOut']}}</label>
															</div>
															<div class="col-lg-6 mb-3">
																<label class="mb-1 font14 font500">Address</label><br>
																<label class="text-secondary font16">
																{{$passengerData['address']}}
																</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Country</label><br>
																<label class="text-secondary font16">{{$passengerData['country']}}</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">State</label><br>
																<label class="text-secondary font16">{{$passengerData['state']}}</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">City</label><br>
																<label class="text-secondary font16">{{$passengerData['city']}}</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">PIN</label><br>
																<label class="text-secondary font16">{{$passengerData['pinNo']}}</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Preffered Class</label><br>
																<label class="text-secondary font16">{{$passengerData['prefferedClass'] ?? '-'}}</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Special Class</label><br>
																<label class="text-secondary font16">{{$passengerData['specialClass'] ?? '-'}}</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Membership Airline</label><br>
																<label class="text-secondary font16">{{$passengerData['memberShipAirline']}}</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Membership ID</label><br>
																<label class="text-secondary font16">{{$passengerData['memberShipId']}}</label>
															</div>
														</div>
													</div>
												</div>
											</div>
											<!-- <div class="card border-0">
												<div class="card-header border-bottom bg-transparent p-0" id="headingTwo">
													<div class="row text-left">
														<div class="col-3">
															<label class="my-2 font500 font16">Verve Online Marketing</label>
														</div>
														<div class="col">
															<label class="my-2 font500 font16">9638527410</label>
														</div>
														<div class="col">
															<label class="my-2 font500 font16">verveonlinemarketing@gmail.com</label>
														</div>
														<div class="col pt-1" style="padding-top: 5px !important;">
															<button type="button" class="btn filter_btn viewMore" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
																View Detail
															</button>
														</div>
													</div>
												</div>
												<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
													<div class="card-body">
														<div class="row">
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Gender</label><br>
																<label class="text-secondary font16">Male</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Date of Birth</label><br>
																<label class="text-secondary font16">01 January 1999</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Passport</label><br>
																<label class="text-secondary font16">ICAO9303</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Visa</label><br>
																<label class="text-secondary font16">9876543210</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Valid Date</label><br>
																<label class="text-secondary font16">10 January 2021</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Occupation</label><br>
																<label class="text-secondary font16">10 January 2025</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Graduation</label><br>
																<label class="text-secondary font16">Business</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Passes Out</label><br>
																<label class="text-secondary font16">BCA</label>
															</div>
															<div class="col-lg-6 mb-3">
																<label class="mb-1 font14 font500">Address</label><br>
																<label class="text-secondary font16">
																	5D, near Punjab Kesari, opposite Regional Transport Office, Jhalana Institutional Area, Jhalana Doongri
																</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Country</label><br>
																<label class="text-secondary font16">India</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">State</label><br>
																<label class="text-secondary font16">Rajasthan</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">City</label><br>
																<label class="text-secondary font16">Jaipur</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">PIN</label><br>
																<label class="text-secondary font16">302001</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Preffered Class</label><br>
																<label class="text-secondary font16">Economy</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Special Class</label><br>
																<label class="text-secondary font16">Student Fare</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Membership Airline</label><br>
																<label class="text-secondary font16">India</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Membership ID</label><br>
																<label class="text-secondary font16">789456123</label>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="card border-0">
												<div class="card-header border-0 bg-transparent p-0" id="headingTwo">
													<div class="row text-left">
														<div class="col-3">
															<label class="my-2 font500 font16">Verve Online Marketing</label>
														</div>
														<div class="col">
															<label class="my-2 font500 font16">9638527410</label>
														</div>
														<div class="col">
															<label class="my-2 font500 font16">verveonlinemarketing@gmail.com</label>
														</div>
														<div class="col pt-1" style="padding-top: 5px !important;">
															<button type="button" class="btn filter_btn viewMore" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
																View Detail
															</button>
														</div>
													</div>
												</div>
												<div id="collapseThree" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
													<div class="card-body">
														<div class="row">
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Gender</label><br>
																<label class="text-secondary font16">Male</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Date of Birth</label><br>
																<label class="text-secondary font16">01 January 1999</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Passport</label><br>
																<label class="text-secondary font16">ICAO9303</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Visa</label><br>
																<label class="text-secondary font16">9876543210</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Valid Date</label><br>
																<label class="text-secondary font16">10 January 2021</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Occupation</label><br>
																<label class="text-secondary font16">10 January 2025</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Graduation</label><br>
																<label class="text-secondary font16">Business</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Passes Out</label><br>
																<label class="text-secondary font16">BCA</label>
															</div>
															<div class="col-lg-6 mb-3">
																<label class="mb-1 font14 font500">Address</label><br>
																<label class="text-secondary font16">
																	5D, near Punjab Kesari, opposite Regional Transport Office, Jhalana Institutional Area, Jhalana Doongri
																</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Country</label><br>
																<label class="text-secondary font16">India</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">State</label><br>
																<label class="text-secondary font16">Rajasthan</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">City</label><br>
																<label class="text-secondary font16">Jaipur</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">PIN</label><br>
																<label class="text-secondary font16">302001</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Preffered Class</label><br>
																<label class="text-secondary font16">Economy</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Special Class</label><br>
																<label class="text-secondary font16">Student Fare</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Membership Airline</label><br>
																<label class="text-secondary font16">India</label>
															</div>
															<div class="col-lg-3 mb-3">
																<label class="mb-1 font14 font500">Membership ID</label><br>
																<label class="text-secondary font16">789456123</label>
															</div>
														</div>
													</div>
												</div>
											</div> -->
											@endforeach
										</div>
										<hr>


										<!-- GST Information -->
										<div class="card-body p-0 pt-3">
											<div class="card-header py-1 my-2" style="border-top: none !important;">
												<b class="font14 mb-0">GST Information</b>
											</div>
											<div class="row">
												<div class="col-lg-3 mb-3">
													<strong>GST Name</strong><br>
													<label class="text-secondary">{{$bookings['gstName'] ??'-'}}</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>GST Number</strong><br>
													<label class="text-secondary">{{$bookings['gstNumber'] ??'-'}}</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>GST Address</strong><br>
													<label class="text-secondary">
													{{$bookings['gstAddress'] ??'-'}}
													</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>Insure Trip</strong><br>
													<label class="text-secondary">{{$bookings['insuranceName'] ??'-'}}</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>Coupon Code</strong><br>
													<label class="text-secondary">{{$bookings['promoCode'] ??'-'}}</label>
												</div>
											</div>
										</div>

										<!-- Baggage Details -->
										<div class="card-body p-0 pt-3">
											<div class="card-header py-1 my-2" style="border-top: none !important;">
												<b class="font14 mb-0">Baggage Details</b>
											</div>
											<div class="row">
												<div class="col-lg-3 mb-3">
													<strong>No. of Bags</strong><br>
													<label class="text-secondary">{{$bookings['totalBags'].'Bags' ??'-'}}</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>Total Weight</strong><br>
													<label class="text-secondary">{{$bookings['totalWeight'].'KG' ??'-'}}</label>
												</div>
											</div>
											<div class="py-2">
												<b class="font14">Hotel Booking</b>
											</div>
											<hr class="mt-0 mb-2">
											<div class="row">
												<!-- <div class="col-lg-3 mb-3">
													<strong>Country</strong><br>
													<label class="text-secondary">India</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>State</strong><br>
													<label class="text-secondary">Rajasthan</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>City</strong><br>
													<label class="text-secondary">Jaipur</label>
												</div> -->
												<div class="col-lg-3 mb-3">
													<strong>Hotel Name</strong><br>
													<label class="text-secondary">{{$bookings['hotelName'] ??'-'}}</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>No. of Rooms</strong><br>
													<label class="text-secondary">{{$bookings['noOfRooms'] ??'-'}}</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>Guest</strong><br>
													<label class="text-secondary">
													{{$bookings['noOfAdult'] ??'-'}} Adults & {{$bookings['noOfChild'] ??'0'}} Child
													</label>
												</div>
												<!-- <div class="col-lg-3 mb-3">
													<strong>Room Type</strong><br>
													<label class="text-secondary">Delux</label>
												</div> -->
												<div class="col-lg-3 mb-3">
													<strong>Check in</strong><br>
													<label class="text-secondary">{{$bookings['checkInDate'] ??'-'}}</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>Check out</strong><br>
													<label class="text-secondary">{{$bookings['checkOutDate'] ??'-'}}</label>
												</div>
											</div>
										</div>

										<!-- Itinerary Details -->
										<div class="card-body p-0 pt-3">
											<div class="card-header py-1 my-2" style="border-top: none !important;">
												<b class="font14 mb-0">Itinerary Details</b>
											</div>
											<div class="row">
												<!-- <div class="col-lg-3 mb-3">
													<strong>Itinerary Details</strong><br>
													<label class="text-secondary">Itinerary</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>Price</strong><br>
													<label class="text-secondary">12,000</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>No. of Tickets</strong><br>
													<label class="text-secondary">2</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>Total Price</strong><br>
													<label class="text-secondary">24,000</label>
												</div> -->
												<table class="table">
													<thead>
														<tr>
															<th>Itinerary Name</th>
															<th>No. of Tickets</th>
														</tr>
													</thead>
													<tbody>
													@if($itineraryArray)
													@foreach($itineraryArray as $itineraryData)
													<tr class="col-lg-3 mb-3">
														<td>{{$itineraryData['itineraryName']}}</td>
														<td>{{$itineraryData['itineraryCount']}}</td>
													</tr>
													@endforeach
													@else 
													<tr class="col-lg-3 mb-3">
														<td>-</td>
														<td>-</td>
													</tr>
													@endif
													</tbody>
												</table>
											</div>
										</div>

										<!-- Meals Details -->
										<div class="card-body p-0 pt-3">
											<div class="card-header py-1 my-2" style="border-top: none !important;">
												<b class="font14 mb-0">Meal Details</b>
											</div>
											<div class="row">
												<!-- <div class="col-lg-3 mb-3">
													<strong>Itinerary Details</strong><br>
													<label class="text-secondary">Itinerary</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>Price</strong><br>
													<label class="text-secondary">12,000</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>No. of Tickets</strong><br>
													<label class="text-secondary">2</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>Total Price</strong><br>
													<label class="text-secondary">24,000</label>
												</div> -->
												<table class="table">
													<thead>
														<tr>
															<th>Meal Name</th>
															<th>No. of Meal</th>
														</tr>
													</thead>
													<tbody>
													@if($mealArray)
													@foreach($mealArray as $mealData)
													<tr class="col-lg-3 mb-3">
														<td>{{$mealData['mealName']}}</td>
														<td>{{$mealData['mealCount']}}</td>
													</tr>
													@endforeach
													@else 
													<tr class="col-lg-3 mb-3">
														<td>-</td>
														<td>-</td>
													</tr>
													@endif
													</tbody>
												</table>
											</div>
										</div>

										<!-- Car Details -->
										<div class="card-body p-0 pt-3">
											<div class="card-header py-1 my-2" style="border-top: none !important;">
												<b class="font14 mb-0">Car Details</b>
											</div>
											<div class="row">
												<div class="col-lg-3 mb-3">
													<strong>Car Name</strong><br>
													@if (isset($bookings['noOfCars']) && is_array($bookings['noOfCars']) && isset($bookings['noOfCars'][0]))
														<label class="text-secondary">{{$bookings['noOfCars'][0]}}</label>
													@else
														<label class="text-secondary">N/A</label>
													@endif
												</div>
												<!-- <div class="col-lg-3 mb-3">
													<strong>Price</strong><br>
													<label class="text-secondary">12,000</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>No. of Tickets</strong><br>
													<label class="text-secondary">2</label>
												</div>
												<div class="col-lg-3 mb-3">
													<strong>Total Price</strong><br>
													<label class="text-secondary">24,000</label>
												</div> -->
												<!-- <table class="table">
													<thead>
														<tr>
															<th>Meal Name</th>
															<th>No. of Meal</th>
														</tr>
													</thead>
													<tbody>
													@if($mealArray)
													@foreach($mealArray as $mealData)
													<tr class="col-lg-3 mb-3">
														<td>{{$mealData['mealName']}}</td>
														<td>{{$mealData['mealCount']}}</td>
													</tr>
													@endforeach
													@else 
													<tr class="col-lg-3 mb-3">
														<td>-</td>
														<td>-</td>
													</tr>
													@endif
													</tbody>
												</table> -->
											</div>
										</div>

										<!-- <section class="row pb-3">
											<div class="col-lg-3 mb-3">
												<strong>Name</strong><br>
												<label class="text-secondary">Mr. Pankaj Sharma</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>Gender</strong><br>
												<label class="text-secondary">Male</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>Date of Birth</strong><br>
												<label class="text-secondary">01 January 1999</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>Mobile No.</strong><br>
												<label class="text-secondary">+91 9876543210</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>Mail ID</strong><br>
												<label class="text-secondary">pankaj@gmail.com</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>Passport</strong><br>
												<label class="text-secondary">ICAO9303</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>Visa</strong><br>
												<label class="text-secondary">9876543210</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>Valid Date</strong><br>
												<label class="text-secondary">10 January 2021</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>Occupation</strong><br>
												<label class="text-secondary">10 January 2025</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>Graduation</strong><br>
												<label class="text-secondary">Business</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>Passes Out</strong><br>
												<label class="text-secondary">BCA</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>Address</strong><br>
												<label class="text-secondary">100%</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>Country</strong><br>
												<label class="text-secondary">
													5D, near Punjab Kesari, opposite Regional Transport Office, Jhalana Institutional Area, Jhalana Doongri
												</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>State</strong><br>
												<label class="text-secondary">India</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>City</strong><br>
												<label class="text-secondary">Rajasthan</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>PIN</strong><br>
												<label class="text-secondary">Jaipur</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>Preffered Class</strong><br>
												<label class="text-secondary">302004</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>Special Class</strong><br>
												<label class="text-secondary">Business</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>Membership Airline</strong><br>
												<label class="text-secondary">Business</label>
											</div>
											<div class="col-lg-3 mb-3">
												<strong>Membership ID</strong><br>
												<label class="text-secondary">Business</label>
											</div>
										</section> -->
									</div>
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