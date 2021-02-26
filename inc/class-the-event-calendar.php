<?php

namespace OpenAgenda\TEC;

use DateTime;
use DateTimeZone;
use OpenAgendaAPI\OpenAgendaApi;
use Tribe__Date_Utils;
use function add_action;
use function add_meta_box;
use function add_post_meta;
use function array_merge;
use function array_pop;
use function array_push;
use function array_reverse;
use function class_exists;
use function date;
use function date_i18n;
use function defined;
use function delete_transient;
use function esc_attr_e;
use function esc_url_raw;
use function explode;
use function extract;
use function get_post_meta;
use function get_post_type;
use function get_the_post_thumbnail_url;
use function get_the_terms;
use function get_transient;
use function implode;
use function intval;
use function json_decode;
use function openwp_debug;
use function print_r;
use function sanitize_email;
use function sanitize_text_field;
use function set_transient;
use function sizeof;
use function sprintf;
use function strlen;
use function strtotime;
use function substr;
use function tribe_create_event;
use function tribe_create_organizer;
use function tribe_create_venue;
use function tribe_get_option;
use function tribe_get_venue_id;
use function tribe_update_event;
use function tribe_update_organizer;
use function tribe_update_venue;
use function update_option;
use function var_dump;
use function wp_get_post_terms;
use function wp_kses;
use function wp_rand;
use function wp_set_post_terms;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_URL;
use const WP_DEBUG;

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
		//add_action( 'save_post_tribe_events', [ 'OpenAgenda\TEC\The_Event_Calendar', 'save_event' ], 20, 2 );
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
		$errors = get_option( 'tec_error' );
		if ( ! empty( $errors ) ) {
			foreach ( $errors as $error ) {
				?>
                <div class="notice notice-error">
                    <p>
						<?php
						echo $error['msg'];
						?>
                    </p>
                </div>
				<?php
			}
			delete_option( 'tec_error' );
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
	    if ( ! empty( $events['location'] ) ) {
		    $location = $events['location'];
	    }
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
		if ( 'tribe_events' !== get_post_type( $post_id ) ) {
			return;
		}
		openwp_debug( "[OA] start to export $post_id to OpenAgenda.com");
		if ( ! empty( $_POST['venue'] && empty( $_POST['venue']['VenueID'][0] ) ) ) {
			$error[] =
				[
					'id'  => $post_id,
					'msg' => __( 'No Venue ID, Event not sent to OpenAgenda', 'wp-openagenda' ),
				];
			update_option( 'tec_error', $error );
			openwp_debug( '[OA]:' .  $error[ 'msg' ] );
		}
		$terms = wp_get_post_terms( $post_id, 'openagenda_agenda' );
		if ( ! empty( $_POST ) && empty( $terms ) ) {
			$error[] =
				[
					'id'  => $post_id,
					'msg' => __( 'No Agenda Selected, Event not sent to OpenAgenda', 'wp-openagenda' ),
				];
			update_option( 'tec_error', $error );
			openwp_debug( '[OA]:' .  $error[ 'msg' ] );
		}
		if ( 'tribe_events' === $event->post_type && ! empty( $_POST['EventStartDate'] ) ) {

			$agendas = get_the_terms( $post_id, 'openagenda_agenda' );
			foreach ( $agendas as $agenda ) {
				$agenda_uid                   = OpenAgendaApi::openwp_get_uid( $agenda->name );
				$agenda_list[ $agenda->name ] = $agenda_uid;

			}
			$msg = implode( ', ', $agenda_list );
			openwp_debug( "[OA] Agenda Updated: $msg" );

			$access_token = OpenAgendaApi::get_acces_token();
			openwp_debug( "[OA] access_token: $access_token" );
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
			openwp_debug( "[OA] route used: $route" );

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
			$keywords = wp_get_post_terms( $post_id, 'post_tag' );
			if ( ! empty( $keywords ) ) {
				$keys = array();
				foreach ( $keywords as $keyword ) {
					array_push( $keys, $keyword->name );
				}
				$keywords = implode( ', ', $keys );
			}
			// format excerpt
			if ( ! empty( $event->post_content ) ) {
				if ( strlen( $event->post_content ) > 197 ) {
					$excerpt = substr( $event->post_content, 0, 197 );
				} else {
					$excerpt = $event->post_content;
				}
				$excerpt = sanitize_text_field( $excerpt ) . '...';
			} else {
				$excerpt = sanitize_text_field( $event->post_title );
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

			$registration         = esc_url_raw( $_POST['EventURL'] );
			$data['registration'] = [
				$registration,
			];
			if ( 'suffix' === $_POST['EventCurrencyPosition'] ) {
				$conditions = $_POST['EventCost'] . $_POST['EventCurrencySymbol'];
			} else {
				$conditions = $_POST['EventCurrencySymbol'] . $_POST['EventCost'];
			}
			$data['conditions'] =
				[
					$locale => $conditions,
				];

			$location = tribe_get_venue_id( $post_id );
			openwp_debug("[OA] Location: $location");
			if ( ! empty( $location ) ) {
				$location_id         = get_post_meta( $location, '_oa_event_uid', true );
				$data['locationUid'] = intval( $location_id );
				openwp_debug("[OA] LocationUID: $location_id");
			}

			// get date
			$format     = Tribe__Date_Utils::datepicker_formats( tribe_get_option( 'datepickerFormat' ) );
			$start      = DateTime::createFromFormat( $format, $_POST['EventStartDate'] );
			$end        = DateTime::createFromFormat( $format, $_POST['EventEndDate'] );
			$start_date = $start->format( 'U' );
			$end_date   = $end->format( 'U' );
			$diff       = intval( floor( ( $end_date - $start_date ) / 86400 ) );
			$end        = DateTime::createFromFormat( $format . ' g:ia', $_POST['EventStartDate'] . ' ' . $_POST['EventEndTime'] );
			$start      = DateTime::createFromFormat( $format . ' g:ia', $_POST['EventStartDate'] . ' ' . $_POST['EventStartTime'] );
			$end        = $end ? $end->format( 'U' ) : 0;
			$start      = $start ? $start->format( 'U' ) : 0;

			$i = 0;
			while ( $i <= $diff ) {
				$date['begin']          = $start + ( $i * DAY_IN_SECONDS );
				$date['end']            = $end + ( $i * DAY_IN_SECONDS );
				$begin                  = new DateTime( date( 'Y-m-d\TH:i:00', $date['begin'] ), new DateTimeZone( $_POST['EventTimezone'] ) );
				$begin                  = $begin->format( 'Y-m-d H:i:sP' );
				$timings[ $i ]['begin'] = $begin;
				$end                    = new DateTime( date( 'Y-m-d\TH:i:00', $date['end'] ), new DateTimeZone(
					$_POST['EventTimezone'] ) );
				$end                    = $end->format( 'Y-m-d H:i:sP' );

				$timings[ $i ]['end'] = $end;
				$i ++;
			}

			$data['timings'] = $timings;

			$data['image'] = [
				'url' => get_the_post_thumbnail_url( $event->ID ),
			];

			$posted = array(
				'access_token' => $access_token,
				'nonce'        => wp_rand(),
				'data'         => json_encode( $data ),
				'lang'         => $locale,
			);
			$ch     = curl_init();

			curl_setopt( $ch, CURLOPT_URL, $route );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $posted );

			$received_content = curl_exec( $ch );

			$decode = json_decode( $received_content, true );
			$msg = print_r( $decode, TRUE );
			openwp_debug( "[OA] OA return: $msg" );

			if ( ! empty( $decode['error'] ) ) {
				openwp_debug( '[OA] decode Error:' . $decode['error'] );
			}

			if ( empty( $decode['error'] ) ) {
				// update event uid
				$uid = intval( $decode['event']['uid'] );
				if ( $uid ) {
					update_post_meta( $event->ID, '_oa_event_uid', $uid );
				}
				openwp_debug( "[OA] Event ID: $uid" );
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
		$address = get_post_meta( $post_id, '_VenueAddress', true );
		$zip     = get_post_meta( $post_id, '_VenueZip', true );
		$city    = get_post_meta( $post_id, '_VenueCity', true );
		$address = $address . ' ' . $zip . ' ' . $city;
		$locale  = OpenAgendaApi::oa_get_locale();
		$coord   = OpenAgendaApi::get_lat_lng( $post_id, $address );
		if ( $coord ) {
			$args['access_token'] = OpenAgendaApi::get_acces_token();
			$args['nonce']        = wp_rand();
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

			$data         = json_decode( $received_content, true );
			$location_uid = $data['uid'];
		}
		$return = update_post_meta( $post_id, '_oa_event_uid', $location_uid );

		return $return;
	}

	public static function display_date( $id ) {

		$start = strtotime( get_post_meta( $id, '_EventStartDate', true ) );
		$end   = strtotime( get_post_meta( $id, '_EventEndDate', true ) );

		if ( empty( $start ) || empty( $end ) ) {
			$msg = __( 'No date for this event!', 'wp-openagenda' );
		}
		$msg = sprintf( __( '<p>From %1$s to %2$s</p>', 'wp-openagenda' ), date_i18n( 'd F Y G\Hi', $start ),
			date_i18n( 'd F Y G\Hi', $end ) );

		if ( ! empty( $start ) && ! empty( $end ) ) {
			$start = date_i18n( 'd F Y', $start );
			$end   = date_i18n( 'd F Y', $end );

			if ( $start === $end ) {
				$msg = sprintf( __( 'On %s', 'wp-openagenda' ), $end );
			}
		}

		return $msg;
	}
}

new The_Event_Calendar();
