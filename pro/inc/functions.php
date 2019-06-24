<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

function is_tec_exists(){
	if ( post_type_exists( 'tribe_events') ){
		__return_true();
	}

	__return_false();
}