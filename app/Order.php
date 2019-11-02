<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\GeneralController;

class Order extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'delivery_man_id',
		'user_delivery_address_id',
		'order_date',
		'delivery_date',
		'status',
		'amount',
		'delivery_man_commission',
		'paid_date',
		'signature',
		'remarks',
	];

	/**
	 * Get the user associated with the order.
	 */
	public function user()
	{
		return $this->belongsTo( 'App\User' );
	}

	/**
	 * Get the user associated with the order.
	 */
	public function deliveryMan()
	{
		return $this->belongsTo( 'App\User', 'delivery_man_id' );
	}

	/**
	 * Get orders by apply search filters
	 */
	public function getOrders( $request )
	{
		DB::enableQueryLog();

		$order_id           = $request->input( 'order_id' );
		$user_id            = $request->input( 'user_id' );
		$delivery_man_id    = $request->input( 'delivery_man_id' );
		$status             = $request->input( 'status' );
		$from_order_date    = empty( $request->input( 'from_order_date' ) ) ? '' : $request->input( 'from_order_date' );
		$to_order_date      = empty( $request->input( 'to_order_date' ) ) ? '' : $request->input( 'to_order_date' );
		$from_delivery_date = empty( $request->input( 'from_delivery_date' ) ) ? '' : $request->input( 'from_delivery_date' );
		$to_delivery_date   = empty( $request->input( 'to_delivery_date' ) ) ? '' : $request->input( 'to_delivery_date' );

		$page   = empty( $request->input( 'page' ) ) ? 1 : $request->input( 'page' );
		$limit  = 20;
		$offset = ( $page - 1 ) * $limit;

		$orders = DB::table( 'orders' )->select( '*' )->when( $order_id != "", function ( $query ) use ( $order_id ) {
			return $query->where( 'id', '=', $order_id );
		} )->when( $user_id != "", function ( $query ) use ( $user_id ) {
			return $query->where( 'user_id', '=', $user_id );
		} )->when( $delivery_man_id != "", function ( $query ) use ( $delivery_man_id ) {
			return $query->where( 'delivery_man_id', '=', $delivery_man_id );
		} )->when( $status != "", function ( $query ) use ( $status ) {
			return $query->where( 'status', '=', $status );
		} )->when( $from_order_date && $to_order_date, function ( $query ) use ( $from_order_date, $to_order_date ) {
			return $query->whereBetween( 'order_date', [ $from_order_date, $to_order_date ] );
		} )->when( $from_delivery_date && $to_delivery_date, function ( $query ) use ( $from_delivery_date, $to_delivery_date ) {
			return $query->whereBetween( 'delivery_date', [ $from_delivery_date, $to_delivery_date ] );
		} )->orderBy( 'id', 'DESC' )->offset( $offset )->limit( $limit )->get();

		/*$generalController = new GeneralController;
		dd($generalController->displayQuery(DB::getQueryLog()));*/

		return $orders;
	}
}
