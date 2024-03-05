<?php
namespace App\Library;

use App\Model\BusRuleRef;
use App\Model\Notification;
use App\Model\User;
use FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class Notify {

	public static function sendMail($view, $data, $subject) {
		/*$sender = Notify::getBusRuleRef('sender_id');
			$mail = Mail::send($view, $data, function ($message) use ($data, $sender, $subject) {
				$message->to($data['email'], $data['first_name'] . " " . $data['last_name'])->subject($subject);
				$message->from(env('MAIL_USERNAME'), 'omrbranch');
				if (isset($data['file'])) {
					$message->attach($data['file']);
				}
		*/

		$array_data = [
			'from' => env('APP_NAME') . ' <info@omrbranch.com>',
			'to' => $data['first_name'] . " " . $data['last_name'] . ' <' . $data['email'] . '>',
			'subject' => $subject,
			'html' => view($view)->with($data),
		];

		$session = curl_init('https://api.mailgun.net/v3/omrbranch.com/messages');
		curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($session, CURLOPT_USERPWD, 'api:9de4a319092d52fa893bfa179fe05248-8d821f0c-79b22966');
		curl_setopt($session, CURLOPT_POST, true);
		curl_setopt($session, CURLOPT_POSTFIELDS, $array_data);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($session);

		if (curl_errno($session)) {
			$error_msg = curl_error($session);
			// dd($error_msg);
		}

		// dd($response);
		curl_close($session);

		if ('' != $response) {
			return json_decode($response, true);
		} else {
			return false;
		}

	}

	public static function getBusRuleRef($rule_name) {
		if ($BusRuleRef = BusRuleRef::where("rule_name", $rule_name)->first()) {
			return $BusRuleRef->rule_value;
		}
	}

	public static function sendNotification($user_id, $parent_id, $title, $description, $type) {
		$user = User::find($user_id);

		$notification = new Notification;
		$notification->receiver_id = $user_id;
		$notification->sender_id = $parent_id;
		$notification->title = $title;
		$notification->description = $description;
		$notification->notification_type = $type;
		$notification->save();

		$url = 'https://fcm.googleapis.com/fcm/send';

		if ($user_id != 1) {

			$fcmApiKey = Notify::getBusRuleRef("fcm_api_key");

			$fcmMsg = array(
				'title' => $title,
				'text' => $description,
				'type' => $type,
				'vibrate' => 1,
				"date_time" => date("Y-m-d H:i:s"),
				'message' => $description,
			);

			if ($user->device_type == "ios") {
				$fcmFields = array(
					'to' => $user->device_token,
					'priority' => 'high',
					'notification' => $fcmMsg,
					'data' => $fcmMsg,
				);
			} else {
				$fcmFields = array(
					'to' => $user->device_token,
					'priority' => 'high',
					'notification' => $fcmMsg,
					'data' => $fcmMsg,
				);
			}

			$headers = array(
				'Authorization: key=' . $fcmApiKey,
				'Content-Type: application/json',
			);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
			$result = curl_exec($ch);
			// dd($result);
			if ($result === false) {

			}
			curl_close($ch);
			return $result;
		}

	}

	public static function sendBulkNotifications($tokens, $notification) {

		$optionBuilder = new OptionsBuilder();
		$optionBuilder->setTimeToLive(60 * 20);
		$notificationBuilder = new PayloadNotificationBuilder($notification->title);
		$notificationBuilder->setBody($notification->description)
			->setSound('default');
		$dataBuilder = new PayloadDataBuilder();
		$dataBuilder->addData(['a_data' => 'my_data']);
		$option = $optionBuilder->build();
		$notification = $notificationBuilder->build();
		$data = $dataBuilder->build();
		//$tokens = MYDATABASE::pluck('fcm_token')->toArray();
		$downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
		// print_r($downstreamResponse);
		// die();
		$downstreamResponse->numberSuccess();
		$downstreamResponse->numberFailure();
		$downstreamResponse->numberModification();

		//return Array - you must remove all this tokens in your database
		$downstreamResponse->tokensToDelete();

		//return Array (key : oldToken, value : new token - you must change the token in your database )
		$downstreamResponse->tokensToModify();

		//return Array - you should try to resend the message to the tokens in the array
		$downstreamResponse->tokensToRetry();

		// return Array (key:token, value:errror) - in production you should remove from your database the tokens present in this array
		$downstreamResponse->tokensWithError();
	}

	public static function sendTestNotification() {
		$url = 'https://fcm.googleapis.com/fcm/send';

		$fcmApiKey = Notify::getBusRuleRef("fcm_api_key");

		$fcmMsg = array(
			'title' => "Title",
			'text' => "description",
			'type' => "type",
			'vibrate' => 1,
			"date_time" => date("Y-m-d H:i:s"),
			'message' => "description",
		);

		$fcmFields = array(
			'to' => 'eY39h4jCJAE:APA91bFsP30c7SNAEDd5_gQx49jgMsro_0dxTC9VYqORi_ASXNtf8pNvYqp2UyDEpYT3dsABUTqFDCLqBYJpFY63S0IXfOX0XN2_UANywvgIXmXWWKLlYjJN960yUwcbXm50athicMWe',
			'priority' => 'high',
			'data' => $fcmMsg,
		);

		$headers = array(
			'Authorization: key=' . $fcmApiKey,
			'Content-Type: application/json',
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
		$result = curl_exec($ch);

		if ($result === false) {

		}
		curl_close($ch);
		print_r($result);

	}

}
