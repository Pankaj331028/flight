@extends('layouts.app')
@section('title', 'Attributes Set - ' . ucfirst($attributesset->title))

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Branch</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('attributes-sets')}}">Attributes Set</a></li>
                    <li class="breadcrumb-item active">View Attributes Set</li>
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
                            <h4 class="card-title m-t-10 m-b-0">{{ ucfirst($attributesset->title) }}</h4>
                            <small class="text-success">{{$attributesset->letter}}</small>
                        </center>
                    </div>
                    <div>
                        <hr> </div>
                    <div class="card-body"> <small class="text-success">Title </small>
                        <h6>{{$attributesset->title}}</h6> 
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-xlg-9 col-md-7">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#profile" role="tab">Profile</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="profile" role="tabpanel">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-xs-6 b-r"> <strong>Letter</strong>
                                        <br>
                                        <p class="text-success">{{ucfirst($attributesset->letter)}}</p>
                                    </div>
                                    <div class="col-md-3 col-xs-6 b-r"> <strong>Title</strong>
                                        <br>
                                        <p class="text-success">{{ucfirst($attributesset->title)}}</p>
                                    </div>
                                    <div class="col-md-6 col-xs-6 b-r"> <strong>Description</strong>
                                        <br>
                                        <p class="text-success">{{ ucfirst($attributesset->description) }}</p>
                                    </div>
                                    <div class="col-md-6 col-xs-6 b-r"> <strong>Image</strong>
                                        <br>
                                        <img src="@if($attributesset->image != null && file_exists(public_path('/uploads/attributesset/'.$attributesset->image))){{URL::asset('/uploads/attributesset/'.$attributesset->image)}}@endif" width="20%" />
                                    </div>
                                </div>
                                <hr>
                                <div class="m-t-30">

                                    <h5 class="p-t-20 db">Create On</h5><small class="text-success db">{{date('Y, M d', strtotime($attributesset->created_at))}}</small>
                                    <h5 class="p-t-20 db">Status</h5><small class="text-success db">{{ucfirst(config('constants.STATUS.'.$attributesset->status))}}</small>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="settings" role="tabpanel">
                            <div class="card-body">
                                <form class="form-horizontal form-material" method="post" action="{{route('changeStatusattributes-sets')}}">
                                    {{csrf_field()}}
                                    <div class="form-group bt-switch">
                                        <div class="col-md-6">
                                            <label class="col-md-6" for="val-block">Status</label>
                                            <input type="hidden" name="statusid" value="{{$attributesset->id}}">
                                            <input type="hidden" name="status" value="{{$attributesset->status}}">
                                            <div class="col-md-2" style="float: right;">
                                                <input type="checkbox" @if($attributesset->status == 'AC') checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" id="statusBranch">
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
