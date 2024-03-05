@extends('layouts.app')
@section('title', ucfirst($type).' Area')

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Area</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('areas')}}">Areas</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Area</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.message')
                    <div class="card-body">

                        @if($type == 'add')
                            <h4>Fill In Area Details</h4>
                        @elseif($type == 'edit')
                            <h4>Edit Area Details</h4>
                        @endif
                        <hr>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form class="form-material m-t-50 row form-valide" method="post" action="{{$url}}" enctype="multipart/form-data">

                            {{csrf_field()}}
                            @if($type == 'edit')
                                <div class="form-group col-md-6 m-t-20">
                                    <label>State</label><sup class="text-reddit"> *</sup>
                                    <select class="form-control" name="state" id="areaState" @if($type=='edit') disabled @endif>
                                        <option value=''>Select State</option>
                                        {{--  @if(is_array($cities))  --}}
                                        @foreach($states as $state)
                                            <option value="{{$state}}" @if($area->city->state->name==$state) selected @endif>{{$state}}</option>
                                        @endforeach
                                        {{--  @endif  --}}
                                    </select>
                                </div>
                                <div class="form-group col-md-6 m-t-20">
                                    <label>City</label><sup class="text-reddit"> *</sup>
                                    <select class="form-control" name="city" id="areaCity" @if($type=='edit') disabled @endif>
                                        <option value=''>Select City</option>
                                        {{--  @if(is_array($cities))  --}}
                                        @foreach($cities as $city)
                                            <option value="{{$city}}" @if($area->city->name==$city) selected @endif>{{$city}}</option>
                                        @endforeach
                                        {{--  @endif  --}}
                                    </select>
                                </div>
                            @else
                                <div class="form-group col-md-6 m-t-20">
                                    <label>State</label><sup class="text-reddit"> *</sup>
                                    <select class="form-control" name="state" id="areaState">
                                        <option value=''>Select State</option>
                                        {{--  @if(is_array($cities))  --}}
                                        @foreach($states as $state)
                                            <option value="{{$state}}" @if(Session::get('globalState')==$state) selected @endif>{{$state}}</option>
                                        @endforeach
                                        {{--  @endif  --}}
                                    </select>
                                </div>
                                <div class="form-group col-md-6 m-t-20">
                                    <label>City</label><sup class="text-reddit"> *</sup>
                                    <select class="form-control" name="city" id="areaCity">
                                        @if(Session::get('globalState') == 'all')
                                            <option value=''>Select State first</option>
                                        @else
                                            <option value=''>Select City</option>
                                            @if($cities)
                                                @foreach($cities as $city)
                                                    <option value="{{$city}}" @if(Session::get('globalCity')==$city) selected @endif>{{$city}}</option>
                                                @endforeach
                                            @endif
                                        @endif
                                    </select>
                                </div>
                            @endif
                            <div class="form-group col-md-6 m-t-20">
                                <label>Area</label><sup class="text-reddit"> *</sup>
                                <input type="text" name="area" class="form-control" value="{{old('area', $area->area)}}" maxlength="200">
                            </div>

                            <input type="hidden" name="status" value="@if(isset($area) && $area->status != null) {{$area->status}} @else AC @endif">
                            <div class="form-group bt-switch col-md-6 m-t-20">
                                <label class="col-md-4">Status</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" @if($type == 'edit') @if(isset($area) && $area->status == 'AC') checked @endif @else checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-status" id="statusArea">
                                </div>
                            </div>
                            <div class="col-12 m-t-20">
                                <button type="submit" class="btn btn-success waves-effect waves-light m-r-10">Save</button>
                                <a href="{{route('areas')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(function(){
             $('#statusArea').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusArea").is(':checked'))
                    $('input[name=status]').val('AC');
                else
                    $('input[name=status]').val('IN');
            });

            @if($type == 'edit')
                $('input[name=area]').rules('add', {remote: {
                    url: APP_NAME + "/admin/areas/checkArea/{{$area->id}}",
                    type: "post",
                    data: {
                        city: function() {
                            return $( "#areaCity" ).val();
                        }
                    }
                }});
            @else

                $('input[name=area]').rules('add', {remote: {
                    url: APP_NAME + "/admin/areas/checkArea",
                    type: "post",
                    data: {
                        city: function() {
                            return $( "#areaCity" ).val();
                        }
                    }
                }});
            @endif

            $('#areaState').change(function(){
                var state = $(this).val();

                $.ajax({
                    url: "{{route('getCity')}}",
                    type: "POST",
                    data: {state: state},
                    success: function(data){
                        var cities = JSON.parse(data);

                        var options = "<option value=''>Select City</option>";
                        for (var i = 0; i < cities.length; i++) {
                            // options += "<option value='" + cities[i] + "'>" + cities[i] + "</option>";
                        }
                        $('#areaCity').html(options);
                    }
                })
            });
        });
    </script>
@endpush
