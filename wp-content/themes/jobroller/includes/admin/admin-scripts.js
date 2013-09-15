/*
 * JobRoller admin jQuery functions
 * Written by AppThemes
 *
 * Copyright (c) 2010 App Themes (http://appthemes.com)
 *
 * Built for use with the jQuery library
 * http://jquery.com
 *
 * Version 1.2
 *
 */

// <![CDATA[

/* initialize the tooltip feature */
jQuery(document).ready(function(){

	jQuery("td.titledesc a").easyTooltip();
	
	/* upload logo and images */
	jQuery('.jobroller .upload_button').click(function() {
		formfield = jQuery(this).attr('rel');
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		return false;
	});

	/* send the uploaded image url to the field */
	window.send_to_editor = function(html) {
		imgurl = jQuery('img',html).attr('src'); // get the image url
		imgoutput = '<img src="' + imgurl + '" />'; //get the html to output for the image preview
		jQuery('#' + formfield).val(imgurl);
		jQuery('#' + formfield).siblings('.upload_image_preview').slideDown().html(imgoutput);
		tb_remove();
	}

});

