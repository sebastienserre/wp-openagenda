<?php


namespace WPOpenAgenda\API;

use OpenAgendaAPI\OpenAgendaApi;
use WP_Error;
use function _e;
use function add_action;
use function array_diff;
use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function curl_setopt_array;
use function esc_attr;
use function esc_url;
use function function_exists;
use function get_field;
use function get_fields;
use function get_locale;
use function get_option;
use function get_transient;
use function is_wp_error;
use function preg_match;
use function printf;
use function rand;
use function set_transient;
use function substr;
use function untrailingslashit;
use function update_post_meta;
use function var_dump;
use function wp_remote_get;
use function wp_remote_post;
use function wp_remote_retrieve_body;
use function wp_remote_retrieve_response_code;
use function WPOpenAgenda\Nominatim\nominatim;
use const CURL_HTTP_VERSION_1_1;
use const CURLOPT_CUSTOMREQUEST;
use const CURLOPT_ENCODING;
use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_HTTP_VERSION;
use const CURLOPT_MAXREDIRS;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_TIMEOUT;
use const CURLOPT_URL;
use const PREG_OFFSET_CAPTURE;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

class Openagenda {

	/**
	 * OpenAgenda API URL
	 */
	private $api_url;

	/**
	 * OpenAgenda API Key
	 */
	private $api_key;

	/**
	 * OpenAgenda API Secret
	 */
	private $api_secret;

	/**
	 * OpenAgenda Access Token
	 */
	private $access_token;

	/**
	 * OpenAgenda Agenda UID
	 */
	public $agenda_uid;

	/**
	 * OpenAgenda Instance
	 */
	public static $instance;

	public function __construct() {
		$this->api_url    = 'https://api.openagenda.com/v2/';
		$this->api_key    = $this->get_api_key();
		$this->api_secret = $this->get_secret_key();
		$this->agenda_uid = $this->get_agenda_uid();
		self::$instance   = $this;

		add_action( 'admin_init', array( $this, 'get_agendas_list' ) );
		add_action( 'save_post_venue', array( $this, 'export_location' ), 10, 3 );
	}

	/**
	 * @return Openagenda
	 * @author  sebastien
	 * @package wp-openagenda
	 * @since   2.2.0
	 */
	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @return int
	 * @author  sebastien
	 * @package wp-openagenda
	 * @since   2.2.0
	 */
	public function get_agenda_uid() {
		return get_option( 'openagenda-wp-agenda' );
	}

	/**
	 * Get API stored in Options
	 *
	 * @return int OpenAgenda API Key
	 * @since 1.0.0
	 */
	public function get_api_key() {
		$key = get_option( 'openagenda_api' );
		if ( ! empty( $key ) ) {
			return $key;
		}

		return;
	}

	/**
	 * Retrieve Secret Key from Options
	 *
	 * @return string Secret Key from OpenAgenda
	 */
	public function get_secret_key() {

		$secret = get_option( 'openagenda_secret' );

		return $secret;
	}

	public function get_post_type() {
		return 'openagenda-events';
	}

	/**
	 * Retrieve data from OpenAgenda.com through API
	 *
	 * @param $args
	 *
	 * @return Array
	 * @author  sebastien
	 * @package wp-openagenda
	 * @since   2.2.0
	 */

	public function get_data( $args ) {
		$url      = $this->api_url . $args;
		$response = wp_remote_get( $this->api_url . $args );
		if ( 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
			$body         = wp_remote_retrieve_body( $response );
			$decoded_body = json_decode( $body, true );

			return $decoded_body;
		} else {
			return new WP_Error( 'OpenAgenda-no-data', 'Response Error' );
		}

	}


	public function get_event_lang( $event ) {
		if ( ! empty( $event['description'] ) ) {
			$site_locale = get_locale();
			$site_lang   = substr( $site_locale, 0, 2 );
			foreach ( $event['description'] as $lang => $description ) {
				$event_lang = $lang;
				if ( $lang === $site_lang ) {
					$event_lang = $lang;
				}
			}
		}

		return $event_lang;
	}

	/**
	 * Retrieve access token to Openagenda data.
	 *
	 * @return string return Openagenda token.
	 */
	public function get_acces_token() {
		$transient = get_transient( 'openagenda_secret' );

		if ( empty( $transient ) ) {
			$url  = $this->api_url . 'requestAccessToken';
			$payload = array(
				'code'       => $this->api_secret,
				'grant_type' => 'authorization_code',
			);

			$decoded_body = $this->oa_remote_post( $url, $payload);

			if ( ! empty( $decoded_body ) ) {
				$token        = $decoded_body[ access_token ];
				$expire       = $decoded_body['expires_in'] - 1;
				set_transient( 'openagenda_secret', $token, $expire );
			}
		} else {
			$token = $transient;
		}

		return $token;

	}

	public function oa_remote_post( $url, $payload ){
		$curl = curl_init();

		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL            => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING       => '',
				CURLOPT_MAXREDIRS      => 10,
				CURLOPT_TIMEOUT        => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST  => 'POST',
				CURLOPT_POSTFIELDS     => $payload,
			)
		);

		$response = curl_exec( $curl );

		curl_close( $curl );

		if ( ! empty( $response ) ) {
			$decoded_body = json_decode( $response, true );
			return $decoded_body;
		}
	}

	public function get_agendas_list() {
		$list = $this->get_data( "me/agendas?key=$this->api_key" );
		if ( is_wp_error( $list ) ) {
			$list = array();

			return $list;
		}
		if ( $list['success'] ) {
			return $list['items'];
		}

	}

	public function export_location( $post_ID, $post, $update ) {
		$location_uid = get_post_meta( $post_ID, 'oa_location_uid', true);
		$url          = $this->api_url . 'agendas/' . $this->agenda_uid . '/locations/';
		if( ! empty( $location_uid ) ){
			$url = $url . $location_uid;
		}

		$fields       = get_fields( $post_ID );
		$country = nominatim()->get_country_code( $fields['oa_loc_address']['center_lat'], $fields['oa_loc_address']['center_lng'] );
		$data         = array(
			'uid' => $location_uid,
			'name'      => esc_attr( $post->post_title ),
			'address'   => esc_attr( $fields['oa_loc_address']['address'] ),
			'latitude'  => esc_attr( $fields['oa_loc_address']['center_lat'] ),
			'longitude' => esc_attr( $fields['oa_loc_address']['center_lng'] ),
			'countryCode' => $country,

		);
		$venue = $this->export_data( $url, $data );
		if ( false !== $venue ) {
			update_post_meta( $post_ID, 'oa_location_uid', $venue );
		}
	}

	public function export_data( $url, $data ) {
		$access_token = $this->get_acces_token();
		$posted       = array(
			'access_token' => $access_token,
			'nonce'        => rand(),
			'data'         => json_encode( $data ),
		);
		$ch = curl_init();

		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $posted );

		$received_content = curl_exec( $ch );

		$decode = json_decode( $received_content, true );
		if ( true === $decode['success'] ){
			$uid = $decode['location']['uid'];
			return $uid;
		}
		return false;
	}

	public function get_locations() {
		$locations = $this->get_data( "agendas/$this->agenda_uid/locations&key=$this->api_key" );
		foreach ( $locations as $location ) {

		}
	}

}
new Openagenda();

if ( ! function_exists( 'openagenda' ) ) {
	function openagenda() {
		return openagenda::$instance;
	}
}
