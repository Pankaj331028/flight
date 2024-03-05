<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Library\Notify;
use App\Library\ResponseMessages;
use App\Mail\SendOtp;
use App\Models\OTP;
use App\Model\AttributeValue;
use App\Model\BusRuleRef;
use App\Model\Cart;
use App\Model\CartItem;
use App\Model\Category;
use App\Model\City;
use App\Model\CMS;
use App\Model\Country;
use App\Model\CouponCode;
use App\Model\CouponRange;
use App\Model\DeliverySlot;
use App\Model\FAQQuestion;
use App\Model\Location;
use App\Model\Notification;
use App\Model\Offer;
use App\Model\Order;
use App\Model\OrderDelivery;
use App\Model\OrderDriver;
use App\Model\OrderItem;
use App\Model\OrderReturn;
use App\Model\Product;
use App\Model\ProductFavourite;
use App\Model\ProductVariation;
use App\Model\Rating;
use App\Model\ReferrerUser;
use App\Model\Role;
use App\Model\ShippingCharge;
use App\Model\Slider;
use App\Model\State;
use App\Model\Ticket;
use App\Model\User;
use App\Model\UserAddress;
use App\Model\Wallet;
use Auth;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session as FacadesSession;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Passport\Client as OClient;
use Session;
use URL;

class ApiController extends MyController {

	// use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public function allowSocial(Request $request) {

		try {
			$type = $this->getBusRuleRef('allow_social');

			$this->response = array(
				'status' => 200,
				'type' => $type,
			);

		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		return $this->shut_down();
	}

	/**
	 * login api
	 *
	 * @return \Illuminate\Http\Response
	 */

	/**
	 * @OA\Post(
	 * path="/postmanBasicAuthLogin",
	 * summary="Sign in with Postman",
	 * description="Basic Authentication for Postman",
	 * operationId="authLoginPostman",
	 *      tags={"Authentication"},
	 * security={{"basicAuth":{}}},
	 *   @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="refer_msg", type="string"),
	 *       @OA\Property(property="cart_count", type="number"),
	 *       @OA\Property(property="role", type="string")
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   )
	 * ),
	 */

	public function apilogin(Request $request) {

		$user = Auth::user();

		try {
			$res = $this->checkUserActive($user->id);
			if (gettype($res) != 'boolean') {
				return $res;
			}

			$user->device_id = $request->device_id;
			$user->device_token = $request->device_token;
			$user->device_type = $request->device_type;
			$user->save();
			$user->profile_picture = ($user->profile_picture != null) ? (stripos($user->profile_picture, "http") !== false) ? $user->profile_picture : URL::asset('/uploads/profiles/' . $user->profile_picture) : '';
			$user->refer_screen = $this->getBusRuleRef('refer_screen') . $this->getBusRuleRef('referrer_amount');

			$user->logtoken = $user->createToken('Thoraipakkam OMR Branch Login')->accessToken;

			if ($user->status == 'AC') {

				Session::put('user', $user);

				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(107),
					'data' => $user,
					'refer_msg' => $this->getReferMessage($user->id),
					'cart_count' => $this->getCartCount($user->id),
					'role' => $user->user_role->isNotEmpty() ? $user->user_role[0]->role : 'user',
				);
			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(34),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}
	/**
	 * @OA\Post(
	 * path="/login",
	 * summary="Sign in",
	 * description="Basic Authentication with email & password",
	 * operationId="authLogin",
	 *      tags={"Authentication"},
	 * @OA\RequestBody(
	 *    required=true,
	 *    description="Pass user credentials",
	 *    @OA\JsonContent(
	 *       required={"email","pass"},
	 *       @OA\Property(property="email", type="string", format="email", example="dummy@gmail.com"),
	 *       @OA\Property(property="pass", type="string", format="password", example="dummy123"),
	 *       @OA\Property(property="device_id", type="string",  format="text", example="Ab#13" ),
	 *       @OA\Property(property="device_type", type="string",  format="text", example="Android"),
	 *       @OA\Property(property="device_token", type="string",  format="text", example="jytrtyteeeeeeeeeeeey"),
	 *    )
	 * ),
	 *   @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="refer_msg", type="string"),
	 *       @OA\Property(property="cart_count", type="number"),
	 *       @OA\Property(property="role", type="string")
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	// function called to login
	public function login(Request $request) {
		// check keys are exist
		$res = $this->checkKeys(array_keys($request->all()), array("email", "pass"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		// $res = $this->checkKeys(array_keys($request->all()), array("email", "password", "device_id", "device_token", "device_type"));
		try {
			// check email or password exist

			$user = User::where('email', $request->email)->whereStatus('AC')->first();

			if (!$user) {
				$this->response = array(
					"status" => 404,
					"message" => ResponseMessages::getStatusCodeMessages(321),
				);
			} elseif ($user->is_verified == 0) {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(78),
				);
			} elseif (stripos($request->url(), '/api') !== false && ($user->travel_access == 'no' || $user->mobile_app_access == 'no')) {
				$this->response = array(
					"status" => 403,
					"message" => ResponseMessages::getStatusCodeMessages(403),
				);
			} elseif (Auth::attempt(["email" => $request->email, "password" => $request->pass, 'status' => 'AC', 'is_verified' => 1])) {

				$user = Auth::user();
				$res = $this->checkUserActive($user->id);
				if (gettype($res) != 'boolean') {
					return $res;
				}
				$cookievalue = base64_encode($user->email) . time();
				$user->device_id = $request->device_id;
				$user->device_token = $request->device_token;
				$user->device_type = $request->device_type;
				$user->login_cookie = $cookievalue;
				$user->save();
				$user->profile_picture = ($user->profile_picture != null) ? (stripos($user->profile_picture, "http") !== false) ? $user->profile_picture : URL::asset('/uploads/profiles/' . $user->profile_picture) : '';
				$user->refer_screen = $this->getBusRuleRef('refer_screen') . $this->getBusRuleRef('referrer_amount');

				$user->logtoken = $user->createToken('Thoraipakkam OMR Branch Login')->accessToken;

				if (Auth::user()->status == 'AC') {

					Session::put('user', $user);
					setcookie('login_session', $cookievalue, time() + (86400 * 365), "/"); // 86400 = 1 day

					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(107),
						'data' => $user,
						'refer_msg' => $this->getReferMessage($user->id),
						'cart_count' => $this->getCartCount($user->id),
						'role' => $user->user_role->isNotEmpty() ? $user->user_role[0]->role : 'user',
					);
				} else {
					$this->response = array(
						"status" => 400,
						"message" => ResponseMessages::getStatusCodeMessages(34),
					);
				}
			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(108),
				);
			}
		} catch (\Exception $ex) {
			dd($ex);
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}
	/**
	 * @OA\Post(
	 * path="/loginWithApiKey",
	 * summary="Sign in",
	 * description="Login by API Key",
	 * operationId="authLoginAPIKey",
	 *      tags={"Authentication"},
	 * @OA\RequestBody(
	 *    required=true,
	 *    description="Pass user account API Key",
	 *    @OA\JsonContent(
	 *       required={"api_key"},
	 *       @OA\Property(property="api_key", type="string", format="api_key", example="JKLMN6487qrs89484"),
	 *       @OA\Property(property="device_id", type="string",  format="text", example="Ab#13" ),
	 *       @OA\Property(property="device_type", type="string",  format="text", example="Android"),
	 *       @OA\Property(property="device_token", type="string",  format="text", example="jytrtyteeeeeeeeeeeey"),
	 *    )
	 * ),
	 *   @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="refer_msg", type="string"),
	 *       @OA\Property(property="cart_count", type="number"),
	 *       @OA\Property(property="role", type="string")
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	// function called to login
	public function login_api_key(Request $request) {
		// check keys are exist
		$res = $this->checkKeys(array_keys($request->all()), array("api_key"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		// $res = $this->checkKeys(array_keys($request->all()), array("email", "password", "device_id", "device_token", "device_type"));
		// try {
		// check email or password exist

		if ($user = User::where('api_key', $request->api_key)->whereStatus('AC')->first()) {
			Auth::login($user);
			$user = Auth::user();
			$res = $this->checkUserActive($user->id);
			if (gettype($res) != 'boolean') {
				return $res;
			}
			$user->device_id = $request->device_id;
			$user->device_token = $request->device_token;
			$user->device_type = $request->device_type;
			$user->save();
			$user->profile_picture = ($user->profile_picture != null) ? (stripos($user->profile_picture, "http") !== false) ? $user->profile_picture : URL::asset('/uploads/profiles/' . $user->profile_picture) : '';
			$user->refer_screen = $this->getBusRuleRef('refer_screen') . $this->getBusRuleRef('referrer_amount');

			$user->logtoken = $user->createToken('Thoraipakkam OMR Branch Login')->accessToken;

			if (Auth::user()->status == 'AC') {

				Session::put('user', $user);

				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(107),
					'data' => $user,
					'refer_msg' => $this->getReferMessage($user->id),
					'cart_count' => $this->getCartCount($user->id),
					'role' => $user->user_role->isNotEmpty() ? $user->user_role[0]->role : 'user',
				);
			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(34),
				);
			}

		} else {
			$this->response = array(
				"status" => 400,
				"message" => ResponseMessages::getStatusCodeMessages(108),
			);
		}
		// } catch (\Exception $ex) {
		//     $this->response = array(
		//         "status" => 501,
		//         "message" => ResponseMessages::getStatusCodeMessages(501),
		//     );
		// }

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}
	/**
	 * @OA\Post(
	 * path="/accessToken",
	 * summary="get Access Token",
	 * description="get access token for OAuth 2.0 authentication",
	 * operationId="authAccessToken",
	 *      tags={"Authentication"},
	 * @OA\RequestBody(
	 *    required=true,
	 *    description="Pass user email and password to get the access token",
	 *    @OA\JsonContent(
	 *       required={"email","password"},
	 *       @OA\Property(property="email", type="string", format="email", example="dummy@gmail.com"),
	 *       @OA\Property(property="password", type="string", format="password", example="dummy123"),
	 *    )
	 * ),
	 *   @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="refer_msg", type="string"),
	 *       @OA\Property(property="cart_count", type="number"),
	 *       @OA\Property(property="role", type="string")
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function access_token(Request $request) {
		// check keys are exist
		$res = $this->checkKeys(array_keys($request->all()), array("email", "password"));

		if (gettype($res) != 'boolean') {
			return $res;
		}

		// try {

		if (Auth::attempt(["email" => $request->email, "password" => $request->password, 'status' => 'AC', 'is_verified' => 1])) {

			$user = Auth::user();
			// $endpoint = URL("oauth/token");
			// $client = new \GuzzleHttp\Client();
			$oclient = OClient::where('password_client', 1)->first();

			$url = 'http://omrbranch.com/oauth/token';
			$headers = array(
				'Content-Type: application/x-www-form-urlencoded',
			);
			$fields = array(
				"grant_type" => 'password',
				"username" => $request->email,
				"password" => $request->password,
				"client_id" => $oclient->id,
				"client_secret" => $oclient->secret,
				'scope' => '*',
			);
			$data = http_build_query($fields, '', '&');
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$response = curl_exec($ch);
			curl_close($ch);

			$content = json_decode($response);

			// dd($content);
			if (isset($content)) {

				if (isset($content->access_token)) {
					$user->accessToken = $content->access_token;
					$user->refreshToken = $content->refresh_token;

					Auth::login($user);

					Session::put('user', $user);

					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(107),
						'data' => $user,
						'refer_msg' => $this->getReferMessage($user->id),
						'cart_count' => $this->getCartCount($user->id),
						'role' => $user->user_role->isNotEmpty() ? $user->user_role[0]->role : 'user',
					);
				} else {
					$this->response = array(
						"status" => 400,
						"message" => 'Token could not be fetched',
						"content" => $content,
					);
				}

			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(108),
				);
			}

		} else {
			$this->response = array(
				"status" => 400,
				"message" => ResponseMessages::getStatusCodeMessages(214),
			);
		}
		// } catch (\Exception $ex) {
		//     $this->response = array(
		//         "status" => 501,
		//         "message" => ResponseMessages::getStatusCodeMessages(501),
		//     );
		// }

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}
	/**
	 * @OA\Post(
	 * path="/refreshToken",
	 * summary="get Refresh Token",
	 * description="get refresh token",
	 * operationId="authRefreshToken",
	 *      tags={"Authentication"},
	 * @OA\RequestBody(
	 *    required=true,
	 *    description="Pass refresh token received from accessToken API",
	 *    @OA\JsonContent(
	 *       required={"email","refresh_token"},
	 *       @OA\Property(property="email", type="string", format="email", example="dummy@gmail.com"),
	 *       @OA\Property(property="refresh_token", type="string", example="skjvhbahfvbawjq4bw4bfq3h48923ed9834hfq8734bfq3ub4rh4j3q5bj3b53j4h5b3jh45b2345b354jh23b5jhg45v"),
	 *    )
	 * ),
	 *   @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="refer_msg", type="string"),
	 *       @OA\Property(property="cart_count", type="number"),
	 *       @OA\Property(property="role", type="string")
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function refresh_token(Request $request) {
		// check keys are exist
		$res = $this->checkKeys(array_keys($request->all()), array("email", "refresh_token"));

		if (gettype($res) != 'boolean') {
			return $res;
		}

		if ($user = User::where('email', $request->email)->whereStatus('AC')->first()) {

			$res = $this->checkUserActive($user->id);
			if (gettype($res) != 'boolean') {
				return $res;
			}

			$oclient = OClient::where('password_client', 1)->first();

			$url = 'http://omrbranch.com/oauth/token';
			$headers = array(
				'Content-Type: application/x-www-form-urlencoded',
			);
			$fields = array(
				"grant_type" => 'refresh_token',
				'client_id' => $oclient->id,
				'client_secret' => $oclient->secret,
				'refresh_token' => $request->refresh_token,
				'scope' => '',
			);

			$data = http_build_query($fields, '', '&');
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$response = curl_exec($ch);
			curl_close($ch);

			$content = json_decode($response);

			if (isset($content)) {

				if (isset($content->access_token)) {
					$user->accessToken = $content->access_token;
					$user->refreshToken = $content->refresh_token;

					Auth::login($user);

					Session::put('user', $user);

					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(109),
						'data' => $user,
						'refer_msg' => $this->getReferMessage($user->id),
						'cart_count' => $this->getCartCount($user->id),
						'role' => $user->user_role->isNotEmpty() ? $user->user_role[0]->role : 'user',
					);
				} else {
					$this->response = array(
						"status" => 400,
						"message" => 'Token could not be fetched',
					);
				}

			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(34),
				);
			}

		} else {
			$this->response = array(
				"status" => 400,
				"message" => ResponseMessages::getStatusCodeMessages(108),
			);
		}

		// if ($user = User::where('email', $request->email)->whereStatus('AC')->first()) {

		// 	$res = $this->checkUserActive($user->id);
		// 	if (gettype($res) != 'boolean') {
		// 		return $res;
		// 	}

		// 	$endpoint = URL("/oauth/token");
		// 	$client = new \GuzzleHttp\Client();

		// 	$oclient = OClient::where('password_client', 1)->first();

		// 	try {
		// 		$response = $client->post(
		// 			$endpoint,
		// 			['form_params' =>
		// 				[
		// 					'grant_type' => 'refresh_token',
		// 					'client_id' => $oclient->id,
		// 					'client_secret' => $oclient->secret,
		// 					'refresh_token' => $request->refresh_token,
		// 					'scope' => '',
		// 				],
		// 			]);

		// 		$statusCode = $response->getStatusCode();
		// 		$content = json_decode((string) $response->getBody());
		// 	} catch (\Exception $e) {
		// 		$this->response = [
		// 			'status' => 401,
		// 			'message' => ResponseMessages::getStatusCodeMessages(310),
		// 		];

		// 		return $this->shut_down();
		// 	}

		// 	if (isset($content->token_type)) {

		// 		$user->accessToken = $content->access_token;
		// 		$user->refreshToken = $content->refresh_token;

		// 		Auth::login($user);

		// 		Session::put('user', $user);

		// 		$this->response = array(
		// 			"status" => 200,
		// 			"message" => ResponseMessages::getStatusCodeMessages(109),
		// 			'data' => $user,
		// 			'refer_msg' => $this->getReferMessage($user->id),
		// 			'cart_count' => $this->getCartCount($user->id),
		// 			'role' => $user->user_role->isNotEmpty() ? $user->user_role[0]->role : 'user',
		// 		);
		// 	} else {
		// 		$this->response = array(
		// 			"status" => 400,
		// 			"message" => ResponseMessages::getStatusCodeMessages(34),
		// 		);
		// 	}

		// } else {
		// 	$this->response = array(
		// 		"status" => 400,
		// 		"message" => ResponseMessages::getStatusCodeMessages(108),
		// 	);
		// }

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function called to register user

	/**
	 * login api
	 *
	 * @return \Illuminate\Http\Response
	 */
	/**
	 * @OA\Post(
	 * path="/userRegister",
	 * description="userRegister",
	 * operationId="authuserRegister",
	 *      tags={"Authentication"},
	 * @OA\RequestBody(
	 *    required=true,
	 *    description="Pass user credentials",
	 *    @OA\JsonContent(
	 *       required={"first_name", "last_name","signup_email","mobile_number","signup_password","confirm_password","device_id","device_token","device_type", "day","month","year", "otp", "hobbies", "gender"},
	 *       @OA\Property(property="first_name", type="string", format="text", example="Abc"),
	 *       @OA\Property(property="last_name", type="string", format="text", example="Abc"),
	 *       @OA\Property(property="signup_email", type="string", format="text", example="Abc@gmail.com"),
	 *       @OA\Property(property="mobile_number", type="string", format="text", example="9856998899"),
	 *       @OA\Property(property="signup_password", type="string", format="text", example="Abc@123"),
	 *       @OA\Property(property="confirm_password", type="string", format="text", example="Abc@123"),
	 *       @OA\Property(property="device_id", type="string",  format="text", example="Ab#13"),
	 *       @OA\Property(property="device_type", type="string",  format="text", example="Android"),
	 *       @OA\Property(property="device_token", type="string",  format="text", example="#qeqweqw"),
	 *       @OA\Property(property="day", type="string", format="text", example="12"),
	 *       @OA\Property(property="month", type="string", format="text", example="12"),
	 *       @OA\Property(property="year", type="string", format="text", example="2022"),
	 *       @OA\Property(property="otp", type="string", format="text", example="1234"),
	 *       @OA\Property(property="hobbies", type="array",@OA\Items(type="string",example="cooking")),
	 *       @OA\Property(property="gender", type="string", format="text", example="Male"),
	 *    )
	 * ),
	 *   @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="refer_msg", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */
	public function userRegister(Request $request) {
		// check keys are exist
		$res = $this->checkKeys(array_keys($request->all()), array("first_name", "last_name", "signup_email", "mobile_number", "signup_password"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		// "device_id", "device_token", "device_type"
		try {

			if ($request->device_type == 'web') {
				if (isset($request->mobile_number)) {
					$user = User::where('mobile_number', $request->mobile_number)->first();
					if ($user) {
						$user_id = $user->id;
					} else {
						$user_id = '';
					}
				} else {
					$user_id = '';
				}
				$validate = Validator($request->all(), [
					'day' => 'required',
					'month' => 'required',
					'year' => 'required',
					'hobbies' => 'required',
					'gender' => 'required',
					'otp' => 'required|numeric',
					'first_name' => 'required|min:3|max:30',
					'last_name' => 'required|min:3|max:30',
					'mobile_number' => ['required', 'numeric', 'digits_between:8,15', Rule::unique('g_users')->whereIn('status', ['AC', 'IN'])],
					'signup_email' => ['required', 'email', Rule::unique('g_users', 'email')->whereIn('status', ['AC', 'IN'])],
					'signup_password' => 'required|regex:"^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&])([a-zA-Z0-9@$!%*?&]{6,})$"',
					'confirm_password' => 'required|same:signup_password',
				], ['signup_password.regex' => 'Please fill minimum 6 character Password with uppercase, lowercase, special character and digit', 'mobile_number.unique' => 'Mobile number should be unique.']);

				$attr = [
					'first_name' => 'First Name',
					'last_name' => 'Last Name',
					'mobile_number' => 'Mobile Number',
					'signup_email' => 'Email Address',
					'signup_password' => 'Password',
					'confirm_password' => 'Confirm Password',
				];
			} else {
				$validate = Validator($request->all(), [
					'day' => 'required',
					'month' => 'required',
					'year' => 'required',
					'hobbies' => 'required',
					'gender' => 'required',
					'first_name' => 'required',
					'last_name' => 'required',
					'mobile_number' => ['required', 'numeric', 'digits_between:8,15', Rule::unique('g_users')->whereIn('status', ['AC', 'IN'])],
					'signup_email' => ['required', 'email', Rule::unique('g_users', 'email')->whereIn('status', ['AC', 'IN'])],
					'signup_password' => 'required',
				]);
				$attr = [
					'first_name' => 'First Name',
					'last_name' => 'Last Name',
					'mobile_number' => 'Mobile Number',
					'signup_email' => 'Email Address',
					'signup_password' => 'Password',
				];
			}

			$validate->setAttributeNames($attr);
			if ($validate->fails()) {
				$errors = $validate->errors();
				$this->response = array(
					"status" => 300,
					"message" => $errors->first(),
					"data" => null,
					"errors" => $errors,
				);
			} else {
				// check if referral code is sent and if user with referral code exists or not
				$count = 0;
				if (isset($request->referral_code)) {
					$referrer = User::where('referral_code', $request->referral_code)->where('status', 'AC')->count();
					if ($referrer > 0) {
						$count++;
					}
				}

				//if referral code is sent and valid, then user will be registered otherwise error message will be shown to user stating invalid referral code
				$check = OTP::where(['mobile_number' => $request->mobile_number, 'expire_status' => 1, 'otp' => $request->otp])->first();

				if (isset($check)) {

					if ((isset($request->referral_code) && $count > 0) || !isset($request->referral_code)) {
						if (!User::where("email", $request->signup_email)->where('status', '!=', 'DL')->first()) {

							// OTP::where('id', $check->id)->update(['expire_status' => 0]);

							$user = new User();
							$user->user_code = Helper::generateNumber('g_users', 'user_code');
							$user->referral_code = Helper::generateReferralCode();
							$user->first_name = $request->first_name;
							$user->last_name = $request->last_name;
							$user->email = $request->signup_email;
							$user->mobile_number = (isset($request->mobile_number) && $request->mobile_number != null) ? $request->mobile_number : '';
							$user->password = bcrypt($request->signup_password);
							$user->device_id = $request->device_id ?? '';
							$user->device_token = $request->device_token ?? '';
							$user->device_type = $request->device_type ?? 'android';
							$user->gender = $request->gender;
							$user->days = $request->day;
							$user->month = $request->month;
							$user->year = $request->year;

							if (isset($request->hobbies) && $request->hobbies != null) {
								$user->hobbies = implode(",", $request->hobbies);
							}

							// return $request->hobbies;
							$user->is_verified = 1;
							$user->save();

							$user->user_role()->sync(
								[Role::whereRole('user')->firstOrFail()->id]
							);

							// send verification mail to user
							$data = $user;
							// $data->verify_link = route('verifyEmail') . '?id=' . base64_encode($user->email);

							// try{
							// 	Notify::sendMail("emails.user_registration", $data->toArray(), "omrbranch - SignUp Verification");
							// 	unset($user->verify_link);
							// }catch(Exception $ex)
							// {
							// 	dd($ex);
							// }

							// check user has added referral code while registering
							if ($this->getBusRuleRef("refer_user") == 1 && isset($request->referral_code)) {
								if ($prntUser = User::select("id", "wallet_amount")->where("referral_code", $request->referral_code)->first()) {
									$referrerAmount = $this->getBusRuleRef("referrer_amount");

									// create relation between user and referral user
									$ReferrerUser = new ReferrerUser();
									$ReferrerUser->referral_code = $request->referral_code;
									$ReferrerUser->user_id = $user->id;
									$ReferrerUser->referrer_id = $prntUser->id;
									$ReferrerUser->amount = $referrerAmount;
									$ReferrerUser->save();

								}
							}
							$user->refer_screen = $this->getBusRuleRef('refer_screen') . $this->getBusRuleRef('referrer_amount');

							// $user->logtoken = $user->createToken('Thoraipakkam OMR Branch Signup')->accessToken;

							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(102),
								"data" => $user,
								'refer_msg' => $this->getReferMessage($user->id),
							);
						} else {
							$this->response = array(
								"status" => 400,
								"message" => ResponseMessages::getStatusCodeMessages(104),
							);
						}
					} else {
						$this->response = array(
							"status" => 400,
							"message" => ResponseMessages::getStatusCodeMessages(212),
						);
					}

				} else {
					$this->response = array(
						"status" => 400,
						"message" => ResponseMessages::getStatusCodeMessages(76),
					);
				}
			}
		} catch (\Exception $ex) {
			dd($ex);
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		return $this->shut_down();
	}

	/**
	 * login api
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function checkSocialUser(Request $request) {

		$res = $this->checkKeys(array_keys($request->all()), array("social_id", "login_type"));

		if (gettype($res) != 'boolean') {
			return $res;
		}

		try {
			$query = User::where('status', '!=', 'DL');

			switch ($request->login_type) {
			case 'facebook':$query->where('facebook_id', $request->social_id);
				break;
			case 'google':$query->where('google_auth_id', $request->social_id);
				break;
			}
			$userinfo = $query->first();

			if (isset($userinfo->id)) {
				$user = User::where('id', $userinfo->id)->first();
				if (isset($user->id)) {

					if ($user->email != null) {
						$user->device_id = $request->device_id;
						$user->device_token = $request->device_token;
						$user->device_type = $request->device_type;

						if ($request->login_type == 'facebook') {
							$user->facebook_signin = 1;
							$user->facebook_id = $request->social_id;
						} elseif ($request->login_type == 'google') {
							$user->google_signin = 1;
							$user->google_auth_id = $request->social_id;
						}
						$user->save();

						$this->response = [
							'status' => 200,
							'message' => 'Email ID present',
							'data' => $user,
							'refer_msg' => $this->getReferMessage($user->id),
							'cart_count' => $this->getCartCount($user->id),
						];
					} elseif ($user->email == null) {
						$this->response = [
							'status' => 400,
							'message' => 'Email ID not present',
							'data' => $user,
							'refer_msg' => $this->getReferMessage($user->id),
							'cart_count' => $this->getCartCount($user->id),
						];
					}
				} else {
					$this->response = ['status' => 400, 'message' => 'User does not exists'];
				}

			} else {
				$this->response = ['status' => 400, 'msg' => 'User doesnot exists'];
			}

		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		return $this->shut_down();
	}

	// function called for socialLogin
	public function socialLogin(Request $request) {
		// check keys are exist
		$res = $this->checkKeys(array_keys($request->all()), array("first_name", "last_name", "email", "type", "device_id", "device_token", "device_type"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {

			if ($request->type == 'web') {
				$this->userRegister($request);
			} else {
				if (!User::where("email", $request->email)->orWhere("facebook_id", $request->social_id)->orWhere("google_auth_id", $request->social_id)->first()) {
					$user = new User();
					$user->user_code = Helper::generateNumber('g_users', 'user_code');
					$user->referral_code = Helper::generateReferralCode();
					$user->first_name = $request->first_name;
					$user->last_name = $request->last_name;
					$user->email = $request->email;
					$user->mobile_number = (isset($request->mobile_number)) ? $request->mobile_number : '';
					$user->device_id = $request->device_id;
					$user->device_token = $request->device_token;
					$user->device_type = $request->device_type;

					// check profile_picture key exist or not
					if ($request->profile_picture != null) {
						$file = $request->profile_picture;
						$user->profile_picture = $file;
					}
					$user->is_verified = 1;
					if ($request->type == 'facebook') {
						$user->facebook_signin = 1;
						$user->facebook_id = $request->social_id;
					} elseif ($request->type == 'google') {
						$user->google_signin = 1;
						$user->google_auth_id = $request->social_id;
					}
					$user->save();

					Notify::sendMail("emails.user_registration", $user->toArray(), "omrbranch - Successful SignUp");

					$user->profile_picture = ($user->profile_picture != null) ? (stripos($user->profile_picture, "http") !== false) ? $user->profile_picture : URL::asset('/uploads/profiles/' . $user->profile_picture) : '';
					$user->refer_screen = $this->getBusRuleRef('refer_screen') . $this->getBusRuleRef('referrer_amount');
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(100),
						"data" => $user,
						'cart_count' => $this->getCartCount($user->id),
					);
				} else {
					$user = User::where('email', $request->email)->orWhere("facebook_id", $request->social_id)->orWhere("google_auth_id", $request->social_id)->first();
					$res = $this->checkUserActive($user->id);
					if (gettype($res) != 'boolean') {
						return $res;
					}
					$user->device_id = $request->device_id;
					$user->device_token = $request->device_token;
					$user->device_type = $request->device_type;

					if ($request->type == 'facebook') {
						$user->facebook_signin = 1;
						$user->facebook_id = $request->social_id;
					} elseif ($request->type == 'google') {
						$user->google_signin = 1;
						$user->google_auth_id = $request->social_id;
					}
					$user->save();
					$user->profile_picture = ($user->profile_picture != null) ? (stripos($user->profile_picture, "http") !== false) ? $user->profile_picture : URL::asset('/uploads/profiles/' . $user->profile_picture) : '';
					$user->refer_screen = $this->getBusRuleRef('refer_screen') . $this->getBusRuleRef('referrer_amount');
					if ($user->status == 'AC') {
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(107),
							'data' => $user,
							'refer_msg' => $this->getReferMessage($user->id),
							'cart_count' => $this->getCartCount($user->id),
						);
					} else {
						$this->response = array(
							"status" => 403,
							"message" => ResponseMessages::getStatusCodeMessages(34),
						);
					}
				}
			}

		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		return $this->shut_down();
	}

	// function called for forgotPassword
	/**
	 * @OA\Post(
	 * path="/forgotPassword",
	 * description="forgotPassword",
	 * operationId="forgotPassword",
	 *      tags={"Authentication"},
	 * @OA\RequestBody(
	 *    required=true,
	 *    description="forgotPassword",
	 *    @OA\JsonContent(
	 *       required={"forgot_email"},
	 *      @OA\Schema(
	 *           type="string"
	 *      ),
	 *       @OA\Property(property="forgot_email", type="email", format="text", example="admin@gmail.com"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	// function called if user wants to reset password
	public function forgotPassword(Request $request) {
		// check keys are exist
		$res = $this->checkKeys(array_keys($request->all()), array("forgot_email"));

		if (gettype($res) != 'boolean') {
			return $res;
		}

		$validate = Validator($request->all(), [
			'forgot_email' => 'required|email',
		]);

		$attr = [
			'forgot_email' => 'Email Address',
		];

		$validate->setAttributeNames($attr);

		if ($validate->fails()) {
			$errors = $validate->errors();
			$this->response = array(
				"status" => 300,
				"message" => $errors->first(),
				"data" => null,
				"errors" => $errors,
			);
		} else {
			try {
				// check email exists or not
				if ($user = User::where("email", $request->forgot_email)->whereIn('status', ['AC', 'IN'])->first()) {
					$user->forgot_key = base64_encode($user->email);
					$user->save();
					$data = array('resetpassword' => route('resetGetApp') . '?id=' . base64_encode($user->email), 'email' => $user->email, 'first_name' => $user->first_name, 'last_name' => $user->last_name);

					Notify::sendMail('emails.forgot_password', $data, 'Forgot Password');
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(9),
					);
				} else {
					$this->response = array(
						"status" => 404,
						"message" => ResponseMessages::getStatusCodeMessages(321),
					);
				}
			} catch (\Exception $ex) {
				$this->response = array(
					"status" => 501,
					"message" => ResponseMessages::getStatusCodeMessages(501),
				);
			}
		}
		return $this->shut_down();
	}

	// function called for updateDevice
	/**
	 * @OA\Put(
	 * path="/updateDevice",
	 * description="updateDevice",
	 * operationId="updateDevice",
	 *      tags={"Authentication"},
	 * security={{"bearerAuth":{}}},
	 * @OA\RequestBody(
	 *    required=true,
	 *    description="updateDevice",
	 *    @OA\JsonContent(
	 *       required={"device_id","device_token"},
	 *       @OA\Property(property="device_id", type="device_id", format="text", example="khdghdfhgd"),
	 *       @OA\Property(property="device_token", type="string", format="text", example="sfhgsfgdfsdfsd"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="cart_count", type="number"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	// function called to update device id for user
	public function updateDevice(Request $request) {
		// check keys are exist
		$user = Auth::user();

		$res = $this->checkKeys(array_keys($request->all()), array("device_id", "device_token"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			// check user exist
			if ($user = User::find($user->id)) {
				$user = Auth::user();
				$user->device_id = $request->device_id;
				$user->device_token = $request->device_token;
				$user->save();
				$user->profile_picture = ($user->profile_picture != null) ? (stripos($user->profile_picture, "http") !== false) ? $user->profile_picture : URL::asset('/uploads/profiles/' . $user->profile_picture) : '';
				$user->refer_screen = $this->getBusRuleRef('refer_screen') . $this->getBusRuleRef('referrer_amount');
				// get user data
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(4),
					"data" => $user,
					'cart_count' => $this->getCartCount($user->id),
				);
			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(5),
					"data" => null,
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		return $this->shut_down();
	}

	// function called to provide user status along with bus rule ref settings

	// function called for updateDevice
	/**
	 * @OA\Post(
	 * path="/userDetail",
	 * description="userDetail",
	 * operationId="userDetail",
	 *      tags={"Authentication"},
	 * security={{"bearerAuth":{}}},
	 * @OA\RequestBody(
	 *    required=true,
	 *    description="userDetail",
	 *    @OA\JsonContent(
	 *       required={"device_id","device_token"},
	 *       @OA\Property(property="device_id", type="device_id", format="text", example="khdghdfhgd"),
	 *       @OA\Property(property="device_token", type="string", format="text", example="sfhgsfgdfsdfsd"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="cart_count", type="number"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function userDetail(Request $request) {
		// check keys are exist
		$user = Auth::user();
		//$res = $this->checkKeys(array_keys($request->all()));
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		try {
			// check user exist
			if ($user = User::find($user->id)) {

				$setting = BusRuleRef::select("rule_name", "rule_value")->whereIn("rule_name", array("ios_update_user", "ios_url_user", "ios_version_user", "android_update_user", "android_url_user", "android_version_user", "app_update_msg"))->where("sts_cd", 'AC')->get();

				$detail = new Response;
				$detail->status = $user->status;
				$detail->ios_update_user = $setting[0]->rule_value;
				$detail->ios_url_user = $setting[1]->rule_value;
				$detail->ios_version_user = $setting[2]->rule_value;
				$detail->android_update_user = $setting[3]->rule_value;
				$detail->android_url_user = $setting[4]->rule_value;
				$detail->android_version_user = $setting[5]->rule_value;
				$detail->app_update_msg = $setting[6]->rule_value;

				unset($detail->headers);
				unset($detail->original);
				unset($detail->exception);

				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(125),
					"data" => $detail,
					'cart_count' => $this->getCartCount($user->id),
				);
			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(5),
					"data" => null,
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		return $this->shut_down();
	}

	// function called to logout user

	/**
	 * @OA\Post(
	 * path="/logout",
	 * description="logout",
	 * operationId="logout",
	 *      tags={"Authentication"},
	 * security={{"bearerAuth":{}}},
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function logout(Request $request) {
		// check keys are exist
		$user = auth()->user();

		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			// check user exist or not
			if ($user = User::find($user->id)) {
				$user->device_id = "";
				$user->save();
				auth()->user()->token()->revoke();
				setcookie("login_session", "", time() - 3600);
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(141),
				);
			} else {
				$this->response = array(
					"status" => 404,
					"message" => ResponseMessages::getStatusCodeMessages(214),
				);
			}
		} catch (\Exception $ex) {
			dd($ex);
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		return $this->shut_down();
	}

	public function changeProfilePic(Request $request) {
		if (isset($request->login_cookie)) {
			$res = $this->checkLoginCookie($request->login_cookie);
			if (gettype($res) != 'boolean') {
				return $res;
			}
		}
		$res = $this->checkKeys(array_keys($request->all()), array("profile_picture"));

		if (gettype($res) != 'boolean') {
			return $res;
		}

		try {

			$user = Auth::user();
			$res = $this->checkUserActive($user->id);
			if (gettype($res) != 'boolean') {
				return $res;
			}

			$validate = Validator($request->all(), [
				'profile_picture' => 'nullable|max:3000',
			]);

			$attr = [
				'profile_picture.max' => 'Profile picture size must be below 3MB',
			];

			$validate->setAttributeNames($attr);
			if ($validate->fails()) {
				$errors = $validate->errors();
				$this->response = array(
					"status" => 300,
					"message" => $errors->first(),
					"data" => null,
					"errors" => $errors,
				);
			} else {
				$filename = "";

				if ($request->hasfile('profile_picture')) {

					$file = $request->file('profile_picture');
					$filename = time() . $file->getClientOriginalName();
					$filename = str_replace('.jpeg', '.jpg', $filename);
					$file->move(public_path('uploads/profiles'), $filename);
					if ($user->profile_picture != null && stripos($user->profile_picture, "http") === false) {
						(public_path('uploads/profiles/' . $user->profile_picture));
					}
					$user->profile_picture = $filename;

				}

				if ($user->save()) {
					$user->profile_picture = ($user->profile_picture != null) ? (stripos($user->profile_picture, "http") !== false) ? $user->profile_picture : URL::asset('/uploads/profiles/' . $user->profile_picture) : '';
					$user->refer_screen = $this->getBusRuleRef('refer_screen') . $this->getBusRuleRef('referrer_amount');
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(122),
						"data" => $user,
						'cart_count' => $this->getCartCount($user->id),
					);
				} else {
					$this->response = array(
						"status" => 303,
						"message" => ResponseMessages::getStatusCodeMessages(123),
						"data" => $user,
						'cart_count' => $this->getCartCount($user->id),
					);
				}
			}

		} catch (\Exception $ex) {

			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		return $this->shut_down();
	}

	/**
	 * @OA\Put(
	 * path="/changePassword",
	 * description="changePassword",
	 * operationId="changePassword",
	 * security={{"bearerAuth":{}}},
	 *      tags={"Authentication"},
	 * @OA\RequestBody(
	 *    required=true,
	 *    description="changePassword",
	 *    @OA\JsonContent(
	 *       required={"device_id","device_token"},
	 *       @OA\Property(property="password", type="string", format="text", example="Teekam@123"),
	 *       @OA\Property(property="new_password", type="string", format="text", example="Teekam@123"),
	 *       @OA\Property(property="confirm_password", type="string", format="text", example="Teekam@123"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="cart_count", type="number"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	// function called to change password
	public function changePassword(Request $request) {
		$res = $this->checkKeys(array_keys($request->all()), array("password", "new_password"));

		// check keys are exist
		$user = Auth::user();

		if (gettype($res) != 'boolean') {
			return $res;
		}

		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		try {

			if ($request->exists('confirm_password')) {
				$validate = Validator($request->all(), [
					'password' => 'required',
					'new_password' => 'required|regex:"^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&])([a-zA-Z0-9@$!%*?&]{6,})$"',
					'confirm_password' => 'required|same:new_password',
				], ['new_password.regex' => 'Please fill minimum 6 character Password with uppercase, lowercase, special character and digit']);

				$attr = [
					'password' => 'Password',
					'new_password' => 'New Password',
					'confirm_password' => 'Confirm Password',
				];
			} else {
				$validate = Validator($request->all(), [
					'password' => 'required',
					'new_password' => 'required',
				], ['new_password.regex' => 'Please fill minimum 6 character Password with uppercase, lowercase, special character and digit']);

				$attr = [
					'password' => 'Password',
					'new_password' => 'New Password',
				];
			}

			$validate->setAttributeNames($attr);

			if ($validate->fails()) {
				$errors = $validate->errors();
				$this->response = array(
					"status" => 300,
					"message" => $errors->first(),
					"data" => null,
					"errors" => $errors,
				);
			} else {
				// check user id or password correct or not

				// if (Auth::attempt(["id" => $user->id, "password" => $request->password])) {
				if (Hash::check($request->password, $user->password)) {

					// check password is not same as old password that is in DB
					if (!Hash::check($request->new_password, Auth::user()->password)) {
						Auth::user()->update(["password" => bcrypt($request->new_password)]);
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(120),
							'cart_count' => $this->getCartCount(Auth::user()->id),
						);
					} else {
						$this->response = array(
							"status" => 400,
							"message" => ResponseMessages::getStatusCodeMessages(121),
							'cart_count' => $this->getCartCount(Auth::user()->id),
						);
					}
				} else {
					$this->response = array(
						"status" => 400,
						"message" => ResponseMessages::getStatusCodeMessages(215),
					);
				}
			}
		} catch (\Exception $ex) {
			//dd($ex);
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		return $this->shut_down();

	}

	// function called for updateProfile
	/**
	 * @OA\Put(
	 * path="/updateProfile",
	 * description="updateProfile",
	 * operationId="updateProfile",
	 * security={{"bearerAuth":{}}},
	 *      tags={"Authentication"},
	 * @OA\RequestBody(
	 *    required=true,
	 *    description="updateProfile",
	 *    @OA\JsonContent(
	 *       required={"first_name","last_name", "mobile_number "},
	 *       @OA\Property(property="first_name", type="string", format="text", example="Teekam"),
	 *       @OA\Property(property="last_name", type="string", format="text", example="Saini"),
	 *       @OA\Property(property="mobile_number", type="string", format="text", example="99828998732"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="cart_count", type="number"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	// function called to update profile
	public function updateProfile(Request $request) {
		$user = Auth::user();

		// check keys are exist
		$res = $this->checkKeys(array_keys($request->all()), array("first_name", "last_name", "mobile_number"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		try {
			if ($request->type == 'web') {
				$required = 'required|between:3,15';

				$validation = ['nullable', 'numeric', 'digits_between:8,15', Rule::unique('g_users')->where('id', '!=', $user->id)->whereIn('status', ['AC', 'IN'])];
				// $validation = 'nullable|digits_between:8,15|unique:g_users,mobile_number,' . $user->id;
			} else {
				$required = 'required';
				$validation = 'nullable|digits_between:8,15';
			}
			$validate = Validator($request->all(), [
				'first_name' => $required,
				'last_name' => $required,
				'mobile_number' => $validation,
			]);

			$attr = [
				'first_name' => 'First Name',
				'last_name' => 'Last Name',
				'mobile_number' => 'Mobile Number',
			];

			$validate->setAttributeNames($attr);

			if ($validate->fails()) {
				$errors = $validate->errors();
				$this->response = array(
					"status" => 300,
					"message" => $errors->first(),
					"data" => null,
					"errors" => $errors,
				);
			} else {

				$user->first_name = $request->first_name;
				$user->last_name = $request->last_name;
				$user->mobile_number = (isset($request->mobile_number) && $request->mobile_number != null) ? $request->mobile_number : '';
				if ($user->save()) {
					$user->profile_picture = ($user->profile_picture != null) ? (stripos($user->profile_picture, "http") !== false) ? $user->profile_picture : URL::asset('/uploads/profiles/' . $user->profile_picture) : '';
					$user->refer_screen = $this->getBusRuleRef('refer_screen') . $this->getBusRuleRef('referrer_amount');

					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(122),
						"data" => $user,
						'cart_count' => $this->getCartCount($user->id),
					);

				} else {
					$this->response = array(
						"status" => 400,
						"message" => ResponseMessages::getStatusCodeMessages(36),
					);
				}
			}

		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}

	}

	// function called to get state list

	/**
	 * @OA\Get(
	 * path="/stateList",
	 * description="stateList",
	 * operationId="stateList",
	 *      tags={"Account"},
	 *   @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function stateList(Request $request) {
		try {
			
			if($request->country_id){
				
				$country_id = $request->country_id;
			}else{
				
				$country_id = Country::where('name', $this->getBusRuleRef('app_country'))->first()->id;
			}

			// get state list based on country
			$CountryStates = State::select("id", "name")->where("country_id", $country_id)->where("status", "AC")->get();
			if ($CountryStates->count() > 0) {
				foreach ($CountryStates as &$value) {
					unset($value->country);
				}
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(144),
					"data" => $CountryStates,
				);
			} else {
				$this->response = array(
					"status" => 404,
					"message" => ResponseMessages::getStatusCodeMessages(219),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function called to get city list

	/**
	 * @OA\Post(
	 * path="/cityList",
	 * description="cityList",
	 * operationId="cityList",
	 *      tags={"Account"},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="cityList",
	 *    @OA\JsonContent(
	 *       required={"state_id","device_token"},
	 *       @OA\Property(property="state_id", type="string", format="text", example="1"),
	 *    )
	 * ),
	 *
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function cityList(Request $request) {
		// check keys are exist
		$res = $this->checkKeys(array_keys($request->all()), array("state_id"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			// get city list based on state
			// dd($request->all());
			$StateCities = City::select("id", "name")->where("state_id", $request->state_id)->where("status", "AC")->get();
			if ($StateCities->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(144),
					"data" => $StateCities,
				);
			} else {
				$this->response = array(
					"status" => 404,
					"message" => ResponseMessages::getStatusCodeMessages(220),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	//function called to get country list 
	/**
	 * @OA\Get(
	 * path="/countryList",
	 * description="countryList",
	 * operationId="countryList",
	 *      tags={"Account"},
	 *   @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */
	public function countryList(Request $request) {

		$Country = Country::where("status", "AC")->get();
		
		try {
			if ($Country->count() > 0) { 
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(144),
					"data" => $Country,
				);
			}else{
				$this->response = array(
					"status" => 404,
					"message" => ResponseMessages::getStatusCodeMessages(219),
				);

			}
			
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function called to get location list
	//
	public function locationList(Request $request) {
		// check keys are exist
		$res = $this->checkKeys(array_keys($request->all()), array("city"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			// get city list based on state
			$locations = Location::where('city_id', $request->city)->where('status', 'AC')->get();

			if (count($locations) > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(41),
					"data" => $locations,
				);
			} else {
				$this->response = array(
					"status" => 404,
					"message" => ResponseMessages::getStatusCodeMessages(42),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		return $this->shut_down();
	}

	// function called to display home page sections
	public function homePageData(Request $request) {
		// check keys are exist
		$user = Auth::user();
		//$res = $this->checkKeys(array_keys($request->all()));
		if (isset($user->id) && $user->id != 0) {
			$res = $this->checkUserActive($user->id);
			if (gettype($res) != 'boolean') {
				return $res;
			}
		}

		try {
			$slider = $this->getSliders('slider');
			$banner = $this->getSliders('banner');
			$offers = $this->getOffers("0");
			$top_offers = $this->getOffers("1");
			$categories = $this->getCategories();
			$cms = $this->getCms();
			$customer_care = $this->getBusRuleRef('customer_care_number');
			//fetch quick grab and exclusive offer products once product module is built at admin panel.
			$quick_grabs = $this->getProducts('quick_grab', $user->id ?? '');
			$exclusives = $this->getProducts('is_exclusive', $user->id ?? '');

			$ncount = 0;

			if (isset($user->id)) {
				$user = User::find($user->id);
				if (isset($user->notifications)) {
					$ncount = $user->notifications->count();
				}

			}

			$this->response = array(
				"status" => 200,
				"message" => ResponseMessages::getStatusCodeMessages(125),
				"currency" => $this->currency,
				"slider" => $slider,
				"banner" => $banner,
				"offers" => $offers,
				"top_offers" => $top_offers,
				"categories" => $categories,
				"quick_grabs" => $quick_grabs,
				"exclusives" => $exclusives,
				"cms" => $cms,
				"customer_care_number" => $customer_care,
				'cart_count' => $this->getCartCount($user->id ?? ''),
				'unread_notification' => $ncount,
			);

		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}

	}

	public function getCms() {
		try
		{
			$cms = CMS::where('status', 'AC')->pluck('content', 'slug');

			$faqs = FAQQuestion::where('status', 'AC')->select('question', 'answer', 'id')->get();

			$cms['faq'] = $faqs;
			return $cms;
		} catch (\Exception $ex) {
			return [];
		}
	}

	public function getSliders($type) {
		try
		{
			$sliders = Slider::select('id', 'category_id', DB::raw("CONCAT('" . URL::asset("uploads/sliders") . "/', image) image"))->where('status', 'AC')->where('type', $type)->orderBy('created_at', 'desc')->get();
			if ($type == 'banner' && $sliders != null && count($sliders) > 0) {
				$sliders = $sliders[0];
			}

			if ($sliders != null && $sliders->count() <= 0) {
				$sliders = null;
			}

			return $sliders;
		} catch (\Exception $ex) {
			return [];
		}
	}

	public function getOffers($top_offer) {
		try
		{
			$query = Offer::select('id', 'category_id', DB::raw("CONCAT('" . URL::asset("uploads/offers") . "/', image) image"))->where('status', 'AC')->whereRaw('? between start_date and end_date', [date('Y-m-d')])->where('is_top_offer', $top_offer);

			$offers = $query->orderBy('created_at', 'desc')->get();

			foreach ($offers as $key => $value) {
				$count = 0;
				//check categories from offer are active and if inactive, remove them
				$array = explode(',', $value->category_id);
				$categories = Category::whereIN('id', $array)->pluck('status', 'id');
				foreach ($array as $k => $v) {
					if ($categories[$v] == 'IN') {
						$count++;
					}
				}
				if ($count == count($array)) {
					unset($offers[$key]);
				}

			}
			if ($top_offer == "1") {
				return array_slice($offers->toArray(), 0, 3);
			}
			return $offers;
		} catch (\Exception $ex) {
			return [];
		}
	}

	public function getCategories() {
		try
		{
			$categories = Category::select('id', 'name', DB::raw("CONCAT('" . URL::asset("uploads/categories") . "/', image) image"))->where('status', 'AC')->whereNULL('parent_id')->orderBy('created_at', 'desc')->limit(9)->get();

			foreach ($categories as &$value) {
				$value->subcategory_count = count($value->childCat);
				unset($value->childCat);
			}
			return $categories;
		} catch (\Exception $ex) {
			return [];
		}
	}

	public function getProducts($column, $user_id) {
		try
		{
			$products = Product::with(['variations' => function ($q) {
				$q->where('status', 'AC');
				$q->orderBy('price', 'asc');
			}])->select('*', DB::raw("CONCAT('" . URL::asset("uploads/products/small") . "/small-', image) as small_image, image"),
				DB::raw("CONCAT('" . URL::asset("uploads/products") . "/', image) image")
			)->where('status', 'AC')->whereHas('variations', function ($q) {
				$q->where('status', 'AC');
			})->where(function ($qu) {
				$qu->whereHas('category', function ($quw) {
					$quw->where('status', 'AC');
				})->orWhereHas('subcategory', function ($quw) {
					$quw->where('status', 'AC');
				});
			})->where('status', 'AC')->where($column, '1')->orderBy('created_at', 'desc')->limit(10)->get();
			$products = $this->formatProducts($products, $user_id);

			return $products;
		} catch (\Exception $ex) {
			return [];
		}
	}

	// function called to get category list

	/**
	 * @OA\get(
	 * path="/categoryList",
	 * description="categoryList",
	 * operationId="categoryList",
	 *      tags={"Products"},
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function categoryList(Request $request) {
		try {
			$query = Category::select('id', 'name', DB::raw("CONCAT('" . URL::asset("uploads/categories") . "/', image) image"))->where('status', 'AC');

			if (isset($request->category_id)) {
				$query->where('parent_id', $request->category_id);
			} else {
				$query->whereNULL('parent_id');
			}
			$category = $query->orderBy('created_at', 'desc')->get();
			if ($category->count() > 0) {

				foreach ($category as &$value) {
					$value->subcategory_count = count($value->childCat);
					unset($value->childCat);
				}
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(144),
					"data" => $category,
				);
			} else {
				$this->response = array(
					"status" => 404,
					"message" => ResponseMessages::getStatusCodeMessages(28),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	/**
	 * @OA\post(
	 * path="/productList",
	 * description="productList",
	 *      tags={"Products"},
	 * operationId="productList",
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="productList",
	 *    @OA\JsonContent(
	 *       required={"category_id", "page_no"},
	 *       @OA\Property(property="category_id", type="string", format="text", example="1"),
	 *       @OA\Property(property="page_no", type="string", format="text", example="0"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="currency", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="banner", type="object"),
	 *       @OA\Property(property="cart_count", type="number")
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	// function called to get product list
	public function productList(Request $request) {
		$res = $this->checkKeys(array_keys($request->all()), array("category_id", "page_no"));

		if (gettype($res) != 'boolean') {
			return $res;
		}

		try {
			$category = Category::find($request->category_id);
			if (isset($category)) {
				$products = Product::with(['variations' => function ($q) {
					$q->where('status', 'AC');
					$q->orderBy('price', 'asc');
				}])->select('*', DB::raw("CONCAT('" . URL::asset("uploads/products") . "/', image) image"))
					->where('status', 'AC')->whereHas('variations', function ($q) {
					$q->where('status', 'AC');
				})->where(function ($q) use ($request, $category) {
					$q->where('parent_id', $request->category_id);
					if (count($category->childCat) > 0) {

						$q->orWhereIN('parent_id', array_column($category->childCat->toArray(), 'id'));
					}
				})->where('status', 'AC')->orderBy('created_at', 'desc')->offset($request->page_no * $this->pagelength)->limit($this->pagelength)->get();

				$user_id = isset($request->user_id) ? $request->user_id : 0;
				$products = $this->formatProducts($products, $user_id);

				if ($products->count() > 0) {
					$banner = URL::asset('/uploads/categories/' . $category->banner);
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(144),
						"currency" => $this->currency,
						"data" => $products,
						"banner" => $banner,
						'cart_count' => $this->getCartCount($user_id),
					);
				} else {
					$this->response = array(
						"status" => 404,
						"message" => ResponseMessages::getStatusCodeMessages(21),
						'cart_count' => $this->getCartCount($user_id),
					);
				}
			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(43),
				);
			}

		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function called to search product list

	/**
	 * @OA\post(
	 * path="/searchProduct",
	 * description="search",
	 *      tags={"Products"},
	 * operationId="search",
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="search",
	 *    @OA\JsonContent(
	 *       required={"text"},
	 *       @OA\Property(property="text", type="string", format="text", example="Av")
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */
	public function search(Request $request) {
		$res = $this->checkKeys(array_keys($request->all()), array("text"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			$user_id = (isset($request->user_id) && $request->user_id != 0) ? $request->user_id : 0;
			$lists = [];

			$products = Product::whereHas('prod_attr')->with('category')->where(function ($q) use ($request) {
				$q->where('name', 'like', '%' . $request->text . '%');
				$q->orWhere('description', 'like', '%' . $request->text . '%');
			})->where(function ($qu) {
				$qu->whereHas('category', function ($quw) {
					$quw->where('status', 'AC');
				})->orWhereHas('subcategory', function ($quw) {
					$quw->where('status', 'AC');
				});
			})->where('status', 'AC')->limit(10)->get();
			if ($products->count() > 0) {
				foreach ($products as &$value) {
					unset($value->variations);
					if (isset($value->category)) {
						$parent = $value->category;
					} elseif (isset($value->subcategory)) {
						$parent = $value->subcategory;
					}

					$list = new Product;
					$list->image = URL::asset('/uploads/categories/' . $parent->image);
					$list->type = 'product';
					$list->text = $value->name . " in " . $parent->name;
					$list->id = $value->id;
					$list->category_id = $parent->id;
					$lists[] = $list;
				}
			}
			/*// else {
				            $subcategories = Category::with('parentCat')->whereNotNull('parent_id')->where(function ($q) use ($request) {
				            $q->where('name', 'like', '%' . $request->text . '%');
				            $q->orWhere('description', 'like', '%' . $request->text . '%');
				            })->where('status', 'AC')->limit(10)->get();
				            if ($subcategories->count() > 0) {
				            foreach ($subcategories as &$value) {
				            $list = new Category;
				            $list->image = URL::asset('/uploads/categories/' . $value->parentCat->image);
				            $list->type = 'subcategory';
				            $list->text = $value->name . " in " . $value->parentCat->name;
				            $list->id = $value->id;
				            $list->category_id = $value->parentCat->id;
				            $lists[] = $list;
				            }
			*/
			// else {
			$categories = Product::with('category')->whereHas('category', function ($qu) use ($request) {
				$qu->where(function ($q) use ($request) {
					$q->where('name', 'like', '%' . $request->text . '%');
					$q->orWhere('description', 'like', '%' . $request->text . '%');
				});
			})->where('status', 'AC')->limit(10)->get();
			if ($categories->count() > 0) {
				foreach ($categories as &$value) {
					$exists = false;
					foreach ($lists as $key) {
						if ($key->type == 'product' && $key->id == $value->id) {
							$exists = true;
							break;
						}
					}
					if (isset($value->category)) {
						$parent = $value->category;
					} elseif (isset($value->subcategory)) {
						$parent = $value->subcategory;
					}
					if (!$exists) {
						unset($value->variations);
						$list = new Product;
						$list->image = URL::asset('/uploads/categories/' . $parent->image);
						$list->type = 'product';
						$list->text = $value->name . " in " . $parent->name;
						$list->id = $value->id;
						$list->category_id = $parent->id;
						$lists[] = $list;
					}
				}
			}
			// }
			// }

			if (count($lists) > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(144),
					"data" => $lists,
					"currency" => $this->currency,
				);
			} else {
				$this->response = array(
					"status" => 404,
					"search_status" => 21,
					"message" => ResponseMessages::getStatusCodeMessages(21),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}
	public function searchProduct(Request $request) {
		$res = $this->checkKeys(array_keys($request->all()), array("text"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			$user_id = (isset($request->user_id) && $request->user_id != 0) ? $request->user_id : 0;
			$lists = [];

			$data = Product::with(['variations' => function ($q) {
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
			})->where(function ($q) use ($request) {
				$q->where('name', 'like', '%' . $request->text . '%');
				$q->orWhere('description', 'like', '%' . $request->text . '%');
			})->groupBy('products.id')->limit(12)->get();

			$products = $this->formatProducts($data, $user_id);

			if (count($products) > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(144),
					"data" => $products,
					"currency" => $this->currency,
				);
			} else {
				$this->response = array(
					"status" => 404,
					"search_status" => 21,
					"message" => ResponseMessages::getStatusCodeMessages(21),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to get product listing from search keyword

	/**
	 * @OA\post(
	 * path="/getSearchResult",
	 * description="getSearchResult",
	 * operationId="getSearchResult",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Products"},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="getSearchResult",
	 *    @OA\JsonContent(
	 *       required={"category_id", "id", "type"},
	 *       @OA\Property(property="category_id", type="string", format="text", example="1"),
	 *       @OA\Property(property="id", type="string", format="text", example="1"),
	 *       @OA\Property(property="type", type="string", format="text", example="category"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="currency", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="cart_count", type="number"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function getSearchResult(Request $request) {
		// check keys are exist
		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array("id", "category_id", "type"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {

			if (isset($user->id) && $user->id != 0) {
				$res = $this->checkUserActive($user->id);
				if (gettype($res) != 'boolean') {
					return $res;
				}
			}

			switch ($request->type) {
			case 'product':
			case 'category':
				$data = [];
				$product = Product::find($request->id)->name;
				$data = Product::whereHas('prod_attr')->with(['variations' => function ($q) {
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
				})->where(function ($q) use ($product) {
					$q->where('name', 'like', '%' . $product . '%');
					$q->orWhere('description', 'like', '%' . $product . '%');
				})->where('parent_id', $request->category_id)->where('status', 'AC')->get();
				break;
			case 'subcategory':
				$data = Product::with(['variations' => function ($q) {
					$q->where('status', 'AC');
					$q->orderBy('price', 'asc');
				}])->select('*', DB::raw("CONCAT('" . URL::asset("uploads/products") . "/', image) image"))
					->where('status', 'AC')->whereHas('variations', function ($q) {
					$q->where('status', 'AC');
				})->where(function ($qu) {
					$qu->whereHas('category', function ($quw) {
						$quw->where('status', 'AC');
					})->orWhereHas('subcategory', function ($quw) {
						$quw->where('status', 'AC');
					});
				})->where('parent_id', $request->id)->get();

				if (count($data) <= 0) {
					$subcat = explode(' ', Category::find($request->id)->name);
					$data = Product::with(['variations' => function ($q) {
						$q->where('status', 'AC');
						$q->orderBy('price', 'asc');
					}])->select('*', DB::raw("CONCAT('" . URL::asset("uploads/products") . "/', image) image"))
						->where('status', 'AC')->whereHas('variations', function ($q) {
						$q->where('status', 'AC');
					})->where(function ($qu) {
						$qu->whereHas('category', function ($quw) {
							$quw->where('status', 'AC');
						})->orWhereHas('subcategory', function ($quw) {
							$quw->where('status', 'AC');
						});
					})->where(function ($q) use ($subcat) {
						$q->where('name', 'like', '%' . $subcat[0] . '%');
						for ($i = 1; $i < count($subcat); $i++) {
							$q->orWhere('name', 'like', '%' . $subcat[$i] . '%');
						}
					})->where('status', 'AC')->where('parent_id', $request->category_id)->get();
				}
				break;
			}
			$products = $this->formatProducts($data, $user->id);
			if ($products->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(144),
					"currency" => $this->currency,
					"data" => $products,
					'cart_count' => $this->getCartCount($user->id),
				);
			} else {
				$this->response = array(
					"status" => 404,
					"message" => ResponseMessages::getStatusCodeMessages(21),
					'cart_count' => $this->getCartCount($user->id),
				);
			}

		} catch (\Exception $ex) {
			//dd($ex);
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to get product listing of specific offer from related categories

	/**
	 * @OA\post(
	 * path="/getOfferDetails",
	 * description="getOfferDetails",
	 * operationId="getOfferDetails",
	 *      tags={"Products"},
	 *  security={{"bearerAuth":{}}},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="getOfferDetails",
	 *    @OA\JsonContent(
	 *       required={"offer_id", "page_no"},
	 *       @OA\Property(property="offer_id", type="string", format="text", example="1"),
	 *       @OA\Property(property="type", type="page_no", format="text", example="2"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="currency", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="banner", type="object"),
	 *       @OA\Property(property="cart_count", type="number"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function getOfferDetails(Request $request) {
		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array("offer_id", "page_no"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		// try {
		$user_id = 0;
		if (isset($user->id) && $user->id != 0) {
			$res = $this->checkUserActive($user->id);
			if (gettype($res) != 'boolean') {
				return $res;
			}
			$user_id = $user->id;
		}
		$offer = Offer::find($request->offer_id);

		$array = [];
		if (isset($offer)) {
			//check categories from offer are active and if inactive, remove them
			$array = explode(',', $offer->category_id);
			$categories = Category::whereIN('id', $array)->orWhereIN('parent_id', $array)->pluck('status', 'id');

			foreach ($categories as $k => $v) {
				if (Offer::whereRaw('find_in_set(?,category_id)', [$k])->where('id', '!=', $offer->id)->whereRaw('? between start_date and end_date', [date('Y-m-d')])->where('status', 'AC')->count() <= 0) {
					if (!in_array($k, $array)) {
						array_push($array, $k);
					}
				}

			}
			foreach ($array as $k => $v) {
				if (isset($categories[$v])) {
					if ($categories[$v] == 'IN') {
						unset($array[$k]);
					}
				}
			}
			$category = implode(',', $array);
		}

		$offers[] = $offer;
		$products = $this->fetchOfferProducts($offers, $array, $request->page_no);

		$user_id = isset($user->id) ? $user->id : 0;
		$products = $this->formatProducts($products, $user_id);

		if ($products->count() > 0) {
			$banner = URL::asset('/uploads/offers/' . $offer->image);
			$this->response = array(
				"status" => 200,
				"message" => ResponseMessages::getStatusCodeMessages(144),
				"currency" => $this->currency,
				"data" => $products,
				"banner" => $banner,
				'cart_count' => $this->getCartCount($user_id),
			);
		} else {
			$this->response = array(
				"status" => 404,
				"message" => ResponseMessages::getStatusCodeMessages(21),
				'cart_count' => $this->getCartCount($user_id),
			);
		}
		// } catch (\Exception $ex) {
		//     $this->response = array(
		//         "status" => 501,
		//         "message" => ResponseMessages::getStatusCodeMessages(501),
		//     );
		// }
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to get product details for quick grab and exclusive view all
	/**
	 * @OA\post(
	 * path="/getExcProducts",
	 * description="getExcProducts",
	 * operationId="getExcProducts",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Products"},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="getExcProducts",
	 *    @OA\JsonContent(
	 *       required={"type", "page_no"},
	 *       @OA\Property(property="type", type="string", format="text", example="is_exclusive"),
	 *       @OA\Property(property="page_no", type="string", format="text", example="0"),
	 *    )
	 * ),
	 *
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="currency", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="cart_count", type="number"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */
	public function getExcProducts(Request $request) {
		$user = Auth::user();

		$res = $this->checkKeys(array_keys($request->all()), array("type", "page_no"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		try
		{
			$user_id = 0;
			if (isset($user->id) && $user->id != 0) {
				$res = $this->checkUserActive($user->id);
				if (gettype($res) != 'boolean') {
					return $res;
				}
				$user_id = $user->id;
			}

			$products = Product::with(['variations' => function ($q) {
				$q->where('status', 'AC');
				$q->orderBy('price', 'asc');
			}])->select('*', DB::raw("CONCAT('" . URL::asset("uploads/products/medium") . "/medium-', image) as medium_image, image"), DB::raw("CONCAT('" . URL::asset("uploads/products") . "/', image) image"))
				->where('status', 'AC')->whereHas('variations', function ($q) {
				$q->where('status', 'AC');
			})->where(function ($qu) {
				$qu->whereHas('category', function ($quw) {
					$quw->where('status', 'AC');
				})->orWhereHas('subcategory', function ($quw) {
					$quw->where('status', 'AC');
				});
			})->where('status', 'AC')->where($request->type, '1');

			if (strpos($request, '/api') !== false) {
				$products = $products->orderBy('created_at', 'desc')->offset($request->page_no * $this->pagelength)->limit($this->pagelength)->get();
			} else {
				$products = $products->paginate();
			}

			$user_id = isset($user->id) ? $user->id : 0;
			$products = $this->formatProducts($products, $user_id);

			if ($products->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(144),
					"currency" => $this->currency,
					"data" => $products,
					'cart_count' => $this->getCartCount($user_id),
				);
			} else {
				$this->response = array(
					"status" => 404,
					"message" => ResponseMessages::getStatusCodeMessages(21),
					'cart_count' => $this->getCartCount($user_id),
				);
			}
		} catch (\Exception $ex) {
			//dd($ex);

			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to get all offers with dynamic key (top_offer=0/1)
	/**
	 * @OA\post(
	 * path="/getAllOffers",
	 * description="getAllOffers",
	 * operationId="getAllOffers",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Products"},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="getAllOffers",
	 *    @OA\JsonContent(
	 *       required={"top_offer", "page_no"},
	 *       @OA\Property(property="top_offer", type="string", format="text", example="1"),
	 *       @OA\Property(property="page_no", type="string", format="text", example="0"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="currency", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="product", type="object"),
	 *       @OA\Property(property="cart_count", type="number"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function getAllOffers(Request $request) {

		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array("top_offer", "page_no"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		try
		{
			$user_id = 0;
			if (isset($user->id) && $user->id != 0) {
				$res = $this->checkUserActive($user->id);
				if (gettype($res) != 'boolean') {
					return $res;
				}
				$user_id = $user->id;
			}

			$query = Offer::select('id', 'category_id', 'min_amount', DB::raw("CONCAT('" . URL::asset("uploads/offers") . "/', image) image"))->where('status', 'AC')->whereRaw('? between start_date and end_date', [date('Y-m-d')])->where('is_top_offer', $request->top_offer);

			$offers = $query->orderBy('created_at', 'desc')->get();
			$categories = [];
			foreach ($offers as $key => $value) {
				$count = 0;
				//check categories from offer are active and if inactive, remove them
				$array = explode(',', $value->category_id);
				$check = Category::whereIN('id', $array)->pluck('status', 'id');
				foreach ($array as $k => $v) {
					if ($check[$v] == 'IN') {
						$count++;
					} else {
						// get all those subcategories which do no have any other offer
						$subcats = Category::where('parent_id', $v)->where('status', 'AC')->pluck('id');
						foreach ($subcats as $val) {
							$off = Offer::whereRaw('find_in_set(?,category_id)', [$val])->where('id', '!=', $value->id)->whereRaw('? between start_date and end_date', [date('Y-m-d')])->where('status', 'AC')->count();
							if ($off <= 0) {
								$categories[] = $val;
							}
						}
						if (!in_array($v, $categories)) {
							$categories[] = $v;
						}
					}

				}
				if ($count == count($array)) {
					unset($offers[$key]);
				}

			}

			$products = $this->fetchOfferProducts($offers, $categories, $request->page_no);

			$user_id = isset($user->id) ? $user->id : 0;
			$products = $this->formatProducts($products, $user_id);

			if ($offers->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(144),
					"currency" => $this->currency,
					"data" => $offers,
					"products" => $products,
					'cart_count' => $this->getCartCount($user_id),
				);
			} else {
				$this->response = array(
					"status" => 404,
					"message" => ResponseMessages::getStatusCodeMessages(21),
					'cart_count' => $this->getCartCount($user_id),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function called to get product description
	/**
	 * @OA\post(
	 * path="/getProductDescription",
	 * description="getProductDescription",
	 * operationId="getProductDescription",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Products"},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="getProductDescription",
	 *    @OA\JsonContent(
	 *       required={"id", "page_no"},
	 *       @OA\Property(property="id", type="string", format="text", example="1"),
	 *       @OA\Property(property="page_no", type="string", format="text", example="0"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="currency", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="cart_count", type="number"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */
	public function getProductDescription(Request $request) {

		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array("id", "type"));

		if (gettype($res) != 'boolean') {
			return $res;
		}

		try {
			$product_id = $request->id;
			if ($request->type == 'variation') {
				$product_id = ProductVariation::find($request->id)->product_id;
			}

			$product = Product::whereHas('prod_attr')->select("*",
				DB::raw("CONCAT('" . URL::asset("uploads/products/medium") . "/medium-', image) as medium_image, image"),
				DB::raw("CONCAT('" . URL::asset("uploads/products") . "/', image) image"))
				->with(['variations' => function ($q) use ($request) {
					$q->with(['product_units' => function ($q) {
						$q->select('id', 'unit');
					}])->where('status', 'AC')->orderBy('price', 'asc');
				}])->where('status', 'AC')->where('id', $product_id)->first();

			$user_id = (isset($user->id) && $user->id != 0) ? $user->id : 0;

			if ($product) {
				$products[] = $product;
				$product = $this->formatProducts($products, $user_id);

				// display price and weight of the selected variation
				$var = '';

				$d = $product[0]->variations;

				foreach ($product[0]->variations as $value) {

					if ($request->type == 'variation') {
						if ($value->id == $request->id) {
							$var = $value;
							break;
						}
					} else {

						// dd($product[0]->product_variation_option);
						$attr = $value->variationOption->pluck('attribute_value_id')->toArray();

						if (count(array_diff($attr, $product[0]->product_variation_option)) <= 0) {
							$var = $value;
							break;
						}
					}
				}

				$option = [];

				foreach ($product[0]->variationsAttribute as $key => $d) {

					if (!isset($option[$d->attribute->id])) {
						$temp_array = [
							'attribute_name' => $d->attribute->name,
							'attribute_option' => AttributeValue::whereIn('id', $product[0]->variationsAttribute->where('attribute_id', $d->attribute->id)->pluck('attribute_value_id')->toArray())->select('id', 'value')->get(),
						];

						$option[$d->attribute->id] = $temp_array;
					}
				}

				$product[0]->qty = $var->qty;
				$product[0]->max_qty = $var->max_qty;
				$product[0]->options = $option;
				$product[0]->price = $var->price;
				$product[0]->special_price = $var->special_price;
				$product[0]->is_available = $var->is_available;
				if (isset($var->warning_msg)) {
					$product[0]->warning_msg = $var->warning_msg;
				}
				// dd($product[0]);

				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(144),
					"currency" => $this->currency,
					"data" => $product[0],
					'cart_count' => $this->getCartCount($user_id),
				);
			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(44),
					'cart_count' => $this->getCartCount($user_id),
				);
			}
		} catch (\Exception $ex) {
			// //dd($ex);
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function called to add product to cart
	/**
	 * @OA\post(
	 * path="/addToCart",
	 * description="addToCart",
	 * operationId="addToCart",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Cart"},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="addToCart",
	 *    @OA\JsonContent(
	 *       required={"product_id", "product_variation_id","type"},
	 *       @OA\Property(property="product_id", type="string", format="text", example="1"),
	 *       @OA\Property(property="product_variation_id", type="string", format="text", example="1"),
	 *       @OA\Property(property="type", type="string", format="text", example="plus/minus"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="currency", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="cart_count", type="number"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function addToCart(Request $request) {
		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array("product_id", "product_variation_id", "type"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		try {
			$check = ProductVariation::where('product_id', $request->product_id)->where('id', $request->product_variation_id)->first();
			if (isset($check->id)) {
				$cart = Cart::where(['user_id' => $user->id, 'status' => 'AC'])->first();
				if (!$cart) {
					$cart = new Cart();
					$cart->cart_code = Helper::generateNumber('c_carts', 'cart_code');
					$cart->user_id = $user->id;
					$cart->payment_method = 'cod';
					$cart->save();
				}
				$addcart = CartItem::where('cart_id', $cart->id)->where('product_id', $request->product_id)->where('product_variation_id', $request->product_variation_id)->where('status', 'AC')->first();

				if (!isset($addcart->id)) {
					$addcart = new CartItem;
					$quantity = ($request->type == 'setqty' ? $request->qty : 1);
				} else {
					$quantity = ($request->type == 'plus') ? $addcart->qty + 1 : ($request->type == 'setqty' ? $request->qty : $addcart->qty - 1);
				}
				$max_qty = ($check->max_qty <= $check->qty) ? $check->max_qty : $check->qty;
				if ($quantity <= $max_qty) {
					if ($quantity == 0) {
						$message = ResponseMessages::getStatusCodeMessages(12);
						$addcart->delete();
						if (count($cart->cart_items) <= 0) {
							$cart->delete();
						}
					} else {
						if ($request->type == 'minus') {
							$message = ResponseMessages::getStatusCodeMessages(8);
						} else {
							$message = ResponseMessages::getStatusCodeMessages(11);
						}

						$productDetail = $this->checkProductOffer($request->product_variation_id);
						$addcart->cart_id = $cart->id;
						$addcart->product_id = $request->product_id;
						$addcart->product_variation_id = $request->product_variation_id;
						$addcart->qty = $quantity;
						$addcart->price = $productDetail->price;
						$addcart->special_price = $productDetail->discount_price;

						$addcart->offer_id = isset($productDetail->offer_id) ? $productDetail->offer_id : null;

						// check if any item exists in cart for single day. If yes, then keep the same date and time for the new item added.
						$selected = CartItem::where('cart_id', $cart->id)->where('scheduled', '0')->where('status', 'AC')->first();

						if (isset($selected->id)) {
							$addcart->delivery_slot_id = $selected->delivery_slot_id;

							$addcart->start_date = $selected->start_date;
							$addcart->end_date = $selected->end_date;
						} else {
							// get recent date and slot available
							$data = $this->getRecentAvailableTime();
							if (isset($data['id'])) {
								$start_date = $data['date'];
								$end_date = $data['date'];
								$slot = $data['id'];
							} else {

								$start_date = date('Y-m-d', strtotime('+3 days'));
								$end_date = date('Y-m-d', strtotime('+3 days'));
								$day = date('D', strtotime($start_date));
								// by default add the day for which delivery slot is available
								$result = DeliverySlot::where('day', strtolower($day))->where('type', 'single')->where('status', 'AC')->orderBy('from_time')->groupBy('day')->get();
								$slot = ($result) ? $result->id : null;
							}
							$addcart->delivery_slot_id = $slot;
							$addcart->start_date = $start_date;
							$addcart->end_date = $end_date;
						}

						$addcart->save();

						// if coupon code is applied on the cart and products with same categories are already present, then update coupon code id for the newly added product
						if (isset($cart) && $cart->user_id == $user->id) {

							$code_id = CartItem::where('cart_id', $cart->id)->whereNotNull('coupon_code_id')->first();

							if ($code_id) {
								$code = CouponCode::whereHas('coupon_range', function ($q) use ($code_id) {
									$q->where('id', $code_id->coupon_code_id);
								})->first();

								// check if coupon code expired
								if (date('Y-m-d', strtotime($code->start_date)) <= date('Y-m-d') && date('Y-m-d', strtotime($code->end_date)) >= date('Y-m-d')) {

									// check usage of coupon code
									$query = Order::whereHas('order_items', function ($q) use ($code_id) {
										$q->whereHas('coupon_range', function ($qu) use ($code_id) {
											$qu->where('coupon_code_id', $code_id->coupon_code_id);
										});
									});

									$total_count = $query->count();
									$user_count = $query->where('user_id', $user->id)->count();

									if ($total_count < $code->max_use_total && $user_count < $code->max_use) {

										$categories = $this->getCouponCatSubcat($code);

										if (in_array($check->product->parent_id, $categories)) {
											$range = 0;
											$discount = 0;
											// check if product special price lies within the coupon code range
											foreach ($code->coupon_range as $k => $v) {
												if (($addcart->special_price * $addcart->qty) >= $v->min_price && ($addcart->special_price * $addcart->qty) <= $v->max_price) {
													$range = $v->id;
													$discount = $v->value;
													break;
												}
											}
											if ($range != 0) {
												$addcart->coupon_code_id = $range;
												$addcart->coupon_discount = $discount;
												$addcart->save();
											} else {
												$addcart->coupon_code_id = null;
												$addcart->coupon_discount = null;
												$addcart->save();
											}
										} else {
											$addcart->coupon_code_id = null;
											$addcart->coupon_discount = null;
											$addcart->save();
										}

									} else {
										$addcart->coupon_code_id = null;
										$addcart->coupon_discount = null;
										$addcart->save();
									}
								} else {
									$addcart->coupon_code_id = null;
									$addcart->coupon_discount = null;
									$addcart->save();
								}
							}
						}
					}

					$cartcount = 0;
					$query = CartItem::selectRaw('sum(qty) as total')->whereHas('product_items', function ($q) {
						$q->where('status', 'AC');
						$q->whereHas('category', function ($qu) {
							$qu->where('status', 'AC');
						})->orWhereHas('subcategory', function ($qu) {
							$qu->where('status', 'AC');
						});
					})->where('cart_id', $cart->id)->groupBy('cart_id')->where('status', 'AC')->first();

					if ($query) {
						$cartcount = intval($query->total);
					}

					if (isset($request->response_type) && $request->response_type == 'fav') {
						$favs = ProductFavourite::where('user_id', $user->id)->whereStatus('AC')->select('product_id', 'product_variation_id')->get();
						$products = [];
						foreach ($favs as $key => &$value) {

							$prod = Product::where('id', $value->product_id)->select('*', DB::raw("CONCAT('" . URL::asset("uploads/products") . "/', image) image"))->with(['variations' => function ($q) use ($value) {
								$q->where('id', $value->product_variation_id);
							}])->first();

							$products[] = $prod;
						}
						$product = $this->formatProducts($products, $user->id, 'fav');
					} else {
						$product = Product::select("*", DB::raw("CONCAT('" . URL::asset("uploads/products") . "/', image) image"))->with(['variations' => function ($q) use ($request) {

							$q->with(['product_units' => function ($q) {
								$q->select('id', 'unit');
							}])->where('status', 'AC')->orderBy('price', 'asc');
						}])->where('status', 'AC')->where('id', $request->product_id)->first();

						$products[] = $product;
						$product = $this->formatProducts($products, $user->id)[0];
					}

					if ($addcart->id) {
						$this->response = array(
							"status" => 200,
							"message" => $message,
							"currency" => $this->currency,
							"cart_count" => $cartcount,
							"data" => $product,

						);
					} elseif ($quantity == 0) {
						$this->response = array(
							"status" => 404,
							"message" => ResponseMessages::getStatusCodeMessages(12),
							"cart_count" => $cartcount,
							"data" => $product,
						);
					}
				} else {
					$this->response = array(
						"status" => 400,
						"message" => ResponseMessages::getStatusCodeMessages(50),
					);
				}

			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(45),
				);
			}

		} catch (\Exception $ex) {
			//dd($ex);
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function called to get cart items

	// function called to add product to cart

	/**
	 * @OA\get(
	 * path="/getCartItems",
	 * description="getCartItems",
	 * operationId="getCartItems",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Cart"},
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="currency", type="string"),
	 *       @OA\Property(property="cart_count", type="number"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function getCartItems(Request $request) {
		// $res = $this->checkKeys(array_keys($request->all()), array("user_id"));
		$user = Auth::user();

		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		// try {
		$cart_exists = Cart::where('user_id', $user->id)->where('status', 'AC')->first();
		$user = User::find($user->id);

		if ($cart_exists) {
			$credit_usage = (int) $cart_exists->wallet;
			$usercartsitem = CartItem::where('cart_id', $cart_exists->id)->with('product_items', 'product_variation', 'delivery_slot')->whereHas('product_items', function ($q) {
				$q->where('status', 'AC');
				$q->whereHas('category', function ($qu) {
					$qu->where('status', 'AC');
				})->orWhereHas('subcategory', function ($qu) {
					$qu->where('status', 'AC');
				});
			})->where('status', 'AC')->get();
			$coupon_code_id = 0;
			$save = 0;

			if ($usercartsitem->count() > 0) {
				$total_amount = $shipping_fee = $coupon_discount = 0;
				foreach ($usercartsitem as $key => &$value) {
					// update start date and end date if set to old date in case of single day and multiple day
					if (date('Y-m-d', strtotime($value->start_date)) <= date('Y-m-d')) {
						// get recent date and time available
						$data = $this->getRecentAvailableTime();
						if (isset($data['id'])) {
							$start_date = $data['date'];
							$end_date = $data['date'];
							$slot = $data['id'];
						} else {

							$start_date = date('Y-m-d', strtotime('+3 days'));
							$end_date = date('Y-m-d', strtotime('+3 days'));
							$day = date('D', strtotime($start_date));
							// by default add the day for which delivery slot is available
							$result = DeliverySlot::where('day', strtolower($day))->where('type', 'single')->where('status', 'AC')->orderBy('from_time')->groupBy('day')->get();
							$slot = ($result) ? $result->id : null;
						}
						if (date('Y-m-d', strtotime($value->end_date)) <= date('Y-m-d')) {
							$value->scheduled = "0";
							$value->end_date = $end_date;
						}

						$value->start_date = $start_date;
						$value->delivery_slot_id = $slot;
						$value->save();
					}

					// check if offer on any variation has changed
					$product_variation = $this->checkProductOffer($value->product_variation_id);

					if (isset($product_variation->offer_id) && $product_variation->offer_id != $value->offer_id) {

						$value->offer_id = isset($product_variation->offer_id) ? $product_variation->offer_id : null;
						$value->special_price = $product_variation->discount_price;
						$value->price = $product_variation->price;
						$value->coupon_code_id = null;
						$value->coupon_discount = null;
						$value->save();
					} elseif (!isset($product_variation->offer_id)) {
						$value->offer_id = null;
						$value->special_price = $product_variation->discount_price;
						$value->price = $product_variation->price;
						$value->save();
					}

					// update coupon code in cart details if coupon code expires on is changed

					if ($value->offer_id == null && count($cart_exists->coupon_range) > 0) {
						$coupon = $this->checkProductCoupon($value->product_variation_id, $user->id, $value->qty);
						if ($coupon != null) {
							$value->coupon_code_id = $coupon['range'];
							$value->coupon_discount = $coupon['discount'];
							$value->save();
						} else {
							$value->coupon_code_id = null;
							$value->coupon_discount = null;
							$value->save();
						}

					}

					// store coupon range id if exists
					if ($coupon_code_id == 0) {
						$coupon_code_id = ($value->coupon_code_id != null) ? $value->coupon_code_id : 0;

					}
					// check product availability
					$available_qty = $this->checkProductAvailability($value->product_variation_id);

					if ($available_qty < $value->qty) {
						$value->qty = $available_qty;
						$value->save();
						$value->is_available = ($available_qty >= 1) ? 1 : 0;
						$value->warning_msg = ($available_qty > 1) ? ("Only " . $available_qty . " are left") : (($available_qty == 0) ? "Out of Stock" : "Only " . $available_qty . " is left");
						if ($available_qty == 0) {
							$value->delete();
							if ($usercartsitem->count() == 0) {
								$cart_exists->delete();
								break;
							}
						}
					} else {
						$value->is_available = 2;
					}

					$value->product_name = $value->product_items->name . ' (' . $value->product_variation->specifications . ')';
					$value->product_image = URL::asset('uploads/products/' . $value->product_items->image);
					$value->product_medium_image = URL::asset('uploads/products/medium/medium-' . $value->product_items->image);
					// $value->weight = $value->product_variation->weight . $value->product_variation->product_units->unit;
					$value->discount = $this->calcDiscount($value->special_price, $value->price);
					$value->delivery_slots = date('h:i A', strtotime($value->delivery_slot->from_time)) . ' to ' . date('h:i A', strtotime($value->delivery_slot->to_time));
					$value->delivery_slot_type = $value->delivery_slot->type;
					unset($value->product_items);
					unset($value->product_variation);
					unset($value->delivery_slot);
					$value->scheduled = (int) $value->scheduled;
					$fav = ProductFavourite::where('product_id', $value->product_id)->where('product_variation_id', $value->product_variation_id)->where('user_id', $user->id)->whereStatus('AC')->count();
					$value->is_favorite = $fav > 0 ? 1 : 0;

					$total_qty = (date_diff(date_create($value->start_date), date_create($value->end_date))->format('%a') + 1) * $value->qty;

					// calculate total_amount
					$total_amount += ($value->special_price * $total_qty);

					// calculate total coupon discount
					if ($value->coupon_discount != null) {
						$coupon_discount += round(($value->special_price * $total_qty * $value->coupon_discount) / 100, 2);
					}
					$value->max_qty = $product_variation->max_qty;
					$save += $total_qty * ($value->price - $value->special_price);

				}

				$code = null;
				if ($coupon_code_id != 0 && $code == '') {
					$code = CouponCode::whereHas('coupon_range', function ($q) use ($coupon_code_id) {
						$q->where('id', $coupon_code_id);
					})->first();
					if ($code == null) {
						$code = '';
					}

				}

				$address = null;
				if ($cart_exists->address_id != null) {
					$address = UserAddress::where('id', $cart_exists->address_id)->whereStatus('AC')->first();
					if (!isset($address->id)) {
						$address = UserAddress::where('user_id', $user->id)->whereStatus('AC')->first();
					}

					if ($address) {
						$address->city = $address->acity->name ?? '';
						$address->state = $address->astate->name ?? '';
						$address->country = $address->acountry->name ?? '';
						$address->default = (int) $address->default ?? '';
						unset($address->acity);
						unset($address->astate);
						unset($address->acountry);
					}

				} else {
					$address = UserAddress::where('user_id', $user->id)->where('default', "1")->whereStatus('AC')->first();
					if (!isset($address->id)) {
						$address = UserAddress::where('user_id', $user->id)->whereStatus('AC')->first();
					}

					if (isset($address->id)) {
						$address->city = $address->acity->name;
						$address->state = $address->astate->name;
						$address->country = $address->acountry->name;
						$address->default = (int) $address->default;
						unset($address->acity);
						unset($address->astate);
						unset($address->acountry);
						$cart_exists->address_id = $address->id;
						$cart_exists->save();
					}
				}

				$payment = '';
				if ($cart_exists->payment_method != null) {
					$payment = Config::get('constants.PAYMENT_METHOD.' . $cart_exists->payment_method);
				}

				// calculate shipping charge
				$amount = $total_amount - $coupon_discount;
				$fee = ShippingCharge::whereRaw("? between min_price and max_price", [$amount])->where('status', 'AC')->first();

				if (isset($fee->id)) {
					$shipping_fee = $fee->shipping_charge;
				}

				$credits_used = (($amount + $shipping_fee) >= $user->wallet_amount) ? $user->wallet_amount : ($amount + $shipping_fee);

				if ($credit_usage == 0) {
					$credits_used = 0;
				}
				$savings = round(($coupon_discount + $save), 2);
				$grand_total = round(($total_amount + $shipping_fee - ($coupon_discount + $credits_used)), 2);

				//  && $credits_used == $user->wallet_amount
				if ($credit_usage == 1 && (int) $user->wallet_amount != 0 && (int) $grand_total == 0) {
					$cart_exists->payment_method = 'wallet';
					$cart_exists->save();
					$payment = 'wallet';
				}

				$response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(144),
					"currency" => $this->currency,
					"data" => $usercartsitem,
					'cart_count' => $this->getCartCount($user->id),
					'payment' => $payment,
					'wallet' => sprintf("%.2f", $user->wallet_amount),
					'total_amount' => sprintf("%.2f", $total_amount),
					'coupon_discount' => sprintf("%.2f", $coupon_discount),
					'shipping_fee' => sprintf("%.2f", $shipping_fee),
					'grand_total' => sprintf("%.2f", $grand_total),
					'savings' => sprintf("%.2f", $savings),
					'credit_usage' => $credit_usage,
					'credits_used' => sprintf("%.2f", $credits_used),
					'payment_key' => $cart_exists->payment_method,
				);
				// set code and address key only if they are not null
				($code) ? ($response['code'] = $code) : '';
				($address) ? ($response['address'] = $address) : '';

				$this->response = $response;
			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(25),
					"currency" => $this->currency,
					'cart_count' => $this->getCartCount($user->id),
				);
			}
		} else {
			$this->response = array(
				"status" => 400,
				"message" => ResponseMessages::getStatusCodeMessages(25),
			);
		}

		// } catch (\Exception $ex) {

		//     $this->response = array(
		//         "status" => 501,
		//         "message" => ResponseMessages::getStatusCodeMessages(501),
		//     );
		// }
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return json_encode($this->response);
		}
	}

	// function called to delete cart item
	/**
	 * @OA\Delete(
	 * path="/deleteCartItems",
	 * description="deleteCartItems",
	 * operationId="deleteCartItems",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Cart"},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="deleteCartItems",
	 *    @OA\JsonContent(
	 *       required={"item_id"},
	 *       @OA\Property(property="item_id", type="string", format="text", example="1"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="cart_count", type="number"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function deleteCartItems(Request $request) {
		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array("item_id"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			$cart_exists = Cart::where('user_id', $user->id)->where('status', 'AC')->first();

			if ($cart_exists) {
				$deleteCartItems = CartItem::where('cart_id', $cart_exists->id)->where('id', $request->item_id)->where('status', 'AC')->first();

				if (isset($deleteCartItems->id) && $deleteCartItems->delete()) {
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(23),
						'cart_count' => $this->getCartCount($user->id),

					);
				} else {
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(46),
						'cart_count' => $this->getCartCount($user->id),

					);
				}

			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(25),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		return $this->shut_down();
	}

	// function called to clear cart
	/**
	 * @OA\get(
	 * path="/clearCart",
	 * description="clearCart",
	 * operationId="clearCart",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Cart"},
	 *   @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *      @OA\MediaType(
	 *           mediaType="application/json",
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated"
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request"
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found"
	 *   ),
	 *      @OA\Response(
	 *          response=403,
	 *          description="Forbidden"
	 *      )
	 * ),
	 */

	public function clearCart(Request $request) {
		$user = Auth::user();
		//$res = $this->checkKeys(array_keys($request->all()));
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			$cart_exists = Cart::where('user_id', $user->id)->where('status', 'AC')->first();

			if ($cart_exists) {
				$deleteCartItems = CartItem::where('cart_id', $cart_exists->id)->where('status', 'AC')->delete();

				// if ($deleteCartItems) {
				$cart_exists->delete();
				// }

				if (Cart::where('user_id', $user->id)->where('status', 'AC')->first()) {
					$this->response = array(
						"status" => 300,
						"message" => ResponseMessages::getStatusCodeMessages(27),
						'cart_count' => 0,
					);
				} else {
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(26),
						'cart_count' => $this->getCartCount($user->id),
					);
				}
			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(25),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		return $this->shut_down();
	}

	// function called to fetch delivery slots
	/**
	 * @OA\post(
	 * path="/getDeliverySlots",
	 * description="getDeliverySlots",
	 * operationId="getDeliverySlots",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Cart"},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="getDeliverySlots",
	 *    @OA\JsonContent(
	 *       required={"type","item_id"},
	 *       @OA\Property(property="type", type="string", format="text", example="multiple"),
	 *       @OA\Property(property="item_id", type="string", format="text", example="1"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="days", type="object"),
	 *       @OA\Property(property="selected_time", type="number"),
	 *       @OA\Property(property="selected_date", type="date"),
	 *       @OA\Property(property="alert_msg", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function getDeliverySlots(Request $request) {
		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array("type", "item_id"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			$slots = [];
			$selected = 0;
			$selected_date = 0;
			$selected_time = 0;
			$start_date = 0;
			$end_date = 0;
			if ($request->type == 'multiple') {
				// get default selected delivery slot id
				$select = CartItem::find($request->item_id);
				if (isset($select->id) && $select->scheduled == '1') {
					$selected = $select->delivery_slot_id;
					$start_date = $select->start_date;
					$end_date = $select->end_date;
				}

				if ($start_date == 0) {
					$start_date = date('Y-m-d', strtotime('+3 days'));
					$end_date = date('Y-m-d', strtotime('+6 days'));
				}

				$slots = DeliverySlot::where('type', $request->type)->where('status', 'AC')->get();
				foreach ($slots as $key => &$value) {
					$id = $value->id;
					$val = new DeliverySlot;
					$val->id = $id;
					$val->slot = date('h:i A', strtotime($value->from_time)) . ' to ' . date('h:i A', strtotime($value->to_time));
					if ($value->status == 'IN') {
						$val->disabled = 1;
						$val->warning_msg = "Delivery is disabled for this slot";
					} else {
						if ($selected == 0) {
							$selected = $id;
							$val->is_selected = 1;
						} elseif ($selected == $id) {
							$val->is_selected = 1;
						} else {
							$val->is_selected = 0;
						}

					}
					$slots[$key] = $val;
				}

				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(144),
					"slots" => $slots,
					"start_date" => $start_date,
					"end_date" => $end_date,
					"selected" => $selected,
					"alert_msg" => ResponseMessages::getStatusCodeMessages(322),
					"warning_msg" => "Sorry! This date delivery slots are full. Please check with next day available delivery slots. Thank you",
				);
			}
			$days = [];
			if ($request->type == 'single') {
				// get default selected delivery slot id and date from the items already present in cart
				$selected = CartItem::find($request->item_id);

				if (isset($selected->id) && $selected->scheduled == '0') {
					$selected_date = date('Y-m-d', strtotime($selected->start_date));
					$selected_time = $selected->delivery_slot_id;
				}
				for ($i = 3; $i <= 9; $i++) {
					$obj = new DeliverySlot;
					$obj->day = date('D', strtotime("+$i day"));
					$obj->date = date('Y-m-d', strtotime("+$i day"));

					$result = DeliverySlot::where('day', strtolower($obj->day))->where('type', $request->type)->where('status', 'AC')->get();

					if (count($result) > 0) {
						if ($selected_date == 0) {
							$selected_date = date('Y-m-d', strtotime($obj->date));
							$obj->is_selected = 1;
						} elseif ($selected_date == date('Y-m-d', strtotime($obj->date))) {
							$obj->is_selected = 1;
						} else {
							$obj->is_selected = 0;
						}
						foreach ($result as $key => &$value) {
							$id = $value->id;

							$val = new DeliverySlot;
							$val->id = $id;
							$val->slot = date('h:i A', strtotime($value->from_time)) . ' to ' . date('h:i A', strtotime($value->to_time));
							if ($value->status == 'IN') {
								$val->disabled = 1;
								$val->warning_msg = "Delivery is disabled for this slot";
							} else {
								if (count($value->product_orders) >= $value->max_order) {
									$val->disabled = 1;
									$val->warning_msg = "This slot has reached its max order limit";
								} else {
									if ($selected_time == 0) {
										$selected_time = $id;
										$val->is_selected = 1;
									} elseif ($selected_time == $id) {
										$val->is_selected = 1;
									} else {
										$val->is_selected = 0;
									}
								}
							}
							$result[$key] = $val;
						}
					} else {
						$obj->is_selected = 0;
						$sobj = new DeliverySlot;
						$sobj->is_available = 0;
						$sobj->is_selected = 0;
						$sobj->warning_msg = "Sorry! This date delivery slots are full. Please check with next day available delivery slots. Thank you";
						$result[] = $sobj;
					}
					// $slots[] = $result;
					$obj->slots = $result;
					$days[] = $obj;
				}
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(144),
					"days" => $days,
					"selected_time" => $selected_time,
					"selected_date" => $selected_date,
					"alert_msg" => ResponseMessages::getStatusCodeMessages(323),
				);
			}

		} catch (\Exception $ex) {
			//dd($ex);
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		return $this->shut_down();
	}

	// function called to set delivery slot for each item
	/**
	 * @OA\post(
	 * path="/setProductDateTime",
	 * description="setProductDateTime",
	 * operationId="setProductDateTime",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Cart"},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="setProductDateTime - slot(optional)",
	 *    @OA\JsonContent(
	 *       required={"type","cart_item_id"},
	 *       @OA\Property(property="type", type="string", format="text"),
	 *       @OA\Property(property="cart_item_id", type="string", format="text", example="1"),
	 *       @OA\Property(property="slot", type="string", format="text"),
	 *    ),
	 * ),
	 * @OA\RequestBody(
	 *  required=false,
	 *    @OA\JsonContent(
	 *       @OA\Property(property="slot", type="string", format="date"),
	 *    )
	 * ),
	 *   @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *      @OA\MediaType(
	 *           mediaType="application/json",
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated"
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request"
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found"
	 *   ),
	 *      @OA\Response(
	 *          response=403,
	 *          description="Forbidden"
	 *      )
	 * ),
	 */

	public function setProductDateTime(Request $request) {

		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array("cart_item_id", "type"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			$cart = CartItem::find($request->cart_item_id);

			if (isset($cart) && $cart->cart->user_id == $user->id) {
				if ($request->type == 'multiple') {
					$res = $this->checkKeys(array_keys($request->all()), array("start_date", "end_date", "slot"));

					if (gettype($res) != 'boolean') {
						return $res;
					}
					$cart->scheduled = "1";
					$cart->start_date = date('Y-m-d', strtotime($request->start_date));
					$cart->end_date = date('Y-m-d', strtotime($request->end_date));
					$cart->delivery_slot_id = $request->slot;
				}

				if ($request->type == 'single') {
					$res = $this->checkKeys(array_keys($request->all()), array("date", "slot"));

					if (gettype($res) != 'boolean') {
						return $res;
					}
					$cart->scheduled = "0";
					$cart->start_date = date('Y-m-d', strtotime($request->date));
					$cart->end_date = date('Y-m-d', strtotime($request->date));
					$cart->delivery_slot_id = $request->slot;
				}
				if ($cart->save()) {
					// update items with overlapping date for date and time
					$check_slot = $this->checkSlot($cart);

					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(13),
					);
				} else {
					$this->response = array(
						"status" => 400,
						"message" => ResponseMessages::getStatusCodeMessages(36),
					);
				}
			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(46),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		return $this->shut_down();
	}

	// function called to apply coupon code
	/**
	 * @OA\post(
	 * path="/applyCouponCode",
	 * description="applyCouponCode",
	 * operationId="applyCouponCode",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Cart"},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="applyCouponCode",
	 *    @OA\JsonContent(
	 *       required={"cart_id","code"},
	 *       @OA\Property(property="cart_id", type="string", format="text"),
	 *       @OA\Property(property="code", type="string", format="text", example="Ab#123"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="code", type="string")
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function applyCouponCode(Request $request) {
		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array("cart_id", "code"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			$cart = Cart::find($request->cart_id);
			if (isset($cart) && $cart->user_id == $user->id) {

				$code = CouponCode::with(['coupon_range' => function ($q) {
					$q->where('status', 'AC');
				}])->where('code', $request->code)->first();

				if ($code) {
					// check if coupon code expired
					if (date('Y-m-d', strtotime($code->start_date)) <= date('Y-m-d') && date('Y-m-d', strtotime($code->end_date)) >= date('Y-m-d')) {

						// check usage of coupon code
						$query = Order::whereHas('order_items', function ($q) use ($code) {
							$q->whereHas('coupon_range', function ($qu) use ($code) {
								$qu->where('coupon_code_id', $code->id);
							});
						});

						$total_count = $query->count();
						$user_count = $query->where('user_id', $user->id)->count();

						if ($total_count < $code->max_use_total && $user_count < $code->max_use) {
							$categories = $this->getCouponCatSubcat($code);
							$cart_items = CartItem::where('cart_id', $cart->id)->whereNULL('offer_id')->where('status', 'AC')->whereHas('product_items', function ($q) use ($categories) {
								$q->where('status', 'AC');
								$q->whereIN('parent_id', $categories);
								$q->where(function ($que) {

									$que->whereHas('category', function ($qu) {
										$qu->where('status', 'AC');
									})->orWhereHas('subcategory', function ($qu) {
										$qu->where('status', 'AC');
									});
								});
							})->get();

							$count = 0;

							if (count($cart_items) > 0) {
								foreach ($cart_items as $key => $value) {
									$range = 0;
									$discount = 0;
									// check if product special price lies within the coupon code range
									foreach ($code->coupon_range as $k => $v) {
										if (($value->special_price * $value->qty) >= $v->min_price && ($value->special_price * $value->qty) <= $v->max_price) {
											$count++;
											$range = $v->id;
											$discount = $v->value;
											break;
										}
									}
									if ($range != 0) {
										$item = CartItem::find($value->id);
										$item->coupon_code_id = $range;
										$item->coupon_discount = $discount;
										$item->save();
										$code->discount = CouponRange::find($range)->value;
									}
								}
							}

							if ($count == 0) {
								$this->response = array(
									"status" => 400,
									"message" => ResponseMessages::getStatusCodeMessages(230),
								);
							} else {
								unset($code->coupon_range);
								$this->response = array(
									"status" => 200,
									"message" => ResponseMessages::getStatusCodeMessages(233),
									'code' => $code,
								);
							}
						} else {
							$this->response = array(
								"status" => 400,
								"message" => ResponseMessages::getStatusCodeMessages(49),
							);
						}

					} else {
						$this->response = array(
							"status" => 400,
							"message" => ResponseMessages::getStatusCodeMessages(232),
						);
					}
				} else {
					$this->response = array(
						"status" => 400,
						"message" => ResponseMessages::getStatusCodeMessages(228),
					);
				}

			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(46),
				);
			}

		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function called to delete coupon code from cart
	/**
	 * @OA\Delete(
	 * path="/deleteCouponCode",
	 * description="deleteCouponCode",
	 * operationId="deleteCouponCode",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Cart"},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="deleteCouponCode",
	 *    @OA\JsonContent(
	 *       required={"cart_id","code_id"},
	 *       @OA\Property(property="cart_id", type="string", format="text"),
	 *       @OA\Property(property="code_id", type="string", format="text", example="3"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string")
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function deleteCouponCode(Request $request) {
		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array("cart_id", "code_id"));

		if (gettype($res) != 'boolean') {
			return $res;
		}

		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		try {
			$cart = Cart::find($request->cart_id);

			if (isset($cart) && $cart->user_id == $user->id) {

				$code = CouponCode::find($request->code_id);

				if ($code) {
					if (CartItem::where('cart_id', $cart->id)->whereHas('coupon_code', function ($q) use ($request) {
						$q->where('coupon_code_id', $request->code_id);
					})->count() > 0) {

						if (CartItem::where('cart_id', $cart->id)->whereHas('coupon_code', function ($q) use ($request) {
							$q->where('coupon_code_id', $request->code_id);
						})->update(['coupon_code_id' => null, 'coupon_discount' => null])) {
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(234),
							);
						} else {
							$this->response = array(
								"status" => 400,
								"message" => ResponseMessages::getStatusCodeMessages(236),
							);
						}
					} else {
						$this->response = array(
							"status" => 400,
							"message" => ResponseMessages::getStatusCodeMessages(237),
						);
					}
				} else {
					$this->response = array(
						"status" => 400,
						"message" => ResponseMessages::getStatusCodeMessages(228),
					);
				}
			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(46),
				);
			}
		} catch (\Exception $ex) {

			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		return $this->shut_down();
	}

	// function to list user addresses
	/**
	 * @OA\get(
	 * path="/getUserAddress",
	 * description="getUserAddress",
	 * operationId="getUserAddress",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Account"},
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function getUserAddress(Request $request) {

		$user = Auth::user();

		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		try
		{
			$address = UserAddress::with(['acountry', 'astate', 'acity'])->where(['user_id' => $user->id, 'status' => 'AC'])->get();

			if (isset($request->cart_id)) {
				$cart = Cart::where('id', $request->cart_id)->where('user_id', $user->id)->first();

			}

			if (count($address) > 0) {
				foreach ($address as $key => &$value) {
					$value->city = $value->acity->name ?? '';
					$value->state = $value->astate->name ?? '';
					$value->country = $value->acountry->name ?? '';
					$value->default = (int) $value->default;

					// if cart exists, and address is selected, then show cart address selected otherwise show default address selected
					if (isset($cart) && isset($cart->id)) {
						if ($cart->address_id == $value->id) {
							$value->is_selected = 1;
						} elseif ($cart->address_id == null) {
							if ($value->default == 1) {
								$value->is_selected = 1;
							}

						} else {
							$value->is_selected = 0;
						}
					} else {
						if ($value->default == 1) {
							$value->is_selected = 1;
						} else {
							$value->is_selected = 0;
						}

					}

					unset($value->acity);
					unset($value->astate);
					unset($value->acountry);
				}
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(144),
					"data" => $address,
				);
			} else {
				$this->response = array(
					"status" => 404,
					"message" => ResponseMessages::getStatusCodeMessages(31),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to add or update user address
	/**
	 * @OA\post(
	 * path="/addUserAddress",
	 * description="Add address",
	 * operationId="addUserAddress",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Account"},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="Add address",
	 *    @OA\JsonContent(
	 *       required={"first_name","last_name","mobile","apartment","state","country","city","zipcode","address","address_type"},
	 *       @OA\Property(property="first_name", type="string", format="text" , example="Raj"),
	 *       @OA\Property(property="last_name", type="string", format="text", example="Khundra"),
	 *       @OA\Property(property="mobile", type="string", format="text", example="1234567898"),
	 *       @OA\Property(property="apartment", type="string", format="text", example="apartment"),
	 *       @OA\Property(property="state", type="number", example="33"),
	 *       @OA\Property(property="city", type="number", example="3378"),
	 *       @OA\Property(property="country", type="number", example="101"),
	 *       @OA\Property(property="zipcode", type="string", format="text", example="202020"),
	 *       @OA\Property(property="address", type="string", format="text", example="64/63 partap nagar"),
	 *       @OA\Property(property="address_type", type="string", format="text", example="home"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="address_id", type="number",example="1")
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function addUserAddress(Request $request) {

		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array("first_name", "last_name", "mobile", "apartment", "state", "country", "city", "zipcode", "address"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		try
		{
			$validate = Validator($request->all(), [
				'first_name' => 'required',
				'last_name' => 'required',
				'mobile' => 'required|digits_between:8,15',
				'apartment' => 'required',
				'address' => 'required',
				'zipcode' => 'required',
				'city' => 'required',
				'state' => 'required',
				'country' => 'required',
				'address_type' => 'required',
			]);

			$attr = [
				'first_name' => 'First Name',
				'last_name' => 'Last Name',
				'mobile' => 'Mobile Number',
				'apartment' => 'Apartment',
				'address' => 'Address',
				'zipcode' => 'Zipcode',
				'city' => 'City',
				'state' => 'State',
				'country' => 'Country',
			];

			$validate->setAttributeNames($attr);

			if ($validate->fails()) {
				$errors = $validate->errors();
				$this->response = array(
					"status" => 300,
					"message" => $errors->first(),
					"errors" => $errors,
				);
			} else {
				$address = '';
				$exists = 1;
				if (isset($request->address_id)) {
					$address = UserAddress::find($request->address_id);
				}

				if (!isset($address->id)) {
					$exists = 0;
					$address = new UserAddress();
					$address->address_code = Helper::generateNumber('user_address', 'address_code');
					if (UserAddress::where('user_id', $user->id)->where('status', 'AC')->count() <= 0) {
						$address->default = "1";
					}
				}
				$address->user_id = $user->id;
				$address->first_name = $request->first_name;
				$address->last_name = $request->last_name;
				$address->mobile = $request->mobile;
				$address->apartment = $request->apartment;
				$address->address = $request->address;
				$address->city_id = $request->city;
				$address->state_id = $request->state;
				$address->country_id = $request->country;
				$address->zipcode = $request->zipcode;
				$address->lat = $request->lat;
				$address->lng = $request->lng;
				$address->address_type = $request->address_type;

				if ($address->save()) {
					if ($exists == 1) {
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(16),
							"address_id" => $address->id,
						);
					} else {
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(15),
							"address_id" => $address->id,
						);
					}
				} else {
					$this->response = array(
						"status" => 501,
						"message" => ResponseMessages::getStatusCodeMessages(221),
					);
				}
			}
		} catch (\Exception $ex) {
			//dd($ex);
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to add or update user address
	/**
	 * @OA\put(
	 * path="/updateUserAddress",
	 * description="Update address",
	 * operationId="updateUserAddress",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Account"},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="Update address",
	 *    @OA\JsonContent(
	 *      @OA\Property(property="address_id", type="string", format="text", example="1"),
	 *       required={"first_name","last_name","mobile","apartment","state","country","city","zipcode","address","address_type"},
	 *       @OA\Property(property="first_name", type="string", format="text" , example="Raj"),
	 *       @OA\Property(property="last_name", type="string", format="text", example="Khundra"),
	 *       @OA\Property(property="mobile", type="string", format="text", example="1234567898"),
	 *       @OA\Property(property="apartment", type="string", format="text", example="apartment"),
	 *       @OA\Property(property="state", type="number", example="33"),
	 *       @OA\Property(property="city", type="number", example="3378"),
	 *       @OA\Property(property="country", type="number", example="101"),
	 *       @OA\Property(property="zipcode", type="string", format="text", example="202020"),
	 *       @OA\Property(property="address", type="string", format="text", example="64/63 partap nagar"),
	 *       @OA\Property(property="address_type", type="string", format="text", example="home"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string")
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */
	public function updateUserAddress(Request $request) {

		$user = Auth::user();
		// $res = $this->checkKeys(array_keys($request->all()), array("first_name", "last_name", "mobile", "apartment", "state", "country", "city", "zipcode", "address"));

		// if (gettype($res) != 'boolean') {
		//     return $res;
		// }
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		try
		{
			$validate = Validator($request->all(), [
				'first_name' => 'required',
				'last_name' => 'required',
				'mobile' => 'required|digits_between:8,15',
				'apartment' => 'required',
				'address' => 'required',
				'zipcode' => 'required',
				'city' => 'required',
				'state' => 'required',
				'country' => 'required',
				'address_type' => 'required',
			]);

			$attr = [
				'first_name' => 'First Name',
				'last_name' => 'Last Name',
				'mobile' => 'Mobile Number',
				'apartment' => 'Apartment',
				'address' => 'Address',
				'zipcode' => 'Zipcode',
				'city' => 'City',
				'state' => 'State',
				'country' => 'Country',
			];

			$validate->setAttributeNames($attr);

			if ($validate->fails()) {
				$errors = $validate->errors();
				$this->response = array(
					"status" => 300,
					"message" => $errors->first(),
					"errors" => $errors,
				);
			} else {
				$address = '';
				$exists = 1;
				if (isset($request->address_id)) {
					$address = UserAddress::find($request->address_id);
				}

				if (!isset($address->id)) {
					$exists = 0;
					$address = new UserAddress();
					$address->address_code = Helper::generateNumber('user_address', 'address_code');
					if (UserAddress::where('user_id', $user->id)->where('status', 'AC')->count() <= 0) {
						$address->default = "1";
					}
				}
				$address->user_id = $user->id;
				$address->first_name = $request->first_name;
				$address->last_name = $request->last_name;
				$address->mobile = $request->mobile;
				$address->apartment = $request->apartment;
				$address->address = $request->address;
				$address->city_id = $request->city;
				$address->state_id = $request->state;
				$address->country_id = $request->country;
				$address->zipcode = $request->zipcode;
				$address->lat = $request->lat;
				$address->lng = $request->lng;
				$address->address_type = $request->address_type;

				if ($address->save()) {
					Session::put('address_update', 1);
					if ($exists == 1) {
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(16),
						);
					} else {
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(15),
						);
					}
				} else {
					$this->response = array(
						"status" => 501,
						"message" => ResponseMessages::getStatusCodeMessages(221),
					);
				}
			}
		} catch (\Exception $ex) {
			dd($ex);
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to add or update user address
	/**
	 * @OA\post(
	 * path="/setAddress",
	 * description="setAddress",
	 * operationId="setAddress",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Cart"},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="setAddress",
	 *    @OA\JsonContent(
	 *       required={"address_id"},
	 *       @OA\Property(property="address_id", type="string", format="text" , example="1"),
	 *       @OA\Property(property="cart_id", type="string", format="text" , example="1"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string")
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function setAddress(Request $request) {
		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array("address_id"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try
		{
			$address = UserAddress::where(['id' => $request->address_id, 'status' => 'AC'])->first();
			if (isset($address->id)) {
				if ($address->user_id == $user->id) {

					if (isset($request->cart_id)) {
						$cart = Cart::where('id', $request->cart_id)->where('user_id', $user->id)->first();
						if ($cart) {
							$cart->address_id = $address->id;

							if ($cart->save()) {

								$this->response = array(
									"status" => 200,
									"message" => ResponseMessages::getStatusCodeMessages(13),

								);
							} else {
								$this->response = array(
									"status" => 400,
									"message" => ResponseMessages::getStatusCodeMessages(36),
								);
							}
						}
					} else {
						UserAddress::where('user_id', $user->id)->update(['default' => '0']);
						$address->default = '1';

						if ($address->save()) {

							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(16),

							);
						} else {
							$this->response = array(
								"status" => 501,
								"message" => ResponseMessages::getStatusCodeMessages(221),
							);
						}
					}

				} else {

					$this->response = array(
						"status" => 404,
						"message" => ResponseMessages::getStatusCodeMessages(223),
					);
				}
			} else {
				$this->response = array(
					"status" => 404,
					"message" => ResponseMessages::getStatusCodeMessages(222),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to delete user address
	/**
	 * @OA\Delete(
	 * path="/deleteAddress",
	 * description="deleteAddress",
	 * operationId="deleteAddress",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Account"},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="deleteAddress",
	 *    @OA\JsonContent(
	 *       required={"address_id"},
	 *       @OA\Property(property="address_id", type="string", format="text" , example="1"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string")
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function deleteAddress(Request $request) {
		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array("address_id"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try
		{
			$address = UserAddress::where(['id' => $request->address_id, 'status' => 'AC'])->first();
			if (isset($address->id)) {
				if ($address->user_id == $user->id) {
					$address->status = 'DL';

					//if cart have this address then update to null
					Cart::where('address_id', $address->id)->update(['address_id' => null]);

					if ($address->save()) {
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(17),

						);
					} else {
						$this->response = array(
							"status" => 501,
							"message" => ResponseMessages::getStatusCodeMessages(221),
						);
					}
				} else {

					$this->response = array(
						"status" => 404,
						"message" => ResponseMessages::getStatusCodeMessages(223),
					);
				}
			} else {
				$this->response = array(
					"status" => 404,
					"message" => ResponseMessages::getStatusCodeMessages(222),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		return $this->shut_down();
	}

	// function to set user's credit to cart
	public function setCartWallet(Request $request) {
		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array("credit"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try
		{
			$cart = Cart::where('user_id', $user->id)->first();
			if (isset($cart) && isset($cart->id)) {
				if ($request->credit == 1) {
					$cart->wallet = "1";

				} else {
					$cart->wallet = "0";
					$cart->payment_method = 'cod';
				}
				if ($cart->save()) {
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(13),
					);
				} else {
					$this->response = array(
						"status" => 400,
						"message" => ResponseMessages::getStatusCodeMessages(36),
					);
				}
			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(47),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to set payment method for cart
	public function setCartPayment(Request $request) {

		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array("payment_method", "cart_id"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try
		{
			$cart = Cart::where('id', $request->cart_id)->where('user_id', $user->id)->first();

			if ($cart) {
				if (array_key_exists($request->payment_method, Config::get('constants.PAYMENT_METHOD'))) {
					$cart->payment_method = $request->payment_method;

					if ($cart->save()) {
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(13),

						);
					} else {
						$this->response = array(
							"status" => 400,
							"message" => ResponseMessages::getStatusCodeMessages(36),
						);
					}
				} else {
					$this->response = array(
						"status" => 400,
						"message" => ResponseMessages::getStatusCodeMessages(48),
					);
				}
			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(47),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		return $this->shut_down();
	}

	// function to create order in case of cod or wallet payment method
	/**
	 * @OA\post(
	 * path="/createOrder",
	 * description="createOrder",
	 * operationId="createOrder",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Order"},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="createOrder",
	 *    @OA\JsonContent(
	 *       required={"payment_method"},
	 *       @OA\Property(property="payment_method", type="string", format="text" , example="cod/debit_card/credit_card"),
	 *       @OA\Property(property="card_no", type="string", format="text" , example="1111222233334444"),
	 *       @OA\Property(property="card_type", type="string", format="text" , example="amex/visa/master/discover"),
	 *       @OA\Property(property="year", type="string", format="text" , example="2023"),
	 *       @OA\Property(property="month", type="string", format="text" , example="03"),
	 *       @OA\Property(property="cvv", type="string", format="text" , example="123"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string")
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="currency", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="order_id", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function createOrder(Request $request) {

		$user = Auth::user();
		//$res = $this->checkKeys(array_keys($request->all()));
		// try
		// {
		$cart = Cart::where('user_id', $user->id)->where('status', 'AC')->first();
		$user = User::find($user->id);

		if ($cart) {
			if (in_array($request->payment_method, ['cod', 'debit_card', 'credit_card'])) {

				if (isset($request->payment_method) && $request->payment_method == 'debit_card') {

					if (in_array($request->card_no, Config::get("constants.debit_card." . $request->card_type))) {

					} else {
						return response()->json([
							"status" => 504,
							"message" => 'Invalid card number',
						]);
						if (stripos($request->url(), '/api') !== false) {
							return $this->shut_down();
						} else {
							return $this->response;
						}
					}
				} elseif (isset($request->payment_method) && $request->payment_method == 'credit_card') {
					if (in_array($request->card_no, Config::get("constants.credit_card." . $request->card_type))) {

					} else {

						return response()->json([
							"status" => 504,
							"message" => 'Invalid card number',
						]);

						if (stripos($request->url(), '/api') !== false) {
							return $this->shut_down();
						} else {
							return $this->response;
						}
					}
				}

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

					$cart->payment_method = $request->payment_method;
					$cart->save();

					$cart->card_type = $request->card_type;
					$cart->year = $request->year;
					$cart->cvv = $request->cvv;
					$cart->month = $request->month;
					$cart->card_no = $request->card_no;
					$this->createFinalOrder($cart, $usercartsitem, $user->id);
				} else {
					$this->response = array(
						"status" => 400,
						"message" => ResponseMessages::getStatusCodeMessages(54),
					);
				}
			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(52),
				);
			}
		} else {
			$this->response = array(
				"status" => 400,
				"message" => ResponseMessages::getStatusCodeMessages(47),
			);
		}
		// } catch (\Exception $ex) {
		// 	$this->response = array(
		// 		"status" => 501,
		// 		"message" => ResponseMessages::getStatusCodeMessages(501),
		// 	);
		// }
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// test function to send notification

	public function sendTestNotification() {
		Notify::sendTestNotification();
	}

	// function to get all user's orders
	/**
	 * @OA\get(
	 * path="/getAllOrders",
	 * description="getAllOrders",
	 * operationId="getAllOrders",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Order"},
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *      @OA\Property(property="currency", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function getAllOrders(Request $request) {
		$user = Auth::user();
		//$res = $this->checkKeys(array_keys($request->all()));
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			$orders = Order::with('order_items')->whereHas('order_items')->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
			if (count($orders) > 0) {
				$result = [];
				foreach ($orders as $value) {
					$order = [];
					$order['id'] = $value->id;
					$order['order_no'] = $value->order_no;
					$order['amount'] = sprintf('%.2f', $value->total_amount + $value->shipping_fee - $value->coupon_discount);
					$order['wallet_show'] = ($value->wallet) ? true : false;
					$order['wallet_amount'] = sprintf('%.2f', $value->credits_used);
					$order['remaining_amount'] = sprintf('%.2f', $order['amount'] - $order['wallet_amount']);

					// get date duration from order item with maximum duration
					// $max_days = 0;
					$start_date = $end_date = '';
					foreach ($value->order_items as $val) {
						/*$days = (date_diff(date_create($val->start_date), date_create($val->end_date))->format('%a') + 1);
							                        if ($days > $max_days) {
							                        $max_days = $days;
							                        $start_date = $val->start_date;
							                        $end_date = $val->end_date;
						*/
						if ($start_date == '') {
							$start_date = $val->start_date;
						} else {
							if (date('Y-m-d', strtotime($val->start_date)) < date('Y-m-d', strtotime($start_date))) {
								$start_date = $val->start_date;
							}

						}

						if ($end_date == '') {
							$end_date = $val->end_date;
						} else {
							if (date('Y-m-d', strtotime($val->end_date)) > date('Y-m-d', strtotime($end_date))) {
								$end_date = $val->end_date;
							}

						}

						$schedules = OrderDelivery::where('order_id', $val->order_id)->where('order_item_id', $val->id)->get();
						foreach ($schedules as $sch => $sval) {
							if ($sval->reschedule_date != null) {
								if (date('Y-m-d', strtotime($sval->reschedule_date)) < date('Y-m-d', strtotime($start_date))) {
									$start_date = $sval->reschedule_date;
								}
								if (date('Y-m-d', strtotime($sval->reschedule_date)) > date('Y-m-d', strtotime($end_date))) {
									$end_date = $sval->reschedule_date;
								}
							}
						}

					}

					if ($start_date == $end_date) {
						$order['duration'] = 'For ' . date('M d,Y', strtotime($start_date));
					} else {
						$order['duration'] = 'From ' . date('M d,Y', strtotime($start_date)) . ' to ' . date('M d,Y', strtotime($end_date));
					}

					$result[] = $order;
				}

				$this->response = array(
					'status' => 200,
					'message' => ResponseMessages::getStatusCodeMessages(144),
					'currency' => $this->currency,
					'data' => $result,
				);

			} else {
				$this->response = array(
					'status' => 400,
					'message' => ResponseMessages::getStatusCodeMessages(22),
				);
			}
		} catch (Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return json_encode($this->response);
		}
	}

	// function to get order details
	/**
	 * @OA\Get(
	 * path="/getOrderDetails?order_id={order_id}",
	 * description="Get Order Details",
	 * operationId="orderDetails",
	 * tags={"Order"},
	 * security={{"bearerAuth":{}}},
	 * @OA\Parameter(
	 *    description="Order ID",
	 *    in="query",
	 *    name="order_id",
	 *    required=true,
	 *    example="1",
	 *    @OA\Schema(
	 *       type="integer",
	 *       format="int64"
	 *    )
	 * ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function getOrderDetails(Request $request) {
		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array('order_id'));
		if (gettype($res) != 'boolean') {
			return $res;
		}

		$res = $this->checkUserActive($user->id);

		if (gettype($res) != 'boolean') {
			return $res;
		}

		try {
			$result = Order::with('order_items')->where('user_id', $user->id)->where('id', $request->order_id)->first();
			$order = [];
			if (isset($result->id)) {

				// $max_days = 0;
				$start_date = $end_date = '';
				foreach ($result->order_items as $val) {

					if ($start_date == '') {
						$start_date = $val->start_date;
					} else {
						if (date('Y-m-d', strtotime($val->start_date)) < date('Y-m-d', strtotime($start_date))) {
							$start_date = $val->start_date;
						}

					}

					if ($end_date == '') {
						$end_date = $val->end_date;
					} else {
						if (date('Y-m-d', strtotime($val->end_date)) > date('Y-m-d', strtotime($end_date))) {
							$end_date = $val->end_date;
						}

					}

					$schedules = OrderDelivery::where('order_id', $val->order_id)->where('order_item_id', $val->id)->get();
					foreach ($schedules as $sch => $sval) {
						if ($sval->reschedule_date != null) {
							if (date('Y-m-d', strtotime($sval->reschedule_date)) < date('Y-m-d', strtotime($start_date))) {
								$start_date = $sval->reschedule_date;
							}
							if (date('Y-m-d', strtotime($sval->reschedule_date)) > date('Y-m-d', strtotime($end_date))) {
								$end_date = $sval->reschedule_date;
							}
						}
					}
				}
				$order['id'] = $result->id;
				$order['order_no'] = $result->order_no;
				if ($start_date == $end_date) {
					$order['duration'] = 'For ' . date('M d,Y', strtotime($start_date));
				} else {
					$order['duration'] = 'From ' . date('M d,Y', strtotime($start_date)) . ' to ' . date('M d,Y', strtotime($end_date));
				}

				// get current date to show default month in calendar
				if (date('Y-m-d') >= $start_date && date('Y-m-d') <= $end_date) {
					$curr_date = date('Y-m-d');
				} else {
					$curr_date = $start_date;
				}

				// fetch products ordered on this current date
				$products = $this->fetchDateProducts($curr_date, $result->id);

				$order_schedules = OrderDelivery::where('order_id', $request->order_id)->orderBy('order_date')->get();
				$dates = [];
				foreach ($order_schedules as $key => $value) {
					$temp = [];
					// check for unique dates in array
					if (!in_array($value->order_date, array_column($dates, 'date'))) {
						$temp['id'] = $value->id;
						$temp['date'] = $value->order_date;
						$temp['status'] = $this->getItemStatus($value->status);

						// if date is rescheduled, add rescheduled date to array
						if ($value->reschedule_date != null) {
							$temp['status'] = 'cancelled';
							$dates[] = $temp;
							$t = [];
							$t['id'] = $value->id;
							$t['date'] = $value->reschedule_date;
							$t['status'] = ($this->getItemStatus($value->status) == 'pending') ? 'rescheduled' : $this->getItemStatus($value->status);
							$dates[] = $t;
						} else {

							$dates[] = $temp;
						}
					} else {
						$ar = 0;
						foreach ($dates as $k => $v) {
							if ($v['date'] == $value->order_date) {
								$ar = $k;
							}
						}
						if ($dates[$ar]['status'] != 'cancelled') {
							$dates[$ar]['status'] = $this->getItemStatus($value->status);
							if ($value->reschedule_date != null) {
								$dates[$ar]['status'] = 'cancelled';
								$t = [];
								$t['id'] = $value->id;
								$t['date'] = $value->reschedule_date;
								$t['status'] = ($this->getItemStatus($value->status) == 'pending') ? 'rescheduled' : $this->getItemStatus($value->status);
								$dates[] = $t;
							}
						}

					}

				}

				usort($dates, function ($a, $b) {
					return strtotime($a['date']) - strtotime($b['date']);
				});

				$this->response = array(
					'status' => 200,
					'message' => ResponseMessages::getStatusCodeMessages(144),
					'currency' => $this->currency,
					'data' => $order,
					'calendar' => $dates,
					'current_date' => $curr_date,
					'products' => $products,
				);

			} else {
				$this->response = array(
					'status' => 400,
					'message' => ResponseMessages::getStatusCodeMessages(56),
				);
			}
		} catch (Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to get order items on specific date
	/**
	 * @OA\post(
	 * path="/getDateProducts",
	 * description="getDateProducts",
	 * operationId="getDateProducts",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Order"},
	 *   @OA\RequestBody(
	 *    required=true,
	 *    description="getDateProducts",
	 *    @OA\JsonContent(
	 *       required={"order_id","date"},
	 *       @OA\Property(property="order_id", type="string", format="text" , example="1"),
	 *       @OA\Property(property="date", type="string", format="string" , example="12-12-2020"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *      @OA\Property(property="currency", type="string"),
	 *      @OA\Property(property="products", type="object"),
	 *       @OA\Property(property="date", type="date"),
	 *      @OA\Property(property="order_id", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function getDateProducts(Request $request) {
		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array('order_id', 'date'));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			$result = Order::with('order_items')->where('user_id', $user->id)->where('id', $request->order_id)->first();
			if (isset($result->id)) {

				if ($request->date != null) {
					// fetch products ordered on this current date
					$products = $this->fetchDateProducts($request->date, $result->id);

					if (count($products) > 0) {
						$this->response = array(
							'status' => 200,
							'message' => ResponseMessages::getStatusCodeMessages(144),
							'currency' => $this->currency,
							"date" => date('Y-m-d', strtotime($request->date)),
							'products' => $products,
							'order_id' => $result->id,
						);
					} else {
						$this->response = array(
							'status' => 400,
							'message' => ResponseMessages::getStatusCodeMessages(57),
						);
					}
				} else {
					$this->response = array(
						'status' => 400,
						'message' => ResponseMessages::getStatusCodeMessages(58),
					);
				}
			} else {
				$this->response = array(
					'status' => 400,
					'message' => ResponseMessages::getStatusCodeMessages(56),
				);
			}
		} catch (Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to get all user notifications
	/**
	 * @OA\get(
	 * path="/getNotifications",
	 * description="getNotifications",
	 * operationId="getNotifications",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Account"},
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *      @OA\Property(property="data", type="object"),

	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */
	public function getNotifications(Request $request) {

		$user = Auth::user();
		//$res = $this->checkKeys(array_keys($request->all()));
		try
		{
			$notifications = Notification::select('title', 'description', 'notification_type', 'image', 'created_at')->where(['receiver_id' => $user->id, 'status' => 'AC'])->orderBy('created_at', 'desc')->get();
			if ($notifications) {

				foreach ($notifications as &$value) {
					if ($value->image == null) {
						switch ($value->notification_type) {
						case 'custom':
							$value->image = URL::asset('uploads/notifications/noti_c.png');
							break;
						case 'order':
							$value->image = URL::asset('uploads/notifications/noti_o.png');
							break;
						case 'wallet':
							$value->image = URL::asset('uploads/notifications/noti_w.png');
							break;
						case 'referral':
							$value->image = URL::asset('uploads/notifications/noti_r.png');
							break;

						}
					} else {
						$value->image = URL::asset('uploads/notifications/' . $value->image);
					}

					$value->date = date('d M Y', strtotime($value->created_at));
					$value->time = date('h:i A', strtotime($value->created_at));

				}

				Notification::where('receiver_id', $user->id)->where('is_read', '0')->update(['is_read' => '1']);

				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					'data' => $notifications,

				);
			} else {
				$this->response = array(
					"status" => 404,
					"message" => ResponseMessages::getStatusCodeMessages(55),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to get all user notifications
	/**
	 * @OA\post(
	 * path="/submitRating",
	 * description="submitRating",
	 * operationId="submitRating",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Order"},
	 *  @OA\RequestBody(
	 *    required=true,
	 *    description="submitRating",
	 *    @OA\JsonContent(
	 *       required={"rate"},
	 *       @OA\Property(property="rate", type="string", format="text" , example="1"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *      @OA\Property(property="data", type="object"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function submitRating(Request $request) {
		$res = $this->checkKeys(array_keys($request->all()), array("rate"));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		try
		{
			$user = Auth::user();

			$rating = Rating::where('user_id', $user->id)->get();
			if ($rating->count() < 1) {
				$rating = new Rating;
				$rating->user_id = $user->id;
				$rating->rating = $request->rate;

				if ($rating->save()) {
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(129),
						'data' => $rating,

					);
				} else {
					$this->response = array(
						"status" => 400,
						"message" => ResponseMessages::getStatusCodeMessages(146),
					);
				}
			} else {
				$this->response = array(
					"status" => 400,
					"message" => ResponseMessages::getStatusCodeMessages(147),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		return $this->shut_down();
	}

	public function getUserDetail(Request $request) {
		$res = $this->checkLoginCookie($request->login_cookie);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		$user = Auth::user();
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		try {
			$user = User::find($user->id);
			$user->profile_picture = ($user->profile_picture != null) ? (stripos($user->profile_picture, "http") !== false) ? $user->profile_picture : URL::asset('/uploads/profiles/' . $user->profile_picture) : '';

			$this->response = array(
				"status" => 200,
				"message" => ResponseMessages::getStatusCodeMessages(200),
				"data" => $user,
			);

			return response()->json($this->response);
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
			return response()->json($this->response);
		}
	}

	// function called to get wallet history
	public function getUserWallet(Request $request) {
		$user = Auth::user();
		// check keys are exist
		// $res = $this->checkKeys(array_keys($request->all()), array("user_id"));
		// check user is active or not
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			// get wallet history
			$wallet = Wallet::with(['order', 'order_return', 'referrer'])->where("user_id", $user->id)->orderBy("created_at", "desc")->get();
			$user = User::find($user->id);

			if ($wallet->count() > 0) {
				$result = [];
				foreach ($wallet as &$value) {
					$key = new Wallet;
					switch ($value->reason) {
					case 'order':
						$key->code = $value->order->order_no;
						break;
					case 'order_return':
						// $key->code = $value->order_return->order->order_no;
						if (isset($value->order_return_id)) {
							$key->code = (isset($value->order_return->order) ? $value->order_return->order->order_no : Order::find($value->order_return_id)->order_no);
						} else {
							$key->code = '';
						}
						break;
					case 'refer':
						if (isset($value->referrer)) {
							$key->code = (isset($value->referrer->referral_code) ? $value->referrer->referral_code : '-');
						} else {
							$key->code = '';
						}
						break;

					}
					switch ($value->type) {
					case 'debit':
						$type = 'Used';
						break;
					case 'credit':
						$type = 'Added';
						break;
					}
					$key->text = $value->description;
					$key->type = $value->reason;
					$key->amount = $value->amount . ' ' . $type;
					$key->date = date('d M Y', strtotime($value->created_at));
					$key->time = date('h:i A', strtotime($value->created_at));

					$result[] = $key;
				}
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(144),
					"currency" => $this->currency,
					"data" => $result,
					"balance" => $user->wallet_amount,
				);
			} else {
				$this->response = array(
					"status" => 404,
					"message" => ResponseMessages::getStatusCodeMessages(235),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to get order details

	/**
	 * @OA\post(
	 * path="/getOrderStatus",
	 * description="getOrderStatus",
	 * operationId="getOrderStatus",
	 *      tags={"Order"},
	 *  security={{"bearerAuth":{}}},
	 *  @OA\RequestBody(
	 *    required=true,
	 *    description="getOrderStatus",
	 *    @OA\JsonContent(
	 *       required={"order_id"},
	 *       @OA\Property(property="order_id", type="string", format="text" , example="1"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *    @OA\Property(property="currency", type="string"),
	 *       @OA\Property(property="message", type="string"),
	 *      @OA\Property(property="order", type="object"),
	 *      @OA\Property(property="data", type="object"),
	 *      @OA\Property(property="products", type="object"),
	 *       @OA\Property(property="balance", type="string"),
	 *       @OA\Property(property="cancel_allowed", type="boolean"),
	 *       @OA\Property(property="return_allowed", type="boolean"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function getOrderStatus(Request $request) {
		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array('order_id'));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			$result = Order::with('order_items')->where('user_id', $user->id)->where('id', $request->order_id)->first();
			$order = [];
			if (isset($result->id)) {

				$order['amount'] = sprintf('%.2f', $result->total_amount + $result->shipping_fee - $result->coupon_discount);
				$order['wallet_show'] = ($result->wallet) ? true : false;
				$order['wallet_amount'] = sprintf('%.2f', $result->credits_used);
				$order['remaining_amount'] = sprintf('%.2f', $order['amount'] - $order['wallet_amount']);
				$order['order_no'] = $result->order_no;
				$order['id'] = $result->id;

				$query = OrderItem::where('order_id', $result->id)->with(['product_items', 'product_variation'])->where('status', '!=', 'DL')->get();

				$products = [];
				$scheduled = 0;
				$no_schedules = 0;

				foreach ($query as $key => $value) {
					$item = new OrderItem;
					$item->id = $value->id;
					$item->image = URL::asset('uploads/products/' . $value->product_items->image);
					$item->medium_image = URL::asset('uploads/products/medium/medium-' . $value->product_items->image);
					$item->name = $value->product_items->name;
					//$item->weight = $value->product_variation->weight . $value->product_variation->product_units->unit;
					$item->price = $value->price;
					$item->special_price = $value->special_price;
					$products[] = $item;

					if ($value->scheduled == '1') {
						$scheduled++;
					} else {
						$no_schedules = OrderDelivery::where('order_id', $value->order_id)->where('order_item_id', $value->id)->where('status', 'PN')->count();
						if ($no_schedules > 1) {
							$scheduled++;
						}

					}

				}

				$return_allowed = ($scheduled == 0 && $result->status == 'CM') ? true : false;

				// if order is one time delivery order, then complete order can be cancelled
				$cancel_allowed = ($no_schedules == 1) ? true : false;

				$this->response = array(
					'status' => 200,
					'message' => ResponseMessages::getStatusCodeMessages(144),
					'currency' => $this->currency,
					'order' => $order,
					'products' => $products,
					'cancel_allowed' => $cancel_allowed,
					'return_allowed' => $return_allowed,
				);

			} else {
				$this->response = array(
					'status' => 400,
					'message' => ResponseMessages::getStatusCodeMessages(56),
				);
			}
		} catch (Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to cancel complete order in case of one time delivery
	/**
	 * @OA\post(
	 * path="/cancelOrder",
	 * description="cancelOrder",
	 * operationId="cancelOrder",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Order"},
	 *  @OA\RequestBody(
	 *    required=true,
	 *    description="cancelOrder",
	 *    @OA\JsonContent(
	 *       required={"order_id"},
	 *       @OA\Property(property="order_id", type="string", format="text" , example="1"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */
	public function cancelOrder(Request $request) {
		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array('order_id'));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		try {
			$result = Order::with('order_items')->where('user_id', $user->id)->where('id', $request->order_id)->first();
			$order = [];
			if (isset($result->id)) {

				if (isset($request->item_id)) {

					$res = $this->checkKeys(array_keys($request->all()), array('date'));

					if (gettype($res) != 'boolean') {
						return $res;
					}
					$item = OrderItem::where('order_id', $result->id)->where('id', $request->item_id)->where('status', '!=', 'DL')->first();

					if (isset($item->id)) {
						// if order cancel request is placed, then update status of order schedule and create entry in order_return table
						$schedule = OrderDelivery::where('order_id', $result->id)->where('order_item_id', $item->id)->where(function ($q) use ($request) {
							$q->where('order_date', date('Y-m-d', strtotime($request->date)));
							$q->orWhere('reschedule_date', date('Y-m-d', strtotime($request->date)));
						})->first();

						if (isset($schedule->id)) {
							if ($schedule->order_date == date('Y-m-d', strtotime($request->date)) && $schedule->reschedule_date != null) {
								$this->response = array(
									'status' => 400,
									'message' => ResponseMessages::getStatusCodeMessages(63),
								);
							} else {
								$schedule->status = 'RFIN';
								if ($schedule->save()) {
									// if this order item is single day delivery item, then change the status of order item to RFIN
									$count = OrderDelivery::where('order_id', $result->id)->where('order_item_id', $item->id)->count();
									if ($count == 1) {
										$item->status = 'RFIN';
										$item->save();
									}

									// $count = OrderDelivery::where('order_id', $result->id)->where('order_item_id', $item->id)->count();
									// if ($count == 1) {
									//     $result->status = 'RFCL';
									//     $result->save();
									// }

									if (OrderReturn::where('order_id', $schedule->order_id)->where('order_item_id', $schedule->order_item_id)->where('order_schedule_id', $schedule->id)->count() <= 0) {
										$cancel = new OrderReturn;
										$cancel->order_id = $schedule->order_id;
										$cancel->order_item_id = $schedule->order_item_id;
										$cancel->order_schedule_id = $schedule->id;
										$cancel->type = 'product';
										$cancel->amount = $this->getItemPrice($schedule->order_item);
										$cancel->save();
									} else {
										OrderReturn::where('order_id', $schedule->order_id)->where('order_item_id', $schedule->order_item_id)->where('order_schedule_id', $schedule->id)->update(['status' => 'PN']);
									}

									$this->response = array(
										'status' => 200,
										'message' => ResponseMessages::getStatusCodeMessages(66),
									);
								}
							}

						} else {
							$this->response = array(
								'status' => 501,
								'message' => ResponseMessages::getStatusCodeMessages(221),
							);
						}
					} else {
						$this->response = array(
							'status' => 400,
							'message' => ResponseMessages::getStatusCodeMessages(59),
						);
					}

				} else {
					$query = OrderItem::where('order_id', $result->id)->where('status', '!=', 'DL')->get();

					$scheduled = 0;

					foreach ($query as $key => $value) {
						if ($value->scheduled == '1') {
							$scheduled++;
						}
					}

					// if order is one time delivery order, then cancel complete order
					if ($scheduled == 0) {

						$result->status = 'RFIN';
						if ($result->save()) {
							// if order cancel request is placed, then update status of order item and order schedule and create entry in order_return table
							if (OrderItem::where('order_id', $result->id)->update(['status' => 'RFIN'])) {
								if (OrderDelivery::where('order_id', $result->id)->update(['status' => 'RFIN'])) {

									$schedules = OrderDelivery::where('order_id', $result->id)->get();

									foreach ($schedules as $value) {
										if (OrderReturn::where('order_id', $value->order_id)->where('order_item_id', $value->order_item_id)->where('order_schedule_id', $value->id)->count() <= 0) {
											$cancel = new OrderReturn;
											$cancel->order_id = $value->order_id;
											$cancel->order_item_id = $value->order_item_id;
											$cancel->order_schedule_id = $value->id;
											$cancel->type = 'order';
											$cancel->amount = $this->getItemPrice($value->order_item);
											$cancel->save();
										} else {
											OrderReturn::where('order_id', $value->order_id)->where('order_item_id', $value->order_item_id)->where('order_schedule_id', $value->id)->update(['status' => 'PN']);
										}
									}
									$this->response = array(
										'status' => 200,
										'message' => ResponseMessages::getStatusCodeMessages(61),
									);
								} else {
									$this->response = array(
										'status' => 501,
										'message' => ResponseMessages::getStatusCodeMessages(221),
									);
								}
							} else {
								$this->response = array(
									'status' => 501,
									'message' => ResponseMessages::getStatusCodeMessages(221),
								);
							}
						} else {
							$this->response = array(
								'status' => 501,
								'message' => ResponseMessages::getStatusCodeMessages(221),
							);
						}

					} else {
						$this->response = array(
							'status' => 301,
							'message' => ResponseMessages::getStatusCodeMessages(60),
						);
					}
				}

			} else {
				$this->response = array(
					'status' => 400,
					'message' => ResponseMessages::getStatusCodeMessages(400),
				);
			}
		} catch (Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		return $this->shut_down();
	}

	// function to get order item details
	/**
	 * @OA\post(
	 * path="/getOrderItemStatus",
	 * description="getOrderItemStatus",
	 * operationId="getOrderItemStatus",
	 *      tags={"Order"},
	 *  security={{"bearerAuth":{}}},
	 *  @OA\RequestBody(
	 *    required=true,
	 *    description="getOrderItemStatus",
	 *    @OA\JsonContent(
	 *       required={"order_id","item_id"},
	 *       @OA\Property(property="order_id", type="string", format="text" , example="1"),
	 *       @OA\Property(property="item_id", type="string", format="text" , example="1"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *    @OA\Property(property="currency", type="string"),
	 *       @OA\Property(property="message", type="string"),
	 *      @OA\Property(property="order", type="object"),
	 *      @OA\Property(property="schedules", type="object"),
	 *      @OA\Property(property="products", type="object"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function getOrderItemStatus(Request $request) {
		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array('order_id', 'item_id'));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			$result = Order::with('order_items')->where('user_id', $user->id)->where('id', $request->order_id)->first();
			$order = [];
			if (isset($result->id)) {

				$order['order_no'] = $result->order_no;
				$item = OrderItem::where('order_id', $result->id)->where('id', $request->item_id)->where('status', '!=', 'DL')->first();

				if (isset($item->id)) {
					$start_date = $item->start_date;
					$end_date = $item->end_date;

					$schedules = OrderDelivery::where('order_id', $item->order_id)->where('order_item_id', $item->id)->get();
					foreach ($schedules as $sch => $sval) {
						if ($sval->reschedule_date != null) {
							if (date('Y-m-d', strtotime($sval->reschedule_date)) < date('Y-m-d', strtotime($start_date))) {
								$start_date = $sval->reschedule_date;
							}
							if (date('Y-m-d', strtotime($sval->reschedule_date)) > date('Y-m-d', strtotime($end_date))) {
								$end_date = $sval->reschedule_date;
							}
						}
					}

					if ($start_date == $end_date) {
						$order['duration'] = 'For ' . date('M d,Y', strtotime($start_date));
					} else {
						$order['duration'] = 'From ' . date('M d,Y', strtotime($start_date)) . ' to ' . date('M d,Y', strtotime($end_date));
					}
					$order['delivery_slot'] = date('h:i A', strtotime($item->delivery_slot->from_time)) . ' to ' . date('h:i A', strtotime($item->delivery_slot->to_time));

					$prod = new OrderItem;
					$prod->id = $item->id;
					$prod->image = URL::asset('uploads/products/' . $item->product_items->image);
					$prod->medium_image = URL::asset('uploads/products/medium/medium-' . $item->product_items->image);
					$prod->name = $item->product_items->name;
					// $prod->weight = $item->product_variation->weight . $item->product_variation->product_units->unit;
					$prod->price = $item->price;
					$prod->start_date = $item->start_date;
					$prod->end_date = $item->end_date;
					$prod->scheduled = (int) $item->scheduled;
					$prod->special_price = $item->special_price;

					// fetch order item delivery dates and their status
					$query = OrderDelivery::where('order_id', $result->id)->where('order_item_id', $item->id)->where('status', '!=', 'DL')->get();

					$schedules = [];
					foreach ($query as $key => $value) {
						$temp = new OrderDelivery;
						$temp['id'] = $value->id;
						$temp['date'] = date('M d, Y', strtotime($value->order_date));
						$temp['status'] = $this->getItemStatus($value->status);

						if ($value->reschedule_date != null) {
							$temp['status'] = 'cancelled';
							$t = new OrderDelivery;
							$t['id'] = $value->id;
							$t['date'] = date('M d, Y', strtotime($value->reschedule_date));
							$t['status'] = ($this->getItemStatus($value->status) == 'pending') ? 'rescheduled' : $this->getItemStatus($value->status);
							$schedules[] = $t;
						}
						$schedules[] = $temp;
					}
					usort($schedules, function ($a, $b) {
						return strtotime($a['date']) - strtotime($b['date']);
					});

					$this->response = array(
						'status' => 200,
						'message' => ResponseMessages::getStatusCodeMessages(144),
						'currency' => $this->currency,
						'products' => $prod,
						'schedules' => $schedules,
						'order' => $order,
					);
				} else {
					$this->response = array(
						'status' => 400,
						'message' => ResponseMessages::getStatusCodeMessages(59),
					);
				}

			} else {
				$this->response = array(
					'status' => 400,
					'message' => ResponseMessages::getStatusCodeMessages(56),
				);
			}
		} catch (Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to reschedule order item

	/**
	 * @OA\post(
	 * path="/rescheduleOrderItem",
	 * description="rescheduleOrderItem",
	 * operationId="rescheduleOrderItem",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Order"},
	 *  @OA\RequestBody(
	 *    required=true,
	 *    description="rescheduleOrderItem",
	 *    @OA\JsonContent(
	 *       required={"order_id","item_id","date","new_date"},
	 *       @OA\Property(property="order_id", type="string", format="text" , example="1"),
	 *       @OA\Property(property="item_id", type="string", format="text" , example="1"),
	 *       @OA\Property(property="date", type="string", format="text" , example="1"),
	 *       @OA\Property(property="new_date", type="string", format="text" , example="1"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function rescheduleOrderItem(Request $request) {
		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array("order_id", "item_id", 'date', 'new_date'));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try
		{
			$result = Order::with('order_items')->where('user_id', $user->id)->where('id', $request->order_id)->first();
			if (isset($result->id)) {

				$item = OrderItem::where('order_id', $result->id)->where('id', $request->item_id)->where('status', '!=', 'DL')->first();

				if (isset($item->id)) {

					// check if item is single day order of multiple days scheduling
					if ($item->scheduled == '1') {
						// check same item for this date is already rescheduled or not
						$schedule = OrderDelivery::where('order_id', $result->id)->where('order_item_id', $item->id)->where('order_date', date('Y-m-d', strtotime($request->date)))->first();

						if (isset($schedule->id)) {

							if ($schedule->reschedule_date == null) {
								// check same item from other date is rescheduled on same new date
								$check = OrderDelivery::where('order_id', $result->id)->where('order_item_id', $item->id)->where('reschedule_date', date('Y-m-d', strtotime($request->new_date)))->first();

								if (!isset($check->id)) {
									$schedule->reschedule_date = date('Y-m-d', strtotime($request->new_date));
									if ($schedule->save()) {
										$this->response = array(
											'status' => 200,
											'message' => ResponseMessages::getStatusCodeMessages(69),
										);
									} else {
										$this->response = array(
											'status' => 501,
											'message' => ResponseMessages::getStatusCodeMessages(221),
										);
									}
								} else {
									$this->response = array(
										'status' => 400,
										'message' => ResponseMessages::getStatusCodeMessages(64),
									);
								}

							} else {
								$this->response = array(
									'status' => 400,
									'message' => ResponseMessages::getStatusCodeMessages(63),
								);
							}

						} else {
							$this->response = array(
								'status' => 400,
								'message' => ResponseMessages::getStatusCodeMessages(62),
							);
						}
					} else {
						$this->response = array(
							'status' => 400,
							'message' => ResponseMessages::getStatusCodeMessages(65),
						);
					}

				} else {
					$this->response = array(
						'status' => 301,
						'message' => ResponseMessages::getStatusCodeMessages(59),
					);
				}

			} else {
				$this->response = array(
					'status' => 400,
					'message' => ResponseMessages::getStatusCodeMessages(56),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		return $this->shut_down();
	}

// ------------------------------------------------   swagger response set   -------------------------------------------------//
	// function to track order
	/**
	 * @OA\Get(
	 * path="/trackOrder/{order_id}/{item_id}",
	 * description="Track Order",
	 * operationId="trackOrder",
	 * tags={"Order"},
	 * security={{"bearerAuth":{}}},
	 * @OA\Parameter(
	 *    description="Order ID",
	 *    in="path",
	 *    name="order_id",
	 *    required=true,
	 *    example="1",
	 *    @OA\Schema(
	 *       type="integer",
	 *       format="int64"
	 *    ),
	 * ),
	 * * @OA\Parameter(
	 *    description="Item ID",
	 *    in="path",
	 *    name="item_id",
	 *    required=true,
	 *    example="1",
	 *    @OA\Schema(
	 *       type="integer",
	 *       format="int64"
	 *    ),
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="title", type="string"),
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function trackOrder(Request $request, $order_id, $item_id) {
		$user = Auth::user();
		// $res = $this->checkKeys(array_keys($request->all()), array('order_id', 'item_id'));

		// if (gettype($res) != 'boolean') {
		//     return $res;
		// }
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			$result = Order::with('order_items')->where('user_id', $user->id)->where('id', $order_id)->first();
			$order = [];
			if (isset($result->id)) {

				$order['order_id'] = $result->id;
				$order['order_no'] = $result->order_no;

				$item = OrderItem::where('order_id', $result->id)->where('id', $item_id)->where('status', '!=', 'DL')->first();

				if (isset($item->id)) {

					if (isset($request->date) && !empty($request->date)) {
						$date = $request->date;
					} else {
						$last = OrderDelivery::select('order_date as date')->where('order_id', $result->id)->where('order_item_id', $item->id)->whereNull('reschedule_date')->where('status', 'PN')->orderBy('order_date')->first();
						if (!isset($last->date)) {

							$last = OrderDelivery::select('reschedule_date as date')->where('order_id', $result->id)->where('order_item_id', $item->id)->where('status', 'PN')->orderBy('reschedule_date')->first();
							if (!isset($last->date)) {

								$last = OrderDelivery::select('order_date as date')->where('order_id', $result->id)->where('order_item_id', $item->id)->orderBy('order_date', 'desc')->first();

								if (!isset($last->date)) {

									$last = OrderDelivery::select('reschedule_date as date')->where('order_id', $result->id)->where('order_item_id', $item->id)->orderBy('reschedule_date', 'desc')->first();
								}
							}
						}
						$date = $last->date;
					}

					$schedule = OrderDelivery::where('order_id', $result->id)->where('order_item_id', $item->id)->where(function ($q) use ($date) {
						$q->where('order_date', date('Y-m-d', strtotime($date)))->orWhere('reschedule_date', date('Y-m-d', strtotime($date)));
					})->first();

					if (isset($schedule->id)) {
						if ($schedule->order_date == date('Y-m-d', strtotime($date)) && $schedule->reschedule_date != null) {
							$this->response = array(
								'status' => 301,
								'message' => ResponseMessages::getStatusCodeMessages(63),
							);

						} else {
							$order['delivery_time'] = date('M d,Y', strtotime($date)) . ' at ' . date('h:i A', strtotime($item->delivery_slot->from_time)) . ' to ' . date('h:i A', strtotime($item->delivery_slot->to_time));

							$address = new UserAddress;
							$address->apartment = $result->user_address->apartment;
							$address->lat = $result->user_address->lat;
							$address->lng = $result->user_address->lng;
							$address->address = $result->user_address->address;
							$address->zipcode = $result->user_address->zipcode;
							$address->city = $result->user_address->acity->name;
							$address->state = $result->user_address->astate->name;
							$address->country = $result->user_address->acountry->name;

							$order['delivery_address'] = $address;
							$order['current_date'] = $date;
							$order['scheduled'] = $item->scheduled;

							$status = $this->getItemStatus($schedule->status);
							$order['status'] = $status;

							$process = array(
								array(
									'title' => 'Order Placed',
									'message' => 'We have received your order',
									'status' => 1,
								),
								array(
									'title' => 'In Process',
									'message' => "We're preparing your order",
									'status' => (in_array($status, ['pending', 'delivered'])) ? 1 : 0,
								),
								array(
									'title' => 'Order Delivered',
									'message' => 'Your order delivered successfully',
									'status' => (in_array($status, ['delivered'])) ? 1 : 0,
								),
							);

							$this->response = array(
								'status' => 200,
								'message' => ResponseMessages::getStatusCodeMessages(144),
								'order_status' => $process,
								'order' => $order,
							);
						}

					} else {
						$this->response = array(
							'status' => 400,
							'message' => ResponseMessages::getStatusCodeMessages(62),
						);
					}

				} else {
					$this->response = array(
						'status' => 400,
						'message' => ResponseMessages::getStatusCodeMessages(59),
					);
				}

			} else {
				$this->response = array(
					'status' => 400,
					'message' => ResponseMessages::getStatusCodeMessages(56),
				);
			}
		} catch (Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to track order no from drawer
	/**
	 * @OA\post(
	 * path="/trackOrderNo",
	 * description="trackOrderNo",
	 * operationId="trackOrderNo",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Order"},
	 *  @OA\RequestBody(
	 *    required=true,
	 *    description="trackOrderNo",
	 *    @OA\JsonContent(
	 *       required={"order_no"},
	 *       @OA\Property(property="order_no", type="string", format="text" , example="1"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="order_status", type="object"),
	 *       @OA\Property(property="order", type="object"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function trackOrderNo(Request $request) {
		$res = $this->checkKeys(array_keys($request->all()), array('order_no'));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			$result = Order::with('order_items')->where('order_no', $request->order_no)->first();
			$order = [];
			if (isset($result->id)) {
				$query = OrderItem::where('order_id', $result->id)->where('status', '!=', 'DL')->get();
				$scheduled = 0;
				$date = '';
				$time = '';

				foreach ($query as $key => $value) {
					if ($value->scheduled == '1') {
						$scheduled++;
					} else {
						$date = $value->start_date;
						$time = $value->delivery_slot;
						$status = $value->status;
					}
				}

				// if order is one time delivery order, then track order
				if ($scheduled != count($query)) {
					if (in_array($result->status, ['PN', 'RFCL', 'CM'])) {
						$order['delivery_time'] = date('M d,Y', strtotime($date)) . ' at ' . date('h:i A', strtotime($time->from_time)) . ' to ' . date('h:i A', strtotime($time->to_time));
						$order['order_id'] = $result->id;
						$order['order_no'] = $result->order_no;
						$order['scheduled'] = $scheduled;

						$address = new UserAddress;
						$address->apartment = $result->user_address->apartment;
						$address->lat = $result->user_address->lat;
						$address->lng = $result->user_address->lng;
						$address->address = $result->user_address->address;
						$address->zipcode = $result->user_address->zipcode;
						$address->city = $result->user_address->acity->name;
						$address->state = $result->user_address->astate->name;
						$address->country = $result->user_address->acountry->name;

						$order['delivery_address'] = $address;
						$status = $this->getItemStatus($status);
						$order['status'] = $status;

						$process = array(
							array(
								'title' => 'Order Placed',
								'message' => 'We have received your order',
								'status' => 1,
							),
							array(
								'title' => 'In Process',
								'message' => "We're preparing your order",
								'status' => (in_array($status, ['pending', 'delivered'])) ? 1 : 0,
							),
							array(
								'title' => 'Order Delivered',
								'message' => 'Your order delivered successfully',
								'status' => (in_array($status, ['delivered'])) ? 1 : 0,
							),
						);

						$this->response = array(
							'status' => 200,
							'message' => ResponseMessages::getStatusCodeMessages(144),
							'order_status' => $process,
							'order' => $order,
						);
					} else {
						$this->response = array(
							'status' => 301,
							'message' => ResponseMessages::getStatusCodeMessages(68),
						);
					}
				} else {
					$this->response = array(
						'status' => 301,
						'message' => ResponseMessages::getStatusCodeMessages(70),
					);
				}

			} else {
				$this->response = array(
					'status' => 400,
					'message' => ResponseMessages::getStatusCodeMessages(56),
				);
			}
		} catch (Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to mark/unmark product favourite
	/**
	 * @OA\post(
	 * path="/manageUserFavourite",
	 * description="manageUserFavourite",
	 * operationId="manageUserFavourite",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Account"},
	 *  @OA\RequestBody(
	 *    required=true,
	 *    description="manageUserFavourite",
	 *    @OA\JsonContent(
	 *       required={"product_id","product_variation_id"},
	 *       @OA\Property(property="product_id", type="string", format="text" , example="1"),
	 *       @OA\Property(property="product_variation_id", type="string", format="text" , example="1"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="cart_count", type="string"),
	 *       @OA\Property(property="currency", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function manageUserFavourite(Request $request) {
		$user = Auth::user();
		$res = $this->checkKeys(array_keys($request->all()), array('product_id', 'product_variation_id'));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			$user = User::find($user->id);

			if ($product = Product::find($request->product_id)) {
				$fav = ProductFavourite::where('user_id', $user->id)->where('product_id', $request->product_id)->where('product_variation_id', $request->product_variation_id)->orderBy('created_at', 'desc')->first();
				$exists = 0;

				if (isset($fav->id)) {
					$fav->status = ($fav->status == 'AC') ? 'IN' : 'AC';
					$exists = ($fav->status == 'AC') ? 1 : 0;
				} else {
					$fav = new ProductFavourite;
					$fav->user_id = $user->id;
					$fav->product_id = $request->product_id;
					$fav->product_variation_id = $request->product_variation_id;
					$fav->status = 'AC';
					$exists = 1;
				}
				if ($fav->save()) {
					$product = Product::select('*', DB::raw("CONCAT('" . URL::asset("uploads/products") . "/', image) image"))->where('id', $request->product_id)->with(['variations' => function ($q) use ($request) {
						$q->where('id', $request->product_variation_id);
					}])->first();

					$products[] = $product;
					$product = $this->formatProducts($products, $user->id);

					$this->response = array(
						'status' => 200,
						'message' => ($exists == 1) ? ResponseMessages::getStatusCodeMessages(71) : ResponseMessages::getStatusCodeMessages(72),
						// 'is_favorite' => ($exists == 1) ? 1 : 0,
						'data' => $product,
						'cart_count' => $this->getCartCount($user->id),
						"currency" => $this->currency,
					);
				} else {
					$this->response = array(
						'status' => 300,
						'message' => ResponseMessages::getStatusCodeMessages(502),
					);
				}
			} else {
				$this->response = array(
					'status' => 400,
					'message' => ResponseMessages::getStatusCodeMessages(44),
				);
			}
		} catch (Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		return $this->shut_down();
	}

	// function to mark/unmark product favourite
	/**
	 * @OA\get(
	 * path="/getUserFavourites",
	 * description="getUserFavourites",
	 * operationId="getUserFavourites",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Account"},
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *       @OA\Property(property="data", type="object"),
	 *       @OA\Property(property="cart_count", type="string"),
	 *       @OA\Property(property="currency", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function getUserFavourites(Request $request) {
		$user = Auth::user();
		//$res = $this->checkKeys(array_keys($request->all()));
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			$user = User::find($user->id);

			$favs = ProductFavourite::where('user_id', $user->id)->whereStatus('AC')->select('product_id', 'product_variation_id')->get();
			$products = [];

			if (count($favs) > 0) {
				foreach ($favs as $key => &$value) {

					$prod = Product::where('id', $value->product_id)->select('*', DB::raw("CONCAT('" . URL::asset("uploads/products/medium") . "/medium-', image) as medium_image, image"), DB::raw("CONCAT('" . URL::asset("uploads/products") . "/', image) image"))->with(['variations' => function ($q) use ($value) {
						$q->where('id', $value->product_variation_id);
					}])->first();

					$products[] = $prod;
				}
				$products = $this->formatProducts($products, $user->id, 'fav');

				$this->response = array(
					'status' => 200,
					'message' => ResponseMessages::getStatusCodeMessages(73),
					'data' => $products,
					'cart_count' => $this->getCartCount($user->id),
					'currency' => $this->currency,
				);

			} else {
				$this->response = array(
					'status' => 400,
					'message' => ResponseMessages::getStatusCodeMessages(324),
				);
			}
		} catch (Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	public function getDriverOrders(Request $request) {
		$res = $this->checkKeys(array_keys($request->all()), array('driver_id'));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($request->driver_id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {

			$orders = OrderDriver::select('order_id', 'date', DB::Raw('DATE_FORMAT(date,"%Y-%m") as duration'))->where('driver_id', $request->driver_id)->orderBy('created_at', 'desc')->groupBy('duration')->get();

			if (count($orders) > 0) {
				$result = [];
				foreach ($orders as $value) {
					$order = [];
					$order['month'] = date('M Y', strtotime($value->duration));

					$all_orders = OrderDriver::select('order_id', 'date')->where('driver_id', $request->driver_id)->whereRaw('DATE_FORMAT(date,"%Y-%m") = ?', [$value->duration])->orderBy('date')->get();

					$order['orders'] = [];

					foreach ($all_orders as $val) {
						$temp = [];
						$temp['id'] = $val->order->id;
						$temp['order_no'] = $val->order->order_no;
						$temp['user_name'] = $val->order->user->first_name . " " . $val->order->user->last_name;
						$temp['date'] = $val->date;

						$address = new UserAddress;
						$address->lat = $val->order->user_address->lat;
						$address->lng = $val->order->user_address->lng;
						$address->apartment = $val->order->user_address->apartment;
						$address->address = $val->order->user_address->address;
						$address->zipcode = $val->order->user_address->zipcode;
						$address->city = $val->order->user_address->acity->name;
						$address->state = $val->order->user_address->astate->name;
						$address->country = $val->order->user_address->acountry->name;
						$address->mobile_number = $val->order->user_address->mobile;

						$temp['delivery_address'] = $address;

						$items = OrderItem::where('order_id', $val->order->id)->whereRaw('? between start_date and end_date', [date('Y-m-d', strtotime($val->date))])->whereIn('status', ['PN', 'RFCL', 'CM', 'RFIN'])->get();

						$grand_total = 0;
						foreach ($items as $v) {

							$price = $v->qty * $v->special_price;
							$discount = ($v->coupon_discount != null) ? round(($v->special_price * $v->qty * $v->coupon_discount) / 100, 2) : 0;

							$total = $price - $discount;
							$grand_total += $total;

						}
						$temp['amount'] = sprintf('%.2f', $grand_total);

						if ($items->count() > 0) {
							$order['orders'][] = $temp;
						}

					}
					$order['total_orders'] = count($order['orders']);
					$result[] = $order;
				}

				$this->response = array(
					'status' => 200,
					'message' => ResponseMessages::getStatusCodeMessages(144),
					'currency' => $this->currency,
					'data' => $result,
				);

			} else {
				$this->response = array(
					'status' => 400,
					'message' => ResponseMessages::getStatusCodeMessages(22),
				);
			}
		} catch (Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	public function getDriverOrderDetails(Request $request) {
		$res = $this->checkKeys(array_keys($request->all()), array('driver_id', 'order_id', 'date'));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($request->driver_id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {
			$result = Order::with(['order_items' => function ($q) use ($request) {
				$q->whereRaw('? between start_date and end_date', [date('Y-m-d', strtotime($request->date))])->whereIn('status', ['PN', 'RFCL', 'CM', 'RFIN']);
			}])->where('id', $request->order_id)->whereHas('order_drivers', function ($q) use ($request) {
				$q->where('driver_id', $request->driver_id)->where('date', date('Y-m-d', strtotime($request->date)));
			}, '>', '0')->first();

			$order = [];
			if (isset($result->id)) {

				$order['id'] = $result->id;
				$order['order_no'] = $result->order_no;
				$order['user_name'] = $result->user->first_name . " " . $result->user->last_name;
				$order['user_mobile'] = $result->user->mobile_number;

				$address = new UserAddress;
				$address->apartment = $result->user_address->apartment;
				$address->lat = $result->user_address->lat;
				$address->lng = $result->user_address->lng;
				$address->address = $result->user_address->address;
				$address->zipcode = $result->user_address->zipcode;
				$address->city = $result->user_address->acity->name;
				$address->state = $result->user_address->astate->name;
				$address->country = $result->user_address->acountry->name;
				$address->mobile_number = $result->user_address->mobile;

				$order['delivery_address'] = $address;
				$order['status'] = $this->getItemStatus(OrderDriver::where('order_id', $result->id)->where('driver_id', $request->driver_id)->where('date', $request->date)->orderBy('id', 'desc')->first()->status);

				$order['products'] = [];
				$grand_total = 0;
				foreach ($result->order_items as $value) {

					$temp = [];

					$temp['image'] = URL::asset('uploads/products/' . $value->product_items->image);
					$temp['name'] = $value->product_items->name;
					$temp['price'] = sprintf("%.2f", $value->qty * $value->special_price);
					$temp['discount'] = ($value->coupon_discount != null) ? round(($value->special_price * $value->qty * $value->coupon_discount) / 100, 2) : 0;

					$temp['total'] = sprintf("%.2f", $temp['price'] - $temp['discount']);
					$grand_total += sprintf("%.2f", $temp['total']);
					$temp['qty'] = $value->qty;

					$order['products'][] = $temp;
				}
				$order['grand_total'] = sprintf('%.2f', $grand_total);

				$this->response = array(
					'status' => 200,
					'message' => ResponseMessages::getStatusCodeMessages(144),
					'currency' => $this->currency,
					'data' => $order,
				);

			} else {
				$this->response = array(
					'status' => 400,
					'message' => ResponseMessages::getStatusCodeMessages(56),
				);
			}
		} catch (Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	public function getDriverHomePage(Request $request) {
		$res = $this->checkKeys(array_keys($request->all()), array('driver_id'));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($request->driver_id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {

			$current_orders = OrderDriver::select('order_id', 'date')->where('driver_id', $request->driver_id)->whereStatus('PN')->where('date', '>=', date('Y-m-d'))->orderBy('created_at', 'desc')->get();

			$current_result = [];
			if (count($current_orders) > 0) {
				foreach ($current_orders as $val) {

					$temp = [];
					$temp['id'] = $val->order->id;
					$temp['order_no'] = $val->order->order_no;
					$temp['date'] = $val->date;
					$temp['user_name'] = $val->order->user->first_name . ' ' . $val->order->user->last_name;

					$address = new UserAddress;
					$address->apartment = $val->order->user_address->apartment;
					$address->lat = $val->order->user_address->lat;
					$address->lng = $val->order->user_address->lng;
					$address->address = $val->order->user_address->address;
					$address->zipcode = $val->order->user_address->zipcode;
					$address->city = $val->order->user_address->acity->name;
					$address->state = $val->order->user_address->astate->name;
					$address->country = $val->order->user_address->acountry->name;
					$address->mobile_number = $val->order->user_address->mobile;

					$temp['delivery_address'] = $address;

					$items = OrderItem::where('order_id', $val->order->id)->whereRaw('? between start_date and end_date', [date('Y-m-d', strtotime($val->date))])->whereIn('status', ['PN', 'RFCL', 'CM', 'RFIN'])->get();

					$grand_total = 0;
					foreach ($items as $v) {

						$price = $v->qty * $v->special_price;
						$discount = ($v->coupon_discount != null) ? round(($v->special_price * $v->qty * $v->coupon_discount) / 100, 2) : 0;

						$total = $price - $discount;
						$grand_total += $total;

					}
					$temp['amount'] = sprintf('%.2f', $grand_total);

					if ($items->count() > 0) {
						$current_result[] = $temp;
					}
				}
			}

			$previous_orders = OrderDriver::select('order_id', 'date')->where('driver_id', $request->driver_id)->where(function ($qu) {
				$qu->whereIn('status', ['CM', 'UN'])->orWhere('date', '<', date('Y-m-d'));
			})->orderBy('date', 'desc')->get();

			$previous_result = [];
			if (count($previous_orders) > 0) {
				foreach ($previous_orders as $val) {

					$temp = [];
					$temp['id'] = $val->order->id;
					$temp['order_no'] = $val->order->order_no;
					$temp['date'] = $val->date;
					$temp['user_name'] = $val->order->user->first_name . ' ' . $val->order->user->last_name;

					$address = new UserAddress;
					$address->apartment = $val->order->user_address->apartment;
					$address->lng = $val->order->user_address->lng;
					$address->lat = $val->order->user_address->lat;
					$address->address = $val->order->user_address->address;
					$address->zipcode = $val->order->user_address->zipcode;
					$address->city = $val->order->user_address->acity->name;
					$address->state = $val->order->user_address->astate->name;
					$address->country = $val->order->user_address->acountry->name;
					$address->mobile_number = $val->order->user_address->mobile;

					$temp['delivery_address'] = $address;

					$items = OrderItem::where('order_id', $val->order->id)->whereRaw('? between start_date and end_date', [date('Y-m-d', strtotime($val->date))])->whereIn('status', ['PN', 'RFCL', 'CM', 'RFIN'])->get();

					$grand_total = 0;
					foreach ($items as $v) {

						$price = $v->qty * $v->special_price;
						$discount = ($v->coupon_discount != null) ? round(($v->special_price * $v->qty * $v->coupon_discount) / 100, 2) : 0;

						$total = $price - $discount;
						$grand_total += $total;

					}
					$temp['amount'] = sprintf('%.2f', $grand_total);

					if ($items->count() > 0) {
						$previous_result[] = $temp;
					}
				}
			}

			$ncount = 0;

			if (isset($request->driver_id)) {
				$user = User::find($request->driver_id);
				if (isset($user->notifications)) {
					$ncount = $user->notifications->count();
				}

			}

			$this->response = array(
				'status' => 200,
				'message' => ResponseMessages::getStatusCodeMessages(144),
				'currency' => $this->currency,
				'current' => $current_result,
				'previous' => $previous_result,
				'unread_notification' => $ncount,
			);

		} catch (Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	public function updateOrderStatus(Request $request) {
		$res = $this->checkKeys(array_keys($request->all()), array('driver_id', 'order_id', 'date', 'status'));

		if (gettype($res) != 'boolean') {
			return $res;
		}
		$res = $this->checkUserActive($request->driver_id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		try {

			$od = OrderDriver::where('driver_id', $request->driver_id)->where('order_id', $request->order_id)->where('date', date('Y-m-d', strtotime($request->date)))->orderBy('id', 'desc')->first();
			$admin = User::find(1);

			if (isset($od->id)) {
				$od->status = $request->status;
				if ($request->status == 'UN') {
					$od->reason = $request->reason;
				}
				$od->save();

				$result = Order::with(['order_items' => function ($q) use ($request) {
					$q->whereRaw('? between start_date and end_date', [date('Y-m-d', strtotime($request->date))])
						->where('status', '<>', 'CL');
				}])->where('id', $request->order_id)->whereHas('order_drivers', function ($q) use ($request) {
					$q->where('driver_id', $request->driver_id)->where('date', date('Y-m-d', strtotime($request->date)));
				}, '>', '0')->first();

				$order = Order::find($result->id);

				if ($od->status == 'CM') {
					OrderDelivery::where('order_id', $order->id)->where(function ($q) use ($request) {
						$q->where('order_date', date('Y-m-d', strtotime($request->date)));
						$q->orWhere('reschedule_date', date('Y-m-d', strtotime($request->date)));
					})->whereIn('status', ['PN', 'RFCL'])->update(['status' => 'CM']);

					OrderItem::where('order_id', $order->id)->where('start_date', $request->date)->where('end_date', $request->date)->where('status', '<>', 'CL')->update(['status' => 'CM']);

					foreach ($result->order_items as $key => $value) {

						$notification_type = "order";
						$title = 'Item Delivered';
						$description = "Your item " . $value->product_items->name . " of order #: " . $order->order_no . " has been delivered for date " . date('d M, Y', strtotime($request->date)) . " to your selected address.";

						Notify::sendNotification($order->user_id, 1, $title, $description, $notification_type);

						$pendingcount = OrderDelivery::where('order_item_id', $value->id)->where('status', 'PN')->count();
						// check if all the scheduled delivery of this order item is delivered. If yes, change the status of that order item to delivered.

						if ($pendingcount <= 0) {
							$value->status = 'CM';
							$value->save();
						}
					}

					$pendingcount = OrderItem::where('order_id', $order->id)->where('status', 'PN')->count();
					// check if all the scheduled delivery of this order item is delivered. If yes, change the status of that order item to delivered.

					if ($pendingcount <= 0) {
						$order->status = 'CM';

						if ($order->save()) {
							$user = User::find($order->user_id);
							OrderDelivery::where('order_id', $order->id)->update(['status' => 'CM']);

							$notification_type = "order";
							$title = 'Order Delivered';
							$description = "Your order #: " . $order->order_no . " has been delivered for date " . date('d M, Y', strtotime($request->date)) . " to your selected address.";
							Notify::sendNotification($order->user_id, 1, $title, $description, $notification_type);
							Notify::sendNotification($user->id, 1, $title, $description, $notification_type);
							$notyToAdminDescription = "Driver has delivered the order for #: " . $order->order_no;
							Notify::sendNotification($admin->id, 1, $title, $notyToAdminDescription, $notification_type);

						}
					}
				} elseif ($od->status == 'UN') {
					$user = User::find($order->user_id);

					$notification_type = "order";
					$title = 'Order Undelivered';
					$description = "Your order #: " . $order->order_no . " was not delivered for date " . date('d M, Y', strtotime($request->date)) . " to your selected address because of the following reason: " . $od->reason;

					Notify::sendNotification($order->user_id, 1, $title, $description, $notification_type);

					$notyToAdminDescription = "Driver cancelled the delivery for order #: " . $order->order_no . " because of the following reason: " . $od->reason;

					Notify::sendNotification($admin->id, 1, $title, $notyToAdminDescription, $notification_type);
				}

				$order = [];

				$order['id'] = $result->id;
				$order['order_no'] = $result->order_no;
				$order['user_name'] = $result->user->first_name . " " . $result->user->last_name;
				$order['user_mobile'] = $result->user->mobile_number;

				$address = new UserAddress;
				$address->apartment = $result->user_address->apartment;
				$address->lat = $result->user_address->lat;
				$address->lng = $result->user_address->lng;
				$address->address = $result->user_address->address;
				$address->zipcode = $result->user_address->zipcode;
				$address->city = $result->user_address->acity->name;
				$address->state = $result->user_address->astate->name;
				$address->country = $result->user_address->acountry->name;
				$address->mobile_number = $result->user_address->mobile;

				$order['delivery_address'] = $address;
				$order['status'] = $this->getItemStatus($od->status);

				$order['products'] = [];
				$grand_total = 0;
				foreach ($result->order_items as $value) {

					$temp = [];

					$temp['image'] = URL::asset('uploads/products/' . $value->product_items->image);
					$temp['name'] = $value->product_items->name;
					$temp['price'] = sprintf("%.2f", $value->qty * $value->special_price);
					$temp['discount'] = ($value->coupon_discount != null) ? round(($value->special_price * $value->qty * $value->coupon_discount) / 100, 2) : 0;

					$temp['total'] = sprintf("%.2f", $temp['price'] - $temp['discount']);
					$grand_total += sprintf("%.2f", $temp['total']);
					$temp['qty'] = $value->qty;

					$order['products'][] = $temp;
				}
				$order['grand_total'] = sprintf("%.2f", $grand_total);

				$this->response = array(
					'status' => 200,
					'message' => $od->status == 'CM' ? ResponseMessages::getStatusCodeMessages(74) : ResponseMessages::getStatusCodeMessages(75),
					'currency' => $this->currency,
					'data' => $order,

				);
			} else {
				$this->response = array(
					'status' => 400,
					'message' => ResponseMessages::getStatusCodeMessages(56),
				);
			}

		} catch (Exception $e) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	/**
	 * @OA\post(
	 * path="/sendOtp",
	 * description="sendOtp",
	 * operationId="sendOtp",
	 *      tags={"Account"},
	 *  @OA\RequestBody(
	 *    required=true,
	 *    description="sendOtp",
	 *    @OA\JsonContent(
	 *       required={"mobile_number", "email"},
	 *       @OA\Property(property="mobile_number", type="string", format="text" , example="9828998732"),
	 *       @OA\Property(property="email", type="string", format="text" , example="abcv@gmail.com"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="boolean",example="true"),
	 *       @OA\Property(property="otp", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function sendOtp(Request $request) {
		$validator = $request->validate([
			'mobile_number' => ['required', Rule::unique('g_users')->whereIn('status', ['AC', 'IN'])],
			'email' => ['required', 'email', Rule::unique('g_users')->whereIn('status', ['AC', 'IN'])],
		]);

		$data = OTP::where('mobile_number', $request->mobile_number)->update(['expire_status' => 0]);
		$otp = new OTP;
		$otp->mobile_number = $request->mobile_number;
		$otp_generate = generateNumericOTP(4);
		$otp->otp = $otp_generate;
		$otp->expire_status = 1;

		if ($otp->save()) {

			$message = 'Use this One Time Password (OTP) ' . $otp->otp . ' to access the VelsBusinessClub Website - VELS BUSINESS CLUB';
			sendSMS($otp->mobile_number, $message);
			$data = [
				'email' => $request->email,
				'first_name' => '',
				'last_name' => '',
				'otp' => '',
			];

			// Notify::sendMail("emails.sendOtp", $data, "omrbranch - OTP Verification");

			return response()->json(['message' => 'OTP has been sent succesfully', 'status' => true, 'otp' => '']);
		} else {
			return response()->json(['message' => 'something went wrong please try again', 'status' => false]);
		}
	}

	public function otpExpire(Request $request) {
		$data = OTP::where('expire_status', 1)->get();
		foreach ($data as $key => $item) {
			$d = $item->created_at->format('%H:%I:%S');
		}
	}

	// --------------------------------------------------------------------------   ticket sbmit ---------------------------------------------

	/**
	 * @OA\post(
	 * path="/ticket-generate-send-otp",
	 * description="ticketSendOtp",
	 * operationId="ticketSendOtp",
	 *      tags={"Account"},
	 *  @OA\RequestBody(
	 *    required=true,
	 *    description="ticketSendOtp",
	 *    @OA\JsonContent(
	 *       required={"mobile_number", "email"},
	 *       @OA\Property(property="mobile_number", type="string", format="text" , example="9828998732"),
	 *       @OA\Property(property="email", type="string", format="text" , example="saini_01gmail.com"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="boolean",example="true"),
	 *       @OA\Property(property="otp", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function ticketSendOtp(Request $request) {
		$validator = $request->validate([
			'mobile' => 'required',
			'email' => 'required|email',
		]);

		$data = Ticket::where('email', $request->email)->update(['otp_expire_status' => 0]);
		$otp = new Ticket;
		$otp->email = $request->email;
		$otp_generate = generateNumericOTP(4);
		$otp->otp = $otp_generate;
		if ($otp->save()) {
			$data = [
				'email' => $request->email,
				'first_name' => '',
				'last_name' => '',
				'otp' => $otp_generate,
			];
			Notify::sendMail("emails.sendOtp", $data, "omrbranch - OTP Verification");
			return response()->json(['message' => 'OTP has been sent succesfully', 'status' => true, 'otp' => $otp_generate]);
		} else {
			return response()->json(['message' => 'something went wrong please try again', 'status' => false]);
		}
	}

	/**
	 * @OA\post(
	 * path="/ticketSubmit",
	 * description="ticketSubmit",
	 *      tags={"Account"},
	 * operationId="ticketSubmit",
	 *  @OA\RequestBody(
	 *    required=true,
	 *    description="ticketSubmit",
	 *    @OA\JsonContent(
	 *       required={"mobile","email","otp","order_no","subject","message"},
	 *       @OA\Property(property="mobile", type="string", format="text" , example="9828998732"),
	 *       @OA\Property(property="otp", type="string", format="text" , example="abc@gmail.com"),
	 *       @OA\Property(property="order_no", type="string", format="text" , example="Ab#123"),
	 *       @OA\Property(property="subject", type="string", format="text" , example="Regarding feedback"),
	 *       @OA\Property(property="message", type="string", format="text" , example="Message"),
	 *    )
	 * ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="boolean",example="true"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="401"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="404"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *       response=403,
	 *      description="Forbidden",
	 *      @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="403"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *    )
	 * ),
	 */

	public function ticketSubmit(Request $request) {
		$validator = $request->validate([
			'otp' => 'required|numeric',
			'order_no' => 'required',
			'subject' => 'required',
			'mobile' => 'required',
			'email' => 'required|email',
			'message' => 'required',
		]);

		$data = Ticket::where(['email' => $request->email, 'otp' => $request->otp, 'otp_expire_status' => 1])->first();
		if (isset($data)) {
			$auth_user = \App\model\User::find(FacadesSession::get('user')->id);
			$data->user_id = $auth_user->id;
			$data->email = $data->email;
			$data->mobile = $request->mobile;
			$data->order_no = $request->order_no;
			$data->subject = $request->subject;
			$data->message = $request->message;
			$data->message = $request->message;
			$data->otp_expire_status = 0;
			$data->otp = null;
			$data->otp_verify = 1;
			if ($data->save()) {
				return response()->json(['message' => 'Ticket generate succesfully', 'status' => true]);
			} else {
				return response()->json(['message' => 'something went wrong please try again', 'status' => false]);
			}
		} else {
			return response()->json(['message' => 'OTP does not match', 'status' => false]);
		}

	}

	public function updateCart() {
		$address_ids = UserAddress::where('status', 'DL')->pluck('id')->toArray();
		Cart::whereIn('address_id', $address_ids)->update(['address_id' => null]);
		dd('done');
	}

}
