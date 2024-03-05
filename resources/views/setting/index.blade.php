@extends('layouts.app')
@section('title', 'Settings')

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Branch</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item active">Settings</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.message')
                    <div class="card-body">
                            <h4>Edit Application Settings</h4>
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

                        <form class="form-material m-t-50 row form-valide" action="{{ route("storeSetting") }}" method="get" enctype="multipart/form-data" id="form">
                            {{csrf_field()}}
                            <div class="form-group col-md-3 m-t-20">
                                <label>Customer Care Number</label>
                                <input type="text" name="customer_care_number" class="form-control" value="{{$setting[8]->rule_value}}" required>
                            </div>
                            <div class="form-group col-md-3 m-t-20">
                                <label>Referral Amount</label>
                                <input type="text" name="referrer_amount" class="form-control decimalInput" value="{{$setting[7]->rule_value}}" required>
                            </div>
                            <div class="form-group col-md-3 m-t-20">
                                <label>Android User App Version</label>
                                <input type="text" name="android_version_user" class="form-control decimalInput" value="{{$setting[5]->rule_value}}" required>
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Android User App URL</label>
                                <input type="text" name="android_url_user" class="form-control" value="{{$setting[4]->rule_value}}" required>
                            </div>
                            <div class="form-group bt-switch col-md-3 m-t-20" style="display: none;">
                                <label>Android User App Force Update</label><br>
                                <div class="col-md-3">
                                    <input type="checkbox" @if($setting[3]->rule_value) checked @endif data-on-color="success" data-off-color="info" data-on-text="ON" data-off-text="OFF" data-size="mini" name="android_update_user">
                                </div>
                            </div>
                            <div class="form-group col-md-3 m-t-20">
                                <label>iOS User App Version</label>
                                <input type="text" name="ios_version_user" class="form-control decimalInput" value="{{$setting[2]->rule_value}}" required>
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>iOS User App URL</label>
                                <input type="text" name="ios_url_user" class="form-control" value="{{$setting[1]->rule_value}}" required>
                            </div>
                            <div class="form-group bt-switch col-md-3 m-t-20" style="display: none;">
                                <label>iOS User App Force Update</label><br>
                                <div class="col-md-3">
                                    <input type="checkbox" @if($setting[0]->rule_value) checked @endif data-on-color="success" data-off-color="info" data-on-text="ON" data-off-text="OFF" data-size="mini" name="ios_update_user">
                                </div>
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Application Update Message</label><br>
                                <textarea name="app_update_msg" class="form-control" required>{{$setting[6]->rule_value}}</textarea>
                            </div>
                            <div class="form-group bt-switch col-md-6 m-t-20">
                                <label class="col-md-4">Mobile App Status</label>
                                <div class="col-md-3" style="float: right;">
                                <input type="checkbox"   {{ $setting->where('rule_name', 'mobile_app_status')->first()->rule_value == 'AC' ? 'checked' : '' }} data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="mobile_app_status" >
                               
                                </div>
                            </div>

                            {{-- <div class="form-group col-md-6 m-t-20">
                                <label>Address</label><br>
                                <textarea name="contact_address" class="form-control" required>{{$config->contact_address}}</textarea>
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Email Address</label><br>
                                <textarea name="contact_email" class="form-control" required>{{$config->contact_email}}</textarea>
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Contact Number</label><br>
                                <textarea name="contact_no" class="form-control" required>{{$config->contact_no}}</textarea>
                            </div> --}}
                            <div class="col-12 m-t-20">
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{URL::asset('/js/jquery-mask-as-number.js')}}"></script>
    <script>
        $(document).find(".decimalInput, .numberInput").on('keyup',function(e){

            if($(this).val().indexOf('-') >=0){
                $(this).val($(this).val().replace(/\-/g,''));
            }
        })

        $(document).find(".numberInput").maskAsNumber({receivedMinus:false});
        $(document).find(".decimalInput").maskAsNumber({receivedMinus:false,decimals:6});
        $('input[name=android_version_user]').val('{{$setting[5]->rule_value}}');
        $('input[name=ios_version_user]').val('{{$setting[2]->rule_value}}');
        $('input[name=referrer_amount]').val('{{$setting[7]->rule_value}}');


        

    </script>
   
@endpush