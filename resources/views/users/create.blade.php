@extends('layouts.app')
@section('title', ucfirst($type).' User')

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Branch</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('users')}}">Users</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} User</li>
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
                            <h4>Fill In User Details</h4>
                        @elseif($type == 'edit')
                            <h4>Edit User Details</h4>
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
                            <div id="userForm" class="row" style="margin: 0">
                                <div class="row" style="margin: 0">
                                    <div class="form-group col-md-6 m-t-20">
                                        <label>First Name</label><sup class="text-reddit"> *</sup>
                                        <input type="text" class="form-control form-control-line" name="first_name" value="{{old('first_name', $user->first_name)}}" maxlength="100">
                                    </div>
                                    <div class="form-group col-md-6 m-t-20">
                                        <label>Last Name</label><sup class="text-reddit"> *</sup>
                                        <input type="text" class="form-control form-control-line" name="last_name" value="{{old('last_name', $user->last_name)}}" maxlength="100">
                                    </div>
                                    <div class="form-group col-md-12 m-t-20">
                                        <label>Contact Number</label><sup class="text-reddit"> *</sup>
                                        <input type="text" class="form-control form-control-line numberInput" name="mobile_number" value="{{old('mobile_number', $user->mobile_number)}}">
                                    </div>
                                    <input type="hidden" name="status" value="@if(isset($user) && $user->status != null) {{$user->status}} @else AC @endif">
                                    <div class="form-group bt-switch col-md-6 m-t-20">
                                        <label class="col-md-4">Status</label>
                                        <div class="col-md-3" style="float: right;">
                                            <input type="checkbox" @if($type == 'edit') @if(isset($user) && $user->status == 'AC') checked @endif @else checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-status" id="statusUser">
                                        </div>
                                    </div>

                                    <input type="hidden" name="verify" value="@if(isset($user) && $user->admin_verify != null) {{$user->admin_verify}} @else AC @endif">

                                    <div class="form-group bt-switch col-md-6 m-t-20">
                                        <label class="col-md-4">Admin Verify </label>
                                        <div class="col-md-3" style="float: right;">
                                            <input type="checkbox" @if($type == 'edit') @if(isset($user) && $user->admin_verify == 1) checked @endif @else checked @endif data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="val-verify" id="verifyUser">
                                        </div>
                                    </div>


                                </div>
                            </div>
                            <div class="col-12 m-t-20">
                                <button type="submit" class="btn btn-success submitBtn m-r-10" @if($type != 'edit') disabled="" @endif>Save</button>
                                <a href="{{route('users')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
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
<script src="{{URL::asset('/js/jquery-mask-as-number.js')}}"></script>
    <script type="text/javascript">
        $(function(){


            $('#statusUser').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusUser").is(':checked'))
                    $('input[name=status]').val('AC');
                else
                    $('input[name=status]').val('IN');
            });


            $('#verifyUser').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#verifyUser").is(':checked'))
                    $('input[name=verify]').val(1);
                else
                    $('input[name=verify]').val(0);
            });





            $(document).find(".decimalInput, .numberInput").on('keyup',function(e){

                if($(this).val().indexOf('-') >=0){
                    $(this).val($(this).val().replace(/\-/g,''));
                }
            })

            $(document).find(".numberInput").maskAsNumber({receivedMinus:false});
            $(document).find(".decimalInput").maskAsNumber({receivedMinus:false,decimals:6});

            $('input[name=mobile_number]').val("{{$user->mobile_number}}");
        });
    </script>
@endpush
