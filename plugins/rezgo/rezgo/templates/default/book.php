<?php
	// handle old-style booking requests
	if ($_REQUEST[uid] && $_REQUEST[date]) {
		$for_array = array('adult', 'child', 'senior', 'price4', 'price5', 'price6', 'price7', 'price8', 'price9');

		$new_header = '/book?order=clear&add[0][uid]='.sanitize_text_field($_REQUEST[uid]).'&add[0][date]='.sanitize_text_field($_REQUEST[date]);

		foreach ($for_array as $v) {
			if ($_REQUEST[$v.'_num']) $new_header .= '&add[0]['.$v.'_num]='.$_REQUEST[$v.'_num'];
		}
		$site->sendTo($new_header);
	}

	$company = $site->getCompanyDetails();
	$companyCountry = $site->getCompanyCountry();

	// non-open date date_selection elements
	$date_types = array('always', 'range', 'week', 'days', 'single'); // centralize this?
?>

<script>
	var elements = new Array();
	var split_total = new Array();
	var overall_total = '0';
	var form_symbol = '$';
	var form_decimals = '2';
	var form_separator = ',';
</script>

<script>
	jQuery(document).ready(function($) {
		// Start international phone input
		$("#tour_sms").intlTelInput({
			initialCountry: '<?php echo $companyCountry; ?>',
			formatOnInit: true,
			preferredCountries: ['us', 'ca', 'gb', 'au'],
			utilsScript: '<?php echo $site->path; ?>/js/intlTelInput/utils.js'
		});

		$("#tour_sms").on("keyup change blur countrychange", function() {
			$('#sms').val($("#tour_sms").intlTelInput("getNumber"));
			//window.console.log('sms: ' + $("#tour_sms").intlTelInput("getNumber"));
		});

		// End international phone input
		$.validator.setDefaults({
			highlight: function(element) {
				if ($(element).attr("type") == "checkbox") {
					$(element).closest('.rezgo-form-checkbox').addClass('has-error');
				} else {
					$(element).closest('.rezgo-form-input').addClass('has-error');
				}

				$(element).closest('.form-group').addClass('has-error');
			},
			unhighlight: function(element) {
				if ( $(element).attr("type") == "checkbox" ) {
					$(element).closest('.rezgo-form-checkbox').removeClass('has-error');
				} else {
					$(element).closest('.rezgo-form-input').removeClass('has-error');
				}

				$(element).closest('.form-group').removeClass('has-error');
			},
			focusInvalid: false,
			errorElement: 'span',
			errorClass: 'help-block',
			errorPlacement: function(error, element) {
				if ($(element).attr("name") == "name" || $(element).attr("name") == "pan" || $(element).attr("name") == "cvv") {
					error.hide();
				} else if ($(element).attr("name") == "agree_terms") {
					error.insertAfter(element.parent());
				} else if ($(element).attr("type") == "checkbox") {
					error.insertAfter(element.siblings('.rezgo-form-comment'));
				} else {
					error.insertAfter(element);
				}
			}
		});

		// Set validation messages
		$('#book').validate({
			messages: {
				tour_first_name: {
					required: "Enter your first name"
				},
				tour_last_name: {
					required: "Enter your last name"
				},
				tour_address_1: {
					required: "Enter your address"
				},
				tour_city: {
					required: "Enter your city"
				},
				tour_postal_code: {
					required: "Enter postal code"
				},
				tour_phone_number: {
					required: "Enter your phone number"
				},
				tour_email_address: {
					required: "Enter a valid email address"
				},
				agree_terms: {
					required: "You must agree to the terms"
				}
			}
		});
	});
</script>

<script type="text/javascript">
	// for iFrameResize native version
	// MDN PolyFil for IE8 
	if (!Array.prototype.forEach) {
		Array.prototype.forEach = function(fun) {
			"use strict";

			if(this === void 0 || this === null || typeof fun !== "function") throw new TypeError();

			var
			t = Object(this),
			len = t.length >>> 0,
			thisArg = arguments.length >= 2 ? arguments[1] : void 0;

			for (var i = 0; i < len; i++) {
				if (i in t) {
					fun.call(thisArg, t[i], i, t);
				}
			}
		};
	}
</script>

<div class="container-fluid rezgo-container">
	<div class="row">
		<div class="col-xs-12">
			<div class="jumbotron rezgo-book-form">
				<ul class="nav nav-tabs" id="book_steps" style="display:none">
					<li class="active"><a href="#book_step_one" data-toggle="tab">Step 1</a></li>
					<li><a href="#book_step_two" data-toggle="tab">Step 2</a></li>
				</ul>

				<form role="form" method="post" id="book" novalidate>
					<div class="tab-content">
						<div class="tab-pane active" id="book_step_one">
							<div class="row">
								<ol class="breadcrumb rezgo-breadcrumb hidden-xs">
									<li><a href="<?php echo $site->base; ?>/order">Your Order</a></li>
									<li class="active">Guest Information</li>
									<li>Billing Information</li>
									<li>Confirmation</li>
								</ol>
							</div>

							<?php 
							$c = 0;
							$cart = $site->getCart(1); // get the cart, remove any dead entries
							if(!count($cart)) {
								$site->sendTo('/'.$site->base);
							}
							$cart_count = count($cart);
							?>

							<?php // start cart loop for each tour in the order ?>
							<?php foreach ($cart as $item) { ?>
								<?php // start cart loop for each tour in the order
								$required_fields = 0;
								$site->readItem($item);
								?>

								<?php if ((int) $item->availability >= (int) $item->pax_count) { ?>
									<?php $c++; // only increment if it's still available ?>

									<div class="rezgo-item-<?php echo $c?> clearfix">
										<script>split_total[<?php echo $c; ?>] = <?php echo $item->overall_total; ?>;</script>

										<?php
										if (in_array((string) $item->date_selection, $date_types)) {
											$booking_date = date("Y-m-d", (string)$item->booking_date);
										} else {
											$booking_date = 'open'; // for open availability
										}
										?>

										<input type="hidden" name="booking[<?php echo $c; ?>][book]" value="<?php echo $item->uid; ?>" />
										<input type="hidden" name="booking[<?php echo $c; ?>][date]" value="<?php echo $booking_date; ?>" />
										<input type="hidden" name="booking[<?php echo $c; ?>][adult_num]" value="<?php echo $item->adult_num; ?>" />
										<input type="hidden" name="booking[<?php echo $c; ?>][child_num]" value="<?php echo $item->child_num; ?>" />
										<input type="hidden" name="booking[<?php echo $c; ?>][senior_num]" value="<?php echo $item->senior_num; ?>" />
										<input type="hidden" name="booking[<?php echo $c; ?>][price4_num]" value="<?php echo $item->price4_num; ?>" />
										<input type="hidden" name="booking[<?php echo $c; ?>][price5_num]" value="<?php echo $item->price5_num; ?>" />
										<input type="hidden" name="booking[<?php echo $c; ?>][price6_num]" value="<?php echo $item->price6_num; ?>" />
										<input type="hidden" name="booking[<?php echo $c; ?>][price7_num]" value="<?php echo $item->price7_num; ?>" />
										<input type="hidden" name="booking[<?php echo $c; ?>][price8_num]" value="<?php echo $item->price8_num; ?>" />
										<input type="hidden" name="booking[<?php echo $c; ?>][price9_num]" value="<?php echo $item->price9_num; ?>" />

										<div id="error_scrollto"></div>

										<h3>
											<span class="text-info">
												<span>Booking <?php echo $c; ?> of </span>
												<span class="rezgo-cart-count"></span>
												<span>&nbsp;</span>
											</span>
											<br />
											<span><?php echo $item->item; ?> &mdash; <?php echo $item->option; ?></span>
										</h3>

										<?php if (in_array((string) $item->date_selection, $date_types)) {	?>
											<h4><?php echo date((string) $company->date_format, (string)$item->booking_date); ?></h4>
										<?php } ?>

										<?php if($item->discount_rules->rule) { ?>
											<h4 class="rezgo-booking-discount">
												<span class="rezgo-discount-span">Discount:</span>

												<?php unset($discount_string); ?>

												<?php foreach ($item->discount_rules->rule as $discount) {
													$discount_string .= ($discount_string) ? ', '.$discount : $discount;
												} ?>

												<span class="rezgo-red"><?php echo $discount_string; ?></span>
											</h4>
										<?php } ?>

										<?php if($item->group != 'hide') { ?>
											<div class="row rezgo-booking-instructions">
												<span>To finish this booking, please complete the following form. </span>
												<span id="required_note-<?php echo $c; ?>" <?php if($item->group == 'require' || $item->group == 'require_name') { echo 'style="display:inline;"'; } else { echo 'style="display:none;"'; } ?>>Please note that fields marked with <em class="fa fa-asterisk"></em> are required.</span>
											</div>

											<?php foreach ($site->getTourPrices($item) as $price) { ?>
												<?php foreach ($site->getTourPriceNum($price, $item) as $num) { ?>
													<div class="row rezgo-form-group">
														<div class="col-sm-12 rezgo-sub-title">
															<span><?php echo $price->label; ?> (<?php echo $num; ?>)</span>
														</div>

														<div class="rezgo-form-row rezgo-form-one form-group rezgo-pax-first-last">
															<label for="<?php echo "frm_".$c."_".$price->name."_".$num; ?>_first_name" class="col-sm-2 control-label rezgo-label-right">
																<span>First&nbsp;Name<?php if($item->group == 'require' || $item->group == 'require_name') { ?>&nbsp;<em class="fa fa-asterisk"></em><?php } ?></span>
															</label>

															<div class="col-sm-4 rezgo-form-input">
																<input type="text" class="form-control<?php echo ($item->group == 'require' || $item->group == 'require_name') ? ' required' : ''; ?>" id="frm_<?php echo $c?>_<?php echo $price->name?>_<?php echo $num?>_first_name" name="booking[<?php echo $c?>][tour_group][<?php echo $price->name?>][<?php echo $num?>][first_name]" /> 
															</div>

															<label for="<?php echo "frm_".$c."_".$price->name."_".$num; ?>_last_name" class="col-sm-2 control-label rezgo-label-right">
																<span>Last&nbsp;Name<?php if($item->group == 'require' || $item->group == 'require_name') { ?>&nbsp;<em class="fa fa-asterisk"></em><?php } ?></span>
															</label>

															<div class="col-sm-4 rezgo-form-input">
																<input type="text" class="form-control <?php echo ($item->group == "require" || $item->group == "require_name") ? 'required' : '' ?>" id="<?php echo "frm_".$c."_".$price->name."_".$num; ?>_last_name" name="<?php echo "booking[".$c."][tour_group][".$price->name."][".$num."]"; ?>[last_name]" />
															</div>
														</div>

														<?php if ($item->group != 'request_name') { ?>
															<div class="rezgo-form-row rezgo-form-one form-group rezgo-pax-phone-email">
																<label for="frm_<?php echo $c?>_<?php echo $price->name?>_<?php echo $num?>_phone" class="col-sm-2 control-label rezgo-label-right">Phone<?php if($item->group == 'require') { ?>&nbsp;<em class="fa fa-asterisk"></em><?php } ?></label>

																<div class="col-sm-4 rezgo-form-input">
																	<input type="text" class="form-control <?php echo ($item->group == "require") ? 'required' : ''; ?>" id="<?php echo "frm_".$c."_".$price->name."_".$num; ?>_phone" name="<?php echo "booking[".$c."][tour_group][".$price->name."][".$num."]"; ?>[phone]" />
																</div>
																<label for="frm_<?php echo $c?>_<?php echo $price->name?>_<?php echo $num?>_email" class="col-sm-2 control-label rezgo-label-right">Email<?php if($item->group == 'require') { ?>&nbsp;<em class="fa fa-asterisk"></em><?php } ?></label>
																<div class="col-sm-4 rezgo-form-input">
																	<input type="email" class="form-control <?php echo ($item->group == "require") ? 'required' : ''; ?>" id="<?php echo "frm_".$c."_".$price->name."_".$num; ?>_email" name="<?php echo "booking[".$c."][tour_group][".$price->name."][".$num."][email]"; ?>" />
																</div>
															</div>
														<?php } ?>

														<?php $form_counter = 1; // form counter to create unique IDs ?>

														<?php foreach ($site->getTourForms('group') as $form) { ?>
															<?php if ($form->require) $required_fields++; ?>

															<?php if ($form->type == 'text') { ?>
																<div class="form-group rezgo-custom-form rezgo-form-input">
																	<label><?php echo $form->title?><?php if($form->require) { ?> <em class="fa fa-asterisk"></em><?php } ?></label>

																	<input type="text" class="form-control <?php echo ($form->require) ? 'required' : ''; ?>" name="<?php echo "booking[".$c."][tour_group][".$price->name."][".$num."][forms][".$form->id."]"; ?>" />

																	<p class="rezgo-form-comment"><?php echo $form->instructions; ?></p>
																</div>
															<?php } ?>

															<?php if ($form->type == 'select') { ?>
																<div class="form-group rezgo-custom-form rezgo-form-input">
																	<label><?php echo $form->title?><?php if($form->require) { ?> <em class="fa fa-asterisk"></em><?php } ?></label>

																	<select class="form-control<?php echo ($form->require) ? ' required' : ''; ?>" name="booking[<?php echo $c?>][tour_group][<?php echo $price->name?>][<?php echo $num?>][forms][<?php echo $form->id?>]">
																		<?php foreach($form->options as $option) { ?>
																			<option><?php echo $option?></option>
																		<?php } ?>
																	</select>

																	<p class="rezgo-form-comment"><?php echo $form->instructions; ?></p>
																</div>
															<?php } ?>

															<?php if ($form->type == 'multiselect') { ?>
																<div class="form-group rezgo-custom-form rezgo-form-input">
																	<label><?php echo $form->title?><?php if($form->require) { ?> <em class="fa fa-asterisk"></em><?php } ?></label>

																	<select class="form-control <?php echo ($form->require) ? 'required' : ''; ?>" multiple="multiple" name="<?php echo "booking[".$c."][tour_group][".$price->name."][".$num."][forms][".$form->id."][]"; ?>">
																		<?php foreach ($form->options as $option) { ?>
																			<option><?php echo $option; ?></option>
																		<?php } ?>
																	</select>

																	<p class="rezgo-form-comment"><?php echo $form->instructions; ?></p>
																</div>
															<?php } ?>

															<?php if ($form->type == 'textarea') { ?>
																<div class="form-group rezgo-custom-form rezgo-form-input">
																	<label><?php echo $form->title?><?php if($form->require) { ?> <em class="fa fa-asterisk"></em><?php } ?></label>

																	<textarea class="form-control<?php echo ($form->require) ? ' required' : ''; ?>" name="<?php echo "booking[".$c."][tour_group][".$price->name."][".$num."][forms][".$form->id."]"; ?>" cols="40" rows="4"></textarea>

																	<p class="rezgo-form-comment"><?php echo $form->instructions; ?></p>
																</div>
															<?php } ?>

															<?php if ($form->type == 'checkbox') { ?>
																<div class="form-group rezgo-custom-form rezgo-form-input">
																	<div class="checkbox rezgo-form-checkbox">
																		<label for="<?php echo $form->id."|".htmlentities($form->title)."|".$form->price."|".$c."|".$price->name."|".$num; ?>">
																			<input type="checkbox" <?php echo ($form->require) ? 'class="required"' : ''; ?> id="<?php echo $form->id."|".htmlentities($form->title)."|".$form->price."|".$c."|".$price->name."|".$num; ?>" name="<?php echo "booking[".$c."][tour_group][".$price->name."][".$num."][forms][".$form->id."]"; ?>" data-id="<?php echo $form_counter ; ?>" data-name="<?php echo htmlentities($form->title); ?>" data-price="<?php if ($form->price_mod=='-') { echo $form->price_mod; } ?><?php echo $form->price; ?>"data-order="<?php echo $c; ?>" />

																			<span><?php echo $form->title; ?></span>
																			<?php if ($form->require) { ?> <em class="fa fa-asterisk"></em><?php } ?>
																			<?php if ($form->price) { ?> <em><?php echo $form->price_mod; ?> <?php echo $site->formatCurrency($form->price); ?></em><?php } ?>
																			<p class="rezgo-form-comment"><?php echo $form->instructions; ?></p>
																		</label>
																	</div>
																</div>
															<?php } ?>

															<?php if ($form->type == 'checkbox_price') { ?>
																<div class="form-group rezgo-custom-form rezgo-form-input">
																	<div class="checkbox rezgo-form-checkbox">
																		<label for="<?php echo $form->id."|".htmlentities($form->title)."|".$form->price."|".$c."|".$price->name."|".$num; ?>">
																			<input type="checkbox" class="<?php echo ($form->require) ? 'required' : ''; ?>" id="<?php echo $form->id."|".htmlentities($form->title)."|".$form->price."|".$c."|".$price->name."|".$num; ?>" name="<?php echo "booking[".$c."][tour_group][".$price->name."][".$num."][forms][".$form->id."]"; ?>" data-id="<?php echo $form_counter; ?>" data-name="<?php echo htmlentities($form->title); ?>" data-price="<?php if ($form->price_mod == '-') {echo $form->price_mod;} ?><?php echo $form->price; ?>" data-order="<?php echo $c; ?>" />

																			<span><?php echo $form->title; ?></span>
																			<?php if ($form->require) { ?> <em class="fa fa-asterisk"></em><?php } ?>
																			<?php if ($form->price) { ?> <em><?php echo $form->price_mod; ?> <?php echo $site->formatCurrency($form->price); ?></em><?php } ?>
																			<p class="rezgo-form-comment"><?php echo $form->instructions; ?></p>
																		</label>
																	</div>
																</div>
															<?php } ?>

															<?php $form_counter++; ?>
														<?php } // end foreach($site->getTourForms ?>
													</div>
												<?php } // end foreach($site->getTourPriceNum ?>
											<?php } ?>
										<?php } ?>

										<?php if($site->getTourForms('primary')) { ?>
											<?php if($item->group == 'hide') { ?>
												<div class="row rezgo-booking-instructions">
													<span>To finish this booking, please complete the following form.</span>
												</div>
											<?php } ?>

											<div class="row rezgo-form-group">
												<div class="col-sm-12 rezgo-sub-title">
													<span>Additional Information</span>
												</div>

												<div class="clearfix rezgo-short-clearfix">&nbsp;</div>
 
												<?php foreach ($site->getTourForms('primary') as $form) { ?>
													<?php if ($form->require) $required_fields++; ?>

													<?php if ($form->type == 'text') { ?>
														<div class="form-group rezgo-custom-form rezgo-form-input">
															<label><?php echo $form->title?><?php if($form->require) { ?> <em class="fa fa-asterisk"></em><?php } ?></label>
												
															<input type="text" class="form-control <?php echo ($form->require) ? 'required' : ''; ?>" name="<?php echo "booking[".$c."][tour_forms][".$form->id."]"; ?>" />

															<p class="rezgo-form-comment"><?php echo $form->instructions; ?></p>
														</div>
													<?php } ?>

													<?php if ($form->type == 'select') { ?>
														<div class="form-group rezgo-custom-form rezgo-form-input">
															<label><?php echo $form->title?><?php if($form->require) { ?> <em class="fa fa-asterisk"></em><?php } ?></label>

															<select name="<?php echo "booking[".$c."][tour_forms][".$form->id."]"; ?>" class="<?php echo ($form->require) ? 'required' : ''; ?>">
																<?php foreach ($form->options as $option) { ?>
																	<option><?php echo $option; ?></option>
																<?php } ?>
															</select>

															<p class="rezgo-form-comment"><?php echo $form->instructions; ?></p>
														</div>
													<?php } ?>

													<?php if ($form->type == 'multiselect') { ?>
														<div class="form-group rezgo-custom-form rezgo-form-input">
															<label><?php echo $form->title; ?><?php if($form->require) { ?> <em class="fa fa-asterisk"></em><?php } ?></label>

															<select multiple="multiple" name="<?php echo "booking[".$c."][tour_forms][".$form->id."][]"; ?>" class="<?php echo ($form->require) ? 'required' : ''; ?>">
																<?php foreach ($form->options as $option) { ?>
																	<option><?php echo $option?></option>
																<?php } ?>
															</select>

															<p class="rezgo-form-comment"><?php echo $form->instructions; ?></p>
														</div>
													<?php } ?>

													<?php if ($form->type == 'textarea') { ?>
														<div class="form-group rezgo-custom-form rezgo-form-input">
															<label><?php echo $form->title?><?php if($form->require) { ?> <em class="fa fa-asterisk"></em><?php } ?></label>

															<textarea class="form-control <?php echo ($form->require) ? 'required' : ''; ?>" name="<?php echo "booking[".$c."][tour_forms][".$form->id."]"; ?>" cols="40" rows="4"></textarea>

															<p class="rezgo-form-comment"><?php echo $form->instructions; ?></p>
														</div>
													<?php } ?>

													<?php if ($form->type == 'checkbox') { ?>
														<div class="form-group rezgo-custom-form rezgo-form-input">
															<div class="checkbox rezgo-form-checkbox">
																<label for="<?php echo $form->id."|".htmlentities($form->title)."|".$form->price."|".$c."|".$price->name."|".$num; ?>">
																	<input type="checkbox" class="<?php echo ($form->require) ? 'required' : ''; ?>" id="<?php echo $form->id."|".htmlentities($form->title)."|".$form->price."|".$c."|".$price->name."|".$num; ?>" name="<?php echo "booking[".$c."][tour_forms][".$form->id."]"; ?>" data-id="<?php echo $form_counter ; ?>" data-name="<?php echo htmlentities($form->title); ?>" data-price="<?php if ($form->price_mod == '-') {echo $form->price_mod;} ?><?php echo $form->price; ?>" data-order="<?php echo $c; ?>" />

																	<span><?php echo $form->title; ?></span>

																	<?php if ($form->require) { ?> <em class="fa fa-asterisk"></em><?php } ?> 

																	<?php if ($form->price) { ?> <em><?php echo $form->price_mod; ?> <?php echo $site->formatCurrency($form->price); ?></em><?php } ?>

																	<p class="rezgo-form-comment"><?php echo $form->instructions; ?></p>
																</label>
															</div>
														</div>
													<?php } ?>

													<?php if ($form->type == 'checkbox_price') { ?>
														<div class="form-group rezgo-custom-form rezgo-form-input">
															<div class="checkbox rezgo-form-checkbox">
																<label for="<?php echo $form->id."|".htmlentities($form->title)."|".$form->price."|".$c."|".$price->name."|".$num; ?>">
																	<input type="checkbox" class="<?php echo ($form->require) ? 'required' : ''; ?>" id="<?php echo $form->id."|".htmlentities($form->title)."|".$form->price."|".$c."|".$price->name."|".$num; ?>" name="<?php echo "booking[".$c."][tour_forms][".$form->id."]"; ?>" data-id="<?php echo $form_counter; ?>" data-name="<?php echo htmlentities($form->title); ?>" data-price="<?php if ($form->price_mod == '-') { echo $form->price_mod; } ?><?php echo $form->price; ?>" data-order="<?php echo $c; ?>" />

																	<span><?php echo $form->title; ?></span>
																	<?php if ($form->require) { ?> <em class="fa fa-asterisk"></em><?php } ?>
																	<?php if ($form->price) { ?> <em><?php echo $form->price_mod; ?> <?php echo $site->formatCurrency($form->price); ?></em><?php } ?>
																	<p class="rezgo-form-comment"><?php echo $form->instructions; ?></p>
																</label>
															</div>
														</div>
													<?php } ?>

													<?php $form_counter++; ?>
												<?php } ?>
											</div>
										<?php } ?>

										<?php if ($item->group == 'hide' && count($site->getTourForms('primary')) == 0) { ?>
											<div>Guest information is not required for booking #<?php echo $c; ?></div>
										<?php } ?>

										<?php if ($required_fields > 0) { ?>
											<script>jQuery(document).ready(function($) {$('#required_note-<?php echo $c; ?>').fadeIn();});</script>
										<?php } ?>
									</div>
								<?php } else { $cart_count--; } ?>
							<?php } ?>
							<?php // end cart loop for each tour in the order ?>

							<div class="row" id="rezgo-booking-btn">
								<div class="col-sm-6 col-xs-3 rezgo-btn-wrp rezgo-chevron-left">
									<?php if($site->getCartState()) { ?>
										<button type="button" class="btn rezgo-btn-default btn-lg center-block" onclick="top.location.href='<?php echo $site->base; ?>/order'; return false;">
											<span class="hidden-xs">Back to order</span>
											<span class="visible-xs-inline">
												<span class="glyphicon glyphicon-chevron-left"></span>
											</span>
										</button>
									<?php } ?>
								</div>

								<div class="col-sm-6 col-xs-9 rezgo-btn-wrp">
									<button type="button" class="btn rezgo-btn-book btn-lg center-block" id="btn-step-forward">Continue</button>
								</div>
							</div>
						</div>

						<script>jQuery(document).ready(function($){
							$('.rezgo-cart-count').text('<?php echo $cart_count; ?>');

							// copy info from first pax to billing fields
							$( "#rezgo-copy-pax" ).click(function() {
								if ($(this).prop("checked") == true) {
									$('#tour_first_name').val($('#frm_1_adult_1_first_name').val());
									$('#tour_last_name').val($('#frm_1_adult_1_last_name').val());
									$('#tour_email_address').val($('#frm_1_adult_1_email').val());
									$('#tour_phone_number').val($('#frm_1_adult_1_phone').val());
								} 
								else if($(this).prop("checked") == false) {
									$('#tour_first_name').val('');
									$('#tour_last_name').val('');
									$('#tour_email_address').val('');
									$('#tour_phone_number').val('');
								}
							});
						});</script>

						<div class="tab-pane" id="book_step_two">
							<div id="step_two_scrollto"></div>

							<div class="row">
									<ol class="breadcrumb rezgo-breadcrumb hidden-xs">
										<?php if($site->getCartState()) { ?>
											<li><a href="<?php echo $site->base; ?>/order">Your Order</a></li>
										<?php } ?>
										<li id="back-to-info">
											<a href="#" id="btn-guest-info">
												<span>Guest Information</span>
											</a>
										</li>
										<li class="active">Billing Information</li>
										<li>Confirmation</li>
									</ol>
							</div>

							<?php $c = 0; ?>

							<?php foreach ($cart as $item) { ?>
								<?php $site->readItem($item); ?>

								<?php if(DEBUG) { ?>
									<div class="row">
										<pre style="max-height:100px; overflow-y:auto; margin:15px 0"><?php var_dump($item); ?></pre>
									</div>
								<?php } ?>

								<?php if ((int) $item->availability >= (int) $item->pax_count) { ?>
									<?php $c++; // only increment if it's still available ?>

									<div class="row rezgo-form-group">
										<h3 class="rezgo-booking-of">
											<span class="text-info">
												<span>Booking <?php echo $c; ?> of </span>
												<span class="rezgo-cart-count"></span>
												<span>&nbsp;</span>
											</span>
											<br />
											<span><?php echo $item->item; ?> &mdash; <?php echo $item->option; ?></span>
										</h3>

										<div class="col-md-5 col-sm-12 col-xs-12">
											<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
												<?php if(in_array((string) $item->date_selection, $date_types)) { ?>
													<tr>
														<td class="rezgo-td-label">Date:</td>
														<td class="rezgo-td-data"><?php echo date((string) $company->date_format, (string) $item->booking_date); ?></td>
													</tr>
												<?php } ?>

												<?php if($item->duration != '') { ?>
													<tr>
														<td class="rezgo-td-label">Duration:</td>
														<td class="rezgo-td-data"><?php echo $item->duration; ?></td>
													</tr>
												<?php } ?>

												<?php if($item->discount_rules->rule) { ?>
													<tr>
														<td class="rezgo-td-label rezgo-booking-discount">
															<span class="rezgo-discount-span">Discount:</span>
														</td>
														<td class="rezgo-td-data">
															<?php unset ($discount_string); ?>

															<?php foreach ($item->discount_rules->rule as $discount) {
																$discount_string .= ($discount_string) ? ', '.$discount : $discount;
															} ?>
															<span class="rezgo-red"><?php echo $discount_string; ?></span>
														</td>
													</tr>
												<?php } ?>
											</table>
										</div>

										<div class="col-md-7 col-sm-12 col-xs-12">
											<table class="table-responsive">
												<table id="<?php echo $item->uid; ?>" class="table table-bordered table-striped rezgo-billing-cart">
													<tr>
														<td class="text-right"><label>Type</label></td>
														<td class="text-right"><label class="hidden-xs">Qty.</label></td>
														<td class="text-right"><label>Cost</label></td>
														<td class="text-right"><label>Total</label></td>
													</tr>

													<?php foreach ($site->getTourPrices($item) as $price) { ?>
														<?php if ($item->{$price->name.'_num'}) { ?>
															<tr>
																<td class="text-right"><?php echo $price->label; ?></td>
																<td class="text-right"><?php echo $item->{$price->name.'_num'}; ?></td>
																<td class="text-right">
																	<?php if ($site->exists($price->base)) { ?>
																		<span class="discount"><?php echo $site->formatCurrency($price->base); ?></span>
																	<?php } ?>
																	<?php echo $site->formatCurrency($price->price); ?>
																</td>
																<td class="text-right">
																	<?php if ($site->exists($price->base)) { ?>
																		<span class="discount"></span>
																	<?php } ?>
																	<?php echo $site->formatCurrency($price->total); ?>
																</td>
															</tr>
														<?php } ?>
													<?php } ?>

													<tr>
														<td colspan="3" class="text-right"><strong>Sub-total</strong></td>
														<td class="text-right"><?php echo $site->formatCurrency($item->sub_total); ?></td>
													</tr>

													<?php $line_items = $site->getTourLineItems(); ?>

													<?php foreach($line_items as $line) { ?>
														<?php unset($label_add); ?> 

														<?php if($site->exists($line->percent) || $site->exists($line->multi)) {
															$label_add = ' (';

															if($site->exists($line->percent)) {
																$label_add .= $line->percent.'%';
															}

															if($site->exists($line->multi)) {
																if(!$site->exists($line->percent)) {
																	$label_add .= $site->formatCurrency($line->multi);
																}

																$label_add .= ' x '.$item->pax;
															}

															$label_add .= ')';	
														} ?>

														<tr>
															<td colspan="3" class="text-right">
																<strong><?php echo $line->label; ?><?php echo $label_add; ?></strong>
															</td>

															<td class="text-right">
																<span class="rezgo-item-tax" rel="<?php echo $line->amount; ?>"><?php echo $site->formatCurrency($line->amount); ?></span>
															</td>
														</tr>
													<?php } ?>

													<tbody id="fee_box_<?php echo $c; ?>" class="rezgo-fee-box">
													</tbody>

													<tr>
														<td colspan="3" class="text-right">
															<strong>Total</strong>
														</td>

														<td class="text-right">
															<strong class="rezgo-item-total" id="total_value_<?php echo $c; ?>" rel="<?php echo $item->overall_total?>"><?php echo $site->formatCurrency($item->overall_total); ?></strong>
														</td>
													</tr>

													<tbody class="rezgo-gc-box" style="display:none">
														<tr>
															<td colspan="3" class="text-right alert-info">
																<strong>Gift Card</strong>
															</td>
															<td class="text-right alert-info">
																<strong><span>-</span> <span class="cur"></span><span class="rezgo-gc-min"></span></strong>
															</td>
														</tr>
													</tbody>

													<?php if ($site->exists($item->deposit)) { ?>
														<tr>
															<td colspan="3" class="text-right">
																<strong>Deposit to Pay Now</strong>
															</td>
															<td class="text-right">
																<strong class="rezgo-item-deposit" id="deposit_value_<?php echo $c; ?>" rel="<?php echo $item->deposit_value?>"><?php echo $site->formatCurrency($item->deposit_value); ?></strong>
															</td>
														</tr>

														<?php $complete_booking_total += (float) $item->deposit_value; ?>
													<?php } else { ?>
														<?php $complete_booking_total += (float) $item->overall_total; ?>
													<?php } ?>
												 </table>
											</table>
										</div>
									</div>
								<?php } // end if((int) $item->availability >= (int) $item->pax_count) ?>
							<?php } // end foreach($cart as $item ) ?>

							<script>
								overall_total = '<?php echo $complete_booking_total; ?>'; 
								form_decimals = '<?php echo $item->currency_decimals; ?>';
								form_symbol = '<?php echo $item->currency_symbol; ?>';
								form_separator = '<?php echo $item->currency_separator; ?>';
							</script>

							<div class="row">
								<div class="col-sm-7 col-xs-12 col-sm-offset-5 rezgo-total-payable">
										<span>Total to Pay Now: </span>

										<span id="total_value" rel="<?php echo $complete_booking_total; ?>"><?php echo $site->formatCurrency($complete_booking_total); ?></span>

										<input type='hidden' name='expected' value="<?php echo $complete_booking_total; ?>" />
								</div>

								<div class="clearfix visible-xs"></div>
							</div>

							<!-- // grand total -->

							<div class="row rezgo-form-group rezgo-booking">
								<div class="col-xs-12">
									<h3 class="text-info">
										<span>Billing Information &nbsp;</span>
										<span id="rezgo-copy-pax-span" style="display:none">
											<br class="visible-xs-inline"/>
											<input type="checkbox" name="copy_pax" id="rezgo-copy-pax" />
											<span id="rezgo-copy-pax-desc" class="rezgo-memo">Use first passenger information</span>
										</span>
									</h3>

									<div class="form-group">
										<label for="tour_first_name" class="control-label">Name</label>

										<div class="rezgo-form-row">
											<div class="col-sm-6 rezgo-form-input">
												<input type="text" class="form-control" id="tour_first_name" name="tour_first_name" value="<?php echo $site->requestStr('tour_first_name'); ?>" placeholder="First Name" />
											</div>

											<div class="col-sm-6 rezgo-form-input">
												<input type="text" class="form-control" id="tour_last_name" name="tour_last_name" value="<?php echo $site->requestStr('tour_last_name'); ?>" placeholder="Last Name" />
											</div>
										</div>
									</div>

									<div class="form-group">
										<label for="tour_address_1" class="control-label">Address</label>

										<div class="rezgo-form-input col-xs-12">
											<input type="text" class="form-control" id="tour_address_1" name="tour_address_1" placeholder="Address 1" />
										</div>
									</div>

									<div class="form-group clearfix">
										<div class="rezgo-form-input col-xs-12">
											<input type="text" class="form-control" id="tour_address_2" name="tour_address_2" placeholder="Address 2 (optional)" />
										</div>
									</div>

									<div class="form-group">
										<div class="rezgo-form-row">
											<label for="tour_city" class="control-label col-sm-8 col-xs-12 rezgo-form-label">City</label>
											<label for="tour_postal_code" class="control-label col-sm-4 hidden-xs rezgo-form-label">Zip/Postal</label>
										</div>

										<div class="rezgo-form-row">
											<div class="col-sm-8 col-xs-12 rezgo-form-input">
												<input type="text" class="form-control" id="tour_city" name="tour_city" placeholder="City" />
											</div>

											<label for="tour_postal_code" class="control-label col-xs-12 hidden-lg hidden-md hidden-sm rezgo-form-label">Zip/Postal</label>
											<div class="col-sm-4 col-xs-12 rezgo-form-input">
												<input type="text" class="form-control" id="tour_postal_code" name="tour_postal_code" placeholder="Zip/Postal Code" />
											</div>
										</div>
									</div>

									<div class="form-group">
										<div class="rezgo-form-row">
											<label for="tour_country" class="control-label col-sm-8 rezgo-form-label">Country</label>
											<label for="tour_stateprov" class="control-label col-sm-4 hidden-xs rezgo-form-label">State/Prov</label>
										</div>

										<div class="rezgo-form-row">
											<div class="col-sm-8 col-xs-12 rezgo-form-input">
											<select name="tour_country" id="tour_country" class="form-control">
												<?php foreach ($site->getRegionList() as $iso => $name) { ?>
													<option value="<?php echo $iso; ?>" <?php echo (($iso == $companyCountry) ? 'selected' : ''); ?>><?php echo ucwords($name); ?></option>
												<?php } ?>
											</select>
											</div>

											<div class="col-sm-4 col-xs-12 rezgo-form-input">
												<div class="rezgo-form-row hidden-lg hidden-md hidden-sm">
													<label for="tour_stateprov" class="control-label col-xs-12 rezgo-form-label">State/Prov</label>
												</div>

												<select id="tour_stateprov" class="form-control"<?php if ($companyCountry != 'ca' && $companyCountry != 'us' && $companyCountry != 'au') { ?> style="display:none"<?php } ?>></select>

												<input id="tour_stateprov_txt" class="form-control" name="tour_stateprov" type="text" value="" <?php if ($companyCountry == 'ca' || $companyCountry == 'us' || $companyCountry == 'au') { ?>style=" display:none"<?php } ?> />
											</div>
										</div>
									</div>

									<div class="form-group">
										<div class="rezgo-form-row">
											<label for="tour_email_address" class="control-label col-sm-6 rezgo-form-label">Email</label>
											<label for="tour_phone_number" class="control-label col-sm-6 hidden-xs rezgo-form-label">Phone</label>
										</div>

										<div class="rezgo-form-row">
											<div class="col-sm-6 col-xs-12 rezgo-form-input">
												<input type="email" class="form-control" id="tour_email_address" name="tour_email_address" placeholder="Email" />
											</div>

											<label for="tour_phone_number" class="control-label col-sm-6 col-xs-12 hidden-lg hidden-md hidden-sm rezgo-form-label">Phone</label>

											<div class="col-sm-6 col-xs-12 rezgo-form-input">
												<input type="text" class="form-control" id="tour_phone_number" name="tour_phone_number" placeholder="Phone" />
											</div>
										</div>
									</div>

									<div class="form-group">
										<div class="rezgo-form-row">
											<div class="col-sm-12 rezgo-sms">
												<span>Would you like to receive SMS messages regarding your booking? If so, please enter your mobile number in the space provided. Please note that your provider may charge additional fees.</span> 
											</div>
										</div>
									</div>

									<div class="form-group">
										<div class="rezgo-form-row">
											<label for="tour_sms" class="control-label col-sm-12 rezgo-form-label">SMS</label>
										</div>
										<div class="rezgo-form-row">
											<div class="col-sm-12 rezgo-form-input">
												<input type="text" name="tour_sms" id="tour_sms" class="form-control col-xs-12" value="" />
												<input type="hidden" name="sms" id="sms" value="" />
											</div>
										</div>
									</div>
								</div>
							</div>

							<?php if(!$site->isVendor() ) { ?>
								<div id="rezgo-gift-card-use" <?php if(($complete_booking_total > 0) == 0) { ?>style="display:none"<?php } ?>>
									<div class="row rezgo-form-group rezgo-booking">
										<div class="col-xs-12">
											<?php require 'gift_card_redeem.php'; ?>
										</div>
									</div>
								</div>
							<?php } ?>

							<hr>

							<div class="row rezgo-form-group rezgo-booking">
								<div class="col-xs-12">
									<h3 class="text-info" id="payment_info_head" style="<?php echo (($complete_booking_total > 0) ? '' : 'display:none;'); ?>">Payment Information</h3>

									<div class="rezgo-payment-frame" id="payment_info" style="<?php echo (($complete_booking_total > 0) ? '' : 'display:none;'); ?>">
										<div class="form-group" id="payment_methods">
											<?php 
												$card_fa_logos = array(
													'visa' => 'fa-cc-visa',
													'mastercard' => 'fa-cc-mastercard',
													'american express' => 'fa-cc-amex',
													'discover' => 'fa-cc-discover'
												);
												$pmc = 1; // payment method counter 1
											?>

											<?php foreach ($site->getPaymentMethods() as $pay) { ?>
												<?php if ($pay[name] == 'Credit Cards') { ?>
													<div class="rezgo-input-radio">
														<input type="radio" name="payment_method" id="payment_method_credit" class="rezgo-payment-method" value="Credit Cards" checked />

														<span>&nbsp;&nbsp;</span>

														<label for="payment_method_credit">
															<span class="hidden-xs">Credit </span>
															<span>Card</span>
															<span>&nbsp;&nbsp;</span>
															<?php foreach ($site->getPaymentCards() as $card) { ?>
																<img src="<?php echo $site->path; ?>/img/logos/<?php echo $card; ?>.png" class="hidden-xs" />
																<span class="visible-xs-inline">
																	<i class="fa <?php echo $card_fa_logos[$card]; ?>"></i>
																</span>
															<?php } ?>
														</label>

														<input type="hidden" name="tour_card_token" id="tour_card_token" value="" />

														<script>
															jQuery(document).ready(function($) {
																$('#tour_card_token').val('');
																setTimeout(function() {
																	$('#payment_method_credit').attr('checked', true);
																}, 600);
															})
														</script>
													</div>
												<?php } else { ?>
													<?php if ($pay[name] == 'PayPal') { ?>
														<?php $set_name = '
															<img src="'.$site->path.'/img/logos/paypal.png" class="hidden-xs" />
															<span class="visible-xs-inline">PayPal <i class="fa fa-cc-paypal"></i></span>
														'; ?>
													<?php } else { ?>
														<?php $set_name = $pay[name]; ?>
													<?php } ?>

													<div class="rezgo-input-radio">
														<input type="radio" name="payment_method" id="payment_method_<?php echo $pmc; ?>" class="rezgo-payment-method" value="<?php echo $pay[name]; ?>" />

														<span>&nbsp;&nbsp;</span>

														<label for="payment_method_<?php echo $pmc; ?>"><?php echo $set_name; ?></label>
													</div>

													<?php $pmc++; ?>
												<?php } ?>
											<?php } // end foreach($site->getPaymentMethods() ?>
										</div><!-- // #payment_methods -->

										<div id="payment_data">
											<?php $pmdc = 1; // payment method counter 1 ?>

											<?php foreach ($site->getPaymentMethods() as $pay) { ?>
												<?php if ($pay[name] == 'Credit Cards') { ?>
													<div id="payment_cards">
														<iframe scrolling="no" frameborder="0" name="tour_payment" id="tour_payment" src="<?php echo home_url(); ?>?rezgo=1&mode=booking_payment"></iframe>

														<script type="text/javascript">
															iFrameResize ({
																enablePublicMethods: true,
																scrolling: false
															});
														</script>
													</div>
												<?php } else { ?>
													<div id="payment_method_<?php echo $pmdc; ?>_box" class="payment_method_box" style="display:none;">
														<?php if ($pay[add]) { ?>
															<div id="payment_method_<?php echo $pmdc; ?>_container" class="payment_method_container">
																<span><?php echo $pay[add]; ?></span>
																<br>
																<input type="text" id="payment_method_<?php echo $pmdc; ?>_field" class="payment_method_field" name="payment_method_add" value="" disabled="disabled" />
															</div>
														<?php } ?>
													</div>

													<?php $pmdc++; ?>
												<?php } ?>
											<?php } // end ?>
										</div><!-- // #payment_data -->
									</div><!-- // #payment_info -->

									<div class="rezgo-form-row">
										<div class="col-sm-12 rezgo-payment-terms">
											<div class="rezgo-form-input">
												<div class="checkbox">
													<label id="rezgo-terms-label">
														<input type="checkbox" id="agree_terms" name="agree_terms" value="1" />
														<span>I agree to the </span>
														<a data-toggle="collapse" class="collapsed" id="rezgo-terms-link" data-target="#rezgo-terms-panel">Terms and Conditions</a>
													</label>
												</div>

												<div id="rezgo-terms-panel" class="collapse">
													<?php echo $site->getPageContent('terms'); ?>
													<?php if($company->tripadvisor_url != '') { ?>
														<p class="rezgo-ta-privacy">
															<span>Privacy Addendum</span>
															<br />
															<span>We may use third-party service providers such as TripAdvisor to process your personal information on our behalf. For example, we may share some information about you with these third parties so that they can contact you directly by email (for example: to obtain post visit reviews about your experience).</span>
														</p>
													<?php } ?>
												</div>
											</div>

											<hr />

											<div id="rezgo-book-terms">
												<div class="help-block" id="terms_credit_card" style="display:<?php if (!$site->getPaymentMethods('Credit Cards')) { ?>none<?php } ?> ;">
													<?php if ($site->getGateway() OR $site->isVendor()) { ?>
														<?php if ($complete_booking_total > 0) { ?>
															<span class='terms_credit_card_over_zero'>Please note that your credit card will be charged.</span>
															<br>
														<?php } ?>
														<span>If you are satisfied with your entries, please click the &quot;Complete Booking&quot; button.</span>
													<?php } else { ?>
														<?php if ($complete_booking_total > 0) { ?>
															<span class='terms_credit_card_over_zero'>Please note that your credit card will not be charged now. Your transaction information will be stored until your payment is processed. Please see the Terms and Conditions for more information.</span>
															<br />
														<?php } ?>
														<span>If you are satisfied with your entries, please click the &quot;Complete Booking&quot; button.</span>
													<?php } ?>
												</div>

												<div class="help-block" id="terms_other" style="display:<?php if ($site->getPaymentMethods('Credit Cards')) { ?>none<?php } ?>;">
													<span>If you are satisfied with your entries, please click the &quot;Complete Booking&quot; button.</span>
												</div>
											</div>

											<div id="rezgo-book-message" style="display:none;">
												<div id="rezgo-book-message-body"></div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="rezgo-form-row">
								<div class="col-sm-6 col-xs-3 rezgo-btn-wrp rezgo-chevron-left">
									<button type="button" id="btn-step-back" class="btn rezgo-btn-default btn-lg btn-block">
										<span class="hidden-xs">Previous Step</span>
										<span class="hidden-sm hidden-md hidden-lg">
											<span class="glyphicon glyphicon-chevron-left"></span>
										</span>
									</button>
								</div>

								<div class="col-sm-6 col-xs-9 rezgo-btn-wrp">
									<input type="submit" class="btn rezgo-btn-book btn-lg btn-block" id="rezgo-complete-booking" value="Complete Booking" />
								</div>
							</div>
						</div><!-- // #book_step_two -->
					</div>
				</form>

				<div class="row">
					<div class="col-sm-12 col-md-6">
						<p>&nbsp;</p>
						<br />
					</div>

					<div class="col-sm-12 col-md-6">
						<div class="alert alert-danger" id="rezgo-book-errors">
							<span>Some required fields are missing. Please complete the highlighted fields.</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>#debug_response {width:100%; height:200px;}</style>

<script>
	var toComplete = 0;
	var response; // needs to be global to work in timeout
	var paypalAccount = 0;

	var ca_states = <?php echo json_encode( $site->getRegionList('ca') ); ?>;
	var us_states = <?php echo json_encode( $site->getRegionList('us') ); ?>;
	var au_states = <?php echo json_encode( $site->getRegionList('au') ); ?>;	

	jQuery(document).ready(function($){
		// Catch form submissions
		$('#book').submit(function(evt) {
			evt.preventDefault();

			submit_booking();
		});

		$('#tour_country').change(function() {
			var country = $(this).val();

			// set SMS country 
			$("#tour_sms").intlTelInput("setCountry", $(this).val());

			$('#tour_stateprov').removeOption(/.*/);

			switch (country) {
				case 'ca':
					$('#tour_stateprov_txt').hide();
					$('#tour_stateprov').addOption(ca_states, false).show();
					$('#tour_stateprov_txt').val($('#tour_stateprov').val());
					break;
				case 'us':
					$('#tour_stateprov_txt').hide();
					$('#tour_stateprov').addOption(us_states, false).show();
					$('#tour_stateprov_txt').val($('#tour_stateprov').val());
					break;
				case 'au':
					$('#tour_stateprov_txt').hide();
					$('#tour_stateprov').addOption(au_states, false).show();
					$('#tour_stateprov_txt').val($('#tour_stateprov').val());
					break;		
				default:
					$('#tour_stateprov').hide();
					$('#tour_stateprov_txt').val('');
					$('#tour_stateprov_txt').show();
					break;			
			}
		});

		$('#tour_stateprov').change(function() {
			var state = $(this).val();
			$('#tour_stateprov_txt').val(state);
		});

		<?php if(in_array($site->getCompanyCountry(), array('ca', 'us', 'au'))) { ?>
			$('#tour_stateprov').addOption(<?php echo $site->getCompanyCountry(); ?>_states, false);

			$('#tour_stateprov_txt').val($('#tour_stateprov').val());
		<?php } ?>

		if (typeof String.prototype.trim != 'function') {
			// detect native implementation
			String.prototype.trim = function () {
				return this.replace(/^\s+/, '').replace(/\s+$/, '');
			};
		}

		// Change the modal dialog box or pass the user to the receipt depending on the response
		function show_response()	{
			response = response.trim();

			if (response != '1') {
				$('#rezgo-complete-booking').val('Complete Booking');
				$('#rezgo-complete-booking').removeAttr('disabled');
			}

			if (response == '2') {
				var title = 'No Availability Left';
				var body = 'Sorry, there is not enough availability left for this item on this date.<br />';
			} else if (response == '3') {
				var title = 'Payment Error';
				var body = 'Sorry, your payment could not be completed. Please verify your card details and try again.<br /';
			} else if (response == '4') {
				var title = 'Booking Error';
				var body = 'Sorry, there has been an error with your booking and it can not be completed at this time.<br />';
			} else if (response == '5') {
				// this error should only come up in preview mode without a valid payment method set
				var title = 'Booking Error';
				var body = 'Sorry, you must have a credit card attached to your Rezgo Account in order to complete a booking.<br><br>Please go to "Settings &gt; Rezgo Account" to attach a credit card.<br />';
			} else if (response == '6') {
				// this error should only come up in preview mode without a valid payment method set
				var title = 'Booking Error';
				var body = 'Sorry, a price on an item you are booking has changed. Please return to the shopping cart and try again.<br />';
			} else {
				// this section is mostly for debug handling
				if (response.indexOf('STOP::') != -1) {	
					var split = response.split('<br><br>');
					if (split[1] == '2' || split[1] == '3' || split[1] == '4') {
						split[1] = '<br /><br />Error Code: ' + split[1] + '<br />';
					} else {
						split[1] = '<div class="clearfix">&nbsp;</div>BOOKING COMPLETED WITHOUT ERRORS<div class="clearfix">&nbsp;</div><button type="button" class="btn btn-default" onclick="window.location.replace(\'<?php echo $site->base; ?>/complete/' + split[1] + '\');">Continue to Receipt</button><div class="clearfix">&nbsp;</div>';
					}
	
					var body = 'DEBUG-STOP ENCOUNTERED<br /><br />' + '<textarea id="debug_response">' + split[0] + '</textarea>' + split[1];
				} else {
					// send the user to the receipt page
					top.location.replace("<?php echo $site->base; ?>/complete/" + response);
					return true; // stop the html replace
				}
			}

			$('#rezgo-book-message-body').html(body);
			$('#rezgo-book-message-body').addClass('alert alert-warning');
		}

		// This function delays the output so we see the loading graphic
		function delay_response(responseText) {
			response = responseText;
			if(response.debug) {
				console.log(response);
			} else {
				setTimeout(function () {
					show_response();
				}, 800);
			}
		}

		function validate_form() {

			var valid = $("#book").valid();

			return valid;
		}

		function error_booking() {
			$('#rezgo-book-errors').fadeIn();

			setTimeout(function () {
					$('#rezgo-book-errors').fadeOut();
			}, 5000);	
			return false;
		}

		function submit_booking() {
			// do nothing if we are on step 1
			if(toComplete == 0) return false;
		
			var validate_check = validate_form();

			$('#rezgo-complete-booking').val('Please wait ...');
			$('#rezgo-complete-booking').attr('disabled','disabled');
			$('#rezgo-book-message-body').removeClass();
			$('#rezgo-book-message-body').html('');
			$('#rezgo-book-message').fadeOut();
			$('#rezgo-book-terms').fadeIn();

			// only activate on actual form submission, check payment info
			if (toComplete == 1 && overall_total != 0) {

				var force_error = 0;

				var payment_method = $('input:radio[name=payment_method]:checked').val();

				if (payment_method == 'Credit Cards') {

					if (!$('#tour_payment').contents().find('#payment').valid()) {
						force_error = 1;
					}

				} else {
					// other payment methods need their additional fields filled

					var id = $('input:radio[name=payment_method]:checked').attr('id');
					if($('#' + id + '_field').length != 0 && !$('#' + id + '_field').val()) {
						// this payment method has additional data that is empty
						force_error = 1;

						$('#' + id + '_container').css('border-color', '#990000');
					}
				}
			}

			if (force_error || !validate_check) {
				$('#rezgo-complete-booking').val('Complete Booking');

				$('#rezgo-complete-booking').removeAttr('disabled');

				return error_booking();
			} else {
				if (toComplete == 1) {
					$('#rezgo-book-message-body').html('<center>Please wait a moment... <i class="fa fa-circle-o-notch fa-spin"></i></center>');

					$('#rezgo-book-terms').fadeOut().promise().done(function(){
						 $('#rezgo-book-message').fadeIn();
					});

					var payment_method = $('input:radio[name=payment_method]:checked').val();
		
					if (payment_method == 'Credit Cards' && overall_total != 0) {
						// clear the existing credit card token, just in case one has been set from a previous attempt
						$('#tour_card_token').val('');

						// submit the card token request and wait for a response
						$('#tour_payment').contents().find('#payment').submit();

						// wait until the card token is set before continuing (with throttling)
						function check_card_token() {
							var card_token = $('#tour_card_token').val();

							if (card_token == '') {
								// card token has not been set yet, wait and try again
								setTimeout(function() {
									check_card_token();
								}, 200);
							} else {
								// the field is present ? submit normally..
								$('#book').ajaxSubmit({
									url: "<?php echo admin_url('admin-ajax.php'); ?>" + '?action=rezgo&method=book_ajax',
									data: { rezgoAction: 'book' }, 
									success: delay_response, 
									error: function() {
									var body = 'Sorry, the system has suffered an error that it can not recover from.<br />Please try again later.<br />';

										$('#rezgo-book-message-body').html(body);
								
										$('#rezgo-book-message-body').addClass('alert alert-warning');
									}
								});
							}
						}

						check_card_token();
					} else {
						// not a credit card payment (or $0) and everything checked out, submit via ajaxSubmit (jquery.form.js)
						$('#book').ajaxSubmit({
							url: "<?php echo admin_url('admin-ajax.php'); ?>" + '?action=rezgo&method=book_ajax',
							data: {
								debug: <?php echo (DEBUG) ? 1 : 0 ?>, // set to 0 on prod 
								rezgoAction: 'book',	
							}, 
							success: delay_response,
							error: function() {
								var body = 'Sorry, the system has suffered an error that it can not recover from.<br><br>Please try again later.<br />';

								$('#rezgo-book-message-body').html(body);

								$('#rezgo-book-message-body').addClass('alert alert-warning');
							}
						});
					}

					// return false to prevent normal browser submit and page navigation 
					return false;
				}
			}
		}

		function stepForward() {
			if(!validate_form()) return error_booking();
	
			toComplete = 1;

			var step_two_position = $('#step_two_scrollto').position();

			var step_two_scroll = Math.round(step_two_position.top);

			if ('parentIFrame' in window) {
				setTimeout(function () {
						parentIFrame.scrollTo(0,0);
				}, 100);
			}

			// show copy pax checkbox if we have pax info
			if ($('#frm_1_adult_1_first_name').val()) {
				$('#rezgo-copy-pax-span').show();
			}

			$('#rezgo-book-errors').fadeOut();
			
			$("#tour_first_name").addClass("required");
			$("#tour_last_name").addClass("required");
		
			$("#tour_address_1").addClass("required");
			$("#tour_city").addClass("required");
			$("#tour_country").addClass("required");
			$("#tour_postal_code").addClass("required");
		
			$("#tour_phone_number").addClass("required");
			$("#tour_email_address").addClass("required");
		
			$("#agree_terms").addClass("required");
		
			$('#book_steps	a:last').tab('show');
		}

		function stepBack() {
			toComplete = 0;

			$('#book_steps	a:first').tab('show');

			$("#tour_first_name").removeClass("required");
			$("#tour_last_name").removeClass("required");

			$("#tour_address_1").removeClass("required");
			$("#tour_city").removeClass("required");
			$("#tour_country").removeClass("required");
			$("#tour_postal_code").removeClass("required");

			$("#tour_phone_number").removeClass("required");
			$("#tour_email_address").removeClass("required");

			$("#agree_terms").removeClass("required");

			if ('parentIFrame' in window) {
				setTimeout(function () {
					parentIFrame.scrollTo(0,0);
				}, 100);
			}
		}

		function toggleCard() {
			if ($('input[name=payment_method]:checked').val() == 'Credit Cards') {
				<?php $pmn = 0; ?>
				<?php foreach ( $site->getPaymentMethods() as $pay ) { ?>	
					<?php if ($pay[name] == 'Credit Cards') { ?>
					<?php } else { ?>
						<?php $pmn++; ?>
						$('#payment_method_<?php echo $pmn; ?>_box').fadeOut();
						$('#payment_method_<?php echo $pmn; ?>_field').attr('disabled', 'disabled');
					<?php } ?>
				<?php } ?>

				setTimeout(function() {
					$('#payment_cards').fadeIn();
				}, 450);

				document.getElementById("terms_other").style.display = 'none';

				document.getElementById("terms_credit_card").style.display = '';	
			} else if ($('input[name=payment_method]:checked').val() == 'PayPal') {
				<?php $pmn = 0; ?>
				<?php foreach ( $site->getPaymentMethods() as $pay ) { ?>	
					<?php if ($pay[name] == 'Credit Cards') { ?>
						$('#payment_cards').fadeOut();
					<?php } else { ?>
						<?php $pmn++; ?>
						$('#payment_method_<?php echo $pmn; ?>_box').fadeOut();
						$('#payment_method_<?php echo $pmn; ?>_field').attr('disabled', 'disabled');
					<?php } ?>
				<?php } ?>	

				document.getElementById("terms_credit_card").style.display = 'none';

				document.getElementById("terms_other").style.display = '';
			} else {
				<?php $pmn = 0; ?>
				<?php foreach ( $site->getPaymentMethods() as $pay ) { ?>	
					<?php if ($pay[name] == 'Credit Cards') { ?>
						$('#payment_cards').fadeOut();
					<?php } else { ?>
						<?php $pmn++; ?>
						$('#payment_method_<?php echo $pmn; ?>_box').fadeOut();
						$('#payment_method_<?php echo $pmn; ?>_field').attr('disabled', 'disabled');
					<?php } ?>
				<?php } ?>

				setTimeout(function() {
					var id = $('input[name=payment_method]:checked').attr('id');
					$('#' + id + '_box').fadeIn();
					$('#' + id + '_field').attr('disabled', false);
				}, 450);

				document.getElementById("terms_credit_card").style.display = 'none';

				document.getElementById("terms_other").style.display = '';
			}
		}

		// these functions do a soft-commit when you click on the paypal option so they
		// can get an express payment token from the paypal API via the XML gateway
		function getPaypalToken(force) {
			// if we aren't forcing it, don't load if we already have an id
			if (!force && paypalAccount == 1) {
				// an account is set, don't re-open the box
				return false;
			}

			$('#book').ajaxSubmit({
				url: "<?php echo admin_url('admin-ajax.php'); ?>" + '?action=rezgo&method=book_ajax',
				data: { rezgoAction: 'get_paypal_token' },
				success: function(token) {
					// this section is mostly for debug handling
					if (token.indexOf('STOP::') != -1) {
						var split = token.split('<br><br>');

						if (split[1] == '0') {
							alert('The system encountered an error with PayPal. Please try again in a few minutes or select another payment method.');
							return false;
						}

						token = split[1];
					}

					dg.startFlow("https://www.paypal.com/incontext?token=" + token.trim());
				}
			});
		}

		function paypalCancel() {
			// the paypal transaction was cancelled, uncheck the radio and close the box
			dg.closeFlow();
			$('#payment_method_paypal').attr('checked', false);
		}

		function creditConfirm(token) {
			// the credit card transaction was completed, give us the token
			$('#tour_card_token').val(token);
		}

		// this function checks through each element on the form, if that element is
		// a checkbox and has a price value and is checked (thanks to browser form retention)
		// then we go ahead and add that to the total like it was clicked
		function saveForm (form) {
			$(':input', form).each(function() {
				if (this.type == 'checkbox' && this.checked == true) {
					var split = this.id.split("|");
					// if the ID contains a price value then add it
					if (split[2]) add_element(split[0], split[1], split[2], split[3]);
				}
			 });
		};

		saveForm('#book');

		// Money Formatting
		// Add/sub elements
		Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator) {
			var n = this,
			decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? form_decimals : decPlaces,
			decSeparator = decSeparator == undefined ? "." : decSeparator,
			thouSeparator = thouSeparator == undefined ? form_separator : thouSeparator,
			sign = n < 0 ? "-" : "",
			i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
			j = (j = i.length) > 3 ? j % 3 : 0;

			var dec;
			var out = sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator);
			if (decPlaces) dec = Math.abs(n - i).toFixed(decPlaces).slice(2);
			if (dec) out += decSeparator + dec;
			return out;
		};

		function clean_money_string(str) {
			// convert to str in case it has strange characters (like a ,)
			str += '';
			// clean (except . and -) and convert back to float
			return parseFloat(str.replace(/[^0-9.-]/, ""));
		}

		function add_element(id, name, price, order_num) {
			// GIFT CARD RESET
			gcReset();

			// ensure our array has an array for the actual elements
			if (!elements[order_num]) elements[order_num] = new Array();

			var num = add_price = clean_money_string(price);

			if (elements[order_num][id]) {
				num = num + clean_money_string(elements[order_num][id]);
			}

			var price = num.formatMoney();

			var display_price = form_symbol + price;

			name = name.replace("\\'", "'");

			if(!elements[order_num][id]) {
				var content = '<tr><td colspan="3" class="text-right" id="element_'+order_num+'_'+id+'"><strong>'+name+'</strong></td><td class="text-right" id="val_'+order_num+'_'+id+'"><span class="rezgo-item-opt" rel="'+price+'">'+display_price+'</span></td></tr>';

				$("#fee_box_" + order_num).html( $("#fee_box_" + order_num).html() + content );

				$("#fee_box_" + order_num).show();
			} else {
				if (document.getElementById('element_' + order_num + '_' + id).style.display == 'none') {
					document.getElementById('element_' + order_num + '_' + id).style.display = '';
				}

				if (document.getElementById('val_' + order_num + '_' + id).style.display == 'none') {
					document.getElementById('val_' + order_num + '_' + id).style.display = '';
				}

				$("#val_" + order_num + "_" + id).html(display_price);
			}
			elements[order_num][id] = price;

			// add to total amount
			var total = split_total[order_num];
			total = clean_money_string(total) + add_price;
			total = total.formatMoney();
			split_total[order_num] = total;

			// set the total for this item
			$("#total_value_" + order_num).html(form_symbol + total);
			$("#total_value_" + order_num).attr('rel', clean_money_string(total));

			// set the order total if this item doesn't have a deposit set
			if (!$("#deposit_value_" + order_num).html()) {
				overall_total = clean_money_string(overall_total) + add_price;
				overall_total = overall_total.formatMoney();

				$("#total_value").html(form_symbol + overall_total);
				$("#total_value").attr('rel', clean_money_string(overall_total));
				$("input[name='expected']").val(clean_money_string(overall_total));
			}

			// if total is greater than 0 then show payment section
			if (overall_total > 0) {
				$('#payment_info').show();
				$('#payment_info_head').show();
			}
		}

		function sub_element(id, price, order_num) {
			// gift card reset
			gcReset();

			// ensure our array has an array for the actual elements
			if(!elements[order_num]) elements[order_num] = new Array();

			if(!elements[order_num][id] || elements[order_num][id] == 0) return false;

			var num = sub_price = clean_money_string(price);
			num = clean_money_string(elements[order_num][id]) - num;

			var price = num.formatMoney();
			if(price < 0) price = 0;

			var display_price = form_symbol + price;

			if (price == 0) {
				document.getElementById('element_' + order_num + '_' + id).style.display = 'none';

				document.getElementById('val_' + order_num + '_' + id).style.display = 'none';
			} else {
				document.getElementById('val_' + order_num + '_' + id).innerHTML = display_price;
			}

			elements[order_num][id] = price;

			// sub from total amount
			var total = split_total[order_num];
			total = clean_money_string(total) - sub_price;
			total = total.formatMoney();
			split_total[order_num] = total;

			// set the total for this item
			$("#total_value_" + order_num).html(form_symbol + total);
			$("#total_value_" + order_num).attr('rel', clean_money_string(total));

			// set the order total if this item doesn't have a deposit set
			if (!$("#deposit_value_" + order_num).html()) {
				overall_total = clean_money_string(overall_total) - sub_price;
				overall_total = overall_total.formatMoney();

				$("#total_value").html(form_symbol + overall_total);
				$("#total_value").attr('rel', clean_money_string(overall_total));
				$("input[name='expected']").val(clean_money_string(overall_total));
			}

			// if total is 0 then hide payment section
			if (overall_total <= 0) {
				$('#payment_info').hide();
				$('#payment_info_head').hide();
			}
		}

		$('.rezgo-form-checkbox').find('input[type="checkbox"]').on('click', function(){
			var cb_id = $(this).data('id');
			var cb_name = $(this).data('name');
			var cb_price = $(this).data('price');
			var cb_order_num = $(this).data('order');

			if (cb_price) {
				if ($(this).attr("checked")) {
					add_element(cb_id, cb_name, cb_price, cb_order_num)
				} else {
					sub_element(cb_id, cb_price, cb_order_num);
				}
			}
		});

		// Step forward/backward
		$('#btn-step-forward').on('click',function(){
			stepForward();
		});
		$('#btn-step-back').on('click',function(){
			stepBack();
			return false;
		});
		$('#btn-guest-info').on('click',function(){
			$('#book_steps a:first').tab('show'); 

			return false;
		});

		// Toggle Payment Method
		$('.rezgo-payment-method').on('click',function(){
			toggleCard();
		});
	});
</script>