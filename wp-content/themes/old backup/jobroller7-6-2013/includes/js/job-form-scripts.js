/*
 * JobRoller job form jQuery functions
 * Written by AppThemes
 *
 * Copyright (c) 2010 AppThemes (http://appthemes.com)
 *
 * Built for use with the jQuery library
 * http://jquery.com
 *
 * Version 1.0
 *
 * Left .js uncompressed so it's easier to customize
 */

jQuery(document).ready(function($) {

	$.validator.messages.required = JR_i18n.required_msg;

	$("input[name=job_submit]").click(function() {
		$("#submit_form").validate();
	});

	function loadFormFields() {
		var data = {
			action: 'app-render-job-form',
			job_category: $(this).val()
		};

		$('#job-form-custom-fields').html('<img class="loading-custom-fields" src = "' + JR_i18n.loading_img + '"> ' + JR_i18n.loading_msg );

		$.post( JR_i18n.ajaxurl, data, function(response) {
			$( '#job-form-custom-fields' ).html( response );
		});
	}

	$( '#job_term_cat' )
		.change(loadFormFields)
		.find( 'option' ).eq(0).val(''); // needed for jQuery.validate()


});