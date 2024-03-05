@extends('layouts.app')
@section('title', ucfirst($type).' Coupon Code')
<style>
.dropdown-menu {
    overflow-y: scroll !important;
    height: 300px !important ;
}
</style>
@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Branch</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('couponCodes')}}">Coupon Codes</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Coupon Code</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.message')
                    <div class="card-body">

                        @if($type == 'add')
                            <h4>Fill In Coupon Code Details</h4>
                        @elseif($type == 'edit')
                            <h4>Edit Coupon Code Details</h4>
                        @endif
                        <hr>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form class="form-material m-t-50 row form-valide" method="post" action="{{$url}}" enctype="multipart/form-data">

                            {{csrf_field()}}
                            <div class="form-group col-md-6 m-t-20">
                                <label>Coupon Code</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" placeholder="Enter Coupon Code" name="coupon_code" value="{{old('coupon_code', $code->code)}}" maxlength="10">
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Title</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" placeholder="Enter Title" name="coupon_title" value="{{old('coupon_title', $code->title)}}" maxlength="200">
                            </div>
                            <div class="form-group col-md-12 m-t-20">
                                <label>Description</label>
                                <textarea name="coupon_description" class="form-control" placeholder="Enter Code Description" value="{{old('coupon_description', $code->description)}}" rows="5">{{old('coupon_description', $code->description)}}</textarea>
                            </div>
                            <div class="form-group col-md-12 m-t-20">
                                <label>Category<sup class="text-reddit"> *</sup> <i class="fa fa-question-circle" class="aria-hidden" data-toggle="tooltip" data-placement="top" title="If any category is disabled, then coupon code is already applied on that category."></i></label>
                                <select class="form-control selectpicker" name="coupon_categories[]" id="categories" multiple>
                                    @if(count($categories) > 0)
                                        @php
                                            $codeCat = explode(',',$code->category_id);
                                            $child=[];
                                        @endphp
                                        @if($categories)
                                            @foreach($categories as $id=> $cat)
                                                @php
                                                    if(isset($subcategories[$id])){
                                                        foreach($subcategories[$id] as $ch=> $sub){
                                                            if(!in_array($sub, $existing) && in_array($id,$codeCat)){
                                                                array_push($child, $sub);
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <option value="{{$id}}" @if(in_array($id,$codeCat)) selected style="color: #000" @elseif(in_array($id, $child)) selected style="color: #000" @else @if(in_array($id,$existing)) disabled @else style="color: #000" @endif @endif>{{$cat}}</option>
                                            @endforeach
                                        @endif
                                    @else
                                        <option value=''>No Categories found</option>
                                    @endif

                                </select>
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Max Use (Per User)</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control numberInput" name="max_use" placeholder="Enter no. of uses allowed for a user" value="{{old('max_use', $code->max_use)}}" maxlength="10">
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Total Max Use</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control numberInput" name="max_use_total" placeholder="Enter no. of uses allowed in total" value="{{old('max_use_total', $code->max_use_total)}}" maxlength="10">
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label for="from_date">From Date<sup class="text-reddit"> *</sup></label>
                                <input type="text" id="start_date" name="start_date" class="form-control" placeholder="Enter start date" value='{{old('start_date', $code->start_date)}}'>
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label for="end_date">End Date<sup class="text-reddit"> *</sup></label>
                                <input type="text" id="end_date" name="end_date" class="form-control" placeholder="Enter end date" value='{{old('end_date', $code->end_date)}}'>
                            </div>

                            <input type="hidden" name="status" value="@if(isset($code) && $code->status != null) {{$code->status}} @else AC @endif">
                            <div class="form-group bt-switch col-md-6 m-t-20">
                                <label class="col-md-4">Status</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" @if($type == 'edit') @if(isset($code) && $code->status == 'AC') checked @endif @else checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-status" id="statusCat">
                                </div>
                            </div>

                            <div class="card card-outline-inverse col-12 m-t-20">
                                <div class="card-body">
                                    <h3 class="card-title">Coupon Code Ranges</h3>
                                    <div class="dt-buttons float-right">
                                        <a href="javascript:void(0)" class="btn dt-button py-2 addRange">Add Range</a>
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="no_ranges" id="no_ranges" value="0">
                                    </div>
                                    <div id="ranges">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 m-t-20">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                                <a href="{{route('couponCodes')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>
    <div id="dummyRow" style="display: none;">
        <div class="rangeRow" id="row_0">
            <h6 class="text-danger" id="codeerror_0" style="display: none;">This coupon range cannot be edited/deleted. It has been used in carts/orders.</h6>
            <div class="float-right">
                <a href="javascript:void(0)" class="deleteRow" id="deleterow_0"><i class="fa fa-trash" aria-hidden="true"></i></a>
            </div><br>
            <input type="hidden" name="coderangeid_0" value="0">
            <div class="row">
                <div class="form-group col-md-4 m-t-20">
                    <label>Range - Minimum Amount (in {{session()->get('currency')}})</label><sup class="text-reddit"> *</sup>
                    <input type="text" name="codemin_0" class="form-control decimalInput" placeholder="Enter Minimum Amount" maxlength="10">
                </div>
                <div class="form-group col-md-4 m-t-20 val-maxamt">
                    <label>Range - Maximum Amount (in {{session()->get('currency')}})</label><sup class="text-reddit"> *</sup>
                    <input type="text" name="codemax_0" class="form-control decimalInput" placeholder="Enter Maximum Amount" maxlength="10">
                </div>
                <div class="form-group col-md-4 m-t-20">
                    <label>Discount Value (in %)</label><sup class="text-reddit"> *</sup>
                    <input type="text" class="form-control decimalInput" name="codedisc_0" placeholder="Enter Discount Value" data-mask-as-number-max="100">
                </div>

                <input type="hidden" name="codestatus_0" value="AC">
                <div class="form-group bt-switch col-md-6 m-t-20">
                    <label class="col-md-4">Status</label>
                    <div class="col-md-3" style="float: right;">
                        <input type="checkbox" data-on-color="success" checked data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-codestatus_0" class="codeStatus" id="codestatus_0">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{URL::asset('/js/jquery-mask-as-number.js')}}"></script>
    <script type="text/javascript">
        $(function(){
            jQuery.validator.addMethod("uniqueRange", function(value, element, param) {
                var clicked = $(element).attr('name').split('_')[1];

                var res = true;
                var count = $('input[name^=codemin]:visible').length;

                if(count > 1){
                    $('input[name^=codemin]:visible').each(function(index,item){
                        var id = $(this).attr('name').split('_')[1];

                        if($(this).val()!='' && $('input[name=codemax_'+id+']').val()!='' && id!=clicked){
                            var minclicked = Number($('input[name=codemin_'+clicked+']').val());
                            var maxclicked = Number($('input[name=codemax_'+clicked+']').val());
                            var minid = Number($(this).val());
                            var maxid = Number($('input[name=codemax_'+id+']').val());

                            if(minclicked <= minid && maxid <= maxclicked){
                                res= false;
                            }
                            else if(minclicked <= minid && (maxclicked <= maxid && maxclicked >= minid)){
                                res= false;
                            }

                            else if((minclicked <= maxid && minclicked >= minid) && maxclicked >= maxid){
                                res= false;
                            }
                        }
                    });
                }
                return res;
            }, "This range is already added.");


            $(document).on('keyup',".decimalInput, .numberInput",function(e){

                if($(this).val().indexOf('-') >=0){
                    $(this).val($(this).val().replace(/\-/g,''));
                }
            })

            @if($type=='edit')

                $('#start_date').bootstrapMaterialDatePicker('setMinDate', new Date("{{$code->start_date}}"));
                $('#end_date').bootstrapMaterialDatePicker('setMinDate', new Date("{{$code->start_date}}"));
            @endif

            $('#categories').selectpicker();

            var selectedCat = '';

            $("#categories").on("changed.bs.select", function(e, clickedIndex, newValue, oldValue) {
                $(this).valid();
                var value = $(this).val();
                var selectedD = $(this).find('option').eq(clickedIndex).val();
                var subcategories = $.parseJSON('<?php echo json_encode($subcategories); ?>');
                console.log(selectedD,subcategories);

                //if parent category is selected then all the subcategories gets selected by default
                if(selectedD in subcategories && $.inArray(selectedD.toString(),value)>=0){
                    $.each(subcategories[selectedD],function(ind,it){
                        if(!$('option[value='+it+']').attr('disabled'))
                            value.push(it.toString());
                        $('#categories').selectpicker('val',value);
                    })
                }
                else{
                    //if all the subcategories are deselected, then parent category will also get deselect
                    $.each(value,function(index,item){
                        var count = 0;
                        if(item in subcategories){
                            $.each(subcategories[item],function(ind,it){
                                if($.inArray(it.toString(),value) < 0){
                                    count++;
                                }
                            });
                            if(subcategories[item].length> 0 && count == subcategories[item].length)
                            {
                                if(!$('option[value='+item+']').attr('disabled'))
                                    value.splice(value.indexOf(item),1);
                                $('#categories').selectpicker('val',value);
                            }
                        }
                    });
                    //if all subcategories are selected then parent category gets selected
                    $.each(subcategories,function(index,item){
                        console.log(item,index);
                        // if parent category is removed, then it won't be added here
                        if(index!=selectedD){
                            var countNew = 0;
                            $.each(subcategories[index],function(ind,it){
                                if($.inArray(it.toString(),value) >= 0){
                                    countNew++;
                                }
                            });
                            if(subcategories[index].length > 0 && countNew == subcategories[index].length)
                            {

                                $.each(item,function(ind,it){
                                    if(!$('option[value='+it+']').attr('disabled'))
                                        value.push(ind.toString());

                                    $('#categories').selectpicker('val',value);
                                });
                            }
                        }
                    });
                console.log(value);
                }

            });

            $(document).find(".numberInput").maskAsNumber({receivedMinus:false});
            $(document).find(".decimalInput").maskAsNumber({receivedMinus:false,decimals:6});
            $('input[name=max_use_total]').val("{{$code->max_use_total}}");
            $('input[name=max_use]').val("{{$code->max_use}}");

            @if($type == 'add')
                addRow();
                $('.deleteRow').hide();
            @else
                var count = 1;

                @foreach($code->coupon_range as $value)
                    addRow();
                    $('#ranges').find('input[name=coderangeid_'+count+']').val("{{$value->id}}");
                    $('#ranges').find('input[name=codemin_'+count+']').val("{{$value->min_price}}");
                    $('#ranges').find('input[name=codemax_'+count+']').val("{{$value->max_price}}");
                    $('#ranges').find('input[name=codedisc_'+count+']').val("{{$value->value}}");
                    $('#ranges').find('input[name=codestatus_'+count+']').val("{{$value->status}}");

                    var state="{{$value->status}}" == 'AC' ? true:false;

                    $('#ranges').find('input[name=val-codestatus_'+count+']').bootstrapSwitch('state', "{{$value->status}}" == 'AC' ? true:false, false);
                    if(!state){
                        var width = Number($('#ranges').find('input[name=val-codestatus_'+count+']').closest('.bootstrap-switch').css('width').split('px')[0])+10;
                        $('#ranges').find('input[name=val-codestatus_'+count+']').closest('.bootstrap-switch').css('width',width+'px');
                    }
                    // $('#ranges input[name=val-codestatus_'+count+']').bootstrapSwitch();

                    //variation cannot be deleted once placed in cart or ordered
                    @if(count($code->coupon_range) > 1)
                        @if(count($value->code_orderitems) <= 0 && count($value->code_cartitems) <= 0)
                            $('#ranges').find('#deleterow_'+count).show();
                        @else
                            $('#ranges').find('#deleterow_'+count).hide();
                            // $('#ranges').find('input[name=coderangeid_'+count+']').attr('disabled',true);
                            $('#ranges').find('input[name=codemin_'+count+']').attr('disabled',true);
                            $('#ranges').find('input[name=codemax_'+count+']').attr('disabled',true);
                            $('#ranges').find('input[name=codedisc_'+count+']').attr('disabled',true);
                            $('#ranges').find('#codeerror_'+count).show();
                        @endif
                    @else
                        $('#ranges').find('#deleterow_'+count).hide();

                        @if(count($value->code_orderitems) > 0 || count($value->code_cartitems ) > 0)
                            // $('#ranges').find('input[name=coderangeid_'+count+']').attr('disabled',true);
                            $('#ranges').find('input[name=codemin_'+count+']').attr('disabled',true);
                            $('#ranges').find('input[name=codemax_'+count+']').attr('disabled',true);
                            $('#ranges').find('input[name=codedisc_'+count+']').attr('disabled',true);
                            $('#ranges').find('#codeerror_'+count).show();
                        @endif
                    @endif
                    count++;
                @endforeach
            @endif

            function addRow(){
                var row = Number($('#no_ranges').val())+1;
                var html = $('#dummyRow').html();
                var updated = html.replace(/_0/g, '_'+row);
                $('#ranges').append(updated);
                $('#no_ranges').val(row);
                // $('.deleteRow').show();
                $('#ranges #row_'+row).find(".numberInput").maskAsNumber({receivedMinus:false});
                $('#ranges #row_'+row).find(".decimalInput").maskAsNumber({receivedMinus:false,decimals:6});
                addRules(row);
                $('input[name=codemin_'+row+']').rules('remove','max');
                $('input[name=codemax_'+row+']').rules('remove','min');
            }

            function addRules(id){
                $('input[name=codemin_'+id+']').rules('add','required');
                $('input[name=codemin_'+id+']').rules('add','uniqueRange');
                $('input[name=codemin_'+id+']').rules('add',{maxlength:10});
                $('input[name=codemax_'+id+']').rules('add',{maxlength:10});
                $('input[name=codemax_'+id+']').rules('add','required');
                $('input[name=codemax_'+id+']').rules('add','uniqueRange');
                $('input[name=codedisc_'+id+']').rules('add','required');
                $('input[name=codedisc_'+id+']').rules('add',{max:100});
                $('#ranges input[name=val-codestatus_'+id+']').bootstrapSwitch();

            }

            function removeRules(id){
                $('input[name=codemin_'+id+']').rules('remove','required');
                $('input[name=codemin_'+id+']').rules('remove','uniqueRange');
                // $('input[name=codemin_'+id+']').rules('remove','max');
                $('input[name=codemin_'+id+']').rules('remove','maxlength');
                $('input[name=codemax_'+id+']').rules('remove','maxlength');
                // $('input[name=codemax_'+id+']').rules('remove','min');
                $('input[name=codemax_'+id+']').rules('remove','required');
                $('input[name=codemax_'+id+']').rules('remove','uniqueRange');
                $('input[name=codedisc_'+id+']').rules('remove','required');
                $('input[name=codedisc_'+id+']').rules('remove','max');
            }

            $(document).on('change','input[name^=codemin]',function(){
                var id = $(this).attr('name').split('_')[1];


                $('input[name=codemax_'+id+']').rules('add',{min: function(value, element, params) {
                    if($(value).val()>0){
                        return parseInt($('input[name=codemin_'+id+']').val());
                    }
                }
                });

                if($('input[name=codemax_'+id+']').val() > 0){
                    $('input[name=codemax_'+id+']').valid();
                }
                if($('input[name=codemin_'+id+']').val() > 0){
                    $('input[name=codemin_'+id+']').valid();
                }
            });

            $(document).on('change','input[name^=codemax_]',function(){
                var id = $(this).attr('name').split('_')[1];

                $('input[name=codemin_'+id+']').rules('add',{max: function(value) {
                    if($(value).val()>0){
                        return parseInt($('input[name=codemax_'+id+']').val());
                    }
                }
                });

                if($(this).val() > 0){
                    $('input[name=codemax_'+id+']').valid();
                }
                if($('input[name=codemin_'+id+']').val() > 0){
                    $('input[name=codemin_'+id+']').valid();
                }
            });

            $(document).on('change', '#ranges input[name^=codemin], #ranges input[name^=codemax]', function(){
                var close = $(this).closest('.rangeRow');
                close.find('input[name^=codemin]').valid();
                close.find('input[name^=codemax]').valid();
            });

            $(document).on('change','input[name=max_use]',function(){

                $('input[name=max_use_total]').rules('add',{min: function() {
                return parseInt($('input[name=max_use]').val()); }});
                if($('input[name=max_use_total]').val() > 0){
                    $('input[name=max_use_total]').valid();
                }
                if($('input[name=max_use]').val() > 0){
                    $('input[name=max_use]').valid();
                }
            });

            $(document).on('change','input[name=max_use_total]',function(){

                $('input[name=max_use]').rules('add',{max: function() {
                return parseInt($('input[name=max_use_total]').val()); }});
                if($(this).val() > 0){
                    $('input[name=max_use_total]').valid();
                }
                if($('input[name=max_use]').val() > 0){
                    $('input[name=max_use]').valid();
                }
            });

            $(document).on('change', 'input[name=max_use], #ranges input[name=max_use_total]', function(){
                if($('input[name=max_use_total]').val() > 0){
                    $('input[name=max_use_total]').valid();
                }
                if($('input[name=max_use]').val() > 0){
                    $('input[name=max_use]').valid();
                }
            });

            $(document).on('click','.deleteRow',function(){
                var id = $(this).attr('id').split('_')[1];
                var deleteId = $('input[name=coderangeid_'+id+']').val();

                if(deleteId>0){
                    $.ajax({
                        type: "post",
                        url: "{{route('deleteRange')}}",
                        data: {id: deleteId},
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

                            toastr.error("Unable to delete variation.","Status",{
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

                removeRules(id);
                $(this).closest('.rangeRow').remove();
                $('#no_ranges').val($('#no_ranges').val()-1);
                if($('#no_ranges').val() == 1)
                    $('.deleteRow').hide();

                var count = 1;
                $('#ranges .rangeRow').each(function(index,elem){
                    var id = $(this).attr('id').split('_')[1];
                    $(this).attr('id','row_' + count);
                    var coderange = $('#ranges').find('input[name=coderangeid_'+id+']').val();
                    var codemin = $('#ranges').find('input[name=codemin_'+id+']').val();
                    var codemax = $('#ranges').find('input[name=codemax_'+id+']').val();
                    var codedisc = $('#ranges').find('input[name=codedisc_'+id+']').val();
                    var codestatus = $('#ranges').find('input[name=codestatus_'+id+']').val();
                    $('#ranges input[name=val-codestatus_'+id+']').bootstrapSwitch('destroy');


                    var replace = "_"+id;
                    var re = new RegExp(replace,"g");
                    var html = $(this).html().replace(re, '_' + count);
                    $(this).html(html);


                    //check this
                    $('#ranges').find('input[name=coderangeid_'+count+']').val(coderange);
                    $('#ranges').find('input[name=codemin_'+count+']').val(codemin);
                    $('#ranges').find('input[name=codemax_'+count+']').val(codemax);
                    $('#ranges').find('input[name=codedisc_'+count+']').val(codedisc);
                    $('#ranges').find('input[name=codestatus_'+count+']').val(codestatus);

                    addRules(count);
                    var state= (codestatus == 'AC') ? true:false;

                    $('#ranges').find('input[name=val-codestatus_'+count+']').bootstrapSwitch('state', state, false);
                    if(!state){
                        var width = Number($('#ranges').find('input[name=val-codestatus_'+count+']').closest('.bootstrap-switch').css('width').split('px')[0])+10;
                        $('#ranges').find('input[name=val-codestatus_'+count+']').closest('.bootstrap-switch').css('width',width+'px');
                    }

                    count++;
                });
            })

            $('.addRange').click(function(){
                addRow();
                // if existing range cannot be deleted, then hide delete button
                $('.rangeRow').each(function(index,item){
                    if(!$(this).find('h6[id^=codeerror]').is(':visible')){
                        $(this).find('.deleteRow').show();
                    }
                })
            })

            $(document).on('switchChange.bootstrapSwitch', 'input[name^=val-codestatus]', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                var id = $(this).attr('id').split('_')[1];

                if($(this).is(':checked'))
                    $('input[name=codestatus_'+id+']').val('AC');
                else
                    $('input[name=codestatus_'+id+']').val('IN');
            });

            $('#statusCat').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusCat").is(':checked'))
                    $('input[name=status]').val('AC');
                else
                    $('input[name=status]').val('IN');
            });


            /*$(document).find('textarea[name=code_description]').summernote({
                height: 350, // set editor height
                minHeight: null, // set minimum height of editor
                maxHeight: null, // set maximum height of editor
                focus: false // set focus to editable area after initializing summernote
            });*/


            @if($type == 'edit')
                $('input[name=coupon_code]').rules('add', {remote: APP_NAME + "/admin/couponCodes/checkCouponCode/{{$code->id}}"});
            @endif

            $('input[name=no_ranges]').rules('add',{min:1,messages:{min:"Please add atleast one range."}});

        });
    </script>
@endpush