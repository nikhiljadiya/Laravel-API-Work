<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralController;
use App\UserDeliveryAddress;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserDeliveryAddressController extends Controller
{

	protected $generalController;

	/**
	 * UserAddressController constructor.
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
    public function index(Request $request)
    {
	    $outputArray = array();
	    $itemList    = array();

	    $user_id         = $request->input( 'user_id' );
	    $page = empty($request->input('page')) ? 1 : $request->input('page');
	    $limit = 20;
	    $offset = ($page - 1) * $limit;

	    $userAddresses = UserDeliveryAddress::where('user_id', '=', $user_id)
		                            ->orderBy('id', 'DESC')
	                                ->offset($offset)
	                                ->limit($limit)
	                                ->get()->toArray();

	    foreach ( $userAddresses as $userAddress ){

		    //$userAddress = $this->generalController->convertNullToStringArray( $userAddress );

		    array_push( $itemList, $userAddress );
	    }

	    $outputArray['user_addresses'] = $itemList;

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $outputArray = array();
        $itemList = array();
        $itemArray = array();

	    $user_id     = $request->input( 'user_id' );
	    $name     = empty( $request->input( 'name' ) ) ? "" : $request->input( 'name' );
	    $phone    = empty( $request->input( 'phone' ) ) ? "" : $request->input( 'phone' );
	    $address1 = empty( $request->input( 'address_1' ) ) ? "" : $request->input( 'address_1' );
	    $address2 = empty( $request->input( 'address_2' ) ) ? "" : $request->input( 'address_2' );
	    $city     = empty( $request->input( 'city' ) ) ? "" : $request->input( 'city' );
	    $state    = empty( $request->input( 'state' ) ) ? "" : $request->input( 'state' );
	    $country  = empty( $request->input( 'country' ) ) ? "" : $request->input( 'country' );

	    $validator = Validator::make($request->all(), [
		    'user_id'           => 'required|integer',
		    'name'           => 'required|string',
		    'address_1'           => 'required|string',
	    ]);

	    if ( $validator->fails() ) {
		    $itemArray['fetch_flag'] = '0';
		    $itemArray['errors']     = $validator->errors();
	    } else {
		    $userAddress = UserDeliveryAddress::create([
			    'user_id' => $user_id,
			    'name' => $name,
			    'phone' => $phone,
			    'address_1' => $address1,
			    'address_2' => $address2,
			    'city' => $city,
			    'state' => $state,
			    'country' => $country,
		    ]);

		    if( $userAddress ){
			    $itemArray['fetch_flag'] = '1';
		    }else{
			    $itemArray['fetch_flag'] = '0';
		    }
	    }

	    array_push($itemList, $itemArray);
	    $outputArray['user_address'] = $itemList;
	    return response()->json($outputArray);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
	    $outputArray = array();
	    $itemList = array();
	    $itemArray = array();

	    $user_id     = $request->input( 'user_id' );
	    $name     = empty( $request->input( 'name' ) ) ? "" : $request->input( 'name' );
	    $phone    = empty( $request->input( 'phone' ) ) ? "" : $request->input( 'phone' );
	    $address1 = empty( $request->input( 'address_1' ) ) ? "" : $request->input( 'address_1' );
	    $address2 = empty( $request->input( 'address_2' ) ) ? "" : $request->input( 'address_2' );
	    $city     = empty( $request->input( 'city' ) ) ? "" : $request->input( 'city' );
	    $state    = empty( $request->input( 'state' ) ) ? "" : $request->input( 'state' );
	    $country  = empty( $request->input( 'country' ) ) ? "" : $request->input( 'country' );

	    $userAddress = UserDeliveryAddress::find($id);

	    if( $userAddress ){
		    $validator = Validator::make($request->all(), [
			    'user_id'           => 'required|integer',
			    'name'           => 'required|string',
			    'address_1'           => 'required|string',
		    ]);

		    if ( $validator->fails() ) {
			    $itemArray['fetch_flag'] = '0';
			    $itemArray['errors']     = $validator->errors();
		    } else {
			    $userAddress->user_id = $user_id;
			    $userAddress->name = $name;
			    $userAddress->phone = $phone;
			    $userAddress->address_1 = $address1;
			    $userAddress->address_2 = $address2;
			    $userAddress->city = $city;
			    $userAddress->state = $state;
			    $userAddress->country = $country;
			    $userAddress->updated_at = date('Y-m-d H:i:s');
			    $userAddress->save();

			    if( $userAddress ){
				    $itemArray['fetch_flag'] = '1';
			    }else{
				    $itemArray['fetch_flag'] = '0';
			    }
		    }
	    }else{
		    $itemArray['fetch_flag'] = '0';
	    }

	    array_push($itemList, $itemArray);
	    $outputArray['user_address'] = $itemList;
	    return response()->json($outputArray);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
	    $outputArray = array();
	    $itemList    = array();
	    $itemArray = array();

	    $user = UserDeliveryAddress::find($id);

	    if( $user ){
		    $user->delete();
		    $itemArray['fetch_flag'] = '1';
	    }else{
		    $itemArray['fetch_flag'] = '0';
	    }
	    array_push($itemList, $itemArray);
	    $outputArray['user_address'] = $itemList;
	    return response()->json($outputArray);
    }
}
