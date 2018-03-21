<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Ecotrek
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500,600,700,800,900" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Abril+Fatface&amp;subset=latin-ext" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Archivo+Black&amp;subset=latin-ext" rel="stylesheet">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
	<?php wp_head(); ?>
	<script>
		(function($) {
		    $(document).on('facetwp-loaded', function() {
		        $.each(FWP.settings.num_choices, function(key, val) {
		            var $parent = $('.facetwp-facet-' + key).closest('.widget');
		            (0 === val) ? $parent.hide() : $parent.show();
		        });
		    });
		})(jQuery);
	</script>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'ecotrek' ); ?></a>

	<header id="masthead" class="site-header">
		<div class="container">
				<?php the_custom_logo(); ?>
				<nav class="main-navigation">
					<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"></button>
					<?php
						wp_nav_menu( array(
							'theme_location' => 'menu-1',
							'menu_id'        => 'primary-menu',
						) );
					?>
				</nav><!-- #site-navigation -->
		</div>	
	</header><!-- #masthead -->

	<div id="content" class="site-content">
