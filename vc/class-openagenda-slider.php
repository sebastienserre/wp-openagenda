<?php
/**
 * Add a beautifull slider.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Class Openagenda_Slider
 */
class Openagenda_Slider {

	/**
	 * Openagenda_Slider constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'openwp_slider_init' ) );
		add_shortcode( 'openwp-slider', array( $this, 'openwp_slider_vc_html' ) );
	}

	public function openwp_slider_init() {
		vc_map( array(
			'name'        => __( 'Slider Openagenda', 'wp-openagenda' ),
			'base'        => 'openwp-slider',
			'description' => __( 'Display Slider with Openagenda events', 'wp-openagenda' ),
			'category'    => __( 'OpenAgenda', 'wp-openagenda' ),
			'icon'        => THFO_OPENWP_PLUGIN_URL . '/assets/img/icon.jpg',
			'params'      => array(
				array(
					'type'        => 'textfield',
					'holder'      => 'h3',
					'class'       => 'title-class',
					'heading'     => __( 'OpenAgenda URL', 'wp-openagenda' ),
					'param_name'  => 'agenda_url',
					'value'       => __( 'my-agenda-URL', 'wp-openagenda' ),
					'description' => __( 'The URL of your agenda in openagenda. For example, Openagenda URL is https://openagenda.com/my-great-calendar', 'wp-openagenda' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda' ),
				),

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
					'holder'      => 'h3',
					'class'       => 'title-class',
					'heading'     => __( 'Number of Events', 'wp-openagenda' ),
					'param_name'  => 'number',
					'value'       => 10,
					'description' => __( 'Choose the number of events to show', 'wp-openagenda' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda' ),
				),

				array(
					'type'        => 'vc_link',
					'holder'      => 'a',
					'class'       => 'read-more-link',
					'heading'     => __( 'Internal URL of Main Agenda Page:', 'wp-openagenda' ),
					'param_name'  => 'agenda_url_intern',
					'value'       => '',
					'description' => __( 'Link to the page - Leave blank to hide', '5p2p-vc-auto-post' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda' ),
				),

				array(
					'type'        => 'colorpicker',
					'holder'      => 'p',
					'class'       => 'title-class',
					'heading'     => __( 'Color to Title', 'wp-openagenda' ),
					'param_name'  => 'agenda_title_color',
					'value'       => '#000000',
					'description' => __( 'Select the color for the  title part between %', 'wp-openagenda' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda' ),
				),

				array(
					'type'        => 'colorpicker',
					'holder'      => 'p',
					'class'       => 'title-class',
					'heading'     => __( 'Background color', 'wp-openagenda' ),
					'param_name'  => 'agenda_date_color',
					'value'       => '#00bfff',
					'description' => __( 'Date Box background color', 'wp-openagenda' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda' ),
				),

				array(
					'type'        => 'colorpicker',
					'holder'      => 'p',
					'class'       => 'title-class',
					'heading'     => __( 'text color', 'wp-openagenda' ),
					'param_name'  => 'agenda_date_text_color',
					'value'       => '#000000',
					'description' => __( 'Date Box text color', 'wp-openagenda' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda' ),
				),

				array(
					'type'        => 'checkbox',
					'holder'      => 'p',
					'class'       => 'title-class',
					'heading'     => __( 'Display venue', 'wp-openagenda' ),
					'param_name'  => 'agenda_lieu',
					'description' => __( 'Display venue', 'wp-openagenda' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda' ),
				),
			),
		) );
	}

	public function openwp_slider_vc_html( $atts ) {
		$atts = shortcode_atts(
			array(
				'agenda_url'             => '',
				'title'                  => '',
				'agenda_url_intern'      => '',
				'agenda_text'            => '',
				'agenda_title_color'     => '',
				'agenda_date_color'      => '',
				'agenda_date_text_color' => '',
				'agenda_lieu'            => '',
				'number'                 => '',
			),
			$atts
		);

		wp_enqueue_script( 'slickjs' );
		wp_enqueue_script( 'openagendaSliderJS' );
		wp_enqueue_style( 'slickcss' );
		wp_enqueue_style( 'slickthemecss' );
		$slide = new OpenagendaSliderShortcode();
		return $slide->openwp_slider_html( $atts );

	}

}

new Openagenda_Slider();
