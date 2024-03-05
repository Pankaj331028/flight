<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Library\Notify;
use App\Model\Order;
use App\Model\OrderDelivery;
use App\Model\OrderDriver;
use App\Model\OrderItem;
use App\Model\OrderReturn;
use App\Model\User;
use App\Model\Wallet;
use Config;
use DB;
use Illuminate\Http\Request;
use Session;

class OrderController extends Controller
{
    public $order;
    public $order_return;
    public $columns;

    public function __construct()
    {
        $this->order = new Order;
        $this->order_return = new OrderReturn;
        $this->columns = [
            "sno", "order_code", "order_no", "user_id", "products", "address", "payment_method", "wallet", "total_amount", "coupon_discount", "shipping_fee", "savings", "credit_used", "grand_total", "status", "action",
        ];

    }
    public function index()
    {
        return view('order.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ordersAjax(Request $request)
    {
        $request->search = $request->search['value'];
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->order->fetchOrders($request, $this->columns);
        $total = $records->count();
        if (isset($request->start)) {
            $orders = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $orders = $records->offset($request->start)->limit($total)->get();
        }
        // echo $total;
        $result = [];
        $i = $request->start + 1;
        // print_r($orders);die();
        foreach ($orders as $order) {
            $data = [];
            $data['sno'] = $i++;
            $data['order_code'] = $order->order_code;
            $data['order_no'] = $order->order_no;
            $data['user_id'] = $order->user['first_name'] . " " . $order->user['last_name'];
            $data['payment_method'] = Config::get('constants.PAYMENT_METHOD.' . $order->payment_method);
            $data['address'] = $order->user_address['first_name'] . " " . $order->user_address['last_name'] . ", " . $order->user_address['apartment'] . ", " . $order->user_address['address'] . ", " . $order->user_address['acity']['name'] . ", " . $order->user_address['astate']['name'] . ", " . $order->user_address['acountry']['name'] . "- " . $order->user_address['zipcode'];
            $data['wallet'] = ucfirst(config('constants.CONFIRM.' . $order->wallet));
            $data['total_amount'] = $order->total_amount;
            $data['coupon_discount'] = $order->coupon_discount;
            $data['shipping_fee'] = $order->shipping_fee;
            $data['grand_total'] = $order->grand_total;
            $data['savings'] = $order->savings;
            $data['credit_used'] = $order->credit_used;
            $data['status'] = ucfirst(config('constants.STATUS.' . $order->status));

            $products = '';
            $count = 1;
            foreach ($order->order_items as $key => $value) {
                $products .= "Item " . $count++ . ": \n";
                $products .= "Product: " . $value->product_items->name . "\n";
                //    $products .= "Size: " . $value->product_variation->weight . " " . $value->product_variation->product_units->unit . "\n";
                $products .= "Quantity: " . $value->qty . "\n";
                $products .= "Price: " . Session::get('currency') . $value->price . "\n";
                $products .= "Special Price: " . (($value->special_price != null) ? Session::get('currency') . $value->special_price : '-') . "\n\n";
                $products .= "Duration: " . (($value->scheduled == '0') ? 'For ' . date('d M, Y', strtotime($value->start_date)) : 'From ' . date('d M, Y', strtotime($value->start_date)) . ' to ' . date('d M, Y', strtotime($value->end_date))) . "\n\n";
                $products .= "Coupon Discount: " . (($value->coupon_discount != null) ? $value->coupon_discount . '%' : '-') . "\n\n";
            }
            $data['products'] = $products;

            $action = '';

            if (Helper::checkAccess(route('viewOrder'))) {
                $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewOrder', ['id' => $order->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

                if (in_array($order->status, ['PN', 'CM'])) {
                    $action .= '&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="toolTip sendInvoice" data-toggle="tooltip" data-id="' . $order->id . '" data-placement="bottom" title="Send Invoice"><i class="fa fa-envelope"></i></a>';
                }

            }
            $data['action'] = $action;

            $result[] = $data;
        }
        $data = json_encode([
            'data' => $result,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
        ]);
        echo $data;

    }
    public function show(Request $request, $id = null)
    {
        if (isset($id) && $id != null) {
            $order = Order::where('id', $id)->first();

            $order->cancelled_amount = $order->order_items()->select(DB::raw('sum(special_price * qty) as total'))->whereIn('status', ['RFCM', 'RFIN'])->pluck('total')->first();

            $order->cancelled_savings = $order->order_items()->select(DB::raw('sum(price-special_price * qty) as total'))->whereIn('status', ['RFCM', 'RFIN'])->pluck('total')->first();

            if ($order->cancelled_amount == null) {
                $order->cancelled_amount = 0;
            }
            if ($order->cancelled_savings == null) {
                $order->cancelled_savings = 0;
            }
            if ($order->cancelled_amount == 0) {
                $order->savings = $order->savings;
            } else {
                $order->savings = $order->savings - $order->cancelled_savings;
            }

            if (isset($order->id)) {

                $drivers = User::whereStatus('AC')->whereHas('user_role', function ($q) {
                    $q->where('role', 'driver');
                })->get();

                $ds = OrderDelivery::where('order_id', $order->id)->whereStatus('PN')->select('order_date')->whereNull('reschedule_date')->distinct()->get();
                $rds = OrderDelivery::where('order_id', $order->id)->whereStatus('PN')->select('reschedule_date as order_date')->whereNotNull('reschedule_date')->distinct()->get();

                //assigned driver
                $assignedDriver = OrderDriver::where('order_id', $order->id)->whereStatus('CM')->orderBy('id', 'desc')->first();

                $dates = $ds->concat($rds);

                foreach ($dates as $key => &$value) {
                    $driver = OrderDriver::where('order_id', $order->id)->where('date', $value->order_date)->orderBy('id', 'desc')->first();
                    $value->driver_id = isset($driver->driver->id) ? $driver->driver->id : 0;
                }

                $return = new OrderReturn;
                if (!in_array($order->status, ['PN', 'CM', 'RN'])) {
                    $return = OrderReturn::where('order_id', $order->id)->where('type', 'order')->first();
                }
                $query = OrderDelivery::where('order_id', $order->id)->where('status', '!=', 'DL')->get();
                $singleday = 0;

                $schedules = [];
                foreach ($query as $key => $value) {
                    $temp = new OrderDelivery;
                    $temp['id'] = $value->id;
                    if (isset($value->order_item->product_variation->product_units->unit)) {

                        $temp['product'] = $value->order_item->product_items->name . "(" . $value->order_item->product_variation->weight . " " . $value->order_item->product_variation->product_units->unit ?? '' . ")";
                    } else {
                        $temp['product'] = '';
                    }

                    $temp['qty'] = $value->order_item->qty ?? '';
                    $temp['price'] = $value->order_item->price;
                    $temp['special_price'] = $value->order_item->special_price;
                    $temp['coupon_code'] = ($value->order_item->coupon_code_id != null) ? $value->order_item->coupon_range->coupon_code->code : '-';
                    $temp['coupon_disc'] = ($value->order_item->coupon_discount != null) ? $value->order_item->coupon_discount : '-';
                    $temp['type'] = ($value->order_item->scheduled != '1') ? 'Single Day Order' : 'Multiple Scheduling';
                    $temp['time'] = date('H:i A', strtotime($value->order_item->delivery_slot->from_time)) . "-" . date('H:i A', strtotime($value->order_item->delivery_slot->to_time));
                    $temp['date'] = date('M d, Y', strtotime($value->order_date));
                    $temp['status'] = Config::get('constants.STATUS.' . $value->status);
                    $temp['rescheduled'] = 'No';
                    $temp['reschedule_date'] = '-';
                    if ($value->order_item->scheduled == '1') {
                        $singleday++;
                    }

                    if ($value->reschedule_date != null) {
                        $temp['rescheduled'] = 'Yes';
                        $temp['reschedule_date'] = date('M d, Y', strtotime($value->reschedule_date));
                        $temp['status'] = ($this->getItemStatus($value->status) == 'pending') ? 'Rescheduled' : Config::get('constants.STATUS.' . $value->status);
                    }
                    $action = '';

                    if ($value->status == 'PN') {
                        $action = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($value->status == 'CM' ? ' checked' : '') . ' data-id="' . $value->id . '" data-on-color="success" data-off-color="info" data-on-text="Delivered" data-off-text="Pending" data-size="mini" name="cstatus" class="statusOrder" id="statusOrder"></div></div>';
                    } elseif ($value->status == 'CM') {
                        if ($value->order_item->scheduled != '1') {

                            $action = '<div class="bt-switch" title="Switch to return order" data-toggle="tooltip"><div class="col-md-2"><input type="checkbox"' . ($value->status == 'RN' ? ' checked' : '') . ' data-id="' . $value->id . '" data-on-color="success" data-off-color="info" data-on-text="Returned" data-off-text="Delivered" data-size="mini" name="cstatus" class="statusCompleteOrder" id="statusOrder"></div></div>';
                        } else {
                            $action = '-';
                        }

                    } elseif ($value->status == 'RN') {
                        $action = '-';
                    } else {
                        $return = OrderReturn::where('order_schedule_id', $value->id)->where('order_item_id', $value->order_item_id)->where('order_id', $order->id)->first();
                        $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewOrderReturn', ['id' => $return->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Cancel Request" target="_blank"><i class="fa fa-eye"></i></a>';
                    }

                    $temp['action'] = $action;
                    $schedules[] = $temp;
                }
                usort($schedules, function ($a, $b) {
                    return strtotime($a['date']) - strtotime($b['date']);
                });

                return view('order.view', compact('order', 'schedules', 'return', 'singleday', 'drivers', 'dates', 'assignedDriver'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('orders');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('orders');
        }

    }

    public function getItemStatus($status)
    {
        switch ($status) {
            case 'PN':
            case 'RFCL':
                $status = 'pending';
                break;
            case 'CM':
                $status = 'delivered';
                break;
            case 'CL':
            case 'RFCM':
                $status = 'cancelled';
                break;
            case 'RFIN':
                $status = 'cancel initiated';
                break;
            case 'RN':
                $status = 'returned';
                break;
            default:
                $status = 'pending';
                break;

        }
        return $status;
    }

    public function update(Request $request)
    {

        $order = Order::find($request->orderid);
        $schedule = OrderDelivery::find($request->statusid);

        if (isset($order->id) && isset($schedule->id)) {
            $item = OrderItem::where('id', $schedule->order_item_id)->first();
            $schedule->status = $request->status;
            if ($schedule->save()) {
                if ($request->status == 'CM') {
                    $date = ($schedule->reschedule_date == null) ? $schedule->order_date : $schedule->reschedule_date;

                    $notification_type = "order";
                    $title = 'Item Delivered';
                    $description = "Your item " . $item->product_items->name . " of order #: " . $order->order_no . " has been delivered for date " . date('d M, Y', strtotime($date)) . " to your selected address.";

                    Notify::sendNotification($order->user_id, 1, $title, $description, $notification_type);

                    // check if all the scheduled delivery of this order item is delivered. If yes, change the status of that order item to delivered.
                    $pendingcount = OrderDelivery::where('order_item_id', $schedule->order_item_id)->where('status', 'PN')->count();
                    if ($pendingcount <= 0) {
                        $item->status = 'CM';
                        $item->save();
                    }

                    // check if all the scheduled delivery is delivered. If yes, change the status of complete order to delivered.
                    $pendingcount = OrderDelivery::where('order_id', $order->id)->where('status', 'PN')->count();
                    if ($pendingcount <= 0) {
                        $order->status = 'CM';
                        $order->save();

                        OrderDriver::where('order_id', $order->id)->whereStatus('PN')->update(['status' => 'CM']);

                        $notification_type = "order";
                        $title = 'Order Completed';
                        $description = "Your order #: " . $order->order_no . " has been delivered  to your selected address.";

                        Notify::sendNotification($order->user_id, 1, $title, $description, $notification_type);
                    }
                    echo json_encode(['status' => 1, 'message' => 'Order Item delivered successfully.']);
                } elseif ($request->status == 'RN') {
                    // check if this is a single day delivery item
                    $checkType = OrderDelivery::where('order_item_id', $item->id)->count();

                    if ($checkType == 1) {
                        $item->status = $request->status;
                        $item->save();

                        // check if all the order items are returned. If yes, change the status of complete order to returned.
                        $pendingcount = OrderDelivery::where('order_id', $order->id)->where('status', 'RN')->count();
                        if ($pendingcount == OrderDelivery::where('order_id', $order->id)->count()) {
                            $order->status = 'RN';
                            $order->save();
                        }
                        $user = User::find($order->user_id);
                        $user->wallet_amount += $this->getItemPrice($item);
                        $user->save();

                        $notification_type = "wallet";
                        $title = 'Refund';

                        $date = ($schedule->reschedule_date == null) ? $schedule->order_date : $schedule->reschedule_date;

                        $description = "Your return request for item " . $item->product_items->name . " of order #: " . $order->order_no . " has been approved/accepted by gogrocery customer support team for date " . date('d M, Y', strtotime($date)) . ". Amount is credited to your wallet.";

                        Notify::sendNotification($user->id, 1, $title, $description, $notification_type);

                        $wallet = new Wallet;
                        $wallet->user_id = $user->id;
                        $wallet->type = 'credit';
                        $wallet->amount = $this->getItemPrice($item);
                        $wallet->description = 'Refund: Order Returned by You';
                        $wallet->reason = 'order_return';
                        $wallet->order_return_id = $request->orderid;
                        $wallet->save();
                        echo json_encode(['status' => 1, 'message' => 'Order Item returned successfully. Amount has been added to user\'s wallet']);
                    } else {
                        echo json_encode(['status' => 0, 'message' => 'This item cannot be returned.']);
                    }
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Unable to update order item. Please try again later.']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid Data']);
        }
    }

    public function completeOrder(Request $request, $id = null)
    {

        if (isset($id) && $id != null) {

            $order = Order::find($request->id);

            if (isset($order->id)) {
                $order->status = 'CM';
                if ($order->save()) {
                    $user = User::find($order->user_id);
                    OrderDelivery::where('order_id', $order->id)->update(['status' => 'CM']);
                    OrderItem::where('order_id', $order->id)->update(['status' => 'CM']);

                    $date = OrderItem::where('order_id', $order->id)->first();

                    $od = OrderDriver::where('order_id', $order->id)->where('date', date('Y-m-d', strtotime($date->start_date)))->orderBy('id', 'desc')->first();

                    if (isset($od->id)) {
                        $od->status = $request->status;
                        $od->save();
                    }
                    $notification_type = "order";
                    $title = 'Order Delivered';
                    $description = "Your order #: " . $order->order_no . " has been delivered for date " . date('d M, Y', strtotime($date->start_date)) . " to your selected address.";

                    Notify::sendNotification($order->user_id, 1, $title, $description, $notification_type);

                    // Notify::sendNotification($user->id, 1, $title, $description, $notification_type);

                    $request->session()->flash('success', 'Order delivered successfully.');
                    return redirect()->back();

                } else {
                    $request->session()->flash('error', 'Unable to update order. Please try again later.');
                    return redirect()->back();
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->back();
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->back();
        }
    }

    public function returnOrder(Request $request, $id = null)
    {

        if (isset($id) && $id != null) {

            $order = Order::find($request->id);

            if (isset($order->id)) {
                $order->status = 'RN';
                if ($order->save()) {
                    OrderDelivery::where('order_id', $order->id)->update(['status' => 'RN']);
                    $amount = 0;
                    $items = OrderItem::where('order_id', $order->id)->get();
                    foreach ($items as $key => $value) {
                        if (!in_array($value->status, ['CL', 'RN', 'RFCM'])) {
                            $amount += $this->getItemPrice($value);
                            $value->status = 'RN';
                            $value->save();
                        }
                    }

                    $user = User::find($order->user_id);
                    $user->wallet_amount += $amount;
                    $user->save();

                    $notification_type = "wallet";
                    $title = 'Refund';

                    $description = "Your return request for order #: " . $order->order_no . " has been approved/accepted by gogrocery customer support team. Amount is credited to your wallet.";

                    Notify::sendNotification($user->id, 1, $title, $description, $notification_type);

                    $wallet = new Wallet;
                    $wallet->user_id = $user->id;
                    $wallet->type = 'credit';
                    $wallet->amount = $amount;
                    $wallet->description = 'Refund: Order Returned by You';
                    $wallet->reason = 'order_return';
                    $wallet->order_return_id = $request->id;
                    $wallet->save();
                    $request->session()->flash('success', 'Order returned successfully. Amount has been added to user\'s wallet');
                    return redirect()->back();

                } else {
                    $request->session()->flash('error', 'Unable to update order. Please try again later.');
                    return redirect()->back();
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->back();
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->back();
        }
    }

    public function getItemPrice($item)
    {
        $days = date_diff(date_create($item->start_date), date_create($item->end_date))->format('%a');

        $price = $item->special_price * ($days + 1);
        if ($item->coupon_code_id != null) {
            $price = (100 - $item->coupon_discount) * $price / 100;
        }
        return $price;
    }

    public function getPrice($item)
    {

        $price = $item->special_price;
        if ($item->coupon_code_id != null) {
            $price = (100 - $item->coupon_discount) * $price / 100;
        }
        return $price;
    }

    public function sendInvoice(Request $request)
    {
        $id = $request->id;
        if (isset($id) && $id != null) {
            $order = Order::where('id', $id)->first();
            if (isset($order->id)) {
                try {

                    $items = OrderItem::where('order_id', $order->id)->get();
                    foreach ($items as $key => &$value) {
                        $value->amount = $this->getItemPrice($value);
                    }

                    $order->amount = $order->total_amount + $order->shipping_fee - $order->savings;
                    $user = User::where("id", $order->user_id)->first();
                    $data = array(
                        'order' => $order,
                        'email' => $user->email,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'items' => $items,
                        'currency' => Session::get('currency'),
                        'customer_care' => Notify::getBusRuleRef('customer_care_number'),
                    );

                    Notify::sendMail("emails.invoice", $data, "Order Invoice #" . $order->order_no . "- omrbranch");
                    echo json_encode(['status' => 1, 'message' => 'Invoice sent successfully.']);
                } catch (Exception $e) {
                    echo json_encode(['status' => 0, 'message' => 'Something went wrong. Please try again later.']);
                }
            }

        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid Data']);
        }
    }

    public function assignDriver(Request $request, $id = null)
    {
        if ($id != null) {
            $order = Order::find($id);

            if (isset($order->id)) {
                $ds = OrderDelivery::where('order_id', $order->id)->whereStatus('PN')->select('order_date')->whereNull('reschedule_date')->distinct()->get();
                $rds = OrderDelivery::where('order_id', $order->id)->whereStatus('PN')->select('reschedule_date as order_date')->whereNotNull('reschedule_date')->distinct()->get();

                $dates = $ds->concat($rds);
                $count = 0;

                if (!isset($request->date)) {
                    foreach ($dates as $key => $value) {
                        $assign = OrderDriver::where('order_id', $order->id)->where('date', $value->order_date)->orderBy('id', 'desc')->first();

                        if (!isset($assign->id)) {
                            $assign = new OrderDriver;
                        } else {
                            if ($assign->driver_id != $request->driver_id) {
                                // send notification & mail to previous driver if driver is changed that order is assigned to someone else. no need to reach warehouse to collect order
                                $notification_type = "order";
                                $title = 'Order Delivery Changed';
                                $description = "Order #: " . $order->order_no . " has been assigned to other driver for date " . date('d M, Y', strtotime($value->order_date)) . ".";

                                Notify::sendNotification($assign->driver_id, 1, $title, $description, $notification_type);

                                $driver = User::find($assign->driver_id);

                                $data = [
                                    'first_name' => $driver->first_name,
                                    'last_name' => $driver->last_name,
                                    'email' => $driver->email,
                                    'order' => $order,
                                    'date' => $value->order_date,
                                ];
                                $sender_id = Config::get('constants.MAIL_SENDER_ID');
                                Notify::sendMail('emails.order_change_driver', $data, $title);
                            }
                        }

                        $assign->order_id = $order->id;
                        $assign->date = date('Y-m-d', strtotime($value->order_date));
                        $assign->driver_id = $request->driver_id;
                        $assign->status = 'PN';
                        $assign->reason = '';

                        if ($assign->save()) {
                            $count++;

                            // send mail & notification to driver related to order delivery and date
                            $notification_type = "order";
                            $title = 'New Order Assigned';
                            $description = "Order #: " . $order->order_no . " has been assigned to you for delivery on date " . date('d M, Y', strtotime($value->order_date)) . ".";

                            Notify::sendNotification($assign->driver_id, 1, $title, $description, $notification_type);

                            $driver = User::find($assign->driver_id);

                            $data = [
                                'first_name' => $driver->first_name,
                                'last_name' => $driver->last_name,
                                'email' => $driver->email,
                                'order' => $order,
                                'date' => $value->order_date,
                            ];
                            $sender_id = Config::get('constants.MAIL_SENDER_ID');
                            Notify::sendMail('emails.new_order_driver', $data, $title);
                        }
                    }
                    if ($count == count($dates)) {
                        $request->session()->flash('success', 'Driver assigned to this order.' . ((count($dates) > 1) ? 'You can change driver for any of the dates.' : ''));
                        return redirect()->back();
                    } else {
                        $request->session()->flash('success', 'Some error occurred while assigning driver for each delivery date. Please check');
                        return redirect()->back();
                    }
                } else {
                    $assign = OrderDriver::where('order_id', $order->id)->where('date', $request->date)->orderBy('id', 'desc')->first();
                    if (!isset($assign->id)) {
                        $assign = new OrderDriver;
                    } else {
                        if ($assign->driver_id != $request->driver_id) {
                            // send notification & mail to previous driver if driver is changed that order is assigned to someone else. no need to reach warehouse to collect order
                            $notification_type = "order";
                            $title = 'Order Delivery Changed';
                            $description = "Order #: " . $order->order_no . " has been assigned to other driver for date " . date('d M, Y', strtotime($request->date)) . ".";

                            Notify::sendNotification($assign->driver_id, 1, $title, $description, $notification_type);

                            $driver = User::find($assign->driver_id);

                            $data = [
                                'first_name' => $driver->first_name,
                                'last_name' => $driver->last_name,
                                'email' => $driver->email,
                                'order' => $order,
                                'date' => $request->date,
                            ];
                            $sender_id = Config::get('constants.MAIL_SENDER_ID');
                            Notify::sendMail('emails.order_change_driver', $data, $title);
                        }
                    }

                    $assign->order_id = $order->id;
                    $assign->date = date('Y-m-d', strtotime($request->date));
                    $assign->driver_id = $request->driver_id;
                    $assign->status = 'PN';
                    $assign->reason = '';

                    if ($assign->save()) {

                        // send mail & notification to driver related to order delivery and date
                        $notification_type = "order";
                        $title = 'New Order Assigned';
                        $description = "Order #: " . $order->order_no . " has been assigned to you for delivery on date " . date('d M, Y', strtotime($request->date)) . ".";

                        Notify::sendNotification($assign->driver_id, 1, $title, $description, $notification_type);

                        $driver = User::find($assign->driver_id);

                        $data = [
                            'first_name' => $driver->first_name,
                            'last_name' => $driver->last_name,
                            'email' => $driver->email,
                            'order' => $order,
                            'date' => $request->date,
                        ];
                        $sender_id = Config::get('constants.MAIL_SENDER_ID');
                        Notify::sendMail('emails.new_order_driver', $data, $title);

                        echo json_encode(['status' => 1, 'message' => 'Driver assigned to this date.']);
                    } else {
                        echo json_encode(['status' => 0, 'message' => 'Some error occurred while assigning driver. Please check']);
                    }
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid Data']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid Data']);
        }
    }

    public function return_index()
    {
        return view('order.return_index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function returnsAjax(Request $request)
    {
        $request->search = $request->search['value'];
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->order_return->fetchReturns($request, $this->columns);
        $total = count($records->get());
        if (isset($request->start)) {
            $returns = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $returns = $records->offset($request->start)->limit($total)->get();
        }
        $result = [];
        $i = $request->start + 1;

        foreach ($returns as $order) {
            if ($order->order->status != 'CM') {
                $data = [];
                $data['sno'] = $i++;
                $data['order_no'] = $order->order->order_no;
                $data['user_id'] = $order->order->user->first_name . " " . $order->order->user->last_name;
                $data['payment_method'] = ucfirst(config('constants.PAYMENT_METHOD.' . $order->order->payment_method));
                $data['type'] = ucfirst($order->type);
                $amt = 0;
                $data['status'] = ucfirst(config('constants.STATUS.' . $order->status));

                $prod = explode(',', $order->products);
                $products = '';

                $count = 1;
                foreach ($prod as $key => $value) {
                    $item = OrderItem::find($value);

                    $products .= "Item " . $count++ . ": " . "\n";
                    $products .= "Product: " . $item->product_items->name . "\n";
                    if (isset($item->product_variation->product_units->unit)) {
                        $products .= "Size: " . $item->product_variation->weight . " " . $item->product_variation->product_units->unit ?? '' . "\n";

                    } else {
                        $products .= "Size: " . $item->product_variation->weight . "\n";
                    }
                    $products .= "Quantity: " . $item->qty . "\n";
                    $products .= "Price: " . Session::get('currency') . $this->getPrice($item) . "\n";
                    $products .= "Special Price: " . (($item->special_price != null) ? Session::get('currency') . $item->special_price : '-') . "\n\n";
                    $products .= "Duration: " . (($item->scheduled == '0') ? 'For ' . date('d M, Y', strtotime($item->start_date)) : 'From ' . date('d M, Y', strtotime($item->start_date)) . ' to ' . date('d M, Y', strtotime($item->end_date))) . "\n\n";
                    $products .= "Coupon Discount: " . (($item->coupon_discount != null) ? $item->coupon_discount . '%' : '-') . "\n\n";
                    $amt += $this->getPrice($item);
                }
                $data['amount'] = $amt;
                $data['product'] = $products;

                $action = '';

                if (Helper::checkAccess(route('viewOrderReturn'))) {
                    $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewOrderReturn', ['id' => $order->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
                }
                $data['action'] = $action;

                $result[] = $data;
            }
        }
        $data = json_encode([
            'data' => $result,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
        ]);
        echo $data;

    }

    public function return_show(Request $request, $id = null)
    {
        if (isset($id) && $id != null) {
            $return = OrderReturn::where('id', $id)->first();
            if (isset($return->id)) {
                if ($return->type == 'order') {
                    $items = OrderReturn::where('type', 'order')->where('order_id', $return->order_id)->get();
                } else {
                    $items[] = $return;
                }
                return view('order.return_view', compact('return', 'items'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('returns');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('returns');
        }

    }

    public function return_update(Request $request)
    {
        if (isset($request->statusid)) {
            $ids = explode(',', $request->statusid);
            $type = (count($ids) > 1) ? 'order' : 'product';

            foreach ($ids as $key => $value) {
                $return = OrderReturn::find($value);
                $user = User::find($return->order->user_id);
                $return->status = $request->status;
                $return->save();

                $status = ($request->status == 'AP') ? 'RFCM' : 'RFCL';
                $schedule = OrderDelivery::find($return->order_schedule_id);
                $schedule->status = $status;
                $schedule->save();

                $item = OrderItem::find($return->order_item_id);
                if ($item->status == 'RFIN') {
                    $item->status = $status;
                }

                $item->save();

                $order = Order::find($return->order_id);
                if ($order->status == 'RFIN') {
                    $order->status = $status;
                }

                $order->save();
            }
            if ($request->status == 'AP') {
                if ($order->wallet == '1' && round($order->credits_used) != 0) {

                    $user->wallet_amount += $request->amount;
                    $user->save();
                }

                $notification_type = "wallet";
                $title = 'Refund';

                if ($type == 'order') {
                    $description = "Your cancellation request for order #: " . $order->order_no . " has been approved/accepted by gogrocery customer support team.";

                    $date = ($schedule->reschedule_date == null) ? $schedule->order_date : $schedule->reschedule_date;

                    OrderDriver::where('order_id', $order->id)->where('status', '!=', 'CM')->update(['status' => 'DL']);

                    $drivers = OrderDriver::where('order_id', $order->id)->where('date', $date)->select('driver_id')->distinct()->get();

                    if (count($drivers) > 0) {
                        foreach ($drivers as $key => $value) {

                            // notifcation and mail to driver for cancelled order
                            $notification_type = "order";
                            $title = 'Order Cancelled';
                            $description = "Order #: " . $order->order_no . " has been cancelled. Do not deliver this order";

                            Notify::sendNotification($value->driver_id, 1, $title, $description, $notification_type);

                            $driver = User::find($value->driver_id);

                            $data = [
                                'first_name' => $driver->first_name,
                                'last_name' => $driver->last_name,
                                'email' => $driver->email,
                                'order' => $order,
                            ];
                            $sender_id = Config::get('constants.MAIL_SENDER_ID');
                            Notify::sendMail('emails.order_cancel_driver', $data, $title);
                        }
                    }
                } else {

                    $date = ($schedule->reschedule_date == null) ? $schedule->order_date : $schedule->reschedule_date;

                    $description = "Your cancellation request for item " . $item->product_items->name . " of order #: " . $order->order_no . " has been approved/accepted by gogrocery customer support team for date " . date('d M, Y', strtotime($date)) . ".";

                    $count = OrderDelivery::where(function ($q) use ($date) {
                        $q->where('order_date', $date)->orWhere('reschedule_date', $date);
                    })->where('status', 'PN')->count();

                    if ($count <= 0) {
                        OrderDriver::where('order_id', $order->id)->where('date', $date)->where('status', '!=', 'CM')->update(['status' => 'DL']);

                        $driver = OrderDriver::where('order_id', $order->id)->where('date', $date)->orderBy('id', 'desc')->first();

                        if (isset($driver->id)) {
                            // notifcation and mail to driver for cancelled order
                            $notification_type = "order";
                            $title = 'Order Cancelled';
                            $description = "Order #: " . $order->order_no . " has been cancelled for date " . date('d M, Y', strtotime($date)) . ". Do not deliver this order";

                            Notify::sendNotification($driver->driver_id, 1, $title, $description, $notification_type);

                            $driver = User::find($driver->driver_id);

                            $data = [
                                'first_name' => $driver->first_name,
                                'last_name' => $driver->last_name,
                                'email' => $driver->email,
                                'order' => $order,
                                'date' => $date,
                            ];
                            $sender_id = Config::get('constants.MAIL_SENDER_ID');
                            Notify::sendMail('emails.order_cancel_driver', $data, $title);
                        }
                    }
                }

                if ($order->wallet == '1' && round($order->credits_used) != 0) {
                    $description .= ' Amount is credited to your wallet.';
                }

                Notify::sendNotification($user->id, 1, $title, $description, $notification_type);

                if ($order->wallet == '1' && round($order->credits_used) != 0) {
                    $wallet = new Wallet;
                    $wallet->user_id = $user->id;
                    $wallet->type = 'credit';
                    $wallet->amount = $request->amount;
                    $wallet->description = 'Refund: Order Cancelled by You';
                    $wallet->reason = 'order_return';
                    $wallet->order_return_id = $request->id;
                    $wallet->save();
                }
            } elseif ($request->status == 'RJ') {
                if ($type == 'order') {
                    $description = "Your cancellation request for order #: " . $order->order_no . " has been rejected by gogrocery customer support team.";
                } else {

                    $date = ($schedule->reschedule_date == null) ? $schedule->order_date : $schedule->reschedule_date;

                    $description = "Your cancellation request for item " . $item->product_items->name . " of order #: " . $order->order_no . " has been rejected by gogrocery customer support team for date " . date('d M, Y', strtotime($date)) . ".";
                }

                Notify::sendNotification($user->id, 1, 'Cancel Request', $description, 'order');
            }

            $request->session()->flash('success', 'Cancellation request updated successfully.');
            return redirect()->back();
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->back();
        }

    }
}
