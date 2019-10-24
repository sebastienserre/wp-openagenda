<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if ( ! function_exists( 'openagenda_keyword' ) ) {

// Register Custom Taxonomy
	function openagenda_keyword() {

		$labels = array(
			'name'                       => _x( 'keywords', 'Taxonomy General Name', 'wp-openagenda-pro' ),
			'singular_name'              => _x( 'keyword', 'Taxonomy Singular Name', 'wp-openagenda-pro' ),
			'menu_name'                  => __( 'keywords', 'wp-openagenda-pro' ),
			'all_items'                  => __( 'All keywords', 'wp-openagenda-pro' ),
			'parent_item'                => __( 'Parent keyword', 'wp-openagenda-pro' ),
			'parent_item_colon'          => __( 'Parent keyword:', 'wp-openagenda-pro' ),
			'new_item_name'              => __( 'New keyword Name', 'wp-openagenda-pro' ),
			'add_new_item'               => __( 'Add New keyword', 'wp-openagenda-pro' ),
			'edit_item'                  => __( 'Edit keyword', 'wp-openagenda-pro' ),
			'update_item'                => __( 'Update keyword', 'wp-openagenda-pro' ),
			'view_item'                  => __( 'View keyword', 'wp-openagenda-pro' ),
			'separate_items_with_commas' => __( 'Separate keywords with commas', 'wp-openagenda-pro' ),
			'add_or_remove_items'        => __( 'Add or remove keywords', 'wp-openagenda-pro' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'wp-openagenda-pro' ),
			'popular_items'              => __( 'Popular keywords', 'wp-openagenda-pro' ),
			'search_items'               => __( 'Search keywords', 'wp-openagenda-pro' ),
			'not_found'                  => __( 'Not Found', 'wp-openagenda-pro' ),
			'no_terms'                   => __( 'No keywords', 'wp-openagenda-pro' ),
			'items_list'                 => __( 'keywords list', 'wp-openagenda-pro' ),
			'items_list_navigation'      => __( 'keywords list navigation', 'wp-openagenda-pro' ),
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
