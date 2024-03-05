@push('js')
<script>
    var alertMsg = window.localStorage.getItem('fav-alert');
    if(alertMsg)
    {
        $('.alert1').removeClass('d-none');
        $('.alertMsg').html(alertMsg);
    }

    setTimeout(() => {
        window.localStorage.removeItem('fav-alert');
        $('.alert1').addClass('d-none');
    }, 1000  * 10);

    @if($auth_user)
    var user_id    = "{{ $auth_user->id }}";
    @else
    var user_id    = '';
    @endif

    function increaseValue(id,product_id,qty) {

        var value = parseInt($('#qty-'+product_id+id).text(), 10);
        value = isNaN(value) ? 0 : value;
       var a = value++;
        $(".cart_count").empty();
        $('.cart_count').html(a);


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

            // var current_value = $('.cart_count').html();
            // var a = current_value;
            // var b = 1;
            // var c = parseInt(a) - parseInt(1);
            // $('.cart_count').html(c)

        }else{

            var current_value = $('.cart_count').html();
            var a = current_value;
            var b = 1;
            var c = parseInt(a) - parseInt(1);
            $('.cart_count').html(c)

            $('#qty-'+product_id+id).html(value);
        }
    }

    //show variant modal on parent call
    $(document).on('click',".dyna_btn",function (e) {
        $(this).each(function(){
            //show product variant modal

            $(this).parent().children().eq(2).modal('show');

        }) ;
    });

    //show variant modal when parent call on click of + button
    $('.value-button:not(.no-modal)').click(function(e){
        $(this).each(function(){
            //show product variant modal
            $(this).parent().siblings().eq(1).modal('show');
        }) ;
    })

    //add to cart function
    function addToCart(x,id=null,type=null,rs=null,qty=null){


        $('#cart-'+id).prop('disabled',true);
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
        headers: {"Authorization": "Bearer {{Session::get('logtoken')}}"},

        data: {
            user_id: "{{ $auth_user->id ?? '' }}",
            type: type,
            response_type : rs, // favourite
		    product_id: product_id,
		    product_variation_id: id,
            qty:qty
        },
        success: function(res)
        {



            // var res = JSON.parse(res);
            var item = res.data;
            if(res.status==200){
                $('#cart-'+id).prop('disabled',false);
                $('.cart').html(res.cart_count);
                //show counter and hide add button
                $('#cart-'+id).hide();
                $('#count-'+id).show();

                //when product added to cart change product qty counter
                $('.addBtn-'+product_id).hide();
                $('.qtyCounter-'+product_id).css('display','');







                //sum of total added product
                // var productQty = item.variations.reduce(function(prev, cur) {
                //     return prev + cur.cart_count;
                // }, 0);
                //if added product match display product then only update counter
                if(id==selected_productID){
                    $('#qty-'+product_id).html(item.cart_count);
                }
                if(res.message=='Product removed successfully'){
                    location.reload();
                }
                // var productQty = $('#qty-'+product_id).html();
                // console.log(productQty);
                //when qty = 0 hide counter and show add to cart button

                toastr.success(res.message,"Status",{
                timeOut: 5000,
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "tapToDismiss": true,
            });

            $('#cart_popup').addClass('active');

            $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
            $.ajax({
                method: 'post',
                url: "{{route('getCartItem')}}",
                dataType: 'html',
                success: function(res){
                $('#cart_innerbox_main').empty();
                $('#cart_innerbox_main').html(res);
                },
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
                "tapToDismiss": true,
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
                "tapToDismiss": true,
            });
        }
    });
    };

//add to cart function
function buynow(x,id=null,type=null,rs=null,qty=null){
    $('#cart-'+id).prop('disabled',true);
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
    headers: {"Authorization": "Bearer {{Session::get('logtoken')}}"},

    data: {
        user_id: "{{ $auth_user->id ?? '' }}",
        type: type,
        response_type : rs, // favourite
        product_id: product_id,
        product_variation_id: id,
        qty:qty
    },
    success: function(res)
    {
        // var res = JSON.parse(res);
        var item = res.data;
        if(res.status==200){
            $('#cart-'+id).prop('disabled',false);
            $('.cart').html(res.cart_count);
            //show counter and hide add button
            $('#cart-'+id).hide();
            $('#count-'+id).show();

            //when product added to cart change product qty counter
            $('.addBtn-'+product_id).hide();
            $('.qtyCounter-'+product_id).css('display','');

            //if added product match display product then only update counter
            if(id==selected_productID){
                $('#qty-'+product_id).html(item.cart_count);
            }
            window.location.replace("{{ config('app.url') . 'my-cart' }}");
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
            "tapToDismiss": true,
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
            "tapToDismiss": true,
        });
    }
});
};

    function addToCartHome(x,id=null,type=null,rs=null,qty=null){

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
            user_id: "{{$auth_user->id ?? '' }}",
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
                $('#cart-'+id).hide();
                $('#count-'+id).show();

                //hide product variant count
                if(res.message=="Product removed successfully"){
                    $('#count-'+id).hide();
                    $('#cart-'+id).show();
                }



                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    method: 'post',
                    url: "{{route('getCartItem')}}",
                    dataType: 'html',
                    success: function(res){
                    $('#cart_innerbox_main').empty();
                    $('#cart_innerbox_main').html(res);
                    },



                });




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
    }

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
    headers: {"Authorization": "Bearer {{Session::get('logtoken')}}"},

    data: {
        user_id:  "{{$auth_user->id ?? '' }}",
        response_type : type,
        product_id: product_id,
        product_variation_id: id,
    },
    success: function (res){
        // var res = JSON.parse(res);
        if(res.status==200){
           $(this).addClass('favdark');
           location.reload();

            @if(in_array(request()->segment(1) , ['category', 'my-favourites', 'listing']))
            window.localStorage.setItem('fav-alert', res.message);
            @else
                toastr.success(res.message,"Status",{
                timeOut: 5000,
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "tapToDismiss": true
                });
            @endif

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
            "tapToDismiss": true,
        });
    }

    });
};
</script>


<script>
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
            var price = '<p class="font16 color20 fontsemibold mb-1 d-flex align-items-center"><span data-testid="new-price">'+currency+' '+variant.price+'</span></p>';
        }else{
            var price = '<p class="font16 color20 fontsemibold mb-1 d-flex align-items-center"><span data-testid="new-price">'+currency+' '+variant.special_price+'</span><strike class="colorb7 ml-2" data-testid="old-price">'+currency+' '+variant.price+'</strike></p>'
        }

        var http = new XMLHttpRequest();
        http.open('HEAD', product.small_image, false);
        http.send();
        if(http.status!=404){
            var image = product.small_image;
        }else{
            var image = '/uploads/products/no_product_image.png';
        }

        var str = '<div class="row mb-5"><div class="col-md-4 col-sm-4 col-4 pl-0 p-relative"><div class="cartProdImg" style="max-width:85%;"><a href='+url+'/'+variant.id+'><img src="'+image+'"></a></div><div class="offerTag" style="top:-35px;left:0;'+discountHide+'"><p class="font10 fontsemibold mb-0">'+product.discount+'%<br>OFF</p></div></div><div class="col-md-8 col-sm-8 col-8 d-flex pl-0"><div class="cartProdDetail mb-md-0 mb-3 d-flex border-right-0 text-left w-100"><div class="col-md-12 p-0"><p class="font18 fontSemiBold color20 mb-1">'+product.name+' ('+variant.specifications+' )'+'</p>'+price+'<p class="font16 color36 mb-1 d-flex align-items-center">'+variant.weight+'</p><span class="font18 font-weight-bold text-danger">'+warning_msg+'</span></div><div class="col-md-2"><div class="addBtn favtag_add justify-content-center d-block dyna_btn_wrapper text-center" '+hideBtn+'><button '+hide+' class="hover1 font16 fontsemibold colorWhite bgTheme px-4 py-1 radius50 variant_btn" data-product="'+product.id+'" id="cart-'+variant.id+'" onclick="addToCartHome(this,'+variant.id+')">Add</button><div class="input_number_form input-group" id="count-'+variant.id+'" '+hideCounter+'><div class="value-button btn-minus" data-product='+product.id+' onclick="addToCartHome(this,'+variant.id+','+minus+')">-</div><span class="number_input col-md-2" id="qty-'+product.id+variant.id+'">'+cartCount+'</span><div class="value-button btn-plus" data-product="'+product.id+'"onclick="addToCartHome(this,'+variant.id+','+plus+',null,'+variant.max_qty+')">+</div></div></div><div class="favtag"><p class="font20 fontsemibold mb-0"><a href="javascript:void(0)" data-product='+product.id+' class='+is_favorite+'  data-product='+product.id+' onclick="addToFavourite(this,'+variant.id+','+fav+')"><i class="fa fa-heart" aria-hidden="true"></i></a></p></div></div></div></div></div>'
        $('.variant-body').append(str);
        $('.variant-modal').append(str);

        // $('#cart_popup').addClass('active');

    });
}


function getVals(){
         // Get slider values
         let parent = this.parentNode;
         let slides = parent.getElementsByTagName("input");
         let slide1 = parseFloat( slides[0].value );
         let slide2 = parseFloat( slides[1].value );

         $('.min_price').val('');
         $('.max_price').val('');


         $('.min_price').val(slide1);
         $('.max_price').val(slide2);


         // Neither slider will clip the other, so make sure we determine which is larger
         if( slide1 > slide2 ){ let tmp = slide2; slide2 = slide1; slide1 = tmp; }

         let displayElement = parent.getElementsByClassName("rangeValues")[0];
         displayElement.innerHTML = "$" + slide1 + " - $" + slide2;
         }

         window.onload = function(){
         // Initialize Sliders
         let sliderSections = document.getElementsByClassName("range-slider");
         for( let x = 0; x < sliderSections.length; x++ ){
         let sliders = sliderSections[x].getElementsByTagName("input");
         for( let y = 0; y < sliders.length; y++ ){
         if( sliders[y].type ==="range" ){
           sliders[y].oninput = getVals;
           // Manually trigger event first time to display values
           sliders[y].oninput();
         }
         }
         }
         }
      </script>
@endpush
