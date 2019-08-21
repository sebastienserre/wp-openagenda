<?php
get_header();
?>
    <!-- Start Openagenda archive -->
    <section id="primary" class="content-area oa-content-area">
        <main id="main" class="site-main">
			<?php
			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
				//	if ( date( 'U' ) <= carbon_get_post_meta( $id, 'oa_start' ) ) {
						?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                            <header class="oa-entry-header">
                                <a href="<?php echo get_the_permalink(); ?>"><?php the_title( '<h1 class="entry-title">',
										'</h1>' ); ?></a>
                            </header>
                            <div class="oa-content">
								<?php
								/**
								 * Display date formated
								 */
								echo openwp_display_date( $id );
								the_excerpt();
								?>
                            </div>
                        </article>
						<?php
				//	}
				}
			}
			?>
        </main>
    </section> <!-- End Openagenda archive -->
<?php
get_footer();