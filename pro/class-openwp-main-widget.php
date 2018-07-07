<?php
/**
 * Main class to display OpenAgenda Widget.
 *
 * @package openwp_widget
 */

/**
 * Class Openwp_Main_Widget
 */
class Openwp_Main_Widget {

	/**
	 * Openwp_Main_Widget constructor.
	 */
	public function __construct() {
	}

	/**
	 * Method to display OpenAgenda Widget.
	 *
	 * @param int    $widget Embeds uid.
	 * @param int    $uid    Agenda UID.
	 * @param array $atts   Shortcode attributs.
	 *
	 * @return string
	 */
	public function openwp_main_widget_html( $widget, $uid, $atts ) {
		//die('ooups');
		switch ( $atts['openagenda_type'] ) {
			case 'general':
				$openagenda_code = '<iframe style="width:100%;" frameborder="0" scrolling="no" allowtransparency="allowtransparency" class="cibulFrame cbpgbdy" data-oabdy src="//openagenda.com/agendas/' . $uid . '/embeds/' . $widget . '/events?lang=fr"></iframe><script type="text/javascript" src="//openagenda.com/js/embed/cibulBodyWidget.js"></script>';
				break;
			case 'map':
				$openagenda_code = '<div class="cbpgmp cibulMap" data-oamp data-cbctl="' . $uid . '/' . $widget . '" data-lang="fr" ></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulMapWidget.js"></script>';
				break;
			case 'search':
				$openagenda_code = '<div class="cbpgsc cibulSearch" data-oasc data-cbctl="' . $uid . '/' . $widget . '|fr" data-lang="fr"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulSearchWidget.js"></script>';
				break;
			case 'categories':
				$openagenda_code = '<div class="cbpgct cibulCategories" data-oact data-cbctl="' . $uid . '/' . $widget . '" data-lang="fr"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulCategoriesWidget.js"></script>';
				break;
			case 'tags':
				$openagenda_code = '<div class="cbpgtg cibulTags" data-oatg data-cbctl="' . $uid . '/' . $widget . '"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulTagsWidget.js"></script>';
				break;
			case 'calendrier':
				$openagenda_code = '<div class="cbpgcl cibulCalendar" data-oacl data-cbctl="' . $uid . '/' . $widget . '|fr" data-lang="fr"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulCalendarWidget.js"></script>';
				break;
			case 'preview':
				$openagenda_code = '<div class="oa-preview cbpgpr" data-oapr data-cbctl="' . $uid . '|fr"> 
  <a href="' . $url['url'] . '">Voir l\'agenda</a> 
</div><script src="//openagenda.com/js/embed/oaPreviewWidget.js"></script>';
				break;
		}

		ob_start();

		echo $openagenda_code;

		return ob_get_clean();
	}
}

new Openwp_Main_Widget();
