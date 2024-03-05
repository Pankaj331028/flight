@extends('layouts.app')
@section('title', ucfirst($type).' Unit')

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Branch</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('units')}}">Units</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Unit</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.message')
                    <div class="card-body">

                        @if($type == 'edit')
                            <h4>Edit Unit</h4>
                        @elseif($type == 'add')
                            <h4>Add new Unit</h4>
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
                                <label>Unit Abbreviation</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" name="unit_name" value="{{old('unit_name', $unit->unit)}}" maxlength="50">
                            </div>

                            <div class="form-group col-md-6 m-t-20">
                                <label>Unit Fullname</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" name="unit_fullname" value="{{old('unit_fullname', $unit->fullname)}}" maxlength="100">
                            </div>

                            <input type="hidden" name="status" value="@if(isset($unit) && $unit->status != null) {{$unit->status}} @else AC @endif">
                            <div class="form-group bt-switch col-md-6 m-t-20">
                                <label class="col-md-4">Status</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" @if($type == 'edit') @if(isset($unit) && $unit->status == 'AC') checked @endif @else checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-status" id="statusUnit">
                                </div>
                            </div>

                            <div class="col-12 m-t-20">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                                <a href="{{route('units')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
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
            $('#statusUnit').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusUnit").is(':checked'))
                    $('input[name=status]').val('AC');
                else
                    $('input[name=status]').val('IN');
            });
            $(document).find(".decimalInput, .numberInput").on('keyup',function(e){

                if($(this).val().indexOf('-') >=0){
                    $(this).val($(this).val().replace(/\-/g,''));
                }
            })

            @if($type == 'edit')
                $('input[name=unit_name]').rules('add', {remote: APP_NAME + "/admin/units/checkUnit/{{$unit->id}}"});
            @endif


        });
    </script>
@endpush