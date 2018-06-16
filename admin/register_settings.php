<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

add_action( 'admin_menu', 'thfo_openwp_add_menu' );

/**
 * Add an option page
 */
function thfo_openwp_add_menu() {
	add_options_page( __( 'OpenAgenda Settings', 'openagenda-wp' ), __( 'OpenAgenda Settings', 'openagenda-wp' ), 'manage_options', 'openagenda-settings', 'options_page' );
}

/**
 *  Display an option page
 */
function options_page() { ?>
	<h3><?php echo esc_html( get_admin_page_title() ); ?></h3>
	<form method="post" action="options.php">
		<?php settings_fields( 'openagenda-wp' ); ?>
		<?php do_settings_sections( 'openagenda-wp' ); ?>
		<?php submit_button( __( 'Save' ) ); ?>
	</form>

	<?php
}

add_action( 'admin_init', 'thfo_openwp_register_settings' );
/**
 * Register OpenAgenda Settings
 */
function thfo_openwp_register_settings() {
	add_settings_section( 'openagenda-wp', '', '', 'openagenda-wp' );
	register_setting( 'openagenda-wp', 'openagenda_api' );
	add_settings_field( 'openagenda-wp', __( 'API Openagenda', 'openagenda-wp' ), 'thfo_openwp_api', 'openagenda-wp', 'openagenda-wp' );

}

/**
 * Register Openagenda API Key
 */
function thfo_openwp_api() { ?>
	<input type="text" name="openagenda_api" value="<?php echo esc_html( get_option( 'openagenda_api' ) ); ?>"/>
	<p><?php esc_html_e( 'Create an account on OpenAgenda, and go to your setting page to get your API key.', 'openagenda-wp' ); ?></p>
	<?php
}