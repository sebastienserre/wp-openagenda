<?php
/**
 * This single will be use by default to display event
 * To customize it, you can sopy it to your-theme/openagenda/single.php
 */
$min_age = carbon_get_the_post_meta( 'oa_min_age' );
$max_age = carbon_get_the_post_meta( 'oa_max_age' );
$start   = date_i18n( 'd F Y', carbon_get_the_post_meta( 'oa_start' ) );
$end     = date_i18n( 'd F Y', carbon_get_the_post_meta( 'oa_end' ) );
$a11y = carbon_get_the_post_meta( 'oa_a11y' );
$condition = carbon_get_the_post_meta( 'oa_conditions' );
$tool = carbon_get_the_post_meta( 'oa_tools' );
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
                        <div class="oa-date">
                            <?php
                            if ( $start === $end ){
	                            printf( __( 'Date : on %1$s', 'wp-openagenda' ), $start );
                            } else {
	                            printf( __( 'Date : from %1$s to %2$s', 'wp-openagenda' ), $start, $end );
                            }
                            ?>
                        </div>
                        <div class="oa-age">
							<?php
							if ( 0 !== $max_age ) {
								printf( __( 'Public : from %1$s to %2$s yo', 'wp-openagenda' ), $min_age, $max_age );
							} else {
								printf( __( 'Public : from %1$s yo', 'wp-openagenda' ), $min_age );
							}
							?>
                        </div>
                        <?php
                        if ( ! empty( $a11y ) ) {
	                        ?>
                            <div class="oa-a11y">
		                        <?php
		                        foreach ( $a11y as $access ){
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
		                        }

		                        ?>
                            </div>

                    </aside>
                    <div class="oa-content">
                        <div class="oa-description">
							<?php the_content(); ?>
                        </div>
                        <div class="oa-condition">
                            <?php echo esc_attr( $condition );?>
                        </div>
                        <div class="oa-tool">
		                    <?php
		                    if (filter_var($tool, FILTER_VALIDATE_URL)) {
			                    ?>
                                <a href="<?php echo $tool;?>"><?php echo $tool;?></a>
                                <?php
		                    } elseif ( is_email( $tool ) ){
		                        ?>
                                <a href="mailto:<?php echo antispambot( $tool ); ?> "><?php echo antispambot( $tool );
                                ?></a>
                                <?php
                            } else {
		                        echo '<p>' . esc_attr( $tool ) . '</p>';
		                    }?>
                        </div>
                    </div>
                </div>
            </article>
        </main>
    </section> <!-- Primary -->
    <!-- End Openagenda Single -->
<?php
get_footer();