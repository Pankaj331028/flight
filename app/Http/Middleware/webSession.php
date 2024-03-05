<?php

namespace App\Http\Middleware;
use App\Model\User;
use Auth;
use Closure;
use Session;
use URL;

class webSession {
	//add an array of Routes to skip login check
	//protected $except_urls = ['/', '/forgot-password', '/reset-password'];
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		if (Auth::guard('front')->check()) {
			if (isset($_COOKIE['login_session']) && Auth::guard('front')->user()->login_cookie != $_COOKIE['login_session']) {
				Auth::guard('web')->logout();
				if (Auth::guard('api')->check()) {
					Auth::guard('api')->logout();
				}
				Session::flush('user');
				setcookie("login_session", "", time() - 3600);
				// Session::flush();
				return redirect()->route('home');
			}

			if (Auth::guard('front')->user()->user_role[0]->role != 'user') {
				Auth::guard('front')->logout();
			}
			if (Session::get('user') != null) {
				if (Session::get('user')->password != User::find(Auth::guard('front')->id())->password) {
					Auth::guard('front')->logout();
				}
			}

			// check url for access and redirect if not allowed

			if (stripos(URL::current(), 'hotel') !== false || stripos(URL::current(), 'booking') !== false) {
				if (Session::has('access') && in_array('travel', Session::get('access'))) {
				} else {
					$request->session()->flash('error', 'You don\'t have access to hotel booking portal.');
					return redirect('/');
				}
			}
			if (stripos(URL::current(), 'flight') !== false) {
				if (Session::has('access') && in_array('flight', Session::get('access'))) {
				} else {
					$request->session()->flash('error', 'You don\'t have access to flight booking portal.');
					return redirect('/');
				}
			}
			if (stripos(URL::current(), 'grocery') !== false || stripos(URL::current(), 'address') !== false || stripos(URL::current(), 'order') !== false || stripos(URL::current(), 'product') !== false || stripos(URL::current(), 'cart') !== false) {
				if (Session::has('access') && in_array('grocery', Session::get('access'))) {
				} else {
					$request->session()->flash('error', 'You don\'t have access to grocery portal.');
					return redirect('/');
				}
			}
			if (stripos(URL::current(), 'sample_api') !== false) {
				if (Session::has('access') && in_array('sample_api', Session::get('access'))) {
				} else {
					$request->session()->flash('error', 'You don\'t have access to sample API.');
					return redirect('/');
				}
			}
			if (stripos(URL::current(), 'user-admin') !== false) {
				if (Session::has('access') && in_array('admin_panel', Session::get('access'))) {
				} else {
					$request->session()->flash('error', 'You don\'t have access to admin panel.');
					return redirect('/');
				}
			}
			if (stripos(URL::current(), 'user-database') !== false) {
				if (Session::has('access') && in_array('database', Session::get('access'))) {
				} else {
					$request->session()->flash('error', 'You don\'t have access to database.');
					return redirect('/');
				}
			}

		}
		return $next($request);
	}
}
