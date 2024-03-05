
@foreach($data as $dataDeatils)
   
    <div class="fliter_box_inner px-4 mb-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="position-relative pb-5">
                    <img src="{{asset('/front/images/indigo.png')}}" alt="" class="airlineImg">
                    <img src="{{asset('/front/images/plan_arrow.png')}}" alt="" class="planeArrow position-absolute">
                </div>
                <div class="row mx-auto">
                    <div class="float-start auto">
                        <div>
                            <span class="text-secondary">From</span> <br>
                            <label class="font18 mb-0">{{$dataDeatils['from_location']}}</label> <br>
                            <span class="text-secondary">{{ \Carbon\Carbon::createFromFormat('H:i', $dataDeatils['depature_time'])->format('h:i A') }} - {{ \Carbon\Carbon::parse(Request::get('departureDates'))->format('D, d M') }}</span>
                        </div>
                    </div>
                    <div class="float-start px-3 middle mx-auto w-50 position-relative">
                        <div class="borderLine"></div>
                        <div class="swapArrow"></div>
                        <div class="text-center pt-1">
                            <b class="">{{$dataDeatils['travel_duration']}}</b><br>
                            <span class="font12 text-secondary">{{$dataDeatils['stop_details']}}</span>
                        </div>
                    </div>
                    @php
                            
                        @endphp
                    <div class="float-end auto">
                        <div>
                            <span class="text-secondary">To</span> <br>
                            <label class="font18 mb-0">{{$dataDeatils['to_location']}}</label> <br>
                            <span class="text-secondary">{{ \Carbon\Carbon::createFromFormat('H:i', $dataDeatils['arrival_time'])->format('h:i A') }} - {{ \Carbon\Carbon::parse(Request::get('departureDates'))->format('D, d M') }}</span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row mx-auto">
                    <div class="float-start w-50 ml-auto">
                        <label class="pricing">
                            <!-- <img src="images/inr.png" alt="Amount" class="inrIcon"> -->
                            &#8377;
                            @if($dataDeatils['offer_price'] <= 0)
                            {{$dataDeatils['original_price']}}
                            @else 
                            {{$dataDeatils['offer_price']}}  <strike  class="text-secondary font16">({{'&#8377;'.$dataDeatils['original_price']}})</strike >  
                            @endif
                            
                        </label>
                    </div>
                    <div class="float-end w-50 text-right">
                        <!-- <button type="submit" class="greenBtn font700" onClick="redirectionURL('checkout.php')">BOOK NOW</button> -->
                        <a onclick="$('.fliter_box_inner').css('background','#fff');$(this).closest('.fliter_box_inner').css('background','#f0f0f0'); return confirm('Proceed to booking?');" href="{{route('view-flight',['id'=>$dataDeatils['routeId']])}}?{{explode('?',Request::getRequestUri())[1]}}" class="btn filter_btn greenBtn">BOOK NOW</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
    
 