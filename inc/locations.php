<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if ( ! function_exists('location') ) {

// Register Custom Post Type
	function location() {

		$labels = array(
			'name'                  => _x( 'Locations', 'Post Type General Name', 'wp-openagenda' ),
			'singular_name'         => _x( 'Location', 'Post Type Singular Name', 'wp-openagenda' ),
			'menu_name'             => __( 'Location', 'wp-openagenda' ),
			'name_admin_bar'        => __( 'Location', 'wp-openagenda' ),
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
			'label'                 => __( 'Location', 'wp-openagenda' ),
			'description'           => __( 'Event Location', 'wp-openagenda' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'thumbnail', 'editor' ),
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
		register_post_type( 'Location', $args );

	}
	add_action( 'init', 'location', 0 );

}

add_action( 'add_meta_boxes', 'oa_Location_metabox' );
function oa_Location_metabox() {
	global $post;
	if ( 'Location' === get_post_type( $post->ID ) ) {
		add_meta_box( 'oa_event_id', 'UID', 'oa_Location_id', '', 'side', 'high' );
	}
}

function oa_Location_id() {
	global $post;
	$event_id = get_post_meta( $post->ID, 'oa_location_uid', true );
	if ( $event_id ){
		echo '<p>' . $event_id . '</p>';
	}
}
