<?php
namespace OpenAgenda\market;

use function get_current_screen;
use function var_dump;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

//add_action( 'admin_notices', 'OpenAgenda\market\free_user' );

function free_user() {
    $screen = get_current_screen();
	if ( openagenda_fs()->is_not_paying() && 'dashboard' === $screen->base ) {
		?>
		<div class="notice notice-success is-dismissible openagenda-notice">
			<h2><?php _e( 'Discover OpenAgenda for WP Premium', 'wp-openagenda' ) ?></h2>
            <?php features_list(); ?>
			<p><a class="product-link" href="<?php echo openagenda_fs()->get_upgrade_url(); ?>">
				<?php _e( 'Upgrade Now!', 'wp-openagenda' ) ?></a>
			</p>
		</div>
		<?php
	}
}

function features_list() {
    ?>
    <ul>
        <li><?php _e( 'No Advertising', 'wp-openagenda' ); ?></li>
        <li><?php _e( 'All Standards features from OpenAGenda for WordPress', 'wp-openagenda' ); ?></li>
        <li><?php _e( 'Shortcode to display your events where ever you want', 'wp-openagenda' ); ?></li>
        <li><?php _e( 'Compatibility with The Event Calendar Plugin', 'wp-openagenda' ); ?></li>
        <li><?php _e( 'Manage your events directly in your WP Dashboard', 'wp-openagenda' ); ?></li>
        <li><?php _e( 'Events created in WP are exported to OpenAgenda.com', 'wp-openagenda' ); ?></li>
        <li><?php _e( 'Events created in OpenAgenda.com are imported into WP', 'wp-openagenda' );
			?></li>
        <li><?php _e( 'Automatic Update', 'wp-openagenda' ); ?></li>
        <li><?php _e( 'Priority support', 'wp-openagenda' ); ?></li>
    </ul>
<?php
}