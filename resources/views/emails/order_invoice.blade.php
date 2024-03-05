<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table.table-bordered {
            border-collapse: collapse;
        }

        table.table-bordered,
        .table-bordered td,
        .table-bordered th {
            border: 1px solid black;
            font-size: 14px;
        }
    </style>
</head>

<body style="font-size: 0">
    <table width="550" align="center" bgcolor="#fff1ea" cellpadding="10" style="font-family: 'DejaVu Sans', sans-serif;width:100%; border-collapse: collapse;font-weight: 400">
        {{-- <tr style="height: 94px;">
            <td align="center" bgcolor="#ff6618">
                <img src="{{ asset('/front/images/logo.png') }}" alt="" width="40%"/>
            </td>
        </tr> --}}
        <tr align="center">
            <td>
                <p style="color: #555;font-size: 16px;word-spacing: 3px;font-weight: bold;text-transform: uppercase;margin: 0 0 30px 0;padding:0 0 10px 0;border-bottom: 1px solid #ff6618;">INVOICE</p>
            </td>
        </tr>
        <tr>
            <td>
                <div style="width: 50%;display:inline-block;height: 80px;color: #555">
                    <div style="width: 100%; display: inline-block;">
                        <div style="width: 100%;">
                            <p style="width: 50%;display:inline-block;margin: 0;font-size: 10px;">
                                <strong style="color:#555">Name:</strong>
                            </p>
                            <p style="width: 50%;display:inline-block;margin: 0;font-size: 10px;">{{$first_name." ".$last_name}}</p>
                        </div>
                        <div style="width: 100%;">
                            <p style="width: 50%;display:inline-block;margin: 0;font-size: 10px;">
                                <strong style="color:#555">Address:</strong>
                            </p>
                            <p style="width: 50%;display:inline-block;margin: 0;font-size: 10px;">{{$order['user_address']->apartment.','.$order['user_address']->address.','.$order['user_address']->acity->name.','.$order['user_address']->astate->name.','.$order['user_address']->acountry->name.'-'.$order['user_address']->zipcode}}
                            </p>
                        </div>

                        <div style="width: 100%;">
                            <p style="width: 50%;display:inline-block;margin: 0;font-size: 10px;">
                                <strong style="color:#555">Mob:</strong>
                            </p>
                            <p style="width: 50%;display:inline-block;margin: 0;font-size: 10px;">{{ $order['user_address']->mobile }}</p>
                        </div>
                    </div>
                </div>
                <div style="width: 50%;display:inline-block;height: 80px;color: #555">
                    <div style="width: 100%; display: inline-block;">
                        <div style="width: 100%;margin: 0">
                            <p style="width: 50%;display:inline-block;margin: 0;font-size: 10px;">
                                <strong style="color:#555">Order No:</strong>
                            </p>
                            <p style="width: 50%;display:inline-block;margin: 0;font-size: 10px;">{{$order['order_no']}}</p>
                        </div>
                        <div style="width: 100%;margin: 0">
                            <p style="width: 50%;display:inline-block;margin: 0;font-size: 10px;">
                                <strong style="color:#555">Date:</strong>
                            </p>
                            <p style="width: 50%;display:inline-block;margin: 0;font-size: 10px;">{{$order['created_at'] ?? ''}}</p>
                        </div>
                        <div style="width: 100%;margin: 0">
                            <p style="width: 50%;display:inline-block;margin: 0;font-size: 10px;">
                                <strong style="color:#555">Total Amount:</strong>
                            </p>
                            <p style="width: 50%;display:inline-block;margin: 0;font-size: 10px;">{{$currency}} {{$order['grand_total'] ?? ''}}</p>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <table class="table table-bordered" width="510" style="border: 1px solid #000;color: #555">
                    <thead>
                        <tr class="tableHead">
                            <th scope="col" align="center" style="font-size:10px;">SNo.</th>
                            <th scope="col" align="center" style="font-size:10px;">Item Name</th>
                            <th scope="col" align="center" style="font-size:10px;">Delivery Time</th>
                            <th scope="col" align="center" style="font-size:10px;">Quantity</th>
                            <th scope="col" align="center" style="font-size:10px;">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i=1;
                        @endphp
                        @foreach($items as $item)
                            <tr>
                                <th scope="row" align="center" style="font-size:10px;">{{$i++}}</th>
                                <td align="center" style="font-size:10px;">{{App\Model\Product::where(["id" => $item->product_id])->pluck("name")[0]}}</td>
                                <td align="center" style="font-size:10px;">@if($item->scheduled=='1')
                                 {{date('d M, Y',strtotime($item->start_date)) .' - '.date('d M, Y',strtotime($item->end_date)) . (isset($item->delivery_slot->id)?('('.date('h:i A', strtotime($item->delivery_slot->from_time)) . ' to ' . date('h:i A', strtotime($item->delivery_slot->to_time)).')'):'-')}}
                                 @else
                                 {{date('d M, Y',strtotime($item->start_date)) . (isset($item->delivery_slot->id)?('('.date('h:i A', strtotime($item->delivery_slot->from_time)) . ' to ' . date('h:i A', strtotime($item->delivery_slot->to_time)).')'):'-')}}
                                 @endif</td>
                                <td align="center" style="font-size:10px;">{{$item->qty}}</td>
                                <td align="center" style="font-size:10px;">{{$currency}}{{$item->amount}}</td>
                            </tr>
                        @endforeach
                        @if(sprintf('%.2f', $order['shipping_fee'])!=0.00)
                        <tr>
                            <!-- <th scope="row">3</th> -->
                            <td align="center" colspan="4" style="font-size:10px;font-weight: bold">Shipping Price</td>
                            <td align="center" style="font-size:10px;font-weight: bold;">{{$currency}} {{$order['shipping_fee']}}</td>
                        </tr>
                     @endif
                     @if(sprintf('%.2f', $order['credits_used'])!=0.00)
                        <tr>
                            <!-- <th scope="row">3</th> -->
                            <td align="center" colspan="4" style="font-size:10px;font-weight: bold">Credits Used</td>
                            <td align="center" style="font-size:10px;font-weight: bold;">{{$currency}} {{$order['credits_used']}}</td>
                        </tr>
                     @endif
                     @if(sprintf('%.2f', $order['coupon_discount'])!=0.00)
                        <tr>
                            <!-- <th scope="row">3</th> -->
                            <td align="center" colspan="4" style="font-size:10px;font-weight: bold">Coupon Discount</td>
                            <td align="center" style="font-size:10px;font-weight: bold;">{{$currency}} {{$order['coupon_discount']}}</td>
                        </tr>
                     @endif
                        <tr>
                            <!-- <th scope="row">3</th> -->
                            <td align="center" colspan="4" style="font-size:10px;font-weight: bold">Grand Total</td>
                            <td align="center" style="font-size:10px;font-weight: bold;">{{$currency}} {{$order['grand_total']}}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
