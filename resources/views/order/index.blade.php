@extends('layouts.app')
@section('title', 'All Orders')
@section('content')
</style>
<div class="content-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">Orders </h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                <li class="breadcrumb-item active">Orders</li>
            </ol>
        </div>
    </div>
    <form action="" method="get" id="filter_form">
    @include("layouts.filter")
    </form>
    <!-- Start Page Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                @include('layouts.message')
                <div class="card-body">
                    <h4 class="card-title">All Orders</h4>

                    <div class="table-responsive m-t-40">
                        <table id="ordersTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>S.No.</div></th>
                                    <th>Order Code</th>
                                    <th>Order No</th>
                                    <th>User</th>
                                    <th>Products</th>
                                    <th>Address</th>
                                    <th>Payment Method</th>
                                    <th>Wallet Used</th>
                                    <th>Total Amount (in {{session()->get('currency')}})</th>
                                    <th>Coupon Discount (in {{session()->get('currency')}})</th>
                                    <th>Shipping Fee (in {{session()->get('currency')}})</th>
                                    <th>Savings (in {{session()->get('currency')}})</th>
                                    <th>Credits Used (in {{session()->get('currency')}})</th>
                                    <th>Grand Total (in {{session()->get('currency')}})</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>S.No.</div></th>
                                    <th>Order Code</th>
                                    <th>Order No</th>
                                    <th>User</th>
                                    <th>Products</th>
                                    <th>Address</th>
                                    <th>Payment Method</th>
                                    <th>Wallet Used</th>
                                    <th>Total Amount (in {{session()->get('currency')}})</th>
                                    <th>Coupon Discount (in {{session()->get('currency')}})</th>
                                    <th>Shipping Fee (in {{session()->get('currency')}})</th>
                                    <th>Savings (in {{session()->get('currency')}})</th>
                                    <th>Credits Used (in {{session()->get('currency')}})</th>
                                    <th>Grand Total (in {{session()->get('currency')}})</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                            <tbody>

                            </tbody>
                        </table>
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
    <!-- start - This is for export functionality only -->
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
    <script type="text/javascript">
        $(function(){
    		var table = $('#ordersTable').DataTable({
                "ajax": {
                    url:"{{route('ordersAjax')}}",
                    dataSrc:"data",
                    data:{
                        from_date: $('input[name=from_date]').val(),
                        end_date: $('input[name=end_date]').val(),
                        status: $('select[name=status]').val(),
                    }
                },
                paging: true,
                pageLength: 10,
                // "bProcessing": true,
                "bServerSide": false,
                "bLengthChange": false,
                "aoColumns": [
                    { "data": "sno" },
                    { "data": "order_code" },
                    { "data": "order_no" },
                    { "data": "user_id" },
                    { "data": "products" },
                    { "data": "address" },
                    { "data": "payment_method" },
                    { "data": "wallet" },
                    { "data": "total_amount" },
                    { "data": "coupon_discount" },
                    { "data": "shipping_fee" },
                    { "data": "savings" },
                    { "data": "credit_used" },
                    { "data": "grand_total" },
                    { "data": "status" },
                    { "data": "action" },
                ],
                @if(Helper::checkAccess('admin/orders/report'))
                dom: 'Bfrtip',
                buttons: [
                    {extend: 'excel',exportOptions: {columns: '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14',format: {
                        body: function ( data, column, row ) {
                            if ( typeof data !== 'string' ) {
                                return data;
                            }
                            if (column === 4) {
                                data = data.replace(/<br\s*\/?>/ig, "\n");
                            }
                            return data;
                        }
                    }}}
                ],
                @endif
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                "columnDefs": [
                    {"targets": [0,3,4,5,15],"orderable": false},
                    {"targets": [1,4,5,8,9,10,11,12], visible: false},
                    { "aTargets": [4],
                        "sType": "html",
                        "fnRender": function(o, val) {
                            return $("<div/>").html(o.aData[4]).text();
                        }
                    }
                ],
                "aaSorting": [],
            });


            $(document).on('click','.sendInvoice',function(){
                var id = $(this).data('id');


                $.ajax({
                    type: "post",
                    url: "{{route('sendInvoiceOrder')}}",
                    data: {id: id,},
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

                        toastr.error("Unable to send invoice.","Status",{
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
            })
        });

    </script>
@endpush
