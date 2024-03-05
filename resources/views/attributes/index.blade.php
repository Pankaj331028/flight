@extends('layouts.app')
@section('title', 'Attribute')

@section('content')
	<div class="content-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Attribute</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item active">Attribute</li>
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
                        <h4 class="card-title">Attribute</h4>

                            <div class="dt-buttons float-right">
                                <a href="{{route('createAttribute')}}" class="btn dt-button">Add Attribute</a>
                            </div>
                        <div class="table-responsive m-t-40">
                            <div class="dt-buttons">
                                <a href="javascript:void(0)" data-href="{{route('deleteattributes')}}" class="btn btn-secondary disabled bulkAction deleteattributes">Delete</a>
                            </div>
                                <div class="dt-buttons">
                                    <a href="javascript:void(0)" data-href="{{route('changeStatusattributes')}}" class="btn btn-secondary disabled bulkAction changeStatusattributes">Activate/Deactivate</a>
                                </div>
                            <table id="attributesTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="attribute_ids[]">Select</label></div></th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Value</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="attribute_ids[]">Select</label></div></th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Value</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        <div class="dt-buttons">
                            <a href="javascript:void(0)" data-href="{{route('deleteattributes')}}" class="btn btn-secondary disabled bulkAction deleteattributes">Delete</a>
                        </div>
                        <div class="dt-buttons">
                            <a href="javascript:void(0)" data-href="{{route('changeStatusattributes')}}" class="btn btn-secondary disabled bulkAction changeStatusattributes">Activate/Deactivate</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>

    <div class="modal fade" id="attributestatusModal" tabindex="-1" role="dialog" aria-labelledby="attributestatusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attributestatusModalLabel">Confirm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-valide" method="post" id="blockForm" action="{{route('changeStatusattributes')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="statusid" id="statusid">
                        <input type="hidden" name="status" id="status">
                        <h5 class="m-t-10 text-danger">Are you sure you want to <span class="attributestatus"></span> Attribute<!--  : <span id="statuscode"></span> --></h5>
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
                    <form class="form-valide" method="post" id="deleteForm" action="{{route('deleteAttribute')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="deleteid" id="deleteid">
                        <h5 class="m-t-10 text-danger">Are you sure you want to delete Attribute<!--  : <span id="val-code"></span> --></h5>
                        <button type="button" class="btn btn-secondary btn-flat cancelBtn m-b-30 m-t-30" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info btn-flat confirmBtn m-b-30 m-t-30">Confirm</button>
                    </form>
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
    		var table = $('#attributesTable').DataTable({
                "ajax": {
                    url:"{{ route('AttributeAjax') }}",
                    dataSrc:"data",
                    data:{
                        from_date: $('input[name=from_date]').val(),
                        end_date: $('input[name=end_date]').val(),
                    }
                    // type: "get"
                },
                paging: true,
                pageLength: 10,
                // "bProcessing": true,
                "bServerSide": false,
                "bLengthChange": false,
                "aoColumns": [
                    { "data": "select" },
                    { "data": "name" },
                    { "data": "type" },
                    { "data": "value" },
                    { "data": "activate" },
                    { "data": "action" },
                ],
                "drawCallback": function(settings){
                    $(".bt-switch input[type='checkbox']").bootstrapSwitch();
                },

                @if(Helper::checkAccess('admin/attributes/report'))
		        dom: 'Bfrtip',
                buttons: [
                    // {extend: 'pdf',exportOptions: {columns: '1,2,3,4,5,6'}},
                    {extend: 'excel',exportOptions: {columns: '1,2,3'}},
                ],
                @endif
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                "columnDefs": [
                    {"targets": [3],"orderable": false},
                    {"targets": [1,2,3,5],"visible": true},
                ],
                "aaSorting": [],
		    });

            $(document).on('click','input[name="attribute_ids[]"]',function(){
                $(document).find('input[name="attribute_ids[]"]').prop('checked', $(this).prop('checked'));
                $(document).find('input[name="attribute_id[]"]').prop('checked', $(this).prop('checked'));
                var length = $('input[name="attribute_id[]"]:checked').length;

                if(length > 0)
                {
                    $('.deleteattributes').removeClass('disabled');
                    $('.changeStatusattributes').removeClass('disabled');
                    table.rows().select();
                }else{
                    $('.deleteattributes').addClass('disabled');
                    $('.changeStatusattributes').addClass('disabled');
                    table.rows().deselect();
                }
            });

            $('tbody').on( 'click', 'tr', function () {
                $(this).toggleClass('selected');
                var check = $(this).find('input[type=checkbox]');
                check.prop('checked',!check.prop("checked"));
                var length = $('input[name="attribute_id[]"]:checked').length;
                if(length > 0)
                {
                    $('.deleteattributes').removeClass('disabled');
                    $('.changeStatusattributes').removeClass('disabled');
                    table.rows().select();
                }else{
                    $('.deleteattributes').addClass('disabled');
                    $('.changeStatusattributes').addClass('disabled');
                    table.rows().deselect();
                }

            } );

            $(document).on('click','input[name="attribute_id[]"]',function(){
                var length = $('input[name="attribute_id[]"]:checked').length;
                var row = $(this).closest('tr');
                var index = row.index();

                if(length > 0)
                {
                    $('.deleteattributes').removeClass('disabled');
                    $('.changeStatusattributes').removeClass('disabled');
                    table.row(index).select();
                }else{
                    $('.deleteattributes').addClass('disabled');
                    $('.changeStatusattributes').addClass('disabled');
                    table.row(index).deselect();
                }

                if($(this).prop('checked'))
                {
                    table.row(index).select();
                }else{
                    table.row(index).deselect();
                }

                if(length == $('input[name="attribute_id[]"]').length){
                    $(document).find('input[name="attribute_ids[]"]').prop('checked', true);
                }else{
                    $(document).find('input[name="attribute_ids[]"]').prop('checked', false);
                }

            });

            $(document).on('switchChange.bootstrapSwitch', '.statusattribute', function (event, state) {
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
                    $('.attributestatus').text('deactivate');
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val('AC');
                    $('.attributestatus').text('activate');
                }

                $.ajax({
                    type: "post",
                    url: "{{ route('changeStatusAttribute') }}",
                    data: {ids: id,status:x},
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

                        toastr.error("Unable to update attributes.","Status",{
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
                // $('#sliderstatusModal').modal('show');
            });


            $(document).on('click','.deleteAttribute', function(){
                var id = $(this).data('id');
                //var code = $(this).data('code');
                $('#deleteid').val(id);
                $('#val-code').text(id);

                $('#confirmDeleteModal').modal('show');
            });


            $('.bulkAction').click(function(){
                var url = $(this).data('href');
                var id = $(this).attr('class');
                var ids = [];
                $.each($('input[name="attribute_id[]"]:checked'), function(){
                    ids.push($(this).val());
                });

                $.ajax({
                    type: "post",
                    url: url,
                    data: {ids: ids},
                    success: function(res)
                    {
                        console.log(res);
                        var data = JSON.parse(res);
                        
                        if(data.status == 1)
                        {
                            //table.clearPipeline();
                            table.ajax.reload();

                            $.each($('input[name="attributes_id[]"]:checked'), function(){
                                $(this).prop('checked', false);
                            });
                            $.each($('input[name="attributes_ids[]"]:checked'), function(){
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

                        $('.deleteattributes').addClass('disabled');
                        $('.changeStatusattributes').addClass('disabled');
                    },
                    error: function(data)
                    {

                        toastr.error("Unable to update attributes.","Status",{
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

                $(this).blur();
            })

    	});
    </script>
@endpush