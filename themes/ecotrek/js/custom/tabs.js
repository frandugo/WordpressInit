$('ul.tabs li').click(function(e){
	e.preventDefault();
	var tab_id = $(this).attr('data-tab');

	$('ul.tabs li').removeClass('current');
	$('.tabs-info-item').removeClass('current');

	$(this).addClass('current');
	$("#"+tab_id).addClass('current');
	console.log(tab_id);
})