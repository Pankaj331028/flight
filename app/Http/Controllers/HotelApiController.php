<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Library\ResponseMessages;
use App\Model\Booking;
use App\Model\Hotel;
use Auth;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use URL;
use Validator;

class HotelApiController extends MyController {
	// function to get all state
	/**
	 * @OA\post(
	 * path="/hotels",
	 * description="getAllHotels",
	 * operationId="getAllHotels",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Booking Api"},
	 *	@OA\RequestBody(
	 *    	required=true,
	 *    	description="get All Hotels",
	 *    	@OA\JsonContent(
	 *       	required={},
	 *       	@OA\Property(property="state", type="string", format="text" , example="Rajasthan"),
	 *       	@OA\Property(property="city", type="string", format="text" , example="Jaipur"),
	 *       	@OA\Property(property="room_type", type="array", @OA\Items(
	 *                       type="string",
	 *                  		), example={"Standard","Deluxe","Suite","Luxury","Studio"}, enum={"Standard","Deluxe","Suite","Luxury","Studio"}),
	 *       	@OA\Property(property="sortby", type="string", format="text" , example="pltoh/phtol/nasc/ndesc"),
	 *    	)
	 * 	),
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
	public function getAllHotels(Request $request) {
		$res = $this->checkLoginCookie($request->login_cookie);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		$user = Auth::user();
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		$validate = Validator::make($request->all(), [

			'check_in' => 'date|after_or_equal:today',
			'check_out' => 'date|after:check_in',
		]);
		if ($validate->fails()) {
			$errors = $validate->errors();
			$this->response = array(
				"status" => 300,
				"message" => $errors->first(),
				"data" => null,
				"errors" => $errors,
			);
		} else {
			$query = Hotel::where('status', 'AC');
			if (!empty($request->room_type) && !empty(array_filter($request->room_type)) && count(array_filter($request->room_type)) > 0) {
				$query = $query->whereIn('type', $request->room_type);
			}
			if (!empty($request->state)) {
				$query = $query->where('state', 'like', '%' . $request->state . '%');
			}
			if (!empty($request->city)) {
				$query = $query->where('city', 'like', '%' . $request->city . '%');
			}
			if (!empty($request->city)) {
				$query = $query->where('city', 'like', '%' . $request->city . '%');
			}
			if (!empty($request->sortby)) {
				if ($request->sortby == 'nasc') {
					$query = $query->orderBy('name', 'ASC');
				} elseif ($request->sortby == 'ndesc') {
					$query = $query->orderBy('name', 'DESC');
				} elseif ($request->sortby == 'pltoh') {
					$query = $query->orderBy('price', 'ASC');
				} elseif ($request->sortby == 'phtol') {
					$query = $query->orderBy('price', 'DESC');
				}
			} else {
				$query = $query->orderBy('id', 'DESC');
			}

			$data = $query->get();

			foreach ($data as &$value) {
				$checkin = date_create($request->check_in);
				$checkout = date_create($request->check_out);

				$days = date_diff($checkin, $checkout);

				$value->total = ($value->price * $request->no_rooms * $days->days) * (1 + (Config::get('constants.tax') / 100));
			}

			if (count($data) > 0) {
				$this->response = array(
					'status' => 200,
					'message' => ResponseMessages::getStatusCodeMessages(144),
					'currency' => $this->currency,
					'data' => $data,
					'gst' => config()->get('constants.gst'),
				);
			} else {
				$this->response = array(
					'status' => 404,
					'message' => ResponseMessages::getStatusCodeMessages(311),
				);
			}
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	// function to get getHotelStates
	/**
	 * @OA\GET(
	 * path="/getHotelStates",
	 * description="getHotelStates",
	 * operationId="getHotelStates",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Booking Api"},
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *      @OA\Property(property="currency", type="string"),
	 *       @OA\Property(property="data", type="array", @OA\Items(
	 *                       type="string",
	 *                  		)),
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
	public function getHotelStates(Request $request) {
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
			$data = Hotel::select(DB::Raw('distinct state as state'))->whereStatus('AC')->get();

			$this->response = array(
				'status' => 200,
				'message' => ResponseMessages::getStatusCodeMessages(144),
				'data' => $data,
			);

		} catch (\Exception $e) {
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

	// function to get getHotelCities
	/**
	 * @OA\GET(
	 * path="/getHotelCities/{state}",
	 * description="getHotelCities",
	 * operationId="getHotelCities",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Booking Api"},
	 *     @OA\Parameter(
	 *         name="state",
	 *         in="path",
	 *         description="State",
	 *         required=true,
	 *         schema={
	 *         		"type": "string",
	 *              "format": "text"
	 *         }
	 *      ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *      @OA\Property(property="currency", type="string"),
	 *       @OA\Property(property="data", type="array", @OA\Items(
	 *                       type="string",
	 *                  		)),
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
	public function getHotelCities(Request $request) {
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
			$data = Hotel::select(DB::Raw('distinct city as city'))->whereState($request->state)->whereStatus('AC')->get();

			$this->response = array(
				'status' => 200,
				'message' => ResponseMessages::getStatusCodeMessages(144),
				'data' => $data,
			);

		} catch (\Exception $e) {
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

	// function to get getHotelDetails
	/**
	 * @OA\GET(
	 * path="/getHotelDetails/{hotel_id}",
	 * description="getHotelDetails",
	 * operationId="getHotelDetails",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Booking Api"},
	 *     @OA\Parameter(
	 *         name="hotel_id",
	 *         in="path",
	 *         description="Hotel ID",
	 *         required=true,
	 *         schema={
	 *         		"type": "integer",
	 *              "format": "int"
	 *         }
	 *      ),
	 *     @OA\Parameter(
	 *         name="check_in",
	 *         in="query",
	 *         description="Check-in Date",
	 *         required=true,
	 *         schema={
	 *         		"type": "string",
	 *              "format": "date"
	 *         }
	 *      ),
	 *     @OA\Parameter(
	 *         name="check_out",
	 *         in="query",
	 *         description="Check-out Date",
	 *         required=true,
	 *         schema={
	 *         		"type": "string",
	 *              "format": "date"
	 *         }
	 *      ),
	 *     @OA\Parameter(
	 *         name="no_rooms",
	 *         in="query",
	 *         description="No. Of Rooms",
	 *         required=true,
	 *         schema={
	 *         		"type": "integer",
	 *         }
	 *      ),
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
	public function getHotelDetails(Request $request) {
		$user = Auth::user();
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		try {
			$data = Hotel::find($request->hotel_id);

			if (isset($data->id)) {
				$checkin = date_create($request->check_in);
				$checkout = date_create($request->check_out);

				$days = date_diff($checkin, $checkout);

				$data->total = ($data->price * $request->no_rooms * $days->days) * (1 + (Config::get('constants.tax') / 100));
				$this->response = array(
					'status' => 200,
					'message' => ResponseMessages::getStatusCodeMessages(144),
					'currency' => $this->currency,
					'data' => $data,
				);
			} else {
				$this->response = array(
					"status" => 404,
					"message" => ResponseMessages::getStatusCodeMessages(325),
				);
			}

		} catch (\Exception $e) {

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

	// function to book hotel
	/**
	 * @OA\POST(
	 * path="/create-booking",
	 * description="Book Hotel",
	 * operationId="createBooking",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Booking Api"},
	 *     @OA\RequestBody(
	 *    	required=true,
	 *    	description="API to book Hotel",
	 *    	@OA\JsonContent(
	 *       	required={},
	 *       	@OA\Property(property="hotel_id", type="integer" , example="1"),
	 *       	@OA\Property(property="check_in", type="string", format="date" , example="2022-12-25"),
	 *       	@OA\Property(property="check_out", type="string", format="date" , example="2022-12-30"),
	 *       	@OA\Property(property="booking_for", type="string", example="own/oth"),
	 *       	@OA\Property(property="title", type="string", format="text" , example="Mr/Mrs"),
	 *       	@OA\Property(property="first_name", type="string", format="text" , example="Prem"),
	 *       	@OA\Property(property="last_name", type="string", format="text" , example="OmrBranch"),
	 *       	@OA\Property(property="mobile_number", type="string", format="text" , example="9874563210"),
	 *       	@OA\Property(property="email", type="string", format="text" , example="prem@mailinator.com"),
	 *       	@OA\Property(property="gst", type="string", example="yes/no"),
	 *       	@OA\Property(property="registration_no", type="string", format="text" , example="MFRE5516551"),
	 *       	@OA\Property(property="company_name", type="string", format="text" , example="OmrBranch"),
	 *       	@OA\Property(property="company_address", type="string", format="text" , example="India"),
	 *       	@OA\Property(property="special_request", type="array", @OA\Items(
	 *                       type="string",
	 *                  		), example={"Smoking Room","Late Check-in","Early Check-in","Room on a high floor","Large Bed"}, enum={"Smoking Room","Late Check-in","Early Check-in","Room on a high floor","Large Bed"}),
	 *       	@OA\Property(property="other_request", type="string", format="text" , example=""),
	 *       	@OA\Property(property="no_rooms", type="integer" , example="2"),
	 *       	@OA\Property(property="no_adults", type="integer" , example="2"),
	 *       	@OA\Property(property="no_child", type="integer",  example="0"),
	 *       	@OA\Property(property="payment_method", type="string", format="text" , example="card/upi"),
	 *       	@OA\Property(property="upi", type="string", format="text" , example="8464546466@prem"),
	 *       	@OA\Property(property="card_no", type="string", format="text" , example="5555555555550000"),
	 *       	@OA\Property(property="payment_type", type="string", format="text" , example="debit_card/credit_card"),
	 *       	@OA\Property(property="card_type", type="string", format="text" , example="amex/visa/master/discover"),
	 *       	@OA\Property(property="cvv", type="integer" , example="123"),
	 *       	@OA\Property(property="expiry_date", type="string", format="date" , example="2023-12"),
	 *       	@OA\Property(property="card_name", type="string", format="text" , example="Prem"),
	 *    	)
	 * 	   ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *      @OA\Property(property="currency", type="string"),
	 *       @OA\Property(property="data", type="string",description="Booking Code"),
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
	 *      response=300,
	 *       description="Invalid Data (Validation Error)",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="300"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Incorrect Payment Details",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="Invalid Hotel",
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
	// for hotel booking webview
	public function bookingwebview(Request $request, $booking_id) {
		$user = Auth::user();
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		$booking = Booking::where(['user_id' => $user->id, 'id' => $booking_id])->with('user', 'hotel')->first();

		if ($booking) {
			return view('front.travel.bookingwebview', ['booking' => $booking]);
		} else {
			echo "Booking does not exist";
		}
	}
	// booking create step one
	public function bookingcreatestepone(Request $request) {
		$res = $this->checkLoginCookie($request->login_cookie);
		if (gettype($res) != 'boolean') {
			return $res;
		}
		$user = Auth::user();
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		// try {

		$validate = Validator::make($request->all(), [
			'hotel_id' => 'required|integer',
			'check_in' => 'required|date|after_or_equal:today',
			'check_out' => 'required|date|after:check_in',
			'booking_for' => 'required|in:own,oth',
			'title' => 'required|in:Mr,Ms',
			'first_name' => 'required',
			'last_name' => 'required',
			'mobile_number' => 'required',
			'email' => 'required',
			'gst' => 'required|in:yes,no',
			'registration_no' => 'required_if:gst,yes',
			'company_name' => 'required_if:gst,yes',
			'company_address' => 'required_if:gst,yes',
			'special_request' => 'nullable',
			'other_request' => 'nullable',
			'no_rooms' => 'required',
			'no_adults' => 'required',
			'no_child' => 'nullable',
		]);

		// $validate->setAttributeNames($attr);
		if ($validate->fails()) {
			$errors = $validate->errors();
			$this->response = array(
				"status" => 300,
				"message" => $errors->first(),
				"data" => null,
				"errors" => $errors,
			);
		} else {
			$hotel = Hotel::find($request->hotel_id);
			$valid = true;
			$validgst = true;

			if (isset($hotel->id)) {

				if (!empty($request->registration_no) && trim($request->registration_no) != Config::get('constants.gst.registration_no')) {
					$validgst = false;
				}

				if (!empty($request->company_name) && trim($request->company_name) != Config::get('constants.gst.name')) {
					$validgst = false;
				}

				if (!empty($request->company_address) && trim($request->company_address) != Config::get('constants.gst.address')) {
					$validgst = false;
				}

				if ($validgst) {
					if ($valid) {

						$booking = new Booking;
						$booking->booking_code = Helper::generateBookingCode();
						$booking->title = $request->title;
						$booking->first_name = $request->first_name;
						$booking->last_name = $request->last_name;
						$booking->booking_for = $request->booking_for;
						$booking->hotel_id = $request->hotel_id;
						$booking->user_id = $user->id;
						$booking->email = $request->email;
						$booking->phone = $request->phone;
						$booking->gst = $request->gst;
						$booking->registration = $request->registration_no;
						$booking->company_name = $request->company_name;
						$booking->company_address = $request->company_address;
						$booking->special_request = !empty($request->special_request) ? implode(',', $request->special_request) : null;
						$booking->other_request = $request->other_request;
						$booking->no_of_rooms = $request->no_rooms;
						$booking->no_of_adults = $request->no_adults;
						$booking->no_of_child = $request->no_child ?? 0;
						$booking->check_in = date('Y-m-d', strtotime($request->check_in));
						$booking->check_out = date('Y-m-d', strtotime($request->check_out));

						$checkin = date_create($request->check_in);
						$checkout = date_create($request->check_out);

						$days = date_diff($checkin, $checkout);

						$booking->price = ($hotel->price * $request->no_rooms * $days->days);
						// + Config::get('constants.room_types.' . $request->room_type);
						$booking->price_with_tax = $booking->price * (1 + (Config::get('constants.tax') / 100));

						if ($booking->save()) {
							$this->response = array(
								'status' => 200,
								'message' => ResponseMessages::getStatusCodeMessages(144),
								'currency' => $this->currency,
								'booking_code' => $booking->booking_code,
								'booking_id' => $booking->id,
								'hotel' => $booking->hotel->name,
							);
						}
					} else {
						$this->response = array(
							'status' => 400,
							'message' => ResponseMessages::getStatusCodeMessages(326),
						);
					}
				} else {
					$this->response = array(
						'status' => 400,
						'message' => ResponseMessages::getStatusCodeMessages(329),
					);
				}

			} else {
				$this->response = array(
					'status' => 404,
					'message' => ResponseMessages::getStatusCodeMessages(325),
				);
			}
		}

		if (stripos($request->url(), '/api') !== false) {
			return $this->shut_down();
		} else {
			return $this->response;
		}
	}

	public function bookHotel(Request $request) {
		$user = Auth::user();
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		// try {

		$validate = Validator::make($request->all(), [
			'hotel_id' => 'required|integer',
			'check_in' => 'required|date|after_or_equal:today',
			'check_out' => 'required|date|after:check_in',
			'booking_for' => 'required|in:own,oth',
			'title' => 'required|in:Mr,Ms',
			'first_name' => 'required',
			'last_name' => 'required',
			'mobile_number' => 'required',
			'email' => 'required',
			'gst' => 'required|in:yes,no',
			'registration_no' => 'required_if:gst,yes',
			'company_name' => 'required_if:gst,yes',
			'company_address' => 'required_if:gst,yes',
			'special_request' => 'nullable',
			'other_request' => 'nullable',
			'no_rooms' => 'required',
			'no_adults' => 'required',
			'no_child' => 'nullable',
			'payment_method' => 'required|in:upi,card',
			'upi' => 'required_if:payment_method,upi',
			'card_no' => 'required_if:payment_method,card',
			'payment_type' => 'nullable|required_if:payment_method,card|in:debit_card,credit_card',
			'card_type' => 'nullable|required_if:payment_method,card|in:amex,visa,master,discover',
			'cvv' => 'required_if:payment_method,card',
			'card_name' => 'required_if:payment_method,card',
			'expiry_date' => 'required_if:payment_method,card|date_format:Y-m|after_or_equal:' . date('Y-m'),
		]);

		// $validate->setAttributeNames($attr);
		if ($validate->fails()) {
			$errors = $validate->errors();
			$this->response = array(
				"status" => 300,
				"message" => $errors->first(),
				"data" => null,
				"errors" => $errors,
			);
		} else {
			$hotel = Hotel::find($request->hotel_id);
			$valid = true;
			$validgst = true;

			if (isset($hotel->id)) {

				switch ($request->payment_method) {
				case 'upi':if (!in_array($request->upi, Config::get('constants.upi'))) {
						$valid = false;
					}

					break;
				case 'card':

					if (!in_array($request->card_no, Config::get('constants.' . $request->payment_type . '.' . $request->card_type))) {
						$valid = false;
					}

					break;
				}

				if (!empty($request->registration_no) && trim($request->registration_no) != Config::get('constants.gst.registration_no')) {
					$validgst = false;
				}

				if (!empty($request->company_name) && trim($request->company_name) != Config::get('constants.gst.name')) {
					$validgst = false;
				}

				if (!empty($request->company_address) && trim($request->company_address) != Config::get('constants.gst.address')) {
					$validgst = false;
				}

				if ($validgst) {
					
					if ($valid) {
						
						$booking = new Booking;
						$booking->booking_code = Helper::generateBookingCode();
						$booking->title = $request->title;
						$booking->first_name = $request->first_name;
						$booking->last_name = $request->last_name;
						$booking->booking_for = $request->booking_for;
						$booking->hotel_id = $request->hotel_id;
						$booking->user_id = $user->id;
						$booking->email = $request->email;
						$booking->phone = $request->phone;
						$booking->gst = $request->gst;
						$booking->registration = $request->registration_no;
						$booking->company_name = $request->company_name;
						$booking->company_address = $request->company_address;
						$booking->special_request = !empty($request->special_request) ? implode(',', $request->special_request) : null;
						$booking->other_request = $request->other_request;
						$booking->no_of_rooms = $request->no_rooms;
						$booking->no_of_adults = $request->no_adults;
						$booking->no_of_child = $request->no_child ?? 0;
						$booking->check_in = date('Y-m-d', strtotime($request->check_in));
						$booking->check_out = date('Y-m-d', strtotime($request->check_out));
						$booking->payment_method = $request->payment_method;
						$booking->payment_type = $request->payment_type;
						$booking->card_no = $request->payment_method == 'card' ? $request->card_no : $request->upi;
						$booking->card_type = $request->card_type;
						$booking->year = date('Y', strtotime($request->expiry_date));
						$booking->month = date('m', strtotime($request->expiry_date));
						$booking->cvv = $request->cvv;

						$checkin = date_create($request->check_in);
						$checkout = date_create($request->check_out);

						$days = date_diff($checkin, $checkout);

						$booking->price = ($hotel->price * $request->no_rooms * $days->days);
						// + Config::get('constants.room_types.' . $request->room_type);
						$booking->price_with_tax = $booking->price * (1 + (Config::get('constants.tax') / 100));

						if ($booking->save()) {
							$this->response = array(
								'status' => 200,
								'message' => ResponseMessages::getStatusCodeMessages(144),
								'currency' => $this->currency,
								'booking_code' => $booking->booking_code,
								'booking_id' => $booking->id,
								'hotel' => $booking->hotel->name,
							);
						}
					} else {
						$this->response = array(
							'status' => 400,
							'message' => ResponseMessages::getStatusCodeMessages(326),
						);
					}
				} else {
					$this->response = array(
						'status' => 400,
						'message' => ResponseMessages::getStatusCodeMessages(329),
					);
				}

			} else {
				$this->response = array(
					'status' => 404,
					'message' => ResponseMessages::getStatusCodeMessages(325),
				);
			}
		}

		// } catch (\Exception $e) {
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

	// function to update booking
	/**
	 * @OA\PUT(
	 * path="/update-booking",
	 * description="Update Check-in & Check-out for booking",
	 * operationId="updateBooking",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Booking Api"},
	 *     @OA\RequestBody(
	 *    	required=true,
	 *    	description="API to book Hotel",
	 *    	@OA\JsonContent(
	 *       	required={},
	 *       	@OA\Property(property="booking_no", type="string",format="text" , example="MFJGU95478"),
	 *       	@OA\Property(property="check_in", type="string", format="date" , example="2022-12-25"),
	 *    	)
	 * 	   ),
	 *     @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="200"),
	 *       @OA\Property(property="message", type="string"),
	 *      @OA\Property(property="currency", type="string"),
	 *       @OA\Property(property="data", type="string",description="Booking Code"),
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
	 *      response=300,
	 *       description="Invalid Data (Validation Error)",
	 *    @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="300"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Incorrect Payment Details",
	 *       @OA\JsonContent(
	 *       @OA\Property(property="status", type="number",example="400"),
	 *       @OA\Property(property="message", type="string"),
	 *        )
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="Invalid Hotel",
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
	public function updateBooking(Request $request) {
		$user = Auth::user();
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		// try {

		$validate = Validator::make($request->all(), [
			'booking_no' => 'required',
			'check_in' => 'required|date|after_or_equal:today',
			// 'check_out' => 'required|date|after:check_in',
		]);

		// $validate->setAttributeNames($attr);
		if ($validate->fails()) {
			$errors = $validate->errors();
			$this->response = array(
				"status" => 300,
				"message" => $errors->first(),
				"data" => null,
				"errors" => $errors,
			);
		} else {
			$booking = Booking::whereBookingCode($request->booking_no)->first();

			if (isset($booking->id) && $booking->user_id == $user->id) {

				if ($booking->status == 'pending') {
					$checkin = date_create($booking->check_in);
					$checkout = date_create($booking->check_out);
					$days = date_diff($checkin, $checkout);
					// if ($booking->check_in > date('Y-m-d', strtotime('+1 Day'))) {

					$booking->check_in = date('Y-m-d', strtotime($request->check_in));
					$booking->check_out = date('Y-m-d', strtotime($request->check_in . ' + ' . $days->days . ' Days'));

					$checkin = date_create($booking->check_in);
					$checkout = date_create($booking->check_out);

					$days = date_diff($checkin, $checkout);
					$hotel = Hotel::find($booking->hotel_id);

					$booking->price = ($hotel->price * $booking->no_of_rooms * $days->days);
					// + Config::get('constants.room_types.' . $booking->room_type);
					$booking->price_with_tax = $booking->price * (1 + (Config::get('constants.tax') / 100));

					if ($booking->save()) {
						$this->response = array(
							'status' => 200,
							'message' => ResponseMessages::getStatusCodeMessages(604),
							'currency' => $this->currency,
							'data' => $booking->booking_code,
						);
					}
				} else {
					$this->response = array(
						'status' => 400,
						'message' => ResponseMessages::getStatusCodeMessages(327),
					);
				}

			} else {
				$this->response = array(
					'status' => 404,
					'message' => ResponseMessages::getStatusCodeMessages(328),
				);
			}
		}

		// } catch (\Exception $e) {
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

	// function to get all booking
	/**
	 * @OA\get(
	 * path="/bookings",
	 * description="getAllBookings",
	 * operationId="getAllBookings",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Booking Api"},
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
	public function getAllBookings(Request $request) {
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
			$condition = ['user_id' => $user->id];

			if (isset($request->status)) {
				$condition['status'] = $request->status;
			}

			$query = Booking::where($condition)->where('payment_method', '!=', '')->with(['hotel']);

			if (isset($request->search)) {
				$query->where('booking_code', 'like', '%' . $request->search . '%');
			}

			$data = $query->orderBy('id', 'desc')->get();

			$this->response = array(
				'status' => 200,
				'message' => ResponseMessages::getStatusCodeMessages(144),
				'currency' => $this->currency,
				'data' => $data,
			);

		} catch (\Exception $e) {
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

	// function to get booking-detail
	/**
	 * @OA\get(
	 * path="/bookings-details",
	 * description="getBookingDetail",
	 * operationId="getBookingDetail",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Booking Api"},
	 *	   @OA\Parameter(
	 *         name="id",
	 *         in="query",
	 *         description="",
	 *         required=true,
	 *      ),
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
	public function getSingleBookings(Request $request) {
		$user = Auth::user();
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		try {
			$data = Booking::where(['id' => $request->id, 'user_id' => $user->id])->with(['hotel'])->first();
			if (empty($data)) {
				$this->response = array(
					'status' => 400,
					'message' => ResponseMessages::getStatusCodeMessages(601),
					'currency' => $this->currency,
				);
			}
			$this->response = array(
				'status' => 200,
				'message' => ResponseMessages::getStatusCodeMessages(144),
				'currency' => $this->currency,
				'data' => $data,
			);

		} catch (\Exception $e) {
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

	// function to cancel booking
	/**
	 * @OA\delete(
	 * path="/cancel-booking",
	 * description="cancelBooking",
	 * operationId="cancelBooking",
	 *  security={{"bearerAuth":{}}},
	 *      tags={"Booking Api"},
	 *  @OA\RequestBody(
	 *    required=true,
	 *    description="cancelBooking",
	 *    @OA\JsonContent(
	 *       required={"id"},
	 *       @OA\Property(property="id", type="string", format="text" , example="1"),
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
	public function cancelBooking(Request $request) {
		$user = Auth::user();
		$res = $this->checkUserActive($user->id);
		if (gettype($res) != 'boolean') {
			return $res;
		}

		try {
			$data = Booking::where(['id' => $request->id, 'user_id' => $user->id])->first();
			if (empty($data)) {
				$this->response = array(
					'status' => 400,
					'message' => ResponseMessages::getStatusCodeMessages(600),
					'currency' => $this->currency,
				);
			} elseif ($data->status == 'cancelled') {
				$this->response = array(
					'status' => 400,
					'message' => ResponseMessages::getStatusCodeMessages(602),
					'currency' => $this->currency,
				);
			} else {
				$data->status = 'cancelled';
				$data->save();
				$this->response = array(
					'status' => 200,
					'message' => ResponseMessages::getStatusCodeMessages(603),
					'currency' => $this->currency,
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

}
