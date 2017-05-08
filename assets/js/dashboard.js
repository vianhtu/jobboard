(function( $ ) {
    "use strict";
    $('.jobboard-table').on('click', '.toggle-row', function () {
        var tr = $(this).parents('tr');
        if(tr.hasClass('is-expanded')){
            tr.removeClass('is-expanded');
        } else {
            tr.addClass('is-expanded');
        }
    });
})( jQuery );
