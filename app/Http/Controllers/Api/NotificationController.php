<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Token;
use App\User;

class NotificationController extends Controller
{

	/**
	 * NotificationController constructor.
	 */
	public function __construct()
	{
	}

	/**
	 * Save push notification token of user.
	 *
	 * @return array
	 */
	public function saveAndroidToken(Request $request) {
		$outputArray = array();
		$itemList    = array();
		$itemArray   = array();

		$userId = empty($request->input('user_id')) ? "" : $request->input('user_id');
		$token = empty($request->input('token')) ? "" : $request->input('token');

		$validator = Validator::make( $request->all(), [
			'user_id'    => 'required',
			'token' => 'required|string',
		] );

		if ( $validator->fails() ) {
			$itemArray['fetch_flag'] = '0';
			$itemArray['errors']     = $validator->errors();
		} else {
			Token::updateOrCreate([ 'user_id' => $userId ],[ 'token_android' => $token ]);
			$itemArray['fetch_flag'] = "1";
		}

		array_push($itemList, $itemArray);
		$outputArray['token'] = $itemList;
		echo json_encode($outputArray);
	}

	/**
	 * Send push notification to specific android devices by token ids.
	 *
	 * @param  array  $tokenIds
	 * @param  array  $messageArray
	 * @return array
	 */
	public function sendPushNotificationAndroid($tokenIds, $messageArray) {

		$title = "";
		$message = "";
		if (is_array($messageArray)) {
			if (array_key_exists("title", $messageArray)) {
				$title = $messageArray["title"];
			}
			if (array_key_exists("message", $messageArray)) {
				$message = $messageArray["message"];
			}
		} else {
			$message = $messageArray;
		}

		$notificationResult = array();
		$google_api_key = env('GOOGLE_API_KEY');
		if (!empty($tokenIds) && $tokenIds != NULL) {
			$url = 'https://fcm.googleapis.com/fcm/send';
			$fields = array(
				'registration_ids' => $tokenIds,
				'notification' => array('title' => $title, 'body' => $message),
				'data' => array(),
			);
			$headers = array(
				'Authorization: key=' . $google_api_key,
				'Content-Type: application/json'
			);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
			$result = curl_exec($ch);

			$status_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

			if ( $status_code == 200 ) {
				$notificationResult = true;
			}else{
				$notificationResult = false;
			}

			/*if ($result === FALSE) {
				die('CURL Failed: ' . curl_error($ch));
			}*/

			curl_close($ch);
		}
		return $notificationResult;
	}

	/**
	 * Send push notification to specific user by user id.
	 *
	 * @param  string  $user_id
	 * @param  string  $message
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function sendPushNotification( Request $request ) {
		$outputArray = array();
		$itemList    = array();
		$itemArray   = array();

		$userId = empty($request->input('user_id')) ? "" : $request->input('user_id');
		$message = empty($request->input('message')) ? "" : $request->input('message');

		$validator = Validator::make( $request->all(), [
			'user_id'    => 'required',
			'message' => 'required|string',
		] );

		if ( $validator->fails() ) {
			$itemArray['fetch_flag'] = '0';
			$itemArray['errors']     = $validator->errors();
		} else {
			$token = User::find($userId)->token->first();
			if( $token && ! empty($token->token_android)){
				$result = $this->sendPushNotificationAndroid($token->token_android, $message);
				$itemArray['fetch_flag'] = $result;
			}else{
				$itemArray['fetch_flag'] = '0';
			}
		}

		array_push($itemList, $itemArray);
		$outputArray['token'] = $itemList;
		echo json_encode($outputArray);
	}
}
