<?php get_header(); ?>	
	<?php
		 $terms = get_the_terms($post->ID, 'tipo'); 
		 foreach ($terms as $term) {
		 	$type = $term->slug; 
		 };
	?>
	<section class="pages">
		<div class="container">
			<div class="container">
				<h2 class="title-pages"><?php the_title(); ?></h2>
			</div>
			<div class="package-detail">
				<div class="image">
					<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail();
						} 
					?>	
				</div>
				<div class="package-detail-info">
					<ul>
						<li><strong><?php _e('Code','theme-text-domain'); ?>:</strong><?php the_field('code'); ?></li>
						<li><strong><?php _e('Price','theme-text-domain'); ?>:</strong><?php the_field('cost'); ?></li>
						<li><strong><?php _e('Dificult level','theme-text-domain'); ?>:</strong><?php the_field('level') ?></li>
						<li><strong><?php _e('Comfort level','theme-text-domain'); ?>:</strong><?php the_field('comfort'); ?></li>
						<li><strong><?php _e('Location','theme-text-domain'); ?>:</strong><?php the_field('location'); ?></li>
						<li><strong><?php _e('Promedy temperature','theme-text-domain'); ?>:</strong><?php the_field('temperature'); ?> °C</li>
						<li><strong><?php _e('Kind of adventure','theme-text-domain'); ?>:</strong> <?php echo $type; ?> </li>
					</ul>
					<a href="<?php get_field('url_booking') ? the_field(url_booking) : '#'; ?>" class="btn btn-small btn-brown btn-radius btn-shadow">Reservar</a>
				</div>
			</div>
		</div>
		<div class="package-tabs">
			<div class="container">
				<ul class="tabs">
					<li class="tab-link current" data-tab="tab-1"><a href="#"><?php _e('Description','theme-text-domain'); ?></a></li>
					<li class="tab-link" data-tab="tab-2"><a href="#"><?php _e('Itinerary','theme-text-domain'); ?></a></li>
					<li class="tab-link" data-tab="tab-3"><a href="#"><?php _e('Include','theme-text-domain'); ?></a></li>
					<li class="tab-link" data-tab="tab-4"><a href="#"><?php _e('Dates and rates','theme-text-domain'); ?></a></li>
					<li class="tab-link" data-tab="tab-5"><a href="#"><?php _e('Comments','theme-text-domain'); ?></a></li>
					<li class="tab-link" data-tab="tab-6"><a href="#"><?php _e('Gallery','theme-text-domain'); ?></a></li>
				</ul>
				<div class="tabs-info">
					<div class="tabs-info-item current" id="tab-1">
						<?php the_field('descripcion'); ?>
					</div>
					<div class="tabs-info-item" id="tab-2">
						<?php the_field('itinerary'); ?>
					</div>
					<div class="tabs-info-item" id="tab-3">
						<?php the_field('include'); ?>
					</div>
					<div class="tabs-info-item" id="tab-4">
						<?php the_field('datesandrates'); ?>
					</div>
					<div class="tabs-info-item" id="tab-5">
						<?php the_field('comments'); ?>
					</div>
					<div class="tabs-info-item" id="tab-6">
						<?php 

						$images = get_field('gallery');
						$size = 'full'; // (thumbnail, medium, large, full or custom size)

						if( $images ): ?>
						        <?php foreach( $images as $image ): ?>
						            <div class="image-gallery">
						            	<?php echo wp_get_attachment_image( $image['ID'], $size ); ?>
						            </div>
						        <?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>	
		</div>
	</section>
<?php get_footer(); ?>
