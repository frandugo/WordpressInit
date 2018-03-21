<section class="partners-foot">
	<div class="container">
			<?php
				$company = new WP_Query( array( 'post_type' => 'company', 'order' => 'ASC') );
				if ( $company->have_posts() ) :
				    while ( $company->have_posts() ) : $company->the_post(); ?>
						<a href="<?php the_field('link'); ?>" target="_blank" class="partner-f">
							<img src="<?php the_field('image'); ?>" alt="<?php the_title(); ?>">
						</a>
			<?php	
					endwhile;
				endif;
				wp_reset_postdata();
			?>	

	</div>
</section>