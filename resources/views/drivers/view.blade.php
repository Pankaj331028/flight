@extends('layouts.app')
@section('title', 'Driver - ' . ucfirst($driver->first_name))

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Branch</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('drivers')}}">Drivers</a></li>
                    <li class="breadcrumb-item active">View Driver</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            @include('layouts.message')
            <div class="col-lg-4 col-xlg-3 col-md-5">
                <div class="card">
                    <div class="card-body">
                        @include('layouts.message')
                        <center class="m-t-30"> <img src="@if($driver->profile_picture != null){{URL::asset('/uploads/profiles/'.$driver->profile_picture)}} @else {{URL::asset('/images/user-gray.png')}} @endif" class="profilePic" />
                            <h4 class="card-title m-t-10 m-b-0">{{$driver->first_name." ".$driver->last_name}}</h4>
                            <small class="text-success">{{$driver->user_role[0]->name}}</small>
                        </center>
                    </div>
                    <div>
                        <hr> </div>
                    <div class="card-body"> <small class="text-success">Email address </small>
                        <h6>{{$driver->email}}</h6> <small class="text-success p-t-20 db">Phone</small>
                        <h6>{{$driver->mobile_number}}</h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-xlg-9 col-md-7">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#profile" role="tab">Profile</a> </li>
                        @if (Helper::checkAccess(route('changeStatusDriver')))
                            <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li>
                        @endif
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="profile" role="tabpanel">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-xs-6 b-r"> <strong>Mobile</strong>
                                        <br>
                                        <p class="text-success">{{$driver->mobile_number}}</p>
                                    </div>
                                    <div class="col-md-6 col-xs-6 b-r"> <strong>Email</strong>
                                        <br>
                                        <p class="text-success">{{$driver->email}}</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="m-t-30">

                                    <h5 class="p-t-20 db">Email ID</h5><small class="text-success db">{{$driver->email}}</small>
                                    <h5 class="p-t-20 db">Driver Registration Code</h5><small class="text-success db">{{$driver->user_code}}</small>
                                    <h5 class="p-t-20 db">Registered On</h5><small class="text-success db">{{date('Y, M d', strtotime($driver->created_at))}}</small>
                                    <h5 class="p-t-20 db">Status</h5><small class="text-success db">{{ucfirst(config('constants.STATUS.'.$driver->status))}}</small>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="settings" role="tabpanel">
                            <div class="card-body">
                                <form class="form-horizontal form-material" method="post" action="{{route('changeStatusDriver')}}">
                                    {{csrf_field()}}
                                    <div class="form-group bt-switch">
                                        <div class="col-md-6">
                                            <label class="col-md-6" for="val-block">Status</label>
                                            <input type="hidden" name="statusid" value="{{$driver->id}}">
                                            <input type="hidden" name="status" value="{{$driver->status}}">
                                            <div class="col-md-2" style="float: right;">
                                                <input type="checkbox" @if($driver->status == 'AC') checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" id="statusBranch">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-success">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <!-- End PAge Content -->
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function(){

            $('#statusBranch').on('switchChange.bootstrapSwitch', function (event, state) {

                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusBranch").is(':checked'))
                    $('input[name=status]').val('AC');
                else
                    $('input[name=status]').val('IN');
            });
        });
    </script>
@endpush