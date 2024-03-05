
@if(Request::get('trip_type')== 'one_way_trip')
@if(!empty($data))
@foreach($data as $dataDeatils)

    <div class="fliter_box_inner px-4 mb-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="position-relative pb-5">

                    <img src="data:image/png;base64,{{ $getAirlineDetails[$dataDeatils['airlineName']]['airlineLogo'] }}" alt="" class="airlineImg">
                    <!-- <img src="{{asset('/front/images/plan_arrow.png')}}" alt="" class="planeArrow position-absolute"> -->
                </div>
                <div class="row mx-auto">
                    <div class="float-start auto fromContent">
                        <div>
                            <span class="text-secondary">From</span> <br>
                            <label class="font18 mb-0">{{$flightNameArray[$dataDeatils['fromlocation']]}}</label> <br>
                            <span class="text-secondary">{{ \Carbon\Carbon::createFromFormat('H:i', $dataDeatils['depatureTime'])->format('h:i A') }} - {{ \Carbon\Carbon::parse(Request::get('departureDates'))->format('D, d M') }}</span>
                        </div>
                    </div>
                    <div class="float-start px-3 middle mx-auto w-50 position-relative">
                        <div class="borderLine"></div>
                        <div class="swapArrow"></div>
                        <div class="text-center pt-1">
                            <b class="">{{$dataDeatils['travelDuration']}}</b><br>
                            <span class="font12 text-secondary">{{$dataDeatils['stopDetails']}}</span>
                        </div>
                    </div>
                    <div class="float-end auto toContent">
                        <div>
                            <span class="text-secondary">To</span> <br>
                            <label class="font18 mb-0">{{$flightNameArray[$dataDeatils['tolocation']]}}</label> <br>
                            <span class="text-secondary">{{ \Carbon\Carbon::createFromFormat('H:i', $dataDeatils['arrivalTime'])->format('h:i A') }} - {{ \Carbon\Carbon::parse(Request::get('departureDates'))->format('D, d M') }}</span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row mx-auto">
                    <div class="float-start w-50 ml-auto amount">
                        <label class="pricing">
                            <!-- <img src="images/inr.png" alt="Amount" class="inrIcon"> -->
                            &#8377;
                            @if($dataDeatils['offerPrice'] <= 0)
                            {{$dataDeatils['originalPrice']}}
                            @else
                            {{$dataDeatils['offerPrice']}}  <strike  class="text-secondary font16">({{'&#8377;'.$dataDeatils['originalPrice']}})</strike ><span  class="text-secondary font16">(Per Person)</span>
                            @endif
                        </label>
                    </div>
                    <div class="float-end w-50 text-right bookBtn">
                        <!-- <button type="submit" class="greenBtn font700" onClick="redirectionURL('checkout.php')">BOOK NOW</button> -->
                        <a onclick="$('.fliter_box_inner').css('background','#fff');$(this).closest('.fliter_box_inner').css('background','#f0f0f0'); return confirm('Proceed to booking?');" href="{{route('view-flight',['id'=>$dataDeatils['routeIds']])}}?{{explode('?',Request::getRequestUri())[1]}}" class="btn filter_btn greenBtn" id="oneWayBooking">BOOK NOW</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
@else
    <h3 class="text-center mt-5">No Flights Found</h3>
@endif


@else
<?php /*?>
<div class="fliter_box_inner px-4 mb-4">
<div class="row">
<div class="col-lg-12 d-none">
<div class="position-relative pb-5">
<img src="{{asset('/front/images/indigo.png')}}" alt="" class="airlineImg">
<img src="{{asset('/front/images/plan_arrow.png')}}" alt="" class="planeArrow position-absolute">
</div>
<div class="row mx-auto">
<div class="float-start auto">
<div>
<span class="text-secondary fromContent">From</span> <br>
<label class="font18 mb-0">{{$dataDeatils['from_location']}}</label> <br>
<span class="text-secondary">{{ \Carbon\Carbon::createFromFormat('H:i', $dataDeatils['depatureTime'])->format('h:i A') }} - {{ \Carbon\Carbon::parse(Request::get('departureDates'))->format('D, d M') }}</span>
</div>
</div>
<div class="float-start px-3 middle mx-auto w-50 position-relative">
<div class="borderLine"></div>
<div class="swapArrow"></div>
<div class="text-center pt-1">
<b class="">{{$dataDeatils['travelDuration']}}</b><br>
<span class="font12 text-secondary">{{$dataDeatils['stopDetails']}}</span>
</div>
</div>
@php

@endphp
<div class="float-end auto">
<div>
<span class="text-secondary toContent">To</span> <br>
<label class="font18 mb-0">{{$dataDeatils['to_location']}}</label> <br>
<span class="text-secondary">{{ \Carbon\Carbon::createFromFormat('H:i', $dataDeatils['arrivalTime'])->format('h:i A') }} - {{ \Carbon\Carbon::parse(Request::get('departureDates'))->format('D, d M') }}</span>
</div>
</div>
</div>
<hr>
<div class="row mx-auto">
<div class="float-start w-50 ml-auto amount">
<label class="pricing">
<!-- <img src="images/inr.png" alt="Amount" class="inrIcon"> -->
&#8377;
@if($dataDeatils['offerPrice'] <= 0)
{{$dataDeatils['originalPrice']}}
@else
{{$dataDeatils['offerPrice']}}  <strike  class="text-secondary font16">({{'&#8377;'.$dataDeatils['originalPrice']}})</strike >
@endif

</label>
</div>
<div class="float-end w-50 text-right bookBtn">

</div>
</div>
</div>
</div>

</div>
<?php */?>

    @if(Request::get('trip_type')== 'roundTrip')
    <div class="kt-grid__item kt-grid__item--fluid kt-app__content">
        <div class="row">
            <div class="col">
                <!--Begin::Section-->
                <div class="kt-portlet kt-portlet--tabs">
                    <div class="kt-portlet__head multiFlightess">
                    <div class="kt-portlet__head ">
                        <div class="kt-portlet__head-toolbar flightNavbar p-1">
                            <ul class=" row mx-auto nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-primary" role="tablist">
                                <li class="nav-item col">
                                    <a class="nav-link active " data-toggle="tab" href="#description" role="tab">{{Request::get('flyingfrom') .' - '. Request::get('flyingTo')}}</a>
                                </li>
                                <li class="nav-item col">
                                    <a class="nav-link " data-toggle="tab" href="#howtodo" role="tab">{{Request::get('flyingTo') .' - '. Request::get('flyingfrom')}}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="description" role="tabpanel">
                                <!--begin:: Widgets/Stats2-1 -->
                                <div class="kt-widget1 p-0">
                                    <div class="kt-widget1__item">
                                        <div class="kt-widget1__info">
                                            <span class="kt-widget1__desc">
                                            @if(!empty($data))
                                                @foreach($data as $dataDeatils)
                                                    <div class="fliter_box_inner px-4 mb-4">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="position-relative pb-5">
                                                                    <img src="data:image/png;base64,{{ $getAirlineDetails[$dataDeatils['airlineName']]['airlineLogo'] }}" alt="" class="airlineImg">
                                                                    <!-- <img src="{{asset('/front/images/plan_arrow.png')}}" alt="" class="planeArrow position-absolute"> -->
                                                                </div>
                                                                <div class="row mx-auto">
                                                                    <div class="float-start auto">
                                                                        <div>
                                                                            <span class="text-secondary">From</span> <br>
                                                                            <label class="font18 mb-0">{{$flightNameArray[$dataDeatils['fromlocation']]}}</label> <br>
                                                                            <span class="text-secondary">{{ \Carbon\Carbon::createFromFormat('H:i', $dataDeatils['depatureTime'])->format('h:i A') }} - {{ \Carbon\Carbon::parse(Request::get('departureDates'))->format('D, d M') }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="float-start px-3 middle mx-auto w-50 position-relative">
                                                                        <div class="borderLine"></div>
                                                                        <div class="swapArrow"></div>
                                                                        <div class="text-center pt-1">
                                                                            <b class="">{{$dataDeatils['travelDuration']}}</b><br>
                                                                            <span class="font12 text-secondary">{{$dataDeatils['stopDetails']}}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="float-end auto">
                                                                        <div>
                                                                            <span class="text-secondary">To</span> <br>
                                                                            <label class="font18 mb-0">{{$flightNameArray[$dataDeatils['tolocation']]}}</label> <br>
                                                                            <span class="text-secondary">{{ \Carbon\Carbon::createFromFormat('H:i', $dataDeatils['arrivalTime'])->format('h:i A') }} - {{ \Carbon\Carbon::parse(Request::get('departureDates'))->format('D, d M') }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="row mx-auto">
                                                                    <div class="float-start w-50 ml-auto">
                                                                        <label class="pricing">
                                                                            <!-- <img src="images/inr.png" alt="Amount" class="inrIcon"> -->
                                                                            &#8377;
                                                                            @if($dataDeatils['offerPrice'] <= 0)
                                                                            {{$dataDeatils['originalPrice']}}
                                                                            @else
                                                                            {{$dataDeatils['offerPrice']}}  <strike  class="text-secondary font16">({{'&#8377;'.$dataDeatils['originalPrice']}})</strike ><span  class="text-secondary font16">(Per Person)</span>
                                                                            @endif
                                                                        </label>
                                                                    </div>
                                                                    <div class="float-end w-50 flightOuterBox ">
                                                                <div class="treavelClass row mx-auto text-center ">
                                                                    <label class="travelClass col economy font14 mb-0 mx-0 @if ($loop->first) active @endif">
                                                                        <input class="form-check-input dNone" type="radio" name="flightRouteIdDepart" id="{{$dataDeatils['routeIds']}}"  @if ($loop->first)checked @endif value="{{$dataDeatils['routeIds']}}">
                                                                        Select
                                                                    </label>

                                                                </div>

                                                            </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <h3 class="text-center mt-5">No Flights Found</h3>
                                            @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="howtodo" role="tabpanel">
                                <!--begin:: Widgets/Stats2-1 -->
                                <div class="kt-widget1 p-0">
                                    <div class="kt-widget1__item">
                                        <div class="kt-widget1__info">
                                            <span class="kt-widget1__desc">

                                            @if(!empty($dataReturn))
                                            @foreach($dataReturn as $data_return)

                                                    <div class="fliter_box_inner px-4 mb-4">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="position-relative pb-5">
                                                                    <img src="data:image/png;base64,{{ $getAirlineDetails[$data_return['airlineName']]['airlineLogo'] }}" alt="" class="airlineImg">
                                                                    <!-- <img src="{{asset('/front/images/plan_arrow.png')}}" alt="" class="planeArrow position-absolute"> -->
                                                                </div>
                                                                <div class="row mx-auto">
                                                                    <div class="float-start auto">
                                                                        <div>
                                                                            <span class="text-secondary">From</span> <br>
                                                                            <label class="font18 mb-0">{{$flightNameArray[$data_return['fromlocation']]}}</label> <br>
                                                                            <span class="text-secondary">{{ \Carbon\Carbon::createFromFormat('H:i', $data_return['depatureTime'])->format('h:i A') }} - {{ \Carbon\Carbon::parse(Request::get('departureDates'))->format('D, d M') }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="float-start px-3 middle mx-auto w-50 position-relative">
                                                                        <div class="borderLine"></div>
                                                                        <div class="swapArrow"></div>
                                                                        <div class="text-center pt-1">
                                                                            <b class="">{{$data_return['travelDuration']}}</b><br>
                                                                            <span class="font12 text-secondary">{{$data_return['stopDetails']}}</span>
                                                                        </div>
                                                                    </div>
                                                                    @php

                                                                        @endphp
                                                                    <div class="float-end auto">
                                                                        <div>
                                                                            <span class="text-secondary">To</span> <br>
                                                                            <label class="font18 mb-0">{{$flightNameArray[$data_return['tolocation']]}}</label> <br>
                                                                            <span class="text-secondary">{{ \Carbon\Carbon::createFromFormat('H:i', $data_return['arrivalTime'])->format('h:i A') }} - {{ \Carbon\Carbon::parse(Request::get('departureDates'))->format('D, d M') }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="row mx-auto">
                                                                    <div class="float-start w-50 ml-auto">
                                                                        <label class="pricing">
                                                                            <!-- <img src="images/inr.png" alt="Amount" class="inrIcon"> -->
                                                                            &#8377;
                                                                            @if($data_return['offerPrice'] <= 0)
                                                                            {{$data_return['originalPrice']}}
                                                                            @else
                                                                            {{$data_return['offerPrice']}}  <strike  class="text-secondary font16">({{'&#8377;'.$data_return['originalPrice']}})</strike ><span  class="text-secondary font16">(Per Person)</span>
                                                                            @endif
                                                                        </label>
                                                                    </div>
                                                                    <div class="float-end w-50 flightOuterBox ">
                                                                <div class="treavelClass row mx-auto text-center ">
                                                                    <label class="travelClass col economy font14 mb-0 mx-0 @if ($loop->first) active @endif">
                                                                        <input class="form-check-input dNone flightRouteId" type="radio" name="flightRouteId" id="{{$data_return['routeIds']}}" @if ($loop->first)checked @endif value="{{$data_return['routeIds']}}" >
                                                                        Select
                                                                    </label>
                                                                </div>

                                                            </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            @endforeach
                                            @else
                                            <h3 class="text-center mt-5">No Flights Found</h3>
                                            @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="kt-portlet__head multiFlightessBottom shadow">
            <div class="kt-portlet__head-toolbar flightNavbar p-1">
                <div class="row mx-auto nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-primary" role="tablist">
                    <div class="col px-1 nav-item">
                        <label class="nav-link px-3 mb-0">{{Request::get('flyingfrom') .' - '. Request::get('flyingTo')}}</label>
                    </div>
                    <div class="col px-1 nav-item">
                        <label class="nav-link px-3 mb-0">{{Request::get('flyingTo') .' - '. Request::get('flyingfrom')}}</label>
                    </div>
                    <div class="col-lg-2 px-1 nav-item" style="padding-top:10px;">
                        <a id="bookNowLink" onclick="$('.fliter_box_inner').css('background','#fff');$(this).closest('.fliter_box_inner').css('background','#f0f0f0'); return confirm('Proceed to booking?');" herf="" class="btn filter_btn greenBtn bookReturn">BOOK NOW</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @else

    <div class="kt-grid__item kt-grid__item--fluid kt-app__content">
        <div class="row">
            <div class="col">
                <!--Begin::Section-->
                <div class="kt-portlet kt-portlet--tabs">
                    <div class="kt-portlet__head multiFlightess">
                        <div class="kt-portlet__head-toolbar flightNavbar p-1">
                            <ul class="row mx-auto nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-primary" role="tablist">
                                @foreach($flightname as $key => $flightName)
                                <li class="col px-1 nav-item">
                                    <a class="nav-link px-3 @if($key == '0') active @endif" data-toggle="tab" href="#flightname_{{$key}}" role="tab">{{$flightName['from_location'] .' - '. $flightName['to_location']}}</a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <input type="hidden" name="CountMultipleFlight" id ="CountMultipleFlight" value="{{count($multipleFlightData)}}">
                        <div class="tab-content">

                            @foreach($multipleFlightData as $key => $multipleFlight)
                                <div class="tab-pane @if($key == '0') active @endif" id="flightname_{{$key}}" role="tabpanel">
                                    <!--begin:: Widgets/Stats2-1 -->

                                    @if(!empty($multipleFlight))
                                    <div class="kt-widget1 p-0">
                                        <div class="kt-widget1__item">
                                            <div class="kt-widget1__info">
                                                <span class="kt-widget1__desc">
                                                @foreach($multipleFlight as $multiple_Flight)
                                                    <div class="fliter_box_inner px-4 mb-4">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="position-relative pb-5">
                                                                    <img src="data:image/png;base64,{{ $getAirlineDetails[$multiple_Flight['airlineName']]['airlineLogo'] }}" alt="" class="airlineImg">
                                                                    <!-- <img src="{{asset('/front/images/plan_arrow.png')}}" alt="" class="planeArrow position-absolute"> -->
                                                                </div>
                                                                <div class="row mx-auto">
                                                                    <div class="float-start auto">
                                                                        <div>
                                                                            <span class="text-secondary">From</span> <br>
                                                                            <label class="font18 mb-0">{{$flightNameArray[$multiple_Flight['fromlocation']]}}</label> <br>
                                                                            <span class="text-secondary">{{ \Carbon\Carbon::createFromFormat('H:i', $multiple_Flight['depatureTime'])->format('h:i A') }} - {{ \Carbon\Carbon::parse(Request::get('departureDates'))->format('D, d M') }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="float-start px-3 middle mx-auto w-50 position-relative">
                                                                        <div class="borderLine"></div>
                                                                        <div class="swapArrow"></div>
                                                                        <div class="text-center pt-1">
                                                                            <b class="">{{$multiple_Flight['travelDuration']}}</b><br>
                                                                            <span class="font12 text-secondary">{{$multiple_Flight['stopDetails']}}</span>
                                                                        </div>
                                                                    </div>

                                                                    <div class="float-end auto">
                                                                        <div>
                                                                            <span class="text-secondary">To</span> <br>
                                                                            <label class="font18 mb-0">{{$flightNameArray[$multiple_Flight['tolocation']]}}</label> <br>
                                                                            <span class="text-secondary">{{ \Carbon\Carbon::createFromFormat('H:i', $multiple_Flight['arrivalTime'])->format('h:i A') }} - {{ \Carbon\Carbon::parse(Request::get('departureDates'))->format('D, d M') }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="row mx-auto">
                                                                    <div class="float-start w-50 ml-auto">
                                                                        <label class="pricing">
                                                                            <!-- <img src="images/inr.png" alt="Amount" class="inrIcon"> -->
                                                                            &#8377;
                                                                            @if($multiple_Flight['offerPrice'] <= 0)
                                                                            {{$multiple_Flight['originalPrice']}}
                                                                            @else
                                                                            {{$multiple_Flight['offerPrice']}}  <strike  class="text-secondary font16">({{'&#8377;'.$multiple_Flight['originalPrice']}})</strike ><span  class="text-secondary font16">(Per Person)</span>
                                                                            @endif
                                                                        </label>
                                                                    </div>
                                                                    <div class="float-end w-50 flightOuterBox ">
                                                                <div class="treavelClass row mx-auto text-center ">
                                                                    <label class="travelClass col economy font14 mb-0 mx-0 @if ($loop->first) active @endif">
                                                                        <input class="form-check-input dNone mutipleSelect" type="radio" name="flightrouteId_{{$key}}" id="" checked @if ($loop->first)checked @endif value="{{$multiple_Flight['routeIds']}}" >
                                                                        Select
                                                                    </label>
                                                                </div>
                                                            </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                        <h3 class="text-center mt-5">No Flights Found</h3>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="kt-portlet__head multiFlightessBottom shadow">
            <div class="kt-portlet__head-toolbar flightNavbar p-1">
                <div class="row mx-auto nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-primary" role="tablist">


                    @foreach($flightname as $key => $flightName)
                    <div class="col px-1 nav-item">
                        <label class="nav-link px-3 mb-0">{{$flightName['from_location'] .' - '. $flightName['to_location']}}</label>
                    </div>
                    @endforeach

                    <div class="col-lg-2 px-1 nav-item" style="padding-top:10px;">
                    <a id="bookNowLink" onclick="$('.fliter_box_inner').css('background','#fff');$(this).closest('.fliter_box_inner').css('background','#f0f0f0'); return confirm('Proceed to booking?');" herf="" class="btn filter_btn greenBtn bookReturn">BOOK NOW</a>
                    <!-- <a class="btn filter_btn greenBtn text-center mx-auto w-100" style="padding:20px;">Book Now</a> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

@endif

<script>

    $(document).ready(function () {

        var trip_type = {!! json_encode(Request::get('trip_type')) !!};
        if(trip_type == 'roundTrip'){
            var flightRouteIdDepart = $("input[name='flightRouteIdDepart']:checked").val();
            var flightRouteId = $("input[name='flightRouteId']:checked").val();

            $('[name=flightRouteIdDepart]').click(function(){
                         flightRouteIdDepart = $("input[name='flightRouteIdDepart']:checked").val();
                        updateHref();
                        // alert(flightRouteIdDepart);

            })
            $('[name=flightRouteId]').click(function(){
                     flightRouteId = $("input[name='flightRouteId']:checked").val();
                    updateHref();

            })
            $("#bookNowLink").on('click', function (e) {
                var hrefValue = $("#bookNowLink").attr("href");

                if(hrefValue == undefined){
                    alert('There are no flights on this route.');
                }

            });
            function updateHref() {

                var baseRoute = "view-flight/" + flightRouteIdDepart + ',' + flightRouteId;

                if(flightRouteIdDepart == undefined || flightRouteId == undefined){
                    // alert('aaaa');

                    $("#bookNowLink").removeAttr("onclick");

                }else{

                    $("#bookNowLink").attr("onclick", "return confirm('Proceed to booking?');");


                    var existingParams = "{{ explode('?', Request::getRequestUri())[1] }}";
                    existingParams = decodeURIComponent(existingParams);
                    existingParams = existingParams.replace(/&amp;/g, '&');
                    $("#bookNowLink").attr("href", baseRoute + "?" + existingParams);
                }

            }
            updateHref();



        }else{

            if(trip_type == 'multiTrip'){
                var flightrouteId_0 = $("input[name='flightrouteId_0']:checked").val();
                var flightrouteId_1 = $("input[name='flightrouteId_1']:checked").val();
                var flightrouteId_2 = $("input[name='flightrouteId_2']:checked").val();
                var flightrouteId_3 = $("input[name='flightrouteId_3']:checked").val();
                var flightrouteId_4 = $("input[name='flightrouteId_4']:checked").val();
                var CountMultipleFlight =$('#CountMultipleFlight').val();

                $('.mutipleSelect').click(function(){
                    flightrouteId_0 = $("input[name='flightrouteId_0']:checked").val();
                    flightrouteId_1 = $("input[name='flightrouteId_1']:checked").val();
                    flightrouteId_2 = $("input[name='flightrouteId_2']:checked").val();
                    flightrouteId_3 = $("input[name='flightrouteId_3']:checked").val();
                    flightrouteId_4 = $("input[name='flightrouteId_4']:checked").val();
                    updateHref();
                })
                $("#bookNowLink").on('click', function (e) {
                    var hrefValue = $("#bookNowLink").attr("href");

                    if(hrefValue == undefined){
                        alert('There are no flights on this route.');
                    }

                });
                function updateHref() {

                    var flightRouteIds = [flightrouteId_0, flightrouteId_1, flightrouteId_2, flightrouteId_3, flightrouteId_4];
                    // alert(CountMultipleFlight);
                    // alert(flightRouteIds);
                    var id = flightRouteIds.filter(function(value) {
                    return value !== undefined;
                    }).join(',');
                    // alert( id.split(",").length);
                    // console.log(id);
                    // id.split(",").length
                    if(id.split(",").length != CountMultipleFlight){
                        // alert(id.length);
                        // alert(CountMultipleFlight);
                        $("#bookNowLink").removeAttr("onclick");

                    }else{

                        $("#bookNowLink").attr("onclick", "return confirm('Proceed to booking?');");
                        var baseRoute = "view-flight/" + id;
                        var existingParams = "{{ explode('?', Request::getRequestUri())[1] }}";
                        existingParams = decodeURIComponent(existingParams);
                        existingParams = existingParams.replace(/&amp;/g, '&');

                        $("#bookNowLink").attr("href", baseRoute + "?" + existingParams);
                    }


                }
                updateHref();
            }

        }


    });
</script>