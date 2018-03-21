<section class="hotels" id="hotels">
	<div class="container">
		<h2 class="title"><?php _e('Our hotels','theme-text-domain'); ?></h2>
	</div>	
	<div class="hotels-container">
		<div class="container">
			<?php
				$hotel = new WP_Query( array( 'post_type' => 'hotel' ) );
				if ( $hotel->have_posts() ) :
				    while ( $hotel->have_posts() ) : $hotel->the_post(); ?>      
				        <a href="<?php the_field('link'); ?>" target="_blank" class="hotel-item">
				        	<img src="<?php the_field('image'); ?>" alt="<?php the_title(); ?>">
				        </a>
			<?php 
					endwhile;
				endif;
				wp_reset_postdata();
			?>	
		</div>	
	</div>		
</section>