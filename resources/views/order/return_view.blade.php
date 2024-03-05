@extends('layouts.app')
@section('title', 'Order Cancel - ' . ucfirst($return->order->order_no))

@section('content')

    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                {{-- <h3 class="text-primary">{{ucfirst($type)}} Branch</h3> --}}
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('returns')}}">Order Cancel Requests</a></li>
                    <li class="breadcrumb-item active">View Order Cancel Request</li>
                </ol>
            </div>
        </div>
        @include('layouts.message')
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-lg-12 col-xlg-12 col-md-12">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#profile" role="tab">Details</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#orders" role="tab">Order Items</a> </li>
                        @if($return->status=='PN')
                            <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li>
                        @endif

                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="profile" role="tabpanel">
                            <div class="card-body">
                                <div class="float-right">
                                    <h6><a href="{{ route('viewOrder', ['id' => $return->order->id]) }}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Cancel Request" target="_blank">View Order</i></a></h6>
                                </div>
                                <div>
                                    <div class="col-md-3 columns">
                                        <h6 class="p-t-20">Order No</h6><small class="text-primary font-15 db">{{$return->order->order_no}}</small>
                                    </div>
                                    <div class="col-md-3 columns">
                                        <h6 class="p-t-20">User</h6><small class="text-primary font-15 db">{{$return->order->user->first_name." ".$return->order->user->last_name}}</small>
                                    </div>
                                    <div class="col-md-3 columns">
                                        <h6 class="p-t-20">Item Type</h6><small class="text-primary font-15 db">{{ucfirst($return->type)}}</small>
                                    </div>
                                    <div class="col-md-3 columns">
                                        <h6 class="p-t-20">Amount</h6><small class="text-primary font-15 db">{{Session::get('currency').Helper::format_number($return->amount)}}</small>
                                    </div>

                                    <div class="col-md-3 columns">
                                        <h6 class="p-t-20">Status</h6><small class="text-primary font-15 db">{{ucfirst(config('constants.STATUS.'.$return->status))}}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="orders" role="tabpanel">
                            <div class="card-body">
                                <div class="float-right">
                                    <h6><a href="{{ route('viewOrder', ['id' => $return->order->id]) }}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Cancel Request" target="_blank">View Order</i></a></h6>
                                </div>
                                <div class="table-responsive m-t-40">
                                    <table id="ordersTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Refund Amount (in {{session()->get('currency')}})</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Refund Amount (in {{session()->get('currency')}})</th>
                                                <th>Status</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            @php
                                                $i=1;
                                                $ids = [];
                                                $amount = 0;
                                            @endphp
                                            @foreach($items as $value)
                                                @php
                                                    array_push($ids, $value->id);
                                                    $amount+=$value->amount;
                                                @endphp
                                                <tr>
                                                    <td>{{$i++}}</td>
                                                    <td> - </td>
                                                    <td>{{$value->order_item->qty}}</td>
                                                    <td>{{$value->amount}}</td>
                                                    <td>{{Config::get('constants.STATUS.'.$value->status)}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="settings" role="tabpanel">
                            <div class="card-body">
                                <form class="form-valide form-horizontal form-material" method="post" action="{{route('updateOrderReturn')}}">
                                    {{csrf_field()}}

                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <label class="col-md-6 m-t-10" for="status">Status</label>
                                            <input type="hidden" name="id" value="{{$return->id}}">
                                            <input type="hidden" name="statusid" value="{{implode(',',$ids)}}">
                                            <input type="hidden" name="amount" value="{{$amount}}">
                                            <div class="col-md-6" style="float: right;">
                                                <select class="form-control" name="status" id="status">
                                                    <option value="" @if($return->status=='PN') selected="selected" @endif disabled>Pending</option>
                                                    <option value="AP" @if($return->status=='AP') selected="selected" @endif>Approved</option>
                                                    <option value="RJ" @if($return->status=='RJ') selected="selected" @endif>Rejected</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group m-t-30">
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
        <!-- End PAge Content -->
    </div>
@endsection

@push('scripts')
    <script src="{{URL::asset('/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript">
        $(function(){
            $('#ordersTable').DataTable({

                paging: true,
                pageLength: 10,
                // "bProcessing": true,
                "bLengthChange": false,
                "aaSorting": [],
                "bFilter": false
            });

            $('select[name=status]').rules('add',{
                required:true,
                messages:{
                    required: "Either reject or approve the cancellation request"
                }
            });

        });
    </script>
@endpush
