<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'token_android'
	];

	public $timestamps = false;
}
