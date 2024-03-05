@extends('front.flight.layouts.master')
@section('template_title','Flight Booking')
@section('content')
<section class="flightBooking">
    <div id="page-wrapper">
        <!-- Flight Searching -->
        <section class="wrapper1 render-QG quickGrab bgf7 topBanner bgNone">
            <div class="black_overlay">
                <div class="container mb-5">
                    <div class="cPadding">
                        <?php $currentDate = date("d-m-Y");?>
                        <div class="flightOuterBox bg-light shadow p-5">
                            <form id="myform" action="{{route('search-flight')}}" class="flight-searching-form" method="get">
                            <div class="pb-4">
                                <button type="button" class="oneWay tripBtn  mr-3  me-1 @if(Request::get('trip_type') == 'one_way_trip')  active @endif" id="one-way-trip"> One-Way Trip</button>
                                <button type="button" class="roundTrip tripBtn  mr-3 @if(Request::get('trip_type') == 'roundTrip')  active @endif" id="removeReadonly">Round Trip</button>
                                <button type="button" class="multiTrip tripBtn @if(Request::get('trip_type') == 'multiTrip')  active @endif" id="multi-Trip">Multi Trip</button>
                                <input type="hidden" id="trip_type" name="trip_type" value="one_way_trip">
                            </div>
                            <section class="" id="oneRoundTrip">

                                    <div class="row">
                                        <div class="col-lg-3 pr-2 position-relative">
                                            <div class="form-group fromBox pl-4 py-2">
                                                <label class="text-secondary mb-1">
                                                    <i class="fa-solid fa-plane-departure" style="color: #6c757d;"></i> &nbsp;
                                                    Flying from
                                                </label>
                                                <input type="text" class="form-control font700 left" id="left" name="flyingfrom" value="{{Request::get('flyingfrom')}}">
                                            </div>
                                            <div class=" Flyingleft text-danger"></div>
                                            <button type="button" class="swapLocation position-absolute" id="swaploction" onClick="switchText()"></button>
                                        </div>

                                        <div class="form-group col-lg-3 px-2">
                                            <div class="toBox pl-4 py-2">
                                                <label class="text-secondary mb-1">
                                                    <i class="fa-solid fa-plane-arrival" style="color: #6c757d;"></i> &nbsp;
                                                    Flying to
                                                </label>
                                                <input type="text" class="form-control font700" id="right" name="flyingTo" value="{{Request::get('flyingTo')}}">
                                            </div>
                                            <div class="Flyingright text-danger"></div>
                                        </div>
                                        <div class="form-group col-lg px-2">
                                            <div class="px-3 py-2 depart">
                                                <label class="text-secondary mb-1">
                                                    <i class="fa-solid fa-calendar-days" style="color: #6c757d;"></i> &nbsp;
                                                    Depart
                                                </label>
                                                <input type="text" class="form-control font700" id="departureDate" name="departureDates"  value="{{Request::get('departureDates')}}">
                                            </div>
                                        </div>
                                        <div class="form-group col-lg px-2 dNone" id="returnId">
                                            <div class="px-3 py-2 depart blockReturn" id="return">
                                                <label class="text-secondary mb-1">
                                                    <i class="fa-solid fa-calendar-days" style="color: #6c757d;"></i> &nbsp;
                                                    Return
                                                </label>
                                                <input type="text" class="form-control font700" id="returnDate" name="returnDates" readonly value="{{Request::get('returnDates')}}">
                                            </div>
                                        </div>

                                        <div class="col-lg px-2 position-relative form-group addPassangerClass" id="">
                                            <div class="px-3 py-2 depart" onClick="showInfo('addPassanger')">
                                                <label class="text-secondary mb-1">
                                                    <i class="fa-solid fa-users" style="color: #6c757d;"></i> &nbsp;
                                                    Travelers
                                                </label>
                                                <input type="text" class="font700" readonly id="passenger" name="passenger" value="{{Request::get('passenger')}}">
                                            </div>

                                            <div class="position-absolute addPassanger bg-light shadow OneId" id="addPassanger">
                                            <!-- ... Your existing HTML ... -->

                                            <div class="p-3 border-bottom">
                                                <div class="row mx-auto mb-3">
                                                    <div class="float-start w-50 text-secondary font14 addPanel">ADULTS (12y +)</div>
                                                    <div class="float-end w-50 text-right">
                                                        <select name="adultsCount" class="w-25" id="adultsCount">
                                                            @for($i=1; $i<=10; $i++)
                                                            <option value="{{$i}}"  @if(Request::get('adultsCount') == $i)  selected @endif>{{$i}}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row mx-auto mb-3">
                                                    <div class="float-start w-50 text-secondary font14 addPanel">CHILDREN (2y - 12y )</div>
                                                    <div class="float-end w-50 text-right">
                                                        <select name="childrenCount" class="w-25" id="childrenCount">
                                                            @for($i=0; $i<=10; $i++)
                                                            <option value="{{$i}}" @if(Request::get('childrenCount') == $i)  selected @endif>{{$i}}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row mx-auto">
                                                    <div class="float-start w-50 text-secondary font14 addPanel">INFANTS (below 2y)</div>
                                                    <div class="float-end w-50 text-right">
                                                        <select name="infantsCount" class="w-25" id="infantsCount">
                                                            @for($i=0; $i<=10; $i++)
                                                            <option value="{{$i}}" @if(Request::get('infantsCount') == $i)  selected @endif>{{$i}}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- ... Your existing JavaScript ... -->

                                                <div class="p-3 border-bottom">
                                                <div class="row mx-auto">
                                                    <div class="float-start w-100 text-secondary font14 pb-3">CHOOSE TRAVEL CLASS</div>
                                                    <div class="float-end w-100">
                                                        <div class="treavelClass row mx-auto text-center">
                                                            <label class="travelClass col economy font14  mb-0 @if(Request::get('travelclass') == 'economy')  active @endif">
                                                                <input class="form-check-input dNone" type="radio" name="travelclass" id="economy" checked value="economy" @if(Request::get('travelclass') == 'economy')  checked @endif>
                                                                Economy
                                                            </label>
                                                            <label class="travelClass col premium font14 mb-0 @if(Request::get('travelclass') == 'premium') active @endif">
                                                                <input class="form-check-input dNone" type="radio" name="travelclass" id="premium" value="premium" @if(Request::get('travelclass') == 'premium') checked @endif>
                                                                Premium
                                                            </label>
                                                            <label class="travelClass col business font14 mb-0 @if(Request::get('travelclass') == 'business')  active @endif">
                                                                <input class="form-check-input dNone" type="radio" name="travelclass" id="business" value="business" @if(Request::get('travelclass') == 'business')  checked @endif>
                                                                Business
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                                <div class="p-3 border-bottom">
                                                <div class="row mx-auto">
                                                    <div class="float-start w-100 text-secondary font14 pb-3">SPECIAL FARE</div>
                                                    <div class="float-end w-100">
                                                        <label class="specialFare">
                                                            <label class="specialFare seniorCitizen font14  mb-0  @if(in_array("seniorCitizen", Request::get('specialFare') ?? [])) active @endif">
                                                                <input class="form-check-input dNone" type="checkbox" name="specialFare[]" id="seniorCitizen"  value="seniorCitizen" @if(in_array("seniorCitizen", Request::get('specialFare') ?? [])) checked @endif>
                                                                Senior Citizen
                                                            </label>
                                                            <label class="specialFare economy font14 mb-0 @if(in_array("armedForces", Request::get('specialFare') ?? [])) active @endif">
                                                                <input class="form-check-input dNone" type="checkbox" name="specialFare[]" id="armedForces"  value="armedForces" @if(in_array("armedForces", Request::get('specialFare') ?? [])) checked @endif>
                                                                Armed Forces
                                                            </label>
                                                            <label class="specialFare economy font14 mb-0 @if(in_array("studentFare", Request::get('specialFare')?? [])) active @endif">
                                                                <input class="form-check-input dNone" type="checkbox" name="specialFare[]" id="studentFare"  value="studentFare" @if(in_array("studentFare", Request::get('specialFare') ?? [])) checked @endif>
                                                                Student Fare
                                                            </label>
                                                        </label>
                                                    </div>
                                                </div>
                                                </div>
                                                <div class="p-3">
                                                    <button type="button" class="greenTransparentBtn active w-100" id="applyTravelers">Apply</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        if(Request::get('trip_type') == 'multiTrip'){
                                            $dropCities = Request::get('dropCity');
                                            $dateDepartures = Request::get('dateDeparture');

                                            $resultDrop = [];

                                            if (is_array($dropCities) && is_array($dateDepartures) && count($dropCities) == count($dateDepartures)) {
                                                for ($i = 0; $i < count($dropCities); $i++) {
                                                    $resultDrop[] = [
                                                        'dropCity' => $dropCities[$i],
                                                        'dateDeparture' => $dateDepartures[$i],
                                                    ];
                                                }
                                            }

                                        }
                                    @endphp
                                    <div class="d-none" id="multiTripSection">
                                        <div class="row mb-3" >

                                            @if(isset($resultDrop) && Request::get('trip_type') == 'multiTrip')
                                            @foreach ($resultDrop as $key => $element)
                                            @if ($loop->first)
                                            <div class="col-lg-3 px-2 position-relative">
                                                <div class="form-group fromBox pl-4 py-2 ">
                                                    <label class="text-secondary mb-1">
                                                        <i class="fa-solid fa-plane-departure" style="color: #6c757d;"></i> &nbsp;
                                                        Drop City
                                                    </label>
                                                    <br>
                                                    <input type="text" class=" form-control font700 left dropCity" name="dropCity[]" value="{{ $element['dropCity'] }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-3 px-2">
                                                <div class="form-group px-3 py-2 depart">
                                                    <label class="text-secondary mb-1">
                                                        <i class="fa-solid fa-calendar-days" style="color: #6c757d;"></i> &nbsp;
                                                        Depart
                                                    </label>
                                                    <br>
                                                    <input type="text" class="form-control font700 DatedepartureClass" id="" name="dateDeparture[]"  value="{{$element['dateDeparture']}}">
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                            @else
                                            <div class="col-lg-3 px-2 position-relative">
                                                <div class="form-group fromBox pl-4 py-2 ">
                                                    <label class="text-secondary mb-1">
                                                        <i class="fa-solid fa-plane-departure" style="color: #6c757d;"></i> &nbsp;
                                                        Drop City
                                                    </label>
                                                    <br>
                                                    <input type="text" class=" form-control font700 left dropCity" name="dropCity[0]" value="" id="dropCity_0">
                                                </div>
                                            </div>
                                            <div class="col-lg-3 px-2">
                                                <div class="form-group px-3 py-2 depart">
                                                    <label class="text-secondary mb-1">
                                                        <i class="fa-solid fa-calendar-days" style="color: #6c757d;"></i> &nbsp;
                                                        Depart
                                                    </label>
                                                    <br>
                                                    <input type="text" class="form-control font700 DatedepartureClass w-100" id="dateDeparture_0" name="dateDeparture[0]"  value="">
                                                </div>
                                            </div>
                                            @endif

                                        </div>
                                        <div class="addMultipleRow" >
                                            @if(isset($resultDrop) && Request::get('trip_type') == 'multiTrip')
                                            @foreach ($resultDrop as $key => $element)
                                            @if (!$loop->first)
                                            <div class="row mb-3 removeClassmultiple" >
                                                <div class="col-lg-3 px-2 position-relative">
                                                    <div class="form-group fromBox pl-4 py-2 ">
                                                        <label class="text-secondary mb-1">
                                                            <i class="fa-solid fa-plane-departure" style="color: #6c757d;"></i> &nbsp;
                                                            Drop City
                                                        </label>
                                                        <br>
                                                        <input type="text" class=" form-control font700 left" name="dropCity[]" value="{{ $element['dropCity'] }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 px-2">
                                                    <div class="form-group px-3 py-2 depart">
                                                        <label class="text-secondary mb-1">
                                                            <i class="fa-solid fa-calendar-days" style="color: #6c757d;"></i> &nbsp;
                                                            Depart
                                                        </label>
                                                        <br>
                                                        <input type="text" class="form-control font700 DatedepartureClass w-100" id="" name="dateDeparture[]"  value="{{$element['dateDeparture']}}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 px-2 position-relative text-center rmv cursor removeIconOuter">
                                                    <div class="px-3 py-4 depart">
                                                        <i class="fa fa-trash " aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                            @endif
                                        </div>
                                        <div class="col-lg-6 px-2 position-relative">
                                            <div class="px-3 py-3 depart">
                                                <button type="button" class="greenTransparentBtn mr-auto w-100" id='add_row'>Add Drop City</button>
                                            </div>
                                        </div>
                                    </div>

                            </section>


                            <div class="pt-4 text-right">
                                <button type="submit" class="mr-auto greenTransparentBtn" id="search_flight">Search Flight</button>
                                <button type="button" class="mr-auto cancelBtn" id="cancelBtn">Cancel</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Flight Listing -->
        <div class="container mobile-space-none flightListing">
            <div class="row listingProdGrid hotel-filter-details mt-5">
                <!-- Left Panel -->

                    <!-- @include('front.flight.left-filtration') -->
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12 my-5">
                                <div class="category-breadcrumb">
                                    <a href="javascript:showFilter('filteration')" class="filterBtn">Filter</a>
                                    <a href="javascript:;" id="sortby" class="d-none">Price low to high</a>
                                    <a href="javascript:;" id="airlines_type" class="d-none">Airlines</a>
                                    <a href="javascript:;" id="offers_type" class="d-none">Offers</a>
                                    <a href="javascript:;" id="stopFilter" class="d-none">Stop</a>
                                    <a href="javascript:;" id="clearsort" class="d-none">Clear</a>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="col-lg-3 mb-4 mobileFiltration" id="filteration">

                        <label class="closeFilteration shadow" onClick="hideFilter('filteration')">
                            &#x2716;
                        </label>

                        <div class="fliter_box_inner">
                            <div class="row">
                                <div class="col-12 py-3">
                                    <div class="filter_box box_css">
                                        <div class="row border-bottom mb-3 pb-3 mx-auto">
                                            <h2 class="col mb-0 px-0" style="padding-top: 2px;">Sort By</h2>
                                            <!-- <a href="#" class="col textGreen mb-0 px-0 text-right font16">Clear All</a> -->
                                        </div>
                                        <div class="size_checkbox">
                                            <div class="checkbox_comman">
                                                <form action="">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group custom_radio mb-2">
                                                                <input type="radio" name="sortby" id="value_pltoh" data-value="3" value="price low to high" class="filter attribute" @if(Request::get('sortby')=='price low to high' ) checked @endif>
                                                                <label for="value_pltoh">Price low to high</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group custom_radio mb-2">
                                                                <input type="radio" name="sortby" id="value_phtol" data-value="3" value="price high to low" class="filter attribute" @if(Request::get('sortby')=='price high to low' ) checked @endif>
                                                                <label for="value_phtol">Price High to low</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group custom_radio mb-2">
                                                                <input type="radio" name="sortby" id="value_nasc" data-value="3" value="name asc" class="filter attribute" @if(Request::get('sortby')=='name asc' ) checked @endif>
                                                                <label for="value_nasc">Flight Name Ascending</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group custom_radio mb-2">
                                                                <input type="radio" name="sortby" id="value_ndesc" data-value="3" value="name desc" class="filter attribute" @if(Request::get('sortby')=='name desc' ) checked @endif>
                                                                <label for="value_ndesc">Flight Name Descending</label>
                                                            </div>
                                                        </div>
                                                        <!-- <div class="col-12">
                                                            <div class="form-group custom_radio mb-2">
                                                                <input type="radio" name="sortby" id="departure_value_nasc" data-value="3" value="datenasc" class="filter attribute" @if(Request::get('sortby')=='datenasc' ) checked @endif>
                                                                <label for="departure_value_nasc">Departure Date Ascending</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group custom_radio mb-2">
                                                                <input type="radio" name="sortby" id="departure_value_ndesc" data-value="3" value="datendesc" class="filter attribute" @if(Request::get('sortby')=='datendesc' ) checked @endif>
                                                                <label for="departure_value_ndesc">Departure Date Descending</label>
                                                            </div>
                                                        </div> -->
                                                        <!-- <div class="col-12">
                                                            <div class="form-group custom_radio mb-2">
                                                                <input type="radio" name="sortby" id="non_stop" data-value="3" value="nonstop" class="filter attribute" @if(Request::get('sortby')=='nonstop' ) checked @endif>
                                                                <label for="non_stop">Non Stop</label>
                                                            </div>
                                                        </div> -->
                                                    </div>
                                                </form>
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
                                        <h2 class="mb-3 pb-3 border-bottom">Airlines</h2>
                                        <div class="size_checkbox">
                                            <div class="checkbox_comman">
                                                <form action="">
                                                    <div class="row">
                                                        @foreach($getAirlineDetails as $getAirlineName)
                                                        <div class="col-12">
                                                            <div class="form-group custom_checkbox mb-2">
                                                                <input type="checkbox" name="airlines_type[]" id="{{$getAirlineName['airlineName']}}" data-value="3" value="{{$getAirlineName['airlineName']}}" class="filter attribute" @if(!empty(Request::get('airlines_type')) && in_array($getAirlineName['airlineName'], Request::get('airlines_type'))) checked @endif>
                                                                <label for="{{$getAirlineName['airlineName']}}">{{$getAirlineName['airlineName']}}</label>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                        <!-- <div class="col-12">
                                                            <div class="form-group custom_checkbox mb-2">
                                                                <input type="checkbox" name="airlines_type[]" id="EtiHad" data-value="3" value="EtiHad" class="filter attribute" @if(!empty(Request::get('airlines_type')) && in_array('EtiHad', Request::get('airlines_type'))) checked @endif>
                                                                <label for="EtiHad">Etihad Airways</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group custom_checkbox mb-2">
                                                                <input type="checkbox" name="airlines_type[]" id="air_india" data-value="3" value="airIndia" class="filter attribute" @if(!empty(Request::get('airlines_type')) && in_array('airIndia', Request::get('airlines_type'))) checked @endif>
                                                                <label for="air_india">Air India</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group custom_checkbox mb-2">
                                                                <input type="checkbox" name="airlines_type[]" id="vistara" data-value="3" value="vistara" class="filter attribute" @if(!empty(Request::get('airlines_type')) && in_array('vistara', Request::get('airlines_type'))) checked @endif>
                                                                <label for="vistara">Vistara</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group custom_checkbox mb-2">
                                                                <input type="checkbox" name="airlines_type[]" id="spiceJet" data-value="3" value="spiceJet" class="filter attribute" @if(!empty(Request::get('airlines_type')) && in_array('spiceJet', Request::get('airlines_type'))) checked @endif>
                                                                <label for="spiceJet">SpiceJet</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group custom_checkbox mb-2">
                                                                <input type="checkbox" name="airlines_type[]" id="qatar_airways" data-value="3" value="qatar_airways" class="filter attribute" @if(!empty(Request::get('airlines_type')) && in_array('qatar_airways', Request::get('airlines_type'))) checked @endif>
                                                                <label for="qatar_airways">Qatar Airways</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group custom_checkbox mb-2">
                                                                <input type="checkbox" name="airlines_type[]" id="emirates" data-value="3" value="emirates" class="filter attribute" @if(!empty(Request::get('airlines_type')) && in_array('emirates', Request::get('airlines_type'))) checked @endif>
                                                                <label for="emirates">Emirates</label>
                                                            </div>
                                                        </div> -->
                                                    </div>
                                                </form>
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
                                        <h2 class="mb-3 pb-3 border-bottom">Number of Stop</h2>
                                        <div class="size_checkbox">
                                            <div class="checkbox_comman">
                                                <form action="">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group custom_radio mb-2">
                                                                <input type="radio" name="stopFilter" id="non_stop" data-value="3" value="Direct Flight" class="filter attribute" @if(Request::get('stopFilter')=='Direct Flight' ) checked @endif>
                                                                <label for="non_stop">Non-stop</label>
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
                        <div class="fliter_box_inner mt-4">
                            <div class="row">
                                <div class="col-12 py-3">
                                    <div class="filter_box box_css">
                                        <h2 class="mb-3 pb-3 border-bottom">Offers</h2>
                                        <div class="size_checkbox">
                                            <div class="checkbox_comman">
                                                <form action="">
                                                    <div class="row">
                                                        <!-- <div class="col-12">
                                                            <div class="form-group custom_radio mb-2">
                                                                <input type="radio" name="offers_type" id="CT50AIR_flat" data-value="3" value="CT50AIR_flat" class="filter attribute" @if(Request::get('offers_type')=='CT50AIR_flat' ) checked @endif>
                                                                <label for="CT50AIR_flat">CT50AIR - Flat 50%</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group custom_radio mb-2">
                                                                <input type="radio" name="offers_type" id="DOM30AIR_flat" data-value="3" value="8" class="filter attribute" @if(Request::get('offers_type')=='DOM30AIR_flat' ) checked @endif>
                                                                <label for="DOM30AIR_flat">DOM30AIR - Flat 30%</label>
                                                            </div>
                                                        </div> -->
                                                        <div class="col-12">
                                                            <div class="form-group custom_checkbox mb-2">
                                                                <input type="checkbox" name="offers_type" id="offersType" data-value="3" value="true" class="filter attribute" @if(!empty(Request::get('offers_type')) && in_array('true', (array) Request::get('offers_type'))) checked @endif>
                                                                <label for="offersType">Offers</label>
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
                <!-- Right Panel -->

                    <div class="col-lg-9 mb-4 near-by-hotels rightPanel">
                        <div class="topBar mx-auto mb-4">

                            <label class="mb-1">{{Request::get('flyingfrom')}}, to {{Request::get('flyingTo')}},

                                @if(Request::get('trip_type') == 'roundTrip')
                                    and back
                                @endif
                                @if(Request::get('trip_type') == 'multiTrip')
                                    VIA({{implode(",",Request::get('dropCity'))}})

                                @endif

                                &nbsp; |  &nbsp; {{ \Carbon\Carbon::parse(Request::get('departureDates'))->format('D, d M') }}
                                @if(Request::get('trip_type') == 'roundTrip')
                                ,Return Date({{ \Carbon\Carbon::parse(Request::get('returnDates'))->format('D, d M') }})
                                @endif
                            </label>
                            <p class="text-secondary">
                                Adult:{{Request::get('adultsCount')}} Children:{{Request::get('childrenCount')}} Infants:{{Request::get('infantsCount')}} &nbsp; |  &nbsp; {{Request::get('travelclass')}}
                            </p>
                        </div>
                        <div class = "" id="flightlist">
                            @include('front.flight.flight_listing',['data' => $data])
                        </div>
                    </div>

            </div>
        </div>


    </div>
</section>
<script>

    $(document).ready(function () {

        var trip_type = {!! json_encode(Request::get('trip_type')) !!};

        if(trip_type == 'roundTrip'){
            $('#returnId').removeClass('dNone');
            $('#returnDate').removeAttr('readonly');
            $('.blockReturn').removeClass('blockReturn');
            $('#trip_type').val(trip_type);
        }else{
            if(trip_type == 'multiTrip'){
                $('#multiTripSection').removeClass('d-none');
			    $('#returnId').addClass('dNone');
                $('#trip_type').val(trip_type);

                $("#myform").delegate("#search_flight", "click", function () {
                    $(".dropCity").each(function(){
                        $(this).rules("add", {
                            required:true,
                            lettersOnly: true,
                            messages: {
                            required: "Please Enter the City",
                            lettersOnly: "Enter valid characters (only alphabets allowed)",
                        }
                        });
                    });
                    $(".DatedepartureClass").each(function(){
                        $(this).rules("add", {
                            required:true,
                            validDateFormat: true,
                            messages: {
                            required: "Please enter a date",
                            validDateFormat: "Enter a valid date in the format DD-MM-YYYY",

                            }
                        });
                    });
                });

            }else{
                $('#trip_type').val(trip_type);
            }
        }


        $("#cancelBtn").on('click', function (e) {
            window.location.href = "{{route('flight')}}";
        });

		$("#one-way-trip").on('click', function (e) {

			$('#returnId').addClass('dNone');
			// $('#oneRoundTrip').removeClass('dNone');
			$('#multiTripSection').addClass('d-none');
			// $('#returnDate').val('');
            $('#trip_type').val('one_way_trip');
			// $('.rmv').closest(".removeClassmultiple").remove();

                function processInputValues(inputName) {
                    var titles = $('input[name^=' + inputName + ']').map(function (idx, elem) {
                        var value = $(elem).val();
                        var combinedValue = value + '(' + $(elem).attr("name") + ')';

                        return {
                            value: value,
                            combinedValue: combinedValue
                        };
                    }).get();

                    var valuesArray = titles.map(function (item) {
                        return item.value;
                    });

                    var combinedValuesArray = titles.map(function (item) {
                        return item.combinedValue;
                    });

                    for (var i = 1; i <= valuesArray.length - 1; i++) {
                        if (valuesArray[i] === '') {
                            var originalString = combinedValuesArray[i].split("[");
                            var searchString = originalString[1].replace("(" + inputName, "");
                            var replacementString = searchString.split("])");
                            var modifiedString = replacementString[0].replace(",", "");
                            $('.removeClassmultiple_' + modifiedString).remove();
                        }
                    }
                }
            processInputValues('dateDeparture');

            processInputValues('dropCity');

        });
		$("#removeReadonly").on('click', function (e) {

			// $("#ReturnId").removeClass("intro");
			$('#returnId').removeClass('dNone');
			// $('#oneRoundTrip').removeClass('dNone');
			$('#multiTripSection').addClass('d-none');
            //
			var tripType = $('#trip_type').val('roundTrip');
            var return_date = $('#returnDate').val();

            if(!return_date){
                const date = new Date()
                let day = date.getDate()
                let month = date.getMonth() + 1
                let year = date.getFullYear()

                let fullDate =  `${day}-${month}-${year}`
                $('#returnDate').val(fullDate);
            }

            function processInputValues(inputName) {
                var titles = $('input[name^=' + inputName + ']').map(function (idx, elem) {
                    var value = $(elem).val();
                    var combinedValue = value + '(' + $(elem).attr("name") + ')';

                    return {
                        value: value,
                        combinedValue: combinedValue
                    };
                }).get();

                var valuesArray = titles.map(function (item) {
                    return item.value;
                });

                var combinedValuesArray = titles.map(function (item) {
                    return item.combinedValue;
                });

                for (var i = 1; i <= valuesArray.length - 1; i++) {
                    if (valuesArray[i] === '') {
                        var originalString = combinedValuesArray[i].split("[");
                        var searchString = originalString[1].replace("(" + inputName, "");
                        var replacementString = searchString.split("])");
                        var modifiedString = replacementString[0].replace(",", "");
                        $('.removeClassmultiple_' + modifiedString).remove();
                    }
                }
            }
            processInputValues('dateDeparture');

            processInputValues('dropCity');

        });

		$("#multi-Trip").on('click', function (e) {
			$('#multiTripSection').removeClass('d-none');
			$('#returnId').addClass('dNone');
			$('#trip_type').val('multiTrip');

        });
		$(function() {
            var availableAirport = {!! json_encode($airportCode) !!};

            // $( ".left" ).autocomplete({
            //    minLength:3,
            //    delay:500,
            //    source: availableAirport
            // });
			$(document).on("input", ".left", function() {
				$(this).autocomplete({
					minLength: 3,
					delay: 500,
					source: availableAirport
				});
			});

            $( "#right" ).autocomplete({
               minLength:3,
               delay:500,
               source: availableAirport
            });

    	});
        var i = 1;
		$("#add_row").click(function(){
            // form.validate();
            // var flags = true;

            // $(".dropCity").each(function () {
            //     if (!$(this).valid()) {
            //         flags = false;
            //        alert('ggg');
            //         return false;
            //     }
            // });
            $("#myform").delegate("#add_row", "click", function () {
                function checkValidity(className) {
                    var flags = true;

                    $("." + className).each(function () {
                        if (!$(this).valid()) {
                            flags = false;
                            return false;
                        }
                    });

                }
                checkValidity("dropCity");
                checkValidity("DatedepartureClass");
            });


            if (validator.form()) {
                var row = '<div class="row mb-3 removeClassmultiple removeClassmultiple_'+i+'" >' +
                '<div class="col-lg-3 px-2 position-relative">' +
                '<div class="form-group fromBox pl-4 py-2">' +
                '<label class="text-secondary mb-1">' +
                '<i class="fa-solid fa-plane-departure" style="color: #6c757d;"></i> &nbsp;' +
                'Drop City' +
                '</label>' +
                '<br>' +
                '<input type="text" class="form-control font700 left dropCity" name="dropCity['+i+']" value="">' +
                '</div>' +
                '</div>' +
                '<div class="col-lg-3 px-2">' +
                '<div class=" form-group px-3 py-2 depart">' +
                '<label class="text-secondary mb-1">' +
                '<i class="fa-solid fa-calendar-days" style="color: #6c757d;"></i> &nbsp;' +
                'Depart' +
                '</label>' +
                '<br>' +
                '<input type="text" class=" form-control font700 DatedepartureClass w-100" id="" name="dateDeparture['+i+']"  value="">' +
                '</div>' +
                '</div>' +
                '<div class="col-lg-1 px-2 position-relative text-center rmv cursor removeIconOuter">' +
                '<div class="px-3 py-4 depart">' +
                '<i class="fa fa-trash " aria-hidden="true"></i>'
                // '<button type="button" class="tripBtn mr-auto w-100 rmv">Remove Drop City</button>' +
                '</div>' +
                '</div>' +
                '</div>';

                $(".addMultipleRow").append(row);
                // initializeValidation();
                i++;
            }

		});



		$("body").on("click",".rmv",function(){
			$(this).closest(".removeClassmultiple").remove();
		});


        var travelclass = $('input[name="travelclass"]:checked').val();

        $("#applyTravelers").on("click", function () {
			adultsCount = parseInt($('#adultsCount').val()) || 0;
			childrenCount = parseInt($('#childrenCount').val()) || 0;
			infantsCount = parseInt($('#infantsCount').val()) || 0;

			var totalCount = adultsCount + childrenCount + infantsCount;
			$('#passenger').val(totalCount);

			var travelclass = $('input[name="travelclass"]:checked').val();
			var checkedSpecialFares = [];


			$("input[name='specialFare[]']:checked").each(function () {
				checkedSpecialFares.push($(this).attr("value")); //
			});
			var specialFare = checkedSpecialFares.join(", ");

			$("#addPassanger").hide();

		});

        // Adding a custom rule for letters only
        $.validator.addMethod(
            "lettersOnly",
            function (value, element) {
                // return this.optional(element) || /^[a-zA-Z,() ]+$/.test(value);
                return this.optional(element) || /^[a-zA-Z,()\- ]+$/.test(value);
            },
            "Enter valid characters (only alphabets allowed)"
        );
        $.validator.addMethod(
            "validDateFormat",
            function (value, element) {

                return this.optional(element) || /^\d{1,2}-\d{1,2}-\d{4}$/.test(value);
            },
            "Enter a valid date in the format DD-MM-YYYY"
        );

        var validator = $(".flight-searching-form").validate({
			ignore: '.tab-pane:not(.active) :input',
			errorClass: "invalid-feedback animated fadeInDown",
			errorElement: "div",
			errorPlacement: function(e, a) {
				if (jQuery(a).closest(".form-group").find('.invalid-feedback').length > 0)
					jQuery(a).closest(".form-group").find('.invalid-feedback').remove();

				jQuery(a).closest(".form-group").append(e)

				var name = jQuery(a).attr('name');
				jQuery(e).attr('id', 'invalid-' + name);
			},
			highlight: function(e) {
				jQuery(e).closest(".form-group").removeClass("is-invalid").addClass("is-invalid")
			},
			success: function(e) {
				jQuery(e).closest(".form-group").removeClass("is-invalid"), jQuery(e).remove()
			},
			rules: {
				flyingfrom:{
					required:!0,
					lettersOnly: true,
				},
				flyingTo:{
					required:!0,
					lettersOnly: true,
				},
				departureDates:{
					required:!0,
					validDateFormat: true,
				},
				// dropCity:{
				// 	required:!0,
				//     lettersOnly: true,
				// },
				// returnDates: {
				// 	required: function(element) {
				// 		return $("#removeReadonly").hasClass("active");
				// 	},
				// 	validDateFormat: true,
				// },


			},
			messages: {
				flyingfrom:{
					required:"Please Enter the City",
					lettersOnly: "Enter valid characters (only alphabets allowed)",
				},
				flyingTo:{
					required:"Please Enter the City",
					lettersOnly: "Enter valid characters (only alphabets allowed)",
				},
				departureDates: {
						required: "Please enter a date",
						dateISO: "Enter a valid date in the format DD-MM-YYYY",
				},
				returnDates: {
						required: "Please enter a date",
						dateISO: "Enter a valid date in the format DD-MM-YYYY",
				},
				// dropCity:{
				//     required:"Please Enter the City",
				//     lettersOnly: "Enter valid characters (only alphabets allowed)",
				// },

			}
		})

		$("#removeReadonly").click(function() {
			validator.settings.rules.returnDates = {
				required:!0,
				validDateFormat: true,
			};
			validator.settings.messages.returnDates = {
				required: "Please enter a return date",
				dateISO: "Enter a valid return date in the format DD-MM-YYYY",
			};
            $("#myform").delegate("#search_flight", "click", function () {
				$(".dropCity, .DatedepartureClass").each(function(){
					$(this).rules("remove");
				});
			});
            validator.resetForm();
    	});

		$("#multi-Trip").click(function() {
			// validator.resetForm();
            $(".dropCity").each(function(){
					$(this).rules("add", {
						required:true,
						lettersOnly: true,
						messages: {
						required: "Please Enter the City",
						lettersOnly: "Enter valid characters (only alphabets allowed)",
					}
					});
			});
            $(".DatedepartureClass").each(function(){
                $(this).rules("add", {
                    required:true,
                    validDateFormat: true,
                    messages: {
                    required: "Please enter a date",
                    validDateFormat: "Enter a valid date in the format DD-MM-YYYY",

                    }
                });
            });
            $("#myform").delegate("#add_row", "click", function () {
			    $(".dropCity").each(function(){
					$(this).rules("add", {
						required:true,
						lettersOnly: true,
						messages: {
						required: "Please Enter the City",
						lettersOnly: "Enter valid characters (only alphabets allowed)",
					}
					});
				});
                $(".DatedepartureClass").each(function(){
					$(this).rules("add", {
						required:true,
						validDateFormat: true,
						messages: {
						required: "Please enter a date",
						validDateFormat: "Enter a valid date in the format DD-MM-YYYY",

						}
					});
				});
            });
            $("#myform").delegate("#search_flight", "click", function () {
				$(".dropCity").each(function(){
					$(this).rules("add", {
						required:true,
						lettersOnly: true,
						messages: {
						required: "Please Enter the City",
						lettersOnly: "Enter valid characters (only alphabets allowed)",
					}
					});
				});
				$(".DatedepartureClass").each(function(){
					$(this).rules("add", {
						required:true,
						validDateFormat: true,
						messages: {
						required: "Please enter a date",
						validDateFormat: "Enter a valid date in the format DD-MM-YYYY",

						}
					});
				});
			});

		});

		$("#one-way-trip").on('click', function (e) {

			validator.showErrors({
                "returnDates": "",
            });
			delete validator.settings.rules.returnDates;
            delete validator.settings.messages.returnDates;
            $("#myform").delegate("#search_flight", "click", function () {
				$(".dropCity, .DatedepartureClass").each(function(){
					$(this).rules("remove");
				});
			});
			validator.resetForm();
        });
		$("#add_row").click(function () {

			// $('.addMultipleRow input[name^="dropCity"]').rules('add', {
			// 	required: true,
			// 	lettersOnly: true,
			// 	messages: {
			// 		required: "Please Enter the City",
			// 		lettersOnly: "Enter valid characters (only alphabets allowed)",
			// 	},
			// });

			// $('.addMultipleRow input[name^="dateDeparture"]').rules('add', {
			// 	required: true,
			// 	validDateFormat: true,
			// 	messages: {
			// 		required: "Please enter a date",
			// 		dateISO: "Enter a valid date in the format DD-MM-YYYY",
			// 	},
			// });


		    validator = $(".flight-searching-form").validate(validator.settings);


			$(".flight-searching-form").valid();
		});

        $(document).on('click', function (e) {
            if (!$(e.target).closest(".addPassangerClass").length) {
                $(".OneId").hide();
            }
        });

        $(".addPassangerClass").on('click', function (e) {
            e.stopPropagation();
        });

        // $("#search_flight").click(function(e) {
        $("#myform").submit(function(e) {
            e.preventDefault();

            if (validator.form()) {
                var checkedSpecialFares = [];
                $("input[name='specialFare[]']:checked").each(function () {
                checkedSpecialFares.push($(this).attr("value")); //
                });
                var specialFareParams = checkedSpecialFares.map(function (fare) {
                return 'specialFare%5B%5D=' + fare;
                });

                var specialFare = specialFareParams.join("&");

                var trip_types = $('#trip_type').val();
                if(trip_types == 'roundTrip'){

                    var refresh = window.location.protocol + "//" + window.location.host + window.location.pathname + '?trip_type='+$('#trip_type').val() + '&flyingfrom= '+ $('#left').val() + '&flyingTo= '+ $('#right').val() + '&departureDates= '+$('#departureDate').val() +'&returnDates='+$('#returnDate').val() + '&passenger='+$('#passenger').val() + '&adultsCount='+$('#adultsCount').val()+'&childrenCount='+$('#childrenCount').val() +'&infantsCount='+$('#infantsCount').val()+'&travelclass='+$('input[name="travelclass"]:checked').val() +'&' +specialFare;

                    }else{
                        if(trip_types== 'one_way_trip'){
                        var refresh = window.location.protocol + "//" + window.location.host + window.location.pathname + '?trip_type='+$('#trip_type').val() + '&flyingfrom= '+ $('#left').val() + '&flyingTo= '+ $('#right').val() + '&departureDates= '+$('#departureDate').val() +'&passenger='+$('#passenger').val() + '&adultsCount='+$('#adultsCount').val()+'&childrenCount='+$('#childrenCount').val() +'&infantsCount='+$('#infantsCount').val()+'&travelclass='+$('input[name="travelclass"]:checked').val() +'&' +specialFare;
                        }else{
                            var dateDepartureFareParams = $('input[name^=dateDeparture]').map(function(idx, elem) {
                                // return $(elem).val();
                                return 'dateDeparture%5B%5D=' + $(elem).val();
                            }).get();
                            var dateDeparture = dateDepartureFareParams.join("&");

                            var dropCityFareParams = $('input[name^=dropCity]').map(function(idx, elem) {
                                // return $(elem).val();
                                return 'dropCity%5B%5D=' + $(elem).val();
                            }).get();
                            var dropCity = dropCityFareParams.join("&");
                            var refresh = window.location.protocol + "//" + window.location.host + window.location.pathname + '?trip_type='+$('#trip_type').val() + '&flyingfrom= '+ $('#left').val() + '&flyingTo= '+ $('#right').val() + '&departureDates= '+$('#departureDate').val() +'&passenger='+$('#passenger').val() + '&adultsCount='+$('#adultsCount').val()+'&childrenCount='+$('#childrenCount').val() +'&infantsCount='+$('#infantsCount').val()+'&travelclass='+$('input[name="travelclass"]:checked').val() +'&' +specialFare + '&' + dateDeparture + '&' + dropCity;
                        }
                }

                window.history.pushState({ path: refresh }, '', refresh);
                setTimeout(function(){
                        location.reload();
                    }, 2000);
            }
        });



    });
</script>
<script>

    $(document).ready(function () {
        // airline
        // airlines_type
        var airlines = '';
        var airlines_types = [];
        $('[name="airlines_type[]"]:checked').each(function(index, item) {
            airlines += '&airlines_type[]=' + $(item).val();
            airlines_types.push($(item).val());
        })

        if($('[name=sortby]:checked').val()!=undefined)
        {

            $('#sortby').removeClass('d-none').html($('[name=sortby]:checked').next().text());
            $('#clearsort').removeClass('d-none');
        }
        else{
            $('#sortby').addClass('d-none');
        }

        // offers type
        if($('[name=offers_type]:checked').val()!=undefined)
        {

            $('#offers_type').removeClass('d-none').html($('[name=offers_type]:checked').next().text());
            $('#clearsort').removeClass('d-none');
        }
        else{
            $('#offers_type').addClass('d-none');
        }
        //stopFilter
        if($('[name=stopFilter]:checked').val()!=undefined)
        {

            $('#stopFilter').removeClass('d-none').html($('[name=stopFilter]:checked').next().text());
            $('#clearsort').removeClass('d-none');
        }
        else{
            $('#stopFilter').addClass('d-none');
        }

        if(airlines_types.length > 0)
        {
            $('#airlines_type').removeClass('d-none').html(airlines_types.join('/'));
            $('#clearsort').removeClass('d-none');
        }
        else{
            $('#airlines_type').addClass('d-none');
        }
        //offers_type  stopFilter
        if($('[name=sortby]:checked').val()==undefined && airlines_types.length <= 0 && $('[name=offers_type]:checked').val()==undefined && $('[name=stopFilter]:checked').val()==undefined){
    	$('#clearsort').addClass('d-none');
        }

        $('.filter').click(function() {
            // alert('dd');
            var airlines = '';
            var airlines_types = [];
            $('[name="airlines_type[]"]:checked').each(function(index, item) {
                airlines += '&airlines_type[]=' + $(item).val();
                airlines_types.push($(item).val());
            })

            // var refresh = window.location.protocol + "//" + window.location.host + window.location.pathname + '?state={{Request::get('state')}}&city={{Request::get('city')}}&no_rooms={{Request::get('no_rooms')}}&no_adults={{Request::get('no_adults')}}&no_child={{Request::get('no_child')}}&check_in={{Request::get('check_in')}}&check_out={{Request::get('check_out')}}&sortby=' + $('[name=sortby]:checked').val() + rooms;
            // window.history.pushState({ path: refresh }, '', refresh);

            var trip_types = {!! json_encode(Request::get('trip_type')) !!};


            var checkedSpecialFares = {!! json_encode(Request::get('specialFare')) !!};

            if(checkedSpecialFares == null){
                var specialFare = '';
            }else{
                var specialFareParams = checkedSpecialFares.map(function (fare) {
                return 'specialFare%5B%5D=' + fare;
                });

                var specialFare = specialFareParams.join("&");
            }


            if(trip_types == 'roundTrip'){

                // alert(specialFare);
                // console.log(specialFare);

                // var refresh = window.location.protocol + "//" + window.location.host + window.location.pathname + '?trip_type='+$('#trip_type').val() + '&flyingfrom= '+ $('#left').val() + '&flyingTo= '+ $('#right').val() + '&departureDates= '+$('#departureDate').val() +'&returnDates='+$('#returnDate').val() + '&passenger='+$('#passenger').val() + '&adultsCount='+$('#adultsCount').val()+'&childrenCount='+$('#childrenCount').val() +'&infantsCount='+$('#infantsCount').val()+'&travelclass='+$('input[name="travelclass"]:checked').val() +'&' +specialFare;
                var refresh = window.location.protocol + "//" + window.location.host + window.location.pathname + '?trip_type={{Request::get('trip_type')}}&flyingfrom={{Request::get('flyingfrom')}}&flyingTo={{Request::get('flyingTo')}}&departureDates={{Request::get('departureDates')}}&returnDates={{Request::get('returnDates')}}&passenger={{Request::get('passenger')}}&adultsCount={{Request::get('adultsCount')}}&childrenCount={{Request::get('childrenCount')}}&infantsCount={{Request::get('infantsCount')}}&travelclass={{Request::get('travelclass')}}&'+specialFare+'&sortby=' + $('[name=sortby]:checked').val() + '&offers_type=' + $('[name=offers_type]:checked').val() + '&stopFilter=' + $('[name=stopFilter]:checked').val() + airlines;

                }else{
                    if(trip_types== 'one_way_trip'){
                    // var refresh = window.location.protocol + "//" + window.location.host + window.location.pathname + '?trip_type='+$('#trip_type').val() + '&flyingfrom= '+ $('#left').val() + '&flyingTo= '+ $('#right').val() + '&departureDates= '+$('#departureDate').val() +'&passenger='+$('#passenger').val() + '&adultsCount='+$('#adultsCount').val()+'&childrenCount='+$('#childrenCount').val() +'&infantsCount='+$('#infantsCount').val()+'&travelclass='+$('input[name="travelclass"]:checked').val() +'&' +specialFare;
                    var refresh = window.location.protocol + "//" + window.location.host + window.location.pathname + '?trip_type={{Request::get('trip_type')}}&flyingfrom={{Request::get('flyingfrom')}}&flyingTo={{Request::get('flyingTo')}}&departureDates={{Request::get('departureDates')}}&passenger={{Request::get('passenger')}}&adultsCount={{Request::get('adultsCount')}}&childrenCount={{Request::get('childrenCount')}}&infantsCount={{Request::get('infantsCount')}}&travelclass={{Request::get('travelclass')}}&'+specialFare+'&sortby=' + $('[name=sortby]:checked').val() + '&offers_type=' + $('[name=offers_type]:checked').val() + '&stopFilter=' + $('[name=stopFilter]:checked').val() + airlines;
                    }else{
                        // alert('specialFare');
                        var dateDepartureArray = {!! json_encode(Request::get('dateDeparture')) !!};
                        // alert(checkedSpecialFares);
                        // console.log(checkedSpecialFares);
                        var dateDepartureFareParams = dateDepartureArray.map(function (fare) {
                            return 'dateDeparture%5B%5D=' + fare;
                        });
                        // var dateDepartureFareParams = dateDepartureArray.map(function(idx, elem) {
                        //     return 'dateDeparture%5B%5D=' + $(elem).val();
                        // }).get();
                        var dateDeparture = dateDepartureFareParams.join("&");

                        var dropCityFareArray = {!! json_encode(Request::get('dropCity')) !!};
                        // var dropCityFareParams = dropCityFareArray.map(function(idx, elem) {
                        //     return 'dropCity%5B%5D=' + $(elem).val();
                        // }).get();
                        var dropCityFareParams = dropCityFareArray.map(function (fare) {
                            return 'dropCity%5B%5D=' + fare;
                        });
                        var dropCity = dropCityFareParams.join("&");
                        // alert()
                        // var refresh = window.location.protocol + "//" + window.location.host + window.location.pathname + '?trip_type='+$('#trip_type').val() + '&flyingfrom= '+ $('#left').val() + '&flyingTo= '+ $('#right').val() + '&departureDates= '+$('#departureDate').val() +'&passenger='+$('#passenger').val() + '&adultsCount='+$('#adultsCount').val()+'&childrenCount='+$('#childrenCount').val() +'&infantsCount='+$('#infantsCount').val()+'&travelclass='+$('input[name="travelclass"]:checked').val() +'&' +specialFare + '&' + dateDeparture + '&' + dropCity;
                        var refresh = window.location.protocol + "//" + window.location.host + window.location.pathname + '?trip_type={{Request::get('trip_type')}}&flyingfrom={{Request::get('flyingfrom')}}&flyingTo={{Request::get('flyingTo')}}&departureDates={{Request::get('departureDates')}}&passenger={{Request::get('passenger')}}&adultsCount={{Request::get('adultsCount')}}&childrenCount={{Request::get('childrenCount')}}&infantsCount={{Request::get('infantsCount')}}&travelclass={{Request::get('travelclass')}}&'+ specialFare + dateDeparture + '&' + dropCity+'&sortby=' + $('[name=sortby]:checked').val() + '&offers_type=' + $('[name=offers_type]:checked').val() + '&stopFilter=' + $('[name=stopFilter]:checked').val() + airlines;
                        // alert(refresh);
                        // comsole.log(refresh);
                    }
            }

            window.history.pushState({ path: refresh }, '', refresh);

            if($('[name=sortby]:checked').val()!=undefined)
            {
                $('#sortby').removeClass('d-none').html($('[name=sortby]:checked').next().text());
                $('#clearsort').removeClass('d-none');
            }
            else{
                $('#sortby').addClass('d-none');
            }
            //offers_type
            if($('[name=offers_type]:checked').val()!=undefined)
            {
                $('#offers_type').removeClass('d-none').html($('[name=offers_type]:checked').next().text());
                $('#clearsort').removeClass('d-none');
            }
            else{
                $('#offers_type').addClass('d-none');
            }
            //stopFilter
            if($('[name=stopFilter]:checked').val()!=undefined)
            {
                $('#stopFilter').removeClass('d-none').html($('[name=stopFilter]:checked').next().text());
                $('#clearsort').removeClass('d-none');
            }
            else{
                $('#stopFilter').addClass('d-none');
            }

            if(airlines_types.length > 0)
            {
                $('#airlines_type').removeClass('d-none').html(airlines_types.join('/'));
                $('#clearsort').removeClass('d-none');
            }else{
                $('#airlines_type').addClass('d-none');
            }
            //offers_type
            if($('[name=sortby]:checked').val()==undefined && airlines_types.length <= 0 && $('[name=offers_type]:checked').val()==undefined && $('[name=stopFilter]:checked').val()==undefined){
                $('#clearsort').addClass('d-none');
            }

            $.ajax({
                type:'get',
                url:window.location.href,
                success:function(data){
                    $('#flightlist').html(data);
                }
            })
        })

        $('#clearsort').click(function(){

            $('[name=sortby]').prop('checked',false);
            $('[name=offers_type]').prop('checked',false);
            $('[name=stopFilter]').prop('checked',false);
            $('[name="airlines_type[]"]').prop('checked',false);

            var trip_types = {!! json_encode(Request::get('trip_type')) !!};


            var checkedSpecialFares = {!! json_encode(Request::get('specialFare')) !!};

            if(checkedSpecialFares == null){
                var specialFare = '';
            }else{
                var specialFareParams = checkedSpecialFares.map(function (fare) {
                return 'specialFare%5B%5D=' + fare;
                });

                var specialFare = specialFareParams.join("&");
            }


            if(trip_types == 'roundTrip'){

                // alert(specialFare);
                // console.log(specialFare);


                var refresh = window.location.protocol + "//" + window.location.host + window.location.pathname + '?trip_type={{Request::get('trip_type')}}&flyingfrom={{Request::get('flyingfrom')}}&flyingTo={{Request::get('flyingTo')}}&departureDates={{Request::get('departureDates')}}&returnDates={{Request::get('returnDates')}}&passenger={{Request::get('passenger')}}&adultsCount={{Request::get('adultsCount')}}&childrenCount={{Request::get('childrenCount')}}&infantsCount={{Request::get('infantsCount')}}&travelclass={{Request::get('travelclass')}}&'+specialFare;

                }else{
                    if(trip_types== 'one_way_trip'){
                    var refresh = window.location.protocol + "//" + window.location.host + window.location.pathname + '?trip_type={{Request::get('trip_type')}}&flyingfrom={{Request::get('flyingfrom')}}&flyingTo={{Request::get('flyingTo')}}&departureDates={{Request::get('departureDates')}}&passenger={{Request::get('passenger')}}&adultsCount={{Request::get('adultsCount')}}&childrenCount={{Request::get('childrenCount')}}&infantsCount={{Request::get('infantsCount')}}&travelclass={{Request::get('travelclass')}}&'+specialFare;
                    }else{

                        var dateDepartureArray = {!! json_encode(Request::get('dateDeparture')) !!};

                        var dateDepartureFareParams = dateDepartureArray.map(function (fare) {
                            return 'dateDeparture%5B%5D=' + fare;
                        });
                        // var dateDepartureFareParams = dateDepartureArray.map(function(idx, elem) {
                        //     return 'dateDeparture%5B%5D=' + $(elem).val();
                        // }).get();
                        var dateDeparture = dateDepartureFareParams.join("&");

                        var dropCityFareArray = {!! json_encode(Request::get('dropCity')) !!};
                        // var dropCityFareParams = dropCityFareArray.map(function(idx, elem) {
                        //     return 'dropCity%5B%5D=' + $(elem).val();
                        // }).get();
                        var dropCityFareParams = dropCityFareArray.map(function (fare) {
                            return 'dropCity%5B%5D=' + fare;
                        });
                        var dropCity = dropCityFareParams.join("&");
                        var refresh = window.location.protocol + "//" + window.location.host + window.location.pathname + '?trip_type={{Request::get('trip_type')}}&flyingfrom={{Request::get('flyingfrom')}}&flyingTo={{Request::get('flyingTo')}}&departureDates={{Request::get('departureDates')}}&passenger={{Request::get('passenger')}}&adultsCount={{Request::get('adultsCount')}}&childrenCount={{Request::get('childrenCount')}}&infantsCount={{Request::get('infantsCount')}}&travelclass={{Request::get('travelclass')}}&'+ specialFare + dateDeparture + '&' + dropCity;

                    }
            }

            window.history.pushState({ path: refresh }, '', refresh);

            var airlines = '';
            var airlines_types = [];

            $('[name="airlines_type[]"]:checked').each(function(index, item) {
                airlines += '&airlines_type[]=' + $(item).val();
                airlines_types.push($(item).val());
            })

            if($('[name=sortby]:checked').val()!=undefined)
            {
                $('#sortby').removeClass('d-none').html($('[name=sortby]:checked').text());
                $('#clearsort').removeClass('d-none');
            }
            else{
                $('#sortby').addClass('d-none');
            }
            //offers_type
            if($('[name=offers_type]:checked').val()!=undefined)
            {
                $('#offers_type').removeClass('d-none').html($('[name=offers_type]:checked').text());
                $('#clearsort').removeClass('d-none');
            }
            else{
                $('#offers_type').addClass('d-none');
            }
            //stopFilter
            if($('[name=stopFilter]:checked').val()!=undefined)
            {
                $('#stopFilter').removeClass('d-none').html($('[name=stopFilter]:checked').text());
                $('#clearsort').removeClass('d-none');
            }
            else{
                $('#stopFilter').addClass('d-none');
            }

            if(airlines_types.length > 0)
            {
                $('#airlines_type').removeClass('d-none').html(airlines_types.join('/'));
                $('#clearsort').removeClass('d-none');
            }
            else{
                $('#airlines_type').addClass('d-none');
            }
            // offers_type stopFilter
            if($('[name=sortby]:checked').val()==undefined && airlines_types.length <= 0 && $('[name=offers_type]:checked').val()==undefined && $('[name=stopFilter]:checked').val()==undefined){
                $('#clearsort').addClass('d-none');
            }

            // $.ajax({
            //     type:'get',
            //     url:window.location.href,
            //     success:function(data){
            //         console.log(data);
            //         $('#flightlist').html(data);
            //     }
            // })
        })


    });

    // Filtration (Hide/Show)
    function showFilter(idName){
        $("#"+idName).show();
    }

    function hideFilter(idName){
        $("#"+idName).hide();
    }
</script>
@endsection