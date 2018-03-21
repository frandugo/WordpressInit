<?php
	// This is the main booking page
	require('rezgo/include/page_header.php');

	// start a new instance of RezgoSite
	$site = new RezgoSite(secure);

	if (isset($_REQUEST['parent_url'])) {
			$site->base = '/' . $site->requestStr('parent_url');
	}
?>

<?php echo $site->getTemplate('frame_header'); ?>

<?php echo $site->getTemplate('book'); ?>

<?php echo $site->getTemplate('frame_footer'); ?>