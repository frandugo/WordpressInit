<?php
	// any new page must start with the page_header, it will include the correct files
	// so that the rezgo parser classes and functions will be available to your templates

	// start a new instance of RezgoSite
	$site = new RezgoSite(sanitize_text_field($_REQUEST['sec']));

	// remove the 'mode=page_type' from the query string we want to pass on
	$_SERVER['QUERY_STRING'] = preg_replace("/([&|?])?mode=([a-zA-Z_]+)/", "", $_SERVER['QUERY_STRING']);

	if ($_REQUEST['title']) {
		$site->setPageTitle(
			sanitize_text_field($_REQUEST['title'])
		);
	}
	else {
		$site->setPageTitle(
			ucwords(
				str_replace(
					"page_", 
					"", 
					sanitize_text_field($_REQUEST['mode'])
				)
			)
		);
	}

	if ($_REQUEST['mode'] == 'page_details') {
		/*
			this query searches for an item based on a com id (limit 1 since we only want one response)
			then adds a $f (filter) option by uid in case there is an option id, and adds a date in case there is a date set	
		*/

		$trs	= 't=com';
		$trs .= '&q='			.sanitize_text_field($_REQUEST['com']);
		$trs .= '&f[uid]=' .sanitize_text_field($_REQUEST['option']);
		$trs .= '&d='			.sanitize_text_field($_REQUEST['date']);
		$trs .= '&limit=1';

		$item = $site->getTours($trs, 0);

		// if the item does not exist, we want to generate an error message and change the page accordingly
		if (!$item) {
			$item = new stdClass();
			$item->unavailable = 1;
			$item->name = 'Item Not Available'; 
		}

		if ($item->seo->seo_title != '') {
			$site->setPageTitle($item->seo->seo_title);
		} 
		else {
			$site->setPageTitle($item->item);
		}

		$site->setMetaTags('
			<meta name="description" content="' . $item->seo->introduction . '" /> 
			<meta property="og:title" content="' . $item->seo->seo_title . '" /> 
			<meta property="og:description" content="' . $item->seo->introduction . '" /> 
			<meta property="og:image" content="' . $item->media->image[0]->path . '" /> 
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
		');
	}

	elseif ($_REQUEST['mode'] == 'index') {
		// expand to include keywords and dates

		if ($_REQUEST['tags']) {
			$site->setPageTitle(ucwords(sanitize_text_field($_REQUEST['tags'])));
		}

		else {
			$site->setPageTitle('Home');
		}
	}
?>

<script type="text/javascript">
	// for iFrameResize native version
	// MDN PolyFil for IE8 
	if (!Array.prototype.forEach) {
		Array.prototype.forEach = function(fun) {
			"use strict";

			if (this === void 0 || this === null || typeof fun !== "function") {
				throw new TypeError();
			}

			var t = Object(this);
			var len = t.length >>> 0;
			var thisArg = arguments.length >= 2 ? arguments[1] : void 0;

			for (var i = 0; i < len; i++) {
				if (i in t) {
					fun.call(thisArg, t[i], i, t);
				}
			}
		};
	}
</script>

<div id="rezgo_content_container" style="width:100%;">
	<?php
	$src	= home_url();
	$src .= '?rezgo=1';
	$src .= '&mode='.sanitize_text_field($_REQUEST['mode']);
	$src .= '&com='.sanitize_text_field($_REQUEST['com']);
	$src .= '&parent_url='.$wp_current_page;
	$src .= '&wp_slug='.$wp_slug;
	$src .= '&tags='.sanitize_text_field($_REQUEST['tags']);
	$src .= '&search_for='.sanitize_text_field($_REQUEST['search_for']);
	$src .= '&start_date='.sanitize_text_field($_REQUEST['start_date']);
	$src .= '&end_date='.sanitize_text_field($_REQUEST['end_date']);
	$src .= '&date='.sanitize_text_field($_REQUEST['date']);
	$src .= '&rezgo_page='.sanitize_text_field($_REQUEST['rezgo_page']);
	$src .= '&option='.sanitize_text_field($_REQUEST['option']);
	$src .= '&cid='.sanitize_text_field($_REQUEST['cid']);
	$src .= '&trans_num='.sanitize_text_field($_REQUEST['trans_num']);
	$src .= '&card='.sanitize_text_field($_REQUEST['card']);
	$src .= '&page_title='.sanitize_text_field($site->pageTitle);
	$src .= '&seo_name='.$site->seoEncode($item->item);
	?>

	<iframe id="rezgo_content_frame" src="<?php echo $src; ?>" style="width:100%; height:1200px; padding:0px; margin:0px;" frameBorder="0" scrolling="no"></iframe>
</div>

<script type="text/javascript">
	iFrameResize ({
		enablePublicMethods: true,
		scrolling: false
	});
</script>

<script type="text/javascript" src="https://d31qbv1cthcecs.cloudfront.net/atrk.js"></script>

<script type="text/javascript">
	_atrk_opts = { atrk_acct: "51dve1aoim00G5", domain:"rezgo.com"};
	atrk();
</script>

<noscript>
	<img src="https://d5nxst8fruw4z.cloudfront.net/atrk.gif?account=51dve1aoim00G5" style="display:none" height="1" width="1" alt="" />
</noscript>
