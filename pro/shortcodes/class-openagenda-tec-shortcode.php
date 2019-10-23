<?php


namespace OpenAgenda\Shortcode\TEC;


use OpenAgenda\TEC\The_Event_Calendar;
use function add_shortcode;
use function apply_filters;
use function esc_attr;
use function get_the_permalink;
use function ob_get_flush;
use function ob_start;
use function shortcode_atts;
use function tribe_get_events;
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
				'contribute'     => 'yes',
			],
			$atts,
			'oa_tec_event_list'
		);
		echo $this->event_list_renderer( $atts );
	}

	public function event_list_renderer( $atts ) {
		$args   =
			[
				'posts_per_page' => $atts['nb'],
				'start_date'     => 'now',
			];
		$events = tribe_get_events( $args );
		ob_start();
		?>
		<div class="oa-event-list">
		<<?php echo esc_attr( $atts['agenda_heading'] ); ?>>
		<?php
		echo esc_attr( $atts['agenda_title'] );
		?>
		</<?php echo $atts['agenda_heading'] ?>>
		</div>
		<?php
		foreach ( $events as $event ){

			?>
			<div class="oa-single-event">
				<h4><a href="<?php echo get_the_permalink( $event->ID ); ?>"><?php echo $event->post_title; ?></a></h4>
                <div class="oa-event-meta">
                    <?php
                    echo The_Event_Calendar::display_date( $event->ID );
                    ?>
                </div>

			</div>
			<?php
		}
		$render = ob_get_clean();

		return apply_filters( 'event_list_renderer', $render );
	}
}

new openagenda_tec_shortcode();
