<?php
/**
 * Set of methods to retrieve data from OpenAgenda
 *
 * @author: sebastienserre
 * @package Openagenda-api
 * @since 1.0.0
 */

/**
 * Retrieve Event data from OpenAgenda.com
 *
 * @package: Openagenda-api.
 */
class OpenAgendaApi {

	/**
	 * OpenAgendaApi constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'thfo_openwp_retrieve_data' ) );
	}

	/**
	 * Get API stored in Options
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function thfo_openwp_get_api_key() {
		$key = get_option( 'openagenda_api' );

		return $key;
	}

	/**
	 * Retrieve data from Openagenda
	 *
	 * @param string $slug Slug of your agenda.
	 * @param int    $nb Number of event to retrieve. Default 10.
	 *
	 * @return array|mixed|object|string
	 */
	public function thfo_openwp_retrieve_data( $slug, $nb = 10 ) {
		if ( empty( $slug ) ) {
			return '<p>' . __( 'You forgot to add a slug of agenda to retrieve', 'openagenda-wp' ) . '</p>';
		}
		if ( empty( $nb ) ) {
			$nb = 10;
		}

		if ( ! empty( $this->thfo_openwp_get_api_key() ) ) {
			$key      = $this->thfo_openwp_get_api_key();
			$response = wp_remote_get( 'https://api.openagenda.com/v1/agendas/uid/' . $slug . '?key=' . $key );
			if ( 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
				$body         = wp_remote_retrieve_body( $response );
				$decoded_body = json_decode( $body, true );
				$uid          = $decoded_body['data']['uid'];
			}
		} else {
			$warning = '<p>' . __( 'Please add an OpenAgenda API Key in Settings / OpenAgenda Settings', 'openagenda-wp' ) . '</p>';

			return $warning;
		}
		if ( $uid ) {
			$url          = 'https://openagenda.com/agendas/' . $uid . '/events.json?key=' . $key . '&limit=' . $nb;
			$response     = wp_remote_get( $url );
			$decoded_body = array();

			if ( 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
				$body         = wp_remote_retrieve_body( $response );
				$decoded_body = json_decode( $body, true );
			} else {
				$decoded_body = '<p>' . __( 'Impossible to retrieve Events Data', 'openagenda-wp' ) . '</p>';
			}
		} else {
			$decoded_body = '<p>' . __( 'Impossible to retrieve Events Data', 'openagenda-wp' ) . '</p>';
		}

		return $decoded_body;
	}

}

new OpenAgendaApi();