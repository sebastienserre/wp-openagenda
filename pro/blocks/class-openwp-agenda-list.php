<?php

namespace WPGC\BlOCKS\Single;

use Carbon_Fields\Field;
use Carbon_Fields\Block;
use function get_the_title;
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
								'type' =>   'post',
								'post_type' =>  'openagenda-events'
							]
						]
					),
				]
			);
	}

	public function render( $block ){
		var_dump( $block );

		foreach ( $block['openwp_event_association'] as $event ){
			?>
			<h2><?php echo get_the_title( $event['id'] ); ?></h2>
			<?php
		}

		?>

		<?php
	}
}
new OA_Event_List();
