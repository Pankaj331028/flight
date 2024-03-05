<?php

namespace App\Model;

use Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable {
	use Notifiable, HasApiTokens;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $table = 'g_users';
	protected $fillable = [
		'name', 'email', 'password',
	];

	// protected $with = ["city"];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'remember_token',
	];

	public function user_role() {
		return $this->belongsToMany('App\Model\Role', 'g_user_roles');
	}

	public function ucountry() {
		return $this->belongsTo('App\Model\Country', 'country', 'name');
	}

	public function ustate() {
		return $this->belongsTo('App\Model\State', 'state', 'name');
	}

	public function ucity() {
		return $this->belongsTo('App\Model\City', 'city', 'name');
	}

	public function favourites() {
		return $this->hasMany(ProductFavourite::class, "user_id");
	}

	public function active_favs() {
		return $this->hasMany(ProductFavourite::class, "user_id")->where('status', 'AC');
	}

	public function inactive_favs() {
		return $this->hasMany(ProductFavourite::class, "user_id")->where('status', 'IN');
	}

	public function carts() {
		return $this->hasMany('App\Model\Cart');
	}

	public function orders() {
		return $this->hasMany('App\Model\Order');
	}

	public function wallet() {
		return $this->hasMany('App\Model\Wallet');
	}

	public function user_invites() {
		return $this->hasMany('App\Model\ReferrerUser', 'referrer_id', 'id');
	}

	public function user_referrer() {
		return $this->belongsToMany('App\Model\User', 'user_referrers', 'user_id', 'referrer_id');
	}

	public function user_couponcode() {
		return $this->hasMany('App\Model\UserCouponCode');
	}

	public function user_addresses() {
		return $this->hasMany('App\Model\UserAddress');
	}

	public function rating() {
		return $this->hasMany('App\Model\Rating');
	}

	public function assignRole($role) {
		return $this->user_role()->sync(
			[Role::whereRole($role)->firstOrFail()->id]
		);
	}

	public function fetchUsers($request, $columns) {
		$query = User::whereHas('user_role', function ($query) use ($request) {
			$query->where('g_roles.role', 'user');
		})->where('status', '!=', 'DL');

		if (Auth::guard('admin')->user()->user_role[0]->role == 'user') {
			$query->whereId(Auth::guard('admin')->id());
		}

		if (isset($request->from_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
		}
		if (isset($request->end_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
		}
		if (isset($request->search)) {
			$query->where(function ($q) use ($request) {
				$q->whereRaw('CONCAT(first_name," ",last_name) like ?', ["%" . $request->search . "%"]);
				$q->orWhere('email', 'like', '%' . $request->search . '%');
				$q->orWhere('mobile_number', 'like', '%' . $request->search . '%');
				$q->orWhere('user_code', 'like', '%' . $request->search . '%');
			});
		}
		if (isset($request->status)) {
			$query->where('status', $request->status);
		}
		if (isset($request->google_signin)) {
			$query->where('google_signin', $request->google_signin);
		}
		if (isset($request->facebook_signin)) {
			$query->where('facebook_signin', $request->facebook_signin);
		}
		if (isset($request->is_verified)) {
			$query->where('is_verified', $request->is_verified);
		}

		if (isset($request->order_column)) {
			$users = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$users = $query->orderBy('created_at', 'desc');
		}
		return $users;
	}

	public function fetchSubadmins($request, $columns) {

		$query = User::with(['user_role'])->whereHas('user_role', function ($query) use ($request) {
			$query->whereNotIn('role', ['admin', 'driver']);
		});

		if (isset($request->role)) {
			$query->whereHas('user_role', function ($query) use ($request) {
				$query->where('role', $request->role);
			});
		}

		if (isset($request->from_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
		}
		if (isset($request->end_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
		}
		if (isset($request->search)) {
			$query->where(function ($q) use ($request) {
				$q->whereRaw("CONCAT_WS(' ', first_name, last_name) like  '%" . $request->search . "%'");
				$q->orWhere('email', 'like', '%' . $request->search . '%');
				$q->orWhere('mobile_number', 'like', '%' . $request->search . '%');
				$q->orWhere('user_code', 'like', '%' . $request->search . '%');
			});
		}
		if (isset($request->status)) {
			$query->where('status', $request->status);
		}

		if (isset($request->order_column)) {
			$users = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$users = $query->orderBy('created_at', 'desc');
		}
		return $users;
	}

	public function fetchDrivers($request, $columns) {
		$query = User::with(['user_role'])->whereHas('user_role', function ($query) use ($request) {
			$query->where('role', 'driver');
		});

		if (isset($request->from_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
		}
		if (isset($request->end_date)) {
			$query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
		}
		if (isset($request->search)) {
			$query->where(function ($q) use ($request) {
				$q->whereRaw("CONCAT_WS(' ', first_name, last_name) like  '%" . $request->search . "%'");
				$q->orWhere('email', 'like', '%' . $request->search . '%');
				$q->orWhere('mobile_number', 'like', '%' . $request->search . '%');
			});
		}
		if (isset($request->status)) {
			$query->where('status', $request->status);
		}

		if (isset($request->order_column)) {
			$users = $query->orderBy($columns[$request->order_column], $request->order_dir);
		} else {
			$users = $query->orderBy('created_at', 'desc');
		}
		return $users;
	}

	public function getFullNameAttribute() {
		return $this->first_name . ' ' . $this->last_name;
	}

	//user has only one cart
	public function cart() {
		return $this->hasOne('App\Model\Cart', 'user_id');
	}

	public function notifications() {

		return $this->hasMany('App\Model\Notification', 'receiver_id')->where('status', 'AC')->where('is_read', '0');

	}
}
