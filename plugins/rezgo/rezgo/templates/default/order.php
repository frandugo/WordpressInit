<?php 
$company = $site->getCompanyDetails();

$date_types = array('always', 'range', 'week', 'days', 'single'); 
?>

<div class="container-fluid rezgo-container">
	<div class="jumbotron rezgo-booking">
		<div class="row hidden-xs">
			<ol class="breadcrumb rezgo-breadcrumb">
				<li class="active">Your Order</li>
				<li>Guest Information</li>
				<li>Billing Information</li>
				<li>Confirmation</li>
			</ol>
		</div>

		<?php $cart = $site->getCart(); ?>

		<?php if (!$cart): ?>
			<div class="row rezgo-form-group cart_empty">
				<p class="lead">
					<span class="hidden-xs">There are </span>
					<span>&nbsp;<span class="hidden-xs">n</span><span class="visible-xs-inline">N</span>o items</span>
					<span class="hidden-xs">&nbsp;in your order.</span>
				</p>
			</div>

			<div class="row" id="rezgo-booking-btn">
				<div class="col-md-4 col-xs-12 rezgo-btn-wrp">
					<a href="<?php echo $site->base; ?>" class="btn rezgo-btn-default btn-lg btn-block">Book More</a>
				</div>
			</div>
		<?php else: ?>
			<?php $item_num = 0; ?>

			<?php foreach ($cart as $item) { ?>
				<?php $site->readItem($item); ?>

				<div class="row rezgo-form-group">
					<div class="col-lg-9 col-sm-8 col-xs-12 rezgo-cart-title">
						<h3>
							<a href="<?php echo $site->base; ?>/details/<?php echo $item->com; ?>/<?php echo $site->seoEncode($item->item); ?>">
								<?php echo $item->item; ?> &mdash; <?php echo $item->option; ?>
							</a>
						</h3>

						<?php if (in_array((string) $item->date_selection, $date_types)): ?>
							<?php $data_book_date = date("Y-m-d", (string)$item->booking_date); ?>

							<label>
								<span>Date: </span>
								<span class="lead"><?php echo date((string) $company->date_format, (string) $item->booking_date); ?></span>
							</label>
						<?php else: ?>
							<?php $data_book_date = date('Y-m-d', strtotime('+1 day')); ?>

							<label>Open Availability</label>
						<?php endif; ?>

						<?php if ($item->discount_rules->rule) {
							echo '<br><label>Discount: ';
							unset($discount_string);
							foreach($item->discount_rules->rule as $discount) {	
								$discount_string .= ($discount_string) ? ', '.$discount : $discount;
							}
							echo '<span class="rezgo-red">'.$discount_string.'</span>
							</label>';
						} ?>

						<div class="rezgo-order-memo rezgo-order-date-<?php echo date('Y-m-d', (string) $item->booking_date)?> rezgo-order-item-<?php echo $item->uid; ?>"></div>
					</div>

					<?php if ($site->getCartState()) { ?>
						<div class="col-lg-3 col-sm-4 col-xs-12">
							<div class="col-sm-12 rezgo-btn-cart-wrp">
								<button 
								type="button" 
								data-toggle="collapse" 
								class="btn rezgo-btn-default btn-block rezgo-pax-edit-btn" 
								data-order-item="<?php echo $item->uid; ?>" 
								data-order-com="<?php echo $item->com; ?>" 
								data-cart-id="<?php echo $item->cartID; ?>" 
								<?php if (in_array((string) $item->date_selection, $date_types)): ?>
									data-book-date="<?php echo date("Y-m-d", (string)$item->booking_date); ?>" 
								<?php else: ?>
									data-book-date="open"
								<?php endif; ?>
								data-target="#pax-edit-<?php echo $item->cartID; ?>"
								>Edit Guests</button>
							</div>

							<div class="col-sm-12 rezgo-btn-cart-wrp">
								<button 
								type="button" 
								class="btn rezgo-btn-remove btn-block" 
								onclick="top.location.href='<?php echo $site->base; ?>/order?edit[<?php echo $item->cartID; ?>][adult_num]=0';"
								>Remove from Order</button>
							</div>
						</div>
					<?php } ?>
				</div>

				<div class="row rezgo-form-group" id="rezgo-cart-pricing-<?php echo $item->uid; ?>">
					<div class="col-sm-12">
						<table class="table table-bordered table-striped rezgo-billing-cart table-responsive">
							<tr>
								<td class="text-right rezgo-billing-type"><label>Type</label></td>
								<td class="text-right rezgo-billing-qty"><label class="hidden-xs">Qty.</label></td>
								<td class="text-right rezgo-billing-cost"><label>Cost</label></td>
								<td class="text-right rezgo-billing-total"><label>Total</label></td>
							</tr>

							<?php foreach ($site->getTourPrices($item) as $price) { ?>
							 <?php if ($item->{$price->name.'_num'}) { ?>
									<tr>
										<td class="text-right"><?php echo $price->label; ?></td>

										<td class="text-right"><?php echo $item->{$price->name.'_num'}?></td>

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

							<?php if ((int) $item->availability < (int) $item->pax_count) { ?>
								<tr>
									<td colspan="4" class="rezgo-order-unavailable">
										<span 
										data-toggle="tooltip" 
										data-placement="top" 
										title="This item has become unavailable after it was added to your order">
											<i class="fa fa-exclamation-triangle"></i>
											<span> No Longer Available</span>
										</span>
									</td>
								</tr>
							<?php } else { $cart_total += (float) $item->sub_total; } ?>

							<tr>
								<td colspan="3" class="text-right"><strong>Sub-total</strong></td>
								<td class="text-right"><?php echo $site->formatCurrency($item->sub_total); ?></td>
							</tr>
						</table>

						<?php if ($site->getTourRelated()) { ?>
							<div class="rezgo-related">
								<div class="rezgo-related-label">
									<span>Related products</span>
								</div>

								<?php foreach($site->getTourRelated() as $related) { ?>
									<a 
									href="<?php echo $site->base; ?>/details/<?php echo $related->com; ?>/<?php echo $site->seoEncode($related->name); ?>" 
									class="rezgo-related-link"
									><?php echo $related->name; ?></a>

									<br/>
								<?php } ?>
							</div>
						<?php } ?>

						<script>
							jQuery(document).ready(function($){
								$('.rezgo-order-unavailable span').tooltip(); 
							});
						</script>
					</div>
				</div>

				<div class="row rezgo-form-group-short">
					<div class="collapse rezgo-pax-edit-box" id="pax-edit-<?php echo $item->cartID; ?>"></div>
				</div>

				<div id="pax-edit-scroll-<?php echo $item->cartID; ?>"></div>

				<?php $item_num++; ?>
			<?php } ?>

			<?php if (!$site->isVendor()) { ?>
				<div class="row rezgo-form-group-short">
					<?php if (!$_SESSION['rezgo_promo']): ?>
						<form 
						class="form-inline" 
						id="rezgo-promo-form" 
						role="form" 
						action="<?php echo home_url(); ?><?php echo $site->base; ?>/order?promo=">
							<label for="rezgo-promo-code">
								<i class="fa fa-tags"></i>
								<span class="rezgo-promo-label">
									<span>Promo code</span>
								</span>
							</label>
							<div class="input-group">
								<input 
								type="text" 
								class="form-control" 
								id="rezgo-promo-code" 
								name="promo" 
								placeholder="Enter Promo Code" 
								value="<?php echo ($_SESSION['rezgo_promo'] ? $_SESSION['rezgo_promo'] : ''); ?>" 
								/>
								<span class="input-group-btn">
									<button class="btn rezgo-btn-default" type="submit">Apply</button>
								</span>
							</div>
						</form>
					<?php else: ?>
						<label for="rezgo-promo-code">
							<i class="fa fa-tags"></i>
							<span>Entered promo code:</span>
						</label>
						<span id="rezgo-promo-value"><?php echo $_SESSION['rezgo_promo']; ?></span>
						<a 
						id="rezgo-promo-clear" 
						class="btn rezgo-btn-default btn-sm" 
						href="<?php echo home_url(); ?><?php echo $site->base; ?>/order?promo=" 
						target="_top"
						>clear</a>
					<?php endif; ?>
				</div>
			<?php } ?>

			<div class="row">
				<div class="col-sm-12 col-sm-offset-0 col-md-6 col-md-offset-6 rezgo-order-total">
					<span class="hidden-xs">Current Order</span>
					<span> Total <?php echo $site->formatCurrency($cart_total); ?></span>
				</div>
			</div>

			<div class="row" id="rezgo-booking-btn">
				<div class="col-md-4 col-sm-6 col-xs-12 rezgo-btn-wrp">
					<?php if ($site->getCartState()) { ?>
						<a href="<?php echo $site->base; ?>/" class="btn rezgo-btn-default btn-lg btn-block">Book More</a>
					<?php } ?>
				</div>

				<div class="col-md-4 col-md-offset-4 col-sm-6 col-xs-12 rezgo-btn-wrp">
					<form role="form" action="<?php echo $site->base; ?>/book">
						<input 
						type="hidden" 
						name="order" 
						value="clear"
						/>

						<?php $add_date_value = ''; ?>

						<?php foreach ($cart as $key => $item) { ?>
							<input 
							type="hidden" 
							name="add[<?php echo $item->cartID; ?>][uid]" 
							value="<?php echo $item->uid; ?>" 
							/>

							<?php 
								if (in_array((string) $item->date_selection, $date_types)) {
									$add_date_value = date("Y-m-d", (string)$item->booking_date);
								} else {
									$add_date_value = 'open';
								}
							?>

							<input 
							type="hidden" 
							name="add[<?php echo $item->cartID; ?>][date]" 
							value="<?php echo $add_date_value; ?>"
							/>

							<?php foreach ($site->getTourPrices($item) as $price) { ?>
								<?php if ($item->{$price->name.'_num'}) { ?>
									<input 
									type="hidden" 
									name="add[<?php echo $item->cartID; ?>][<?php echo $price->name; ?>_num]" 
									value="<?php echo $item->{$price->name.'_num'}; ?>"
									/>
								<?php } ?>
							<?php } ?>
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

						<?php if ($_COOKIE['show_standard']) { ?>
							<input 
							type="hidden" 
							name="show_standard" 
							value="<?php echo $_COOKIE['show_standard']; ?>"
							/>
						<?php } ?>

						<?php if (count($cart)) { ?>
							<input 
							class="btn rezgo-btn-book btn-lg btn-block" 
							value="Proceed to Check Out" 
							type="submit"
							/>
						<?php } ?>
					</form>
				</div>
			</div>
		<?php endif; ?>

		<?php
			// build 'share this order' link
			$pax_nums = array (
				'adult_num', 
				'child_num', 
				'senior_num', 
				'price4_num', 
				'price5_num', 
				'price6_num', 
				'price7_num', 
				'price8_num', 
				'price9_num'
			);

			$order_share_link = 'http://'.$_SERVER['HTTP_HOST'].$site->base.'/order/?order=clear';

			foreach ($cart as $key => $item) {
				if (in_array((string) $item->date_selection, $date_types)) {	
					$order_share_date = date("Y-m-d", (string)$item->booking_date);
				} else {
					$order_share_date = 'open'; // for open availability
				}

				$order_share_link .= '&add['.$item->cartID.'][uid]='.$item->uid;

				$order_share_link .= '&add['.$item->cartID.'][date]='.$order_share_date;

				foreach ($pax_nums as $pax) {
					if ($item->{$pax} != '') {
						$order_share_link .= '&add['.$item->cartID.']['.$pax.']='.$item->{$pax};
					}
				}
			}

			// finally, include promo if set
			if ($_SESSION['rezgo_promo']) {
				$order_share_link .= '&promo='.$_SESSION['rezgo_promo'];
			}
		?>

		<?php if ($site->getCartState() && count($cart)) { ?>
			<a id="rezgo-share-order">
				<i class="fa fa-share-alt-square"></i>
				<span>share this order</span>
			</a>

			<br />

			<input 
			type="text" 
			id="rezgo-order-url" 
			style="display:none;" 
			class="form-control" 
			onclick="this.select();" 
			value="<?php echo $order_share_link; ?>" 
			/>
		<?php } ?>
	</div>
</div>

<script>
	jQuery(document).ready(function($){
		$('.rezgo-pax-edit-btn').each(function() {
			var order_com = $(this).attr('data-order-com'); 
			var order_item = $(this).attr('data-order-item');
			var cart_id = $(this).attr('data-cart-id'); 
			var book_date = $(this).attr('data-book-date'); 
			var security = '<?php echo wp_create_nonce('rezgo-nonce'); ?>';
			var method	= 'edit_pax.php?';
					method += 'com='				 + order_com;
					method += '&id='				 + order_item;
					method += '&order_id='	 + cart_id;
					method += '&date='			 + book_date;
					method += '&parent_url=<?php echo $site->base; ?>';

			$.ajax({
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					action: 'rezgo',
					method: method,
					security: security
				},
				context: document.body,
				success: function(data) {
					$('#pax-edit-' + cart_id).html(data);
				}
			});
		});

		// EDIT PAX 
		$('.rezgo-pax-edit-btn').on('click',function(){
			var cart_id = $(this).attr('data-cart-id');

			var pax_edit_position = $('#pax-edit-scroll-' + cart_id).position();

			var pax_edit_scroll = Math.round(pax_edit_position.top);

			if ('parentIFrame' in window) {
				setTimeout(function () {
					parentIFrame.scrollTo(0,pax_edit_scroll);
				}, 100);
			}
		});

		// SHARE 
		$('#rezgo-share-order').on('click',function(e){
			e.preventDefault();

			$('#rezgo-order-url').toggle('fade');
		});
	});
</script>
