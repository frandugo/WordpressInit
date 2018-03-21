<noscript>
	<iframe src="//www.googletagmanager.com/ns.html?id=GTM-TK6F39" height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>

<script>
	datalayer = [{ 'cid':'<?php echo REZGO_CID; ?>' }];
	(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-TK6F39');
</script>

<div class="container-fluid">
	<?php if ($_COOKIE['rezgo_refid_val']) { ?>
		<div id="rezgo-refid"> RefID: <?php echo $_COOKIE['rezgo_refid_val']; ?> </div>
	<?php } ?>

</div>

</body>

<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-1943654-2', 'auto');
	ga('send', 'pageview');

	<?php
	if ($_SERVER['SCRIPT_NAME'] == '/page_order.php' || $_SERVER['SCRIPT_NAME'] == '/page_book.php' || $_SERVER['SCRIPT_NAME'] == '/booking_complete.php') {
		echo "ga('require', 'ec');"."\n";
		if ($_SERVER['SCRIPT_NAME'] == '/page_order.php') {
			echo "
			ga('ec:setAction','checkout', {
					'step': 1
			});
			";
		}
		if ($_SERVER['SCRIPT_NAME'] == '/page_book.php') {
			echo "
			ga('ec:setAction','checkout', {
					'step': 2
			});
			";
		}
		if ($_SERVER['SCRIPT_NAME'] == '/booking_complete.php' && $_SESSION['REZGO_CONVERSION_ANALYTICS']) {
			echo "
			ga('ec:setAction','checkout', {
					'step': 3
			});
			";

			echo "
			ga('require', 'ecommerce');
			".$ga_add_transacton."
			ga('ecommerce:send');
			";
		}
	}
	?>

	var transcode = '<?php echo REZGO_CID ?>';
</script>

</html>