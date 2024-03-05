@extends('front.layouts.master')
@section('template_title')
{{ isset($category) ? ucfirst($category->name) : 'Products' }}
@endsection
@section('content')

<section class="wrapper2 quickGrab bgf7 py-3 py-md-5">
    <div class="container">
        <div class="sectionTitleBlock d-flex mb-5">
            <div class="sectionTitle font35 font-weight-bold color11">Search Result</div>
            <div class="filter_dropdown position-relative" style="margin-left: auto;">
            <a class="filter_btn shadow" href="javascript:;">Sort by </a>
            <div class="filter_dropdown_box">
                <ul id="filter_dropdown_box" data-value="">
                    <li><a href="javascript:;" data-value="price_low_to_high"> Price Low to High </a></li>
                    <li><a href="javascript:;" data-value="price_high_to_low"> Price High to Low </a></li>
                    <li><a href="javascript:;" data-value="name_asc"> Name ASC </a></li>
                    <li><a href="javascript:;" data-value="name_desc"> Name  DESC </a></li>
                </ul>
            </div>
        </div>

        </div>
        <div class="row listingProdGrid">




      @include('front.filter')

      <div class="col">
                <div class="row" id="productResult">
            @if(!empty($products)&&count($products)>0)
            @forelse ($products as $product)
                  <?php
$outOfStock = $product->variations->filter(function ($item) {
    return $item->qty == 0;
});

$str = public_path(substr($product->medium_image, strpos($product->medium_image, 'uploads')));

if (File::exists($str)) {
    $medium_image = $product->medium_image;
} else {
    $medium_image = asset('/uploads/products/no_product_image.png');
}
?>
            <div class="col-md-4 mb-4" style="max-height: 300px;">
                    <div class="items" style="height: 100%;">
                        <div class="productImg">
                            <a href="{{ url('product/grocery/'.$product->id) }}" target="_blank"><img src="{{$medium_image}}" alt="product" height=100 style="width:auto !important;"></a>
                        </div>
                        <h5 class="productName font18 color26 mb-1 fontsemibold text-center mt-3">{{ $product->name }}</h5>
                        <div class="d-flex justify-content-around">
                            <input type="hidden" value="{{ $product->product_variation_id }}" id="prod-{{ $product->product_variation_id }}">
                            @if($product->price == $product->special_price || $product->special_price=='0')
                            <p class="price"><span class="newPrice font18 color20">{{ $currency }}{{ $product->price }}</p>
                            @else
                            <p class="price"><span class="newPrice font18 color20">{{ $currency }}{{ $product->special_price }}</span>
                            <span class="oldPrice font18 colorb7"><strike>{{ $currency }}
                            {{ $product->price }}</strike></span></p>
                            @endif
                            <p class="quantity font18 color20" id="#weight-{{ $product->weight }}">{{ $product->weight }}</p>
                        </div>

                        <div class="addBtn justify-content-center d-block dyna_btn_wrapper text-center">
                            @if($outOfStock->count()==1 && $product->variations->count()==1)
                                <span class="font18 font-weight-bold text-danger">Out of Stock</span>
                            @else
                                <a @if($product->variations->sum('cart_count')!=0) style="display:none" @endif class="hover1 font16 fontsemibold colorWhite bgTheme px-4 py-1 radius50 dyna_btn addBtn-{{ $product->id }}" href="#">Add</a>
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
            @empty
            </div>
            <div class="col-md-9 text-center">
                <h5 class="">Oops! No products found</h5>
            </div>
        </div>
            @endforelse
            @else

            <div class="col-md-9 text-center">
                <h5 class="">Oops! No products found</h5>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection
@include('front.products.partial-script')