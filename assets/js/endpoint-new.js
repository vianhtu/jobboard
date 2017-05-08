(function( $ ) {
    "use strict";

    $('.field-location select').on('change', function () {
        var level = $(this).data('level');
        var terms = $(this).parents('.field-location');

        terms.find('select').each(function () {
            var _level = parseInt($(this).data('level'));
            if(_level > level){
                $(this).prop("disabled", true);
            }
        });

        load_location($(this), level);
    });

    $(window).on('load', function() {
        $('.field-location').each(function () {
            if(!$(this).find('select[data-level="1"]').val()){
                $(this).find('select[data-level="0"]').trigger('change');
            }
        });
    });
    
    function load_location(parent, level) {
        var terms   = parent.parents('.field-location');
        var value   = parent.val();
        $.post(
            ajaxurl,
            {
                'action': 'rc_taxonomy_level',
                'parent': value,
                'taxonomy': terms.data('taxonomy'),
            },
            function (response) {
                terms.find('select').each(function () {
                    var _level = parseInt($(this).data('level'));
                    if(_level == level + 1){
                        terms.find('select[data-level="'+_level+'"]').html(response);
                    } else if(_level > level + 1){
                        terms.find('select[data-level="'+_level+'"]').html('');
                    }
                });

                terms.find('select').prop("disabled", false);
            }
        );
    }

})( jQuery );