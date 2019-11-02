<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\User;
use App\Token;
use Auth;

class AuthController extends Controller
{
	protected $generalController;

	/**
	 * AuthController constructor.
	 */
	public function __construct()
	{
		$this->generalController = new GeneralController();
	}

	/**
	 * Login User.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function login( Request $request )
	{
		$outputArray = array();
		$itemList    = array();
		$itemArray   = array();
		$errorFlag   = 0;

		$validator = Validator::make( $request->all(), [
			'email'    => 'required|email',
			'password' => 'required|string',
		] );

		if ( $validator->fails() ) {
			$itemArray['fetch_flag'] = '0';
			$itemArray['errors']     = $validator->errors();
		} else {
			if ( User::where( 'email', $request->get( 'email' ) )->exists() ) {
				$user = User::where( 'email', $request->get( 'email' ) )->first();
				$auth = Hash::check( $request->get( 'password' ), $user->password );
				if ( $user && $auth ) {
					$user->updateAPIToken(); //update api token
					$user['fetch_flag']     = '1';
					$user['photo_id_front'] = $this->generalController->getPhotoIdUrl( $user['photo_id_front'] );
					$user['photo_id_back']  = $this->generalController->getPhotoIdUrl( $user['photo_id_back'] );
					$itemArray              = $user;
				} else {
					$errorFlag = 1;
				}
			} else {
				$errorFlag = 1;
			}

			if ( $errorFlag == 1 ) {
				$itemArray['fetch_flag'] = '0';
				$itemArray['errors']     = [ 'login' => 'Invalid Credentials' ];
			}
		}

		array_push( $itemList, $itemArray );
		$outputArray['login'] = $itemList;

		return response()->json( $outputArray );
	}

	/**
	 * Register User.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function register( Request $request )
	{
		$outputArray = array();
		$itemList    = array();
		$itemArray   = array();

		$type     = $request->input( 'type' );
		$name     = empty( $request->input( 'name' ) ) ? "" : $request->input( 'name' );
		$email    = empty( $request->input( 'email' ) ) ? "" : $request->input( 'email' );
		$password = empty( $request->input( 'password' ) ) ? "" : $request->input( 'password' );
		$phone    = empty( $request->input( 'phone' ) ) ? "" : $request->input( 'phone' );
		$address1 = empty( $request->input( 'address_1' ) ) ? "" : $request->input( 'address_1' );
		$address2 = empty( $request->input( 'address_2' ) ) ? "" : $request->input( 'address_2' );
		$city     = empty( $request->input( 'city' ) ) ? "" : $request->input( 'city' );
		$state    = empty( $request->input( 'state' ) ) ? "" : $request->input( 'state' );
		$country  = empty( $request->input( 'country' ) ) ? "" : $request->input( 'country' );
		$photoIdFrontFile     = $request->file( 'photo_id_front' );
		$photoIdBackFile      = $request->file( 'photo_id_back' );

		$validator = Validator::make( $request->all(), [
			'name'           => 'required|string',
			'email'          => 'required|unique:users|email',
			'password'       => 'required|string',
			'photo_id_front' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			'photo_id_back'  => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
		] );

		if ( $validator->fails() ) {
			$itemArray['fetch_flag'] = '0';
			$itemArray['errors']     = $validator->errors();
		} else {

			$photoIdFrontFileName = "";
			if ($request->hasFile('photo_id_front')) {
				$photoIdFrontFileName = time() . '_front.' . $photoIdFrontFile->getClientOriginalExtension();
				$photoIdFrontFile->storeAs( 'photo_ids', $photoIdFrontFileName, 'public' );
			}

			$photoIdBackFileName = "";
			if ($request->hasFile('photo_id_back')) {
				$photoIdBackFileName  = time() . '_back.' . $photoIdBackFile->getClientOriginalExtension();
				$photoIdBackFile->storeAs( 'photo_ids', $photoIdBackFileName, 'public' );
			}

			$user = User::create( [
				'type'           => $type,
				'name'           => $name,
				'email'          => $email,
				'password'       => Hash::make( $password ),
				'phone'          => $phone,
				'address_1'      => $address1,
				'address_2'      => $address2,
				'city'           => $city,
				'state'          => $state,
				'country'        => $country,
				'photo_id_front' => $photoIdFrontFileName,
				'photo_id_back'  => $photoIdBackFileName,
				'api_token'      => Str::random( 60 ),
			] );

			if ( $user ) {
				$user['photo_id_front'] = $this->generalController->getPhotoIdUrl( $user['photo_id_front'] );
				$user['photo_id_back']  = $this->generalController->getPhotoIdUrl( $user['photo_id_back'] );
				$itemArray               = $user;
				$itemArray['otp'] = rand(0000,9999);
				$itemArray['fetch_flag'] = '1';
			} else {
				$itemArray['fetch_flag'] = '0';
			}
		}

		array_push( $itemList, $itemArray );
		$outputArray['register'] = $itemList;

		return response()->json( $outputArray );
	}

	/**
	 * Logout User.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function logout( Request $request )
	{
		$outputArray = array();
		$itemList    = array();
		$itemArray   = array();

		$userId     = $request->input( 'user_id' );

		$validator = Validator::make( $request->all(), [
			'user_id'           => 'required',
		] );

		if ( $validator->fails() ) {
			$itemArray['fetch_flag'] = '0';
			$itemArray['errors']     = $validator->errors();
		} else {
			Token::where('user_id', '=', $userId)->delete();

			$user = User::find($userId);
			$user->updateAPIToken(); //update api token
			$user->save();
			$itemArray['fetch_flag'] = '1';
		}

		array_push( $itemList, $itemArray );
		$outputArray['logout'] = $itemList;

		return response()->json( $outputArray );
	}

	/**
	 * Set user OTP verified
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function setOTPVerified( Request $request )
	{
		$outputArray = array();
		$itemList    = array();
		$itemArray   = array();

		$userId     = $request->input( 'user_id' );
		$otpVerified     = $request->input( 'otp_verified' );

		$validator = Validator::make( $request->all(), [
			'user_id'           => 'required',
			'otp_verified'           => 'required',
		] );

		if ( $validator->fails() ) {
			$itemArray['fetch_flag'] = '0';
			$itemArray['errors']     = $validator->errors();
		} else {

			$user = User::find($userId);
			$user->otp_verified = $otpVerified;
			$user->save();
			$itemArray['fetch_flag'] = '1';
		}

		array_push( $itemList, $itemArray );
		$outputArray['logout'] = $itemList;

		return response()->json( $outputArray );
	}
}
