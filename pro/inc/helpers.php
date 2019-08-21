<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

add_action( 'created_openagenda_agenda', 'openwp_launch_import_on_new_agenda', 10, 2 );
function openwp_launch_import_on_new_agenda( $term_id, $tt_id ) {
	$agenda = get_term_by( 'id', $term_id, 'openagenda_agenda' );
	$agenda = $agenda->name;
	import_oa_events__premium_only( $agenda );
}

add_action( 'admin_init', 'openwp_sync_from_admin' );
function openwp_sync_from_admin() {
	if ( ! empty( $_GET['sync'] ) && 'now' === $_GET['sync'] && wp_verify_nonce( $_GET['_wpnonce'], 'force_sync' ) ) {
		import_oa_events__premium_only();
	}
}

/**
 * @param $id post_id.
 *
 * @return string Date formated
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
 * @param $id post_id
 *
 * @return string formated age
 *
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

//add_filter( 'pre_get_posts', 'openwp_chg_archive_order', 10 );
function openwp_chg_archive_order( $query ) {
	if ( is_post_type_archive( 'openagenda-events' ) ) {
		$query->set( 'order', 'asc' );
		$query->set(
			'meta_query', [
				[
					'key' => 'oa_start',
				]
			]
		);
		return $query;
	}
}

add_filter( 'pre_get_posts', 'openwp_hide_past_event', 20 );
function openwp_hide_past_event( $query ) {
	if ( is_post_type_archive( 'openagenda-events' ) ) {
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
