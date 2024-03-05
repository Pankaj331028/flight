@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    @if(Auth::guard('admin')->user()->user_role[0]->role!='user')
	<div class="content-wrapper">
        <div class="row">
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card sc-card">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class=" fa fa-user text-danger icon-lg"></i>
                            </div>
                            <div class="float-right">
                                <p class="mb-0 text-right">Total Users</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$users}}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="text-muted mt-3 mb-0">
                            <i class="mdi mdi-alert-octagon mr-1" aria-hidden="true"></i> 65% lower growth
                        </p> --}}
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card sc-card">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class=" fa fa-user text-danger icon-lg"></i>
                            </div>
                            <div class="float-right">
                                <p class="mb-0 text-right">Total Drivers</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$drivers}}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="text-muted mt-3 mb-0">
                            <i class="mdi mdi-alert-octagon mr-1" aria-hidden="true"></i> 65% lower growth
                        </p> --}}
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card sc-card">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-product-hunt text-warning icon-lg"></i>
                            </div>
                            <div class="float-right">
                                <p class="mb-0 text-right">Total Products</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$products}}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="text-muted mt-3 mb-0">
                            <i class="mdi mdi-bookmark-outline mr-1" aria-hidden="true"></i> Product-wise sales
                        </p> --}}
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card sc-card">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-shopping-bag text-warning icon-lg"></i>
                            </div>
                            <div class="float-right" style="width:70%">
                                <p class="mb-0 text-right">Total Orders</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$orders}}</h3>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card sc-card">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-shopping-bag text-warning icon-lg"></i>
                            </div>
                            <div class="float-right" style="width:70%">
                                <p class="mb-0 text-right">Upcoming Deliveries</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$deliveries}}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="text-muted mt-3 mb-0">
                            <i class="mdi mdi-bookmark-outline mr-1" aria-hidden="true"></i> Product-wise sales
                        </p> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Orders</h4>
                        <div class="row">
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="display_mode">Display Mode</label>
                                    <select class="form-control" id="barmodeFilter">
                                        <option value="year">Yearly</option>
                                        <option value="month">Monthly</option>
                                        <option value="days">Days</option>
                                    </select>
                                </div>
                            </div>
                            <div id="barmonthFilter" class="col-lg-3 col-md-4" style="display: none;">
                                <div class="col-lg-12 col-md-12 float-left">
                                    <div class="form-group">
                                        <label for="baryearSelect">Year</label>
                                        <select class="form-control" id="baryearSelect">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="bardayFilter" class="col-lg-6 col-md-6" style="display: none;">
                                <div class="col-lg-6 col-md-6 float-left">
                                    <div class="form-group">
                                        <label for="barmodefrom_date">From Date</label>
                                        <input type="text" id="barmodefrom_date" name="barmodefrom_date" class="form-control" placeholder="Enter from date">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 float-left">
                                    <div class="form-group">
                                        <label for="barmodeend_date">End Date</label>
                                        <input type="text" id="barmodeend_date" name="barmodeend_date" class="form-control" placeholder="Enter end date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-12 d-flex align-items-center">
                                <div class="form-group flex-column flex-md-row">
                                    <input type="button" id="bargraphFilter" class="btn btn-success mt-2" value="Filter">
                                </div>
                            </div>
                        </div>
                        <canvas id="barChart" style="height:250px"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                <h4 class="card-title">Coupon Code Usage</h4>
                <table id="usersTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Coupon Code</th>
                            <th>Usage</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Coupon Code</th>
                            <th>Usage</th>
                        </tr>
                    </tfoot>
                    <tbody>
                    	@foreach($codes as $code)
							<tr>
								<td>{{$code->code}}</td>
								<td>{{$code->total}}</td>
							</tr>
                    	@endforeach
                    </tbody>
                </table>
                </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Payment Modes</h4>
                        <div class="row">
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="display_mode">Display Mode</label>
                                    <select class="form-control" id="paymentmodeFilter">
                                        <option value="year">Yearly</option>
                                        <option value="month">Monthly</option>
                                        <option value="days">Days</option>
                                    </select>
                                </div>
                            </div>
                            <div id="paymentmonthFilter" class="col-lg-3 col-md-4" style="display: none;">
                                <div class="col-lg-12 col-md-12 float-left">
                                    <div class="form-group">
                                        <label for="yearSelect">Year</label>
                                        <select class="form-control" id="paymentyearSelect">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="paymentdayFilter" class="col-lg-6 col-md-6" style="display: none;">
                                <div class="col-lg-6 col-md-6 float-left">
                                    <div class="form-group">
                                        <label for="modefrom_date">From Date</label>
                                        <input type="text" id="paymentmodefrom_date" name="paymentmodefrom_date" class="form-control" placeholder="Enter from date">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 float-left">
                                    <div class="form-group">
                                        <label for="modeend_date">End Date</label>
                                        <input type="text" id="paymentmodeend_date" name="paymentmodeend_date" class="form-control" placeholder="Enter end date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-12 d-flex align-items-center">
                                <div class="form-group flex-column flex-md-row">
                                    <input type="button" id="paymentgraphFilter" class="btn btn-success mt-2" value="Filter">
                                </div>
                            </div>
                        </div>
                        <canvas id="paymentlineChart" style="height:250px"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Order Cancellation Requests</h4>
                        <div class="row">
                            <div class="col-lg-3 col-md-4">
                                <div class="form-group">
                                    <label for="display_mode">Display Mode</label>
                                    <select class="form-control" id="cancelmodeFilter">
                                        <option value="year">Yearly</option>
                                        <option value="month">Monthly</option>
                                        <option value="days">Days</option>
                                    </select>
                                </div>
                            </div>
                            <div id="cancelmonthFilter" class="col-lg-3 col-md-4" style="display: none;">
                                <div class="col-lg-12 col-md-12 float-left">
                                    <div class="form-group">
                                        <label for="cancelyearSelect">Year</label>
                                        <select class="form-control" id="cancelyearSelect">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="canceldayFilter" class="col-lg-6 col-md-6" style="display: none;">
                                <div class="col-lg-6 col-md-6 float-left">
                                    <div class="form-group">
                                        <label for="modefrom_date">From Date</label>
                                        <input type="text" id="cancelmodefrom_date" name="cancelmodefrom_date" class="form-control" placeholder="Enter from date">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 float-left">
                                    <div class="form-group">
                                        <label for="modeend_date">End Date</label>
                                        <input type="text" id="cancelmodeend_date" name="cancelmodeend_date" class="form-control" placeholder="Enter end date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-12 d-flex align-items-center">
                                <div class="form-group flex-column flex-md-row">
                                    <input type="button" id="cancelgraphFilter" class="btn btn-success mt-2" value="Filter">
                                </div>
                            </div>
                        </div>
                        <canvas id="cancellineChart" style="height:250px"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="content-wrapper">
        <div class="row">
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card sc-card">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-shopping-bag text-warning icon-lg"></i>
                            </div>
                            <div class="float-right" style="width:70%">
                                <p class="mb-0 text-right">Total Orders</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$orders}}</h3>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
@push('scripts')
    <script src="{{URL::asset('/js/dashboard.js')}}"></script>
    <script type="text/javascript">
        @if(Auth::guard('admin')->user()->user_role[0]->role!='user')
        $(document).ready(function(){

            var lineChart='';
            var cancellineChart='';
            var barlineChart='';

            callPaymentGraphData('year',[]);
            callCancelGraphData('year',[]);
            callBarGraphData('year',[]);
            $('#paymentmodeFilter').change(function(){
                if($(this).val() == 'month'){
                    $('#paymentmonthFilter').show();
                    $('#paymentdayFilter').hide();
                }
                else if($(this).val() == 'days'){
                    $('#paymentmonthFilter').hide();
                    $('#paymentdayFilter').show();
                }
                else{
                    $('#paymentmonthFilter').hide();
                    $('#paymentdayFilter').hide();
                }
            })
            $('#cancelmodeFilter').change(function(){
                if($(this).val() == 'month'){
                    $('#cancelmonthFilter').show();
                    $('#canceldayFilter').hide();
                }
                else if($(this).val() == 'days'){
                    $('#cancelmonthFilter').hide();
                    $('#canceldayFilter').show();
                }
                else{
                    $('#cancelmonthFilter').hide();
                    $('#canceldayFilter').hide();
                }
            })

            $('#paymentgraphFilter').click(function(){
                var mode = $('#paymentmodeFilter').val();
                var pfilter_data=[];

                if(mode == 'month')
                {
                    pfilter_data=$('#paymentyearSelect').val();
                }

                if(mode == 'days')
                {
                    pfilter_data=$('#paymentmodefrom_date').val();
                    pfilter_data+='&'+$('#paymentmodeend_date').val();
                }
                // console.log(filter_data);
                callPaymentGraphData(mode,pfilter_data);
            });

            $('#cancelgraphFilter').click(function(){
                var mode = $('#cancelmodeFilter').val();
                var cfilter_data=[];

                if(mode == 'month')
                {
                    cfilter_data=$('#cancelyearSelect').val();
                }

                if(mode == 'days')
                {
                    cfilter_data=$('#cancelmodefrom_date').val();
                    cfilter_data+='&'+$('#cancelmodeend_date').val();
                }
                // console.log(filter_data);
                callCancelGraphData(mode,cfilter_data);
            });

            $('#barmodeFilter').change(function(){
                if($(this).val() == 'month'){
                    $('#barmonthFilter').show();
                    $('#bardayFilter').hide();
                }
                else if($(this).val() == 'days'){
                    $('#barmonthFilter').hide();
                    $('#bardayFilter').show();
                }
                else{
                    $('#barmonthFilter').hide();
                    $('#bardayFilter').hide();
                }
            })

            $('#bargraphFilter').click(function(){
                var mode = $('#barmodeFilter').val();
                var filter_data=[];

                if(mode == 'month')
                {
                    filter_data=$('#baryearSelect').val();
                }

                if(mode == 'days')
                {
                    filter_data=$('#barmodefrom_date').val();
                    filter_data+='&'+$('#barmodeend_date').val();
                }
                // console.log(filter_data);
                callBarGraphData(mode,filter_data);
            });

            function callPaymentGraphData(mode,filter_data){
                console.log(filter_data);

                $.ajax({
                    type:"POST",
                    url:"{{route('getPaymentGraphData')}}",
                    data:{mode:mode,filter_data:filter_data},
                    success:function(data){
                        console.log(data);
                        var result = JSON.parse(data);
                        createpaymentgraph(result,mode);
                    }
                })
            }

            function callCancelGraphData(mode,filter_data){
                console.log(filter_data);

                $.ajax({
                    type:"POST",
                    url:"{{route('getCancelGraphData')}}",
                    data:{mode:mode,filter_data:filter_data},
                    success:function(data){
                        console.log(data);
                        var result = JSON.parse(data);
                        createcancelgraph(result,mode);
                    }
                })
            }

            function callBarGraphData(mode,filter_data){
                console.log(filter_data);
                $.ajax({
                    type:"POST",
                    url:"{{route('getBarGraphData')}}",
                    data:{mode:mode,filter_data:filter_data},
                    success:function(data){
                        console.log(data);
                        var result = JSON.parse(data);
                        createbargraph(result,mode);
                    }
                })
            }

            function createpaymentgraph(data,mode){
                var labels = [];
                var codtemp = [];
                var onlinetemp = [];
                var wallettemp = [];
                var cod = [];
                var online = [];
                var wallet = [];
                var yearhtml = '';

                if(mode == 'year'){
                    for(var i = 0; i < data.length; i++){
                        if($.inArray(data[i].year, labels) < 0){
                            labels.push(data[i].year);
                        }
                        if(data[i].payment_method == 'cod'){
                            codtemp["key"+data[i].year] = data[i].total;
                        }else if(data[i].payment_method == 'online'){
                            onlinetemp["key"+data[i].year] = data[i].total;
                        }else{
                            wallettemp["key"+data[i].year]=data[i].total;
                        }

                    }

                    if(labels.length <= 1)
                        labels.unshift("0");

                    for(var j = 0; j < labels.length; j++){
                        if(labels[j]!="0"){
                            yearhtml = "<option value='"+labels[j]+"'>"+labels[j]+"</option>";
                        }

                        if(!("key"+labels[j] in codtemp)){
                            codtemp["key"+labels[j]] = 0;
                        }
                        if(!("key"+labels[j] in onlinetemp)){
                            onlinetemp["key"+labels[j]]=0;
                        }
                        if(!("key"+labels[j] in wallettemp)){
                            wallettemp["key"+labels[j]]=0;
                        }
                        cod.push(codtemp["key"+labels[j]]);
                        online.push(onlinetemp["key"+labels[j]]);
                        wallet.push(wallettemp["key"+labels[j]]);

                    }
                    $('#paymentyearSelect').html(yearhtml);
                    $('#paymentyearSelect').val(labels[1]);
                }

                else if(mode == 'month'){
                    for(var i = 0; i < data.length; i++){
                        if($.inArray(data[i].month, labels) < 0){
                            labels.push(data[i].month);
                        }
                        if(data[i].payment_method == 'cod'){
                            codtemp["key"+data[i].month] = data[i].total;
                        }
                        else if(data[i].payment_method == 'online'){
                            onlinetemp["key"+data[i].month] = data[i].total;
                        }else{
                            wallettemp["key"+data[i].month]=data[i].total;
                        }

                    }

                    if(labels.length <= 1)
                        labels.unshift("0");

                    for(var j = 0; j < labels.length; j++){

                        if(!("key"+labels[j] in onlinetemp)){
                            onlinetemp["key"+labels[j]] = 0;
                        }
                        if(!("key"+labels[j] in wallettemp)){
                            wallettemp["key"+labels[j]]=0;
                        }
                        if(!("key"+labels[j] in codtemp)){
                            codtemp["key"+labels[j]]=0;
                        }
                        cod.push(codtemp["key"+labels[j]]);
                        online.push(onlinetemp["key"+labels[j]]);
                        wallet.push(wallettemp["key"+labels[j]]);

                    }
                }
                else if(mode == 'days'){
                    for(var i = 0; i < data.length; i++){
                        if($.inArray(data[i].date, labels) < 0){
                            labels.push(data[i].date);
                        }
                        if(data[i].payment_method == 'cod'){
                            codtemp["key"+data[i].date] = data[i].total;
                        }
                        else if(data[i].payment_method == 'online'){
                            onlinetemp["key"+data[i].date] = data[i].total;
                        }else{
                            wallettemp["key"+data[i].date]=data[i].total;
                        }

                    }

                    if(labels.length <= 1)
                        labels.unshift("0");

                    for(var j = 0; j < labels.length; j++){

                        if(!("key"+labels[j] in onlinetemp)){
                            onlinetemp["key"+labels[j]] = 0;
                        }
                        if(!("key"+labels[j] in wallettemp)){
                            wallettemp["key"+labels[j]]=0;
                        }
                        if(!("key"+labels[j] in codtemp)){
                            codtemp["key"+labels[j]]=0;
                        }
                        cod.push(codtemp["key"+labels[j]]);
                        wallet.push(wallettemp["key"+labels[j]]);
                        online.push(onlinetemp["key"+labels[j]]);

                    }
                }
                var data = {
                    labels: labels,
                    datasets: [{
                        label: 'Cash On Delivery',
                        data: cod,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(0,0,0,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1,
                    },{
                        label: 'Online Payment',
                        data: online,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255,99,132,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1,

                    },{
                        label: 'Credit Wallet',
                        data: wallet,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255,99,132,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1,

                    }]
                };

                var options = {
                    plugins: {
                        filler: {
                            propagate: true
                        }
                    },
                    tooltips: {
                        enabled: true,
                        mode: 'single',
                        callbacks: {
                            label: function(tooltipItems, data) {
                                return data.datasets[tooltipItems.datasetIndex].label+': '+"{{session()->get('currency')}}"+' '+tooltipItems.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                            }
                        }
                    },
                    scales: {
                        yAxes: [
                            {
                                ticks: {
                                    callback: function(label, index, labels) {
                                        return ''+"{{session()->get('currency')}}"+' '+label.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                    }
                                }
                            }
                        ]
                    }
                };

                if(lineChart)
                    lineChart.destroy();

                var lineChartCanvas = $("#paymentlineChart").get(0).getContext("2d");
                var lineChart = new Chart(lineChartCanvas, {
                    type: 'line',
                    data: data,
                    options: options
                });
            }

            function createcancelgraph(data,mode){
                var labels = [];
                var ptemp = [];
                var rtemp = [];
                var atemp = [];
                var p = [];
                var r = [];
                var a = [];
                var yearhtml = '';

                if(mode == 'year'){
                    for(var i = 0; i < data.length; i++){
                        if($.inArray(data[i].year, labels) < 0){
                            labels.push(data[i].year);
                        }
                        if(data[i].status == 'PN'){
                            ptemp["key"+data[i].year] = data[i].total;
                        }else if(data[i].status == 'RJ'){
                            rtemp["key"+data[i].year] = data[i].total;
                        }else if(data[i].status == 'AP'){
                            atemp["key"+data[i].year]=data[i].total;
                        }

                    }

                    if(labels.length <= 1)
                        labels.unshift("0");

                    for(var j = 0; j < labels.length; j++){
                        if(labels[j]!="0"){
                            yearhtml = "<option value='"+labels[j]+"'>"+labels[j]+"</option>";
                        }

                        if(!("key"+labels[j] in ptemp)){
                            ptemp["key"+labels[j]] = 0;
                        }
                        if(!("key"+labels[j] in rtemp)){
                            rtemp["key"+labels[j]]=0;
                        }
                        if(!("key"+labels[j] in atemp)){
                            atemp["key"+labels[j]]=0;
                        }
                        p.push(ptemp["key"+labels[j]]);
                        r.push(rtemp["key"+labels[j]]);
                        a.push(atemp["key"+labels[j]]);

                    }
                    $('#cancelyearSelect').html(yearhtml);
                    $('#cancelyearSelect').val(labels[1]);
                }

                else if(mode == 'month'){
                    for(var i = 0; i < data.length; i++){
                        if($.inArray(data[i].month, labels) < 0){
                            labels.push(data[i].month);
                        }
                        if(data[i].status == 'PN'){
                            ptemp["key"+data[i].month] = data[i].total;
                        }else if(data[i].status == 'RJ'){
                            rtemp["key"+data[i].month] = data[i].total;
                        }else if(data[i].status == 'AP'){
                            atemp["key"+data[i].month]=data[i].total;
                        }

                    }

                    if(labels.length <= 1)
                        labels.unshift("0");

                    for(var j = 0; j < labels.length; j++){

                        if(!("key"+labels[j] in rtemp)){
                            rtemp["key"+labels[j]] = 0;
                        }
                        if(!("key"+labels[j] in atemp)){
                            atemp["key"+labels[j]]=0;
                        }
                        if(!("key"+labels[j] in ptemp)){
                            ptemp["key"+labels[j]]=0;
                        }
                        p.push(ptemp["key"+labels[j]]);
                        r.push(rtemp["key"+labels[j]]);
                        a.push(atemp["key"+labels[j]]);

                    }
                }
                else if(mode == 'days'){
                    for(var i = 0; i < data.length; i++){
                        if($.inArray(data[i].date, labels) < 0){
                            labels.push(data[i].date);
                        }
                        if(data[i].status == 'PN'){
                            ptemp["key"+data[i].date] = data[i].total;
                        }else if(data[i].status == 'RJ'){
                            rtemp["key"+data[i].date] = data[i].total;
                        }else if(data[i].status == 'AP'){
                            atemp["key"+data[i].date]=data[i].total;
                        }

                    }

                    if(labels.length <= 1)
                        labels.unshift("0");

                    for(var j = 0; j < labels.length; j++){

                        if(!("key"+labels[j] in rtemp)){
                            rtemp["key"+labels[j]] = 0;
                        }
                        if(!("key"+labels[j] in atemp)){
                            atemp["key"+labels[j]]=0;
                        }
                        if(!("key"+labels[j] in ptemp)){
                            ptemp["key"+labels[j]]=0;
                        }
                        p.push(ptemp["key"+labels[j]]);
                        a.push(atemp["key"+labels[j]]);
                        r.push(rtemp["key"+labels[j]]);

                    }
                }
                var data = {
                    labels: labels,
                    datasets: [{
                        label: 'Pending Requests',
                        data: p,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(0,0,0,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1,
                    },{
                        label: 'Rejected Requests',
                        data: r,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255,99,132,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1,

                    },{
                        label: 'Approved Requests',
                        data: a,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255,99,132,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1,

                    }]
                };

                var options = {
                    plugins: {
                        filler: {
                            propagate: true
                        }
                    },
                    tooltips: {
                        enabled: true,
                        mode: 'single',
                        callbacks: {
                            label: function(tooltipItems, data) {
                                return data.datasets[tooltipItems.datasetIndex].label+': '+tooltipItems.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                            }
                        }
                    },
                    scales: {
                        yAxes: [
                            {
                                ticks: {
                                    callback: function(label, index, labels) {
                                        return label.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                    }
                                }
                            }
                        ]
                    }
                };

                if(cancellineChart)
                    cancellineChart.destroy();

                var lineChartCanvas = $("#cancellineChart").get(0).getContext("2d");
                cancellineChart = new Chart(lineChartCanvas, {
                    type: 'line',
                    data: data,
                    options: options
                });
            }

            function createbargraph(data,mode){
                var labels = [];
                var temp = [];
                var order = [];
                var yearhtml = '';

                if(mode == 'year'){
                    for(var i = 0; i < data.length; i++){
                        if($.inArray(data[i].year, labels) < 0){
                            labels.push(data[i].year);
                        }
                        temp["key"+data[i].year]=data[i].total;

                    }

                    for(var j = 0; j < labels.length; j++){
                        if(labels[j]!="0"){
                            yearhtml = "<option value='"+labels[j]+"'>"+labels[j]+"</option>";
                        }
                        if(!("key"+labels[j] in temp)){
                            temp["key"+labels[j]]=0;
                        }
                        order.push(temp["key"+labels[j]]);

                    }
                    $('#baryearSelect').html(yearhtml);
                    $('#baryearSelect').val(labels[0]);
                }

                else if(mode == 'month'){
                    for(var i = 0; i < data.length; i++){
                        if($.inArray(data[i].month, labels) < 0){
                            labels.push(data[i].month);
                        }
                        temp["key"+data[i].month]=data[i].total;

                    }


                    for(var j = 0; j < labels.length; j++){

                        if(!("key"+labels[j] in temp)){
                            temp["key"+labels[j]]=0;
                        }
                        order.push(temp["key"+labels[j]]);

                    }
                }
                else if(mode == 'days'){
                    for(var i = 0; i < data.length; i++){
                        if($.inArray(data[i].date, labels) < 0){
                            labels.push(data[i].date);
                        }
                        temp["key"+data[i].date]=data[i].total;

                    }

                    for(var j = 0; j < labels.length; j++){

                        if(!("key"+labels[j] in temp)){
                            temp["key"+labels[j]]=0;
                        }
                        order.push(temp["key"+labels[j]]);

                    }
                }
                var data = {
                    labels: labels,
                    datasets: [{
                        label: 'No. Of Orders',
                        data: order,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(0,0,0,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1,
                    }]
                };
                var options = {
                    plugins: {
                        filler: {
                            propagate: true
                        }
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        enabled: true,
                        mode: 'single',
                        callbacks: {
                            label: function(tooltipItems, data) {
                                return data.datasets[tooltipItems.datasetIndex].label+': '+tooltipItems.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                            }
                        }
                    },
                    scales: {
                        yAxes: [
                            {
                                ticks: {
                                    callback: function(label, index, labels) {
                                        return label.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                    }
                                }
                            }
                        ]
                    }
                };

                if(barlineChart)
                    barlineChart.destroy();

                var lineChartCanvas = $("#barChart").get(0).getContext("2d");
                barlineChart = new Chart(lineChartCanvas, {
                    type: 'bar',
                    data: data,
                    options: options
                });
            }

        })
        @endif
    </script>
@endpush