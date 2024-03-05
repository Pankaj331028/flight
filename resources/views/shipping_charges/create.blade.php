@extends('layouts.app')
@section('title', ucfirst($type).' Shipping Charge')

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Branch</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('shippingCharges')}}">Shipping Charges</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Shipping Charge</li>
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
                            <h4>Fill In Shipping Charge Details</h4>
                        @elseif($type == 'edit')
                            <h4>Edit Shipping Charge Details</h4>
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
                            <div class="form-group col-md-4 m-t-20">
                                <label>Range - Minimum Amount (in {{session()->get('currency')}})</label><sup class="text-reddit"> *</sup>
                                <input type="text" name="min_price" class="form-control numberInput" id="min_price" placeholder="Enter Minimum Amount" data-mask-as-number-min="1" maxlength="10">
                            </div>
                            <div class="form-group col-md-4 m-t-20 val-maxamt">
                                <label>Range - Maximum Amount (in {{session()->get('currency')}})</label><sup class="text-reddit"> *</sup>
                                <input type="text" name="max_price" class="form-control numberInput" id="max_price" placeholder="Enter Maximum Amount" data-mask-as-number-min="1" maxlength="10">
                            </div>
                            <div class="form-group col-md-4 m-t-20">
                                <label>Shipping Charge (in {{session()->get('currency')}})</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control numberInput" name="shipping_charge" placeholder="Enter Shipping Charge" data-mask-as-number-min="1" maxlength="10">
                            </div>
                            <input type="hidden" name="status" value="@if(isset($charge) && $charge->status != null) {{$charge->status}} @else AC @endif">
                            <div class="form-group bt-switch col-md-6 m-t-20">
                                <label class="col-md-4">Status</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" @if($type == 'edit') @if(isset($charge) && $charge->status == 'AC') checked @endif @else checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-status" id="statusCat">
                                </div>
                            </div>

                            <div class="col-12 m-t-20">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                                <a href="{{route('shippingCharges')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
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
            $(document).on('keyup',".decimalInput, .numberInput",function(e){

                if($(this).val().indexOf('-') >0){
                    $(this).val($(this).val().replace(/\-/g,''));
                }
            })



            $(document).find(".numberInput").maskAsNumber({receivedMinus:false});
            $(document).find(".decimalInput").maskAsNumber({receivedMinus:false,decimals:6});

            $('input[name=max_price]').val("{{$charge->max_price}}");
            $('input[name=min_price]').val("{{$charge->min_price}}");
            $('input[name=shipping_charge]').val("{{$charge->shipping_charge}}");

            $('#statusCat').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusCat").is(':checked'))
                    $('input[name=status]').val('AC');
                else
                    $('input[name=status]').val('IN');
            });

            @if($type == 'edit')
                $('input[name=min_price]').rules('add', {remote: {
                    url: APP_NAME + "/admin/shippingCharges/checkShippingCharge/{{$charge->id}}",
                    type: "post",
                    data: {
                      max_price: function() {
                        return $( "#max_price" ).val();
                      },
                    }
                }});

            @endif

            $(document).on('change','input[name=min_price]',function(){

                $('input[name=max_price]').rules('add',{min: function() {
                return parseFloat($('input[name=min_price]').val()); }});
                if($('input[name=max_price]').val() > 0){
                    $('input[name=max_price]').valid();
                }
                if($('input[name=min_price]').val() > 0){
                    $('input[name=min_price]').valid();
                }
            });

            $(document).on('change','input[name=max_price]',function(){

                $('input[name=min_price]').rules('add',{max: function() {
                return parseFloat($('input[name=max_price]').val()); }});
                if($(this).val() > 0){
                    $('input[name=max_price]').valid();
                }
                if($('input[name=min_price]').val() > 0){
                    $('input[name=min_price]').valid();
                }
            });

            $(document).on('change', 'input[name=min_price], #ranges input[name=max_price]', function(){
                if($('input[name=max_price]').val() > 0){
                    $('input[name=max_price]').valid();
                }
                if($('input[name=min_price]').val() > 0){
                    $('input[name=min_price]').valid();
                }
            });

        });
    </script>
@endpush