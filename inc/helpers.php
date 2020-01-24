<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

function is_openwp_pro() {
    if ( '1' === get_option( 'openwppro') || function_exists( 'OpenAgenda_WP_Pro' ) ){
        return true;
    }
    return false;
}
function create_css_files( $name, $id, $block ) {

	if ( ! file_exists( WP_CONTENT_DIR . '/openagenda/css/' ) ) {
		wp_mkdir_p( WP_CONTENT_DIR . '/openagenda/css/' );
	}
	$file = WP_CONTENT_DIR . '/openagenda/css/' . $name . '-' . $id . '.css';
	$url  = WP_CONTENT_URL . '/openagenda/css/' . $name . '-' . $id . '.css';

	$css  = openwp_generate_css( $block );
	$open = fopen( $file, 'w+' );
	fwrite( $open, $css );
	fclose( $open );
	wp_register_style( $name . '-' . $id, $url );
}

function openwp_generate_css( $block ) {
	$openagenda_description_background = $block['openagenda_description_background'];
	$openagenda_description_color      = $block['openagenda_description_color'];
	$openagenda_date_background        = $block['openagenda_date_background'];
	$openagenda_date_color             = $block['openagenda_date_color'];
	$nb_columns                        = $block['nb_events_per_line'];
	$width                             = 100 / $nb_columns;

	/**
	 * Filters colors // return the hex color code
	 */
	$openagenda_description_background = apply_filters( 'openagenda/mainBlock/description/bg', $openagenda_description_background );
	$openagenda_description_color      = apply_filters( 'openagenda/mainBlock/description/txt', $openagenda_description_color );
	$openagenda_date_background        = apply_filters( 'openagenda/mainBlock/date/bg', $openagenda_date_background );
	$openagenda_date_color             = apply_filters( 'openagenda/mainBlock/date/txt', $openagenda_date_color );

	ob_start();

	if ( is_openwp_pro() && true !== $block['openagenda_masonry'] ) {
		?>
		.main_openagenda {
		display: grid;
		grid-template-columns: repeat(<?php echo $block['nb_events_per_line'] ?>, auto);
		grid-gap: 10px 20px;
		}
		<?php
	} else { ?>
		.openagenda_event.openagenda_masonry {
		float: left;
		width: calc( <?php echo $width ?>% - 10px );
		margin: 0 5px 10px 5px;
		}
		<?php
	}
	?>
	.openagenda_when {
	background: <?php echo $openagenda_date_background; ?>;
	color: <?php echo $openagenda_date_color; ?>;
	}
	.openagenda_when p {
	text-align: center;
	}
	.openagenda_event {
	background: <?php echo $openagenda_description_background ?>;
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

	return $css;
}
