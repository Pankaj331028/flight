@extends('front.layouts.master')
@section('template_title','My Profile')
@section('content')
    <style>
        button#save_btn {
            display: none;
        }
        button.btn.btn-primary.b-2 {
            margin-bottom: 20px;
        }
    </style>






    <section class="wrappernotification1 my-5">
        <div class="container">
            <div class="font35 font-weight-bold color11 text-center pageTitle">My Account</div>
            <div class="acountTabing mt-md-5 mt-3">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="nav flex-sm-column nav-pills" id="v-pills-tab" role="tablist"
                            aria-orientation="vertical">
                            <a class="nav-link font16 fontSemiBold text-capitalize active" id="v-pills-profile-tab"
                                data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile"
                                aria-selected="false">Profile</a>
                            <a class="nav-link font16 fontSemiBold text-capitalize" id="v-pills-orders-tab"
                                data-toggle="pill" href="#v-pills-orders" role="tab" aria-controls="v-pills-orders"
                                aria-selected="true">orders</a>
                            <a class="nav-link font16 fontSemiBold text-capitalize" id="v-pills-address-tab"
                                data-toggle="pill" href="#v-pills-address" role="tab" aria-controls="v-pills-address"
                                aria-selected="false">address</a>
                            <a class="nav-link font16 fontSemiBold text-capitalize" id="v-pills-wallet-tab"
                                data-toggle="pill" href="#v-pills-wallet" role="tab" aria-controls="v-pills-wallet"
                                aria-selected="false">wallet</a>
                            <a class="nav-link font16 fontSemiBold text-capitalize" id="v-pills-changepassword-tab"
                                data-toggle="pill" href="#v-pills-changepassword" role="tab"
                                aria-controls="v-pills-changepassword" aria-selected="false">change password</a>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-12">
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel"
                                aria-labelledby="v-pills-profile-tab">
                                <div class="clientProfile d-flex align-items-center mt-sm-0 mt-4">
                                    <div class="userProfileImg">
                                        <form action="{{ url('api/changeProfilePic') }}" id="updatePic" method="POST" enctype="multipart/form-data">
                                            <div class="avatar-upload">
                                                <div class="avatar-edit">
                                                    <input type='file' name="profile_picture" id="imageUpload" accept=".png, .jpg, .jpeg" />
                                                    <label for="imageUpload"></label>
                                                </div>
                                                <div class="avatar-preview">
                                                    <div id="imagePreview" style="background-image: url(<?=!empty($user->profile_picture) ? 'uploads/profiles/' . $user->profile_picture : 'uploads/profiles/no_avatar.jpg'?>);">
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-success ml-4" id="save_btn"> Save </button>
                                        </form>
                                    </div>

                                    <div class="profileEditPart d-flex flex-sm-row flex-column justify-content-between w-100">
                                        <ul class="ml-5">
                                            <li class="font16 color20 fontsemibold d-flex align-items-center mb-2">
                                                <i class="fa fa-user" aria-hidden="true"></i>{{ $user->fullname }}
                                            </li>
                                            <li class="font16 color20 fontsemibold d-flex align-items-center mb-2">
                                                <i class="fa fa-phone" aria-hidden="true"></i>{{ (!empty($user->mobile_number)) ? $user->mobile_number : '-' }}</li>
                                            <li class="font16 color20 fontsemibold d-flex align-items-center mb-2">
                                                <i class="fa fa-envelope" aria-hidden="true"></i>{{ $user->email }}</li>
                                        </ul>
                                        <div class="editBtn ml-sm-0 ml-5 mt-sm-0 mt-2">
                                            <a class="font16 colorTheme border1 radius50 py-1 px-5 py-sm-2" href="javascript:;" data-toggle="modal" data-target="#editProfileModal">Edit</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- -User order details --}}
                            <div class="tab-pane fade" id="v-pills-orders" role="tabpanel" aria-labelledby="v-pills-orders-tab">
                                <div class="orderStatus mt-sm-0 mt-4">
                                @forelse ($items as $item)
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-3">
                                        <div class="">
                                        <a href="javascript:;" class="font16 fontSemiBold color20 bgNone borderNone">
                                            <p class="mb-0">Order No :<span class="font16 ml-2">{{ $item->order_no }}</span></p>
                                            <p class="mb-0">From : <span class="font16 ml-2">{{ $item->duration }}</span></p>
                                        </a>
                                        <div class="editBtn my-3">
                                            <a class="font16 colorTheme border1 radius50 px-3 py-1" href="{{ url('order-detail/'.$item->id) }}">More Details</a>
                                        </div>
                                        </div>
                                        <div class="editBtn">
                                            <p class="mb-0"> <span class="font14 font-weight-bold ml-2">{{ $item->wallet_show ? $currency.$item->wallet_amount : ''}}</span></p>
                                        </div>
                                    </div>
                                  @empty
                                    <h5 class="font18 font-weight-bold">No order found</h5>
                                  @endforelse
                                </div>
                            </div>
                            <div class="tab-pane fade" id="v-pills-address" role="tabpanel"
                                aria-labelledby="v-pills-address-tab">

                                <button type="button" class="btn btn-primary b-2" data-toggle="modal" data-target="#exampleModal">
                                     Remove Address
                                </button>


                                <div class="addressBlock">
                                    <div class="row">
                                        @if(isset($user_addresses))
                                            @foreach ($user_addresses as $key => $user_address)
                                            <?php
$address = $user_address->apartment . ' ' . $user_address->address . ', ' . $user_address->city . ', ' . $user_address->country . ' (' . $user_address->zipcode . ')';
?>
                                            <div class="col-md-6">
                                                <div class="diffAddres mt-sm-0 mt-4 mb-3">
                                                    <div class="adresEdit d-flex align-items-center justify-content-between mb-2">
                                                        <p class="font16 fontSemiBold color20 mb-0">Address {{$key+1}} </p><br>
                                                        <a href="javascript:;"><i class="allinone edit"></i></a>
                                                    </div>
                                                    <p class="font16 fontSemiBold color20 mb-0"> <strong> {{$user_address->address_type ?? '-'}} </strong> </p>
                                                    <p class="font16 color36">{{ $address }}</p>
                                                        <div class="favtag res_acc">
                                                            <p class="font16 fontsemibold mb-0">
                                                                <a href="javascript:void(0)" class="editAddress" data-toggle="modal" data-target="#addressModal" data-value="{{ $user_address }}">
                                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                                </a>
                                                            </p>
                                                        </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif
                                        <div class="col-md-6">
                                            <div class="diffAddres addAddress d-flex justify-content-center align-items-center mt-sm-0 mt-4">
                                                <a href="javascript:;" class="">
                                                    <img src="{{ URL::asset('/front/images/plus.png') }}">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="v-pills-wallet" role="tabpanel"
                                aria-labelledby="v-pills-wallet-tab">
                                <div class="walletBalance mt-sm-0 mt-4">
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                        <p class="mb-0 font18 color20 fontSemiBold">Current Balance</p>
                                        <p class="mb-0 font22 font-weight-bold colorTheme">{{ $wallet_balance }}</p>
                                    </div>
                                    @forelse($user_wallet as $wallet)
                                    <p class="font16 color20 font-weight-bold my-3">{{ $currency.''.$wallet['amount'] }}</p>
                                    <div class="borderBottom1">
                                        <p class="font16 color20 font-weight-bold mb-2"> @if($wallet['type']!=='refer') Order No: @else Referral Code @endif<span class="font16 color20 ml-1 font-weight-normal">{{ $wallet['code'] }}</span></p>
                                        <p class="font16 color20 font-weight-bold mb-2">Date:<span class="font16 color20 ml-1 font-weight-normal">{{ $wallet['date'] }}</span></p>
                                        <p class="font16 color20 font-weight-bold mb-2">Time:<span class="font16 color20 ml-1 font-weight-normal">{{ $wallet['time'] }}</span></p>
                                    </div>
                                    @empty
                                    @endforelse
                                </div>
                            </div>
                            <div class="tab-pane fade" id="v-pills-changepassword" role="tabpanel"
                                aria-labelledby="v-pills-changepassword-tab">
                                <div class="chngePwd mt-sm-0 mt-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <form id="changePassword" action="{{ url('api/changePassword') }}" method="POST">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                <div class="form-group">
                                                    <input type="password" name="password" class="form-control height50" placeholder="Current Password*">
                                                    <small class="help-block error text-danger"></small>
                                                </div>
                                                <div class="form-group">
                                                    <input type="password" name="new_password" class="form-control height50" placeholder="New Password*">
                                                    <small class="help-block error text-danger"></small>
                                                </div>
                                                <div class="form-group">
                                                    <input type="password" name="confirm_password" class="form-control height50" placeholder="Confirm Password*">
                                                    <small class="help-block error text-danger"></small>
                                                </div>
                                                <div class="loginBtnBlock d-flex  mt-4">
                                                    <button type="submit" class="font18 colorWhite fontSemiBold radius50 bgTheme px-5 py-2 borderNone hover1 text-center">Save</button>
                                                </div>
                                            </form>
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
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"> Remove Address </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="{{route('removeAddress')}}" method="POST">
      <div class="modal-body">
      <select class="selectpicker" multiple data-live-search="true" name="address_id[]" id="removeAddress_input">
        <option value="" disabled>  Select Address  </option>
        @if(isset($user_addresses))
        @foreach ($user_addresses as $key => $user_address)
        @php
        $address = $user_address->apartment . ' ' . $user_address->address . ', ' . $user_address->city . ', ' . $user_address->country . ' (' . $user_address->zipcode . ')';
        @endphp
        <option value="{{$user_address->id}}">  {{$address}} </option>
        @endforeach
        @endif
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"> Close </button>
        <button type="button" class="btn btn-primary"  id="removeAddress"> Delete  </button>
      </div>
    </form>

    </div>
  </div>
</div>
    <!-- edit Profile Modal -->
    <div class="modal fade" id="editProfileModal"  tabindex="-1" role="dialog" aria-labelledby="editProfileModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <h5 class="modal-title font28 font-weight-bold color11 text-center" id="editProfileModalTitle">Edit
                        Profile</h5>
                    <button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('api/updateProfile') }}" id="updateProfile" method="POST">
                        <input type="hidden" name="user_id" id="user_id" value="{{ $auth_user->id }}">
                        <input type="hidden" name="type" value="web">




                        <div class="row">
                            <div class="form-group col-md-6">
                                <input name="first_name" value="{{ $user->first_name }}" type="text" class="form-control height50" placeholder="First name*">
                                <small class="help-block error text-danger"></small>
                            </div>
                            <div class="form-group col-md-6">
                                <input name="last_name" value="{{ $user->last_name }}" type="text" class="form-control height50" placeholder="Last name*">
                                <small class="help-block error text-danger"></small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12 position-relative">
                                <input name="email" value="{{ $user->email }}" type="email" class="form-control height50" placeholder="Enter email" disabled>
                                <div class="check_email">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <input name="mobile_number" value="{{ $user->mobile_number }}" type="text" class="form-control height50" placeholder="Contact No">
                                <small class="help-block error text-danger"></small>
                            </div>
                        </div>
                        <div class="loginBtnBlock d-flex justify-content-center mt-4">
                            <button type="submit" class="font16 colorWhite fontSemiBold radius50 bgTheme px-5 py-2 borderNone hover1 text-center">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('front.modals.address')

@endsection
@include('front.partial-address-script')