<?php
get_header();
?>
	<section id="primary" class="content-area">
		<main id="main" class="site-main">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
				<?php  the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</header>
			</article>
		</main>
	</section> <!-- Primary -->
<?php
get_footer();