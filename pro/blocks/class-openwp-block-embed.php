<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

use Carbon_Fields\Block;
use Carbon_Fields\Field;

class OpenwpBlockEmbed {
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'openwp_embed_block_init' ) );
	}

	public function openwp_embed_block_init() {
		Block::make( __( 'OpenAgenda embed Block', 'wp-openagenda' ) )
		     ->set_category( 'custom-category', 'Openagenda', 'calendar-alt' )
		     ->set_render_callback( array( $this, 'openwp_embed_render' ) )
		     ->add_fields(
			     array(
				     Field::make( 'text', 'openwp_url', __( 'Openagenda URL', 'wp-openagenda' ) ),
				     Field::make( 'text', 'lang', __( '2 letters language code', 'wp-openagenda' ) )
				          ->set_attribute( 'maxLength', 2 ),
				     Field::make( 'select', 'widget', __( 'OpenAgenda\'s widget', 'wp-openagenda' ) )
				          ->set_options( array(
						          'none'       => __( 'Select an OpenAgenda Widget', 'wp-openagenda' ),
						          'general'    => __( 'General', 'wp-openagenda' ),
						          'map'        => __( 'Map', 'wp-openagenda' ),
						          'search'     => __( 'Search', 'wp-openagenda' ),
						          'categories' => __( 'Categories', 'wp-openagenda' ),
						          'tags'       => __( 'Tags', 'wp-openagenda' ),
						          'calendrier' => __( 'Calendrier', 'wp-openagenda' ),
						          'preview'    => __( 'Preview', 'wp-openagenda' ),
					          )
				          ),
			     ) );
	}

	public function openwp_embed_render( $block ) {
		$openwp = new OpenAgendaApi();
		$uid    = $openwp->openwp_get_uid( $block['openwp_url'] );
		$key    = $openwp->thfo_openwp_get_api_key();
		$embed  = $openwp->openwp_get_embed( $uid, $key );

		echo $openwp->openwp_main_widget_html__premium_only( $embed, $uid, $block );
	}
}

new OpenwpBlockEmbed();
