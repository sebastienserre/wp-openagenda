<?php

use OpenAgenda\Import\Import_OA;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

add_action( 'created_openagenda_agenda', 'openwp_launch_import_on_new_agenda', 10, 2 );

/**
 * Import event from OpenAgenda when an agenda is added
 * @param $term_id
 * @param $tt_id
 * @since 3.0.0
 * @authors sebastienserre
 * @package OpenAgenda\Import
 */
function openwp_launch_import_on_new_agenda( $term_id, $tt_id ) {
	$agenda = get_term_by( 'id', $term_id, 'openagenda_agenda' );
	$agenda = $agenda->name;
	Import_OA::register_venue__premium_only();
	Import_OA::import_oa_events__premium_only( $agenda );
}

add_action( 'admin_init', 'openwp_sync_from_admin', 15000 );
/**
 * Import event from OpenAgenda when the link in admin is clicked (by admin)
 * @since 3.0.0
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
 * @param $id post_id.
 *
 * @return string Date formated
 * @since 3.0.0
 * @authors sebastienserre
 * @package OpenAgenda\Import
 */
function openwp_display_date( $id ) {
	if ( empty( $id ) ) {
		error_log( 'WP-OpenAgenda - an id should be passed on params' );

		return;
	}

	$dates = get_field( 'oa_date', $id );

	if ( empty( $dates ) ) {
		$msg = __( 'No date for this event!', 'wp-openagenda-pro' );
	}

	foreach ( $dates as $date ) {
		$msg .= sprintf( __( '<p>From %1$s to %2$s</p>', 'wp-openagenda-pro' ), date_i18n( 'd F Y G\Hi', $date['begin'] ),
			date_i18n( 'd F Y G\Hi', $date['end'] ) );
	}
	if ( ! empty( $start ) && ! empty( $end ) ) {


		if ( $start === $end ) {
			$msg = sprintf( __( 'On %s', 'wp-openagenda-pro' ), $end );
		}
	}

	return $msg;
}

/**
 * Display the attendee age
 * @param $id post_id
 *
 * @return string formated age
 * @since 3.0.0
 * @authors sebastienserre
 * @package OpenAgenda\Import
 */
function openwp_display_age( $id ) {

	$min_age = get_field( 'oa_min_age', $id );
	$max_age = get_field( 'oa_max_age', $id );

	if ( empty( $min_age ) ) {
		$msg = __( 'Welcome Everybody!', 'wp-openagenda-pro' );
	}
	if ( empty( $end ) ) {
		$msg = sprintf( __( 'From %s years old', 'wp-openagenda-pro' ), $min_age );
	}

	if ( ! empty( $start ) && ! empty( $end ) ) {
		$msg = sprintf( __( 'From %1$s years old to %2$s years old', 'wp-openagenda-pro' ), $min_age, $max_age );

		if ( $min_age === $max_age ) {
			$msg = sprintf( __( 'From %s years old', 'wp-openagenda-pro' ), $end );
		}
	}

	return $msg;
}

/**
 * Display the Event Accessibility
 * @param int $id Ebent ID
 *
 * @return mixed|void
 * @since 3.0.0
 * @authors sebastienserre
 * @package OpenAgenda\Import
 */
function openwp_display_accessibilty( $id ) {
	$a11y = carbon_get_post_meta( $id, 'oa_a11y' );
	if ( ! empty( $a11y ) ) {
		ob_start();
	}
	?>
    <div class="oa-a11y">
		<?php
		foreach ( $a11y as $access ) {
			switch ( $access ) {
				case 'mi':
					$name = __( 'Accessible to disabled people', 'wp-openagenda-pro' );
					break;
				case 'hi':
					$name = __( 'Accessible to the hearing impaired', 'wp-openagenda-pro' );
					break;
				case 'pi':
					$name = __( 'Accessible to the psychic handicapped', 'wp-openagenda-pro' );
					break;
				case 'vi':
					$name = __( 'Accessible to visually impaired', 'wp-openagenda-pro' );
					break;
				case 'sl':
					$name = __( 'Accessible in sign language', 'wp-openagenda-pro' );
					break;

			}
			?>
            <p class="oa-a11y-details oa-<?php echo $access ?>"><?php echo $name
				?></p>
			<?php
		}


		?>
    </div>

	<?php
	$a11y = ob_get_clean();

	/**
	 * Filters the A11y HTML Markup
     * @since 3.0.0
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
	if ( is_single() ) {
		return openwp_get_template_hierarchy( 'single' );
	}
}

function openwp_get_template_hierarchy( $template ) {
	// Get the template slug
	$template_slug = rtrim( $template, '.php' );
	$template      = $template_slug . '.php';

	// Check if a custom template exists in the theme folder, if not, load the plugin template file
	if ( $theme_file = locate_template( array( 'openagenda/' . $template ) ) ) {
		$file = $theme_file;
	} else {
		$file = THFO_OPENWP_PLUGIN_PATH . '/pro/template/' . $template;
	}

	return apply_filters( 'rc_repl_template_' . $template, $file );
}

function MediaFileAlreadyExists($filename){
	global $wpdb;
	$query = "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_value LIKE '%/$filename'";
	return ($wpdb->get_var($query)  > 0) ;
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

add_filter( 'manage_tribe_events_posts_columns', 'openwp_add_column', 10,1);
add_filter( 'manage_tribe_venue_posts_columns', 'openwp_add_column', 10,1);
function openwp_add_column( $columns ) {
    $columns['oa'] = 'OpenAgenda ID';
    return $columns;
}

add_action( 'manage_tribe_events_posts_custom_column' , 'openwp_oa_id', 10, 2 );
add_action( 'manage_tribe_venue_posts_custom_column' , 'openwp_oa_id', 10, 2 );
function openwp_oa_id( $column, $post_id ){
    switch ( $column ){
        case 'oa':
            $id = get_post_meta( $post_id, '_oa_event_uid', true );
            if ( ! empty( $id ) ){
                echo $id;
            } else {
                _e( 'Not saved to OpenAgenda', 'openagenda-pro' );
            }
    }
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
	$msg    .= ' on ' . $caller['file'] . ':' . $caller['line'];
	if ( ! defined( 'WP_DEBUG' ) && true !== WP_DEBUG ) {
		return;
	}
	error_log( $msg );
}