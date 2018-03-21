<?php
function rezgo_register_settings(){
	register_setting('rezgo_options', 'rezgo_cid');
	register_setting('rezgo_options', 'rezgo_api_key');
	register_setting('rezgo_options', 'rezgo_version');

	register_setting('rezgo_options', 'rezgo_captcha_pub_key');
	register_setting('rezgo_options', 'rezgo_captcha_priv_key');

	register_setting('rezgo_options', 'rezgo_result_num');
	register_setting('rezgo_options', 'rezgo_template');

	register_setting('rezgo_options', 'rezgo_forward_secure');
	register_setting('rezgo_options', 'rezgo_secure_url');
	register_setting('rezgo_options', 'rezgo_all_secure');

	wp_register_style('rezgo_settings_css', plugins_url('/css/settings.css', __FILE__), array(), REZGO_PLUGIN_VERSION );

	if (!get_option('rezgo_version')) {
		add_option('rezgo_version', REZGO_PLUGIN_VERSION);
		update_option('rezgo_template', 'default');
	} else {
		update_option('rezgo_version', REZGO_PLUGIN_VERSION);
	}
}
function rezgo_plugin_menu(){
	$icon = rezgo_embed_settings_image('icon.png');
	$menu_page = add_menu_page('Rezgo Settings', 'Rezgo', 'manage_options', 'rezgo-settings', 'rezgo_plugin_settings', $icon);
	add_action('admin_print_styles-' . $menu_page, 'rezgo_plugin_admin_styles');
}
function rezgo_ajax(){
	global $site;

	$site = new RezgoSite();

	$method = sanitize_text_field($_REQUEST['method']);

	$get = '';

	if (preg_match('/(.+)(\?com=.+)/', $method, $matches)) {
		if (isset($matches[1])) {
			$method = $matches[1];
		}

		if (isset($matches[2])) {
			$get = $matches[2];
		}

		$method = str_replace('.php', '', $method);
	}

	include( dirname(plugin_dir_path(__FILE__)) . '/' . $method . '.php');

	die();
}
function rezgo_query_vars($query_vars){
	$query_vars[] = 'rezgo';

	return $query_vars;
}
function rezgo_parse_request($items){
	if (array_key_exists('rezgo', $items->query_vars)) {
		rezgo_plugin_scripts_and_styles();

		include( dirname(plugin_dir_path(__FILE__)) . '/frame_router.php');

		die();
	}
}
function rezgo_plugin_scripts_and_styles(){
	global $wp_styles;

	$path = dirname( plugin_dir_url( __FILE__ ) ) . '/';

	// JS FILES 
	$jsIframeresizer = $path.'js/iframeResizer.min.js';
	$jsIframeresizerContentWindow = $path.'js/iframeResizer.contentWindow.min.js';
	$jsBootstrap = $path.'rezgo/templates/'.REZGO_TEMPLATE.'/js/bootstrap.min.js';
	$jsJqueryForm = $path.'rezgo/templates/'.REZGO_TEMPLATE.'/js/jquery.form.js';
	$jsJqueryValidate = $path.'rezgo/templates/'.REZGO_TEMPLATE.'/js/jquery.validate.min.js';
	$jsJquerySelect = $path.'rezgo/templates/'.REZGO_TEMPLATE.'/js/jquery.selectboxes.js';
	$jsCalendar = $path.'rezgo/templates/'.REZGO_TEMPLATE.'/js/responsive-calendar.min.js';
	$jsMapAPI = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDkCWu6MoROFlsRGoqFj-AXPEApsVjyTiA&sensor=false&libraries=places';
	$jsBarcode = $path.'rezgo/templates/'.REZGO_TEMPLATE.'/js/JsBarcode.all.min.js';
	$jsIntlTelInput = $path.'rezgo/templates/'.REZGO_TEMPLATE.'/js/intlTelInput/intlTelInput.min.js';

	// CSS FILES
	$cssBootstrap = $path.'rezgo/templates/'.REZGO_TEMPLATE.'/css/bootstrap.min.css';
	$cssFontAwesome = $path.'rezgo/templates/'.REZGO_TEMPLATE.'/css/font-awesome.min.css';
	$cssRezgo = $path.'rezgo/templates/'.REZGO_TEMPLATE.'/css/rezgo.css';
	$cssCalendar = $path.'rezgo/templates/'.REZGO_TEMPLATE.'/css/responsive-calendar.css';
	$cssCalendarRezgo = $path.'rezgo/templates/'.REZGO_TEMPLATE.'/css/responsive-calendar.rezgo.css';
	$cssIntlTelInput = $path.'rezgo/templates/'.REZGO_TEMPLATE.'/css/intlTelInput.css';

	// ENQUEUES
	wp_enqueue_script('iframe-resizer', $jsIframeresizer, array('jquery'), null, false);
	wp_enqueue_script( 'js-iframe-content-window', $jsIframeresizerContentWindow );

	if (isset($_REQUEST['rezgo'])) {
		wp_enqueue_style( 'css-bootstrap', $cssBootstrap, array(), '3.1.1' );
		wp_enqueue_style( 'css-font-awesome', $cssFontAwesome, array(), '4.6.3' );
		wp_enqueue_style( 'css-rezgo', $cssRezgo, array(), REZGO_PLUGIN_VERSION );
		wp_enqueue_script( 'js-bootstrap', $jsBootstrap, array('jquery') );
	}

	if (!isset($_REQUEST['mode'])) {
		return;
	}

	$arr = array(
		'page_contact',
		'page_book',
		'gift_card'
	);

	if (in_array($_REQUEST['mode'], $arr) || (isset($_REQUEST['method']) && $_REQUEST['method'] == 'booking_payment')) {
		wp_enqueue_script( 'js-form', $jsJqueryForm);
		wp_enqueue_script( 'js-validate', $jsJqueryValidate);
		wp_enqueue_script( 'js-selectbox', $jsJquerySelect);
	}

	if ($_REQUEST['mode'] == 'page_details') {
		wp_enqueue_style( 'css-calendar', $cssCalendar);
		wp_enqueue_style( 'css-calendar-rezgo', $cssCalendarRezgo);
		wp_enqueue_script( 'js-calendar', $jsCalendar);
	}

	$arr = array(
		'booking_voucher',
		'gift_card_details',
		'gift_card_print'
	);

	if (in_array($_REQUEST['mode'], $arr)) {
		wp_enqueue_script( 'js-barcode', $jsBarcode);
	}

	if ($_REQUEST['mode'] == 'page_book') {
		wp_enqueue_script( 'js-IntlTelInput', $jsIntlTelInput);
		wp_enqueue_style( 'css-IntlTelInput', $cssIntlTelInput);
	}

	$arr = array(
		'page_details',
		'page_contact',
		'booking_complete',
		'booking_complete_print',
		'booking_order_print'
	);

	if (in_array($_REQUEST['mode'],$arr)) {
		wp_enqueue_script( "js-map-api", $jsMapAPI);
	}
}
function rezgo_plugin_admin_styles(){
	wp_enqueue_style('rezgo_settings_css');
	wp_enqueue_script('rezgo_settings_js');
}
function rezgo_plugin_settings_link($links){
	$settings_link = '<a href="admin.php?page=rezgo-settings">Settings</a>';
	array_unshift($links, $settings_link);
	return $links;
}
function rezgo_plugin_settings(){
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}

	$rezgoPluginUpdated = false;

	if (isset($_POST['rezgo_update'])) {
		rezgo_plugin_settings_update();
		$rezgoPluginUpdated = true;
	}

	$rezgoCID = get_option('rezgo_cid');
	$rezgoApiKey = get_option('rezgo_api_key');
	$companyName = '';
	$companyDomain = '';

	if (!empty($rezgoCID) && !empty($rezgoApiKey)) {
		$xmlCheckOutput = rezgo_curl_get_page('http://xml.rezgo.com/xml?transcode=' . $rezgoCID . '&key=' . $rezgoApiKey . '&i=company');
		/**
		* @TODO change check of output result
		*/
		if ((string)$xmlCheckOutput->company_name) {
			$companyName = (string)$xmlCheckOutput->company_name;
			$companyDomain = (string)$xmlCheckOutput->domain;
		}
	}

	rezgo_render_settings_view('main_page.php', array(
		'permalinkStructure' => get_option('permalink_structure'),
		'rezgoCID' => get_option('rezgo_cid'),
		'rezgoApiKey' => get_option('rezgo_api_key'),
		'companyName' => $companyName,
		'companyDomain' => $companyDomain,
		'rezgoPluginUpdated' => $rezgoPluginUpdated,
		'safe_mode_on' => ini_get('safe_mode'),
		'open_basedir' => ini_get('open_basedir')
			)
	);
}
function rezgo_plugin_settings_update(){
	if (isset($_POST['rezgo_secure_url'])) {
		$_POST['rezgo_secure_url'] = str_replace("http://", "", $_POST['rezgo_secure_url']);
		$_POST['rezgo_secure_url'] = str_replace("https://", "", $_POST['rezgo_secure_url']);
	}

	if (!isset($_POST['rezgo_result_num'])) {
		$_POST['rezgo_result_num'] = 10;
	}

	update_option('rezgo_cid', $_POST['rezgo_cid']);
	update_option('rezgo_api_key', $_POST['rezgo_api_key']);

	update_option('rezgo_captcha_pub_key', $_POST['rezgo_captcha_pub_key']);
	update_option('rezgo_captcha_priv_key', $_POST['rezgo_captcha_priv_key']);

	update_option('rezgo_result_num', $_POST['rezgo_result_num']);
	update_option('rezgo_template', $_POST['rezgo_template']);

	// since we set this value to 1 as default, make sure it's set to 0 if it's off
	if (!isset($_POST['rezgo_forward_secure'])) {
		$_POST['rezgo_forward_secure'] = 0;
	}
	
	if (!isset($_POST['rezgo_all_secure'])) {
		$_POST['rezgo_all_secure'] = 0;
	}

	update_option('rezgo_forward_secure', $_POST['rezgo_forward_secure']);
	update_option('rezgo_secure_url', $_POST['rezgo_secure_url']);
	update_option('rezgo_all_secure', $_POST['rezgo_all_secure']);

	return true;
}

add_action('admin_init', 'rezgo_register_settings');
add_action('admin_menu', 'rezgo_plugin_menu');
add_filter('query_vars', 'rezgo_query_vars');
add_action('parse_request', 'rezgo_parse_request');
add_action('wp_ajax_nopriv_rezgo', 'rezgo_ajax');
add_action('wp_ajax_rezgo', 'rezgo_ajax');
add_action('wp_enqueue_scripts', 'rezgo_plugin_scripts_and_styles');
add_filter('plugin_action_links_rezgo/rezgo.php', 'rezgo_plugin_settings_link');
