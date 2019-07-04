<?php

namespace WPGC\BlOCKS\Single;

use function _e;
use function apply_filters_ref_array;
use Carbon_Fields\Field;
use Carbon_Fields\Block;
use function carbon_get_post_meta;
use function date_i18n;
use function get_the_content;
use function get_the_post_thumbnail;
use function get_the_title;
use function has_post_thumbnail;
use function var_dump;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

class OA_Event_List {
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'block_init' ) );
	}

	public function block_init() {
		Block::make( __( 'Openagenda Single', 'wp-openagenda' ) )
		     ->set_category( 'custom-category', 'Openagenda', 'calendar-alt' )
		     ->set_render_callback( array( $this, 'render' ) )
		     ->add_fields(
			     [
				     Field::make( 'association', 'openwp_event_association', __( 'Event', 'wp-openagenda' ) )
				          ->set_types(
					          [
						          [
							          'type'      => 'post',
							          'post_type' => 'openagenda-events'
						          ]
					          ]
				          ),
			     ]
		     );
	}

	public function render( $block ) {

		foreach ( $block['openwp_event_association'] as $event ) {
			$start      = date_i18n( 'd F Y', carbon_get_post_meta( $event['id'], 'oa_start' ) );
			$end        = date_i18n( 'd F Y', carbon_get_post_meta( $event['id'], 'oa_end' ) );
			$conditions = carbon_get_post_meta( $event['id'], 'oa_conditions' );
			$tools      = carbon_get_post_meta( $event['id'], 'oa_tools' );
			$min_age    = carbon_get_post_meta( $event['id'], 'oa_min_age' );
			$max_age    = carbon_get_post_meta( $event['id'], 'oa_max_age' );
			$a11y       = carbon_get_post_meta( $event['id'], 'oa_a11y' );

		    $template = apply_filters( 'oa_block_list_template', THFO_OPENWP_PLUGIN_PATH . 'pro/template/block_list.php');

		    require $template;
		}

		?>

		<?php
	}
}

new OA_Event_List();
