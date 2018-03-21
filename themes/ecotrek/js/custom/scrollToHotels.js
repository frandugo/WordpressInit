$('.clickToScroll a').click(function(){
	if (location.hash){
		var target = location.hash;
	}else{
		var hashTarget = this.href.split("#")[1];
		var target = '#' + hashTarget;
	}	
    $('html, body').animate({
        scrollTop: $(target).offset().top
    }, 800);
    return false;
});

var jump = function(e){
   if (e){
       e.preventDefault();
       var target = $(this).attr("href");
   }else{
       var target = location.hash;
   }

   $('html,body').animate(
   {
       scrollTop: $(target).offset().top
   },800,function()
   {
       location.hash = target;
   });

}

//$('html, body').hide();

$(document).ready(function(){
    //$('a[href^=#]').on("click", jump);
    if (location.hash){
        setTimeout(function(){
            $('html, body').scrollTop(0).show();
            jump();
        }, 0);
    }else{
        $('html, body').show();
    }
});