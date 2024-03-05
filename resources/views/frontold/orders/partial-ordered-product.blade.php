<section class="orderDetailWrapper3 mb-5">
    <div class="container">
        @foreach($item['products'] as $product)
        <form id="cancelProduct" action="{{ url('api/cancelOrder') }}" method="post">
        <div class="row align-items-center py-4">
            <?php
            if(isset($item['data'])){
                $order_id = $item['data']['id'];
            }else{
                $order_id = $item['order_id'];
            }
            if(isset($item['current_date'])){
                $current_date = $item['current_date'];
            }else{
                $current_date = $item['date'];
            }
            
            $str = substr($product->medium_image, strpos($product->medium_image, 'uploads'));

            if(File::exists($str)){
                $medium_image = $product->medium_image;
            }else{
                $medium_image = asset('/uploads/products/no_product_image.png');
            }

            ?>
            <input type="hidden" name="order_id" value="{{ $order_id }}">
            <input type="hidden" name="user_id" value="{{ $auth_user->id }}">
            <input type="hidden" name="item_id" value="{{ $product->id }}">
            <input type="hidden" name="date" value="{{ $current_date }}">
            <div class="col-md-6">
                <div class="orderDetailProd  pb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="orderDetailProd"> 
                                <img src="{{ $medium_image }}" alt="" style="width: 70px;">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="orderDetailDesc">
                                <div class="cartProdDetail">
                                <p class="font18 fontSemiBold color20 mb-1">{{ $product->name }}</p>
                                
                                <p class="font16 color20 fontsemibold mb-1 d-flex align-items-center">
                                    @if($product->special_price == $product->price || $product->special_price=='0')
                                        <span>{{ $item['currency'] }}{{ $product->price }}</span>
                                    @else
                                    <span>{{ $item['currency'] }}{{ $product->special_price }}</span>
                                    <strike class="colorb7 ml-2">{{ $item['currency'] }}{{ $product->price }}</strike>
                                    @endif
                                </p>
                                <p class="font16 color36 mb-1">{{ $product->weight }}</p>
                              
                                {{-- <div class="favtag">
                                    <p class="font20 fontsemibold mb-0">
                                        <a href="javascript:void(0)">
                                            <i class="fa fa-heart" aria-hidden="true"></i>
                                        </a>
                                    </p>
                                </div> --}}
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if($product->scheduled==1)
                <div class="col-md-6">
                    <div class="orderDetailCalender d-flex align-items-center justify-content-between">
                    @if($product->status=='pending')
                    <div class="btn_or_1">
                        <button type="submit" class="cancelOrder font16 btn_trans colorTheme border1 radius50 px-4 py-2 d-flex align-items-center">Cancel</button>
                    </div>
                    <div class="btn_or_1">
                        <a href="javascript:;" onclick="editReschedule(this)" data-date="{{ $current_date }}" data-item_id="{{ $product->id }}" data-id="{{$order_id}}" class="font16 colorWhite fontSemiBold radius50 bgTheme px-4 py-2 borderNone hover1 text-center mr-4">Re-Schedule</a>
                    </div>
                    @elseif($product->status=='rescheduled')
                    <div class="btn_or_1">
                        <button type="submit" class="cancelOrder font16 btn_trans colorTheme border1 radius50 px-4 py-2 d-flex align-items-center">Cancel</button>
                    </div>
                    @endif
                    </div>
                </div>
            @endif

            @if($product->scheduled==0)
            <div class="col-md-6">
                <div class="orderDetailCalender d-flex align-items-center justify-content-between">
                  @if($product->status=='pending')
                    <div class="btn_or_1">
                        <button type="submit" class="cancelOrder font16 btn_trans colorTheme border1 radius50 px-4 py-2 d-flex align-items-center">Cancel</button>
                  </div>
                  <div class="btn_or_1">
                    <a href="{{ url('track-order/'.$order_id.'/'.$product->id) }}" class="font16 btn_trans colorTheme border1 radius50 px-4 py-2 d-flex align-items-center">Track</a>
                  </div>
                  @elseif($product->status=='delivered')
                    <div class="btn_or_1">
                        <a href="{{ url('/chat-support') }}" class="font16 btn_trans colorTheme border1 radius50 px-4 py-2 d-flex align-items-center">Return</a>
                    </div>
                    <div class="btn_or_1">
                        <a href="{{ url('track-order/'.$order_id.'/'.$product->id) }}" class="font16 btn_trans colorTheme border1 radius50 px-4 py-2 d-flex align-items-center">Track</a>
                    </div>
                  @endif
                </div>
            </div>
            @endif
        </div>
        </form>
        @endforeach
    </div>
</section>