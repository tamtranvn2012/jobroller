/* <![CDATA[ */

	jQuery.noConflict();

	//JobRoller Extended Features Scripts

	jQuery(document).ready( function( $j ) {

		$j('.jr_fx_meta_application_show_all').click( function(event){
			$j('.jr_fx_meta_application').show().slideDown();
			$j('.jr_fx_meta_application_show_all').fadeOut('slow');
			event.preventDefault();
		});

	});

/* ]]> */