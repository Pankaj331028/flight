<?php

namespace App\Http\Controllers;

use App\Model\Booking;
use App\Model\User;
use Config;
use Illuminate\Http\Request;

class BookingController extends Controller {
	public $booking;
	public $columns;

	public function __construct() {
		$this->booking = new Booking;
		$this->columns = [
			"sno", "booking_code", "hotel_id", "user_id", "no_of_rooms", "check_in", "payment_method", "check_out", "price", "price_with_tax", "no_of_adults", "no_of_child", "status", 'created_at',
		];

	}
	public function index() {
		return view('bookings.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function bookingAjax(Request $request) {

		$request->search = $request->search['value'] ?? '';
		//dd($request->search);
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->booking->fetchBookings($request, $this->columns);
		$total = $records->count();
		if (isset($request->start)) {
			$bookings = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$bookings = $records->offset($request->start)->limit($total)->get();
		}
		// echo $total;
		$result = [];
		$i = $request->start + 1;
		foreach ($bookings as $key => $booking) {
			$data = [];
			$data['sno'] = $key + 1;
			$data['booking_code'] = $booking->booking_code;
			$data['user_id'] = $booking->user['first_name'] . " " . $booking->user['last_name'];
			$data['hotel_id'] = $booking->hotel['name'];
			$data['payment_method'] = Config::get('constants.PAYMENT_METHOD.' . $booking->payment_method);
			$data['price'] = $booking->price;
			$data['price_with_tax'] = $booking->price_with_tax;
			$data['no_of_rooms'] = $booking->no_of_rooms;
			$data['check_in'] = $booking->check_in;
			$data['check_out'] = $booking->check_out;
			$data['no_of_adults'] = $booking->no_of_adults;
			$data['no_of_child'] = $booking->no_of_child;
			$status = '<form class="form-inline px-3 form-valide" method="post" action="' . route('booking-status', ['id' => $booking->id]) . '">
                                            <div class="form-group">
                                                ' . csrf_field() . '
                                                <select class="form-control" name="status"  required>
                                                    <option value="pending" ' . ($booking->status == 'pending' ? 'selected' : '') . '>Pending</option>
                                                    <option value="cancelled" ' . ($booking->status == 'cancelled' ? 'selected' : '') . '>Cancelled</option>
                                                    <option value="confirmed" ' . ($booking->status == 'confirmed' ? 'selected' : '') . '>Confirmed</option>
                                                </select>
                                            </div>
                                        </form>';
			$data['status'] = $status;

			$result[] = $data;
		}
		$data = json_encode([
			'data' => $result,
			'recordsTotal' => $total,
			'recordsFiltered' => $total,
		]);
		echo $data;

	}

	public function bookingStatus(Request $request, $id) {

		if (isset($id) && $id != null) {
			if (Booking::whereId($id)->update(['status' => $request->status])) {
				$request->session()->flash('success', 'Booking updated successfully.');
				return redirect()->back();
			} else {
				$request->session()->flash('error', 'Unable to update booking. Please try again later.');
				return redirect()->back();
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->back();
		}

	}

}
