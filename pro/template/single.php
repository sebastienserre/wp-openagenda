<?php
/**
 * This single will be use by default to display event
 * To customize it, you can sopy it to your-theme/openagenda/single.php
 */
$min_age   = carbon_get_the_post_meta( 'oa_min_age' );
$max_age   = carbon_get_the_post_meta( 'oa_max_age' );
$start     = date_i18n( 'd F Y', carbon_get_the_post_meta( 'oa_start' ) );
$end       = date_i18n( 'd F Y', carbon_get_the_post_meta( 'oa_end' ) );

$condition = carbon_get_the_post_meta( 'oa_conditions' );
$tool      = carbon_get_the_post_meta( 'oa_tools' );
get_header();

?>
    <!-- Start Openagenda Single -->
    <section id="primary" class="content-area oa-content-area">
        <main id="main" class="site-main">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="oa-entry-header">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                </header>
                <div class="oa-content">
                    <aside>
                        <p class="oa-date">
							<?php

							_e( 'Date : ', 'wp-openagenda' );
							echo openwp_display_date( $start, $end );

							?>
                        </p>
                        <p class="oa-age">
							<?php
							_e( 'Public : ', 'wp-openagenda' );
							echo openwp_display_age( $min_age, $max_age );

							?>
                        </p>
                        <?php
                        /**
                         * Display acessibility
                         */
                        echo openwp_display_accessibilty( get_the_ID() );
                        ?>

                    </aside>
                    <div class="oa-content">
                        <div class="oa-description">
							<?php the_content(); ?>
                        </div>
                        <div class="oa-condition">
							<?php echo esc_attr( $condition ); ?>
                        </div>
                        <div class="oa-tool">
							<?php
							if ( filter_var( $tool, FILTER_VALIDATE_URL ) ) {
								?>
                                <a href="<?php echo $tool; ?>"><?php echo $tool; ?></a>
								<?php
							} elseif ( is_email( $tool ) ) {
								?>
                                <a href="mailto:<?php echo antispambot( $tool ); ?> "><?php echo antispambot( $tool );
									?></a>
								<?php
							} else {
								echo '<p>' . esc_attr( $tool ) . '</p>';
							} ?>
                        </div>
                    </div>
                </div>
            </article>
        </main>
    </section> <!-- Primary -->
    <!-- End Openagenda Single -->
<?php
get_footer();