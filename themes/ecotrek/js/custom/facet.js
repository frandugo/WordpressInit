(function($) {
    $(document).on('facetwp-loaded', function() {
        $.each(FWP.settings.num_choices, function(key, val) {
            var $parent = $('.facetwp-facet-' + key).closest('.widget');
            (0 === val) ? $parent.hide() : $parent.show();
            console.log("Key " + key);
        });
    });
})(jQuery);