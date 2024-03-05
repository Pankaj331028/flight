@push('js')
<script>
    //hide element when not found
    $('.calendarProduct').hide();
    //initialize calendar
    @if(isset($item['calendar']))
    $(document).ready(function(){
      $("#calendar").MEC({
        events: sampleEvents,
      });
    });
    var series = [];


    @foreach($item['calendar'] as $calendar)
        series.push({
            title: 'Product found',
            date : "{{ $calendar['date'] }}",
            link : '#'
        })
    @endforeach

    var sampleEvents = series;

    $("#calendar").on("click touchstart", ".a-date", function(e){
        var data = $(this).data('event');
        if(data==null){
            $('.orderDetailWrapper3').hide()
            $('.calendarProduct').show()
        }else{
            var date = data.date;
            getProductDateWise(date);
            $('.calendarProduct').hide()
        }
    })
    @endif

    var end_date = $('#end_date').val();

    $('.re-date').datepicker({
        dateFormat: "{{ config('app.date_format_js') }}",
        minDate: new Date(end_date) ,
        onClose: function (selectedDate) {
            $('.to').datepicker( "option", "minDate", selectedDate );
        }
    });

    //cancel order
    $(document).on('submit', '#cancelProduct', function(e) {
        e.preventDefault();
        $.ajax({
        method: $(this).attr('method'),
        url: $(this).attr('action'),
        headers: {"Authorization": "Bearer {{Session::get('logtoken')}}"},
        data: $(this).serialize(),
        dataType: "json",
        success: function (data){
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
                    window.location.href = "{{ config('app.url') }}"
                }, 1000);
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

    function getProductDateWise(date,order_id){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            method: 'post',
            url: "{{url('product-date-order')}}",
            data: {
                'order_id' : $('#order_id').val(),
                'user_id'  : $('#user_id').val(),
                'date'     : date,
            },
            success: function (data){
                if(data.status==200){
                    $('.orderDetailWrapper3').replaceWith(data.html);
                }

            }
        });
    };

    function editReschedule(x){
        $('#editReschedule').modal('show');
        var id       = $(x).data('id');
        var date     = $(x).data('date');
        var itemId   = $(x).data('item_id');

        $('.order_id').val(id);
        $('.date, .re-date').val(date);
        $('.item_id').val(itemId);
    }

    $('.updateSchedule').on('click', function(e){
        e.preventDefault();
        var data = $('#reschedule').serialize();
        var self = $("input[name='set']:checked").val();
        if(self){
            if(self=='date'){
                var url = "{{url('api/rescheduleOrderItem')}}"
            }else{
                var url = "{{url('api/cancelOrder')}}"
            }

            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            });
            $.ajax({
                method: 'post',
                url: url,
                data: data,
                success: function(res)
                {
                    // var res = JSON.parse(res);
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
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
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
        }else{
            $('#editReschedule').modal('hide');
        }

    });
</script>
@endpush
