<?php get_header(); ?>
	<?php
		$loop = new WP_Query( array( 'post_type' => 'home' ) );
		if ( $loop->have_posts() ) :
		    while ( $loop->have_posts() ) : $loop->the_post(); ?>
		    	<?php $first_title = get_field('first_title'); ?>
		    	<?php $background_first_title = get_field('background_first_title'); ?>
		    	<?php $second_title = get_field('second_title'); ?>
		    	<?php $background_second_title = get_field('background_second_title'); ?>

				<section class="bg">
					<div class="background" style="background-image: url('<?php the_field('background'); ?>');"></div>
					<div class="video">
						<video width="1920" height="1280" autoplay muted loop>
		    				<source src="<?php the_field('video_background'); ?>" type="video/mp4" />
		    			</video>
		    		</div>	
				    <div class="cloud cloud1" style="background-image: url(<?php the_field('cloud1'); ?>);"></div>
				    <div class="cloud cloud2" style="background-image: url(<?php the_field('cloud2'); ?>);"></div>
				    <div class="text">
				        <span class="line" style="<?php if( !get_field('second_text') ){ echo 'margin-bottom:40px;'; } ?>" ><?php the_field('first_text'); ?></span>
				        <?php if(get_field('second_text')){ ?>
				        	<span class="big"><?php the_field('second_text'); ?></span>
				        <?php }else{ ?>
							<span class="image">
								<img src="<?php the_field('imagen_title'); ?>">
							</span>
				    	<?php } ?>
				        <span class="medium"><?php the_field('third_text'); ?></span>
				        <span class="small"><?php the_field('fourth_text'); ?></span>
				    </div>
				    <?php if ( get_field('link')): ?>
				        <a href="<?php the_field('link'); ?>" class="btn btn-big btn-bg btn-coffee link"><?php the_field('text_button'); ?></a>
				    <?php endif; ?>
				</section>   

	<?php 	endwhile;
		endif;
		wp_reset_postdata();
	?>	
				<?php get_template_part( 'template-parts/front-page-search') ?>
				<section class="title-bg">
					<div class="container">
						<div class="title-bg-content">
							<div class="title-bg-title">
								<h2><?php echo $background_first_title; ?></h2>
							</div>	
							<p><?php echo $first_title; ?></p>
						</div>	
					</div>
				</section>
				

				<?php get_template_part( 'template-parts/package-slider') ?>

				<?php get_template_part( 'template-parts/hotels-slider') ?>


				<section class="partners">
					<div class="container">
						<h2 class="title"><?php _e('Our partners','theme-text-domain'); ?></h2>
						<!-- <h2 class="title">Nuestros aliados</h2> -->
					</div>
					<?php get_template_part( 'template-parts/partner-slider') ?>
				</section>
				<section class="services">
					<div class="container">
						<h2 class="title"><?php _e('Services','theme-text-domain'); ?></h2>
					</div>
					<?php get_template_part( 'template-parts/service-slider') ?>	
				</section>
				<section class="title-bg">
					<div class="container">
						<div class="title-bg-content">
							<div class="title-bg-title">
								<h2><?php echo $background_second_title; ?></h2>
							</div>	
							<p><?php echo $second_title; ?></p>
						</div>	
					</div>
				</section>

		 
<?php get_footer(); ?>










