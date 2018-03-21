<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Ecotrek
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">
		<h1>Single page</h1>
		<?php
		while ( have_posts() ) : the_post();

			the_post_navigation();

			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile;
		?>

		</main>
	</div>

<?php
get_sidebar();
get_footer();
