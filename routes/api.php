<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware( 'auth:api' )->get( '/user', function ( Request $request ) {
	return $request->user();
} );

Route::any( 'login', 'Api\AuthController@login' )->name( 'login' );
Route::any( 'register', 'Api\AuthController@register' )->name( 'register' );

Route::middleware( 'auth:api' )->group( function () {
	Route::any( 'logout', 'Api\AuthController@logout' )->name( 'logout' );
	Route::any( 'otp_verified', 'Api\AuthController@setOTPVerified' )->name( 'otp_verified' );
	Route::resource('users', 'Api\UserController');
	Route::resource('user_delivery_addresses', 'Api\UserDeliveryAddressController');
	Route::resource('orders', 'Api\OrderController');
	Route::any( 'settings', 'Api\SettingController@index' )->name( 'settings.index' );
	Route::any( 'settings/update', 'Api\SettingController@update' )->name( 'settings.update' );

	Route::any('tokens/android/save', 'Api\NotificationController@saveAndroidToken');
	Route::any('push_notification/send', 'Api\NotificationController@sendPushNotification');
} );