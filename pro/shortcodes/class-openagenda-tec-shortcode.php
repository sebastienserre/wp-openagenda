<?php


namespace OpenAgenda\Shortcode\TEC;


use OpenAgenda\TEC\The_Event_Calendar;
use OpenAgendaAPI\OpenAgendaApi;
use function add_shortcode;
use function apply_filters;
use function date_i18n;
use function esc_attr;
use function get_the_permalink;
use function in_array;
use function ob_get_flush;
use function ob_start;
use function shortcode_atts;
use function tribe_get_events;
use function tribe_get_start_date;
use function var_dump;

class openagenda_tec_shortcode {
	public function __construct() {
		add_shortcode(
			'oa_tec_event_list',
			[
				$this,
				'event_list',
			]
		);
	}

	public function event_list( $atts ) {
		$atts = shortcode_atts(
			[
				'nb'             => 10,
				'agenda_title'   => 'My Agenda',
				'agenda_heading' => 'h2',
				'contribute'     => 'https://openagenda.com/thivinfo',
			],
			$atts,
			'oa_tec_event_list'
		);
		return $this->event_list_renderer( $atts );
	}

	public function event_list_renderer( $atts ) {
		$args       =
			[
				'posts_per_page' => $atts['nb'],
				'start_date'     => 'now',
			];
		$events     = tribe_get_events( $args );
		$agenda     = OpenAgendaApi::get_agenda_list__premium_only();
		$contribute = in_array( $atts['contribute'], $agenda );
		if ( $contribute ) {
			$text = sprintf( wp_kses( __( 'Have an Event to display here? <a href="%s">Add it!</a>', 'wp-openagenda'
			), array( 'a' => array( 'href' => array() ) ) ), esc_url( $atts['contribute'] ) );
			$text = apply_filters( 'openwp_custom_add_event_text', $text );
		}
		ob_start();
		?>
        <div class="oa-event-list">
        <<?php echo esc_attr( $atts['agenda_heading'] ); ?>>
		<?php
		echo esc_attr( $atts['agenda_title'] );
		?>
        </<?php echo $atts['agenda_heading'] ?>>

		<?php
		foreach ( $events as $event ) {
			$start = tribe_get_start_date( $event->ID, false, 'U' );
			$end = tribe_get_end_date( $event->ID, false, 'U' );
			$today = date_i18n( 'U' );
			if ( ( $start <= $today && $today <= $end ) || $start >= $today ) {
				?>
                <div class="oa-single-event">
                    <h4><a href="<?php echo get_the_permalink( $event->ID ); ?>"
                           target="_blank"><?php echo $event->post_title;
							?></a></h4>
                    <div class="oa-event-meta">
						<?php
						echo The_Event_Calendar::display_date( $event->ID );
						?>
                    </div>

                </div>
				<?php
			}
		}
		echo $text;
		?>
        </div>
		<?php
		$render = ob_get_clean();

		return apply_filters( 'event_list_renderer', $render );
	}
}

new openagenda_tec_shortcode();
