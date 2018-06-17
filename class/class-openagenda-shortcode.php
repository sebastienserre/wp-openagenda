<?php
/**
 * Generate WordPress Shortcodes
 * @package openagenda-shortcode
 */

/**
 * Class OpenAgenda_Shortcode
 */
class OpenAgenda_Shortcode {

	/**
	 * OpenAgenda_Shortcode constructor.
	 */
	public function __construct() {
		add_shortcode( 'openwp_basic', array( $this, 'thfo_openwp_basic_shortcode' ) );
	}

	/**
	 * Display a basic Shortcode
	 * [openwp_basic slug='your_slug' nb=11 lang='fr']
	 * slug => string with slug of agenda.
	 * nb => Number of events to display.
	 * lang => language to display (2 letters country code: en/fr...)

	 * @param array $atts contain slug, nb and languages.
	 * @return array|mixed|object|string
	 */
	public function thfo_openwp_basic_shortcode( $atts ) {

		$atts   = shortcode_atts( array(
			'slug' => 'test',
			'nb' => 10,
			'lang' => 'fr'
		), $atts, 'openwp_basic' );
		$openwp = new OpenAgendaApi();

		$openwp_data = $openwp->thfo_openwp_retrieve_data( $atts['slug'], $atts['nb'] );

		ob_start();
		foreach ($openwp_data['events'] as $events) {
		//	var_dump($events);
			?>
			<a href="<?php echo esc_url( $events['canonicalUrl'] ); ?>" target="_blank">
				<p><?php echo esc_attr( $events['range'][ $atts['lang'] ] ); ?></p>
				<img src="<?php echo esc_attr( $events['image'] ); ?>">
				<h3><?php echo esc_attr( $events['title'][ $atts['lang'] ] ); ?></h3>
				<p><?php echo esc_textarea( $events['longDescription'][ $atts['lang'] ] ); ?></p>
			</a>
			<hr>
			<?php
		}
		return ob_get_clean();
	}

}

new OpenAgenda_Shortcode();
