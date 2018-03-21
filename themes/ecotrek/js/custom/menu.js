jQuery('.menu-toggle').click(function(event) {
	event.preventDefault();
	$('.menu-header-container').slideToggle( "slow", function() {});
	$('.menu-header-ingles0-container').slideToggle( "slow", function() {});
});