<?php
/**
 * Create a VC Element to display Openagenda Widget.
 *
 * @package openagenda_main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
function openwp_vc_openagenda_main_init() {

	vc_map( array(
			'name'        => __( 'Main Openagenda', 'wp-openagenda' ),
			'base'        => 'openagenda-main',
			'description' => __( 'Display Openagenda Script', 'wp-openagenda' ),
			'category'    => __( 'OpenAgenda', 'wp-openagenda' ),
			'icon'        => THFO_OPENWP_PLUGIN_URL . '/assets/img/icon.jpg',
			'params'      => array(
				array(
					'type'        => 'textfield',
					'holder'      => 'h3',
					'class'       => 'title-class',
					'heading'     => __( 'Title', 'wp-openagenda' ),
					'param_name'  => 'title',
					'value'       => __( 'Title', 'wp-openagenda' ),
					'description' => __( 'Add a word between % to add a different style. Only 1 allowed', 'wp-openagenda' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda' ),
				),
				array(
					'type'        => 'textfield',
					'holder'      => 'a',
					'class'       => 'url-class',
					'heading'     => __( 'OpenAgenda URL', 'wp-openagenda' ),
					'param_name'  => 'agenda_url',
					'value'       => esc_url( site_url() ),
					'description' => __( 'URL to the OpenAgenda Agenda', 'wp-openagenda' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda' ),
				),

				array(
					'type'        => 'textfield',
					'holder'      => 'a',
					'class'       => 'url-class',
					'heading'     => __( 'Number of events:', 'wp-openagenda' ),
					'param_name'  => 'agenda_nb',
					'value'       => 10,
					'description' => __( 'Number of Events', 'wp-openagenda' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda' ),
				),

				array(
					'type'        => 'dropdown',
					'holder'      => 'p',
					'class'       => 'title-class',
					'heading'     => __( 'OpenAgenda Widget to display:', 'wp-openagenda' ),
					'param_name'  => 'widget',
					'value'       => array(
						__( 'Please select an OpenAgenda Widget', 'wp-openagenda' ) => 'nothing',
						__( 'General', 'wp-openagenda' )                            => 'general',
						__( 'Map', 'wp-openagenda' )                                => 'map',
						__( 'Search', 'wp-openagenda' )                             => 'search',
						__( 'Categories', 'wp-openagenda' )                         => 'categories',
						__( 'Tags', 'wp-openagenda' )                               => 'tags',
						__( 'Calendrier', 'wp-openagenda' )                         => 'calendrier',
						__( 'Preview Widget', 'wp-openagenda' )                     => 'preview',
					),
					'description' => __( 'Select the widget to display', 'wp-openagenda' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda' ),
				),
			),
		)
	);

}

add_action( 'init', 'openwp_vc_openagenda_main_init' );

/**
 * Display the Shortcode.
 *
 * @param mixed $atts data from VC settings.
 *
 * @return string
 */
function openwp_vc_openagenda_main( $atts ) {
	$atts = shortcode_atts(
		array(
			'agenda_url' => '',
			'title'      => '',
			'widget'     => 'general',
			'agenda_nb'  => 10,
		),
		$atts, 'openagenda-main'
	);
	$key  = get_option( 'openagenda_api' );
	if ( ! empty( $key ) ) {
		$openwp = new OpenAgendaApi\OpenAgendaApi();
		$uid    = $openwp->openwp_get_uid( $atts['agenda_url'] );
	}
	if ( $uid ) {

		$embed = $openwp->openwp_get_embed( $uid, $key );
		$main  = new OpenAgendaApi\OpenAgendaApi();
		echo $main->openwp_main_widget_html__premium_only( $embed, $uid, $atts );
	} else {
		return '<p>' . $warning . '</p>';
	}
}

add_shortcode( 'openagenda-main', 'openwp_vc_openagenda_main' );
