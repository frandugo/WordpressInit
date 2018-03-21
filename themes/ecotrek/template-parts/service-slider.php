<section class="slider-content">
	<div class="container">
		<ul class="slider-services">

		<?php	$service = new WP_Query( array( 'post_type' => 'service' ) );
			if ( $service->have_posts() ) :
			    while ( $service->have_posts() ) : $service->the_post(); ?>  

					<li>
						<a href="<?php the_permalink(); ?>" class="service">
							<h3><?php the_title(); ?></h3>
							<img src="<?php the_field('icon'); ?>" alt="<?php the_title(); ?>">
						</a>	
					</li>

		<?php 
				endwhile;
			endif;
			wp_reset_postdata();
		?>			

		</div>
	</div>
</section>	