<?php get_header(); ?>	
	<section class="pages">
		<div class="container">
			<div class="container">
				<h2 class="title-pages"><?php the_title(); ?></h2>
			</div>
		</div> 
				<?php	$posts = get_field('sub_service');

				if( $posts ): ?>
					<?php $i = 0; ?>
				    <?php foreach( $posts as $post): // variable must be called $post (IMPORTANT) ?>
				        <?php setup_postdata($post); ?>
            					<div class="service-detail <?php if($i % 2 != 0){ echo 'right'; } ?>">
            						<div class="container">
            							<ul class="slider-services-detail">
		        					    	<?php
		        									if( have_rows('slider') ):
		        					    	    		while ( have_rows('slider') ) : the_row();
		        					    	?>
		        		    								<li>
		        		    									<img src="<?php the_sub_field('image'); ?>" alt="">
		        		    								</li>
		        							<?php
		        										endwhile;	
		        									endif;
		        							?>
										</ul>
										<div class="service-detail-info">
											<h2><?php the_title(); ?></h2>
											<p>
												<?php the_excerpt() ?>
											</p>
											<a href="<?php the_permalink(); ?>" class="btn btn-small btn-brown btn-radius bt-shadow"><?php _e('Reserve','theme-text-domain'); ?></a>
										</div>
									</div>	
								</div>		
				        <?php $i++; ?>
				    <?php endforeach; ?>
				    <?php wp_reset_postdata();  ?>

				<?php endif; ?>

	</section>
<?php get_footer(); ?>
