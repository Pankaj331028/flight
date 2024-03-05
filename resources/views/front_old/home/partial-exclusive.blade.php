<section class="wrapper4 render-EO bgf7 py-3 py-md-5">
    <div class="container">
        <div class="sectionTitleBlock d-flex justify-content-between align-items-center mb-5">
            <div class="sectionTitle font35 font-weight-bold color11">Our Exclusive Offers</div>
            <a class="font16 colorTheme" href="{{ url('view/is_exclusive/0') }}">View All</a>
        </div>
        <div class="owl-carousel exclusiveOfferSlider">
            @foreach ($exclusives as $exclusive)
            <?php
unset($exclusive->description);
$outOfStock = $exclusive->variations->filter(function ($item) {
    return $item->qty == 0;
});

$str = public_path(substr($exclusive->small_image, strpos($exclusive->small_image, 'uploads')));

if (File::exists($str)) {
    $small_image = $exclusive->small_image;
} else {
    $small_image = asset('/uploads/products/no_product_image.png');
}
?>
            <div class="items">
                <div class="productImg">
                    <a href="{{ url('product/grocery/'.$exclusive->id) }}" target="_blank"><img src="{{$exclusive->image}}" alt=""></a>
                </div>
                <p data-placement="top" data-toggle="tooltip" title="{{ $exclusive->name }}" class="productName font18 color26 mb-1 fontsemibold text-center mt-3">{{  str_limit($exclusive->name, 20, '...') }}</p>
                <div class="d-flex justify-content-around">

                    @if($exclusive->price == $exclusive->special_price || $exclusive->special_price=='0')
                        <p class="price"><span class="newPrice font18 color20">{{ $currency }}{{ $exclusive->price }}</span>
                    @else
                        <p class="price"><span class="newPrice font18 color20">{{ $currency }}{{ $exclusive->special_price }}</span>
                            <span class="oldPrice font18 colorb7"><strike>{{ $currency }}{{ $exclusive->price }}</strike></span>
                        </p>
                    @endif
                    <p class="quantity font18 color20">{{ $exclusive->weight }}</p>
                </div>
                <div class="addBtn justify-content-center d-block dyna_btn_wrapper text-center">
                    @if($outOfStock->count()==1 && $exclusive->variations->count()==1)
                        <span class="font18 font-weight-bold text-danger">Out of Stock</span>
                    @else
                        <a @if($exclusive->variations->sum('cart_count')!=0) style="display:none" @endif onclick=fetchProductVariants(this) class="variantModal hover1 font16 fontsemibold colorWhite bgTheme px-4 py-1 radius50" href="javascript:;" data-product="{{ $exclusive }}">Add</a>
                        <div class="input_number_form w-auto input-group" @if($exclusive->variations->sum('cart_count')==0) style="display:none" @endif>
                            <div class="value-button variantModal" onclick=fetchProductVariants(this) data-product="{{ $exclusive }}">-</div>
                            <span class="number_input col-md-2">{{ $exclusive->variations->sum('cart_count') }}</span>
                            <div class="value-button variantModal" onclick=fetchProductVariants(this) id="increase" data-product="{{ $exclusive }}">+</div>
                        </div>
                    @endif
                </div>
                @if($exclusive->discount!=0)
                <div class="offerTag">
                    <p class="font14 fontsemibold mb-0">{{ $exclusive->discount }}%<br>OFF</p>
                </div>
                @endif
                <div class="favtag">
                    <p class="font20 fontsemibold mb-0">
                        <a class="{{ $exclusive->is_favorite==1 ? 'favdark' : '' }}" href="javascript:void(0)" data-product="{{ $exclusive->id }}" onclick="addToFavourite(this,{{ $exclusive->variations[0]->id }},'fav')">
                            <i class="fa fa-heart" aria-hidden="true"></i>
                        </a>
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>