<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Class Openagenda_Search
 */
class Openagenda_Search {
	/**
	 * Openagenda_Search constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'openagenda_search_init' ) );
		add_shortcode( 'vc_openagenda_search', array( $this, 'openagenda_seach_sc' ) );
	}

	/**
	 * Initialize Visual Composer element
	 */
	public function openagenda_search_init() {
		vc_map(
			array(
				'name'        => __( 'OpenAgenda Search', 'wp-openagenda' ),
				'base'        => 'vc_openagenda_search',
				'description' => __( 'Display your openAgenda searchbar in WordPress', 'wp-openagenda' ),
				'category'    => __( 'Openagenda', 'wp-openagenda' ),
				'icon'        => THFO_OPENWP_PLUGIN_URL . '/assets/img/icon.jpg',
				'params'      => array(
					array(
						'type'        => 'textfield',
						'holder'      => 'h3',
						'class'       => 'title-class',
						'heading'     => __( 'Title', 'wp-openagenda' ),
						'param_name'  => 'agenda_title',
						'admin_label' => false,
						'weight'      => 0,
						'group'       => __( 'Settings', 'wp-openagenda' ),
					),
					array(
						'type'        => 'textfield',
						'holder'      => 'h3',
						'class'       => 'title-class',
						'heading'     => __( 'Agenda URL', 'wp-openagenda' ),
						'param_name'  => 'agenda_url',
						'value'       => __( 'my-agenda-url', 'wp-openagenda' ),
						'admin_label' => false,
						'weight'      => 0,
						'group'       => __( 'Settings', 'wp-openagenda' ),
					),
					array(
						'type'        => 'dropdown',
						'holder'      => 'h3',
						'class'       => 'title-class',
						'heading'     => __( 'Agenda title heading', 'wp-openagenda' ),
						'param_name'  => 'agenda_heading',
						'value'       => array(
							'H2' => 'h2',
							'H3' => 'h3',
							'H4' => 'h4',
							'H5' => 'h5',
							'H6' => 'h6',
						),
						'admin_label' => false,
						'weight'      => 0,
						'group'       => __( 'Settings', 'wp-openagenda' ),
					),
					array(
						'type'        => 'textfield',
						'holder'      => 'h3',
						'class'       => 'title-class',
						'heading'     => __( 'Agenda language', 'wp-openagenda' ),
						'param_name'  => 'agenda_lang',
						'value'       => __( 'fr', 'wp-openagenda' ),
						'admin_label' => false,
						'weight'      => 0,
						'group'       => __( 'Settings', 'wp-openagenda' ),
					),
					array(
						'type'        => 'checkbox',
						'holder'      => 'p',
						'class'       => 'title-class',
						'heading'     => __( 'Check the searchg field to show.', 'wp-openagenda' ),
						'param_name'  => 'search_criteria',
						'admin_label' => false,
						'value'       => array(
							__( 'Date', 'wp-openagenda' )         => 'date',
							__( 'Tags', 'wp-openagenda' )         => 'tag',
							__( 'Category', 'wp-openagenda' )     => 'category',
							__( 'Search field', 'wp-openagenda' ) => 'search',
							__( 'Venue', 'wp-openagenda' )        => 'venue',

						),
						'weight'      => 0,
						'group'       => __( 'Settings', 'wp-openagenda' ),
					),
				),
			)
		);
	}

	public function openagenda_seach_sc( $atts ) {

		$atts = shortcode_atts( array(
			'agenda_url'      => '',
			'search_criteria' => '',
			'agenda_lang'     => 'fr',
			'agenda_title'    => '',
			'agenda_heading'  => 'h2',
		), $atts, 'vc_openagenda_search' );
		Openagenda_search_shortcode::openagenda_search( $atts );
	}

}

new Openagenda_Search();

