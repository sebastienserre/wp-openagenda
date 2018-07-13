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
		add_action( 'wp_enqueue_scripts', array( $this, 'openwp_slider_register_scripts' ) );
		add_shortcode( 'openwp-slider', array( $this, 'openwp_slider_vc_html' ) );
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
					'type'        => 'textfield',
					'holder'      => 'h3',
					'class'       => 'title-class',
					'heading'     => __( 'Number of Events', 'wp-openagenda-pro' ),
					'param_name'  => 'number',
					'value'       => 10,
					'description' => __( 'Choose the number of events to show', 'wp-openagenda-pro' ),
					'admin_label' => false,
					'weight'      => 0,
					'group'       => __( 'Settings', 'wp-openagenda-pro' ),
				),

				array(
					'type'        => 'vc_link',
					'holder'      => 'a',
					'class'       => 'read-more-link',
					'heading'     => __( 'URL to Agenda', 'wp-openagenda-pro' ),
					'param_name'  => 'agenda_url_intern',
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
		wp_enqueue_script( 'psliderjs' );
		wp_enqueue_style( 'slickcss' );
		wp_enqueue_style( 'slickthemecss' );
		//wp_enqueue_style( 'pslidercss' );


		$openwp = new OpenAgendaApi();
		$datas  = $openwp->thfo_openwp_retrieve_data( $atts['agenda_url'], $atts['number'] );

		$car_exist = strpos( $atts['title'], '%' );

		if ( $car_exist ) {
			$title = explode( '%', $atts['title'] );

			$title = $title[0] . '<span class="bloc-openagenda__title" >' . $title[1] . '</span> ' . $title[2];
		} else {
			$title = $atts['title'];
		}

		$html = '<div class="bloc-openagenda"><h2 class="bloc-openagenda__title openagenda" style="color:' . $atts['agenda_title_color'] . ';">' . $title . '</h2>';

		if ( 0 === $datas['total'] ) {

			$html .= '<p>' . __( 'Sorry, no events found', 'wp-openagenda-pro' ) . '</p>';
		} else {

			$html .= '<div class="bloc-openagenda__slider">';

			foreach ( $datas as $event ) {
				foreach ( $event as $ev ) {

					if ( empty( $atts['agenda_url_intern'] ) ) {
						$url = $atts['agenda_url'] . '?oaq%5Buids%5D%5B0%5D=' . $ev['uid'];
					} else {
						$url = $atts['agenda_url_interne'] . '?oaq%5Buids%5D%5B0%5D=' . $ev['uid'];
					}
					$date = $ev['range']['fr'];

					if ( strlen( $ev['title']['fr'] ) > '59' ) {
						$ev['title']['fr'] = substr( $ev['title']['fr'], 0, 59 ) . '...';
					}

					$html .= '<div class="bloc-openagenda__slide" >';
					$html .= '<a class="bloc-openagenda__link" href="' . $url . '" style="color:' . $atts['agenda_date_text_color'] . ';" >';
					$html .= '<div class="bloc-openagenda__pic">';
					$html .= '<img src="' . $ev['image'] . '"  />';
					$html .= '</div>';

					$html .= '<div class="bloc-openagenda__date" style="background: ' . $atts['agenda_date_color'] . '; color:' . $atts['agenda_date_text_color'] . ';">';
					$html .= '<div class="bloc-openagenda__box-title bloc-openagenda__box-title--uppercase" style=" color:' . $atts['agenda_date_text_color'] . ';"> ' . $date . '</div>';

					if ( 'true' === $atts['agenda_lieu'] ) {
						$html .= '<p class="bloc-openagenda__lieu">' . $ev['locationName'] . '</p>';
					}

					$html .= '</div>';
					$html .= '</a>';
					$html .= '</div>';

				}
			}
			$html .= '</div><div class="arrows-bottom"></div></div><a href="' . $atts['agenda_url'] . '">' . $atts['agenda_url_text'] . '</a>';


		}

		return $html;

	}

	public function openwp_slider_register_scripts() {
		wp_register_script( 'slickjs', THFO_OPENWP_PLUGIN_URL . 'assets/slick/slick.min.js', array( 'jquery' ) );
		wp_register_script( 'psliderjs', THFO_OPENWP_PLUGIN_URL . 'assets/js/openagenda_slick.js', array(
			'jquery',
			'slickjs',
		) );
		wp_register_style( 'slickcss', THFO_OPENWP_PLUGIN_URL . 'assets/slick/slick.css' );
		wp_register_style( 'slickthemecss', THFO_OPENWP_PLUGIN_URL . 'assets/slick/slick-theme.css' );
		//wp_register_style( 'pslidercss', THFO_OPENWP_PLUGIN_URL . 'assets/css/p2p5-pslider.css' );
	}
}

new Openagenda_Slider();
