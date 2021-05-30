<?php
namespace Openagenda\Acf;

use function add_action;
use function defined;
use const WP_DEBUG;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

add_action( 'plugins_loaded', 'Openagenda\Acf\load_acf', 15 );
function load_acf(){
	include_once( MY_ACF_PATH . 'acf.php' );
}

// Customize the url setting to fix incorrect asset URLs.
add_filter('acf/settings/url', 'Openagenda\Acf\acf_settings_url');
function acf_settings_url( $url ) {
	return MY_ACF_URL;
}

// (Optional) Hide the ACF admin menu item.
add_filter('acf/settings/show_admin', 'Openagenda\Acf\acf_settings_show_admin');
function acf_settings_show_admin( $show_admin ) {
	if ( defined( 'WP_DEBUG') && false === WP_DEBUG ) {
		return false;
	}
	return true;
}