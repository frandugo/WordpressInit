<section class="slider-content">
	<div class="container">
		<h2 class="title"><?php _e('Plans','theme-text-domain'); ?></h2>
		<ul class="slider-pack">
			<?php
				$loop = new WP_Query( array( 'post_type' => 'package' ) );
				if ( $loop->have_posts() ) :
				    while ( $loop->have_posts() ) : $loop->the_post(); ?>
						<li style="background-image: url(<?php the_field('image'); ?>)">
							<div class="text">
								<h3><?php the_title(); ?></h3>
								<a href="<?php the_permalink(); ?>" class="btn btn-small btn-transparent"><?php _e('See more','theme-text-domain'); ?></a>
							</div>	
						</li>
			<?php 
					endwhile;
				endif;
				wp_reset_postdata();
			?>	

		</ul>
	</div>
</section>