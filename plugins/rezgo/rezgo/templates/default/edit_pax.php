<?php
	$re = explode('?',sanitize_text_field($_REQUEST['method']));

	parse_str($re[1], $data);

	$trs	= 't=uid';
	$trs .= '&q=' . $data['id'];
	$trs .= '&d=' . $data['date'];
	$trs .= '&file=edit_pax';

	$option = $site->getTours($trs);

	$site->readItem($option[0]);

	$order_id = sanitize_text_field($data['order_id']);

	$cart = $site->getCart();

	if (isset($data['parent_url'])) {
		$site->base = sanitize_text_field($data['parent_url']);
	}

	// non-open date date_selection elements
	$date_types = array('always', 'range', 'week', 'days', 'single');
?>

<div class="rezgo-edit-pax-content">
	<script>
		var fields_<?php echo $order_id; ?> = new Array();

		var required_num_<?php echo $order_id; ?> = 0;

		function isInt(n) {
			 return n % 1 === 0;
		}

		jQuery(document).ready(function($){
			function check_pax_<?php echo $order_id; ?>(e) {
				var err;
				var count = 0;
				var required = 0;

				for (v in fields_<?php echo $order_id; ?>) {
					// total number of spots
					count += $('#' + v).val() * 1;

					// has a required price point been used
					if (
						fields_<?php echo $order_id; ?>[v] && 
						$('#' + v).val() >= 1
					) { 
						required = 1; 
					}
				}

				if (count == 0 || !count) {
					err = 'Please enter the number you would like to book.';
				} 

				else if (required_num_<?php echo $order_id; ?> > 0 && required == 0) {
					err = 'At least one marked ( * ) price point is required to book.';
				} 

				else if (!isInt(count)) {
					err = 'Please enter a whole number. No decimal places allowed.';
				} 

				<?php if (!empty($option[0]->per)) { ?>
					else if (count < <?php echo $option[0]->per; ?>) {
						err = '<?php echo $option[0]->per; ?> minimum required to book.';
					}
				<?php } ?>

				<?php if (!empty($option[0]->date->availability)) { ?>
					else if (count > <?php echo $option[0]->date->availability; ?>) {
						err = 'There is not enough availability to book ' + count;
					} 
				<?php } ?>

				else if (count > 150) {
					err = 'You can not book more than 150 spaces in a single booking.';
				}

				if (err) {
					e.preventDefault();
					$('#error_text_<?php echo $order_id; ?>').html(err);

					$('#error_text_<?php echo $order_id; ?>').slideDown().delay(2000).slideUp('slow');

					return false;
				}
			}

			$('.rezgo-btn-book[rel="<?php echo $order_id; ?>"]').on('click', function(e){
				check_pax_<?php echo $order_id; ?>(e);
			});
		});
	</script>

	<?php if ($option[0]->date->availability != 0): ?>
		<form class="rezgo-order-form clearfix" name="rezgo-edit-pax-<?php echo $order_id; ?>" id="rezgo-edit-pax-<?php echo $order_id; ?>" action="<?php echo $site->base; ?>/order">
			<input type="hidden" name="edit[<?php echo $order_id; ?>][uid]" value="<?php echo $option[0]->uid; ?>" />

			<input type="hidden" name="edit[<?php echo $order_id; ?>][date]" value="<?php echo $data['date']; ?>" />

			<?php if (!$site->getCartState()) { ?>
				<input 
				type="hidden" 
				name="order" 
				value="clear" 
				/>
			<?php } ?>

			<?php if ($_COOKIE['rezgo_promo']) { ?>
				<input type="hidden" name="promo" value="<?php echo $_COOKIE['rezgo_promo']; ?>" />
			<?php } ?>

			<?php if ($_COOKIE['rezgo_refid_val']) { ?>
				<input type="hidden" name="refid" value="<?php echo $_COOKIE['rezgo_refid_val']; ?>" />
			<?php } ?>

			<?php $prices = $site->getTourPrices($option[0]);	?>

			<?php if ($site->getTourRequired()) { ?>
				<span class="rezgo-memo">
					<span>At least one marked ( </span>
					<em><i class="fa fa-asterisk"></i></em>
					<span> ) price point is required to book.</span>
				</span>
			<?php } ?>

			<?php if ($option[0]->per > 1) { ?>
				<span class="rezgo-memo">At least <?php echo $option[0]->per; ?> are required to book.</span>
			<?php } ?>

			<?php $total_required = 0; ?>

			<?php foreach($prices as $price): ?>
				<script>
				fields_<?php echo $order_id; ?>['<?php echo $price->name; ?>_<?php echo $order_id; ?>'] = <?php echo (($price->required) ? 1 : 0); ?>;
				</script>

				<div class="form-group row">
					<div class="col-md-2 col-xs-4">
						<input 
						type="number" 
						name="edit[<?php echo $order_id; ?>][<?php echo $price->name; ?>_num]" 
						value="<?php echo (string) $cart[$order_id]->{$price->name.'_num'} ?>" 
						id="<?php echo $price->name; ?>_<?php echo $order_id; ?>" 
						size="3" 
						class="form-control input-sm" 
						/>
					</div>

					<label 
					for="<?php echo $price->name; ?>" 
					class="col-xs-8 control-label rezgo-label-margin">
						<span>x&nbsp;&nbsp;</span>
						<span><?php echo $price->label; ?></span>
						<?php echo (($price->required && $site->getTourRequired()) ? ' <em><i class="fa fa-asterisk"></i></em>' : ''); ?>
						<span class="rezgo-pax-price">
							<?php if (in_array( $price->name, array('adult', 'child', 'senior'))) { $price_name = 'price_'.$price->name; } 
							else { $price_name = $price->name; } ?>
							<span>(</span>
							<?php if ($site->exists($cart[$order_id]->date->base_prices->{$price_name})) { ?>
								<span class="discount"><?php echo $site->formatCurrency($cart[$order_id]->date->base_prices->{$price_name}); ?></span>
							<?php } ?>
							<span><?php echo $site->formatCurrency($cart[$order_id]->date->{$price_name}); ?></span>
							<span>)</span>
						</span>
					</label>
				</div>

				<?php if ($price->required) { $total_required++; } ?>
			<?php endforeach; ?>

			<script>
			required_num_<?php echo $order_id; ?> = <?php echo $total_required; ?>;
			</script>

			<div class="text-danger rezgo-option-error" id="error_text_<?php echo $order_id; ?>" style="display:none;"></div>

			<div class="form-group pull-right cta">
				<a 
				data-toggle="collapse" 
				href="#pax-edit-<?php echo $order_id; ?>">Cancel</a>
				<span>&nbsp;</span>
				<button 
				type="submit" 
				class="btn rezgo-btn-book" 
				rel="<?php echo $order_id; ?>">Update Booking</button>
			</div>
		</form>
	<?php else: ?>
		<div class="rezgo-order-unavailable">
			<span>Sorry, there is no availability left for this option.</span>
		</div>
	<?php endif; ?>
</div>
