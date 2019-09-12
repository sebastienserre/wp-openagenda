<?php

namespace OpenAgenda\TEC;

use function add_action;
use function class_exists;
use function esc_attr;
use function esc_attr_e;
use function var_dump;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

class The_Event_Calendar {

	public static $tec_option;
	public static $tec_activated;

	public function __construct() {
		self::$tec_option    = self::tec_option_getter();
		self::$tec_activated = self::tec_activated_getter();

		add_action( 'admin_notices', [ $this, 'tec_notices' ] );
	}

	/**
	 * @return bool
	 * @author  Sébastien SERRE
	 * @package wp-openagenda
	 * @since
	 */
	public function tec_activated_getter() {
		if ( class_exists( 'Tribe__Events__Main' ) ){
			return true;
		}
		return false;
	}

	public function tec_option_getter() {
		$tec = get_option( 'openagenda-tec' );
		if ( 'yes' !== $tec ) {
			return false;
		}
		return true;
	}

	public function tec_notices() {
		if ( true === self::$tec_option && false === self::$tec_activated ) {
			?>
			<div class="notice notice-warning is-dismissible">
				<p>
					<?php
					esc_attr_e( 'You checked you\'re using The Event Calendar in Openagenda\'s settings but this plugin is not activated', 'wp-openagenda' );
					?>
				</p>
			</div>
			<?php
		}
	}

}
new The_Event_Calendar();
