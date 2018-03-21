$('.buildurl').click(function(event) {
	$from = $('.filter-service #from').val();
	$to = $('.filter-service #to').val();
	$people = $('.people').val();
	console.log('From ' + $from + ' To ' + $to + ' People ' + $people );
	window.location.replace("https://www.booking.com/hotel/co/posada-las-trampas.html?aid=357026;label=gog235jc-hotel-XX-co-posadaNlasNtrampas-unspec-co-com-L%3Axu-O%3AosSx-B%3Achrome-N%3AXX-S%3Abo-U%3Ac-H%3As;sid=c1c1682f6c98d7c4460ba251738e1504;bshb=2;checkin=" + $from + ";checkout=" + $to + ";dest_id=-586395;dest_type=city;dist=0;&group_adults="+ $people +";group_children=0;hapos=1;hpos=1;no_rooms=1;room1=A%2CA;sb_price_type=total;soh=1;soldout=0%2C0;srepoch=1513865973;srfid=9c0054bfc073574ec744dee97cc76df54cc9fdceX1;srpvid=a66e64ba4dab0079;type=total;ucfs=1&#no_availability_msg");
});