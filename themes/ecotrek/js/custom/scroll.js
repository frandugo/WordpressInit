$(window).scroll(function() {
    if ($(window).scrollTop() >= ( ($(document).height() - $(window).height()) - 200) ) {
      $('.cloud1').css( "left", "0" );
      $('.cloud2').css( "right", "-23px" );
    }else{
      $('.cloud1').css( "left", "-450px" ); 
      $('.cloud2').css( "right", "-350px" ); 
  }
});