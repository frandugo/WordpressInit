<?php
if (!function_exists('_recaptcha_qsencode')) {
	require(plugins_url().'/rezgo/recaptchalib.php');
}

if ($_POST['rezgoAction'] == 'contact') {
	// NONCE CHECK
	check_admin_referer('rezgo-nonce');

	if ($_POST['hp_rezgo'] != '') {
		$bot_request = TRUE;
	} else {
		$site->cleanRequest();

		$resp = recaptcha_check_answer(REZGO_CAPTCHA_PRIV_KEY, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

		if (!$resp->is_valid) {
			$captcha_error = 'There was an error with your captcha text, please try again.';
		} else {
			$result = $site->sendContact();
		}
	}
}
?>

<?php if (isset($captcha_error)) { ?>
	<script type="text/javascript">
	jQuery(document).ready(function($){
		var captcha_position = $('#rezgo-scrollto-captcha').position();

		var captcha_scroll = Math.round(captcha_position.top);

		if ('parentIFrame' in window) {
			setTimeout(function () {
				parentIFrame.scrollTo(0,captcha_scroll);
			}, 100);
		}
	});
	</script>
<?php } ?>

<div class="container-fluid">
	<div class="rezgo-content-row">
		<h1 id="rezgo-contact-head">Add a Review</h1>

		<?php if ($result->status == 1 && $bot_request !== TRUE) { ?>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					if ('parentIFrame' in window) {
						parentIFrame.size();

						setTimeout(function () {
								parentIFrame.scrollTo(0,0);
						}, 100);
					}
				});
			</script>

			<div class="row rezgo-form-group">
				<div id="contact_success" class="alert alert-success">Thank you for your review.</div>
			</div>
		<?php } else { ?>
			<div class="row rezgo-form-group" id="rezgo-contact-form">
				<form 
				class="form-horizontal" 
				id="review_form" 
				role="form" 
				method="post" 
				action="page_contact?" 
				target="_self">
					<input type="hidden" name="rezgoAction" value="contact" />

					<div class="form-group">
						<label for="contact_fullname" class="col-sm-2 control-label">Name</label>

						<div class="col-sm-10">
							<input 
							type="text" 
							class="form-control" 
							id="contact_fullname" 
							placeholder="Full Name" 
							required name="full_name" 
							value="<?php echo sanitize_text_field($_REQUEST['full_name']); ?>" />
						</div>
					</div>

					<div class="form-group">
						<div class="required-group">
							<label for="contact_email" class="col-sm-2 control-label">Email</label>

							<div class="col-sm-4">
								<input 
								type="email" 
								class="form-control" 
								id="contact_email" 
								placeholder="Email" 
								required 
								name="email" 
								value="<?php echo sanitize_text_field($_REQUEST['email']); ?>" />
							</div>
						</div>

						<label for="contact_phone" class="col-sm-2 control-label">Phone</label>

						<div class="col-sm-4">
							<input 
							type="text" 
							class="form-control" 
							id="contact_phone" 
							placeholder="Phone Number" 
							name="phone" 
							value="<?php echo sanitize_text_field($_REQUEST['phone']); ?>" />
						</div>
					</div>

					<div class="form-group">
						<label for="contact_address" class="col-sm-2 control-label">Address</label>

						<div class="col-sm-10">
							<input 
							type="text" 
							class="form-control" 
							id="contact_address" 
							placeholder="123 My Street" 
							name="address" 
							value="<?php echo sanitize_text_field($_REQUEST['address']); ?>" />
						</div>
					</div>

					<div class="form-group">
						<label for="contact_city" class="col-sm-2 control-label">City</label>

						<div class="col-sm-10">
							<input 
							type="text" 
							class="form-control" 
							id="contact_city" 
							placeholder="My City" 
							name="city" 
							value="<?php echo sanitize_text_field($_REQUEST['city']); ?>" />
						</div>
					</div>

					<div class="form-group">
						<label for="contact_state" class="col-sm-2 control-label">State</label>

						<div class="col-sm-4">
							<input 
							type="text" 
							class="form-control" 
							id="contact_state" 
							placeholder="My State" 
							name="state_prov" 
							value="<?php echo sanitize_text_field($_REQUEST['state_prov']); ?>" />
						</div>

						<label for="" class="col-sm-2 control-label">Country</label>

						<div class="col-sm-4">
							<select class="form-control" id="contact_country" name="country">
								<?php foreach ($site->getRegionList() as $iso => $country_name) {
									echo '<option value="'.$iso.'"';

									if ($iso == $_REQUEST['country']) {
										echo ' selected';
									} elseif ($iso == $site->getCompanyCountry() && !$_REQUEST['country']) {
										echo ' selected';
									}

									echo '>'.ucwords($country_name).'</option>';
								} ?>
							</select>
						</div>
					</div>

					<span id="rezgo-scrollto-captcha"></span>

					<div class="form-group">
						<label for="contact_comment" class="col-sm-2 control-label">Comment</label>

						<div class="col-sm-10">
							<textarea class="form-control" name="body" id="contact_comment" rows="8" wrap="on" required><?php echo sanitize_text_field($_REQUEST['body']);?></textarea>

							<input type="text" name="hp_rezgo" class="hp_rez" value="" />
						</div>
					</div>

					<?php if ($site->exists(REZGO_CAPTCHA_PUB_KEY)) { ?>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Verification</label>

							<div class="col-sm-10">
								<div id="captcha">
									<?php echo recaptcha_get_html(REZGO_CAPTCHA_PUB_KEY, null, 1); ?>

									<br />

									<div id="rezgo-captcha-error-container" class="rezgo-captcha-error"<?php echo (isset($captcha_error) ? '' : ' style="display:none"' ); ?>>
										<?php echo $captcha_error; ?>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>

					<?php wp_nonce_field('rezgo-nonce'); ?>

					<div class="col-sm-3 col-sm-offset-9 col-xs-12">
						<input 
						type="submit" 
						class="btn btn-primary btn-lg btn-block" 
						value="Send Request" />
					</div>
				</form>
			</div>
		<?php } ?>

		<?php $company = $site->getCompanyDetails(); ?>

		<div class="rezgo-content-row" id="rezgo-contact-address">
			<div class="col-sm-12 col-md-3">
				<address>
					<h3><?php echo $company->company_name; ?></h3>
					<span><?php echo $company->address_1; ?> <?php echo $company->address_2; ?></span>
					<br />
					<span><?php echo $company->city; ?>, </span>
					<?php if ($site->exists($company->state_prov)) { ?>
						<span><?php echo $company->state_prov; ?>, </span>
					<?php } ?>
					<span><?php echo $site->countryName($company->country); ?></span>
					<br />
					<span><?php echo $company->postal_code; ?></span>
					<br />
					<br />
					<?php if ($site->exists($company->phone)) { ?>
						<span>Phone: <?php echo $company->phone; ?></span>
						<br />
					<?php } ?>
					<?php if ($site->exists($company->fax)) { ?>
						<span>Fax: <?php echo $company->fax; ?></span>
						<br />
					<?php } ?>
						<span>Email: <?php echo $company->email; ?></span>
					<?php if ($site->exists($company->tax_id)) { ?>
						<br />
						<span>Tax ID: <?php echo $company->tax_id; ?></span>
					<?php } ?>
				</address>
			</div>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	$.validator.setDefaults({
		highlight: function(element) {
			if ($(element).attr("name") == "email" ) {
				$(element).closest('.required-group').addClass('has-error'); // only highlight email
			} else {
				$(element).closest('.form-group').addClass('has-error');
			}
		},
		unhighlight: function(element) {
			if ($(element).attr("name") == "email" ) {
				$(element).closest('.required-group').removeClass('has-error'); // unhighlight email
			} else {
				$(element).closest('.form-group').removeClass('has-error');
			}
		},
		errorElement: 'span',
		errorClass: 'help-block',
		errorPlacement: function(error, element) {
			if(element.parent('.input-group').length) {
				error.insertAfter(element.parent());
			} else {
				error.insertAfter(element);
			}
		}
	});	

	$('#review_form').validate({
		rules: {
			full_name: {
				required: true
			},
			email: {
				required: true,
				email: true
			},
			body: {
				required: true,
			}
		},
		messages: {
			full_name: {
				required: "Please enter your full name"
			},
			email: {
				required: "Please enter a valid email address"
			},
			body: {
				required: "Please enter a comment"
			}
		}
	});
});
</script>
