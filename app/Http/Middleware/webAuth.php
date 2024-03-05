<?php

namespace App\Http\Middleware;
use Auth;
use Closure;

class webAuth {
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
		if (!Auth::guard('front')->check()) {
			return redirect('/');
		} else {

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
				return redirect()->route('index');
			}
		}

		return $next($request);
	}
}