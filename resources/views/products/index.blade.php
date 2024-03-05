@extends('layouts.app')
@section('title', 'Products')

@section('content')
	<div class="content-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Products</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item active">Products</li>
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
                        <h4 class="card-title">Products</h4>

                        @if (Helper::checkAccess(route('createProduct')))
                            <div class="dt-buttons float-right">
                                <a href="{{ route('exportView') }}" data-toggle="tooltip" data-placement="bottom" title="Only quantity & maximum quantity can be updated" class="toolTip btn dt-button py-2">Stock Update</a>
                            </div>
                            <div class="dt-buttons float-right">
                                <a href="javascript:;" data-toggle="tooltip" data-placement="bottom" title="Use this file for bulk update" class="toolTip bulkExport btn dt-button py-2">Export</a>
                            </div>
                            <div class="dt-buttons float-right">
                                <a href="{{route('importProducts')}}" class="btn dt-button py-2">Bulk Import</a>
                            </div>
                            <div class="dt-buttons float-right">
                                <a href="{{route('createProduct')}}" class="btn dt-button py-2">Add Product</a>
                            </div>
                        @endif
                        <div class="table-responsive m-t-40">
                            <div class="dt-buttons">
                                <a href="javascript:void(0)" data-href="{{route('deleteProducts')}}" class="btn btn-secondary disabled bulkAction deleteProducts">Delete</a>
                            </div>
                            @if (Helper::checkAccess(route('changeStatusProducts')))
                                <div class="dt-buttons">
                                    <a href="javascript:void(0)" data-href="{{route('changeStatusProducts')}}" class="btn btn-secondary disabled bulkAction changeStatusProducts">Activate/Deactivate</a>
                                </div>
                            @endif
                            <table id="productsTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_ids[]">Select</label></div></th>
                                        <th>S No</th>
                                        <th>Product Code</th>
                                        <th>Brand</th>
                                        <th>Category</th>
                                        <th>SubCategory</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Image</th>
                                        <th>Variations</th>
                                        <th>Manage Stock</th>
                                        <th>Tax</th>
                                        <th>Type</th>
                                        <th>Quick Grab</th>
                                        <th>Is Exclusive</th>
                                        <th>Status</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                        <th>Favorite Count</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_ids[]">Select</label></div></th>
                                        <th>S No</th>
                                        <th>Product Code</th>
                                        <th>Brand</th>
                                        <th>Category</th>
                                        <th>SubCategory</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Image</th>
                                        <th>Variations</th>
                                        <th>Manage Stock</th>
                                        <th>Tax</th>
                                        <th>Type</th>
                                        <th>Quick Grab</th>
                                        <th>Is Exclusive</th>
                                        <th>Status</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                        <th>Favorite Count</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        <div class="dt-buttons">
                            <a href="javascript:void(0)" data-href="{{route('deleteProducts')}}" class="btn btn-secondary disabled bulkAction deleteProducts">Delete</a>
                        </div>
                        @if (Helper::checkAccess(route('changeStatusProducts')))
                            <div class="dt-buttons">
                                <a href="javascript:void(0)" data-href="{{route('changeStatusProducts')}}" class="btn btn-secondary disabled bulkAction changeStatusProducts">Activate/Deactivate</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>

    <div class="modal fade" id="userstatusModal" tabindex="-1" role="dialog" aria-labelledby="userstatusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userstatusModalLabel">Confirm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-valide" method="post" id="blockForm" action="{{route('changeStatusProduct')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="statusid" id="statusid">
                        <input type="hidden" name="status" id="status">
                        <h5 class="m-t-10 text-danger">Are you sure you want to <span class="categorystatus"></span> category : <span id="statuscode"></span></h5>
                        <button type="button" class="btn btn-secondary btn-flat cancelBtn m-b-30 m-t-30" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info btn-flat confirmBtn m-b-30 m-t-30">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{-- <form class="form-valide" method="post" id="deleteForm" action="{{route('deleteCategory')}}"> --}}
                        {{csrf_field()}}
                        <input type="hidden" name="deleteid" id="deleteid">
                        <input type="hidden" name="deleteurl" id="deleteurl">
                        <h5 class="m-t-10 text-danger">Deleting products will delete their variations as well. Are you sure you want to delete selected product(s)?</h5>
                        <button type="button" class="btn btn-secondary btn-flat cancelBtn m-b-30 m-t-30" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-info btn-flat confirmDeleteBtn m-b-30 m-t-30">Confirm</button>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
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
    		var table = $('#productsTable').DataTable({
                "ajax": {
                    url:"{{route('productsAjax')}}",
                    dataSrc:"data",
                    data:{
                        from_date: $('input[name=from_date]').val(),
                        end_date: $('input[name=end_date]').val(),
                        status: $('select[name=status]').val(),
                        manage_stock: $('select[name=manage_stock]').val(),
                        quick_grab: $('select[name=quick_grab]').val(),
                        is_exclusive: $('select[name=is_exclusive]').val(),
                        categoryFilter: $('select[name=categoryFilter]').val(),
                    }
                },
                paging: true,
                pageLength: 10,
                // "bProcessing": true,
                "bServerSide": false,
                "bLengthChange": false,
                "aoColumns": [
                    { "data": "select" },
                    { "data": "sno" },
                    { "data": "product_code" },
                    { "data": "brand_id" },
                    { "data": "category_id" },
                    { "data": "subcategory_id" },
                    { "data": "name" },
                    { "data": "description" },
                    { "data": "image" },
                    { "data": "variations" },
                    { "data": "manage_stock" },
                    { "data": "tax_id" },
                    { "data": "type" },
                    { "data": "quick_grab" },
                    { "data": "is_exclusive" },
                    { "data": "status" },
                    { "data": "activate" },
                    { "data": "action" },
                    { "data": "fav_count" },
                ],
                "drawCallback": function(settings){
                    $(".bt-switch input[type='checkbox']").bootstrapSwitch();
                },

                @if(Helper::checkAccess('admin/products/report'))
		        dom: 'Bfrtip',
                buttons: [
                    {extend: 'excel',exportOptions: {columns: '2,4,5,6,7,9,13,14,15',format: {
                        body: function ( data, column, row ) {
                            if ( typeof data !== 'string' ) {
                                return data;
                            }
                            if (column === 8) {
                                data = data.replace(/<br\s*\/?>/ig, "\n");
                            }
                            return data;
                        }
                    }}},
                    /*{
                        extend: 'pdf',
                        exportOptions: {columns: '1,3,4,5,6,8,12,13,14',format: {
                            body: function ( data, column, row ) {
                                if (column === 8) {
                                    data = data.replace(/<br\s*\/?>/ig, "\n");
                                }
                                return data;
                            }
                        }},
                        pageSize: 'LETTER',
                        customize: function(doc, config) {
                            doc.pageOrientation = 'landscape';
                        }
                    },*/
                ],
                @endif
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                "columnDefs": [
                    {"targets": [0,15,16],"orderable": false},
                    {"targets": [2,3,7,8,9,10,11,12,13,14,15,18], visible: false},
                    { "aTargets": [8],
                        "sType": "html",
                        "fnRender": function(o, val) {
                            return $("<div/>").html(o.aData[8]).text();
                        }
                    }
                ],
                "aaSorting": [],
		    });

            $(document).on('click','input[name="user_ids[]"]',function(){
                $(document).find('input[name="user_ids[]"]').prop('checked', $(this).prop('checked'));
                $(document).find('input[name="user_id[]"]').prop('checked', $(this).prop('checked'));
                var length = $('input[name="user_id[]"]:checked').length;

                if(length > 0)
                {
                    $('.deleteProducts').removeClass('disabled');
                    $('.changeStatusProducts').removeClass('disabled');
                    table.rows().select();
                }else{
                    $('.deleteProducts').addClass('disabled');
                    $('.changeStatusProducts').addClass('disabled');
                    table.rows().deselect();
                }
            });

            $('tbody').on( 'click', 'tr', function () {
                $(this).toggleClass('selected');
                var check = $(this).find('input[type=checkbox]');
                check.prop('checked',!check.prop("checked"));
                var length = $('input[name="user_id[]"]:checked').length;
                if(length > 0)
                {
                    $('.deleteProducts').removeClass('disabled');
                    $('.changeStatusProducts').removeClass('disabled');
                    table.rows().select();
                }else{
                    $('.deleteProducts').addClass('disabled');
                    $('.changeStatusProducts').addClass('disabled');
                    table.rows().deselect();
                }

            } );

            $(document).on('click','input[name="user_id[]"]',function(){
                var length = $('input[name="user_id[]"]:checked').length;
                var row = $(this).closest('tr');
                var index = row.index();

                if(length > 0)
                {
                    $('.deleteProducts').removeClass('disabled');
                    $('.changeStatusProducts').removeClass('disabled');
                    table.row(index).select();
                }else{
                    $('.deleteProducts').addClass('disabled');
                    $('.changeStatusProducts').addClass('disabled');
                    table.row(index).deselect();
                }

                if($(this).prop('checked'))
                {
                    table.row(index).select();
                }else{
                    table.row(index).deselect();
                }

                if(length == $('input[name="user_id[]"]').length){
                    $(document).find('input[name="user_ids[]"]').prop('checked', true);
                }else{
                    $(document).find('input[name="user_ids[]"]').prop('checked', false);
                }

            });

            $(document).on('switchChange.bootstrapSwitch', '.statusProduct', function (event, state) {
                var x;

                if($(this).is(':checked'))
                    x = 'AC';
                else
                    x = 'IN';

                var id = $(this).data('id');
                $('#statuscode').text($(this).data('code'));
                if(x == 'IN')
                {
                    $('#statusid').val(id);
                    $('#status').val('IN');
                    $('.categorystatus').text('deactivate');
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val('AC');
                    $('.categorystatus').text('activate');
                }

                $.ajax({
                    type: "post",
                    url: "{{route('changeStatusAjaxProduct')}}",
                    data: {statusid: id,status:x},
                    success: function(res)
                    {
                        var data = JSON.parse(res);
                        if(data.status == 1)
                        {
                            table.ajax.reload();
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

                        toastr.error("Unable to update product.","Status",{
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
                // $('#offerstatusModal').modal('show');
            });


            $(document).on('click','.deleteProduct',function(){
                var id = $(this).data('id');
                $('#deleteid').val(id);
                $('#deleteurl').val("{{route('deleteProduct')}}");

                $('#confirmDeleteModal').modal('show');
            });

            $(document).on('click', '.confirmDeleteBtn', function (event, state) {

                $.ajax({
                    type: "post",
                    url: $('#deleteurl').val(),
                    data: {deleteid: $('#deleteid').val()},
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
                            table.ajax.reload();
                            $('.deleteProducts').addClass('disabled');
                            $('.changeStatusProducts').addClass('disabled');
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

                        toastr.error("Unable to delete product.","Status",{
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
                    $('#confirmDeleteModal').modal('hide');
                // $('#offerstatusModal').modal('show');
            });

            $('.bulkAction').click(function(){
                var url = $(this).data('href');
                var id = $(this).attr('class');
                var ids = [];
                $.each($('input[name="user_id[]"]:checked'), function(){
                    ids.push($(this).val());
                });

                 if(url.includes('delete')){
                    $('#deleteid').val(ids.join(','));
                    $('#deleteurl').val(url);
                    $('#confirmDeleteModal').modal('show');
                }
                else{
                    $.ajax({
                        type: "post",
                        url: url,
                        data: {ids: ids},
                        success: function(res)
                        {
                            var data = JSON.parse(res);
                            if(data.status == 1)
                            {
                                table.ajax.reload();

                                $.each($('input[name="user_id[]"]:checked'), function(){
                                    $(this).prop('checked', false);
                                });
                                $.each($('input[name="user_ids[]"]:checked'), function(){
                                    $(this).prop('checked', false);
                                });

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

                            $('.deleteProducts').addClass('disabled');
                            $('.changeStatusProducts').addClass('disabled');
                        },
                        error: function(data)
                        {

                            toastr.error("Unable to update products.","Status",{
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
                    })
                }
                $(this).blur();
            })

    	});
    </script>
    <script>
        //Export selected products
        $('.bulkExport').click(function(){
            var ids = $('input[name="user_id[]"]:checked').map(function() {
                return this.value;
            }).toArray();

            var url = "{{url('/admin/products/export/?id=')}}" + ids;
            window.open(url, '_blank');

            // $.ajaxSetup({
            //     headers: {
            //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //     }
            // });

            

            // $.ajax({
            // method: 'get',
            // url: "{{ route('exportProducts') }}",
            // responseType: 'blob', // important
            // cache: false,
            // data: {
            //     id: ids,
            // },
            // success:function(res){
            //     window.location.href = "{{ route('exportProducts') }}";
            // }
            // });
        })
    </script>
@endpush