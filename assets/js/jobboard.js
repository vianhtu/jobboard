(function( $ ) {
    "use strict";

    var scroll_top      = 0;
    var window_height   = 0;

    /**
     * Ordering.
     */
    $('.jobboard-archive-actions').on('change', '.archive-orderby', function () {
        submit_ordering_form();
    });

    /**
     * Widgets date filters.
     */
    $('.widget-date-filters').on('change', 'input[name="date-filters"]', function () {
        $('.jobboard-archive-actions').find('input[name="date-filters"]').remove();
        $('.jobboard-archive-actions').append('<input type="hidden" name="date-filters" value="' + $(this).val() + '" />');
        submit_ordering_form();
    });

    /**
     * Widgets specialism filters.
     */
    var specialism_modal = false,  specialism_timeout = false;

    /* add inputs to form. */
    $('.widget-specialism-filters').on('change', 'input[name="specialism-filters[]"]', function () {
        $('.jobboard-archive-actions').find('input[name="specialism-filters[]"]').remove();

        $('.widget-specialism-filters input[type="checkbox"]:checked').each(function () {
            $('.jobboard-archive-actions').append('<input type="hidden" name="specialism-filters[]" value="' + $(this).val() + '" />');
        });
        specialism_modal = true;
    });

    /* click to specialism inputs. */
    $('.widget-specialism-filters').on('click', '.widget-content > ul > li:not(:last-child)', function () {
        if(specialism_timeout == true){
            return
        }
        setTimeout(function(){submit_ordering_form()}, 1000);
        specialism_timeout = true;
    });

    /* click out side specialism modal. */
    $('.md-overlay').on('click', function () {
        if(specialism_modal == true) {
            submit_ordering_form();
        }
        specialism_modal = false;
    });

    /* click close specialism modal. */
    $('.widget-specialism-filters').on('click', '.md-close', function () {
        if(specialism_modal == true) {
            submit_ordering_form();
        }
        specialism_modal = false;
    });

    /* enter key to specialism. */
    $('.widget-specialism-filters').on('keypress',(function (e) {
        if (e.which == 13) {
            submit_ordering_form();
        }
    }));

    /* remove notice. */
    $('.jobboard-notices').on('click', '.remove', function () {
        $(this).parents('.messages').removeClass('active');
    });

    /**
     * fields
     */
    $('.jobboard-form select.select').select2({
        allowClear: true
    });

    $('.jobboard-form select.tags').select2({
        tags: true, tokenSeparators: [',', ' ']
    });

    $('.field-media').on('change', 'input', function (event) {

        var media       = $(this).parents('.field-media');
        var files       = event.target.files;
        var size        = parseInt($(this).data('size')) * 1024;
        var size_notice = $(this).data('size-notice');
        var type        = $(this).data('type').split(',');
        var type_notice = $(this).data('type-notice');

        if(!files[0]){
            return;
        }

        var extension = files[0].name.slice((Math.max(0, files[0].name.lastIndexOf(".")) || Infinity) + 1);

        if(type.length > 0 && $.inArray(extension, type) == -1){
            $(this).val('');
            jobboard_create_notices(type_notice, 'error');
            return;
        }

        if(size != NaN && files[0].size > size){
            $(this).val('');
            jobboard_create_notices(size_notice, 'error');
            return;
        }

        media.find('.file-name').html(files[0].name);

        if(files[0].type.search('image') != -1) {
            var file_part = window.URL.createObjectURL(files[0]);
            media.find('.file-thumb').html('<img src="' + file_part + '" alt="' + files[0].name + '">');
        }
    });

    $('.field-media').on('click', 'button', function () {
        $(this).parent().find('input[type="file"]').trigger('click');
    });

    /**
     * widgets layout actions.
     */
    var mouse_leave_widget = false;

    /* click widget title. */
    $('.jobboard-widget').on('click', '.jobboard-widget-title', function () {

        scroll_top = $(window).scrollTop();

        var content = $(this).parents('.jobboard-widget').find('.widget-click');

        if(content.length <= 0){
            return;
        }

        if(content.hasClass('active')) {
            content.removeClass('active');
        } else {
            hide_widget_other($(this));
            content.addClass('active');
        }
    });

    /* hover widget title. */
    $('.jobboard-widget').on('hover', '.jobboard-widget-title', function () {

        scroll_top = $(window).scrollTop();

        var content = $(this).parents('.jobboard-widget').find('.widget-hover');

        if(content.length <= 0){
            return;
        }

        hide_widget_other($(this));
        content.addClass('active');
    });

    /* mouse leave widget. */
    $('.jobboard-widget').on('mouseleave', function () {
        mouse_leave_widget = true;
    });

    /* mouse enter widget. */
    $('.jobboard-widget').on('mouseenter', function () {
        mouse_leave_widget = false;
    });

    /* out side widget click. */
    $('body').on('click', function (e) {
        if(mouse_leave_widget == true) {
            hide_widget_content();
        }
    });

    /**
     * window events.
     */
    $(window).on('scroll', function() {
        
        if(mouse_leave_widget == false){
            return;
        }

        /* scroll + - 100px. */
        if(scroll_top + 100 < $(window).scrollTop() || scroll_top - 100 > $(window).scrollTop()){
            hide_widget_content();
        }
    });

    $(window).on('load', function() {

        window_height = $( window ).height();

        jobboard_notices();
        validate_input();
    });

    $(window).on('resize', function() {
        window_height = $( window ).height();
    });

    /**
     * functions
     */
    function hide_widget_content() {
        $('.widget-content.active').removeClass('active');
    }

    function hide_widget_other(widget) {
        $('.widget-content.active').not(widget).removeClass('active');
    }
    
    function submit_ordering_form() {
        $('.jobboard-archive-actions').submit();
    }
    
    function validate_input() {

        if($('.field-validated').length < 1){
            return false;
        }

        $('html, body').animate({
            scrollTop: $(".field-validated:first-child").offset().top - (window_height / 2)
        }, 1000);
    }

})( jQuery );

function jobboard_confirm($content, callback) {
    var alert = jQuery('#jobboard-modal-alert');
    alert.find('.alert-content').html($content);
    alert.find('.alert-actions').css('display', 'none');
    alert.addClass('md-show');
    if(callback != undefined) {
        alert.find('.alert-actions').css('display', 'block');
        alert.on('click', '.alert-actions button', function () {
            if (jQuery(this).val() == '1') {
                callback();
            }
            alert.removeClass('md-show');
        });
    }
}

function jobboard_notices() {
    var messages = jQuery('.jobboard-notices');
    messages.find('> .messages:not(.messages-blank, .active)').each(function (index) {
        var msg = jQuery(this);
        setTimeout(function(){
            msg.addClass('active');
            setTimeout(function(){
                msg.removeClass('active');
                setTimeout(function(){
                    msg.remove();
                }, 2000);
            }, 5000 );
        }, index == 0 ? 0 : 2000);
    });
}

function jobboard_create_notices(message, status) {
    var notice = jQuery('.jobboard-notices .messages-blank').clone();
    notice.find('p').html(message);
    notice.removeClass('messages-blank').addClass('messages-' + status);
    notice.appendTo('.jobboard-notices');
    jobboard_notices();
}