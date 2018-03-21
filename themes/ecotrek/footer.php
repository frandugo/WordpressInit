<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Ecotrek
 */

?>

	</div><!-- #content -->
	
	<footer class="site-footer">
		<?php get_template_part( 'template-parts/companies') ?>
		<div class="container">
			<div class="cloud1"></div>
			<div class="cloud2"></div>
			<div class="footer-info">
				<?php if ( is_active_sidebar( 'footer_left_1' ) ) : ?>
					<div class="widget information">
						<?php dynamic_sidebar( 'footer_left_1' ); ?>
					</div><!-- #primary-sidebar -->
				<?php endif; ?>
				<?php if ( is_active_sidebar( 'footer_left_2' ) ) : ?>
					<div class="widget menu-footer">
						<?php dynamic_sidebar( 'footer_left_2' ); ?>
					</div><!-- #primary-sidebar -->
				<?php endif; ?>
			</div>	
			<?php if ( is_active_sidebar( 'footer_left_3' ) ) : ?>
				<div class="social-footer">
					<?php dynamic_sidebar( 'footer_left_3' ); ?>
					<ul>
						<li>
							<a href="<?php the_field('facebook', 'option'); ?>"><img src="<?php the_field('icon_facebook', 'option'); ?>" alt=""></a>
						</li>
						<li>
							<a href="<?php the_field('instagram', 'option'); ?>"><img src="<?php the_field('icon_instagram', 'option'); ?>" alt=""></a>
						</li>
					</ul>
					<p><?php the_field('copyright', 'option'); ?></p>
				</div><!-- #primary-sidebar -->
			<?php endif; ?>
			<?php // if(is_home()): ?>
				<div class="joonik">
					<span>Design by</span>
					<img src="<?php bloginfo('template_url'); ?>/images/bin/joonik.png" alt="Joonik">
				</div>	
			<?php // endif ?>	
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
<script src="<?php bloginfo('template_url'); ?>/js/calendar.js"></script>
<script src="<?php bloginfo('template_url'); ?>/js/slick.js"></script>
<script src="<?php bloginfo('template_url'); ?>/js/all.min.js"></script>
</body>
</html>
