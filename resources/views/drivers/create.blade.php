@extends('layouts.app')
@section('title', ucfirst($type).' Driver')

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
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Driver</li>
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
                            <h4>Fill In Driver Details</h4>
                        @elseif($type == 'edit')
                            <h4>Edit Driver Details</h4>
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
                            
                            <div class="form-group col-md-6 m-t-20 float-left">
                                <label>Profile Picture</label><sup class="text-reddit"></sup>
                                <input type="hidden" name="image_exists" id="image_exists" value="1">
                                @if($type == 'add' || ($type == 'edit' && $driver->profile_picture == null))
                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                        <div class="form-control" data-trigger="fileinput"> <i class="glyphbanner glyphbanner-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span>
                                        <input type="file" name="profile_picture"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                @elseif($type == 'edit')
                                    <br>
                                    <div id="profilePicture">
                                        <img src="@if($driver->profile_picture != null && file_exists(public_path('/uploads/profiles/'.$driver->profile_picture))){{URL::asset('/uploads/profiles/'.$driver->profile_picture)}}@endif" width="70"" />
                                        &nbsp;&nbsp;&nbsp;<a id="changeImage" href="javascript:void(0)" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Delete">Change</a>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>First Name</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" name="first_name" value="{{old('first_name', $driver->first_name)}}" maxlength="100">
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Last Name</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" name="last_name" value="{{old('last_name', $driver->last_name)}}" maxlength="100">
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Contact Number</label><sup class="text-reddit"> *</sup>
                                {{-- <br> --}}
                                <div class="d-flex">
                                    {{-- <select class="form-control col-md-4 float-left" name="phonecode" id="phonecode">
                                        <option value="">Phonecode</option>
                                        @foreach($phonecode as $val)
                                            <option value="{{$val->phonecode}}" @if(isset($driver->mobile_number) && explode('-',$driver->mobile_number)[0]=='+'.$val->phonecode) selected @endif>{{$val->phonecode}}</option>
                                        @endforeach
                                    </select> --}}
                                    <input type="text" class="form-control form-control-line" name="driver_phone" id="phone_number" value="{{old('mobile_number', $driver->mobile_number)}}">
                                </div>
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Email Address</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" name="driver_email" value="{{old('email', $driver->email)}}">
                            </div>

                            <div class="form-group col-md-6 m-t-20">
                                <label>Password</label><sup class="text-reddit"> *</sup>
                                <input type="password" class="form-control form-control-line" name="pass" placeholder="**********" id="pass">

                            </div>

                            <div class="form-group col-md-6 m-t-20">
                                <label>Confirm Password</label><sup class="text-reddit"> *</sup>
                                <input type="password" class="form-control form-control-line" name="confirm-pass" placeholder="**********">
                            </div>
                            <input type="hidden" name="status" value="@if(isset($driver) && $driver->status != null) {{$driver->status}} @else AC @endif">
                            <div class="form-group bt-switch col-md-6 m-t-20">
                                <label class="col-md-4">Status</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" @if($type == 'edit') @if(isset($driver) && $driver->status == 'AC') checked @endif @else checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-status" id="statusUser">
                                </div>
                            </div>
                            <div class="col-12 m-t-20">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                                <a href="{{route('drivers')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
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
            $(document).find(".decimalInput, .numberInput").on('keyup',function(e){

                if($(this).val().indexOf('-') >=0){
                    $(this).val($(this).val().replace(/\-/g,''));
                }
            })

            $(document).find(".numberInput").maskAsNumber({receivedMinus:false});
            $(document).find(".decimalInput").maskAsNumber({receivedMinus:false,decimals:6});

            @if($type == 'edit')
            $('input[name=driver_email]').rules('add', {remote: APP_NAME + "/admin/drivers/checkDriver/{{$driver->id}}"});
                $('input[name=driver_phone]').rules('add', {remote: APP_NAME + "/admin/drivers/checkPhone/{{$driver->id}}"});
                $('input[name=pass]').rules('remove', 'required');
                $('input[name=confirm-pass]').rules('remove', 'required');
            @endif

            $('input[name=driver_phone]').val("{{$driver->mobile_number}}");

            //profile picture edit/remove
            $('#changeImage').click(function(){
                $('#profilePicture').parent().append('<div class="fileinput fileinput-new input-group" data-provides="fileinput"><div class="form-control" data-trigger="fileinput"> <i class="glyphbanner glyphbanner-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span><input type="file" name="profile_picture"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a></div>');
                $('.tooltip').tooltip('hide');
                $('#profilePicture').remove();
                $('#image_exists').val(0);
            });
        });
    </script>
@endpush