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
	}

	public function openwp_slider_init() {
		vc_map( array(
			'name'        => __( 'Slider Openagenda', 'wp-openagenda-pro' ),
			'base'        => 'openwp-slider',
			'description' => __( 'Display Slider with Openagenda events', 'wp-openagenda-pro' ),
			'category'    => __( 'OpenAgenda', 'wp-openagenda' ),
			'icon'        => THFO_OPENWP_PLUGIN_URL . '/assets/img/icon.jpg',
			'params'      => array(
				array(
					'type'        => 'textfield',
					'holder'      => 'h3',
					'class'       => 'title-class',
					'heading'     => __( 'OpenAgenda URL', 'wp-openagenda' ),
					'param_name'  => 'agenda_url',
					'value'       => __( 'my-agenda-URL', 'wp-openagenda-pro' ),
					'description' => __( 'The URL of your agenda in openagenda. For example, Openagenda URL is https://openagenda.com/my-great-calendar', 'wp-openagenda-pro' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda-pro' ),
				),

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
					'type'        => 'vc_link',
					'holder'      => 'a',
					'class'       => 'read-more-link',
					'heading'     => __( 'URL to Agenda', 'wp-openagenda-pro' ),
					'param_name'  => 'agenda_url',
					'value'       => '',
					'description' => __( 'Link to the page - Leave blank to hide', '5p2p-vc-auto-post' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda-pro' ),
				),

				array(
					'type'        => 'colorpicker',
					'holder'      => 'p',
					'class'       => 'title-class',
					'heading'     => __( 'Color to Title', 'wp-openagenda-pro' ),
					'param_name'  => 'agenda_title_color',
					'value'       => '#000000',
					'description' => __( 'Select the color for the  title part between %', 'wp-openagenda-pro' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda-pro' ),
				),

				array(
					'type'        => 'colorpicker',
					'holder'      => 'p',
					'class'       => 'title-class',
					'heading'     => __( 'Background color', 'wp-openagenda-pro' ),
					'param_name'  => 'agenda_date_color',
					'value'       => '#00bfff',
					'description' => __( 'Date Box background color', 'wp-openagenda-pro' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda-pro' ),
				),

				array(
					'type'        => 'colorpicker',
					'holder'      => 'p',
					'class'       => 'title-class',
					'heading'     => __( 'text color', 'wp-openagenda-pro' ),
					'param_name'  => 'agenda_date_text_color',
					'value'       => '#000000',
					'description' => __( 'Date Box text color', 'wp-openagenda-pro' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda-pro' ),
				),

				array(
					'type'        => 'textfield',
					'holder'      => 'p',
					'class'       => 'title-class',
					'heading'     => __( 'tag Filter', 'wp-openagenda-pro' ),
					'param_name'  => 'agenda_tag',
					'description' => __( 'Tag Filter', 'wp-openagenda-pro' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda-pro' ),
				),

				array(
					'type'        => 'textfield',
					'holder'      => 'p',
					'class'       => 'title-class',
					'heading'     => __( 'Cat Filter', 'wp-openagenda-pro' ),
					'param_name'  => 'agenda_cat',
					'description' => __( 'Cat Filter', 'wp-openagenda-pro' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda-pro' ),
				),

				array(
					'type'        => 'textfield',
					'holder'      => 'p',
					'class'       => 'title-class',
					'heading'     => __( 'search', 'wp-openagenda-pro' ),
					'param_name'  => 'agenda_search',
					'description' => __( 'search Filter', 'wp-openagenda-pro' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda-pro' ),
				),

				array(
					'type'        => 'checkbox',
					'holder'      => 'p',
					'class'       => 'title-class',
					'heading'     => __( 'Display venue', 'wp-openagenda-pro' ),
					'param_name'  => 'agenda_lieu',
					'description' => __( 'Display Venue', 'wp-openagenda-pro' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda-pro' ),
				),
			),
		) );
	}

	public function
}
new Openagenda_Slider();
