@extends('front.travel.layouts.master')
@section('template_title','Sample API')
@section('content')
    <style type="text/css">
        .faqs #hwefaqaccordion .panelhead a {
            justify-content: space-between;
        }
        .faqs #hwefaqaccordion i.trigger {
            left: auto !important;
        }
        #hwefaqaccordion .border-bottom:nth-of-type(5){
            border-bottom: 1px solid #dee2e6!important
        }
    </style>
    <section class="faqs my-5">
        <div class="container">
            <h5 class="font35 font-weight-bold color11 text-center pageTitle">Sample API</h5>
            <div class="acountTabing mt-md-5 mt-3">
                <div class="row">
                    <div id="hwefaqaccordion" class="mt-5 w-100">
                        <div class="border-bottom pb-3 mb-3">
                            <div class="panelhead">
                                <a role="button" data-toggle="collapse" data-parent="#hwefaqaccordion" href="#panel-1" class="font-weight-bold color20 font18 collapsed"><span><span class="btn filter_btn m-0 d-inline mr-3 py-2 btn-info">GET</span>LIST FLIGHTS</span><i class="trigger"></i></a>
                            </div>

                            <div id="panel-1" class="collapse colorTriple5" data-parent="#hwefaqaccordion">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="fliter_box_inner mb-3">
                                            <h4>Request</h2>
                                            <a href="/api/flights?page=1">/api/flights?page=1</a>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="fliter_box_inner mb-3">
                                            <h4>Response</h2>
                                            <a href="javascript:;">200</a>
                                            <hr>
                                            @php
                                            $request=new \Illuminate\Http\Request;
                                            $request->page=1;
                                            $data=app('App\Http\Controllers\SampleApiController')->getFlights($request);
                                            @endphp
                                            <p><pre>{{json_encode($data->getData(), JSON_PRETTY_PRINT)}}</pre></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="fliter_box_inner mb-3">
                                            <h4>Request</h2>
                                            <a href="/api/flights?page=2">/api/flights?page=2</a>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="fliter_box_inner mb-3">
                                            <h4>Response</h2>
                                            <a href="javascript:;">200</a>
                                            <hr>
                                            @php
                                            $request=new \Illuminate\Http\Request;
                                            $request->page=2;
                                            $data=app('App\Http\Controllers\SampleApiController')->getFlights($request);
                                            @endphp
                                            <p><pre>{{json_encode($data->getData(), JSON_PRETTY_PRINT)}}</pre></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-bottom pb-3 mb-3">
                            <div class="panelhead">
                                <a role="button" data-toggle="collapse" data-parent="#hwefaqaccordion" href="#panel-2" class="font-weight-bold color20 font18 collapsed"><span><span class="btn filter_btn m-0 d-inline mr-3 py-2 btn-info">GET</span>SINGLE FLIGHT</span><i class="trigger"></i></a>
                            </div>

                            <div id="panel-2" class="collapse colorTriple5" data-parent="#hwefaqaccordion">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="fliter_box_inner mb-3">
                                            <h4>Request</h2>
                                                @php
                                                $id = App\Model\Flight::orderBy('id','desc')->first()->id;
                                                @endphp
                                            <a href="/api/flight/{{$id}}">/api/flight/{{$id}}</a>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="fliter_box_inner mb-3">
                                            <h4>Response</h2>
                                            <a href="javascript:;">200</a>
                                            <hr>
                                            @php
                                            $request=new \Illuminate\Http\Request;
                                            $data=app('App\Http\Controllers\SampleApiController')->getSingleFlight($request,$id);
                                            @endphp
                                            <p><pre>{{json_encode($data->getData(), JSON_PRETTY_PRINT)}}</pre></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-bottom pb-3 mb-3">
                            <div class="panelhead">
                                <a role="button" data-toggle="collapse" data-parent="#hwefaqaccordion" href="#panel-3" class="font-weight-bold color20 font18 collapsed"><span><span class="btn filter_btn m-0 d-inline mr-3 py-2">POST</span>CREATE FLIGHT</span><i class="trigger"></i></a>
                            </div>

                            <div id="panel-3" class="collapse colorTriple5" data-parent="#hwefaqaccordion">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="fliter_box_inner mb-3">
                                            <h4>Request</h2>
                                            @php
                                            $request=new \Illuminate\Http\Request;
                                            $request->replace([
                                                'flightName'=>'AirIndia',
                                                'Country'=>'India',
                                                'Destinations'=>'87',
                                                'URL'=>'https://en.wikipedia.org/wiki/Air_India'
                                            ]);
                                            @endphp
                                            <a href="/api/flights">/api/flights</a>
                                            <hr>
                                            <p><pre>{{json_encode($request->all(), JSON_PRETTY_PRINT)}}</pre></p>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="fliter_box_inner mb-3">
                                            <h4>Response</h2>
                                            <a href="javascript:;">201</a>
                                            <hr>
                                            @php
                                            $data=app('App\Http\Controllers\SampleApiController')->createFlight($request);
                                            @endphp
                                            <p><pre>{{json_encode($data->getData(), JSON_PRETTY_PRINT)}}</pre></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-bottom pb-3 mb-3">
                            <div class="panelhead">
                                <a role="button" data-toggle="collapse" data-parent="#hwefaqaccordion" href="#panel-4" class="font-weight-bold color20 font18 collapsed"><span><span class="btn filter_btn btn-warning m-0 d-inline mr-3 py-2">PUT</span>UPDATE FLIGHT</span><i class="trigger"></i></a>
                            </div>

                            <div id="panel-4" class="collapse colorTriple5" data-parent="#hwefaqaccordion">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="fliter_box_inner mb-3">
                                            <h4>Request</h2>
                                            @php
                                            $request=new \Illuminate\Http\Request;
                                            $id = App\Model\Flight::orderBy('id','desc')->first()->id;
                                            $request->replace([
                                                'flightName'=>'AirIndia',
                                                'Country'=>'India',
                                                'Destinations'=>mt_rand(10,100),
                                                'URL'=>'https://en.wikipedia.org/wiki/Air_India'
                                            ]);
                                            @endphp
                                            <a href="/api/flight/{{$id}}">/api/flight/{{$id}}</a>
                                            <hr>
                                            <p><pre>{{json_encode($request->all(), JSON_PRETTY_PRINT)}}</pre></p>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="fliter_box_inner mb-3">
                                            <h4>Response</h2>
                                            <a href="javascript:;">200</a>
                                            <hr>
                                            @php
                                            $data=app('App\Http\Controllers\SampleApiController')->updateFlight($request, $id);
                                            @endphp
                                            <p><pre>{{json_encode($data->getData(), JSON_PRETTY_PRINT)}}</pre></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-bottom pb-3 mb-3">
                            <div class="panelhead">
                                <a role="button" data-toggle="collapse" data-parent="#hwefaqaccordion" href="#panel-5" class="font-weight-bold color20 font18 collapsed"><span><span class="btn filter_btn m-0 d-inline mr-3 py-2 btn-warning">PATCH</span>UPDATE FLIGHT</span><i class="trigger"></i></a>
                            </div>

                            <div id="panel-5" class="collapse colorTriple5" data-parent="#hwefaqaccordion">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="fliter_box_inner mb-3">
                                            <h4>Request</h2>
                                            @php
                                            $request=new \Illuminate\Http\Request;
                                            $request->replace([
                                                'Destinations'=>mt_rand(10,100),
                                            ]);
                                            $id = App\Model\Flight::orderBy('id','desc')->first()->id;
                                            @endphp
                                            <a href="/api/flight/{{$id}}">/api/flight/{{$id}}</a>
                                            <hr>
                                            <p><pre>{{json_encode($request->all(), JSON_PRETTY_PRINT)}}</pre></p>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="fliter_box_inner mb-3">
                                            <h4>Response</h2>
                                            <a href="javascript:;">200</a>
                                            <hr>
                                            @php
                                            $data=app('App\Http\Controllers\SampleApiController')->updateFlight($request, $id);
                                            @endphp
                                            <p><pre>{{json_encode($data->getData(), JSON_PRETTY_PRINT)}}</pre></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-bottom pb-3 mb-3">
                            <div class="panelhead">
                                <a role="button" data-toggle="collapse" data-parent="#hwefaqaccordion" href="#panel-6" class="font-weight-bold color20 font18 collapsed"><span><span class="btn filter_btn m-0 d-inline mr-3 py-2 btn-danger">DELETE</span>DELETE FLIGHT</span><i class="trigger"></i></a>
                            </div>

                            <div id="panel-6" class="collapse colorTriple5" data-parent="#hwefaqaccordion">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="fliter_box_inner mb-3">
                                            <h4>Request</h2>
                                            @php
                                            $request=new \Illuminate\Http\Request;
                                            $id = App\Model\Flight::orderBy('id','desc')->first()->id;
                                            @endphp
                                            <a href="/api/flight/{{$id}}">/api/flight/{{$id}}</a>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="fliter_box_inner mb-3">
                                            <h4>Response</h2>
                                            <a href="javascript:;">204</a>
                                            <hr>
                                            @php
                                            $data=app('App\Http\Controllers\SampleApiController')->deleteFlight($request, $id);
                                            @endphp
                                            <p><pre>{{json_encode($data->getData(), JSON_PRETTY_PRINT)}}</pre></p>
                                            {{-- <p><pre>{{json_encode([], JSON_PRETTY_PRINT)}}</pre></p> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
  <!-- Button trigger modal -->
@endsection