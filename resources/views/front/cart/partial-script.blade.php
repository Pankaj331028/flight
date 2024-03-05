@push('js')

<script src="{{URL::asset('/js/jquery.validate.min.js')}}"></script>
<script>
    var user_id = $('#user_id').val(); //global variable for userid
    //delete cart item function

    $('#form_validate').validate({ // initialize the plugin

        errorPlacement: function(e, a) {
                jQuery(a).closest(".form-group").append(e)
            },
        rules: {
            card_no: {
                required: true
            },
            cvv: {
                required: true
            },

            year: {
                required: true
            },
            month: {
                required: true
            },
            card_no: {
                required: true
            },

            payment_type: {
                required: true
            },

            card_type: {
                required: true
            }

        }
    });



    function deleteCartItem(item_id)
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            method: 'delete',
            url: "{{url('api/deleteCartItems')}}",
            headers: {"Authorization": "Bearer {{Session::get('logtoken')}}"},

            data: {
                // user_id: "{{$auth_user->id ?? '' }}",
                item_id: item_id,
            },
            success:function(data){
                // var data = JSON.parse(res);
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
                    location.reload();
                }else{
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
            error: function(data){
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
        })
    }

    $('#placeOrder').click(function(e){
        e.preventDefault();

        if($('#form_validate').valid()){
            var cvv = $("input[name=cvv]").val();
            var card_no = $("input[name=card_no]").val();
            var card_type = $('input[name="card_type"]:checked').val();
            var year = $("select[name=year]").val();
            var month = $("select[name=month]").val();
            var payment_method = $("#payment_type").val();

            $.ajax({
                type: 'post',
                url: "{{ url('api/createOrder') }}",
                headers: {"Authorization": "Bearer {{Session::get('logtoken')}}"},
                data: {year:year,month:month,cvv:cvv,card_type:card_type,card_no:card_no, payment_method:payment_method},
                // data: {user_id : $('#user_id').val(), year:year,month:month,cvv:cvv,card_type:card_type,card_no:card_no, payment_method:payment_method},
                dataType: "json",
                beforeSend:function(data){


                    // Show image container
                    $(".loader").show();
                    $('#placeOrder').attr('disabled',true);



                },
                complete:function(data){
                    if(data.status==504){
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
                    // Hide image container
                    $(".loader").hide();
                    $('#placeOrder').attr('disabled',false);
                },
                success: function(data) {
                    if(data.status==504){
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
                    // console.log(data);
                    // console.log('__________________________');

                    if(data.status==200){
                        $(".loader").hide();
                        swal({
                            icon: "success",
                            title: "Thank you",
                            text: "Your order is successfully placed !",
                            timer: 3000,
                            button: false ,
                            }).then(function() {
                                window.location.href= "{{ config('app.url') }}/order-detail/"+data.order_id
                            });
                    }
                    if(data.status!=200){
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
            });
        }
        else{
            $('html,body').animate({
                scrollTop:$('.error:first').offset().top-200
            },500);
        }
    })

    $('.applyCoupon').click(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'post',
            url: "{{ url('applyCouponCode') }}",
            data: {
                code    : $('#coupon_code').val(),
                cart_id : $('#cart_id').val(),
                user_id : $('#user_id').val(),
            },
            dataType: "json",
            success : function(data){
                if(data.status==200){
                    $('.cart_details').empty().html(data.html);

                    //coupon applied
                    $('.codeBlock').css('display','flex');
                    $('.couponCodeApplied').html(data.code.code);
                    $('#coupon_code').val('');
                    $('#code').val(data.code.id);

                    toastr.success(data.message,"Status",{
                        timeOut: 5000,
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": true,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": true,
                        "tapToDismiss": true
                    });
                }else{
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
    });

    function removeCoupon ()
    {
        $.ajax({
            type: 'delete',
            url: "{{ url('api/deleteCouponCode') }}",
            headers: {"Authorization": "Bearer {{Session::get('logtoken')}}"},

            data: {
                code_id : $('#code').val(),
                cart_id : $('#cart_id').val(),
                // user_id : $('#user_id').val(),
            },
            dataType: "json",
            success : function(data){
                if(data.status==200){
                    location.reload();
                    $('.codeBlock').hide();
                    toastr.success(data.message,"Status",{
                        timeOut: 5000,
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": true,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": true,
                        "tapToDismiss": true
                    });
                }else{
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
    }

    function scheduleDelivery(type,item_id)
    {
        if(type=='single'){
            $('#scheduleSingleDay').modal('show');
            // $('#singleDay').prop('checked',true);
        }else{
            $('#scheduleMultipleDay').modal('show');
            // $('#multipleDay').prop('checked',true);
        }
        //send item id to single and multiple submit buttons
        $('.item_id').val(item_id);
        //
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
        method: 'post',
        url: "{{url('api/getDeliverySlots')}}",
        headers: {"Authorization": "Bearer {{Session::get('logtoken')}}"},

        data: {
            type: type,
		    user_id: user_id,
		    item_id: item_id,
        },
        success:function(res){
            // var res = JSON.parse(res);
            var maxDate;
            if(type=="single"){
                var singleDays = res.days;
                $('.singleSchedule').empty();
                var i =1;

                let result = singleDays.map(singleDay => singleDay.is_selected==0);
                // let result = bigCities.every( e  => e == false);
                // console.log(result);
                result = result.includes(true);
                if(result==true){
                    getSlots(singleDays[0].slots,res.selected_time);
                }
                //when selected time not in list send default
                var defaultSlot = singleDays[0];
                $.each(singleDays, function(key,value){
                    var str       = moment(value.date).format('ll');
                    var dateMonth = str.substring(0, str.indexOf(','));
                    var slots     = JSON.stringify(value.slots);
                    if(value.is_selected==1){
                        getSlots(value.slots,res.selected_time);
                    }
                    var row = "<label for='s_"+i+"' class='s_"+i+" modal_flex_grid' onchange='getSlots("+slots+','+res.selected_time+")'><div class='modal_grid_inner'><input type='radio' id='s_"+i+"' name='date' value="+value.date+"><h2 class='font22 font-weight-bold'>"+value.day+"</h2><p>"+dateMonth+"</p></div></label>";

                    $('.singleSchedule').append(row);

                    if(value.is_selected==1){
                        $('.s_'+i).addClass('active');
                    }
                    i++;
                });
                $('.grid_timing_wrapper .modal_flex_grid').click(function(){
                    $('.grid_timing_wrapper .modal_flex_grid').removeClass('active');
                    $(this).addClass('active');
                }) ;
            }else{
                $('.from').val(res.start_date);
                $('.to').val(res.end_date);
                var toMinDate =  $('.from').val();

                $('.from').datepicker({
                    dateFormat: "{{ config('app.date_format_js') }}",
                    minDate: new Date(res.start_date) ,
                    onClose: function (selectedDate) {
                        $('.to').datepicker( "option", "minDate", selectedDate );
                    }
                });

                $('.to').datepicker({
                    dateFormat: "{{ config('app.date_format_js') }}",
                    minDate: toMinDate ,
                });

                $('.multipleTimeSlot').empty();
                console.log(res.slots);
                if(res.slots.length==0){
                    $(".multipleTimeSlot").append('<li class="mb-2 text-danger" style="width: 450px;">Sorry! This date delivery slots are full. Please check with next day available delivery slots. Thank you</li>');
                }else{
                    $.each(res.slots,function(index,time){
                    if(time.disabled){
                        var disbaled = 'disabled';
                        var msg      = time.warning_msg;
                    }else{
                        var disbaled = '';
                        var msg      = '';
                    }
                    if(time.is_selected==1){
                        $(".multipleTimeSlot").append('<li class="mb-2"><div class="custom-control custom-radio"><input type="radio" id='+time.id+' name="slot" value='+time.id+' class="custom-control-input" checked><label class="custom-control-label font14 label_weight_text color20" for='+time.id+' checked>'+time.slot+'<p class="text-danger">'+msg+'</p></label></div></li>');
                    }else{
                        $(".multipleTimeSlot").append('<li class="mb-2"><div class="custom-control custom-radio"><input type="radio" id='+time.id+' name="slot" value='+time.id+' class="custom-control-input"><label class="custom-control-label font14 label_weight_text color20" for='+time.id+'>'+time.slot+'<p class="text-danger">'+msg+'</p></label></div></li>');
                    }
                    });
                }
            }
        },
        error:function(){

        }
        })
    }

    //get time slots
    function getSlots(slots,id){
        $('.timeSlot').empty();
        $.each(slots, function(index,time){
            var x = time.is_available;
            if(typeof x !== 'undefined'){
                console.log(time);
                // if(time.is_available==0){
                    var row = '<li class="mb-2 text-danger" style="width: 450px;">'+time.warning_msg+'</li>'
                // }
            }else{
                console.log(time);
                if(time.disabled){
                var disabled = 'disabled';
                var msg      = time.warning_msg;
                }else{
                    var disabled = '';
                    var msg      = '';
                }
                if(id==time.id){
                    var row = '<li class="mb-2"><div class="custom-control custom-radio"><input type="radio"  value='+time.id+' id='+time.id+' name="slot" class="custom-control-input" checked '+disabled+' value='+time.id+'><label class="custom-control-label font14 label_weight_text color20" for='+time.id+'>'+time.slot+'<p class="text-danger">'+msg+'</p></label></div></li>'
                }else{
                    var row = '<li class="mb-2"><div class="custom-control custom-radio"><input type="radio" value='+time.id+' id='+time.id+' name="slot" class="custom-control-input" '+disabled+' ><label class="custom-control-label font14 label_weight_text color20" for='+time.id+'>'+time.slot+'<p class="text-danger">'+msg+'</p></label></div></li>'
                }
            }

            $('.timeSlot').append(row);
        });

    }

    function saveSchedule(id,type)
    {
        var item_id = $('.item_id').val();
        var data    = $('#'+id).serializeArray();
        data.push({name : 'cart_item_id',value : item_id},{name : 'user_id',value : user_id},{name : 'type',value : type});
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var result = data.filter(data => data.name=='slot');
        if(result.length>0){
            $.ajax({
                method: 'post',
                url: "{{url('api/setProductDateTime')}}",
                headers: {"Authorization": "Bearer {{Session::get('logtoken')}}"},

                data: data,
                success:function(data){
                    location.reload();
                }
            });
        }else{
            toastr.error('Please select date and time',"Status",{
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

    //set cart payment method
    $('.editPayment').on('change',function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            method: 'post',
            url: "{{url('api/setCartPayment')}}",
            headers: {"Authorization": "Bearer {{Session::get('logtoken')}}"},

            data: {
                user_id        : user_id,
                cart_id        : $('#cart_id').val(),
                payment_method : $(this).val(),
            },
            success:function(data){
                // var data = JSON.parse(res);
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
                }else{
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
        });
    });

    //use credit points
    $('.useWallet').on('click', function(e){
        if($(this).is(':checked')){
            var value = 1;
        }else{
            var value = 0;
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            method: 'post',
            url: "{{url('setCartWallet')}}",
            data: {
                user_id : user_id,
                credit  : value,
            },
            success: function(res)
            {
                var data = JSON.parse(res);
                if(res.status==200){
                    $('.cart_details').empty().html(res.html);
                    toastr.success(res.message,"Status",{
                    timeOut: 5000,
                    timeOut: 5000,
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": true,
                    "tapToDismiss": true
                    });
                }else{
                    toastr.success(res.message,"Status",{
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
    });

    //add to cart
    function addToCartProduct(x,id=null,type=null,rs=null,qty=null){




        // $('#cart-'+id).prop('disabled',true);
        var product_id = $(x).data('product');
        var selected_productID = $('#prod-'+id).val();
        if(type=="plus"){
          increaseValue(id,product_id,qty);


          var current_value = $('.cart_count').html();
          var a = current_value;


          var c = parseInt(a) + parseInt(1);
          $('.cart_count').html(c)
          $('.cart_qty').html(c)


        }
        else if (type=="minus")
        {
            var current_value = $('.cart_qty').html();
            var c = parseInt(current_value) - parseInt(1);
            $('.cart_qty').html(c);
            decreaseValue(id,product_id,x);
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
        method: 'post',
        url: "{{url('addToCartProduct')}}",
        data: {
            type: type,
            response_type : rs, // favourite
		    product_id: product_id,
		    product_variation_id: id,
        },
        success: function(res)
        {
            if(res.status==200){
                $('#cart-'+id).prop('disabled',false);
                $('.cart_details').empty().html(res.html);

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
                "tapToDismiss": true,
            });
            }else{

                if(res.status==400){
                    // var current_value = $('.cart_count').html();
                    // var a = current_value;
                    // var b = 1;
                    // var c = parseInt(a) - parseInt(1);
                    // $('.cart_count').html(c);
                }


                if(res.status==321){
                    var message = 'Please login to continue.' ;
                } else{
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
</script>





@endpush
