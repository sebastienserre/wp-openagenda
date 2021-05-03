<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if ( ! function_exists('venues') ) {

// Register Custom Post Type
	function venues() {

		$labels = array(
			'name'                  => _x( 'Venues', 'Post Type General Name', 'wp-openagenda' ),
			'singular_name'         => _x( 'Venue', 'Post Type Singular Name', 'wp-openagenda' ),
			'menu_name'             => __( 'Venue', 'wp-openagenda' ),
			'name_admin_bar'        => __( 'Venue', 'wp-openagenda' ),
			'archives'              => __( 'Item Archives', 'wp-openagenda' ),
			'attributes'            => __( 'Item Attributes', 'wp-openagenda' ),
			'parent_item_colon'     => __( 'Parent Item:', 'wp-openagenda' ),
			'all_items'             => __( 'All Items', 'wp-openagenda' ),
			'add_new_item'          => __( 'Add New Item', 'wp-openagenda' ),
			'add_new'               => __( 'Add New', 'wp-openagenda' ),
			'new_item'              => __( 'New Item', 'wp-openagenda' ),
			'edit_item'             => __( 'Edit Item', 'wp-openagenda' ),
			'update_item'           => __( 'Update Item', 'wp-openagenda' ),
			'view_item'             => __( 'View Item', 'wp-openagenda' ),
			'view_items'            => __( 'View Items', 'wp-openagenda' ),
			'search_items'          => __( 'Search Item', 'wp-openagenda' ),
			'not_found'             => __( 'Not found', 'wp-openagenda' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wp-openagenda' ),
			'featured_image'        => __( 'Featured Image', 'wp-openagenda' ),
			'set_featured_image'    => __( 'Set featured image', 'wp-openagenda' ),
			'remove_featured_image' => __( 'Remove featured image', 'wp-openagenda' ),
			'use_featured_image'    => __( 'Use as featured image', 'wp-openagenda' ),
			'insert_into_item'      => __( 'Insert into item', 'wp-openagenda' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'wp-openagenda' ),
			'items_list'            => __( 'Items list', 'wp-openagenda' ),
			'items_list_navigation' => __( 'Items list navigation', 'wp-openagenda' ),
			'filter_items_list'     => __( 'Filter items list', 'wp-openagenda' ),
		);
		$args = array(
			'label'                 => __( 'Venue', 'wp-openagenda' ),
			'description'           => __( 'Event Venue', 'wp-openagenda' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'thumbnail' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-admin-site-alt2',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		register_post_type( 'venue', $args );

	}
	add_action( 'init', 'venues', 0 );

}
