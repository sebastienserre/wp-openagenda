<?php
/**
 * Generate WordPress Shortcodes
 *
 * @package openagenda-shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Class OpenAgenda_Shortcode
 */
class OpenAgenda_Shortcode {

	/**
	 * OpenAgenda_Shortcode constructor.
	 */
	public function __construct() {
		add_shortcode( 'openwp_basic', array( $this, 'thfo_openwp_basic_shortcode' ) );
	}

	/**
	 * Display a basic Shortcode
	 * [openwp_basic slug='your_slug' nb=11 lang='fr']
	 * slug => string with slug of agenda. required.
	 * nb => Number of events to display. default 10. optional.
	 * lang => language to display (2 letters country code: en/fr...). default: en. optional.
	 *
	 * @param array $atts contain slug, nb and languages.
	 *
	 * @return array|mixed|object|string
	 */
	public function thfo_openwp_basic_shortcode( $atts ) {

		$atts   = shortcode_atts( array(
			'url'               => 'https://openagenda.com/cite-des-sciences',
			'nb'                => 10,
			'lang'              => 'en',
			'openwp_img'               => 'yes',
			'event-title'       => 'yes',
			'event-description' => 'yes',
		), $atts, 'openwp_basic' );
		$openwp = new OpenAgendaApi\OpenAgendaApi();


		$openwp_data = $openwp->thfo_openwp_retrieve_data( $atts['url'], $atts['nb'] );
		$lang        = $atts['lang'];

		ob_start();

		$openwp->openwp_basic_html( $openwp_data, $lang, $atts );


		return ob_get_clean();
	}

}

new OpenAgenda_Shortcode();
