<?php
/**
 * Plugin Name: WP Openagenda
 * Plugin URI: https://openagenda4wp.com/
 * Description: Easily display an OpenAgenda.com in your WordPress website
 * Version: 1.9.2
 * Author: Sébastien Serre
 * Author URI: http://www.thivinfo.com
 * Tested up to: 5.3
 * Text Domain: wp-openagenda
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

		/**
		 * Define Constant
		 */
		define( 'THFO_OPENWP_VERSION', '1.9.2' );
		define( 'THFO_OPENWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		define( 'THFO_OPENWP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		define( 'THFO_OPENWP_PLUGIN_DIR', untrailingslashit( THFO_OPENWP_PLUGIN_PATH ) );
		define( 'OPENWP_LINK', 'https://thivinfo.com/extensions-wordpress/openagenda-pour-wordpress/' );
		define( 'OPENWP_PLUGIN_PRICE', '49€' );

		// Update
		define( 'THFO_CONSUMER_KEY', 'ck_f7ad715df821fa3ad0fff47d7fc099a24fa385e6' );
		define( 'THFO_CONSUMER_SECRET', 'cs_5dff11320385b6ba8c8bee647a29b0f4ebdef284' );
		define( 'WP_MAIN_FILE_PLUGIN_PATH', __FILE__ );
		define( 'WP_PLUGIN_ID', '2186460' );

		/**
		 * Load Files
		 */
		add_action( 'plugins_loaded', array( $this, 'thfo_openwp_load_files' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'thfo_openwp_load_style' ) );
		add_action( 'admin_print_styles', array( $this, 'openwp_load_admin_style' ) );
		add_action( 'plugins_loaded', array( $this, 'openwp_load' ), 400 );
	}

	/**
	 * Load 3rd party
	 */
	public
	function openwp_load() {
		require_once THFO_OPENWP_PLUGIN_PATH . '/3rd-party/vendor/autoload.php';
	}


	/**
	 * Include all files needed to the plugin work
	 */
	public
	function thfo_openwp_load_files() {
		include_once THFO_OPENWP_PLUGIN_PATH . '/inc/helpers.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/inc/market.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/admin/register-settings.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/class/class-openagendaapi.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/shortcodes/class-openagenda-shortcode.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/shortcodes/sc-main-agenda.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/3rd-party/vendor/erusev/parsedown/Parsedown.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/class/class-openagenda-wp-basic-widget.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/views/main-agenda.php';

	}

	/**
	 * Load light style CSS
	 */
	public
	function thfo_openwp_load_style() {
		wp_enqueue_style( 'openwp', THFO_OPENWP_PLUGIN_URL . 'assets/css/openwp.css' );
	}

	/**
	 * Load Admin Styles.
	 */
	public
	function openwp_load_admin_style() {
		wp_enqueue_style( 'openawp-admin-style', THFO_OPENWP_PLUGIN_URL . 'admin/assets/openwp-admin-styles.css' );
	}
}

new Openagenda_WP_Main();
