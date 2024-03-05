<?php

namespace App\Model;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model {

    protected $table ='bookings';
	protected $fillable = ['id','booking_code','user_id','payment_method','card_no','payment_type','card_type','cvv','card_name','month','year'];
	public function user() {
		return $this->belongsTo(User::class, "user_id", "id");
	}

	public function hotel() {
		return $this->belongsTo(Hotel::class, "hotel_id", "id");
	}

	public function fetchBookings($request, $columns) {
		$query = Booking::with(['user', 'hotel'])->where('status', '!=', 'DL');

		if (Auth::guard('admin')->user()->user_role[0]->role == 'user') {
			$query->whereUserId(Auth::guard('admin')->id());
		}

		/*if (isset($request->from_date)) {
				$query->where(function ($que) use ($request) {
					$que->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
				});
			}
			if (isset($request->end_date)) {
				$query->where(function ($que) use ($request) {
					$que->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
				});
			}
			if (isset($request->search)) {
				$query->where(function ($q) use ($request) {
					$q->orWhere('booking_code', 'like', '%' . $request->search . '%');
					//$q->orWhere('booking_no', 'like', '%' . $request->search . '%');
				});
			}
			if (isset($request->status)) {
				$query->where(function ($que) use ($request) {
					$que->where('status', $request->status);
				});
			}
			if (isset($request->order_column)) {
				$plans = $query->orderBy($columns[$request->order_column], $request->order_dir);
			} else {
		*/
		$plans = $query->orderBy('created_at', 'desc');
		return $plans;
	}
}
