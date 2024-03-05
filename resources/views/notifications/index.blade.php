@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
	<div class="content-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Notifications</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item active">Notifications</li>
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
                        <h4 class="card-title">Notifications</h4>
                        {{-- <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6> --}}
                        <div class="dt-buttons float-right">
                            @if (Helper::checkAccess(route('createNotifications')))
                                <a href="{{route('createNotification')}}" class="btn dt-button">Send Notification</a>
                            @endif
                        </div>
                        <div class="table-responsive m-t-40">
                            <table id="notificationTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Sender</th>
                                        <th>Receiver</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Notification Type</th>
                                        <th>Date & Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Sender</th>
                                        <th>Receiver</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Notification Type</th>
                                        <th>Date & Time</th>
                                        <th>Status</th>
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
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script type="text/javascript">
    	$(function(){
            var table = $('#notificationTable').DataTable({
                "ajax": {
                    url:"{{route('notificationsAjax')}}",
                    dataSrc:"data",
                    data:{
                        from_date: $('input[name=from_date]').val(),
                        end_date: $('input[name=end_date]').val(),
                        status: $('select[name=status]').val(),
                        notification_type: $('select[name=notification_type]').val(),
                    }
                    // type: "get"
                },
                paging: true,
                pageLength: 10,
                // "bProcessing": true,
                "bServerSide": true,
                "bLengthChange": false,
                "aoColumns": [
                    { "data": "sno" },
                    { "data": "user_id" },
                    { "data": "parent_id" },
                    { "data": "title" },
                    { "data": "description" },
                    { "data": "notification_type" },
                    { "data": "created_at" },
                    { "data": "status" },
                ],

                "columnDefs": [
                    {"targets": [0],"orderable": false},
                ],
                "aaSorting": [],
            });

            $('#notificationList').html('<a class="dropdown-item"><p class="mb-0 font-weight-normal float-left">No new notifications</p></a>');
            $('#notCount').hide();
    	});
    </script>
@endpush