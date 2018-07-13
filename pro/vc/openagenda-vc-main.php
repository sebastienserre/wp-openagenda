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
			'name'        => __( 'Main Openagenda', 'wp-openagenda-pro' ),
			'base'        => 'openagenda-main',
			'description' => __( 'Display Openagenda Script', 'wp-openagenda-pro' ),
			'category'    => __( 'OpenAgenda', 'wp-openagenda-pro' ),
			'icon'        => THFO_OPENWP_PLUGIN_URL . '/assets/img/icon.jpg',
			'params'      => array(
				array(
					'type'        => 'textfield',
					'holder'      => 'h3',
					'class'       => 'title-class',
					'heading'     => __( 'Title', 'wp-openagenda-pro' ),
					'param_name'  => 'title',
					'value'       => __( 'Title', 'wp-openagenda-pro' ),
					'description' => __( 'Add a word between % to add a different style. Only 1 allowed', 'wp-openagenda-pro' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda-pro' ),
				),
				array(
					'type'        => 'textfield',
					'holder'      => 'a',
					'class'       => 'url-class',
					'heading'     => __( 'OpenAgenda URL', 'wp-openagenda' ),
					'param_name'  => 'agenda_url',
					'value'       => esc_url( site_url() ),
					'description' => __( 'URL to the OpenAgenda Agenda', 'wp-openagenda-pro' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda-pro' ),
				),

				array(
					'type'        => 'textfield',
					'holder'      => 'a',
					'class'       => 'url-class',
					'heading'     => __( 'Number of events:', 'wp-openagenda' ),
					'param_name'  => 'agenda_nb',
					'value'       => 10,
					'description' => __( 'Number of events to display', 'wp-openagenda-pro' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda-pro' ),
				),

				array(
					'type'        => 'dropdown',
					'holder'      => 'p',
					'class'       => 'title-class',
					'heading'     => __( 'OpenAgenda Widget to display:', 'wp-openagenda' ),
					'param_name'  => 'openagenda_type',
					'value'       => array(
						__( 'Please select an OpenAgenda Widget', 'wp-openagenda-pro' ) => 'nothing',
						__( 'General', 'wp-openagenda-pro' )                            => 'general',
						__( 'Map', 'wp-openagenda-pro' )                                => 'map',
						__( 'Search', 'wp-openagenda-pro' )                             => 'search',
						__( 'Categories', 'wp-openagenda-pro' )                         => 'categories',
						__( 'Tags', 'wp-openagenda-pro' )                               => 'tags',
						__( 'Calendrier', 'wp-openagenda-pro' )                         => 'calendrier',
						__( 'Preview Widget', 'wp-openagenda-pro' )                     => 'preview',
					),
					'description' => __( 'Select the widget to display', 'wp-openagenda-pro' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda-pro' ),
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
	$atts = shortcode_atts( array(
		'agenda_url'      => '',
		'title'           => '',
		'openagenda_type' => 'nothing',
		'agenda_nb' => 10,
		),
		$atts, 'openagenda-main'
	);


	$key = get_option( 'openagenda_api' );
	if ( ! empty( $key ) ) {
		$openwp = new OpenAgendaApi();
		$uid    = $openwp->openwp_get_uid( $atts['agenda_url'] );
	}

	if ( $uid ) {

		$embed = $openwp->openwp_get_embed( $uid, $key );

		$main = new OpenAgendaApi();
		echo $main->openwp_main_widget_html__premium_only( $embed, $uid, $atts );
	} else {
		return '<p>' . $warning . '</p>';
	}
}

add_shortcode( 'openagenda-main', 'openwp_vc_openagenda_main' );