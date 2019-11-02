<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\User;
use Auth;

class UserController extends Controller
{

	protected $generalController;

	/**
	 * UserController constructor.
	 */
	public function __construct()
	{
		$this->generalController = new GeneralController();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index( Request $request )
	{
		$outputArray = array();
		$itemList    = array();

		$user = new User();

		$users = $user->getUsers( $request );

		foreach ( $users as $user ){
			$user->photo_id_front = $this->generalController->getPhotoIdUrl( $user->photo_id_front );
			$user->photo_id_back  = $this->generalController->getPhotoIdUrl( $user->photo_id_back );
			array_push( $itemList, $user );
		}

		$outputArray['users'] = $itemList;

		return response()->json( $outputArray );
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store( Request $request )
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show( $id )
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit( $id )
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update( Request $request, $id )
	{
		$outputArray = array();
		$itemList    = array();
		$itemArray = array();

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
		$status  = $request->input( 'status' );
		$otpVerified  = $request->input( 'otp_verified' );
		$photoIdFrontFile     = $request->file( 'photo_id_front' );
		$photoIdBackFile      = $request->file( 'photo_id_back' );

		$user = User::find($id);

		if( $user ){

			$validator = Validator::make( $request->all(), [
				'name'           => 'required|string',
				'email' => [
					'required',
					Rule::unique('users')->ignore($user->id),
				],
				'photo_id_front' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
				'photo_id_back'  => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			] );

			if ( $validator->fails() ) {
				$itemArray['fetch_flag'] = '0';
				$itemArray['errors']     = $validator->errors();
			} else {
				$user->type = $type;
				$user->name = $name;
				$user->email = $email;
				if(! empty($password) ){
					$user->password = Hash::make( $password );
				}
				$user->phone = $phone;
				$user->address_1 = $address1;
				$user->address_2 = $address2;
				$user->city = $city;
				$user->state = $state;
				$user->country = $country;

				if ($request->hasFile('photo_id_front')) {
					$photoIdFrontFileName = time() . '_front.' . $photoIdFrontFile->getClientOriginalExtension();
					$photoIdFrontFile->storeAs( 'photo_ids', $photoIdFrontFileName, 'public' );
					$user->photo_id_front = $photoIdFrontFileName;
				}

				if ($request->hasFile('photo_id_back')) {
					$photoIdBackFileName  = time() . '_back.' . $photoIdBackFile->getClientOriginalExtension();
					$photoIdBackFile->storeAs( 'photo_ids', $photoIdBackFileName, 'public' );
					$user->photo_id_back = $photoIdBackFileName;
				}

				$user->status = $status;
				$user->otp_verified = $otpVerified;
				$user->save();

				$user->photo_id_front = $this->generalController->getPhotoIdUrl( $user->photo_id_front );
				$user->photo_id_back  = $this->generalController->getPhotoIdUrl( $user->photo_id_back );
				$itemArray = $user;
				$itemArray['fetch_flag'] = '1';
			}
		}else{
			$itemArray['fetch_flag'] = '0';
		}
		array_push($itemList, $itemArray);
		$outputArray['user'] = $itemList;
		return response()->json($outputArray);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy( $id )
	{
		$outputArray = array();
		$itemList    = array();
		$itemArray = array();

		$user = User::find($id);

		if( $user ){
			$user->userDeliveryAddresses()->delete();
			$user->token()->delete();
			$user->orders()->delete();
			$user->delete();
			$itemArray['fetch_flag'] = '1';
		}else{
			$itemArray['fetch_flag'] = '0';
		}
		array_push($itemList, $itemArray);
		$outputArray['user'] = $itemList;
		return response()->json($outputArray);
	}
}
