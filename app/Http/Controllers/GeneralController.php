<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class GeneralController extends Controller
{

	/**
	 * GeneralController constructor.
	 */
	public function __construct()
	{
	}

	/**
	 * Get photo id url from storage.
	 *
	 * @param  int $filename
	 *
	 * @return string $url
	 */
	public function getPhotoIdUrl( $filename )
	{
		$url = "";
		if ( ! empty( $filename ) ) {
			$url = url( Config::get( 'constants.photo_id_path' ) . $filename );
		}

		return $url;
	}

	/**
	 * Get photo id url from storage.
	 *
	 * @param  int $filename
	 *
	 * @return string $url
	 */
	public function getSignatureUrl( $filename )
	{
		$url = "";
		if ( ! empty( $filename ) ) {
			$url = url( Config::get( 'constants.signature_path' ) . $filename );
		}

		return $url;
	}

	/**
	 * Convert null to string of array's element.
	 *
	 * @param  array $array
	 *
	 * @return array $array
	 */
	public function convertNullToStringArray( $array )
	{
		$array = array_map(function($item) {
			if ( $item === NULL ) {
				return '';
			}
			return $item;
		}, $array);

		return $array;
	}

	/**
	 * Get final query with bindings from array
	 *
	 * @param  array $queryArray
	 *
	 * @return string $query
	 */
	public function displayQuery($queryArray) {
		foreach ($queryArray as $item) {
			$query = $item['query'];
			$bindings = $item['bindings'];
			$searchArray = array();
			$replaceArray = array();
			foreach ($bindings as $item) {
				$searchArray[] = "/\?/";
				$replaceArray[] = "'" . $item . "'";
			}
			$query = preg_replace($searchArray, $replaceArray, $query, 1);
			echo $query . "<br>";
		}
	}
}
