<?php
/*
Template Name: Aliados
*/
?>

<?php get_header(); ?>	
	<section class="pages">
		<div class="container">
			<h2 class="title-pages sisas"><?php the_title(); ?></h2>
		</div>
		<div class="partners-content">
			<?php	$partner = new WP_Query( array_merge( array( 'post_type' => 'aliado', 'order' => 'ASC' ), array_filter( $_POST ) ) );
				if ( $partner->have_posts() ) :
				    while ( $partner->have_posts() ) : $partner->the_post(); ?>  
						<?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full');  ?>
						<?php
							$field = get_field_object('background');
							$value = $field['value'];
							$label = $field['choices'][ $value ];
							if($value == 'green'){ $color = 'green'; }else{ $color = 'dark'; };
						?>	
						<div class="partner <?php echo $color; ?>" style="background-image: url('<?php echo $featured_img_url; ?>');">
							<div class="container">
								<div class="text">
									<h2><?php the_title(); ?></h2>
									<p><?php the_content(); ?></p>
									<div class="actions">
										<a href="#" class="btn btn-small btn-brown btn-radius btn-shadow"><?php _e('Reserve','theme-text-domain'); ?></a>
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
<!-- 		<div class="overpop">
			<div class="popup">
				<a href="#" class="close"></a>
				<form action="">
					<fieldset>
						<label for="">Nombre y apellido</label>
						<input type="text">
					</fieldset>
					<fieldset>
						<label for="">Número de contacto</label>
						<input type="text">
					</fieldset>
					<fieldset>
						<label for="">Correo electrónico</label>
						<input type="text">
					</fieldset>
					<p>
						Gracias por reservar con nosotros,
						muy pronto uno de nuestros asesores
						se pondrá en contacto con usted.
					</p>
					<div class="actions">
						<input type="submit" value="Enviar" class="btn btn-big btn-bg btn-coffee link">
					</div>	
				</form>
			</div>
		</div> -->	
	</section>
<?php get_footer(); ?>
