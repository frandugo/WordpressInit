<?php
	$site->getCalendar(
		sanitize_text_field($_REQUEST['uid']), 
		sanitize_text_field($_REQUEST['date'])
	);

	$calendar_days = $site->getCalendarDays();

	foreach ( $site->getCalendarDays() as $day ) {
		if ($day->cond == 'a') { $class = ''; } // available
		elseif ($day->cond == 'p') { $class = 'passed'; }
		elseif ($day->cond == 'f') { $class = 'full'; }
		elseif ($day->cond == 'i' || $day->cond == 'u') { $class = 'unavailable'; }
		elseif ($day->cond == 'c') { $class = 'cutoff'; }		

		if ($day->date) { // && (int)$day->lead != 1
			$calendar_events .= '"'.date('Y-m-d', $day->date).'":{"class": "'.$class.'"},'."\n";
		}	
	}

	$calendar_events = trim($calendar_events, ','."\n");
?>

<script>
jQuery(document).ready(function($){
	$('.responsive-calendar').responsiveCalendar('edit', {
			<?php echo $calendar_events; ?>
	});
});
</script>