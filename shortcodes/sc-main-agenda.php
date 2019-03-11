<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

add_shortcode( 'main-openagenda', 'openwp_main_sc' );

/**
 * Add Shortcode Main Agenda
 * @param $atts
 */
function openwp_main_sc( $atts ) {
	$atts       = shortcode_atts(
		array(
			'openwp_url'                        => '',
			'lang'                              => 'fr',
			'nb_events_per_line'                => '1',
			'nb_event'                          => '10',
			'openagenda_show_long_desc'         => 'yes',
			'openagenda_show_desc'              => 'yes',
			'openagenda_description_background' => '#ffffff',
			'openagenda_description_color'      => '#000000',
			'openagenda_date_background'        => '#ffffff',
			'openagenda_date_color'             => '#000000',
		),
		$atts,
		'main-openagenda'
	);
	$openagenda = new OpenAgendaApi\OpenAgendaApi();
	$events     = $openagenda->thfo_openwp_retrieve_data( $atts['openwp_url'], $atts['nb_event'] );
	if ( is_array( $events ) ) {
		$events = $events['events'];
	}
	if ( function_exists( 'openwp_main_agenda_render_html' ) ) {
		openwp_main_agenda_render_html( $events, $atts );
	}
	if ( false === apply_filters( 'openagenda_deactivate_css_generation', false ) ) {

		create_css_files( 'main_agenda_sc', get_the_ID(), $atts );
		wp_enqueue_style( 'main_agenda_sc-' . get_the_ID() );
	}
}
