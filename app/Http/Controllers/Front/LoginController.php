<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\ApiController as ApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MyController as MyController;
use App\Library\Helper;
use App\Library\Notify;
use App\Library\ResponseMessages;
use App\Model\Hotel;
use App\Model\Ticket;
use App\Model\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Session;
use Socialite;
use URL;

class LoginController extends Controller {
	public function __construct(Request $request) {
		$this->api = (new ApiController($request));
		$this->function = (new MyController($request));
	}

	protected function guard() {
		return auth()->guard('front');
	}

	// public function signin() {
	// 	return view('front.login');
	// }

	public function index(Request $request) {
		if (Auth::guard('front')->user()) {
			$user_id = Auth::guard('front')->user()->id;

			$access = [];

			$user = Auth::guard('front')->user();

			if ($user->travel_access == 'yes') {
				$access[] = 'travel';
			}
			if ($user->flight_access == 'yes') {
				$access[] = 'flight';
			}
			if ($user->travel_with_api_access == 'yes') {
				$access[] = 'travel_api';
			}
			if ($user->grocery_front_access == 'yes') {
				$access[] = 'grocery';
			}
			if ($user->sample_api_access == 'yes') {
				$access[] = 'sample_api';
			}
			if ($user->database_access == 'yes') {
				$access[] = 'database';
			}
			if ($user->admin_panel_access == 'yes') {
				$access[] = 'admin_panel';
			}
			Session::put('access', $access);

			if (in_array('grocery', $access)) {
				if (in_array('travel', $access) || in_array('flight', $access)) {
					return view('front.page_selection');
				} else {
					$user = Auth::guard('front')->user()->load('user_addresses');
					$usercartsitem = [];
					$requestData = $request->merge(['user_id' => $user_id]);

					if (Auth::guard('front')->user()) {
						$items = json_decode($this->api->getCartItems($requestData));
						if ($items->status == 200) {
							$usercartsitem = $items->data;
						}
					}
					$user_id = $request->merge(['user_id' => $user_id]);

					$item = $this->api->homePageData($user_id);

					$sliders = $item['slider'] ? $item['slider'] : [];

					$currency = $item['currency'] ? $item['currency'] : '';
					$banner = $item['banner'] ? $item['banner'] : null;
					$offers = $item['offers'] ? $item['offers'] : null;
					$top_offers = $item['top_offers'] ? $item['top_offers'] : null;
					$categories = $item['categories'] ? $item['categories'] : null;
					$quick_grabs = $item['quick_grabs'] ? $item['quick_grabs'] : null;
					$exclusives = $item['exclusives'] ? $item['exclusives'] : null;

					return view('front.home.index', compact('sliders', 'currency', 'banner', 'offers', 'top_offers', 'usercartsitem', 'categories', 'quick_grabs', 'exclusives'));
				}
			} elseif (in_array('travel', $access)) {
				if (in_array('flight', $access)) {
					return view('front.page_selection');
				} else {
					$states = Hotel::select(DB::Raw('distinct state as state'))->whereStatus('AC')->pluck('state')->toArray();
					return view('front.travel.index', compact('states'));
				}
			} elseif (in_array('flight', $access)) {
				return redirect()->route('flight');
			} elseif (in_array('sample_api', $access)) {
				return redirect()->route('sample-api');
			} elseif (in_array('admin_panel', $access)) {
				return redirect()->route('user-admin');
			} elseif (in_array('database', $access)) {
				return redirect('https://omrbranch.com/user-database');
			} else {
				return view('front.no_access');
			}

		} else {
			$user_id = '';
			$user = '';
			return view('front.login');
		}

	}

	public function grocery(Request $request) {
		if (Auth::guard('front')->user()) {
			if (Session::has('access') && in_array('grocery', Session::get('access'))) {
				$user_id = Auth::guard('front')->user()->id;

				$user = Auth::guard('front')->user()->load('user_addresses');
				$usercartsitem = [];
				$requestData = $request->merge(['user_id' => $user_id]);

				if (Auth::guard('front')->user()) {
					$items = json_decode($this->api->getCartItems($requestData));
					if ($items->status == 200) {
						$usercartsitem = $items->data;
					}
				}
				$user_id = $request->merge(['user_id' => $user_id]);

				$item = $this->api->homePageData($user_id);

				$sliders = $item['slider'] ? $item['slider'] : [];

				$currency = $item['currency'] ? $item['currency'] : '';
				$banner = $item['banner'] ? $item['banner'] : null;
				$offers = $item['offers'] ? $item['offers'] : null;
				$top_offers = $item['top_offers'] ? $item['top_offers'] : null;
				$categories = $item['categories'] ? $item['categories'] : null;
				$quick_grabs = $item['quick_grabs'] ? $item['quick_grabs'] : null;
				$exclusives = $item['exclusives'] ? $item['exclusives'] : null;

				return view('front.home.index', compact('sliders', 'currency', 'banner', 'offers', 'top_offers', 'usercartsitem', 'categories', 'quick_grabs', 'exclusives'));
			} else {
				return redirect('/');
			}

		} else {
			$user_id = '';
			$user = '';
			return view('front.login');
		}

	}

	public function sampleApi(Request $request) {
		if (Auth::guard('front')->user()) {
			if (Session::has('access') && in_array('sample_api', Session::get('access'))) {
				return view('front.sample_api');
			} else {
				return redirect('/');
			}

		} else {
			$user_id = '';
			$user = '';
			return view('front.login');
		}

	}

	public function login(Request $request) {

		$this->guard()->logout();

		// check keys are exist
		$this->function->checkKeys(array_keys($request->all()), array("email", "pass"));
		$validate = Validator($request->all(), [
			'email' => 'required',
			'pass' => 'required',
		]);

		$attr = [
			'email' => 'Email',
			'pass' => 'Password',
		];
		$validate->setAttributeNames($attr);

		if ($validate->fails()) {
			$errors = $validate->errors();
			return $this->response = array(
				"status" => 300,
				"data" => null,
				"errors" => $errors,
			);

		} else if (!$this->guard()->check()) {
			// try {
			// check email or password exist
			$remember = ($request->post('remember') == "on") ? true : false;

			$response = $this->api->login($request);

			if (isset($response['data'])) {
				$user = $response['data'];

				Session::put('logtoken', $user->logtoken);
				if ($this->guard()->attempt(["email" => $request->email, "password" => $request->pass, 'status' => 'AC'], $remember)) {
					$user = $this->guard()->user();

					$res = $this->function->checkUserActive($user->id);

					if (gettype($res) != 'boolean') {
						return $res;
					}
					$user->save();

					$user->profile_picture = ($user->profile_picture != null) ? (stripos($user->profile_picture, "http") !== false) ? $user->profile_picture : URL::asset('/uploads/profiles/' . $user->profile_picture) : '';
					$user->refer_screen = $this->function->getBusRuleRef('refer_screen') . $this->function->getBusRuleRef('referrer_amount');
					// dd($user->logtoken);
					if ($user->admin_verify == 1) {
						if ($user->status == 'AC') {
							$user->logtoken = Session::get('logtoken');
							// dd(Session::get('logtoken'));
							return $this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(107),
								'data' => $user,
								'refer_msg' => $this->function->getReferMessage($user->id),
								'cart_count' => $this->function->getCartCount($user->id),
								'role' => $user->user_role->isNotEmpty() ? $user->user_role[0]->role : 'user',
							);
						} else {
							return $this->response = array(
								"status" => 34,
								"message" => ResponseMessages::getStatusCodeMessages(34),
							);
						}
					} else {
						unset($user->refer_screen);
						$this->guard()->logout();
						return $this->response = array(
							"status" => 78,
							"message" => ResponseMessages::getStatusCodeMessages(78),
						);
					}

				} else {
					return $this->response = array(
						"status" => 108,
						"message" => 'Invalid Login details or Your Password might have expired. <a type="text" data-toggle="modal" data-target="#exampleModal12" class="forgetPassword">Click here</a> to reset your password',
					);
				}
			} else {
				return $this->response = array(
					"status" => $response['status'],
					"message" => (isset($response['message']) && $response['message'] != 'Invalid credentials') ? $response['message'] : 'Invalid Login details or Your Password might have expired. <a type="text" data-toggle="modal" data-target="#exampleModal12" class="forgetPassword">Click here</a> to reset your password',
				);
			}
			// } catch (\Exception $ex) {
			//     return $this->response = array(
			//         "status" => 501,
			//         "message" => ResponseMessages::getStatusCodeMessages(501),
			//         "error" => $ex,
			//     );
			// }
		}

	}

	public function social_login($type) {
		return Socialite::driver($type)->redirect();
	}

	public function social_login_callback(Request $request, $type) {

		$userInfo = Socialite::driver($type)->user();
		if (isset($userInfo->email)) {
			if (!User::where("email", $userInfo->email)->orWhere("facebook_id", $userInfo->id)->orWhere("google_auth_id", $userInfo->id)->first()) {
				if (isset($userInfo->user['first_name'])) {
					$first_name = $userInfo->user['first_name'];
					$last_name = $userInfo->user['last_name'];
				} else {
					$first_name = $userInfo->user['given_name'];
					$last_name = $userInfo->user['family_name'];
				}
				$user = new User();
				$user->user_code = Helper::generateNumber('g_users', 'user_code');
				$user->referral_code = Helper::generateReferralCode();
				$user->first_name = $first_name;
				$user->last_name = $last_name;
				$user->device_type = 'web';
				$user->email = $userInfo->email;
				$user->mobile_number = (isset($userInfo->mobile_number)) ? $userInfo->mobile_number : '';

				// check profile_picture key exist or not
				if ($request->profile_picture != null) {
					$file = $request->profile_picture;
					$user->profile_picture = $file;
				}
				$user->is_verified = 1;
				if ($type == 'facebook') {
					$user->facebook_signin = 1;
					$user->facebook_id = $userInfo->id;
				} elseif ($type == 'google') {
					$user->google_signin = 1;
					$user->google_auth_id = $userInfo->id;
				}
				$user->save();

				Notify::sendMail("emails.user_registration", $user->toArray(), "omrbranch - Successful SignUp");

				$user->profile_picture = ($user->profile_picture != null) ? (stripos($user->profile_picture, "http") !== false) ? $user->profile_picture : URL::asset('/uploads/profiles/' . $user->profile_picture) : '';
				$user->refer_screen = $this->function->getBusRuleRef('refer_screen') . $this->function->getBusRuleRef('referrer_amount');

				$request->session()->flash('success', "Registration successfully done");
				return redirect()->route('home');

			} else {
				$user = User::where('email', $userInfo->email)->orWhere("facebook_id", $userInfo->id)->orWhere("google_auth_id", $userInfo->id)->first();
				$this->function->checkUserActive($user->id);

				if ($type == 'facebook') {
					$user->facebook_signin = 1;
					$user->facebook_id = $userInfo->id;
				} elseif ($type == 'google') {
					$user->google_signin = 1;
					$user->google_auth_id = $userInfo->id;
				}
				$user->save();
				$user->profile_picture = ($user->profile_picture != null) ? (stripos($user->profile_picture, "http") !== false) ? $user->profile_picture : URL::asset('/uploads/profiles/' . $user->profile_picture) : '';
				$user->refer_screen = $this->function->getBusRuleRef('refer_screen') . $this->function->getBusRuleRef('referrer_amount');
				if ($user->status == 'AC') {
					Session::put('user', $user);
					auth()->guard('front')->login($user);
					$request->session()->flash('success', "Login successfully");
					return redirect()->route('home');

				} else {
					$request->session()->flash('error', "You are blocked please contact to administrator");
					return redirect('/');
				}
			}
		} else {
			if (!User::orWhere("facebook_id", $userInfo->id)->orWhere("google_auth_id", $userInfo->id)->first()) {
				Session::put('social-user', $userInfo);
				Session::put('social-type', $type);
				$request->session()->flash('error', "You haven't linked your email id with your account.");
				return redirect()->route('home');
			} else {
				$user = User::orWhere("facebook_id", $userInfo->id)->orWhere("google_auth_id", $userInfo->id)->first();
				$this->function->checkUserActive($user->id);

				if ($type == 'facebook') {
					$user->facebook_signin = 1;
					$user->facebook_id = $userInfo->id;
				} elseif ($type == 'google') {
					$user->google_signin = 1;
					$user->google_auth_id = $userInfo->id;
				}
				$user->save();
				$user->profile_picture = ($user->profile_picture != null) ? (stripos($user->profile_picture, "http") !== false) ? $user->profile_picture : URL::asset('/uploads/profiles/' . $user->profile_picture) : '';
				$user->refer_screen = $this->function->getBusRuleRef('refer_screen') . $this->function->getBusRuleRef('referrer_amount');
				if ($user->status == 'AC') {
					Session::put('user', $user);
					auth()->guard('front')->login($user);
					$request->session()->flash('success', "Login successfully");
					return redirect()->route('home');

				} else {
					$request->session()->flash('error', "You are blocked please contact to administrator");
					return redirect('/');
				}
			}

		}

	}

	public function socialSignup(Request $request) {
		$userInfo = Session::get('social-user');
		$type = Session::get('social-type');
		$userInfo->email = $request->email;

		if (isset($userInfo->user['first_name'])) {
			$first_name = $userInfo->user['first_name'];
			$last_name = $userInfo->user['last_name'];
		} else {
			$first_name = $userInfo->user['given_name'];
			$last_name = $userInfo->user['family_name'];
		}

		$user = new User();
		$user->user_code = Helper::generateNumber('g_users', 'user_code');
		$user->referral_code = Helper::generateReferralCode();
		$user->first_name = $first_name;
		$user->last_name = $last_name;
		$user->device_type = 'web';
		$user->email = $userInfo->email;
		$user->mobile_number = (isset($userInfo->mobile_number)) ? $userInfo->mobile_number : '';

		$user->is_verified = 1;
		if ($type == 'facebook') {
			$user->facebook_signin = 1;
			$user->facebook_id = $userInfo->id;
		} elseif ($type == 'google') {
			$user->google_signin = 1;
			$user->google_auth_id = $userInfo->id;
		}
		$user->save();
		Notify::sendMail("emails.user_registration", $user->toArray(), "omrbranch - Successful SignUp");

		$user->profile_picture = ($user->profile_picture != null) ? (stripos($user->profile_picture, "http") !== false) ? $user->profile_picture : URL::asset('/uploads/profiles/' . $user->profile_picture) : '';
		$user->refer_screen = $this->function->getBusRuleRef('refer_screen') . $this->function->getBusRuleRef('referrer_amount');

		Session::flush('social-user');
		Session::flush('social-type');

		$request->session()->flash('success', "Registration successfully done");
		return redirect()->route('home');
	}
	//search listing
	public function filter(Request $request) {
		$products = $this->api->search($request);
		$products = $this->api->getSearchResult($requestData);
	}

	//ajax autocomplete
	public function searchResult(Request $request, $type = null, $id = null, $category_id = null) {
		if (isset($request->search) && !empty($request->search)) {
			$requestData = $request->merge(['text' => $request->search, 'user_id' => isset(auth()->guard('front')->user()->id) ? auth()->guard('front')->user()->id : 0]);
			$products = $this->api->searchProduct($requestData);
			$currency = isset($products['currency']) ? $products['currency'] : null;
			$products = isset($products['data']) ? $products['data'] : null;
			return view('front.products.search', compact('products', 'currency'));
		} elseif ($type != null) {
			$requestData = $request->merge(['type' => $type, 'id' => $id, 'category_id' => $category_id, 'user_id' => isset(auth()->guard('front')->user()->id) ? auth()->guard('front')->user()->id : 0]);
			$products = $this->api->getSearchResult($requestData);
			$currency = isset($products['currency']) ? $products['currency'] : null;
			$products = isset($products['data']) ? $products['data'] : null;
			return view('front.listing.view-offer', compact('products', 'currency'));
		} else {
			return redirect('/');
		}

	}

	public function addToCartHome(Request $request) {

		$view1 = '';
		$view2 = '';
		$message = 'Please login to continue';
		$status = 'error';
		$cart_count = '';

		if (isset($request->user_id)) {
			$cart = $this->api->addToCart($request);
			$message = $cart['message'];
			$status = $cart['status'];
			$cart_count = isset($cart['cart_count']) ? $cart['cart_count'] : $cart_count;
			$item = $this->api->homePageData($request);
			$view1 = view("front.home.partial-quick_grab", ['quick_grabs' => $item['quick_grabs'], 'currency' => $item['currency']])->render();
			$view2 = view("front.home.partial-exclusive", ['exclusives' => $item['exclusives'], 'currency' => $item['currency']])->render();
		}
		return response()->json(['status' => $status, 'message' => $message, 'cart_count' => $cart_count, 'html1' => $view1, 'html2' => $view2]);
	}

	public function ticketGenerate() {
		return view("front.ticket.form");
	}

	public function ticketGenerateOtp() {
		return view("front.ticket.otp_ifrem");
	}

	public function submitTicket(Request $request) {
		$data = new Ticket();
		$data->user_id = Auth::guard('front')->user()->id;
		$data->mobile = $request->mobile;
		$data->email = $request->email;
		$data->order_no = $request->order_no;
		$data->message = $request->message;
		$data->subject = $request->subject;
		if ($data->save()) {
			return response()->json(['status' => true, 'message' => 'Ticket Generate successfully']);
		} else {
			return response()->json(['status' => false, 'message' => 'Something went wrong']);
		}
	}

	public function closeTicket(Request $request) {
		$data = Ticket::where('id', $request->id)->first();
		if ($data) {
			Ticket::where('id', $request->id)->update(['status' => 1]);
			return response()->json(['status' => true, 'message' => 'Ticket Closed successfully']);
		} else {
			return response()->json(['status' => false, 'message' => 'Something went wrong']);
		}
	}

	public function moveBinTicket(Request $request) {
		$data = Ticket::where('id', $request->id)->first();
		if ($data) {
			Ticket::where('id', $request->id)->update(['status' => 2]);
			return response()->json(['status' => true, 'message' => 'Ticket move to bin']);
		} else {
			return response()->json(['status' => false, 'message' => 'Something went wrong']);
		}
	}
}
