<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

add_action( 'created_openagenda_agenda', 'openwp_launch_import_on_new_agenda', 10, 2);
function openwp_launch_import_on_new_agenda( $term_id, $tt_id ) {
	$agenda = get_term_by( 'id', $term_id, 'openagenda_agenda');
	$agenda = $agenda->name;
	import_oa_events__premium_only( $agenda );
}

add_action( 'admin_init', 'openwp_sync_from_admin' );
function openwp_sync_from_admin(){
	if ( ! empty( $_GET['sync'] && 'now' === $_GET[ 'sync'] && wp_verify_nonce( $_GET['_wpnonce'], 'force_sync' ) ) ){
		import_oa_events__premium_only();
	}
}

function openwp_display_date( $start, $end ){
	if ( empty( $start ) ){
		$msg = __( 'No date for this event!', 'wp-openagenda' );
	}
	if ( empty( $end ) ){
		$msg = sprintf( __( 'On %s', 'wp-openagenda' ), $end );
	}

	if ( ! empty( $start ) && !empty( $end ) ){
		$msg = sprintf( __( 'From %1$s to %2$s', 'wp-openagenda' ), $start, $end );

		if( $start === $end ){
			$msg = sprintf( __( 'On %s', 'wp-openagenda' ), $end );
		}
	}
	return $msg;
}

function openwp_display_age( $min_age, $max_age){

	if ( empty( $min_age ) ){
		$msg = __( 'Welcome Everybody!', 'wp-openagenda' );
	}
	if ( empty( $end ) ){
		$msg = sprintf( __( 'From %s years old', 'wp-openagenda' ), $min_age );
	}

	if ( ! empty( $start ) && !empty( $end ) ){
		$msg = sprintf( __( 'From %1$s years old to %2$s years old', 'wp-openagenda' ), $min_age, $max_age );

		if( $min_age === $max_age ){
			$msg = sprintf( __( 'From %s years old', 'wp-openagenda' ), $end );
		}
	}
	return $msg;
}

function openwp_display_accessibilty( $id ){
	$a11y      = carbon_get_post_meta( $id, 'oa_a11y' );
if ( ! empty( $a11y ) ) {
	ob_start();
}
?>
<div class="oa-a11y">
	<?php
	foreach ( $a11y as $access ) {
		switch ( $access ) {
			case 'mi':
				$name = __( 'Accessible to disabled people', 'wp-openagenda' );
				break;
			case 'hi':
				$name = __( 'Accessible to the hearing impaired', 'wp-openagenda' );
				break;
			case 'pi':
				$name = __( 'Accessible to the psychic handicapped', 'wp-openagenda' );
				break;
			case 'vi':
				$name = __( 'Accessible to visually impaired', 'wp-openagenda' );
				break;
			case 'sl':
				$name = __( 'Accessible in sign language', 'wp-openagenda' );
				break;

		}
		?>
		<p class="oa-a11y-details oa-<?php echo $access ?>"><?php echo $name
			?></p>
		<?php
	}


	?>
</div>

<?php
$a11y = ob_get_clean();
 return apply_filters( 'oa_a11y_display', $a11y );
}

// Load Template

add_filter( 'template_include', 'openwp_choose_template' );
function openwp_choose_template( $template ){
	// Post ID
	$post_id = get_the_ID();

	// For all other CPT
	if ( get_post_type( $post_id ) != 'openagenda-events' ) {
		return $template;
	}

	// Else use custom template
	if ( is_single() ) {
		return openwp_get_template_hierarchy( 'single' );
	}
}

function openwp_get_template_hierarchy( $template ){
	// Get the template slug
	$template_slug = rtrim( $template, '.php' );
	$template = $template_slug . '.php';

	// Check if a custom template exists in the theme folder, if not, load the plugin template file
	if ( $theme_file = locate_template( array( 'openagenda/' . $template ) ) ) {
		$file = $theme_file;
	}
	else {
		$file = THFO_OPENWP_PLUGIN_PATH . '/pro/template/' . $template;
	}

	return apply_filters( 'rc_repl_template_' . $template, $file );
}

function MediaFileAlreadyExists($filename){
	global $wpdb;
	$query = "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_value LIKE '%/$filename'";
	return ($wpdb->get_var($query)  > 0) ;
}