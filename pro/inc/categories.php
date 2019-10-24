<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if ( ! function_exists( 'openagenda_Category' ) ) {

// Register Custom Taxonomy
	function openagenda_Category() {

		$labels = array(
			'name'                       => _x( 'Categories', 'Taxonomy General Name', 'wp-openagenda-pro' ),
			'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'wp-openagenda-pro' ),
			'menu_name'                  => __( 'Categories', 'wp-openagenda-pro' ),
			'all_items'                  => __( 'All Categories', 'wp-openagenda-pro' ),
			'parent_item'                => __( 'Parent Category', 'wp-openagenda-pro' ),
			'parent_item_colon'          => __( 'Parent Category:', 'wp-openagenda-pro' ),
			'new_item_name'              => __( 'New Category Name', 'wp-openagenda-pro' ),
			'add_new_item'               => __( 'Add New Category', 'wp-openagenda-pro' ),
			'edit_item'                  => __( 'Edit Category', 'wp-openagenda-pro' ),
			'update_item'                => __( 'Update Category', 'wp-openagenda-pro' ),
			'view_item'                  => __( 'View Category', 'wp-openagenda-pro' ),
			'separate_items_with_commas' => __( 'Separate Categories with commas', 'wp-openagenda-pro' ),
			'add_or_remove_items'        => __( 'Add or remove Categories', 'wp-openagenda-pro' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'wp-openagenda-pro' ),
			'popular_items'              => __( 'Popular Categories', 'wp-openagenda-pro' ),
			'search_items'               => __( 'Search Categories', 'wp-openagenda-pro' ),
			'not_found'                  => __( 'Not Found', 'wp-openagenda-pro' ),
			'no_terms'                   => __( 'No Categories', 'wp-openagenda-pro' ),
			'items_list'                 => __( 'Categories list', 'wp-openagenda-pro' ),
			'items_list_navigation'      => __( 'Categories list navigation', 'wp-openagenda-pro' ),
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
