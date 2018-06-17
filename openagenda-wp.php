<?php
/**
 * Plugin Name: Openagenda WP
 * Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
 * Description: Easily display an OpenAgenga.com in your WordPress website
 * Version: 1.0
 * Author: Sébastien Serre
 * Author URI: http://www.thivinfo.com
 * Tested up to: 4.9
 * Text Domain: openagenda-wp
 * License: GPLv3

 * @package openagenda-wp
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Class Openagenda_WP_Main
 */
class Openagenda_WP_Main {

	/**
	 * Openagenda_WP_Main constructor.
	 */
	public function __construct() {

		/**
		 * Define Constant
		 */
		define( 'THFO_OPENWP_VERSION', '1.0.0' );
		define( 'THFO_OPENWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		define( 'THFO_OPENWP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		define( 'THFO_OPENWP_PLUGIN_DIR', untrailingslashit( THFO_OPENWP_PLUGIN_PATH ) );
		/**
		 * Load Files
		 */
		add_action( 'plugins_loaded', array( $this, 'thfo_openwp_load_files' ) );

	}

	/**
	 * Include all files needed to the plugin work
	 */
	public function thfo_openwp_load_files() {
		include_once THFO_OPENWP_PLUGIN_PATH . '/admin/register-settings.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/class/class-openagendaapi.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/class/class-openagenda-shortcode.php';
	}
}
new Openagenda_WP_Main();
