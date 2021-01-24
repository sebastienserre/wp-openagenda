<?php


namespace WPOpenAgenda\API;

use OpenAgendaAPI\OpenAgendaApi;
use WP_Error;
use function _e;
use function esc_url;
use function get_option;
use function get_transient;
use function preg_match;
use function printf;
use function set_transient;
use function untrailingslashit;
use function wp_remote_get;
use function wp_remote_post;
use function wp_remote_retrieve_body;
use function wp_remote_retrieve_response_code;
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
		$this->api_url = 'https://api.openagenda.com/v2/';
		$this->api_key = $this->get_api_key();
		$this->api_secret = $this->get_secret_key();
		$this->agenda_uid = $this->get_agenda_uid();
		self::$instance = $this;

		add_action( 'admin_init', array( $this, 'get_agendas_list' ) );
	}

	/**
	 * @return Openagenda
	 * @author  sebastien
	 * @package wp-openagenda
	 * @since   2.2.0
	 */
	public static function instance() {
		if (self::$instance === null) {
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
	public function get_agenda_uid(){
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

		return $key;
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

	public function get_data( $args ){
	    $url = $this->api_url . $args;
		$response = wp_remote_get( $this->api_url . $args );
		if ( 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
			$body         = wp_remote_retrieve_body( $response );
			$decoded_body = json_decode( $body, true );
			return $decoded_body;
		} else {
		    return new WP_Error( 'OpenAgenda-no-data', 'Response Error');
        }

	}

	/**
	 * Retrieve access token to Openagenda data.
	 *
	 * @return string return Openagenda token.
	 */
	public function get_acces_token() {
		$transient = get_transient( 'openagenda_secret' );
		if ( empty( $transient ) ) {

			$args   = array(
				'sslverify' => false,
				'timeout'   => 15,
				'body'      => array(
					'grant_type' => 'authorization_code',
					'code'       => $this->api_secret,
				),
			);

			$ch = wp_remote_post( $this->api_url, $args );

			if ( 200 === (int) wp_remote_retrieve_response_code( $ch ) ) {
				$body         = wp_remote_retrieve_body( $ch );
				$decoded_body = json_decode( $body, true );
				return $decoded_body;
			} else {
				$this->errors[] = $ch;
			}

			$token        = $decoded_body['access_token'];
			set_transient( 'openagenda_secret', $decoded_body['access_token'], $decoded_body['expires_in'] );
		} else {
			$token = $transient;
		}

		return $token;

	}

	public function get_agendas_list(){
	    $list = $this->get_data(  "me/agendas?key=$this->api_key");
	    if ( true === $list['success'] ){
	        return $list['items'];
        }
		return new WP_Error( 'OpenAgenda-no-agenda', 'Agenda list empty');
	}

}
new Openagenda();
