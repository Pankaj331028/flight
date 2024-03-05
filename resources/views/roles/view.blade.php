@extends('layouts.app')
@section('title', 'Role - ' . ucfirst($role->name))

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('roles')}}">Roles</a></li>
                    <li class="breadcrumb-item active">View Role</li>
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
                        <center class="m-t-30">
                            <h4 class="m-t-10 m-b-0">{{$role->name}}</h4>
                        </center>
                        <div>
                            <hr>

                            <h5 class="p-t-30">Created On</h5>
                            <small class="text-success db">{{date('Y, M d', strtotime($role->created_at))}}</small>

                            <h5 class="p-t-30">Status</h5>
                            <small class="text-success db">{{ucfirst(config('constants.STATUS.'.$role->status))}}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-xlg-9 col-md-7">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#permissions" role="tab">Permissions</a> </li>
                        @if (Helper::checkAccess(route('changeStatusRole')))
                            <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li>
                        @endif

                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="permissions" role="tabpanel">
                            <div class="card-body">
                                <div>
                                    @foreach($role_module as $module)
                                        <h5 class="p-t-20">{{$modules[$module]}}</h5>
                                        <small class="text-success db">{{implode(', ', $role_operations[$module])}}</small>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="settings" role="tabpanel">
                            <div class="card-body">
                                <form class="form-horizontal form-material" method="post" action="{{route('changeStatusRole')}}">
                                    {{csrf_field()}}
                                    <div class="form-group bt-switch">
                                        <div class="col-md-6">
                                            <label class="col-md-6" for="val-block">Status</label>
                                            <input type="hidden" name="statusid" value="{{$role->id}}">
                                            <input type="hidden" name="status" value="{{$role->status}}">
                                            <div class="col-md-2" style="float: right;">
                                                <input type="checkbox" @if($role->status == 'AC') checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" id="statusRole">
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

            $('#statusRole').on('switchChange.bootstrapSwitch', function (event, state) {

                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusRole").is(':checked'))
                    $('input[name=status]').val('AC');
                else
                    $('input[name=status]').val('IN');
            });
        });
    </script>
@endpush
