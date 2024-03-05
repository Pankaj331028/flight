@extends('front.layouts.master')
@section('template_title','Favorites')
@section('content')

<section class="wrapper2 quickGrab bgf7 py-3 py-md-5">
    <div class="container">
        <div class="sectionTitleBlock d-flex mb-5">
            <div class="sectionTitle font35 font-weight-bold color11">Favorites</div>
        </div>
        <div class="row listingProdGrid">
            @forelse ($products as $product)
            <?php
$outOfStock = $product->variations->filter(function ($item) {
    return $item->qty == 0;
});

$str = substr($product->medium_image, strpos($product->medium_image, 'uploads'));

if (File::exists($str)) {
    $medium_image = $product->medium_image;
} else {
    $medium_image = asset('/uploads/products/no_product_image.png');
}
?>
            <div class="col-md-4 mb-4" style="max-height: 300px;">
                <div class="items" style="height: 100%;">
                    <div class="productImg">
                        <img src="{{ $medium_image }}" alt="product" height=100>
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
                        <p class="quantity font18 color20">{{ $product->weight }}</p>
                    </div>
                    <div class="addBtn justify-content-center d-block dyna_btn_wrapper text-center">
                        @if($outOfStock->count()==1 && $product->variations->count()==1)
                                <span class="font18 font-weight-bold text-danger">Out of Stock</span>
                        @else
                            <button @if($product->cart_count!=0) style="display:none" @endif class="hover1 font16 fontsemibold colorWhite bgTheme px-4 py-1 radius50 variant_btn" data-product="{{ $product->id }}" id="cart-{{ $product->product_variation_id }}" onclick="addToCart(this,{{ $product->product_variation_id }})">Add</button>
                            <div class="input_number_form input-group qtyCounter-{{ $product->id }}" id="count-{{ $product->product_variation_id }}" @if($product->cart_count==0) style="display:none" @endif>
                                <div class="value-button btn-minus" data-product="{{ $product->id }}" onclick="addToCart(this,{{ $product->product_variation_id }},'minus')">-</div>
                                <span class="number_input col-md-2" id="qty-{{ $product->id.$product->product_variation_id }}">{{ $product->cart_count==0 ? 1 : $product->cart_count }}</span>
                                <div class="value-button btn-plus" data-product="{{ $product->id }}" onclick="addToCart(this,{{ $product->product_variation_id }},'plus',{{ $product->variations[0]->max_qty }})">+</div>
                            </div>
                        @endif
                    </div>
                    @if($product->discount!=0)
                    <div class="offerTag">
                        <p class="font14 fontsemibold mb-0">{{ $product->discount }}%<br>OFF</p>
                    </div>
                    @endif
                    <div class="favtag">
                        <p class="font20 fontsemibold mb-0">
                            <a href="javascript:void(0)" class="favdark" data-product="{{ $product->id }}" onclick="addToFavourite(this,{{ $product->product_variation_id }},'fav')">
                                <i class="fa fa-heart" aria-hidden="true"></i>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-md-12 text-center">
                <h5 class="">Oops! No products found</h5>
            </div>
            @endforelse
        </div>
        {{ (count($products)>0 && Request::segment(1)!=='my-favourites') ? $products->links("pagination::bootstrap-4") : '' }}
    </div>
</section>
@endsection
@push('js')
<script>
    var user_id    = "{{ $user_id }}";
    function increaseValue(id,product_id,qty) {

        var value = parseInt($('#qty-'+product_id+id).text(), 10);
        value = isNaN(value) ? 0 : value;
        value++;
        if(value<=qty){
            $('#qty-'+product_id+id).html(value);
        }
    }

    function decreaseValue(id,product_id,x) {
        var value = parseInt($('#qty-'+product_id+id).text(), 10);
        value < 1 ? value = 1 : '';
        value--;

        

        if(value==0){
            $(x).css('pointer-events','none');
            $('#qty-'+product_id+id).html(1);
        }else{
            $('#qty-'+product_id+id).html(value);
        }
    }

    //add to cart function
    function addToCart(x,id,type=null,qty=null){


        var product_id = $(x).data('product');
        var selected_productID = $('#prod-'+id).val();

        if(type=="plus"){
            increaseValue(id,product_id,qty);
        }
        else if (type=="minus")
        {
            decreaseValue(id,product_id,x);
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
        method: 'post',
        url: "{{url('api/addToCart')}}",
        data: {
            // user_id: user_id,
            type: type,
		    product_id: product_id,
		    product_variation_id: id,
        },
        success: function(res)
        {
            var item = res.data;
            if(res.status==200){
                $('.cart').html(res.cart_count);
                //show counter and hide add button
                $('#cart-'+id).hide();
                $('#count-'+id).show();

                //when product added to cart change product qty counter
                $('.qtyCounter-'+id).css('display','');

                if(id==selected_productID){
                    $('#qty-'+product_id).html(item.cart_count);
                }
                if(res.message=='Product removed successfully'){
                    location.reload();
                }

                toastr.success(res.message,"Status",{
                timeOut: 5000,
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "tapToDismiss": true

            });
            }else{
                if(res.status==321){
                    var message = 'Please login to continue.' ;
                }
                else{
                    var message = res.message;
                }
                toastr.error(message,"Status",{
                timeOut: 5000,
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "tapToDismiss": true

                });
            }
        },
        error: function()
        {
            toastr.error("Unable to add it to cart.","Status",{
                timeOut: 5000,
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "tapToDismiss": true

            });
        }
    });
};
function addToFavourite(x,id=null,type) {
    var product_id = $(x).data('product');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
    method: 'post',
    url: "{{url('api/manageUserFavourite')}}",
    data: {
        user_id:  "{{$auth_user->id ?? '' }}",
        response_type : type,
        product_id: product_id,
        product_variation_id: id,
    },
    success: function (res){
        // var res = JSON.parse(res);
        toastr.success(res.message,"Status",{
            timeOut: 5000,
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": true,
            "tapToDismiss": true
        });
        location.reload();
    },
    error: function (){
        toastr.error(res.message,"Status",{
            timeOut: 5000,
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": true,
            "tapToDismiss": true
        });
    }

    });
};
</script>
@endpush