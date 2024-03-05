@extends('front.flight.layouts.master')
@section('template_title','Flight checkout')
@push('styles')

@endpush
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
                                        <button type="button" class="oneWay tripBtn  mr-3  me-1 @if(Request::get('trip_type') == 'one_way_trip')  active @endif" id="one-way-trip" disabled> One-Way Trip</button>
                                        <button type="button" class="blockBtn roundTrip tripBtn  mr-3 @if(Request::get('trip_type') == 'roundTrip')  active @endif" id="removeReadonly" disabled>Round Trip</button>
                                        <button type="button" class="blockBtn multiTrip tripBtn @if(Request::get('trip_type') == 'multiTrip')  active @endif" id="multi-Trip" disabled>Multi Trip</button>
                                        <input type="hidden" id="trip_type" name="trip_type" value="{{Request::get('trip_type')}}">
                                    </div>
                                    <section class="" id="oneRoundTrip">
                                            <div class="row">
                                                <div class="col-lg-3 pr-2 position-relative">
                                                    <div class="form-group fromBox pl-4 py-2">
                                                        <label class="text-secondary mb-1">
                                                            <i class="fa-solid fa-plane-departure" style="color: #6c757d;"></i> &nbsp;
                                                            Flying from
                                                        </label>
                                                        <input type="text" class="form-control font700 left" id="left" name="flyingfrom" value="{{Request::get('flyingfrom')}}" readonly>
                                                    </div>
                                                    <div class=" Flyingleft text-danger"></div>
                                                    <button type="button" class="swapLocation position-absolute"  id="swaploction"></button>
                                                </div>

                                                <div class="form-group col-lg-3 px-2">
                                                    <div class="toBox pl-4 py-2">
                                                        <label class="text-secondary mb-1">
                                                            <i class="fa-solid fa-plane-arrival" style="color: #6c757d;"></i> &nbsp;
                                                            Flying to
                                                        </label>
                                                        <input type="text" class="form-control font700" id="right" name="flyingTo" value="{{Request::get('flyingTo')}}" readonly>
                                                    </div>
                                                    <div class="Flyingright text-danger"></div>
                                                </div>
                                                <div class="form-group col-lg px-2">
                                                    <div class="px-3 py-2 depart">
                                                        <label class="text-secondary mb-1">
                                                            <i class="fa-solid fa-calendar-days" style="color: #6c757d;"></i> &nbsp;
                                                            Depart
                                                        </label>
                                                        <input type="text" class="form-control font700" id="Datesdeparture" name="departureDates"  value="{{Request::get('departureDates')}}" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group col-lg px-2 dNone" id="returnId">
                                                    <div class="px-3 py-2 depart blockReturn" id="return">
                                                        <label class="text-secondary mb-1">
                                                            <i class="fa-solid fa-calendar-days" style="color: #6c757d;"></i> &nbsp;
                                                            Return
                                                        </label>
                                                        <input type="text" class="form-control font700 " id="Datesreturn" name="returnDates"  value="{{Request::get('returnDates')}}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg px-2 position-relative addPassangerClass" id="">
                                                    <div class="px-3 py-2 depart" onClick="showInfo('addPassanger')">
                                                        <label class="text-secondary mb-1">
                                                            <i class="fa-solid fa-users" style="color: #6c757d;"></i> &nbsp;
                                                            Travelers
                                                        </label>
                                                        <input type="text" class="font700" readonly id="passenger" name="passenger" value="{{Request::get('passenger')}}">
                                                    </div>
                                                    <div class="position-absolute addPassanger bg-light shadow OneId" id="addPassanger">


                                                    <div class="p-3 border-bottom">
                                                        <div class="row mx-auto mb-3">
                                                            <div class="float-start w-50 text-secondary font14 addPanel">ADULTS (12y +)</div>
                                                            <div class="float-end w-50 text-right">
                                                                <select name="adultsCount" class="w-25" id="adultsCount" readonly>
                                                                    <option value="{{Request::get('adultsCount')}}" >{{Request::get('adultsCount')}}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row mx-auto mb-3">
                                                            <div class="float-start w-50 text-secondary font14 addPanel">CHILDREN (2y - 12y )</div>
                                                            <div class="float-end w-50 text-right">
                                                                <select name="childrenCount" class="w-25" id="childrenCount" readonly>
                                                                    <option value="{{Request::get('childrenCount')}}">{{Request::get('childrenCount')}}</option>
                                                                </select>
                                                                <!-- <div class="wrap">
                                                                    <button type="button" class="sub">-</button>
                                                                    <input class="count text-center" type="text" id="childrenCount" value="0" min="1" max="100" />
                                                                    <button type="button" class="add">+</button>
                                                                </div> -->
                                                            </div>
                                                        </div>
                                                        <div class="row mx-auto">
                                                            <div class="float-start w-50 text-secondary font14 addPanel">INFANTS (below 2y)</div>
                                                            <div class="float-end w-50 text-right">
                                                                <select name="infantsCount" class="w-25" id="infantsCount" readonly>
                                                                    <option value="{{Request::get('infantsCount')}}" >{{Request::get('infantsCount')}}</option>
                                                                </select>
                                                                <!-- <div class="wrap">
                                                                    <button type="button" class="sub">-</button>
                                                                    <input class="count text-center" type="text" id="infantsCount" value="0" min="1" max="100" />
                                                                    <button type="button" class="add">+</button>
                                                                </div> -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                        <div class="p-3 border-bottom">
                                                        <div class="row mx-auto">
                                                            <div class="float-start w-100 text-secondary font14 pb-3">CHOOSE TRAVEL CLASS</div>
                                                            <div class="float-end w-100">
                                                                <div class="treavelClass row mx-auto text-center">
                                                                    <label class="col economy font14  mb-0 @if(Request::get('travelclass') == 'economy')  active @endif">
                                                                        <input class="form-check-input dNone" type="radio" name="travelclass" id="economy" checked value="economy" @if(Request::get('travelclass') == 'economy')  checked @endif  >
                                                                        Economy
                                                                    </label>
                                                                    <label class="col premium font14 mb-0 @if(Request::get('travelclass') == 'premium') active @endif">
                                                                        <input class="form-check-input dNone" type="radio" name="travelclass" id="premium" value="premium" @if(Request::get('travelclass') == 'premium') checked @endif   >
                                                                        Premium
                                                                    </label>
                                                                    <label class="col business font14 mb-0 @if(Request::get('travelclass') == 'business')  active @endif">
                                                                        <input class="form-check-input dNone" type="radio" name="travelclass" id="business" value="business" @if(Request::get('travelclass') == 'business')  checked @endif  >
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
                                                                    <label class=" seniorCitizen font14  mb-0  @if(in_array("seniorCitizen", Request::get('specialFare') ?? [])) active @endif">
                                                                        <input class="form-check-input dNone" type="checkbox" name="specialFare[]" id="seniorCitizen"  value="seniorCitizen" @if(in_array("seniorCitizen", Request::get('specialFare') ?? [])) checked @endif>
                                                                        Senior Citizen
                                                                    </label>
                                                                    <label class=" economy font14 mb-0 @if(in_array("armedForces", Request::get('specialFare') ?? [])) active @endif">
                                                                        <input class="form-check-input dNone" type="checkbox" name="specialFare[]" id="armedForces"  value="armedForces" @if(in_array("armedForces", Request::get('specialFare') ?? [])) checked @endif>
                                                                        Armed Forces
                                                                    </label>
                                                                    <label class=" economy font14 mb-0 @if(in_array("studentFare", Request::get('specialFare')?? [])) active @endif">
                                                                        <input class="form-check-input dNone" type="checkbox" name="specialFare[]" id="studentFare"  value="studentFare" @if(in_array("studentFare", Request::get('specialFare') ?? [])) checked @endif>
                                                                        Student Fare
                                                                    </label>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        </div>
                                                        <div class="p-3">
                                                            <button type="button" class="tripBtn active w-100" id="applyTravelers">Apply</button>
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
                                                    <div class="col-lg-3 pr-2 position-relative">
                                                        <div class="form-group fromBox pl-4 py-2 ">
                                                            <label class="text-secondary mb-1">
                                                                <i class="fa-solid fa-plane-departure" style="color: #6c757d;"></i> &nbsp;
                                                                Drop City
                                                            </label>
                                                            <br>
                                                            <input type="text" class=" form-control font700 left " name="dropCity[]" value="{{ $element['dropCity'] }}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 px-2">
                                                        <div class="form-group px-3 py-2 depart">
                                                            <label class="text-secondary mb-1">
                                                                <i class="fa-solid fa-calendar-days" style="color: #6c757d;"></i> &nbsp;
                                                                Depart
                                                            </label>
                                                            <br>
                                                            <input type="text" class="form-control font700 " id="" name="dateDeparture[]"  value="{{$element['dateDeparture']}}" readonly>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                    <div class="col-lg-6 px-2 position-relative">
                                                        <div class="px-3 py-3 depart">
                                                            <button type="button" class="greenTransparentBtn mr-auto w-100" id='add_drop_city' disabled>Add Drop City</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="addMultipleRow" >
                                                    @if(isset($resultDrop) && Request::get('trip_type') == 'multiTrip')
                                                    @foreach ($resultDrop as $key => $element)
                                                    @if (!$loop->first)
                                                    <div class="row mb-3 " >
                                                        <div class="col-lg-3 pr-2 position-relative">
                                                            <div class="form-group fromBox pl-4 py-2 ">
                                                                <label class="text-secondary mb-1">
                                                                    <i class="fa-solid fa-plane-departure" style="color: #6c757d;"></i> &nbsp;
                                                                    Drop City
                                                                </label>
                                                                <br>
                                                                <input type="text" class=" form-control font700 " name="dropCity[]" value="{{ $element['dropCity'] }}" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-3 px-2">
                                                            <div class="form-group px-3 py-2 depart">
                                                                <label class="text-secondary mb-1">
                                                                    <i class="fa-solid fa-calendar-days" style="color: #6c757d;"></i> &nbsp;
                                                                    Depart
                                                                </label>
                                                                <br>
                                                                <input type="text" class="form-control font700 " id="" name="dateDeparture[]"  value="{{$element['dateDeparture']}}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-1 px-2 position-relative text-center cursor ">
                                                            <div class="px-3 py-4 depart">
                                                                <i class="fa fa-trash " aria-hidden="true"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                    </section>
                                    <div class="pt-4 text-right">
                                        <button type="button" class="mr-auto cancelBtn" id="cancelBtn">Cancel</button>
                                    </div>
                                    </form>
                                </div>
                               
                                @foreach($airlineDeatails as $airlineData)
                                <div class="fliter_box_inner px-4 mb-4 mt-4">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="position-relative pb-5">
                                            <img src="data:image/png;base64,{{ $getAirlineDetails[$airlineData['airlineName']] ['airlineLogo'] }}" alt="" class="airlineImg">
                                            </div>
                                            <div class="row mx-auto">
                                                <div class="float-start auto">
                                                    <div>
                                                        <span class="text-secondary">From</span> <br>
                                                        <label class="font18 mb-0">{{$flightNameArray[$airlineData['fromlocation']]}}</label> <br>
                                                        <span class="text-secondary">{{ \Carbon\Carbon::createFromFormat('H:i', $airlineData['depatureTime'])->format('h:i A') }} - {{ \Carbon\Carbon::parse($airlineData['deptDate'])->format('D, d M') }}</span>
                                                    </div>
                                                </div>
                                                <div class="float-start px-3 middle mx-auto w-50 position-relative">
                                                    <div class="borderLine"></div>
                                                    <div class="swapArrow"></div>
                                                    <div class="text-center pt-1">
                                                        <b class="">{{$airlineData['travelDuration']}}</b><br>
                                                        <span class="font12 text-secondary">{{$airlineData['stopDetails']}}</span>
                                                    </div>
                                                </div>
                                                <div class="float-end auto">
                                                    <div>
                                                        <span class="text-secondary">From</span> <br>
                                                        <label class="font18 mb-0">{{$flightNameArray[$airlineData['tolocation']]}}</label> <br>
                                                        <span class="text-secondary">{{ \Carbon\Carbon::createFromFormat('H:i', $airlineData['arrivalTime'])->format('h:i A') }} - {{ \Carbon\Carbon::parse(Request::get('departureDates'))->format('D, d M') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row mx-auto">
                                                <div class="float-start w-50 ml-auto">
                                                    <label class="pricing">
                                                        <!-- <img src="images/inr.png" alt="Amount" class="inrIcon"> -->
                                                        &#8377;
                                                        @if($airlineData['offerPrice'] <= 0)
                                                        {{$airlineData['originalPrice']}}
                                                        @else
                                                        {{$airlineData['offerPrice']}}  <strike  class="text-secondary font16">({{'&#8377;'.$airlineData['originalPrice']}})</strike ><span  class="text-secondary font16">(Per Person)</span>
                                                        @endif
                                                    </label>
                                                </div>
                                                <div class="float-end w-50 text-right">
                                                    <!-- <button type="submit" class="greenBtn font700" onClick="redirectionURL('checkout.php')">BOOK NOW</button> -->
                                                    <!-- <a onclick="$('.fliter_box_inner').css('background','#fff');$(this).closest('.fliter_box_inner').css('background','#f0f0f0'); return confirm('Proceed to booking?');" href="{{route('view-flight',['id'=>'1'])}}?{{explode('?',Request::getRequestUri())[1]}}" class="btn filter_btn greenBtn">BOOK NOW</a> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
							</div>
						</div>
					</div>
				</section>


				<section class="py-5 StepsOuter">
                    <div class="container">
                        <div class="row">
                            <div class="wizard">
                                <div class="wizard-inner">
                                    <div class="connecting-line"></div>
                                    <ul class="nav nav-pills nav-wizard" id="tabs">
                                        <li class="">
                                            <a href="#step-1" class="active show" id="step1" data-toggle="tab"><span>1</span>Passenger Details</a>

                                        </li>
                                        <li class="disabled">
                                            <a href="#step-2" data-toggle="tab" id="step2"><span>2</span>GST Information</a>
                                        </li>
                                        <li class="disabled">
                                            <a href="#step-3" data-toggle="tab" id="step3"><span>3</span>Add Baggage Details</a>
                                        </li>
                                        <li class="disabled">
                                            <a href="#step-4" data-toggle="tab" id="step4"><span>4</span>Add Itinerary Details</a>
                                        </li>
                                        <li class="disabled">
                                            <a href="#step-5" data-toggle="tab" id="step5"><span>5</span>Selected Seats</a>
                                        </li>
                                    </ul>
                                    <form method="POST" action="{{route('book-flight')}}" class="checkout-form" id="passengerform" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="tripId" value="{{$createTrip['tripDetailsId']}}">
                                    <input type="hidden" name="tripDetailsId" value="{{$createTripDetails['tripDetailsId']}}">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="step-1">
                                            <div class="row explore-hotels mt-5">
                                                <div class="col-md-12">
                                                    <div class="">
                                                            <div class="">
                                                                <!-- Step 1 -->
                                                                <div class="form-in filters-results">
                                                                    <div class="row px-3">
                                                                        <div class="inout-control col-md-12 radio-button pb-3">
                                                                            <b class="font16">Passenger Details: 1</b>
                                                                        </div>
                                                                        <div class="inout-control col-md-4">
                                                                            <div class="row mx-auto form-group">
                                                                                <div class="form-check col">
                                                                                    <input class="form-check-input cRadioBtn form-control mr_type" value="Mr" type="radio" name="mr_type[0]" id="mr_0"  style="margin-top: -5px;">
                                                                                    <label class="form-check-label" for="mr_0">
                                                                                        Mr.
                                                                                    </label>
                                                                                </div>
                                                                                <div class="form-check col">
                                                                                    <input class="form-check-input cRadioBtn form-control mr_type" value="Mrs" type="radio" name="mr_type[0]" id="mrs_0"  style="margin-top: -5px;">
                                                                                    <label class="form-check-label" for="mrs_0">
                                                                                        Mrs.
                                                                                    </label>
                                                                                </div>
                                                                                <div class="form-check col">
                                                                                    <input class="form-check-input cRadioBtn form-control mr_type" value="Miss" type="radio" name="mr_type[0]" id="miss_0"  style="margin-top: -5px;">
                                                                                    <label class="form-check-label" for="miss_0">
                                                                                        Miss.
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md-4">
                                                                            <div class="input-group form-group mb-3">
                                                                                <input type="text" class="form-control firstName" id="firstName_0" name="firstName[0]" placeholder="First Name" aria-label="Text input with dropdown button">
                                                                            </div>
                                                                            <div class="ackMsg"></div>
                                                                        </div>
                                                                        <div class="inout-control col-md-4">
                                                                            <div class="input-group form-group mb-3">
                                                                                <input class="form-control lastName" type="text" placeholder="Last Name" id ="lastName_0" name="lastName[0]">
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control  col-md-4">
                                                                            <div class=" row mb-3 pt-2 mx-auto">
                                                                                <div class="from-check col form-group">

                                                                                    <input type="radio" class="form-check-input cRadioBtn form-control gender" id="male_0" name="gender[0]" value="male" style="margin-top: -5px;">
                                                                                    <label for="male_0" class="form-check-label">Male</label>
                                                                                </div>
                                                                                <div class="from-check col form-group">
                                                                                    <input type="radio" class="form-check-input cRadioBtn form-control gender" id="female_0" name="gender[0]" value="female" style="margin-top: -5px;">
                                                                                    <label for="female_0" class="form-check-label">Female</label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md-4">
                                                                            <div class="input-group form-group mb-3">
                                                                                <input type="text" class="form-control font700 dobClass dob" id="dob_0" name="dob[0]"  value="" placeholder="DOB">
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md-4">
                                                                            <div class="imageUpload form-group">
                                                                                <input type="file" class="form-control profile" name="profile[0]" style="opacity:0;" id="">
                                                                                <label class="text-center text-light" id="fileName_0">Upload File</label>
                                                                            </div>
                                                                        </div>

                                                                        <div class="inout-control col-md-4">
                                                                            <div class="input-group form-group mb-3">
                                                                                <input class="form-control mobileNumber" type="text" name="mobileNumber[0]" id="mobileNumber_0" placeholder="Mobile No."  maxlength="10">
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md-4">
                                                                            <div class="input-group form-group mb-3">
                                                                                <input class="form-control passengerEmail" type="email" name="passengerEmail[0]" id="passengerEmail_0" placeholder="Email ID">
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control  col-md-4">
                                                                            <div class="input-group form-group mb-3">
                                                                                <input class="form-control passport" type="text" name="passport[0]" id="passport_0" placeholder="Passport" maxlength="10">
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md-4">
                                                                            <div class="input-group form-group mb-3">
                                                                                <input class="form-control visa" type="text" placeholder="VISA" name="visa[0]" id="visa_0" maxlength="7">
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md-4">
                                                                            <div class="input-group form-group mb-3">
                                                                                <!-- <input class="form-control" type="text" placeholder="Valid Date"> -->
                                                                                <input type="text" class="form-control font700 validClassDate validDate" id="valid_date_0" name="validDate[0]"  value="" placeholder="Valid Date">
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md-4">
                                                                            <div class="input-group form-group mb-3">
                                                                                <input class="form-control occupation" type="text" placeholder="Occupation" name="occupation[0]" id="occupation_0">
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md-4">
                                                                            <div class="input-group form-group mb-3">
                                                                                <input class="form-control graduation" type="text" placeholder="Graduation" name="graduation[0]" id="graduation_0">
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md-4">
                                                                            <div class="input-group form-group mb-3">
                                                                                <input class="form-control passed_out" type="text" placeholder="Passed Out" name="passed_out[0]" id="passed_out_0">
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md-12">
                                                                            <div class="input-group form-group mb-3">
                                                                                <input class="form-control address" type="text" placeholder="Address" name="address[0]" id="address_0">
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md-4">
                                                                            <div class="input-group form-group mb-3">
                                                                                <select class="form-control city_name" name="city_name[0]" id="city_0">
                                                                                    <option value="">Please select a state first</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md-4">
                                                                            <div class="input-group form-group mb-3">
                                                                                <select class="form-control state_name" name="state_name[0]" id="state_0">
                                                                                    <option value="">Please select a country first</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md-4">
                                                                            <div class="input-group form-group mb-3">
                                                                                <input class="form-control pin_number" type="text" placeholder="Pin" name="pin_number[0]" id="pincode_0" maxlength="6">
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md-4">
                                                                            <div class="input-group form-group mb-3">
                                                                                <select class="form-control country_name" name="country_name[0]" id="country_0">
                                                                                    <option value="">Select Country</option>
                                                                                    @foreach($countryList['data'] as $country_List)
                                                                                    <option value="{{$country_List->id}}">{{$country_List->name}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="inout-control col-md-4 " >
                                                                            <div class="input-group form-group mb-3 ">
                                                                                <select class="form-control nriCountry d-none" name="nriCountry[0]" id="nriCountry_0">
                                                                                    <option value="">Select Country</option>
                                                                                    @foreach($countryList['data'] as $country_List)
                                                                                    <option value="{{$country_List->id}}">{{$country_List->name}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="inout-control col-md-8">
                                                                            <div class="mb-3 pt-2">
                                                                                <label class="w-auto">
                                                                                    <input type="checkbox" class="nriClass" id="nri_0"  name="nri[0]" id="nri_0" value="nri">
                                                                                    <label for="nri_0">NRI</label>
                                                                                </label>
                                                                                <label class="w-auto">
                                                                                    Preffered Class:
                                                                                    &nbsp;
                                                                                    <input type="radio" id="Economy_0" name="preffere_class[0]" value="Economy" checked>
                                                                                    <label for="Economy_0">Economy</label>
                                                                                    &nbsp;
                                                                                    <input type="radio" id="Business_0" name="preffere_class[0]" value="Business">
                                                                                    <label for="Business_0">Business</label>
                                                                                    &nbsp;
                                                                                    <input type="radio" id="Premium_0" name="preffere_class[0]" value="Premium">
                                                                                    <label for="Premium_0">Premium</label>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md-12">
                                                                            <div class="mb-3 pt-2">
                                                                                <label class="w-auto">
                                                                                    Special Class:
                                                                                    &nbsp;
                                                                                    <input type="checkbox" id="Student_0" name="special_class[0][]" value="Student">
                                                                                    <label for="Student_0">Student Fare</label>
                                                                                    &nbsp;
                                                                                    <input type="checkbox" id="Armed_0" name="special_class[0][]" value="Armed">
                                                                                    <label for="Armed_0">Armed Fare</label>
                                                                                    &nbsp;
                                                                                    <input type="checkbox" id="Senior_0" name="special_class[0][]" value="Senior">
                                                                                    <label for="Senior_0">Senior Fare</label>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md-4">
                                                                            <div class="input-group form-group mb-3">

                                                                                <select class="form-control selectpicker airline_name" name="airline_name[0]"  id= "airline_0"  multiple>
                                                                                    <option value="">Membership Airline</option>
                                                                                    @foreach($getAirlinesName as $getAirlines)
                                                                                        <option value="{{$getAirlines}}">{{$getAirlines}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                                <!-- <section class="col-lg pb-2 customMSelect">
                                                                                    <div class="multiCustom-select">
                                                                                        <select id="multi-select" multiple>
                                                                                            <option value="1">Mumbai</option>
                                                                                            <option value="2">Delhi</option>
                                                                                            <option value="3">Bangalore</option>
                                                                                            <option value="4">Hyderabad</option>
                                                                                            <option value="5">Ahmedabad</option>
                                                                                            <option value="6">Chennai</option>
                                                                                            <option value="7">Surat</option>
                                                                                            <option value="8">Jodhpur</option>
                                                                                            <option value="9">Jaipur</option>
                                                                                            <option value="10">Indore</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </section> -->


                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md-4">
                                                                            <div class="input-group form-group  mb-3">
                                                                                <input class="form-control membership_id" type="text" placeholder="Membership ID" name="membership_id[0]" id="membership_id_0" maxlength="10" >
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="addMultipleRows">

                                                                    </div>
                                                                    <div class="px-3 mt-3 checkout-next mx-auto">
                                                                        <button type="button" class="btn filter_btn addPassangerBtn" id='add_row'>Add Passanger</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row px-3 mt-3 checkout-next">
                                                                <button type="button" id="step1next" class="stepbtn btn filter_btn">Next</button>
                                                            </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="step-2">
                                            <div class="row explore-hotels mt-5">
                                                <div class="col-md-12">
                                                    <div class="">
                                                        <!-- Step 2 -->
                                                        <div class="form-in filters-results">
                                                            <div>
                                                                <div class="inout-control col-md-12 mt-2">
                                                                    <input type="Checkbox" id="gst" name="gst">
                                                                    <label for="gst">Enter GST Details (Optional)</label>
                                                                </div>
                                                                <div id="gstrow" class="d-none w-100 row px-3" style="">
                                                                    <div class="inout-control col-md-12 radio-button pb-3">
                                                                        <b class="font16">GST Number (Optional)</b>
                                                                    </div>
                                                                    <div class="inout-control col-md-4">
                                                                        <div class="input-group form-group mb-3">
                                                                            <input class="form-control" type="text" placeholder="GST Number" id="gst_registration" name="registration" maxlength="100">
                                                                            <!-- <input class="form-control" type="text" placeholder="Enter Registration No. *" > -->
                                                                        </div>
                                                                    </div>
                                                                    <div class="inout-control col-md-4">
                                                                        <div class="input-group form-group mb-3">
                                                                            <input class="form-control" type="text" placeholder="GST Name" name="company_name" id="company_name" maxlength="100">

                                                                        </div>
                                                                    </div>
                                                                    <div class="inout-control col-md-4">
                                                                        <div class="input-group form-group mb-3">
                                                                            <input class="form-control" type="text" placeholder="GST Address" name="company_address" id="company_address" maxlength="250">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="row px-3">
                                                                    <div class="inout-control col-md-12 radio-button pb-3">
                                                                        <b class="font16">Insure Trip</b>
                                                                    </div>

                                                                    <div class="inout-control col-md-12">
                                                                        <div class="mb-3 pt-2">
                                                                            <input type="radio" id="yes" name="insure_type" value="yes">
                                                                            <label for="yes">Yes</label>

                                                                            <input type="radio" id="no" name="insure_type" checked value="no">
                                                                            <label for="no">No</label>
                                                                        </div>
                                                                    </div>
                                                                    <div id="InsuremainDiv" class="d-none col-lg-10">
                                                                        <div class="inout-control">
                                                                            @foreach($getInsurances as $getInsurancesDetails)
                                                                            <a class="btn filter_btn m-0 mb-2 font14 draggable" data-id="{{$getInsurancesDetails['insuranceId']}}" data-passenger="{{$getInsurancesDetails['insuranceName']}}">{{$getInsurancesDetails['insuranceName']}}</a>
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="inout-control col-md-8">
                                                                            <div class="dragDrop">
                                                                                <p>Drop here</p>
                                                                            </div>
                                                                            <div class="row" style="padding-left:35px;">
                                                                                <div class="form-group float-start w-50">
                                                                                    <input class="form-control" type="hidden" placeholder="" name="insurance" id="InsureId" value="">
                                                                                </div>
                                                                                <div class="float-end w-50">
                                                                                    <button type="button" class="btn filter_btn px-3 py-2 mt-2 mb-2 font14 cancel-btn">Cancel</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="row px-3">
                                                                    <div class="inout-control col-md-12 radio-button pb-3">
                                                                        <b class="font16">Coupon Code</b>
                                                                    </div>
                                                                    <div class="inout-control col-md-4">
                                                                        <div class="">
                                                                            @foreach($getPromotions as $getPromotionsDetails)
                                                                            <button type="button" class="btn coupon filter_btn m-0 mb-2 font14" style="width:120px;" id="{{$getPromotionsDetails['promoId']}}">{{$getPromotionsDetails['promoName']}}</button>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                    <div class="inout-control col-md-4">
                                                                        <div class="row">
                                                                            <div class="float-start">
                                                                                <label class="pt-4">Selected Coupon</label>
                                                                            </div>
                                                                            <div class="float-end">
                                                                                <!-- <button type="button" id="selectedCoupon" class="btn coupon  filter_btn m-0 mb-2 font14" style="width:189px;">no coupon applied</button> -->
                                                                                <input type="text" name="selected_coupon" id="selectedCoupon" value="No coupon applied" class="inputBtn font14 text-light">
                                                                                <input type="hidden" name="selectedCouponID" id="selectedCouponID"  class="inputBtn font14 text-light">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- <div class="inout-control col-md-8">
                                                                        <div class="dragDrop"></div>
                                                                        <button type="button" class="btn filter_btn px-3 py-2 mt-2 mb-2 font14 ">Cancel</button>
                                                                    </div> -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row px-3 mt-3 checkout-next">
                                                            <button type="button" id="step2next" class="stepbtn btn filter_btn">Next</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="step-3">
                                            <div class="row explore-hotels mt-5">
                                                <div class="col-md-12">
                                                    <div class="">
                                                        <!-- Step 3 -->
                                                        <div class="form-in filters-results">
                                                            <div>
                                                                <div class="row px-3">
                                                                    <div class="inout-control col-md-12 radio-button pb-3">
                                                                        <b class="font16">Add Baggage Details</b>
                                                                    </div>
                                                                    <div class="inout-control col-md-4">
                                                                        <div class="input-group form-group mb-3">
                                                                            <input class="form-control" type="text" id="numberbagsId" name="numberbags" placeholder="No. of Bags">
                                                                        </div>
                                                                    </div>
                                                                    <div class="inout-control col-md-4">
                                                                        <div class="input-group form-group mb-3">
                                                                            <input class="form-control" type="text" id="totalweightId" name="totalweight" placeholder="Total Weight">
                                                                        </div>
                                                                    </div>
                                                                    <div class="inout-control col-md-4"></div>
                                                                    <div class="inout-control col-md-8 text-center">
                                                                        <button type="button" id="confirmbag" class="btn filter_btn px-4 py-2 mt-2 mb-2 font14" style="display: inline;">Confirm</button>
                                                                        <button type="button" id="cancelbag" class="btn filter_btn px-4 py-2 mt-2 mb-2 font14" style="display: inline;">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="pBookHotel">
                                                                <div class="row px-3">
                                                                    <div class="inout-control col-md-12 radio-button pb-3">
                                                                        <b class="font16">Book Hotel</b>
                                                                    </div>
                                                                    <!-- <div class="inout-control col-md-4">
                                                                        <div class="input-group form-group mb-3">
                                                                            <select class="form-control">
                                                                                <option value="">Select State</option>
                                                                                <option value="1">Rajasthan</option>
                                                                                <option value="2">Madhya Pradesh</option>
                                                                                <option value="3">Maharashtra</option>
                                                                                <option value="4">Punjab</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="inout-control col-md-4">
                                                                        <div class="input-group form-group mb-3">
                                                                            <select class="form-control">
                                                                                <option value="">Select City</option>
                                                                                <option value="1">Jodhpur</option>
                                                                                <option value="2">Jaipur</option>
                                                                                <option value="3">Udaipur</option>
                                                                                <option value="4">Jaisalmer</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="inout-control col-md-4">
                                                                        <div class="input-group form-group mb-3">
                                                                            <input class="form-control" type="text" placeholder="Hotel Name">
                                                                        </div>
                                                                    </div>
                                                                    <div class="inout-control col-md-4">
                                                                        <div class="input-group form-group mb-3">
                                                                            <input class="form-control" type="text" placeholder="No. of rooms">
                                                                        </div>
                                                                    </div>
                                                                    <div class="inout-control col-md-4">
                                                                        <div class="input-group form-group mb-3">
                                                                            <input class="form-control" type="text" placeholder="Guest (Adult)">
                                                                        </div>
                                                                    </div>
                                                                    <div class="inout-control col-md-4">
                                                                        <div class="input-group form-group mb-3">
                                                                            <input class="form-control" type="text" placeholder="Guest (Child)">
                                                                        </div>
                                                                    </div>
                                                                    <div class="inout-control col-md-4">
                                                                        <div class="input-group form-group mb-3">
                                                                            <select class="form-control">
                                                                                <option value="">Select Room Type</option>
                                                                                <option value="1">Single Room</option>
                                                                                <option value="2">Deluxe Room</option>
                                                                                <option value="3">Double Room</option>
                                                                                <option value="4">Studio</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="inout-control col-md-4">
                                                                        <div class="input-group form-group mb-3">
                                                                            <input class="form-control" id="departureDate" type="date" placeholder=""  name="" value="Check In">
                                                                        </div>
                                                                    </div>
                                                                    <div class="inout-control col-md-4">
                                                                        <div class="input-group form-group mb-3">
                                                                            <input class="form-control" id="departureDate" type="text" placeholder=""  name="" value="">
                                                                        </div>
                                                                    </div> -->
                                                                    <!-- <div class="inout-control col-md-8">
                                                                        <div class="dragDrop"></div>
                                                                        <button type="button" class="btn filter_btn px-3 py-2 mt-2 mb-2 font14 ">Cancel</button>
                                                                    </div> -->
                                                                </div>
                                                                <div class="row px-3">
                                                                    <div class="inout-control col-md-4 mb-4 position-relative">
                                                                        <div class="input-group form-group mb-2" style="height:46px;">
                                                                            <span class="input-group-addon"><i class="fa fa-map-marker mr-3 location" aria-hidden="true"></i></span>
                                                                            <select name="state" id="state" class="form-control selectpicker border-0 hotelfieldChange">
                                                                                <option value="">Select State *</option>
                                                                                @foreach($states as $state)
                                                                                <option value="{{$state}}">{{$state}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="inout-control col-md-4 mb-4 position-relative">
                                                                        <div class="input-group form-group mb-2" style="height:46px;">
                                                                            <span class="input-group-addon"><i class="fa fa-map-marker mr-3 location" aria-hidden="true"></i></span>
                                                                            <select name="city" id="city" class="form-control selectpicker select2 hotelfieldChange">
                                                                                <option value="">Select City *</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="inout-control col-md-4 mb-4 position-relative">
                                                                        <div class="input-group form-group room-height mb-2" style="height:46px;">
                                                                            <span class="input-group-addon"><i class="fa fa-bed bed" aria-hidden="true"></i></span>
                                                                            <select name="room_type[]" id="room_type" class="form-control selectpicker select2 hotelfieldChange" multiple="multiple" placeholder="Select Room Type" data-placeholder="Select Room Type">
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
                                                                            <input class="form-control from hotelfieldChange" type="text" name="check_in" placeholder="Check-In --/--/-- *" readonly />
                                                                        </div>
                                                                    </div>
                                                                    <div class="inout-control col-md-3">
                                                                        <div class="input-group form-group mb-2 date" data-date-format="mm-dd-yyyy">
                                                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar calendar"></i></span>
                                                                            <input class="form-control to hotelfieldChange" type="text" name="check_out" placeholder="Check-Out --/--/-- *" readonly />
                                                                        </div>
                                                                    </div>
                                                                    <div class="inout-control col-md-2">
                                                                        <div class="input-group form-group mb-2">
                                                                            <span class="input-group-addon"><i class="fa fa-bed gate" aria-hidden="true"></i></span>
                                                                            <select name="no_rooms" id="no_rooms" class="form-control selectpicker hotelfieldChange">
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
                                                                            <select name="no_adults" id="no_adults" class="form-control selectpicker hotelfieldChange">
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
                                                                <div class="inout-control col-md-12 text-center">
                                                                    <button type="button" id="hotelconfirm" class="btn filter_btn px-4 py-2 mt-2 mb-2 font14" style="display: inline;" disabled>Confirm</button>
                                                                </div>
                                                                <div class="inout-control col-md-4 mb-4 position-relative  hotelDiv d-none">
                                                                    <div class="input-group form-group mb-2" style="height:46px;">
                                                                        <span class="input-group-addon"><i class="fa fa-bed bed" aria-hidden="true"></i></span>
                                                                        <select class="hotel_name" name="hotel_name" id="hotel_name" class="form-control  border-0">
                                                                            <option value="">Select Hotel Name *</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row px-3 mt-3 checkout-next">
                                                            <button type="button" id="step3next" class="stepbtn btn filter_btn" disabled>Next</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                          <!-- Step 4 -->
                                        <div class="tab-pane" id="step-4">
                                            <div class="row explore-hotels mt-5">
                                                    <div class="col-md-12">
                                                        <div class="">
                                                            <div class="form-in filters-results">
                                                                <div class=" px-3">
                                                                    <div class="inout-control col-md-12 radio-button pb-3">
                                                                        <b class="font16">Add Itinerary Details</b>
                                                                    </div>
                                                                   
                                                                    <div class="row itineraryfirstRow">
                                                                        <div class="inout-control col-md">
                                                                            <div class="input-group form-group mb-3">
                                                                                <select class="form-control itinerary_name" name="itinerary_name[0]"  id="itinerary_name_0">
                                                                                    <option value="">Select Itinerary</option>
                                                                                    @foreach($getItinerary as $getItineraryDetails)
                                                                                    <option value="{{$getItineraryDetails['itineraryName']}}" data-price="{{$getItineraryDetails['itineraryPrice']}}">{{$getItineraryDetails['itineraryName']}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md">
                                                                            <div class="input-group form-group mb-3">
                                                                                <input class="form-control price_itinerary" type="text" name="price_itinerary[0]" id="price_itinerary_0" placeholder="Price" readonly>
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md">
                                                                            <div class="input-group form-group mb-3">
                                                                                <input class="form-control number_ticket" type="text" placeholder="No. of Tickets" name="number_ticket[0]" id="number_ticket_0">
                                                                            </div>
                                                                        </div>
                                                                        <div class="inout-control col-md">
                                                                            <div class="input-group form-group mb-3">
                                                                                <input class="form-control total_price" type="text" placeholder="Total Price" name="total_price[0]" id="total_price" readonly>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-1 px-2 position-relative text-center rmvfirstItinerary cursor removeIconOuter float-right">
                                                                            <div class="px-3 py-3 depart" style="width: 55px;">
                                                                                <i class="fa fa-trash " aria-hidden="true"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="addItineraryMultipleRow" >
                                                                </div>
                                                                <div class="col-lg-3 position-relative float-right px-1 ml-auto">
                                                                    <div class="h-auto depart">
                                                                        <button type="button" class="greenTransparentBtn mr-auto w-100" id='add_itinerary_row'>Add</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <section class="mealOuter">
                                                                <table class="table table-striped table-hover table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">Meals</th>
                                                                            <th scope="col">Passanger 1</th>
                                                                            <th scope="col">Passanger 2</th>
                                                                            <th scope="col">Passanger 3</th>
                                                                            <th scope="col">Passanger 4</th>
                                                                            <th scope="col">Passanger 5</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                <ol type="1" class="pl-3">
                                                                                    <li>Espresso [Coffee Shot 60 Ml]</li>
                                                                                    <li>Cafe Moch</li>
                                                                                    <li>Hot Nutella</li>
                                                                                    <li>Brownie Shrappe</li>
                                                                                    <li>Veg Grilled Sandwich</li>
                                                                                    <li>Corn Cheese Grilled Sandwich</li>
                                                                                    <li>Tandoori Paneer Burger</li>
                                                                                    <li>Veggie Fingers</li>
                                                                                    <li>Cheesy Fries</li>
                                                                                </ol>
                                                                            </td>
                                                                            <td class="align-middle">
                                                                                <div>
                                                                                    <ul class="pl-3">
                                                                                        <li>Cafe Moch</li>
                                                                                        <li>Veg Grilled Sandwich</li>
                                                                                    </ul>
                                                                                </div>
                                                                            </td>
                                                                            <td class="align-middle">
                                                                                <ul class="pl-3">
                                                                                    <li>Veggie Fingers</li>
                                                                                    <li>Espresso [Coffee Shot 60 Ml]</li>
                                                                                </ul>
                                                                            </td>
                                                                            <td class="align-middle">
                                                                                <ul class="pl-3">
                                                                                    
                                                                                </ul>
                                                                            </td>
                                                                            <td class="align-middle">
                                                                                <ul class="pl-3">
                                                                                    <li>Brownie Shrappe</li>
                                                                                    <li>Cheesy Fries</li>
                                                                                </ul>
                                                                            </td>
                                                                            <td class="align-middle">
                                                                                <ul class="pl-3">
                                                                                    
                                                                                </ul>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </section>
                                                            <div class="row px-3 mt-3 checkout-next">
                                                                <button type="button" id="step4next" class="stepbtn btn filter_btn">Next</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                            

                                        </div>
                                            <!-- Step 5 -->
                                        <div class="tab-pane" id="step-5">
                                            <div class="row explore-hotels mt-5">
                                                    <div class="col-md-12">
                                                        <div class="">
                                                            <div class="form-in filters-results">
                                                                <div class="row mx-auto">
                                                                    <div class="SelectPlaneSeat col-lg-5">
                                                                        <div class="selected-seats text-start px-4 py-3 font16">
                                                                            Selected Seats:
                                                                            <span class="font14" id="selected-seats-display"></span>
                                                                        </div>
                                                                        <div class="p-4 fixHeight" id="cScrollBar">
                                                                            <div class="planeFront">
                                                                                <img src="images/plane-front.png" alt="">
                                                                            </div>
                                                                            <div class="planeDesign p-4">
                                                                                <?php for ($c = 1; $c <= 20; $c++) {?>
                                                                                <div class="row mx-auto">
                                                                                    <div class="float-start w-50 airplane">
                                                                                        <label class="seat"><?=$c;?>A</label>
                                                                                        <label class="seat"><?=$c;?>B</label>
                                                                                        <label class="seat"><?=$c;?>C</label>
                                                                                    </div>
                                                                                    <div class="float-end w-50 airplane text-right">
                                                                                        <label class="seat"><?=$c;?>D</label>
                                                                                        <label class="seat"><?=$c;?>E</label>
                                                                                        <label class="seat"><?=$c;?>F</label>
                                                                                    </div>
                                                                                </div>
                                                                                <?php }?>
                                                                            </div>
                                                                            <div class="planeBack">
                                                                                <img src="images/plane-back.png" alt="">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row px-3 mt-3 checkout-next">
                                                                <!-- <button type="submit" class="btn filter_btn">Next</button> -->
                                                                <button type="submit" id="submitBtn" class="btn filter_btn">Submit</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
			    </section>
			</div>
		</section>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="{{URL::asset('/js/jquery.validate.min.js')}}"></script>
    <script src="{{URL::asset('/js/jquery.validate-init.js')}}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script type="text/javascript">
    function nextTab(elem) {
		    $(elem).next().find('a[data-toggle="tab"]').click();
	}
    function prevTab(elem) {
        $(elem).prev().find('a[data-toggle="tab"]').click();
    }
    $(document).ready(function () {
        $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {

        var $target = $(e.target);

        if ($target.parent().hasClass('disabled')) {
            return false;
        }
        });

        $(".next-step").click(function (e) {

        var $active = $('.wizard .nav-tabs li.active');
        $active.next().removeClass('disabled');
        nextTab($active);

        });
        $(".prev-step").click(function (e) {

        var $active = $('.wizard .nav-tabs li.active');
        prevTab($active);

        });

        $('#tabs').tabs();


        $(".treavelClass input[type='radio']").prop("readonly", true);
        var trip_type = {!! json_encode(Request::get('trip_type')) !!};
        var totelPassenger = {!! json_encode( Request::get('passenger')) !!};


        if(trip_type == 'roundTrip'){
            $('#returnId').removeClass('dNone');
        //    $('.returnDate').removeAttr('readonly');
            $('.blockReturn').removeClass('blockReturn');
            $('#trip_type').val(trip_type);
        }else{
            if(trip_type == 'multiTrip'){
                $('#multiTripSection').removeClass('d-none');
                $('#returnId').addClass('dNone');
                $('#trip_type').val(trip_type);
            }else{
                $('#trip_type').val(trip_type);
            }
        }

        $("#cancelBtn").on('click', function (e) {
            window.location.href = "{{route('flight')}}";
        });

        $("body").on("click",".rmvpassenger",function(){
			$(this).closest(".removeClassmultiple").remove();
		});

        $.validator.addMethod(
            "nameValidation",
            function (value, element) {
                return this.optional(element) || /^[a-zA-Z]{2,}$/.test(value);
            },
            "Enter valid characters with a minimum of 2 characters (only alphabets allowed)"
        );

        $.validator.addMethod(
            "upperCaseAndNumber",
            function (value, element) {
                return this.optional(element) || /^[A-Z0-9]{10}$/.test(value);
            },
            "Enter exactly 10 characters, only uppercase letters and numbers allowed"
        );
        $.validator.addMethod(
            "startsWithV",
            function (value, element) {
                return this.optional(element) || /^V[A-Z0-9]{6}$/.test(value);
            },
            "Enter exactly 7 characters, starting with the letter 'V', followed by uppercase letters and numbers"
        );
        $.validator.addMethod(
            "alphabetsUnderscores",
            function (value, element) {
                return this.optional(element) || /^[a-zA-Z0-9_-]{10}$/.test(value);
            },
            "Enter exactly 10 characters, only alphabets, underscores, hyphens, and numbers allowed"
        );

        $.validator.addMethod(
            "validDateFormat",
            function (value, element) {
                return this.optional(element) || /^\d{1,2}-\d{1,2}-\d{4}$/.test(value);
            },
            "Enter a valid date in the format DD-MM-YYYY"
        );

        $.validator.addMethod('maxsize', function(value, element, param) {
        return this.optional(element) || (element.files[0].size <= param);
        }, 'File size must be below {0}');

        $.validator.addMethod('filesize', function(value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
        }, 'File size must be less than {0} bytes');

        var validator = $(".checkout-form").validate({
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
				// 'firstName[0]':{
				// 	required:true,
                //     nameValidation:true,
				// },
                // 'mr_type[0]':{
				// 	required:!0
				// },
                // 'lastName[0]':{
				// 	required:true,
                //     nameValidation:true,
				// },
                // 'gender[0]':{
				// 	required:!0
				// },
                // 'dob[0]':{
                //     required: true,
                //     validDateFormat: true,
				// },
                // 'profile[0]':{
                //     filesize:5242880,
				// },
                // 'mobileNumber[0]':{
                //     required:true,
                //     digits:true,
                //     minlength: 10,
				// },
                // 'passengerEmail[0]':{
				// 	required:true,
                //     email:true,
				// },
                // 'passport[0]':{
				// 	required:true,
                //     upperCaseAndNumber:true,
				// },
                // 'visa[0]':{
				// 	required:true,
                //     startsWithV:true,
				// },
                // 'validDate[0]':{
				// 	required:!0
				// },
                // 'occupation[0]':{
				// 	required:!0
				// },
                // 'graduation[0]':{
				// 	required:!0
				// },
                // 'passed_out[0]':{
				// 	required:!0
				// },
                // 'address[0]':{
				// 	required:true,
                //     minlength: 3,
				// },
                // 'city_name[0]':{
				// 	required:!0
				// },
                // 'state_name[0]':{
				// 	required:!0
				// },
                // 'pin_number[0]':{
                //     required: true,
                //     digits: true,
                //     minlength: 6,
                //     maxlength: 6,
				// },
                // 'country_name[0]':{
				// 	required:!0
				// },
                // 'airline_name[0]':{
				// 	required:!0
				// },
                // 'membership_id[0]':{
				// 	required: true,
                //     alphabetsUnderscores: true,
				// },

			},
			messages: {
				'firstName[0]':{
					required: "Please Enter the First Name",
                    nameValidation: "Enter valid characters with a minimum of 2 characters (only alphabets allowed)",
				},
                'mr_type[0]':{
					required: "Please select a valid title (Mr, Mrs, or Miss)",
				},
                'lastName[0]':{
					required: "Please Enter the First Name",
                    nameValidation: "Enter valid characters with a minimum of 2 characters (only alphabets allowed)",
				},
                'gender[0]':{
					required: "Please select the gender",
				},
                'dob[0]':{
					required: "Please Enter the dob",
                    validDateFormat: "Enter a valid date in the format DD-MM-YYYY",
				},
                'profile[0]':{
                    filesize: "image size must be less than 5 MB",
				},
                'mobileNumber[0]':{
                    required: "Please Enter the Phone Number",
                    digits:"Contact Number is invalid",
                    minlength:"Please enter at least 10 characters",
				},
                'passengerEmail[0]':{
					required: "Please Enter the Email",
                    email:"Email ID is invalid",
				},
                'passport[0]':{
					required: "Please Enter the Passport Number",
                    upperCaseAndNumber:"Enter exactly 10 characters, only uppercase letters and numbers allowed",
				},
                'visa[0]':{
					required: "Please Enter the VISA",
                    startsWithV:"Enter exactly 7 characters, starting with the letter 'V', followed by uppercase letters and numbers",
				},
                'validDate[0]':{
					required: "Please Enter the valid Date",
				},
                'occupation[0]':{
					required: "Please Enter the Occupation",
				},
                'graduation[0]':{
					required: "Please Enter the graduation",
				},
                'passed_out[0]':{
					required: "Please Enter the passed out",
				},
                'address[0]':{
					required: "Please Enter the Address",
                    minlength:"Please enter at least 3 characters",
				},
                'city_name[0]':{
					required: "Please Select the City",
				},
                'state_name[0]':{
					required: "Please Select the State",
				},
                'pin_number[0]':{
					required: "Please enter a 6-digit number",
                    digits: "Please enter only digits",
                    minlength: "Please enter exactly 6 digits",
                    maxlength: "Please enter exactly 6 digits",
				},
                'country_name[0]':{
					required: "Please Select the Country",
				},
                'airline_name[0]':{
					required: "Please Select the Airline Name",
				},
                'membership_id[0]':{
					required: "Please enter a 6-digit number",
                    alphabetsUnderscores: "Enter exactly 10 characters, only alphabets, underscores, hyphens, and numbers allowed",
				},
                registration:{
	            	required:"Please provide your GST Registration No."
	            },
	            company_name:{
	            	required:"Please provide your registered Company Name"
	            },
	            company_address:{
	            	required:"Please provide your registered Company Address"
	            },
                insurance:{
                    required:"Please provide valid insurance"
                },
                numberbags:{
                    required:"Please Enter the Number of Bag",
                    digits: "Please enter only digits."
                },
                totalweight:{
                    required:"Please Enter the Total Weight",
                    digits: "Please enter only digits."
                },
                state:{
					required: "Please Select  State",
				},
                city:{
					required: "Please select city",
				},
                check_in:{
					required: "Please select Check-in date",
				},
                check_out:{
					required: "Please select Check-out date",
				},
                room_type:{
					required: "Please select room Type",
				},
                no_rooms:{
					required: "Please select no. of rooms",
				},
                no_adults:{
					required: "Please select no. of adults",
				},
                hotel_name:{
                    required:"Please Select Hotel Name",
                },
                'itinerary_name[0]':{
					required: "Please Select the itinerary Name",
				},
                // 'price_itinerary[0]':{
				// 	required: "Please Enter the Price",
				// },
                'number_ticket[0]':{
					required: "Please Enter the Number of ticket",
                    digits: "Please enter only digits."
				},
                // 'total_price[0]':{
				// 	required: "Please Enter the totel Price",
				// },

			}
		})

      
        var i = 1;

		$("#add_row").click(function(){
            var firstName = $('input[name^=firstName]').map(function(idx, elem) {
                    return $(elem).val();
                }).get();
                console.log(firstName.length);
            if(firstName.length < totelPassenger){
                // alert(firstName.length)
                // alert(totelPassenger);
                if($('.checkout-form').valid()){
                    var row = '<div class="row px-3 removeClassmultiple">' +
                                '<div class="inout-control col-md-12 radio-button pb-3">' +
                                '<b class="font16">Passenger Details : ' + (i + 1) + '</b>' +
                                    // '<b class="font16">Passenger Details : '+i+1+'</b>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="row form-group">' +
                                        '<div class="form-check col">' +
                                            '<input class="form-check-input cRadioBtn form-control mr_type" value="Mr" type="radio" name="mr_type['+i+']" id="mr_'+i+'"  style="margin-top: -5px;">' +
                                            '<label class="form-check-label" for="mr_'+i+'">' +
                                                'Mr.' +
                                            '</label>' +
                                        '</div>' +
                                        '<div class="form-check col">' +
                                            '<input class="form-check-input cRadioBtn form-control mr_type" value="Mrs" type="radio" name="mr_type['+i+']" id="mrs_'+i+'"  style="margin-top: -5px;">' +
                                            '<label class="form-check-label" for="mrs_'+i+'">' +
                                                'Mrs.' +
                                            '</label>' +
                                        '</div>' +
                                        '<div class="form-check col">' +
                                            '<input class="form-check-input cRadioBtn form-control mr_type" value="Miss" type="radio" name="mr_type['+i+']" id="miss_'+i+'"  style="margin-top: -5px;">' +
                                            '<label class="form-check-label" for="miss_'+i+'">' +
                                                'Miss.' +
                                            '</label>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<input type="text" class="form-control firstName" id="firstName_'+i+'" name="firstName['+i+']" placeholder="First Name" aria-label="Text input with dropdown button">' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<input class="form-control lastName" type="text" placeholder="Last Name" id ="lastName_'+i+'" name="lastName['+i+']">' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="row mb-3 pt-2 mx-auto">' +
                                        '<div class="from-check col form-group">' +
                                            '<input type="radio" class="form-check-input cRadioBtn form-control gender" id="male_'+i+'" name="gender['+i+']" value="male" style="margin-top: -5px;">' +
                                            '<label for="male_'+i+'" class="form-check-label" >Male</label>' +
                                        '</div>' +

                                        '<div class="from-check col form-group">' +
                                            '<input type="radio" class="form-check-input cRadioBtn form-control gender" id="female_'+i+'" name="gender['+i+']" value="female" style="margin-top: -5px;">' +
                                            '<label for="female_'+i+'" class="form-check-label">Female</label>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<input type="text" class="form-control font700 dobClass dob" id="" name="dob['+i+']"  value="" placeholder="DOB">' +
                                '</div>' +


                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="imageUpload form-group ">' +
                                        '<input type="file" class="form-control profile" name="profile['+i+']" style="opacity:0;" id="profile_'+i+'">' +
                                        '<label class="text-center text-light" id="fileName_'+i+'">Upload File</label>' +
                                    '</div>' +
                                '</div>' +

                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<input class="form-control mobileNumber" type="text" name="mobileNumber['+i+']" id="mobileNumber_'+i+'" placeholder="Mobile No."  maxlength="10">' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<input class="form-control passengerEmail" type="email" name="passengerEmail['+i+']" id="passengerEmail_'+i+'" placeholder="Email ID">' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<input class="form-control passport" type="text" name="passport['+i+']" id="passport_'+i+'" placeholder="Passport" maxlength="10">' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<input class="form-control visa" type="text" placeholder="VISA" name="visa['+i+']" id="visa_'+i+'" maxlength="7">' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<input type="text" class="form-control font700 validClassDate validDate" id="" name="validDate['+i+']"  value="" placeholder="Valid Date">' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<input class="form-control occupation" type="text" placeholder="Occupation" name="occupation['+i+']" id="occupation_'+i+'">' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<input class="form-control graduation" type="text" placeholder="Graduation" name="graduation['+i+']" id="graduation_'+i+'">' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<input class="form-control passed_out" type="text" placeholder="Passed Out" name="passed_out['+i+']" id="passed_out_'+i+'">' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-12">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<input class="form-control address" type="text" placeholder="Address" name="address['+i+']" id="address_'+i+'">' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<select class="form-control city_name" name="city_name['+i+']" id="city_'+i+'">' +
                                            '<option value="">Please select a state first</option>'+
                                        '</select>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<select class="form-control state_name" name="state_name['+i+']" id="state_'+i+'">' +
                                            '<option value="">Please select a country first</option>'+
                                        '</select>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<input class="form-control pin_number" type="text" placeholder="Pin" name="pin_number['+i+']" id="pincode_'+i+'" maxlength="6">'+
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<select class="form-control country_name" name="country_name['+i+']" id="country_'+i+'">' +
                                            '<option value="">Select Country</option>' +
                                            '@foreach($countryList['data'] as $country_List)' +
                                                '<option value="{{$country_List->id}}">{{$country_List->name}}</option>' +
                                            '@endforeach' +
                                        '</select>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<select class="form-control nriCountry d-none" name="nriCountry['+i+']" id="nriCountry_'+i+'">' +
                                            '<option value="">Select Country</option>' +
                                            '@foreach($countryList['data'] as $country_List)' +
                                                '<option value="{{$country_List->id}}">{{$country_List->name}}</option>' +
                                            '@endforeach' +
                                        '</select>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-8">' +
                                    '<div class="mb-3 pt-2">' +
                                        '<label class="w-auto">' +
                                            '<input type="checkbox"  class="nriClass" name="nri['+i+']" id="nri_'+i+'" value="nri">' +
                                            '<label for="nri_'+i+'">NRI</label>' +
                                        '</label>' +
                                        '<label class="w-auto">' +
                                            'Preffered Class:' +
                                            '&nbsp;' +
                                            '<input type="radio" id="Economy_'+i+'" name="preffere_class['+i+']" value="Economy" checked>' +
                                            '<label for="Economy_'+i+'">Economy</label>' +
                                            '&nbsp;' +
                                            '<input type="radio" id="Business_'+i+'" name="preffere_class['+i+']" value="Business">' +
                                            '<label for="Business_'+i+'">Business</label>' +
                                            '&nbsp;' +
                                            '<input type="radio" id="Premium_'+i+'" name="preffere_class['+i+']" value="Premium">' +
                                            '<label for="Premium_'+i+'">Premium</label>' +
                                        '</label>' +
                                    '</div>	' +
                                '</div>' +
                                '<div class="inout-control col-md-12">' +
                                    '<div class="mb-3 pt-2">' +
                                        '<label class="w-auto">' +
                                            'Special Class:' +
                                            '&nbsp;' +
                                            '<input type="checkbox" id="Student_'+i+'" name="special_class['+i+'][]" value="Student">' +
                                            '<label for="Student_'+i+'">Student Fare</label>' +
                                            '&nbsp;' +
                                            '<input type="checkbox" id="Armed_'+i+'" name="special_class['+i+'][]" value="Armed">' +
                                            '<label for="Armed_'+i+'">Armed Fare</label>' +
                                            '&nbsp;' +
                                            '<input type="checkbox" id="Senior_'+i+'" name="special_class['+i+'][]" value="Senior">' +
                                            '<label for="Senior_'+i+'">Senior Fare</label>' +
                                        '</label>' +
                                    '</div>	' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<select class="form-control airline_name" name="airline_name['+i+']" multiple id="airline_'+i+'">' +
                                            '<option value="">Membership Airline</option>' +
                                            '@foreach($getAirlinesName as $getAirlines)' +
                                            '<option value="{{$getAirlines}}">{{$getAirlines}}</option>' +
                                            '@endforeach' +
                                        '</select>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="inout-control col-md-4">' +
                                    '<div class="input-group form-group mb-3">' +
                                        '<input class="form-control membership_id" type="text" placeholder="Membership ID" name="membership_id['+i+']" id="membership_id_'+i+'" maxlength="10">' +
                                    '</div>' +
                                '</div>' +
                                '<div class="px-3 mt-3 checkout-next mx-auto rmvpassenger">' +
                                    '<button type="button" class="mr-auto cancelBtn" id="cancelBtn">Cancel</button>' +
                                '</div>' +
                            '</div>';

                    $(".addMultipleRows").append(row);
                    // initializeValidation();
                    i++;

                } 

            }


		});
        function checkValidity(className) {
            var flags = true;
            $("." + className).each(function () {
                if (!$(this).valid()) {
                    flags = false;
                    return false;
                }
            });
        }
        $("#passengerform").delegate("#add_row", "click", function () {
            checkValidity("firstName");
            checkValidity("lastName");
            checkValidity("membership_id");
            checkValidity("pin_number");
            checkValidity("address");
            checkValidity("visa");
            checkValidity("passport");
            checkValidity("mobileNumber");
            checkValidity("mr_type");
            checkValidity("passengerEmail");
            checkValidity("gender");
            checkValidity("dob");
            checkValidity("profile");
            checkValidity("validDate");
            checkValidity("occupation");
            checkValidity("graduation");
            checkValidity("passed_out");
            checkValidity("city_name");
            checkValidity("state_name");
            checkValidity("country_name");
            checkValidity("airline_name");


        });

        //country ajax
        $(".StepsOuter").delegate(".country_name", "change", function(){

            var selectedCountryId = $(this).val();

            var countryId =$(this).attr("id");

                $.ajax({
                    url:"{{route('state-list')}}",
                    method: 'GET',
                    data: {
                        country_id: selectedCountryId
                    },
                    success: function (response) {
                        var currentCountrySelect = countryId.replace('country_', '');
                       if(response){

                        updateStateDropdown(response, currentCountrySelect);

                        var cityDropdown = $('#city_'+ currentCountrySelect +'');
                        cityDropdown.empty();
                        cityDropdown.append('<option value="">Please select a state first</option>');
                       }else{

                        var stateDropdown = $('#state_'+ currentCountrySelect +'');
                        stateDropdown.empty();
                        stateDropdown.append('<option value="">Please select a country first</option>');

                        var cityDropdown = $('#city_'+ currentCountrySelect +'');
                        cityDropdown.empty();
                        cityDropdown.append('<option value="">Please select a state first</option>');
                       }

                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
        });

        function updateStateDropdown(states, currentCountrySelect) {

            var stateDropdown = $('#state_'+ currentCountrySelect +'');
            stateDropdown.empty();
            stateDropdown.append('<option value="">Select State</option>');

            $.each(states, function (index, state) {
                stateDropdown.append('<option value="' + state.id + '">' + state.name + '</option>');
            });
        }

        // state Ajax
        $(".StepsOuter").delegate(".state_name", "change", function(){

           var selectedStateId = $(this).val();
           var stateId =$(this).attr("id");

               $.ajax({
                   url:"{{route('city-list')}}",
                   method: 'GET',
                   data: {
                       state_id: selectedStateId
                   },
                   success: function (response) {

                    var currentStateSelect = stateId.replace('state_', '');
                       if(response){
                        updateCityDropdown(response, currentStateSelect);
                       }else{

                        var cityDropdown = $('#city_'+ currentStateSelect +'');
                        cityDropdown.empty();
                        cityDropdown.append('<option value="">Please select a state first</option>');
                       }

                   },
                   error: function (error) {
                       console.error(error);
                   }
               });

       });

        function updateCityDropdown(cities,currentStateSelect) {

            var cityDropdown = $('#city_'+ currentStateSelect +'');
            cityDropdown.empty();
            cityDropdown.append('<option value="">Select City</option>');

            $.each(cities, function (index, city) {
                cityDropdown.append('<option value="' + city.id + '">' + city.name + '</option>');
            });
        }

        $(".StepsOuter").delegate(".nriClass", "click", function () {
        // $('.nriClass').click(function(){
            if($(this).is(":checked")){
                
                var nriId =$( this ).attr( "id" );
                var currentId = nriId.replace('nri_', '');
                $('#nriCountry_' + currentId).closest('div').removeClass('d-none');
                $('#nriCountry_'+currentId+'').removeClass('d-none');
                
                    $(".nriCountry").each(function(){
                        $(this).rules("add", {
                            required: true,
                            messages: {
                            required: "Please Select the Permanent Country",
                            }
                        });
			        });
            }else{
                var nriId =$( this ).attr( "id" );
                var currentId = nriId.replace('nri_', '');
                $('#nriCountry_'+currentId+'').rules('remove','required');
                $('#nriCountry_'+currentId+'').valid();
                 $('#nriCountry_'+currentId+'').val();
                $('#nriCountry_' + currentId).closest('div').addClass('d-none');
                
            }
        });

        $('#step1next, #add_row').on('click', function (e) {

            $(".firstName").each(function(){
					$(this).rules("add", {
						required:true,
                        nameValidation:true,
						messages: {
						required: "Please Enter the First Name",
                        nameValidation: "Enter valid characters with a minimum of 2 characters (only alphabets allowed)",
					}
					});
			});
            $(".lastName").each(function(){
					$(this).rules("add", {
						required:true,
                        nameValidation:true,
						messages: {
						required: "Please Enter the Last Name",
                        nameValidation: "Enter valid characters with a minimum of 2 characters (only alphabets allowed)",
					}
					});
			});
            $(".mobileNumber").each(function(){
					$(this).rules("add", {
						required:true,
                        digits:true,
                        minlength: 10,
						messages: {
						required: "Please Enter the Phone Number",
                        digits:"Contact Number is invalid",
                        minlength:"Please enter at least 10 characters",

					}
					});
			});
            $(".passengerEmail").each(function(){
					$(this).rules("add", {
						required:true,
                        email:true,
						messages: {
						required: "Please Enter the Email",
                        email:"Email ID is invalid",
					}
					});
			});
            $(".passport").each(function(){
					$(this).rules("add", {
						required:true,
                        upperCaseAndNumber:true,
						messages: {
						required: "Please Enter the Passport Number",
                        upperCaseAndNumber:"Enter exactly 10 characters, only uppercase letters and numbers allowed",
					}
					});
			});
            $(".visa").each(function(){
					$(this).rules("add", {
						required:true,
                        startsWithV:true,
						messages: {
						required: "Please Enter the VISA",
                        startsWithV:"Enter exactly 7 characters, starting with the letter 'V', followed by uppercase letters and numbers",
					}
					});
			});
            $(".address").each(function(){
					$(this).rules("add", {
						required:true,
                        minlength: 3,
						messages: {
						required: "Please Enter the Address",
                        minlength:"Please enter at least 3 characters",
					}
					});
			});
            $(".pin_number").each(function(){
					$(this).rules("add", {
                        required: true,
                        digits: true,
                        minlength: 6,
                        maxlength: 6,
						messages: {
                        required: "Please enter a 6-digit number",
                        digits: "Please enter only digits",
                        minlength: "Please enter exactly 6 digits",
                        maxlength: "Please enter exactly 6 digits",
					}
					});
			});
            $(".membership_id").each(function(){
					$(this).rules("add", {
                        required: true,
                        alphabetsUnderscores: true,
						messages: {
                        required: "Please enter a 6-digit number",
                        alphabetsUnderscores: "Enter exactly 10 characters, only alphabets, underscores, hyphens, and numbers allowed",

					}
					});
			});
            $(".profile").each(function(){
					$(this).rules("add", {
                        // extension: "png|jpg|jpeg",
                        filesize: 5242880,
						messages: {
                            // extension: "Please upload a file with a valid extension (png, jpg, jpeg)",
                            filesize: "image size must be less than 5 MB",
					}
					});
			});
            $(".mr_type").each(function(){
					$(this).rules("add", {
                        required: true,
						messages: {
                        required: "Please select a valid title (Mr, Mrs, or Miss)",
					}
					});
			});
            $(".gender").each(function(){
					$(this).rules("add", {
                        required: true,
						messages: {
                        required: "Please select the gender",
					}
					});
			});
            $(".dob").each(function(){
					$(this).rules("add", {
                        required: true,
                        validDateFormat: true,
						messages: {
                        required: "Please Enter the dob",
                        validDateFormat: "Enter a valid date in the format DD-MM-YYYY",
					}
					});
			});
            $(".validDate").each(function(){
					$(this).rules("add", {
                        required: true,
						messages: {
                        required: "Please Enter the valid Date",
					}
					});
			});
            $(".occupation").each(function(){
					$(this).rules("add", {
                        required: true,
						messages: {
                        required: "Please Enter the Occupation",
					}
					});
			});
            $(".graduation").each(function(){
					$(this).rules("add", {
                        required: true,
						messages: {
                        required: "Please Enter the graduation",
					}
					});
			});
            $(".passed_out").each(function(){
					$(this).rules("add", {
                        required: true,
						messages: {
                        required: "Please Enter the passed out",
					}
					});
			});
            $(".city_name").each(function(){
					$(this).rules("add", {
                        required: true,
						messages: {
                        required: "Please Select the city",
					}
					});
			});
            $(".state_name").each(function(){
					$(this).rules("add", {
                        required: true,
						messages: {
                        required: "Please Select the State",
					}
					});
			});
            $(".country_name").each(function(){
					$(this).rules("add", {
                        required: true,
						messages: {
                        required: "Please Select the Country",
					}
					});
			});
            $(".airline_name").each(function(){
					$(this).rules("add", {
                        required: true,
						messages: {
                        required: "Please Select the Airline Name",
					}
					});
			});

            var flagsstep_1 = true;
            if($('.checkout-form').valid()){
                flagsstep_1 = false;
            }
        });

        $('#step1next').on('click', function (e) {

            var firstName = $('input[name^=firstName]').map(function(idx, elem) {
                    return $(elem).val();
                }).get();
            var tripDetailsId = $('input[name=tripDetailsId]').val();
            if(firstName.length == totelPassenger){
                if($('.checkout-form').valid()){
                    
                    var firstName = $('input[name^=firstName]').map(function(idx, elem) {
                        return $(elem).val();
                    }).get();
                
                    var mr_type = $('input[name^=mr_type]:checked').map(function(idx, elem) {
                        return $(elem).val();
                    }).get();
                    var lastName = $('input[name^=lastName]').map(function(idx, elem) {
                        return $(elem).val();
                    }).get();
                    var gender = $('input[name^=gender]:checked').map(function(idx, elem) {
                        return $(elem).val();
                    }).get();
                    var dob = $('input[name^=dob]').map(function(idx, elem) {
                        return $(elem).val();
                    }).get();
                    var mobileNumber = $('input[name^=mobileNumber]').map(function(idx, elem) {
                        return $(elem).val();
                    }).get();
                    var passengerEmail = $('input[name^=passengerEmail]').map(function(idx, elem) {
                        return $(elem).val();
                    }).get();
                    var passport = $('input[name^=passport]').map(function(idx, elem) {
                        return $(elem).val();
                    }).get();
                    var visa = $('input[name^=visa]').map(function(idx, elem) {
                        return $(elem).val();
                    }).get();
                    var validDate = $('input[name^=validDate]').map(function(idx, elem) {
                        return $(elem).val();
                    }).get();
                    var graduation = $('input[name^=graduation]').map(function(idx, elem) {
                        return $(elem).val();
                    }).get();
                    var occupation = $('input[name^=occupation]').map(function(idx, elem) {
                        return $(elem).val();
                    }).get();
                    
                    var passed_out = $('input[name^=passed_out]').map(function(idx, elem) {
                        return $(elem).val();
                    }).get();
                    var address = $('input[name^=address]').map(function(idx, elem) {
                        return $(elem).val();
                    }).get();
                    var city_name = $('select[name^=city_name]').map(function(idx, elem) {
                       
                        return $(elem).find('option:selected').text();
                    }).get();
                    var state_name = $('select[name^=state_name]').map(function(idx, elem) {
                       
                        return $(elem).find('option:selected').text();
                    }).get();
                    var pin_number = $('input[name^=pin_number]').map(function(idx, elem) {
                        return $(elem).val();
                    }).get();
                    var country_name = $('select[name^=country_name]').map(function(idx, elem) {
                        
                        return $(elem).find('option:selected').text();
                    }).get();
                   
                    var nriCountry = $('select[name^=nriCountry]').map(function(idx, elem) {
                        return $(elem).find('option:selected').text();
                    }).get();



                    var membership_id = $('input[name^=membership_id]').map(function(idx, elem) {
                        return $(elem).val();
                    }).get();
                    
                    var preffere_class = $('input[name^=preffere_class]:checked').map(function(idx, elem) {
                        return $(elem).val();
                    }).get();
                    
                    var specialClassesArray = [];
                    var nriArray = [];
                    var airlineNameArray = [];
                    var profileArray  =[];
                    for(var q=0 ;q<=firstName.length-1;q++){
                        // var special_class = $('input[name="special_class[' + q + ']"]').val();
                        // var special_class = $('input[name="special_class[' + q + ']"]:checked').val();
                        // alert(special_class);
                        // pecial_class = special_class || null;
                        // specialClassesArray.push(special_class);

                        var nri = $('input[name="nri[' + q + ']"]:checked').val();
                        if(nri == undefined){
                            nriArray.push('');
                        }else{
                            nriArray.push(nri);
                        }
                        
                        var specialClass =  $('input[name="special_class[' + q + '][]"]:checked').map(function(_, el) {
                            return $(el).val();
                        }).get();
                      
                        if(specialClass == ''){
                            specialClassesArray.push('');
                        }else{
                            specialClassesArray.push(specialClass);
                        }

                        // var specialClass =  $('input[name="special_class[' + q + '][]"]:checked').map(function(_, el) {
                        //     return $(el).val();
                        // }).get();
                        var airline_name =  $('select[name="airline_name[' + q + ']"]').val();
                        // alert(airline_name);
                        airlineNameArray.push(airline_name);
                        // console.log(airline_name);
                        // var profile = $('input[name^=profile])[' + q + '].files[0]);
                        var profile = $('input[name="profile[' + q + ']"]')[0].files[0];
                        if(profile == undefined){
                            profileArray.push(null);
                        }else{
                            profileArray.push(profile);
                        }
                        // console.log(profile);
                        // profileArray
                        // profileArray.push(airline_name);
                        // alert(profile);

                    }

                    // var formData = new FormData();
                    // formData.append('firstName',firstName);
                    // formData.append('mr_type',mr_type);
                    // formData.append('lastName',lastName);
                    // formData.append('gender',gender);
                    // formData.append('dob',dob);
                    // formData.append('mobileNumber',mobileNumber);
                    // formData.append('passengerEmail',passengerEmail);
                    // formData.append('passport',passport);
                    // formData.append('visa',visa);
                    // formData.append('validDate',validDate);
                    // formData.append('graduation',graduation);
                    // formData.append('passed_out',passed_out);
                    // formData.append('address',address);
                    // formData.append('city_name',city_name);
                    // formData.append('state_name',state_name);
                    // formData.append('pin_number',pin_number);
                    // formData.append('country_name',country_name);
                    // formData.append('nri',nriArray);
                    // formData.append('airline_name',airlineNameArray);
                    // formData.append('membership_id',membership_id);
                    // formData.append('profile',profileArray);
                    // formData.append('preffere_class',preffere_class);
                    // formData.append('special_class',specialClassesArray);
                    var formDataArray = [];
                    for (var q = 0; q < firstName.length; q++) {
                        var formData = new FormData();
                        formData.append('firstName', firstName[q]);
                        formData.append('mr_type', mr_type[q]);
                        formData.append('lastName', lastName[q]);
                        formData.append('gender', gender[q]);
                        formData.append('dob', dob[q]);
                        formData.append('mobileNumber', mobileNumber[q]);
                        formData.append('passengerEmail', passengerEmail[q]);
                        formData.append('passport',passport[q]);
                        formData.append('visa',visa[q]);
                        formData.append('validDate',validDate[q]);
                        formData.append('graduation',graduation[q]);
                        formData.append('passed_out',passed_out[q]);
                        formData.append('address',address[q]);
                        formData.append('city_name',city_name[q]);
                        formData.append('state_name',state_name[q]);
                        formData.append('pin_number',pin_number[q]);
                        formData.append('country_name',country_name[q]);
                        formData.append('nri',nriArray[q]);
                        formData.append('airline_name',airlineNameArray[q]);
                        formData.append('membership_id',membership_id[q]);
                        formData.append('profile',profileArray[q]);
                        formData.append('preffere_class',preffere_class[q]);
                        formData.append('special_class',specialClassesArray[q]);
                        formData.append('occupation',occupation[q]);
                        formData.append('nriCountry',nriCountry[q]);
                        formData.append('tripDetailsId',tripDetailsId);
                        
                        
                        formDataArray.push(formData);
                    }

                    console.log(formDataArray);


                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    for (var i = 0; i < formDataArray.length; i++) {
                        $.ajax({
                            type: 'POST',
                            url: "{{ route('step1next') }}",
                            data: formDataArray[i],
                            dataType: "json",
                            contentType: false,
                            processData: false,
                            success: function (data) {
                                console.log(data);
                            },
                            error: function (response) {
                                alert(response);
                            }
                        });
                    }
                    // $.ajax({
                    //     type: 'POST',
                    //     url:"{{route('step1next')}}",
                    //     data: JSON.stringify(formData),
                    //     dataType: "json",
                    //     // data: {
                    //     //     firstName: firstName,
                    //     //     mr_type: mr_type,
                    //     //     lastName: lastName,
                    //     //     gender: gender,
                    //     //     dob: dob,
                    //     //     mobileNumber: mobileNumber,
                    //     //     passengerEmail: passengerEmail,
                    //     //     passport: passport,
                    //     //     visa: visa,
                    //     //     validDate: validDate,
                    //     //     graduation: graduation,
                    //     //     passed_out: passed_out,
                    //     //     address: address,
                    //     //     city_name: city_name,
                    //     //     state_name: state_name,
                    //     //     pin_number: pin_number,
                    //     //     country_name: country_name,
                    //     //     nri: nriArray,
                    //     //     airline_name: airlineNameArray,
                    //     //     membership_id: membership_id,
                    //     //     profile:profile,
                    //     //     preffere_class:preffere_class,
                    //     //     special_class:specialClassesArray,
                    //     // },
                    //     contentType: false,
                    //     processData: false,
                    //     success:function(data){
                    //         console.log(data);
                    //         // alert('abcd');
                    //         // $('#flightlist').html(data);
                    //     },
                    //     error: function(response) {
                    //         alert(response);
                    //     }
                    // })
                }
            }
           
        });
        
       
        $('#step2next').on('click', function (e) {
            if($('.checkout-form').valid()){
                
                var tripId = $('input[name=tripId]').val();
                var tripDetailsId = $('input[name=tripDetailsId]').val();
                var gst = $('input[name="gst"]:checked').val();
                if(gst){
                    var registration = $("input[name=registration]").val();
                    var company_name = $("input[name=company_name]").val();
                    var company_address = $("input[name=company_address]").val();
                }else{
                    var registration = '';
                    var company_name ='';
                    var company_address ='';
                }

                var insure_type = $('input[name="insure_type"]:checked').val();
                if(insure_type == 'yes'){
                    var insurance = $("input[name=insurance]").val();
                }else{
                    var insurance = '';
                }
               
                var selected_coupon = $("input[name=selected_coupon]").val();
                var selectedCouponID = $("input[name=selectedCouponID]").val()
                
                $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url:"{{route('step2next')}}",
                    data: {
                        registration: registration,
                        company_name: company_name,
                        company_address: company_address,
                        insurance:insurance,
                        // selected_coupon:selected_coupon,
                        selectedCouponID:selectedCouponID,
                        tripId:tripId,
                        tripDetailsId:tripDetailsId,
                    },
                    success:function(data){
                        console.log(data);
                        
                        // $('#flightlist').html(data);
                    },
                    error: function(response) {
                        alert(response);
                    }
                })
            }
        });

        var j=1;
		$("#add_itinerary_row").click(function(){
            // $('[name="price_itinerary[0]"]').rules('add','required');
            $('[name="itinerary_name[0]"').rules('add','required');
            $('[name="number_ticket[0]"]').rules('add','required');
            // $('[name="total_price[0]"]').rules('add','required');
            $('[name="number_ticket[0]"]').rules('add','digits');

            $("#passengerform").delegate("#add_itinerary_row", "click", function () {

                    function checkValidity(className) {
                        var flags = true;
                        $("." + className).each(function () {
                            if (!$(this).valid()) {
                                flags = false;
                                return false;
                            }
                        });
                    }
                    checkValidity("itinerary_name");
                    checkValidity("price_itinerary");
                    checkValidity("number_ticket");
                    // checkValidity("total_price");

                });
                if($('.checkout-form').valid()){

                var row ='<div class="row px-3 removeItineraryClassmultiple">' +
                    '<div class="inout-control col-md">' +
                        '<div class="input-group form-group mb-3">' +
                            '<select class="form-control itinerary_name" name="itinerary_name['+j+']"  id="itinerary_name_'+j+'">' +
                                '<option value="">Select Itinerary</option>' +
                                '@foreach($getItinerary as $getItineraryDetails) ' +
                                '<option value="{{$getItineraryDetails['itineraryName']}}" data-price="{{$getItineraryDetails['itineraryPrice']}}">{{$getItineraryDetails['itineraryName']}}</option>' +
                                '@endforeach ' +
                            '</select>' +
                        '</div>' +
                    '</div>' +
                    '<div class="inout-control col-md">' +
                        '<div class="input-group form-group mb-3">' +
                            '<input class="form-control price_itinerary" type="text" name="price_itinerary['+j+']" id="price_itinerary_'+j+'" placeholder="Price" readonly>' +
                        '</div>' +
                    '</div>' +
                    '<div class="inout-control col-md">' +
                        '<div class="input-group form-group mb-3">' +
                            '<input class="form-control number_ticket" type="text" placeholder="No. of Tickets" name="number_ticket['+j+']" id="number_ticket_'+j+'">' +
                        '</div>' +
                    '</div>' +
                    '<div class="inout-control col-md">' +
                        '<div class="input-group form-group mb-3">' +
                            '<input class="form-control total_price" type="text" placeholder="Total Price" name="total_price['+j+']" id="total_price_'+j+'" readonly>' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-lg-1 px-2 position-relative text-center rmvItinerary cursor removeIconOuter float-right">' +
                            '<div class="px-3 py-3 depart" style="width:55px;">' +
                                '<i class="fa fa-trash " aria-hidden="true"></i>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                // '</div>';
                $(".addItineraryMultipleRow").append(row);
                j++;
                }
        });
        $("body").on("click",".rmvItinerary",function(){
			$(this).closest(".removeItineraryClassmultiple").remove();
		});
        $("body").on("click",".rmvfirstItinerary",function(){
			$(this).closest(".itineraryfirstRow").remove();
		});

        $('#confirmbag').on('click', function (e) {
        // $('#step3next').on('click', function (e) {
            $('[name=numberbags]').rules('add','required');
            $('[name=numberbags]').rules('add','digits');
            $('[name=totalweight]').rules('add','required');
            $('[name=totalweight]').rules('add','digits');

                var flagsstep_1 = true;
            if($('.checkout-form').valid()){
                flagsstep_1 = false;
            }
            if($('.checkout-form').valid()){
                var bagsweight = prompt("Please enter the Bags total weight");
                if (bagsweight != null) {
                    var totalweightId =$('#totalweightId').val();
                    if(bagsweight == totalweightId){

                        $("#confirmbag").attr("disabled", true);
                        $("#hotelconfirm").removeAttr("disabled");
                        // $("#step3next").removeAttr("disabled");
                        $("#numberbagsId").attr("disabled", true);
                        $("#totalweightId").attr("disabled", true);
                    }else{
                        alert('Please Enter the same weight');
                    }
                }
            }
        });
        $('#cancelbag').on('click',function(e){
            $('#numberbagsId').val('');
            $('#totalweightId').val('');

            $("#step3next").attr("disabled", true);
            $("#hotelconfirm").attr("disabled", true);
            $("#numberbagsId").removeAttr("disabled");
            $("#confirmbag").removeAttr("disabled");
            $("#totalweightId").removeAttr("disabled");

            $('[name=state]').rules('remove','required');
            $('[name=city]').rules('remove','required');
            $('[name=hotel_name]').rules('remove','required');
            $('[name=check_in]').rules('remove','required');
            $('[name=room_type]').rules('remove','required');
            
            $('[name=check_out]').rules('remove','required');
            $('[name=no_rooms]').rules('remove','required');
            $('[name=no_adults]').rules('remove','required');
            $('.hotelDiv').addClass('d-none');
            validator.resetForm();

        });
       
        $(".hotelfieldChange").change(function(){
            $('.hotelDiv').addClass('d-none');
            $('select[name="hotel_name"]').val('');
            $("#step3next").attr("disabled", true);
            $('[name=hotel_name]').rules('remove','required');

        });

        $('#hotelconfirm').on('click', function (e) {
            // alert(bagsweight);
            $('[name=state]').rules('add','required');
            $('[name=city]').rules('add','required');
            // $('[name=hotel_name]').rules('add','required');
            $('[name=check_in]').rules('add','required');
            $('[name=check_out]').rules('add','required');
            $('[name=room_type]').rules('add','required');
            
            $('[name=no_rooms]').rules('add','required');
            $('[name=no_adults]').rules('add','required');

            // var flagsstep_1 = true;

            if($('.checkout-form').valid()){
                // flagsstep_1 = false;
                $("#step3next").removeAttr("disabled");
                alert('Please confirm your hotel details');

                var state = $('select[name="state"]').val();
                var city = $('select[name="city"]').val();
                var check_in = $("input[name=check_in]").val();
                var check_out = $("input[name=check_out]").val();
                var room_type = $('#room_type').val();

                $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url:"{{route('hotel-name')}}",
                    data: {
                        // numberbags: numberbags,
                        // totalweight: totalweight,
                        state: state,
                        city: city,
                        check_in: check_in,
                        check_out: check_out,
                        // no_child: no_child,
                        // no_rooms: no_rooms,
                        // no_adults: no_adults,
                        room_type: room_type,
                        // tripDetailsId:tripDetailsId,
                    },
                    success:function(data){
                        // console.log(data);
                        // alert('abcd');
                        // $('#flightlist').html(data);
                        $('.hotelDiv').removeClass('d-none');

                        var hotelName = $('.hotel_name');
                        hotelName.empty();
                        hotelName.append('<option value="">Select Hotel Name</option>');

                        $.each(data, function (index, details) {
                            hotelName.append('<option value="' + details.id + '">' + details.name + ' ' + details.type + '</option>');
                        });
                    },
                    error: function(response) {
                        alert(response);
                    }
                })


            }
        });
        
        $('#step3next').on('click', function (e) {
            // alert(bagsweight);
            $('[name=hotel_name]').rules('add','required');
            if($('.checkout-form').valid()){

                var tripDetailsId = $('input[name=tripDetailsId]').val();
                var numberbags = $("input[name=numberbags]").val();
                var totalweight = $("input[name=totalweight]").val();
                // alert(numberbags);
                // alert(totalweight);

                // var state = $('select[name="state"]').val();
                // var city = $('select[name="city"]').val();
                var check_in = $("input[name=check_in]").val();
                var check_out = $("input[name=check_out]").val();
                var no_child = $("input[name=no_child]").val();
                
                // var room_type = $('select[name="room_type"]').val();
                var no_rooms = $('select[name="no_rooms"]').val();
                var no_adults = $('select[name="no_adults"]').val();
                var room_type = $('#room_type').val();
                var hotel_name = $('select[name="hotel_name"]').val();
                // alert(room_type);
                // console.log(room_type);


                $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url:"{{route('step3next')}}",
                    data: {
                        numberbags: numberbags,
                        totalweight: totalweight,
                        // state: state,
                        // city: city,
                        check_in: check_in,
                        check_out: check_out,
                        no_child: no_child,
                        no_rooms: no_rooms,
                        no_adults: no_adults,
                        room_type: room_type,
                        tripDetailsId:tripDetailsId,
                        hotel_name:hotel_name,
                    },
                    success:function(data){
                        console.log(data);
                        // alert('abcd');
                        // $('#flightlist').html(data);
                    },
                    error: function(response) {
                        alert(response);
                    }
                })
            }
        });


        $('#step4next,#add_itinerary_row').on('click', function (e) {

            $('[name="itinerary_name[0]"').rules('add','required');
            // $('[name="price_itinerary[0]"]').rules('add','required');
            $('[name="number_ticket[0]"]').rules('add','required');
            $('[name="number_ticket[0]"]').rules('add','digits');
            // $('[name="total_price[0]"]').rules('add','required');

            // $(".price_itinerary").each(function(){
			// 		$(this).rules("add", {
			// 			required:true,
			// 			messages: {
			// 			required: "Please Enter the Price",
			// 		}
			// 		});
			// });
            $(".itinerary_name").each(function(){
					$(this).rules("add", {
						required:true,
						messages: {
						required: "Please Select the itinerary Name",
					}
					});
			});
            $(".number_ticket").each(function(){
					$(this).rules("add", {
						required:true,
                        digits:true,
						messages: {
						required: "Please Enter the Number of ticket",
                        digits: "Please enter only digits."
					}
					});
			});
            // $(".total_price").each(function(){
			// 		$(this).rules("add", {
			// 			required:true,
			// 			messages: {
			// 			required: "Please Enter the totel Price",
			// 		}
			// 		});
			// });

            var flagsstep_1 = true;
            if($('.checkout-form').valid()){
                flagsstep_1 = false;

            }
        });

        $(".StepsOuter").delegate(".itinerary_name", "change", function(){
            // var selectedCountryId = $(this).val();
            var itineraryId =$(this).attr("id");
            var selectedOption = $(this).find(':selected');
            var selectedValue = selectedOption.val();
            var selectedPrice = selectedOption.data('price');
            // alert(itineraryId);
            // itinerary_name_0
            var currentId = itineraryId.replace('itinerary_name_', '');
            // price_itinerary_0
            // alert('#price_itinerary_'+currentId+'');
            var price_itinerary = $('#price_itinerary_'+currentId+'').val(selectedPrice);
            // alert(price_itinerary);
            // console.log(price_itinerary);
            // alert(itineraryId);
           
          
             var number_ticket = $('#number_ticket_'+currentId+'').val();
            if(number_ticket){
                // alert(price_itinerary);
                // alert(number_ticket);
                var price_itinerary = $('#price_itinerary_'+currentId+'').val();

                var total_price = price_itinerary * number_ticket;
                // alert(total_price);
                var totelPrice = $('#total_price_'+currentId+'').val(total_price);
                // console.log(totelPrice);
            }
        });

        $('#step4next').on('click', function (e) {
            if($('.checkout-form').valid()){
                // alert('aa');
                var tripDetailsId = $('input[name=tripDetailsId]').val();

                // var itinerary_name = $('input[name^=itinerary_name]').map(function(idx, elem) {
                //     return $(elem).val();
                // }).get();

                var price_itinerary = $('input[name^=price_itinerary]').map(function(idx, elem) {
                    return $(elem).val();
                }).get();

                var number_ticket = $('input[name^=number_ticket]').map(function(idx, elem) {
                    return $(elem).val();
                }).get();

                var total_price = $('input[name^=total_price]').map(function(idx, elem) {
                    return $(elem).val();
                }).get();

                var state_name = $('select[name^=itinerary_name]').map(function(idx, elem) {
                    // return $(elem).val();
                       return $(elem).find('option:selected').text();
                }).get();

                // alert(price_itinerary);
                // alert(number_ticket);
                // alert(total_price);
                // alert(state_name);


                if(itinerary_name){
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: 'POST',
                        url:"{{route('step4next')}}",
                        data: {
                            itinerary_name: itinerary_name,
                            price_itinerary: price_itinerary,
                            number_ticket: number_ticket,
                            total_price: total_price,
                            tripDetailsId:tripDetailsId,
                        },
                        success:function(data){
                            console.log(data);
                            // alert('abcd');
                            // $('#flightlist').html(data);
                        },
                        error: function(response) {
                            alert(response);
                        }
                    })
                }

            }
        });
        $(document).on('input', '.price_itinerary, .number_ticket', function() {
            var row = $(this).closest('.row');

            var price_itinerary = row.find('.price_itinerary').val();
            var number_ticket = row.find('.number_ticket').val();


            var total_price = price_itinerary * number_ticket;
            row.find('.total_price').val(total_price);
        });
        $('.stepbtn').click(function(){
            var firstName = $('input[name^=firstName]').map(function(idx, elem) {
                    return $(elem).val();
                }).get();
               
            if(firstName.length == totelPassenger){
                if($('.checkout-form').valid()){

                    var $active = $('.wizard .nav li a[href="#' + $(this).closest('.tab-pane').attr('id') + '"]');

                    $active.closest('li').next().removeClass('disabled')
                    nextTab($active.closest('li'));
                }
            }else{
                alert('Please add all passenger details');
            }

	        	
	    })

        $('[name=gst]').click(function(){
	        	if($(this).is(":checked")){
	        		$('#gstrow').removeClass('d-none').addClass('d-flex');
	        		$('[name=registration]').rules('add','required');
	        		$('[name=company_name]').rules('add','required');
	        		$('[name=company_address]').rules('add','required');
	        		
	        	}else{
	        		$('#gstrow').addClass('d-none').removeClass('d-flex');
	        		$('[name=registration]').rules('remove','required');
	        		$('[name=company_name]').rules('remove','required');
	        		$('[name=company_address]').rules('remove','required');
	        	
	        	}
	    })

        $('[name=insure_type]').click(function(){
            var insure_type = $('input[name="insure_type"]:checked').val();
            if(insure_type == 'yes'){
                $('#InsuremainDiv').removeClass('d-none').addClass('d-flex');
                var insurance = $('#InsureId').val();
                $('[name=insurance]').rules('add','required');

            }else{
                $('#InsuremainDiv').addClass('d-none').removeClass('d-flex');
                $('[name=insurance]').rules('remove','required');
            }
        })

        $(".draggable").draggable({
            helper: 'clone',
            revert: 'invalid',
            // revert: true
            // appendTo: "body",
            // helper: "clone"
        });

        $(".dragDrop").droppable({
            accept: ".draggable",
            drop: function(event, ui) {
                var passengerData = $(ui.helper).data("passenger");
                var passengerid = $(ui.helper).data("id");
                var insurance = $('#InsureId').val(passengerid);
                // var insurance = $('#InsureId').val(passengerData);
                // var passengerData = $(ui.draggable).data("passenger");
                // $(this).append($(ui.draggable));
                $(this).html("<p>" + passengerData + "</p>");
            }
        });

        $(".cancel-btn").on("click", function() {
            var insureId = $('#InsureId').val('');
            $(".dragDrop").empty();
        });


        $(".coupon").on("dblclick", function() {
            applyCoupon($(this));
        });

        // Right-click event to remove coupon
        $("#selectedCoupon").on("contextmenu", function(e) {
            e.preventDefault(); // Prevent the default right-click context menu
            removeCoupon($(this));
        });

        function applyCoupon(button) {
            var couponId = button.attr('id');
            var couponCode = button.text();
            // $('#selectedCoupon').text(couponCode);
            $('#selectedCoupon').val(couponCode);
            $('#selectedCouponID').val(couponId);
        }

        function removeCoupon(button) {
            // var couponCode = button.text();
            $('#selectedCoupon').val('no coupon applied');
            $('#selectedCouponID').val('');
        }

        $("#multi-Trip").on('click', function (e) {
            $('#multiTripSection').removeClass('d-none');
            $('#returnId').addClass('dNone');
            $('#trip_type').val('multiTrip');

        });

        $("#applyTravelers").on("click", function () {
            $("#addPassanger").hide();
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest(".addPassangerClass").length) {
                $(".OneId").hide();
            }
        });

        $(".addPassangerClass").on('click', function (e) {
            e.stopPropagation();
        });

    });
</script>
@endsection