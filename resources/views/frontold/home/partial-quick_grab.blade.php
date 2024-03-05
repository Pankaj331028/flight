<section class="wrapper2 render-QG quickGrab bgf7 py-3 py-md-5">
    <div class="container">
        <div class="sectionTitleBlock d-flex justify-content-between align-items-center mb-5">
            <div class="sectionTitle font35 font-weight-bold color11">Quick Grabs</div>
            <a class="font16 colorTheme" href="{{ url('view/quick_grab/0') }}" target="_blank">View All</a>
        </div>

        <div class="owl-carousel quickGrabSlider">
            @foreach ($quick_grabs as $quick_grab)
            <?php
unset($quick_grab->description);
$outOfStock = $quick_grab->variations->filter(function ($item) {
    return $item->qty == 0;
});

$str = substr($quick_grab->small_image, strpos($quick_grab->small_image, 'uploads'));

if (File::exists($str)) {
    $small_image = $quick_grab->small_image;
} else {
    $small_image = asset('/uploads/products/no_product_image.png');
}
?>




            <div class="items">
                <div class="productImg">
                    <a href="{{ url('product/grocery/'.$quick_grab->id) }}" target="_blank" ><img src="{{$quick_grab->image}}" alt=""></a>
                </div>
                <p data-placement="top" data-toggle="tooltip" title="{{ $quick_grab->name }}" class="productName font18 color26 mb-1 fontsemibold text-center mt-3">{{  str_limit($quick_grab->name, 20, '...') }}</p>
                <div class="d-flex justify-content-around">
                    @if($quick_grab->price == $quick_grab->special_price || $quick_grab->special_price=='0')
                        <p class="price"><span class="newPrice font18 color20">{{ $currency }}{{ $quick_grab->price }}</span>
                    @else
                        <p class="price"><span class="newPrice font18 color20">{{ $currency }}{{ $quick_grab->special_price }}</span>
                        <span class="oldPrice font18 colorb7"><strike>{{ $currency }}{{ $quick_grab->price }}</strike></span>
                    </p>
                    @endif
                    <p class="quantity font18 color20">{{ $quick_grab->weight }}</p>
                </div>
                <div class="addBtn justify-content-center d-block dyna_btn_wrapper text-center">
                    @if($outOfStock->count()==1 && $quick_grab->variations->count()==1)
                        <span class="font18 font-weight-bold text-danger">Out of Stock</span>
                    @else
                        <a @if($quick_grab->variations->sum('cart_count')!=0) style="display:none" @endif onclick=fetchProductVariants(this) class="variantModal hover1 font16 fontsemibold colorWhite bgTheme px-4 py-1 radius50" href="javascript:;" data-product="{{ $quick_grab }}" >Add</a>
                        <div class="input_number_form w-auto input-group" @if($quick_grab->variations->sum('cart_count')==0) style="display:none" @endif>
                            <div class="value-button variantModal" onclick=fetchProductVariants(this) data-product="{{ $quick_grab }}">-</div>
                            <span class="number_input col-md-2">{{ $quick_grab->variations->sum('cart_count') }}</span>
                            <div data-toggle="modal" onclick=fetchProductVariants(this) class="value-button variantModal" id="increase" data-product="{{ $quick_grab }}">+</div>
                        </div>
                    @endif
                </div>
                @if($quick_grab->discount!=0)
                <div class="offerTag">
                    <p class="font14 fontsemibold mb-0">{{ $quick_grab->discount }}%<br>OFF</p>
                </div>
                @endif
                <div class="favtag">
                    <p class="font20 fontsemibold mb-0">
                        <a class="{{ $quick_grab->is_favorite==1 ? 'favdark' : '' }}" href="javascript:void(0)" data-product="{{ $quick_grab->id }}" onclick="addToFavourite(this,{{ $quick_grab->product_variation_id }},'fav')">
                            <i class="fa fa-heart" aria-hidden="true"></i>
                        </a>
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>