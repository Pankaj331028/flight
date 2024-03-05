@extends('layouts.app')
@section('title', 'Order - ' . ucfirst($order->order_no))

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Branch</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('orders')}}">Orders</a></li>
                    <li class="breadcrumb-item active">View Order</li>
                </ol>
            </div>
        </div>
        @include('layouts.message')
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-lg-12 col-xlg-12 col-md-12">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#profile" role="tab">Details</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#orders" role="tab">Order Items</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#scheduling" role="tab">Daily Scheduling</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#driver-assign" role="tab">Drivers Assigned</a> </li>

                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="profile" role="tabpanel">
                            <div class="card-body">
                                @if (!in_array($order->status, ['PN', 'CM','RN']))
                                    <div class="float-right">
                                        <h6><a href="{{ route('viewOrderReturn', ['id' => $return->id]) }}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Cancel Request" target="_blank">View Order Cancel Request</i></a></h6>
                                    </div>
                                @elseif ($order->status== 'CM' && $singleday==0)
                                    <div class="float-right">
                                        <h6><a href="{{ route('returnOrder', ['id' => $order->id]) }}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Click to return complete order">Return Complete Order</i></a></h6>
                                    </div>
                                @endif
                                @if ($singleday==0 && $order->status=='PN')
                                    <div class="float-right">
                                        <h6><a href="{{ route('completeOrder', ['id' => $order->id]) }}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Click to complete order">Complete Order</i></a></h6>
                                    </div>
                                    <div class="float-right overall">
                                        <form class="form-inline px-3 form-valide" method="post" action="{{route('assignDriver',['id'=>$order->id])}}">
                                            <div class="form-group">
                                                {{csrf_field()}}
                                                <label for="assigndriver" class="px-3">Assign Driver</label>
                                                <select class="form-control" name="driver_id" id="assigndriver" required>
                                                    <option value="">Select Driver</option>
                                                    @foreach($drivers as $driver)
                                                        <option value="{{$driver->id}}">{{ucwords($driver->first_name." ".$driver->last_name)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                                <div>
                                    <div class="col-md-4 columns">
                                        <h6 class="p-t-20">Order No</h6><small class="text-primary font-15 db">{{$order->order_no}}</small>
                                    </div>
                                    <div class="col-md-4 columns">
                                        <h6 class="p-t-20">Order Code</h6><small class="text-primary font-15 db">{{$order->order_code}}</small>
                                    </div>
                                    <div class="col-md-4 columns">
                                        <h6 class="p-t-20">User</h6><small class="text-primary font-15 db">{{$order->user->first_name." ".$order->user->last_name}}</small>
                                    </div>


                                    <div class="col-md-4 columns">
                                        <h6 class="p-t-20">Payment Method</h6><small class="text-primary font-15 db">{{Config::get('constants.PAYMENT_METHOD.'.$order->payment_method)}} ({{ $order->card_type ?? ''}})  </small>
                                    </div>

                                    <div class="col-md-4 columns">
                                        <h6 class="p-t-20"> Card No </h6><small class="text-primary font-15 db"> XXXX-XXXX-{{substr($order->card_no, -4)}} </small>
                                    </div>

                                    <div class="col-md-4 columns">
                                        <h6 class="p-t-20">Total Amount</h6><small class="text-primary font-15 db">{{Session::get('currency').Helper::format_number($order->total_amount)}}</small>
                                    </div>
                                    <div class="col-md-4 columns">
                                        <h6 class="p-t-20">Coupon Discount</h6><small class="text-primary font-15 db">{{Session::get('currency').Helper::format_number($order->coupon_discount)}}</small>
                                    </div>
                                    <div class="col-md-4 columns">
                                        <h6 class="p-t-20">Shipping Fee</h6><small class="text-primary font-15 db">{{Session::get('currency').Helper::format_number($order->shipping_fee)}}</small>
                                    </div>
                                    <div class="col-md-4 columns">
                                        <h6 class="p-t-20">Savings</h6><small class="text-primary font-15 db">{{Session::get('currency').Helper::format_number($order->savings)}}</small>
                                    </div>
                                    <div class="col-md-4 columns">
                                        <h6 class="p-t-20">Cancelled Amount</h6><small class="text-primary font-15 db">{{Session::get('currency').Helper::format_number($order->cancelled_amount)}}</small>
                                    </div>
                                    <div class="col-md-4 columns">
                                        <h6 class="p-t-20">Wallet Used</h6><small class="text-primary font-15 db">{{ucfirst(config('constants.CONFIRM.'.$order->wallet))}}</small>
                                    </div>
                                    <div class="col-md-4 columns">
                                        <h6 class="p-t-20">Credits Used</h6><small class="text-primary font-15 db">{{Session::get('currency').Helper::format_number($order->credits_used)}}</small>
                                    </div>
                                    <div class="col-md-4 columns">
                                        <h6 class="p-t-20">Delivery Address</h6><small class="text-primary font-15 db">{{$order->user_address->first_name . " " . $order->user_address->last_name . ", " . $order->user_address->apartment . ", " . $order->user_address->address . ", " . $order->user_address->acity->name . ", " . $order->user_address->astate->name . ", " . $order->user_address->acountry->name . "- " . $order->user_address->zipcode}}</small>
                                    </div>
                                    <div class="col-md-4 columns">
                                        <h6 class="p-t-20">Status</h6><small class="text-primary font-15 db">{{ucfirst(config('constants.STATUS.'.$order->status))}}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="orders" role="tabpanel">
                            <div class="card-body">
                                @if (!in_array($order->status, ['PN', 'CM','RN']))
                                    <div class="float-right">
                                        <h6><a href="{{ route('viewOrderReturn', ['id' => $return->id]) }}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Cancel Request" target="_blank">View Order Cancel Request</i></a></h6>
                                    </div>
                                @elseif ($order->status== 'CM' && $singleday==0)
                                    <div class="float-right">
                                        <h6><a href="{{ route('returnOrder', ['id' => $order->id]) }}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Click to return complete order">Return Complete Order</i></a></h6>
                                    </div>
                                @endif
                                <div class="table-responsive m-t-40">
                                    <table id="ordersTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Price (in {{session()->get('currency')}})</th>
                                                <th>Special Price (in {{session()->get('currency')}})</th>
                                                <th>Coupon Code Used</th>
                                                <th>Coupon Discount(%)</th>
                                                <th>Type</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Delivery Slot</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Price (in {{session()->get('currency')}})</th>
                                                <th>Special Price (in {{session()->get('currency')}})</th>
                                                <th>Coupon Code Used</th>
                                                <th>Coupon Discount(%)</th>
                                                <th>Type</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Delivery Slot</th>
                                                <th>Status</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            @php
                                                $i=1;
                                            @endphp
                                            @foreach($order->order_items as $value)
                                                <tr>
                                                    <td>{{$i++}}</td>
                                                    <td>{{$value->qty}}</td>
                                                    <td>{{$value->price}}</td>
                                                    <td>{{$value->special_price}}</td>
                                                    <td>{{($value->coupon_code_id!=null)?$value->coupon_range->coupon_code->code:'-'}}</td>
                                                    <td>{{($value->coupon_discount!=null)?$value->coupon_discount:'-'}}</td>
                                                    <td>{{($value->scheduled!='1')?'Single Day Order':'Multiple Scheduling'}}</td>
                                                    <td>{{date('Y-m-d',strtotime($value->start_date))}}</td>
                                                    <td>{{date('Y-m-d',strtotime($value->end_date))}}</td>
                                                    <td>{{date('H:i A',strtotime($value->delivery_slot->from_time))."-".date('H:i A',strtotime($value->delivery_slot->to_time))}}</td>
                                                    <td>{{Config::get('constants.STATUS.'.$value->status)}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="scheduling" role="tabpanel">
                            <div class="card-body">
                                <div class="table-responsive m-t-40">
                                    <table id="schedulingTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Price (in {{session()->get('currency')}})</th>
                                                <th>Special Price (in {{session()->get('currency')}})</th>
                                                <th>Coupon Code Used</th>
                                                <th>Coupon Discount(%)</th>
                                                <th>Type</th>
                                                <th>Delivery Slot</th>
                                                <th>Date</th>
                                                <th>Rescheduled</th>
                                                <th>Reschedule Date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Price (in {{session()->get('currency')}})</th>
                                                <th>Special Price (in {{session()->get('currency')}})</th>
                                                <th>Coupon Code Used</th>
                                                <th>Coupon Discount(%)</th>
                                                <th>Type</th>
                                                <th>Delivery Slot</th>
                                                <th>Date</th>
                                                <th>Rescheduled</th>
                                                <th>Reschedule Date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            @php
                                                $i=1;
                                            @endphp
                                            @foreach($schedules as $value)
                                                <tr>
                                                    <td>{{$i++}}</td>
                                                    <td>{{$value->product}}</td>
                                                    <td>{{$value->qty}}</td>
                                                    <td>{{$value->price}}</td>
                                                    <td>{{$value->special_price}}</td>
                                                    <td>{{$value->coupon_code}}</td>
                                                    <td>{{$value->coupon_disc}}</td>
                                                    <td>{{$value->type}}</td>
                                                    <td>{{$value->time}}</td>
                                                    <td>{{$value->date}}</td>
                                                    <td>{{$value->rescheduled}}</td>
                                                    <td>{{$value->reschedule_date}}</td>
                                                    <td>{{$value->status}}</td>
                                                    <td>{!! $value->action !!}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="driver-assign" role="tabpanel">
                            <div class="card-body">
                                <div class="table-responsive m-t-40">
                                    <table id="schedulingTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Delivery Dates</th>
                                                <th>Action</th>
                                                @if(isset($assignedDriver))<th>Status</th>@endif
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Delivery Dates</th>
                                                <th>Action</th>
                                                @if(isset($assignedDriver))<th>Status</th>@endif
                                            </tr>
                                        </tfoot>
                                        <tbody>

                                            @php
                                                $i=1;
                                            @endphp
                                            @if(isset($assignedDriver))
                                                <tr>
                                                    <td>{{$i++}}</td>
                                                    <td>{{ $assignedDriver->date }}</td>
                                                    <td>{{ ucfirst($assignedDriver->driver->first_name).' '.ucfirst($assignedDriver->driver->last_name) }}</td>
                                                    <td>Delivered</td>
                                                </tr>
                                            @else
                                                @foreach($dates as $value)
                                                    <tr>
                                                        <td>{{$i++}}</td>
                                                        <td>{{$value->order_date}}</td>
                                                        <td>
                                                            <div>
                                                                <select class="form-control" id="driver_id_{{$value->order_date}}"  required>
                                                                    <option value="">Select Driver</option>
                                                                    @foreach($drivers as $driver)
                                                                        <option value="{{$driver->id}}" @if($value->driver_id==$driver->id) selected="selected" @endif>{{ucwords($driver->first_name." ".$driver->last_name)}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>
@endsection

@push('scripts')
    <script src="{{URL::asset('/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript">
        $(function(){
            $('#ordersTable').DataTable({

                paging: true,
                pageLength: 10,
                // "bProcessing": true,
                "bLengthChange": false,
                "aaSorting": [],
                "bFilter": false
            });
            $('#schedulingTable').DataTable({

                paging: true,
                pageLength: 10,
                // "bProcessing": true,
                "bLengthChange": false,
                "aaSorting": [],
                "drawCallback": function(settings){
                    $(".bt-switch input[type='checkbox']").bootstrapSwitch();
                },
                "bFilter": false
            });

            $('.overall [name=driver_id]').change(function(){
                $(this).closest('form').submit();
            })

            $(document).on('change', '[id^=driver_id_]', function (event, state) {

                var data=$(this).attr('id').split('_');
                var date=data[2];
                var self=$(this);
                var id=$(this).val();

                $.ajax({
                    type: "post",
                    url: "{{route('assignDriver',['id'=>$order->id])}}",
                    data: {driver_id: id,date:date},
                    success: function(res)
                    {
                        var data = JSON.parse(res);
                        if(data.status == 1)
                        {
                            toastr.success(data.message,"Status",{
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

                            });
                        }
                        else
                        {
                            toastr.error(data.message,"Status",{
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

                            });
                        }
                    },
                    error: function(data)
                    {

                        toastr.error("Unable to assign driver.","Status",{
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

                        });

                    }
                });
            });


            $(document).on('switchChange.bootstrapSwitch', '.statusOrder', function (event, state) {
                var x;
                var self=$(this);
                var tr=self.closest('tr').find('td:nth-child(13)');

                if($(this).is(':checked'))
                    x = 'CM';
                else
                    x = 'PN';

                var id = $(this).data('id');

                $.ajax({
                    type: "post",
                    url: "{{route('updateOrder')}}",
                    data: {statusid: id,orderid:"{{$order->id}}",status:x},
                    success: function(res)
                    {
                        var data = JSON.parse(res);
                        if(data.status == 1)
                        {
                            self.closest('td').html('-');
                            toastr.success(data.message,"Status",{
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

                            });
                            tr.html('Delivered');
                            window.location.reload();
                        }
                        else
                        {
                            toastr.error(data.message,"Status",{
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

                            });
                        }
                    },
                    error: function(data)
                    {

                        toastr.error("Unable to update order.","Status",{
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

                        });

                    }
                });
            });

            $(document).on('switchChange.bootstrapSwitch', '.statusCompleteOrder', function (event, state) {
                var x;
                var self=$(this);
                var tr=self.closest('tr').find('td:nth-child(13)');

                if($(this).is(':checked'))
                    x = 'RN';
                else
                    x = 'CM';

                var id = $(this).data('id');

                $.ajax({
                    type: "post",
                    url: "{{route('updateOrder')}}",
                    data: {statusid: id,orderid:"{{$order->id}}",status:x},
                    success: function(res)
                    {
                        var data = JSON.parse(res);
                        if(data.status == 1)
                        {
                            self.closest('td').html('-');
                            toastr.success(data.message,"Status",{
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

                            });
                            tr.html('Returned');
                        }
                        else
                        {
                            toastr.error(data.message,"Status",{
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

                            });
                        }
                    },
                    error: function(data)
                    {

                        toastr.error("Unable to update order.","Status",{
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

                        });

                    }
                });
            });
        });
    </script>
@endpush
