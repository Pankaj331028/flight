@extends('layouts.app')
@section('title', ucfirst($type).' Product')

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
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Product</li>
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
                            <h4>Fill In Product Details</h4>
                        @elseif($type == 'edit')
                            <h4>Edit Product Details</h4>
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
                            <div class="col-md-12 p-0">
                                <div class="form-group col-md-6 m-t-20 float-left">
                                    <label>Image</label><sup class="text-reddit"> *</sup>
                                    <input type="hidden" name="image_exists" id="image_exists" value="1">

                                    @if($type == 'add' || ($type == 'edit' && $product->image == null))
                                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                            <div class="form-control" data-trigger="fileinput"> <i class="glyphbanner glyphbanner-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span>
                                            <input type="file" name="product_image"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                        </div>
                                    @elseif($type == 'edit')
                                        <br>
                                        <div id="productImage">

                                            <img src="@if($product->image != null && file_exists(public_path('/uploads/products/'.$product->image))){{URL::asset('/uploads/products/'.$product->image)}}@endif" width="70"" />
                                            &nbsp;&nbsp;&nbsp;<a id="changeImage" href="javascript:void(0)" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Delete">Change</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-md-6 m-t-20" id="brandcol" style="display: none;">
                                <label>Brand</label>
                                <select class="form-control" name="brand_id" id="brand_id">
                                    @if(count($brands) > 0)
                                        <option value=''>Select Brand</option>
                                        @foreach($brands as $id=> $brand)
                                            <option value="{{$id}}" @if($product->brand_id==$id) selected @endif>{{$brand}}</option>
                                        @endforeach
                                    @else
                                        <option value=''>No brands found</option>
                                    @endif

                                </select>
                            </div>
                            <div class="form-group col-md-6 m-t-20" id="taxcol" style="display: none;">
                                <label>Tax</label>
                                <select class="form-control" name="tax_id" id="tax_id">
                                    @if(count($taxes) > 0)
                                        <option value=''>Select Tax</option>
                                        <option value='0'>None</option>
                                        @foreach($taxes as $tax)
                                            <option value="{{$tax->id}}" @if($product->tax_id==$tax->id) selected @endif>{{$tax->name."(".$tax->value."%)"}}</option>
                                        @endforeach
                                    @else
                                        <option value=''>No taxes found</option>
                                    @endif

                                </select>
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Category</label><sup class="text-reddit"> *</sup>
                                <select class="form-control" name="category_id" id="category_id">
                                    @php
                                        $category = 0;
                                        if($type=='edit'){
                                            $category = (isset($product->category->id) ? $product->category->id :$product->subcategory->parentCat->id) . ( isset($product->subcategory->id) && ($product->subcategory->id != null) ? '-' . $product->subcategory->id : '');
                                        }
                                        $parents = [];
                                    @endphp
                                    {{-- to disable all the categories which have subcategories --}}
                                    @foreach($categories as $id=>$cat)
                                        @php
                                            $ids = explode('-',$id);
                                            $parents[$ids[0]] = count($ids);
                                        @endphp
                                    @endforeach
                                    @if(count($categories) > 0)
                                        <option value=''>Select Category</option>
                                        @foreach($categories as $id=>$cat)
                                            @php
                                                $ids = explode('-',$id);
                                                $parent = $ids[0];
                                            @endphp
                                            <option value="{{$id}}" @if($category==$id) selected @endif @if($parents[$parent] > 1 && $parent == $id) disabled @else style="color: #000" @endif>{{$cat}}</option>
                                        @endforeach
                                    @else
                                        <option value=''>No categories found</option>
                                    @endif

                                </select>
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Name</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" name="product_name" value="{{old('product_name', $product->name)}}" maxlength="100">
                            </div>
                            <div class="form-group col-md-12 m-t-20">
                                <label>Description</label><sup class="text-reddit"> *</sup>
                                <textarea class="form-control form-control-line check_content" name="prod_description" rows="5">{{old('prod_description', $product->description)}}</textarea>
                            </div>
                            <div class="form-group col-md-6 m-t-20" id="typecol" style="display: none;">
                                <label>Type</label>
                                <select class="form-control" name="product_type" id="product_type" disabled="">

                                    @foreach(config()->get('constants.PRODUCT_TYPE') as $key=>$ptype)
                                        <option value="{{$key}}" @if($product->type==$key || $key=='variable') selected @endif>{{$ptype}}</option>
                                    @endforeach

                                </select>
                            </div>

                            <input type="hidden" name="manage_stock" value="@if(isset($product) && $product->manage_stock != null) {{$product->manage_stock}} @else 1 @endif">
                            <div class="form-group bt-switch col-md-6 m-t-20" style="display: none;">
                                <label class="col-md-4">Manage Stock</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" @if($type == 'edit') @if(isset($product) && $product->manage_stock == '1') checked @endif @endif data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="val-manage_stock" id="manageStock">
                                </div>
                            </div>

                            <input type="hidden" name="quick_grab" value="@if(isset($product) && $product->quick_grab != null) {{$product->quick_grab}} @else 0 @endif">
                            <div class="form-group bt-switch col-md-6 m-t-20">
                                <label class="col-md-4">Quick Grab</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" @if($type == 'edit') @if(isset($product) && $product->quick_grab == '1') checked @endif @endif data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="val-quick_grab" id="quickGrab">
                                </div>
                            </div>

                            <input type="hidden" name="is_exclusive" value="@if(isset($product) && $product->is_exclusive != null) {{$product->is_exclusive}} @else 0 @endif">
                            <div class="form-group bt-switch col-md-6 m-t-20">
                                <label class="col-md-4">Is Exclusive</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" @if($type == 'edit') @if(isset($product) && $product->is_exclusive == '1') checked @endif @endif data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="val-is_exclusive" id="exclusive">
                                </div>
                            </div>

                            <input type="hidden" name="status" value="@if(isset($product) && $product->status != null) {{$product->status}} @else AC @endif">
                            <div class="form-group bt-switch col-md-6 m-t-20">
                                <label class="col-md-4">Status</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" @if($type == 'edit') @if(isset($product) && $product->status == 'AC') checked @endif @else checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-status" id="statusCat">
                                </div>
                            </div>


                            @php
                            $selectedAttribute = $product->prod_attr()->groupBy('attribute_id')->pluck('product_attribute_variations.attribute_id')->toArray();
                            @endphp
                            <div class="form-group col-md-6 m-t-20">
                                <label>Attribute</label><sup class="text-reddit"> * <i class="fa fa-question-circle" title="Cannot delete existing attributes"></i></sup>
                                <select class="form-control attributeSelect" name="attribute_id[]" id="attribute_id" multiple>
                                    @if(isset($attributes))
                                    @if(count($attributes) > 0)
                                        <option value='0'>Select Attribute</option>
                                        @foreach($attributes as $key=>$val)
                                            <option value="{{ $val->id }}" @if(in_array($val->id, $selectedAttribute)) selected @endif>{{ ucfirst($val->name) }}</option>
                                        @endforeach
                                    @else
                                        <option value=''>No attribute found</option>
                                    @endif
                                    @endif
                                </select>
                            </div>



                            <div class="card card-outline-inverse col-12 m-t-20">
                                <div class="card-body">
                                    <h3 class="card-title">Product Variations</h3>
                                    <div class="dt-buttons float-right addVariantButton" @if($type != 'edit') style="display: none; @endif">
                                        <a href="javascript:void(0)" class="btn dt-button py-2 addVariant">Add Variant</a>
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="no_variants" id="no_variants" value="0">
                                    </div>
                                    <div id="variants">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 m-t-20">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                                <a href="{{route('products')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>



    <div id="dummyRow" style="display: none;">
        @if($type == 'edit')
        <div class="variantRow" id="row_1">
            <h6 class="text-danger" id="proderror_1" style="display: none;">Size for this variant cannnot be edited/deleted. Cart/Order has been placed.</h6>
            <div class="float-right">
                <a href="javascript:void(0)" class="deleteRow" id="deleterow_1"><i class="fa fa-trash" aria-hidden="true"></i></a>
            </div><br>
            <input type="hidden" name="prodvarid_1" value="0">
            <div class="row">
                <div class="form-group col-md-4 m-t-20 ">
                    <label>Quantity</label><sup class="text-reddit"> *</sup>
                    <input type="text" class="form-control form-control-line numberInput" name="prodqty_1" value="{{old('prodqty_1')}}" maxlength="10">
                </div>
                <div class="form-group col-md-4 m-t-20 ">
                    <label>Maximum Quantity</label><sup class="text-reddit"> *</sup>
                    <input type="text" class="form-control form-control-line numberInput" name="prodmaxqty_1" value="{{old('prodmaxqty_1')}}" maxlength="10">
                </div>
                <div class="form-group col-md-4 m-t-20 ">
                    <label>MRP</label><sup class="text-reddit"> *</sup>
                    <input type="text" class="form-control form-control-line numberInput" name="prodmrp_1" value="{{old('prodmrp_1')}}" maxlength="10">
                </div>
                <div class="form-group col-md-4 m-t-20 ">
                    <label>Special Price</label>
                    <input type="text" class="form-control form-control-line numberInput" name="prodspecial_1" value="{{old('prodspecial_1')}}" maxlength="10">
                </div>

                <input type="hidden" name="prodstatus_1" value="AC">
                <div class="form-group bt-switch col-md-6 m-t-20 p-0">
                    <label class="col-md-4">Status</label>
                    <div class="col-md-3" style="float: right;">
                        <input type="checkbox" data-on-color="success" checked data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-prodstatus_1" class="prodStatus" id="prodstatus_1">
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>


@endsection

@push('scripts')
<script src="{{URL::asset('/js/jquery-mask-as-number.js')}}"></script>
    <script type="text/javascript">
        $(function(){
            jQuery.validator.addMethod("uniqueVariations", function(value, element, param) {
                var clicked = $(element).attr('name').split('_')[1];
                var allValueArray = $('#variants input[name^=prodweight]').map(function() {
                    var id = $(this).attr('name').split('_')[1];
                    if ($('input[name=prodweight_'+id+']').val() != '' && $('select[name=produnit_'+id+']').val() != ''){
                        return $('input[name=prodweight_'+id+']').val() + " " + $('select[name=produnit_'+id+']').val();
                    }
                }).get();

                //Create array of duplicates if there are any
                var duplicateValueArray = allValueArray.filter(function(element, pos) {
                    if(allValueArray.indexOf(element) != pos){
                        return true;
                    }
                    else{
                        return false;
                    }
                });
                //check length of duplicate array, if any duplicate element found it is stored in duplicateValueArray after filter out.
                if (duplicateValueArray.length != 0){
                    return false;
                }
                else{
                    return true;
                }
            }, "This variation is already added.");


            $(document).on('keyup',".decimalInput, .numberInput",function(e){

                if($(this).val().indexOf('-') >=0){
                    $(this).val($(this).val().replace(/\-/g,''));
                }
            })

            $(".attributeSelect option").on("mousedown", function() {

                var $me = $(this);
                $me.data("was-selected", $me.prop("selected"));
                $(".attributeSelect").data("selected", $(".attributeSelect").find("option:selected"));
            }).on("mouseup", function() {

                var $me = $(this);
                $(".attributeSelect").data("selected").prop("selected", true);
                $me.prop("selected", !$me.data("was-selected"));
                var options = document.getElementById('attribute_id').selectedOptions;
                var values = Array.from(options).map(({ value }) => value);

                $.ajax({
                    type: "post",
                    url: "{{ route('attributeOptionGet') }}",
                    data: {option: values, type: '{{ $type }}', product_id: "{{ isset($product->id) ? $product->id : '' }}"},
                    success: function(res)
                    {
                        $('#variants').empty();
                        $('#variants').html(res.html);
                        $('#dummyRow').html(res.dummy_html);
                        $('#variants input[name=val-prodstatus_1]').bootstrapSwitch();
                        if(values == 0){

                            $('#variants input[name=val-prodstatus_1]').bootstrapSwitch();
                        }else{

                            $('.addVariantButton').css('display', 'block');
                            var row = Number($('#no_variants').val())+1;

                            $('#no_variants').val(1);
                            $('input[name=prodqty_'+row+']').rules('add','required');
                            $('input[name=prodqty_'+row+']').rules('add',{maxlength:10});
                            $('input[name=prodmaxqty_'+row+']').rules('add','required');
                            $('input[name=prodmaxqty_'+row+']').rules('add',{maxlength:10});
                            $('input[name=prodmrp_'+row+']').rules('add','required');
                            $('input[name=prodmrp_'+row+']').rules('add',{maxlength:20});
                            $('input[name=prodspecial_'+row+']').rules('add',{maxlength:20})
                            $('#variants input[name=val-prodstatus_'+row+']').bootstrapSwitch();
                        }
                    },
                    error: function(data)
                    {
                        $('#variants').empty();
                        if(values == 0){
                            $('.addVariantButton').css('display', 'none');
                        }else{
                            $('.addVariantButton').css('display', 'block');
                        }
                    }
                });
            });

            $(document).find(".numberInput").maskAsNumber({receivedMinus:false});
            $(document).find(".decimalInput").maskAsNumber({receivedMinus:false,decimals:6});

            @if($type == 'add')
                addRow();
                $('.deleteRow').hide();
            @else
                var count = 1;

                @foreach($product->variations as $value)
                    addRow();
                    $('#variants').find('input[name=prodvarid_'+count+']').val("{{$value->id}}");
                    $('#variants').find('input[name=prodweight_'+count+']').val("{{$value->weight}}");
                    $('#variants').find('input[name=prodqty_'+count+']').val("{{$value->qty}}");
                    $('#variants').find('input[name=prodmaxqty_'+count+']').val("{{$value->max_qty}}");
                    $('#variants').find('input[name=prodmrp_'+count+']').val("{{$value->price}}");
                    $('#variants').find('input[name=prodspecial_'+count+']').val("{{$value->special_price}}");
                    $('#variants').find('input[name=prodstatus_'+count+']').val("{{$value->status}}");

                    var options = document.getElementById('attribute_id').selectedOptions;
                    var values = Array.from(options).map(({ value }) => value);

                    $.ajax({
                        type: "post",
                        url: "{{ route('attributeOptionGet') }}",
                        data: {option: values, type: '{{ $type }}', product_id: "{{ isset($product->id) ? $product->id : '' }}"},
                        success: function(res)
                        {

                            $('#variants').empty();
                            $('#variants').html(res.html);
                            $('#dummyRow').html(res.dummy_html);

                        },
                        error: function(data)
                        {
                            $('#variants').empty();
                            if(values == 0){
                                $('.addVariantButton').css('display', 'none');
                            }else{
                                $('.addVariantButton').css('display', 'block');
                            }
                        }
                    });

                    var state="{{$value->status}}" == 'AC' ? true:false;

                    $('#variants').find('input[name=val-prodstatus_'+count+']').bootstrapSwitch('state', "{{$value->status}}" == 'AC' ? true:false, false);
                    if(!state){
                        var width = Number($('#variants').find('input[name=val-prodstatus_'+count+']').closest('.bootstrap-switch').css('width').split('px')[0])+10;
                        $('#variants').find('input[name=val-prodstatus_'+count+']').closest('.bootstrap-switch').css('width',width+'px');
                    }
                    $('#variants input[name=val-prodstatus_'+count+']').bootstrapSwitch();

                    //variation cannot be deleted once placed in cart or ordered
                    @if(count($product->variations) > 1)
                        @if(count($value->product_orders) <= 0 && count($value->product_carts) <= 0)
                            $('#variants').find('#deleterow_'+count).show();
                        @else
                            $('#variants').find('#deleterow_'+count).hide();
                            $('#variants').find('input[name=prodvarid_'+count+']').attr('disabled',true);
                            $('#variants').find('input[name=prodweight_'+count+']').attr('readonly',true);
                            $('#variants').find('select[name=produnit_'+count+']').attr('readonly',true);
                            // $('#variants').find('input[name=prodqty_'+count+']').attr('disabled',true);
                            // $('#variants').find('input[name=prodmaxqty_'+count+']').attr('disabled',true);
                            // $('#variants').find('input[name=prodmrp_'+count+']').attr('disabled',true);
                            // $('#variants').find('input[name=prodspecial_'+count+']').attr('disabled',true);
                            $('#variants').find('#proderror_'+count).show();
                        @endif
                    @else
                        $('#variants').find('#deleterow_'+count).hide();
                        @if(count($value->product_orders) > 0 || count($value->product_carts) > 0)
                            // $('#variants').find('input[name=prodvarid_'+count+']').attr('disabled',true);
                            $('#variants').find('input[name=prodweight_'+count+']').attr('readonly',true);
                            $('#variants').find('select[name=produnit_'+count+']').attr('readonly',true);
                            $('#variants').find('input[name=prodqty_'+count+']').attr('disabled',true);
                            $('#variants').find('input[name=prodmaxqty_'+count+']').attr('disabled',true);
                            $('#variants').find('input[name=prodmrp_'+count+']').attr('disabled',true);
                            $('#variants').find('input[name=prodspecial_'+count+']').attr('disabled',true);
                            $('#variants').find('#proderror_'+count).show();
                        @endif
                    @endif

                    //variation'weights and quantity cannot be edited once placed in cart or ordered

                    count++;
                @endforeach
            @endif

            // function addRow(){
            //     var row = Number($('#no_variants').val())+1;
            //     var html = $('#dummyRow').html();
            //     var updated = html.replace(/_0/g, '_'+row);
            //     $('#variants').append(updated);
            //     $('#no_variants').val(row);
            //     $('#variants #row_'+row).find(".numberInput").maskAsNumber({receivedMinus:false});
            //     $('#variants #row_'+row).find(".decimalInput").maskAsNumber({receivedMinus:false,decimals:6});
            //     addRules(row);
            // }


            function addRow(){
                var row = Number($('#no_variants').val())+1;
                // console.log(row);
                var html = $('#dummyRow').html();
                var updated = html.replace(/_1/g, '_'+row);
                $('#variants').append(updated);
                $('#no_variants').val(row);
                $('#variants #row_'+row).find(".numberInput").maskAsNumber({receivedMinus:false});
                $('#variants #row_'+row).find(".decimalInput").maskAsNumber({receivedMinus:false,decimals:6});
                $('#variants #row_'+row).find("select option:selected").removeAttr('selected');
                // $('#variants #row_'+row).find('[name=val-prodstatus_'+row+'] .bootstrap-switch-wrapper').bootstrapSwitch('destroy');
                addRules(row);
            }



            function addRules(id){
                $('input[name=prodweight_'+id+']').rules('add','required');
                $('input[name=prodweight_'+id+']').rules('add','uniqueVariations');
                $('input[name=prodweight_'+id+']').rules('add',{maxlength:10});
                $('select[name=produnit_'+id+']').rules('add','required');
                $('select[name=produnit_'+id+']').rules('add','uniqueVariations');
                $('input[name=prodqty_'+id+']').rules('add','required');
                $('input[name=prodqty_'+id+']').rules('add',{maxlength:10});
                $('input[name=prodmaxqty_'+id+']').rules('add','required');
                $('input[name=prodmaxqty_'+id+']').rules('add',{maxlength:10});
                $('input[name=prodmrp_'+id+']').rules('add','required');
                $('input[name=prodmrp_'+id+']').rules('add',{maxlength:20});
                $('input[name=prodspecial_'+id+']').rules('add',{maxlength:20});
                $('#variants input[name=val-prodstatus_'+id+']').bootstrapSwitch();

            }

            function removeRules(id){
                $('input[name=prodweight_'+id+']').rules('remove','required');
                $('input[name=prodweight_'+id+']').rules('remove','maxlength');
                $('input[name=prodweight_'+id+']').rules('remove','uniqueVariations');
                $('select[name=produnit_'+id+']').rules('remove','required');
                $('select[name=produnit_'+id+']').rules('remove','uniqueVariations');
                $('input[name=prodqty_'+id+']').rules('remove','required');
                $('input[name=prodqty_'+id+']').rules('remove','maxlength');
                $('input[name=prodmaxqty_'+id+']').rules('remove','required');
                $('input[name=prodmaxqty_'+id+']').rules('remove','maxlength');
                $('input[name=prodmrp_'+id+']').rules('remove','required');
                $('input[name=prodmrp_'+id+']').rules('remove','maxlength');
                $('input[name=prodspecial_'+id+']').rules('remove','maxlength');
            }

            $(document).on('input','input[name^=prodmrp]',function(){
                var id = $(this).attr('name').split('_')[1];

                $('input[name=prodspecial_'+id+']').rules('add',{max: function() {
                return parseInt($('input[name=prodmrp_'+id+']').val()); }});
                $('input[name=prodspecial_'+id+']').rules('add',{min:1});
                if($('input[name=prodspecial_'+id+']').val() > 0){
                    $('input[name=prodspecial_'+id+']').valid();
                }
            });

            $(document).on('change','input[name^=prodspecial]',function(){
                var id = $(this).attr('name').split('_')[1];
                if($(this).val() > 0){
                    $('input[name=prodspecial_'+id+']').valid();
                }
            });

            $(document).on('change', '#variants input[name^=prodweight], #variants select[name^=produnit]', function(){
                var close = $(this).closest('.variantRow');
                close.find('input[name^=prodweight]').valid();
                close.find('select[name^=produnit]').valid();
            });

            $(document).on('change', 'select[name=category_id]', function(){
                if($('input[name=product_name]').val()!='')
                    $('input[name=product_name]').valid();
            });

            $(document).on('click','.deleteRow',function(){
                var id = $(this).attr('id').split('_')[1];
                var deleteId = $('input[name=prodvarid_'+id+']').val();

                if(deleteId>0){
                    $.ajax({
                        type: "post",
                        url: "{{route('deleteVariation')}}",
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
                $(this).closest('.variantRow').remove();
                $('#no_variants').val($('#no_variants').val()-1);
                if($('#no_variants').val() == 1)
                    $('.deleteRow').hide();

                var count = 1;
                $('#variants .variantRow').each(function(index,elem){
                    var id = $(this).attr('id').split('_')[1];
                    $(this).attr('id','row_' + count);
                    var prodvarid = $('#variants').find('input[name=prodvarid_'+id+']').val();
                    var prodweight = $('#variants').find('input[name=prodweight_'+id+']').val();
                    var produnit = $('#variants').find('select[name=produnit_'+id+']').val();
                    var prodqty = $('#variants').find('input[name=prodqty_'+id+']').val();
                    var prodmaxqty = $('#variants').find('input[name=prodmaxqty_'+id+']').val();
                    var prodmrp = $('#variants').find('input[name=prodmrp_'+id+']').val();
                    var prodspecial = $('#variants').find('input[name=prodspecial_'+id+']').val();
                    var prodstatus = $('#variants').find('input[name=prodstatus_'+id+']').val();
                    $('#variants input[name=val-prodstatus_'+id+']').bootstrapSwitch('destroy');


                    var replace = "_"+id;
                    var re = new RegExp(replace,"g");
                    var html = $(this).html().replace(re, '_' + count);
                    $(this).html(html);


                    //check this
                    $('#variants').find('input[name=prodvarid_'+count+']').val(prodvarid);
                    $('#variants').find('input[name=prodweight_'+count+']').val(prodweight);
                    $('#variants').find('select[name=produnit_'+count+']').val(produnit);
                    $('#variants').find('input[name=prodqty_'+count+']').val(prodqty);
                    $('#variants').find('input[name=prodmaxqty_'+count+']').val(prodmaxqty);
                    $('#variants').find('input[name=prodmrp_'+count+']').val(prodmrp);
                    $('#variants').find('input[name=prodspecial_'+count+']').val(prodspecial);
                    $('#variants').find('input[name=prodstatus_'+count+']').val(prodstatus);

                    var state= (prodstatus == 'AC') ? true:false;

                    $('#variants').find('input[name=val-prodstatus_'+count+']').bootstrapSwitch('state', state, false);
                    if(!state){
                        var width = Number($('#variants').find('input[name=val-prodstatus_'+count+']').closest('.bootstrap-switch').css('width').split('px')[0])+10;
                        $('#variants').find('input[name=val-prodstatus_'+count+']').closest('.bootstrap-switch').css('width',width+'px');
                    }

                    count++;
                });
            })

            $('.addVariant').click(function(){
                addRow();
                // if existing variant cannot be deleted, then hide delete button
                $('.variantRow').each(function(index,item){
                    if(!$(this).find('h6[id^=proderror]').is(':visible')){
                        $(this).find('.deleteRow').show();
                    }
                })
            })


            $(document).on('switchChange.bootstrapSwitch', 'input[name^=val-prodstatus]', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                var id = $(this).attr('id').split('_')[1];

                if($(this).is(':checked'))
                    $('input[name=prodstatus_'+id+']').val('AC');
                else
                    $('input[name=prodstatus_'+id+']').val('IN');
            });

            $('#statusCat').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusCat").is(':checked'))
                    $('input[name=status]').val('AC');
                else
                    $('input[name=status]').val('IN');
            });

            $('#manageStock').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#manageStock").is(':checked'))
                    $('input[name=manage_stock]').val('1');
                else
                    $('input[name=manage_stock]').val('0');
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

            $('form').data('validator').settings.ignore = ".note-editor *";
            $(document).find('textarea[name=prod_description]').summernote({
                height: 350, // set editor height
                minHeight: null, // set minimum height of editor
                maxHeight: null, // set maximum height of editor
                focus: false, // set focus to editable area after initializing summernote,
                callbacks: {
                    onChange: function(contents, $editable) {
                        $('textarea[name=prod_description]').val($('textarea[name=prod_description]').summernote('isEmpty') ? "" : contents);

                        $('form').data('validator').element($('textarea[name=prod_description]'));
                        $('textarea[name=prod_description]').rules('add','check_content');
                        $('textarea[name=prod_description]').valid();
                    }
                }
            });


            $('#changeImage').click(function(){
                $('#productImage').parent().append('<div class="fileinput fileinput-new input-group" data-provides="fileinput"><div class="form-control" data-trigger="fileinput"> <i class="glyphbanner glyphbanner-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span><input type="file" name="product_image"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a></div>');
                $('.tooltip').tooltip('hide');
                $('#productImage').remove();
                $('#image_exists').val(0);
            });

            $('input[name=no_variants]').rules('add',{min:1,messages:{min:"Please add atleast one variant."}});


// --------------------- DYNAMIC ATTRIBUTE ---------------------------

            function remove(as)
        {
            $('#productImage'+as).parent().append('<div class="fileinput fileinput-new input-group" data-provides="fileinput"><div class="form-control" data-trigger="fileinput"> <i class="glyphbanner glyphbanner-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span><input type="file" name="product_image"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a></div>');
            $('.tooltip').tooltip('hide');
            $('#productImage'+as).remove();
            $('#image_exists'+as).val(0);

        }


        function removeOther(as)
        {
            $('#productImages'+as).parent().append('<div class="fileinput fileinput-new input-group" data-provides="fileinput"><div class="form-control" data-trigger="fileinput"> <i class="glyphbanner glyphbanner-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span><input type="file" name="product_images[]" multiple> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a></div>');
            $('.tooltip').tooltip('hide');
            $('#productImages'+as).remove();
            $('#image_exists'+as).val(0);
        }



         jQuery.validator.addMethod("uniqueVariations", function(value, element, param) {
                var clicked = $(element).attr('name').split('_')[1];
                var allValueArray = $('#variants input[name^=prodweight]').map(function() {
                    var id   = $(this).attr('name').split('_')[1];
                    var name = $(this).attr('name').split('_')[0];
                    // console.log(name);

                    if ($('input[name='+name+'_'+id+']').val() != '' && $('select[name=produnit_'+id+']').val() != ''){
                        return $('input['+name+'_'+id+']').val() + " " + $('select[name=produnit_'+id+']').val();
                    }
                }).get();

                //Create array of duplicates if there are any
                var duplicateValueArray = allValueArray.filter(function(element, pos) {
                    if(allValueArray.indexOf(element) != pos){
                        return true;
                    }
                    else{
                        return false;
                    }
                });
                if (duplicateValueArray.length != 0){
                    return false;
                }
                else{
                    return true;
                }
            }, "This variation is already added.");







        // $(".attributeSelect option").on("mousedown", function() {
        //     var $me = $(this);
        //         $me.data("was-selected", $me.prop("selected"));
        //         $(".attributeSelect").data("selected", $(".attributeSelect").find("option:selected"));
        //     }).on("mouseup", function() {
        //         var $me = $(this);
        //      $(".attributeSelect").data("selected").prop("selected", true);
        //         $me.prop("selected", !$me.data("was-selected"));
        //         var options = document.getElementById('attribute_id').selectedOptions;
        //         var values = Array.from(options).map(({ value }) => value);

        //         $.ajax({
        //         type: "post",
        //         url: "{{ route('attributeOptionGet') }}",
        //         data: {option: values, type: '{{ $type }}', product_id: "{{ isset($product->id) ? $product->id : '' }}"},
        //         success: function(res)
        //         {
        //             $('#dummyRow').empty();
        //             $('#dummyRow').html(res);
        //             $('#variants').empty();
        //             $('#variants').html(res);
        //             $('#variants input[name=val-prodstatus_1]').bootstrapSwitch();
        //             if(values == 0){
        //                 $('.addVariantButton').css('display', 'none');
        //                 $('#variants input[name=val-prodstatus_1]').bootstrapSwitch();
        //             }else{
        //                 $('.addVariantButton').css('display', 'block');
        //                 var row = Number($('#no_variants').val())+1;

        //                 $('#no_variants').val(1);
        //                 $('input[name=prodqty_'+row+']').rules('add','required');
        //                 $('input[name=prodqty_'+row+']').rules('add',{maxlength:10});
        //                 $('input[name=prodmaxqty_'+row+']').rules('add','required');
        //                 $('input[name=prodmaxqty_'+row+']').rules('add',{maxlength:10});
        //                 $('input[name=prodmrp_'+row+']').rules('add','required');
        //                 $('input[name=prodmrp_'+row+']').rules('add',{maxlength:20});
        //                 $('input[name=prodspecial_'+row+']').rules('add',{maxlength:20})
        //                 $('#variants input[name=val-prodstatus_'+row+']').bootstrapSwitch();
        //             }
        //         },
        //         error: function(data)
        //         {
        //             $('#dummyRow').empty();
        //             $('#variants').empty();
        //             if(values == 0){
        //                 $('.addVariantButton').css('display', 'none');
        //             }else{
        //                 $('.addVariantButton').css('display', 'block');
        //             }
        //         }
        //     });
        // });




});
</script>
@endpush
