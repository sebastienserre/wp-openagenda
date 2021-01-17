<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'plugins_loaded', 'oa_create_custom_fields', 410 );
function oa_create_custom_fields() {

	$accessibility = array(
		'mi' => __( 'Accessible to disabled people', 'wp-openagenda' ),
		'hi' => __( 'Accessible to the hearing impaired', 'wp-openagenda' ),
		'pi' => __( 'Accessible to the psychic handicapped', 'wp-openagenda' ),
		'vi' => __( 'Accessible to visually impaired', 'wp-openagenda' ),
		'sl' => __( 'Accessible in sign language', 'wp-openagenda' ),

	);

	Container::make( 'post_meta', 'OpenAgenda Data' )
		->where( 'post_type', '=', 'openagenda-events' )
		->add_fields(
			[
				Field::make( 'text', 'oa_conditions', __( 'Conditions of participation, rates', 'wp-openagenda' ) ),
			]
		)
		->add_fields(
			[
				Field::make( 'text', 'oa_tools', __( 'Registration tools', 'wp-openagenda' ) )
					->set_attribute( 'placeholder', __( 'URL or email', 'wp-openagenda' ) ),
			]
		)
		->add_fields(
			[
				Field::make( 'select', 'oa_min_age', __( 'Minimum age', 'wp-openagenda' ) )
					->set_options( oa_age() )
					->set_width( 50 ),
			]
		)
		->add_fields(
			[
				Field::make( 'select', 'oa_max_age', __( 'Maximum age', 'wp-openagenda' ) )
					->set_options( oa_age() )
					->set_width( 50 ),
			]
		)
		->add_fields(
			[
				Field::make( 'multiselect', 'oa_a11y', __( 'Accessibility', 'wp-openagenda' ) )
					->add_options( $accessibility ),
			]
		)
		->add_fields(
			Field::make( 'complex', 'oa_event_date', __( 'Event', 'wp-openagenda' ) )
				->add_fields( 'date',
					[
						Field::make( 'date_time', 'oa_start', __( 'start date', 'wp-openagenda' ) )
							->set_storage_format( 'U' )
							->set_attribute( 'placeholder', __( 'Start date for this event', 'wp-openagenda' ) )
							->set_picker_options( [ 'allowInput' => false ] )
							->set_width( 50 )
							->set_required( true ),
						Field::make( 'date_time', 'oa_end', __( 'End date', 'wp-openagenda' ) )
							->set_storage_format( 'U' )
							->set_attribute( 'placeholder', __( 'End date for this event', 'wp-openagenda' ) )
							->set_picker_options( [ 'allowInput' => false ] )
							->set_width( 50 )
							->set_required( true ),
					]
				)
		);


}



add_action( 'plugins_loaded', 'oa_location_fields', 10000 );
function oa_location_fields() {
	Container::make( 'term_meta', __( 'Openagenda data', 'wp-openagenda' ) )
	         ->where( 'term_taxonomy', '=', 'openagenda_venue' )
	         ->add_fields( array(
		         Field::make( 'text', 'oa_location_uid', 'LocationUID' ),
	         ) );
}

