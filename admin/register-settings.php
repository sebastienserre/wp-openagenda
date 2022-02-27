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
function thfo_openwp_options_page() {
	$tabs = array(
		'general' => __( 'General', 'wp-openagenda' ),
		'help'    => __( 'Help', 'wp-openagenda' ),
	);
	$tabs = apply_filters( 'openwp_settings_tabs', $tabs );

	if ( isset( $_GET['tab'] ) ) {

		$active_tab = $_GET['tab'];

	} else {
		$active_tab = 'general';
	}

	$class = 'wrap-premium';

	?>
	<div class="wrap <?php echo $class ?>">
		<h3><?php echo esc_html( get_admin_page_title() . ' ' . THFO_OPENWP_VERSION ); ?></h3>
		<?php settings_errors(); ?>

		<h2 class="nav-tab-wrapper">
			<?php
			foreach ( $tabs as $tab => $value ) {
				?>
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=openagenda-settings&tab=' . $tab ) ); ?>"
				   class="nav-tab <?php echo 'nav-tab-' . $tab;
				   echo $active_tab === $tab ? ' nav-tab-active' : ''; ?>"><?php echo $value ?></a>
			<?php } ?>
		</h2>
		<form method="post" action="options.php">
			<?php $active_tab = apply_filters( 'openwp_setting_active_tab', $active_tab ); ?>
			<?php
			switch ( $active_tab ) {
				case 'general':
                default:
					settings_fields( 'openagenda-wp' );
					do_settings_sections( 'openagenda-wp' );
					break;
				case 'help':
					settings_fields( 'openagenda-wp-help' );
					do_settings_sections( 'openagenda-wp-help' );
					break;
			}
			submit_button( __( 'Save' ) );
			?>
		</form>
        <?php do_action( 'openagenda_after_settings' );?>
	</div>
	<?php
    do_action( 'openagenda_after_settings_wrap' );
}

add_action( 'admin_init', 'thfo_openwp_register_settings' );
/**
 * Register OpenAgenda Settings
 */
function thfo_openwp_register_settings() {
	add_settings_section( 'openagenda-wp-help', __( 'Help Center', 'wp-openagenda' ), 'thfo_openwp_help', 'openagenda-wp-help' );
	add_settings_section( 'openagenda-wp', '', '', 'openagenda-wp' );
	register_setting( 'openagenda-wp', 'openagenda_api' );
	register_setting('openagenda-wp', 'openagenda_uid');
	add_settings_field( 'openagenda-wp', __( 'API Openagenda', 'wp-openagenda' ), 'thfo_openwp_api', 'openagenda-wp', 'openagenda-wp' );
	add_settings_field('openagenda-uid', __('Agenda UID','wp-openagenda'), 'vc_openagenda_uid', 'openagenda-wp', 'openagenda-wp');

}

function vc_openagenda_uid(){
	?>
    <input type="text" name="openagenda_uid" value="<?php echo get_option('openagenda_uid')?>"/>
    <p><?php _e('Find your UID key in the bottom right of your Openagenda page.','wp-openagenda') ?></p>
	<?php
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
	do_action( 'openagenda_check_api' );
}

function thfo_openwp_help() {

	$support_link = 'https://www.thivinfo.com/soumettre-un-ticket/';
	$support      = sprintf( wp_kses( __( 'If you encounter a bug, you can leave me a ticket on <a href="%1$s" target="_blank">Thivinfo.com</a>', 'wp-openagenda' ), array(
		'a' => array(
			'href'   => array(),
			'target' => array()
		)
	) ), esc_url( $support_link ) );
	?>
	<p><?php _e( 'Welcome on the support center', 'wp-openagenda' ); ?></p>
	<p><?php echo $support; ?></p>
	<p>
		<a href="https://docs.thivinfo.com/collection/5-openagenda-pour-wordpress"><?php _e( 'Documentation Center', 'wp-openagenda' ); ?></a>
	</p>
	<?php
	if ( openagenda_fs()->is_not_paying() ) {
		echo '<section class="pro-pub">
<h2>' . __( 'Discover Our Pro Version', 'wp-openagenda' ) . '</h2>
<p>' . __( 'Easy display all OpenAgenda Widget without any code to copy/past! Configure and that\'s it', 'wp-openagenda' ) . '</p>';
		echo '<p><a class="upgrade_button" href="' . openagenda_fs()->get_upgrade_url() . '">' .
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
add_action( 'openagenda_after_settings', 'thfo_openwp_credits');
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

add_action( 'admin_init', 'openwp_pro_register_settings', 30 );
function openwp_pro_register_settings() {

	register_setting( 'openagenda-wp', 'openagenda_secret' );
	add_settings_field( 'openagenda-wp-secret', __( 'Secret Key Openagenda', 'wp-openagenda' ), 'openwp_oa_secret', 'openagenda-wp', 'openagenda-wp' );
	add_settings_field( 'openagenda-wp-sync', __( 'Force Import', 'wp-openagenda' ), 'openwp_oa_sync', 'openagenda-wp',
		'openagenda-wp' );

	//add_settings_section( 'openagenda-wp-3rd', __( 'The Event Calendar', 'wp-openagenda' ), '', 'openagenda-wp-3rd' );
	register_setting( 'openagenda-wp', 'openagenda-tec' );

}

function openwp_oa_sync() {
	$url = wp_nonce_url(
		add_query_arg(
			[
				'oaimport' => 'now',
			],
			admin_url()
		),
		'force_sync',
		'_wpnonce'
	);
	?>
    <a href="<?php echo esc_url( $url ); ?>"><?php esc_attr_e( 'Import Agenda Now', 'wp-openagenda' ); ?></a>
	<?php
}

function openwp_oa_secret() {
	?>
    <input type="text" name="openagenda_secret" value="<?php echo esc_html( get_option( 'openagenda_secret' ) ); ?>"/>
	<?php
	$allowed_html = array(
		'a' => array(
			'href' => array(),
			'p'    => array(),
		),
		'p' => array(),

	);
	$link         = antispambot( 'support@openagenda.com' );
	$body         = __( 'Hello, Could you please activate my Secret Key ?', 'wp-openagenda' );
	$url          = 'mailto:' . $link . '?subject=' . __( 'Secret Key Activation', 'wp-openagenda' ) . '&body=' . $body; ?>
	<?php // translators: Add the OpenAGenda URL.
	?>
    <p><?php printf( wp_kses( __( 'Send a mail to <a href="%s" >OpenAgenda</a>, and ask them to activate your secret key.', 'wp-openagenda' ), $allowed_html ), esc_url( $url ) ); ?></p>
	<?php
	do_action( 'openagenda_after_secret' );
}
