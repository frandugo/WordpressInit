<section class="reserve-bg">
	<script>
		function changeSelect(){
			var filter = document.getElementById("filter");
			var pageId = document.getElementById("type").value;
			filter.action = pageId;
			if('http://localhost/ecotrek/services/?lang=en' == pageId || 'http://localhost/ecotrek/servicios/' == pageId ){
				document.getElementById("duration").disabled = true;
				document.getElementById("date").disabled = true;
			}else{
				if('http://localhost/ecotrek/partner/?lang=en' == pageId || 'http://localhost/ecotrek/aliados/' == pageId ){
					document.getElementById("duration").disabled = true;
					document.getElementById("date").disabled = true;
				}else{
					document.getElementById("duration").disabled = false;
					document.getElementById("date").disabled = false;
				}
			}
		}
	</script> 
	
	<div class="filter container">

		<fieldset class="selectPage">
			<select name="tipo" id="type" onchange="changeSelect()">
				<option value=""><?php _e('Kind','theme-text-domain'); ?></option>
				<option disabled>----------</option>
				<option value="<?php echo get_page_link('2'); ?>"><?php _e('Plans','theme-text-domain'); ?></option>
				<option value="<?php echo get_page_link('11'); ?>"><?php _e('Services','theme-text-domain'); ?></option>
				<option value="<?php echo get_page_link('13'); ?>"><?php _e('Hotel','theme-text-domain'); ?></option>
			</select>
		</fieldset>	

		<form id="filter" action="<?php echo get_page_link('2'); ?>" method="POST">
			<fieldset>	
				<select name="destino" class="destinations">
					<option value=""><?php _e('Destinations','theme-text-domain'); ?></option>
					<option disabled>----------</option>
					<?php
						$terms = get_terms('destino'); 
						foreach ( $terms as $term ) :
							echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
						endforeach;
					?>
				</select>
			</fieldset>
			<fieldset>
				<select name="duracion" class="duration" id="duration">
					<option value=""><?php _e('Duration','theme-text-domain'); ?></option>
					<option disabled>----------</option>
					<?php
						$terms = get_terms('duracion'); 
						foreach ( $terms as $term ) :
							echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
						endforeach;
					?>
				</select>
			</fieldset>
			<fieldset>
				<select name="fecha" class="date" id="date">
					<option value=""><?php _e('Travel date','theme-text-domain'); ?></option>
					<option disabled>----------</option>
					<?php
						$terms = get_terms('fecha'); 
						foreach ( $terms as $term ) :
							echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
						endforeach;
					?>
				</select>
			</fieldset>
			<fieldset>	
				<input type="submit" value="<?php _e('See Plan','theme-text-domain'); ?>">
			</fieldset>	
		</form>

	</div>
</section>
