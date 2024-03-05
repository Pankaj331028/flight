<!-- ===== jQuery ===== -->
<script src="{{asset('/js/jquery.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<!-- ===== Bootstrap JavaScript ===== -->
<script src="{{asset('/js/bootstrap.min.js')}}"></script>
<!-- ===== Plugins JavaScript ===== -->
<script src="{{ asset('/js/clipboard.min.js')}}"></script>
<script src="{{ asset('/js/owl.carousel.js') }}"></script>
<script src="{{ asset('/js/magiczoomplus.js') }}"></script>
<script src="{{ asset('/js/magicscroll.js') }}"></script>
<script src="{{ asset('/plugins/jqueryui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('/js/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('/js/toastr/toastr.init.js') }}"></script>
<script src="{{ asset('/js/mini-event-calendar.js') }}"></script>
<script src="{{ asset('/plugins/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('/plugins/sweetalert/sweetalert.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>



<script type="text/javascript">
    var csrfToken = $('[name="csrf_token"]').attr('content');
    function refreshToken(){
        $.get('refresh-csrf').done(function(data){
            csrfToken = data; // the new token
        });
    }
    function setCookie(name,value,days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }
    function eraseCookie(name) {
        document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }

    function checkCookie(){
        var cookieEnabled = navigator.cookieEnabled;
        if (!cookieEnabled){
            document.cookie = "testcookie";
            cookieEnabled = document.cookie.indexOf("testcookie")!=-1;
        }
        return cookieEnabled || showCookieFail();
    }

    function showCookieFail(){
        $('#enableCookiesModal').modal('show');
    }
    checkCookie();
</script>

<!---Global function--->
<script>
    new ClipboardJS('.refer_code_wrapper'); //copy to clipboard

    //Tawk.to Script
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/5ebd3bbf8ee2956d73a11607/default';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();


    //get user current location
    if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition,showError);
        }
    function showError(error) {
        switch(error.code) {
            case error.PERMISSION_DENIED:
            console.log("User denied the request for Geolocation.");
            break;
            case error.POSITION_UNAVAILABLE:
            console.log("Location information is unavailable.");
            break;
            case error.TIMEOUT:
            console.log("The request to get user location timed out.");
            break;
            case error.UNKNOWN_ERROR:
            console.log("An unknown error occurred.");
            break;
        }
        showPosition();
    }

    function showPosition(position){
        if(position){
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            //send latitude and longitude
            $('#latitude').val(lat);
            $('#longitude').val(lng);
            var key = "{{\App\Library\Notify::getBusRuleRef('google_map_key')}}"
            $.get('https://maps.googleapis.com/maps/api/geocode/json?key='+key+'&latlng='+lat+','+lng, function(data){
                if(data.status=='OK'){
                    const locality = data.results[0].address_components.filter((obj) => {
                        return obj.types.includes('sublocality');
                    })[0].long_name;

                    const city = data.results[0].address_components.filter((obj) => {
                        return obj.types.includes('locality');
                    })[0].long_name;
                    console.log(city);
                    $('.area').html(locality+' , '+city);
                }
            });
        }else{
            var location = {{ \App\Library\Helper::getLocationFormIp() }}
            $('#latitude').val(location.lat);
            $('#longitude').val(location.lng);
            $('.area').html(location.city+' , '+location.region);
        }
    }
    //sticky header
    $(window).scroll(function(){
        var scroll = $(window).scrollTop();
        if (scroll > 50) {
            $(".navbar_wrapper").addClass("sticky-color");
        }
        else{
            $(".navbar_wrapper").removeClass("sticky-color");
        }

        var btn = $('#button');
        if ($(window).scrollTop() > 300) {
            btn.addClass('show');
        } else {
            btn.removeClass('show');
        }
    });

    $('#button').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop:0}, '300');
    });

    $(".menuToggle").click(function () {
        $(".menuBlock ul").addClass("showMenus");
    });

    $(".closeMenu").click(function () {
        $(".menuBlock ul").removeClass("showMenus");
    });

    $(".subMenumenu").click(function () {
        $(".subMenu ul").toggleClass("showSubMenus");
    });

    $(".scheduleClose").click(function () {
            $(this).closest(".row").find(".scheduleRow").hide();
    });

    $(".closenoti").click(function(){
        $(this).closest(".notificationBar").addClass("hideNoti");
    });

    $(".favtag p a").click(function(){
        $(this).each(function () {
            $(this).toggleClass("favdark");
        });
    });

    $(".scheduleClose").click(function () {
        $(this).closest(".row").find(".scheduleRow").hide();
    });

    $('input[name=profile_picture]').change(function(){
        var input = this;
        var url = $(this).val();
        var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
        if (input.files && input.files[0]&& (ext == "png" || ext == "jpeg" || ext == "jpg"))
            {
                
            if(input.files[0].size > 3145728){
                toastr.error('File size must be less than 3MB',"Status",{
                    timeOut: 5000,
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": true,
                    "tapToDismiss": true
                });
            }else{
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                    $('#imagePreview').hide();
                    $('#imagePreview').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    })

    //detail-page script
    jQuery(".qutBox-1 .minus").click(function () {
        var curVal = (parseInt(jQuery(".qutBox-1 .qutInput input").val()) - 1);
        var val = (curVal - 1) < 0 ? 0 : curVal - 1;
        jQuery(".qutBox-1 .qutInput input").val(val);
    });
    jQuery(".qutBox-1 .plus").click(function () {
        var curVal = jQuery(".qutBox-1 .qutInput input").val();
        jQuery(".qutBox-1 .qutInput input").val(parseInt(curVal) + parseInt(1));
    });

    //index
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
                nav: false,
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
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

$(document).ready(function() {
    // Open active tab based on button clicked
        $('.btn-modal').on('click', function() {
            var switchTab = $(this).data('tab');
            activaTab(switchTab);
            function activaTab(switchTab) {
                $('.nav-tabs a[href="#' + switchTab + '"]').tab('show');
            };
            if(switchTab === 'register') {
            $('.modal_tabs li').removeClass('active') ;
            $('.sign_dyna').addClass('active') ;
            }else{
                $('.modal_tabs li').removeClass('active') ;
                $('.login_dyna').addClass('active') ;
            }
        });

        $('.modal_tabs li').click(function(){
            $('.modal_tabs li').removeClass('active') ;
            $(this).addClass('active') ;
        }) ;
        $('.sign_active').click(function(){
            $('.modal_tabs li').removeClass('active') ;
            $('.sign_dyna').addClass('active') ;

        }) ;
        $('.login_active').click(function(){
            $('.modal_tabs li').removeClass('active') ;
            $('.login_dyna').addClass('active') ;
        }) ;

        $('.reset-pwd').on('click', function() {
            $('#forgot-password').modal('show');
            $('#loginSignupModal').modal('hide');
        });

        $('.socialShare').click(function(){
            $('#referModal').modal('hide');
            $('#socialShare').modal('show');
        })
        $(".control_refer").hide();
    });

    //refer
    $(function () {
        $(".check_refer").click(function () {
            if ($(this).is(":checked")) {
                $(".control_refer").show();
                $('.control_refer').val('');
            } else {
                $('.control_refer').val('');
                $(".control_refer").hide();

            }
        });
    });

    //user login
    $(document).on('submit', '#userLogin', function(e) {
        if(!csrfToken){
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
        }else{
            var csrfToken = csrfToken;
        }
        e.preventDefault();
        $('.s-login').html('Please wait...');
        $('.s-login').attr('disabled',true);
        $('input+small').text('');
        $('input').parent().removeClass('has-error');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
        $.ajax({
            type: 'post',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: "json"
        })
        .done(function(data) {
            if(data.status==200){
                if(data.role=='user'){
                    // setCookie('login_session',(btoa(data.email)+"{{time()}}"),365);
                    toastr.success(data.message,"Status",{
                        timeOut: 5000,
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": true,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": true,
                        "tapToDismiss": true
                    });

                    setTimeout(function () {
                        $('#loginSignupModal').modal('hide');
                        location.reload();
                        // window.location.href = data.url;
                    }, 1000);
                }else{
                    $('.s-login').html('Login');
                    $('.s-login').attr('disabled',false);
                    toastr.error('Invalid credentials',"Status",{
                        timeOut: 5000,
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": true,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": true,
                        "tapToDismiss": true
                    });
                }
            }else if(data.errors){
                $('#opensignup').find('small').empty();
                $('.s-login').html('Login');
                    $('.s-login').attr('disabled',false);
                    $.each(data.errors, function (key, value) {
                        var input = '#userLogin input[name=' + key + ']';
                        $(input + '+small').text(value);
                        $(input).parent().addClass('has-error');
                    });
            }else{
                $('.s-login').html('Login');
                $('.s-login').attr('disabled',false);
                toastr.error(data.message,"Status",{
                timeOut: 5000,
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "tapToDismiss": true
                });
            }
        })
        .fail(function(data) {
            $('.s-login').html('Login');
            $('.s-login').attr('disabled',false);
            if(data.responseJSON.errors!=''){
                $.each(data.responseJSON.errors, function (key, value) {
                    var input = '#formLogin input[name=' + key + ']';
                    $(input + '+small').text(value);
                    $(input).parent().addClass('has-error');
                });
            }
        });
    });

    //register user
    $(document).on('submit', '#signup', function(e) {
        e.preventDefault();
        $('#sub_button').html('Please wait...');
        $('#sub_button').attr('disabled',true);
        $('small').text('');
        $('#signup input').parent().removeClass('has-error');

        $.ajax({
            method: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: "json",
        })
        .done(function(data) {
                if(data.status==200){
                    toastr.success(data.message,"Status",{
                    timeOut: 5000,
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": true,
                    "tapToDismiss": true
                    });

                    setTimeout(function () {
                        $('#signup').modal('hide');
                        location.reload();
                    }, 1000);
                }else{
                    $('#sub_button').html('Sign Up');
                    if(data.errors){
                        $('#userLogin').find('small').empty();
                        console.log(data.errors);
                        $('#sub_button').attr('disabled',false);
                        $.each(data.errors, function (key, value) {
                            console.log(key);
                            var input = '#signup [name=' + key + ']';
                            $('#signup small.'+key).text(value);
                            $(input).parent().addClass('has-error');
                        });
                    }else{
                        $('#sub_button').attr('disabled',false);
                        toastr.error(data.message,"Status",{
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
            })
            .fail(function(data) {
                $('#sub_button').html('Sign Up');
                if(data.responseJSON.errors){
                    $('#sub_button').attr('disabled',false);
                    $.each(data.responseJSON.errors, function (key, value) {
                        var input = '#signup input[name=' + key + ']';
                        $('#signup small.'+key).text(value);
                        $(input).parent().addClass('has-error');
                    });
                }else{
                    toastr.error(data.responseJSON.message);
                }
            });
        });
        
        //forgot password
        $(document).on('submit', '#resetPassword', function(e) {
        e.preventDefault();
        // $(".loader_div").show();
        $('#resetSbt').attr('disabled',true);
        $('input+small').text('');
        $('input').parent().removeClass('has-error');
        $.ajax({
            type: 'post',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: "json"
        })
        .done(function(data) {
            if(data.status==200){
                toastr.success(data.message,"Status",{
                    timeOut: 5000,
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": true,
                    "tapToDismiss": true
                    });

                    setTimeout(function () {
                        $('#loginSignupModal').modal('hide');
                        location.reload();
                        // window.location.href = data.url;
                    }, 1000);

            }else{
                $('#resetSbt').attr('disabled',false);
                toastr.error(data.message,"Status",{
                timeOut: 5000,
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "tapToDismiss": true
                });
            }
        }).fail(function(data) {
            var data = data.responseJSON;
            $('#resetSbt').attr('disabled',false);
            toastr.error(data.message,"Status",{
                timeOut: 5000,
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "tapToDismiss": true
            });
            });
        });

        //social signup when no email found

        @if(Session::get('social-user'))
         $('#social-signup').modal('show');
        @endif

        function getRating(x){
            var rating = $(x).val();
            $('.star').val(rating);
        }

        $('#rateStar').on('submit',function(e){
            e.preventDefault();
            if($('.star').val()==''){
                $('#rateusModal').modal('hide');
            }else{
                $.ajax({
            method: "post",
            url: "{{ url('api/submitRating') }}",
            data: {user_id: "{{ isset($auth_user->id) ? $auth_user->id : '' }}" , rate: $('.star').val()},
            dataType: "json",
                success: function (res) {
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

                    }else{
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
                }
                });
            }

        });

        if($(".search").length > 0){
            // define array for caching the results for better performance
            var productCache = {};
            $(".search").autocomplete({
                source: function (request, response) {
                // if the term is new, then fetch the results or else use the original results
                if (!productCache[request.term]) {
                    productCache[request.term] = $.ajax({
                        url: "{{ url('api/searchProduct') }}",
                        data: { text: request.term, type: 'autocomplete' },
                        dataType: "json",
                        type: "post",
                    });
                }

                productCache[request.term].done(function(json){
                    //append value
                   if(json.search_status=='21'){
                        var result = [
                            {
                                id: '',
                                label: 'No product found',
                            }
                        ];
                        return response(result);
                    }else{
                        response($.map(json.data, function (el) {
                        var url = "{{ url('listing') }}";
                            return {
                                id: el.id,
                                label: el.text,
                                image: el.image,
                                href: url+'/'+el.type+'/'+el.id+'/'+el.category_id,
                            };
                        }));
                    }

                }).fail(function(json){
                    //append value
                   if(json.responseJSON.search_status=='21'){
                        var result = [
                            {
                                id: '',
                                label: 'No product found',
                            }
                        ];
                        return response(result);
                    }

                });
            },
            classes : {
                "ui-autocomplete": "productList dropdown-menu"
            },
            }).data('ui-autocomplete').
                _renderItem= function( ul, item ) {
                    if(item.id==""){
                        return $( "<li>" )
	                    .append( item.label )
	                    .appendTo( ul );
                    }else{
                        return $( "<a href="+item.href+">" )
                        .append("<li><img class='mr-2' src='"+item.image+"' height='35' width='40'><p>"+item.label+"</p></li>")
                        .appendTo( ul );
                    }
            };
        }

        //social share
        var popupSize = {
            width: 780,
            height: 550
        };

        $(document).on('click', '.social-buttons > a', function(e){
            console.log('work');
            var
                verticalPos = Math.floor(($(window).width() - popupSize.width) / 2),
                horisontalPos = Math.floor(($(window).height() - popupSize.height) / 2);

            var popup = window.open($(this).prop('href'), 'social',
                'width='+popupSize.width+',height='+popupSize.height+
                ',left='+verticalPos+',top='+horisontalPos+
                ',location=0,menubar=0,toolbar=0,status=0,scrollbars=1,resizable=1');

            if (popup) {
                popup.focus();
                e.preventDefault();
            }

        });
</script>
<script type="text/javascript">
	@foreach (['error', 'warning', 'success', 'info'] as $key)
		@if(Session::has($key))
			toastr.{{$key}}('{{ Session::get($key) }}',"Status",{
		        timeOut: 5000,
		        "closeButton": true,
		        "debug": false,
		        "newestOnTop": true,
		        "progressBar": true,
		        "positionClass": "toast-top-right",
		        "preventDuplicates": true,
		        "onclick": null,
		        "showDuration": "300",
		        "hideDuration": "1000",
		        "extendedTimeOut": "1000",
		        "showEasing": "swing",
		        "hideEasing": "linear",
		        "showMethod": "fadeIn",
		        "hideMethod": "fadeOut",
		        "tapToDismiss": false

		    })
		    @php
		    	Session::forget($key)
		    @endphp
		@endif
	@endforeach
</script>
