<?php

namespace App\Providers;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\MyController;
use App\Model\BusRuleRef;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Session as FacadesSession;

class AppServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	// public function __construct(Request $request) {
	//     $this->function = (new MyController($request));
	// }

	public function boot(Request $request) {
		// URL::forceScheme('https');
		// Share a common variable with all views via Composer:
		view()->composer('*', function ($view) use ($request) {
			$api = new MyController();

			$currency = BusRuleRef::where('rule_name', 'currency')->first()->rule_value;

			if (FacadesSession::get('user') && FacadesSession::get('user') != null && $request->segment(1) != 'admin') {
				$auth_user = \App\model\User::find(FacadesSession::get('user')->id);
				$access = [];

				if ($auth_user->travel_access == 'yes') {
					$access[] = 'travel';
				}
				if ($auth_user->flight_access == 'yes') {
					$access[] = 'flight';
				}
				if ($auth_user->travel_with_api_access == 'yes') {
					$access[] = 'travel_api';
				}
				if ($auth_user->grocery_front_access == 'yes') {
					$access[] = 'grocery';
				}
				if ($auth_user->sample_api_access == 'yes') {
					$access[] = 'sample_api';
				}
				if ($auth_user->database_access == 'yes') {
					$access[] = 'database';
				}
				if ($auth_user->admin_panel_access == 'yes') {
					$access[] = 'admin_panel';
				}
				FacadesSession::put('access', $access);

				$cart_count = $api->getCartCount($auth_user['id']);
				$unreadnotification = $auth_user['notifications'] ? count($auth_user->notifications) : '';
				$user_id = $auth_user->id;
				$request = new Request();
				$api_controller = new ApiController($request);
				$requestData = $request->merge(['user_id' => $user_id]); //converting into request for api
				$items = json_decode($api_controller->getCartItems($requestData));
				$usercartsitem = isset($items->data) ? $items->data : [];

				// $usercartsitem = $items->data;

				if ($unreadnotification == 0) {
					$unreadnotification = '';
				}
			} else {
				$auth_user = '';
				$cart_count = 0;
				$unreadnotification = '';
				$auth_user = '';
				$user_id = '';
				$usercartsitem = [];
			}
			$view->with(['cart_count' => $cart_count, 'auth_user' => $auth_user, 'currency' => $currency, 'unreadnotification' => $unreadnotification, 'usercartsitem' => $usercartsitem]);
		});
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->app->register(\L5Swagger\L5SwaggerServiceProvider::class);
	}
}
