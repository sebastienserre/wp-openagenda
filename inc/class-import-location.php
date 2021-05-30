<?php


namespace Openagenda\Location\Import;


use function get_locale;
use function get_posts;
use function implode;
use function substr;
use function wp_insert_post;

class Import_location {

	public static $instance;

	public function __construct(){
		self::$instance     = $this;
	}

	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function insert_location( $location ) {
		$locale = substr( get_locale(), 0, 2 );
		$args   = array(
			'post_type'  => 'venue',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'   => 'oa_location_uid',
					'value' => $location['uid'],
				),
			),
		);
		$venue  = get_posts( $args );
		$venue  = $venue[0];
		$id     = '';
		if ( ! empty( $venue ) ) {
			$id = $venue->ID;
		}
		$postarr = array(
			'ID'           => $id,
			'post_content' => $location['description'][ $locale ],
			'post_title'   => $location['name'],
			'post_type'    => 'venue',
			'post_status'   => 'publish',
			'meta_input'   => array(
				'oa_location_uid' => $location['uid'],
			),
		);
		$id      = wp_insert_post( $postarr );

		// ACF fields
		$address = implode(
			', ',
			array(
				$location['address'],
				$location['postalCode'],
				$location['city'],
				$location['region'],
				$location['region'],
				$location['countryCode'],
			),
		);
		$osm = array(
			'lat'     => $location['latitude'],
			'lng'     => $location['longitude'],
			'zoom'    => 12,
			'markers' => array(
				array(
					'label'         => $address,
					'default_label' => $address,
					'lat'           => $location['latitude'],
					'lng'           => $location['longitude'],
				),
			),
			'address' => $address,
			'layers'  => array(
				'OpenStreetMap.Mapnik',
			),
			'version' => '1.3.2',
		);
		update_field( 'oa_loc_address', $osm, $id );

		// Access
		update_field( 'oa_loc_access', $location['access'][$locale], $id );

		// Image Credits
		update_field( 'oa_loc_image_credits', $location['imageCredits'], $id );

		// Website
		update_field( 'oa_loc_website', $location['website'], $id );

		//Email
		update_field( 'oa_loc_e-mail', $location['email'], $id );

		//Phone
		update_field( 'oa_loc_phone', $location['phone'], $id );


		return $id;
	}

}
add_action( 'plugins_loaded', 'location', 20 );
function location(){
	//$openagenda = new Openagenda();
	$GLOBALS['OpenAgenda_Import_Location'] = new Import_location();
}

function OA_Import_locations(){
	return location();
}