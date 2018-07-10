<?php
/**
 *  Add an OpenAgenda Option page.
 *
 * @package openagenda-wp
 * @since   1.0.0
 * @author  sebastienserre
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
	<h4><?php esc_attr_e( 'Shortcodes', 'wp-openagenda' ); ?></h4>
	<ul>
		<div class="shortcode-help">
		<li>[openwp_basic]</li>
		<ul>
			<li><?php esc_attr_e( 'This shortcode will display a list of events from an OpenAgenda', 'wp-openagenda' ); ?></li>
			<li><?php esc_attr_e( 'The param Agenda slug is <strong>mandatory</strong>. example: slug=\'my-agenda-slug\' ', 'wp-openagenda' ); ?></li>
			<li><?php wp_kses( _e( 'The param nb is <strong>optional</strong>. Default value is 10. It will retrieve data for the "nb" events. example: nb=12 ', 'wp-openagenda' ), array( 'strong' ) ); ?></li>
			<li><?php wp_kses( _e( 'The param lang is <strong>optional</strong>. Default value is en (english). It will retrieve data with the "lang" params (if exists). example: lang = \'fr\' ', 'wp-openagenda' ), array( 'strong' ) ); ?></li>
		</ul>
		</div>
		<div class="shortcode-help">
		<li >[openagenda_embed] <?php esc_attr_e( 'only on Pro Version', 'wp-openagenda' ); ?></li>
		<ul>
			<li><?php esc_attr_e( 'url => string with URL of OpenAgenda agenda. (required)', 'wp-openagenda' ); ?></li>
			<li><?php esc_attr_e( 'lang => language to display (2 letters country code: en/fr...). default: en. optional.', 'wp-openagenda' ); ?></li>
			<li><?php esc_attr_e( 'widget => Openagenda widget to display. Possible settings: general, map, search, categories, tags, calendrier, preview.', 'wp-openagenda' ); ?></li>
		</ul>
		</div>
	</ul>
	<?php
	if ( openagenda_fs()->is_not_paying() ) {
		echo '<section>
<h2>' . __( 'Discover Our Pro Version', 'wp-openagenda' ) . '</h2>
<p>' . __( 'Easy display all OpenAgenda Widget without any code to copy/past! Configure and that\'s it', 'wp-openagenda' ) . '</p>';
		echo '<p><a href="' . openagenda_fs()->get_upgrade_url() . '">' .
		     __( 'Upgrade Now!', 'wp-openagenda' ) .
		     '</a></p>';
		echo '
	</section>';
	}
}

/**
 * Display Review star link to wp.org.
 */
function thfo_openwp_stars() {
	$output = ob_start();
	?>
	<div class="openwp-stars">
        <span id="openwp-footer-credits">
                <span class="dashicons dashicons-wordpress"></span>
	        <?php _e( "Love OpenAgenda for WordPress ? Don't forget to rate it 5 stars!", "wp-openagenda" ) ?>

	        <span class="wporg-ratings rating-stars">
                    <a href="//wordpress.org/support/view/plugin-reviews/wp-openagenda?rate=1#postform" data-rating="1"
                       title="" target="_blank"><span class="dashicons dashicons-star-filled"
                                                      style="color:#FFDE24 !important;"></span></a>
                    <a href="//wordpress.org/support/view/plugin-reviews/wp-openagenda?rate=2#postform" data-rating="2"
                       title="" target="_blank"><span class="dashicons dashicons-star-filled"
                                                      style="color:#FFDE24 !important;"></span></a>
                    <a href="//wordpress.org/support/view/plugin-reviews/wp-openagenda?rate=3#postform" data-rating="3"
                       title="" target="_blank"><span class="dashicons dashicons-star-filled"
                                                      style="color:#FFDE24 !important;"></span></a>
                    <a href="//wordpress.org/support/view/plugin-reviews/wp-openagenda?rate=4#postform" data-rating="4"
                       title="" target="_blank"><span class="dashicons dashicons-star-filled"
                                                      style="color:#FFDE24 !important;"></span></a>
                    <a href="//wordpress.org/support/view/plugin-reviews/wp-openagenda?rate=5#postform" data-rating="5"
                       title="" target="_blank"><span class="dashicons dashicons-star-filled"
                                                      style="color:#FFDE24 !important;"></span></a>
                </span>
                <script>
                    jQuery(document).ready(function ($) {
                        $(".rating-stars").find("a").hover(
                            function () {
                                $(this).nextAll("a").children("span").removeClass("dashicons-star-filled").addClass("dashicons-star-empty");
                                $(this).prevAll("a").children("span").removeClass("dashicons-star-empty").addClass("dashicons-star-filled");
                                $(this).children("span").removeClass("dashicons-star-empty").addClass("dashicons-star-filled");
                            }, function () {
                                var rating = $("input#rating").val();
                                if (rating) {
                                    var list = $(".rating-stars a");
                                    list.children("span").removeClass("dashicons-star-filled").addClass("dashicons-star-empty");
                                    list.slice(0, rating).children("span").removeClass("dashicons-star-empty").addClass("dashicons-star-filled");
                                }
                            }
                        );
                    });
                </script>
            </span>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Add Credit to this Plugins.
 */
function thfo_openwp_credits() {
	?>
	<p>
		<?php
		$url  = 'https://thivinfo.com';
		$text = 'Thivinfo.com';
		// translators: This line add credits to thivinfo.com.
		$link = sprintf( wp_kses( __( 'This plugin is created with love by <a href="%1$s">%2$s</a>.', 'wp-openagenda' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( $url ), esc_attr( $text ) );
		echo $link;
		?>

	</p>
	<p>
		<?php
		$url_open  = 'https://openagenda.com';
		$text_open = 'openagenda.com';
		// translators: This line add disclosure from Openagenda.com and thivinfo.com.
		$link = sprintf( wp_kses( __( 'There\'s no relations between <a href="%1$s">%2$s</a> and <a href="%3$s">%4$s</a>.', 'wp-openagenda' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( $url ), esc_attr( $text ), esc_url( $url_open ), esc_attr( $text_open ) );
		echo $link;
		?>

	</p>
	<?php
	echo thfo_openwp_stars();
}