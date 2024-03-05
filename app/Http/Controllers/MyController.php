<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Library\Notify;
use App\Library\ResponseMessages;
use App\Model\BusRuleRef;
use App\Model\Cart;
use App\Model\CartItem;
use App\Model\Category;
use App\Model\CouponCode;
use App\Model\DeliverySlot;
use App\Model\Offer;
use App\Model\Order;
use App\Model\OrderDelivery;
use App\Model\OrderItem;
use App\Model\Product;
use App\Model\ProductFavourite;
use App\Model\ProductVariation;
use App\Model\ShippingCharge;
use App\Model\User;
use App\Model\UserCouponCode;
use App\Model\Wallet;
use Auth;
use DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use PDF;
use Session;
use URL;

class MyController extends Controller {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	/**
	 * @OA\Info(
	 *    title="Your super  ApplicationAPI",
	 *    version="2.0.0",
	 * )
	 *  @OA\Server(
	 *   url=L5_SWAGGER_CONST_HOST,
	 * ),
	 * @OA\Server(
	 *   url=L5_SWAGGER_CONST_WWW_HOST,
	 * ),
	 * @OA\Tag(
	 *     name="Authentication",
	 *     description="APIs related to User Authentication"
	 * ),
	 * @OA\Tag(
	 *     name="Account",
	 *     description="APIs related to User Account"
	 * ),
	 * @OA\Tag(
	 *     name="Cart",
	 *     description="APIs related to User Cart"
	 * ),
	 * @OA\Tag(
	 *     name="Order",
	 *     description="APIs related to User Orders"
	 * ),
	 * @OA\Tag(
	 *     name="Products",
	 *     description="APIs related to Products & Listing"
	 * ),
	 * @OA\Tag(
	 *     name="Booking Api",
	 *     description="APIs related to Hotel Booking"
	 * ),
	 * @OA\SecurityScheme(
	 *    securityScheme="bearerAuth",
	 *    type="https",
	 *    type="http",
	 *    scheme="bearer",
	 * )
	 */

	public $response = array(
		"status" => 500,
		"message" => "Internal server error",
	);
	public $currency;
	public $allowed;
	public $pagelength;

	public function __construct() {
		$this->currency = BusRuleRef::where('rule_name', 'currency')->first()->rule_value;
		$this->pagelength = BusRuleRef::where('rule_name', 'page_length')->first()->rule_value;
	}

	// function __destruct() {
	//     echo json_encode($this->response);
	// }

	public function shut_down() {
		return response()->json($this->response, $this->response['status']);
		// echo json_encode($this->response);
	}

	public function checkKeys($input = array(), $required = array()) {

		$existance = implode(", ", array_diff($required, $input));
		if (!empty($existance)) {
			if (count(array_diff($required, $input)) == 1) {
				$this->response = array(
					"status" => 400,
					"message" => $existance . " key is missing",
				);
			} else {
				$this->response = array(
					"status" => 400,
					"message" => $existance . " keys are missing",
				);
			}
			return $this->shut_down();
			exit;
		}
		return false;
	}

	public function checkUserActive($userId, Request $request = null) {

		if ($user = User::select("status", "is_verified")->where("id", $userId)->first()) {

			if ($user->status == "AC") {
				if ($user->is_verified == 1) {
					return true;
				} else {
					$this->response = array(
						"status" => 400,
						"message" => ResponseMessages::getStatusCodeMessages(145),
					);
					return $this->shut_down();
					exit;
				}
			} else {
				$user->device_id = "";
				$user->save();
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(216),
				);
				if (strpos($request, '/api') !== false) {
					return $this->shut_down();
					exit;
				} else {
					auth()->guard('front')->logout();
					Session::flush('user');
					return $this->response;
				}
			}
		} else {
			$this->response = array(
				"status" => 400,
				"message" => ResponseMessages::getStatusCodeMessages(321),
			);
			return $this->shut_down();
			exit;
		}
	}

	public function checkLoginCookie($cookie) {

		if (Auth::check()) {

			if (!empty($cookie) && Auth::user()->login_cookie != $cookie) {
				$user = Auth::user();
				$user->device_id = "";
				$user->save();
				Auth::user()->token()->revoke();
				setcookie("login_session", "", time() - 3600);
				$this->response = array(
					"status" => 401,
					"message" => 'Unauthenticated.',
				);
				return $this->shut_down();
				exit;
			}
		}
		return true;
	}

	public function getReferMessage($user_id) {
		$user = User::select("referral_code")->where("id", $user_id)->first();
		$message = $this->getBusRuleRef("refer_share_message");
		$androidLink = $this->getBusRuleRef("android_url_user");
		$iosLink = $this->getBusRuleRef("ios_url_user");
		$referrerAmount = $this->getBusRuleRef("referrer_amount");
		// Register on VL Taxi with refer_code and earn Rs. refer_amount. Download on Android android_link or iOS ios_link
		$message = str_replace("refer_code", $user->referral_code, $message);
		$message = str_replace("refer_amount", $referrerAmount, $message);
		$message = str_replace("android_link", $androidLink, $message);
		$message = str_replace("ios_link", $iosLink, $message);
		return $message;
	}

	public function updateUserStatus($userId, $status) {
		$user = User::where("id", $userId)->update(["user_status" => $status]);
	}

	public function calcDiscount($special, $price) {
		return ($special != 0) ? round((($price - $special) * 100 / $price)) : 0;
	}

	public function getCartCount($user_id) {

		$cart = Cart::where(['user_id' => $user_id, 'status' => 'AC'])->first();
		$cartcount = 0;
		if ($cart) {
			$query = CartItem::selectRaw('sum(qty) as total')->where('cart_id', $cart->id)->whereHas('product_items', function ($q) {
				$q->where('status', 'AC');
				$q->whereHas('category', function ($qu) {
					$qu->where('status', 'AC');
				})->orWhereHas('subcategory', function ($qu) {
					$qu->where('status', 'AC');
				});
			})->groupBy('cart_id')->where('status', 'AC')->first();

			if ($query) {
				$cartcount = $query->total;
			}
		}

		return intval($cartcount);
	}

	public function fetchOfferProducts($offers, $array, $page_no, Request $request = null) {
		// print_r($offers);die();
		$products = Product::with(['variations' => function ($q) {
			$q->where('status', 'AC');
			$q->orderBy('price', 'asc');
		}])->select('*', DB::raw("CONCAT('" . URL::asset("uploads/products/medium") . "/medium-', image) as medium_image, image"),
			DB::raw("CONCAT('" . URL::asset("uploads/products") . "/', image) image"))
			->where('status', 'AC')->whereHas('variations', function ($q) {
			$q->where('status', 'AC');
		})->where(function ($qu) {
			$qu->whereHas('category', function ($quw) {
				$quw->where('status', 'AC');
			})->orWhereHas('subcategory', function ($quw) {
				$quw->where('status', 'AC');
			});
		})->where(function ($q) use ($array) {
			$q->whereIN('parent_id', $array);
		})->where('status', 'AC');

		if (strpos($request, '/api') !== false) {
			$products = $products->orderBy('created_at', 'desc')->offset($page_no * $this->pagelength)->limit($this->pagelength)->get();
		} else {
			$products = $products->paginate();
		}

		// check products match with all offers minimum amount
		foreach ($products as $k => &$value) {
			$category = Category::find($value->parent_id);
			$offer_min = 0;
			// get offer minimum amount with the product category
			foreach ($offers as $key) {
				$cat = explode(',', $key->category_id);
				if (in_array($category->id, $cat)) {
					$offer_min = $key->min_amount;
					break;
				}
			}

			// if category is not present, check offer with parent category and get minimum amount
			if ($offer_min == 0) {
				$sub = $category;
				foreach ($offers as $key) {
					$cat = explode(',', $key->category_id);
					while (isset($sub->parentCat)) {

						if (isset($sub->parentCat->id) && in_array($sub->parentCat->id, $cat)) {
							$offer_min = $key->min_amount;
							break;
						}
						$sub = $category->parentCat;
					}
				}
			}

			foreach ($value->variations as $kv => &$var) {
				$price = ($var->special_price != 0) ? $var->special_price : $var->price;

				if (($offer_min == 0) || ($price < $offer_min)) {
					$value->variations->forget($kv);
				}
			}
			if (count($value->variations) <= 0) {
				unset($products[$k]);
			}
		}
		return $products;
	}

	public function formatProducts($products, $user_id, $type = '') {
		$cart_exists = 0;

		//check if logged in user has cart
		if ($user_id != 0) {
			$cart = Cart::where('user_id', $user_id)->where('status', 'AC')->first();
			$cart_exists = ($cart != null) ? $cart->count() : 0;
		}
		$varid = 0;
		foreach ($products as &$value) {
			unset($value->brand_id);
			unset($value->parent_id);
			unset($value->tax_id);
			unset($value->type);
			unset($value->created_at);
			unset($value->updated_at);
			if ($cart_exists > 0 && $type != 'fav') {
				//update cart count for product to show on home page and in variation popup
				$query = CartItem::with('product_variation')->where(['cart_id' => $cart->id, 'product_id' => $value->id, 'status' => 'AC']);
				$cart_items = $query->orderBy('updated_at', 'desc')->get();

				if (count($cart_items) > 0) {

					$value->cart_count = $cart_items[0]->qty;
					$value->price = $cart_items[0]->price;
					//new
					$discount_price = $this->getDiscountPrice($cart_items[0]->product_variation);
					$value->special_price = $discount_price;
					$value->discount = $this->calcDiscount($discount_price, $cart_items[0]->price);
					// $value->special_price = $cart_items[0]->special_price;
					// $value->discount = $this->calcDiscount($cart_items[0]->special_price, $cart_items[0]->price);
					//old

					$value->product_variation_option = $cart_items[0]->product_variation->variationOption->pluck('attribute_value_id')->toArray();

					// dd($product_variation_option);

					$varid = $cart_items[0]->product_variation->id;
					$value->product_variation_id = $cart_items[0]->product_variation->id;
					$variations = $value->variations->values();
					unset($value->variations);
					$value->variations = $variations;
					foreach ($value->variations as &$var) {
						$cart_var = CartItem::with('product_variation')->where(['cart_id' => $cart->id, 'product_id' => $value->id, 'status' => 'AC'])->where('product_variation_id', $var->id)->orderBy('updated_at', 'desc')->first();

						if (isset($cart_var->id)) {
							$var->cart_count = $cart_var->qty;
							$var->price = $cart_var->price;
							//new
							$discount_price = $this->getDiscountPrice($cart_var->product_variation);
							$var->special_price = $discount_price;
							$var->discount = $this->calcDiscount($discount_price, $cart_var->price);
							// $var->special_price = $cart_var->special_price;
							// $var->original_special_price = $cart_var->special_price; //new
							// $var->discount = $this->calcDiscount($cart_var->special_price, $cart_var->price);
							// $var->weight = $cart_var->product_variation->weight . "" . $cart_var->product_variation->product_units->unit;
							$var->max_qty = ($var->max_qty <= $var->qty) ? $var->max_qty : $var->qty;
							// print_r($cart_var);
							if ($var->qty < $cart_var->qty) {
								$var->is_available = ($var->qty >= 1) ? 1 : 0;
								$var->warning_msg = ($var->qty > 1) ? ("Only " . $var->qty . " are left") : (($var->qty == 0) ? "Out of Stock" : "Only " . $var->qty . " is left");
								/*if ($var->qty == 0) {
									                            $cart_var->delete();
								*/
							} else {
								if ($var->qty <= 0) {
									$var->is_available = 0;
									$var->warning_msg = 'Out of Stock';
								} else {
									$var->is_available = 2;
								}

							}
						} else {

							$var->cart_count = 0;
							$discount_price = $this->getDiscountPrice($var);
							$var->special_price = $discount_price;
							$var->original_special_price = $var->special_price; //new
							// $var->weight = $var->weight . "" . $var->product_units->unit;
							$var->discount = $this->calcDiscount($discount_price, $var->price);
							if ($var->qty <= 0) {
								$var->is_available = 0;
								$var->warning_msg = 'Out of Stock';
							} else {
								$var->is_available = 2;
							}
							$var->max_qty = ($var->max_qty <= $var->qty) ? $var->max_qty : $var->qty;
						}
						$fav = ProductFavourite::where('product_id', $value->id)->where('product_variation_id', $var->id)->where('user_id', $user_id)->whereStatus('AC')->count();
						$var->is_favorite = $fav > 0 ? 1 : 0;
						unset($var->product_units);
					}
				} else {
					//if cart is not there, then update details with first variation with lowest price

					$value->cart_count = 0;

					// dd($value->variations->toArray());

					foreach ($value->variations as $v => $i) {
						$k = $v;
						break;
					}

					// if ($value->variations->count() <= 0) {
					//     dd($value);
					// }

					$discount_price = $this->getDiscountPrice($value->variations[$k]) ?? 0;
					$value->price = $value->variations[$k]->price ?? 0;
					$value->special_price = $discount_price ?? 0;
					$value->original_special_price = $value->variations[$k]->special_price ?? 0; //new
					$value->discount = $this->calcDiscount($discount_price, $value->variations[$k]->price) ?? 0;
					$value->product_variation_option = $value->variations[$k]->variationOption->pluck('attribute_value_id')->toArray();

					// $value->weight = $value->variations[$k]->weight . "" . $value->variations[$k]->product_units->unit;
					$value->product_variation_id = $value->variations[$k]->id;
					$varid = $value->variations[$k]->id ?? '';

					$variations = $value->variations->values();
					unset($value->variations);
					$value->variations = $variations;
					foreach ($value->variations as &$var) {
						$var->cart_count = 0;
						$discount_price = $this->getDiscountPrice($var);
						$var->special_price = $discount_price;
						$var->original_special_price = $var->special_price; //new
						// $var->weight = $var->weight . "" . $var->product_units->unit;
						$var->discount = $this->calcDiscount($discount_price, $var->price);
						if ($var->qty <= 0) {
							$var->is_available = 0;
							$var->warning_msg = 'Out of Stock';
						} else {
							$var->is_available = 2;
						}
						$fav = ProductFavourite::where('product_id', $value->id)->where('product_variation_id', $var->id)->where('user_id', $user_id)->whereStatus('AC')->count();
						$var->is_favorite = $fav > 0 ? 1 : 0;

						$var->max_qty = ($var->max_qty <= $var->qty) ? $var->max_qty : $var->qty;
						unset($var->product_units);
					}
				}
			} else {
				$value->cart_count = 0;
				// $k = array_key_first($value->variations->toArray());

				$variations = $value->variations->values();
				unset($value->variations);
				$value->variations = $variations;
				foreach ($value->variations as $v => $i) {
					$k = $v;
					break;
				}

				if (!isset($value->variations[$k])) {
					dd($value);
				}
				// $discount_price = $this->getDiscountPrice($value->variations[$k]) ?? 0;

				$discount_price = $this->getDiscountPrice($value->variations[$k]) ? $this->getDiscountPrice($value->variations[$k]) : '';

				$value->price = $value->variations[$k]->price;
				$value->special_price = $discount_price;
				$value->original_special_price = $value->variations[$k]->special_price; //new
				$value->discount = $this->calcDiscount($discount_price, $value->variations[$k]->price);
				$value->product_variation_option = $value->variations[$k]->variationOption->pluck('attribute_value_id')->toArray();

				$value->product_variation_id = $value->variations[$k]->id;
				$varid = $value->variations[$k]->id;

				foreach ($value->variations as &$var) {

					if ($type == 'fav' && $cart_exists > 0) {
						$count = CartItem::where(['cart_id' => $cart->id, 'product_id' => $value->id, 'status' => 'AC', 'product_variation_id' => $var->id])->first();
						if (isset($count->qty)) {
							$var->cart_count = $count->qty;
							$value->cart_count = $count->qty;
						} else {
							$var->cart_count = 0;
							$value->cart_count = 0;
						}
					} else {
						$var->cart_count = 0;
						$value->cart_count = 0;
					}
					$discount_price = $this->getDiscountPrice($var);
					$var->special_price = $discount_price;
					$var->original_special_price = $var->special_price; //new
					// $var->weight = $var->weight ?? '' . "" . $var->product_units->unit ?? '';
					$var->discount = $this->calcDiscount($discount_price, $var->price);
					$var->max_qty = ($var->max_qty <= $var->qty) ? $var->max_qty : $var->qty;
					if ($var->qty <= 0) {
						$var->is_available = 0;
						$var->warning_msg = 'Out of Stock';
					} else {
						$var->is_available = 2;
					}
					$fav = ProductFavourite::where('product_id', $value->id)->where('product_variation_id', $var->id)->where('user_id', $user_id)->whereStatus('AC')->count();
					$var->is_favorite = $fav > 0 ? 1 : 0;

					unset($var->product_units);
				}
				// print_r($value->variations);
			}

			$fav = ProductFavourite::where('product_id', $value->id)->where('product_variation_id', $varid)->where('user_id', $user_id)->whereStatus('AC')->count();
			$value->is_favorite = $fav > 0 ? 1 : 0;

		}
		// die();
		// print_r($products);die();
		return $products;
	}

	public function getDiscountPrice($var) {
		$category = Category::whereHas('products', function ($q) use ($var) {
			$q->whereHas('variations', function ($qu) use ($var) {
				$qu->where('id', $var->id);
			});
		})->first();

		if (isset($category->id)) {
			$offer = Offer::whereRaw('FIND_IN_SET(?,category_id)', [$category->id])->whereRaw('? between start_date and end_date', [date('Y-m-d')])->where('status', 'AC')->orderBy('created_at', 'desc')->first();

			$sub = $category;

			while (!isset($offer->id)) {

				$temp = Category::where('id', $sub->parent_id)->first();
				if (isset($temp->id)) {

					$offer = Offer::whereRaw('FIND_IN_SET(?,category_id)', [$category->parentCat->id])->whereRaw('? between start_date and end_date', [date('Y-m-d')])->where('status', 'AC')->orderBy('created_at', 'desc')->first();
					if (isset($offer->id)) {
						break;
					}
				} else {
					break;
				}
				$sub = $temp;
			}
		}

		$discount_price = ($var->special_price != 0) ? $var->special_price : $var->price;
		if (isset($offer) && $discount_price >= $offer->min_amount) {
			$disc = $offer->value * $discount_price / 100;
			$discount = (100 - $offer->value) / 100 * $discount_price;
			$discount_price = ($disc <= $offer->max_amount) ? $discount : ($discount_price - $offer->max_amount);
		}
		return sprintf('%.2f', $discount_price);
	}

	public function checkProductAvailability($id) {
		$var = ProductVariation::find($id);
		return $var->qty;

	}

	public function getCouponCatSubcat($code) {
		$categories = explode(',', $code->category_id);

		// check if products in the cart lies within the category of coupon code entered

		// get subcategories of the code categories if exists and remove those which are present in other coupon codes
		$subcategories = Category::whereIN('parent_id', $categories)->pluck('id')->toArray();

		if (count($subcategories) > 0) {
			foreach ($subcategories as $key => $value) {

				$temp = Category::where('parent_id', $value)->pluck('id')->toArray();
				if (count($temp) > 0) {
					$subcategories = array_merge($subcategories, $temp);
				}

				if (CouponCode::whereRaw("FIND_IN_SET(?,category_id)", [$value])->where('status', 'AC')->count() > 0) {
					unset($subcategories[$key]);
				}
			}
		}
		// merge categories and subcategories and check the cart items that exists within these.
		$categories = array_merge($categories, $subcategories);
		return $categories;
	}

	public function checkProductCoupon($id, $user_id, $qty) {
		// $var = ProductVariation::find($id);
		$product = Product::whereHas('variations', function ($q) use ($id) {
			$q->where('id', $id);
		})->first();

		$category = Category::where('id', $product->parent_id)->first();
		$subcategory = Category::where('parent_id', $product->parent_id)->first();
		$code = null;
		$cart = Cart::where('user_id', $user_id)->where('status', 'AC')->first();

		if ($category) {
			$code = CouponCode::with(['coupon_range' => function ($q) {
				$q->where('status', 'AC');
			}])->whereRaw("FIND_IN_SET(?,category_id)", [$category->id])->whereRaw("? between start_date and end_date", [date('Y-m-d')])->where('status', 'AC')->first();
			$sub = $category;

		}

		if (!isset($code->id) && $subcategory) {
			$code = CouponCode::with(['coupon_range' => function ($q) {
				$q->where('status', 'AC');
			}])->whereRaw("FIND_IN_SET(?,category_id)", [$subcategory->id])->whereRaw("? between start_date and end_date", [date('Y-m-d')])->where('status', 'AC')->first();
			$sub = $subcategory;
		}

		while (!isset($code->id)) {

			$temp = Category::where('id', $sub->parent_id)->first();
			if (isset($temp->id)) {

				$code = CouponCode::with(['coupon_range' => function ($q) {
					$q->where('status', 'AC');
				}])->whereRaw("FIND_IN_SET(?,category_id)", [$temp->id])->whereRaw("? between start_date and end_date", [date('Y-m-d')])->where('status', 'AC')->first();
				if (isset($code->id)) {
					break;
				}
			} else {
				break;
			}
			$sub = $temp;
		}

		if ($code) {

			// create coupon range array from the code relation
			$coupon_range = array_column($code->coupon_range->toArray(), 'id');

			// check if cart contains any item with coupon code applied
			$cart_coupon = CartItem::where('cart_id', $cart->id)->where('status', 'AC')->whereIN('coupon_code_id', $coupon_range)->first();

			if (isset($cart_coupon->coupon_code_id)) {

				// check usage of coupon code
				$query = Order::whereHas('order_items', function ($q) use ($code) {
					$q->whereHas('coupon_range', function ($qu) use ($code) {
						$qu->where('coupon_code_id', $code->id);
					});
				});

				$total_count = $query->count();
				$user_count = $query->where('user_id', $user_id)->count();

				if ($total_count < $code->max_use_total && $user_count < $code->max_use) {
					$cartitem = CartItem::where('cart_id', $cart->id)->where('product_variation_id', $id)->where('status', 'AC')->first();
					$range = 0;
					$discount = 0;
					// check if product special price lies within the coupon code range
					foreach ($code->coupon_range as $k => $v) {
						if (($cartitem->special_price * $qty) >= $v->min_price && ($cartitem->special_price * $qty) <= $v->max_price) {
							$range = $v->id;
							$discount = $v->value;
							break;
						}
					}
					if ($range != 0) {
						return ['range' => $range, 'discount' => $discount];
					} else {
						return null;
					}
				}

			}
		}

		return null;

	}

	public function checkProductOffer($id) {
		$var = ProductVariation::find($id);
		$category = Category::whereHas('products', function ($q) use ($var) {
			$q->whereHas('variations', function ($qu) use ($var) {
				$qu->where('id', $var->id);
			});
		})->first();

		$offer = Offer::whereRaw('FIND_IN_SET(?,category_id)', [$category->id])->whereRaw('? between start_date and end_date', [date('Y-m-d')])->where('status', 'AC')->orderBy('created_at', 'desc')->first();
		$sub = $category;

		/*if (!isset($offer->id) && isset($category->parentCat->id)) {

			        $offer = Offer::whereRaw('FIND_IN_SET(?,category_id)', [$category->parentCat->id])->whereRaw('? between start_date and end_date', [date('Y-m-d')])->where('status', 'AC')->orderBy('created_at', 'desc')->first();
		*/

		while (!isset($offer->id)) {

			$temp = Category::where('id', $sub->parent_id)->first();
			if (isset($temp->id)) {

				$offer = Offer::whereRaw('FIND_IN_SET(?,category_id)', [$category->parentCat->id])->whereRaw('? between start_date and end_date', [date('Y-m-d')])->where('status', 'AC')->orderBy('created_at', 'desc')->first();
				if (isset($offer->id)) {
					break;
				}
			} else {
				break;
			}
			$sub = $temp;
		}

		$discount_price = ($var->special_price != 0) ? $var->special_price : $var->price;
		if ($offer && $discount_price >= $offer->min_amount) {
			$disc = $offer->value * $discount_price / 100;
			$discount = (100 - $offer->value) / 100 * $discount_price;
			$discount_price = ($disc <= $offer->max_amount) ? $discount : ($discount_price - $offer->max_amount);
			$var->offer_id = $offer->id;
		}
		$var->discount_price = sprintf('%.2f', $discount_price);
		return $var;

	}

	public function createFinalOrder($cart, $usercartsitem, $user_id) {

		if ($usercartsitem->count() > 0) {
			$credit_usage = (int) $cart->wallet;
			$coupon_code_id = 0;

			// create new order
			$order = new Order;
			$order->order_code = Helper::generateNumber('orders', 'order_code');
			$order->order_no = Helper::generateNumberOrder();
			$order->user_id = $user_id;
			$order->address_id = $cart->address_id;
			$order->payment_method = $cart->payment_method;
			$order->wallet = $cart->wallet;

			$order->year = $cart->year;
			$order->month = $cart->month;
			$order->card_no = $cart->card_no;
			$order->cvv = $cart->cvv;
			$order->card_type = $cart->card_type;

			$order->save();

			unset($order->year);
			unset($order->month);
			unset($order->card_no);
			unset($order->cvv);
			unset($order->card_type);

			$count = 0;
			$total_amount = $shipping_fee = $coupon_discount = 0;
			$product_saving = 0;
			$order_items = [];

			foreach ($usercartsitem as $key => &$value) {

				// check if offer on any variation has changed
				$product_variation = $this->checkProductOffer($value->product_variation_id);

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
				$coupon = $this->checkProductCoupon($value->product_variation_id, $user_id, $value->qty);

				if ($coupon != null) {
					$value->coupon_code_id = $coupon['range'];
					$value->coupon_discount = $coupon['discount'];
					$coupon_code_id = $coupon['range'];
				} else {
					$value->coupon_code_id = null;
					$value->coupon_discount = null;
					$coupon_code_id = 0;
				}

				// check product availability
				$available_qty = $this->checkProductAvailability($value->product_variation_id);

				if ($available_qty < $value->qty) {
					$value->qty = $available_qty;
					$value->save();
					$value->is_available = ($available_qty >= 1) ? 1 : 0;
					$value->warning_msg = ($available_qty > 1) ? ("Only " . $available_qty . " are left") : (($available_qty == 0) ? "Out of Stock" : "Only " . $available_qty . " is left");
					$count++;
				}

				if ($count <= 0) {
					$value->discount = $this->calcDiscount($value->special_price, $value->price);

					$days = date_diff(date_create($value->start_date), date_create($value->end_date))->format('%a');
					$total_qty = ($days + 1) * $value->qty;

					// calculate total_amount
					$total_amount += ($value->special_price * $total_qty);

					//calculate product saving
					$product_saving += ($value->price - $value->special_price) * $total_qty;

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

				$savings = round(($coupon_discount + $credits_used), 2);
				$grand_total = round(($total_amount + $shipping_fee - ($savings)), 2);

				// save calculations in order table
				$order->total_amount = sprintf('%.2f', $total_amount);
				$order->coupon_discount = sprintf('%.2f', $coupon_discount);
				$order->shipping_fee = sprintf('%.2f', $shipping_fee);
				$order->grand_total = sprintf('%.2f', $grand_total);
				$order->savings = sprintf('%.2f', $savings + $product_saving);
				$order->credits_used = sprintf('%.2f', $credits_used);

				if ($order->save()) {

					// save entry in wallets table if used in order
					if ($order->wallet == "1" && (int) $order->credits_used != 0) {
						$wallet = new Wallet;
						$wallet->user_id = $user_id;
						$wallet->type = 'debit';
						$wallet->amount = $credits_used;
						$wallet->description = 'Wallet amount of ' . $this->currency . $credits_used . ' was used to make the order ' . $order->order_no . 'on ' . date('d M,Y');
						$wallet->reason = 'order';
						$wallet->order_id = $order->id;
						$wallet->save();

						$user->wallet_amount = $user->wallet_amount - $credits_used;
						$user->save();

						// send notification to user in case of wallet amount used

						$title = "Wallet amount used in Order";
						$description = "You have used wallet amount of " . $this->currency . $order->credits_used . " to create a new order with order # " . $order->order_no . ".";
						$type = 'wallet';
						Notify::sendNotification($user_id, 1, $title, $description, $type);
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
						$description = "Congratulations! Your order has been placed. You can check Order # " . $order->order_no . ' in My Orders Section.';
						$type = 'order';
					}
					// $description = ($orderCount == 1) ? "Congratulations on your first order!!" : $description;

					Notify::sendNotification($user_id, 1, $title, $description, $type);
					// send notification to admin

					$title = "New Order";
					$description = "You have received a new order with # " . $order->order_no . ".";
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
						'currency' => $this->currency,
						'customer_care' => Notify::getBusRuleRef('customer_care_number'),
						'email_support' => Notify::getBusRuleRef('customer_care_email'),
					);

					$pdf = PDF::loadView('emails.order_invoice', $data);
					\Storage::put('public/pdf/' . $order->order_no . '.pdf', $pdf->output());

					$data['file'] = storage_path('app/public/pdf/' . $order->order_no . '.pdf');

					Notify::sendMail("emails.invoice", $data, "Order Invoice #" . $order->order_no . "- omrbranch");

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

					// $cart->delete();

					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(14),
						"currency" => $this->currency,
						"data" => $order,
						"order_id" => $order->id,
					);
				} else {
					$this->response = array(
						"status" => 501,
						"message" => ResponseMessages::getStatusCodeMessages(53),
					);
				}
			} else {
				$this->response = array(
					'status' => 234,
					'message' => ResponseMessages::getStatusCodeMessages(51),
				);
			}

		} else {
			$this->response = array(
				"status" => 404,
				"message" => ResponseMessages::getStatusCodeMessages(25),
			);
		}
	}

	public function getBusRuleRef($rule_name) {
		if ($BusRuleRef = BusRuleRef::where("rule_name", $rule_name)->first()) {
			return $BusRuleRef->rule_value;
		}
	}

	public function getAppInfo() {
		if ($BusRuleRef = BusRuleRef::select("rule_name", "rule_value")->whereIn("rule_name", array('ios_update_driver', 'android_update_driver', 'android_url_driver', 'ios_url_driver', 'ios_version_driver', 'android_version_driver', 'ios_update_user', 'android_update_user', 'android_url_user', 'ios_url_user', 'ios_version_user', 'android_version_user', 'app_update_msg'))->get()) {
			return $BusRuleRef;
		}
	}

	public function checkSlot($cartitem) {
		$cart = Cart::whereHas('cart_items', function ($q) use ($cartitem) {
			$q->where('id', $cartitem->id);
		})->where('status', 'AC')->first();

		if ($cart) {
			if ($cartitem->scheduled == '0') {
				CartItem::where(['scheduled' => '0', 'status' => 'AC', 'cart_id' => $cart->id])->update(['scheduled' => '0', 'start_date' => $cartitem->start_date, 'end_date' => $cartitem->end_date, 'delivery_slot_id' => $cartitem->delivery_slot_id]);
			} elseif ($cartitem->scheduled == '1') {
				CartItem::where(function ($qu) use ($cartitem) {
					$qu->where(function ($q) use ($cartitem) {
						$q->where('start_date', '<=', $cartitem->start_date);
						$q->where('end_date', '>=', $cartitem->start_date);
					})->orWhere(function ($que) use ($cartitem) {
						$que->where(function ($q) use ($cartitem) {
							$q->where('start_date', '<=', $cartitem->end_date);
							$q->where('end_date', '>=', $cartitem->end_date);
						})->orWhere(function ($q) use ($cartitem) {
							$q->where('start_date', '<=', $cartitem->end_date);
							$q->where('start_date', '>=', $cartitem->start_date);
						})->orWhere(function ($q) use ($cartitem) {
							$q->where('end_date', '<=', $cartitem->end_date);
							$q->where('end_date', '>=', $cartitem->start_date);
						});
					});
				})->where('scheduled', '1')->where('status', 'AC')->update(['scheduled' => '1', 'delivery_slot_id' => $cartitem->delivery_slot_id]);
			}
		}
		return $cartitem;
	}
	/*
		    public function getRecentAvailableTime() {
		    $arr = array();
		    for ($i = 7; $i <= 13; $i++) {

		    $result = DeliverySlot::where('type', 'single')->select('id')->selectRaw('DATE_FORMAT(NOW() + INTERVAL (? - DAYOFWEEK(NOW())) DAY,"%Y-%m-%d") as date', [$i])->whereRaw('day = DATE_FORMAT(NOW() + INTERVAL (? - DAYOFWEEK(NOW())) DAY,"%a")', [$i])->where('status', 'AC')->orderBy('from_time')->first();
		    if (isset($result->id)) {
		    $arr = array(
		    'id' => $result->id,
		    'date' => $result->date,
		    );
		    break;
		    }
		    }
		    return $arr;
		    }
	*/

	public function getRecentAvailableTime() {
		$arr = array();

		$i = 3;
		// get recent available date for single day delivery which has not exceeded the max order limit
		do {
			$date = date('Y-m-d', strtotime('+ ' . $i . ' days'));
			$day = strtolower(date('D', strtotime('+ ' . $i . ' days')));

			$slot = DeliverySlot::where('day', $day)->where(function ($q) use ($date) {
				if ($date == date('Y-m-d')) {
					$q->whereRaw('? between from_time and to_time', [date('H:i:s')]);
				}
			})->whereType('single')->whereStatus('AC')->with(['product_orders' => function ($q) use ($date) {
				$q->select('delivery_slot_id', DB::Raw('COALESCE(count(id),0) as total'))->where('start_date', $date)->where('end_date', $date)->whereStatus('PN')->groupBy('delivery_slot_id');
			}])->first();

			if (isset($slot->id)) {
				if (!isset($slot->product_orders->total) || (isset($slot->product_orders->total) && $slot->product_orders->total < $slot->max_order)) {
					$arr['id'] = $slot->id;
					$arr['date'] = $date;
				}
			}
			$i++;

		} while (!isset($arr['date']));

		return $arr;
	}

	public function fetchDateProducts($curr_date, $order_id) {

		$result = OrderItem::where('order_id', $order_id)->with(['order_schedule' => function ($qu) use ($curr_date) {
			$qu->where('order_date', $curr_date)->orWhere('reschedule_date', $curr_date);
		}])->whereHas('order_schedule', function ($qu) use ($curr_date) {
			$qu->where('order_date', $curr_date)->orWhere('reschedule_date', $curr_date);
		})->with(['product_items', 'product_variation'])->get();

		$products = [];

		foreach ($result as $key => &$value) {
			$item = new OrderItem;
			$item->id = $value->id;
			$item->image = URL::asset('uploads/products/' . $value->product_items->image);
			$item->medium_image = URL::asset('uploads/products/medium/medium-' . $value->product_items->image);
			$item->name = $value->product_items->name;
			//$item->weight = $value->product_variation->weight . $value->product_variation->product_units->unit;
			$item->price = $value->price;
			$item->special_price = $value->special_price;
			$item->start_date = $value->start_date;
			$item->end_date = $value->end_date;
			$item->scheduled = (int) $value->scheduled;
			$item->status = $this->getItemStatus($value->order_schedule[0]->status);
			if ($value->order_schedule[0]->reschedule_date != null) {
				if ($value->order_schedule[0]->reschedule_date == $curr_date) {
					$item->status = ($this->getItemStatus($value->order_schedule[0]->status) == 'pending') ? 'rescheduled' : $this->getItemStatus($value->order_schedule[0]->status);
				} else {
					$item->status = 'cancelled';
				}
			}
			$products[] = $item;
		}
		return $products;

	}

	public function getItemStatus($status) {
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
		case 'RN':
			$status = 'returned';
			break;
		case 'RFIN':
			$status = 'cancel initiated';
			break;
		case 'UN';
			$status = 'undelivered';
			break;
		default:
			$status = 'pending';
			break;
		}
		return $status;
	}

	public function getItemPrice($item) {
		$price = $item->special_price;
		if ($item->coupon_code_id != null) {
			$price = (100 - $item->coupon_discount) * $price / 100;
		}
		return $price;
	}

}
