<?php
/*
Template Name: Conocenos
*/
?>

<?php get_header(); ?>	
	<section class="pages">
		<div class="container">
			<h2 class="title-pages"><?php the_title(); ?></h2>
		</div>
		<?php	$service = new WP_Query( array( 'post_type' => 'about', 'order' => 'ASC' ) );
			if ( $service->have_posts() ) :
			    while ( $service->have_posts() ) : $service->the_post(); ?> 
					<div class="about">
						<div class="about-text" style="background-color: <?php the_field('color_background_head'); ?>">
							<div class="container">
								<p><?php the_field('text_head'); ?></p>
							</div>
						</div>	
						<div class="video">
							<iframe  onload="this.width=screen.width;this.height=screen.height" src="<?php the_field('video'); ?>" frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe>
						</div>
					</div>
		<?php
				$colorText = get_field('color_text_head');
				endwhile;
			endif;		
		?>			
	</section>
	<style>
		.about .about-text p{
			color: <?php echo $colorText; ?>;
		}
	</style>
<?php get_footer('about'); ?>
