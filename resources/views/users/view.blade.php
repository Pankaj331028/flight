@extends('layouts.app')
@section('title', 'User - ' . ucfirst($user->first_name." ".$user->last_name))

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
                    <li class="breadcrumb-item active">View User</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-lg-4 col-xlg-3 col-md-5">
                <div class="card">
                    <div class="card-body">
                        @include('layouts.message')
                        <center class="m-t-30"> <img src="@if($user->profile_picture != null){{URL::asset('/uploads/profiles/'.$user->profile_picture)}} @else {{URL::asset('/images/user-gray.png')}} @endif" class="profilePic" />
                            <h4 class="card-title m-t-10 m-b-0">{{$user->first_name." ".$user->last_name}}</h4>
                        </center>
                    </div>
                    <div>
                        <hr> </div>
                    <div class="card-body"> <h6 class="p-t-20">Email address </h6>
                        <small class="text-primary font-15 db">{{$user->email}}</small> <h6 class="p-t-20">Phone</h6>
                        <small class="text-primary font-15 db">+{{$phonecode}} - {{$user->mobile_number}}</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-xlg-9 col-md-7">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#profile" role="tab">Profile</a> </li>
                        @if(Auth::guard('admin')->user()->user_role[0]->role!='user')
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li>
                        @endif

                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="profile" role="tabpanel">
                            <div class="card-body">
                                <div>
                                    <h6 class="p-t-20">User Registration Code</h6><small class="text-primary font-15 db">{{$user->user_code}}</small>
                                    <h6 class="p-t-20">Google Account</h6><small class="text-primary font-15 db">{{($user->google_signin==1)?'Connected':'Not Connected'}}</small>
                                    <h6 class="p-t-20">Facebook Account</h6><small class="text-primary font-15 db">{{($user->facebook_signin==1)?'Connected':'Not Connected'}}</small>
                                    <h6 class="p-t-20">Referral Code</h6><small class="text-primary font-15 db">{{$user->referral_code}}</small>
                                    @if(isset($user->user_referrer[0]->id))
                                        <h6 class="p-t-20">Referrer</h6><small class="text-primary font-15 db"><a href="{{route('viewUser',['id'=>$user->user_referrer[0]->id])}}" target="_blank">{{$user->user_referrer[0]->first_name." ".$user->user_referrer[0]->last_name}}</a></small>
                                    @endif
                                    <h6 class="p-t-20">Wallet Amount</h6><small class="text-primary font-15 db">{{$currency.$user->wallet_amount}}</small>
                                    <h6 class="p-t-20">Device Type</h6><small class="text-primary font-15 db">{{ucfirst($user->device_type)}}</small>
                                    <h6 class="p-t-20">Registered On</h6><small class="text-primary font-15 db">{{date('Y, M d', strtotime($user->created_at))}}</small>
                                    <h6 class="p-t-20">Verified</h6><small class="text-primary font-15 db">{{ucfirst(config('constants.CONFIRM.'.$user->is_verified))}}</small>
                                    <h6 class="p-t-20">Status</h6><small class="text-primary font-15 db">{{ucfirst(config('constants.STATUS.'.$user->status))}}</small>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="settings" role="tabpanel">
                            <div class="card-body">
                                <form class="form-horizontal form-material" method="post" action="{{route('changeStatusUser')}}">
                                    {{csrf_field()}}
                                    <div class="form-group bt-switch">
                                        <div class="col-md-6">
                                            <label class="col-md-6" for="val-block">Status</label>
                                            <input type="hidden" name="statusid" value="{{$user->id}}">
                                            <input type="hidden" name="status" value="{{$user->status}}">
                                            <div class="col-md-2" style="float: right;">
                                                <input type="checkbox" @if($user->status == 'AC') checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" id="statusBranch">
                                            </div>
                                        </div>


                                        <div class="col-md-6">
                                            <label class="col-md-6" for="val-block">Admin Verified </label>
                                            <input type="hidden" name="verifyid" value="{{$user->id}}">
                                            <input type="hidden" name="verify" value="{{$user->admin_verify}}">
                                            <div class="col-md-2" style="float: right;">
                                                <input type="checkbox" @if($user->admin_verify == 1) checked @endif data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="cverify" id="verifyBranch">
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

        <div>
            <div class="card">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs profile-tab" role="tablist">
                    <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#orders" role="tab">Orders</a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#favs" role="tab">Favorites</a> </li>
                    {{-- @if(Auth::guard('admin')->user()->user_role[0]->role!='user') --}}
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#wallet" role="tab">Wallet</a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#addresses" role="tab">Addresses</a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#ratings" role="tab">Ratings</a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#couponcodes" role="tab">Coupon Code</a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#invites" role="tab">User Invites</a> </li>
                    {{-- @endif --}}
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">

                    <div class="tab-pane active" id="orders" role="tabpanel">
                        <div class="card-body">
                            <div class="table-responsive m-t-40">
                                <table id="ordersTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Order No</th>
                                            <th>Total Amount (in {{session()->get('currency')}})</th>
                                            <th>Wallet Used</th>
                                            <th>Wallet Amount (in {{session()->get('currency')}})</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Order No</th>
                                            <th>Total Amount (in {{session()->get('currency')}})</th>
                                            <th>Wallet Used</th>
                                            <th>Wallet Amount (in {{session()->get('currency')}})</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @php
                                            $i=1;
                                        @endphp
                                        @foreach($user->orders as $value)
                                            <tr>
                                                <td>{{$i++}}</td>
                                                <td>{{$value->order_no}}</td>
                                                <td>{{$value->total_amount}}</td>
                                                <td>{{Config::get('constants.CONFIRM.'.$value->wallet)}}</td>
                                                <td>{{$value->credits_used}}</td>
                                                <td>{{Config::get('constants.STATUS.'.$value->status)}}</td>
                                                <td>&nbsp;&nbsp;&nbsp;<a href="{{route('viewOrder', ['id' => $value->id])}}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail" target="_blank"><i class="fa fa-eye"></i></a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="favs" role="tabpanel">
                        <div class="card-body">

                            <ul class="nav nav-tabs profile-tab" role="tablist">
                                <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#active_favs" role="tab">Active Favorites</a> </li>
                                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#inactive_favs" role="tab">Removed Favorites</a> </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane active" id="active_favs" role="tabpanel">
                                    <div class="table-responsive m-t-40">
                                        <table id="active_favsTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>S.No.</th>
                                                    <th>Product</th>
                                                    <th>Product Variation</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>S.No.</th>
                                                    <th>Product</th>
                                                    <th>Product Variation</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                @php
                                                    $i=1;
                                                @endphp
                                                @foreach($user->active_favs as $value)
                                                    <tr>
                                                        <td>{{$i++}}</td>
                                                        <td>{{$value->product->name}}</td>
                                                        <td>{{$value->product_variation->getSpecificationsAttribute()}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="inactive_favs" role="tabpanel">
                                    <div class="table-responsive m-t-40">
                                        <table id="inactive_favsTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>S.No.</th>
                                                    <th>Product</th>
                                                    <th>Product Variation</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>S.No.</th>
                                                    <th>Product</th>
                                                    <th>Product Variation</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                @php
                                                    $i=1;
                                                @endphp
                                                @foreach($user->inactive_favs as $value)
                                                    <tr>
                                                        <td>{{$i++}}</td>
                                                        <td>{{$value->product->name}}</td>
                                                        <td>{{$value->product_variation->getSpecificationsAttribute()}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="wallet" role="tabpanel">
                        <div class="card-body">
                            <div class="table-responsive m-t-40">
                                <table id="walletsTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Type</th>
                                            <th>Amount (in {{session()->get('currency')}})</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Type</th>
                                            <th>Amount (in {{session()->get('currency')}})</th>
                                            <th>Description</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @php
                                            $i=1;
                                        @endphp
                                        @foreach($user->wallet as $value)
                                            <tr>
                                                <td>{{$i++}}</td>
                                                <td>{{ucfirst($value->type)}}</td>
                                                <td>{{$value->amount}}</td>
                                                <td>{{$value->description}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="addresses" role="tabpanel">
                        <div class="card-body">
                            <div class="table-responsive m-t-40">
                                <table id="addressesTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Address Code</th>
                                            <th>Name</th>
                                            <th>Contact Number</th>
                                            <th>Apartment</th>
                                            <th>Address</th>
                                            <th>Zipcode</th>
                                            <th>City</th>
                                            <th>State</th>
                                            <th>Country</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Address Code</th>
                                            <th>Name</th>
                                            <th>Contact Number</th>
                                            <th>Apartment</th>
                                            <th>Address</th>
                                            <th>Zipcode</th>
                                            <th>City</th>
                                            <th>State</th>
                                            <th>Country</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @php
                                            $i=1;
                                        @endphp
                                        @foreach($user->user_addresses as $value)
                                            <tr>
                                                <td>{{$i++}}</td>
                                                <td>{{$value->address_code}}</td>
                                                <td>{{$value->first_name." ".$value->last_name}}</td>
                                                <td>{{'+'.$phonecode.'-'.$value->mobile}}</td>
                                                <td>{{$value->apartment}}</td>
                                                <td>{{$value->address}}</td>
                                                <td>{{$value->zipcode}}</td>
                                                <td>{{$value->acity->name}}</td>
                                                <td>{{$value->astate->name}}</td>
                                                <td>{{$value->acountry->name}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="ratings" role="tabpanel">
                        <div class="card-body">
                            <div class="table-responsive m-t-40">
                                <table id="ratingsTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Rating</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Rating</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @php
                                            $i=1;
                                        @endphp
                                        @foreach($user->rating as $value)
                                            <tr>
                                                <td>{{$i++}}</td>
                                                <td>{{$value->rating}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="couponcodes" role="tabpanel">
                        <div class="card-body">
                            <div class="table-responsive m-t-40">
                                <table id="couponCodesTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Coupon Code</th>
                                            <th>Order</th>
                                            <th>Discount</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Coupon Code</th>
                                            <th>Order</th>
                                            <th>Discount</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @php
                                            $i=1;
                                        @endphp
                                        @foreach($user->user_couponcode as $value)
                                            <tr>
                                                <td>{{$i++}}</td>
                                                <td>{{$value->coupon_code->coupon_code->code}}</td>
                                                <td>{{$value->order->order_no}}</td>
                                                <td>{{$value->coupon_code->value}}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="invites" role="tabpanel">
                        <div class="card-body">
                            <div class="table-responsive m-t-40">
                                <table id="referrersTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>User Referred</th>
                                            <th>Registered On</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>User Referred</th>
                                            <th>Registered On</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @php
                                            $i=1;
                                        @endphp
                                        @foreach($user->user_invites as $value)
                                            <tr>
                                                <td>{{$i++}}</td>
                                                <td>{{$value->user->first_name." ".$value->user->last_name}}</td>
                                                <td>{{date('d M Y', strtotime($value->created_at))}}</td>
                                                <td>{{Config::get('constants.STATUS.'.$value->status)}}</td>
                                                <td>&nbsp;&nbsp;&nbsp;<a href="{{route('viewUser', ['id' => $value->id])}}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail" target="_blank"><i class="fa fa-eye"></i></a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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

            $('#verifyBranch').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#verifyBranch").is(':checked'))
                    $('input[name=verify]').val(1);
                else
                    $('input[name=verify]').val(0);
            });

        });

        $('#ordersTable').DataTable({

            paging: true,
            pageLength: 10,
            // "bProcessing": true,
            "bLengthChange": false,
            "aaSorting": [],
            "bFilter": false
        });

        $('#walletsTable').DataTable({

            paging: true,
            pageLength: 10,
            // "bProcessing": true,
            "bLengthChange": false,
            "aaSorting": [],
            "bFilter": false
        });

        $('#addressesTable').DataTable({

            paging: true,
            pageLength: 10,
            // "bProcessing": true,
            "bLengthChange": false,
            "aaSorting": [],
            "bFilter": false
        });
        $('#ratingsTable').DataTable({

            paging: true,
            pageLength: 10,
            // "bProcessing": true,
            "bLengthChange": false,
            "aaSorting": [],
            "bFilter": false
        });
        $('#couponCodesTable').DataTable({

            paging: true,
            pageLength: 10,
            // "bProcessing": true,
            "bLengthChange": false,
            "aaSorting": [],
            "bFilter": false
        });
        $('#referrersTable').DataTable({

            paging: true,
            pageLength: 10,
            // "bProcessing": true,
            "bLengthChange": false,
            "aaSorting": [],
            "bFilter": false
        });
        $('#active_favsTable').DataTable({

            paging: true,
            pageLength: 10,
            // "bProcessing": true,
            "bLengthChange": false,
            "aaSorting": [],
            "bFilter": false
        });
        $('#inactive_favsTable').DataTable({

            paging: true,
            pageLength: 10,
            // "bProcessing": true,
            "bLengthChange": false,
            "aaSorting": [],
            "bFilter": false
        });
    </script>
@endpush
