<?php
namespace App\Library;

use App\Model\Booking;
use App\Model\Module;
use App\Model\ModuleAction;
use App\Model\Permission;
use App\Model\Role;
use App\Model\User;
use Auth;
use Config;
use DB;

class Helper {
	public static function generateNumber($table, $column) {
		$length = 5;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return Config::get('constants.UID.' . $table) . $randomString;
		// DB::table($table)->where($column, $uid)->count();
		// do {
		//     $random = mt_rand(1000, 9999);
		//     $uid = Config::get('constants.UID.' . $table) . $random;
		//     $exists = DB::table($table)->where($column, $uid)->count();
		// } while ($exists > 0);
		// return $uid;
	}
	public static function generateReferralCode() {
		do {
			$letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$code = substr($letters, mt_rand(0, 24), 2) . mt_rand(1000, 9999) . substr($letters, mt_rand(0, 23), 3) . mt_rand(10, 99);
			$exists = User::where('referral_code', $code)->count();
		} while ($exists > 0);
		return $code;
	}
	public static function generateBookingCode() {
		do {
			$letters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

			$length = 5;
			$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$numbers = '0123456789';
			$charactersLength = strlen($characters);
			$code = '';

			for ($i = 0; $i < $length; $i++) {
				$code .= $characters[rand(0, $charactersLength - 1)];
			}
			for ($i = 0; $i < $length; $i++) {
				$code .= $numbers[rand(0, strlen($numbers) - 1)];
			}

			$exists = Booking::where('booking_code', $code)->count();
		} while ($exists > 0);
		return $code;
	}
	public static function generateApiKey() {
		do {
			$letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$code = substr($letters, mt_rand(0, 24), 5) . mt_rand(1000, 9999) . strtolower(substr($letters, mt_rand(0, 23), 3)) . mt_rand(10000, 99999);
			$exists = User::where('api_key', $code)->count();
		} while ($exists > 0);
		return $code;
	}
	public static function generateNumberRole($role) {
		do {
			$random = mt_rand(1000, 9999);
			$uid = strtoupper(substr($role, 0, 3)) . $random;
			$exists = DB::table('g_users')->where('user_code', $uid)->count();
		} while ($exists > 0);
		return $uid;
	}
	public static function generateNumberOrder() {
		do {
			$random = mt_rand(100000, 999999);
			// $random1 = mt_rand(1000000, 9999999);
			$uid = $random;
			$exists = DB::table('orders')->where('order_no', $uid)->count();
		} while ($exists > 0);
		return $uid;
	}
	public static function getRoleName($role) {
		$name = Role::where('role', $role)->first()->name;
		return $name;
	}
	public static function compress_image($url, $quality) {
		$info = getimagesize($url);
		if ($info['mime'] == 'image/jpeg') {
			$image = imagecreatefromjpeg($url);
			$url = str_replace('.jpeg', '.jpg', $url);
			imagejpeg($image, $url, $quality);
		} elseif ($info['mime'] == 'image/gif') {
			$image = imagecreatefromgif($url);
			imagegif($image, $url, $quality);
		} elseif ($info['mime'] == 'image/png') {
			$image = imagecreatefrompng($url);
			$quality = floor($quality / 10);
			imagepng($image, $url, $quality);
		}
		return $url;
	}
	public static function checkAccess($route) {
		if (Auth::guard('admin')->user()['user_role']) {
			if (Auth::guard('admin')->user()->user_role[0]->role == 'admin') {
				return true;
			}
			$url = explode('admin/', $route);

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
					return false;
				}

			} else {
				return true;
			}
		}
	}

	public static function get_time_ago($time) {
		$time_difference = time() - strtotime($time);
		if ($time_difference < 1) {return 'less than 1 second ago';}
		$condition = array(12 * 30 * 24 * 60 * 60 => 'year',
			30 * 24 * 60 * 60 => 'month',
			24 * 60 * 60 => 'day',
			60 * 60 => 'hour',
			60 => 'minute',
			1 => 'second',
		);
		foreach ($condition as $secs => $str) {
			$d = $time_difference / $secs;
			if ($d >= 1) {
				$t = round($d);
				return 'about ' . $t . ' ' . $str . ($t > 1 ? 's' : '') . ' ago';
			}
		}
	}
	public static function format_number($amount) {
		$decimal = (string) ($amount - floor($amount));
		$money = floor($amount);
		$length = strlen($money);
		$delimiter = '';
		$money = strrev($money);
		for ($i = 0; $i < $length; $i++) {
			if (($i == 3 || ($i > 3 && ($i - 1) % 2 == 0)) && $i != $length) {
				$delimiter .= ',';
			}
			$delimiter .= $money[$i];
		}
		$result = strrev($delimiter);
		$decimal = preg_replace("/0\./i", ".", $decimal);
		$decimal = substr($decimal, 0, 3);
		if ($decimal != '0') {
			$result = $result . $decimal;
		}
		return $result;
	}

	//get IP address
	public static function getUserIP() {
		$client = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote = $_SERVER['REMOTE_ADDR'];
		if (filter_var($client, FILTER_VALIDATE_IP)) {
			$ip = $client;
		} elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
			$ip = $forward;
		} else {
			$ip = $remote;
		}
		return $ip;
	}

	public static function getLocationFormIp() {

		return 1;

		// $result = json_decode(file_get_contents("http://ipinfo.io/" . Helper::getUserIP() . "/json"));
		// $data = [];
		// if (isset($result->loc)) {
		//     $location = explode(',', $result->loc);
		//     $data['lat'] = $location[0];
		//     $data['lng'] = $location[1];
		//     $data['city'] = $result->city;
		//     $data['region'] = $result->region;
		// }
		// echo json_encode($data);
	}

}
