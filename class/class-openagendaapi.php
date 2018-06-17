<?php
/**
Set of methods to retrieve data from OpenAgenda

 * @author: sebastienserre
 * @package Openagenda-api
 * @since 1.0.0
 */

/**
 * Retrieve Event data from OpenAgenda.com

 * @package: Openagenda-api.
 */
class OpenAgendaApi {

	/**
	 * OpenAgendaApi constructor

	 * @since 1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Get API stored in Options

	 * @return mixed|void
	 */
	public function thfo_openwp_get_api_key() {
		$key = get_option( 'openagenda_api' );
		return $key;
	}

}
