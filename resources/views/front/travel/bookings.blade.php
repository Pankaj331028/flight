@extends('front.travel.layouts.master')
@section('template_title','My Bookings')
@section('content')
	<style type="text/css">
		.invalid-feedback{
			color: white;
		    font-size: 14px;
		    margin-top: 0;
		    background: #ff00009e;
		    padding: 1px 10px;
		}
		input[type="number"] {
		  -webkit-appearance: textfield;
		     -moz-appearance: textfield;
		          appearance: textfield;
		}
		input[type=number]::-webkit-inner-spin-button,
		input[type=number]::-webkit-outer-spin-button {
		  -webkit-appearance: auto;
		  appearance: auto;
		  -moz-appearance: auto;
		}
	</style>
	<div id="page-wrapper">
		<section class="wrapper2 render-QG quickGrab bgf7 py-5">
			<div class="container mobile-space-none">
				<div class="row listingProdGrid hotel-filter-details">
					<div class="col-lg-3 mb-4">
						<div class="fliter_box_inner">
						    <div class="row">
						        <div class="col-12">
						            <div class="filter_box box_css booking-filter">
						            	<ul class="nav nav-pills nav-wizard" id="tabs">
						            		{{-- <li>
						            			<a href="#confirmed" class="active show" id="step1" data-toggle="tab">Confirmed Booking</a>
						            		</li> --}}
						            		<li>
						            			<a href="#bookings" class="active show" id="step2" data-toggle="tab">My Bookings</a>
						            		</li>
						            		<li>
						            			<a href="#account" id="step3" data-toggle="tab">Account Settings</a>
						            		</li>
						            		<li>
						            			<a href="#change" id="step4" data-toggle="tab">Change Password</a>
						            		</li>
						            	</ul>
						            </div>
						        </div>
						    </div>
						</div>
					</div>
					<div class="col-lg-9 mb-4 near-by-hotels">

		                <div class="alert d-none mb-5">
		                    <ul>
		                        <li class="alertMsg"></li>
		                    </ul>
		                </div>
						<div class="tab-content">
							{{-- <div class="tab-pane active" id="confirmed">
								<div class="row">
									<div class="col-md-5">
										<h4>Confirmed Bookings ({{count($confirmed)}})</h4>
									</div>
									<div class="col-md-7 d-flex align-items-center justify-content-around">
										<span>Search By Booking #</span>
										<input type="text" placeholder="" name="confirm_search" class="form-control w-50 d-inline-block" />
                    					<div class="category-breadcrumb d-none">
											<a href="javascript:;" class="clearSearch">Clear</a>
										</div>
									</div>
								</div>
								<div id="confirmedlist">
									@include('front.travel.confirmed-bookinglist',['confirmed'=>$confirmed,'currency'=>$currency])
								</div>
							</div> --}}
							<div class="tab-pane active" id="bookings">

								<div class="row">
									<div class="col-md-5">
										<h4>Bookings ({{count($bookings)}})</h4>
									</div>
									<div class="col-md-7 d-flex align-items-center justify-content-around">
										<span>Search By Booking #</span>
										<input type="text" placeholder="" name="search" class="form-control w-50 d-inline-block" />
                    					<div class="category-breadcrumb d-none">
											<a href="javascript:;" class="allclearSearch">Clear</a>
										</div>
									</div>
								</div>
								<div id="bookinglist" class="mt-3">
									@include('front.travel.all-bookinglist',['bookings'=>$bookings,'currency'=>$currency])
								</div>
							</div>
							<div class="tab-pane" id="account">
								<div class="clientProfile d-flex align-items-center mt-sm-0 mt-4">
                                    <div class="userProfileImg">
                                        <form action="{{ url('api/changeProfilePic') }}" id="updatePic" method="Post" enctype="multipart/form-data">
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
							<div class="tab-pane" id="change">
								<div class="chngePwd mt-sm-0 mt-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <form id="changePassword" action="{{ url('api/changePassword') }}">
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
		</section>
	</div>
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
	@include('front.travel.layouts.partials.partial-address-script')
	<script type="text/javascript">
		$(document).ready(function(){
			$('[name=confirm_search]').on('change input',function(){
				if($(this).val()!=''){
					$('.clearSearch').closest('div').removeClass('d-none');

					$.ajax({
						type:'get',
						url:"{{route('get-bookings')}}/confirmed",
						data:{search:$('[name=confirm_search]').val()},
						success:function(data){
							$('#confirmedlist').html(data);
						}
					})
				}
				else{
					$('.clearSearch').closest('div').addClass('d-none');
				}
			})

			$('.clearSearch').click(function(){
				$(this).closest('.tab-pane').find('input').val('');
				$('.clearSearch').closest('div').addClass('d-none');

				$.ajax({
					type:'get',
					url:"{{route('get-bookings')}}/confirmed",
					data:{search:$('[name=confirm_search]').val()},
					success:function(data){
						$('#confirmedlist').html(data);
					}
				})
			})
			$('[name=search]').on('change input',function(){
				if($(this).val()!=''){
					$('.allclearSearch').closest('div').removeClass('d-none');

					$.ajax({
						type:'get',
						url:"{{route('get-bookings')}}",
						data:{search:$('[name=search]').val()},
						success:function(data){
							$('#bookinglist').html(data);
						}
					})
				}
				else{
					$('.allclearSearch').closest('div').addClass('d-none');
				}
			})

			$('.allclearSearch').click(function(){
				$(this).closest('.tab-pane').find('input').val('');
				$('.allclearSearch').closest('div').addClass('d-none');

				$.ajax({
					type:'get',
					url:"{{route('get-bookings')}}",
					data:{search:$('[name=search]').val()},
					success:function(data){
						$('#bookinglist').html(data);
					}
				})
			})
		})
	</script>
@endsection