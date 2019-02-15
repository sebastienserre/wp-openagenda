<?php

namespace WPGC\BlOCKS\SLIDER;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

use function __return_null;
use Carbon_Fields\Block;
use Carbon_Fields\Field;

class OpenwpBlockSlider {
	public function __construct() {

		add_action( 'after_setup_theme', array( $this, 'slider_block_init' ) );
	}

	public function slider_block_init(){
		Block::make( __( 'OpenAgenda Slider Block', 'wp-openagenda' ) )
		     ->set_category( 'custom-category', 'Openagenda', 'calendar-alt' )
		     ->set_render_callback( array( $this, 'slider_render' ) )
		     ->add_fields(
			     array(
				     Field::make( 'text', 'title', __( 'Title :', 'wp-openagenda' ) ),
				     Field::make( 'text', 'agenda_url', __( 'Openagenda URL', 'wp-openagenda' ) ),
				     Field::make( 'text', 'lang', __( '2 letters language code', 'wp-openagenda' ) )
				          ->set_attribute( 'maxLength', 2 ),
				     Field::make( 'text', 'agenda_url_intern', __( 'Internal URL of Main Agenda Page:', 'wp-openagenda' ) ),
				     Field::make( 'checkbox', 'agenda_lieu', __( 'Display venue', 'wp-openagenda' ) )
					     ->set_option_value( 'yes'
				          ),
				     Field::make( 'color', 'agenda_title_color', __( 'Title Color', 'wp-openagenda' ) ),
				     Field::make( 'color', 'agenda_date_color', __( 'Date Color' ), 'wp-openagenda' ),
				     Field::make( 'color', 'agenda_date_text_color', __( 'Text Color', 'wp-openagenda' ) ),
			     ) );
	}

	public function slider_render( $block ){
		$slide = new \OpenagendaSliderShortcode();
		$slickjs = wp_enqueue_script( 'slickjs' );
		$slickSlider = wp_enqueue_script( 'openagendaSliderJS' );
		$slickCss = wp_enqueue_style( 'slickcss' );
		$slick_theme = wp_enqueue_style( 'slickthemecss' );

		$display_title = false;
		echo $slide->openwp_slider_html( $block );
	}
}
new OpenwpBlockSlider();
