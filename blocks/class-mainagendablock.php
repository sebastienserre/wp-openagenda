<?php

namespace Openagenda\MainAgendaBlock;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

use function add_action;
use function apply_filters;
use Carbon_Fields\Block;
use Carbon_Fields\Field;
use function create_css_files;
use function date_i18n;
use function fopen;
use function function_exists;
use function get_the_archive_description;
use function is_array;
use function is_string;
use function ob_get_clean;
use function ob_start;
use OpenAgendaApi;
use function strtotime;
use function wp_enqueue_script;
use function wp_register_style;

class MainAgendaBlock {
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'block_init' ) );
	}

	public function block_init() {
		Block::make( __( 'OpenAgenda Main block', 'wp-openagenda' ) )
		     ->set_category( 'custom-category', 'Openagenda', 'calendar-alt' )
		     ->set_render_callback( array( $this, 'render' ) )
		     ->add_fields(
			     array(
				     Field::make( 'text', 'openwp_url', __( 'Openagenda URL', 'wp-openagenda' ) ),
				     Field::make( 'text', 'lang', __( '2 letters language code', 'wp-openagenda' ) )
				          ->set_attribute( 'maxLength', 2 ),
				     Field::make( 'select', 'nb_events_per_line', __( 'Number of Events per line', 'wp-openagenda' ) )
				          ->set_options( array(
					          '1'  => 1,
					          '2'  => 2,
					          '3'  => 3,
					          '4'  => 4,
					          '5'  => 5,
					          '6'  => 6,
					          '7'  => 7,
					          '8'  => 8,
					          '9'  => 9,
					          '10' => 10,
				          ) ),
				     Field::make( 'select', 'nb_event', __( 'Number of Events', 'wp-openagenda' ) )
				          ->set_options( array(
						          '1'  => 1,
						          '2'  => 2,
						          '3'  => 3,
						          '4'  => 4,
						          '5'  => 5,
						          '6'  => 6,
						          '7'  => 7,
						          '8'  => 8,
						          '9'  => 9,
						          '10' => 10,
						          '15' => 15,
						          '20' => 20,
						          '30' => 30,
						          '40' => 40,
						          '50' => 50,
					          )

				          ),
				     Field::make( 'checkbox', 'openagenda_show_long_desc', __( 'Show Long Description', 'wp_openagenda' ) )
				          ->set_option_value( 'yes' ),
				     Field::make( 'checkbox', 'openagenda_show_desc', __( 'Show Description', 'wp_openagenda' ) )
				          ->set_option_value( 'yes' ),
				     Field::make( 'checkbox', 'openagenda_masonry', __( 'Display Masonry (Pro Only)', 'wp_openagenda' ) )
				          ->set_option_value( 'yes' ),
				     Field::make( 'color', 'openagenda_description_background', __( 'Description Background Color', 'wp-openagenda' ) ),
				     Field::make( 'color', 'openagenda_description_color', __( 'Description Text Color', 'wp-openagenda' ) ),
				     Field::make( 'color', 'openagenda_date_background', __( 'Date Background Color', 'wp-openagenda' ) ),
				     Field::make( 'color', 'openagenda_date_color', __( 'Date text Color', 'wp-openagenda' ) ),
			     ) );
	}

	public function render( $block ) {
		$openagenda = new OpenAgendaApi\OpenAgendaApi();
		$events     = $openagenda->thfo_openwp_retrieve_data( $block['openwp_url'], $block['nb_event'] );
		if ( is_array( $events ) ) {
			$events = $events['events'];
		}
		if ( function_exists( 'openwp_main_agenda_render_html' ) ){
			openwp_main_agenda_render_html( $events, $block );
		}
		if ( true === apply_filters( 'openagenda/deactivate/css/generation', true ) ) {
			create_css_files( 'mainagendablock', get_the_ID(), $block );
			wp_enqueue_style( 'mainagendablock' . '-' . get_the_ID() );
		}
	}
}

new MainAgendaBlock();
