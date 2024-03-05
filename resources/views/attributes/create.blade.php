@extends('layouts.app')
@section('title', ucfirst($type).' Attribute')

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Branch</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('attributes')}}">Attribute</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Attribute</li>
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
                            <h4>Fill In Attribute Details</h4>
                        @elseif($type == 'edit')
                            <h4>Edit Attribute Details</h4>
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
                                <label>Name</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" name="name" value="{{ old('name', $attribute->name) }}" required="required" maxlength="100" placeholder="Please enter name">
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Type</label><sup class="text-reddit"> *</sup>
                                <select class="form-control" name="type" id="attribute_type" required="required">
                                    <option value="">--</option>
                                   <?php /* <option value="text" {{ ( $attribute->type == 'text') ? 'selected' : '' }}>Text</option>
                                    <option value="textarea" {{ ( $attribute->type == 'textarea') ? 'selected' : '' }}>Text Area</option>
                                    <option value="date" {{ ( $attribute->type == 'date') ? 'selected' : '' }}>Date</option>  */?>
                                    <option value="multiple_select" {{ ( $attribute->type == 'multiple_select') ? 'selected' : '' }}>Multiple Select</option>
                                   <?php /* <option value="dropdown" {{ ( $attribute->type == 'dropdown') ? 'selected' : '' }}>Dropdown</option> */?>
                                </select>
                            </div>
                            @if($type == 'add')
                                <div class="form-group attr-field attr-type-text col-md-6 m-t-20" style="display: none;">
                                  <label>Default Text</label>
                                  <input type="text" name="text" class="form-control" >
                                </div>
                                <div class="form-group attr-field attr-type-textarea col-md-6 m-t-20" style="display: none;">
                                  <label>Default Text Area</label>
                                  <textarea name="textarea" class="form-control"></textarea>
                                </div>
                                <div class="form-group attr-field attr-type-date col-md-6 m-t-20" style="display: none;">
                                  <label>Default Date</label>
                                  <input type="date" name="date" class="form-control" >
                                </div>
                                <div class="form-group attr-field attr-type-dropdown col-md-6 m-t-20" style="display: none;">
                                  <label>Multiple Select</label>
                                  <div class="well py-3 px-3">
                                      <a href="javascript:void(0)" id="add_option">
                                        <i class="fa fa-plus"></i>
                                        Add Option
                                      </a>

                                      <hr>

                                      <div class="options"></div>
                                  </div>
                                </div>
                            @elseif($type == 'edit')
                                @if($attribute->type == 'text')
                                    <div class="form-group attr-field attr-type-text col-md-6 m-t-20" style="display: none;">
                                      <label>Default Text</label>
                                      <input type="text" name="text" class="form-control" >
                                    </div>
                                @endif
                                @if($attribute->type == 'textarea')

                                    <div class="form-group attr-field attr-type-textarea col-md-6 m-t-20" style="display: none;">
                                      <label>Default Text Area</label>
                                      <textarea name="textarea" class="form-control"></textarea>
                                    </div>
                                @endif
                                @if($attribute->type == 'date')
                                    <div class="form-group attr-field attr-type-date col-md-6 m-t-20" style="display: none;">
                                      <label>Default Date</label>
                                      <input type="date" name="date" class="form-control" >
                                    </div>
                                @endif
                                @if(($attribute->type == 'multiple_select') || ($attribute->type == 'dropdown') || ($attribute->type == '0'))
                                    <div class="form-group attr-field attr-type-dropdown col-md-6 m-t-20" style="display: none;">
                                      <label>Multiple Select</label>
                                      <div class="well py-3 px-3">
                                          <a href="javascript:void(0)" id="add_option">
                                            <i class="fa fa-plus"></i>
                                            Add Option
                                          </a>

                                          <hr>

                                          <div class="options">
                                            @if(isset($AttributeVariation) && !empty($AttributeVariation))
                                                @foreach($AttributeVariation as $k=> $val)
                                                    <div class="form-group option">
                                                        <label>Option #{{$k+1}}</label>
                                                        <div class="input-group">
                                                            <input type="text" name="option[{{$k+1}}]" class="form-control" value="{{$val->value}}" placeholder="Name">
                                                            <span class="input-group-addon">
                                                                <a href="javascript:void(0)" class="remove-option"><i class="fa fa-remove"></i>
                                                                </a>
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                          </div>
                                      </div>
                                    </div>
                                @endif
                            @endif
                            
                            
                            <input type="hidden" name="status" value="@if(isset($attribute) && $attribute->status != null) {{$attribute->status}} @else AC @endif">
                            <div class="form-group bt-switch col-md-6 m-t-20">
                                <label class="col-md-4">Status</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" @if($type == 'edit') @if(isset($attribute) && $attribute->status == 'AC') checked @endif @else checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-status" id="statusattribute">
                                </div>
                            </div>
                            <div class="col-12 m-t-20">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                                <a href="{{route('attributes')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
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
            $('#statusattribute').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusattribute").is(':checked'))
                    $('input[name=status]').val('AC');
                else
                    $('input[name=status]').val('IN');
            });
            $(document).find(".decimalInput, .numberInput").on('keyup',function(e){

                if($(this).val().indexOf('-') >=0){
                    $(this).val($(this).val().replace(/\-/g,''));
                }
            })

            $(document).find(".numberInput").maskAsNumber({receivedMinus:false});
            $(document).find(".decimalInput").maskAsNumber({receivedMinus:false,decimals:6});

            @if($type == 'edit')
                $('input[name=name]').rules('add', {remote: APP_NAME + "/admin/attributes/checkAttribute/{{$attribute->id}}"});
            @endif
        });
    </script>

        <script>

            // Reopen attribute specific filed type on redirect back
            $(document).ready(function() {
                var type = $('#attribute_type').val();

                if(type == 'multiple_select') {
                  type = 'dropdown';

                  $('.attr-type-dropdown').children('label').html('Multiple Select');
                } else {
                  $('.attr-type-dropdown').children('label').html('Dropdown');
                }

                $('.attr-type-'+type).show();

                optionNumeration();
            });

            // Option numerotation function
            function optionNumeration() {
              var i = 0;
                $('.option').each(function() {
                  i++;
                  $(this).find('label').html('Option #'+i);
                  $(this).find('input[name^="option"]').attr('name', 'option['+i+']');
                });
            }

            // Add Option
            $(document).on('click', '#add_option', function() {
                // Create option template
                var option_template = '<div class="form-group option">'
                    option_template +=      '<label>#Option</label>'
                    option_template +=      '<div class="input-group">'
                    option_template +=        '<input type="text" name="option[]" class="form-control" placeholder="Name"/>'
                    option_template +=        '<span class="input-group-addon"><a href="javascript:void(0)" class="remove-option"><i class="fa fa-remove"></i></a></span>'
                    option_template +=      '</div>'
                    option_template +=  '</div>'

                // Append option
                $('.options').append(option_template);

                // Numerotate options
                optionNumeration();
            });

            // Remove selected option
            $(document).on('click', '.remove-option', function() {
                $(this).closest('.option').remove();

                // Numerotate options
                optionNumeration();
            });

            // Show / Hide attribute specific field type
            $(document).on('change', '#attribute_type', function () {
                var type = $(this).val();

                $('.attr-field').hide();
                $('.options').empty();

                if(type == 'multiple_select') {
                  type = 'dropdown';

                  $('.attr-type-dropdown').find('label').html('Multiple Select');
                } else {
                  $('.attr-type-dropdown').find('label').html('Dropdown');
                }

                $('.attr-type-'+type).show();

            });
        </script>

@endpush