<?php

namespace OpenAgenda\TEC;

use DateTime;
use DateTimeZone;
use OpenAgendaAPI\OpenAgendaApi;
use function add_action;
use function add_meta_box;
use function add_post_meta;
use function array_merge;
use function array_pop;
use function array_push;
use function array_reverse;
use function class_exists;
use function curl_init;
use function date;
use function esc_attr_e;
use function esc_url_raw;
use function explode;
use function extract;
use function get_post_meta;
use function get_post_type;
use function get_the_terms;
use function implode;
use function intval;
use function json_encode;
use function sanitize_email;
use function sanitize_text_field;
use function sizeof;
use function tribe_create_event;
use function tribe_create_organizer;
use function tribe_create_venue;
use function tribe_get_venue_id;
use function tribe_update_event;
use function tribe_update_organizer;
use function tribe_update_venue;
use function var_dump;
use function wp_create_nonce;
use function wp_get_post_terms;
use function wp_rand;
use function wp_set_post_terms;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

class The_Event_Calendar {

	public static $tec_option;
	public static $tec_activated;
	public static $tec_used;

	public function __construct() {
		self::$tec_option    = self::tec_option_getter();
		self::$tec_activated = self::tec_activated_getter();
		self::$tec_used      = self::is_tec_used();

		add_action( 'admin_notices', [ $this, 'tec_notices' ] );

		add_action( 'wp_insert_post', [ 'OpenAgenda\TEC\The_Event_Calendar', 'save_event' ], 20, 2 );
		add_action( 'wp_insert_post', [ 'OpenAgenda\TEC\The_Event_Calendar', 'create_venue_in_oa' ], 20, 2 );

		add_action( 'add_meta_boxes', [ 'OpenAgenda\TEC\The_Event_Calendar', 'add_venue_notice_metabox' ] );

	}

	/**
	 * @return bool
	 * @author  Sébastien SERRE
	 * @package wp-openagenda
	 * @since
	 */
	public function is_tec_used() {
		if ( true === self::$tec_activated && true === self::$tec_option ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 * @author  Sébastien SERRE
	 * @package wp-openagenda
	 * @since
	 */
	public function tec_activated_getter() {
		if ( class_exists( 'Tribe__Events__Main' ) ) {
			return true;
		}

		return false;
	}

	public function tec_option_getter() {
		$tec = get_option( 'openagenda-tec' );
		if ( 'yes' !== $tec ) {
			return false;
		}

		return true;
	}

	public function tec_notices() {
		if ( true === self::$tec_option && false === self::$tec_activated ) {
			?>
            <div class="notice notice-warning is-dismissible">
                <p>
					<?php
					esc_attr_e( 'You checked you\'re using The Event Calendar in Openagenda\'s settings but this plugin is not activated', 'wp-openagenda' );
					?>
                </p>
            </div>
			<?php
		}
	}

	public static function prepare_data( $id, $events, $date ) {
		$datepicker_format = \Tribe__Date_Utils::datepicker_formats( tribe_get_option( 'datepickerFormat' ) );

		$start['date'] = date( $datepicker_format, $date['start'] );
		$start['hour'] = date( 'H', $date['start'] );
		$start['min']  = date( 'i', $date['start'] );

		$end['date'] = date( $datepicker_format, $date['end'] );
		$end['hour'] = date( 'H', $date['end'] );
		$end['min']  = date( 'i', $date['end'] );
		$args        = [
			'ID'               => $id,
			'post_content'     => $events['longDescription']['fr'],
			'post_title'       => $events['title']['fr'],
			'post_excerpt'     => $events['description']['fr'],
			'post_status'      => 'publish',
			'post_type'        => 'tribe_events',
			'EventStartDate'   => $start['date'],
			'EventEndDate'     => $end['date'],
			'EventStartHour'   => $start['hour'],
			'EventStartMinute' => $start['min'],
			'EventEndHour'     => $end['hour'],
			'EventEndMinute'   => $end['min'],
			'EventCost'        => $events['conditions']['fr'],
			'EventURL'         => $events['registrationUrl'],
			'comment_status'   => 'closed',
			'ping_status'      => 'closed',
		];

		return $args;
	}

	/**
	 * Create Event from OA to TEC
	 *
	 * @param $id
	 * @param $events
	 * @param $dates
	 *
	 * @return bool|int
	 * @author  Sébastien SERRE
	 * @package wp-openagenda
	 * @since
	 */
	public static function create_event( $id, $events, $dates ) {

		$date['start'] = array_pop( array_reverse( $dates ) );
		$date['start'] = $date['start']['field_5d61787c65c27'];
		$date['end']   = array_pop( $dates );
		$date['end']   = $date['end']['field_5d61789f65c28'];

		$data = self::prepare_data( $id, $events, $date );

		if ( empty( $id ) ) {
			$id = tribe_create_event( $data );
			add_post_meta( $id, '_oa_event_uid', $events['uid'] );

			//create Organizer & assign
			$organiser_id = self::create_organisers( $id, $events );
			add_post_meta( $id, '_EventOrganizerID', $organiser_id );

			// create Venue & assign
			$venue_id = self::create_venue( $events );
			add_post_meta( $id, '_EventVenueID', $venue_id );


		} else {
			$id = tribe_update_event( $id, $data );
			self::update_organisers( $id, $events );
			self::create_venue( $events );
		}

		// insert Keywords
		wp_set_post_terms( $id, $events['keywords']['fr'], 'post_tag' );

		return $id;
	}

	/**
	 * Create a venue in The Event Calendar from Openagenda.com
	 *
	 * @param array $events
	 *
	 * @return int
	 * @since
	 * @author  Sébastien SERRE
	 * @package wp-openagenda
	 */
	public static function create_venue( $events = array() ) {
		$location = $events['location'];
		// Search for an already registred venue
		$args   = [
			'post_type'  => 'tribe_venue',
			'meta_key'   => '_oa_event_uid',
			'meta_value' => $location['uid'],
		];
		$venue  = get_posts( $args );
		$locale = OpenAgendaApi::oa_get_locale();

		$country = OpenAgendaApi::get_country( $location['countryCode'] );

		$args = [
			'Description' => $location['description'][ $locale ],
			'Venue'       => $location['name'],
			'Country'     => $country,
			'City'        => $location['postalCode'],
			'State'       => $location['countryCode'],
			'Province'    => $location['region'],
			'Zip'         => $location['postalCode'],
			'Address'     => $location['address'],
			'Phone'       => $location['phone'],
			'URL'         => $location['website'],
		];

		// venue doesn't exists
		if ( empty( $venue ) ) {
			$id = tribe_create_venue( $args );
			add_post_meta( $id, '_oa_event_uid', $location['uid'] );
			OpenAgendaApi::upload_thumbnail( $location['image'], $id, $location['name'] );
		} else { //venue exits
			foreach ( $venue as $v ) {
				tribe_update_venue( $v->ID, $args );
				OpenAgendaApi::upload_thumbnail( $location['image'], $v->ID, $location['name'] );
				$id = $v->ID;
			}
		}

		return $id;
	}


	/**
	 * Create Organizer from OA to TEC
	 *
	 * @param $event_id
	 * @param $event
	 *
	 * @return int|void
	 * @author  Sébastien SERRE
	 * @package wp-openagenda
	 * @since
	 */
	public static function create_organisers( $event_id, $event ) {
		if ( empty( $event['registration'] ) ) {
			return;
		}

		foreach ( $event['registration'] as $registration ) {
			switch ( $registration['type'] ) {
				case 'link':
					$url = $registration['value'];
					break;
				case 'phone':
					$phone = $registration['value'];
					break;
				case 'email':
					$mail = $registration['value'];
					break;
			}
		}
		$args = [
			'Organizer' => 'Organiser event ' . $event['uid'],
			'Phone'     => sanitize_text_field( $phone ),
			'Website'   => esc_url_raw( $url ),
			'Email'     => sanitize_email( $mail ),
		];
		$id   = tribe_create_organizer( $args );
		add_post_meta( $id, '_oa_event_uid', $event['uid'] );

		return $id;
	}

	/**
	 * Update Organizer from OA to TEC
	 *
	 * @param $id
	 * @param $event
	 *
	 * @author  Sébastien SERRE
	 * @package wp-openagenda
	 * @since
	 */
	public static function update_organisers( $id, $event ) {
		$organizer = get_posts( [
			'post_type'  => 'tribe_organizer',
			'meta_key'   => '_oa_event_uid',
			'meta_value' => $event['uid']
		] );

		if ( empty( $event['registration'] ) ) {
			return;
		}

		foreach ( $event['registration'] as $registration ) {
			switch ( $registration['type'] ) {
				case 'link':
					$url = $registration['value'];
					break;
				case 'phone':
					$phone = $registration['value'];
					break;
				case 'email':
					$mail = $registration['value'];
					break;
			}
		}
		$args = [
			'Organizer' => 'Organiser event ' . $event['uid'],
			'Phone'     => sanitize_text_field( $phone ),
			'Website'   => esc_url_raw( $url ),
			'Email'     => sanitize_email( $mail ),
		];

		foreach ( $organizer as $o ) {
			tribe_update_organizer( $o->ID, $args );
		}
	}

	/**
	 * Create Event from TEC to OA
	 *
	 * @param $post_id
	 * @param $event
	 *
	 * @author  Sébastien SERRE
	 * @package wp-openagenda
	 * @since
	 */
	public static function save_event( $post_id, $event ) {
		if ( 'tribe_events' === $event->post_type ) {
			$agendas = get_the_terms( $post_id, 'openagenda_agenda' );
			foreach ( $agendas as $agenda ) {
				$agenda_uid                   = OpenAgendaApi::openwp_get_uid( $agenda->name );
				$agenda_list[ $agenda->name ] = $agenda_uid;
			}
			$access_token = OpenAgendaApi::get_acces_token();
			$locale       = OpenAgendaApi::oa_get_locale();
			$options      = array( 'lang' => $locale );
			$eventuid     = get_post_meta( $post_id, '_oa_event_uid', true );

			// Create Route to OpenAgenda API
			if ( empty( $eventuid ) ) {
				//create
				$route = "https://api.openagenda.com/v2/agendas/$agenda_uid/events";
			} else {
				//update
				$route = "https://api.openagenda.com/v2/agendas/$agenda_uid/events/$eventuid";
			}

			extract(
				array_merge(
					[
						'lang' => $locale,
					],
					$options
				)
			);
			// General Datas
			// retrieve event keywords
			$keywords = wp_get_post_terms( $post_id, 'openagenda_keyword' );
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
					$excerpt = __( 'No data found', 'wp-openagenda' );
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

			$location = tribe_get_venue_id( $post_id );
			if ( ! empty( $location ) ) {
				$location_id = get_post_meta( $location, '_oa_event_uid', true );
			}

			// get date
			$dates = [
				'start_date' => sanitize_text_field( $_POST['EventStartDate'] ),
				'start_time' => sanitize_text_field( $_POST['EventStartTime'] ),
				'end_time'   =>
					sanitize_text_field( $_POST['EventEndTime'] ),
				'end_date'   => sanitize_text_field( $_POST['EventEndDate'] ),
				'tz'         => sanitize_text_field( $_POST['EventTimezone'] ),

			];

			$tz = self::get_time_zone( $dates );

			$i = 0;
			foreach ( $dates as $date ) {

				$timings[ $i ]['begin'] = date( "Y-m-d\TH:i:00$tz", $date['begin'] );
				$timings[ $i ]['end']   = date( "Y-m-d\TH:i:00$tz", $date['end'] );
				$i ++;
			}
		}
	}

	public static function add_venue_notice_metabox() {
		if ( 'tribe_events' !== get_post_type() ) {
			return;
		}
		add_meta_box( 'venue_metabox', 'Important Notice', [
			'OpenAgenda\TEC\The_Event_Calendar',
			'add_venue_notice_metabox_msg'
		], '', 'side', 'high' );

	}

	public static function get_time_zone( $dates ) {
		$tz = get_option( 'gmt_offset' );

		if ( ! empty( $dates['tz'] ) ) {
			$tz      = new DateTimeZone( $dates['tz'] );
			$tz_date = new DateTime( 'now', $tz );
			$tz      = $tz->getOffset( $tz_date );
			$tz      = $tz / 3600;
		}
		/**/
		$tz = explode( '-', $tz );
		if ( 2 === sizeof( $tz ) ) {
			//TZ is Neg
			if ( empty( $tz[0] ) ) {
				$sign = '-';
				$tz   = explode( '.', $tz[1] );
				if ( 1 === sizeof( $tz[0] ) ) {
					$tz[0] = '0' . $tz[0];
				}
				if ( 1 === sizeof( $tz[1] ) ) {
					$tz[1] = $tz[1] . '0';
				}
			} else {
				$sign = '+';
			}
		} else {
			$sign = '+';
			$tz   = explode( '.', $tz[0] );
			if ( 1 === sizeof( $tz[0] ) ) {
				$tz[0] = '0' . $tz[0];
			}
			if ( 1 === sizeof( $tz[1] ) ) {
				$tz[1] = $tz[1] . '0';
			}
			if ( empty( $tz[1] ) ) {
				$tz[1] = '00';
			}
		}


		$tz = $sign . $tz[0] . $tz[1];

		return $tz;
	}

	public static function add_venue_notice_metabox_msg() {

		?>
        <div style="background: #fe5000; color: white; padding: 5px 10px">
			<?php
			esc_attr_e( 'Please create your venue in openagenda.com first then sync in settings', 'wp-openagenda' );
			?>
        </div>
		<?php
	}

	public static function create_venue_in_oa( $post_id, $location ) {
		if ( 'tribe_venue' !== get_post_type() ) {
			return;
		}
		$address              = get_post_meta( $post_id, '_VenueAddress', true );
		$zip                  = get_post_meta( $post_id, '_VenueZip', true );
		$city                 = get_post_meta( $post_id, '_VenueCity', true );
		$address              = $address . ' ' . $zip . ' ' . $city;
		$locale               = OpenAgendaApi::oa_get_locale();
		$coord                = OpenAgendaApi::get_lat_lng( $address );
		$args['access_token'] = OpenAgendaApi::get_acces_token();
		$args['nonce']        = rand();
		$args['data']         = wp_json_encode(
			[
				'placename'   => $location->post_title,
				'description' => [
					$locale => $location->post_content,
				],
				'address'     => $address,
				'latitude'    => $coord['lat'],
				'longitude'   => $coord['long'],

			]
		);
		$uid                  = get_post_meta( $post_id, '_oa_event_uid', true );
		if ( empty( $uid ) ) {
			$route = 'https://api.openagenda.com/v1/locations';
		} else {
			$route = 'https://api.openagenda.com/v1/locations/' . $uid;
		}
		$ch = curl_init();

		curl_setopt( $ch, CURLOPT_URL, $route );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $args );
		$received_content = curl_exec( $ch );
		$data             = json_decode( $received_content, true );
		$location_uid     = $data['uid'];
		add_post_meta( $post_id, '_oa_event_uid', $location_uid );
	}
}

new The_Event_Calendar();
