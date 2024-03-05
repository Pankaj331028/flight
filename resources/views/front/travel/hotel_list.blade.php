@if($data['status']==200)
	@foreach($data['data'] as $hotel)
		<div class="fliter_box_inner mb-4">
		    <div class="row align-items-center">
		        <div class="col-md-3">
		        	<img src="{{$hotel->image}}">
		        </div>
		        <div class="col-md-5 hotel-suites">
		        	<h5>{{$hotel->name}} {{$hotel->type}}</h5>
		        	<div class="row align-items-center mb-3">
			        	<div class="reating mr-3 ml-3">
			        		<span class="@if($hotel->rating>=1) close @else open @endif"></span>
			        		<span class="@if($hotel->rating>=2) close @else open @endif"></span>
			        		<span class="@if($hotel->rating>=3) close @else open @endif"></span>
			        		<span class="@if($hotel->rating>=4) close @else open @endif"></span>
			        		<span class="@if($hotel->rating==5) close @else open @endif"></span>
			        	</div>
			        	<span class="reviews mr-2">{{$hotel->rating_count}} Reviews</span>
			        	<span class="location"><i class="fa fa-map-marker" aria-hidden="true"></i>{{$hotel->city}}</span>
			        </div>
			        <div class="popular">
			        	<i class="fa fa-map-marker" aria-hidden="true"></i>
			        	<span>Popular! Last Booked {{intval($hotel->last_booked)}} Hour Ago</span>
			        </div>
			        <div class="heighly-review">
			        	<i class="fa fa-map-marker" aria-hidden="true"></i>
			        	<span>Highly Reviewed By Couples</span>
			        </div>
			        <div class="near-by">
			        	<i class="fa fa-map-marker" aria-hidden="true"></i>
			        	<span>{{$hotel->distance}} kms from {{$hotel->city}} City</span>
			        </div>
		        </div>
		        <div class="col-md-4">
		        	<div class="prize">
		        		<h2>{{$data['currency']}} {{number_format($hotel->price)}}</h2>
		        		<p>+ {{Config::get('constants.tax')}}% taxes & fees Per Night</p>
		        		<span>No Cost EMI</span>
		        		<strong class="total-prize">{{$data['currency']}} {{number_format($hotel->total)}}</strong>
		        		<a onclick="$('.fliter_box_inner').css('background','#fff');$(this).closest('.fliter_box_inner').css('background','#f0f0f0'); return confirm('Are you sure you would like to book this hotel?');" href="{{route('view-hotel',['id'=>$hotel->id])}}?{{explode('?',Request::getRequestUri())[1]}}" class="btn filter_btn">Continue</a>
		        	</div>
		        </div>
		    </div>
		</div>
	@endforeach
@else
	<h4 class="error text-danger">{{$data['message']}}</h4>
@endif