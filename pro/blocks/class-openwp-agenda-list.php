<?php

namespace WPGC\BlOCKS\Single;

use function _e;
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
			?>
            <div class="openagenda-event-list">
				<?php
				if ( has_post_thumbnail( $event['id'] ) ) {
					?>
                    <div class="event-thumbnail">
						<?php
						echo get_the_post_thumbnail( $event['id'], 'post-thumbnail' );
						?>
                    </div>
					<?php
				}
				?>

                <h2><?php echo get_the_title( $event['id'] ); ?></h2>
                <p>
					<?php
					echo get_the_content( '', '', $event['id'] );;
					?>
                </p>
                <div class="meta">
					<?php
					$start      = date_i18n( 'd F Y', carbon_get_post_meta( $event['id'], 'oa_start' ) );
					$end        = date_i18n( 'd F Y', carbon_get_post_meta( $event['id'], 'oa_end' ) );
					$conditions = carbon_get_post_meta( $event['id'], 'oa_conditions' );
					$tools      = carbon_get_post_meta( $event['id'], 'oa_tools' );
					$min_age    = carbon_get_post_meta( $event['id'], 'oa_min_age' );
					$max_age    = carbon_get_post_meta( $event['id'], 'oa_max_age' );
					$a11y       = carbon_get_post_meta( $event['id'], 'oa_a11y' );
					echo openwp_display_date( $start, $end );
					?>
					<?php
					if ( ! empty( $conditions ) ) { ?>
                        <h3>
							<?php _e( 'Conditions of participation, rates', 'wp-openagenda' ); ?>
                        </h3>
                        <p><?php echo $conditions; ?></p>
						<?php
					}
					?>
	                <?php
	                if ( ! empty( $tools ) ) { ?>
                        <h3>
			                <?php _e( 'Registration tools', 'wp-openagenda' ); ?>
                        </h3>
                        <p><?php echo $tools; ?></p>
		                <?php
	                }
	                ?>
	                <?php
	                if ( ! empty( $min_age ) || ! empty( $max_age ) ) { ?>
                        <h3>
			                <?php _e( 'Age', 'wp-openagenda' ); ?>
                        </h3>
                        <p><?php echo openwp_display_age( $min_age, $max_age); ?></p>
		                <?php
	                }
	                ?>
                </div>
            </div>
			<?php
		}

		?>

		<?php
	}
}

new OA_Event_List();
