@extends('layouts.app')
@section('title', ucfirst($type).' Tax')

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Branch</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('categories')}}">Taxes</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Tax</li>
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
                            <h4>Fill In Tax Details</h4>
                        @elseif($type == 'edit')
                            <h4>Edit Tax Details</h4>
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
                                <input type="text" class="form-control form-control-line" name="tax_name" value="{{old('tax_name', $tax->name)}}" maxlength="50">
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Value</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line decimalInput" name="tax_value" value="{{old('tax_value', $tax->value)}}" maxlength="10">
                            </div>

                            <input type="hidden" name="status" value="@if(isset($tax) && $tax->status != null) {{$tax->status}} @else AC @endif">
                            <div class="form-group bt-switch col-md-6 m-t-20">
                                <label class="col-md-4">Status</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" @if($type == 'edit') @if(isset($tax) && $tax->status == 'AC') checked @endif @else checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-status" id="statusTax">
                                </div>
                            </div>
                            <div class="col-12 m-t-20">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                                <a href="{{route('taxes')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
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
            $('#statusTax').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusTax").is(':checked'))
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
                $('input[name=tax_value]').rules('add', {remote: {
                    url: APP_NAME + "/admin/taxes/checkTax/{{$tax->id}}",
                    type: "post",
                    data: {
                      tax_value: function() {
                        return $( "input[name=tax_value]" ).val();
                      }
                    }
                  }});
            @else
                $('input[name=tax_value]').rules('add', {remote: {
                    url: APP_NAME + "/admin/taxes/checkTax",
                    type: "post",
                    data: {
                      tax_value: function() {
                        return $( "input[name=tax_value]" ).val();
                      }
                    }
                  }});
            @endif

        });
    </script>
@endpush