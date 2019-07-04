<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

add_action( 'created_openagenda_agenda', 'openwp_launch_import_on_new_agenda', 10, 2);
function openwp_launch_import_on_new_agenda( $term_id, $tt_id ) {
	$agenda = get_term_by( 'id', $term_id, 'openagenda_agenda');
	$agenda = $agenda->name;
	import_oa_events__premium_only( $agenda );
}

add_action( 'admin_init', 'openwp_sync_from_admin' );
function openwp_sync_from_admin(){
	if ( ! empty( $_GET['sync'] && 'now' === $_GET[ 'sync'] && wp_verify_nonce( $_GET['_wpnonce'], 'force_sync' ) ) ){
		import_oa_events__premium_only();
	}
}

function openwp_display_date( $start, $end ){
	if ( empty( $start ) ){
		$msg = __( 'No date for this event!', 'wp-openagenda' );
	}
	if ( empty( $end ) ){
		$msg = sprintf( __( 'On %s', 'wp-openagenda' ), $end );
	}

	if ( ! empty( $start ) && !empty( $end ) ){
		$msg = sprintf( __( 'From %1$s to %2$s', 'wp-openagenda' ), $start, $end );

		if( $start === $end ){
			$msg = sprintf( __( 'On %s', 'wp-openagenda' ), $end );
		}
	}
	return $msg;
}

function openwp_display_age( $min_age, $max_age){

	if ( empty( $min_age ) ){
		$msg = __( 'Welcome Everybody!', 'wp-openagenda' );
	}
	if ( empty( $end ) ){
		$msg = sprintf( __( 'From %s years old', 'wp-openagenda' ), $min_age );
	}

	if ( ! empty( $start ) && !empty( $end ) ){
		$msg = sprintf( __( 'From %1$s years old to %2$s years old', 'wp-openagenda' ), $min_age, $max_age );

		if( $min_age === $max_age ){
			$msg = sprintf( __( 'From %s years old', 'wp-openagenda' ), $end );
		}
	}
	return $msg;
}


function MediaFileAlreadyExists($filename){
	global $wpdb;
	$query = "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_value LIKE '%/$filename'";
	return ($wpdb->get_var($query)  > 0) ;
}