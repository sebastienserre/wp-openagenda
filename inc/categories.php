<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if ( ! function_exists( 'openagenda_Category' ) ) {

// Register Custom Taxonomy
	function openagenda_Category() {

		$labels = array(
			'name'                       => _x( 'Categories', 'Taxonomy General Name', 'wp-openagenda' ),
			'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'wp-openagenda' ),
			'menu_name'                  => __( 'Categories', 'wp-openagenda' ),
			'all_items'                  => __( 'All Categories', 'wp-openagenda' ),
			'parent_item'                => __( 'Parent Category', 'wp-openagenda' ),
			'parent_item_colon'          => __( 'Parent Category:', 'wp-openagenda' ),
			'new_item_name'              => __( 'New Category Name', 'wp-openagenda' ),
			'add_new_item'               => __( 'Add New Category', 'wp-openagenda' ),
			'edit_item'                  => __( 'Edit Category', 'wp-openagenda' ),
			'update_item'                => __( 'Update Category', 'wp-openagenda' ),
			'view_item'                  => __( 'View Category', 'wp-openagenda' ),
			'separate_items_with_commas' => __( 'Separate Categories with commas', 'wp-openagenda' ),
			'add_or_remove_items'        => __( 'Add or remove Categories', 'wp-openagenda' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'wp-openagenda' ),
			'popular_items'              => __( 'Popular Categories', 'wp-openagenda' ),
			'search_items'               => __( 'Search Categories', 'wp-openagenda' ),
			'not_found'                  => __( 'Not Found', 'wp-openagenda' ),
			'no_terms'                   => __( 'No Categories', 'wp-openagenda' ),
			'items_list'                 => __( 'Categories list', 'wp-openagenda' ),
			'items_list_navigation'      => __( 'Categories list navigation', 'wp-openagenda' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => false,
			'show_ui'                    => true,
			'show_admin_column'          => false,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => false,
			'rewrite'                    => false,
			'show_in_rest'               => false,
		);
		register_taxonomy( 'openagenda_Category', array( 'openagenda-events' ), $args );

	}
	add_action( 'init', 'openagenda_Category', 0 );

}
