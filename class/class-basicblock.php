<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

use Carbon_Fields\Block;
use Carbon_Fields\Field;

class BasicBlock {

	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'openwp_basic_block' ), 500 );
	}

	public function openwp_basic_block() {
		Block::make( __( 'OpenAgenda Basic Block', 'wp-openagenda' ) )
		     ->add_fields( array(
			     Field::make( 'text', 'openwp_url', __( 'Openagenda URL', 'wp-openagenda' ) ),
			     Field::make( 'select', 'openwp_nb_results', __( 'Number of events', 'wp-openagenda' ) )
			          ->set_options( array(
			          		'0'   => 0,
					        '5'   => 5,
					        '10'  => 10,
					        '15'  => 15,
					        '20'  => 20,
					        '50'  => 50,
					        '100' => 100,
				          )
			          ),
			     Field::make( 'text', 'lang', __( '2 letters language code', 'wp-openagenda') )
			     ->set_attribute( 'maxLength', 2),
			     Field::make( 'radio', 'openwp_img', __( 'Display Images', 'wp-openagenda' ) )
			     ->set_options( array(
			     		'yes'   =>  __( 'Yes', 'wp-openagenda' ),
				        'no'    =>  __( 'No', 'wp-openagenda' ),
			     )),
			     Field::make( 'radio', 'event-title', __( 'Display Title', 'wp-openagenda' ) )
			          ->set_options( array(
				          'yes'   =>  __( 'Yes', 'wp-openagenda' ),
				          'no'    =>  __( 'No', 'wp-openagenda' ),
			          )),
			     Field::make( 'radio', 'event-description', __( 'Display description', 'wp-openagenda' ) )
			          ->set_options( array(
				          'yes'   =>  __( 'Yes', 'wp-openagenda' ),
				          'no'    =>  __( 'No', 'wp-openagenda' ),
			          ))

		     )
		     )
		     ->set_description( __( 'basic Openagenda Block', 'wp-openagenda' ) )
			->set_category( 'custom-category', 'Openagenda', 'calendar-alt' )
		     ->set_render_callback( array( $this, 'openwp_basic_render' ) );


	}

	public function openwp_basic_render( $block ) {
		$openwp =new OpenAgendaApi();
		$openwp_data = $openwp->thfo_openwp_retrieve_data( $block['openwp_url'], $block['openwp_nb_results'] );
		ob_start();

		if ( empty( $block['lang'] ) ){
			$block['lang'] = 'fr';
		}

		$openwp->openwp_basic_html( $openwp_data, $block['lang'], $block );
		echo ob_get_clean();
	}

}

new BasicBlock();
