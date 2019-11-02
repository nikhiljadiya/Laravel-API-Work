<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\GeneralController;

class UserDeliveryAddress extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'name',
		'phone',
		'address_1',
		'address_2',
		'city',
		'state',
		'country',
	];
}
