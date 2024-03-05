<?php
namespace App\Http\Controllers;

use App\Library\Notify;
use App\Model\Order;
use App\Model\OrderDelivery;
use App\Model\OrderItem;
use App\Model\OrderReturn;
use App\Model\Product;
use App\Model\User;
use Auth;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller {

	protected function guard() {
		return auth()->guard('admin');
	}

	public function index(Request $request) {
		if ($this->guard()->check()) {
			if (isset($this->guard()->user()->user_role) && $this->guard()->user()->user_role[0]->id != '' && ($this->guard()->user()->user_role[0]->role == 'admin' || ($this->guard()->user()->user_role[0]->role == 'user' && $this->guard()->user()->admin_panel_access == 'yes'))) {
				DB::enableQueryLog();
				$city = Session::get('globalCity');
				$state = Session::get('globalState');
				$country = Session::get('globalCountry');

				$users = User::whereHas('user_role', function ($query) use ($request) {
					$query->where('g_roles.id', '!=', '0');
				}, '=', '0')->where('status', '!=', 'DL')->where('id', '!=', 1)->count();

				$products = Product::where('status', '!=', 'DL')->whereHas('variations', function ($q) {
					$q->where('status', '!=', 'DL');
				})->count();

				$orders = Order::where('status', '!=', 'DL')->where(function ($q) {
					if ($this->guard()->user()->user_role[0]->role == 'user') {
						$q->whereUserId($this->guard()->id());
					}

				})->count();

				$deliveries = OrderDelivery::whereRaw('DATE_FORMAT(order_date, "%Y-%m-%d") >= "' . date("Y-m-d") . '"')->orWhereRaw('DATE_FORMAT(reschedule_date, "%Y-%m-%d") >= "' . date("Y-m-d") . '"')->where('status', 'PN')->count();

				$drivers = User::whereHas('user_role', function ($query) use ($request) {
					$query->where('g_roles.id', '=', '3');
				})->where('status', '!=', 'DL')->where('id', '!=', 1)->count();

				$codes = OrderItem::select('c.code', DB::raw('count(DISTINCT order_id) as total'))
					->leftJoin('c_coupon_range as r', 'r.id', '=', 'order_items.coupon_code_id')
					->leftJoin('c_coupon_codes as c', 'c.id', '=', 'r.coupon_code_id')
					->whereNotNULL('order_items.coupon_code_id')
					->groupBy('r.coupon_code_id')
					->get();

				return view('dashboard', compact('users', 'products', 'orders', 'deliveries', 'codes', 'drivers'));
			} else {
				$request->session()->flash('error', "You don't have permission to access this panel");
				return view('login');
			}
		}
		return view('login');
	}

	public function login(Request $request) {

		$credentials = ['email' => $request->post('email'), 'password' => $request->post('password'), 'status' => 'AC'];

		$udetail = User::with('user_role')->where('email', $request->post('email'))->whereStatus('AC')->first();
		// dd($udetail);

		if (isset($udetail->user_role) && count($udetail->user_role) > 0) {
			$remember = ($request->post('remember') == "on") ? true : false;
			if (isset($udetail->user_role) && $udetail->user_role[0]->role != '' && ($udetail->user_role[0]->role == 'admin' || ($udetail->user_role[0]->role == 'user' && $udetail->admin_panel_access == 'yes'))) {
				if ($this->guard()->attempt($credentials, $remember)) {
					if ($udetail->status == 'AC') {
						Session::put('globalCity', 'all');
						Session::put('globalState', 'all');
						Session::put('lastlogin', time());
						Session::put('currency', config('constants.currency'));

						$request->session()->flash('success', 'You are logged In.');
						return redirect()->route('index');
					} else {
						$this->guard()->logout();
						$request->session()->flash('error', "Your account has been deactivated.");
						return redirect()->route('index');
					}
				} else {
					$this->guard()->logout();
					$request->session()->flash('error', 'Fill correct credentials');
					return redirect()->route('index');
				}

			} else {
				$request->session()->flash('error', "You don't have permission to access this panel");
				return redirect()->route('index');
			}
		} else {
			$request->session()->flash('error', 'Email ID does not exists.');
			return redirect()->route('index');
		}
	}

	public function getPaymentGraphData(Request $request) {

		$query = Order::where('status', '!=', 'DL');

		switch ($request->mode) {
		case 'year':$query->selectRaw('sum(grand_total) as total, DATE_FORMAT(created_at, "%Y") as year,payment_method')->groupBy('year', 'payment_method')->orderBy('year', 'asc');
			break;
		case 'month':$query->selectRaw('sum(grand_total) as total, DATE_FORMAT(created_at, "%M") as month, DATE_FORMAT(created_at, "%c") as temp, payment_method')->whereRaw('DATE_FORMAT(created_at, "%Y") = ' . $request->filter_data)->groupBy('month', 'payment_method')->orderBy('temp', 'asc');
			break;
		case 'days':$dates = explode('&', $request->filter_data);
			$query->selectRaw('sum(grand_total) as total, DATE_FORMAT(created_at, "%d %b %Y") as date, payment_method,created_at')->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($dates[0])) . '"')->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($dates[1])) . '"')->groupBy('date', 'payment_method')->orderBy('created_at', 'asc');
			break;
		}

		$orders = $query->get();
		// print_r(DB::getQueryLog());die();
		echo json_encode($orders);
	}

	public function getCancelGraphData(Request $request) {

		$query = OrderReturn::where('status', '!=', 'DL');

		switch ($request->mode) {
		case 'year':$query->selectRaw('count(id) as total, DATE_FORMAT(created_at, "%Y") as year,status')->groupBy('year', 'status')->orderBy('year', 'asc');
			break;
		case 'month':$query->selectRaw('count(id) as total, DATE_FORMAT(created_at, "%M") as month, DATE_FORMAT(created_at, "%c") as temp, status')->whereRaw('DATE_FORMAT(created_at, "%Y") = ' . $request->filter_data)->groupBy('month', 'status')->orderBy('temp', 'asc');
			break;
		case 'days':$dates = explode('&', $request->filter_data);
			$query->selectRaw('count(id) as total, DATE_FORMAT(created_at, "%d %b %Y") as date, status,created_at')->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($dates[0])) . '"')->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($dates[1])) . '"')->groupBy('date', 'status')->orderBy('created_at', 'asc');
			break;
		}

		$orders = $query->get();
		// print_r(DB::getQueryLog());die();
		echo json_encode($orders);
	}

	public function getBarGraphData(Request $request) {
		$query = Order::where('status', '!=', 'DL');

		switch ($request->mode) {
		case 'year':$query->selectRaw('count(id) as total, DATE_FORMAT(created_at, "%Y") as year')->groupBy('year')->orderBy('year', 'asc');
			break;
		case 'month':$query->selectRaw('count(id) as total, DATE_FORMAT(created_at, "%M") as month, DATE_FORMAT(created_at, "%c") as temp')->whereRaw('DATE_FORMAT(created_at, "%Y") = ' . $request->filter_data)->groupBy('month')->orderBy('temp', 'asc');
			break;
		case 'days':$dates = explode('&', $request->filter_data);
			$query->selectRaw('count(id) as total, DATE_FORMAT(created_at, "%d %b %Y") as date,created_at')->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($dates[0])) . '"')->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($dates[1])) . '"')->groupBy('date')->orderBy('created_at', 'asc');
			break;
		}

		$orders = $query->get();
		// print_r(DB::getQueryLog());die();
		echo json_encode($orders);
	}

	public function sendPasswordMail(Request $request) {
		if ($request->isMethod('post')) {
			$user = User::where('email', $request->post('email'))->whereIn('status', ['AC', 'IN'])->first();
			if ($user) {
				$user->forgot_key = base64_encode($user->email);
				$user->save();
				$sender_id = Config::get('constants.MAIL_SENDER_ID');
				$data = array('resetpassword' => route('resetGet') . '?id=' . base64_encode($user->email), 'email' => $user->email, 'first_name' => $user->first_name, 'last_name' => $user->last_name);

				Notify::sendMail('emails.forgot_password', $data, 'Forgot Password');
				$request->session()->flash('success', 'Password Reset Link sent on your mail id.');
				if (isset($user->user_role[0]->role)) {
					return redirect()->route('index');
				} else {
					return redirect()->back();
				}

			} else {
				$request->session()->flash('error', 'Username does not exists.');
				return view('forgot');
			}
		}
		return view('forgot');
	}

	public function resetPassword(Request $request) {
		if ($request->isMethod('post')) {
			$user = User::where('email', $request->post('email'))->whereIn('status', ['AC', 'IN'])->first();

			if ($user) {

				if ($request->post('pass') == $request->post('confirm-pass')) {
					$user->password = bcrypt($request->post('pass'));
					$user->forgot_key = "";
					if ($user->save()) {
						$request->session()->flash('success', 'Password changed successfully.');
						return redirect()->route('index');
					} else {
						$request->session()->flash('error', 'Password not changed! Try again later.');
						return view('reset');
					}
				} else {
					$request->session()->flash('error', 'Passwords do not match.');
					return view('reset');
				}
			} else {
				$request->session()->flash('error', 'Email ID does not exists.');
				return view('reset');
			}
		}
		$id = $request->get('id');
		if (isset($id) && $id != "") {
			$user = User::where('email', base64_decode($request->get('id')))->whereIn('status', ['AC', 'IN'])->first();

			if ($user) {
				if ($user->forgot_key != "" && $user->forgot_key == base64_encode($user->email)) {
					$email = base64_decode($request->get('id'));
					$url = route('resetPost');
					return view('reset', compact('email', 'url'));
				} else {
					$request->session()->flash('error', 'Reset Link expired.');
					return view('forgot');
				}

			} else {
				$request->session()->flash('error', 'Email ID does not exists.');
				return view('forgot');
			}
		} else {
			$request->session()->flash('error', 'Email ID does not exists.');
			return view('forgot');
		}
	}

	public function resetPasswordApp(Request $request) {
		if ($request->isMethod('post')) {
			$user = User::where('email', $request->post('email'))->whereIn('status', ['AC', 'IN'])->first();

			if ($user) {

				if ($request->post('pass') == $request->post('confirm-pass')) {
					$user->password = bcrypt($request->post('pass'));
					$user->forgot_key = "";
					if ($user->save()) {
						$request->session()->flash('success', 'Password changed successfully.');
						return redirect()->route('appIndex');
					} else {
						$request->session()->flash('error', 'Password not changed! Try again later.');
						return redirect()->back();
					}
				} else {
					$request->session()->flash('error', 'Passwords do not match.');
					return redirect()->back();
				}
			} else {
				$request->session()->flash('error', 'Email ID does not exists.');
				return redirect()->back();
			}
		}
		$id = $request->get('id');
		if (isset($id) && $id != "") {
			$user = User::where('email', base64_decode($request->get('id')))->whereIn('status', ['AC', 'IN'])->first();

			if ($user) {
				if ($user->forgot_key != "" && $user->forgot_key == base64_encode($user->email)) {
					$email = base64_decode($request->get('id'));
					$url = route('resetPostApp');
					return view('reset', compact('email', 'url'));
				} else {
					$request->session()->flash('error', 'Reset Link expired.');
					return view('forgot');
				}
			} else {
				$request->session()->flash('error', 'Email ID does not exists.');
				return view('forgot');
			}
		} else {
			$request->session()->flash('error', 'Email ID does not exists.');
			return view('forgot');
		}
	}

	public function changePassword(Request $request) {
		if ($request->isMethod('post')) {
			$user = $this->guard()->user();

			if ($user) {

				if ($request->post('pass') == $request->post('confirm-pass')) {
					$user->password = bcrypt($request->post('pass'));
					$user->forgot_key = "";
					if ($user->save()) {
						$this->guard()->logout();
						$request->session()->flash('success', 'Password changed successfully. Please login again.');
						return redirect('/admin');
					} else {
						$request->session()->flash('error', 'Password not changed! Try again later.');
						return view('changepassword');
					}
				} else {
					$request->session()->flash('error', 'Passwords do not match.');
					return view('changepassword');
				}
			} else {
				$request->session()->flash('error', 'Access Denied');
				return view('changepassword');
			}
		}

		if ($this->guard()->check()) {
			$user = $this->guard()->user();
			if ($user) {
				return view('changepassword');
			} else {
				$request->session()->flash('error', 'Access Denied');
				return redirect('/admin');
			}
		} else {
			$request->session()->flash('error', 'Access Denied');
			return redirect('/admin');
		}
	}
	public function afterAppReset(Request $request) {
		return view('app_index');
	}
	public function home(Request $request) {
		return view('index');
	}

	public function logout() {
		$this->guard()->logout();
		return redirect('/admin');
	}

}
