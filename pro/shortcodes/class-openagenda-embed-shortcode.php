<?php
/**
 * Display OpenAgenda Widget in WP Shortcode.
 *
 * @package OpenagendaEmbedShortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.
/**
 * Class OpenagendaEmbedShortcode
 */
class Openagenda_Embed_Shortcode {

	/**
	 * Openagenda_Embed_Shortcode constructor.
	 */
	public function __construct() {
		add_shortcode( 'openagenda_embed', array( $this, 'openwp_pro_shortcode' ) );
	}

	/**
	 * Display Openagenda embeds with a WordPress Shortcode.
	 * [openagenda_embed url="https://openagenda.com/iledefrance" lang="fr" widget="map"]
	 * url => string with URL of OpenAgenda agenda. (required).
	 * lang => language to display (2 letters country code: en/fr...). default: en. optional.
	 * widget => Openagenda widget to display. Possible settings: general, map, search, categories, tags, calendrier, preview.
	 *
	 * @param array $atts Shortcode params.
	 *
	 * @return string
	 */
	public function openwp_pro_shortcode( $atts ) {
		$atts   = shortcode_atts( array(
			'url'    => 'test',
			'widget' => 'general',
			'lang'   => 'fr',
		), $atts, 'openagenda_embed' );
		$openwp = new OpenAgendaApi\OpenAgendaApi();
		$uid    = $openwp->openwp_get_uid( $atts['url'] );
		$key    = $openwp->thfo_openwp_get_api_key();
		$embed  = $openwp->openwp_get_embed( $uid, $key );
		ob_start();
		echo $openwp->openwp_main_widget_html__premium_only($embed, $uid, $atts);

		return ob_get_clean();
	}

}
new Openagenda_Embed_Shortcode();
