<?php

use OpenAgenda\Import\Import_OA;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.
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

	if ( ! empty( $block['openagenda_masonry'] ) && true !== $block['openagenda_masonry'] ) {
		?>
		.main_openagenda {
		display: grid;
		grid-template-columns: repeat(<?php echo $block['nb_events_per_line']; ?>, auto);
		grid-gap: 10px 20px;
		}
		<?php
	} else {
		?>
		.openagenda_event.openagenda_masonry {
		float: left;
		width: calc( <?php echo $width; ?>% - 10px );
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
	background: <?php echo $openagenda_description_background; ?>;
	color: <?php echo $openagenda_description_color; ?>;
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

add_action( 'created_openagenda_agenda', 'openwp_launch_import_on_new_agenda', 10, 2 );

/**
 * Import event from OpenAgenda when an agenda is added
 *
 * @param $term_id
 * @param $tt_id
 *
 * @since   3.0.0
 * @authors sebastienserre
 * @package OpenAgenda\Import
 */
function openwp_launch_import_on_new_agenda( $term_id, $tt_id ) {
	$agenda = get_term_by( 'id', $term_id, 'openagenda_agenda' );
	$agenda = $agenda->name;
	Import_OA::register_venue__premium_only();
	Import_OA::import_oa_events__premium_only( $agenda );
}

//add_action( 'admin_init', 'openwp_sync_from_admin', 15000 );
/**
 * Import event from OpenAgenda when the link in admin is clicked (by admin)
 *
 * @since   3.0.0
 * @authors sebastienserre
 * @package OpenAgenda\Import
 */
function openwp_sync_from_admin() {
	if ( ! empty( $_GET['oaimport'] ) && 'now' === $_GET['oaimport'] && wp_verify_nonce( $_GET['_wpnonce'], 'force_sync' ) ) {
		Import_OA::register_venue__premium_only();
		Import_OA::import_oa_events__premium_only();
		Import_OA::export_event__premium_only();
	}
}

/**
 * Display the Event Date
 *
 * @param $id post_id.
 *
 * @return string Date formated
 * @since   3.0.0
 * @authors sebastienserre
 * @package OpenAgenda\Import
 */
function openwp_display_date( $id ) {
	if ( empty( $id ) ) {
		error_log( 'WP-OpenAgenda - an id should be passed on params' );

		return;
	}

	$dates   = get_field( 'oa_date', $id );
	$startts = strtotime( $dates['begin'] );
	$endts   = strtotime( '05/31/2021' );

	if ( empty( $dates ) ) {
		$msg = __( 'No date for this event!', 'wp-openagenda' );
	}

	$msg = sprintf( __( '<p>From %1$s to %2$s</p>', 'wp-openagenda' ), $dates['begin'], $dates['end'] );

	if ( $dates['end'] === $dates['begin'] ) {
		$msg = sprintf( __( 'On %s', 'wp-openagenda' ), $dates['begin'] );
	}

	return $msg;
}

/**
 * Display the attendee age
 *
 * @param $id post_id
 *
 * @return string formated age
 * @since   3.0.0
 * @authors sebastienserre
 * @package OpenAgenda\Import
 */
function openwp_display_age( $id ) {

	$min_age = get_field( 'oa_min_age', $id );
	$max_age = get_field( 'oa_max_age', $id );

	if ( empty( $min_age ) ) {
		$msg = __( 'Welcome Everybody!', 'wp-openagenda' );
	}
	if ( empty( $end ) ) {
		$msg = sprintf( __( 'From %s years old', 'wp-openagenda' ), $min_age );
	}

	if ( ! empty( $start ) && ! empty( $end ) ) {
		$msg = sprintf( __( 'From %1$s years old to %2$s years old', 'wp-openagenda' ), $min_age, $max_age );

		if ( $min_age === $max_age ) {
			$msg = sprintf( __( 'From %s years old', 'wp-openagenda' ), $end );
		}
	}

	return $msg;
}

/**
 * Display the Event Accessibility
 *
 * @param int $id Ebent ID
 *
 * @return mixed|void
 * @since   3.0.0
 * @authors sebastienserre
 * @package OpenAgenda\Import
 */
function openwp_display_accessibilty( $id ) {
	$a11y = get_field( 'oa_a11y', $id );
	if ( ! empty( $a11y ) ) {
		ob_start();
	}
	?>
	<div class="oa-a11y">
		<?php
		foreach ( $a11y as $access ) {
			switch ( $access ) {
				case 'mi':
					$name = __( 'Accessible to disabled people', 'wp-openagenda' );
					break;
				case 'hi':
					$name = __( 'Accessible to the hearing impaired', 'wp-openagenda' );
					break;
				case 'pi':
					$name = __( 'Accessible to the psychic handicapped', 'wp-openagenda' );
					break;
				case 'vi':
					$name = __( 'Accessible to visually impaired', 'wp-openagenda' );
					break;
				case 'sl':
					$name = __( 'Accessible in sign language', 'wp-openagenda' );
					break;

			}
			?>
			<p class="oa-a11y-details oa-<?php echo $access; ?>">
													<?php
													echo $name
													?>
				</p>
			<?php
		}

		?>
	</div>

	<?php
	$a11y = ob_get_clean();

	/**
	 * Filters the A11y HTML Markup
	 *
	 * @since   3.0.0
	 * @authors sebastienserre
	 * @package OpenAgenda\Import
	 */
	return apply_filters( 'oa_a11y_display', $a11y );
}

// Load Template

add_filter( 'template_include', 'openwp_choose_template' );
function openwp_choose_template( $template ) {
	// Post ID
	$post_id = get_the_ID();

	// For all other CPT
	if ( get_post_type( $post_id ) != 'openagenda-events' ) {
		return $template;
	}

	// Else use custom template
	if ( is_singular( 'openagenda-events' ) ) {
		return openwp_get_template_hierarchy( 'single' );
	}
	if ( is_post_type_archive( 'openagenda-events' ) ) {
		return openwp_get_template_hierarchy( 'archive' );
	}

	return $template;
}

function openwp_get_template_hierarchy( $template ) {
	// Get the template slug
	$template_slug = rtrim( $template, '.php' );
	$template      = $template_slug . '.php';

	// Check if a custom template exists in the theme folder, if not, load the plugin template file
	if ( $theme_file = locate_template( array( 'openagenda/' . $template ) ) ) {
		$file = $theme_file;
	} else {
		$file = THFO_OPENWP_PLUGIN_PATH . '/template/' . $template;
	}

	return apply_filters( 'rc_repl_template_' . $template, $file );
}

function MediaFileAlreadyExists( $filename ) {
	global $wpdb;
	$query = "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_value LIKE '%/$filename'";

	return ( $wpdb->get_var( $query ) > 0 );
}

function oa_age() {
	$i   = 0;
	$age = array();
	while ( $i <= 100 ) {
		array_push( $age, $i );
		$i ++;
	}

	return $age;
}


/**
 * @param $msg
 *
 * @author  Sébastien SERRE
 * @package wp-openagenda
 * @since   1.8.9
 */
function openwp_debug( $msg ) {
	$bt     = debug_backtrace();
	$caller = array_shift( $bt );
	$msg   .= ' on ' . $caller['file'] . ':' . $caller['line'];
	if ( ! defined( 'WP_DEBUG' ) && true !== WP_DEBUG ) {
		return;
	}
	error_log( $msg );
}

/**
 * @param $event_id
 *
 * @return mixed
 * @author  sebastien
 * @package wp-openagenda
 * @since   2.2.0
 */
function get_venue_data( $event_id ) {
	$event_venue = get_field( 'oa_event_venues', $event_id );

	return get_field( 'oa_loc_address', $event_venue[0] );
}

/**
 * @param $event_id
 *
 * @author  sebastien
 * @package wp-openagenda
 * @since   2.2.0
 */
function display_map( $event_id ) {
	$venue_data = get_venue_data( $event_id );
	?>
	<div id="mapid" style="height: 400px">
		<div class="marker" data-url="<?php the_permalink(); ?>"
			 data-title='<h3><a href="{{{PHP2}}}" rel="bookmark"><?php the_title(); ?></a></h3>'
			 data-lat="<?php echo $venue_data['lat']; ?>"
			 data-lng="<?php echo $venue_data['lng']; ?>">
		</div>
	</div>
	<script>
		jQuery(function ($) {
			var map = L.map('mapid').setView([ <?php echo $venue_data['lat']; ?>,  <?php echo $venue_data['lng']; ?>], <?php echo $venue_data['zoom']; ?>);
			L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
				attribution: '© <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> © <a href="https://carto.com/attributions">CARTO</a>',
				subdomains: 'abcd',
				maxZoom: 19,
			}).addTo(map);
			$('.marker').each(function () {
				var lat = $(this).attr('data-lat');
				var lng = $(this).attr('data-lng');
				var name = $(this).attr('data-title');
				var marker = new L.marker([lat, lng]).bindPopup(name).addTo(map);
			});
		});

	</script>
	<?php
}
