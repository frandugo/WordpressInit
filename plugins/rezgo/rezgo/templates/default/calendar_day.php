<?php
	$company = $site->getCompanyDetails();
	$availability_title = '';

	if ($_REQUEST['option_num']) {
		$option_num = sanitize_text_field($_REQUEST['option_num']);
	} 
	else {
		$option_num = 1;

		if ($_REQUEST['type'] != 'open') {
			$availability_title = '<div class="rezgo-date-options" style="display:none;">
				<span class="rezgo-calendar-avail">
					<span>Availability&nbsp;for:</span>
				</span> 
				<strong>'.date((string) $company->date_format, strtotime($_REQUEST['date'])).'</strong>
			</div>';	
		}
	}

	if ($_REQUEST['date'] != 'open') {
		$date_request = '&d='.sanitize_text_field($_REQUEST['date']);
	} 
	else {
		$date_request = '';
	}

	$trs	= 't=com';
	$trs .= '&q='.sanitize_text_field($_REQUEST['com']);
	$trs .= $date_request;
	$trs .= '&file=calendar_day';

	$options = $site->getTours($trs);
?>

<?php if ($options) { ?>
	<?php echo $availability_title; ?>

	<span class="rezgo-date-memo rezgo-calendar-date-<?php echo $_REQUEST['date']; ?>"></span>

	<div class="panel-group" id="rezgo-select-option-<?php echo $option_num; ?>">
		<?php if (count($options) != 1) { ?>
			<span class="rezgo-choose-options">
				<span>Choose one of the options below </span>
				<i class="fa fa-angle-double-down"></i>
			</span>
		<?php } ?>

		<?php 
			if ($_REQUEST['type'] == 'open') {
				$sub_option = 'o1';
			} 
			else {
				$sub_option = 'a';
			}
		?>

		<?php foreach ($options as $option): ?>
			<?php $site->readItem($option); ?>

			<?php 
				if ($option->date->availability == 0) {
					$panel_unclass = ' panel-unavailable';
				} 

				else {
					$panel_unclass = '';
				}
			?>

			<?php if (
				(($_REQUEST['type'] == 'calendar' || $_REQUEST['type'] == 'single') && (string) $option->date['value'] != 'open') || 
				($_REQUEST['type'] == 'open' && (string) $option->date['value'] == 'open' )
			): ?>
				<div class="panel panel-default<?php echo $panel_unclass; ?>">
					<script>
						var fields_<?php echo $option_num.'_'.$sub_option; ?> = new Array();

						var required_num_<?php echo $option_num.'_'.$sub_option; ?> = 0;

						function isInt(n) {
							 return n % 1 === 0;
						}

						jQuery(document).ready(function($){
							function check_<?php echo $option_num.'_'.$sub_option; ?>(e) {
								var err;

								var count_<?php echo $option_num.'_'.$sub_option; ?> = 0;

								var required_<?php echo $option_num.'_'.$sub_option; ?> = 0;

								for (v in fields_<?php echo $option_num.'_'.$sub_option; ?>) {
									// total number of spots
									count_<?php echo $option_num.'_'.$sub_option; ?> += $('#' + v).val() * 1;
									// has a required price point been used
									if (fields_<?php echo $option_num.'_'.$sub_option; ?>[v] && $('#' + v).val() >= 1) { 
										required_<?php echo $option_num.'_'.$sub_option; ?> = 1; 
									}
								}

								if (count_<?php echo $option_num.'_'.$sub_option; ?> == 0 || !count_<?php echo $option_num.'_'.$sub_option; ?>) {
									err = 'Please enter the number you would like to book.';
								} else if (required_num_<?php echo $option_num.'_'.$sub_option; ?> > 0 && required_<?php echo $option_num.'_'.$sub_option; ?> == 0) {
									err = 'At least one marked ( * ) price point is required to book.';
								} else if (!isInt(count_<?php echo $option_num.'_'.$sub_option; ?>)) {
									err = 'Please enter a whole number. No decimal places allowed.';
								} else if (count_<?php echo $option_num.'_'.$sub_option; ?> < <?php echo $option->per; ?>) {
									err = '<?php echo $option->per; ?> minimum required to book.';
								} else if (count_<?php echo $option_num.'_'.$sub_option; ?> > <?php echo $option->date->availability; ?>) {
									err = 'There is not enough availability to book ' + count_<?php echo $option_num.'_'.$sub_option; ?>;
								} else if (count_<?php echo $option_num.'_'.$sub_option; ?> > 150) {
									err = 'You can not book more than 150 spaces in a single booking.';
								}

								/*
									NUM minimum required to book
									There is not enough availability to book NUM.
									You cannot book more than 150 in a single booking.
									At least NUM are required.
								*/

								if (err) {
									e.preventDefault();
									<?php if (!$site->config('REZGO_MOBILE_XML')) { ?>
										$('#error_text_<?php echo $option_num.'_'.$sub_option; ?>').html(err);
										$('#error_text_<?php echo $option_num.'_'.$sub_option; ?>').slideDown().delay(2000).slideUp('slow');
									<?php } else { ?>
										$('#error_mobile_text_<?php echo $option_num.'_'.$sub_option; ?>').html(err);
										$('#error_mobile_text_<?php echo $option_num.'_'.$sub_option; ?>').slideDown().delay(2000).slideUp('slow');
									<?php } ?>

									return false;
								}
							}

							$('#book_btn_<?php echo $option_num."_".$sub_option; ?>').on('click',function(e){
								check_<?php echo $option_num.'_'.$sub_option; ?>(e);
							});
						});
					</script>

					<a 
					data-toggle="collapse" 
					data-parent="#rezgo-select-option-<?php echo $option_num.'_'.$sub_option; ?>" 
					data-target="#option_<?php echo $option_num.'_'.$sub_option; ?>" 
					class="panel-heading panel-title rezgo-panel-option-link">
						<span class="rezgo-panel-option"><?php echo $option->option; ?></span>
					</a>

					<div 
					id="option_<?php echo $option_num.'_'.$sub_option; ?>" 
					class="panel-collapse collapse<?php echo (((count($options) == 1 && $option_num == 1) || $_REQUEST['id'] == (int) $option->uid) ? ' in' : ''); ?>">
						<div class="panel-body">
							<?php if ($option->date->availability != 0) { ?>
								<span class="rezgo-option-memo rezgo-option-date-<?php echo $_REQUEST['date']; ?>"></span>

								<form 
								class="rezgo-order-form" 
								name="checkout_<?php echo $option_num.'_'.$sub_option; ?>" 
								id="checkout_<?php echo $option_num.'_'.$sub_option; ?>" 
								action="<?php echo $site->base; ?>/order">
									<input 
									type="hidden" 
									name="add[0][uid]" 
									value="<?php echo $option->uid; ?>" 
									/>

									<input 
									type="hidden" 
									name="add[0][date]" 
									value="<?php echo $_REQUEST['date']; ?>" 
									/>

									<?php if (!$site->getCartState()) { ?>
										<input 
										type="hidden" 
										name="order" 
										value="clear" 
										/>
									<?php } ?>

									<?php if ($_COOKIE['rezgo_promo']) { ?>
										<input 
										type="hidden" 
										name="promo" 
										value="<?php echo $_COOKIE['rezgo_promo']; ?>"
										/>
									<?php } ?>

									<?php if ($_COOKIE['rezgo_refid_val']) { ?>
										<input 
										type="hidden" 
										name="refid" 
										value="<?php echo $_COOKIE['rezgo_refid_val']; ?>"
										/>
									<?php } ?>

									<div class="row">
										<div class="col-xs-12">
											<?php if (!$site->exists($option->date->hide_availability)) { ?>
												<span class="rezgo-memo rezgo-availability">
													<strong>Availability: </strong> 
													<span><?php echo ($option->date->availability == 0 ? 'full' : (string) $option->date->availability); ?></span>
													<br />
												</span>
											<?php } ?>

											<?php if ($option->duration != '') { ?>
												<span class="rezgo-memo rezgo-duration">
													<strong>Duration: </strong> 
													<span><?php echo (string) $option->duration;?></span>
													<br />
												</span>	
											<?php } ?>

											<?php if ($option->time != '') { ?>
												<span class="rezgo-memo rezgo-time">
													<strong>Time: </strong>
													<span><?php echo (string) $option->time;?></span>
													<br />
												</span>
											<?php } ?>

											<?php $prices = $site->getTourPrices($option);	?>

											<?php if ($site->getTourRequired() == 1) { ?>
												<span class="rezgo-memo">
													<span>At least one marked ( </span>
													<em><i class="fa fa-asterisk"></i></em>
													<span> ) price point is required.</span>
												</span>
											<?php } ?>

											<?php if ($option->per > 1) { ?>
												<span class="rezgo-memo">At least <?php echo $option->per; ?> are required to book.</span>
											<?php } ?>

											<div class="clearfix">
												&nbsp;
											</div>

											<div 
											class="text-danger rezgo-option-error" 
											id="error_text_<?php echo $option_num.'_'.$sub_option; ?>" 
											style="display:none;">
											</div>

											<?php $total_required = 0; ?>

											<?php foreach ($prices as $price) { ?>
												<script>fields_<?php echo $option_num.'_'.$sub_option; ?>['<?php echo $price->name; ?>_<?php echo $option_num.'_'.$sub_option; ?>'] = <?php echo (($price->required) ? 1 : 0); ?>;</script>

												<div class="form-group row">
													<div class="col-md-3 col-xs-4 max-80">
														<input 
														type="number" 
														name="add[0][<?php echo $price->name; ?>_num]" 
														value="<?php echo $_REQUEST[$price->name.'_num']; ?>" 
														id="<?php echo $price->name; ?>_<?php echo $option_num.'_'.$sub_option; ?>" 
														class="form-control input-sm" 
														placeholder=""
														/>
													</div>

													<label 
													for="<?php echo $price->name; ?>_<?php echo $option_num.'_'.$sub_option; ?>" 
													class="col-xs-8 control-label rezgo-label-margin rezgo-label-padding-left">
														<span>x&nbsp;</span>

														<span><?php echo $price->label; ?> </span>

														<?php if ($price->required && $site->getTourRequired()) { ?>
															<em><i class="fa fa-asterisk"></i></em>
														<?php } ?>

														<span class="rezgo-pax-price">
															<span>(&nbsp;</span>
															<?php if ($site->exists($price->base)) { ?>
																<span class="discount"><?php echo $site->formatCurrency($price->base); ?></span> 
															<?php } ?>
															<span><?php echo $site->formatCurrency($price->price); ?></span>
															<span>&nbsp;)</span>
														</span>
													</label>
												</div>

												<?php if ($price->required) { $total_required++; } ?>
											<?php } ?>

											<script>required_num_<?php echo $option_num.'_'.$sub_option; ?> = <?php echo $total_required; ?>;</script>

											<div 
											class="text-danger rezgo-option-error" 
											id="error_mobile_text_<?php echo $option_num.'_'.$sub_option; ?>" 
											style="display:none;"></div>
										</div>

										<div class="col-lg-8 col-md-9 col-xs-12 pull-right">
											<?php $cart = $site->getCartState(); ?>

											<button 
											id="book_btn_<?php echo $option_num.'_'.$sub_option; ?>"
											type="submit" 
											class="btn btn-block rezgo-btn-book btn-lg"
											><?php echo (($cart) ? 'Add to Order' : 'Book Now'); ?></button>
										</div>
									</div>
								</form>
							<?php } else { ?>
								<div class="rezgo-order-unavailable">
									<span>Sorry, there is no availability left for this option</span>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>

				<?php $sub_option++; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php } else { ?>
	<?php echo 'rezgo-option-hide'; ?>
<?php } ?>