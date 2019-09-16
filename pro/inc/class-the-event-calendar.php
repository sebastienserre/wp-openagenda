<?php

namespace OpenAgenda\TEC;

use OpenAgendaAPI\OpenAgendaApi;
use function add_action;
use function array_pop;
use function array_reverse;
use function class_exists;
use function date;
use function esc_attr;
use function esc_attr_e;
use function tribe_create_event;
use function tribe_create_venue;
use function tribe_update_event;
use function var_dump;
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
		if ( class_exists( 'Tribe__Events__Main' ) ){
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
		$args = [
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
			'meta_input'       => [
				'oa_event_uid' => $events['uid'],
			],
		];

		return $args;
	}

	public static function create_event( $id, $events, $dates ) {

		$date['start'] = array_pop( array_reverse( $dates ) );
		$date['start'] = $date['start']['field_5d61787c65c27'];
		$date['end']   = array_pop( $dates );
		$date['end']   = $date['end']['field_5d61789f65c28'];

		$data = self::prepare_data( $id, $events, $date );

		if ( empty( $id ) ) {
			$id = tribe_create_event( $data );
		} else {
			$id = tribe_update_event( $id, $data );
		}

		// insert Keywords
		wp_set_post_terms( $id, $events['keywords']['fr'], 'post_tag' );

		return $id;
	}
	
	/**
     * Create a venue in The Event Calendar from Openagenda.com
	 * @author  Sébastien SERRE
	 * @package wp-openagenda
	 * @since
	 */
	public static function create_venue() {
		$openagenda = new OpenAgendaApi();
		$url_oa     = $openagenda->get_agenda_list__premium_only();
		foreach ( $url_oa as $url ) {
			$uid       = $openagenda->openwp_get_uid( $url );
			$decoded[] = OpenAgendaApi::get_venue_oa( $uid );
		}
		if ( ! empty( $decoded ) ) {
			$venues = tribe_get_venues();
			if ( empty( $venues ) ) {
				foreach ( $decoded as $data ) {
					foreach ( $data['items'] as $location ) {
						$args = [
							'Venue'      => $location['name'],
							'Country'    => OpenAgendaApi::get_country( $location['countryCode'] ),
							'City'       => $location['postalCode'],
							'State'      => $location['countryCode'],
							'Province'   => $location['region'],
							'Zip'        => $location['postalCode'],
							'Address'    => $location['address'],
							'Phone'      => $location['phone'],
							'URL'        => $location['website'],
							'meta_input' => [
								'uid' => $location['uid'],
							]
						];
						$id   = tribe_create_venue( $args );
						OpenAgendaApi::upload_thumbnail( $location['image'], $id, $location['name'] );
					}
				}
			} else {
			    // update
			}
		}
	}

}
new The_Event_Calendar();
