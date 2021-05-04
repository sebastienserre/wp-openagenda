<?php


namespace WPOpenAgenda\Nominatim;


use WPOpenAgenda\API\Openagenda;
use function delete_post_meta;
use function function_exists;
use function get_post_meta;
use function get_transient;
use function rawurlencode;
use function set_transient;
use function update_post_meta;
use function var_dump;
use function wp_remote_get;
use function wp_remote_retrieve_body;
use function wp_remote_retrieve_response_code;
use const DAY_IN_SECONDS;

class Nominatim {

	public static $instance;

	public function __construct(){
		self::$instance   = $this;
	}

	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function get_lat_lng( $id, $address ) {
		$address = rawurlencode( $address );
		$coord   = get_transient( 'geocode_' . $address );
		if ( false === $coord  ) {
			$url  = 'http://nominatim.openstreetmap.org/?format=json&addressdetails=1&q=' . $address . '&format=json&limit=1';
			$json = wp_remote_get( $url );
			if ( 200 === (int) wp_remote_retrieve_response_code( $json ) ) {
				$body = wp_remote_retrieve_body( $json );
				$json = json_decode( $body, true );
			}
			if ( ! empty( $json[0]['lat'] ) || ! empty( $json[0]['lon'] ) ) {
				$coord['lat']  = $json[0]['lat'];
				$coord['long'] = $json[0]['lon'];

				set_transient( 'geocode_' . $address, $coord, DAY_IN_SECONDS * 30 );
				delete_post_meta( $id, 'geocode_error' );
			} else {
				$error_msg = __('Address not found - We\'re using zipcode and city', 'wp-openagenda' );
				update_post_meta( $id, 'geocode_error', $error_msg );
				$zip     = get_post_meta( $id, '_VenueZip', true );
				$city    = get_post_meta( $id, '_VenueCity', true );
				$address = rawurlencode($zip . ' ' . $city );
				$url     = 'http://nominatim.openstreetmap.org/?format=json&addressdetails=1&q=' . $address . '&format=json&limit=1';
				$json    = wp_remote_get( $url );
				if ( 200 === (int) wp_remote_retrieve_response_code( $json ) ) {
					$body = wp_remote_retrieve_body( $json );
					$json = json_decode( $body, true );
					if ( ! empty( $json[0]['lat'] ) || ! empty( $json[0]['lon'] ) ) {
						$coord['lat']  = $json[0]['lat'];
						$coord['long'] = $json[0]['lon'];

						set_transient( 'geocode_' . $address, $coord, DAY_IN_SECONDS * 30 );
					}
				}
			}
		}
		return $coord;
	}

	public function get_country_code( $lat, $lng ){
		$url = "https://nominatim.openstreetmap.org/reverse?lat=$lat&lon=$lng&format=json&addressdetails=3";
		$json = wp_remote_get( $url );
		if ( 200 === (int) wp_remote_retrieve_response_code( $json ) ) {
			$body = wp_remote_retrieve_body( $json );
			$json = json_decode( $body, true );
		}
		return $json['address']['country_code'];
	}
}

new Nominatim();

if ( ! function_exists( 'nominatim' ) ) {
	function nominatim() {
		return Nominatim::$instance;
	}
}