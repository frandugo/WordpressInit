<section class="slider-content">
	<div class="container">
		<ul class="slider-inner">

		<?php	$sliderpartners = new WP_Query( array( 'post_type' => 'sliderpartners' ) );
			if ( $sliderpartners->have_posts() ) :
			    while ( $sliderpartners->have_posts() ) : $sliderpartners->the_post(); ?>  

					<li class="<?php if( get_field('second_image' )){ echo 'two'; } ?>" >
						<div class="partner" style="background-image: url(<?php the_field('image'); ?>)">
							<div class="text">
								<h3><?php the_field('title') ?></h3>
								<?php if(get_field('url')){ ?><a href="<?php the_field('url'); ?>" class="btn btn-small btn-transparent"><?php _e('See more','theme-text-domain'); ?></a><?php } ?>
							</div>
						</div>
						<?php if( get_field('second_image' )){ ?>
							<div class="partner" style="background-image: url(<?php the_field('second_image'); ?>)">
								<div class="text">
									<h3><?php the_field('second_title') ?></h3>
									<?php if(get_field('second_url')){ ?><a href="<?php the_field('url'); ?>" class="btn btn-small btn-transparent"><?php _e('See more','theme-text-domain'); ?></a><?php } ?>
								</div>
							</div>
						<?php } ?>	
					</li>	
		<?php			
				endwhile;
			endif;
			wp_reset_postdata();
		?>			

		</ul>
	</div>
</section>