<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/run-cmd', function () {
	//Artisan::call('config:clear');
	//Artisan::call('cache:clear');
	//Artisan::call('view:clear');
	//Artisan::call('route:clear');
	//Artisan::call('migrate');
	//dd(Artisan::output());
});