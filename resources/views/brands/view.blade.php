@extends('layouts.app')
@section('title', 'Brand - ' . ucfirst($brand->name))

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Branch</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('brands')}}">Brands</a></li>
                    <li class="breadcrumb-item active">View Brand</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            @include('layouts.message')
            <div class="col-lg-12 col-xlg-12 col-md-12">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link active show" data-toggle="tab" href="#profile" role="tab">Details</a> </li>
                        @if (Helper::checkAccess(route('changeStatusBrand')))
                            <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li>
                        @endif

                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="profile" role="tabpanel">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-xs-6 b-r"> <strong>Brand Name</strong>
                                        <br>
                                        <p class="text-success">{{ucfirst($brand->name)}}</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4 columns">
                                        <small class="text-success p-t-30 db">Brand Code</small><h5>{{$brand->brand_code}}</h5>
                                    </div>
                                    <div class="col-md-4 columns">
                                        <small class="text-success p-t-30 db">Created On</small><h5>{{date('Y, M d', strtotime($brand->created_at))}}</h5>
                                    </div>
                                    <div class="col-md-4 columns">
                                        <small class="text-success p-t-30 db">Status</small><h5>{{ucfirst(config('constants.STATUS.'.$brand->status))}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="settings" role="tabpanel">
                            <div class="card-body">
                                <form class="form-horizontal form-material" method="post" action="{{route('changeStatusBrand')}}">
                                    {{csrf_field()}}
                                    <div class="form-group bt-switch">
                                        <div class="col-md-6">
                                            <label class="col-md-6" for="val-block">Status</label>
                                            <input type="hidden" name="statusid" value="{{$brand->id}}">
                                            <input type="hidden" name="status" value="{{$brand->status}}">
                                            <div class="col-md-2" style="float: right;">
                                                <input type="checkbox" @if($brand->status == 'AC') checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" id="statusBrand">
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

            $('#statusBrand').on('switchChange.bootstrapSwitch', function (event, state) {

                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusBrand").is(':checked'))
                    $('input[name=status]').val('AC');
                else
                    $('input[name=status]').val('IN');
            });
        });
    </script>
@endpush
