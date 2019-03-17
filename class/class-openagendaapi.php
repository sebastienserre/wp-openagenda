<?php

namespace OpenAgendaAPI;

use function add_action;
use function add_query_arg;
use function array_merge;
use function array_push;
use function esc_url;
use function get_post_meta;
use function get_term_meta;
use function implode;
use function is_null;
use function is_wp_error;
use function media_sideload_image;
use function pi;
use function pippin_get_image_id;
use function set_post_thumbnail;
use function set_transient;
use function strtotime;
use function update_term_meta;
use function var_dump;
use function wp_create_nonce;
use WP_Error;
use function wp_get_post_cats;
use function wp_handle_sideload;
use function wp_remote_get;
use function wp_set_post_terms;
use function wp_update_term;

/**
 * Set of methods to retrieve data from OpenAgenda
 *
 * @author  : sebastienserre
 * @package Openagenda-api
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Retrieve Event data from OpenAgenda.com
 *
 * @package: Openagenda-api.
 */
class OpenAgendaApi {

	/**
	 * OpenAgendaApi constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'openagenda_check_api', array( $this, 'check_api' ) );

		if ( openagenda_fs()->is__premium_only() ) {
			add_action( 'admin_init', array( $this, 'import_oa_events__premium_only' ) );
			//add_action( 'admin_init', array( $this, 'export_event__premium_only' ) );

			//add_action( 'openagenda_hourly_event', array( $this, 'register_venue__premium_only' ), 10 );
			//add_action( 'openagenda_hourly_event', array( $this, 'import_oa_events__premium_only' ), 20 );
			//add_action( 'openagenda_hourly_event', array( $this, 'export_event__premium_only' ), 30 );
		}
	}

	/**
	 * Get API stored in Options
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function thfo_openwp_get_api_key() {
		$key = get_option( 'openagenda_api' );

		return $key;
	}

	/**
	 * Get Slug from an URL.
	 *
	 * @param string $slug URL of openagenda.com Agenda.
	 *
	 * @return string
	 */
	public function openwp_get_slug( $slug ) {
		$re = '/[a-zA-Z\.\/:]*\/([a-zA-Z\.\/:\0-_9]*)/';

		preg_match( $re, $slug, $matches, PREG_OFFSET_CAPTURE, 0 );

		$slug = untrailingslashit( $matches[1][0] );

		return $slug;
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
	 */
	public function thfo_openwp_retrieve_data( $url, $nb = 10, $when = 'current' ) {
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

		$key = $this->thfo_openwp_get_api_key();
		$uid = $this->openwp_get_uid( $url );
		if ( $uid ) {

			$url          = 'https://openagenda.com/agendas/' . $uid . '/events.json';
			$url          = add_query_arg( array(
				'key'   => $key,
				'limit' => $nb,
				'when'  => $when,
			), $url );
			$response     = wp_remote_get( $url );
			$decoded_body = array();

			if ( 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
				$body         = wp_remote_retrieve_body( $response );
				$decoded_body = json_decode( $body, true );
			} else {
				$decoded_body = '<p>' . __( 'Impossible to retrieve Events Data', 'wp-openagenda' ) . '</p>';
			}
		} else {
			$decoded_body = '<p>' . __( 'Impossible to retrieve Events Data', 'wp-openagenda' ) . '</p>';
		}

		return $decoded_body;
	}

	/**
	 * Retrieve OpenAenda UID.
	 *
	 * @param mixed|string $slug OpenAgenda Agenda URL.
	 *
	 * @return mixed
	 */
	public function openwp_get_uid( $slug ) {
		$slug = $this->openwp_get_slug( $slug );
		if ( ! empty( $this->thfo_openwp_get_api_key() ) ) {
			$key      = $this->thfo_openwp_get_api_key();
			$response = wp_remote_get( 'https://api.openagenda.com/v1/agendas/uid/' . $slug . '?key=' . $key );
			if ( 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
				$body         = wp_remote_retrieve_body( $response );
				$decoded_body = json_decode( $body, true );
				$uid          = $decoded_body['data']['uid'];
			}
		}

		return $uid;
	}

	/**
	 * Display a basic WordPress Widget.
	 *
	 * @param   object $openwp_data Object with OPenagenda Events data.
	 * @param   string $lang        Language code to display.
	 * @param   string $slug        OpenAgenda Agenda URL.
	 */
	public function openwp_basic_html( $openwp_data, $lang, $instance ) {
		if ( is_array( $instance ) ) {
			if ( empty( $instance['url'] ) ) {
				$instance['openwp_url'] = $instance['openwp_url'];
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
	 * @param   int    $uid OpenAgenda ID.
	 * @param   string $key OpenAgenda API Key.
	 *
	 * @return array|WP_Error
	 */
	public function openwp_get_embed( $uid, $key ) {
		$embed = wp_remote_get( 'https://openagenda.com/agendas/' . $uid . '/settings.json?key=' . $key );
		if ( 200 === (int) wp_remote_retrieve_response_code( $embed ) ) {
			$body         = wp_remote_retrieve_body( $embed );
			$decoded_body = json_decode( $body, true );
		}
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
		$agenda = wp_remote_get( 'https://openagenda.com/agendas/' . $uid . '/events.json?lang=fr&key=' . $key );
		//$agenda = wp_remote_get( 'https://openagenda.com/agendas/35241188/events.json?lang=fr&key=hd2cmLOQHQgrfJqRTd7qW45TQ43eFVgN' );
		if ( 200 === (int) wp_remote_retrieve_response_code( $agenda ) ) {
			$body         = wp_remote_retrieve_body( $agenda );
			$decoded_body = json_decode( $body, true );
			foreach ( $decoded_body['events'] as $event ) {
				$slug          = $event['origin']['slug'];
				$org           = $event['origin']['uid'];
				$slugs[ $org ] = $slug;
			}

			$slugs = array_unique( $slugs );
		}

		return $slugs;
	}

	public function check_api() {
		$key   = $this->thfo_openwp_get_api_key();
		$check = $this->openwp_get_uid( 'https://openagenda.com/iledefrance' );
		//$check = wp_remote_get( 'https://api.openagenda.com/v1/events?key=' . $key . '&lang=fr' );
		if ( null === $check ) {
			?>
			<div class="notice notice-error openagenda-notice">
				<p><?php _e( 'Woot! Your API Key seems to be non valid', 'wp-openagenda' ); ?></p>
				<p><?php printf( __( '<a href="%s" target="_blank">Find help</a>', 'wp-openagenda' ), esc_url( 'https://thivinfo.com/docs/openagenda-pour-wordpress/' ) ); ?></p>
			</div>
			<?php
		} else {
			?>
			<div class="notice notice-success openagenda-notice"><?php _e( 'OpenAgenda API Key valid', 'wp-openagenda' ); ?></div>
			<?php
		}

	}

	/**
	 * Get OA list
	 *
	 * @return array key: term_id Value; term name (OA URL)
	 */
	public function get_agenda_list__premium_only() {
		/**
		 * Get List of Agenda
		 */
		$terms = get_terms( array(
			'taxonomy'   => 'openagenda_agenda',
			'hide_empty' => false,
		) );
		foreach ( $terms as $term ) {
			$url_oa[ $term->term_id ] = $term->name;
		}

		return $url_oa;
	}

	public function get_venue__premium_only( $uid ) {
		$args   = array(
			'taxonomy'   => 'openagenda_venue',
			'hide_empty' => false,
			'meta_key'   => '_oa_location_uid',
			'meta_value' => (string) $uid,
		);
		$venues = get_terms(
			$args
		);

		return $venues;
	}

	/**
	 *  Register Venue from OpenAgenda
	 */
	public function register_venue__premium_only() {

		$url_oa = $this->get_agenda_list__premium_only();

		/**
		 * Get UID for each
		 */
		foreach ( $url_oa as $url ) {
			$uid  = $this->openwp_get_uid( $url );
			$json = wp_remote_get( 'https://openagenda.com/agendas/' . $uid . '/locations.json' );
			if ( 200 === (int) wp_remote_retrieve_response_code( $json ) ) {
				$body         = wp_remote_retrieve_body( $json );
				$decoded_body = json_decode( $body, true );
				/**
				 * get all venue to update if exists
				 */


				foreach ( $decoded_body['items'] as $location ) {
					$venues = $this->get_venue__premium_only( $location['uid'] );

					$name = implode( ' - ', array(
						$location['name'],
						$location['city'],
						$location['countryCode'],
						$location['uid'],
					) );
					if ( empty( $venues ) ) {

						$insert = wp_insert_term( $name, 'openagenda_venue' );
						if ( is_wp_error( $insert ) ) {
							$error = 'Fatal Error -- Import OpenAgenda: ' . $insert->get_error_message();
							error_log( $error );
						} else {
							update_term_meta( $insert['term_id'], '_oa_location_uid', $location['uid'] );
						}


					} else {

						foreach ( $venues as $venue ) {
							$locationuid = get_term_meta( $venue->term_id, '_oa_location_uid' );
							$args        = array(
								'name' => $name,
							);
							// si $locationuid existe alors update
							$locationuid = intval( $locationuid[0] );
							if ( $location['uid'] === $locationuid ) {
								wp_update_term( $venue->term_id, 'openagenda_venue', $args );
							}

						}
					}
				}
			}
		}
	}

	public function get_secret_key__premium_only() {

		$secret = get_option( 'openagenda_secret' );

		return $secret;
	}

	/**
	 * Retrieve access token to Openagenda data.
	 *
	 * @return string return Openagenda token.
	 */
	public function get_acces_token() {
		$transient = get_transient( 'openagenda_secret' );
		if ( empty( $transient ) ) {
			$secret = $this->get_secret_key__premium_only();
			$args   = array(
				'sslverify' => false,
				'timeout'   => 15,
				'body'      => array(
					'grant_type' => 'authorization_code',
					'code'       => $secret,
				),
			);

			$ch = wp_remote_post( 'https://api.openagenda.com/v1/requestAccessToken', $args );

			if ( 200 === (int) wp_remote_retrieve_response_code( $ch ) ) {
				$body         = wp_remote_retrieve_body( $ch );
				$decoded_body = json_decode( $body, true );
				$token        = $decoded_body['access_token'];
				set_transient( 'openagenda_secret', $decoded_body['access_token'], $decoded_body['expires_in'] );

			}
		} else {
			$token = $transient;
		}

		return $token;

	}

	/**
	 * Import OA events from OpenAgenda to WordPress
	 */
	public function import_oa_events__premium_only() {
		if ( empty( $_GET['test'] ) && 'import' === $_GET['test'] ) {
			return;
		}
		$url_oa = $this->get_agenda_list__premium_only();

		foreach ( $url_oa as $url ) {
			$agendas[ $url ] = $this->thfo_openwp_retrieve_data( $url, 999, 'current' );
		}

		foreach ( $agendas as $agenda ) {
			foreach ( $agenda['events'] as $events ) {
				if ( is_null( $events['longDescription']['fr'] ) ) {
					$events['longDescription']['fr'] = $events['description']['fr'];
				}

				//handicap

				// Date Formating
				$start = array_pop( array_reverse( $events['timings'] ) );
				$start = $start['start'];
				$start = strtotime( $start );

				$end = array_pop( $events['timings'] );
				$end = $end['end'];
				$end = strtotime( $end );

				$args              = array(
					'post_type'   => 'openagenda-events',
					'meta_key'    => '_oa_event_uid',
					'meta_value'  => $events['uid'],
					'post_status' => 'publish',
				);
				$openagenda_events = get_posts(
					$args
				);
				if ( ! empty( $openagenda_events ) ) {
					$id = $openagenda_events[0]->ID;
				}

				$args   = array(
					'ID'             => $id,
					'post_content'   => $events['longDescription']['fr'],
					'post_title'     => $events['title']['fr'],
					'post_excerpt'   => $events['description']['fr'],
					'post_status'    => 'publish',
					'post_type'      => 'openagenda-events',
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					'meta_input'     => array(
						'_oa_conditions' => $events['conditions']['fr'],
						'_oa_event_uid'  => $events['uid'],
						'_oa_tools'      => $events['registrationUrl'],
						'_oa_min_age'    => $events['age']['min'],
						'_oa_max_age'    => $events['age']['max'],
						'_oa_start_date' => $start,
						'_oa_end_date'   => $end,
					),
				);
				$insert = wp_insert_post( $args );

				// Insert Post Term venue
				$venues    = $this->get_venue__premium_only( $events['location']['uid'] );
				$venues_id = array();
				foreach ( $venues as $venue ) {
					array_push( $venues_id, $venue->term_id );
				}
				if ( ! empty( $venues_id ) ) {
					wp_set_post_terms( $insert, $venues_id, 'openagenda_venue' );
				}

				// insert origin Agenda
				$agendas = get_term_by( 'name', 'https://openagenda.com/' . $events['origin']['slug'], 'openagenda_agenda' );

				if ( ! empty( $agendas ) ) {
					wp_set_post_terms( $insert, $agendas->term_id, 'openagenda_agenda' );
				}

				// insert Keywords
				wp_set_post_terms( $insert, $events['keywords']['fr'], 'openagenda_keyword' );

				// insert post thumbnail
				// Gives us access to the download_url() and wp_handle_sideload() functions
				require_once( ABSPATH . 'wp-admin/includes/file.php' );

				// Download file to temp dir
				$timeout_seconds = 5;
				$url             = $events['originalImage'];

				// Download file to temp dir.
				$temp_file = download_url( $url, $timeout_seconds );
				if ( ! is_wp_error( $temp_file ) ) {

					// Array based on $_FILE as seen in PHP file uploads.
					$file = array(
						'name'     => basename( $url ), // ex: wp-header-logo.png
						'type'     => 'image/png',
						'tmp_name' => $temp_file,
						'error'    => 0,
						'size'     => filesize( $temp_file ),
					);

					$overrides = array(
						/*
						 * Tells WordPress to not look for the POST form fields that would
						 * normally be present, default is true, we downloaded the file from
						 * a remote server, so there will be no form fields.
						 */
						'test_form'   => false,

						// Setting this to false lets WordPress allow empty files, not recommended.
						'test_size'   => true,

						// A properly uploaded file will pass this test. There should be no reason to override this one.
						'test_upload' => true,
					);

					// Move the temporary file into the uploads directory.
					$results       = wp_handle_sideload( $file, $overrides );
					$wp_upload_dir = wp_upload_dir();

					if ( empty( $results['error'] ) ) {

						$filename      = $results['file']; // Full path to the file.
						$filetype      = wp_check_filetype( $filename, null );
						$attachment    = array(
							'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
							'post_mime_type' => $filetype['type'],
							'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
							'post_content'   => '',
							'post_status'    => 'inherit',
						);
						$attachment_id = wp_insert_attachment( $attachment, $filename, $insert );
						update_post_meta( $attachment_id, '_wp_attachment_image_alt', $events['title']['fr'] );

						// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
						require_once ABSPATH . 'wp-admin/includes/image.php';

						// Generate the metadata for the attachment, and update the database record.
						$attach_data = wp_generate_attachment_metadata( $attachment_id, $filename );
						wp_update_attachment_metadata( $attachment_id, $attach_data );
						set_post_thumbnail( $insert, $attachment_id );
					}
				}
			}
		}
	}

	public function export_event__premium_only() {
		if ( empty( $_GET['test'] ) && 'export' === $_GET['export'] ) {
			return;
		}

		 $options = array();
		$agendas     = $this->get_agenda_list__premium_only();
		$accessToken = $this->get_acces_token();
		foreach ( $agendas as $agenda ) {
			$agendaUid = $this->openwp_get_uid( $agenda );

			// Get Post openagenda-events
			$events = get_posts(
					array(
							'post_type' =>  'openagenda-events'
					)
			);

			foreach ( $events as $event ) {
				$eventuid = get_post_meta( $event->ID, '_oa_event_uid' );
				if ( empty( $eventuid[0] ) ) {
					//create
					$route = "https://api.openagenda.com/v2/agendas/$agendaUid/events";
				} else {
					//update
					$route = "https://api.openagenda.com/v2/agendas/$agendaUid/events/$eventuid[0]";
				}

				// retrieve event keywords
				$keywords = wp_get_post_terms( $event->ID, 'openagenda_keyword' );
				if ( ! empty( $keywords ) ){
					$keys = array();
					foreach ( $keywords as $keyword ) {
						array_push( $keys, $keyword->name );
					}
					$keywords = implode( ', ', $keys );
				}

				// get min age
				$min_age = get_post_meta( $event->ID, '_oa_min_age');

				// get max age
				$max_age = get_post_meta( $event->ID, '_oa_max_age');

				$age = array(
						'min'   => $min_age[0],
						'max'   => $max_age[0],
				);

				// get conditions
				$conditions = get_post_meta( $event->ID, '_oa_conditions');

				//get registration
				$registrations = get_post_meta( $event->ID, '_oa_tools');

				// retrieve locationUID
				$locationuid = wp_get_post_terms( $event->ID, 'openagenda_venue' );
				$locationuid = get_term_meta( $locationuid[0]->term_id, '_oa_location_uid');

				// get start date
				$start = get_post_meta( $event->ID, '_oa_start_date');
				var_dump( $start);

			}
				$data = array(
					'slug'            => "$event->post_name-" . rand(),
					'title'           => $event->post_title,
					'description'     => $event->post_excerpt,
					'longDescription' => $event->post_content,
					'keywords'        => $keywords,
					'age'             => $age,
					'accessibility'   => array( 'hi', 'vi' ),
					'conditions'      => $conditions[0],
					'registration'    => $registrations,
					'locationUid'     => $locationuid[0],
					'timings'         => array(
						array(
							'begin' => '2019-09-05T13:45:00+0200',
							'end'   => '2019-09-05T15:30:00+0200',
						),
					),
				);

				extract( array_merge( array(), $options ) );

				$imageLocalPath = null;

				if ( isset( $data['image'] ) && isset( $data['image']['file'] ) ) {
					$imageLocalPath = $data['image']['file'];

					unset( $data['image']['file'] );
				}

				$ch = curl_init();

				curl_setopt( $ch, CURLOPT_URL, $route );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_POST, true );

				$posted = array(
					'access_token' => $accessToken,
					'nonce'        => wp_create_nonce(),
					'data'         => json_encode( $data ),
				);

				if ( $imageLocalPath ) {
					$posted['image'] = $imageLocalPath;
				}

				curl_setopt( $ch, CURLOPT_POSTFIELDS, $posted );

				$received_content = curl_exec( $ch );

				return json_decode( $received_content, true );
			}
		}


}

new OpenAgendaApi();
