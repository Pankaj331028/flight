@extends('layouts.app')
@section('title', ucfirst($type).' Offer')
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
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Offer</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('offers')}}">Offers</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Offer</li>
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
                            <h4>Fill In Offer Details</h4>
                        @elseif($type == 'edit')
                            <h4>Edit Offer Details</h4>
                        @endif

                        @if ($errors->any())
						    <div class="alert alert-danger">
						        <ul>
						            @foreach ($errors->all() as $error)
						                <li>{{ $error }}</li>
						            @endforeach
						        </ul>
						    </div>
						@endif

                        <form class="form-material m-t-40 row form-valide" method="post" action="{{$url}}" enctype="multipart/form-data">

                        	{{csrf_field()}}
                            <div class="col-md-12 px-0">
                                <div class="form-group col-md-6 m-t-20">
                                    <label>Offer Image<sup class="text-reddit"> *</sup></label>
                                    <input type="hidden" name="image_exists" id="image_exists" value="1">

                                    @if($type == 'add' || ($type == 'edit' && $offer->image == null))
                                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                            <div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span>
                                            <input type="file" name="offer_image"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                        </div>
                                    @elseif($type == 'edit')
                                        <br>
                                        <div id="offerImage">
                                            <img src="@if($offer->image != null && file_exists(public_path('/uploads/offers/'.$offer->image))){{URL::asset('/uploads/offers/'.$offer->image)}}@endif" width="20%" />
                                            &nbsp;&nbsp;&nbsp;<a id="changeImage" href="javascript:void(0)" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Delete">Change</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-md-6 m-t-20" style="display: none;">
                                <label>Offer Type<sup class="text-reddit"> *</sup></label>
                                <select class="form-control" name="offer_type" id="offerType">
                                    @foreach(config('constants.PLAN_TYPE') as $key => $dtype)
                                        <option value="{{$key}}" @if($key=='percent') selected @endif>{{$dtype}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Discount Value (in <span class="valueType">%</span>)<sup class="text-reddit"> *</sup></label>
                                <input type="text" class="form-control decimalInput" name="discount_value" placeholder="Enter Discount Value" data-mask-as-number-max="100" value="{{old('discount_value', $offer->value)}}">
                            </div>
                            <div class="form-group col-md-6 m-t-20" id="maxAmountCol">
                                <label>Maximum Discount Amount (in {{session()->get('currency')}})<sup class="text-reddit"> *</sup></label>
                                <input type="text" name="max_amount" class="form-control numberInput" placeholder="Enter Maximum Amount" maxlength="10" value="{{old('max_amount', $offer->max_amount)}}">
                            </div>
                            <div class="form-group col-md-6 m-t-20" id="minAmountCol">
                                <label>Minimum Amount (in {{session()->get('currency')}})<sup class="text-reddit"> *</sup> <i class="fa fa-question-circle" class="aria-hidden" data-toggle="tooltip" data-placement="top" title="This is the minimum product price for the offer to apply. Products having price less than this won't be applicable for this offer"></i></label>
                                <input type="text" name="min_amount" class="form-control numberInput" placeholder="Enter Minimum Amount" maxlength="10" value="{{old('min_amount', $offer->min_amount)}}">
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label for="from_date">From Date<sup class="text-reddit"> *</sup></label>
                                <input type="text" id="start_date" name="start_date" class="form-control" placeholder="Enter start date" value='{{old('start_date', $offer->start_date)}}'>
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label for="end_date">End Date<sup class="text-reddit"> *</sup></label>
                                <input type="text" id="end_date" name="end_date" class="form-control" placeholder="Enter end date" value='{{old('end_date', $offer->end_date)}}'>
                            </div>
                            <div class="form-group col-md-12 m-t-20"> 
                                <label>Category<sup class="text-reddit"> *</sup> <i class="fa fa-question-circle" class="aria-hidden" data-toggle="tooltip" data-placement="top" title="If any category is disabled, then offer is already applied on that category."></i></label>
                                <select class="form-control selectpicker" name="categories[]" id="categories" multiple>
                                    @if(count($categories) > 0)
                                        @php
                                            $offerCat = explode(',',$offer->category_id);
                                            $child=[];
                                        @endphp
                                        @if($categories)
                                            @foreach($categories as $id=> $cat)
                                                @php
                                                    if(isset($subcategories[$id])){
                                                        foreach($subcategories[$id] as $ch=> $sub){
                                                            if(!in_array($sub, $existing) && in_array($id,$offerCat)){
                                                                array_push($child, $sub);
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <option value="{{$id}}" @if(in_array($id,$offerCat)) selected style="color: #000" @elseif(in_array($id, $child)) selected style="color: #000" @else @if(in_array($id,$existing)) disabled @else style="color: #000" @endif @endif>{{$cat}}</option>
                                            @endforeach
                                        @endif
                                    @else
                                        <option value=''>No Categories found</option>
                                    @endif

                                </select>
                            </div>
                            <input type="hidden" name="status" value="@if(isset($offer) && $offer->status != null) {{$offer->status}} @else AC @endif">
                            <div class="form-group bt-switch col-md-6 m-t-20">
                                <label class="col-md-4">Status</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" @if($type == 'edit') @if(isset($offer) && $offer->status == 'AC') checked @endif @else checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-status" id="statusOffer">
                                </div>
                            </div>
                            <input type="hidden" name="top_offer" value="@if(isset($offer) && $offer->is_top_offer != null) {{$offer->is_top_offer}} @else 0 @endif">
                            <div class="form-group bt-switch col-md-6 m-t-20">
                                <label class="col-md-4">Top Offer</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" @if($type == 'edit') @if(isset($offer) && $offer->is_top_offer == '1') checked @endif @endif data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="topOffer" id="topOffer">
                                </div>
                            </div>
							<div class="col-12 m-t-20">
	                            <button type="submit" class="btn btn-success waves-effect waves-light m-r-10">Save</button>
	                            <a href="{{route('offers')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
	                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>
@endsection
@push('scripts')
<script src="{{URL::asset('/js/jquery-mask-as-number.js')}}"></script>
    <script type="text/javascript">
        $(function(){
            $(document).find(".decimalInput, .numberInput").on('keyup',function(e){

                if($(this).val().indexOf('-') >=0){
                    $(this).val($(this).val().replace(/\-/g,''));
                }
            })

            $(document).find(".numberInput").maskAsNumber({receivedMinus:false});
            $(document).find(".decimalInput").maskAsNumber({receivedMinus:false,decimals:6});
            $('input[name=max_amount]').val("{{round($offer->max_amount,2)}}");
            $('input[name=min_amount]').val("{{round($offer->min_amount,2)}}");

            $('#statusOffer').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusOffer").is(':checked'))
                    $('input[name=status]').val('AC');
                else
                    $('input[name=status]').val('IN');
            });

            $('#topOffer').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#topOffer").is(':checked'))
                    $('input[name=top_offer]').val('1');
                else
                    $('input[name=top_offer]').val('0');
            });

            $('#categories').selectpicker();

            $('#changeImage').click(function(){
                $('#offerImage').parent().append('<div class="fileinput fileinput-new input-group" data-provides="fileinput"><div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span><input type="file" name="offer_image"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a></div>');
                $('.tooltip').tooltip('hide');
                $('#offerImage').remove();
                $('#image_exists').val(0);
            });


            @if($type=='edit')

                $('#start_date').bootstrapMaterialDatePicker('setMinDate', new Date("{{$offer->start_date}}"));
                $('#end_date').bootstrapMaterialDatePicker('setMinDate', new Date("{{$offer->start_date}}"));
            @endif


            $('#offerType').change(function(){
                if($(this).val() == 'percent')
                {
                    $('#maxAmountCol').show();
                    $('input[name="max_amount"]').rules('add', 'required');
                    $('input[name="discount_value"]').rules('add', {max: 100});
                    (!$('#discount_value-error').is(':visible') && $('input[name="discount_value"]').val() > 100) ? $('input[name="discount_value"]').closest('.form-group').append('<div id="discount_value-error" class="invalid-feedback animated fadeInDown" style="display: block;">Please enter a value less than or equal to 100.</div>') : $('#discount_value-error').show();
                    $('.valueType').text('%');
                }
                else
                {
                    $('#maxAmountCol').hide();
                    $('input[name="max_amount"]').rules('remove', "required");
                    $('input[name="discount_value"]').rules('remove', "max");
                    $('#discount_value-error').remove();
                    $('.valueType').text('{{session()->get("currency")}}');

                }
            });

            var selectedCat = '';

            $("#categories").on("changed.bs.select", function(e, clickedIndex, newValue, oldValue) {
                var value = $(this).val();
                var selectedD = $(this).find('option').eq(clickedIndex).val();
                var subcategories = $.parseJSON('<?php echo json_encode($subcategories); ?>');
                console.log(subcategories);
                console.log(value);
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
                                if(!$('option[value='+it+']').attr('disabled'))
                                    value.splice(value.indexOf(item),1);
                                $('#categories').selectpicker('val',value);
                            }
                        }
                    });
                    //if all subcategories are selected then parent category gets selected
                    $.each(subcategories,function(index,item){
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
                                if(!$('option[value='+it+']').attr('disabled'))
                                    value.push(index.toString());
                                $('#categories').selectpicker('val',value);
                            }
                        }
                    });
                }

            });

        });
    </script>
    <script>
        $('body').scroll(function(e){
            console.log('in');
        })
    </script>
@endpush