<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Ecotrek
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main">
			<section>
				<h1>Resultados search & filter</h1>
			</section>
			<section class="pages">
				<div class="container">
					<div class="packages" id="plans">

						<?php	
							$args = array('post_type' => 'package');
							$args['search_filter_id'] = 315;
							$service = new WP_Query($args);
							if ( $service->have_posts() ) :
							    while ( $service->have_posts() ) : $service->the_post(); ?>
									
								<a href="<?php the_permalink(); ?>" class="package">
									<div class="image">
										<img src="<?php the_field('image'); ?>" alt="">
										<div class="overlay">
											<h2><?php the_title(); ?></h2>
											<p><?php the_excerpt(); ?></p>
										</div>
									</div>
									<ul>
										<li><i class="icon location"></i><?php the_field('location'); ?></li>
										<li><i class="icon travel"></i><?php the_field('travel'); ?></li>
										<li><i  class="icon days"></i><?php the_field('day'); ?> Dias / <?php the_field('night'); ?> Noches  / <?php the_field('cost'); ?></li>
									</ul>	
								</a>

						<?php
								endwhile;
							endif;
						?>	
					</div>		
				</div>
			</section>	
		</main><!-- #main -->
	</section><!-- #primary -->

<?php
get_footer();