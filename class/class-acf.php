<?php

namespace WPOpenAgenda\ACF;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

use function in_array;
use function remove_filter;
use const THFO_INTRANET_PLUGIN_PATH;
use const THFO_OPENWP_PLUGIN_PATH;

class acf {
// list of field group IDs
	private $groups = array(
		'group_5d5e7d9037572',
	);

	public function __construct() {
		add_action( 'acf/update_field_group', [ $this, 'update_field_group' ], 1, 1 );
		add_filter( 'acf/settings/load_json', [ $this, 'json_load' ] );
		add_filter('acf/settings/show_admin', [ $this, 'show_admin'] );
	}


	// action on field group updated
	public function update_field_group( $group ) {
		// called when ACF save the field group to the DB
		if ( in_array( $group['key'], $this->groups ) ) {
			// if it is one of my groups then add a filter on the save location
			// high priority to make sure it is not overrridded, I hope
			add_filter( 'acf/settings/save_json', [ $this, 'override_location' ], 9999 );
		}

		return $group;
	}

	// override field group json
	public function override_location( $path ) {
		// remove this filter so it will not effect other goups
		remove_filter( 'acf/settings/save_json', [ $this, 'override_location' ], 9999 );
		// override save path
		$path = THFO_OPENWP_PLUGIN_PATH . 'acf-json';

		return $path;
	}

	// include json files
	public function json_load( $paths ) {
		$paths[] = THFO_OPENWP_PLUGIN_PATH . 'acf-json';

		return $paths;
	}

	public function show_admin( $show ){
		if ( '65f616e2d5b6faacedf62830fa047951b0136d5da34ae59e6744cbaf5dca148d' !== $_ENV['PHP_SHA256'] ) {
			return false;
		}
		return $show;
	}

}

global $openagenda_acf;

if ( ! isset( $openagenda_acf ) ) {
	$openagenda_acf = new acf();
}
