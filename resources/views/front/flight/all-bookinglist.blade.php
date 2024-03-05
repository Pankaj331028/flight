@if(count($bookings) > 0)
	@foreach($bookings as $key => $book)
		<div class="fliter_box_inner mb-4">
			<div class="row align-items-center">
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
							<div class="col-md-5 mobile-space-none pt-3">
								<input class="form-control" type="text" placeholder="DeptDates - {{date('d M, Y',strtotime($book['deptDates'][0]))}}" readonly />
							</div>
							@endif
						</div>
						@if($key == 12)
							@php 
							
							@endphp
						@endif
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
						@elseif($book['bookingStatus'] =='Confirmed')
							<!-- <button type="submit" class="mb-3 edit btn filter_btn">Confirmed</button> -->
						@endif
						<p>Total Amount Paid</p>
						<strong class="total-prize">Rs {{number_format($book['totalPrice'])}}</strong>
						<span>Payment By {{$book['payment_method'] == 'upi' ? 'UPI' : Config::get('constants.PAYMENT_METHOD.' . $book['payment_type'])}}</span>
						@if ( $deptDateTimestamp > $currentTimestamp && isset($book['deptDates'][0]))
						@if($book['bookingStatus'] =='Confirmed')
						<button type="submit" class="edit btn filter_btn" onclick="window.location='{{route('flight-edit-booking',['id'=>$book['bookingNumber']])}}'">Edit</button>
						<a href="{{route('flight-cancel-booking',['id'=>$book['bookingNumber']])}}" onclick="return confirm('Are you sure you want to cancel this booking?')" class="cancle btn filter_btn">Cancel</a>
						@endif
						@endif
						<!-- @if($book['bookingStatus'] =='pending') -->
						<!-- <button type="submit" class="edit btn filter_btn" onclick="window.location='{{route('edit-booking',['id'=>$book['bookingNumber']])}}'">Edit</button> -->
						<!-- <a href="{{route('flight-cancel-booking',['id'=>$book['bookingNumber']])}}" onclick="return confirm('Are you sure you want to cancel this booking?')" class="cancle btn filter_btn">Cancel</a> -->
						<!-- @endif -->
					</div>
				</div>
			</div>
		</div>
	@endforeach
@else
	<h4 class="error text-danger">No Bookings</h4>
@endif