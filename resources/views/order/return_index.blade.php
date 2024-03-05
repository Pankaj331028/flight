@extends('layouts.app')
@section('title', 'All Order Cancel Requests')
@section('content')
</style>
<div class="content-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">Order Cancel Requests </h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                <li class="breadcrumb-item active">Order Cancel Requests</li>
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
                    <h4 class="card-title">All Order Cancel Requests</h4>

                    <div class="table-responsive m-t-40">
                        <table id="ordersTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>S.No.</div></th>
                                    <th>Order No</th>
                                    <th>User</th>
                                    <th>Payment Method</th>
                                    <th>Return Type</th>
                                    <th>Product</th>
                                    <th>Amount (in {{session()->get('currency')}})</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>S.No.</div></th>
                                    <th>Order No</th>
                                    <th>User</th>
                                    <th>Payment Method</th>
                                    <th>Return Type</th>
                                    <th>Product</th>
                                    <th>Amount (in {{session()->get('currency')}})</th>
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
                    url:"{{route('returnsAjax')}}",
                    dataSrc:"data",
                    data:{
                        from_date: $('input[name=from_date]').val(),
                        end_date: $('input[name=end_date]').val(),
                        return_type: $('select[name=return_type]').val(),
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
                    { "data": "order_no" },
                    { "data": "user_id" },
                    { "data": "payment_method" },
                    { "data": "type" },
                    { "data": "product" },
                    { "data": "amount" },
                    { "data": "status" },
                    { "data": "action" },
                ],
                @if(Helper::checkAccess('admin/orderReturns/report'))
                dom: 'Bfrtip',
                buttons: [
                    {extend: 'excel',exportOptions: {columns: '0,1,2,3,4,5,6,7',format: {
                        body: function ( data, column, row ) {
                            if ( typeof data !== 'string' ) {
                                return data;
                            }
                            if (column === 5) {
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
                    {"targets": [0,2,5,8],"orderable": false},
                    {"targets": [5],"visible": false},
                    { "aTargets": [5],
                        "sType": "html",
                        "fnRender": function(o, val) {
                            return $("<div/>").html(o.aData[4]).text();
                        }
                    }
                ],
                "aaSorting": [],
            });
        });

    </script>
@endpush