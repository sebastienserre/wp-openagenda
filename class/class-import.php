<?php


namespace WPOpenAgenda\API\Import;

use DateTime;
use WPOpenAgenda\API\Openagenda;
use function add_query_arg;
use function carbon_set_post_meta;
use function date;
use function get_locale;
use function get_posts;
use function is_null;
use function strtotime;
use function substr;
use function update_post_meta;
use function var_dump;
use function wp_date;
use function wp_insert_post;
use function wp_remote_get;
use function wp_remote_retrieve_body;
use function wp_remote_retrieve_response_code;
use function wp_verify_nonce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

class Import {

	public static $instance;
	public $key;
	public $uid;
	public $post_type;

	public function __construct() {
		self::$instance  = $this;
		$this->key       = Openagenda::$instance->get_api_key();
		$this->uid       = Openagenda::$instance->get_agenda_uid();
		$this->post_type = Openagenda::$instance->get_post_type();

		add_action( 'admin_init', array( $this, 'force_import' ) );
	}

	public static function instance() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function force_import() {
		if ( ! empty( $_GET['oaimport'] ) && 'now' === $_GET['oaimport'] && wp_verify_nonce( $_GET['_wpnonce'], 'force_sync' ) ) {
			$this->import_events();
		}
	}

	public function import_events(){
		$uid = Openagenda::$instance->agenda_uid;
		$url = "https://openagenda.com/agendas/$uid/events.json";
		$events = $this->get_data( $url );

		if ( empty($events['events'] ) ) {
			return;
		}
		$events = $events[ 'events'];
		foreach ( $events as $event ){
			$lang = Openagenda::$instance->get_event_lang( $event );
			if ( ! empty( $events['longDescription'][$lang] ) && is_null( $events['longDescription'][$lang] ) ) {
				$events['longDescription'][$lang] = $events['description'][$lang];
			}

			$args = array(
				'post_type'   => $this->post_type,
				'meta_key'    => '_oa_event_uid',
				'meta_value'  => $event['uid'],
				'post_status' => 'publish',
			);

			// Get Existing Events
			$openagenda_events = get_posts(	$args );

			// Detect ID if exists
			if ( ! empty( $openagenda_events ) ) {
				$id = $openagenda_events[0]->ID;
			} else {
				// Set ID as NULL, so that it doesn't take the previous value
				$id = '';
			}

			$args = array(
				'ID'             => $id,
				'post_content'   => $event['longDescription'][$lang],
				'post_title'     => $event['title'][$lang],
				'post_excerpt'   => $event['description'][$lang],
				'post_status'    => 'publish',
				'post_type'      => $this->post_type,
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'meta_input'     => array(
					'_oa_conditions' => $event['conditions'][$lang],
					'_oa_event_uid'  => $event['uid'],
					'_oa_tools'      => $event['registrationUrl'],
					'_oa_min_age'    => $event['age']['min'],
					'_oa_max_age'    => $event['age']['max'],
				),
			);
			$insert = wp_insert_post( $args );

			//Import Event UID
			if ( ! empty( $event['uid'] ) ){
				update_post_meta( $insert, '_oa_event_uid', $event['uid'] );
			}

			//handicap
			if ( ! empty( $event['accessibility'] ) ) {
				$i = 0;
				foreach ( $event['accessibility'] as $accessibility ) {
					$a11y[] = $accessibility;
					$i ++;
				}
					carbon_set_post_meta( $insert, 'oa_a11y', $a11y );
				unset( $i );
			}

			// Date Formating
			$dates = array();
			$i = 0;
			foreach ( $event['timings'] as $timing ) {
				$begin_date = new DateTime( $timing['start'] );
				$start_date = $begin_date->format( 'U' );
				$start_timestamp = $begin_date->getTimestamp();

				$end = new DateTime( $timing['end'] );
				$end_date = $end->format( 'U' );
				$end_timestamp = $end->getTimestamp();


				$dates[$i]['oa_start'] = $start_timestamp;
				$dates[$i]['oa_end'] = $end_timestamp;

				carbon_set_post_meta( $insert, 'oa_event_date', $dates );

				$i++;
			}
		}
	}

	public function get_data( $url, $nb = 10, $when = 'current' ) {
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
		if ( $this->uid ) {

			$url          = add_query_arg( array(
				'key'   => $this->key,
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

}
new Import();
