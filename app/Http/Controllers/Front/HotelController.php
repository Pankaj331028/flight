<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HotelApiController as HotelApiController;
use App\Http\Controllers\MyController as MyController;
use App\Model\Booking;
use App\Model\BusRuleRef;
use App\Model\Hotel;
use App\Model\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Session;

class HotelController extends Controller {
	public function __construct(Request $request) {
		$this->api = (new HotelApiController($request));
		$this->function = (new MyController($request));
	}

	protected function guard() {
		return auth()->guard('front');
	}

	// public function signin() {
	// 	return view('front.login');
	// }

	public function travel(Request $request) {
		if (Auth::guard('front')->user()) {
			$user_id = Auth::guard('front')->user()->id;

			$states = Hotel::select(DB::Raw('distinct state as state'))->whereStatus('AC')->pluck('state')->toArray();

			return view('front.travel.index', compact('states'));
		} else {
			$user_id = '';
			$user = '';
			return view('front.login');
		}
	}

	public function searchHotel(Request $request) {
		
		$data = $this->api->getAllHotels($request);
		// dd($data);
		$states = Hotel::select(DB::Raw('distinct state as state'))->whereStatus('AC')->pluck('state')->toArray();
		$cities = [];

		if (isset($request->state) && !empty($request->state)) {
			$cities = Hotel::select(DB::Raw('distinct city as city'))->whereState($request->state)->whereStatus('AC')->pluck('city')->toArray();
		}

		if ($request->ajax()) {
			
			if (isset($data['status'])) {
				// dd($request->all());
				echo view('front.travel.hotel_list', compact('data'))->render();
			} else {
				echo 'error';
			}
		} else {
			if (isset($data['status'])) {
				// dd('hotels');
				return view('front.travel.hotels', compact('data', 'states', 'cities'));
			} else {
				$request->session()->flash('error', 'Something went wrong. Please try again later!');
				return redirect()->back();
			}
		}
	}

	public function viewHotel(Request $request, $id) {
		$request->merge(['hotel_id' => $id]);

		if ($request->check_in <= date('Y-m-d')) {

			$checkin = date_create($request->check_in);
			$checkout = date_create($request->check_out);
			$days = date_diff($checkin, $checkout);
			$request->merge([
				'check_in' => date('Y-m-d', strtotime('+1 day')),
				'check_out' => date('Y-m-d', strtotime(' + ' . (1 + $days->days) . ' Days')),
			]);
		}

		$data = $this->api->getHotelDetails($request);

		if (isset($data['status'])) {
			if ($data['status'] == 200) {
				$hotel = $data['data'];
				$currency = $data['currency'];
				return view('front.travel.hotel-details', compact('hotel', 'currency'));
			} else {

				$request->session()->flash('error', $data['message']);
				return redirect()->back();
			}
		} else {

			$request->session()->flash('error', 'Something went wrong. Please try again later!');
			return redirect()->back();
		}
	}
	// webview
	//
	public function bookingwebview(Request $request, $user_id, $booking_id) {

		$booking = Booking::where(['user_id' => $user_id, 'id' => $booking_id])->with('user', 'hotel')->first();

		if ($booking) {
			return view('front.travel.bookingwebview', ['booking' => $booking, 'user_id' => $user_id]);
		} else {
			echo "Booking does not exist";
		}
	}
	// bookingsuccess
	public function bookingsuccess(Request $request) {
		return view('front.travel.bookingsuccess');
	}

	// payment update on booking table using website.

	public function updateBookingwebview(Request $request, $user_id, $booking_id) {
		$booking = Booking::where('user_id', $user_id)
			->where('id', $booking_id)
			->first();

		$inputdata = $request->all();
		// $validatedData = $request->validate([
		// 	'payment_method' => 'required',

		// ]);
		$booking->fill($inputdata)->save();

		if ($booking) {
			return redirect()->route('bookingsuccess');
		} else {
			echo "Booking not found";
		}

	}

//

	public function bookHotel(Request $request) {
		// dd($request->all());
		$request->merge([
			'mobile_number' => $request->phone,
			'registration_no' => $request->registration,
			'gst' => ($request->gst == 'on') ? 'yes' : 'no',
		]);

		if ($request->payment_method == 'card') {
			$request->merge([
				'expiry_date' => $request->card_year . '-' . $request->card_month,
			]);
		}

		$data = $this->api->bookHotel($request);

		if (isset($data['booking_code'])) {
			$booking_no = $data['booking_code'];

			if (isset($data['hotel'])) {
				$hotel = $data['hotel'];
				return redirect()->route('confirmed', ['no' => $booking_no, 'hotel' => $hotel]);
			} else {
				$request->session()->flash('error', 'Hotel information is missing in the response.');
				return redirect()->back();
			}
		} else {
			$request->session()->flash('error', 'Booking information is missing in the response.');
			return redirect()->back();
		}
	}

	public function bookings(Request $request) {

		$data = $this->api->getAllBookings($request);

		$request->merge(['status' => 'confirmed']);

		$data1 = $this->api->getAllBookings($request);
		$user = User::find(Auth::guard('front')->user()->id);

		if (isset($data['status'])) {
			$bookings = $data['data'];
			$confirmed = $data1['data'];
			$currency = $data['currency'];
			return view('front.travel.bookings', compact('bookings', 'currency', 'confirmed', 'user'));
		} else {

			$request->session()->flash('error', 'Something went wrong. Please try again later!');
			return redirect()->route('travel');
		}
	}

	public function getBookings(Request $request, $status = null) {
		
		if ($status == 'confirmed') {
			$request->merge(['status' => 'confirmed']);
		}

		$data = $this->api->getAllBookings($request);

		if (isset($data['status'])) {
			if ($status == 'confirmed') {
				$confirmed = $data['data'];
				$currency = $data['currency'];
				echo view('front.travel.confirmed-bookinglist', compact('confirmed'))->render();
			} else {
				dd($request->all());
				$bookings = $data['data'];
				$currency = $data['currency'];
				echo view('front.travel.all-bookinglist', compact('bookings'))->render();
			}
		} else {
			echo 'error';
		}
	}

	public function cancelBooking(Request $request, $id) {

		$request->merge(['id' => $id]);

		$data = $this->api->cancelBooking($request);

		if (isset($data['status'])) {
			$request->session()->flash(($data['status'] == 200) ? 'alertsuccess' : 'alertdanger', $data['message']);
			return redirect()->back();
		} else {

			$request->session()->flash('alertdanger', 'Something went wrong. Please try again later!');
			return redirect()->back();
		}
	}

	public function updateBooking(Request $request, $id) {

		$data = $this->api->updateBooking($request);

		if (isset($data['status'])) {
			if ($data['status'] == 200) {
				$request->session()->flash('alertsuccess', $data['message']);
				return redirect()->route('my-bookings');
			} else {
				$request->session()->flash('alertdanger', $data['message']);
				return redirect()->back();
			}
		} else {

			$request->session()->flash('alertdanger', 'Something went wrong. Please try again later!');
			return redirect()->back();
		}
	}

	public function editBooking(Request $request, $id) {
		$book = Booking::find($id);

		if (isset($book->id) && $book->status == 'pending') {
			$currency = BusRuleRef::where('rule_name', 'currency')->first()->rule_value;
			return view('front.travel.edit-booking', compact('book', 'currency'));
		} else {
			$request->session()->flash('alertdanger', 'You cannot edit this booking now');
			return redirect()->back();
		}
	}

	public function viewBooking(Request $request, $no) {
		$book = Booking::whereBookingCode($no)->first();

		if (isset($book->id)) {
			$currency = BusRuleRef::where('rule_name', 'currency')->first()->rule_value;
			return view('front.travel.view-booking', compact('book', 'currency'));
		} else {
			$request->session()->flash('alertdanger', 'Invalid Booking');
			return redirect()->back();
		}
	}
}
