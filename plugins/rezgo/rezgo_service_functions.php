<?php
function rezgo_return_file($filePath = '', $additionalVars = array()) 
{
	reset($GLOBALS);
	foreach($GLOBALS as $key => $val) {
		if(($key != strstr($key,"HTTP_")) && ($key != strstr($key, "_")) && ($key != 'GLOBALS')) {
			global ${$key};
		} 
	}

	extract($additionalVars);

	if (is_file(dirname(__FILE__) . DIRECTORY_SEPARATOR . $filePath)) {
		ob_start();
		include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . $filePath);
		$contents = ob_get_contents();
		ob_end_clean();
	} else {
		$this->error('"'.$filePath.'" file not found');
	}
	return $contents;
}

function rezgo_include_file($filePath = '', $additionalVars = array())
{
	extract($additionalVars);
	include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . $filePath);
}

function rezgo_include_settings_file($filePath = '')
{
	include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'settings' .DIRECTORY_SEPARATOR . $filePath);
}

function rezgo_render_settings_view($viewFile = '', $vars)
{
	extract($vars);
	include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'settings/views' .DIRECTORY_SEPARATOR . $viewFile);
}

function rezgo_embed_settings_image($imageName)
{
	return plugins_url('/settings/images/' . $imageName, __FILE__);
}

function rezgo_curl_get_page($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_TIMEOUT,30);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	$file = curl_exec($ch);

	curl_close($ch);

	$result = simplexml_load_string(utf8_encode($file));

	return $result;
}

