@extends('layouts.app')
@section('title', 'Product - ' . ucfirst($product->name))

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Branch</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('products')}}">Products</a></li>
                    <li class="breadcrumb-item active">View Product</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            @include('layouts.message')
            <div class="col-lg-4 col-xlg-3 col-md-5">
                <div class="card">
                    <div class="card-body">
                        <center class="m-t-30"> <img class="card-title" src="@if($product->image != null){{URL::asset('/uploads/products/'.$product->image)}} @endif" width="100%" />
                            <h4 class="m-t-10 m-b-0">{{$product->name}}</h4>
                        </center>
                    </div>

                </div>
            </div>
            <div class="col-lg-8 col-xlg-9 col-md-7">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link active show" data-toggle="tab" href="#profile" role="tab">Details</a> </li>
                        @if (Helper::checkAccess(route('changeStatusProduct')))
                            <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li>
                        @endif
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="profile" role="tabpanel">
                            <div class="card-body">
                                <div>
                                    <h5 class="p-t-20 db">Name</h5><small class="text-success db">{{$product->name}}</small>
                                    <h5 class="p-t-20 db">Product Code</h5><small class="text-success db">{{$product->product_code}}</small>
                                    <h5 class="p-t-20 db">Category</h5><small class="text-success db">{{($product->category!=null)?$product->category->name:$product->subcategory->parentCat->name}}</small>
                                    @if($product->subcategory!=null)
                                        <h5 class="p-t-20 db">Sub-Category</h5><small class="text-success db">{{$product->subcategory->name}}</small>
                                    @endif
                                    <h5 class="p-t-20 db">Quick Grab</h5><small class="text-success db">{{ucfirst(config('constants.CONFIRM.'.$product->quick_grab))}}</small>
                                    <h5 class="p-t-20 db">Is Exclusive</h5><small class="text-success db">{{ucfirst(config('constants.CONFIRM.'.$product->is_exclusive))}}</small>
                                    <h5 class="p-t-20 db">Created On</h5><small class="text-success db">{{date('Y, M d', strtotime($product->created_at))}}</small>
                                    <h5 class="p-t-20 db">Status</h5><small class="text-success db">{{ucfirst(config('constants.STATUS.'.$product->status))}}</small>
                                    <h5 class="p-t-20 db">Description</h5><small class="text-success db">{!! $product->description !!}</small>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="settings" role="tabpanel">
                            <div class="card-body">
                                <form class="form-horizontal form-material" method="post" action="{{route('changeStatusProduct')}}">
                                    {{csrf_field()}}
                                    <div class="form-group bt-switch">
                                        <div class="col-md-6">
                                            <label class="col-md-6" for="val-block">Status</label>
                                            <input type="hidden" name="statusid" value="{{$product->id}}">
                                            <input type="hidden" name="status" value="{{$product->status}}">
                                            <input type="hidden" name="quick_grab" value="{{$product->quick_grab}}">
                                            <input type="hidden" name="is_exclusive" value="{{$product->is_exclusive}}">
                                            <div class="col-md-2" style="float: right;">
                                                <input type="checkbox" @if($product->status == 'AC') checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" id="statusProduct">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group bt-switch">
                                        <div class="col-md-6">
                                            <label class="col-md-6" for="val-block">Quick Grab</label>
                                            <input type="hidden" name="quick_grab" value="{{$product->quick_grab}}">
                                            <div class="col-md-2" style="float: right;">
                                                <input type="checkbox" @if($product->quick_grab == '1') checked @endif data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="cquickGrab" id="quickGrab">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group bt-switch">
                                        <div class="col-md-6">
                                            <label class="col-md-6" for="val-block">Is Exclusive</label>
                                            <input type="hidden" name="featured" value="{{$product->is_exclusive}}">
                                            <div class="col-md-2" style="float: right;">
                                                <input type="checkbox" @if($product->is_exclusive == '1') checked @endif data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="cfeatured" id="exclusive">
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
        <div class="row">
            <div class="card w-100">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs profile-tab" role="tablist">
                    <li class="nav-item"> <a class="nav-link active show" data-toggle="tab" href="#variations" role="tab">Variations</a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#favs" role="tab">Favorites</a> </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="variations" role="tabpanel">
                        <div class="card-body">
                            <div class="table-responsive m-t-40">

                                @if (Helper::checkAccess(route('changeStatusVariations')))
                                    <div class="dt-buttons">
                                        <a href="javascript:void(0)" data-href="{{route('changeStatusVariations')}}" class="btn btn-secondary disabled bulkAction changeStatusVariations">Activate/Deactivate</a>
                                    </div>
                                @endif
                                <table id="variationsTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_ids[]">Select</label></div></th>
                                            <th>S.No.</th>
                                            @foreach($attributes as $attr)
                                            <th>{{$attr}}</th>
                                            @endforeach
                                            <th>Quantity</th>
                                            <th>Max Quantity</th>
                                            <th>Price</th>
                                            <th>Special Price</th>
                                            <th>Status</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_ids[]">Select</label></div></th>
                                            <th>S.No.</th>
                                            @foreach($attributes as $attr)
                                            <th>{{$attr}}</th>
                                            @endforeach
                                            <th>Quantity</th>
                                            <th>Max Quantity</th>
                                            <th>Price</th>
                                            <th>Special Price</th>
                                            <th>Status</th>
                                            <th>Status</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach($product->variations as $key => $var)
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="{{$var->id}}"><i class="input-helper"></i></label></div>
                                            </td>
                                            <td>{{$key+1}}</td>
                                            @foreach($attributes as $id=>$attr)
                                            <td>{{\App\Model\ProductAttributeVariation::whereVariationId($var->id)->whereAttributeId($id)->whereStatus('AC')->first()->attribute_option->value}}</td>
                                            @endforeach
                                            <td>{{$var->qty}}</td>
                                            <td>{{$var->max_qty}}</td>
                                            <td>{{$var->price}}</td>
                                            <td>{{($var->special_price) ? $var->special_price : '-'}}</td>
                                            <td>{{ucfirst(config('constants.STATUS.' . $var->status))}}</td>
                                            <td>
                                                @if(Helper::checkAccess(route('changeStatusAjaxVariation')))
                                                <div class="bt-switch"><div class="col-md-2"><input type="checkbox" @if($var->status == 'AC') checked @endif data-id="{{ $var->id}}" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusVariation"></div></div>
                                                @else
                                                {{ucfirst(config('constants.STATUS.' . $var->status))}}
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if (Helper::checkAccess(route('changeStatusVariations')))
                                    <div class="dt-buttons">
                                        <a href="javascript:void(0)" data-href="{{route('changeStatusVariations')}}" class="btn btn-secondary disabled bulkAction changeStatusVariations">Activate/Deactivate</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="favs" role="tabpanel">
                        <div class="card-body">

                            <ul class="nav nav-tabs profile-tab" role="tablist">
                                <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#active_favs" role="tab">Active Favorites</a> </li>
                                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#inactive_favs" role="tab">Removed Favorites</a> </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane active" id="active_favs" role="tabpanel">
                                    <div class="table-responsive m-t-40">
                                        <table id="active_favsTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>S.No.</th>
                                                    <th>User</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>S.No.</th>
                                                    <th>User</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                @php
                                                    $i=1;
                                                @endphp
                                                @foreach($product->active_favs()->whereHas('user')->get() as $value)
                                                    <tr>
                                                        <td>{{$i++}}</td>
                                                        <td>#</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="inactive_favs" role="tabpanel">
                                    <div class="table-responsive m-t-40">
                                        <table id="inactive_favsTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>S.No.</th>
                                                    <th>User</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>S.No.</th>
                                                    <th>User</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                @php
                                                    $i=1;
                                                @endphp
                                                @foreach($product->inactive_favs()->whereHas('user')->get() as $value)
                                                    <tr>
                                                        <td>{{$i++}}</td>
                                                        <td>{{$value->user->first_name." ".$value->user->last_name}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
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
    <script type="text/javascript">
        $(document).ready(function(){


            $('#statusProduct').on('switchChange.bootstrapSwitch', function (event, state) {

                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusProduct").is(':checked'))
                    $('input[name=status]').val('AC');
                else
                    $('input[name=status]').val('IN');
            });


            $('#quickGrab').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#quickGrab").is(':checked'))
                    $('input[name=quick_grab]').val('1');
                else
                    $('input[name=quick_grab]').val('0');
            });

            $('#exclusive').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#exclusive").is(':checked'))
                    $('input[name=is_exclusive]').val('1');
                else
                    $('input[name=is_exclusive]').val('0');
            });
            var table = $('#variationsTable').DataTable({
                // "ajax": {
                //     url:"{{route('variationsAjax')}}",
                //     dataSrc:"data",
                //     data:{
                //         product_id: "{{$product->id}}"
                //     }
                // },
                paging: true,
                pageLength: 10,
                // "bProcessing": true,
                // "bServerSide": true,
                "bLengthChange": false,
                // "aoColumns": [
                //     { "data": "select" },
                //     { "data": "sno" },
                //     { "data": "weight" },
                //     { "data": "unit" },
                //     { "data": "qty" },
                //     { "data": "max_qty" },
                //     { "data": "price" },
                //     { "data": "special" },
                //     { "data": "status" },
                //     { "data": "activate" },
                // ],
                // "drawCallback": function(settings){
                //     $(".bt-switch input[type='checkbox']").bootstrapSwitch();
                // },
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                // "columnDefs": [
                //     {"targets": [0,8,9],"orderable": false},
                //     {"targets": [8], visible: false},
                // ],
                "aaSorting": [],
            });
            $(".bt-switch input[type='checkbox']").bootstrapSwitch();

            $('#active_favsTable').DataTable({

                paging: true,
                pageLength: 10,
                // "bProcessing": true,
                "bLengthChange": false,
                "aaSorting": [],
                "bFilter": false
            });
            $('#inactive_favsTable').DataTable({

                paging: true,
                pageLength: 10,
                // "bProcessing": true,
                "bLengthChange": false,
                "aaSorting": [],
                "bFilter": false
            });

            $(document).on('click','input[name="user_ids[]"]',function(){
                $(document).find('input[name="user_ids[]"]').prop('checked', $(this).prop('checked'));
                $(document).find('input[name="user_id[]"]').prop('checked', $(this).prop('checked'));
                var length = $('input[name="user_id[]"]:checked').length;

                if(length > 0)
                {
                    // $('.deleteUsers').removeClass('disabled');
                    $('.changeStatusVariations').removeClass('disabled');
                    table.rows().select();
                }else{
                    // $('.deleteUsers').addClass('disabled');
                    $('.changeStatusVariations').addClass('disabled');
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
                    // $('.deleteUsers').removeClass('disabled');
                    $('.changeStatusVariations').removeClass('disabled');
                    table.row(index).select();
                }else{
                    // $('.deleteUsers').addClass('disabled');
                    $('.changeStatusVariations').addClass('disabled');
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

            $(document).on('switchChange.bootstrapSwitch', '.statusVariation', function (event, state) {
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
                    url: "{{route('changeStatusAjaxVariation')}}",
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

                        toastr.error("Unable to update variation.","Status",{
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

                        // $('.deleteUsers').addClass('disabled');
                        $('.changeStatusVariations').addClass('disabled');
                    },
                    error: function(data)
                    {

                        toastr.error("Unable to update variations.","Status",{
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
