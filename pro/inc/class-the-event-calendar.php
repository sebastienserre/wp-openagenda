<?php
	
	namespace OpenAgenda\TEC;
	
	use OpenAgendaAPI\OpenAgendaApi;
	use function add_action;
	use function add_post_meta;
	use function array_pop;
	use function array_reverse;
	use function class_exists;
	use function date;
	use function esc_attr_e;
	use function esc_url_raw;
	use function sanitize_email;
	use function sanitize_text_field;
	use function sanitize_url;
	use function tribe_create_event;
	use function tribe_create_organizer;
	use function tribe_create_venue;
	use function tribe_get_organizers;
	use function tribe_update_event;
	use function tribe_update_organizer;
	use function tribe_update_venue;
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
				$venued_id = self::create_venue( $events );
				add_post_meta( $id, '_EventVenueID', $venued_id );
				
				
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
		 * @author  Sébastien SERRE
		 * @package wp-openagenda
		 * @since
		 */
		public static function create_venue( $events ) {
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
		
	}
	
	new The_Event_Calendar();
