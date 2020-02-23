<?php

use OpenAgenda\TEC\The_Event_Calendar;
use OpenAgendaAPI\OpenAgendaApi;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Create a file with the date to avoid launching cron twice
 */
function wp_openagenda_create_pid() {

	$date = date( 'd F Y @ H\hi:s' );
	$file = fopen( THFO_OPENWP_PLUGIN_PATH . 'pid.txt', 'w+' );
	fwrite( $file, $date );
	fclose( $file );
}

function wp_openagenda_delete_pid() {
	if ( file_exists( THFO_OPENWP_PLUGIN_PATH . 'pid.txt' ) ) {
		unlink( THFO_OPENWP_PLUGIN_PATH . 'pid.txt' );
	}
}

if ( openagenda_fs()->is__premium_only() ) {
	add_action( 'openagenda_hourly_event', 'register_venue__premium_only', 10 );
	add_action( 'openagenda_hourly_event', 'import_oa_events__premium_only', 20 );
	add_action( 'openagenda_hourly_event', 'export_event__premium_only', 30 );

}


/**
 *  Register Venue from OpenAgenda
 */
function register_venue__premium_only() {
	$openagenda = new OpenAgendaApi();
	$url_oa     = $openagenda->get_agenda_list__premium_only();

	/**
	 * Get UID for each
	 */
	foreach ( $url_oa as $url ) {
		$uid  = $openagenda->openwp_get_uid( $url );
		$json = wp_remote_get( 'https://openagenda.com/agendas/' . $uid . '/locations.json' );
		if ( 200 === (int) wp_remote_retrieve_response_code( $json ) ) {
			$body         = wp_remote_retrieve_body( $json );
			$decoded_body = json_decode( $body, true );

			/**
			 * get all venue to update if exists
			 */

			foreach ( $decoded_body['items'] as $location ) {
				$venues = $openagenda->get_venue__premium_only( $location['uid'] );

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

/**
 * Import OA events from OpenAgenda to WordPress
 */
if ( ! empty( $_GET['test'] ) && 'ok' === $_GET['test'] ) {
	add_action( 'admin_init', 'import_oa_events__premium_only' );
}
function import_oa_events__premium_only( $url_oa = '' ) {

	$openagenda = new OpenAgendaApi();

	if ( empty( $url_oa ) ) {
		$url_oa = $openagenda->get_agenda_list__premium_only();

		foreach ( $url_oa as $url ) {
			$agendas[ $url ] = $openagenda->thfo_openwp_retrieve_data( $url, 999, 'current' );
		}
	} else {
		$agendas[ $url_oa ] = $openagenda->thfo_openwp_retrieve_data( $url_oa, 999, 'current' );
	}


	foreach ( $agendas as $agenda ) {
		foreach ( $agenda['events'] as $events ) {
			if ( is_null( $events['longDescription']['fr'] ) ) {
				$events['longDescription']['fr'] = $events['description']['fr'];
			}

			$args = array(
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

			// Date Formating
			$start          = array_pop( array_reverse( $events['timings'] ) );
			$start1         = $start['start'];
			$start_firstday = strtotime( $start1 );

			$start2        = array_pop( $events['timings'] );
			$start_lastday = $start2['start'];
			$start_lastday = strtotime( $start_lastday );

			/**
			 * Add support to TEC
			 */
			if ( The_Event_Calendar::$tec_activated ) {
				$args = The_Event_Calendar::prepare_data( $id, $start_firstday, $end_lastday, $events );
			} else {

				$args   = array(
					'ID'             => $id,
					'post_content'   => $events['longDescription']['fr'],
					'post_title'     => $events['title']['fr'],
					'post_excerpt'   => $events['description']['fr'],
					'post_status'    => 'publish',
					'post_type'      => $post_type,
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					'meta_input'     => array(
						'_oa_conditions' => $events['conditions']['fr'],
						'_oa_event_uid'  => $events['uid'],
						'_oa_tools'      => $events['registrationUrl'],
						'_oa_min_age'    => $events['age']['min'],
						'_oa_max_age'    => $events['age']['max'],
					),
				);
			}

			$insert = wp_insert_post( $args );
			carbon_set_post_meta( $insert, 'oa_start', $start_firstday );
			carbon_set_post_meta( $insert, 'oa_end', $start_lastday );

			//handicap
			$i = 0;
			foreach ( $events['accessibility'] as $accessibility ) {
				add_post_meta( $insert, "_oa_a11y|||$i|value", $accessibility );
				$i ++;
			}
			unset( $i );

			// Insert Post Term venue
			$venues    = $openagenda->get_venue__premium_only( $events['location']['uid'] );
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
			$filename        = basename( $url );
			$file_exists     = MediaFileAlreadyExists( $filename );

			// Download file to temp dir.
			$temp_file = download_url( $url, $timeout_seconds );
			if ( ! is_wp_error( $temp_file ) && false === $file_exists ) {

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

add_action( 'save_post_openagenda-events', 'export_event__premium_only' );
function export_event__premium_only() {

	$locale = get_locale();
	$locale = explode( '_', $locale );
	$locale = $locale[0];

	$openagenda = new OpenAgendaApi();

	$options     = array( 'lang' => 'fr' );
	$agendas     = $openagenda->get_agenda_list__premium_only();
	$accessToken = $openagenda->get_acces_token();
	foreach ( $agendas as $agenda ) {
		$agendaUid = $openagenda->openwp_get_uid( $agenda );

		// Get Post openagenda-events
		$events = get_posts(
			array(
				'post_type' => 'openagenda-events',
			)
		);

		foreach ( $events as $event ) {
			$eventuid = carbon_get_post_meta( $event->ID, 'oa_event_uid' );
			if ( empty( $eventuid ) ) {
				//create
				$route = "https://api.openagenda.com/v2/agendas/$agendaUid/events";
			} else {
				//update
				$route = "https://api.openagenda.com/v2/agendas/$agendaUid/events/$eventuid";
			}

			extract( array_merge( array(
				'lang' => 'fr'
			), $options ) );

			// retrieve event keywords
			$keywords = wp_get_post_terms( $event->ID, 'openagenda_keyword' );
			if ( ! empty( $keywords ) ) {
				$keys = array();
				foreach ( $keywords as $keyword ) {
					array_push( $keys, $keyword->name );
				}
				$keywords = implode( ', ', $keys );
			}

			// get min age
			$min_age = get_post_meta( $event->ID, '_oa_min_age' );

			// get max age
			$max_age = get_post_meta( $event->ID, '_oa_max_age' );

			$age = array(
				'min' => $min_age[0],
				'max' => $max_age[0],
			);

			// get conditions
			$conditions = get_post_meta( $event->ID, '_oa_conditions' );

			//get registration
			$registrations = get_post_meta( $event->ID, '_oa_tools' );

			// retrieve locationUID
			$locationuid = wp_get_post_terms( $event->ID, 'openagenda_venue' );
			$locationuid = get_term_meta( $locationuid[0]->term_id, '_oa_location_uid' );

			// get start date
			$debut = carbon_get_post_meta( $event->ID, 'oa_start' );

			//get end date
			$fin = carbon_get_post_meta( $event->ID, 'oa_end' );

			// day number between start and en of the events
			$diff = $fin - $debut;
			$diff = ceil( $diff / 86400 );

			$i     = 0;
			$dates = [];
			$date  = [];

			while ( $i < $diff ) {
				$debut = intval( $debut );
				$end   = ( $debut + 86400 * $i ) + 7200;
				$date  = array(
					'begin' => date( 'Y-m-d\Th:i:00+0200', $debut + 86400 * $i ),
					'end'   => date( 'Y-m-d\Th:i:00+0200', $end ),
				);

				array_push( $dates, $date );
				$i ++;
			}

			//a11y
			$a11y = carbon_get_post_meta( $event->ID, 'oa_a11y' );

		}
		if ( empty( $event->post_excerpt ) ) {
			$excerpt = __( 'No data found', 'wp-openagenda' );
		} else {
			$excerpt = $event->post_excerpt;
		}

		$data = array(
			'slug'            => "$event->post_name-" . rand(),
			'title'           =>
				[
					$locale => $event->post_title,
				],
			'description'     =>
				[
					$locale => $excerpt,
				],
			'longDescription' =>
				[
					$locale => $event->post_content,
				],
			'keywords'        =>
				[
					$locale => $keywords,
				],
			'age'             => $age,
			'accessibility'   => $a11y,
			'conditions'      =>
				[
					$locale => $conditions[0],
				],
			'registration'    => $registrations,
			'locationUid'     => $locationuid[0],
			'timings'         => $dates,
		);


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
			'nonce'        => rand(),
			'data'         => json_encode( $data ),
			'lang'         => 'fr',
		);

		if ( $imageLocalPath ) {
			$posted['image'] = $imageLocalPath;
		}

		curl_setopt( $ch, CURLOPT_POSTFIELDS, $posted );

		$received_content = curl_exec( $ch );

		$decode = json_decode( $received_content, true );

		// update event uid
		$uid = intval( $decode['event']['uid'] );
		if ( $uid ) {
			add_post_meta( $event->ID, '_oa_event_uid', $uid );
		}

		return $decode;
	}
}
