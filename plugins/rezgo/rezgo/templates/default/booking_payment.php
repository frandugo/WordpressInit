<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<?php
		rezgo_plugin_scripts_and_styles();
		wp_print_scripts();
		wp_print_styles();
		?>

		<?php if ($site->exists($site->getStyles())) { ?>
			<style><?php echo $site->getStyles();?></style>
		<?php } ?>
	</head>
	<body class="rezgo-booking-payment-body">
		<script>
			jQuery(document).ready(function($){
				function check_valid() {
					var valid = $("#payment").valid();

					return valid;
				}

				function creditConfirm(token) {
					// the credit card transaction was completed, give us the token
					$(parent.document).find("#tour_card_token").val(token);
					$(parent.document).find("#gift_card_token").val(token);
				}

				<?php if ($_REQUEST['rezgoAction'] == 'return') { ?>
					creditConfirm("<?php echo sanitize_text_field($_REQUEST['token']); ?>");
				<?php } ?>
			});
		</script>
	
		<form method="post" id="payment" action="https://process.rezgo.com/form" autocomplete="off">
			<input type="hidden" name="return" value="<?php echo home_url(); ?>?rezgo=1&mode=booking_payment&rezgoAction=return&" />

			<div id="payment_card_info" class="container-fluid">
				<div class="row">
				<div class="form-group col-xs-12">
					<label for="name" class="control-label">Cardholder Name</label>

					<input type="text" class="form-control" id="name" name="name" value="<?php echo $site->requestStr('name'); ?>" required />
				</div>

				<div class="form-group col-xs-12">
					<label for="pan" class="control-label">Card Number</label>

					<input type="text" class="form-control" id="pan" name="pan" value="<?php echo $site->requestStr('pan'); ?>" required />
				</div>

				<div class="form-group col-xs-6">
					<label for="exp_month" class="control-label">Card Exp<span class="hidden-xs">iration</span></label>

					<select name="exp_month" id="exp_month" class="form-control">
						<option value="01">01</option>
						<option value="02">02</option>
						<option value="03">03</option>
						<option value="04">04</option>
						<option value="05">05</option>
						<option value="06">06</option>
						<option value="07">07</option>
						<option value="08">08</option>
						<option value="09">09</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
					</select>
				</div>

				<div class="form-group col-xs-6">
					<label for="exp_year" class="control-label">&nbsp;</label>

					<select name="exp_year" id="exp_year" class="form-control">
						<?php for($d=date("Y"); $d <= date("Y")+12; $d++) { ?>
							<option value="<?php echo substr($d, -2); ?>"><?php echo $d; ?></option>
						<?php } ?>
					</select>	
				</div>

				<?php if ($site->getCVV()) { ?>
					<div class="form-group col-sm-6 col-xs-12">
						<label for="rezgo-cvv" class="control-label" id="rezgo-cvv-label">
							<span>CVV&nbsp;</span>

							<a href="javascript:void(0);" onclick="javascript:window.open('<?php echo $site->path; ?>/img/cvv_cards.png',null,'width=600,height=300,status=no,toolbar=no,menubar=no,location=no');">
								<span>what is this?</span>
							</a>
						</label>

						<br />

						<input type="text" class="form-control col-xs-5" name="cvv" id="rezgo-cvv" required />
					</div>
				<?php } ?>
				</div>
			</div>
		</form>
	</body>
</html>