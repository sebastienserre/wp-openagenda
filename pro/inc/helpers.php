<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

function is_tec_used(){
	$tec = get_option( 'openagenda-tec' );
	if ( 'yes' === $tec ) {
		return true;
	}
	return false;
}