@extends('front.layouts.master')
@section('template_title', $product->name)
@section('og_title', 'omrbranch')
@section('og_url', "{{ Request::url() }}")
@section('og_description', "{{ ($product ? $product->description : '') }}")
@section('og_image', "{{ ($product ? $product->image : '') }}")



@section('content')
<div class="wraper product-detail-page mt-5">
    <div class="container">
        <div class="row">
            <?php
$str = substr($product->medium_image, strpos($product->medium_image, 'uploads'));
if (File::exists($str)) {
    $medium_image = $product->medium_image;
} else {
    $medium_image = asset('/uploads/products/no_product_image.png');
}
?>
            <div class="col-lg-6 col-md-6">
                <div class="row mt-5">
                    <div class="col-lg-12">
                        <div class="preview col">
                            <div class="app-figure" id="zoom-fig">
                                <div class="d-flex flex-wrap">
                                    <div class="flex-fill">
                                        <a id="Zoom-1" class="MagicZoom imgWithVideo" title="" href="{{ $product->image }}">
                                            <img src="{{ $product->image }}" alt="">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <form action="{{ url('api/addToCart') }}" method="POST">
                    {{ csrf_field() }}
                <div class="content-part">
                    <div class="content-head mb-2">
                        @if($product->discount!=0)
                        <div class="offerTag" style="left:0;">
                            <p class="font14 fontsemibold mb-0">{{ $product->discount }}%<br>OFF</p>
                        </div>
                        @endif
                        <div class="d-flex justify-content-end">
                            <a href="javascriptvoid(0);" data-toggle="modal" data-target="#socialShare" class="share_abs p-2"><i class="fa fa-share" aria-hidden="true"></i></a>
                            @include('front.share',['url'=> Request::url()])
                        </div>
                        <h5 class="color20 font19 fontSemiBold prodTitle">{{ $product->name }}</h1>
                        <div class="favtag">
                            <p class="font20 fontsemibold mb-0">
                                <a href="javascript:void(0)" data-product="{{ $product->id }}" class="{{ $product->is_favorite==1 ? 'favdark' : '' }}" data-product="{{ $product->id }}" onclick="addToFavourite(this,{{ $product->product_variation_id }},'fav')">
                                    <i class="fa fa-heart" aria-hidden="true"></i>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="big-prize d-flex flex-column">
                        @if($product->price == $product->special_price || $product->special_price=='0')
                        <div class="d-flex mb-2">
                            <span class="font18 fontSemiBold color20">MRP :</span>
                            <span class="font18 color36 fontNormal">&nbsp;&nbsp;{{ $currency }}{{ $product->price }}</span>
                        </div>
                    @else
                        <div class="d-flex mb-2">
                            <span class="font18 fontSemiBold color20">MRP :</span>
                            <strike class="font18 colorb7 fontNormal">&nbsp;&nbsp; {{ $currency }}{{ $product->price }}</strike>
                        </div>
                        <div class="d-flex mb-2">
                            <span class="font18 fontSemiBold color20">Offer Price :</span>
                            <span class="font18 color36 fontNormal">&nbsp;&nbsp;{{ $currency }}{{ $product->special_price }}</span>
                        </div>
                    @endif
                    </div>
                    <div class="availableBlock">
                        <p class="font18 fontSemiBold color20">Available in:</p>


                    @if(isset($product->options) && count($product->options) > 0)
                    @foreach($product->options as $key => $option)

                    <span class="font18 fontSemiBold color20"> {{$option['attribute_name']}} </span>
                       @foreach($option['attribute_option'] as $k => $d)
                        <fieldset>
                            <input type="radio" id="radio{{$d->id}}" name="option_value[{{$key}}]" value="{{$d->id}}" class="radio_btn_value">
                            <label for="radio{{$d->id}}"> {{$d->value}} </label>
                        </fieldset>
                        @endforeach
                      @endforeach
                    @endif

                    </div>
                    <?php
if ($auth_user) {

    if (!is_null($auth_user->cart)) {
        $user = $auth_user->load('cart.cart_items');

        $user_cart = $user->cart->cart_items->filter(function ($cart) {
            return $cart;
        });

        if ($type == 'grocery') {
            $quantity = $user_cart->filter(function ($cart) use ($product) {
                return $cart->product_variation_id == $product->product_variation_id;
            })->pluck('qty')->first();

        } else {
            $quantity = $user_cart->filter(function ($cart) use ($id) {
                return $cart->product_variation_id == $id;
            })->pluck('qty')->first();
        }
    } else {
        $quantity = null;
    }
} else {
    $quantity = null;
}

?>




                    <div class="addBtn justify-content-center d-block dyna_btn_wrapper mt-2">
                        @if($product->qty==0)
                        <span class="font18 font-weight-bold text-danger">{{ $product->warning_msg }}</span>
                        @else


                        <!-- <div class="input_number_form input-group">
                            <div class="value-button btn-minus" data-product="{{ $product->id }}" onclick="addToCart(this,{{ $product->product_variation_id }},'minus')">-</div>
                            <span class="number_input col-md-2" id="qty-{{ $product->id.$product->product_variation_id }}" > 1</span>
                            <div class="value-button btn-plus" data-product="{{ $product->id }}" onclick="addToCart(this,{{ $product->product_variation_id }},'plus',null,{{ $product->max_qty }})">+</div>
                        </div> -->

                     <div class="hidden_section">
                        <div class="input_number_form input-group">
                            <div class="value-button no-modal btn-minus" onclick="addToCartQty('minus')">-</div>
                            <span class="number_input col-md-2" id="pro_qty" > 1 </span>
                            <input type="hidden" name="current_qty" value="1" class="current_qty">
                            <div class="value-button no-modal btn-plus" onclick="addToCartQty('plus')">+</div>
                        </div>

                        <a
                        @if($quantity!==null) style="display:none" @endif id="add_to_cart_btn"
                         class="hover1 font16 fontsemibold colorWhite bgTheme px-4 py-1 radius50 variant_btn add_to_cart_btn"
                         data-product="{{ $product->id }}" href="javascript:;" >
                         Add to Cart
                        </a>


                        <a
                        @if($quantity!==null) style="display:none" @endif id="go_to_cart_btn"
                         class="hover1 font16 fontsemibold colorWhite bgTheme px-4 py-1 radius50 variant_btn go_to_cart_btn"
                         data-product="{{ $product->id }}" href="javascript:;">
                        Update Cart
                        </a>

                        <a id="buynow"
                         class="hover1 font16 fontsemibold colorWhite bgTheme px-4 py-1 radius50 variant_btn buynow"
                         data-product="{{ $product->id }}" href="javascript:;" >
                        Buy Now
                        </a>


                        @endif
                         </div>
                    </div>

                    <div class="prodDesc borderTop1 mt-sm-4 mt-2 pt-sm-4 pt-2">
                        <div class="font18 fontSemiBold color20 mb-3">Product Description</div>
                        <div>
                            <p class="font16 color36 mb-2">{!! $product->description !!}</p>
                        </div>
                    </div>

                    <input type="hidden" name="">
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('front.home.partial-quick_grab')
@include('front.home.product-variant')
</section>





@endsection
@include('front.products.partial-script')

@push('js')
<script>

   //add to cart function
   var count = 1;

   function addToCartQty(type=null,qty=null){



       if(type=="plus"){
           count++;
           var product_id = $('#pro_qty').attr('data-product');
           $("#pro_qty").html(+count);
           $(".current_qty").val(+count);
        }
        else if (type=="minus")
        {
           var c =  count-1;
            if(c >= 1){
            count--
            $("#pro_qty").html(+count);
                $(".current_qty").val(+count);
            }
        }
    };

    $('.radio_btn_value').click(function(){
        var ids=[];
        var check=true;

        $('[name^=option_value]').each(function(index,item){
            var name=$(this).attr('name');

            if($('[name="'+name+'"]:checked').val() == undefined){
                // toast
                toastr.error("Please select all specifications to purchase product");
                check=false;
                return false;
            }else{
                ids.push($('[name="'+name+'"]:checked').val());
            }
        })

        if(check){
            $.ajax({
            type: "GET",
            url: "{{route('variationSend')}}",
            data: {ids:ids, product_id:"{{ $product->id }}"},

            success: function(data){
                console.log(data);
                if(data.status == true){
                $('.hidden_section').removeClass('hidden_section');

                if(data.cartcount == null){
                    // $("#pro_qty").html(data.cartcount);
                    $(".add_to_cart_btn").show();
                    $("#pro_qty").html(1);

                    $(".current_qty").val(1);

                    $(".go_to_cart_btn").hide();
                    document.getElementById('add_to_cart_btn').onclick=function(){addToCart($(".add_to_cart_btn"),data.variation_id,'setqty',null,$(".current_qty").val());}

                }else{
                    $("#pro_qty").html(data.cartcount);
                    $(".go_to_cart_btn").show();
                    $(".add_to_cart_btn").hide();
                    $(".current_qty").val(data.cartcount);
                    document.getElementById('go_to_cart_btn').onclick=function(){addToCart($(".go_to_cart_btn"),data.variation_id,'setqty',null,$(".current_qty").val());}
                }

                document.getElementById('buynow').onclick=function(){buynow($(".buynow"),data.variation_id,'setqty',null,$(".current_qty").val());}

                // cartcount


                }else{
                    toastr.error("Selected item not available");
                $('.hidden_section').addClass('hidden_section');
                }
                // $("#resultarea").text(data);
            },
            error: function (error) {
                console.log(error);
                toastr.error("Something went wrong");

            },


            });
        }





    })

</script>
@endpush