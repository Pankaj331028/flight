@extends('layouts.app')
@section('title', 'Coupon Code - ' . $code->offer_code)

@section('content')
	<div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-12 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('couponCodes')}}">Coupon Codes</a></li>
                    <li class="breadcrumb-item active">Coupon Code</li>
                </ol>
            </div>
        </div>
        <div class="row">
            @include('layouts.message')
            <div class="col-lg-12 col-xlg-12 col-md-12">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link active show" data-toggle="tab" href="#details" role="tab">Details</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#ranges" role="tab">Coupon Ranges</a> </li>
                        @if (Helper::checkAccess(route('changeStatusCouponCode')))
                            <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li>
                        @endif
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="details" role="tabpanel">
                            <div class="card-body">
                                <div>
                                    <small class="text-success p-t-30 db">Coupon Code</small><h5>{{$code->code}}</h5>
                                    <small class="text-success p-t-30 db">Title</small><h5>{{$code->title}}</h5>
                                    <small class="text-success p-t-30 db">Start date</small><h5>{{date('Y M, d', strtotime($code->start_date))}}</h5>
                                    <small class="text-success p-t-30 db">End Date</small><h5>{{date('Y M, d', strtotime($code->end_date))}}</h5>
                                    <small class="text-success p-t-30 db">Categories</small>
                                    <div id="nestable">

                                    </div>
                                    <small class="text-success p-t-30 db">Max Use Total</small><h5>{{$code->max_use_total}}</h5>
                                    <small class="text-success p-t-30 db">Max Use Per User</small><h5>{{$code->max_use}}</h5>
                                    <small class="text-success p-t-30 db">Created On</small><h5>{{date('Y M, d', strtotime($code->created_at))}}</h5>
                                    <small class="text-success p-t-30 db">Description</small><h5>{{$code->description!=null?$code->description:'-'}}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="ranges" role="tabpanel">
                            <div class="card-body">
                                <div class="table-responsive m-t-40">

                                    @if (Helper::checkAccess(route('changeStatusRanges')))
                                        <div class="dt-buttons">
                                            <a href="javascript:void(0)" data-href="{{route('changeStatusRanges')}}" class="btn btn-secondary disabled bulkAction changeStatusRanges">Activate/Deactivate</a>
                                        </div>
                                    @endif
                                    <table id="rangesTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_ids[]">Select</label></div></th>
                                                <th>S.No.</th>
                                                <th>Min Price</th>
                                                <th>Max Price</th>
                                                <th>Discount %</th>
                                                <th>Status</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_ids[]">Select</label></div></th>
                                                <th>S.No.</th>
                                                <th>Min Price</th>
                                                <th>Max Price</th>
                                                <th>Discount %</th>
                                                <th>Status</th>
                                                <th>Status</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                        </tbody>
                                    </table>
                                    @if (Helper::checkAccess(route('changeStatusRanges')))
                                        <div class="dt-buttons">
                                            <a href="javascript:void(0)" data-href="{{route('changeStatusRanges')}}" class="btn btn-secondary disabled bulkAction changeStatusRanges">Activate/Deactivate</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="settings" role="tabpanel">
                            <div class="card-body">
                                <form class="form-horizontal form-material" method="post" action="{{route('changeStatusCouponCode')}}">
                                    {{csrf_field()}}
                                    <div class="form-group bt-switch">
                                        <div class="col-md-6">
                                            <label class="col-md-6" for="val-block">Status</label>
                                            <input type="hidden" name="statusid" value="{{$code->id}}">
                                            <input type="hidden" name="status" value="{{$code->status}}">
                                            <div class="col-md-2" style="float: right;">
                                                <input type="checkbox" @if($code->status == 'AC') checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" id="statusCouponCode">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-success">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function(){

            $('#statusCouponCode').on('switchChange.bootstrapSwitch', function (event, state) {

                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusCouponCode").is(':checked'))
                    $('input[name=status]').val('AC');
                else
                    $('input[name=status]').val('IN');
            });

            var categories = JSON.parse(("{{json_encode($categories)}}").replace(/&quot;/g,'"'));

            var list = '';

            $.each(categories,function(index,item){
                list += item+"<br>";
            })

            $('#nestable').html(list);

            var table = $('#rangesTable').DataTable({
                "ajax": {
                    url:"{{route('rangesAjax')}}",
                    dataSrc:"data",
                    data:{
                        code_id: "{{$code->id}}"
                    }
                },
                paging: true,
                pageLength: 10,
                // "bProcessing": true,
                "bServerSide": true,
                "bLengthChange": false,
                "aoColumns": [
                    { "data": "select" },
                    { "data": "sno" },
                    { "data": "min_price" },
                    { "data": "max_price" },
                    { "data": "value" },
                    { "data": "status" },
                    { "data": "activate" },
                ],
                "drawCallback": function(settings){
                    $(".bt-switch input[type='checkbox']").bootstrapSwitch();
                },
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                "columnDefs": [
                    {"targets": [0,6],"orderable": false},
                    {"targets": [5], visible: false},
                ],
                "aaSorting": [],
            });
            $(document).on('click','input[name="user_ids[]"]',function(){
                $(document).find('input[name="user_ids[]"]').prop('checked', $(this).prop('checked'));
                $(document).find('input[name="user_id[]"]').prop('checked', $(this).prop('checked'));
                var length = $('input[name="user_id[]"]:checked').length;

                if(length > 0)
                {
                    $('.changeStatusRanges').removeClass('disabled');
                    table.rows().select();
                }else{
                    $('.changeStatusRanges').addClass('disabled');
                    table.rows().deselect();
                }
            });

            $('tbody').on( 'click', 'tr', function () {
                $(this).toggleClass('selected');
                var check = $(this).find('input[type=checkbox]');
                check.prop('checked',!check.prop("checked"));
                var length = $('input[name="user_id[]"]:checked').length;

            } );

            $(document).on('click','input[name="user_id[]"]',function(){
                var length = $('input[name="user_id[]"]:checked').length;
                var row = $(this).closest('tr');
                var index = row.index();

                if(length > 0)
                {
                    $('.changeStatusRanges').removeClass('disabled');
                    table.row(index).select();
                }else{
                    $('.changeStatusRanges').addClass('disabled');
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

            $(document).on('switchChange.bootstrapSwitch', '.statusRange', function (event, state) {
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
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val('AC');
                }

                $.ajax({
                    type: "post",
                    url: "{{route('changeStatusAjaxRange')}}",
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

                        toastr.error("Unable to update range.","Status",{
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

                        $('.changeStatusRanges').addClass('disabled');
                    },
                    error: function(data)
                    {

                        toastr.error("Unable to update ranges.","Status",{
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
