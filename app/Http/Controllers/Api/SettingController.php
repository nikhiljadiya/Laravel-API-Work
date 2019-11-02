<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Setting;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{

	/**
	 * SettingController constructor.
	 */
	public function __construct()
	{
	}

	/**
	 * Display settings.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$outputArray = array();
		$itemList    = array();

		$settings = Setting::first();
		$itemArray = $settings;
		array_push($itemList, $itemArray);
		$outputArray['settings'] = $itemList;
		return response()->json($outputArray);
	}

	/**
	 * Update settings
	 */
	public function update( Request $request )
	{
		$outputArray = array();
		$itemList    = array();

		$commission     = $request->input( 'commission' );
		$delivery_charge     = $request->input( 'delivery_charge' );

		$validator = Validator::make( $request->all(), [
			'commission'    => 'required',
			'delivery_charge' => 'required',
		] );

		if ( $validator->fails() ) {
			$itemArray['fetch_flag'] = '0';
			$itemArray['errors']     = $validator->errors();
		} else {
			$result = Setting::updateOrInsert(['id' => 1], [
				'commission' => $commission,
				'delivery_charge' => $delivery_charge,
			]);

			$settings = array();
			if($result){
				$settings = Setting::first();
				$settings['fetch_flag'] = '1';
			}else{
				$settings['fetch_flag'] = '0';
			}
			$itemArray = $settings;
		}

		array_push($itemList, $itemArray);
		$outputArray['settings'] = $itemList;
		return response()->json($outputArray);

	}
}
