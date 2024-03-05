@extends('front.travel.layouts.master')
@section('template_title','Hotel Booking')
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
		.filter_btn {
		    margin-left: inherit !important;
		}
	</style>
	<div id="page-wrapper">
		<section class="wrapper1 render-QG quickGrab bgf7 py-3 py-md-5">
			<div class="container">
		        <div class="row explore-hotels">
		        	<div class="col-md-12">
		        		<div class="fliter_box_inner">
				            <h5 class="font35 font-weight-bold text-white mb-4">Explore Hotels</h5>
				            <form id="searchform" action="{{route('search-hotel')}}" class="form-valide" method="get">
				            	<div class="form-in">
					            	<div class="row px-3">
						            	<div class="inout-control col-md-4">
						            		<div class="input-group form-group mb-2">
						            			<span class="input-group-addon"><i class="fa fa-map-marker mr-3 location" aria-hidden="true"></i></span>
								            	<select name="state" id="state" class="form-control selectpicker">
											        <option value="">Select State *</option>
											        @foreach($states as $state)
											        <option value="{{$state}}">{{$state}}</option>
											        @endforeach
											    </select>
											</div>
										</div>
										<div class="inout-control col-md-4">
											<div class="input-group form-group mb-2">
												<span class="input-group-addon"><i class="fa fa-map-marker mr-3 location" aria-hidden="true"></i></span>
								            	<select name="city" id="city" class="form-control selectpicker select2">
											        <option value="">Select City *</option>
											    </select>
											</div>
										</div>
										<div class="inout-control col-md-4 fHotelBooking">
											<div class="input-group form-group room-height mb-2">
												<span class="input-group-addon"><i class="fa fa-bed bed" aria-hidden="true"></i></span>
								            	<select name="room_type[]" id="room_type" class="form-control selectpicker select2" multiple="multiple" placeholder="Select Room Type" data-placeholder="Select Room Type">
											        <option value="" disabled>Room Type</option>
											        <option value="Standard">Standard</option>
											        <option value="Deluxe">Deluxe</option>
											        <option value="Suite">Suite</option>
											        <option value="Luxury">Luxury</option>
											        <option value="Studio">Studio</option>
											    </select>
											</div>
										</div>
									</div>

									<div class="row px-3">
						            	<div class="inout-control col-md-3">
									        <div class="input-group form-group mb-2 date" data-date-format="mm-dd-yyyy">
									        	<span class="input-group-addon"><i class="fa fa-heart calendar" aria-hidden="true"></i></span>
											    <input class="form-control from" type="text" name="check_in" placeholder="Check-In --/--/-- *" readonly />
											</div>
										</div>
										<div class="inout-control col-md-3">
							            	<div class="input-group form-group mb-2 date" data-date-format="mm-dd-yyyy">
									        	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar calendar"></i></span>
											    <input class="form-control to" type="text" name="check_out" placeholder="Check-Out --/--/-- *" readonly />
											</div>
										</div>
										<div class="inout-control col-md-2">
											<div class="input-group form-group mb-2">
												<span class="input-group-addon"><i class="fa fa-bed gate" aria-hidden="true"></i></span>
								            	<select name="no_rooms" id="no_rooms" class="form-control selectpicker">
											        <option value="">No. Of Room *</option>
											        <option value="1">{{Config::get('constants.COUNTING.1')}}</option>
											        <option value="2">{{Config::get('constants.COUNTING.2')}}</option>
											        <option value="3">{{Config::get('constants.COUNTING.3')}}</option>
											        <option value="4">{{Config::get('constants.COUNTING.4')}}</option>
											        <option value="5">{{Config::get('constants.COUNTING.5')}}</option>
											    </select>
											</div>
										</div>
										<div class="inout-control col-md-2">
											<div class="input-group form-group mb-2">
												<span class="input-group-addon"><i class="fa fa-bed profile" aria-hidden="true"></i></span>
								            	<select name="no_adults" id="no_adults" class="form-control selectpicker">
											        <option value="">No. Of Adults *</option>
											        <option value="1">{{Config::get('constants.COUNTING.1')}}</option>
											        <option value="2">{{Config::get('constants.COUNTING.2')}}</option>
											        <option value="3">{{Config::get('constants.COUNTING.3')}}</option>
											        <option value="4">{{Config::get('constants.COUNTING.4')}}</option>
											        <option value="5">{{Config::get('constants.COUNTING.5')}}</option>
											    </select>
							            	</div>
										</div>
										<div class="inout-control col-md-2">
											<div class="input-group form-group mb-2">
												<span class="input-group-addon"><i class="fa fa-bed children" aria-hidden="true"></i></span>
								            	<input name="no_child" id="no_child" class="form-control" placeholder="No. Of Children"  type="number" min="0" />
								            </div>
										</div>
									</div>
								</div>
								<button type="reset" id="resetbtn" class="btn d-none btn-secondary ml-3">Reset</button>
								<iframe frameborder="0" class="iframe" id="hotelsearch_iframe" src="{{route('hotelsearch-iframe')}}" allowTransparency="true"></iframe>
						    </form>
		        		</div>
		        	</div>
		        </div>
			</div>
		</section>
	</div>

    <script src="{{URL::asset('/js/jquery.validate.min.js')}}"></script>
    <script src="{{URL::asset('/js/jquery.validate-init.js')}}"></script>

    <script type="text/javascript">
    	// setTimeout(function(){
    	document.getElementById("hotelsearch_iframe").onload = function(){

	    	var MyIFrame = document.getElementById("hotelsearch_iframe");
			var MyIFrameDoc = (MyIFrame.contentWindow || MyIFrame.contentDocument);
			if (MyIFrameDoc.document) MyIFrameDoc = MyIFrameDoc.document;

			$(MyIFrameDoc.getElementById("searchBtn")).click(function(){
				$('#searchform').submit();
			});

			$(MyIFrameDoc.getElementById("resetBtn")).click(function(){
				$('#resetbtn').click();
				$("#room_type").val([]);
				$("#city").val('');
				$("#room_type").trigger("change");
				$("#city").trigger("change");
			});

		};
    	// },5000);
    </script>
@endsection