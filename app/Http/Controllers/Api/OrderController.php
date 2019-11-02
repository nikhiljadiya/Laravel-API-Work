<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralController;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

	protected $generalController;

	/**
	 * OrderController constructor.
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

	    $order = new Order();

	    $orders = $order->getOrders( $request );

	    foreach ( $orders as $order ){
		    $order->signature  = $this->generalController->getSignatureUrl( $order->signature );
		    array_push( $itemList, $order );
	    }

	    $outputArray['orders'] = $itemList;

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
	    $delivery_man_id     = $request->input( 'delivery_man_id' );
	    $user_delivery_address_id     = $request->input( 'user_delivery_address_id' );
	    $order_date     = empty( $request->input( 'order_date' ) ) ? "" : $request->input( 'order_date' );
	    $delivery_date     = empty( $request->input( 'delivery_date' ) ) ? "" : $request->input( 'delivery_date' );
	    $status     = $request->input( 'status' );
	    $amount     = empty( $request->input( 'amount' ) ) ? "" : $request->input( 'amount' );
	    $delivery_man_commission     = empty( $request->input( 'delivery_man_commission' ) ) ? "" : $request->input( 'delivery_man_commission' );
	    $paid_date     = empty( $request->input( 'paid_date' ) ) ? "" : $request->input( 'paid_date' );
	    $remarks     = empty( $request->input( 'remarks' ) ) ? "" : $request->input( 'remarks' );
	    $signatureFile      = $request->file( 'signature' );

	    $validator = Validator::make($request->all(), [
		    'user_id'           => 'required|integer',
		    'delivery_man_id'           => 'required|integer',
		    'user_delivery_address_id'           => 'required|integer',
		    'order_date'           => 'required|string',
		    'signature'  => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
	    ]);

	    if ( $validator->fails() ) {
		    $itemArray['fetch_flag'] = '0';
		    $itemArray['errors']     = $validator->errors();
	    } else {

		    $signatureFileName = "";
		    if ($request->hasFile('signature')) {
			    $signatureFileName  = time() . $signatureFile->getClientOriginalExtension();
			    $signatureFile->storeAs( 'signatures', $signatureFileName, 'public' );
		    }

		    $order = Order::create([
			    'user_id' => $user_id,
			    'delivery_man_id' => $delivery_man_id,
			    'user_delivery_address_id' => $user_delivery_address_id,
			    'order_date' => $order_date,
			    'delivery_date' => $delivery_date,
			    'status' => $status,
			    'amount' => $amount,
			    'delivery_man_commission' => $delivery_man_commission,
			    'paid_date' => $paid_date,
			    'signature' => $signatureFileName,
			    'remarks' => $remarks,
		    ]);

		    if( $order ){
			    $itemArray['fetch_flag'] = '1';
		    }else{
			    $itemArray['fetch_flag'] = '0';
		    }
	    }

	    array_push($itemList, $itemArray);
	    $outputArray['order'] = $itemList;
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
	    $delivery_man_id     = $request->input( 'delivery_man_id' );
	    $user_delivery_address_id     = $request->input( 'user_delivery_address_id' );
	    $order_date     = empty( $request->input( 'order_date' ) ) ? "" : $request->input( 'order_date' );
	    $delivery_date     = empty( $request->input( 'delivery_date' ) ) ? "" : $request->input( 'delivery_date' );
	    $status     = $request->input( 'status' );
	    $amount     = empty( $request->input( 'amount' ) ) ? "" : $request->input( 'amount' );
	    $delivery_man_commission     = empty( $request->input( 'delivery_man_commission' ) ) ? "" : $request->input( 'delivery_man_commission' );
	    $paid_date     = empty( $request->input( 'paid_date' ) ) ? "" : $request->input( 'paid_date' );
	    $remarks     = empty( $request->input( 'remarks' ) ) ? "" : $request->input( 'remarks' );
	    $signatureFile      = $request->file( 'signature' );

	    $order = Order::find($id);

	    if( $order ){
		    $validator = Validator::make($request->all(), [
			    'user_id'           => 'required|integer',
			    'delivery_man_id'           => 'required|integer',
			    'user_delivery_address_id'           => 'required|integer',
			    'order_date'           => 'required|string',
			    'signature'  => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
		    ]);

		    if ( $validator->fails() ) {
			    $itemArray['fetch_flag'] = '0';
			    $itemArray['errors']     = $validator->errors();
		    } else {

			    $order->user_id                  = $user_id;
			    $order->delivery_man_id          = $delivery_man_id;
			    $order->user_delivery_address_id = $user_delivery_address_id;
			    $order->order_date               = $order_date;
			    $order->delivery_date            = $delivery_date;
			    $order->status                   = $status;
			    $order->amount                   = $amount;
			    $order->delivery_man_commission  = $delivery_man_commission;
			    $order->paid_date                = $paid_date;
			    $order->remarks                  = $remarks;

			    if ($request->hasFile('signature')) {
				    $signatureFileName  = time() . $signatureFile->getClientOriginalExtension();
				    $signatureFile->storeAs( 'signatures', $signatureFileName, 'public' );
				    $order->signature = $signatureFileName;
			    }

			    $order->save();

			    if ( $order ) {
				    $itemArray['fetch_flag'] = '1';
			    } else {
				    $itemArray['fetch_flag'] = '0';
			    }
		    }
	    }else{
		    $itemArray['fetch_flag'] = '0';
	    }
	    array_push($itemList, $itemArray);
	    $outputArray['order'] = $itemList;
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

	    $order = Order::find($id);

	    if( $order ){
		    $order->delete();
		    $itemArray['fetch_flag'] = '1';
	    }else{
		    $itemArray['fetch_flag'] = '0';
	    }
	    array_push($itemList, $itemArray);
	    $outputArray['order'] = $itemList;
	    return response()->json($outputArray);
    }
}
