@extends('layouts.app')
@section('title', 'Areas')

@section('content')
    <div class="content-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Areas</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item active">Areas</li>
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
                        <h4 class="card-title">Areas</h4>

                        @if (Helper::checkAccess(route('createArea')))
                            <div class="dt-buttons float-right">
                                <a href="{{route('createArea')}}" class="btn dt-button py-2">Add Area</a>
                            </div>
                        @endif
                        <div class="table-responsive m-t-40">
                            {{-- @if (Helper::checkAccess(route('deleteAreas')))
                                <div class="dt-buttons">
                                    <a href="javascript:void(0)" data-href="{{route('deleteAreas')}}" class="btn btn-secondary disabled bulkAction deleteAreas">Delete</a>
                                </div>
                            @endif --}}
                            @if (Helper::checkAccess(route('changeStatusAreas')))
                                <div class="dt-buttons">
                                    <a href="javascript:void(0)" data-href="{{route('changeStatusAreas')}}" class="btn btn-secondary disabled bulkAction changeStatusAreas">Activate/Deactivate</a>
                                </div>
                            @endif
                            <table id="areasTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_ids[]">Select</label></div></th>
                                        <th>Area Code</th>
                                        <th>Name</th>
                                        <th>City</th>
                                        <th>Status</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                        <th>S.No.</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_ids[]">Select</label></div></th>
                                        <th>Area Code</th>
                                        <th>Name</th>
                                        <th>City</th>
                                        <th>Status</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                        <th>S.No.</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        {{-- @if (Helper::checkAccess(route('deleteAreas')))
                            <div class="dt-buttons">
                                <a href="javascript:void(0)" data-href="{{route('deleteAreas')}}" class="btn btn-secondary disabled bulkAction deleteAreas">Delete</a>
                            </div>
                        @endif --}}
                        @if (Helper::checkAccess(route('changeStatusAreas')))
                            <div class="dt-buttons">
                                <a href="javascript:void(0)" data-href="{{route('changeStatusAreas')}}" class="btn btn-secondary disabled bulkAction changeStatusAreas">Activate/Deactivate</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>

    <div class="modal fade" id="areastatusModal" tabindex="-1" role="dialog" aria-labelledby="areastatusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="areastatusModalLabel">Confirm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-valide" method="post" id="blockForm" action="{{route('changeStatusArea')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="statusid" id="statusid">
                        <input type="hidden" name="status" id="status">
                        <h5 class="m-t-10 text-danger">Are you sure you want to <span class="areastatus"></span> area : <span id="statuscode"></span></h5>
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
            var table = $('#areasTable').DataTable({
                "ajax": {
                    url:"{{route('areaAjax')}}",
                    dataSrc:"data",
                    data:{
                        from_date: $('input[name=from_date]').val(),
                        end_date: $('input[name=end_date]').val(),
                        status: $('select[name=status]').val(),
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
                    { "data": "area_code" },
                    { "data": "area" },
                    { "data": "city" },
                    { "data": "status" },
                    { "data": "activate" },
                    { "data": "action" },
                    { "data": "sno" },
                ],
                "drawCallback": function(settings){
                    $(".bt-switch input[type='checkbox']").bootstrapSwitch();
                },

                @if(Helper::checkAccess('admin/areas/report'))
                dom: 'Bfrtip',
                buttons: [
                    // {extend: 'pdf',exportOptions: {columns: '1,2,3'}},
                    {extend: 'excel',exportOptions: {columns: '7,1,2,3,4'}},
                ],
                @endif
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                "columnDefs": [
                    {"targets": [0,4,5],"orderable": false},
                    {"targets": [1,4,7],"visible": false},
                ],
                "aaSorting": [],
            });

            $(document).on('click','input[name="user_ids[]"]',function(){
                $(document).find('input[name="user_ids[]"]').prop('checked', $(this).prop('checked'));
                $(document).find('input[name="user_id[]"]').prop('checked', $(this).prop('checked'));
                var length = $('input[name="user_id[]"]:checked').length;

                if(length > 0)
                {
                    $('.deleteAreas').removeClass('disabled');
                    $('.changeStatusAreas').removeClass('disabled');
                    table.rows().select();
                }else{
                    $('.deleteAreas').addClass('disabled');
                    $('.changeStatusAreas').addClass('disabled');
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
                    $('.deleteAreas').removeClass('disabled');
                    $('.changeStatusAreas').removeClass('disabled');
                    table.rows().select();
                }else{
                    $('.deleteAreas').addClass('disabled');
                    $('.changeStatusAreas').addClass('disabled');
                    table.rows().deselect();
                }

            } );

            $(document).on('click','input[name="user_id[]"]',function(){
                var length = $('input[name="user_id[]"]:checked').length;
                var row = $(this).closest('tr');
                var index = row.index();

                if(length > 0)
                {
                    $('.deleteAreas').removeClass('disabled');
                    $('.changeStatusAreas').removeClass('disabled');
                    table.row(index).select();
                }else{
                    $('.deleteAreas').addClass('disabled');
                    $('.changeStatusAreas').addClass('disabled');
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

            $(document).on('switchChange.bootstrapSwitch', '.statusArea', function (event, state) {
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
                    $('.areastatus').text('deactivate');
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val('AC');
                    $('.areastatus').text('activate');
                }

                $.ajax({
                    type: "post",
                    url: "{{route('changeStatusAjaxArea')}}",
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

                        toastr.error("Unable to update area.","Status",{
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
                // $('#areastatusModal').modal('show');
            });


            $(document).on('click','.deleteArea',function(){
                var id = $(this).data('id');
                var code = $(this).data('code');
                $('#deleteid').val(id);
                $('#val-code').text(code);

                $('#confirmDeleteModal').modal('show');
            });


            $('.bulkAction').click(function(){
                var url = $(this).data('href');
                var id = $(this).attr('class');
                var ids = [];
                $.each($('input[name="user_id[]"]:checked'), function(){
                    ids.push($(this).val());
                });

                $.ajax({
                    type: "post",
                    url: url,
                    data: {ids: ids},
                    success: function(res)
                    {
                        var data = JSON.parse(res);
                        table.ajax.reload();
                        $.each($('input[name="user_id[]"]:checked'), function(){
                            $(this).prop('checked', false);
                        });
                        $.each($('input[name="user_ids[]"]:checked'), function(){
                            $(this).prop('checked', false);
                        });
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

                        $('.deleteAreas').addClass('disabled');
                        $('.changeStatusAreas').addClass('disabled');
                    },
                    error: function(data)
                    {

                        toastr.error("Unable to update areas.","Status",{
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
