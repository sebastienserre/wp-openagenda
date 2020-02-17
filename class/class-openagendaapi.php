<?php

namespace OpenAgendaAPI;

use function add_action;
use function add_query_arg;
use function esc_url;
use function get_transient;
use function preg_match;
use function set_transient;
use WP_Error;
use function wp_remote_get;
use function wp_remote_retrieve_body;
use function wp_remote_retrieve_response_code;
use const PREG_OFFSET_CAPTURE;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Retrieve Event data from OpenAgenda.com
 * Set of methods to retrieve data from OpenAgenda
 *
 * @author  : sebastienserre
 * @package Openagenda-api
 * @since   1.0.0
 *
 * @package : OpenAgendaApi.
 */
class OpenAgendaApi {

	/**
	 * OpenAgendaApi constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'openagenda_check_api', array( $this, 'check_api' ) );
	}

	/**
	 * Get API stored in Options
	 *
	 * @return int OpenAgenda API Key
	 * @since 1.0.0
	 */
	public static function thfo_openwp_get_api_key() {
		$key = get_option( 'openagenda_api' );

		return $key;
	}

	/**
	 * Get Slug from an URL.
	 *
	 * @param string $slug URL of openagenda.com Agenda.
	 *
	 * @return string
	 * @author sebastienserre
	 * @since  1.0.0
	 */
	public static function openwp_get_slug( $slug ) {
		$re = '/(\/[^?]+).*/';
		preg_match( $re, $slug, $matches, PREG_OFFSET_CAPTURE, 0 );
		$re = '/[a-zA-Z\.\/:]*\/([a-zA-Z\.\/:\0-_9]*)[?a-zA-Z\.\/:\0-_9]*/';
		preg_match( $re, $matches[1][0], $matches, PREG_OFFSET_CAPTURE, 0 );
		$slug = untrailingslashit( $matches[1][0] );

		return $slug;
	}

	public static function get_decoded_body( $url ) {
		$response     = wp_remote_get( $url );
		$decoded_body = array();

		if ( 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
			$body         = wp_remote_retrieve_body( $response );
			$decoded_body = json_decode( $body, true );

			return $decoded_body;
		}

		return false;
	}

	/**
	 * Retrieve data from Openagenda
	 *
	 * @param string $slug Slug of your agenda.
	 * @param int    $nb   Number of event to retrieve. Default 10.
	 * @param string $when Date of events. default: 'current'.
	 *                     all: events from 01/01/1970 to 31/12/2050.
	 *                     current: from today to 31/12/2050.
	 *                     past: from 01/01/1970 to today.
	 *
	 * @return array|mixed|object|string
	 * @author sebastienserre
	 * @since  1.0.0
	 */
	public static function thfo_openwp_retrieve_data( $url, $nb = 10, $when = 'current' ) {
		if ( empty( $url ) ) {
			return '<p>' . __( 'You forgot to add an agenda\'s url to retrieve', 'wp-openagenda' ) . '</p>';
		}
		if ( empty( $nb ) ) {
			$nb = 10;
		}

		$today = date( 'd/m/Y' );
		switch ( $when ) {
			case 'current':
				$when = $today . '-31/12/2050';
				break;
			case 'all':
				$when = '01/01/1970-31/12/2050';
				break;
			case 'past':
				$when = '01/01/1970-' . $today;
		}

		$key = self::thfo_openwp_get_api_key();
		$uid = self::openwp_get_uid( $url );
		if ( $uid ) {

			$url          = 'https://openagenda.com/agendas/' . $uid . '/events.json';
			$url          = add_query_arg( array(
				'key'   => $key,
				'limit' => $nb,
				'when'  => $when,
			), $url );
			$decoded_body = self::get_decoded_body( $url );
			if ( false === $decoded_body ) {
				$decoded_body = '<p>' . __( 'Impossible to retrieve Events Data', 'wp-openagenda' ) . '</p>';
			}
		} else {
			$decoded_body = '<p>' . __( 'Impossible to retrieve Events Data', 'wp-openagenda' ) . '</p>';
		}

		return $decoded_body;
	}

	/**
	 * Retrieve OpenAgenda UID.
	 *
	 * @param string $slug OpenAgenda Agenda URL.
	 *
	 * @return int Agenda UID
	 * @author sebastienserre
	 * @since  1.0.0
	 */
	public static function openwp_get_uid( $slug ) {
		$slug = self::openwp_get_slug( $slug );
		if ( ! empty( self::thfo_openwp_get_api_key() ) ) {
			$key          = self::thfo_openwp_get_api_key();
			$decoded_body = self::get_decoded_body( 'https://api.openagenda.com/v1/agendas/uid/' . $slug . '?key=' . $key );
			if ( false !== $decoded_body ) {
				$uid = $decoded_body['data']['uid'];
			}
		}

		return $uid;
	}

	/**
	 * Display a WP Widget
	 *
	 * @param $openwp_data array array with Event from OpenAgenda
	 * @param $lang        string 2 letters for your lang (refer to OA doc)
	 * @param $instance
	 *
	 * @author sebastienserre
	 * @since  1.0.0
	 */
	public function openwp_basic_html( $openwp_data, $lang, $instance ) {
		if ( is_array( $instance ) ) {
			if ( empty( $instance['url'] ) ) {
				/**
				 * @todo Y'a un truc chelou la!
				 */
				$instance['openwp_url'] = $instance['openwp_url'];
			} else {
				$instance['openwp_url'] = $instance['url'];
			}
			$slug = $instance['openwp_url'];
		}

		if ( null === $lang ) {
			$lang = 'fr';
		}
		?>
        <div class="openwp-events">
            <!-- OpenAgenda for WordPress Plugin downloadable for free on https://wordpress.org/plugins/wp-openagenda/-->
			<?php
			do_action( 'openwp_before_html' );
			$parsedown = new \Parsedown();

			foreach ( $openwp_data['events'] as $events ) {
				?>
                <div class="openwp-event">
                    <a class="openwp-event-link" href="<?php echo esc_url( $events['canonicalUrl'] ); ?>"
                       target="_blank">
                        <p class="openwp-event-range"><?php echo esc_attr( $events['range'][ $lang ] ); ?></p>
						<?php
						if ( false !== $events['image'] && 'yes' === $instance['openwp_img'] ) {
							?>
                            <img class="openwp-event-img" src="<?php echo esc_attr( $events['image'] ); ?>">
							<?php
						}
						?>
						<?php if ( 'yes' === $instance['event-title'] && ! empty( $events['title'][ $lang ] ) ) { ?>
                            <h3 class="openwp-event-title"><?php echo esc_attr( $events['title'][ $lang ] ); ?></h3>
						<?php } else {
							?>
                            <h3 class="openwp-event-title"><?php echo esc_attr( $events['title']['en'] ); ?></h3>
							<?php

						} ?>
						<?php if ( 'yes' === $instance['event-description'] && ! empty( $events['description'][ $lang ] ) ) { ?>
                        <p class="openwp-event-description"><?php echo $parsedown->text( esc_textarea( $events['description'][ $lang ] ) ); ?></p>
                    </a>
					<?php } else {
						?>
                        <p class="openwp-event-description"><?php echo $parsedown->text( esc_textarea( $events['description']['en'] ) ); ?></p>
						<?php
					} ?>

                </div>
				<?php
			}
			do_action( 'openwp_after_html' );
			// translators: this is a link to add events in Openagenda.com.
			$text = sprintf( wp_kses( __( 'Have an Event to display here? <a href="%s">Add it!</a>', 'wp-openagenda' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( $slug ) );
			$text = apply_filters( 'openwp_custom_add_event_text', $text );
			echo $text;

			?>
        </div>
		<?php
	}

	/**
	 * Method to display OpenAgenda Widget.
	 *
	 * @param int   $embed Embeds uid.
	 * @param int   $uid   Agenda UID.
	 * @param array $atts  Shortcode attributs.
	 *
	 * @return string
	 */
	public function openwp_main_widget_html__premium_only( $embed, $uid, $atts ) {
		if ( null === $embed ) {
			return __( 'This agenda doesn\'t have any embeds layout in their params.<br> We\'re sorry, but wen can\'t display it :(', 'wp-openagenda' );

		}
		switch ( $atts['widget'] ) {
			case 'general':
				$openagenda_code = '<iframe style="width:100%;" frameborder="0" scrolling="no" allowtransparency="allowtransparency" class="cibulFrame cbpgbdy" data-oabdy src="//openagenda.com/agendas/' . $uid . '/embeds/' . $embed . '/events?lang=fr"></iframe><script type="text/javascript" src="//openagenda.com/js/embed/cibulBodyWidget.js"></script>';
				break;
			case 'map':
				$openagenda_code = '<div class="cbpgmp cibulMap" data-oamp data-cbctl="' . $uid . '/' . $embed . '" data-lang="fr" data-count="' . $atts['agenda_nb'] . '" ></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulMapWidget.js"></script>';
				break;
			case 'search':
				$openagenda_code = '<div class="cbpgsc cibulSearch" data-oasc data-cbctl="' . $uid . '/' . $embed . '|fr" data-lang="fr"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulSearchWidget.js"></script>';
				break;
			case 'categories':
				$openagenda_code = '<div class="cbpgct cibulCategories" data-oact data-cbctl="' . $uid . '/' . $embed . '" data-lang="fr"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulCategoriesWidget.js"></script>';
				break;
			case 'tags':
				$openagenda_code = '<div class="cbpgtg cibulTags" data-oatg data-cbctl="' . $uid . '/' . $embed . '"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulTagsWidget.js"></script>';
				break;
			case 'calendrier':
				$openagenda_code = '<div class="cbpgcl cibulCalendar" data-oacl data-cbctl="' . $uid . '/' . $embed . '|fr" data-lang="fr"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulCalendarWidget.js"></script>';

				break;
			case 'preview':
				$openagenda_code = '<div class="oa-preview cbpgpr" data-oapr data-cbctl="' . $uid . '|' . $atts['lang'] . '"><a href="' . $atts['url'] . '">Voir l\'agenda</a> 
</div><script src="//openagenda.com/js/embed/oaPreviewWidget.js"></script>';
				break;
		}

		ob_start();

		echo $openagenda_code;

		return ob_get_clean();
	}

	/**
	 * Retrieved OpenAgenda embed Code
	 *
	 * @param int    $uid OpenAgenda ID.
	 * @param string $key OpenAgenda API Key.
	 *
	 * @return array|WP_Error
	 */
	public function openwp_get_embed( $uid, $key ) {
		$decoded_body = self::get_decoded_body( 'https://openagenda.com/agendas/' . $uid . '/settings.json?key=' . $key );
		if ( empty( $decoded_body['embeds'] ) ) {
			$embed = null;
		} else {
			$embed = $decoded_body['embeds'][0];
		}

		return $embed;
	}

	/**
	 * Get OA Agenda slug name
	 *
	 * @param $uid
	 * @param $key
	 *
	 * @return array
	 */
	public function openwp_get_oa_slug( $uid, $key ) {
		$decoded_body = self::get_decoded_body( 'https://openagenda.com/agendas/' . $uid . '/events.json?lang=fr&key=' . $key );
		if ( false !== $decoded_body ) {
			foreach ( $decoded_body['events'] as $event ) {
				$slug          = $event['origin']['slug'];
				$org           = $event['origin']['uid'];
				$slugs[ $org ] = $slug;
			}

			$slugs = array_unique( $slugs );
		}

		return $slugs;
	}

	/**
	 * Check if API Key is Valid or not
	 */
	public function check_api() {
		$check = $this->openwp_get_uid( 'https://openagenda.com/thivinfo' );
		if ( null === $check ) {
			?>
            <div class="notice notice-error openagenda-notice">
                <p><?php _e( 'Woot! Your API Key seems to be non valid', 'wp-openagenda' ); ?></p>
                <p><?php printf( __( '<a href="%s" target="_blank">Find help</a>', 'wp-openagenda' ), esc_url( 'https://thivinfo.com/docs/openagenda-pour-wordpress/' ) ); ?></p>
            </div>
			<?php
		} else {
			$transient = get_transient( 'OA_api_key_valid' );
			if ( empty( $transient ) ) {
				set_transient( 'OA_api_key_valid', $check, HOUR_IN_SECONDS * 24 );
				?>
                <div
                        class="notice notice-success openagenda-notice"><?php _e( 'OpenAgenda API Key valid', 'wp-openagenda' ); ?></div>
				<?php
			}
		}
	}
}

new OpenAgendaApi();
