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
	if ( ! empty( $_GET['sync'] ) && 'now' === $_GET['sync'] && wp_verify_nonce( $_GET['_wpnonce'], 'force_sync' ) ) {
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

	$start = date_i18n( 'd F Y', carbon_get_post_meta( $id, 'oa_start' ) );
	$end   = date_i18n( 'd F Y', carbon_get_post_meta( $id, 'oa_end' ) );

	if ( empty( $start ) ) {
		$msg = __( 'No date for this event!', 'wp-openagenda' );
	}
	if ( empty( $end ) ) {
		$msg = sprintf( __( 'On %s', 'wp-openagenda' ), $end );
	}

	if ( ! empty( $start ) && ! empty( $end ) ) {
		$msg = sprintf( __( 'From %1$s to %2$s', 'wp-openagenda' ), $start, $end );

		if ( $start === $end ) {
			$msg = sprintf( __( 'On %s', 'wp-openagenda' ), $end );
		}
	}

	return $msg;
}

/**
 * Dislay the attendee age
 * @param $id post_id
 *
 * @return string formated age
 * @since 3.0.0
 * @authors sebastienserre
 * @package OpenAgenda\Import
 */
function openwp_display_age( $id ) {

	$min_age = carbon_get_post_meta( $id, 'oa_min_age' );
	$max_age = carbon_get_post_meta( $id, 'oa_max_age' );

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
	if ( is_archive() ) {
		return openwp_get_template_hierarchy( 'archive' );
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

add_filter( 'pre_get_posts', 'openwp_hide_past_event', 20 );
function openwp_hide_past_event( $query ) {
	if ( ! is_admin() && is_post_type_archive( 'openagenda-events' ) ) {
		$query->set( 'order', 'ASC' );
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'meta_key', '_oa_start' );
		$query->set(
			'meta_query',
			[
				[
					'key'     => '_oa_start',
					'type'    => 'NUMERIC',
					'value'   => current_time( 'timestamp' ),
					'compare' => '>=',
				],
			]
		);
	}

	return $query;
}

/**
 * @return array $age Return the attendee ages in a array
 * @since 3.0.0
 * @authors sebastienserre
 * @package OpenAgenda\Import
 */
function oa_age() {
	$i   = 0;
	$age = array();
	while ( $i <= 100 ) {
		array_push( $age, $i );
		$i ++;
	}

	return $age;
}

//add_filter('acf/update_value/type=date_time_picker', 'my_update_value_date_time_picker', 10, 3);

/**
 * Change the store format date
 * @param $value string the datetime
 * @param $post_id int
 * @param $field string Fields to work
 *
 * @return false|int
 */
function my_update_value_date_time_picker( $value, $post_id, $field ) {

	return strtotime($value);

}