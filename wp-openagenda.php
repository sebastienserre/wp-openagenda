<?php
/**
 * Plugin Name: WP Openagenda
 * Plugin URI: https://github.com/sebastienserre/wp-openagenda
 * Description: Easily display an OpenAgenda.com in your WordPress website
 * Version: 2.6
 * Author: Sébastien Serre
 * Author URI: http://thivinfo.com
 * Tested up to: 6.0
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
		define( 'THFO_OPENWP_VERSION', '2.6' );
		define( 'THFO_OPENWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		define( 'THFO_OPENWP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		define( 'THFO_OPENWP_PLUGIN_DIR', untrailingslashit( THFO_OPENWP_PLUGIN_PATH ) );
		define( 'THFO_OPENWP_CUST_INC', THFO_OPENWP_PLUGIN_PATH . 'inc/' );


		/**
		 * Actions
		 */
		add_action( 'plugins_loaded', array( $this, 'thfo_openwp_load_files' ), 999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'thfo_openwp_load_style' ) );
		add_action( 'admin_print_styles', array( $this, 'openwp_load_admin_style' ) );
		add_action( 'plugins_loaded', array( $this, 'openwp_load' ), 400 );
		add_action( 'wp_enqueue_scripts', array( $this, 'openwp_pro_load_style__premium_only' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'openwp_register_script__premium_only' ) );
		add_action( 'plugins_loaded', array( $this, 'openwp_load_acf' ) );

		/**
		 * Filters
		 */
	//

		register_activation_hook( __FILE__, array( $this, 'openwp_activation__premium_only' ) );
	}

	public function openwp_activation__premium_only() {
		if ( ! wp_next_scheduled( 'openagenda_hourly_event' ) ) {
			wp_schedule_event( time(), 'hourly', 'openagenda_hourly_event' );
		}
	}

	/**
	 * Load Carbon-field v3
	 */
	public
	function openwp_load() {
		require_once THFO_OPENWP_PLUGIN_PATH . '3rd-party/vendor/autoload.php';
		\Carbon_Fields\Carbon_Fields::boot();
	}


	/**
	 * Include all files needed to the plugin work
	 */
	public
	function thfo_openwp_load_files() {

		include_once THFO_OPENWP_CUST_INC . '1-helpers.php';
		include_once THFO_OPENWP_CUST_INC . 'acf-fields.php';
		include_once THFO_OPENWP_CUST_INC . 'agenda.php';
		include_once THFO_OPENWP_CUST_INC . 'categories.php';
		include_once THFO_OPENWP_CUST_INC . 'class-import-oa.php';
		include_once THFO_OPENWP_CUST_INC . 'cpt.php';
		include_once THFO_OPENWP_CUST_INC . 'custom-fields.php';
		include_once THFO_OPENWP_CUST_INC . 'keywords.php';
		include_once THFO_OPENWP_CUST_INC . 'venues.php';



		include_once THFO_OPENWP_PLUGIN_PATH . 'admin/register-settings.php';

		include_once THFO_OPENWP_PLUGIN_PATH . 'class/class-openagendaapi.php';
		include_once THFO_OPENWP_PLUGIN_PATH . 'class/class-openagenda-wp-basic-widget.php';

		include_once THFO_OPENWP_PLUGIN_PATH . 'shortcodes/class-openagenda-shortcode.php';
		include_once THFO_OPENWP_PLUGIN_PATH . 'shortcodes/sc-main-agenda.php';
		include_once THFO_OPENWP_PLUGIN_PATH . 'shortcodes/class-openagenda-embed-shortcode.php';
		include_once THFO_OPENWP_PLUGIN_PATH . 'shortcodes/class-openagendaslidershortcode.php';
		include_once THFO_OPENWP_PLUGIN_PATH . 'shortcodes/class-openagenda-search-shortcode.php';
		include_once THFO_OPENWP_PLUGIN_PATH . 'shortcodes/class-openagenda-tec-shortcode.php';

		include_once THFO_OPENWP_PLUGIN_PATH . '3rd-party/vendor/erusev/parsedown/Parsedown.php';

		include_once THFO_OPENWP_PLUGIN_PATH . 'blocks/class-basicblock.php';
		include_once THFO_OPENWP_PLUGIN_PATH . 'blocks/class-mainagendablock.php';
		include_once THFO_OPENWP_PLUGIN_PATH . 'blocks/class-openwp-block-embed.php';
		include_once THFO_OPENWP_PLUGIN_PATH . 'blocks/class-openwp-agenda-list.php';

		include_once THFO_OPENWP_PLUGIN_PATH . 'views/main-agenda.php';

		include_once THFO_OPENWP_PLUGIN_PATH . 'widget/class-openagenda-main-widget.php';
		include_once THFO_OPENWP_PLUGIN_PATH . 'widget/class-openagenda-slider-widget.php';

		if ( class_exists( 'Vc_Manager' ) ) {
			include_once THFO_OPENWP_PLUGIN_PATH . '/vc/openagenda-vc-main.php';
			include_once THFO_OPENWP_PLUGIN_PATH . '/vc/class-vc-events.php';
			include_once THFO_OPENWP_PLUGIN_PATH . '/vc/class-openagenda-slider.php';
			include_once THFO_OPENWP_PLUGIN_PATH . '/vc/class-openagenda-search.php';
		}

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

	public function my_acf_settings_url__premium_only( $url ) {
		return MY_ACF_URL;
	}

	public function my_acf_settings_show_admin__premium_only( $show_admin ) {
		return false;
	}


	public
	function openwp_register_script__premium_only() {
		wp_register_script( 'dateOA', THFO_OPENWP_PLUGIN_URL . 'assets/js/datepickerOA.js',
			array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-datepicker',
			)
		);
		wp_enqueue_style( 'openwp-pro', THFO_OPENWP_PLUGIN_URL . 'assets/css/openwp-pro.css', array( 'slickthemecss' ) );
		wp_register_script( 'IsotopeOA', THFO_OPENWP_PLUGIN_URL . 'assets/js/isotope.pkgd.min.js',
			array(
				'jquery',
			)
		);
		wp_register_script( 'IsotopeInit', THFO_OPENWP_PLUGIN_URL . 'assets/js/isotope-init.js',
			array(
				'IsotopeOA',
			)
		);
	}

	/**
	 * Load light style CSS
	 */
	public
	function openwp_pro_load_style__premium_only() {
		wp_enqueue_style( 'jquery-ui-dp', THFO_OPENWP_PLUGIN_URL . 'assets/css/jquery-ui.min.css' );
	}

	public function openwp_load_acf(){
		// do not load if already activated standalone
		add_filter( 'acf/settings/show_admin', '__return_false' );
		if ( class_exists( 'ACF') ){
			return;
		}
		define( 'MY_ACF_PATH', THFO_OPENWP_PLUGIN_PATH . '3rd-party/acf/' );
		define( 'MY_ACF_URL', THFO_OPENWP_PLUGIN_URL . '3rd-party/acf/' );
		add_filter( 'acf/settings/url', [ $this, 'my_acf_settings_url__premium_only' ] );


		include_once MY_ACF_PATH . 'acf.php';
	}
}

new Openagenda_WP_Main();

