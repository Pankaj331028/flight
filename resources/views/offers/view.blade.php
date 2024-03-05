@extends('layouts.app')
@section('title', 'Offer - ' . $offer->offer_code)

@section('content')
	<div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-12 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('offers')}}">Offers</a></li>
                    <li class="breadcrumb-item active">Offer</li>
                </ol>
            </div>
        </div>
        <div class="row">
            @include('layouts.message')
            <div class="col-lg-4 col-xlg-3 col-md-5">
                <div class="card">
                    <div class="card-body">
                        <center class="m-t-30"> <img class="card-title" src="@if($offer->image != null){{URL::asset('/uploads/offers/'.$offer->image)}} @endif" width="40%" />
                            <h4 class="m-t-10 m-b-0">{{$offer->offer_code}}</h4>
                        </center>
                    </div>

                </div>
            </div>
            <div class="col-lg-8 col-xlg-9 col-md-7">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#details" role="tab">Details</a> </li>
                        @if (Helper::checkAccess(route('changeStatusOffer')))
                            <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li>
                        @endif
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="details" role="tabpanel">
                            <div class="card-body">
                                <div>
                                    <small class="text-success p-t-30 db">Offer Code</small><h5>{{$offer->offer_code}}</h5>
                                    <small class="text-success p-t-30 db">Start date</small><h5>{{date('Y M, d', strtotime($offer->start_date))}}</h5>
                                    <small class="text-success p-t-30 db">End Date</small><h5>{{date('Y M, d', strtotime($offer->end_date))}}</h5>
                                    <small class="text-success p-t-30 db">Discount Type</small><h5>{{ucfirst($offer->type)}}</h5>
                                    <small class="text-success p-t-30 db">Categories</small>
                                    <div id="nestable">

                                    </div>
                                    @if($offer->type == 'percent')
                                        <small class="text-success p-t-30 db">Max. Discount Amount  (in {{session()->get('currency')}})</small><h5>{{round($offer->max_amount,2)}}</h5>
                                    @endif
                                    <small class="text-success p-t-30 db">Min. Amount  (in {{session()->get('currency')}})</small><h5>{{round($offer->min_amount,2)}}</h5>
                                    <small class="text-success p-t-30 db">Discount Value (in @if($offer->type == 'percent') % @else {{session()->get('currency')}}@endif)</small><h5>{{$offer->value}}</h5>
                                    <small class="text-success p-t-30 db">Created On</small><h5>{{date('Y M, d', strtotime($offer->created_at))}}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="settings" role="tabpanel">
                            <div class="card-body">
                                <form class="form-horizontal form-material" method="post" action="{{route('changeStatusOffer')}}">
                                    {{csrf_field()}}
                                    <div class="form-group bt-switch">
                                        <div class="col-md-6">
                                            <label class="col-md-6" for="val-block">Status</label>
                                            <input type="hidden" name="statusid" value="{{$offer->id}}">
                                            <input type="hidden" name="status" value="{{$offer->status}}">
                                            <div class="col-md-2" style="float: right;">
                                                <input type="checkbox" @if($offer->status == 'AC') checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" id="statusOffer">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group bt-switch">
                                        <div class="col-md-6">
                                            <label class="col-md-6" for="val-block">Top Offer</label>
                                            <input type="hidden" name="top_offer" value="{{$offer->is_top_offer}}">
                                            <div class="col-md-2" style="float: right;">
                                                <input type="checkbox" @if($offer->is_top_offer == '1') checked @endif data-on-color="success" data-off-color="info" data-on-text="Yes" data-off-text="No" data-size="mini" name="topOffer" id="topOffer">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-success">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function(){

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
            var categories = JSON.parse(("{{json_encode($categories)}}").replace(/&quot;/g,'"'));

            var list = '';

            $.each(categories,function(index,item){
                list += item+"<br>";
            })

            $('#nestable').html(list);
        });
    </script>
@endpush
