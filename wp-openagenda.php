<?php
/**
 * Plugin Name: WP Openagenda
 * Plugin URI: https://www.thivinfo.com/blog/afficher-facilement-vos-agendas-openagenda-com-sur-votre-site-wordpress-avec-openagenda-pour-wordpress/
 * Description: Easily display an OpenAgenga.com in your WordPress website
 * Version: 1.2.3
 * Author: Sébastien Serre
 * Author URI: http://www.thivinfo.com
 * Tested up to: 4.9
 * Text Domain: wp-openagenda
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
		define( 'THFO_OPENWP_VERSION', '1.2.3' );
		define( 'THFO_OPENWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		define( 'THFO_OPENWP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		define( 'THFO_OPENWP_PLUGIN_DIR', untrailingslashit( THFO_OPENWP_PLUGIN_PATH ) );
		/**
		 * Load Files
		 */
		add_action( 'plugins_loaded', array( $this, 'thfo_openwp_load_files' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'thfo_openwp_load_style' ) );

	}

	/**
	 * Include all files needed to the plugin work
	 */
	public function thfo_openwp_load_files() {
		include_once THFO_OPENWP_PLUGIN_PATH . '/admin/register-settings.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/class/class-openagendaapi.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/class/class-openagenda-shortcode.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/3rd-party/parsedown/Parsedown.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/class/class-openwpbasicwidget.php';

		if ( class_exists( 'Vc_Manager' ) ) {
			include_once THFO_OPENWP_PLUGIN_PATH . '/inc/visual-composer/class-vc-events.php';
		}
	}

	/**
	 * Load light style CSS
	 */
	public function thfo_openwp_load_style() {
		wp_enqueue_style( 'openwp', THFO_OPENWP_PLUGIN_URL . 'assets/css/openwp.css' );
	}
}
new Openagenda_WP_Main();
