@push('js')
<script>
    alertMsg = window.localStorage.getItem('success');
    if(alertMsg)
    {
        $('.alert').removeClass('d-none');
        $('.alertMsg').html(alertMsg);
    }

    setTimeout(() => {
        window.localStorage.removeItem('success');
        $('.alert').addClass('d-none');
    }, 1000  * 10);

    function initMap() {
        var lat = $('#latitude').val();
        var lng = $('#longitude').val();

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 13,
            center: new google.maps.LatLng(lat, lng)
        });
        marker = new google.maps.Marker({
            map: map,
        });

        marker.setPosition(map.getCenter());

        map.addListener('click', function(e) {
            //update lat lng
            $('#latitude').val(e.latLng.lat());
            $('#longitude').val(e.latLng.lng());

            animatedMove(marker, .5, marker.position, e.latLng);
        });

        // move marker from position current to moveto in t seconds
        function animatedMove(marker, t, current, moveto) {
        var lat = current.lat();
        var lng = current.lng();

        var deltalat = (moveto.lat() - current.lat()) / 100;
        var deltalng = (moveto.lng() - current.lng()) / 100;

        var delay = 10 * t;
        for (var i = 0; i < 100; i++) {
            (function(ind) {
            setTimeout(
                function() {
                var lat = marker.position.lat();
                var lng = marker.position.lng();
                lat += deltalat;
                lng += deltalng;
                latlng = new google.maps.LatLng(lat, lng);
                marker.setPosition(latlng);
                }, delay * ind
            );
            })(i)
        }
        }
    }

    //get city list on selection of state
    $('.state').on('change',function(){
        $.ajax({
            type: 'post',
            url: "{{ url('api/cityList') }}",
            headers: {"Authorization": "Bearer {{Session::get('logtoken')}}"},

            data: {state_id : $(this).val()},
            dataType: "json",
            success : function(res){
                $('.city').empty();
                    $('.city').append('<option value="" selected disabled>Select City</option>');
                $.each(res.data,function(key,state){
                    $('.city').append('<option value='+state.id+'>'+state.name+'</option>');
                });
            }
        })
    });
    //get state list
    function getState(state_id){
        $.ajax({
            method: 'get',
            url: "{{url('api/stateList')}}",
            headers: {"Authorization": "Bearer {{Session::get('logtoken')}}"},

            success: function(res) {
                // var res = JSON.parse(res);
                $('#addressModal').find('select[name=state]').empty();
                            $('#addressModal').find('select[name=state]').append('<option value="" selected disabled>Select State</option>')
                if(res['data']){
                    $.each(res['data'],function(key,state){
                        if(state.id==state_id){
                            $('#addressModal').find('select[name=state]').append('<option value='+state.id+' selected>'+state.name+'</option>')
                        }else{
                            $('#addressModal').find('select[name=state]').append('<option value='+state.id+'>'+state.name+'</option>')
                        }
                    });
                }else{
                    $('#addressModal').find('select[name=state]').prepend('<option>No state found</option>');
                }
            }

        });
    }

    function getCity(state_id,city_id){
        $.ajax({
            method: 'post',
            url: "{{url('api/cityList')}}",
            data: {
                state_id: state_id,
            },
            success: function(res) {
                // var res = JSON.parse(res);
                $('#addressModal').find('select[name=city]').empty();
                            $('#addressModal').find('select[name=city]').append('<option value="" selected disabled>Select City</option>')

                if(res['data']){
                    $.each(res['data'],function(key,city){
                        if(city.id==city_id){
                            $('#addressModal').find('select[name=city]').append('<option value='+city.id+' selected>'+city.name+'</option>')
                        }else{
                            $('#addressModal').find('select[name=city]').append('<option value='+city.id+'>'+city.name+'</option>')
                        }
                    });
                }else{
                    $('#addressModal').find('select[name=city]').prepend('<option>No city found</option>');
                }

            }
        });
    }



    //add address modal
    $('.addAddress').on('click',function(){
        $('#addressModal').find('select[name=city]').empty();
        var state = $('select[name=state]').find("option:first-child").val();
        getCity(state,null);
        $('#addressModal').find('select[name=state]').empty();
        $('#addaddressTitle').html('New Address');
        $('#addressModal').find('form')[0].reset();
        $('#addressModal').modal('show');
        getState(null);
        $('#map').empty();
        $('#addressModal').find('select[name=state]').prepend('<option value="">Select State</option>');
        $('#addressModal').find('select[name=city]').prepend('<option value="">Select City</option>');
        // initMap();
    });

    //edit address
    $('.editAddress').on('click',function(){
        var value = $(this).data('value');
        $('#addaddressTitle').html('Edit Address');
		$('#addressModal').find('input[name=address_id]').val(value.id);
		$('#addressModal').find('input[name=first_name]').val(value.first_name);
		$('#addressModal').find('input[name=last_name]').val(value.last_name);
		$('#addressModal').find('input[name=mobile]').val(value.mobile);
		$('#addressModal').find('input[name=apartment]').val(value.apartment);
		$('#addressModal').find('input[name=address]').val(value.address);
        $('#addressModal').find('input[name=zipcode]').val(value.zipcode);
        $('#addressModal').find('input[name=lat]').val(value.lat);
        $('#addressModal').find('input[name=lng]').val(value.lng);
        $('#addressModal').find('select[name=address_type]').val(value.address_type);

        // $('#gmap_canvas').attr('src',"https://maps.google.com/maps?q=loc:"+value.lat+","+value.lng+"&output=embed");
        // $('#map').empty();
        // initMap();
        //get state selected
        getState(value.state_id);
        //get city selected
        getCity(value.state_id, value.city_id);
    });


    //add and update address
    $(document).on('submit', '#updateAddress', function(e) {
        e.preventDefault();
        $('.saveAddress').prop('disabled',true);
        $('input+small').text('');
        $('input').parent().removeClass('has-error');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'put',
            url: $(this).attr('action'),
            headers: {"Authorization": "Bearer {{Session::get('logtoken')}}"},
            data: $(this).serialize(),
            dataType: "json"
        })
        .done(function(data) {
            if(data.status==200){
                $('#addressModal').modal('hide');

                @if(request()->segment(1) != 'account-settings')
                    toastr.success(data.message,"Status",{
                    timeOut: 5000,
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": true,
                    "tapToDismiss": true
                    });
                @else
                    window.localStorage.setItem('success', data.message);
                @endif

                location.reload();
            }
            else if(data.errors){
                $('.saveAddress').prop('disabled',false);
                $.each(data.errors, function (key, value) {
                    var input = '#updateAddress input[name=' + key + ']';
                    $(input + '+small').text(value);
                    $(input).parent().addClass('has-error');
                });
            }else{
                $('#addressModal').modal('hide');
                $('.saveAddress').prop('disabled',false);
                @if(request()->segment(1) != 'account-settings')
                    toastr.success(data.message,"Status",{
                    timeOut: 5000,
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": true,
                    "tapToDismiss": true
                    });
                @else
                    window.localStorage.setItem('success', data.message);
                @endif
            }
        }).fail(function(data) {
            $('.saveAddress').attr('disabled',false);
            if(data.responseJSON.errors != ''){
                $.each(data.responseJSON.errors, function (key, value) {
                    var input = '#updateAddress input[name=' + key + ']';
                    var input1 = '#updateAddress select[name=' + key + ']';
                    $(input1 + '+small').text(value);
                    $(input + '+small').text(value);
                    $(input).parent().addClass('has-error');
                    $(input1).parent().addClass('has-error');
                });
            }
        })
    });



    //profile pic update
    let validExt = ['jpg', 'jpeg', 'png'];
    $(document).on('change', '#updatePic', function(e) {
        e.preventDefault();
        var extension = $('#imageUpload')[0].files[0].type.split('/')[1]
        if(validExt.indexOf(extension) == -1){
            toastr.error('Allowed extensions are JPG, JPEG, PNG',"Status",{
                timeOut: 5000,
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "tapToDismiss": true
            });
        }
            $('#save_btn').show();

    });

    $(document).on('click', '#save_btn', function(e) {
        e.preventDefault();
        var extension = $('#imageUpload')[0].files[0].type.split('/')[1]
        if(validExt.indexOf(extension) == -1){
            toastr.error('Allowed extensions are JPG, JPEG, PNG',"Status",{
                timeOut: 5000,
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "tapToDismiss": true
            });
        }else{
            if(confirm('Are you sure? You want update profile')){
                var formData = new FormData();
        formData.append('user_id',$('#user_id').val());
        formData.append('profile_picture',$('#imageUpload')[0].files[0]);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'Post',
            url: $(this).closest('form').attr('action'),
            data: formData,
            headers : {
                'Authorization' : "Bearer {{Session::get('logtoken')}}",
                'Accept' : 'application/json',
            },
            dataType: "json",
            contentType: false,
            processData: false,
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
                location.reload();
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
        })
        }
        }
    });

//remove address button

    $(document).on('click', '#removeAddress', function(e) {
        e.preventDefault();

       var id = $('#removeAddress_input').val();

        if(confirm('Are you sure? You want to delete selected address')){
        $.ajax({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            url: $(this).closest('form').attr('action'),
            data: {id:id},
            dataType: "json",
        })

        .done(function(data) {
            if(data.status==true){
                @if(request()->segment(1) != 'account-settings')
                    toastr.success(data.message,"Status",{
                    timeOut: 5000,
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": true,
                    "tapToDismiss": true
                    });
                @else
                    window.localStorage.setItem('success', data.message);
                @endif
                location.reload();
            }else{
                @if(request()->segment(1) != 'account-settings')
                    toastr.success(data.message,"Status",{
                    timeOut: 5000,
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": true,
                    "tapToDismiss": true
                    });
                @else
                    window.localStorage.setItem('success', data.message);
                @endif
                location.reload();
            }
        });
    }
});


//update password
$('#changePassword').on('submit',function(e){
    e.preventDefault();
    $.ajax({
        type: 'put',
        url: $(this).attr('action'),
        data: $(this).serialize(),
        dataType: "json",
        headers : {
            'Authorization' : "Bearer {{Session::get('logtoken')}}",
            'Accept' : 'application/json',
        },
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
            location.reload();
        }if(data.errors){
            $.each(data.errors, function (key, value) {
                var input = '#changePassword input[name=' + key + ']';
                $(input + '+small').text(value);
                $(input).parent().addClass('has-error');
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
    })
    .fail(function(data) {
        if(data.responseJSON.errors!=''){
            $.each(data.responseJSON.errors, function (key, value) {
                var input = '#changePassword input[name=' + key + ']';
                $(input + '+small').text(value);
                $(input).parent().addClass('has-error');
            });
        }
    });
})

$('.changeAddress').click(function(){

    $('#changeAddress').modal('show');
})

//update profile
$('#updateProfile').on('submit',function(e){
    e.preventDefault();
    $.ajax({
        type: 'put',
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
            location.reload();
        }
        if(data.errors){
            $.each(data.errors, function (key, value) {
                var input = '#updateProfile input[name=' + key + ']';
                $(input + '+small').text(value);
                $(input).parent().addClass('has-error');
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
    })
})
</script>
@endpush
