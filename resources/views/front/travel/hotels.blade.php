@extends('front.travel.layouts.master')
@section('template_title','Hotel Booking')
@section('content')
<style type="text/css">
.invalid-feedback {
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
    background:#fff;
}
</style>
<div id="page-wrapper">
    <section class="wrapper1 render-QG quickGrab bgf7 py-3 py-md-5">
        <div class="container">
            <div class="row explore-hotels">
                <div class="col-md-12">
                    <div class="">
                        <h5 class="font35 font-weight-bold mb-4">Select Hotel</h5>
                        <form id="searchform" action="{{route('search-hotel')}}" class="form-valide" method="get">
                            <div class="form-in filters-results">
                                <div class="row px-3">
                                    <div class="inout-control col-md-3">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon"><i class="fa fa-map-marker mr-3 location" aria-hidden="true"></i></span>
                                            <select name="state" id="state" class="form-control selectpicker">
                                                <option value="">Select State *</option>
                                                @foreach($states as $state)
                                                <option value="{{$state}}" @if($state==Request::get('state')) selected @endif>{{$state}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="inout-control col-md-3">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon" style="width:auto;"><i class="fa fa-map-marker mr-3 location" aria-hidden="true"></i></span>
                                            <select name="city" id="city" class="form-control selectpicker select2">
                                                <option value="">Select City *</option>
                                                @foreach($cities as $city)
                                                <option value="{{$city}}" @if($city==Request::get('city')) selected @endif>{{$city}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    {{-- <div class="inout-control col-md-4">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon"><i class="fa fa-bed bed" aria-hidden="true"></i></span>
                                            <select name="room_type" id="room_type" class="form-control selectpiker select2" multiple>
                                                <option value="" disabled>Room Type</option>
                                                <option value="Standard" @if("Standard"==Request::get('room_type')) selected @endif>Standard</option>
                                                <option value="Deluxe" @if("Deluxe"==Request::get('room_type')) selected @endif>Deluxe</option>
                                                <option value="Suite" @if("Suite"==Request::get('room_type')) selected @endif>Suite</option>
                                                <option value="Luxury" @if("Luxury"==Request::get('room_type')) selected @endif>Luxury</option>
                                                <option value="Studio" @if("Studio"==Request::get('room_type')) selected @endif>Studio</option>
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class="inout-control col-md-2">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon"><i class="fa fa-bed gate" aria-hidden="true"></i></span>
                                            <select name="no_rooms" id="no_rooms" class="form-control selectpiker">
                                                <option value="" disabled>No. Of Room *</option>
                                                <option value="1" @if("1"==Request::get('no_rooms')) selected @endif>{{Config::get('constants.COUNTING.1')}}</option>
                                                <option value="2" @if("2"==Request::get('no_rooms')) selected @endif>{{Config::get('constants.COUNTING.2')}}</option>
                                                <option value="3" @if("3"==Request::get('no_rooms')) selected @endif>{{Config::get('constants.COUNTING.3')}}</option>
                                                <option value="4" @if("4"==Request::get('no_rooms')) selected @endif>{{Config::get('constants.COUNTING.4')}}</option>
                                                <option value="5" @if("5"==Request::get('no_rooms')) selected @endif>{{Config::get('constants.COUNTING.5')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="inout-control col-md-2">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon"><i class="fa fa-bed profile" aria-hidden="true"></i></span>
                                            <select name="no_adults" id="no_adults" class="form-control selectpicker">
                                                <option value="">No. Of Adults *</option>
                                                <option value="1" @if("1"==Request::get('no_adults')) selected @endif>{{Config::get('constants.COUNTING.1')}}</option>
                                                <option value="2" @if("2"==Request::get('no_adults')) selected @endif>{{Config::get('constants.COUNTING.2')}}</option>
                                                <option value="3" @if("3"==Request::get('no_adults')) selected @endif>{{Config::get('constants.COUNTING.3')}}</option>
                                                <option value="4" @if("4"==Request::get('no_adults')) selected @endif>{{Config::get('constants.COUNTING.4')}}</option>
                                                <option value="5" @if("5"==Request::get('no_adults')) selected @endif>{{Config::get('constants.COUNTING.5')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="inout-control col-md-2">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon"><i class="fa fa-bed children" aria-hidden="true"></i></span>
                                            <input name="no_child" id="no_child" class="form-control" placeholder="No. Of Children" type="number" min="0" value="{{Request::get('no_child')}}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-3">
                                    <div class="inout-control col-md-3">
                                        <div class="input-group form-group mb-2 date" data-date-format="mm-dd-yyyy">
                                            <span class="input-group-addon"><i class="fa fa-heart calendar" aria-hidden="true"></i></span>
                                            <input class="form-control from" type="text" name="check_in" placeholder="Check-In --/--/-- *" value="{{Request::get('check_in')}}" readonly />
                                        </div>
                                    </div>
                                    <div class="inout-control col-md-3">
                                        <div class="input-group form-group mb-2 date" data-date-format="mm-dd-yyyy">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar calendar"></i></span>
                                            <input class="form-control to" type="text" name="check_out" placeholder="Check-Out --/--/-- *" value="{{Request::get('check_out')}}" readonly />
                                        </div>
                                    </div>
                                    <div class="inout-control col-md-2">
                                        <button type="submit" class="btn filter_btn">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="wrapper2 render-QG quickGrab bgf7 pb-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="category-breadcrumb">
                        <a href="javascript:;" id="sortby" class="d-none">Price low to high</a>
                        <a href="javascript:;" id="room_type" class="d-none">Standard</a>
                        <a href="javascript:;" id="clearsort" class="d-none">Clear</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container mobile-space-none">
            <div class="row listingProdGrid hotel-filter-details mt-5">
                <div class="custom-sticky-out col-lg-3 mb-4">
                    <div class="custom-sticky filterDiv">
                        <div class="fliter_box_inner">
                            <div class="row">
                                <div class="col-12 py-3">
                                    <div class="filter_box box_css">
                                        <h2 class="mb-3 pb-3 border-bottom">Sort By</h2>
                                        <div class="size_checkbox">
                                            <div class="checkbox_comman">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group custom_radio mb-2">
                                                            <input type="radio" name="sortby" id="value_pltoh" data-value="3" value="pltoh" class="filter attribute" @if(Request::get('sortby')=='pltoh' ) checked @endif>
                                                            <label for="value_pltoh">Price low to high</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group custom_radio mb-2">
                                                            <input type="radio" name="sortby" id="value_phtol" data-value="3" value="phtol" class="filter attribute" @if(Request::get('sortby')=='phtol' ) checked @endif>
                                                            <label for="value_phtol">Price High to low</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group custom_radio mb-2">
                                                            <input type="radio" name="sortby" id="value_nasc" data-value="3" value="nasc" class="filter attribute" @if(Request::get('sortby')=='nasc' ) checked @endif>
                                                            <label for="value_nasc">Name Ascending</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group custom_radio mb-2">
                                                            <input type="radio" name="sortby" id="value_ndesc" data-value="3" value="ndesc" class="filter attribute" @if(Request::get('sortby')=='ndesc' ) checked @endif>
                                                            <label for="value_ndesc">Name Descending</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="fliter_box_inner mt-4">
                            <div class="row">
                                <div class="col-12 py-3">
                                    <div class="filter_box box_css">
                                        <h2 class="mb-3 pb-3 border-bottom">Room Type</h2>
                                        <div class="size_checkbox">
                                            <div class="checkbox_comman">
                                                <form action="">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group custom_checkbox mb-2">
                                                                <input type="checkbox" name="room_type[]" id="Standard" data-value="3" value="Standard" class="filter attribute" @if(!empty(Request::get('room_type')) && in_array('Standard', Request::get('room_type'))) checked @endif>
                                                                <label for="Standard">Standard</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group custom_checkbox mb-2">
                                                                <input type="checkbox" name="room_type[]" id="Deluxe" data-value="3" value="Deluxe" class="filter attribute" @if(!empty(Request::get('room_type')) && in_array('Deluxe', Request::get('room_type'))) checked @endif>
                                                                <label for="Deluxe">Deluxe</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group custom_checkbox mb-2">
                                                                <input type="checkbox" name="room_type[]" id="Suite" data-value="3" value="Suite" class="filter attribute" @if(!empty(Request::get('room_type')) && in_array('Suite', Request::get('room_type'))) checked @endif>
                                                                <label for="Suite">Suite</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group custom_checkbox mb-2">
                                                                <input type="checkbox" name="room_type[]" id="Luxury" data-value="3" value="Luxury" class="filter attribute" @if(!empty(Request::get('room_type')) && in_array('Luxury', Request::get('room_type'))) checked @endif>
                                                                <label for="Luxury">Luxury</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group custom_checkbox mb-2">
                                                                <input type="checkbox" name="room_type[]" id="Studio" data-value="3" value="Studio" class="filter attribute" @if(!empty(Request::get('room_type')) && in_array('Studio', Request::get('room_type'))) checked @endif>
                                                                <label for="Studio">Studio</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 mb-4 near-by-hotels" id="hotellist">
                    @include('front.travel.hotel_list',['data'=>$data])
                </div>
            </div>
        </div>
    </section>
</div>
<script src="{{URL::asset('/js/jquery.validate.min.js')}}"></script>
<script src="{{URL::asset('/js/jquery.validate-init.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {


    var rooms = '';
    var room_types = [];
    $('[name="room_type[]"]:checked').each(function(index, item) {
        rooms += '&room_type[]=' + $(item).val();
        room_types.push($(item).val());
    })

    if($('[name=sortby]:checked').val()!=undefined)
    {

    	$('#sortby').removeClass('d-none').html($('[name=sortby]:checked').next().text());
    	$('#clearsort').removeClass('d-none');
    }
    else{
    	$('#sortby').addClass('d-none');
    }

    if(room_types.length > 0)
    {
    	$('#room_type').removeClass('d-none').html(room_types.join('/'));
    	$('#clearsort').removeClass('d-none');
    }
    else{
    	$('#room_type').addClass('d-none');
    }
    if($('[name=sortby]:checked').val()==undefined && room_types.length <= 0){
    	$('#clearsort').addClass('d-none');
    }

    $('.filter').click(function() {

        var rooms = '';
        var room_types = [];
        $('[name="room_type[]"]:checked').each(function(index, item) {
            rooms += '&room_type[]=' + $(item).val();
            room_types.push($(item).val());
        })

        var refresh = window.location.protocol + "//" + window.location.host + window.location.pathname + '?state={{Request::get('state')}}&city={{Request::get('city')}}&no_rooms={{Request::get('no_rooms')}}&no_adults={{Request::get('no_adults')}}&no_child={{Request::get('no_child')}}&check_in={{Request::get('check_in')}}&check_out={{Request::get('check_out')}}&sortby=' + $('[name=sortby]:checked').val() + rooms;
        window.history.pushState({ path: refresh }, '', refresh);

        if($('[name=sortby]:checked').val()!=undefined)
        {
        	$('#sortby').removeClass('d-none').html($('[name=sortby]:checked').next().text());
        	$('#clearsort').removeClass('d-none');
        }
        else{
            $('#sortby').addClass('d-none');
        }

        if(room_types.length > 0)
        {
        	$('#room_type').removeClass('d-none').html(room_types.join('/'));
        	$('#clearsort').removeClass('d-none');
        }else{
            $('#room_type').addClass('d-none');
        }

        if($('[name=sortby]:checked').val()==undefined && room_types.length <= 0){
        	$('#clearsort').addClass('d-none');
        }

        $.ajax({
        	type:'get',
        	url:window.location.href,
        	success:function(data){
        		$('#hotellist').html(data);
        	}
        })
    })

    $('#clearsort').click(function(){

    	$('[name=sortby]').prop('checked',false);
    	$('[name="room_type[]"]').prop('checked',false);

    	var refresh = window.location.protocol + "//" + window.location.host + window.location.pathname + '?state={{Request::get('state')}}&city={{Request::get('city')}}&no_rooms={{Request::get('no_rooms')}}&no_adults={{Request::get('no_adults')}}&no_child={{Request::get('no_child')}}&check_in={{Request::get('check_in')}}&check_out={{Request::get('check_out')}}';
        window.history.pushState({ path: refresh }, '', refresh);


        var rooms = '';
        var room_types = [];
        $('[name="room_type[]"]:checked').each(function(index, item) {
            rooms += '&room_type[]=' + $(item).val();
            room_types.push($(item).val());
        })

        if($('[name=sortby]:checked').val()!=undefined)
        {
        	$('#sortby').removeClass('d-none').html($('[name=sortby]:checked').text());
        	$('#clearsort').removeClass('d-none');
        }
        else{
        	$('#sortby').addClass('d-none');
        }

        if(room_types.length > 0)
        {
        	$('#room_type').removeClass('d-none').html(room_types.join('/'));
        	$('#clearsort').removeClass('d-none');
        }
        else{
        	$('#room_type').addClass('d-none');
        }
        if($('[name=sortby]:checked').val()==undefined && room_types.length <= 0){
        	$('#clearsort').addClass('d-none');
        }

        $.ajax({
        	type:'get',
        	url:window.location.href,
        	success:function(data){
        		console.log(data);
        		$('#hotellist').html(data);
        	}
        })
    })
})
</script>
@endsection