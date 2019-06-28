<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

add_action( 'admin_init', 'openwp_pro_register_settings', 30);
function openwp_pro_register_settings(){

	register_setting( 'openagenda-wp', 'openagenda_secret' );
	add_settings_field( 'openagenda-wp-secret', __( 'Secret Key Openagenda', 'wp-openagenda' ), 'openwp_oa_secret', 'openagenda-wp', 'openagenda-wp' );

	//add_settings_section( 'openagenda-wp-3rd', __( 'The Event Calendar', 'wp-openagenda' ), '', 'openagenda-wp-3rd' );
	register_setting( 'openagenda-wp', 'openagenda-tec' );
	add_settings_field( 'openagenda-tec', __( 'Use The Event Calendar ?', 'wp-openagenda' ), 'openwp_tec', 'openagenda-wp',
        'openagenda-wp' );

}

function openwp_oa_secret(){
	?>
	<input type="text" name="openagenda_secret" value="<?php echo esc_html( get_option( 'openagenda_secret' ) ); ?>"/>
	<?php
	$allowed_html = array(
			'a' => array(
					'href' => array(),
					'p' => array(),
			),
			'p'=>array(),

	);
	$link = antispambot( 'support@openagenda.com');
	$body = __( 'Hello, Could you please activate my Secret Key ?', 'wp-openagenda' );
	$url = 'mailto:' . $link . '?subject=' . __( 'Secret Key Activation', 'wp-openagenda') . '&body=' . $body; ?>
	<?php // translators: Add the OpenAGenda URL. ?>
	<p><?php printf( wp_kses( __( 'Send a mail to <a href="%s" >OpenAgenda</a>, and ask them to activate your secret key.', 'wp-openagenda' ), $allowed_html ), esc_url( $url ) ); ?></p>
	<?php
	do_action( 'openagenda_after_secret' );
}

function openwp_tec(){
    $tec = get_option( 'openagenda-tec' );
   ?>
    <input name="openagenda-tec" type="checkbox" value="yes" <?php checked( 'yes', $tec ); ?>>
<?php
}