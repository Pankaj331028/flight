<!--------------Product variant modal----------------->
<div class="modal fade productVariant" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal_header border-bottom-0">
                <h5 class="modal-title text-center font20 font-weight-bold modal_title_rel" id="exampleModalLabel">
                <span>Size Variants</span>
                </h5>
                <button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
                <img src="{{ URL::asset('/front/images/cross.png') }}">
                </button>
            </div>
            <div class="modal_form">
                <div class="modal-body">
                <div class="cartProd py-3">
                    @foreach($product->variations as $variant)
                    <div class="row mb-5">
                        <div class="col-md-4 col-sm-4 col-4 pl-0 p-relative">
                            <div class="cartProdImg" style="max-width:85%;">

                                <a href="{{ url('product/variation/'.$variant->id) }}" target="_blank"><img src="{{ $medium_image }}" alt="" height="100"></a>
                            </div>
                            @if($product->discount!=0)
                            <div class="offerTag" style="top:-35px;left:0;">
                                <p class="font10 fontsemibold mb-0">{{ $product->discount }}%<br>OFF</p>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-8 col-sm-8 col-8 d-flex pl-0">
                            <div class="cartProdDetail mb-md-0 mb-3 d-flex border-right-0 text-left w-100">
                                <div class="col-md-12 p-0">
                                    <p class="font18 fontSemiBold color20 mb-1">{{ $product->name.' ('.$variant->specifications.')'}}</p>
                                    @if($variant->price == $variant->special_price || $variant->special_price=='0')
                                    <p class="font16 color20 fontsemibold mb-1 d-flex align-items-center">
                                    <span>{{ $currency }}{{ $variant->price }}</span></p>
                                    @else
                                    <p class="font16 color20 fontsemibold mb-1 d-flex align-items-center">
                                    <span>{{ $currency }}{{ $variant->special_price }}</span>
                                    <strike class="colorb7 ml-2">{{ $currency }} {{ $variant->price }}</strike>
                                    </p>
                                    @endif
                                    <p class="font16 color36 mb-1 d-flex align-items-center">{{ $variant->weight }}</p>
                                    @if($variant->qty==0)
                                    <span class="font18 font-weight-bold text-danger">{{ $variant->warning_msg }}</span>
                                    @endif
                                </div>
                                <div class="col-md-2">
                                    @if($variant->qty>0)
                                    <div class="addBtn favtag_add justify-content-center d-block dyna_btn_wrapper text-center">
                                        <button @if($variant->cart_count!=0) style="display:none" @endif class="hover1 font16 fontsemibold colorWhite bgTheme px-4 py-1 radius50 variant_btn" data-product="{{ $product->id }}" id="cart-{{ $variant->id }}" onclick="addToCart(this,{{ $variant->id }})">Add</button>
                                        <div class="text-right input_number_form input-group" id="count-{{ $variant->id }}" @if($variant->cart_count==0) style="display:none" @endif>
                                            <div class="value-button btn-minus" data-product="{{ $product->id }}" onclick="addToCart(this,{{ $variant->id }},'minus')">-</div>
                                            <span class="number_input col-md-2" id="qty-{{ $product->id.$variant->id }}">{{ $variant->cart_count==0 ? 1 : $variant->cart_count}}</span>
                                            <div class="value-button btn-plus" data-product="{{ $product->id }}" onclick="addToCart(this,{{ $variant->id }},'plus',null,{{ $variant->max_qty }})">+</div>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="favtag">
                                        <p class="font20 fontsemibold mb-0">
                                            <a href="javascript:void(0)" data-product="{{ $product->id }}" class="{{ $variant->is_favorite==1 ? 'favdark' : '' }}" data-product="{{ $product->id }}" onclick="addToFavourite(this,{{ $variant->id }},'fav')">
                                                <i class="fa fa-heart" aria-hidden="true"></i>
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!----Modal end---->
