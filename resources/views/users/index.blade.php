@extends('layouts.app')
@section('title', 'Users')

@section('content')
	<div class="content-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Users</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item active">Users</li>
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
                        <h4 class="card-title">Users</h4>
                        <h6 class="card-subtitle">Export data to Excel, PDF</h6>
                        <div class="table-responsive m-t-40">
                            <div class="dt-buttons">
                                <a href="{{route('deleteUsers')}}" onclick="return confirm('Are you sure you want to delete all users?')" class="btn btn-secondary">Delete All Users</a>
                            </div>
                            <div class="dt-buttons">
                                <a href="javascript:void(0)" data-href="{{route('changeStatusUsers')}}" class="btn btn-secondary disabled bulkAction changeStatusUsers">Activate/Deactivate</a>
                            </div>
                            <table id="usersTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_ids[]">Select</label></div></th>
                                        <th>Profile Pic</th>
                                        <th>User Code</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile Number</th>
                                        <th>Google SignIn</th>
                                        <th>Facebook SignIn</th>
                                        <th>Referral Code</th>
                                        <th>Wallet Amount</th>
                                        <th>Device Type</th>
                                        <th>Registered On</th>
                                        <th>Verified</th>
                                        <th> Admin Verified </th>
                                        <th>Status</th>
                                        <th>Status</th>
                                        <th>Travel Access</th>
                                        <th>Travel Access</th>
                                        <th>Grocery Front Access</th>
                                        <th>Grocery Front Access</th>
                                        <th>Travel With Api Access</th>
                                        <th>Travel With Api Access</th>
                                        <th>Sample API Access</th>
                                        <th>Sample API Access</th>
                                        <th>Database Access</th>
                                        <th>Database Access</th>
                                        <th>Admin Panel Access</th>
                                        <th>Admin Panel Access</th>
                                        <th>Mobile App Access</th>
                                        <th>Mobile App Access</th>
                                        <th>Flight Access</th>
                                        <th>Flight Access</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_ids[]">Select</label></div></th>
                                        <th>Profile Pic</th>
                                        <th>User Code</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile Number</th>
                                        <th>Google SignIn</th>
                                        <th>Facebook SignIn</th>
                                        <th>Referral Code</th>
                                        <th>Wallet Amount</th>
                                        <th>Device Type</th>
                                        <th>Registered On</th>
                                        <th>Verified</th>
                                        <th> Admin Verified </th>
                                        <th>Status</th>
                                        <th>Status</th>
                                        <th>Travel Access</th>
                                        <th>Travel Access</th>
                                        <th>Grocery Front Access</th>
                                        <th>Grocery Front Access</th>
                                        <th>Travel With Api Access</th>
                                        <th>Travel With Api Access</th>
                                        <th>Sample API Access</th>
                                        <th>Sample API Access</th>
                                        <th>Database Access</th>
                                        <th>Database Access</th>
                                        <th>Admin Panel Access</th>
                                        <th>Admin Panel Access</th>
                                        <th>Mobile App Access</th>
                                        <th>Mobile App Access</th>
                                        <th>Flight Access</th>
                                        <th>Flight Access</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                </tbody>
                            </table>
                        </div>


                        <div class="dt-buttons">
                            <a href="javascript:void(0)" data-href="{{route('changeStatusUsers')}}" class="btn btn-secondary disabled bulkAction changeStatusUsers">Activate/Deactivate</a>
                        </div>
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
                    <form class="form-valide" method="post" id="blockForm" action="{{route('changeStatusUser')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="statusid" id="statusid">
                        <input type="hidden" name="status" id="status">
                        <h5 class="m-t-10 text-danger">Are you sure you want to <span class="userstatus"></span> user : <span id="statuscode"></span></h5>
                        <button type="button" class="btn btn-secondary btn-flat cancelBtn m-b-30 m-t-30" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info btn-flat confirmBtn m-b-30 m-t-30">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>




    <!-- <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
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
                        <h5 class="m-t-10 text-danger">Deleting categories will delete its subcategories as well. Are you sure you want to delete selected category/categories?</h5>
                        <button type="button" class="btn btn-secondary btn-flat cancelBtn m-b-30 m-t-30" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-info btn-flat confirmDeleteBtn m-b-30 m-t-30">Confirm</button>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
    </div> -->




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
    		var table = $('#usersTable').DataTable({
                "ajax": {
                    url:"{{route('userAjax')}}",
                    dataSrc:"data",
                    data:{
                        from_date: $('input[name=from_date]').val(),
                        to_date: $('input[name=to_date]').val(),
                        google_signin: $('select[name=google_signin]').val(),
                        facebook_signin: $('select[name=facebook_signin]').val(),
                        is_verified: $('select[name=is_verified]').val(),
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
                    { "data": "profile_picture" },
                    { "data": "user_code" },
                    { "data": "name" },
                    { "data": "email" },
                    { "data": "mobile_number" },
                    { "data": "google_signin" },
                    { "data": "facebook_signin" },
                    { "data": "referral_code" },
                    { "data": "wallet_amount" },
                    { "data": "device_type" },
                    { "data": "created_at" },
                    { "data": "is_verified" },
                    { "data": "admin_verified" },
                    { "data": "status" },
                    { "data": "activate" },
                    { "data": "travel_access" },
                    { "data": "travel_access1" },
                    { "data": "grocery_front_access" },
                    { "data": "grocery_front_access1" },
                    { "data": "travel_with_api_access" },
                    { "data": "travel_with_api_access1" },
                    { "data": "sample_api_access" },
                    { "data": "sample_api_access1" },
                    { "data": "database_access" },
                    { "data": "database_access1" },
                    { "data": "admin_panel_access" },
                    { "data": "admin_panel_access1" },
                    { "data": "mobile_app_access" },
                    { "data": "mobile_app_access1" },
                    { "data": "flight_access" },
                    { "data": "flight_access1" },
                    { "data": "action" },
                ],
                "drawCallback": function(settings){
                    $(".bt-switch input[type='checkbox']").bootstrapSwitch();
                },

                @if(Helper::checkAccess('admin/users/report'))
                dom: 'Bfrtip',
                buttons: [
                    /*{
                        extend: 'pdf',
                        exportOptions: {columns: '1,2,3,4,5,6,7,8,9,10,11,12,13'},
                        pageSize: 'LETTER',
                        customize: function(doc, config) {
                            doc.pageOrientation = 'landscape';
                        }
                    },*/
                    {extend: 'excel',exportOptions: {columns: '1,2,3,4,5,6,7,8,9,10,11,12,13,16,18,20,22,24,26,28,30'}},
                ],
                @endif
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                "columnDefs": [
                    {"targets": [0,13,15,17,18,20,19,21,22,23,24,25,26,27,29,31,32],"orderable": false},
                    // {"targets": [1,2,6,7,8,9,10,13], visible: false}
                    //{"targets": [1,2,6,7,8,9,10, -3], visible: false}
                    {"targets": [1,2,6,7,8,9,10,12,14,16,18,20,22,24,26,28,30], visible: false}
                ],
                "aaSorting": [],
		    });

            $(document).on('click','input[name="user_ids[]"]',function(){
                $(document).find('input[name="user_ids[]"]').prop('checked', $(this).prop('checked'));
                $(document).find('input[name="user_id[]"]').prop('checked', $(this).prop('checked'));
                var length = $('input[name="user_id[]"]:checked').length;

                if(length > 0)
                {
                    // $('.deleteUsers').removeClass('disabled');
                    $('.changeStatusUsers').removeClass('disabled');
                    table.rows().select();
                }else{
                    // $('.deleteUsers').addClass('disabled');
                    $('.changeStatusUsers').addClass('disabled');
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
                    // $('.deleteUsers').removeClass('disabled');
                    $('.changeStatusUsers').removeClass('disabled');
                    table.rows().select();
                }else{
                    // $('.deleteUsers').addClass('disabled');
                    $('.changeStatusUsers').addClass('disabled');
                    table.rows().deselect();
                }

            } );

            $(document).on('click','input[name="user_id[]"]',function(){
                var length = $('input[name="user_id[]"]:checked').length;
                var row = $(this).closest('tr');
                var index = row.index();

                if(length > 0)
                {
                    // $('.deleteUsers').removeClass('disabled');
                    $('.changeStatusUsers').removeClass('disabled');
                    table.row(index).select();
                }else{
                    // $('.deleteUsers').addClass('disabled');
                    $('.changeStatusUsers').addClass('disabled');
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


            $(document).on('switchChange.bootstrapSwitch', '.statusUser', function (event, state) {
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
                    $('.userstatus').text('deactivate');
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val('AC');
                    $('.userstatus').text('activate');
                }

                $.ajax({
                    type: "post",
                    url: "{{route('changeStatusAjaxUser')}}",
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

                        toastr.error("Unable to update user.","Status",{
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
                // $('#brandstatusModal').modal('show');
            });



            $(document).on('switchChange.bootstrapSwitch', '.verifyUser', function (event, state) {
                var x;

                if($(this).is(':checked'))
                    x = 1;
                else
                    x = 0;
                var id = $(this).data('id');
                $('#statuscode').text($(this).data('code'));
                if(x == 0)
                {
                    $('#statusid').val(id);
                    $('#status').val(0);
                    $('.userstatus').text('deactivate');
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val(1);
                    $('.userstatus').text('Yes');
                }

                $.ajax({
                    type: "post",
                    url: "{{route('changeVerifyStatusAjaxUser')}}",
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

                        toastr.error("Unable to update user.","Status",{
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
                // $('#brandstatusModal').modal('show');
            });
            $(document).on('switchChange.bootstrapSwitch', '.travelAccess', function (event, state) {
                var x;

                if($(this).is(':checked'))
                    x = 'yes';
                else
                    x = 'no';
                var id = $(this).data('id');
                $('#statuscode').text($(this).data('code'));
                if(x == 'no')
                {
                    $('#statusid').val(id);
                    $('#status').val('no');
                    $('.userstatus').text('No');
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val('yes');
                    $('.userstatus').text('Yes');
                }

                $.ajax({
                    type: "post",
                    url: "{{route('changeTravelAcessAjaxUser')}}",
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

                        toastr.error("Unable to update user.","Status",{
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
                // $('#brandstatusModal').modal('show');
            });
            $(document).on('switchChange.bootstrapSwitch', '.flightAccess', function (event, state) {
                var x;

                if($(this).is(':checked'))
                    x = 'yes';
                else
                    x = 'no';
                var id = $(this).data('id');
                $('#statuscode').text($(this).data('code'));
                if(x == 'no')
                {
                    $('#statusid').val(id);
                    $('#status').val('no');
                    $('.userstatus').text('No');
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val('yes');
                    $('.userstatus').text('Yes');
                }

                $.ajax({
                    type: "post",
                    url: "{{route('changeFlightAcessAjaxUser')}}",
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

                        toastr.error("Unable to update user.","Status",{
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
                // $('#brandstatusModal').modal('show');
            });

            $(document).on('switchChange.bootstrapSwitch', '.groceryFrontAccess', function (event, state) {
                var x;

                if($(this).is(':checked'))
                    x = 'yes';
                else
                    x = 'no';
                var id = $(this).data('id');
                $('#statuscode').text($(this).data('code'));
                if(x == 'no')
                {
                    $('#statusid').val(id);
                    $('#status').val('no');
                    $('.userstatus').text('No');
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val('yes');
                    $('.userstatus').text('Yes');
                }

                $.ajax({
                    type: "post",
                    url: "{{route('changeGroceryAcessAjaxUser')}}",
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

                        toastr.error("Unable to update user.","Status",{
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
                // $('#brandstatusModal').modal('show');
            });
            $(document).on('switchChange.bootstrapSwitch', '.groceryFrontWithApiAccess', function (event, state) {
                var x;

                if($(this).is(':checked'))
                    x = 'yes';
                else
                    x = 'no';
                var id = $(this).data('id');
                $('#statuscode').text($(this).data('code'));
                if(x == 'no')
                {
                    $('#statusid').val(id);
                    $('#status').val('no');
                    $('.userstatus').text('No');
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val('yes');
                    $('.userstatus').text('Yes');
                }

                $.ajax({
                    type: "post",
                    url: "{{route('changeGroceryWithApiAcessAjaxUser')}}",
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

                        toastr.error("Unable to update user.","Status",{
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
                // $('#brandstatusModal').modal('show');
            });

            $(document).on('switchChange.bootstrapSwitch', '.sampleApiAccess', function (event, state) {
                var x;

                if($(this).is(':checked'))
                    x = 'yes';
                else
                    x = 'no';
                var id = $(this).data('id');
                $('#statuscode').text($(this).data('code'));
                if(x == 'no')
                {
                    $('#statusid').val(id);
                    $('#status').val('no');
                    $('.userstatus').text('No');
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val('yes');
                    $('.userstatus').text('Yes');
                }

                $.ajax({
                    type: "post",
                    url: "{{route('changeSampleApiAcessAjaxUser')}}",
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

                        toastr.error("Unable to update user.","Status",{
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
                // $('#brandstatusModal').modal('show');
            });

            $(document).on('switchChange.bootstrapSwitch', '.databaseAccess', function (event, state) {
                var x;

                if($(this).is(':checked'))
                    x = 'yes';
                else
                    x = 'no';
                var id = $(this).data('id');
                $('#statuscode').text($(this).data('code'));
                if(x == 'no')
                {
                    $('#statusid').val(id);
                    $('#status').val('no');
                    $('.userstatus').text('No');
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val('yes');
                    $('.userstatus').text('Yes');
                }

                $.ajax({
                    type: "post",
                    url: "{{route('changeDatabaseAcessAjaxUser')}}",
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

                        toastr.error("Unable to update user.","Status",{
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
                // $('#brandstatusModal').modal('show');
            });

            $(document).on('switchChange.bootstrapSwitch', '.adminPanelAccess', function (event, state) {
                var x;

                if($(this).is(':checked'))
                    x = 'yes';
                else
                    x = 'no';
                var id = $(this).data('id');
                $('#statuscode').text($(this).data('code'));
                if(x == 'no')
                {
                    $('#statusid').val(id);
                    $('#status').val('no');
                    $('.userstatus').text('No');
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val('yes');
                    $('.userstatus').text('Yes');
                }

                $.ajax({
                    type: "post",
                    url: "{{route('changeAdminPanelAcessAjaxUser')}}",
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

                        toastr.error("Unable to update user.","Status",{
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
                // $('#brandstatusModal').modal('show');
            });



            $(document).on('switchChange.bootstrapSwitch', '.mobileAppAccess', function (event, state) {
                var x;

                if($(this).is(':checked'))
                    x = 'yes';
                else
                    x = 'no';
                var id = $(this).data('id');
                $('#statuscode').text($(this).data('code'));
                if(x == 'no')
                {
                    $('#statusid').val(id);
                    $('#status').val('no');
                    $('.userstatus').text('No');
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val('yes');
                    $('.userstatus').text('Yes');
                }

                $.ajax({
                    type: "post",
                    url: "{{route('changeMobileAppAcessAjaxUser')}}",
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

                        toastr.error("Unable to update user.","Status",{
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
                // $('#brandstatusModal').modal('show');
            });



            $('.deleteUser').click(function(){
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
                        if(data.status == 1)
                        {
                            table.ajax.reload();
                            /*var ids = JSON.parse(data.ids);
                            for(var i = 0; i < ids.length; i++)
                            {
                                if(id.includes('delete'))
                                    $('tr[data-id='+ids[i]+']').remove();
                                else{
                                    var status = $('tr[data-id='+ids[i]+'] td:nth-child(7)').text();

                                    if(status == 'Active'){
                                        $('tr[data-id='+ids[i]+'] td:nth-child(7)').text('Inactive');
                                        $('tr[data-id='+ids[i]+'] td:nth-child(8)').find('.changeStatus').attr('data-original-title', 'Activate User').tooltip();
                                        $('tr[data-id='+ids[i]+'] td:nth-child(8)').find('.changeStatus').find('.fa').removeClass('fa-lock').addClass('fa-unlock');
                                    }
                                    else{
                                        $('tr[data-id='+ids[i]+'] td:nth-child(7)').text('Active');
                                        $('tr[data-id='+ids[i]+'] td:nth-child(8)').find('.changeStatus').attr('data-original-title', 'Deactivate User').tooltip();
                                        $('tr[data-id='+ids[i]+'] td:nth-child(8)').find('.changeStatus').find('.fa').removeClass('fa-unlock').addClass('fa-lock');
                                    }
                                }
                            }*/

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

                        // $('.deleteUsers').addClass('disabled');
                        $('.changeStatusUsers').addClass('disabled');
                    },
                    error: function(data)
                    {

                        toastr.error("Unable to update users.","Status",{
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
