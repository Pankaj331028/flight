<!----Add Address Modal --->
<div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="addaddressTitle"
aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header d-flex justify-content-center">
            <h5 class="modal-title font28 font-weight-bold color11 text-center" id="addaddressTitle">New Address
            </h5>
            <button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="updateAddress" method="Put" action="{{ url('api/updateUserAddress') }}">
                <input type="hidden" name="user_id" value="{{ $auth_user->id }}">
                <input type="hidden" name="address_id">
                <input type="hidden" id="latitude" name="lat" value="">
                <input type="hidden" id="longitude" name="lng" value="">
                <div class="form-row">

                <div class="form-group col-md-12">
                <select name="address_type" class="form-control height50" placeholder="Select" id="address_type">
                <option value="">Select Address Type  </option>
                <option value="Work">Work </option>
                <option value="Office">Office </option>
                <option value="Home">Home </option>
                <option value="Other">Other </option>
                </select>
                    <small class="help-block error text-danger address_type"></small>
                </div>

                    <div class="form-group col-md-6">
                        <input type="text" name="first_name" class="form-control height50" placeholder="First name*">
                        <small class="help-block error text-danger first_name"></small>
                    </div>
                    <div class="form-group col-md-6">
                        <input type="text" name="last_name" class="form-control height50" placeholder="Last name*">
                        <small class="help-block error text-danger last_name"></small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <input type="text" name="mobile" class="form-control height50" placeholder="Contact No*">
                        <small class="help-block error text-danger mobile"></small>
                    </div>
                    <div class="form-group col-md-12">
                        <input type="text" name="apartment" class="form-control height50" placeholder="House No*">
                        <small class="help-block error text-danger apartment"></small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <input type="text" name="address" class="form-control height50" placeholder="Address*">
                        <small class="help-block error text-danger address"></small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <div id="map" class="w-100 map300"></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <select name="country" class="form-control height50 country" readonly>
                        <option value=""> Select Country </option>
                        @if(isset($countries))
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" selected>{{ $country->name }}</option>
                            @endforeach
                        @endif
                        </select>
                        <small class="help-block error text-danger country"></small>
                    </div>


                    <div class="form-group col-md-6">
                        <select name="state" class="form-control height50 state">
                        <option value=""> Select State </option>

                            @if(isset($stateList))
                                @foreach ($stateList as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <small class="help-block error text-danger state"></small>
                    </div>
                </div>

                @php
                use App\Model\City;
                    $d = $stateList[0] ?? '';
                    $city = City::where('state_id', $d->id ?? '')->get();
                @endphp


                <div class="form-row">
                    <div class="form-group col-md-6">
                        <select name="city" class="form-control height50 city">
                        <option value=""> Select City </option>
                        @if(isset($city))
                                @foreach ($city as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <small class="help-block error text-danger"></small>
                    </div>
                     <div class="form-group col-md-6">
                        <input type="text" name="zipcode" class="form-control height50" placeholder="Zip Code*">
                        <small class="help-block error text-danger zipcode"></small>
                    </div>
                </div>
                <div class="modal-footer border-top-0 d-flex justify-content-center">
                    <button type="submit" class="saveAddress font18 fontSemiBold colorWhite bgTheme radius50 borderNone px-5 py-2 hover1">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<!----Change Cart Address Modal --->
@if((!($user->user_addresses->isEmpty()))))
<div class="modal fade" id="changeAddress" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-center">
                <h5 class="modal-title font28 font-weight-bold color11 text-center" id="addaddressTitle">Change Address
                </h5>
                <a href="javascript:;" class="closebtn" data-dismiss="modal" aria-label="Close">
                    <i class="allinone close"></i>
                </a>
            </div>
            <div class="modal-body m-10">
                <form method="POST" action="{{ url('update-address') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="user_id" value="{{ $auth_user->id }}">
                    <input type="hidden" name="user_id" value="{{ $auth_user->id }}">
                    <input type="hidden" name="cart_id" value="{{ $item->cart_id }}">

                    @if(isset($user->user_addresses) && count($user->user_addresses) > 0)
                    @foreach($user->user_addresses as $user_address)
                    <div class="row">
                        <div class="col-md-12">
                             <div class="custom-control custom-checkbox mb-3">
                            <input type="radio" @if($cart_address->id==$user_address->id) checked @endif id="adrss-{{ $user_address->id }}" class="custom-control-input" name="address_id" value="{{ $user_address->id }}">
                            <label for="adrss-{{ $user_address->id }}" class="custom-control-label font-weight-bold mr-5">{{ $user_address->apartment ?? '' .' '.$user_address->address ?? '' .', '.  $user_address->acity->name ?? '' .', '. $user_address->acountry->name ?? '' .' ('. $user_address->zipcode .')' }}</label>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif

                    <div class="loginBtnBlock d-flex justify-content-center mt-4">
                        <button type="submit" class="font18 colorWhite fontSemiBold radius50 bgTheme px-5 py-2 borderNone hover1 text-center">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
