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