<?php
/*
Template Name: Paquetes
*/
?>

<?php get_header(); ?>	
	<section class="pages">
		<div class="container">
			<h2 class="title-pages"><?php the_title(); ?></h2>
                        <div class="packages rezgo">
                            <?php // echo do_shortcode('[rezgo_shortcode]'); ?>
                        </div>
			<div class="packages">
				<!-- <pre><?php print_r($_POST); ?></pre> -->
				<?php
					$loop = new WP_Query( array_merge( array( 'post_type' => 'package' ), array_filter( $_POST ) ) );
					if ( $loop->have_posts() ) :
					    while ( $loop->have_posts() ) : $loop->the_post(); ?>
					    	<?php
					    		 $terms = get_the_terms($post->ID, 'destino'); 
					    		 foreach ($terms as $term) {
					    		 	$destinations = $term->slug; 
					    		 };
					    	?>
							<a href="<?php the_permalink(); ?>" class="package">
								<div class="image">
									<img src="<?php the_field('image'); ?>" alt="">
									<div class="overlay">
										<h2><?php the_title(); ?></h2>
										<p><?php the_excerpt(); ?></p>
									</div>
								</div>
								<ul>
									<li><i class="icon location"></i><?php echo $destinations; ?></li>
									<li><i class="icon travel"></i><?php the_field('travel'); ?></li>
									<li><i  class="icon days"></i><?php the_field('day'); ?> Dias / <?php the_field('night'); ?> Noches  / <?php the_field('cost'); ?></li>
								</ul>	
							</a>
				<?php 
						endwhile;
					endif;
				?>
			</div>
		</div>
	</section>
<?php get_footer(); ?>










