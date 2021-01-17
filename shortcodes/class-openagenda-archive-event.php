<?php


namespace OpenAgenda\Shortcode\Archive;

use function add_shortcode;
use function get_field;
use function shortcode_atts;
use function var_dump;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Class OpenAgenda_Archive_event
 * Display an archive page with all your local events
 *
 * @package OpenAgenda\Shortcode\Archive
 * @since 1.7.8
 * @authors sebastienserre
 */
class OpenAgenda_Archive_event {
	public function __construct() {
		add_shortcode( 'openagenda-event', [ 'OpenAgenda\Shortcode\Archive\OpenAgenda_Archive_event', 'openagenda_event_sc' ] );
	}

	public static function openagenda_event_sc( $atts ) {
		$atts = shortcode_atts(
			[
				'agenda_url' => 'https://openagenda.com/thivinfo',
				'number'     => '10',
			],
			$atts,
			'openagenda-event'
		);
		$args =
			[
				'post_type'      => 'openagenda-events',
				'posts_per_page' => $atts['number'],
				'tax_query'      =>
					[
						'relation' => 'AND',
						[
							'taxonomy' => 'openagenda_agenda',
							'terms'    => $atts['agenda_url'],
							'field'    => 'name',
						],
					],
			];
		$events = get_posts( $args );
		$today = date( 'U' );
		foreach ( $events as $event ){
			$timings = get_field( 'oa_date', $event->ID );
			foreach ( $timings as $key => $timing ){
				if ( $today >= $timing['begin'] ){
					unset( $timings[ $key ] );
				}
			}
		}
	}

}
new  OpenAgenda_Archive_event();