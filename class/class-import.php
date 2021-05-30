<?php


namespace WPOpenAgenda\Import;

use function add_action;
use function var_dump;
use function wp_verify_nonce;
use function WPOpenAgenda\API\openagenda;

class Import {
	public static $instance;
	public function __construct() {
		self::$instance = $this;
		add_action( 'admin_init', array( $this, 'import_locations' ) );

	}

	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function import_locations() {
		if ( ! empty( $_GET['oa-import-locations'] ) && 'now' === $_GET['oa-import-locations'] && wp_verify_nonce( $_GET['_wpnonce'], 'sync' ) ) {
			$agendaUid = openagenda()->agenda_uid;
			$key = openagenda()->get_api_key();
			$data = openagenda()->get_data( "agendas/$agendaUid/locations?detailed=1");
			if ( empty( $data['locations'] ) ){
				return;
			}
			foreach ( $data['locations'] as $location) {
				openagenda()->insert_location( $location );
			}
			return true;
		}
		return false;
	}
}
new Import();
if ( ! function_exists( 'oa_import' ) ) {
	function oa_import() {
		return Import::$instance;
	}
}
