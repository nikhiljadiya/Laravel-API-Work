<?php

namespace App;

use DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Http\Controllers\GeneralController;

class User extends Authenticatable
{
	use Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'type',
		'name',
		'email',
		'password',
		'api_token',
		'phone',
		'address_1',
		'address_2',
		'city',
		'state',
		'country',
		'photo_id_front',
		'photo_id_back',
		'status',
		'otp_varified',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [];

	/**
	 * Get the orders associated with the user.
	 */
	public function orders()
	{
		return $this->hasMany( 'App\Orders' );
	}

	/**
	 * Get the push notification token associated with the user.
	 */
	public function token()
	{
		return $this->hasOne( 'App\Token' );
	}

	/**
	 * Get the addresses associated with the user.
	 */
	public function userDeliveryAddresses()
	{
		return $this->hasMany( 'App\UserDeliveryAddress' );
	}

	/**
	 * Update API Token Key
	 */
	public function updateAPIToken()
	{
		do {
			$this->api_token = Str::random( 60 );
		} while ( $this->where( 'api_token', $this->api_token )->exists() );

		$this->save();
	}

	/**
	 * Get users by apply search filters
	 */
	public function getUsers( $request )
	{
		DB::enableQueryLog();

		$user_id         = $request->input( 'user_id' );
		$type         = empty($request->input('type')) ? "" : $request->input('type');
		$name         = empty($request->input('name')) ? "" : $request->input('name');
		$status       = $request->input('status');
		$otp_verified = $request->input('otp_verified');

		$page = empty($request->input('page')) ? 1 : $request->input('page');
		$limit = 20;
		$offset = ($page - 1) * $limit;

		$users = DB::table( 'users' )->select( '*' )->when( $user_id != "", function ( $query ) use ( $user_id ) {
			return $query->where( 'id', '=', $user_id );
		} )->when( $type != "", function ( $query ) use ( $type ) {
				return $query->where( 'type', '=', $type );
			} )->when( $name, function ( $query ) use ( $name ) {
				return $query->where( 'name', 'LIKE', "%" . $name . "%" );
			} )->when( $status != "", function ( $query ) use ( $status ) {
				return $query->where( 'status', '=', $status );
			} )->when( $otp_verified != "", function ( $query ) use ( $otp_verified ) {
				return $query->where( 'otp_verified', '=', $otp_verified );
			} )->orderBy( 'id', 'DESC' )->offset($offset)->limit($limit)
		       ->get();

		/*$generalController = new GeneralController;
		dd($generalController->displayQuery(DB::getQueryLog()));*/
		return $users;
	}

}
