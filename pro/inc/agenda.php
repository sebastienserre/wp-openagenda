<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.
if ( ! function_exists( 'openagenda_agenda' ) ) {

// Register Custom Taxonomy
	function openagenda_agenda() {

		$labels = array(
			'name'                       => _x( 'Agendas', 'Taxonomy General Name', 'wp-openagenda-pro' ),
			'singular_name'              => _x( 'Agenda', 'Taxonomy Singular Name', 'wp-openagenda-pro' ),
			'menu_name'                  => __( 'Agenda', 'wp-openagenda-pro' ),
			'all_items'                  => __( 'All Agendas', 'wp-openagenda-pro' ),
			'parent_item'                => __( 'Parent Agenda', 'wp-openagenda-pro' ),
			'parent_item_colon'          => __( 'Parent Agenda:', 'wp-openagenda-pro' ),
			'new_item_name'              => __( 'New Agenda Name', 'wp-openagenda-pro' ),
			'add_new_item'               => __( 'Add New Agenda', 'wp-openagenda-pro' ),
			'edit_item'                  => __( 'Edit Agenda', 'wp-openagenda-pro' ),
			'update_item'                => __( 'Update Agenda', 'wp-openagenda-pro' ),
			'view_item'                  => __( 'View Agenda', 'wp-openagenda-pro' ),
			'separate_items_with_commas' => __( 'Separate Agendas with commas', 'wp-openagenda-pro' ),
			'add_or_remove_items'        => __( 'Add or remove Agendas', 'wp-openagenda-pro' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'wp-openagenda-pro' ),
			'popular_items'              => __( 'Popular Agendas', 'wp-openagenda-pro' ),
			'search_items'               => __( 'Search Agendas', 'wp-openagenda-pro' ),
			'not_found'                  => __( 'Not Found', 'wp-openagenda-pro' ),
			'no_terms'                   => __( 'No Agendas', 'wp-openagenda-pro' ),
			'items_list'                 => __( 'Items list', 'wp-openagenda-pro' ),
			'items_list_navigation'      => __( 'Items list navigation', 'wp-openagenda-pro' ),
		);
		$args   = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'rewrite'           => false,
			'show_in_rest'      => true,
		);
		register_taxonomy( 'openagenda_agenda', array( 'openagenda-events', 'tribe_events' ), $args );

	}

	add_action( 'init', 'openagenda_agenda', 0 );

}
