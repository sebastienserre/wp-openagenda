<?php

use OpenAgendaAPI\OpenAgendaApi;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

class OpenagendaSliderShortcode {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'openwp_slider_register_scripts' ) );

		add_shortcode( 'openagenda_slider', array( $this, 'openwp_slider_sc' ) );
	}

	public function openwp_slider_sc( $atts ) {
		$atts = shortcode_atts( array(
			'agenda_url'             => '',
			'title'                  => '',
			'agenda_url_intern'      => '',
			'agenda_text'            => '',
			'agenda_title_color'     => '',
			'agenda_date_color'      => '',
			'agenda_date_text_color' => '',
			'agenda_lieu'            => 'true',
			'number'                 => '10',
			'display_description'    => '',
		), $atts, 'openagenda_slider' );

		wp_enqueue_script( 'slickjs' );
		wp_enqueue_script( 'openagendaSliderJS' );
		wp_enqueue_style( 'slickcss' );
		wp_enqueue_style( 'slickthemecss' );

		return $this->openwp_slider_html( $atts );
	}

	public function openwp_slider_register_scripts() {
		wp_register_script( 'slickjs', THFO_OPENWP_PLUGIN_URL . 'assets/slick/slick.min.js', array( 'jquery' ) );
		$openagendasliderjs = apply_filters( 'openwp_openagendasliderjs', THFO_OPENWP_PLUGIN_URL . 'assets/js/openagenda_slick.js' );
		wp_register_script( 'openagendaSliderJS', $openagendasliderjs, array(
			'jquery',
			'slickjs',
		) );
		wp_register_style( 'slickcss', THFO_OPENWP_PLUGIN_URL . 'assets/slick/slick.css' );
		wp_register_style( 'slickthemecss', THFO_OPENWP_PLUGIN_URL . 'assets/slick/slick-theme.css', array('slickcss') );
	}

	public function openwp_slider_html( $atts, $display_title = true ) {
		$openwp = new OpenAgendaApi();
		if ( empty( $atts['number'] ) ) {
			$atts['number'] = '10';
		}
		$datas     = $openwp->thfo_openwp_retrieve_data( $atts['agenda_url'], $atts['number'] );
		$car_exist = strpos( $atts['title'], '%' );

		if ( $car_exist ) {
			$title = explode( '%', $atts['title'] );

			$title = $title[0] . '<span class="bloc-openagenda__title" >' . $title[1] . '</span> ' . $title[2];
		} else {
			$title = $atts['title'];
		}

		ob_start(); ?>
		<div class="bloc-openagenda">

		<?php

		if ( $display_title === true ) {
			?>
			<h2 class="bloc-openagenda__title openagenda"
			    style="color:<?php echo $atts['agenda_title_color'] ?>"> <?php echo $title ?></h2>
		<?php }

		if ( 0 === $datas['total'] ) {
			?>
			<p><?php _e( 'Sorry, no events found', 'wp-openagenda' ) ?></p>
			<?php
		} else {
			?>
			<div class="bloc-openagenda__slider">
				<?php

				foreach ( $datas['events'] as $event ) {

					if ( empty( $atts['agenda_url_intern'] ) ) {
						$url = $atts['agenda_url'] . '?oaq%5Buids%5D%5B0%5D=' . $event['uid'];
					} else {
						$url = $atts['agenda_url_intern'] . '?oaq%5Buids%5D%5B0%5D=' . $event['uid'];
					}
					$date = $event['range']['fr'];

					if ( strlen( $event['title']['fr'] ) > '59' ) {
						$event['title']['fr'] = substr( $event['title']['fr'], 0, 59 ) . '...';
					}

					?>
					<div class="bloc-openagenda__slide">
						<div class="bloc-openagenda__pic">
							<a class="bloc-openagenda__link" href="<?php echo $url; ?>"
							   style="color:<?php echo $atts['agenda_date_text_color']; ?>;"><img
										src="<?php echo $event['image']; ?>"/>
							</a>
						</div> <!-- bloc-openagenda__pic -->

						<div class="bloc-openagenda__date"
						     style="background: <?php echo $atts['agenda_date_color']; ?>; color:<?php echo $atts['agenda_date_text_color']; ?>">
							<div class="bloc-openagenda__box-title bloc-openagenda__box-title--uppercase"
							     style=" color:<?php echo $atts['agenda_date_text_color'] ?>">
								<a class="bloc-openagenda__link" href="<?php echo $url; ?>"
								   style="color:<?php echo $atts['agenda_date_text_color']; ?>;">
									<p class="oa_date"><?php echo $date ?></p>
									<p class="oa_title"><?php echo $event['title']['fr'] ?></p>
								</a>
							</div>
							<?php if( 'true' === $atts['display_description'] ){ ?>
							<div class="oa_description">
								<p><?php echo $event['description']['fr']; ?></p>
							</div>
						<?php } ?>
							<?php
							if ( 'true' === $atts['agenda_lieu'] ) {
								?>
								<a class="bloc-openagenda__link" href="<?php echo $url; ?>"
								   style="color:<?php echo $atts['agenda_date_text_color']; ?>;">
									<p class="bloc-openagenda__lieu"><?php echo $event['locationName']; ?></p>
								</a>
								<?php
							}
							?>

							<div class="arrows-bottom"></div>
						</div> <!-- bloc-openagenda__date -->
					</div> <!-- bloc-openagenda__slide -->
					<?php
				}
				?>
			</div><!-- bloc-openagenda__slider -->

			<?php
			if ( ! empty( $atts['agenda_url_text'] ) ) {
				?>
				<a href="<?php echo $atts['agenda_url']; ?>"> <?php echo $atts['agenda_url_text']; ?></a>
				<?php
			}
			?>
			</div><!-- bloc-openagenda -->
			<?php
		}

		return ob_get_clean();
	}
}

new OpenagendaSliderShortcode();
