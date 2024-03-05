 @php
if (isset($type)) {
if ($offerid == '1' || $type == 'top_offer') {
$title = 'Top Offers';
} else if ($type == 'is_exclusive') {
$title = 'Our Exclusive Offers';
} else if ($type == 'quick_grab') {
$title = 'Quick Grabs';
} else if ($offerid == '0') {
$title = 'Offers';
}
} else {
$title = 'Product Listing';
}
@endphp

<div class="row listingProdGrid">
@if(isset($products) && count($products) > 0)
@foreach($products as $key => $product)
    @php
    $outOfStock = $product->variations->filter(function ($item) {
            return $item->qty == 0;
        });
    if (isset($product->image)) {
        $medium_image = $product->medium_image;
    } else {
        $medium_image = asset('/uploads/products/no_product_image.png');
    }
@endphp



<div class="col-md-4 mb-4">
    <div class="items">
        <div class="productImg">
            <img src="{{$product->image}}" alt="">
        </div>
        <p class="productName font16 color26 mb-1 fontsemibold text-center mt-3"> {{$product->name ?? '-'}} </p>
        <div class="d-flex justify-content-around">
             @if($product->price == $product->special_price || $product->special_price=='0')
                <p class="price"><span class="newPrice font18 color20">{{ $currency }} {{ $product->price }}</p>
                @else
                <p class="price"><span class="newPrice font18 color20">{{ $currency }}{{ $product->special_price }}</span>
                    <span class="oldPrice font18 colorb7"><strike>{{ $currency }}
                    {{ $product->price }}</strike></span>
                </p>
                @endif
        </div>

        <!-- <div class="addBtn justify-content-center d-block dyna_btn_wrapper text-center"> -->
        <div class="addBtn justify-content-center d-block dyna_btn_wrapper text-center">

            @if($outOfStock->count()==1 && $product->variations->count()==1)
            <span class="font18 font-weight-bold text-danger">Out of Stock</span>
            @else
            <a @if($product->variations->sum('cart_count')!=0) style="display:none" @endif class="hover1 font16 fontsemibold colorWhite bgTheme px-4 py-1 radius50 dyna_btn addBtn-{{ $product->id }}" href="javascript:void(0)">Add</a>
            <div class="input_number_form input-group qtyCounter-{{ $product->id }}" @if($product->cart_count==0) style="display:none" @endif>
                <div class="value-button btn-minus" id="decrease"  value="Decrease Value">-</div>
                <span class="number_input col-md-2" id="qty-{{ $product->id }}">{{ $product->cart_count }}</span>
                <div class="value-button btn-plus" id="increase" data-product="{{ $product->id }}">+</div>
            </div>
            @include('front.modals.product-variant')
            @endif
        </div>


        @if($product->discount!=0)
            <div class="offerTag">
                <p class="font14 fontsemibold mb-0">{{ $product->discount }}%<br>OFF</p>
            </div>
            @endif

            <div class="favtag">
            <p class="font20 fontsemibold mb-0">
                <a class="{{ $product->is_favorite==1 ? 'favdark' : '' }}" href="javascript:void(0)" data-product="{{ $product->id }}" onclick="addToFavourite(this,{{ $product->product_variation_id }},'fav')">
                <i class="fa fa-heart" aria-hidden="true"></i>
                </a>
            </p>
        </div>
    </div>
</div>
@endforeach

@else
<h4 style="margin-left: 38px;"> Product Not Found </h4>
@endif
</div>
