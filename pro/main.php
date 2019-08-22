<?php

namespace OPENWP\Pro\Main;

use function basename;
use function class_exists;
use function dirname;
use function load_plugin_textdomain;
use function wp_enqueue_style;
use function wp_register_script;
use const __FILE__;
use const THFO_OPENWP_PLUGIN_PATH;
use const THFO_OPENWP_PLUGIN_URL;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.


class Main {

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'load_files'] );
		add_action( 'wp_enqueue_scripts', array( $this, 'openwp_pro_load_style__premium_only' ) );
		add_action( 'plugins_loaded', array( $this, 'openwp_load_textdomain__premium_only' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'openwp_register_script__premium_only' ), 999 );
		add_action( 'admin_print_styles', array( $this, 'openwp_admin_style__premium_only') );
	}

	public function load_files(){
		/**
		 * If Visual Composer activated, load VC elements.
		 */
		if ( class_exists( 'Vc_Manager' ) ) {
			include_once THFO_OPENWP_PLUGIN_PATH . '/pro/vc/openagenda-vc-main.php';
			include_once THFO_OPENWP_PLUGIN_PATH . '/pro/vc/class-vc-events.php';
			include_once THFO_OPENWP_PLUGIN_PATH . '/pro/vc/class-openagenda-slider.php';
			include_once THFO_OPENWP_PLUGIN_PATH . '/pro/vc/class-openagenda-search.php';
		}

		include_once THFO_OPENWP_PLUGIN_PATH . '/pro/admin/settings.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/pro/inc/cpt.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/pro/inc/venues.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/pro/inc/keywords.php';
		//include_once THFO_OPENWP_PLUGIN_PATH . '/pro/inc/custom-fields.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/pro/inc/agenda.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/pro/widget/class-openagenda-main-widget.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/pro/widget/class-openagenda-slider-widget.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/pro/shortcodes/class-openagenda-embed-shortcode.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/pro/shortcodes/class-openagendaslidershortcode.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/pro/shortcodes/class-openagenda-search-shortcode.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/pro/blocks/class-openwp-block-embed.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/pro/cronjob.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/pro/inc/helpers.php';
		include_once THFO_OPENWP_PLUGIN_PATH . '/pro/blocks/class-openwp-agenda-list.php';
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @since 1.0.0
	 */
	public
	function openwp_load_textdomain__premium_only() {
		load_plugin_textdomain( 'wp-openagenda', false, basename( dirname( __FILE__ ) ) . '/pro/languages' );
	}

	public
	function openwp_register_script__premium_only() {
		wp_register_script( 'dateOA', THFO_OPENWP_PLUGIN_URL . 'pro/assets/js/datepickerOA.js',
			array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-datepicker',
			)
		);
		wp_enqueue_style( 'openwp-pro', THFO_OPENWP_PLUGIN_URL . 'pro/assets/css/openwp-pro.css', array('slickthemecss') );
		wp_register_script( 'IsotopeOA', THFO_OPENWP_PLUGIN_URL . 'pro/assets/js/isotope.pkgd.min.js',
			array(
				'jquery',
			)
		);
		wp_register_script( 'IsotopeInit', THFO_OPENWP_PLUGIN_URL . 'pro/assets/js/isotope-init.js',
			array(
				'IsotopeOA',
			)
		);
	}

	public function openwp_admin_style__premium_only(){
		wp_enqueue_style( 'openwp-pro', THFO_OPENWP_PLUGIN_URL . 'pro/assets/css/openwp-pro.css' );
	}

	/**
	 * Load light style CSS
	 */
	public
	function openwp_pro_load_style__premium_only() {
		wp_enqueue_style( 'jquery-ui-dp', THFO_OPENWP_PLUGIN_URL . 'pro/assets/css/jquery-ui.min.css' );
	}
}
new namespace\Main();