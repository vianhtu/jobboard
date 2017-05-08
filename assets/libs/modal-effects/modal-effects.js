/**
 * modalEffects.js v1.0.0
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * Copyright 2013, Codrops
 * http://www.codrops.com
 */
jQuery(document).ready(function($) {
	"use strict";

	/* show modal. */
	$("body").on('click', '.md-trigger', function () {

		var modal = $(this).data('modal');

		$('#' + modal).addClass('md-show');
	});

	$("body").on('click', '.md-overlay', function () {
		$('.md-modal').removeClass('md-show');
	});

	$("body").on('click', '.md-close', function () {
		$(this).parents('.md-modal').removeClass('md-show');
	});
});