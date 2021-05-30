<?php

Namespace Openagenda;

use function add_action;
use function define;
use function is_file;
use function plugin_dir_path;
use function plugin_dir_url;
use function scandir;
use function untrailingslashit;
use const THFO_INTRANET_PLUGIN_PATH;
use const THFO_OPENWP_PLUGIN_PATH;
use const THFO_OPENWP_PLUGIN_URL;

/**
 * Plugin Name: WP Openagenda
 * Plugin URI: https://github.com/sebastienserre/wp-openagenda
 * Description: Easily display an OpenAgenda.com in your WordPress website
 * Version: 3.0
 * Author: Sébastien Serre
 * Author URI: http://www.thivinfo.com
 * Tested up to: 5.6
 * License: GPLv3
 *
 * @package         openagenda-wp
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
		add_action( 'plugins_loaded', array( $this, 'define_constant' ) );
		add_action( 'plugins_loaded', array( $this, 'load_files' ) );
	}

	function define_constant(){
		/**
		 * Define Constant
		 */
		define( 'THFO_OPENWP_VERSION', '3.0' );
		define( 'THFO_OPENWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		define( 'THFO_OPENWP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		define( 'THFO_OPENWP_PLUGIN_DIR', untrailingslashit( THFO_OPENWP_PLUGIN_PATH ) );
		define( 'THFO_OPENWP_CUST_INC', THFO_OPENWP_PLUGIN_PATH . 'inc/' );
		define( 'MY_ACF_PATH', THFO_OPENWP_PLUGIN_PATH . '/3rd-party/vendor/advanced-custom-fields/' );
		define( 'MY_ACF_URL', THFO_OPENWP_PLUGIN_URL . '/3rd-party/vendor/advanced-custom-fields/' );
	}

	function load_files(){
		$files = scandir( THFO_OPENWP_CUST_INC );

		//Load files
		foreach ( $files as $file ) {
			if ( is_file( THFO_OPENWP_CUST_INC . $file ) ) {
				require_once THFO_OPENWP_CUST_INC . $file;
			}
		}
	}

}

$openagenda = new Openagenda_WP_Main();

