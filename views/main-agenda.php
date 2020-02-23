<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

function openwp_main_agenda_render_html( $events, $block ) {
	$parsedown = new \Parsedown();
	?>
	<div class="main_openagenda">
		<?php
		if ( is_string( $events ) ) {
			?>
			<p><?php _e( 'No event received from OpenAgenda, Please check your API Key. You can find it in your Openagenda Account. ', 'wp-openagenda' ); ?>
				<a href="https://thivinfo.com/docs/openagenda-pour-wordpress/"><?php _e( 'Need Help?', 'wp-openagenda' ); ?></a>
			</p>
			<?php
		} else {
			foreach ( $events as $event ) {
				if ( ! empty( $event['originalImage'] ) ) {
					$img = '<div class="openagenda_event_image"><img src="' . $event['originalImage'] . '" ></div>';
				}
				$firstDate = date_i18n( 'd F Y', strtotime( $event['firstDate'] ) );
				$lastDate  = date_i18n( 'd F Y', strtotime( $event['lastDate'] ) );

				if ( $event['firstDate'] !== $event['lastDate'] ) {
					$date = sprintf( __( 'From %1s to %2s', 'wp-openagenda' ), $firstDate, $lastDate );
				} else {
					$date = sprintf( __( 'On: %s', 'wp-openagenda' ), $firstDate );
				}
				if ( !empty( $block['openagenda_masonry'] ) ){
					wp_enqueue_script( 'IsotopeOA');
					wp_enqueue_script( 'IsotopeInit');
				}

				?>
				<div class="openagenda_event <?php if ( ! empty( $block['openagenda_masonry'] ) ){ echo 'openagenda_masonry'; }?>">
					<div class="openagenda_when">
						<p><?php echo $date; ?></p>
					</div>
					<div class="openagenda_description">
						<h3><?php echo $event['title'][ $block['lang'] ] ?></h3>
						<?php
                        if ( ! empty( $img ) ) {
	                        echo $img;
                            }
						if ( ! empty( $event['description'][ $block['lang'] ] ) && ( $block['openagenda_show_desc'] ) ) {
							echo '<p>' . $parsedown->text( $event['description'][ $block['lang'] ] ) . '</p>';
						}
						if ( ! empty( $event['longDescription'][ $block['lang'] ] ) && ( true === $block['openagenda_show_desc'] ) ) {
							echo '<p>' . $parsedown->text( $event['longDescription'][ $block['lang'] ] ) . '</p>';
						}
						?>
					</div>
					<div class="openagenda_meta">
						<div class="openagenda_where">
							<p><?php echo $event['locationName'] ?></p>
							<p><?php echo $event['address'] ?></p>
							<p><?php echo $event['postalCode'] ?></p>
							<p><?php echo $event['city'] ?></p>
						</div>
						<a href="<?php echo $event['canonicalUrl']; ?>" target="_blank"
						   title="<?php _e( 'Link to the event', 'wp-openagenda' ); ?>"><?php _e( 'Read More', 'wp-openagenda' ); ?></a>
					</div>
				</div>
				<?php
				unset( $img );
			}
		}
		?>
	</div><!--main_openagenda -->
	<?php
}

