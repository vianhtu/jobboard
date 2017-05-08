(function( $ ) {
    "use strict";

    var table_id        = 0;
    var ajax            = false;
    var page            = 0;
    var post_id         = null;
    var s               = '';

    var applications    = $('#table-applications');
    var loading         = $('.applications-filter .jobboard-loading');
    var table_wrap      = $('.applications-table .table-wrap');

    /**
     * Get applications.
     */
    $('#table-jobs').on('click', '.application', function () {

        post_id     = $(this).data('id');

        if(!post_id){
            return;
        }

        if(post_id !== table_id) {
            applications.find('tbody tr:not(:first-child)').remove();
            page = 0;
            get_applications();
        }

        table_id = post_id;
    });

    /**
     * Edit
     */
    $('#table-jobs').on('click', '.action-edit', function () {

        var edit = $(this).data('url');

        if(!edit){
            return;
        }

        window.location.href = edit;
    });

    /**
     * Delete.
     */
    $('#table-jobs').on('click', '.action-delete', function () {

        var id = $(this).data('id');
        var tr = $(this).parents('tr');

        if(!id){
            return;
        }

        var confirm = $(this).data('confirm');
        var title   = $(this).data('title');

        jobboard_confirm(confirm + ' <span>' + title + '</span>', function () {
            tr.css('opacity', 0.5);
            tr.find('button').prop('disabled', true);
            $.post(ajaxurl, {'action': 'jobboard_delete_job', 'id': id}, function(response) {
                jobboard_create_notices(response, 'success');
                tr.remove();
            });
        });
    });

    /**
     * Approve & reject.
     */
    $('#table-applications').on('click', '.action-approve, .action-reject', function () {

        var id = $(this).data('id');
        var tr = $(this).parents('tr');

        tr.css('opacity', 0.5);
        tr.find('button').prop('disabled', true);

        if($(this).hasClass('action-approve')) {
            update_application('approved', id, tr);
        } else {
            update_application('reject', id, tr);
        }
    });

    /**
     * Load more.
     */
    table_wrap.on('scroll', function() {
        var top     = $(this).scrollTop();
        var height  = $(this).find('.table').height();

        if (top >= height - 500 && ajax == false){
            ajax = true;
            get_applications();
        }
    });

    /**
     * Search applications.
     */
    $('.applications-filter').on('keypress change', '.search-field', function (e) {

        if(!post_id){
            return;
        }

        if (e.which == 13 || e.type == 'change') {
            applications.find('tbody tr:not(:first-child)').remove();
            s       = $(this).val();
            page    = 0;
            get_applications();
        }
    });

    function get_applications() {

        loading.css('display', 'block');

        $.post(ajaxurl, {'action': 'jobboard_get_applications', 'id': post_id, 's': s, 'page': page}, function(response) {

            table_wrap.find('.table-not-found').remove();

            if(response.error == 0){
                applications.find('tbody').append(response.html);
                page++;
                ajax = false;
            } else if(response.error == 2) {
                ajax = true;
            } else {
                table_wrap.append(response.html);
                ajax = false;
            }

            loading.css('display', 'none');
        });
    }

    function update_application(status, id, tr) {

        if(!id || !status){
            return;
        }

        loading.css('display', 'block');

        $.post(ajaxurl, {'action': 'jobboard_update_applications', 'status' : status, 'id': id}, function(response) {
            tr.css('opacity', 1);
            tr.find('button').prop('disabled', false);
            loading.css('display', 'none');
            if(response != '') {
                tr.find('td.column-status').html(response);
            }
        });
    }

})( jQuery );