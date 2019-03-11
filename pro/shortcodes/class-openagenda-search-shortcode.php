<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

class Openagenda_search_shortcode {
	public function __construct() {
		add_shortcode( 'openagenda_search', array( $this, 'openwp_pro_search_sc' ) );
	}

	public function openwp_pro_search_sc( $atts ) {
		$atts = shortcode_atts(
			array(
				'agenda_url'      => '',
				'agenda_lang'     => '',
				'search_criteria' => '',
				'agenda_title'    => 'My Agenda',
				'agenda_heading'  => 'h2',

			),
			$atts,
			'openagenda_search'
		);
		self::openagenda_search( $atts );
	}

	public static function openagenda_search( $atts ) {
		wp_enqueue_script( 'dateOA', '', '', '', true );
		$criterias    = explode( ',', $atts['search_criteria'] );
		$openwp       = new OpenAgendaApi\OpenAgendaApi();
		$original_uid = $openwp->openwp_get_uid( $atts['agenda_url'] );
		if ( isset( $_GET['venue'] ) && $_GET['venue'] != 'All' ) {
			$uid = $_GET['venue'];
		} else {
			$uid = $original_uid;
		}
		$key   = $openwp->thfo_openwp_get_api_key();
		$embed = $openwp->openwp_get_embed( $uid, $key );
		ob_start();
		?>
		<div class="openagenda_search">
			<<?php echo esc_html( $atts['agenda_heading'] ); ?> class="openagenda_title"
			><?php echo esc_html( $atts['agenda_title'] ); ?> </<?php echo esc_html( $atts['agenda_heading'] ); ?>>

		<form>
			<?php

			foreach ( $criterias as $criteria ) {
				$funcname = 'openagenda_' . $criteria . '_html';
				if ( 0 <= strpos( $atts['search_criteria'], $criteria ) && $funcname != 'openagenda_venue_html' ) {

					echo self::$funcname( $uid, $embed, $atts );
				}

				if ( $funcname === 'openagenda_venue_html' ) {
					echo self::$funcname( $original_uid, $embed, $atts );
				}

			}

			?>
		</form>
		<iframe style="width:100%;" frameborder="0" scrolling="no" allowtransparency="allowtransparency"
		        class="cibulFrame cbpgbdy" data-oabdy
		        src="//openagenda.com/agendas/<?php echo $uid; ?>/embeds/<?php echo $embed; ?>/events?lang=fr"></iframe>
		<script type="text/javascript" src="//openagenda.com/js/embed/cibulBodyWidget.js"></script>
		</div>
		<?php

		return ob_get_clean( $html );
	}

	/**
	 * @param string $uid   Unique OpenAgenda agenda id.
	 * @param string $embed Unique Openagende embed code.
	 * @param array  $atts  VC params.
	 *
	 * @return string html code to display.
	 */
	public function openagenda_search_html( $uid, $embed, $atts ) {
		$html = '<p>' . __( 'Search', 'wp-openagenda' ) . '</p><div class="cbpgsc cibulSearch" data-oasc data-cbctl="' . $uid . '/' . $embed . '|' . $atts['agenda_lang'] . '" data-lang="fr"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulSearchWidget.js"></script>';

		return $html;
	}

	public function openagenda_date_html( $uid, $embed, $atts ) {

		if ( isset( $_GET['oaq']['from'] ) ) {
			$from = 'value="' . $_GET['oaq']['from'] . '"';
		}
		if ( isset( $_GET['oaq']['to'] ) ) {
			$to = 'value="' . $_GET['oaq']['to'] . '"';
		}
		$html = '<p>From: <input type="text" id="datepicker" class="dateFrom auto-select" name="oaq[from]"' . $from . '></p>';
		$html .= '<p>to: <input type="text" id="datepicker2" class="dateTo auto-select" name="oaq[to]" ' . $to . '></p>';
		$html .= '<script>
jQuery("input.dateTo").change(function(){
    //window.location.href = $(this).find(\'option:selected\').val();
    jQuery(this).parents("form").eq(0).submit();
});
</script>';

		return $html;
	}

	public function openagenda_tag_html( $uid, $embed, $atts ) {
		$html = '<p>' . __( 'Tags', 'wp-openagenda' ) . '</p><div class="cbpgtg cibulTags" data-oatg data-cbctl="' . $uid . '/' . $embed . '"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulTagsWidget.js"></script>';

		return $html;
	}

	public function openagenda_venue_html( $uid, $embed, $atts ) {

		$openwp = new OpenAgendaApi\OpenAgendaApi();
		$key    = $openwp->thfo_openwp_get_api_key();
		$lieu   = $openwp->openwp_get_oa_slug( $uid, $key );

		$html = 'Lieu:';
		$html .= '<select class="auto-select" name="venue">';
		$html .= '<option>' . __( 'All', 'wp-openagenda' ) . '</option>';
		foreach ( $lieu as $key => $l ) {
			$selected = selected( $_GET['venue'], $key, false );
			$html     .= '<option value="' . $key . '" ' . $selected . '>' . $l . '</option>';
		}
		$html .= '</select>';
		$html .= '<script>
jQuery("select.auto-select").change(function(){
    //window.location.href = $(this).find(\'option:selected\').val();
    jQuery(this).parents("form").eq(0).submit();
});
 </script>';


		return $html;
	}

	public function openagenda_category_html( $uid, $embed, $atts ) {
		$html = '<p>' . __( 'Category', 'wp-openagenda' ) . '</p><div class="cbpgct cibulCategories" data-oact data-cbctl="' . $uid . '/' . $embed . '" data-lang="' . $atts['agenda_lang'] . '"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulCategoriesWidget.js"></script>';

		return $html;
	}

}

new Openagenda_search_shortcode();
