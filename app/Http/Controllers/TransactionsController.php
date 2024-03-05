<?php

namespace App\Http\Controllers;

use App\Library\Crypto;
use App\Library\Helper;
use App\Library\Notify;
use App\Library\ResponseMessages;
use App\Model\Cart;
use App\Model\CartItem;
use App\Model\Order;
use App\Model\OrderDelivery;
use App\Model\OrderItem;
use App\Model\ProductVariation;
use App\Model\ShippingCharge;
use App\Model\Transaction;
use App\Model\User;
use Illuminate\Http\Request;

class TransactionsController extends Controller {
	public $ccavenSettings;
	public $ccaven;
	public $mycontr;
	public $payment;
	public $columns;

	public function __construct() {
		$this->ccaven = new Crypto;
		$this->ccavenSettings = $this->ccaven->getConfig();
		$this->mycontr = new MyController;
		$this->payment = new Transaction;
		$this->columns = [
			"sno", "user_id", "order_id", "txn_id", "ccavenue_txnamount", "ccavenue_status", "status",
		];
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		return view('payments.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function paymentsAjax(Request $request) {
		if(isset($request->search['value'])){
            $request->search = $request->search['value'];
        }
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->payment->fetchPayments($request, $this->columns);
		$total = $records->get();
		$payments = $records->offset($request->start)->limit($request->length)->get();
		// echo $total;
		$result = [];
		$i = $request->start + 1;
		foreach ($payments as $payment) {
			$data = [];
			$data['sno'] = $i++;
			$data['user_id'] = ($payment->user) ? $payment->user->first_name . " " . $payment->user->last_name : '-';
			$data['order_id'] = ($payment->order) ? $payment->order->order_no : '-';
			$data['txn_id'] = $payment->txn_id != null ? $payment->txn_id : '-';
			$data['ccavenue_txnamount'] = $payment->ccavenue_txnamount != null ? $payment->ccavenue_txnamount : '-';
			$data['ccavenue_status'] = $payment->ccavenue_status != null ? $payment->ccavenue_status : '-';
			$data['status'] = ucfirst(config('constants.STATUS.' . $payment->status));

			$result[] = $data;
		}
		$data = json_encode([
			'data' => $result,
			'recordsTotal' => count($total),
			'recordsFiltered' => count($total),
		]);
		// &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="toolTip deleteUser" data-code="{{$user->name}}" data-id="{{$user->id}}" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a></td>
		echo $data;

	}

	/**
	 * redirect user to ccavenue autosubmit form
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function payment(Request $request) {

		$required = array("user_id");
		$input = array_keys($request->all());
		$existance = implode(", ", array_diff($required, $input));
		if (!empty($existance)) {
			if (count(array_diff($required, $input)) == 1) {
				$response = array(
					"status" => 101,
					"message" => $existance . " key is missing",
				);
			} else {
				$response = array(
					"status" => 101,
					"message" => $existance . " keys are missing",
				);
			}
			echo json_encode($response);
			exit;
		}
		$user = User::find($request->user_id);

		if ($user) {

			if ($user->status == "AC") {

			} else {
				$response = array(
					"status" => 216,
					"message" => ResponseMessages::getStatusCodeMessages(216),
				);
				echo json_encode($response);
				exit;
			}
		} else {
			$response = array(
				"status" => 321,
				"message" => ResponseMessages::getStatusCodeMessages(321),
			);
			echo json_encode($response);
			exit;
		}

		$cart = Cart::where('user_id', $request->user_id)->where('status', 'AC')->first();
		$user = User::find($request->user_id);

		if ($cart) {
			if ($cart->payment_method == 'online') {
				// check if delivery address is selected for the order
				if ($cart->address_id != null) {

					$usercartsitem = CartItem::where('cart_id', $cart->id)->with(['product_items', 'product_variation', 'delivery_slot'])->whereHas('product_items', function ($q) {
						$q->where('status', 'AC');
						$q->whereHas('category', function ($qu) {
							$qu->where('status', 'AC');
						})->orWhereHas('subcategory', function ($qu) {
							$qu->where('status', 'AC');
						});
					})->where('status', 'AC')->get();

					if ($usercartsitem->count() > 0) {
						$credit_usage = (int) $cart->wallet;
						$coupon_code_id = 0;

						// create new order
						$order = new Order;
						$order->order_code = Helper::generateNumber('orders', 'order_code');
						$order->order_no = Helper::generateNumberOrder();
						$order->user_id = $request->user_id;
						$order->address_id = $cart->address_id;
						$order->payment_method = $cart->payment_method;
						$order->wallet = $cart->wallet;
						$order->save();

						$amount = $this->calculateOrderAmount($cart, $usercartsitem, $request->user_id, $order);

						$transaction = new Transaction;
						$transaction->transaction_code = Helper::generateNumber("t_transactions", "transaction_code");
						$transaction->user_id = $order->user_id;
						$transaction->order_id = $order->id;
						$transaction->amount = $order->grand_total;
						$transaction->save();

						$settings = $this->ccavenSettings;
						$order_id = $order->id;
						$amount = $order->grand_total;
					} else {
						$response = array(
							"status" => 25,
							"message" => ResponseMessages::getStatusCodeMessages(25),
						);
						echo json_encode($response);
						exit;
					}
				} else {
					$response = array(
						"status" => 54,
						"message" => ResponseMessages::getStatusCodeMessages(54),
					);
					echo json_encode($response);
					exit;
				}
			} else {
				$response = array(
					"status" => 52,
					"message" => ResponseMessages::getStatusCodeMessages(52),
				);
				echo json_encode($response);
				exit;
			}
		} else {
			$response = array(
				"status" => 47,
				"message" => ResponseMessages::getStatusCodeMessages(47),
			);
			echo json_encode($response);
			exit;
		}
		return view('ccavenue', compact('settings', 'user', 'order_id', 'amount', 'transaction'));
	}

	public function calculateOrderAmount($cart, $usercartsitem, $user_id, $order) {

		$count = 0;
		$total_amount = $shipping_fee = $coupon_discount = 0;
		foreach ($usercartsitem as $key => &$value) {

			// check if offer on any variation has changed
			$product_variation = $this->mycontr->checkProductOffer($value->product_variation_id);

			if (isset($product_variation->offer_id) && $product_variation->offer_id != $value->offer_id) {

				$value->offer_id = isset($product_variation->offer_id) ? $product_variation->offer_id : null;
				$value->special_price = $product_variation->discount_price;
				$value->price = $product_variation->price;
				$value->coupon_code_id = null;
				$value->coupon_discount = null;
			} elseif (!isset($product_variation->offer_id)) {
				$value->offer_id = null;
				$value->special_price = $product_variation->discount_price;
				$value->price = $product_variation->price;
			}

			// update coupon code in cart details if coupon code expires on is changed
			$coupon = $this->mycontr->checkProductCoupon($value->product_variation_id, $user_id, $value->qty);

			if ($coupon != null) {
				$value->coupon_code_id = $coupon['range'];
				$value->coupon_discount = $coupon['discount'];
				$coupon_code_id = $coupon['range'];
			}

			// check product availability
			$available_qty = $this->mycontr->checkProductAvailability($value->product_variation_id);

			if ($available_qty < $value->qty) {
				$value->qty = $available_qty;
				$value->save();
				$value->is_available = ($available_qty >= 1) ? 1 : 0;
				$value->warning_msg = ($available_qty > 1) ? ("Only " . $available_qty . " are left") : (($available_qty == 0) ? "Out of Stock" : "Only " . $available_qty . " is left");
				$count++;
			}

			if ($count <= 0) {
				$value->discount = $this->mycontr->calcDiscount($value->special_price, $value->price);

				$days = date_diff(date_create($value->start_date), date_create($value->end_date))->format('%a');
				$total_qty = ($days + 1) * $value->qty;

				// calculate total_amount
				$total_amount += ($value->special_price * $total_qty);

				// calculate total coupon discount
				if ($value->coupon_discount != null) {
					$coupon_discount += round(($value->special_price * $total_qty * $value->coupon_discount) / 100, 2);
				}

			}
		}
		$grand_total = 0;

		if ($count <= 0) {
			// calculate shipping charge
			$amount = $total_amount - $coupon_discount;
			$fee = ShippingCharge::whereRaw("? between min_price and max_price", [$amount])->where('status', 'AC')->first();

			if (isset($fee->id)) {
				$shipping_fee = $fee->shipping_charge;
			}
			$user = User::find($user_id);

			$credits_used = (($amount + $shipping_fee) >= $user->wallet_amount) ? $user->wallet_amount : ($amount + $shipping_fee);

			if ($order->wallet == "0") {
				$credits_used = 0;
			}

			$savings = round(($coupon_discount + $credits_used), 2);
			$grand_total = round(($total_amount + $shipping_fee - ($savings)), 2);

			$order->total_amount = sprintf('%.2f', $total_amount);
			$order->coupon_discount = sprintf('%.2f', $coupon_discount);
			$order->shipping_fee = sprintf('%.2f', $shipping_fee);
			$order->grand_total = sprintf('%.2f', $grand_total);
			$order->savings = sprintf('%.2f', $savings);
			$order->credits_used = sprintf('%.2f', $credits_used);
			$order->save();
		}
		return $order;
	}

	/**
	 * ccavenue request handler
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function ccavRequestHandler(Request $request) {

		$settings = $this->ccavenSettings;
		$data = array(
			'tid' => $request->tid,
			'merchant_id' => $request->merchant_id,
			'order_id' => $request->order_id,
			'currency' => $request->currency,
			'amount' => $request->amount,
			'redirect_url' => $request->redirect_url,
			'cancel_url' => $request->cancel_url,
			'language' => $request->language,
			'user_id' => $request->user_id,
			'billing_name' => $request->billing_name,
			'billing_tel' => $request->billing_tel,
			'billing_email' => $request->billing_email,
		);

		$merchant_data = '';
		$working_key = $settings['ccavenue_working_key']; //Shared by CCAVENUES
		$access_code = $settings['ccavenue_access_code']; //Shared by CCAVENUES

		foreach ($data as $key => $value) {
			$merchant_data .= $key . '=' . urlencode($value) . '&';
		}
		$custom_code = $request->custom_code;
		$encrypted_data = $this->ccaven->encrypt($merchant_data, $working_key); // Method for encrypting the data.

		return view('ccavRequestHandler', compact('data', 'settings', 'access_code', 'encrypted_data', 'custom_code'));
	}

	/**
	 * ccavenue response handler
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function paymentSuccess(Request $request) {

		$settings = $this->ccavenSettings;
		$working_key = $settings['ccavenue_working_key']; //Shared by CCAVENUES
		$access_code = $settings['ccavenue_access_code']; //Shared by CCAVENUES

		$encResponse = $request->encResp; //This is the response sent by the CCAvenue Server
		$rcvdString = $this->ccaven->decrypt($encResponse, $working_key); //Crypto Decryption used as per the specified working key.
		$order_status = "";
		$decryptValues = explode('&', $rcvdString);
		$dataSize = sizeof($decryptValues);

		for ($i = 0; $i < $dataSize; $i++) {
			$information = explode('=', $decryptValues[$i]);
			if ($i == 3) {
				$order_status = $information[1];
			}

		}
		// dd($decryptValues);
		$values = [];
		foreach ($decryptValues as $key) {
			$temp = explode('=', $key);
			$values[$temp[0]] = $temp[1];
		}

		if ($values['order_status'] == 'Success') {
			$order = Order::find($values['order_id']);
			$this->createFinalOrder($order);
		}
		// } else {
		// 	// continue with failure or cancel status

		// 	$order = Order::find($values['order_id']);
		// 	$order->status = 'CL';
		// 	$order->save();
		// }

		$transaction = Transaction::where('order_id', $values['order_id'])->first();
		$transaction->txn_id = $values['tracking_id'];
		$transaction->ccavenue_order_id = $values['tracking_id'];
		$transaction->ccavenue_mid = $settings['ccavenue_merchant_id'];
		$transaction->ccavenue_txnamount = $values['amount'];
		$transaction->ccavenue_currency = $values['currency'];
		$transaction->ccavenue_status = $values['order_status'];
		$transaction->ccavenue_respond = $rcvdString;
		$transaction->save();

		echo "Payment done Successfully.";
	}

	/**
	 * ccavenue response handler
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function paymentFailure(Request $request) {

		$settings = $this->ccavenSettings;
		$working_key = $settings['ccavenue_working_key']; //Shared by CCAVENUES
		$access_code = $settings['ccavenue_access_code']; //Shared by CCAVENUES

		$encResponse = $request->encResp; //This is the response sent by the CCAvenue Server
		$rcvdString = $this->ccaven->decrypt($encResponse, $working_key); //Crypto Decryption used as per the specified working key.
		$order_status = "";
		$decryptValues = explode('&', $rcvdString);
		$dataSize = sizeof($decryptValues);

		for ($i = 0; $i < $dataSize; $i++) {
			$information = explode('=', $decryptValues[$i]);
			if ($i == 3) {
				$order_status = $information[1];
			}

		}
		// dd($decryptValues);
		$values = [];
		foreach ($decryptValues as $key) {
			$temp = explode('=', $key);
			$values[$temp[0]] = $temp[1];
		}

		if ($values['order_status'] != 'Success') {

			// continue with failure or cancel status

			$order = Order::find($values['order_id']);
			$order->status = 'CL';
			$order->save();
		}

		$transaction = Transaction::where('order_id', $values['order_id'])->first();
		$transaction->txn_id = $values['tracking_id'];
		$transaction->ccavenue_order_id = $values['tracking_id'];
		$transaction->ccavenue_mid = $settings['ccavenue_merchant_id'];
		$transaction->ccavenue_txnamount = $values['amount'];
		$transaction->ccavenue_currency = $values['currency'];
		$transaction->ccavenue_status = $values['order_status'];
		$transaction->ccavenue_respond = $rcvdString;
		$transaction->save();

		echo "Payment Failed.";
	}

	public function createFinalOrder($order) {
		$user_id = $order->user_id;

		$cart = Cart::where('user_id', $user_id)->where('status', 'AC')->first();
		$user = User::find($user_id);

		$usercartsitem = CartItem::where('cart_id', $cart->id)->with(['product_items', 'product_variation', 'delivery_slot'])->whereHas('product_items', function ($q) {
			$q->where('status', 'AC');
			$q->whereHas('category', function ($qu) {
				$qu->where('status', 'AC');
			})->orWhereHas('subcategory', function ($qu) {
				$qu->where('status', 'AC');
			});
		})->where('status', 'AC')->get();
		if ($usercartsitem->count() > 0) {
			$credit_usage = (int) $order->wallet;
			$coupon_code_id = 0;

			$count = 0;
			$total_amount = $shipping_fee = $coupon_discount = 0;
			$order_items = [];

			foreach ($usercartsitem as $key => &$value) {

				// check if offer on any variation has changed
				$product_variation = $this->mycontr->checkProductOffer($value->product_variation_id);

				if (isset($product_variation->offer_id) && $product_variation->offer_id != $value->offer_id) {

					$value->offer_id = isset($product_variation->offer_id) ? $product_variation->offer_id : null;
					$value->special_price = $product_variation->discount_price;
					$value->price = $product_variation->price;
					$value->coupon_code_id = null;
					$value->coupon_discount = null;
				} elseif (!isset($product_variation->offer_id)) {
					$value->offer_id = null;
					$value->special_price = $product_variation->discount_price;
					$value->price = $product_variation->price;
				}

				// update coupon code in cart details if coupon code expires on is changed
				$coupon = $this->mycontr->checkProductCoupon($value->product_variation_id, $user_id, $value->qty);

				if ($coupon != null) {
					$value->coupon_code_id = $coupon['range'];
					$value->coupon_discount = $coupon['discount'];
					$coupon_code_id = $coupon['range'];
				}

				// check product availability
				$available_qty = $this->mycontr->checkProductAvailability($value->product_variation_id);

				if ($available_qty < $value->qty) {
					$value->qty = $available_qty;
					$value->save();
					$value->is_available = ($available_qty >= 1) ? 1 : 0;
					$value->warning_msg = ($available_qty > 1) ? ("Only " . $available_qty . " are left") : (($available_qty == 0) ? "Out of Stock" : "Only " . $available_qty . " is left");
					$count++;
				}

				if ($count <= 0) {
					$value->discount = $this->mycontr->calcDiscount($value->special_price, $value->price);

					$days = date_diff(date_create($value->start_date), date_create($value->end_date))->format('%a');
					$total_qty = ($days + 1) * $value->qty;

					// calculate total_amount
					$total_amount += ($value->special_price * $total_qty);

					// calculate total coupon discount
					if ($value->coupon_discount != null) {
						$coupon_discount += round(($value->special_price * $total_qty * $value->coupon_discount) / 100, 2);
					}

					// create new order item
					$item = new OrderItem;
					$item->order_id = $order->id;
					$item->product_id = $value->product_id;
					$item->product_variation_id = $value->product_variation_id;
					$item->qty = $value->qty;
					$item->price = $value->price;
					$item->special_price = $value->special_price;
					$item->offer_id = $value->offer_id;
					$item->coupon_code_id = $value->coupon_code_id;
					$item->coupon_discount = $value->coupon_discount;
					$item->scheduled = $value->scheduled;
					$item->start_date = $value->start_date;
					$item->end_date = $value->end_date;
					$item->delivery_slot_id = $value->delivery_slot_id;
					$item->status = 'PN';

					if ($item->save()) {
						$order_items[] = $item;
						$value->delete();

						// update product quantity once order is placed
						$var = ProductVariation::find($value->product_variation_id);
						$qty = $var->qty - $value->qty;
						$var->qty = ($qty >= 0) ? $qty : 0;
						$var->save();

						for ($i = 0; $i <= $days; $i++) {
							$schedule = new OrderDelivery;
							$schedule->order_id = $order->id;
							$schedule->order_item_id = $item->id;
							$schedule->order_date = date('Y-m-d', strtotime('+ ' . $i . 'days', strtotime($item->start_date)));
							$schedule->save();
						}
					}
				}
			}

			if ($count <= 0) {
				// calculate shipping charge
				$amount = $total_amount - $coupon_discount;
				$fee = ShippingCharge::whereRaw("? between min_price and max_price", [$amount])->where('status', 'AC')->first();

				if (isset($fee->id)) {
					$shipping_fee = $fee->shipping_charge;
				}
				$user = User::find($user_id);

				$credits_used = (($amount + $shipping_fee) >= $user->wallet_amount) ? $user->wallet_amount : ($amount + $shipping_fee);

				if ($order->wallet == "0") {
					$credits_used = 0;
				}

				// save entry in wallets table if used in order
				if ($order->wallet == "1") {
					$wallet = new Wallet;
					$wallet->user_id = $user_id;
					$wallet->type = 'debit';
					$wallet->amount = $credits_used;
					$wallet->description = 'Wallet amount of ' . $credits_used . ' was used to make the order ' . $order->order_no . 'on ' . date('d M,Y');
					$wallet->reason = 'order';
					$wallet->order_id = $order->id;
					$wallet->save();

					$user->wallet_amount = $user->wallet_amount - $credits_used;
					$user->save();
				}

				// if coupon code is applied, make entry in user_couponcode table
				if ($coupon_code_id != 0) {
					$usercoupon = new UserCouponCode;
					$usercoupon->user_id = $user_id;
					$usercoupon->coupon_code_id = $coupon_code_id;
					$usercoupon->order_id = $order->id;
					$usercoupon->save();
				}

				// send notification to user
				$orderCount = Order::where('user_id', $user_id)->count();

				if ($orderCount > 0) {
					$title = "Order Placed Successfully!";
					$description = "Congratulations! Your order has been placed. You can check Order # " . $order->order_no . " in My Orders Section.";
					$type = 'order';
				}
				$description = ($orderCount == 1) ? "Congratulations on your first order!!" : $description;

				Notify::sendNotification($user_id, 1, $title, $description, $type);
				// send notification to admin

				$title = "New Order";
				$description = "You have received a new order with order # " . $order->order_no . ".";
				$type = 'order';
				Notify::sendNotification(1, $user_id, $title, $description, $type);

				$data = array(
					'user' => $user->toArray(),
					'order' => $order->toArray(),
					'order_items' => $order_items,
					'email' => $user->email,
					'first_name' => $user->first_name,
					'last_name' => $user->last_name,
				);
				Notify::sendMail('emails.new_order_user', $data, 'New Order Placed');

				$admin = User::find(1);
				$data = array(
					'user' => $admin->toArray(),
					'order' => $order->toArray(),
					'order_items' => $order_items,
					'email' => $admin->email,
					'first_name' => $admin->first_name,
					'last_name' => $admin->last_name,
				);
				Notify::sendMail('emails.new_order_admin', $data, 'New Order Placed');

				$cart->delete();
			}
		}
		return $order;
	}

	//test method
	public function test() {
		return view('test');
	}
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		//
	}
}
