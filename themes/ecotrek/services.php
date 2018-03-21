<?php
/*
Template Name: Servicios
*/
?>

<?php get_header(); ?>	
	<section class="pages">
		<div class="container">
			<h2 class="title-pages"><?php the_title(); ?></h2>
		</div>
		<div class="services-content">
			<?php	$service = new WP_Query( array_merge( array( 'post_type' => 'service', 'order' => 'ASC' ), array_filter( $_POST ) ) );
				if ( $service->have_posts() ) :
				    while ( $service->have_posts() ) : $service->the_post(); ?>  
						<?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full');  ?>
						<?php
							$field = get_field_object('background');
							$value = $field['value'];
							$label = $field['choices'][ $value ];
							if($value == 'green'){ $color = 'green'; }else{ $color = 'dark'; };
						?>
						<div class="service <?php echo $color; ?>" style="background-image: url('<?php  echo $featured_img_url; ?>');">
							<div class="container">
								<h2><?php the_title(); ?></h2>
								<div class="text">
									<p>
										<?php the_content(); ?>
									</p>
									<div class="actions">
										<a href="<?php the_permalink(); ?>" class="btn btn-small btn-brown btn-radius btn-shadow"><?php _e('Destinations','theme-text-domain'); ?></a>
									</div>	
								</div>	
							</div>	
						</div>

			<?php 
					endwhile;
				endif;
				wp_reset_postdata();
			?>			
		</div>
	</section>
<?php get_footer(); ?>







