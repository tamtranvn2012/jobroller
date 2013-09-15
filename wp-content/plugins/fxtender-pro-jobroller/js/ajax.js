/* <![CDATA[ */

(function($){

		$(document).ready(function(){
				// qTip2 -->

				$('ol.jobs li.job:has(input.jr_fx_preview_pid)').live('mouseover', function(event) 
				{
					var existThumb = 0;
					var job_id = $('.jr_fx_preview_pid',this).val();
					var selector = $(this);
					
					($('.jr_fx_job_listing_thumb',this).html())?existThumb=1:existThumb=0;

					// conditionally load Ajax calls or get preview from the cache
					if ( ! $('#jr_fx_preview_cached_' + job_id ).length  ) {
						qTipAjaxObject = $.ajax ({
								 url: jr_fx_ajax.ajaxurl, // URL to the local file
								 type: 'POST', // POST or GET
								 data: { 
										action : 'jr_fx_qtip_callback',
										jobID: $('.jr_fx_preview_pid',this).val(),
										jobType: $('dd.type .jtype',this).text(),
										jobLocation: $('dd.location',this).text(),
										hasThumb: existThumb
								 }, // Data to pass along with your request
								success: function(data, status) {
									// Process the data
									// Set the content manually (required!)
									$(selector).qtip('option','content.text', data);
								 },
								error: function(request, status, error) {
									$(selector).qtip('option','content.text', 'Could not retrieve information...');
								}
						})
						$(this).qtip('option', 'ajax', qTipAjaxObject );
					} else {
						var content = $('#jr_fx_preview_cached_' + job_id );
						$(this).qtip('option', 'content.text', content );
					}

					// We make use of the .each() loop to gain access to each element via the "this" keyword...
					$(this).qtip(
					{ 
						overwrite: false, // Make sure the tooltip won't be overridden once created <--
						content: {
							// Set the text to an image HTML string with the correct src URL to the loading image you want to use
							text: '<img class="throbber" src="' + jr_fx_ajax.qTipThrobber + '" alt="Loading..." />',				
							title: {
								text: $('a:first',this).text(), // Give the tooltip a title using each elements text
								button: false
							}
						},
						position: {
							at: 'bottom center', // Position the tooltip bellow the link
							my: 'top center',
							target: $(this),
							viewport: $(window) // Keep the tooltip on-screen at all times
						},
						show: {
							event: event.type,
							ready: true,
							solo: true // Only show one tooltip at a time
						},
						hide: 'mouseleave',
						style: {
							classes: 'ui-tooltip-jr-fx' + ' ' + jr_fx_ajax.qTipColor + ' ' + jr_fx_ajax.qTipStyle
						}
					},event)
				});
				// qTip2 <--
		});

})(jQuery);

/* ]]> */
