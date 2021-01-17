<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

$accessibility = array(
	'mi' => __( 'Accessible to disabled people', 'wp-openagenda' ),
	'hi' => __( 'Accessible to the hearing impaired', 'wp-openagenda' ),
	'pi' => __( 'Accessible to the psychic handicapped', 'wp-openagenda' ),
	'vi' => __( 'Accessible to visually impaired', 'wp-openagenda' ),
	'sl' => __( 'Accessible in sign language', 'wp-openagenda' ),

);

function custom_acf_settings_textdomain( $domain ) {
	return 'wp-openagenda';
}

add_filter( 'acf/settings/l10n_textdomain', 'custom_acf_settings_textdomain' );


if ( function_exists( 'acf_add_local_field_group' ) ):

	acf_add_local_field_group( array(
		'key'                   => 'group_5d5e7d9037572',
		'title'                 => __( 'Events', 'wp-openagenda' ),
		'fields'                => array(
			array(
				'key'               => 'field_5d5e7e2889e37',
				'label'             => __( 'Conditions of participation', 'wp-openagenda' ),
				'name'              => 'oa_conditions',
				'type'              => 'text',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '',
				'placeholder'       => '',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => '',
			),
			array(
				'key'               => 'field_5d5e7e661b977',
				'label'             => __( 'Registration tools', 'wp-openagenda' ),
				'name'              => 'oa_tools',
				'type'              => 'text',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '',
				'placeholder'       => '',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => '',
			),
			array(
				'key'               => 'field_5d5e7e8588a69',
				'label'             => __( 'Minimum age', 'wp-openagenda' ),
				'name'              => 'oa_min_age',
				'type'              => 'select',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'choices'           => oa_age(),
				'default_value'     => array(),
				'allow_null'        => 0,
				'multiple'          => 0,
				'ui'                => 0,
				'return_format'     => 'value',
				'ajax'              => 0,
				'placeholder'       => '',
			),
			array(
				'key'               => 'field_5d5e7ea91df1d',
				'label'             => __( 'Maximum age', 'wp-openagenda' ),
				'name'              => 'oa_max_age',
				'type'              => 'select',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'choices'           => oa_age(),
				'default_value'     => array(),
				'allow_null'        => 0,
				'multiple'          => 0,
				'ui'                => 0,
				'return_format'     => 'value',
				'ajax'              => 0,
				'placeholder'       => '',
			),
			array(
				'key'               => 'field_5d5e7ec216a31',
				'label'             => __( 'Accessibility', 'wp-openagenda' ),
				'name'              => 'oa_a11y',
				'type'              => 'select',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'choices'           => $accessibility,
				'default_value'     => array(),
				'allow_null'        => 0,
				'multiple'          => 1,
				'ui'                => 0,
				'return_format'     => 'value',
				'ajax'              => 0,
				'placeholder'       => '',
			),
			array(
				'key'               => 'field_5d50075c33c2d',
				'label'             => __( 'Date', 'wp-openagenda' ),
				'name'              => 'oa_date',
				'type'              => 'repeater',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'collapsed'         => '',
				'min'               => 0,
				'max'               => 0,
				'layout'            => 'table',
				'button_label'      => '',
				'sub_fields'        => array(
					array(
						'key'               => 'field_5d61787c65c27',
						'label'             => __( 'Begin', 'wp-openagenda' ),
						'name'              => 'begin',
						'type'              => 'date_time_picker',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'display_format'    => 'd/m/Y H:i:s',
						'return_format'     => 'U',
						'first_day'         => 1,
					),
					array(
						'key'               => 'field_5d61789f65c28',
						'label'             => __( 'End', 'wp-openagenda' ),
						'name'              => 'end',
						'type'              => 'date_time_picker',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'display_format'    => 'd/m/Y H:i:s',
						'return_format'     => 'U',
						'first_day'         => 1,
					),
				),
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'openagenda-events',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => '',
	) );

endif;