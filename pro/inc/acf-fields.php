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

if ( function_exists( 'acf_add_local_field_group' ) ):

	acf_add_local_field_group( array(
		'key'                   => 'group_5d5e7d9037572',
		'title'                 => 'Évenements',
		'fields'                => array(
			array(
				'key'               => 'field_5d5e7e2889e37',
				'label'             => 'Conditions of participation',
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
				'label'             => 'Registration tools',
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
				'label'             => 'Minimum age',
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
				'label'             => 'Maximum age',
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
				'label'             => 'Accessibility',
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
				'key'               => 'field_5d5e81a5a7114',
				'label'             => 'Date',
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
						'key'               => 'field_5d5e81baa7115',
						'label'             => 'start date',
						'name'              => 'oa_start',
						'type'              => 'date_time_picker',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'display_format'    => 'd/m/Y g:i a',
						'return_format'     => 'U',
						'first_day'         => 1,
					),
					array(
						'key'               => 'field_5d5e81eea7116',
						'label'             => 'End date',
						'name'              => 'oa_end',
						'type'              => 'date_time_picker',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'display_format'    => 'd/m/Y g:i a',
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