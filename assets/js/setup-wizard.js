(function( $ ) {
    "use strict";
    $('.select').select2({allowClear: true});

    $('.form-table').on('click', '.require-rc-framework', function () {

        var _this = $(this);
        _this.parent().find('.spinner').addClass('is-active');
        _this.remove();

        $.post(jobboard_setup_wizard.ajaxurl,
            {'action': 'jobboard_setup_require_plugins'},
            function(response) {
                location.reload();
            }
        );
    });

})( jQuery );