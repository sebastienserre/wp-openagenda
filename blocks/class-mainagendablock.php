<?php

namespace Openagenda\MainAgendaBlock;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

use function add_action;
use function apply_filters;
use Carbon_Fields\Block;
use Carbon_Fields\Field;
use function date_i18n;
use function fopen;
use function function_exists;
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
		add_action( 'wp_enqueue_scripts', array( $this, 'register_css' ) );

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
				     Field::make( 'color', 'openagenda_description_background', __( 'Description Background Color', 'wp-openagenda' ) ),
				     Field::make( 'color', 'openagenda_description_color', __( 'Description Text Color', 'wp-openagenda' ) ),
				     Field::make( 'color', 'openagenda_date_background', __( 'Date Background Color', 'wp-openagenda' ) ),
				     Field::make( 'color', 'openagenda_date_color', __( 'Date text Color', 'wp-openagenda' ) ),
			     ) );
	}

	public function render( $block ) {
		$openagenda = new OpenAgendaApi();
		$events     = $openagenda->thfo_openwp_retrieve_data( $block['openwp_url'], $block['nb_event'] );
		if ( is_array( $events ) ) {
			$events = $events['events'];
		}
		if ( function_exists( 'openwp_main_agenda_render_html' ) ){
			openwp_main_agenda_render_html( $events, $block );
		}
		if ( true === apply_filters( 'openagenda/deactivate/css/generation', true ) ) {
			self::generate_css( $block['nb_events_per_line'], $block );
			wp_enqueue_style( 'openagenda_main_block' );
		}
	}

	public static function register_css() {
		wp_register_style( 'openagenda_main_block', THFO_OPENWP_PLUGIN_URL . 'assets/css/generated.css' );
	}

	public static function generate_css( $columns, $block ) {
		$path                              = THFO_OPENWP_PLUGIN_PATH . '/assets/css/generated.css';
		$file                              = fopen( $path, 'w+' );
		$openagenda_description_background = $block['openagenda_description_background'];
		$openagenda_description_color      = $block['openagenda_description_color'];
		$openagenda_date_background        = $block['openagenda_date_background'];
		$openagenda_date_color             = $block['openagenda_date_color'];

		/**
		 * Filters colors // return the hex color code
		 */
		$openagenda_description_background = apply_filters( 'openagenda/mainBlock/description/bg', $openagenda_description_background );
		$openagenda_description_color      = apply_filters( 'openagenda/mainBlock/description/txt', $openagenda_description_color );
		$openagenda_date_background        = apply_filters( 'openagenda/mainBlock/date/bg', $openagenda_date_background );
		$openagenda_date_color             = apply_filters( 'openagenda/mainBlock/date/txt', $openagenda_date_color );

		ob_start();
		?>
		.main_openagenda {
		display: grid;
		grid-template-columns: repeat(<?php echo $columns ?>, auto);
		grid-gap: 10px 20px;
		}
		.openagenda_when {
		background: <?php echo $openagenda_date_background; ?>;
		color: <?php echo $openagenda_date_color; ?>;
		}
		.openagenda_when p {
		text-align: center;
		}
		.openagenda_event {
		background: <?php echo $openagenda_description_background  ?>;
		color: <?php echo $openagenda_description_color ?>;
		}
		.openagenda_event p,
		.openagenda_event h3{
		margin: 0;
		}

		.openagenda_description,
		.openagenda_meta{
		padding: 10px;
		}

		.openagenda_event_image {
		text-align: center;
		}
		<?php
		$css = ob_get_clean();
		fwrite( $file, $css );
		fclose( $file );
	}

	function load_css() {
		wp_enqueue_script( 'openagenda_main_block', THFO_OPENWP_PLUGIN_URL . 'assets/css/generated.css' );
	}
}

new MainAgendaBlock();
