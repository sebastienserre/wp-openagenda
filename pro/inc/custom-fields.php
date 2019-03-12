<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'plugins_loaded', 'oa_create_custom_fields', 410 );
function oa_create_custom_fields(){
	Container::make( 'post_meta', 'OpenAgenda Data')
		->where( 'post_type', '=', 'openagenda-events' )
		->add_fields(
			array(
				Field::make( 'text', 'oa_conditions', __( 'Conditions of participation, rates', 'wp-openagenda' ))
			)
		)
		->add_fields(
		array(
			Field::make( 'text', 'oa_tools', __( 'Registration tools', 'wp-openagenda' ))
		)
	);
}
