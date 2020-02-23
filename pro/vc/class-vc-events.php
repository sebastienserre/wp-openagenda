<?php
/**
 * Class to initialize a WP Bakery Visual Composer Element.
 *
 * @package VC_Events
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 *
 * Class VC_Events
 */
class Vc_Events {
	/**
	 * Vc_Events constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'openwp_vc_openagenda_element' ) );
		add_shortcode( 'openwp-vc-basic', array( $this, 'openwp_vc_retrieve_info' ) );
	}

	/**
	 * Initialize a new VC Element.
	 */
	public function openwp_vc_openagenda_element() {

		$params = array(

			array(
				'type'        => 'textfield',
				'holder'      => 'h3',
				'class'       => 'title-class',
				'heading'     => __( 'Title', 'wp-openagenda' ),
				'param_name'  => 'title',
				'value'       => __( 'Title', 'wp-openagenda' ),
				'admin_label' => false,
				'weight'      => 0,
				'group'       => __( 'Settings', 'wp-openagenda' ),
			),

			array(
				'type'        => 'textfield',
				'holder'      => 'a',
				'class'       => 'url-class',
				'heading'     => __( 'Internal URL of Main Agenda Page:', 'wp-openagenda' ),
				'param_name'  => 'agenda_url',
				'value'       => esc_url( site_url() ),
				'description' => __( 'Internal URL of Main Agenda Page. You must create a page integrating OpenAgenda', 'wp-openagenda' ),
				'admin_label' => false,
				'weight'      => 0,
				'group'       => __( 'Settings', 'wp-openagenda' ),
			),

			array(
				'type'        => 'textfield',
				'holder'      => 'p',
				'class'       => 'openagenda_lang',
				'heading'     => __( 'Languages of events:', 'wp-openagenda' ),
				'param_name'  => 'lang',
				'description' => __( 'Choose the lang to display.', 'wp-openagenda' ),
				'admin_label' => false,
				'value'       => '',
				'weight'      => 0,
				'group'       => __( 'Settings', 'wp-openagenda' ),
			),
			array(
				'type'        => 'textfield',
				'holder'      => 'p',
				'class'       => 'title-class',
				'heading'     => __( 'Nb of event', 'wp-openagenda' ),
				'param_name'  => 'nb_event',
				'value'       => '1',
				'description' => __( 'Number of Events', 'wp-openagenda' ),
				'admin_label' => false,
				'weight'      => 0,
				'group'       => __( 'Settings', 'wp-openagenda' ),
			),


		);
		vc_map( array(
				'name'        => __( 'Event Openagenda', 'wp-openagenda' ),
				'base'        => 'openwp-vc-basic',
				'description' => __( 'Display Event from Openagenda', 'wp-openagenda' ),
				'category'    => __( 'OpenAgenda', 'wp-openagenda' ),
				'icon'        => THFO_OPENWP_PLUGIN_URL . '/assets/img/icon.jpg',
				'params'      => apply_filters('openwp_vc_params', $params),
			)
		);

	}

	/**
	 * Clean Special charactere found.
	 *
	 * @param string $string The String to clean.
	 *
	 * @return mixed
	 */
	public function clean( $string ) {
		$string = str_replace( ' ', '-', $string ); // Replaces all spaces with hyphens.
		$transliterationtable = array(
			'á' => 'a',
			'Á' => 'A',
			'à' => 'a',
			'À' => 'A',
			'ă' => 'a',
			'Ă' => 'A',
			'â' => 'a',
			'Â' => 'A',
			'å' => 'a',
			'Å' => 'A',
			'ã' => 'a',
			'Ã' => 'A',
			'ą' => 'a',
			'Ą' => 'A',
			'ā' => 'a',
			'Ā' => 'A',
			'ä' => 'ae',
			'Ä' => 'AE',
			'æ' => 'ae',
			'Æ' => 'AE',
			'ḃ' => 'b',
			'Ḃ' => 'B',
			'ć' => 'c',
			'Ć' => 'C',
			'ĉ' => 'c',
			'Ĉ' => 'C',
			'č' => 'c',
			'Č' => 'C',
			'ċ' => 'c',
			'Ċ' => 'C',
			'ç' => 'c',
			'Ç' => 'C',
			'ď' => 'd',
			'Ď' => 'D',
			'ḋ' => 'd',
			'Ḋ' => 'D',
			'đ' => 'd',
			'Đ' => 'D',
			'ð' => 'dh',
			'Ð' => 'Dh',
			'é' => 'e',
			'É' => 'E',
			'è' => 'e',
			'È' => 'E',
			'ĕ' => 'e',
			'Ĕ' => 'E',
			'ê' => 'e',
			'Ê' => 'E',
			'ě' => 'e',
			'Ě' => 'E',
			'ë' => 'e',
			'Ë' => 'E',
			'ė' => 'e',
			'Ė' => 'E',
			'ę' => 'e',
			'Ę' => 'E',
			'ē' => 'e',
			'Ē' => 'E',
			'ḟ' => 'f',
			'Ḟ' => 'F',
			'ƒ' => 'f',
			'Ƒ' => 'F',
			'ğ' => 'g',
			'Ğ' => 'G',
			'ĝ' => 'g',
			'Ĝ' => 'G',
			'ġ' => 'g',
			'Ġ' => 'G',
			'ģ' => 'g',
			'Ģ' => 'G',
			'ĥ' => 'h',
			'Ĥ' => 'H',
			'ħ' => 'h',
			'Ħ' => 'H',
			'í' => 'i',
			'Í' => 'I',
			'ì' => 'i',
			'Ì' => 'I',
			'î' => 'i',
			'Î' => 'I',
			'ï' => 'i',
			'Ï' => 'I',
			'ĩ' => 'i',
			'Ĩ' => 'I',
			'į' => 'i',
			'Į' => 'I',
			'ī' => 'i',
			'Ī' => 'I',
			'ĵ' => 'j',
			'Ĵ' => 'J',
			'ķ' => 'k',
			'Ķ' => 'K',
			'ĺ' => 'l',
			'Ĺ' => 'L',
			'ľ' => 'l',
			'Ľ' => 'L',
			'ļ' => 'l',
			'Ļ' => 'L',
			'ł' => 'l',
			'Ł' => 'L',
			'ṁ' => 'm',
			'Ṁ' => 'M',
			'ń' => 'n',
			'Ń' => 'N',
			'ň' => 'n',
			'Ň' => 'N',
			'ñ' => 'n',
			'Ñ' => 'N',
			'ņ' => 'n',
			'Ņ' => 'N',
			'ó' => 'o',
			'Ó' => 'O',
			'ò' => 'o',
			'Ò' => 'O',
			'ô' => 'o',
			'Ô' => 'O',
			'ő' => 'o',
			'Ő' => 'O',
			'õ' => 'o',
			'Õ' => 'O',
			'ø' => 'oe',
			'Ø' => 'OE',
			'ō' => 'o',
			'Ō' => 'O',
			'ơ' => 'o',
			'Ơ' => 'O',
			'ö' => 'oe',
			'Ö' => 'OE',
			'ṗ' => 'p',
			'Ṗ' => 'P',
			'ŕ' => 'r',
			'Ŕ' => 'R',
			'ř' => 'r',
			'Ř' => 'R',
			'ŗ' => 'r',
			'Ŗ' => 'R',
			'ś' => 's',
			'Ś' => 'S',
			'ŝ' => 's',
			'Ŝ' => 'S',
			'š' => 's',
			'Š' => 'S',
			'ṡ' => 's',
			'Ṡ' => 'S',
			'ş' => 's',
			'Ş' => 'S',
			'ș' => 's',
			'Ș' => 'S',
			'ß' => 'SS',
			'ť' => 't',
			'Ť' => 'T',
			'ṫ' => 't',
			'Ṫ' => 'T',
			'ţ' => 't',
			'Ţ' => 'T',
			'ț' => 't',
			'Ț' => 'T',
			'ŧ' => 't',
			'Ŧ' => 'T',
			'ú' => 'u',
			'Ú' => 'U',
			'ù' => 'u',
			'Ù' => 'U',
			'ŭ' => 'u',
			'Ŭ' => 'U',
			'û' => 'u',
			'Û' => 'U',
			'ů' => 'u',
			'Ů' => 'U',
			'ű' => 'u',
			'Ű' => 'U',
			'ũ' => 'u',
			'Ũ' => 'U',
			'ų' => 'u',
			'Ų' => 'U',
			'ū' => 'u',
			'Ū' => 'U',
			'ư' => 'u',
			'Ư' => 'U',
			'ü' => 'ue',
			'Ü' => 'UE',
			'ẃ' => 'w',
			'Ẃ' => 'W',
			'ẁ' => 'w',
			'Ẁ' => 'W',
			'ŵ' => 'w',
			'Ŵ' => 'W',
			'ẅ' => 'w',
			'Ẅ' => 'W',
			'ý' => 'y',
			'Ý' => 'Y',
			'ỳ' => 'y',
			'Ỳ' => 'Y',
			'ŷ' => 'y',
			'Ŷ' => 'Y',
			'ÿ' => 'y',
			'Ÿ' => 'Y',
			'ź' => 'z',
			'Ź' => 'Z',
			'ž' => 'z',
			'Ž' => 'Z',
			'ż' => 'z',
			'Ż' => 'Z',
			'þ' => 'th',
			'Þ' => 'Th',
			'µ' => 'u',
			'а' => 'a',
			'А' => 'a',
			'б' => 'b',
			'Б' => 'b',
			'в' => 'v',
			'В' => 'v',
			'г' => 'g',
			'Г' => 'g',
			'д' => 'd',
			'Д' => 'd',
			'е' => 'e',
			'Е' => 'E',
			'ё' => 'e',
			'Ё' => 'E',
			'ж' => 'zh',
			'Ж' => 'zh',
			'з' => 'z',
			'З' => 'z',
			'и' => 'i',
			'И' => 'i',
			'й' => 'j',
			'Й' => 'j',
			'к' => 'k',
			'К' => 'k',
			'л' => 'l',
			'Л' => 'l',
			'м' => 'm',
			'М' => 'm',
			'н' => 'n',
			'Н' => 'n',
			'о' => 'o',
			'О' => 'o',
			'п' => 'p',
			'П' => 'p',
			'р' => 'r',
			'Р' => 'r',
			'с' => 's',
			'С' => 's',
			'т' => 't',
			'Т' => 't',
			'у' => 'u',
			'У' => 'u',
			'ф' => 'f',
			'Ф' => 'f',
			'х' => 'h',
			'Х' => 'h',
			'ц' => 'c',
			'Ц' => 'c',
			'ч' => 'ch',
			'Ч' => 'ch',
			'ш' => 'sh',
			'Ш' => 'sh',
			'щ' => 'sch',
			'Щ' => 'sch',
			'ъ' => '',
			'Ъ' => '',
			'ы' => 'y',
			'Ы' => 'y',
			'ь' => '',
			'Ь' => '',
			'э' => 'e',
			'Э' => 'e',
			'ю' => 'ju',
			'Ю' => 'ju',
			'я' => 'ja',
			'Я' => 'ja'
		);

		return str_replace( array_keys( $transliterationtable ), array_values( $transliterationtable ), $string );
	}

	public function openwp_vc_retrieve_info( $atts ) {
		$atts = shortcode_atts( array(
			'agenda_url'        => '',
			'title'             => '',
			'lang'    => '',
			'nb_event'          => '1',
			),
			$atts, 'openwp-vc-basic'
		);

		$atts['openagenda_cat'] = $this->clean( $atts['openagenda_cat'] );
		$atts['openagenda_tag'] = $this->clean( $atts['openagenda_tag'] );


		$openwp = new OpenAgendaApi\OpenAgendaApi();
		$openwp_data = $openwp->thfo_openwp_retrieve_data( $atts['agenda_url'], $atts['nb_event'] );

		ob_start();
		$openwp->openwp_basic_html( $openwp_data, $atts['lang'], $atts['agenda_url'] );

		return ob_get_clean();

	}


}

new Vc_Events();
