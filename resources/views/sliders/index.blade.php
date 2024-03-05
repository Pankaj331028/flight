@extends('layouts.app')
@section('title', 'Sliders/Banners')

@section('content')
    <div class="content-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Sliders/Banners</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item active">Sliders/Banners</li>
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
                        <h4 class="card-title">Sliders/Banners</h4>

                        @if (Helper::checkAccess(route('createSlider')))
                            <div class="dt-buttons float-right">
                                <a href="{{route('createSlider')}}" class="btn dt-button py-2">Add Slide/Banner</a>
                            </div>
                        @endif
                        <div class="table-responsive m-t-40">
                            @if (Helper::checkAccess(route('deleteSliders')))
                                <div class="dt-buttons">
                                    <a href="javascript:void(0)" data-href="{{route('deleteSliders')}}" class="btn btn-secondary disabled bulkAction deleteSliders">Delete</a>
                                </div>
                            @endif
                            @if (Helper::checkAccess(route('changeStatusSliders')))
                                <div class="dt-buttons">
                                    <a href="javascript:void(0)" data-href="{{route('changeStatusSliders')}}" class="btn btn-secondary disabled bulkAction changeStatusSliders">Activate/Deactivate</a>
                                </div>
                            @endif
                            <table id="slidersTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_ids[]">Select</label></div></th>
                                        <th>S No</th>
                                        <th>Image</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_ids[]">Select</label></div></th>
                                        <th>S No</th>
                                        <th>Image</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        @if (Helper::checkAccess(route('deleteSliders')))
                            <div class="dt-buttons">
                                <a href="javascript:void(0)" data-href="{{route('deleteSliders')}}" class="btn btn-secondary disabled bulkAction deleteSliders">Delete</a>
                            </div>
                        @endif
                        @if (Helper::checkAccess(route('changeStatusSliders')))
                            <div class="dt-buttons">
                                <a href="javascript:void(0)" data-href="{{route('changeStatusSliders')}}" class="btn btn-secondary disabled bulkAction changeStatusSliders">Activate/Deactivate</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>

    <div class="modal fade" id="sliderstatusModal" tabindex="-1" role="dialog" aria-labelledby="sliderstatusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sliderstatusModalLabel">Confirm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{csrf_field()}}
                    <input type="hidden" name="statusid" id="statusid">
                    <input type="hidden" name="status" id="status">
                    <input type="hidden" name="newid" id="newid">
                    <p class="m-t-10">Below image has already been added as banner. If you'll <span class="sliderstatus"></span> image, this one will be removed.</p>
                    <div class="text-center m-t-20">
                        <img src="" id="previousBanner" width="50%">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info btn-flat confirmBtn">Confirm</button>
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
                    <form class="form-valide" method="post" id="deleteForm" action="{{route('deleteSlider')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="deleteid" id="deleteid">
                        <h5 class="m-t-10 text-danger">Are you sure you want to delete this slide?</h5>
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
            var table = $('#slidersTable').DataTable({
                "ajax": {
                    url:"{{route('slidersAjax')}}",
                    dataSrc:"data",
                    data:{
                        from_date: $('input[name=from_date]').val(),
                        end_date: $('input[name=end_date]').val(),
                        category_filter: $('select[name=category_filter]').val(),
                        status: $('select[name=status]').val(),
                    }
                    // type: "get"
                },
                paging: true,
                searching: false,
                pageLength: 10,
                // "bProcessing": true,
                "bServerSide": false,
                "bLengthChange": false,
                "aoColumns": [
                    { "data": "select" },
                    { "data": "sno" },
                    { "data": "image" },
                    { "data": "category" },
                    { "data": "type" },
                    { "data": "status" },
                    { "data": "activate" },
                    { "data": "action" },
                ],
                "drawCallback": function(settings){
                    $(".bt-switch input[type='checkbox']").bootstrapSwitch();
                },
/*
                @if(Helper::checkAccess('admin/sliders/report'))
                dom: 'Bfrtip',
                buttons: [
                    {extend: 'pdf',exportOptions: {columns: '1,2'}},
                    {extend: 'excel',exportOptions: {columns: '1,2'}},
                ],
                @endif*/
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                "columnDefs": [
                    {"targets": [0,5,6],"orderable": false},
                    {"targets": [5],"visible": false},
                ],
                "aaSorting": [],
            });

            $(document).on('click','input[name="user_ids[]"]',function(){
                $(document).find('input[name="user_ids[]"]').prop('checked', $(this).prop('checked'));
                $(document).find('input[name="user_id[]"]').prop('checked', $(this).prop('checked'));
                var length = $('input[name="user_id[]"]:checked').length;

                if(length > 0)
                {
                    $('.deleteSliders').removeClass('disabled');
                    $('.changeStatusSliders').removeClass('disabled');
                    table.rows().select();
                }else{
                    $('.deleteSliders').addClass('disabled');
                    $('.changeStatusSliders').addClass('disabled');
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
                    $('.deleteSliders').removeClass('disabled');
                    $('.changeStatusSliders').removeClass('disabled');
                    table.rows().select();
                }else{
                    $('.deleteSliders').addClass('disabled');
                    $('.changeStatusSliders').addClass('disabled');
                    table.rows().deselect();
                }

            } );

            $(document).on('click','input[name="user_id[]"]',function(){
                var length = $('input[name="user_id[]"]:checked').length;
                var row = $(this).closest('tr');
                var index = row.index();

                if(length > 0)
                {
                    $('.deleteSliders').removeClass('disabled');
                    $('.changeStatusSliders').removeClass('disabled');
                    table.row(index).select();
                }else{
                    $('.deleteSliders').addClass('disabled');
                    $('.changeStatusSliders').addClass('disabled');
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


            $(document).on('switchChange.bootstrapSwitch', '.statusSlider', function (event, state) {
                var type = $(this).data('type');
                var chance = 'no';

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
                    $('.sliderstatus').text('deactivate');
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val('AC');
                    $('.sliderstatus').text('activate');
                }

                if(type=='banner' && x == 'AC'){
                    var width = Number($(this).closest('.bootstrap-switch').css('width').split('px')[0])+10;
                    $(this).bootstrapSwitch('state', false,true );
                    $(this).closest('.bootstrap-switch').css('width',width+'px');
                    $.ajax({
                        type:"POST",
                        url:"{{route('checkBanner')}}",
                        data:{id:id},
                        success: function(data){
                            if($.trim(data) != 'no')
                            {
                                var result = JSON.parse(data);
                                chance = result.id;
                                $('#newid').val(result.id);
                                $('#previousBanner').attr('src','{{URL::asset('/uploads/sliders/')}}/'+ result.image);
                                $('#sliderstatusModal').modal('show');
                            }

                            if(chance == 'no'){

                                $.ajax({
                                    type: "post",
                                    url: "{{route('changeStatusAjaxSlider')}}",
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

                                        toastr.error("Unable to update slide.","Status",{
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
                            }
                        }
                    })
                }
                else{

                    $.ajax({
                        type: "post",
                        url: "{{route('changeStatusAjaxSlider')}}",
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

                            toastr.error("Unable to update slide.","Status",{
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
                }

            });


            $('.confirmBtn').click(function(){
                var id = $('#statusid').val();
                var x = $('#status').val();
                var newid = $('#newid').val();
                $.ajax({
                    type: "post",
                    url: "{{route('changeStatusAjaxSlider')}}",
                    data: {statusid: id,status:x,newid:newid},
                    success: function(res)
                    {
                        $('#sliderstatusModal').modal('hide');
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

                        toastr.error("Unable to update slide.","Status",{
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

            $(document).on('click','.deleteSlide',function(){
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

                        $('.deleteSliders').addClass('disabled');
                        $('.changeStatusSliders').addClass('disabled');
                    },
                    error: function(data)
                    {

                        toastr.error("Unable to update slides.","Status",{
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