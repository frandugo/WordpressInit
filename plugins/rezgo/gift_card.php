<?php 
	// This is the gift card page
	require('rezgo/include/page_header.php');

	// start a new instance of RezgoSite
	$site = new RezgoSite(secure);

	if (isset($_REQUEST['parent_url'])) {
			$site->base = '/' . $site->requestStr('parent_url');
	}
?>

<?php echo $site->getTemplate('frame_header'); ?>

<?php echo $site->getTemplate('gift_card'); ?>

<?php echo $site->getTemplate('frame_footer'); ?>