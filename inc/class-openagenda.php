<?php


//namespace Openagenda\OA;


use ThfoIntranet\acf\acf;
use function add_action;
use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt_array;
use function function_exists;
use function get_transient;
use function is_wp_error;
use function set_transient;
use const CURL_HTTP_VERSION_1_1;
use const CURLOPT_CUSTOMREQUEST;
use const CURLOPT_ENCODING;
use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_HTTP_VERSION;
use const CURLOPT_MAXREDIRS;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_TIMEOUT;
use const CURLOPT_URL;

class Openagenda {

	/**
	 * OpenAgenda API URL
	 */
	public $api_url;

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
		self::$instance     = $this;
		$this->api_url      = 'https://api.openagenda.com/v2/';
		$this->api_key      = $this->get_api_key();
		$this->api_secret   = $this->get_secret_key();
		$this->agenda_uid   = $this->get_agenda_uid();
		$this->access_token = $this->get_acces_token();

		add_action( 'admin_init', array( $this, 'get_agendas_list' ) );
	}

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

	public function get_acces_token() {
		$transient = get_transient( 'openagenda_secret' );

		if ( empty( $transient ) ) {
			$url     = $this->api_url . 'requestAccessToken';
			$payload = array(
				'code'       => $this->api_secret,
				'grant_type' => 'authorization_code',
			);

			$decoded_body = $this->oa_remote_post( $url, $payload );

			if ( ! empty( $decoded_body ) ) {
				$token  = $decoded_body[ 'access_token' ];
				$expire = $decoded_body['expires_in'] - 1;
				set_transient( 'openagenda_secret', $token, $expire );
			}
		} else {
			$token = $transient;
		}

		return $token;

	}

	public function oa_remote_post( $url, $payload ) {
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

		/*$response = wp_remote_post( $url,
			array(
				'headers' => array(
					'access-token' => $this->access_token,
					'nonce'        => wp_rand(),
				),
				'body'    => $payload,
			),
		);*/



		if ( ! empty( $response ) ) {
			$decoded_body = json_decode( $response, true );
			return $decoded_body;
		}
	}

	public function get_agendas_list() {
		if ( get_transient( 'oa_agenda_list' ) ) {
			return get_transient( 'oa_agenda_list' );
		}
		$list = $this->get_data( "me/agendas?key=$this->api_key" );
		if ( is_wp_error( $list ) ) {
			$list = array();

			return $list;
		}
		if ( $list['success'] ) {
			set_transient( 'oa_agenda_list', $list['items'], 3600 );
			return $list['items'];
		}

	}

	public function get_data( $args ) {
		$response = wp_remote_get( $this->api_url . $args, array(
			'headers' => array(
				'access-token' => $this->access_token,
				'nonce' => wp_rand(),
			)
		));
		if ( 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
			$body         = wp_remote_retrieve_body( $response );
			$decoded_body = json_decode( $body, true );

			return $decoded_body;
		} else {
			return new WP_Error( 'OpenAgenda-no-data', 'Response Error' );
		}

	}


}

add_action( 'plugins_loaded', 'oa', 20 );
function oa(){
	//$openagenda = new Openagenda();
	$GLOBALS['OpenAgenda'] = new Openagenda();
}

function OpenAgenda(){
	return $GLOBALS['OpenAgenda'];
}