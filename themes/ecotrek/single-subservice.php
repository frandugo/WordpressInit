<?php get_header(); ?>	

	<section class="pages">
		<div class="filter-service" style="display:none">
			<div class="container">
				<input type="text" id="from" class="from" placeholder="<?php _e('From','theme-text-domain'); ?>">
				<input type="text" id="to" class="to" placeholder="<?php _e('To','theme-text-domain'); ?>" >
				<input type="text" class="people" placeholder="<?php _e('Adults','theme-text-domain'); ?>" >
				<a href="#" class="btn btn-small btn-brown buildurl"><?php _e('Reserve','theme-text-domain'); ?></a>
			</div>	
		</div>
		<div class="container" style="margin-top:20px">		
			<h2 style="display:none;" class="title-pages"><?php the_title(); ?></h2>
			<div class="package-detail">
				<div class="image" style="display:none;">
					<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail();
						} 
					?>	
				</div>
				<div class="MAYpackage-detail-info">
					<p><?php the_content(); ?></p>
				</div>
			</div>
		</div>
	</section>
<?php get_footer(); ?>