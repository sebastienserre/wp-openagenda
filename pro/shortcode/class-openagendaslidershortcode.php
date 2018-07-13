<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

class OpenagendaSliderShortcode {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'openwp_slider_register_scripts' ) );

		add_shortcode( 'openagenda_slider', array( $this, 'openwp_slider_sc' ) );
	}

	public function openwp_slider_sc( $atts ) {
		$atts = shortcode_atts( array(
			'agenda_url'             => '',
			'title'                  => '',
			'agenda_url_intern'      => '',
			'agenda_text'            => '',
			'agenda_title_color'     => '',
			'agenda_date_color'      => '',
			'agenda_date_text_color' => '',
			'agenda_lieu'            => '',
			'number'                 => '',
		), $atts, 'openagenda_slider' );

		wp_enqueue_script( 'slickjs' );
		wp_enqueue_script( 'openagendaSliderJS' );
		wp_enqueue_style( 'slickcss' );
		wp_enqueue_style( 'slickthemecss' );

		return $this->openwp_slider_html( $atts );
	}

	public function openwp_slider_register_scripts() {
		wp_register_script( 'slickjs', THFO_OPENWP_PLUGIN_URL . 'pro/assets/slick/slick.min.js', array( 'jquery' ) );
		$openagendasliderjs = apply_filters( 'openwp_openagendasliderjs', THFO_OPENWP_PLUGIN_URL . 'pro/assets/js/openagenda_slick.js' );
		wp_register_script( 'openagendaSliderJS', $openagendasliderjs, array(
			'jquery',
			'slickjs',
		) );
		wp_register_style( 'slickcss', THFO_OPENWP_PLUGIN_URL . 'pro/assets/slick/slick.css' );
		wp_register_style( 'slickthemecss', THFO_OPENWP_PLUGIN_URL . 'pro/assets/slick/slick-theme.css' );
	}

	public function openwp_slider_html( $atts ) {
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

					$html .= '<div class="arrows-bottom"></div></div>';
					$html .= '</a>';
					$html .= '</div>';

				}
			}
			$html .= '</div></div><a href="' . $atts['agenda_url'] . '">' . $atts['agenda_url_text'] . '</a>';


		}

		return $html;
	}
}

new OpenagendaSliderShortcode();
