<div class="wrap" id="rezgo_settings">
	<img src="<?php echo rezgo_embed_settings_image('logo_rezgo.png'); ?>"/>

	<?php if ($rezgoPluginUpdated) { ?>
		<div class="notice notice-success">
			<p>Your Rezgo options have been updated.</p>
		</div>
	<?php } ?>

	<?php if ($safe_mode_on) { ?>
		<div class="notice notice-error">	
			<p>It appears that <em>safe_mode</em> is enabled in your server's PHP settings. The Rezgo plugin requires safe_mode to be off for proper functioning.</p>
		</div>
	<?php } ?>

	<?php if ($open_basedir) { ?>
		<div class="notice notice-error">
			<p>A <em>open_basedir</em> restriction is in effect on your server. The Rezgo plugin requires there to be no open_basedir restriction for full functionality.</p>
		</div>
	<?php } ?>

		<p>
			Rezgo makes it easy for you to accept bookings on your tour or activity business WordPress site. To manage your Rezgo account, <a href="http://login.rezgo.com" target="_blank">login here</a>.
		</p>

		<h3>Getting Started</h3>

		<p>
			<ol>
				<li><a href="http://www.rezgo.com">Sign-up for a Rezgo account</a>.</li>
				<li>Setup your inventory and configure your account on Rezgo.</li>
				<li>Complete the Rezgo WordPress Plugin settings below.</li>
				<li>Create a Page and embed the Rezgo booking engine by using the shortcode: [rezgo_shortcode].</li>
				<li>
					<span>Ensure you are using a non default permalink structure.</span>
					&nbsp;
					<?php if ($permalinkStructure == '') { ?>
						<div style="border:1px solid #9E0000; padding: 4px; padding-left:6px; padding-right:6px; background-color: #D97F7E; width:-moz-fit-content;">
							<strong>currently using [</strong>default<strong>] which may not work correctly! <a href="/wp-admin/options-permalink.php" style="color:#333333;">Click here</a> to change it.</strong>
						</div>
					<?php } else { ?>
						Your current structure should work!
					<?php } ?>
				</li>
			</ol>
		</p>

	<form method="post" action="">
		<?php echo settings_fields('rezgo_options'); ?>

		<script>
		var cid_value = '<?php echo $rezgoCid; ?>';

		var key_value = '<?php echo $rezgoApiKey; ?>';

		jQuery(document).ready(function($) {
			function check_values() {
				var cid = $('#rezgo_cid').val();
				var key = $('#rezgo_api_key').val();

				// do nothing if we changed nothing
				if (cid_value != cid || key_value != key) {
					cid_value = cid;
					key_value = key;

					if (cid && key) {
						$('#check_values').html('<img src="<?php echo rezgo_embed_settings_image('load.gif') ?>">');

						$('#check_values').load('<?php echo REZGO_URL_BASE.'/settings/settings_ajax.php'?>?cid=' + cid.trim() + '&key=' + key.trim());
					} else {
						reset_check();
					}
				}
			}

			function reset_check() {
				$('#check_values').html('<span style="required_missing">Information is missing.</span>');
			}
		});
		</script>

		<div class="field_frame">
			<fieldset>
				<legend class="account_info">Account Information</legend>

				<dl>
					<dt class=note>Your Company Code and API Key can be found on the Rezgo settings page.</dt>

					<br><br>

					<dt>Rezgo Company Code:</dt>

					<dd>
						<input type="text" name="rezgo_cid" id="rezgo_cid" size="10" value="<?php echo $rezgoCID ?>" onkeyup="check_values()" />
					</dd>

					<dt>Rezgo API Key:</dt>

					<dd>
						<input type="text" name="rezgo_api_key" id="rezgo_api_key" size="20" value="<?php echo $rezgoApiKey ?>" onkeyup="check_values()" />
					</dd>

					<div class="api_box" id="check_values">
						<?php if ($rezgoCID && $rezgoApiKey) { ?>
							<?php if (!empty($companyName)) { ?>
								<span class="ajax_success">XML API Connected</span><br>
								<span class="ajax_success_message"><?php echo $companyName ?></span>
								<a href="http://<?php echo $companyDomain ?>.rezgo.com" class="ajax_success_url" target="_blank">
									<span><?php echo $companyDomain ?>.rezgo.com</span>
								</a>
							<?php } else { ?>
								<span class="ajax_error">XML API Error</span><br>
								<span class="ajax_error_message"><?php echo $companyError ?></span>
							<?php } ?>
						<?php } else { ?>
							<span style="required_missing">Information is missing</span>
						<?php } ?>
					</div>
				</dl>
			</fieldset>
		</div>

		<div class="field_frame">
			<fieldset>
				<legend class="recaptcha_key">Recaptcha API Keys</legend>

				<dl>
					<dt class=note>
						If you wish to use Recaptcha on your contact page, enter your API credentials here. You can get Recaptcha for free from
						<a href="http://www.google.com/recaptcha" target="_blank">Google</a>
					</dt>
					<br><br>
					<dt>Recaptcha Public Key:</dt>
					<dd>
						<input type="text" name="rezgo_captcha_pub_key" size="50"	value="<?php echo get_option('rezgo_captcha_pub_key') ?>" />
					</dd>
					<dt>Recaptcha Private Key:</dt>
					<dd>
						<input type="text" name="rezgo_captcha_priv_key" size="50" value="<?php echo get_option('rezgo_captcha_priv_key') ?>"/>
					</dd>
				</dl>
			</fieldset>
		</div>
		<?php $template_url = REZGO_DIR.'/templates/'; ?>

		<script type="text/javascript">
		jQuery(document).ready(function($){
			var tpl_text = {
				'default': 'This is the default Rezgo template.',
				'custom': 'This is a custom template.',
				'error': 'Warning, your custom directory does not exist.'
			};

			<?php if (get_option('rezgo_template') == 'default') { ?>
				$('#template_description').html(tpl_text['default']);
			<?php } else if (!is_dir($_SERVER['DOCUMENT_ROOT'].$template_url.get_option('rezgo_template').'/')) { ?>
				$('#template_description').html(tpl_text['error']).addClass('template_error');
			<?php } else { ?>
				$('#template_description').html(tpl_text['custom']);
			<?php } ?>

			$('#template_select').change(function() {
				var tpl = $(this).attr('value');

				switch (tpl) {
					case 'default':
						$('#template_description').html(tpl_text['default']).removeClass('template_error');
						break;
					default:
						$('#template_description').html(tpl_text['custom']).removeClass('template_error');
						break;
				}
			});

			$(document).on('click','.rezgo-forward-secure-checkbox',function(e){
				if ($(this)[0].checked == true) { 
					$('#alternate_url').fadeOut(); 
				} else { 
					$('#alternate_url').fadeIn(); 
				}
			});
		});
		</script>

		<div class="field_frame">
			<fieldset>
				<legend class="general_settings">General Settings</legend>

				<dl>
					<dt class=note>
						How many results do you want to show on each page? We suggest 10. Higher numbers may have an impact on performance.
						<br /><br />
					</dt>
					<?php
					$results_num = get_option('rezgo_result_num');

					if (!$results_num) $results_num = 10;
					?>
					<dt>Number of results:</dt>
					<dd>
						<input 
						type="text" 
						name="rezgo_result_num" 
						size="5" 
						value="<?php echo $results_num ?>" 
						/>
					</dd>
					<dt class=note>
						The Rezgo template you want to use. Add new templates to <?php echo $template_url; ?>
					</dt>
					<br><br>
					<dt>Template:</dt>
					<dd>
						<select name="rezgo_template" id="template_select">
							<?php
							
							if (!ini_get('safe_mode') && !ini_get('open_basedir')) {
								
								$handle = opendir($_SERVER['DOCUMENT_ROOT'].$template_url);
	
								while (false !== ($file = readdir($handle))) {
									if (strpos($file, '.') === FALSE) {
										$select = ($file == get_option('rezgo_template')) ? 'selected' : '';
										echo '<option value="'.$file.'" '.$select.'>'.$file.'</option>';
									}
								}
	
								closedir($handle);
								
							} else {
								
								echo '<option value="default">default</option>';
								
							}
							?>
						</select>

						<div id="template_description">
						</div>
					</dd>

					<dt class=note>
						If you wish all pages on your site to be secure, check the option below.
					</dt>
					<br><br>
					<?php $all_secure_checked = (get_option('rezgo_all_secure')) ? 'checked ' : ''; ?>
					<dt>Support ALL secure pages:</dt>
					<dd>
						<input 
						type="checkbox" 
						name="rezgo_all_secure" 
						value="1" 
						<?php echo $all_secure_checked; ?> 
						/>
					</dd>
					<dt class="note">
						If you do not have your own security certificate (SSL), you can forward users to the Rezgo white-label for bookings or gift card purchases.
					</dt>
					<br><br>
					<?php
					// if forward secure is not yet set to anything, check it as default
					if (get_option('rezgo_forward_secure') === '' || get_option('rezgo_forward_secure') === false) {
						$forward_secure_checked = 'checked ';
					} else {
						$forward_secure_checked = (get_option('rezgo_forward_secure')) ? 'checked ' : '';
					}
					?>
					<dt>Forward secure page to Rezgo:</dt>
					<dd>
						<input 
						type="checkbox" 
						class="rezgo-forward-secure-checkbox" 
						name="rezgo_forward_secure" 
						value="1" 
						<?php echo $forward_secure_checked; ?> 
						/>
					</dd>
				</dl>

				<div id="alternate_url" style="display: <?php echo (($forward_secure_checked) ? 'none' : ''); ?>">
					<dl>
						<dt class="note">
							By default, Rezgo will use your current domain for the secure site. If you have another
							secure domain you want to use (such as secure.mysite.com) you can specify it here. Otherwise leave
							this blank.
						</dt>
						<dt>Alternate Secure URL:</dt>
						<dd>
							<input 
							type="text" 
							name="rezgo_secure_url" 
							size="50" 
							value="<?php echo get_option('rezgo_secure_url'); ?>"
							/>
						</dd>
					</dl>

					<br clear="all" />
				</div>
			</fieldset>
		</div>

		<br/>

		<input type="submit" class="button-primary" value="Save Changes"/>

		<input type="hidden" name="rezgo_update" value="1"/>

		<input type="hidden" name="action" value="update"/>

		<input type="hidden" name="page_options" value="rezgo_cid,rezgo_api_key,rezgo_uri,rezgo_result_num"/>
	</form>

	<br clear="all"/>
</div>