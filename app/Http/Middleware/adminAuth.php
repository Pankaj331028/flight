<?php

namespace App\Http\Middleware;

use App\Model\BusRuleRef;
use App\Model\City;
use App\Model\Module;
use App\Model\ModuleAction;
use App\Model\Permission;
use App\Model\State;
use Auth;
use Closure;
use Config;
use Session;

class adminAuth {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		// dd(Auth::guard('admin')->user());
		if (Auth::guard('admin')->check()) {
			if (!isset(Auth::guard('admin')->user()->user_role) || (isset(Auth::guard('admin')->user()->user_role) && count(Auth::guard('admin')->user()->user_role) <= 0)) {

				Auth::guard('admin')->logout();
				$request->session()->flash('error', "You don't have permission to access this panel");
				return redirect()->route('index');
			}

			$access = $this->checkAccess($request);
			if (!$access) {
				$request->session()->flash('error', "You don't have permission to access this panel");
				return redirect()->route('index');
			}
			$this->setLocation();
		}
		if (Session::get('lastlogin') && (time() - Session::get('lastlogin')) > (Config::get('session.lifetime') * 60 * 1000)) {
			$request->session()->flush();
			Auth::guard('admin')->logout();
			$request->session()->flash('error', 'Your session timeout. Please login again.');
		}
		return $next($request);
	}

	public function checkAccess($request) {
		if (Auth::guard('admin')->user()->user_role[0]->role == 'admin') {
			return true;
		}
		$url = explode('admin/', url()->current());
		if (isset($url[1])) {

			$uri = explode('/', $url[1]);
			$role = Auth::guard('admin')->user()->user_role[0]->id;

			$module = Module::where('slug', $uri[0])->first();
			if (isset($module)) {

				if ((Auth::guard('admin')->user()->user_role[0]->role == 'user' && Auth::guard('admin')->user()->admin_panel_access == 'yes')) {
					if (in_array($module->slug, ['orders', 'users', 'bookings'])) {
						return true;
					} else {
						return false;
					}

				}

				$uri[1] = str_replace('bulk', '', $uri[1]);
				$action = ModuleAction::where('module_id', $module->id)->whereHas('action', function ($q) use ($uri) {
					$q->where('g_actions.name', $uri[1]);
				})->first();
				if (isset($action)) {
					$permission = Permission::where('role_id', $role)->where('module_action_id', $action->id)->where('status', 'AC')->count();
				} else {
					return true;
				}

				if ($permission > 0) {
					return true;
				} else {
					return false;
				}
			} else {
				return true;
			}
		} else {
			return true;
		}

	}

	public function setLocation() {

		$currency = BusRuleRef::where('rule_name', 'currency')->first()->rule_value;
		$country = BusRuleRef::where('rule_name', 'app_country')->first()->rule_value;
		config(['constants.currency' => $currency]);
		Session::put('currency', config('constants.currency'));
		Session::put('globalCountry', $country);

		//update config variable for countries, states and cities to be managed in complete admin panel
		$cities = [];
		$states = [];
		if (Session::get('globalState') != "" && Session::get('globalState') != "all") {
			$cities = City::whereHas('state', function ($query) {
				$query->where('g_states.name', Session::get('globalState'));
			})->where("status", "AC")->pluck('name');
		}
		if (Session::get('globalCountry') != "" && Session::get('globalCountry') != "all") {
			$states = State::whereHas('country', function ($query) {
				$query->where('g_countries.name', Session::get('globalCountry'));
			})->where("status", "AC")->pluck('name');
		}
		$configData = [
			'states' => $states,
			'cities' => $cities,
		];
		config(['statecity' => $configData]);
	}
}
