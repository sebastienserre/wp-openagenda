<?php


namespace OpenAgenda\Import;

use OpenAgenda\TEC\The_Event_Calendar;
use OpenAgendaAPI\OpenAgendaApi;
use function add_action;
use function add_post_meta;
use function array_merge;
use function array_push;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function date;
use function error_log;
use function explode;
use function extract;
use function fclose;
use function file_exists;
use function fopen;
use function fwrite;
use function get_field;
use function get_locale;
use function get_post_meta;
use function get_posts;
use function get_term_by;
use function get_term_meta;
use function implode;
use function intval;
use function is_array;
use function is_null;
use function is_wp_error;
use function strtotime;
use function tribe_get_event;
use function unlink;
use function update_field;
use function update_term_meta;
use function var_dump;
use function wp_get_post_terms;
use function wp_insert_post;
use function wp_insert_term;
use function wp_rand;
use function wp_set_post_terms;
use function wp_update_term;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_URL;
use const THFO_OPENWP_PLUGIN_PATH;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * This Class will import event from OpenAgenda
 *
 * @package OpenAgenda\Import
 * @since   3.0.0
 * @authors sebastienserre
 */
class Import_OA {
	public function __construct() {
		add_action(
			'openagenda_hourly_event',
			[
				'OpenAgenda\Import\Import_OA',
				'register_venue__premium_only',
			],
			10
		);
		add_action(
			'openagenda_hourly_event',
			[
				'OpenAgenda\Import\Import_OA',
				'import_oa_events__premium_only',
			],
			20
		);
		add_action(
			'openagenda_hourly_event',
			[
				'OpenAgenda\Import\Import_OA',
				'export_event__premium_only',
			],
			30
		);
		add_action(
			'save_post_openagenda-events',
			[
				'OpenAgenda\Import\Import_OA',
				'export_event__premium_only',
			],
			999
		);
	}

	/**
	 * Create a file with the date to avoid launching cron twice
	 *
	 * @since   3.0.0
	 * @authors sebastienserre
	 * @package OpenAgenda\Import
	 */

	public static function wp_openagenda_create_pid() {

		$date = date( 'd F Y @ H\hi:s' );
		$file = fopen( THFO_OPENWP_PLUGIN_PATH . 'pid.txt', 'w+' );
		fwrite( $file, $date );
		fclose( $file );
	}

	/**
	 * Delete the created PID
	 *
	 * @since   3.0.0
	 * @authors sebastienserre
	 * @package OpenAgenda\Import
	 */
	public static function wp_openagenda_delete_pid() {
		if ( file_exists( THFO_OPENWP_PLUGIN_PATH . 'pid.txt' ) ) {
			unlink( THFO_OPENWP_PLUGIN_PATH . 'pid.txt' );
		}
	}

	/**
	 * Register Venue from OpenAgenda
	 *
	 * @since   3.0.0
	 * @authors sebastienserre
	 * @package OpenAgenda\Import
	 */
	public static function register_venue__premium_only() {
			$openagenda = new OpenAgendaApi();
			$url_oa     = $openagenda->get_agenda_list__premium_only();

			/**
			 * Get UID for each
			 */
			foreach ( $url_oa as $url ) {
				$uid          = $openagenda->openwp_get_uid( $url );
				$decoded_body = OpenAgendaApi::get_venue_oa( $uid );

				if ( ! empty( $decoded_body ) ) {

					/**
					 * get all venue to update if exists
					 */

					foreach ( $decoded_body['items'] as $location ) {
						$venues = $openagenda->get_venue__premium_only( $location['uid'] );

						$name = implode(
							' - ',
							[
								$location['name'],
								$location['city'],
								$location['countryCode'],
								$location['uid'],
							]
						);
						if ( The_Event_Calendar::$tec_used ) {
							The_Event_Calendar::create_venue();
						} else {

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
	}

	/**
	 * Import OA events from OpenAgenda to WordPress
	 *
	 * @since   3.0.0
	 * @authors sebastienserre
	 * @package OpenAgenda\Import
	 */

	public static function import_oa_events__premium_only( $url_oa = '' ) {

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

				if ( The_Event_Calendar::$tec_used ) {
					$post_type = 'tribe_events';
				} else {
					$post_type = 'openagenda-events';
				}


				$args = array(
					'post_type'   => $post_type,
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
				$dates = [];
				foreach ( $events['timings'] as $timing ) {

					$begin = strtotime( $timing['start'] );
					$begin = $begin + 7200;
					$end   = strtotime( $timing['end'] );
					$end   = $end + 7200;

					$dates[] =
						[
							'field_5d61787c65c27' => $begin,
							'field_5d61789f65c28' => $end,
						];
				}

				if ( The_Event_Calendar::$tec_used ) {

					$insert = The_Event_Calendar::create_event( $id, $events, $dates );

				} else {
					$args = array(
						'ID'             => $id,
						'post_content'   => $events['longDescription']['fr'],
						'post_title'     => $events['title']['fr'],
						'post_excerpt'   => $events['description']['fr'],
						'post_status'    => 'publish',
						'post_type'      => 'openagenda-events',
						'comment_status' => 'closed',
						'ping_status'    => 'closed',
						'meta_input'     => array(
							'oa_conditions' => $events['conditions']['fr'],
							'oa_event_uid'  => $events['uid'],
							'oa_tools'      => $events['registrationUrl'],
							'oa_min_age'    => $events['age']['min'],
							'oa_max_age'    => $events['age']['max'],
						),
					);

					$insert = wp_insert_post( $args );

					$dates = update_field( 'field_5d50075c33c2d', $dates, $insert );
					// insert Keywords
					wp_set_post_terms( $insert, $events['keywords']['fr'], 'openagenda_keyword' );
				}

				//handicap
				$i = 0;
				foreach ( $events['accessibility'] as $accessibility ) {
					$a11y[ $accessibility ] = true;
					update_field( 'oa_a11y', $a11y, $insert );
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

				// insert post thumbnail
				OpenAgendaApi::upload_thumbnail( $events['originalImage'], $insert, $events['title']['fr'] );
				unset( $dates );
			}
		}
	}

	/**
	 * Export the local events to OpenAgenda
	 *
	 * @return array $decode Retrun an arry with event data to store in OpenAgenda
	 * @since   3.0.0
	 * @authors sebastienserre
	 * @package OpenAgenda\Import
	 */
	public static function export_event__premium_only() {
		$openagenda = new OpenAgendaApi();
		$locale     = $openagenda::oa_get_locale();

		$options     = array( 'lang' => $locale );
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
				$eventuid = get_post_meta( $event->ID, '_oa_event_uid', true );
				if ( empty( $eventuid ) ) {
					//create
					$route = "https://api.openagenda.com/v2/agendas/$agendaUid/events";
				} else {
					//update
					$route = "https://api.openagenda.com/v2/agendas/$agendaUid/events/$eventuid";
				}

				extract( array_merge( array(
					'lang' => $locale
				), $options ) );

				// General Datas
				// retrieve event keywords
				$keywords = wp_get_post_terms( $event->ID, 'openagenda_keyword' );
				if ( ! empty( $keywords ) ) {
					$keys = array();
					foreach ( $keywords as $keyword ) {
						array_push( $keys, $keyword->name );
					}
					$keywords = implode( ', ', $keys );
				}

				// format excerpt
				if ( empty( $event->post_excerpt ) ) {
					if ( ! empty( $event->post_content ) ) {
						$excerpt = $event->post_content;
					} else {
						$excerpt = __( 'No data found', 'wp-openagenda-pro' );
					}
				} else {
					$excerpt = $event->post_excerpt;
				}

				$data = [
					'slug'            => "$event->post_name-" . wp_rand(),
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
						]
				];


				// get min age
				$min_age = get_post_meta( $event->ID, 'oa_min_age', true );

				// get max age
				$max_age = get_post_meta( $event->ID, 'oa_max_age', true );

				$age = array(
					'min' => $min_age[0],
					'max' => $max_age[0],
				);

				// get conditions
				$conditions = get_post_meta( $event->ID, 'oa_conditions', true );

				//get registration
				$registrations = get_post_meta( $event->ID, 'oa_tools' );

				// retrieve locationUID
				$locationuid = wp_get_post_terms( $event->ID, 'openagenda_venue' );
				$locationuid = get_term_meta( $locationuid[0]->term_id, '_oa_location_uid' );

				// get start date
				$dates = get_field( 'field_5d50075c33c2d', $event->ID );

				$i = 0;
				if ( is_array( $dates ) ) {
					foreach ( $dates as $date ) {
						$timings[ $i ]['begin'] = date( 'Y-m-d\TH:i:00+0200', $date['begin'] );
						$timings[ $i ]['end']   = date( 'Y-m-d\TH:i:00+0200', $date['end'] );
						$i ++;
					}
				}

				//a11y
				$a11y = get_field( 'oa_a11y', $event->ID );
				if ( ! empty( $a11y ) ) {
					foreach ( $a11y as $key => $value ) {
						$accessibility[ $value ] = true;
					}
				} else {
					$accessibility['hi'] = false;
				}

				$data['age']           = $age;
				$data['accessibility'] = $accessibility;
				$data['conditions']    =
					[
						$locale => $conditions,
					];
				$data['registration']  = $registrations;
				$data['locationUid']   = $locationuid[0];
				$data['timings']       = $timings;

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
					add_post_meta( $event->ID, 'oa_event_uid', $uid );
				}
			}

			return $decode;
		}
	}

}

new Import_OA();
