<?php

namespace OpenAgendaAPI;

use finfo;
use function add_action;
use function add_query_arg;
use function array_key_exists;
use function basename;
use function delete_post_meta;
use function download_url;
use function esc_url;
use function filesize;
use function get_locale;
use function get_transient;
use function is_wp_error;
use function preg_replace;
use function set_post_thumbnail;
use function set_transient;
use WP_Error;
use function strtoupper;
use function substr;
use function update_post_meta;
use function var_dump;
use function wc_strtoupper;
use function wp_check_filetype;
use function wp_generate_attachment_metadata;
use function wp_handle_sideload;
use function wp_insert_attachment;
use function wp_remote_get;
use function wp_remote_retrieve_body;
use function wp_remote_retrieve_response_code;
use function wp_update_attachment_metadata;
use function wp_upload_dir;
use const DAY_IN_SECONDS;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Retrieve Event data from OpenAgenda.com
 * Set of methods to retrieve data from OpenAgenda
 *
 * @author  : sebastienserre
 * @package Openagenda-api
 * @since   1.0.0
 *
 * @package: OpenAgendaApi.
 */
class OpenAgendaApi {

	/**
	 * OpenAgendaApi constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'openagenda_check_api', array( $this, 'check_api' ) );
	}

	/**
	 * Get API stored in Options
	 *
	 * @return int OpenAgenda API Key
	 * @since 1.0.0
	 */
	public static function thfo_openwp_get_api_key() {
		$key = get_option( 'openagenda_api' );

		return $key;
	}

	/**
	 * Get Slug from an URL.
	 *
	 * @param string $slug URL of openagenda.com Agenda.
	 *
	 * @return string
	 * @author sebastienserre
	 * @since  1.0.0
	 */
	public static function openwp_get_slug( $slug ) {
		$re = '/[a-zA-Z\.\/:]*\/([a-zA-Z\.\/:\0-_9]*)/';

		preg_match( $re, $slug, $matches, PREG_OFFSET_CAPTURE, 0 );

		$slug = untrailingslashit( $matches[1][0] );

		return $slug;
	}

	/**
	 * Retrieve data from Openagenda
	 *
	 * @param string $slug Slug of your agenda.
	 * @param int    $nb   Number of event to retrieve. Default 10.
	 * @param string $when Date of events. default: 'current'.
	 *                     all: events from 01/01/1970 to 31/12/2050.
	 *                     current: from today to 31/12/2050.
	 *                     past: from 01/01/1970 to today.
	 *
	 * @return array|mixed|object|string
	 * @author sebastienserre
	 * @since  1.0.0
	 */
	public static function thfo_openwp_retrieve_data( $url, $nb = 10, $when = 'current' ) {
		if ( empty( $url ) ) {
			return '<p>' . __( 'You forgot to add an agenda\'s url to retrieve', 'wp-openagenda' ) . '</p>';
		}
		if ( empty( $nb ) ) {
			$nb = 10;
		}

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

		$key = self::thfo_openwp_get_api_key();
		$uid = self::openwp_get_uid( $url );
		if ( $uid ) {

			$url          = 'https://openagenda.com/agendas/' . $uid . '/events.json';
			$url          = add_query_arg( array(
				'key'   => $key,
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

	/**
	 * Retrieve OpenAenda UID.
	 *
	 * @param string $slug OpenAgenda Agenda URL.
	 *
	 * @return int Agenda UID
	 * @author sebastienserre
	 * @since  1.0.0
	 */
	public static function openwp_get_uid( $slug ) {
		return get_option( 'openagenda-uid' );
	}

	/**
	 * Display a WP Widget
	 *
	 * @param $openwp_data array array with Event from OpenAgenda
	 * @param $lang        string 2 letters for your lang (refer to OA doc)
	 * @param $instance
	 *
	 * @author sebastienserre
	 * @since  1.0.0
	 */
	public function openwp_basic_html( $openwp_data, $lang, $instance ) {
		if ( is_array( $instance ) ) {
			if ( empty( $instance['url'] ) ) {
				/**
				 * @todo Y'a un truc chelou la!
				 */
				$instance['openwp_url'] = $instance['openwp_url'];
			} else {
				$instance['openwp_url'] = $instance['url'];
			}
			$slug = $instance['openwp_url'];
		}

		if ( null === $lang ) {
			$lang = 'fr';
		}
		?>
        <div class="openwp-events">
            <!-- OpenAgenda for WordPress Plugin downloadable for free on https://wordpress.org/plugins/wp-openagenda/-->
			<?php
			do_action( 'openwp_before_html' );
			$parsedown = new \Parsedown();

			foreach ( $openwp_data['events'] as $events ) {
				?>
                <div class="openwp-event">
                    <a class="openwp-event-link" href="<?php echo esc_url( $events['canonicalUrl'] ); ?>"
                       target="_blank">
                        <p class="openwp-event-range"><?php echo esc_attr( $events['range'][ $lang ] ); ?></p>
						<?php
						if ( false !== $events['image'] && 'yes' === $instance['openwp_img'] ) {
							?>
                            <img class="openwp-event-img" src="<?php echo esc_attr( $events['image'] ); ?>">
							<?php
						}
						?>
						<?php if ( 'yes' === $instance['event-title'] && ! empty( $events['title'][ $lang ] ) ) { ?>
                            <h3 class="openwp-event-title"><?php echo esc_attr( $events['title'][ $lang ] ); ?></h3>
						<?php } else {
							?>
                            <h3 class="openwp-event-title"><?php echo esc_attr( $events['title']['en'] ); ?></h3>
							<?php

						} ?>
						<?php if ( 'yes' === $instance['event-description'] && ! empty( $events['description'][ $lang ] ) ) { ?>
                        <p class="openwp-event-description"><?php echo $parsedown->text( esc_textarea( $events['description'][ $lang ] ) ); ?></p>
                    </a>
					<?php } else {
						?>
                        <p class="openwp-event-description"><?php echo $parsedown->text( esc_textarea( $events['description']['en'] ) ); ?></p>
						<?php
					} ?>

                </div>
				<?php
			}
			do_action( 'openwp_after_html' );
			// translators: this is a link to add events in Openagenda.com.
			$text = sprintf( wp_kses( __( 'Have an Event to display here? <a href="%s">Add it!</a>', 'wp-openagenda' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( $slug ) );
			$text = apply_filters( 'openwp_custom_add_event_text', $text );
			echo $text;

			?>
        </div>
		<?php
	}

	/**
	 * Method to display OpenAgenda Widget.
	 *
	 * @param int   $embed Embeds uid.
	 * @param int   $uid   Agenda UID.
	 * @param array $atts  Shortcode attributs.
	 *
	 * @return string
	 */
	public function openwp_main_widget_html__premium_only( $embed, $uid, $atts ) {
		if ( null === $embed ) {
			return __( 'This agenda doesn\'t have any embeds layout in their params.<br> We\'re sorry, but wen can\'t display it :(', 'wp-openagenda' );

		}
		if ( empty( $atts['agenda_nb'] ) ){
			$atts['agenda_nb'] = 10;
        }
		switch ( $atts['widget'] ) {
			case 'general':
				$openagenda_code = '<iframe style="width:100%;" frameborder="0" scrolling="no" allowtransparency="allowtransparency" class="cibulFrame cbpgbdy" data-oabdy src="//openagenda.com/agendas/' . $uid . '/embeds/' . $embed . '/events?lang=fr"></iframe><script type="text/javascript" src="//openagenda.com/js/embed/cibulBodyWidget.js"></script>';
				break;
			case 'map':
				$openagenda_code = '<div class="cbpgmp cibulMap" data-oamp data-cbctl="' . $uid . '/' . $embed . '" data-lang="fr" data-count="' . $atts['agenda_nb'] . '" ></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulMapWidget.js"></script>';
				break;
			case 'search':
				$openagenda_code = '<div class="cbpgsc cibulSearch" data-oasc data-cbctl="' . $uid . '/' . $embed . '|fr" data-lang="fr"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulSearchWidget.js"></script>';
				break;
			case 'categories':
				$openagenda_code = '<div class="cbpgct cibulCategories" data-oact data-cbctl="' . $uid . '/' . $embed . '" data-lang="fr"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulCategoriesWidget.js"></script>';
				break;
			case 'tags':
				$openagenda_code = '<div class="cbpgtg cibulTags" data-oatg data-cbctl="' . $uid . '/' . $embed . '"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulTagsWidget.js"></script>';
				break;
			case 'calendrier':
				$openagenda_code = '<div class="cbpgcl cibulCalendar" data-oacl data-cbctl="' . $uid . '/' . $embed . '|fr" data-lang="fr"></div><script type="text/javascript" src="//openagenda.com/js/embed/cibulCalendarWidget.js"></script>';

				break;
			case 'preview':
				$openagenda_code = '<div class="oa-preview cbpgpr" data-oapr data-cbctl="' . $uid . '|' . $atts['lang'] . '"><a href="' . $atts['url'] . '">Voir l\'agenda</a> 
</div><script src="//openagenda.com/js/embed/oaPreviewWidget.js"></script>';
				break;
		}

		ob_start();

		echo $openagenda_code;

		return ob_get_clean();
	}

	/**
	 * Retrieved OpenAgenda embed Code
	 *
	 * @param int    $uid OpenAgenda ID.
	 * @param string $key OpenAgenda API Key.
	 *
	 * @return array|WP_Error
	 */
	public function openwp_get_embed( $uid, $key ) {
		$embed = wp_remote_get( 'https://openagenda.com/agendas/' . $uid . '/settings.json?key=' . $key );
		if ( 200 === (int) wp_remote_retrieve_response_code( $embed ) ) {
			$body         = wp_remote_retrieve_body( $embed );
			$decoded_body = json_decode( $body, true );
		}
		if ( empty( $decoded_body['embeds'] ) ) {
			$embed = null;
		} else {
			$embed = $decoded_body['embeds'][0];
		}

		return $embed;
	}

	/**
	 * Get OA Agenda slug name
	 *
	 * @param $uid
	 * @param $key
	 *
	 * @return array
	 */
	public function openwp_get_oa_slug( $uid, $key ) {
		$agenda = wp_remote_get( 'https://openagenda.com/agendas/' . $uid . '/events.json?lang=fr&key=' . $key );
		//$agenda = wp_remote_get( 'https://openagenda.com/agendas/35241188/events.json?lang=fr&key=hd2cmLOQHQgrfJqRTd7qW45TQ43eFVgN' );
		if ( 200 === (int) wp_remote_retrieve_response_code( $agenda ) ) {
			$body         = wp_remote_retrieve_body( $agenda );
			$decoded_body = json_decode( $body, true );
			foreach ( $decoded_body['events'] as $event ) {
				$slug          = $event['origin']['slug'];
				$org           = $event['origin']['uid'];
				$slugs[ $org ] = $slug;
			}

			$slugs = array_unique( $slugs );
		}

		return $slugs;
	}

	/**
	 * Check if API Key is Valid or not
	 */
	public function check_api() {
		$check = $this->openwp_get_uid( 'https://openagenda.com/thivinfo' );
		if ( null === $check ) {
			?>
            <div class="notice notice-error openagenda-notice">
                <p><?php _e( 'Woot! Your API Key seems to be non valid', 'wp-openagenda' ); ?></p>
                <p><?php printf( __( '<a href="%s" target="_blank">Find help</a>', 'wp-openagenda' ), esc_url( 'https://thivinfo.com/docs/openagenda-pour-wordpress/' ) ); ?></p>
            </div>
			<?php
		} else {
			?>
            <div class="notice notice-success openagenda-notice"><?php _e( 'OpenAgenda API Key valid', 'wp-openagenda' ); ?></div>
			<?php
		}

	}

	/**
	 * Get OA list
	 *
	 * @return array key: term_id Value; term name (OA URL)
	 */
	public static function get_agenda_list__premium_only() {
	    $url_oa = null;
		/**
		 * Get List of Agenda
		 */
		$terms = get_terms( array(
			'taxonomy'   => 'openagenda_agenda',
			'hide_empty' => false,
		) );
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$url_oa[ $term->term_id ] = $term->name;
			}
		}

		return $url_oa;
	}

	/**
	 * Retrieve venue from OpenAgenda
	 *
	 * @param $uid int OpenAgenda UID
	 *
	 * @return array|int|WP_Error
	 */
	public function get_venue__premium_only( $uid ) {

		$args   = array(
			'taxonomy'   => 'openagenda_venue',
			'hide_empty' => false,
			'meta_key'   => '_oa_location_uid',
			'meta_value' => $uid,
		);
		$venues = get_terms(
			$args
		);

		return $venues;
	}

	/**
	 * Retrieve Secret Key from Options
	 *
	 * @return string Secret Key from OpenAgenda
	 */
	public function get_secret_key__premium_only() {

		$secret = get_option( 'openagenda_secret' );

		return $secret;
	}

	/**
	 * Retrieve access token to Openagenda data.
	 *
	 * @return string return Openagenda token.
	 */
	public static function get_acces_token() {
		$transient = get_transient( 'openagenda_secret' );
		if ( empty( $transient ) ) {
			$secret = self::get_secret_key__premium_only();
			$args   = array(
				'sslverify' => false,
				'timeout'   => 15,
				'body'      => array(
					'grant_type' => 'authorization_code',
					'code'       => $secret,
				),
			);

			$ch = wp_remote_post( 'https://api.openagenda.com/v2/requestAccessToken', $args );

			if ( 200 === (int) wp_remote_retrieve_response_code( $ch ) ) {
				$body         = wp_remote_retrieve_body( $ch );
				$decoded_body = json_decode( $body, true );
				$token        = $decoded_body['access_token'];
				set_transient( 'openagenda_secret', $decoded_body['access_token'], $decoded_body['expires_in'] );

			}
		} else {
			$token = $transient;
		}

		return $token;

	}

	/**
	 * @param $agenda_uid
	 *
	 * @return array List of venue registred for this Agenda.
	 * @author  Sébastien SERRE
	 * @package wp-openagenda
	 * @since
	 */
	public static function get_venue_oa( $agenda_uid ) {
		$json = wp_remote_get( 'https://openagenda.com/agendas/' . $agenda_uid . '/locations.json' );
		if ( 200 === (int) wp_remote_retrieve_response_code( $json ) ) {
			$body         = wp_remote_retrieve_body( $json );
			$decoded_body = json_decode( $body, true );
		}

		return $decoded_body;
	}

	public static function get_venue( $locationID ) {
		$key = self::thfo_openwp_get_api_key();
        $uid = get_option( 'openagenda-uid' );

		$json = wp_remote_get( "https://api.openagenda.com/v2/agendas/$uid/locations/$locationID?key=$key" );

		if ( 200 === (int) wp_remote_retrieve_response_code( $json ) ) {
			$body         = wp_remote_retrieve_body( $json );
			$decoded_body = json_decode( $body, true );
		}

		return $decoded_body;
	}

	/**
	 * @param $url   string image url
	 * @param $id    int id of post to link the image with
	 * @param $title string title of the image
	 *
	 * @author  Sébastien SERRE
	 * @package wp-openagenda
	 * @since
	 */
	public static function upload_thumbnail( $url, $id, $title ) {

		require_once( ABSPATH . 'wp-admin/includes/file.php' );

		// Download file to temp dir
		$timeout_seconds = 5;
		$url             = 'https:' . $url;
		// Download file to temp dir.
		$temp_file = download_url( $url, $timeout_seconds );
		if ( ! is_wp_error( $temp_file ) ) {

			$filename = basename( $url );
			$mime     = self::get_file_mime_type( $filename );
			// Array based on $_FILE as seen in PHP file uploads.
			$file = array(
				'name'     => $filename, // ex: wp-header-logo.png
				'type'     => $mime,
				'tmp_name' => $temp_file,
				'error'    => 0,
				'size'     => filesize( $temp_file ),
			);

			$overrides = array(
				/*
				 * Tells WordPress to not look for the POST form fields that would
				 * normally be present, default is true, we downloaded the file from
				 * a remote server, so there will be no form fields.
				 */
				'test_form'   => false,

				// Setting this to false lets WordPress allow empty files, not recommended.
				'test_size'   => true,

				// A properly uploaded file will pass this test. There should be no reason to override this one.
				'test_upload' => true,
			);

			// Move the temporary file into the uploads directory.
			$results       = wp_handle_sideload( $file, $overrides );
			$wp_upload_dir = wp_upload_dir();

			if ( empty( $results['error'] ) ) {

				$filename      = $results['file']; // Full path to the file.
				$filetype      = wp_check_filetype( $filename, null );
				$attachment    = array(
					'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
					'post_mime_type' => $filetype['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
					'post_content'   => '',
					'post_status'    => 'inherit',
				);
				$attachment_id = wp_insert_attachment( $attachment, $filename, $id );
				update_post_meta( $attachment_id, '_wp_attachment_image_alt', $title );

				// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
				require_once ABSPATH . 'wp-admin/includes/image.php';

				// Generate the metadata for the attachment, and update the database record.
				$attach_data = wp_generate_attachment_metadata( $attachment_id, $filename );
				wp_update_attachment_metadata( $attachment_id, $attach_data );
				set_post_thumbnail( $id, $attachment_id );
			}
		}
	}

	/**
	 * Retrieve the file mime type
	 *
	 * @param      $filename
	 * @param bool $debug
	 * @source https://chrisjean.com/generating-mime-type-in-php-is-not-magic/
	 *
	 * @return array|mixed|string
	 * @author  Sébastien SERRE
	 * @package wp-openagenda
	 * @since
	 */
	public static function get_file_mime_type( $filename, $debug = false ) {
		if ( function_exists( 'finfo_open' ) && function_exists( 'finfo_file' ) && function_exists( 'finfo_close' ) ) {
			$fileinfo  = \finfo_open( FILEINFO_MIME );
			$mime_type = \finfo_file( $fileinfo, $filename );
			finfo_close( $fileinfo );

			if ( ! empty( $mime_type ) ) {
				if ( true === $debug ) {
					return array( 'mime_type' => $mime_type, 'method' => 'fileinfo' );
				}

				return $mime_type;
			}
		}
		if ( function_exists( 'mime_content_type' ) ) {
			$mime_type = mime_content_type( $filename );

			if ( ! empty( $mime_type ) ) {
				if ( true === $debug ) {
					return array( 'mime_type' => $mime_type, 'method' => 'mime_content_type' );
				}

				return $mime_type;
			}
		}

		$mime_types = array(
			'ai'      => 'application/postscript',
			'aif'     => 'audio/x-aiff',
			'aifc'    => 'audio/x-aiff',
			'aiff'    => 'audio/x-aiff',
			'asc'     => 'text/plain',
			'asf'     => 'video/x-ms-asf',
			'asx'     => 'video/x-ms-asf',
			'au'      => 'audio/basic',
			'avi'     => 'video/x-msvideo',
			'bcpio'   => 'application/x-bcpio',
			'bin'     => 'application/octet-stream',
			'bmp'     => 'image/bmp',
			'bz2'     => 'application/x-bzip2',
			'cdf'     => 'application/x-netcdf',
			'chrt'    => 'application/x-kchart',
			'class'   => 'application/octet-stream',
			'cpio'    => 'application/x-cpio',
			'cpt'     => 'application/mac-compactpro',
			'csh'     => 'application/x-csh',
			'css'     => 'text/css',
			'dcr'     => 'application/x-director',
			'dir'     => 'application/x-director',
			'djv'     => 'image/vnd.djvu',
			'djvu'    => 'image/vnd.djvu',
			'dll'     => 'application/octet-stream',
			'dms'     => 'application/octet-stream',
			'doc'     => 'application/msword',
			'dvi'     => 'application/x-dvi',
			'dxr'     => 'application/x-director',
			'eps'     => 'application/postscript',
			'etx'     => 'text/x-setext',
			'exe'     => 'application/octet-stream',
			'ez'      => 'application/andrew-inset',
			'flv'     => 'video/x-flv',
			'gif'     => 'image/gif',
			'gtar'    => 'application/x-gtar',
			'gz'      => 'application/x-gzip',
			'hdf'     => 'application/x-hdf',
			'hqx'     => 'application/mac-binhex40',
			'htm'     => 'text/html',
			'html'    => 'text/html',
			'ice'     => 'x-conference/x-cooltalk',
			'ief'     => 'image/ief',
			'iges'    => 'model/iges',
			'igs'     => 'model/iges',
			'img'     => 'application/octet-stream',
			'iso'     => 'application/octet-stream',
			'jad'     => 'text/vnd.sun.j2me.app-descriptor',
			'jar'     => 'application/x-java-archive',
			'jnlp'    => 'application/x-java-jnlp-file',
			'jpe'     => 'image/jpeg',
			'jpeg'    => 'image/jpeg',
			'jpg'     => 'image/jpeg',
			'js'      => 'application/x-javascript',
			'kar'     => 'audio/midi',
			'kil'     => 'application/x-killustrator',
			'kpr'     => 'application/x-kpresenter',
			'kpt'     => 'application/x-kpresenter',
			'ksp'     => 'application/x-kspread',
			'kwd'     => 'application/x-kword',
			'kwt'     => 'application/x-kword',
			'latex'   => 'application/x-latex',
			'lha'     => 'application/octet-stream',
			'lzh'     => 'application/octet-stream',
			'm3u'     => 'audio/x-mpegurl',
			'man'     => 'application/x-troff-man',
			'me'      => 'application/x-troff-me',
			'mesh'    => 'model/mesh',
			'mid'     => 'audio/midi',
			'midi'    => 'audio/midi',
			'mif'     => 'application/vnd.mif',
			'mov'     => 'video/quicktime',
			'movie'   => 'video/x-sgi-movie',
			'mp2'     => 'audio/mpeg',
			'mp3'     => 'audio/mpeg',
			'mpe'     => 'video/mpeg',
			'mpeg'    => 'video/mpeg',
			'mpg'     => 'video/mpeg',
			'mpga'    => 'audio/mpeg',
			'ms'      => 'application/x-troff-ms',
			'msh'     => 'model/mesh',
			'mxu'     => 'video/vnd.mpegurl',
			'nc'      => 'application/x-netcdf',
			'odb'     => 'application/vnd.oasis.opendocument.database',
			'odc'     => 'application/vnd.oasis.opendocument.chart',
			'odf'     => 'application/vnd.oasis.opendocument.formula',
			'odg'     => 'application/vnd.oasis.opendocument.graphics',
			'odi'     => 'application/vnd.oasis.opendocument.image',
			'odm'     => 'application/vnd.oasis.opendocument.text-master',
			'odp'     => 'application/vnd.oasis.opendocument.presentation',
			'ods'     => 'application/vnd.oasis.opendocument.spreadsheet',
			'odt'     => 'application/vnd.oasis.opendocument.text',
			'ogg'     => 'application/ogg',
			'otg'     => 'application/vnd.oasis.opendocument.graphics-template',
			'oth'     => 'application/vnd.oasis.opendocument.text-web',
			'otp'     => 'application/vnd.oasis.opendocument.presentation-template',
			'ots'     => 'application/vnd.oasis.opendocument.spreadsheet-template',
			'ott'     => 'application/vnd.oasis.opendocument.text-template',
			'pbm'     => 'image/x-portable-bitmap',
			'pdb'     => 'chemical/x-pdb',
			'pdf'     => 'application/pdf',
			'pgm'     => 'image/x-portable-graymap',
			'pgn'     => 'application/x-chess-pgn',
			'png'     => 'image/png',
			'pnm'     => 'image/x-portable-anymap',
			'ppm'     => 'image/x-portable-pixmap',
			'ppt'     => 'application/vnd.ms-powerpoint',
			'ps'      => 'application/postscript',
			'qt'      => 'video/quicktime',
			'ra'      => 'audio/x-realaudio',
			'ram'     => 'audio/x-pn-realaudio',
			'ras'     => 'image/x-cmu-raster',
			'rgb'     => 'image/x-rgb',
			'rm'      => 'audio/x-pn-realaudio',
			'roff'    => 'application/x-troff',
			'rpm'     => 'application/x-rpm',
			'rtf'     => 'text/rtf',
			'rtx'     => 'text/richtext',
			'sgm'     => 'text/sgml',
			'sgml'    => 'text/sgml',
			'sh'      => 'application/x-sh',
			'shar'    => 'application/x-shar',
			'silo'    => 'model/mesh',
			'sis'     => 'application/vnd.symbian.install',
			'sit'     => 'application/x-stuffit',
			'skd'     => 'application/x-koan',
			'skm'     => 'application/x-koan',
			'skp'     => 'application/x-koan',
			'skt'     => 'application/x-koan',
			'smi'     => 'application/smil',
			'smil'    => 'application/smil',
			'snd'     => 'audio/basic',
			'so'      => 'application/octet-stream',
			'spl'     => 'application/x-futuresplash',
			'src'     => 'application/x-wais-source',
			'stc'     => 'application/vnd.sun.xml.calc.template',
			'std'     => 'application/vnd.sun.xml.draw.template',
			'sti'     => 'application/vnd.sun.xml.impress.template',
			'stw'     => 'application/vnd.sun.xml.writer.template',
			'sv4cpio' => 'application/x-sv4cpio',
			'sv4crc'  => 'application/x-sv4crc',
			'swf'     => 'application/x-shockwave-flash',
			'sxc'     => 'application/vnd.sun.xml.calc',
			'sxd'     => 'application/vnd.sun.xml.draw',
			'sxg'     => 'application/vnd.sun.xml.writer.global',
			'sxi'     => 'application/vnd.sun.xml.impress',
			'sxm'     => 'application/vnd.sun.xml.math',
			'sxw'     => 'application/vnd.sun.xml.writer',
			't'       => 'application/x-troff',
			'tar'     => 'application/x-tar',
			'tcl'     => 'application/x-tcl',
			'tex'     => 'application/x-tex',
			'texi'    => 'application/x-texinfo',
			'texinfo' => 'application/x-texinfo',
			'tgz'     => 'application/x-gzip',
			'tif'     => 'image/tiff',
			'tiff'    => 'image/tiff',
			'torrent' => 'application/x-bittorrent',
			'tr'      => 'application/x-troff',
			'tsv'     => 'text/tab-separated-values',
			'txt'     => 'text/plain',
			'ustar'   => 'application/x-ustar',
			'vcd'     => 'application/x-cdlink',
			'vrml'    => 'model/vrml',
			'wav'     => 'audio/x-wav',
			'wax'     => 'audio/x-ms-wax',
			'wbmp'    => 'image/vnd.wap.wbmp',
			'wbxml'   => 'application/vnd.wap.wbxml',
			'wm'      => 'video/x-ms-wm',
			'wma'     => 'audio/x-ms-wma',
			'wml'     => 'text/vnd.wap.wml',
			'wmlc'    => 'application/vnd.wap.wmlc',
			'wmls'    => 'text/vnd.wap.wmlscript',
			'wmlsc'   => 'application/vnd.wap.wmlscriptc',
			'wmv'     => 'video/x-ms-wmv',
			'wmx'     => 'video/x-ms-wmx',
			'wrl'     => 'model/vrml',
			'wvx'     => 'video/x-ms-wvx',
			'xbm'     => 'image/x-xbitmap',
			'xht'     => 'application/xhtml+xml',
			'xhtml'   => 'application/xhtml+xml',
			'xls'     => 'application/vnd.ms-excel',
			'xml'     => 'text/xml',
			'xpm'     => 'image/x-xpixmap',
			'xsl'     => 'text/xml',
			'xwd'     => 'image/x-xwindowdump',
			'xyz'     => 'chemical/x-xyz',
			'zip'     => 'application/zip'
		);

		$ext = strtolower( array_pop( explode( '.', $filename ) ) );

		if ( ! empty( $mime_types[ $ext ] ) ) {
			if ( true === $debug ) {
				return array( 'mime_type' => $mime_types[ $ext ], 'method' => 'from_array' );
			}

			return $mime_types[ $ext ];
		}

		if ( true === $debug ) {
			return array( 'mime_type' => 'application/octet-stream', 'method' => 'last_resort' );
		}

		return 'application/octet-stream';
	}

	/**
	 * @param $code string two letters code
	 *
	 * @return string
	 * @author  Sébastien SERRE
	 * @package wp-openagenda
	 * @since
	 */
	public static function get_country( $code ) {


		$countries = array
		(
			'AF' => 'Afghanistan',
			'AX' => 'Aland Islands',
			'AL' => 'Albania',
			'DZ' => 'Algeria',
			'AS' => 'American Samoa',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AQ' => 'Antarctica',
			'AG' => 'Antigua And Barbuda',
			'AR' => 'Argentina',
			'AM' => 'Armenia',
			'AW' => 'Aruba',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'AZ' => 'Azerbaijan',
			'BS' => 'Bahamas',
			'BH' => 'Bahrain',
			'BD' => 'Bangladesh',
			'BB' => 'Barbados',
			'BY' => 'Belarus',
			'BE' => 'Belgium',
			'BZ' => 'Belize',
			'BJ' => 'Benin',
			'BM' => 'Bermuda',
			'BT' => 'Bhutan',
			'BO' => 'Bolivia',
			'BA' => 'Bosnia And Herzegovina',
			'BW' => 'Botswana',
			'BV' => 'Bouvet Island',
			'BR' => 'Brazil',
			'IO' => 'British Indian Ocean Territory',
			'BN' => 'Brunei Darussalam',
			'BG' => 'Bulgaria',
			'BF' => 'Burkina Faso',
			'BI' => 'Burundi',
			'KH' => 'Cambodia',
			'CM' => 'Cameroon',
			'CA' => 'Canada',
			'CV' => 'Cape Verde',
			'KY' => 'Cayman Islands',
			'CF' => 'Central African Republic',
			'TD' => 'Chad',
			'CL' => 'Chile',
			'CN' => 'China',
			'CX' => 'Christmas Island',
			'CC' => 'Cocos (Keeling) Islands',
			'CO' => 'Colombia',
			'KM' => 'Comoros',
			'CG' => 'Congo',
			'CD' => 'Congo, Democratic Republic',
			'CK' => 'Cook Islands',
			'CR' => 'Costa Rica',
			'CI' => 'Cote D\'Ivoire',
			'HR' => 'Croatia',
			'CU' => 'Cuba',
			'CY' => 'Cyprus',
			'CZ' => 'Czech Republic',
			'DK' => 'Denmark',
			'DJ' => 'Djibouti',
			'DM' => 'Dominica',
			'DO' => 'Dominican Republic',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'SV' => 'El Salvador',
			'GQ' => 'Equatorial Guinea',
			'ER' => 'Eritrea',
			'EE' => 'Estonia',
			'ET' => 'Ethiopia',
			'FK' => 'Falkland Islands (Malvinas)',
			'FO' => 'Faroe Islands',
			'FJ' => 'Fiji',
			'FI' => 'Finland',
			'FR' => 'France',
			'GF' => 'French Guiana',
			'PF' => 'French Polynesia',
			'TF' => 'French Southern Territories',
			'GA' => 'Gabon',
			'GM' => 'Gambia',
			'GE' => 'Georgia',
			'DE' => 'Germany',
			'GH' => 'Ghana',
			'GI' => 'Gibraltar',
			'GR' => 'Greece',
			'GL' => 'Greenland',
			'GD' => 'Grenada',
			'GP' => 'Guadeloupe',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GG' => 'Guernsey',
			'GN' => 'Guinea',
			'GW' => 'Guinea-Bissau',
			'GY' => 'Guyana',
			'HT' => 'Haiti',
			'HM' => 'Heard Island & Mcdonald Islands',
			'VA' => 'Holy See (Vatican City State)',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran, Islamic Republic Of',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IM' => 'Isle Of Man',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JE' => 'Jersey',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KI' => 'Kiribati',
			'KR' => 'Korea',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyzstan',
			'LA' => 'Lao People\'s Democratic Republic',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LS' => 'Lesotho',
			'LR' => 'Liberia',
			'LY' => 'Libyan Arab Jamahiriya',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MO' => 'Macao',
			'MK' => 'Macedonia',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MV' => 'Maldives',
			'ML' => 'Mali',
			'MT' => 'Malta',
			'MH' => 'Marshall Islands',
			'MQ' => 'Martinique',
			'MR' => 'Mauritania',
			'MU' => 'Mauritius',
			'YT' => 'Mayotte',
			'MX' => 'Mexico',
			'FM' => 'Micronesia, Federated States Of',
			'MD' => 'Moldova',
			'MC' => 'Monaco',
			'MN' => 'Mongolia',
			'ME' => 'Montenegro',
			'MS' => 'Montserrat',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'MM' => 'Myanmar',
			'NA' => 'Namibia',
			'NR' => 'Nauru',
			'NP' => 'Nepal',
			'NL' => 'Netherlands',
			'AN' => 'Netherlands Antilles',
			'NC' => 'New Caledonia',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NE' => 'Niger',
			'NG' => 'Nigeria',
			'NU' => 'Niue',
			'NF' => 'Norfolk Island',
			'MP' => 'Northern Mariana Islands',
			'NO' => 'Norway',
			'OM' => 'Oman',
			'PK' => 'Pakistan',
			'PW' => 'Palau',
			'PS' => 'Palestinian Territory, Occupied',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar',
			'RE' => 'Reunion',
			'RO' => 'Romania',
			'RU' => 'Russian Federation',
			'RW' => 'Rwanda',
			'BL' => 'Saint Barthelemy',
			'SH' => 'Saint Helena',
			'KN' => 'Saint Kitts And Nevis',
			'LC' => 'Saint Lucia',
			'MF' => 'Saint Martin',
			'PM' => 'Saint Pierre And Miquelon',
			'VC' => 'Saint Vincent And Grenadines',
			'WS' => 'Samoa',
			'SM' => 'San Marino',
			'ST' => 'Sao Tome And Principe',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'RS' => 'Serbia',
			'SC' => 'Seychelles',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SK' => 'Slovakia',
			'SI' => 'Slovenia',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia And Sandwich Isl.',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SD' => 'Sudan',
			'SR' => 'Suriname',
			'SJ' => 'Svalbard And Jan Mayen',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syrian Arab Republic',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TH' => 'Thailand',
			'TL' => 'Timor-Leste',
			'TG' => 'Togo',
			'TK' => 'Tokelau',
			'TO' => 'Tonga',
			'TT' => 'Trinidad And Tobago',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks And Caicos Islands',
			'TV' => 'Tuvalu',
			'UG' => 'Uganda',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'GB' => 'United Kingdom',
			'US' => 'United States',
			'UM' => 'United States Outlying Islands',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan',
			'VU' => 'Vanuatu',
			'VE' => 'Venezuela',
			'VN' => 'Viet Nam',
			'VG' => 'Virgin Islands, British',
			'VI' => 'Virgin Islands, U.S.',
			'WF' => 'Wallis And Futuna',
			'EH' => 'Western Sahara',
			'YE' => 'Yemen',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe',
		);
		$country   = array_key_exists( strtoupper( $code ), $countries );

		if ( $country ) {
			$country = $countries[ strtoupper( $code ) ];
		}

		return $country;

	}

	public static function oa_get_locale() {
		$locale = get_locale();
		$locale = substr( $locale, 0, 2 );

		return $locale;
	}

	public static function get_lat_lng( $id, $address ) {
		$address = rawurlencode( $address );
		$coord   = get_transient( 'geocode_' . $address );
		if ( false === $coord  ) {
			$url  = 'http://nominatim.openstreetmap.org/?format=json&addressdetails=1&q=' . $address . '&format=json&limit=1';
			$json = wp_remote_get( $url );
			if ( 200 === (int) wp_remote_retrieve_response_code( $json ) ) {
				$body = wp_remote_retrieve_body( $json );
				$json = json_decode( $body, true );
			}
			if ( ! empty( $json[0]['lat'] ) || ! empty( $json[0]['lon'] ) ) {
				$coord['lat']  = $json[0]['lat'];
				$coord['long'] = $json[0]['lon'];

				set_transient( 'geocode_' . $address, $coord, DAY_IN_SECONDS * 30 );
				delete_post_meta( $id, 'geocode_error' );
			} else {
			    $error_msg = __('Address not found - We\'re using zipcode and city', 'wp-openagenda' );
			    update_post_meta( $id, 'geocode_error', $error_msg );
				$zip     = get_post_meta( $id, '_VenueZip', true );
				$city    = get_post_meta( $id, '_VenueCity', true );
				$address = rawurlencode($zip . ' ' . $city );
				$url     = 'http://nominatim.openstreetmap.org/?format=json&addressdetails=1&q=' . $address . '&format=json&limit=1';
				$json    = wp_remote_get( $url );
				if ( 200 === (int) wp_remote_retrieve_response_code( $json ) ) {
					$body = wp_remote_retrieve_body( $json );
					$json = json_decode( $body, true );
					if ( ! empty( $json[0]['lat'] ) || ! empty( $json[0]['lon'] ) ) {
						$coord['lat']  = $json[0]['lat'];
						$coord['long'] = $json[0]['lon'];

						set_transient( 'geocode_' . $address, $coord, DAY_IN_SECONDS * 30 );
					}
				}
			}
		}
		return $coord;
	}

	public static function get_event_lang( $event ){
	    if ( !empty( $event['description'] ) ){
	        $site_locale = get_locale();
	        $site_lang = substr( $site_locale, 0, 2 );
	        foreach ( $event['description'] as $lang => $description ){
	            $event_lang = $lang;
	            if ($lang === $site_lang ){
	                $event_lang = $lang;
	            }
	        }
	    }
	    return $event_lang;
	}
}

new OpenAgendaApi();
