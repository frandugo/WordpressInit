<?php
	$trans_num = $site->decode(sanitize_text_field($_REQUEST['trans_num']));

	// send the user home if they shoulden't be here
	if(!$trans_num) $site->sendTo($site->base."/booking-not-found:empty");

	// unset promo session and cookie
	$site->resetPromoCode();

	// start a session so we can grab the analytics code
	session_start();

	$company = $site->getCompanyDetails();
?>

<div class="container-fluid rezgo-container">
	<div class="jumbotron rezgo-booking">
		<?php if (!$site->getBookings('q='.$trans_num)) { ?>
			<?php $site->sendTo($site->base."/booking-not-found:".sanitize_text_field($_REQUEST['trans_num'])); ?>
		<?php } ?>

		<?php foreach ($site->getBookings('q='.$trans_num) as $booking) { ?>
			<?php $item = $site->getTours('t=uid&q='.$booking->item_id, 0); ?>

			<?php $site->readItem($booking); ?>

			<div class="row">
				<ol class="breadcrumb rezgo-breadcrumb hidden-xs">
					<?php if ($site->exists($booking->order_code) && $site->getCartState()) { ?>
						<li>
							<a href="<?php echo $site->base; ?>/complete/<?php echo $site->encode($booking->order_code); ?>">Back to Order Summary</a>
						</li>
					<?php } ?>

					<li class="active">Booking Details</li>
				</ol>

				<?php if ($site->exists($booking->order_code) && $site->getCartState()) { ?>
					<h3 id="rezgo-back-to-summary-xs" class="hidden-sm hidden-md hidden-lg">
						<i class="fa fa-chevron-circle-left fa-lg"></i>
						<span>&nbsp;</span>
						<a href="<?php echo $site->base; ?>/complete/<?php echo $site->encode($booking->order_code); ?>">Back to Summary</a>
					</h3>
				<?php } ?>
			</div>

			<!-- // breadcrumb-->
			<div class="row rezgo-confirmation-head">
				<?php if ($booking->status == 1 || $booking->status == 4) { ?>
					<h3 class="rezgo-confirm-complete">
						<span>BOOKING COMPLETE</span>
					</h3>

					<p class="rezgo-confirm-complete">
						<span>Click on the button below for your printable</span>
						<span><?php echo ((string) $booking->ticket_type == 'ticket') ? ' ticket' : ' voucher' ?>.</span>
					</p>
				<?php } ?>

				<?php if ($booking->status == 2) { ?>
					<h3 class="rezgo-confirm-pending">
						<span>BOOKING NOT YET COMPLETE</span>
					</h3>

					<p class="rezgo-confirm-pending">
						<span>
							<?php if ($site->exists($booking->paypal_owed)) { ?>
								To complete your booking, make your payment by clicking on the button below.
								<br />
								AMOUNT PAYABLE NOW:
								<?php echo $site->formatCurrency($booking->paypal_owed); ?>
							<?php } else { ?>
								Your booking will be complete once payment has been processed.
							<?php } ?>
						</span>
					</p>
				<?php } ?>

				<?php if ($booking->status == 3) { ?>
					<h3 class="rezgo-confirm-cancelled">
						<span>This booking has been CANCELLED</span>
					</h3>
				<?php } ?>

				<?php if ($site->exists($booking->paypal_owed)) { ?>
					<div class="row">
						<center>
							<?php $company_paypal = $site->getCompanyPaypal(); ?>

							<form role="form" method="post" action="<?php echo REZGO_DIR; ?>/php_paypal/process.php">
								<input type="hidden" name="firstname" id="firstname" value="<?php echo $booking->first_name; ?>" />
								<input type="hidden" name="lastname" id="lastname" value="<?php echo $booking->last_name; ?>" />
								<input type="hidden" name="address1" id="address1" value="<?php echo $booking->address_1; ?>" />
								<input type="hidden" name="address2" id="address2" value="<?php echo $booking->address_2; ?>" />
								<input type="hidden" name="city" value="<?php echo $booking->city; ?>" />
								<input type="hidden" name="state" value="<?php echo $booking->stateprov; ?>" />
								<input type="hidden" name="country" value="<?php echo $site->countryName($booking->country); ?>" />
								<input type="hidden" name="zip" value="<?php echo $booking->postal_code; ?>" />
								<input type="hidden" name="email" id="email" value="<?php echo $booking->email_address; ?>" />
								<input type="hidden" name="phone" id="phone" value="<?php echo $booking->phone_number; ?>" />
								<input type="hidden" name="item_name" id="item_name" value="<?php echo $booking->tour_name; ?> - <?php echo $booking->option_name; ?>" />
								<input type="hidden" name="encoded_transaction_id" id="encoded_transaction_id" value="<?php echo sanitize_text_field($_REQUEST['trans_num']); ?>" />
								<input type="hidden" name="item_number" id="item_number" value="<?php echo $trans_num; ?>" />
								<input type="hidden" name="amount" id="amount" value="<?php echo $booking->paypal_owed; ?>" />
								<input type="hidden" name="quantity" id="quantity" value="1" />
								<input type="hidden" name="business" value="<?php echo $company_paypal; ?>" />
								<input type="hidden" name="currency_code" value="<?php echo $site->getBookingCurrency(); ?>" />
								<input type="hidden" name="domain" value="<?php echo $site->getDomain(); ?>.rezgo.com" />
								<input type="hidden" name="cid" value="<?php echo REZGO_CID; ?>" />
								<input type="hidden" name="paypal_signature" value="" />
								<input type="hidden" name="base_url" value="rezgo.com" />
								<input type="hidden" name="cancel_return" value="http://<?php echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>" />
								<input type="image" class="paypal_button" name="submit_image" src="<?php echo $site->path; ?>/img/logos/paypal_pay.png" />
							</form>
						</center>
					</div>
				<?php } ?>

				<div class="center-block">
					<button class="btn btn-lg rezgo-btn-print" onclick="window.open('<?php echo $site->base; ?>/complete/<?php echo $site->encode($trans_num); ?>/print', '_blank'); return false;">
						<i class="fa fa-print fa-lg"></i>
						<span>&nbsp;Print Receipt</span>
					</button>

					<?php if ($booking->status == 1 || $booking->status == 4) { ?>
						<a href="<?php echo $site->base; ?>/tickets/<?php echo $site->encode($booking->trans_num); ?>" class="btn btn-lg rezgo-btn-print-voucher" target="_blank">
							<i class="fa fa-print fa-lg"></i>
							<span>Print </span>
							<span><?php echo ((string) $booking->ticket_type == 'ticket') ? 'Tickets' : 'Voucher'; ?></span>
						</a>
					<?php } ?>
				</div>
			</div>

			<div class="row rezgo-form-group rezgo-confirmation-detail">
				<div class="col-sm-12">
					<h3 class="rezgo-book-name">
						<span><?php echo $booking->tour_name; ?> - <?php echo $booking->option_name; ?></span>

						<?php if ((string) $booking->date != 'open') { ?>
							<div class="rezgo-add-cal">
								<div class="rezgo-add-cal-cell">
									<a href="https://feed.rezgo.com/b/<?php echo $booking->trans_num; ?>">
										<i class="fa fa-calendar"></i>
										<span>&nbsp;Add to my calendar</span>
									</a>
								</div>
							</div>
						<?php } ?>
					</h3>

					<small class="rezgo-booked-on">
						<span>booked on </span>
						<span><?php echo date((string) $company->date_format, (int) $booking->date_purchased_local); ?></span>
						<span> / local time</span>
					</small>

					<table class="table table-responsive table-bordered table-striped rezgo-billing-cart">
						<tr>
							<td class="text-right"><label>Type</label></td>
							<td class="text-right"><label class="hidden-xs">Qty.</label></td>
							<td class="text-right"><label>Cost</label></td>
							<td class="text-right"><label>Total</label></td>
						</tr>

						<?php foreach ($site->getBookingPrices() as $price) { ?>
							<tr>
								<td class="text-right">
									<?php echo $price->label; ?></td>
								<td class="text-right">
									<?php echo $price->number; ?></td>
								<td class="text-right">
									<?php if ($site->exists($price->base)) { ?>
										<span class="discount"><?php echo $site->formatCurrency($price->base); ?></span>
									<?php } ?>
									<span>&nbsp;<?php echo $site->formatCurrency($price->price); ?></span>
								</td>
								<td class="text-right">
									<span><?php echo $site->formatCurrency($price->total); ?></span>
								</td>
							</tr>
						<?php } ?>

							<tr>
								<td colspan="3" class="text-right"><strong>Sub-total</strong></td>
								<td class="text-right"><?php echo $site->formatCurrency($booking->sub_total); ?></td>
							</tr>

						<?php foreach ($site->getBookingLineItems() as $line) { ?>
							<?php unset($label_add); ?>

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

							<?php if ($site->exists($line->amount)) { ?>
								<tr>
									<td colspan="3" class="text-right"><strong><?php echo $line->label; ?><?php echo $label_add; ?></strong></td>
									<td class="text-right"><?php echo $site->formatCurrency($line->amount); ?></td>
								</tr>
							<?php } ?>
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
				</div>

				<div class="col-sm-12">
					<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
						<tr id="rezgo-receipt-transnum">
							<td class="rezgo-td-label">Transaction&nbsp;#:</td>
							<td class="rezgo-td-data"><?php echo $booking->trans_num; ?></td>
						</tr>

						<?php if ((string) $booking->date != 'open') { ?>
							<tr id="rezgo-receipt-booked-for">
								<td class="rezgo-td-label"><span>Date:</span></td>
								<td class="rezgo-td-data">
									<span><?php echo date((string) $company->date_format, (int) $booking->date); ?></span>
									<?php if ($booking->time != '') { ?>
										<span> at <?php echo $booking->time; ?></span>
									<?php } ?>
								</td>
							</tr>
						<?php } else { ?>
							<?php if ($booking->time) { ?>
								<tr id="rezgo-receipt-booked-for">
									<td class="rezgo-td-label"><span>Time:</span></td>
									<td class="rezgo-td-data"><span><?php echo $booking->time; ?></span></td>
								</tr>
							<?php } ?>
						<?php } ?>

						<?php if (isset($booking->expiry)) { ?>
							<tr>
								<td class="rezgo-td-label">Expires:</td>
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
							<td class="rezgo-td-label">Duration:</td>
							<td class="rezgo-td-data"><?php echo $item->duration; ?></td>
						</tr>

						<tr id="rezgo-receipt-location">
							<td class="rezgo-td-label">Location:</td>
							<td class="rezgo-td-data">
								<?php
								if ($item->location_name != '') {
									echo $item->location_name . ', ' . $item->location_address;
								} else {
									unset($loc);
									if ($site->exists($item->city)) $loc[] = $item->city;
									if ($site->exists($item->state)) $loc[] = $item->state;
									if ($site->exists($item->country)) $loc[] = $site->countryName($item->country);
									if ($loc) echo implode(', ', $loc);
								}
								?>
							</td>
						</tr>

						<tr id="rezgo-receipt-pickup">
							<td class="rezgo-td-label"><span>Pickup/Departure Information:</span></td>
							<td class="rezgo-td-data"><?php echo $item->details->pick_up; ?></td>
						</tr>

						<tr id="rezgo-receipt-dropoff">
							<td class="rezgo-td-label">Dropoff/Return Information:</td>
							<td class="rezgo-td-data"><?php echo $item->details->drop_off; ?></td>
						</tr>

						<tr id="rezgo-receipt-thingstobring">
							<td class="rezgo-td-label">Things to bring:</td>
							<td class="rezgo-td-data"><?php echo $item->details->bring; ?></td>
						</tr>

						<tr class="rezgo-receipt-itinerary">
							<td colspan="2"><strong>Itinerary:</strong></td>
						</tr>

						<tr class="rezgo-receipt-itinerary">
							<td colspan="2" class="rezgo-td-data"><?php echo $item->details->itinerary; ?></td>
						</tr>
					</table>
				</div>
			</div>

			<?php if ($item->lat != '' && $item->lon != '') { ?>
				<script>
					var markersArray = [];
					var map;
					var lat = <?php echo $item->lat; ?>;
					var lon = <?php echo $item->lon; ?>;
					var zoom = <?php echo (($item->zoom != '' && $item->zoom > 0) ? $item->zoom : 6); ?>;
					var myCenter = new google.maps.LatLng(lat, lon);

					function initialize() {
						var mapProp = {
							center: myCenter,
							zoom: zoom,
							scrollwheel: false,
							<?php if ($site->config('REZGO_MOBILE_XML')) { ?>
								draggable: false,
							<?php } ?>
							mapTypeControl: false,
							sensor: true,
							mapTypeId:google.maps.MapTypeId.<?php echo $item->map_type; ?>
						};

						map = new google.maps.Map(document.getElementById("rezgo-receipt-map"), mapProp);

						google.maps.event.addListener(map, 'zoom_changed', function() {
							document.getElementById("zoom").value = map.getZoom();
						});

						var marker = new google.maps.Marker({
							position: new google.maps.LatLng(<?php echo $item->lat; ?>, <?php echo $item->lon; ?>),
							map: map
						});

						markersArray.push(marker);
					}

					function clearOverlays() {
						for (var i = 0; i < markersArray.length; i++ ) {
							markersArray[i].setMap(null);
						}
					}

					google.maps.event.addDomListener(window, 'load', initialize);
				</script>	

				<div class="row" id="rezgo-receipt-map-container">
					<div class="col-xs-12">
						<h3 id="rezgo-receipt-head-map"><span>Map</span></h3>

						<div id="rezgo-receipt-map"></div>

						<div class="rezgo-map-location rezgo-map-shadow">
							<?php if ($item->location_name) { ?>
								<div class="rezgo-map-icon pull-left">
									<i class="fa fa-comment"></i>
								</div> 

								<span> <?php echo $item->location_name; ?></span>

								<div class="rezgo-map-hr"></div>
							<?php } ?>

							<?php if ($item->location_address) { ?>
								<div class="rezgo-map-icon pull-left">
									<i class="fa fa-location-arrow"></i>
								</div> 

								<span> <?php echo $item->location_address; ?></span>

								<div class="rezgo-map-hr"></div>
							<?php } else { ?>
								<div class="rezgo-map-icon pull-left">
									<i class="fa fa-location-arrow"></i>
								</div>

								<span> <?php echo $item->city.' '.$item->state.' '.$a->countryFormat($item->country); ?></span>

								<div class="rezgo-map-hr"></div>
							<?php } ?>

							<div class="rezgo-map-icon pull-left">
								<i class="fa fa-globe"></i>
							</div>

							<span> <?php echo round((float) $item->lat, 3); ?>, <?php echo round((float) $item->lon, 3); ?></span>
						</div>
					</div>
				</div>
			<?php } ?>

			<div class="row rezgo-form-group rezgo-confirmation-additional-info">
				<div class="col-md-7 col-xs-12">
					<h3 id="rezgo-receipt-head-billing-info"><span>Billing Information</span></h3>

					<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
						<tr id="rezgo-receipt-name">
							<td class="rezgo-td-label">Name</td>
							<td class="rezgo-td-data"><?php echo $booking->first_name; ?> <?php echo $booking->last_name; ?></td>
						</tr>

						<tr id="rezgo-receipt-address">
							<td class="rezgo-td-label">Address</td>
							<td class="rezgo-td-data">
								<?php echo $booking->address_1; ?><?php if ($site->exists($booking->address_2)) { ?>, <?php echo $booking->address_2; ?><?php } ?><?php if ($site->exists($booking->city)) { ?>, <?php echo $booking->city; ?><?php } ?><?php if ($site->exists($booking->stateprov)) { ?>, <?php echo $booking->stateprov; ?><?php } ?><?php if ($site->exists($booking->postal_code)) { ?>, <?php echo $booking->postal_code; ?><?php } ?>, <?php echo $site->countryName($booking->country); ?>
							</td>
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
								<td class="rezgo-td-data"><?php echo $booking->payment_method; ?></td>
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
							<td class="rezgo-td-data">
								<?php echo (($booking->status == 1) ? 'CONFIRMED' : ''); ?>
								<?php echo (($booking->status == 2) ? 'PENDING' : ''); ?>
								<?php echo (($booking->status == 3) ? 'CANCELLED' : ''); ?>
							</td>
						</tr>

						<?php if ($site->exists($booking->trigger_code)) { ?>
							<tr id="rezgo-receipt-trigger">
								<td class="rezgo-td-label rezgo-promo-label">Promotional Code</td>
								<td class="rezgo-td-data"><?php echo $booking->trigger_code; ?></td>
							</tr>
						<?php } ?>
					</table>

					<?php if (count($site->getBookingForms()) > 0 OR count($site->getBookingPassengers()) > 0) { ?>
						<h4 id="rezgo-receipt-head-customer-info"><span>Customer Information</span></h4>

						<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
							<?php foreach ($site->getBookingForms() as $form) { ?>
								<?php if(in_array($form->type, array('checkbox','checkbox_price'))) { 
									if($site->exists($form->answer)) { $form->answer = 'yes'; } else { $form->answer = 'no'; }
								} ?>

								<tr class="rezgo-receipt-primary-forms">
									<td class="rezgo-td-label"><?php echo $form->question; ?></td>
									<td class="rezgo-td-data"><?php echo $form->answer; ?>&nbsp;</td>
								</tr>
							<?php } ?>

							<?php foreach ($site->getBookingPassengers() as $passenger) { ?>
								<tr class="rezgo-receipt-pax">
									<td class="rezgo-td-label"><?php echo $passenger->label; ?></td>
									<td class="rezgo-td-data">( <?php echo $passenger->num; ?> )&nbsp;</td>
								</tr>

								<tr class="rezgo-receipt-pax">
									<td class="rezgo-td-label">Name</td>
									<td class="rezgo-td-data"><?php echo $passenger->first_name; ?> <?php echo $passenger->last_name; ?>&nbsp;</td>
								</tr>

								<?php if ((string) $passenger->phone_number != '') { ?>
									<tr class="rezgo-receipt-pax-phone">
										<td class="rezgo-td-label">Phone Number</td>
										<td class="rezgo-td-data"><?php echo $passenger->phone_number; ?>&nbsp;</td>
									</tr>
								<?php } ?>

								<?php if ((string) $passenger->email_address != '') { ?>
									<tr class="rezgo-receipt-pax-email">
										<td class="rezgo-td-label">Email</td>
										<td class="rezgo-td-data"><?php echo $passenger->email_address; ?>&nbsp;</td>
									</tr>
								<?php } ?>

								<?php foreach ($passenger->forms->form as $form) { ?>
									<?php if(in_array($form->type, array('checkbox','checkbox_price'))) { ?>
										<?php if($site->exists($form->answer)) { $form->answer = 'yes'; } else { $form->answer = 'no'; } ?>
									<?php } ?>

									<tr class="rezgo-receipt-guest-forms">
										<td class="rezgo-td-label"><?php echo $form->question; ?></td>
										<td class="rezgo-td-data"><?php echo $form->answer; ?>&nbsp;</td>
									</tr>
								<?php } ?>
							<?php } ?>
						</table>
					<?php } ?>
				</div>

				<div class="col-md-5 col-xs-12 rezgo-company-info">
					<h3 id="rezgo-receipt-head-cancel"><span>Cancellation Policy</span></h3>

					<p>
						<?php if ($site->exists($booking->rezgo_gateway)) { ?>
							Canceling a booking with Rezgo can result in cancellation fees being applied by Rezgo, as outlined below. Additional fees may be levied by the individual supplier/operator (see your Rezgo <?php echo ((string) $booking->ticket_type == 'ticket') ? 'Ticket' : 'Voucher' ?> for specific details). When canceling any booking you will be notified via email, facsimile or telephone of the total cancellation fees.
							<br />
							<br />
							1. Event, Attraction, Theater, Show or Coupon Ticket
							<br />
							These are non-refundable in all circumstances.
							<br />
							<br />
							2. Gift Certificate
							<br />
							These are non-refundable in all circumstances.
							<br />
							<br />
							3. Tour or Package Commencing During a Special Event Period
							<br />
							These are non-refundable in all circumstances. This includes, but is not limited to, Trade Fairs, Public or National Holidays, School Holidays, New Year's, Thanksgiving, Christmas, Easter, Ramadan.
							<br />
							<br />
							4. Other Tour Products & Services
							<br />
							If you cancel at least 7 calendar days in advance of the scheduled departure or commencement time, there is no cancellation fee.
							<br />
							If you cancel between 3 and 6 calendar days in advance of the scheduled departure or commencement time, you will be charged a 50% cancellation fee.
							<br />
							If you cancel within 2 calendar days of the scheduled departure or commencement time, you will be charged a 100% cancellation fee.
							<br />
							<br />
						<?php } else { ?>
							<?php if ($site->exists($item->details->cancellation)) { ?>
								<?php echo $item->details->cancellation; ?>
								<br />
								<br />
							<?php } ?>
						<?php } ?>

						<a href="javascript:void(0);" onclick="javascript:window.open('<?php echo $site->base; ?>/terms',null,'width=800,height=600,status=no,toolbar=no,menubar=no,location=no,scrollbars=1');">Click here to view terms and conditions.</a>
					</p>

					<?php if ($site->exists($booking->rid)) { ?>
						<h3 id="rezgo-receipt-head-customer-service"><span>Customer Service</span></h3>

						<p>
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
								<?php echo $company->company_name; ?>
								<br />
								<?php echo $company->address_1; ?>
								<?php echo $company->address_2; ?>
								<br />
								<?php echo $company->city; ?>
								,
								<?php if ($site->exists($company->state_prov)) { ?>
									<?php echo $company->state_prov; ?>
									,
								<?php } ?>
								<?php echo $site->countryName($company->country); ?>
								<br />
								<?php echo $company->postal_code; ?>
								<br />
								<?php echo $company->phone; ?>
								<br />
								<?php echo $company->email; ?>
								<?php if ($site->exists($company->tax_id)) { ?>
									<br />
									<br />
									<?php echo $company->tax_id; ?>
								<?php } ?>
							<?php } ?>
						</p>
					<?php } ?>

					<h3 id="rezgo-receipt-head-provided-by">
						<span>Service Provided by</span>
					</h3>

					<address>
						<?php $company = $site->getCompanyDetails($booking->cid); ?>
						<strong><?php echo $company->company_name; ?></strong><br />
						<?php echo $company->address_1; ?><?php if ($site->exists($company->address_2)) { ?>, <?php echo $company->address_2; ?><?php } ?>
						<br />
						<?php echo $company->city; ?>,
						<?php if ($site->exists($company->state_prov)) { ?>
							<?php echo $company->state_prov; ?>, 
						<?php } ?>
						<?php echo $site->countryName($company->country); ?><br />
						<?php echo $company->postal_code; ?><br />
						<?php echo $company->phone; ?><br />
						<?php echo $company->email; ?>
						<?php if ($site->exists($company->tax_id)) { ?><br />Tax ID: <?php echo $company->tax_id; ?><?php } ?>
					</address>
				</div>
			</div>
		<?php } ?>
	</div>
</div>

<?php if ($_SESSION['REZGO_CONVERSION_ANALYTICS']) { ?>
	<?php echo $_SESSION['REZGO_CONVERSION_ANALYTICS']; ?>

	<?php unset($_SESSION['REZGO_CONVERSION_ANALYTICS']); ?>
<?php } ?>
