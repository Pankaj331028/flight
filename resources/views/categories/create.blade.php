@extends('layouts.app')
@section('title', ucfirst($type).' Category')

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Branch</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('categories')}}">Categories</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Category</li>
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
                            <h4>Fill In Category Details</h4>
                        @elseif($type == 'edit')
                            <h4>Edit Category Details</h4>
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

                                    @if($type == 'add' || ($type == 'edit' && $category->image == null))
                                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                            <div class="form-control" data-trigger="fileinput"> <i class="glyphbanner glyphbanner-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span>
                                            <input type="file" name="cat_image"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                        </div>
                                    @elseif($type == 'edit')
                                        <br>
                                        <div id="catImage">

                                            <img src="@if($category->image != null && file_exists(public_path('/uploads/categories/'.$category->image))){{URL::asset('/uploads/categories/'.$category->image)}}@endif" width="70"" />
                                            &nbsp;&nbsp;&nbsp;<a id="changeImage" href="javascript:void(0)" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Delete">Change</a>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-6 m-t-20">
                                    <label>Banner</label><sup class="text-reddit"> *</sup>
                                    <input type="hidden" name="banner_exists" id="banner_exists" value="1">

                                    @if($type == 'add' || ($type == 'edit' && $category->banner == null))
                                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                            <div class="form-control" data-trigger="fileinput"> <i class="glyphbanner glyphbanner-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span>
                                            <input type="file" name="cat_banner"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                        </div>
                                    @elseif($type == 'edit')
                                        <br>
                                        <div id="catBanner">

                                            <img src="@if($category->banner != null && file_exists(public_path('/uploads/categories/'.$category->banner))){{URL::asset('/uploads/categories/'.$category->banner)}}@endif" width="70"" />
                                            &nbsp;&nbsp;&nbsp;<a id="changeBanner" href="javascript:void(0)" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Delete">Change</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Name</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" name="cat_name" value="{{old('cat_name', $category->name)}}" maxlength="100">
                            </div>
                            <div class="form-group col-md-6 m-t-20" id="parentcol">
                                <label>Parent Category <i class="fa fa-question-circle" class="aria-hidden" data-toggle="tooltip" data-placement="top" title="You may leave this blank to make your current category as root category"></i></label>
                                <select class="form-control" name="parent_id" id="parent_id" @if($type=='edit' && $category->parent_id==null) disabled @endif>
                                    @if(count($categories) > 0)
                                        <option value=''>Select Root Category</option>
                                        @if($categories)
                                            @foreach($categories as $id=> $cat)
                                                <option value="{{$id}}" @if($category->parent_id==$id) selected @endif>{{$cat}}</option>
                                            @endforeach
                                        @endif
                                    @else
                                        <option value=''>No Categories found</option>
                                    @endif

                                </select>
                            </div>
                            <div class="form-group col-md-12 m-t-20">
                                <label>Description</label><sup class="text-reddit"> *</sup>
                                <textarea class="form-control form-control-line" name="description" rows="5">{{old('description', $category->description)}}</textarea>
                            </div>
                            @if(count($offers) > 0)
                                <div class="form-group col-md-6 m-t-20" id="parentcol">
                                    <label>Offer <i class="fa fa-question-circle" class="aria-hidden" data-toggle="tooltip" data-placement="top" title="Select offer if you want to apply to this category"></i></label>
                                    <select class="form-control" name="offer" id="offer">
                                        <option value=''>Select Offer</option>
                                        @if($offers)
                                            @foreach($offers as $offer)
                                                <option value="{{$offer->id}}" @if($category->offer==$offer->id) selected @endif>{{$offer->offer_code." (".$offer->value."%) (".date('d M, Y',strtotime($offer->start_date))." - ".date('d M, Y',strtotime($offer->end_date)).")"}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if($type=='edit' && count($category->childCat) > 0)
                                        <div class="form-check form-check-flat selectOption">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" name="subcat_overwrite">
                                                <i class="input-helper"></i>Replace offer within all subcategories with this offer
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if($type=='edit')
                                <input type="hidden" name="category_offer" value="{{$category->offer}}">
                            @endif

                            <input type="hidden" name="status" value="@if(isset($category) && $category->status != null) {{$category->status}} @else AC @endif">
                            <div class="form-group bt-switch col-md-6 m-t-20">
                                <label class="col-md-4">Status</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" @if($type == 'edit') @if(isset($category) && $category->status == 'AC') checked @endif @else checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-status" id="statusCat">
                                </div>
                            </div>
                            <div class="col-12 m-t-20">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                                <a href="{{route('categories')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm</h5>
                    {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> --}}
                </div>
                <div class="modal-body">
                    <h5 class="m-t-10 text-danger changeOffer">Are you sure you want to current offer to new offer? Category will be removed from the current offer.</h5>
                    <button type="button" class="btn btn-secondary btn-flat cancelBtn m-b-30 m-t-30" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-info btn-flat confirmBtn m-b-30 m-t-30">Confirm</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{URL::asset('/js/jquery-mask-as-number.js')}}"></script>
    <script type="text/javascript">
        $(function(){
            $('#statusCat').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusCat").is(':checked'))
                    $('input[name=status]').val('AC');
                else
                    $('input[name=status]').val('IN');
            });
            // $('#featuredCat').on('switchChange.bootstrapSwitch', function (event, state) {
            //     var x = $(this).data('on-text');
            //     var y = $(this).data('off-text');
            //     if($("#featuredCat").is(':checked'))
            //         $('input[name=featured]').val('1');
            //     else
            //         $('input[name=featured]').val('0');
            // });
            $(document).find(".decimalInput, .numberInput").on('keyup',function(e){

                if($(this).val().indexOf('-') >=0){
                    $(this).val($(this).val().replace(/\-/g,''));
                }
            })

            $('#changeImage').click(function(){
                $('#catImage').parent().append('<div class="fileinput fileinput-new input-group" data-provides="fileinput"><div class="form-control" data-trigger="fileinput"> <i class="glyphbanner glyphbanner-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span><input type="file" name="cat_image"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a></div>');
                $('.tooltip').tooltip('hide');
                $('#catImage').remove();
                $('#image_exists').val(0);
            });

            $('#changeBanner').click(function(){
                $('#catBanner').parent().append('<div class="fileinput fileinput-new input-group" data-provides="fileinput"><div class="form-control" data-trigger="fileinput"> <i class="glyphbanner glyphbanner-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span><input type="file" name="cat_banner"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a></div>');
                $('.tooltip').tooltip('hide');
                $('#catBanner').remove();
                $('#banner_exists').val(0);
            });

            $(document).find(".numberInput").maskAsNumber({receivedMinus:false});
            $(document).find(".decimalInput").maskAsNumber({receivedMinus:false,decimals:6});

            @if($type == 'edit')
                $('input[name=cat_name]').rules('add', {remote: {
                    url: APP_NAME + "/admin/categories/checkCategory/{{$category->id}}",
                    type: "post",
                    data: {
                      parent: function() {
                        return $( "#parent_id" ).val();
                      }
                    }
                  }});

                $('#offer').change(function(){
                    var original = "{{$category->offer}}";
                    if(original!=''){
                        if($(this).val()==''){
                            $('.changeOffer').text('Are you sure you want to remove the offer from this category?');
                            $('#confirmDeleteModal').modal('show');
                        }
                        else if(original!=$(this).val()){
                            $('.changeOffer').text('Are you sure you want to current offer to new offer? Category will be removed from the current offer.');
                            $('#confirmDeleteModal').modal('show');
                        }
                    }
                });
            @else
                $('input[name=cat_name]').rules('add', {remote: {
                    url: APP_NAME + "/admin/categories/checkCategory",
                    type: "post",
                    data: {
                      parent: function() {
                        return $( "#parent_id" ).val();
                      }
                    }
                  }});
            @endif

            $('#parent_id').change(function(){
                var parent=$(this).val();
                if(parent==''){
                    $('#offer').val('');
                }
                else{
                    $.ajax({
                        type: "POST",
                        url: "{{route('fetchCategoryOffer')}}",
                        data: {parent:parent},
                        success: function(data){
                            $('#offer').val(data);
                        }
                    });
                }
            })


            $('.confirmBtn').click(function(){
                $('#confirmDeleteModal').modal('hide');
            });

            $('.cancelBtn').click(function(){
                $('#offer').val("{{$category->offer}}");
                $('#confirmDeleteModal').modal('hide');
            });

            $('#parent_id').change(function(){
                $('input[name=cat_name]').valid();
            })

        });
    </script>
@endpush