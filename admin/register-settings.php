<?php
/**
 *  Add an OpenAgenda Option page.

 * @package openagenda-wp
 * @since 1.0.0
 * @author sebastienserre
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

add_action( 'admin_menu', 'thfo_openwp_add_menu' );

/**
 * Add an option page
 */
function thfo_openwp_add_menu() {
	add_options_page( __( 'OpenAgenda WP', 'wp-openagenda' ), __( 'OpenAgenda Settings', 'wp-openagenda' ), 'manage_options', 'openagenda-settings', 'thfo_openwp_options_page' );
}

/**
 *  Display an option page
 */
function thfo_openwp_options_page() { ?>
	<h3><?php echo esc_html( get_admin_page_title() . ' ' . THFO_OPENWP_VERSION ); ?></h3>
	<form method="post" action="options.php">
		<?php settings_fields( 'openagenda-wp' ); ?>
		<?php do_settings_sections( 'openagenda-wp' ); ?>
		<?php submit_button( __( 'Save' ) ); ?>
		<?php do_settings_sections( 'openagenda-wp-help' ); ?>
		<?php do_settings_sections( 'openagenda-wp-credits' ); ?>
	</form>

	<?php
}

add_action( 'admin_init', 'thfo_openwp_register_settings' );
/**
 * Register OpenAgenda Settings
 */
function thfo_openwp_register_settings() {
	add_settings_section( 'openagenda-wp-help', __( 'Help Center', 'wp-openagenda' ), 'thfo_openwp_help', 'openagenda-wp-help' );
	add_settings_section( 'openagenda-wp', '', '', 'openagenda-wp' );
	add_settings_section( 'openagenda-wp-credits', __( 'Credits', 'wp-openagenda' ), 'thfo_openwp_credits', 'openagenda-wp-credits' );
	register_setting( 'openagenda-wp', 'openagenda_api' );
	add_settings_field( 'openagenda-wp', __( 'API Openagenda', 'wp-openagenda' ), 'thfo_openwp_api', 'openagenda-wp', 'openagenda-wp' );

}

/**
 * Register Openagenda API Key
 */
function thfo_openwp_api() {
	?>
	<input type="text" name="openagenda_api" value="<?php echo esc_html( get_option( 'openagenda_api' ) ); ?>"/>
	<?php $url = esc_url( 'https://openagenda.com' ); ?>
	<?php // translators: Add the OpenAGenda URL. ?>
	<p><?php printf( wp_kses( __( 'Create an account on <a href="%s" target="_blank">OpenAgenda</a>, and go to your setting page to get your API key.', 'wp-openagenda' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( $url ) ); ?></p>
	<?php
}

function thfo_openwp_help() {
	?>
	<h4><?php _e( 'Shortcodes', 'wp-openagenda' ); ?></h4>
	<ul>
		<li>[openwp_basic]</li>
		<ul>
			<li><?php _e( 'This shortcode will display a list of events from an OpenAgenda', 'wp-openagenda' ); ?></li>
			<li><?php _e( 'The param Agenda slug is <strong>mandatory</strong>. example: slug=\'my-agenda-slug\' ', 'wp-openagenda' ); ?></li>
			<li><?php _e( 'The param nb is <strong>optional</strong>. Default value is 10. It will retrieve data for the "nb" events. example: nb=12 ', 'wp-openagenda' ); ?></li>
			<li><?php _e( 'The param lang is <strong>optional</strong>. Default value is en (english). It will retrieve data with the "lang" params (if exists). example: lang = \'fr\' ', 'wp-openagenda' ); ?></li>
		</ul>
	</ul>
<?php
}

function thfo_openwp_credits() {
	?>
	<p><?php _e('This plugin is created with love by ', 'wp-openagenda'); ?><a href="https://thivinfo.com">Thivinfo.com</a></p>
<?php
}
