
 <div class="col-12">
<a href="#" class="cart_btn_remove"> Close </a>
@if(isset($usercartsitem))
@foreach ($usercartsitem as $item)
@php
$str = substr($item->product_medium_image, strpos($item->product_medium_image, 'uploads'));
if (File::exists($str)) {
$medium_image = $item->product_medium_image;
} else {
$medium_image = asset('/uploads/products/no_product_image.png');
}
@endphp
<div class="row cart_drop_box align-items-center py-3 border-bottom mx-0">
    <div class="col-auto pl-2">
        <a href="#"><img src="{{ $medium_image }}" alt="cate img"></a>
    </div>
    <div class="col-6 px-0">
        <div class="cart_drop_text text-left">
            <a class="ellipsis-1" href="#">{{ $item->product_name }}</a>
            <p class="font16 color20 fontsemibold mb-1">
            @if($item->price == $item->special_price || $item->special_price=='0')
            <span>{{ $currency }} {{ $item->price }}</span>
            @else
            <span>{{ $currency }} {{ $item->special_price }}</span>
            <strike class="colorb7 ml-2">{{ $currency }} {{ $item->price }}</strike>
            @endif
            </p>
            <!-- <span>$90 <del>$130</del></span> -->
            <div class="product_quantity mt-2">
            <div class="input_number_form input-group text_icon mx-0" id="count-{{ $item->product_variation_id }}">
                <div class="value-button input-number-decrement btn-minus" data-product="{{ $item->product_id }}" onclick="addToCartProduct(this,{{ $item->product_variation_id }},'minus')">-</div>
                <span class="number_input col-md-2" id="qty-{{ $item->product_id.$item->product_variation_id }}">{{ $item->qty }}</span>
                <div class="value-button input-number-increment" data-product="{{ $item->product_id }}" onclick="addToCartProduct(this,{{ $item->product_variation_id }},'plus',null,{{ $item->max_qty }})">+</div>
            </div>
            </div>
        </div>
    </div>
    <div class="col-auto pl-2 pr-1">
        <div class="favtag ">
            <p class="font20 fontsemibold mb-0 p-0">
            <a class="" href="javascript:void(0)" data-product="{{ $item->product_id }}" onclick="addToFavourite(this,{{ $item->product_variation_id }},'fav')">
            <i class="fa fa-heart" aria-hidden="true"></i>
            </a>
            </p>
        </div>
        <div class="scheduleClose">
            <a href="javascript:;" class="allinone close" onclick="deleteCartItem({{ $item->id }})">
            <i class="fa fa-close" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</div>
@endforeach
@else
<section class="cartwrapper1 my-5">
    <div class="container">
        <div class="container text-center mt-5">
            <p class="font18 color11">Oops! Looks like you don't have any product in your cart.</p>
        </div>
    </div>
</section>
@endif
</div>
