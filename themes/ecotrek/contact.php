<?php
/*
Template Name: Contactanos
*/
?>

<?php get_header(); ?>	
	<section class="pages">
		<div class="container">
			<h2 class="title-pages"><?php the_title(); ?></h2>
		</div>
		<div class="contact">
			<div class="contact-location">
				<div class="map">
					<img src="<?php bloginfo('template_url'); ?>/images/bin/bg-location.png" alt="">
					<div class="content-info-location">
						<?php
							$loop = new WP_Query( array( 'post_type' => 'contactus' ) );
							if ( $loop->have_posts() ) :
							    while ( $loop->have_posts() ) : $loop->the_post(); ?> 
								<div class="info-location">
									<span class="marker"></span>
									<p><?php the_field('location'); ?></p>
								</div>
						<?php
								endwhile; 
							endif;
						?>		
					</div>	
				</div>	
			</div>
			<div class="container">
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
					<?php the_content(); ?>
				<?php endwhile; else : ?>
					<p><?php esc_html_e( 'Sorry, no posts matched your criteria.' ); ?></p>
				<?php endif; ?>	
			</div>	
		</div>	
	</section>
<?php get_footer(); ?>
