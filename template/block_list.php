<?php
/**
 * Template to display the list of event
 * used in wp-content/plugins/wp-openagenda/blocks/class-openwp-agenda-list.php
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.
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
		echo openwp_display_date( $event['id'] );
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
            <p><?php echo openwp_display_age( $event['id]'] ); ?></p>
			<?php
		}
		?>
    </div>
</div>