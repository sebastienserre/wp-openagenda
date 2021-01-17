<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if ( ! function_exists( 'openagenda_keyword' ) ) {

// Register Custom Taxonomy
	function openagenda_keyword() {

		$labels = array(
			'name'                       => _x( 'keywords', 'Taxonomy General Name', 'wp-openagenda' ),
			'singular_name'              => _x( 'keyword', 'Taxonomy Singular Name', 'wp-openagenda' ),
			'menu_name'                  => __( 'keywords', 'wp-openagenda' ),
			'all_items'                  => __( 'All keywords', 'wp-openagenda' ),
			'parent_item'                => __( 'Parent keyword', 'wp-openagenda' ),
			'parent_item_colon'          => __( 'Parent keyword:', 'wp-openagenda' ),
			'new_item_name'              => __( 'New keyword Name', 'wp-openagenda' ),
			'add_new_item'               => __( 'Add New keyword', 'wp-openagenda' ),
			'edit_item'                  => __( 'Edit keyword', 'wp-openagenda' ),
			'update_item'                => __( 'Update keyword', 'wp-openagenda' ),
			'view_item'                  => __( 'View keyword', 'wp-openagenda' ),
			'separate_items_with_commas' => __( 'Separate keywords with commas', 'wp-openagenda' ),
			'add_or_remove_items'        => __( 'Add or remove keywords', 'wp-openagenda' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'wp-openagenda' ),
			'popular_items'              => __( 'Popular keywords', 'wp-openagenda' ),
			'search_items'               => __( 'Search keywords', 'wp-openagenda' ),
			'not_found'                  => __( 'Not Found', 'wp-openagenda' ),
			'no_terms'                   => __( 'No keywords', 'wp-openagenda' ),
			'items_list'                 => __( 'keywords list', 'wp-openagenda' ),
			'items_list_navigation'      => __( 'keywords list navigation', 'wp-openagenda' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => false,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => false,
			'rewrite'                    => false,
			'show_in_rest'               => false,
		);
		register_taxonomy( 'openagenda_keyword', array( 'openagenda-events' ), $args );

	}
	add_action( 'init', 'openagenda_keyword', 0 );

}
