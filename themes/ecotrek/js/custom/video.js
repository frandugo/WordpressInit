var min_w = 300;
var vid_w_orig;
var vid_h_orig;

$(function() {
    if($(window).width() > 1350 ){
        vid_w_orig = parseInt($('video').attr('width'));
        vid_h_orig = parseInt($('video').attr('height'));

        $(window).resize(function () { fitVideo(); });
        $(window).trigger('resize');
    }    

});

function fitVideo() {
    if($(window).width() > 1350 ){
        $('.video').width($('.bg').width());
        $('.video').height($('.bg').height());
        var scale_h = $('.bg').width() / vid_w_orig;
        var scale_v = $('.bg').height() / vid_h_orig;
        var scale = scale_h > scale_v ? scale_h : scale_v;

        if (scale * vid_w_orig < min_w) {scale = min_w / vid_w_orig;};

        $('video').width(scale * vid_w_orig);
        $('video').height(scale * vid_h_orig);

        $('.video').scrollLeft(($('video').width() - $('.bg').width()) / 2);
        $('.video').scrollTop(($('video').height() - $('.bg').height()) / 2);
    }    
    
};  