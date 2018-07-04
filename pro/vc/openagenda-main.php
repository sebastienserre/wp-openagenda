<?php
/**
 * Create a VC Element to display Openagenda Widget.
 *
 * @package openagenda_main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
function openwp_vc_openagenda_main_init() {

	vc_map( array(
		'name'        => __( 'Main Openagenda', 'wp-openagenda' ),
		'base'        => 'openagenda-main',
		'description' => __( 'Display Openagenda Script', 'wp-openagenda' ),
		'category'    => __( 'OpenAgenda', 'wp-openagenda' ),
		'icon'        => THFO_OPENWP_PLUGIN_URL . '/assets/img/icon.jpg',
		'params'      => array(
			array(
				'type'        => 'textfield',
				'holder'      => 'h3',
				'class'       => 'title-class',
				'heading'     => __( 'Title', 'wp-openagenda' ),
				'param_name'  => 'title',
				'value'       => __( 'Title', 'wp-openagenda' ),
				'description' => __( 'Add a word between % to add a different style. Only 1 allowed', 'wp-openagenda' ),
				'admin_label' => false,
				'weight'      => 0,
				'group'       => __( 'Settings', 'wp-openagenda' ),
			),
			array(
				'type'        => 'textfield',
				'holder'      => 'a',
				'class'       => 'url-class',
				'heading'     => __( 'URL to Event', 'wp-openagenda' ),
				'param_name'  => 'agenda_url',
				'value'       => esc_url( site_url() ),
				'description' => __( 'URL to the OpenAgenda Agenda', 'wp-openagenda' ),
				'admin_label' => false,
				'weight'      => 0,
				'group'       => __( 'Settings', 'wp-openagenda' ),
			),

			array(
				'type'        => 'dropdown',
				'holder'      => 'p',
				'class'       => 'title-class',
				'heading'     => __( 'Widget Type', 'wp-openagenda' ),
				'param_name'  => 'openagenda_type',
				'value'       => array(
					__( 'Please select an OpenAgenda Widget', 'wp-openagenda' ) => 'nothing',
					__( 'General', 'wp-openagenda' )    => 'general',
					__( 'Map', 'wp-openagenda' )    => 'map',
					__( 'Search', 'wp-openagenda' ) => 'search',
					__( 'Categories', 'wp-openagenda' ) => 'categories',
					__( 'Tags', 'wp-openagenda' )   => 'tags',
					__( 'Calendrier', 'wp-openagenda' ) => 'calendrier',
					__( 'Preview Widget', 'wp-openagenda' ) => 'preview',
				),
				'description' => __( 'Select the widget to display', 'wp-openagenda' ),
				'admin_label' => false,
				'weight'      => 0,
				'group'       => __( 'Settings', 'wp-openagenda' ),
			),
			array(
				'type'        => 'vc_link',
				'holder'      => 'p',
				'class'       => 'title-class',
				'heading'     => __( 'Agenda page in your Website', 'wp-openagenda' ),
				'param_name'  => 'openagenda_url',
				'description' => __( 'Select the page where the main agenda is', 'wp-openagenda' ),
				'admin_label' => false,
				'weight'      => 0,
				'group'       => __( 'Settings', 'wp-openagenda' ),
			),
		),
			)
	);

}

add_action( 'init', 'openwp_vc_openagenda_main_init' );

/**
 * Display the Shortcode.
 *
 * @param mixed $atts data from VC settings.
 *
 * @return string
 */
function openwp_vc_openagenda_main( $atts ) {
	$atts = shortcode_atts( array(
		'agenda_url'      => '',
		'title'           => '',
		'openagenda_type' => 'nothing',
		'openagenda_url'  => '',
		),
		$atts, 'openagenda-main'
	);

	$url = ( ! empty( $atts['openagenda_url'] ) ) ? vc_build_link( $atts['openagenda_url'] ) : '';

	$re = '/[a-zA-Z\.\/:]*\/([a-zA-Z\.\/:\0-_9]*)/';

	preg_match( $re, $atts['agenda_url'], $matches, PREG_OFFSET_CAPTURE, 0 );

	$slug = untrailingslashit( $matches[1][0] );

	$key = get_option( 'openagenda_api' );
	if ( ! empty( $key ) ) {
		$openwp = new OpenAgendaApi();
		$uid = $openwp->openwp_get_uid( $slug );
	}

	if ( $uid ) {
		$widget = wp_remote_get( 'https://openagenda.com/agendas/'. $uid .'/settings.json?key='. $key );
		if ( 200 === (int) wp_remote_retrieve_response_code( $widget ) ) {
			$body         = wp_remote_retrieve_body( $widget );
			$decoded_body = json_decode( $body, true );
		}
		$widget = $decoded_body['embeds'][0];

		switch ( $atts['openagenda_type'] ) {
			case 'general':
				$openagenda_code = '<iframe style="width:100%;" frameborder="0" scrolling="no" allowtransparency="allowtransparency" class="cibulFrame cbpgbdy" data-oabdy src="//openagenda.com/agendas/' . $uid . '/embeds/' . $widget . '/events?lang=fr"></iframe><script type="text/javascript" src="//openagenda.com/js/embed/cibulBodyWidget.js"></script>';
				break;
			case 'map':
				$openagenda_code = '<div class="cbpgmp cibulMap" data-oamp data-cbctl="' . $uid . '/' . $widget . '" data-lang="fr" ></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulMapWidget.js"></script>';
				break;
			case 'search':
				$openagenda_code = '<div class="cbpgsc cibulSearch" data-oasc data-cbctl="' . $uid . '/' . $widget . '|fr" data-lang="fr"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulSearchWidget.js"></script>';
				break;
			case 'categories':
				$openagenda_code = '<div class="cbpgct cibulCategories" data-oact data-cbctl="' . $uid . '/' . $widget . '" data-lang="fr"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulCategoriesWidget.js"></script>';
				break;
			case 'tags':
				$openagenda_code = '<div class="cbpgtg cibulTags" data-oatg data-cbctl="' . $uid . '/' . $widget . '"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulTagsWidget.js"></script>';
				break;
			case 'calendrier':
				$openagenda_code = '<div class="cbpgcl cibulCalendar" data-oacl data-cbctl="' . $uid . '/' . $widget . '|fr" data-lang="fr"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulCalendarWidget.js"></script>';
				break;
			case 'preview':
				$openagenda_code = '<div class="oa-preview cbpgpr" data-oapr data-cbctl="' . $uid . '|fr"> 
  <a href="' . $url['url'] . '">Voir l\'agenda</a> 
</div><script src="//openagenda.com/js/embed/oaPreviewWidget.js"></script>';
				break;
		}

		ob_start();

		echo $openagenda_code;

		return ob_get_clean();

	} else {
		echo '<p>' . $warning . '</p>';
	}
}

add_shortcode( 'openagenda-main', 'openwp_vc_openagenda_main' );