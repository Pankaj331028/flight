@extends('layouts.app')
@section('title', ucfirst($type).' Slide/Banner')

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Slider</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('sliders')}}">Sliders/Banners</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Slider/Banner</li>
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
                            <h4>Add Image</h4>
                        @elseif($type == 'edit')
                            <h4>Edit Image</h4>
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
                            <div class="form-group col-md-6 m-t-20">
                                <label>Slide Image</label>
                                <input type="hidden" name="image_exists" id="image_exists" value="1">

                                @if($type == 'add' || ($type == 'edit' && $slider->image == null))
                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                        <div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span>
                                        <input type="file" name="slider_image"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                @elseif($type == 'edit')
                                    <br>
                                    <div id="sliderImage">
                                        <img src="@if($slider->image != null && file_exists(public_path('/uploads/sliders/'.$slider->image))){{URL::asset('/uploads/sliders/'.$slider->image)}}@endif" width="20%" />
                                        &nbsp;&nbsp;&nbsp;<a id="changeImage" href="javascript:void(0)" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Delete">Change</a>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Category <i class="fa fa-question-circle" class="aria-hidden" data-toggle="tooltip" data-placement="top" title="Select category if you want to link this slide to specific category"></i></label>
                                <select class="form-control" name="parent_id" id="parent_id">
                                    @if(count($categories) > 0)
                                        <option value=''>Select Category</option>
                                        @if($categories)
                                            @foreach($categories as $id=> $cat)
                                                <option value="{{$id}}" @if($slider->category_id==$id) selected @endif>{{$cat}}</option>
                                            @endforeach
                                        @endif
                                    @else
                                        <option value=''>No Categories found</option>
                                    @endif

                                </select>
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Type</label>
                                <div class="form-radio form-radio-flat col-md-12 m-auto">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-radio-input" name="type" value="slider" @if(isset($slider) && $slider->type=='slider') checked @elseif($slider->type=='') checked @endif>
                                        Add As Slider Image
                                    </label>
                                </div>
                                <div class="form-radio form-radio-flat col-md-12 m-t-10">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-radio-input" name="type" value="banner" @if(isset($slider) && $slider->type=='banner') checked @endif>
                                        Add As Banner
                                    </label>
                                </div>
                            </div>
                            <input type="hidden" name="banner_exists" value="0">
                            <input type="hidden" name="status" value="@if(isset($slider) && $slider->status != null) {{$slider->status}} @else AC @endif">
                            <div class="form-group bt-switch col-md-6 m-t-20">
                                <label class="col-md-4">Status</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" @if($type == 'edit') @if(isset($slider) && $slider->status == 'AC') checked @endif @else checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-status" id="statusSlider">
                                </div>
                            </div>
							<div class="col-12 m-t-20">
	                            <button type="submit" id="submitBtn" class="btn btn-success waves-effect waves-light m-r-10">Save</button>
	                            <a href="{{route('sliders')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
	                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>

    <div class="modal fade" id="confirmTypeModal" tabindex="-1" role="dialog" aria-labelledby="confirmTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmTypeModalLabel">Confirm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{csrf_field()}}
                    <p class="m-t-10">Below image has already been added as banner. If you'll add new image, this one will be removed.</p>
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
@endsection
@push('scripts')
    <script type="text/javascript">
        $(function(){

            $('#statusSlider').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusSlider").is(':checked'))
                    $('input[name=status]').val('AC');
                else
                    $('input[name=status]').val('IN');
            });

            $('#changeImage').click(function(){
                $('#sliderImage').parent().append('<div class="fileinput fileinput-new input-group" data-provides="fileinput"><div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span><input type="file" name="slider_image"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a></div>');
                $('.tooltip').tooltip('hide');
                $('#sliderImage').remove();
                $('#image_exists').val(0);
            });

            var chance='no';

            $('form').submit(function(e){
                if($('form').valid()){
                    var id = 0;
                    var self = $(this);

                    @if(isset($slider->id))
                        id = "{{$slider->id}}";
                    @endif

                    var type = $('input[name=type]:checked').val();

                    if(type == 'banner' && chance=='no'){
                        e.preventDefault();
                        $.ajax({
                            type:"POST",
                            url:"{{route('checkBanner')}}",
                            data:{id:id},
                            success: function(data){
                                if($.trim(data) != 'no')
                                {
                                    var result = JSON.parse(data);
                                    chance = $.trim(data);
                                    $('input[name=banner_exists]').val(result.id);
                                    $('#previousBanner').attr('src','{{URL::asset('/uploads/sliders/')}}/'+ result.image);
                                    $('#confirmTypeModal').modal('show');
                                }
                                else{
                                    chance='not';
                                    self.submit();
                                }
                            }
                        })
                    }
                }

            });

            $('.confirmBtn').click(function(){
                $('form').submit();
            });

        });
    </script>
@endpush