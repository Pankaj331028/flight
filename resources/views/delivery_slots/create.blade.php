@extends('layouts.app')
@section('title', ucfirst($type).' Delivery Slot')

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Branch</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('deliverySlots')}}">Delivery Slots</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Delivery Slot</li>
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
                            <h4>Fill In Delivery Slot Details</h4>
                        @elseif($type == 'edit')
                            <h4>Edit Delivery Slot Details</h4>
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
                                <label for="role">Type</label><sup class="text-reddit"> *</sup>
                                <select class="form-control unit" name="slot_typenew" id="slot_type">
                                    <option value="">Select Type</option>
                                    @foreach(config('constants.SLOT_TYPE') as $key=>$t)
                                        <option value="{{$key}}" {{ (isset($slot->type)) ? ($slot->type==$key) ? "selected" : "" : "" }}>{{$t}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6 m-t-20" id="dayCol" @if($type=='edit' && $slot->type=='multiple') style="display: none;" @elseif($type=='add') style="display: none;"@endif>
                                <label for="role">Day</label><sup class="text-reddit"> *</sup>
                                <select class="form-control unit" name="slot_day" id="slot_day">
                                    <option value="">Select Day</option>
                                    @foreach(config('constants.WEEK_DAY') as $key=>$t)
                                        <option value="{{$key}}" {{ (isset($slot->day)) ? ($slot->day==$key) ? "selected" : "" : "" }}>{{$t}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                                <label>Start Time</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" id="from_time" name="from_time" value="{{old('from_time', $slot->from_time)}}">
                            </div>

                            <div class="form-group col-md-6 m-t-20">
                                <label>End Time</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" id="to_time" name="to_time" value="{{old('to_time', $slot->to_time)}}">
                            </div>

                            <div class="form-group col-md-6 m-t-20" id="orderCol" @if($type=='edit' && $slot->type=='multiple') style="display: none;" @elseif($type=='add') style="display: none;"@endif>
                                <label>Order Limit</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line numberInput" id="max_order" name="max_order" value="{{old('max_order', $slot->max_order)}}" maxlength="10">
                            </div>

                            <input type="hidden" name="status" value="@if(isset($slot) && $slot->status != null) {{$slot->status}} @else AC @endif">
                            <div class="form-group bt-switch col-md-6 m-t-20">
                                <label class="col-md-4">Status</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" @if($type == 'edit') @if(isset($slot) && $slot->status == 'AC') checked @endif @else checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-status" id="statusCat">
                                </div>
                            </div>
                            <div class="col-12 m-t-20">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                                <a href="{{route('deliverySlots')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
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
            $('#statusCat').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusCat").is(':checked'))
                    $('input[name=status]').val('AC');
                else
                    $('input[name=status]').val('IN');
            });

            @if($type == 'edit')
                $('input[name=from_time]').rules('add', {remote: {
                    url: APP_NAME + "/admin/delivery_slots/checkDeliverySlot/{{$slot->id}}",
                    type: "post",
                    data: {
                      to_time: function() {
                        return $( "#to_time" ).val();
                      },
                      type: function() {
                        return $( "#slot_type" ).val();
                      },
                      day: function() {
                        return $( "#slot_day" ).val();
                      }
                    }
                }});

            @endif

            $(document).on('keyup',".decimalInput, .numberInput",function(e){

                if($(this).val().indexOf('-') >=0){
                    $(this).val($(this).val().replace(/\-/g,''));
                }
            })

            $(document).find(".numberInput").maskAsNumber({receivedMinus:false});
            $(document).find(".decimalInput").maskAsNumber({receivedMinus:false,decimals:6});
            $('#max_order').val("{{$slot->max_order}}");

            $('#from_time').bootstrapMaterialDatePicker({ format: 'HH:mm', time: true, date: false }).on('change', function(e, date){

                $('#to_time').bootstrapMaterialDatePicker('setMinDate', date);
                if(new Date($('#to_time').val()) < new Date($(this).val())){
                    $('#to_time').val('');
                }
                $('#to_time').rules('add',{greaterThanDate: "#from_time"});

                if($('#to_time').val()!='')
                    $('#to_time').valid();
                $('#from_time').valid();
            });

            $('#slot_type').change(function(){
                if($(this).val()=='single'){
                    $('#dayCol').show();
                    $('#orderCol').show();
                    $('#slot_day').rules('add','required');
                    $('#max_order').rules('add','required');
                    $('#max_order').rules('add',{min:1});
                }
                else{
                    $('#dayCol').hide();
                    $('#orderCol').hide();
                    $('#slot_day').rules('remove','required');
                    $('#slot_day').prop('selectedIndex',0);
                    $('#max_order').rules('remove','required');
                    $('#max_order').rules('remove','min');
                    $('#max_order').val(0);
                }

                if($('#from_time').val()!=''){
                    $('#from_time').valid();
                }
                if($('#to_time').val()!=''){
                    $('#to_time').valid();
                }
            })

            $('#slot_day').change(function(){

                if($('#from_time').val()!=''){
                    $('#from_time').valid();
                }
                if($('#to_time').val()!=''){
                    $('#to_time').valid();
                }
            })

            @if($type == 'add')
                $('#to_time').bootstrapMaterialDatePicker({ format: 'HH:mm', time: true, date: false}).on('change', function(e, date){
                    $('#to_time').rules('add',{greaterThanDate: "#from_time"});
                    $('#to_time').valid();
                    $('#from_time').valid();
                });
            @else
                $('#to_time').bootstrapMaterialDatePicker({ format: 'HH:mm', time: true, date: false, minDate:"{{$slot->from_time}}"}).on('change', function(e, date){
                    $('#to_time').rules('add',{greaterThanDate: "#from_time"});
                    $('#to_time').valid();
                    $('#from_time').valid();
                });
            @endif

        });
    </script>
@endpush