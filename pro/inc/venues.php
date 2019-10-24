<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if ( ! function_exists( 'openagenda_venue' ) ) {

// Register Custom Taxonomy
	function openagenda_venue() {

		$labels = array(
			'name'                       => _x( 'Venues', 'Taxonomy General Name', 'wp-openagenda-pro' ),
			'singular_name'              => _x( 'Venue', 'Taxonomy Singular Name', 'wp-openagenda-pro' ),
			'menu_name'                  => __( 'Venues', 'wp-openagenda-pro' ),
			'all_items'                  => __( 'All Venues', 'wp-openagenda-pro' ),
			'parent_item'                => __( 'Parent Venue', 'wp-openagenda-pro' ),
			'parent_item_colon'          => __( 'Parent Venue:', 'wp-openagenda-pro' ),
			'new_item_name'              => __( 'New Venue Name', 'wp-openagenda-pro' ),
			'add_new_item'               => __( 'Add New Venue', 'wp-openagenda-pro' ),
			'edit_item'                  => __( 'Edit Venue', 'wp-openagenda-pro' ),
			'update_item'                => __( 'Update Venue', 'wp-openagenda-pro' ),
			'view_item'                  => __( 'View Venue', 'wp-openagenda-pro' ),
			'separate_items_with_commas' => __( 'Separate Venues with commas', 'wp-openagenda-pro' ),
			'add_or_remove_items'        => __( 'Add or remove Venues', 'wp-openagenda-pro' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'wp-openagenda-pro' ),
			'popular_items'              => __( 'Popular Venues', 'wp-openagenda-pro' ),
			'search_items'               => __( 'Search Venues', 'wp-openagenda-pro' ),
			'not_found'                  => __( 'Not Found', 'wp-openagenda-pro' ),
			'no_terms'                   => __( 'No Venues', 'wp-openagenda-pro' ),
			'items_list'                 => __( 'Venues list', 'wp-openagenda-pro' ),
			'items_list_navigation'      => __( 'Venues list navigation', 'wp-openagenda-pro' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => false,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => false,
			'rewrite'                    => false,
			'show_in_rest'               => true,
		);
		register_taxonomy( 'openagenda_venue', array( 'openagenda-events' ), $args );

	}
	add_action( 'init', 'openagenda_venue', 0 );

}

/*
add_action( 'pre_insert_term', function ( $term, $taxonomy )
{
	return ( 'openagenda_venue' === $taxonomy )
		? new WP_Error( 'term_addition_blocked', __( 'You cannot add terms to this taxonomy', 'wp-openagenda-pro' ) )
		: $term;
}, 0, 2 );
*/
