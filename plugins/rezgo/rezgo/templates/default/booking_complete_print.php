<?php
	// grab and decode the trans_num if it was set
	$trans_num = $site->decode(sanitize_text_field($_REQUEST['trans_num']));

	// send the user 404 if they shoulden't be here
	if (!$trans_num) {
		$site->sendTo("/".$current_wp_page."/booking-not-found");
	}

	$company = $site->getCompanyDetails();

	$rzg_payment_method = 'None';
?>

<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="robots" content="noindex, nofollow">
		<title>Booking - <?php echo $trans_num; ?></title>

		<?php
		rezgo_plugin_scripts_and_styles();
		wp_print_scripts();
		wp_print_styles();
		?>

		<?php if ($site->exists($site->getStyles())) { ?>
			<style>
				<?php echo $site->getStyles();?>
			</style>
		<?php } ?>
	</head>

	<body>
		<div class="container-fluid rezgo-container">
			<?php if (!$site->getBookings('q='.$trans_num)) {
				$site->sendTo("/tour"); 
			} ?>

			<?php foreach ($site->getBookings('q='.$trans_num) as $booking) { ?>
				<?php $item = $site->getTours('t=uid&q='.$booking->item_id, 0); ?>

				<?php $site->readItem($booking) ?>

				<div class="rezgo-content-row">
					<h2 id="rezgo-receipt-head-your-booking">
						<span>Your Booking (booked on </span>
						<span><?php echo date((string) $company->date_format, (int) $booking->date_purchased_local); ?></span>
						<span> / local time)</span>
					</h2>

					<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
						<tr id="rezgo-receipt-transnum">
							<td class="rezgo-td-label"><span>Transaction #</span></td>
							<td class="rezgo-td-data"><?php echo $booking->trans_num; ?></td>
						</tr>

						<tr id="rezgo-receipt-have-booked">
							<td class="rezgo-td-label"><span>You have booked</span></td>
							<td class="rezgo-td-data"><?php echo $booking->tour_name; ?> &mdash; <?php echo $booking->option_name; ?></td>
						</tr>

						<?php if ((string) $booking->date != 'open') { ?>
							<tr id="rezgo-receipt-booked-for">
								<td class="rezgo-td-label"><span>Booked For</span></td>
								<td class="rezgo-td-data">
									<?php echo date((string) $company->date_format, (int) $booking->date)?>
									<?php if ($booking->time != '') { ?><span> at <?php echo $booking->time?></span><?php } ?>
								</td>
							</tr>
						<?php } else { ?>
							<?php if ($booking->time) { ?>
								<tr id="rezgo-receipt-booked-for">
									<td class="rezgo-td-label"><span>Time</span></td>
									<td class="rezgo-td-data"><span><?php echo $booking->time; ?></span></td>
								</tr>
							<?php } ?>
						<?php } ?>

						<?php if (isset($booking->expiry)) { ?>
							<tr>
								<td class="rezgo-td-label">Expires</td>
								<td class="rezgo-td-data">
									<?php if ((int) $booking->expiry !== 0) { ?>
										<span><?php echo date((string) $company->date_format, (int) $booking->expiry); ?></span>
									<?php } else { ?>
										<span>Never</span>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>

						<tr id="rezgo-receipt-duration">
							<td class="rezgo-td-label"><span>Duration</span></td>
							<td class="rezgo-td-data"><?php echo $item->duration; ?></td>
						</tr>

						<tr id="rezgo-receipt-location">
							<td class="rezgo-td-label"><span>Location</span></td>
							<td class="rezgo-td-data">
								<?php if ($item->location_name != '') {
									echo $item->location_name . ', ' . $item->location_address;
								} else {
									unset($loc);
									if ($site->exists($item->city)) $loc[] = $item->city;
									if ($site->exists($item->state)) $loc[] = $item->state;
									if ($site->exists($item->country)) $loc[] = $site->countryName($item->country);
									if ($loc) echo implode(', ', $loc);
								} ?>
							</td>
						</tr>

						<tr id="rezgo-receipt-pickup">
							<td class="rezgo-td-label"><span>Pickup/Departure Information</span></td>
							<td class="rezgo-td-data"><?php echo $item->details->pick_up; ?></td>
						</tr>

						<tr id="rezgo-receipt-dropoff">
							<td class="rezgo-td-label"><span>Drop Off/Return Information</span></td>
							<td class="rezgo-td-data"><?php echo $item->details->drop_off; ?></td>
						</tr>

						<tr id="rezgo-receipt-thingstobring">
							<td class="rezgo-td-label"><span>Things to bring</span></td>
							<td class="rezgo-td-data"><?php echo $item->details->bring; ?></td>
						</tr>

						<tr id="rezgo-receipt-itinerary">
							<td class="rezgo-td-label"><span>Itinerary</span></td>
							<td class="rezgo-td-data"><?php echo $item->details->itinerary; ?></td>
						</tr>
					</table>
				</div>

				<?php if ($item->lat != '' && $item->lon != '') { ?>
					<?php if ($item->map_type == 'ROADMAP') {
						$embed_type = 'roadmap'; 
					} else {
						$embed_type = 'satellite';
					} ?>

					<div style="page-break-after:always;"></div>

					<div class="row" id="rezgo-receipt-map-container">
						<div class="col-xs-12">
							<h3 id="rezgo-receipt-head-map"><span>Map</span></h3>

							<?php if ($item->location_name) { ?>
								<div id="rezgo-receipt-map-location">
									<strong><?php echo $item->location_name; ?></strong>

									<br />

									<span><?php echo $item->location_address; ?></span>
								</div>
							<?php } ?>

							<div id="rezgo-receipt-map">
								<?php 
								$mapSrc	= 'https://www.google.com/maps/embed/v1/view?';
								$mapSrc .= 'key=AIzaSyCqFNdI5b319sgzE3WH3Bw97fBl4kRVzWw';
								$mapSrc .= '&maptype=' . $embed_type;
								$mapSrc .= '&center=' . $item->lat.','.$item->lon;
								$mapSrc .= '&zoom=' . (($item->zoom != '' && $item->zoom > 0) ? $item->zoom : '6');
								?>

								<iframe 
								width="100%" 
								height="390" 
								frameborder="0" 
								style="border:0;margin-bottom:0;pointer-events:none;" 
								src="<?php echo $mapSrc; ?>"
								></iframe>
							</div>
						</div>
					</div>	
				<?php } ?>

				<div style="page-break-after:always;"></div>

				<?php if ($booking->payment_method != 'None') {
					$rzg_payment_method = (string) $booking->payment_method;
				} ?>

				<div class="rezgo-content-row" id="rezgo-receipt-payment-info">
					<h2 id="rezgo-receipt-head-payment-info"><span>Payment Information</span></h2>

					<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
						<tr id="rezgo-receipt-name">
							<td class="rezgo-td-label">Name</td>
							<td class="rezgo-td-data"><?php echo $booking->first_name; ?> <?php echo $booking->last_name; ?></td>
						</tr>

						<tr id="rezgo-receipt-address">
							<td class="rezgo-td-label">Address</td>
							<td class="rezgo-td-data"><?php echo $booking->address_1; ?><?php if ($site->exists($booking->address_2)) { ?>, <?php echo $booking->address_2; ?><?php } ?><?php if ($site->exists($booking->city)) { ?>, <?php echo $booking->city; ?><?php } ?><?php if ($site->exists($booking->stateprov)) { ?>, <?php echo $booking->stateprov; ?><?php } ?><?php if ($site->exists($booking->postal_code)) { ?>, <?php echo $booking->postal_code; ?><?php } ?>, <?php echo $site->countryName($booking->country); ?></td>
						</tr>

						<tr id="rezgo-receipt-phone">
							<td class="rezgo-td-label">Phone Number</td>
							<td class="rezgo-td-data"><?php echo $booking->phone_number; ?></td>
						</tr>

						<tr id="rezgo-receipt-email">
							<td class="rezgo-td-label">Email Address</td>
							<td class="rezgo-td-data"><?php echo $booking->email_address; ?></td>
						</tr>

						<?php if ($booking->overall_total > 0) { ?>
							<tr id="rezgo-receipt-payment-method">
								<td class="rezgo-td-label">Payment Method</td>
								<td class="rezgo-td-data"><?php echo $rzg_payment_method; ?></td>
							</tr>

							<?php if ($booking->payment_method == 'Credit Cards') { ?>
								<tr id="rezgo-receipt-cardnum">
									<td class="rezgo-td-label">Card Number</td>
									<td class="rezgo-td-data"><?php echo $booking->card_number; ?></td>
								</tr>
							<?php } ?>

							<?php if ($site->exists($booking->payment_method_add->label)) { ?>
								<tr>
									<td class="rezgo-td-label"><?php echo $booking->payment_method_add->label; ?></td>
									<td class="rezgo-td-data"><?php echo $booking->payment_method_add->value; ?></td>
								</tr>
							<?php } ?>
						<?php } ?>

						<tr id="rezgo-receipt-payment-status">
							<td class="rezgo-td-label">Payment Status</td>
							<td class="rezgo-td-data"><?php echo (($booking->status == 1) ? 'CONFIRMED' : ''); ?><?php echo (($booking->status == 2) ? 'PENDING' : ''); ?><?php echo (($booking->status == 3) ? 'CANCELLED' : ''); ?></td>
						</tr>

						<?php if ($site->exists($booking->trigger_code)) { ?>
							<tr id="rezgo-receipt-trigger">
								<td class="rezgo-td-label" class="rezgo-promo-label"><span>Promotional Code</span></td>
								<td class="rezgo-td-data"><?php echo $booking->trigger_code; ?></td>
							</tr>
						<?php } ?>

						<tr id="rezgo-receipt-charges">
							<td class="rezgo-td-label">Charges</td>
							<td class="rezgo-td-data">
								<table class="table table-responsive table-bordered table-striped rezgo-billing-cart">
									<tr>
										<td class="text-right"><label>Type</label></td>
										<td class="text-right"><label class="hidden-xs">Qty.</label></td>
										<td class="text-right"><label>Cost</label></td>
										<td class="text-right"><label>Total</label></td>
									</tr>

									<?php foreach ($site->getBookingPrices() as $price) { ?>
										<tr>
											<td class="text-right"><?php echo $price->label; ?></td>
											<td class="text-right"><?php echo $price->number; ?></td>
											<td class="text-right">
												<?php if ($site->exists($price->base)) { ?>
													<span class="discount"><?php echo $site->formatCurrency($price->base); ?></span>
												<?php } ?>
												<span>&nbsp;<?php echo $site->formatCurrency($price->price); ?></span>
											</td>
											<td class="text-right"><?php echo $site->formatCurrency($price->total); ?></td>
										</tr>
									<?php } ?>

									<tr>
										<td colspan="3" class="text-right"><strong>Sub-total</strong></td>
										<td class="text-right"><?php echo $site->formatCurrency($booking->sub_total); ?></td>
									</tr>

									<?php foreach ($site->getBookingLineItems() as $line) { ?>
										<?php unset ($label_add); ?>

										<?php if ($site->exists($line->percent) || $site->exists($line->multi)) {
											$label_add = ' (';

											if ($site->exists($line->percent)) {
												$label_add .= $line->percent.'%';
											}

											if ($site->exists($line->multi)) {
												if (!$site->exists($line->percent)) {
													$label_add .= $site->formatCurrency($line->multi);
												}

												$label_add .= ' x '.$booking->pax;
											}

											$label_add .= ')'; 
										} ?>

										<tr>
											<td colspan="3" class="text-right"><strong><?php echo $line->label; ?><?php echo $label_add; ?></strong></td>
											<td class="text-right"><?php echo $site->formatCurrency($line->amount); ?></td>
										</tr>
									<?php } ?>

									<?php foreach ($site->getBookingFees() as $fee) { ?>
										<?php if ($site->exists($fee->total_amount)) { ?>
											<tr>
												<td colspan="3" class="text-right"><strong><?php echo $fee->label; ?></strong></td>
												<td class="text-right"><?php echo $site->formatCurrency($fee->total_amount); ?></td>
											</tr>
										<?php } ?>
									<?php } ?>

									<tr>
										<td colspan="3" class="text-right"><strong>Total</strong></td>
										<td class="text-right">
											<strong><?php echo $site->formatCurrency($booking->overall_total); ?></strong>
										</td>
									</tr>

									<?php if ($site->exists($booking->deposit)) { ?>
										<tr>
											<td colspan="3" class="text-right"><strong>Deposit</strong></td>
											<td class="text-right">
												<strong><?php echo $site->formatCurrency($booking->deposit); ?></strong>
											</td>
										</tr>
									<?php } ?>

									<?php if ($site->exists($booking->overall_paid)) { ?>
										<tr>
											<td colspan="3" class="text-right"><strong>Total Paid</strong></td>
											<td class="text-right">
												<strong><?php echo $site->formatCurrency($booking->overall_paid); ?></strong>
											</td>
										</tr>

										<tr>
											<td colspan="3" class="text-right"><strong>Total&nbsp;Owing</strong></td>
											<td class="text-right">
												<strong><?php echo $site->formatCurrency(((float)$booking->overall_total - (float)$booking->overall_paid)); ?></strong>
											</td>
										</tr>
									<?php } ?>
								</table>
							</td>
						</tr>
					</table>
				</div>

				<?php if (count($site->getBookingForms()) > 0 || count($site->getBookingPassengers()) > 0) { ?>
					<div class="rezgo-content-row" id="rezgo-receipt-guest-info">
						<h2 id="rezgo-receipt-head-guest-info"><span>Guest Information</span></h2>

						<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
							<?php foreach ($site->getBookingForms() as $form) { ?>
								<?php if(in_array($form->type, array('checkbox','checkbox_price'))) { ?>
									<?php if($site->exists($form->answer)) { $form->answer = 'yes'; } else { $form->answer = 'no'; } ?>
								<?php } ?>

								<tr class="rezgo-receipt-primary-forms">
									<td class="rezgo-td-label"><?php echo $form->question; ?>:</td>
									<td class="rezgo-td-data"><?php echo $form->answer; ?></td>
								</tr>
							<?php } ?>

							<?php foreach ($site->getBookingPassengers() as $passenger) { ?>
								<tr class="rezgo-receipt-pax">
									<td class="rezgo-td-label"><?php echo $passenger->label; ?> <?php echo $passenger->num; ?>:</td>
									<td class="rezgo-td-data"><?php echo $passenger->first_name; ?> <?php echo $passenger->last_name; ?></td>
								</tr>

								<?php if ((string) $passenger->phone_number != '') { ?>
									<tr class="rezgo-receipt-pax-phone">
										<td class="rezgo-td-label">Phone Number:</td>
										<td class="rezgo-td-data"><?php echo $passenger->phone_number; ?></td>
									</tr>
								<?php } ?>

								<?php if ((string) $passenger->email_address != '') { ?>
									<tr class="rezgo-receipt-pax-email">
										<td class="rezgo-td-label">Email:</td>
										<td class="rezgo-td-data"><?php echo $passenger->email_address; ?></td>
									</tr>
								<?php } ?>

								<?php foreach ($passenger->forms->form as $form) { ?>
									<?php if(in_array($form->type, array('checkbox','checkbox_price'))) { ?>
										<?php if($site->exists($form->answer)) { $form->answer = 'yes'; } else { $form->answer = 'no'; } ?>
									<?php } ?>

									<tr class="rezgo-receipt-guest-forms">
										<td class="rezgo-td-label"><?php echo $form->question; ?>:</td>
										<td class="rezgo-td-data"><?php echo $form->answer; ?></td>
									</tr>
								<?php } ?>

								<tr>
									<td class="rezgo-td-label">&nbsp;</td>
									<td class="rezgo-td-data">&nbsp;</td>
								</tr>
							<?php } ?>
						</table>
					</div>
				<?php } ?>

				<div class="rezgo-content-row" id="rezgo-receipt-customer-service-section">
					<h2 id="rezgo-receipt-head-customer-service"><span>Customer Service</span></h2>

					<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
						<tr id="rezgo-receipt-cancel">
							<td class="rezgo-td-label"><span>Cancellation Policy</span></td>
							<td class="rezgo-td-data">
								<?php if ($site->exists($booking->rezgo_gateway)) { ?>
									Canceling a booking with Rezgo can result in cancellation fees being applied by Rezgo, as outlined below. Additional fees may be levied by the individual supplier/operator (see your Rezgo <?php echo ((string) $booking->ticket_type == 'ticket') ? 'Ticket' : 'Voucher'; ?> for specific details). When canceling any booking you will be notified via email, facsimile or telephone of the total cancellation fees.<br />
									<br />
									1. Event, Attraction, Theater, Show or Coupon Ticket<br />
									These are non-refundable in all circumstances.<br />
									<br />
									2. Gift Certificate<br />
									These are non-refundable in all circumstances.<br />
									<br />
									3. Tour or Package Commencing During a Special Event Period<br />
									These are non-refundable in all circumstances. This includes, but is not limited to, Trade Fairs, Public or National Holidays, School Holidays, New Year's, Thanksgiving, Christmas, Easter, Ramadan.<br />
									<br />
									4. Other Tour Products & Services<br />
									If you cancel at least 7 calendar days in advance of the scheduled departure or commencement time, there is no cancellation fee.<br />
									If you cancel between 3 and 6 calendar days in advance of the scheduled departure or commencement time, you will be charged a 50% cancellation fee.<br />
									If you cancel within 2 calendar days of the scheduled departure or commencement time, you will be charged a 100% cancellation fee.
									<br />
								<?php } else { ?>
									<?php if ($site->exists($item->details->cancellation)) { ?>
										<?php echo $item->details->cancellation; ?>
										<br />
									<?php } ?>
								<?php } ?>

								View terms and conditions: <strong>http://<?php echo $site->getDomain(); ?>.rezgo.com/terms</strong>
							</td>
						</tr>

						<?php if ($site->exists($booking->rid)) { ?>
							<tr id="rezgo-receipt-customer-service">
								<td class="rezgo-td-label"><span>Customer Service</span></td>
								<td class="rezgo-td-data">
									<?php if ($site->exists($booking->rezgo_gateway)) { ?>
										Rezgo.com<br />
										Attn: Partner Bookings<br />
										333 Brooksbank Avenue<br />
										Suite 718<br />
										North Vancouver, BC<br />
										Canada V7J 3V8<br />
										(604) 983-0083<br />
										bookings@rezgo.com
									<?php } else { ?>
										<?php $company = $site->getCompanyDetails('p'.$booking->rid); ?>
										<?php echo $company->company_name; ?><br />
										<?php echo $company->address_1; ?> <?php echo $company->address_2; ?><br />
										<?php echo $company->city; ?>, <?php if ($site->exists($company->state_prov)) { ?><?php echo $company->state_prov; ?>, <?php } ?><?php echo $site->countryName($company->country); ?><br />
										<?php echo $company->postal_code; ?><br />
										<?php echo $company->phone; ?><br />
										<?php echo $company->email; ?>
										<?php if ($site->exists($company->tax_id)) { ?>
											<br />
											<br />
											<?php echo $company->tax_id; ?>
										<?php } ?>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>

						<tr id="rezgo-receipt-provided-by">
							<td class="rezgo-td-label"><span>Service Provided By</span></td>
							<td class="rezgo-td-data">
								<?php $company = $site->getCompanyDetails($booking->cid); ?>
								<?php echo $company->company_name; ?><br />
								<?php echo $company->address_1; ?> <?php echo $company->address_2; ?><br />
								<?php echo $company->city; ?>, <?php if($site->exists($company->state_prov)) { ?><?php echo $company->state_prov; ?>, <?php } ?><?php echo $site->countryName($company->country); ?><br />
								<?php echo $company->postal_code; ?><br />
								<?php echo $company->phone; ?><br />
								<?php echo $company->email; ?>
								<?php if ($site->exists($company->tax_id)) { ?>
									<br />
									Tax ID: <?php echo $company->tax_id; ?>
								<?php } ?>
							</td>
						</tr>
					</table>
				</div>
			<?php } ?>
		</div>

		<script>
			var title = parent.document.title;
			parent.document.title = title + ' - <?php echo $booking->tour_name; ?>';
		</script>
	</body>
</html>