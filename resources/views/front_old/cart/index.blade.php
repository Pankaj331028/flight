@extends('front.layouts.master')
@section('template_title','My Cart')
@section('content')
@php
use App\Model\CouponCode;
$data = CouponCode::whereRaw("? between start_date and end_date", [date('Y-m-d')])->where('status', 'AC')->get();
@endphp


<style>
.error{
    color: red;
}
    </style>

@php
if(!isset($wallet)){
    $wallet = 0;
}

if(!isset($payment_key)){
    $payment_key  = 0;
}

if(!isset($total_amount)){
    $total_amount  = 0;
}

if(!isset($shipping_fee)){
    $shipping_fee  = 0;
}


if(!isset($coupon_discount)){
    $coupon_discount  = 0;
}


if(!isset($credits_used )){
    $credits_used   = 0;
}


if(!isset($grand_total )){
    $grand_total   = 0;
}


if(!isset($savings )){
    $savings   = 0;
}

if(!isset($user )){
    $user   ='';
}
$cart_id=0;
@endphp

    @if(isset($usercartsitem))
        <section class="cartwrapper1 my-5">
            <div class="container">
                <div class="font35 font-weight-bold color11 text-center pageTitle">My Cart</div>
                @foreach ($usercartsitem as $item)
                @php
                $cart_id=$item->cart_id;
                @endphp
                <input type="hidden" id="user_id" value="{{ $auth_user->id }}">
                <input type="hidden" id="cart_id" value="{{ $item->cart_id }}">
                <div class="row mt-sm-5 mt-3 align-items-center border2 rounded p-3 scheduleRow">
                    <div class="col-md-7">
                        <div class="cartProd">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="cartProdImg">


                                        <?php
$str = public_path(substr($item->product_medium_image, strpos($item->product_medium_image, 'uploads')));

if (File::exists($str)) {
    $medium_image = $item->product_medium_image;
} else {
    $medium_image = $item->product_medium_image;
}

?>

                                        <img src="{{ $medium_image }}" alt="">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="cartProdDetail mb-md-0 mb-3">
                                        <p class="font18 fontSemiBold color20 mb-1">{{ $item->product_name }}</p>
                                        <!-- <p class="font16 color36 mb-1">#</p> -->
                                        <p class="font16 color20 fontsemibold mb-1 d-flex align-items-center">
                                            @if($item->price == $item->special_price || $item->special_price=='0')
                                            <span>{{ $currency }} {{ $item->price }}</span>
                                            @else
                                            <span>{{ $currency }} {{ $item->special_price }}</span>
                                            <strike class="colorb7 ml-2">{{ $currency }} {{ $item->price }}</strike>
                                            @endif
                                        </p>
                                        <div class="addBtn justify-content-center d-block dyna_btn_wrapper text-center">
                                            <div class="input_number_form input-group text_icon" id="count-{{ $item->product_variation_id }}">
                                                <div class="value-button btn-minus" data-product="{{ $item->product_id }}" onclick="addToCartProduct(this,{{ $item->product_variation_id }},'minus')">-</div>
                                                <span class="number_input col-md-2 cart_qty" id="qty-{{ $item->product_id.$item->product_variation_id }}" >{{ $item->qty }}</span>
                                                <div class="value-button btn-plus" data-product="{{ $item->product_id }}" onclick="addToCartProduct(this,{{ $item->product_variation_id }},'plus',null,{{ $item->max_qty }})">+</div>
                                            </div>
                                        </div>




                                        <div class="favtag">
                                            <p class="font20 fontsemibold mb-0 p-0">
                                                <a class="{{ $item->is_favorite==1 ? 'favdark' : '' }}" href="javascript:void(0)" data-product="{{ $item->product_id }}" onclick="addToFavourite(this,{{ $item->product_variation_id }},'fav')">
                                                    <i class="fa fa-heart" aria-hidden="true"></i>
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="cartSchedule">
                            <div class="font18 color36 fontSemiBold mb-3">Select no. of days, you want this product</div>
                            <div class="d-flex justify-content-between">
                              <div class="custom-control custom-radio">
                                  <input type="radio" @if($item->delivery_slot_type=="single") checked @endif name="days{{ $item->id }}" id="singleDay-{{ $item->id }}" class="custom-control-input " onclick="scheduleDelivery('single',{{ $item->id }})">
                                  <label class="custom-control-label font14 label_weight_text color20" for="singleDay-{{ $item->id }}">Same Day</label>
                              </div>
                              <div class="custom-control custom-radio">
                                  <input type="radio" @if($item->delivery_slot_type=="multiple") checked @endif name="days{{ $item->id }}" id="multipleDay-{{ $item->id }}" class="custom-control-input" onclick="scheduleDelivery('multiple',{{ $item->id }})">
                                  <label class="custom-control-label font14 label_weight_text color20" for="multipleDay-{{ $item->id }}">Scheduled Delivery</label>
                              </div>
                            </div>
                            <div class="delivery_sche my-3">
                              <p class="mb-0"><b>Schedule Delivery : </b>
                                <span class="font16 ml-2 date-{{ $item->id }}">
                                    {{ Carbon\Carbon::parse($item->start_date)->format('d M, Y') }} To {{ Carbon\Carbon::parse($item->end_date)->format('d M, Y') }}
                                </span>
                            </p>
                              <p class="mb-0"> <b>Delivery Slot : </b><span class="font16 ml-2 time-{{ $item->id }}">{{ $item->delivery_slots }}</span></p>
                              <div class="icon_edit_manage">
                                <a href="#" onclick="scheduleDelivery('{{ $item->delivery_slot_type }}','{{ $item->id }}')">
                                  <i class="fa fa-pencil" aria-hidden="true"></i>
                                </a>

                              </div>
                            </div>
                            <div class="scheduleClose">
                                <a href="javascript:;" class="allinone close" onclick="deleteCartItem({{ $item->id }})">
                                    <img src="{{ asset('/front/images/close-icon.png') }}">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
        </section>

        <section class="cartWarpper2">
            <div class="container borderTop1 mt-sm-5 mt-3 pt-sm-5 pt-3 px-md-0">
                <div class="font18 fontSemiBold color20 mb-4">Delivery Address</div>
                <div class="row">
                    @if(isset($cart_address))
                    <div class="col-md-4">
                        <div class="diffAddres mb-md-0 mb-2">
                            <div class="adresEdit d-flex align-items-center justify-content-between mb-2">
                                <p class="font16 fontSemiBold color20 mb-0">Address 1</p>
                                <a href="javascript:;"><i class="allinone edit"></i></a>
                            </div>
                            <p class="font14 color36">{{ $cart_address->apartment .' '.$cart_address->address .', '.  $cart_address->city .', '. $cart_address->country .' ('. $cart_address->zipcode .')'}}</p>
                            <div class="favtag">
                                <p class="font20 fontsemibold mb-0">
                                    <a href="javascript:void(0)" data-value="{{ json_encode($cart_address) }}" data-toggle="modal" data-target="#addressModal" class="editAddress">
                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-4">
                        <div class="diffAddres addAddress d-flex justify-content-center align-items-center mb-md-0 mb-2" data-toggle="modal" data-target="#addressModal">
                            <a class="" href="javascript:;">
                                <img src="{{ URL::asset('/front/images/plus.png') }}">
                            </a>
                        </div>
                    </div>
                    @if(isset($user) &&  !($user->user_addresses->isEmpty()))
                    <div class="col-md-4">
                        <div class="editBtn d-flex justify-content-end">
                            <a class="font16 colorTheme border1 radius50 px-4 py-1 changeAddress" href="javascript:;">Change</a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="cartWrapper3">
            <div class="container borderTop1 mt-sm-5 mt-3 pt-sm-5 pt-3 px-md-0">
                <div class="row">
                    <div class="col-md-6 my-1">
                        <div class="border_manage_cart border-right mb-md-0 mb-3">
                        <div class="font20 fontSemiBold colorTheme mb-4">Apply Coupon</div>
                            <div class="row">
                                <div class="col-sm-8 my-1">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="coupon_code"
                                            placeholder="Apply Code">
                                        {{-- <div class="input-group-prepend">
                                            <input type="hidden" id="code">
                                            <button class="applyCoupon input-group-text">Apply</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row codeBlock">
                                <div class="couponCodeApplied ml-3 mt-2 p-1 col-md-6 bg-warning"></div>
                                <div class="col-md-4 d-flex align-items-center">
                                    <button class="close" onclick="removeCoupon()">
                                        <img src="{{ asset('/front/images/close-icon.png') }}">
                                    </button>
                                </div> --}}
                                <div class="input-group-prepend">
                                    <input type="hidden" id="code" value=@if(isset($code)) {{ $code->id }} @endif>
                                    <button class="applyCoupon input-group-text">Apply</button>
                                </div>
                            </div>

                        </div>
                    </div>



                    <div class="font20 fontSemiBold colorTheme mt-4  mb-2">Coupons </div>
                    <table class="table table-hover">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col"> Code </th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(isset($data) && count($data) > 0)
                        @foreach($data as $key=> $item)
                        <tr class="table_row">
                            <input type="hidden" name="coupon_code" value="{{$item->code}}" class="coupon_code">
                        <th scope="row"> {{$key+1}}  </th>
                        <td> {{$item->code}} </td>
                        </tr>
                        @endforeach
                       @endif
                    </tbody>
                    </table>


                    <div class="row codeBlock" @if(isset($code)) style="display:flex"; @endif>
                        <div class="couponCodeApplied ml-3 mt-2 p-1 col-8 col-sm-8 col-md-6 bg-warning">@if(isset($code)) {{ $code->code }} @endif</div>
                        <div class="col-2 col-sm-2 col-md-4 d-flex align-items-center">
                            <button class="close" onclick="removeCoupon()">
                                <img src="{{ asset('/front/images/close-icon.png') }}">
                            </button>
                        </div>

                        </div>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" {{ $wallet==0 ? 'disabled' : '' }} class="useWallet custom-control-input" id="credits" name="credit" {{ $payment_key=='wallet' ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold mr-5" for="credits">Use omrbranch Credits</label>
                            <span class="font-weight-bold font16">{{ $currency }}{{ $wallet ? $wallet: '0' }}</span>
                          </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cartWrapper4">
            <div class="container borderTop1 mt-sm-5 mt-3 pt-sm-5 pt-3 px-md-0">
                <div class="row">
                    <div class="col-md-6">
                        <div class="paymentMethod">
                            <div class="font20 fontSemiBold color20 mb-4">  Payment Method </div>
                          <form id="form_validate">
                            <div class="col-md-6">
                            <select class="form-control" aria-label="Default select example" id="payment_type">
                                <option value="cod"> Cash On Delivery </option>
                                <option value="debit_card"> Debit Card </option>
                                <option value="credit_card"> Credit Card </option>
                            </select>
                            </div>

                         <div class="hidden_section" id="hidden_section">
                            <div class="col-md-6 form-group">
                                <ul class="mt-2">
                                    <li class="mb-2">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="master_card" name="card_type" class="custom-control-input" value="master">
                                            <label class="custom-control-label font14 color20" for="master_card"> Master </label>
                                        </div>
                                    </li>

                                    <li class="mb-2">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="visa_card" name="card_type" class="custom-control-input" value="visa">
                                            <label class="custom-control-label font14 color20" for="visa_card"> Visa </label>
                                        </div>
                                    </li>


                                    <li class="mb-2">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="amex_card" name="card_type" class="custom-control-input" value="amex">
                                            <label class="custom-control-label font14 color20" for="amex_card"> Amex </label>
                                        </div>
                                    </li>

                                    <li class="mb-2">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="discover" name="card_type" class="custom-control-input" value="discover">
                                            <label class="custom-control-label font14 color20" for="discover"> Discover </label>
                                        </div>
                                    </li>
                                </ul>
                            </div>


                            <div class="mt-2">
                               <div class="col-md-8 form-group">
                                 <input type="text" class="form-control mt-2" name="card_no" placeholder="Card Number" maxlength="16">
                                </div>

                            <div class="row mt-2">

                            <div class="col-md-4 form-group">
                                <select class="form-control" aria-label="Default select example" name="month" id="month">
                                <option selected value=""> Select Month </option>
                                <option value="January"> January </option>
                                <option value="February"> February </option>
                                <option value="March"> March </option>
                                <option value="April"> April </option>
                                <option value="May"> May </option>
                                <option value="June"> June </option>
                                <option value="July"> July </option>
                                <option value="August"> August </option>
                                <option value="September"> September </option>
                                <option value="October"> October </option>
                                <option value="November"> November</option>
                                <option value="December"> December</option>
                                </select>
                            </div>

                            <div class="col-md-4 form-group">
                                <select class="form-control" aria-label="Default select example" id="year" name="year">
                                    <option selected value=""> Years  </option>
                                    @for ($i = date('Y'); $i <= date('Y', strtotime('+10 year')); $i++)
                                        <option value="{{$i}}"> {{$i}} </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-4 form-group">
                              <input type="password" class="form-control" name="cvv" placeholder="CVV" maxlength="4">
                            </div>
                          </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cartWrapper5">
            @include('front.cart.partial-cart_details')
            <div class="placeOrderBtn d-flex justify-content-center mt-sm-5 mt-3 mb-5">
                <button id="placeOrder" class="font22 colorWhite fontSemiBold radius50 bgTheme px-5 py-2 borderNone hover1 text-center" @if(count($usercartsitem) <=0) style="cursor: not-allowed;" disabled @endif>Place
                    Order</button>
                <img class="loader" src="{{ asset('/front/images/loading.gif') }}" alt="loading..." style='display: none;'>
            </div>
            </form>
        </section>

        @include('front.modals.schedule')
        @include('front.modals.address')



    @else
    <section class="cartwrapper1 my-5">
        <div class="container">
            <div class="font35 font-weight-bold color11 text-center pageTitle">My Cart</div>
            <div class="container text-center mt-5"><p class="font18 color11">Oops! Looks like you don't have any product in your cart.</p></div>
        </div>
    </section>
    @endif


    <script>
        $('document').ready(function(){
            $('#payment_type').on('change', function(){
            var value =    $('#payment_type').val();
            if(value != 'cod'){
                $('.hidden_section').removeClass('hidden_section');
            }else{
                $('#hidden_section').addClass('hidden_section');
            }
            });
        });
    </script>


<script>
    $(document).ready(function(){
    $(".table_row").dblclick(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'post',
            url: "{{ url('applyCouponCode') }}",
            data: {
                code    :  $(this).find('.coupon_code').val(),
                cart_id : $('#cart_id').val(),
            },
            dataType: "json",
            success : function(data){
                if(data.status==200){
                    $('.cart_details').empty().html(data.html);
                    $('.codeBlock').css('display','flex');
                    $('.couponCodeApplied').html(data.code.code);
                    $('.coupon_code').val('');
                    $('#code').val(data.code.id);

                    toastr.success(data.message,"Status",{
                        timeOut: 5000,
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": true,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": true,
                        "tapToDismiss": true
                    });
                }else{
                    toastr.error(data.message,"Status",{
                        timeOut: 5000,
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": true,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": true,
                        "tapToDismiss": true
                    });
                }
            }
        })
    });

    document.oncontextmenu = function() {return false;};
    $('.table_row').mousedown(function(e){
    if( e.button == 2 ) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'post',
            url: "{{ url('applyCouponCode') }}",
            data: {
                code    :  $(this).find('.coupon_code').val(),
                cart_id : $('#cart_id').val(),
            },
            dataType: "json",
            success : function(data){
                if(data.status==200){
                    $('.cart_details').empty().html(data.html);
                    $('.codeBlock').css('display','flex');
                    $('.couponCodeApplied').html(data.code.code);
                    $('.coupon_code').val('');
                    $('#code').val(data.code.id);

                    toastr.success(data.message,"Status",{
                        timeOut: 5000,
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": true,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": true,
                        "tapToDismiss": true
                    });
                }else{
                    toastr.error(data.message,"Status",{
                        timeOut: 5000,
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": true,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": true,
                        "tapToDismiss": true
                    });
                }
            }
        })
    }
    return true;
    });

    });


</script>



@endsection
@if(isset($usercartsitem))
    @include('front.products.partial-script')
    @include('front.cart.partial-script')
    @include('front.partial-address-script')
@endif




