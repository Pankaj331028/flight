@extends('front.layouts.master')
@section('template_title','Home')
@section('template_title')
Home
@endsection

@section('content')
@if($sliders->count()>0)
<section class="wrapper1">
    <div id="carouselExampleFade" class="carousel slide carousel-fade" data-ride="carousel">
        <div class="carousel-inner">
            @php($i=1)
            @foreach ($sliders as $slider)
            <div class="carousel-item @if($i==1)active @endif">
                <?php
if ($slider->category_id) {
    $cat_id = $slider->category_id;
} else {
    $cat_id = 0;
}
?>
                <a href="{{ url('category/groceries/product/'.$cat_id) }}"><img src="{{ $slider->image }}" class="d-block w-100" alt="..."></a>
            </div>
            @php($i++)
            @endforeach
        </div>
        <div class="bannerNavs">
            <a class="carousel-control-prev" href="#carouselExampleFade" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleFade" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
</section>
@endif



@if($quick_grabs != null &&   $quick_grabs->count()>0 )
@include('front.home.partial-quick_grab')
@endif

@if(isset($top_offers))
<section class="wrapper3 topOffers py-3 py-md-5">
    <div class="container">
        <div class="sectionTitleBlock d-flex justify-content-between align-items-center mb-5">
            <div class="sectionTitle font35 font-weight-bold color11">Top Offers</div>
            <a class="font16 colorTheme" href="{{ url('view/offer/1') }}">View All</a>
        </div>
        @foreach ($top_offers as $top_offer)
        <div class="row">
            <div class="col-md-12">
                <a href="{{ url('view/top_offer/'.$top_offer['id']) }}"><img src="{{ $top_offer['image'] }}" alt=""></a>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif
@if(  $exclusives != null &&  $exclusives->count()>0)
@include('front.home.partial-exclusive')
@endif

<section class="wrapper5 topOffers py-3 py-md-5">
    <div class="container">
        <div class="sectionTitleBlock d-flex mb-5">
            <div class="sectionTitle font35 font-weight-bold color11">Categories</div>
        </div>
        <div class="row">
            @if(isset($categories))
            @foreach ($categories as $category)
            <div class="col-md-6 mb-4 category">
                <a class=" d-flex align-items-center" href="{{ url('category/groceries/'.$category->id) }}">
                    <div class="categoryBlock bgWhite">
                        <div class="categoryImg mr-4">
                            <img src="{{ $category->image }}" alt="" width="100">
                        </div>
                        <div class="ctaegoryDetail">
                            <p class="font18 fontsemibold color20 mb-1">{{ $category->name }}</p>
                            <p class="font16 color36">{{ $category->description }}</p>
                        </div>
                        <div class="categoryClickArrow">
                            <a href="{{ url('category/groceries/'.$category->id) }}"><i class="allinone categoryarrow"></i></a>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</section>



@include('front.home.product-variant')
@endsection
@push('js')
<script>
   @if($auth_user)
    var user_id  = "{{ $auth_user->id }}";
    @else
    var user_id    = '';

    @endif

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

    function addToCart(x,id=null,type=null,rs=null,qty=null){



        $('#cart-'+id).prop('disabled',true);
        var product_id = $(x).data('product');
        if(type=='plus'){
            increaseValue(id,product_id,qty);
        }else if (type=="minus"){
            decreaseValue(id,product_id,x);
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $.ajax({
        method: 'post',
        url: "{{url('addToCartHome')}}",

        data: {
            // user_id: user_id,
            type: type,
            response_type : rs, // favourite
		    product_id: product_id,
		    product_variation_id: id,
        },

        success: function(res)
        {

            if(res.status==200){
                $('#cart-'+id).prop('disabled',false);
                $('.cart').html(res.cart_count);
                $('.number_input col-md-2').html(res.cart_count);


                $('#cart-'+id).hide();
                $('#count-'+id).show();

                //hide product variant count
                if(res.message=="Product removed successfully"){
                    $('#count-'+id).hide();
                    $('#cart-'+id).show();
                }

                $(".render-QG").replaceWith(res.html1);
                $(".render-EO").replaceWith(res.html2);
                $('.quickGrabSlider,.exclusiveOfferSlider').owlCarousel({
                    loop: false,
                    margin: 10,
                    responsiveClass: true,
                    responsive: {
                        0: {
                            items: 1,
                            nav: false
                        },
                        600: {
                            items: 2,
                            nav: false
                        },
                        1000: {
                            items: 4,
                            nav: true,
                            afterAction: function(){
                                if ( this.itemsAmount > this.visibleItems.length ) {
                                    $('.next').show();
                                    $('.prev').show();

                                    $('.next').removeClass('disabled');
                                    $('.prev').removeClass('disabled');
                                    if ( this.currentItem == 0 ) {
                                    $('.prev').addClass('disabled');
                                    }
                                    if ( this.currentItem == this.maximumItem ) {
                                    $('.next').addClass('disabled');
                                    }

                                } else {
                                    $('.next').hide();
                                    $('.prev').hide();
                                }
                            }
                        }
                    }
                })

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
        var res = JSON.parse(res);
        if(res.status==200){
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

//product variant modal
function fetchProductVariants(x){
    $('.product').modal('show');
    var product = $(x).data('product');
    $('.variant-body').empty();
    $.each(product.variations,function(key,variant){
        if(variant.cart_count!=0){
            var hide = 'style="display:none"';
        }else{
            var hide = '';
        }
        if(variant.cart_count==0){
            var hideCounter = 'style="display:none"';
        }else{
            var hideCounter = '';
        }
        if(variant.cart_count==0 ){
            var cartCount = 1;
        }else{
            var cartCount = variant.cart_count;
        }
        if(variant.is_favorite==1){
            var is_favorite = 'favdark';
        }else{
            var is_favorite = '';
        }
        var url = "{{ url('product/variation') }}";

        var currency = "{{ $currency }}";
        var minus  = "'minus'";
        var plus = "'plus'";
        var fav = "'fav'";

        if(variant.qty==0){
            var warning_msg = variant.warning_msg;
            var hideBtn = 'style="display:none!important;"'
            var show = '';
        }else{
            var warning_msg = '';
            var hideBtn     = '';
            var show = 'style="display:none!important;"'
        }


        if(variant.discount==0){
            var discountHide = 'display:none!important;'
        }else{
            var discountHide = '';
        }

        if(variant.price == variant.special_price || variant.special_price=='0'){
            var price = '<p class="font16 color20 fontsemibold mb-1 d-flex align-items-center"><span>'+currency+' '+variant.price+'</span></p>';
        }else{
            var price = '<p class="font16 color20 fontsemibold mb-1 d-flex align-items-center"><span>'+currency+' '+variant.special_price+'</span><strike class="colorb7 ml-2">'+currency+' '+variant.price+'</strike></p>'
        }

        var http = new XMLHttpRequest();
        http.open('HEAD', product.small_image, false);
        http.send();
        if(http.status!=404){
            var image = product.small_image;
        }else{
            var image = '/uploads/products/no_product_image.png';
        }


        var str = '<div class="row mb-5"><div class="col-md-4 col-sm-4 col-4 pl-0 p-relative"><div class="cartProdImg" style="max-width:85%;"><a href='+url+'/'+variant.id+'><img src="'+image+'"></a></div><div class="offerTag" style="top:-35px;left:0;'+discountHide+'"><p class="font10 fontsemibold mb-0">'+product.discount+'%<br>OFF</p></div></div><div class="col-md-8 col-sm-8 col-8 d-flex pl-0"><div class="cartProdDetail mb-md-0 mb-3 d-flex border-right-0 text-left w-100"><div class="col-md-12 p-0"><p class="font18 fontSemiBold color20 mb-1">'+product.name+' ('+variant.specifications+' )'+'</p>'+price+'<p class="font16 color36 mb-1 d-flex align-items-center">'+variant.weight+'</p><span class="font18 font-weight-bold text-danger">'+warning_msg+'</span></div><div class="col-md-2"><div class="addBtn favtag_add justify-content-center d-block dyna_btn_wrapper text-center" '+hideBtn+'><button '+hide+' class="hover1 font16 fontsemibold colorWhite bgTheme px-4 py-1 radius50 variant_btn" data-product="'+product.id+'" id="cart-'+variant.id+'" onclick="addToCart(this,'+variant.id+')">Add</button><div class="input_number_form input-group" id="count-'+variant.id+'" '+hideCounter+'><div class="value-button btn-minus" data-product='+product.id+' onclick="addToCart(this,'+variant.id+','+minus+')">-</div><span class="number_input col-md-2" id="qty-'+product.id+variant.id+'">'+cartCount+'</span><div class="value-button btn-plus" data-product="'+product.id+'"onclick="addToCart(this,'+variant.id+','+plus+',null,'+variant.max_qty+')">+</div></div></div><div class="favtag"><p class="font20 fontsemibold mb-0"><a href="javascript:void(0)" data-product='+product.id+' class='+is_favorite+'  data-product='+product.id+' onclick="addToFavourite(this,'+variant.id+','+fav+')"><i class="fa fa-heart" aria-hidden="true"></i></a></p></div></div></div></div></div>'
        $('.variant-body').append(str);
    });
}
</script>
@endpush