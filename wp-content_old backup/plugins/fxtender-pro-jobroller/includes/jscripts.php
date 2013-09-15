<?php
/**
 * Dynamic jQuery Functions
 */

function jr_fx_add_jquery () {
	global $current_user, $post, $posted, $app_abbr, $wp, $wp_query, $job_details;

	get_currentuserinfo();
?>
	<?php
		$linkedin_key = jr_fx_validate( '_text_integration_linkedin_key' );
		
		if ( $linkedin_key ) :
	?>
		<script type="text/javascript" src="http://platform.linkedin.com/in.js">
			api_key: <?php echo $linkedin_key; ?>
		</script>
	<?php
		endif;
	?>

	<script type="text/javascript">
	/* <![CDATA[ */

		jQuery.noConflict();

		//JobRoller Extended Features Scripts

		jQuery(document).ready(function($j) {

			<?php
			/*******************************************************************
			* Check for extra messages from JobRoller Extended
			********************************************************************/
			?>
			<?php 
				$notice = get_transient('jr-fx-notice');

				if ( $notice ) :
					delete_transient( 'jr-fx-notice' );
			?>
					var notice = "<?php echo substr($notice,0,1); ?>";
					// append extra notices after the page title <h1>
					<?php if ( substr($notice,0,1) == '_' ) { ?>
						$j('h1:first').before( "<?php echo jr_fx_parse_notices( jr_fx_validate( $notice ), $notice ); ?>" );
					<?php } else { ?>
						<?php if (  substr($notice,0,1) == '+' ) { ?>
							$j('h1:first').before( "<?php echo substr($notice,1); ?>" );
						<?php } else {?>
						$j('.inner #mainContent').prepend( "<?php jr_fx_show_notice( $notice ); ?>" );
						<?php } ?>
					<?php } ?>
			<?php
				else:
			?>
					$j('.jr_fx_notice').remove();
			<?php
				endif;
			?>

			<?php
			/*******************************************************************
			* Feature: Breadcrumbs
			********************************************************************/
			?>
			<?php
					$breadcrumbs_pos = jr_fx_validate( '_opt_site_breadcrumbs' );

					# append the breadcrumbs to the content
					if ( !is_home() && $breadcrumbs_pos && $breadcrumbs_pos != 'no' ) : 
			?>
						<?php if ( $breadcrumbs_pos == 'under' ) { ?>
							$j("div#mainContent > .section:first").before( "<?php jr_fx_feat_breadcrumb(); ?>" );
						<?php } else { ?>
							$j("div#mainContent").prepend( "<?php jr_fx_feat_breadcrumb(); ?>" );
						<?php } ?>	
			<?php
					endif;
			?>
			<?php
			/*******************************************************************
			* Feature: Hide buttons
			********************************************************************/
			?>
			<?php 
					# if the user is viewing a job page hide the selected buttons
					if( is_singular( JR_FX_JR_POST_TYPE ) ) {
						$hide_button = jr_fx_validate( '_opt_jobs_buttons' );

						if ( $hide_button != 'no' ) {
			?>
						<?php if ( $hide_button == 'apply' ) { ?>
								$j("li.apply, li.apply_online").hide();
						<?php } else {
									if ( $hide_button == 'print' ) { ?>
										$j("li.print").hide();
									<?php 
										} else if ( $hide_button == 'all' ){ ?>
											$j("#mainContent .section_footer").hide();	
											$j("li.print").hide();
											$j("li.apply, li.apply_online").hide();
									<?php } }
						}
					}
			?>

			<?php
			/*******************************************************************
			* Feature: Show 'Apply Online' button
			********************************************************************/
			?>
			<?php
					# if the user is viewing a job page hide the apply online button
					if( isset($post) && is_singular( JR_FX_JR_POST_TYPE ) ) : 
						$show_apply = jr_fx_validate( '_opt_jobs_apply_button' );

						# check for Indeed Jobs and give it priority to hide the 'Apply Online' button
						if ( get_post_meta( $post->ID, 'indeed_key', true ) ) : 
							
							$hide_apply_indeed = jr_fx_validate( '_opt_jobs_indeed_apply_button' );
							if ( $hide_apply_indeed && $hide_apply_indeed == 'yes' ) {
								 $show_apply = 'never';
							}
						endif;

						# if option is set get the jobpacks
						if ( $show_apply ) {
							$paid_job = jr_fx_is_paid_job( $post );
						}
			?>
						<?php if ( $show_apply && ( $show_apply == 'never' || ( $show_apply == 'jobpack' && ! $paid_job ) ) ) { ?>
								$j("li.apply, li.apply_online").hide();
						<?php }	else if ( $show_apply == 'always' ||  ( $show_apply == 'jobpack' && $paid_job ) ) { ?>
								$j("#mainContent .section_footer").show();
								$j("li.apply, li.apply_online").show();
						<?php }
					endif;
			?>
			<?php
			/*******************************************************************
			* Feature: Redirect visitors on 'Apply Online' 
			********************************************************************/
			?>
			<?php
					if( !is_user_logged_in() && is_singular(JR_FX_JR_POST_TYPE) ) {
						if ( jr_fx_validate('_opt_jobs_apply_visitors_redirect', 'yes') ) {
		?>
							var jr_fx_apply_redirect = "<?php echo wp_login_url( $_SERVER['REQUEST_URI'] ); ?>";
							$j("a.apply_online").unbind('click');
							$j("a.apply_online").click( function() {
								<!--
								window.location = jr_fx_apply_redirect;
								return false;
								//-->
							});
			<?php
						};
					};
			?>	

			<?php
			/*******************************************************************
			* Feature: Redirect free Job Seeker on 'Apply Online' 
			********************************************************************/
			?>
			<?php
					if( is_user_logged_in() && is_singular(JR_FX_JR_POST_TYPE) ) {
						if ( function_exists('jr_fx_validate_feat_s2member') && jr_fx_validate_feat_s2member( '_opt_jobs_apply_s2member_level', 'jscript', 's2member' ) ) :
							$redirect_url = jr_fx_validate( '_opt_resume_s2member_redirect' );

							if ( !$redirect_url ) 
								$redirect_url = get_bloginfo('url');
							else
								$redirect_url = get_permalink( $redirect_url );
			?>
							var jr_fx_apply_redirect = "<?php echo $redirect_url; ?>";
							$j("a.apply_online").unbind('click');
							$j("a.apply_online").click( function() {
								<!--
								window.location = jr_fx_apply_redirect;
								return false;
								//-->
							});
			<?php
						endif;
					};
			?>

			<?php
			/*******************************************************************
			* Feature: Apply with Registered Emails Only
			********************************************************************/
			?>
			<?php
					global $jr_fx_email_error, $jr_fx_invalid_email;

					if( isset($jr_fx_email_error) && is_singular(JR_FX_JR_POST_TYPE) && jr_fx_validate('_opt_jobs_apply_registered_email', 'yes') ) :
			?>
							$j("#apply_form_result.errors").append("<li><?php echo $jr_fx_email_error; ?></li>");
							$j("#your_email").val("<?php echo sprintf( __('[UNREGISTERED EMAIL: %s]', JR_FX_i18N_DOMAIN), $jr_fx_invalid_email) ?>");
			<?php
					endif;
			?>

			<?php
			/*******************************************************************
			* Feature: Hide Nav Widget
			********************************************************************/
			?>
			<?php
					# if the user is viewing a job page hide the apply online button
					if( is_singular(JR_FX_JR_POST_TYPE) && jr_fx_validate( '_opt_widget_nav','yes' ) ) :
			?>
						$j(".widget-nav").hide();
			<?php 
					endif;
			?>

			<?php
			/*******************************************************************
			* Feature: Hide 'Google Maps' / Ignore Geolocation
			********************************************************************/
			?>
			<?php 
					# if the user is submitting a job hide the google map
					if ( is_page( jr_fx_get_page_id( 'submit_page_id' ) ) || is_page( jr_fx_get_page_id( 'edit_job_page_id' ) ) ) {
						if ( jr_fx_validate('_opt_jobs_gmaps','yes') ) {
			?>
							$j("#geolocation-load, #geolocation-map, #map_wrap").hide();
							
							$j("#geolocation-address").keyup(function(e) {
								$j("#geolocation-load").remove();
							});
			<?php
							# replace the geolocation location for the custom location
							if ( get_query_var('job_id') ) {
								$jr_address =  get_post_meta( intval(get_query_var('job_id')), '_jr_address', true );
							}

							if ( !empty($jr_address) ):
			?>
								$j("ol.jobs li.job dd.location").html("<?php echo $jr_address; ?>");
			<?php
							endif;
						};
					}
			?>

			<?php 
					# Hide the radius on the search bar if the user disabled google maps
					if ( jr_fx_validate('_opt_jobs_gmaps','yes') ) {
			?>
						if ( $j(".radius").html() != undefined )
							$j(".radius").hide();
			<?php
					}
			?>

			<?php
			/*******************************************************************
			* Feature: Add Job Duration Field
			********************************************************************/
			?>
			<?php
					# if the user is submitting a job show the field to set the days until job expiration

					if ( jr_fx_display_job_duration_field() ):
						$jr_fx_caption = jr_fx_validate( '_text_jobs_duration_caption' );

						# get min and max days for this feature
						$min_days = jr_fx_validate( '_int_jobs_min_duration' );
						$max_days = jr_fx_validate( '_int_jobs_max_duration' );

						if ( isset($_POST['jr_fx_job_duration_field']) ) {
							$posted_duration = intval($_POST['jr_fx_job_duration_field']);
						} elseif( get_query_var('job_id') ) {
							$posted_duration = get_post_meta( (int)get_query_var('job_id'), '_jr_fx_expire_days', true );
						}

						if ( empty($posted_duration) ) $posted_duration = $max_days;
		?>
						if ( $j('form#submit_form input[name="confirm"]').html() != undefined ||
							 $j('form#submit_form input[name="job_submit"]').html() != undefined ) {

							var job_duration_field =
									"<div class='jr_fx_custom_field_div' sid='jr_fx_job_duration_field_container'><h2><?php _e('Job Duration',JR_FX_i18N_DOMAIN); ?></h2>" +
									"<input type='text' class='text expire_days jr_fx_custom_field' name='jr_fx_job_duration_field' id='jr_fx_job_duration_field' value='<?php echo esc_attr($posted_duration); ?>'>  " +
									"<p class='jr_fx_custom_field-'><?php echo ($jr_fx_caption?$jr_fx_caption:''); ?><br/>" +
									"<span class='jr_fx_job_duration_limit'><?php echo sprintf( __('Value must be between %s and %s.',JR_FX_i18N_DOMAIN),$min_days, $max_days); ?></span>"	+
									"</div>";

							$j('form#submit_form input[name=goback], form#submit_form input[name=job_submit]').before( job_duration_field );

							$j("input[name='confirm'], input[name='job_submit']" ).click( 
								function () { 
									if ( $j("#jr_fx_job_duration_field").val() == '' ) {
										alert('<?php _e('Job duration is a required field.',JR_FX_i18N_DOMAIN); ?>');
										return false;
									} else {
										if ( $j("#jr_fx_job_duration_field").val() <  <?php echo $min_days; ?>  || $j("#jr_fx_job_duration_field").val() >  <?php echo $max_days; ?> ) {
											alert('<?php _e('Job duration must be between '.$min_days. ' and '.$max_days.'.',JR_FX_i18N_DOMAIN); ?>');
											return false;
										}
									}
							});
						};

			<?php
					endif;
			?>

			<?php
			/*******************************************************************
			* Feature: Add Applications Email Field
			********************************************************************/
			?>
			<?php
					### if the user is submitting a job show the field to set the days until job expiration
					if ( jr_fx_display_application_email_field() ) : 

						if ( isset($_POST['jr_fx_field_email_applications']) ) {
							$app_email_field = intval($_POST['jr_fx_field_email_applications']);
						} elseif( get_query_var('job_id') ) {
							$app_email_field = get_post_meta( (int)get_query_var('job_id'), JR_FX_FIELDS_PREFIX . '_apps_recipient_address', true );
						}
						if ( empty($app_email_field) ) $app_email_field = $current_user->user_email;
			?>
						if ( $j('form#submit_form input[name="confirm"]').html() != undefined ||
						 	$j('form#submit_form input[name="job_submit"]').html() != undefined ) {

							var apps_email_field =
									"<div class='jr_fx_custom_field_div' id='jr_fx_field_email_apps_container'><h2><?php _e('Applications email address (admins only)',JR_FX_i18N_DOMAIN); ?></h2>" +
									"<input type='text' class='text email_applications jr_fx_custom_field' name='jr_fx_field_email_applications' id='jr_fx_field_email_applications' value='<?php echo esc_attr( $app_email_field ); ?>'>" +
									"<p class='jr_fx_custom_field-'><?php _e('This is the recipient email address that will receive applications for this job. Only an Admin can see this field.', JR_FX_i18N_DOMAIN); ?>" +
									"</div>";

							$j('form#submit_form input[name=goback], form#submit_form input[name="job_submit"]').before( apps_email_field );

							$j("input[name='confirm'], input[name='job_submit']").click( 
								function () {

									function isValidEmailAddress(emailAddress) {
										var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
										return pattern.test(emailAddress);
									}
									if ( !isValidEmailAddress( $j("#jr_fx_field_email_applications").val() ) ) {
										alert('<?php _e('Email address is invalid!',JR_FX_i18N_DOMAIN); ?>');
										return false;
									}
							});
						}
			<?php
					endif;
			?>
			<?php
			/*******************************************************************
			* Feature: Optional Apply Online
			********************************************************************/
			?>
			<?php
					# if the user is submitting a job show the field

					if ( jr_fx_display_optional_apply_online_field() ) :
						$jr_fx_optional_apply_caption = __('Yes', JR_FX_i18N_DOMAIN );
						$jr_fx_optional_apply_footer = __(' (Check this option if you want to avoid job seekers applying to jobs directly using our application form)', JR_FX_i18N_DOMAIN );

						if ( get_query_var('job_id') ) {
							$disable_apply_online = get_post_meta( intval( get_query_var('job_id') ), 'jr_fx_disable_apply_online', true );
						}

						if ( empty( $disable_apply_online ) ) $disable_apply_online = '';
			?>
							if ( $j('form#submit_form input[name="confirm"]').html() != undefined ||
								 $j('form#submit_form input[name="job_submit"]').html() != undefined ) {
							
								var disable_apply_field =
										"<div class='jr_fx_custom_field_div' sid='jr_fx_disable_apply_field_container'><h2><?php _e('Disable Apply Online',JR_FX_i18N_DOMAIN); ?></h2>" +
										"<input type='checkbox' class='text jr_fx_custom_field optional_apply_online' name='jr_fx_disable_apply_field' id='jr_fx_disable_apply_field' value='yes' <?php checked($disable_apply_online); ?> >  " +
										"<?php echo ($jr_fx_optional_apply_caption?$jr_fx_optional_apply_caption:''); ?>" +
										"<?php echo ($jr_fx_optional_apply_footer?$jr_fx_optional_apply_footer:''); ?>" +
										"</div><br/>";

								$j('form#submit_form input[name=goback], form#submit_form input[name=job_submit]').before( disable_apply_field )
							};
							 
			<?php		
					endif;
			?>	
			<?php
					# if the user is viewing a job page and the lister does not allow online applications

					$custom_optional_apply_field = jr_fx_validate( '_opt_jobs_optional_apply_online', 'yes' );

					if( isset($post) && is_singular(JR_FX_JR_POST_TYPE) && $custom_optional_apply_field ) : 
						$disable_apply_online = jr_fx_selective_disable('jr_fx_disable_apply_online', $post->ID);

						if ( $disable_apply_online ) {
			?>
							$j('.section_footer li.apply, .section_footer li.apply_online').remove();
			<?php
						}

					endif;
			?>
			<?php
			/*******************************************************************
			* Feature: Optional Apply With LinkedIn
			********************************************************************/
			?>
			<?php 
					# if the user is submitting a job show the field
					if ( jr_fx_display_optional_apply_linkedin_field() ) :
						$jr_fx_optional_apply_ld_caption = __('Yes', JR_FX_i18N_DOMAIN );
						$jr_fx_optional_apply_ld_footer = __(' (Check this option to disable the \'Apply with LinkedIn\' button)', JR_FX_i18N_DOMAIN );

						if ( get_query_var('job_id') ) {
							$disable_apply_ld_field = get_post_meta( intval( get_query_var('job_id') ), 'jr_fx_disable_apply_linkedin', true );
						}

						if ( empty( $disable_apply_ld_field ) ) $disable_apply_ld_field = '';
			?>
							if ( $j('form#submit_form input[name="confirm"]').html() != undefined ||
								 $j('form#submit_form input[name="job_submit"]').html() != undefined ) {
							
								var disable_apply_ld_field =
										"<div class='jr_fx_custom_field_div' sid='jr_fx_disable_apply_ld_field_container'><h2><?php _e('Disable \'Apply with LinkedIn\'',JR_FX_i18N_DOMAIN); ?></h2>" +
										"<input type='checkbox' class='text jr_fx_custom_field optional_apply_ld' name='jr_fx_disable_apply_ld_field' id='jr_fx_disable_apply_ld_field' value='yes' <?php checked($disable_apply_ld_field); ?> > " +
										"<?php echo ($jr_fx_optional_apply_ld_caption?$jr_fx_optional_apply_ld_caption:''); ?>" +
										"<?php echo ($jr_fx_optional_apply_ld_footer?$jr_fx_optional_apply_ld_footer:''); ?>" +
										"</div><br/>";

								$j('form#submit_form input[name=goback], form#submit_form input[name=job_submit]').before( disable_apply_ld_field )
							};
			<?php
					endif;
			?>

			<?php
			/*******************************************************************
			* Feature: Thumbs
			********************************************************************/
			?>
			<?php
				 if ( is_front_page() || ( isset($post) && $post->post_type == JR_FX_JR_POST_TYPE && !is_single() && !is_singular() )  ):
					$listing_logo = jr_fx_validate( '_opt_listings_logo' );

					if ( $listing_logo && $listing_logo != 'no' ) : 
							$logo_pos = jr_fx_validate( '_opt_listings_logo_pos' );
			?>
							$j('#sidebar .widget .jr_fx_temp_thumb').remove();
									$j('#sidebar .widget a').each( function() {
										var post_title = $j(this).attr("title");
										if ( post_title != undefined && post_title.indexOf("jr_fx") >= 0 ) {
											  $j(this).attr("title","");
										}
							});
			
							if ( $j('#mainContent .jobs li.job') != undefined ) {
								var sLogoPos = '<?php echo $logo_pos ?>';
								var bLogoFeat = <?php echo ($listing_logo == 'featured'?1:0); ?>;
								var bLogoJobpack = <?php echo ($listing_logo == 'jobpack'?1:0); ?>;
								var bLogoYes = <?php echo ($listing_logo == 'all'?1:0); ?>;
								var thumbs_width = <?php echo get_option('thumbnail_size_w', true); ?>;
								var pad_margin = ((thumbs_width)/2);
								var adjust_size = 5;

								var count_thumbs = 0;
								$j("#mainContent .jobs li.job").each( function () {

									if ( bLogoFeat || bLogoJobpack || bLogoYes ) {

										var imgThumb =  $j('.jr_fx_temp_thumb', $j(this));
										var has_thumb = false;

										var valid_thumb = ( ( $j('.jr_fx_job_listing_thumb', imgThumb).hasClass('jr_fx_paid') && bLogoJobpack )
															|| ( $j(this).hasClass('job-featured') && bLogoFeat ) 
															|| ( bLogoYes ) 
															);

										if ( sLogoPos == 'right' ) {

											$j(".location", $j(this)).addClass("jr_fx_list_thumbs location jr_fx_r");
											$j(".date", $j(this)).addClass("jr_fx_list_thumbs date jr_fx_r");
											$j(".title", $j(this)).addClass("jr_fx_list_thumbs title jr_fx_r");

											if ( valid_thumb) {
												if ( $j(".jr_fx_job_listing_thumb",imgThumb).html() != null ) {
													$j(".date",$j(this)).after( imgThumb );
													has_thumb = true;
													count_thumbs++;
												}
											}
											// dynamic thumb positioning and sizing >>
											var title_width = $j("dd.title", $j(this)).css('width');
											title_width = parseInt(title_width.replace('px',''));
											var cssTitle = {
												'width': title_width-thumbs_width + 'px'
											 };
											$j("dd.title", $j(this)).css( cssTitle );
											var location_width = $j("dd.location", $j(this)).css('width');
											location_width = parseInt(location_width.replace('px',''));
											var cssLocation = {
												'width': location_width+pad_margin-thumbs_width + 'px'
											 };
											$j("dd.location", $j(this)).css( cssLocation );
											// << dynamic thumb positioning and sizing 

											$j(".jr_fx_job_listing_thumb",$j(this)).addClass("jr_fx_r");

										} else if ( sLogoPos == 'left' ) { // left

											$j(".title", $j(this)).addClass("jr_fx_list_thumbs title jr_fx_l");
											$j(".location", $j(this)).addClass("jr_fx_list_thumbs location jr_fx_l");

											if ( valid_thumb ) {
												if ( $j(".jr_fx_job_listing_thumb",imgThumb).html() != null ) {
													$j(".type",$j(this)).after( imgThumb );
													has_thumb = true;
													count_thumbs++;
												}
											}

											$j(".jr_fx_job_listing_thumb",$j(this)).addClass("jr_fx_l");

											// dynamic thumb positioning and sizing >>
											var title_width = $j("dd.title", $j(this)).css('width');
											title_width = parseInt(title_width.replace('px',''));
											var cssTitle = {
												'width': title_width-thumbs_width + 'px',
												'padding-left':  (pad_margin/2)-adjust_size
											 };
											$j("dd.title", $j(this)).css( cssTitle );

											var location_width = $j("dd.location", $j(this)).css('width');
											location_width = parseInt(location_width.replace('px',''));
											var cssLocation = {
												'width': location_width+pad_margin-thumbs_width + 'px'
											 };
											$j("dd.location", $j(this)).css( cssLocation );
											// << dynamic thumb positioning and sizing

											//add padding to empty thumbs
											if ( !has_thumb ) { 
												$j('.jr_fx_list_thumbs',$j(this)).addClass('jr_fx_list_no_thumb'); 
												cssTitle = {
													'padding-left': thumbs_width+pad_margin-adjust_size + 'px'
												};
												$j('dd.title', $j(this)).css( cssTitle );
											}

										} else if ( sLogoPos == 'under' )  { // under

											if ( valid_thumb ) {
												if ( $j(".jr_fx_job_listing_thumb",imgThumb).html() != null ) {
													$j("dl",$j(this)).after( imgThumb );		
													has_thumb = true;
													count_thumbs++;

													// dynamic thumb positioning and sizing >>
													var type_width = $j("dd.type", $j(this)).css('width');
													type_width = parseInt(type_width.replace('px',''));
													var cssThumb = {
														'margin-left': ((type_width-thumbs_width)/2) + 'px'
													};
													$j(".jr_fx_temp_thumb",$j(this)).css( cssThumb );
													// dynamic thumb positioning and sizing <<
												}
											}

										} else if ( sLogoPos == 'collapse' )  { 

											if ( valid_thumb ) {
												if ( $j(".jr_fx_job_listing_thumb",imgThumb).html() != null ) {

													$j(".title", $j(this)).addClass("jr_fx_list_thumbs title jr_fx_l");
													$j(".type", $j(this)).after("<span class='jr_fx_thumbs_wrap'></span>");
													$j(".jr_fx_thumbs_wrap", $j(this)).append("<span class='jr_fx_thumbs_title_wrap'></span>");
													$j(".jr_fx_thumbs_title_wrap", $j(this)).append( $j("dd.title", $j(this)) );
													$j(".jr_fx_thumbs_title_wrap", $j(this)).after("<br/><br/><span class='jr_fx_thumbs_collapsed_wrap'></span>");
													$j(".jr_fx_thumbs_collapsed_wrap", $j(this)).append( $j("dd.location", $j(this)) );
													$j(".jr_fx_thumbs_collapsed_wrap", $j(this)).append( $j("dd.date", $j(this)) );
													$j(".jr_fx_job_listing_thumb",$j(this)).addClass("jr_fx_l");

													// dynamic thumb positioning and sizing >>
													var title_width = $j("dd.title", $j(this)).css('width');
													title_width = parseInt(title_width.replace('px',''));
													var cssTitle = {
														'width': title_width-thumbs_width + 'px',
														'padding-left': (pad_margin/2)-adjust_size + 'px'
													};
													$j("dd.title", $j(this)).css( cssTitle );
													var location_width = $j("dd.location", $j(this)).css('width');
													location_width = parseInt(location_width.replace('px',''));
													var cssLocation = {
														'width': ((location_width+title_width)-thumbs_width-pad_margin+adjust_size) + 'px',
														'padding-left': (pad_margin/2)-adjust_size + 'px'
													};
													$j("dd.location", $j(this)).css( cssLocation );
													// << dynamic thumb positioning and sizing

													$j(".type",$j(this)).after( imgThumb );
													has_thumb = true;
													count_thumbs++;
												}
											}
										}
										//cleanup
										if ( !valid_thumb && $j(".jr_fx_job_listing_thumb",imgThumb).html() != null ) {
											 $j(".jr_fx_job_listing_thumb",imgThumb).remove();
										}
									} // logo options	
								});
							}
							//if there are no thumbs remove the padding
							if ( count_thumbs == 0 ) { 
								$j('*').removeClass('jr_fx_list_thumbs jr_fx_list_no_thumb  jr_fx_l jr_fx_r');
								$j('dd.title, dd.location, dd.date').removeAttr('style');
								$j('.jr_fx_temp_thumb',$j(this)).remove();
							} else {
								$j('.jr_fx_temp_thumb').show();
								<?php
									# check for transient featured jobs for clearing
									delete_transient ('jr-fx-featured');
								?>
							}
			<?php
					endif;
				endif;
			?>

			<?php
			/*******************************************************************
			* Feature: Replace Date with Days Left
			********************************************************************/
			?>

			<?php
				$days_expire = jr_fx_validate( '_opt_listings_days_left' );
				echo "var days_expire = '$days_expire';";
				if ( $days_expire ) :
					if ( ( $days_expire == 'all' || $days_expire == 'listing' ) && !is_single() && !is_singular() ) :
			?>	
					if ( $j('#mainContent .jobs li.job') != undefined ) {
						$j('.jr_fx_days_left_pid').each( function() {
								$j('dd.date',$j(this).parents('li')).html('');
								$j('dd.date',$j(this).parents('li')).html( '<strong class="jr_fx_days_left">'+$j(this).val()+'</strong>' );
						});
					};
			<?php
					else:
						if ( ( $days_expire == 'job' || $days_expire == 'all' ) && ( isset($post) && $post->post_type == JR_FX_JR_POST_TYPE && (is_single() || is_singular() ) ) ) :
			?>
							if ( $j('.section.single .section_header .date') != undefined ) {
									$j('.section.single .section_header .date').html( '<strong class="jr_fx_days_left">'+$j('.jr_fx_days_left_pid').val()+'</strong>' );
							};
			<?php	
						endif; //job
					endif; //listings	
				endif; 
			?>
			<?php
			/*******************************************************************
			* Feature: Google+
			********************************************************************/
			?>
			<?php
				$google_id = jr_fx_validate( '_opt_widget_google_plus_id' );
				if ( $google_id ) : 
				?>
					var google_social ='<li class="jr_fx_social jr_fx_social_buzz"><a href="https://plus.google.com/u/0/<?php echo $google_id ?>" title="<?php _e('Follow us', JR_FX_i18N_DOMAIN); ?>"><?php _e("Follow us", JR_FX_i18N_DOMAIN); ?></a><br/>'+
								 '<span><?php _e('Come join us on Google+', JR_FX_i18N_DOMAIN) ?></span></li>';

					$j(".widget_jr_social ul").append( google_social );

			<?php
				endif;
			?>

			<?php
			/*******************************************************************
			* Feature: YouTube
			********************************************************************/
			?>
			<?php
				$youtube_id = jr_fx_validate( '_opt_widget_youtube_id' );
				if ( $youtube_id ) : 
				?>			
					var youtube_social ='<li class="jr_fx_social jr_fx_social_youtube"><a href="http://www.youtube.com/user/<?php echo $youtube_id ?>" title="<?php _e('Subscribe Channel', JR_FX_i18N_DOMAIN); ?>"><?php _e("Subscribe Channel", JR_FX_i18N_DOMAIN); ?></a><br/>'+
								 '<span><?php _e('Check our videos on YouTube', JR_FX_i18N_DOMAIN) ?></span></li>';

					$j(".widget_jr_social ul").append( youtube_social );
			<?php
				endif;
			?>	
			<?php
			/*******************************************************************
			* Feature: FB
			********************************************************************/
			?>
			<?php
				$fb_id = get_option( 'jr_facebook_id' );
				if ( jr_fx_validate( '_opt_widget_facebook', 'yes' ) && $fb_id ) : 
				?>
					var fb_social ='<li class="jr_fx_social jr_fx_social_fb"><a href="http://www.facebook.com/<?php echo $fb_id ?>" title="<?php _e('Be our Friend on Facebook', JR_FX_i18N_DOMAIN); ?>"><?php _e('Be Our Friend', JR_FX_i18N_DOMAIN); ?></a><br/>'+
								 '<span><?php _e('Be Our Friend on Facebook', JR_FX_i18N_DOMAIN) ?></span></li>';

					$j(".widget_jr_social ul").append( fb_social );
			<?php
				endif;
			?>
			<?php
			/*******************************************************************
			* Feature: Gateway Select
			********************************************************************/
			?>

			<?php
				# if the user is submitting a job show the field to set the days until job expiration
				if ( ! current_theme_supports('app-payments') && JR_FX_VERSION != JR_FX_VER_FREE && function_exists('jr_fx_show_gateway_select') && jr_fx_show_gateway_select() ):

					$charge_featured_jobs = 0;
					if (function_exists( 'jr_fx_validate_feat_jobs_free_offer' )):
						$offer_job_listing = jr_fx_validate_feat_jobs_free_offer( 'jQuery', 'hide payment text' ); 
						if ($offer_job_listing) $charge_featured_jobs = jr_fx_free_listing_featured_cost();
					endif;

					if (!$offer_job_listing || $charge_featured_jobs > 0):

						//selected gateways
						$show_paypal = get_option( $app_abbr .'_jobs_paypal_email' );
						$show_google = jr_fx_validate( '_opt_gateway_google','yes' );
						$show_2checkout = jr_fx_validate( '_opt_gateway_2checkout','yes' );
						$show_authorize = jr_fx_validate( '_opt_gateway_authorize','yes' );
						$show_manual = jr_fx_validate( '_opt_gateway_manual','yes' );

						if ( $show_google || $show_2checkout || $show_authorize || $show_manual || $show_paypal ) :
				?>

						// payment type
						var new_listing = $j('form#submit_form input[name="confirm"]').html() != undefined;
						var relisting = $j('form#submit_form input[name="relist"]').val() == 'true';
						var pending_pay = $j('#pending td.actions a[href*="pay_for_listing"]').html() != undefined;
						//JR 1.5.x
						var subscribe_resume = $j('.section p.button:last').html() != undefined;

						if ( new_listing || relisting || pending_pay || subscribe_resume ) {

							var gateways_select = "";
							<?php if ( jr_fx_validate( '_opt_gateway_display','dropdown' )) {?>
								gateways_select = 
										"<h2><?php _e('Payment Gateway',JR_FX_i18N_DOMAIN) ?></h2><div id='jr_fx_gateway_container'>" +
										"<a name='jr_fx_book_gateway'></a><div style='padding: 5px;'>" + 
											"<span><?php _e('Please choose your preferred payment gateway below:',JR_FX_i18N_DOMAIN) ?></span><p/>" +
											"<select name='jr_fx_gateway'>" +
												<?php if ( $show_paypal ) { ?>
												"<option value='paypal'>Paypal</option>" +
												<?php } // end show_paypal ?>
												<?php if ( $show_google ) { ?>
												"<option value='google'>Google Checkout</option>" +
												<?php } // end show_google ?>
												<?php if ( $show_authorize ) { ?>
												"<option value='authorize'>Authorize.net</option>" +
												<?php } // end show_authorize ?>
												<?php if ( $show_2checkout ) { ?>
												"<option value='2checkout'>2Checkout</option>" +
												<?php } // end show_2checkout ?>
												<?php if ( $show_manual ) { ?>
												"<option value='manual'>Manual</option>" +
												<?php } // end show_manual ?>
											"</select>" +
										"</div></div>";
							<?php } else{ ?>
								gateways_select = 
										"<h2><?php _e('Payment Gateway',JR_FX_i18N_DOMAIN) ?></h2><div id='jr_fx_gateway_container'>" +
										"<a name='jr_fx_book_gateway'></a><div style='padding: 5px;'>" + 
											"<span><?php _e('Please choose your preferred payment gateway below:',JR_FX_i18N_DOMAIN) ?></span><p/>" +
											<?php if ( $show_paypal ) { ?>
											"<span class='jr_fx_gateway_input'><input type='radio' name='jr_fx_gateway' value='paypal' checked='checked' /><img title='PayPal' src='<?php echo JR_FX_PLUGIN_URL ?>images/paypal.gif'></span>" +
											<?php } // end show_paypal ?>
											<?php if ( $show_google ) { ?>
											"<span class='jr_fx_gateway_input left'><input type='radio' name='jr_fx_gateway' value='google' checked='checked'/><img  title='Google Checkout' src='<?php echo JR_FX_PLUGIN_URL ?>images/googlecheckout.gif'></span>" +
											<?php } // end show_google ?>
											<?php if ( $show_authorize ) { ?>
											"<span class='jr_fx_gateway_input left'><input type='radio' name='jr_fx_gateway' value='authorize' checked='checked'/><img  title='Authorize.Net' src='<?php echo JR_FX_PLUGIN_URL ?>images/authorizenet.gif'></span>" +
											<?php } // end show_authorize ?>
											<?php if ( $show_2checkout ) { ?>
											"<span class='jr_fx_gateway_input left'><input type='radio' name='jr_fx_gateway' value='2checkout' checked='checked'/><img title='2Checkout' src='<?php echo JR_FX_PLUGIN_URL ?>images/2co.gif'></span>" +
											<?php } // end show_2checkout ?>
											<?php if ( $show_manual ) { ?>
											"<span class='jr_fx_gateway_input left'><input type='radio' name='jr_fx_gateway' value='manual' checked='checked'/><img title='Manual Payment' src='<?php echo JR_FX_PLUGIN_URL ?>images/manual.png'></span>" +
											<?php } // end show_manual ?>
										"</div></div>";
							<?php } ?>
							
							if ( new_listing ) {
								$j('form#submit_form input.goback').before( gateways_select );
							} else
								if ( relisting ) {
									$j('form#submit_form input.submit').before( gateways_select );
								} else
									if ( pending_pay ) {
										$j('div.myjobs').after( gateways_select );

										$j("#pending.myjobs_section table th:last").append("<a class='jr_fx_choose_gateway' href='#jr_fx_book_gateway'><br/><?php _e('(choose gateway)',JR_FX_i18N_DOMAIN); ?></a>");										
										
										$j('#pending td.actions a[href*="pay_for_listing"]').mouseover( function(){
											jr_fx_href = $j(this).attr("href");
											var href = jr_fx_href;
											
											if ( href.indexOf("pay_for_listing") >= 0 ) {
												var gateway_sel = $j("#jr_fx_gateway_container input[name='jr_fx_gateway']:checked").val();

												if (!gateway_sel || gateway_sel == undefined) {
													gateway_sel = $j("#jr_fx_gateway_container select[name='jr_fx_gateway']").val();
												}
												$j(this).attr("href",href + "&jr_fx_gateway="+gateway_sel);
											}
										}).mouseout(function(){
												$j(this).attr("href",jr_fx_href);
										});
									} else 
										if ( subscribe_resume ) {
											$j('.section').after( gateways_select );
									}

						}
				<?php
						endif; //show_gateways
					endif; //rules
				endif; //post	
			?>
			<?php
			/*******************************************************************
			* Feature: Job Seeker Auto Complete
			********************************************************************/
			?>
			<?php
					# job seeker auto complete
					$auto_complete_js = jr_fx_validate( '_opt_jobs_seeker_auto_complete' );

					# check if the user is on a job page and the feature is selected
					if( is_singular(JR_FX_JR_POST_TYPE) && $auto_complete_js != 'no' && is_user_logged_in() ) :
			?>
						var auto_complete_js = '<?php echo $auto_complete_js ?>';
						// check if the apply form exist and process the option accordingly
						if ( $j("#apply_form").html() != null ) {

							$j(".jr_fx_auto_complete").live('click',function() {
								$j("#apply_form #your_name").val("<?php echo $current_user->display_name; ?>");
								$j("#apply_form #your_email").val("<?php echo $current_user->user_email; ?>");
								return false;
							});

							$j("#apply_form > h2").after("<a title='<?php _e('Auto complete name and email',JR_FX_i18N_DOMAIN);  ?>' class='jr_fx_auto_complete' href='#'><?php _e('Auto complete name and email',JR_FX_i18N_DOMAIN); ?></a>");
							if ( auto_complete_js == 'always' ) {
								$j(".jr_fx_auto_complete").trigger('click');
								$j(".jr_fx_auto_complete").hide();
							}

						}
					
			<?php 
					endif;
			?>
			<?php
			/*******************************************************************
			* Feature: Job Lister Auto Complete
			********************************************************************/
			?>
			<?php
					# job seeker auto complete
					$auto_complete_jl = jr_fx_validate( '_opt_jobs_lister_auto_complete' );

					# check if the user is on a job page and the feature is selected
					if ( is_page( jr_fx_get_page_id( 'submit_page_id' ) ) && $auto_complete_jl != 'no' ) :
			?>
						var auto_complete_jl = '<?php echo $auto_complete_jl ?>';
						// check if the apply form exist and process the option accordingly
						if ( $j(".jr_job_submit #submit_form #job_title").html() != undefined ||
							 $j(".jr_job_submit #submit_form #post_title").html() != undefined ) {

							$j(".jr_fx_auto_complete").live('click',function() {
								$j("#submit_form #your_name").val("<?php echo $current_user->nickname; ?>");
								$j("#submit_form #website").val("<?php echo $current_user->user_url ; ?>");
								return false;
							});

							$j("#submit_form > fieldset:first").prepend("<a title='<?php _e('Auto complete Company details',JR_FX_i18N_DOMAIN);  ?>' class='jr_fx_auto_complete' href='#'><?php _e('Auto complete Company details',JR_FX_i18N_DOMAIN); ?></a>");
							if ( auto_complete_jl == 'always' ) {
								$j(".jr_fx_auto_complete").trigger('click');
								$j(".jr_fx_auto_complete").hide();
							}

						}
					
			<?php 
					endif;
			?>

			<?php
			/*******************************************************************
			* Feature: Uploaded CV's
			********************************************************************/
			?>
			<?php
				if ( is_page( jr_fx_get_page_id('dashboard_page_id') ) && !current_user_can('can_submit_job') ):
					if ( function_exists('jr_fx_validate_feat_cvs_upload') && jr_fx_validate_feat_cvs_upload() ):
			?>
					$j("#resumes form.submit_form.main_form").append($j("#jr_fx_dashboard_cv_list_temp").html());

					$j("#jr_fx_dashboard_cv_list_temp").remove()

					$j("#jr_fx_upload_cv_file").change( function() {
						$j(".jr_fx_submit_cv_form").submit();
						return;
					});

					$j(".delete_cv").click( function () {
						var anwser = confirm('<?php _e("Are you sure you want to delete this Resume file?" ,JR_FX_i18N_DOMAIN)?>');
						if ( anwser ) {
							$j(".jr_fx_list_cv_form").submit();
							return;
						}
						return false;
					});

			<?php
					endif;
				endif;
			?>
			<?php
				if ( is_singular(JR_FX_JR_POST_TYPE) && !current_user_can('can_submit_job') && function_exists('jr_fx_validate_feat_cvs_upload') && jr_fx_validate_feat_cvs_upload() ):
			?>
					$j("#apply_form form #your_cv").after($j("#jr_fx_preset_cv_list").html());
			<?php
				endif;
			?>
			<?php
			/*******************************************************************
			* Feature: S2Member - Job Seeker Dashboard
			********************************************************************/
			?>	
			<?php
				# integrate S2Member User Role in the User sidebar Info
				if ( jr_fx_get_page_id('dashboard_page_id') && !current_user_can('can_submit_job') && JR_FX_S2MEMBER_EXIST && jr_fx_validate( '_opt_s2member_restrict_access','yes' ) ) :

					$account_upgrade_text =  __('upgrade your Account to activate this option',JR_FX_i18N_DOMAIN);
			?>	
					$j('.widget_user_info li:nth-child(2)').html('<strong><?php _e('Account type:','appthemes')?></strong> <?php echo S2MEMBER_CURRENT_USER_ACCESS_LABEL; ?>');

					<?php 
						$member_page = jr_fx_validate( '_opt_resume_s2member_member_page' );
						if ( $member_page ) :
					?>
							$j('.widget_user_options li:nth-child(1)').after("<li><a href='<?php echo get_permalink( $member_page ); ?>'><?php echo get_the_title( $member_page ); ?></a></li>");

					<?php endif;?>
					<?php 
						# show call to action upgrade icon
						if ( function_exists('jr_fx_validate_feat_s2member') && jr_fx_validate_feat_s2member( '_opt_resume_s2member_level', 'jscript', 's2member' ) ) : 
								$upgrade_page = get_permalink( jr_fx_validate( '_opt_resume_s2member_redirect') );
					?>
								$j('#resumes h2:first').before("<a title='<?php echo $account_upgrade_text; ?>' href='<?php echo $upgrade_page; ?>'><div class='jr_fx_call_to_action_icon jr_fx_s2_level<?php echo jr_fx_validate('_opt_resume_s2member_level'); ?>'></div></a>");
								$j('#resumes h2:first').append("<a class='jr_fx_upgrade_acccount' title='<?php echo $account_upgrade_text; ?>' href='<?php echo $upgrade_page; ?>'> (<?php echo $account_upgrade_text; ?>)</a>");
								$j('#prefs h2:first').before("<a title='<?php echo $account_upgrade_text; ?>' href='<?php echo $upgrade_page ?>'><div class='jr_fx_call_to_action_icon jr_fx_s2_level<?php echo jr_fx_validate('_opt_resume_s2member_level'); ?>'></div></a>");
								$j('#prefs h2:first').append("<a class='jr_fx_upgrade_acccount' title='<?php echo $account_upgrade_text; ?>' href='<?php echo $upgrade_page; ?>'> (<?php echo $account_upgrade_text; ?>)</a>");
					<?php 
						endif; 
					?>
					<?php
						# show call to action upgrade icon
						if ( function_exists('jr_fx_validate_feat_s2member') && jr_fx_validate_feat_s2member( '_opt_resume_upload_s2member_level', 'jscript', 's2member' ) ) : 
								$upgrade_page = get_permalink( jr_fx_validate( '_opt_resume_s2member_redirect') );
					?>	
								$j('#jr_fx_dashboard_cv_list h2').before("<a title='<?php echo $account_upgrade_text ?>' href='<?php echo $upgrade_page; ?>'><div class='jr_fx_call_to_action_icon jr_fx_s2_level<?php echo jr_fx_validate('_opt_resume_upload_s2member_level'); ?>'></div></a>");
								$j('#jr_fx_dashboard_cv_list h2').append("<a class='jr_fx_upgrade_acccount' title='<?php echo $account_upgrade_text; ?>' href='<?php echo $upgrade_page; ?>'> (<?php echo $account_upgrade_text; ?>)</a>");
					<?php 
						endif; 
					?>
					<?php
						# show call to action upgrade icon on Job Alerts
						if ( function_exists('jr_fx_validate_feat_s2member') && jr_fx_validate_feat_s2member( '_opt_job_alerts_s2member_level', 'jscript', 's2member' ) ) :
								$upgrade_page = get_permalink( jr_fx_validate( '_opt_resume_s2member_redirect') );
					?>
								$j('#alert_status option[value="active"]').remove();
								$j('#alerts h2:first').before("<a title='<?php echo $account_upgrade_text ?>' href='<?php echo $upgrade_page; ?>'><div class='jr_fx_call_to_action_icon jr_fx_s2_level<?php echo jr_fx_validate('_opt_job_alerts_s2member_level'); ?>'></div></a>");
								$j('#alerts h2:first').append("<a class='jr_fx_upgrade_acccount' title='<?php echo $account_upgrade_text; ?>' href='<?php echo $upgrade_page; ?>'> (<?php echo $account_upgrade_text; ?>)</a>");
					<?php 
						endif; 
					?>
			<?php
				endif;
			?>

			<?php
			/*******************************************************************
			* Feature: Download CV attachments
			********************************************************************/
			?>

			<?php
					# check if the user is on a resume page and the feature is enabled
					if( is_singular(JR_FX_JR_RESUME) && function_exists('jr_fx_validate_feat_cvs_download') && jr_fx_validate_feat_cvs_download() ) :
			?>		
						if ( $j("#jr_fx_download_cv_list") ) {
							<?php if ( jr_fx_validate( '_opt_resume_cv_download' , 'header' ) ) : ?>
								$j(".resume_header p.meta").after($j("#jr_fx_download_cv_list"));
							<?php else: ?>
								$j(".section_content .resume_section:last").after($j("#jr_fx_download_cv_list"));
							<?php endif; ?>

							$j("#jr_fx_download_cv_list").show();
						}
			<?php
					endif;
			?>
			<?php
			/*******************************************************************
			* Feature: Apply with LinkedIn
			********************************************************************/
			?>
			<?php
				if( is_singular(JR_FX_JR_POST_TYPE) && function_exists('jr_fx_validate_feat_linkedin_apply') && jr_fx_validate_feat_linkedin_apply() ) : 
			?> 
					var obj_linkedin_apply = $j("#jr_fx_linkedin_apply");
					$j("#jr_fx_linkedin_apply").remove();
					$j(".section_footer").append( obj_linkedin_apply );
					$j("#jr_fx_linkedin_apply").show();
			<?php
				endif;
			?>
			<?php
			/*******************************************************************
			* Feature: Redirect free Job Seeker on 'Apply with LinkedIn' 
			********************************************************************/
			?>
			<?php
					if( is_user_logged_in() && is_singular(JR_FX_JR_POST_TYPE) ) {
						if ( function_exists('jr_fx_validate_feat_s2member') && jr_fx_validate_feat_s2member( '_opt_jobs_apply_linkedin_s2member_level', 'jscript', 's2member' ) ) :

							$redirect_url = jr_fx_validate( '_opt_resume_s2member_redirect' );
							
							if ( !$redirect_url ) 
								$redirect_url = get_bloginfo('url');
							else
								$redirect_url = get_permalink( $redirect_url );
			?>
							var jr_fx_apply_redirect = "<?php echo $redirect_url; ?>";
							$j("#jr_fx_linkedin_apply").wrap('<a class="jr_fx_redirect">');
							
							$j("a.jr_fx_redirect").live('click', function() {
								<!--
								window.location = jr_fx_apply_redirect;
								return false;
								//-->
							});
			<?php
						endif;
					};
			?>
			<?php
			/*******************************************************************
			* Feature: LinkedIn Profile
			********************************************************************/
			?>
			<?php
				if( is_author() && function_exists('jr_fx_validate_feat_linkedin_profile') && jr_fx_validate_feat_linkedin_profile() ) :
					$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
					$role = jr_fx_validate( '_opt_integration_linkedin_profile');
			?>
					var obj_linkedin_profile = $j("#jr_fx_linkedin_profile");

					$j("#jr_fx_linkedin_profile").remove();
					
					<?php if ( $role == 'members' || 
							  ( $role == 'job_lister' && user_can($curauth->ID, 'can_submit_job') ) ||
							  ( $role == 'job_seeker' && !user_can($curauth->ID, 'can_submit_job') )
							  ) : ?>

						$j("li.linkedin").append( obj_linkedin_profile );

						$j("#jr_fx_linkedin_profile").show();	
					<?php endif; ?>

			<?php
				endif;
			?>
			<?php
			/*******************************************************************
			* Feature: LinkedIn Profile Resume
			********************************************************************/
			?>
			<?php
				if( is_singular(JR_FX_JR_RESUME) && function_exists('jr_fx_validate_feat_linkedin_profile_resume') && jr_fx_validate_feat_linkedin_profile_resume() ) : 
			?>
					var obj_linkedin_profile_resume = $j("#jr_fx_linkedin_profile_resume");
					$j("#jr_fx_linkedin_profile_resume").remove();
					$j(".resume_header p.meta").after( obj_linkedin_profile_resume );
					$j("#jr_fx_linkedin_profile_resume").show();
			<?php
				endif;
			?>
			<?php
			/*******************************************************************
			* Feature: LinkedIn Profile Job
			********************************************************************/
			?>
			<?php
				if( is_singular(JR_FX_JR_POST_TYPE) && function_exists('jr_fx_validate_feat_linkedin_profile_job') && jr_fx_validate_feat_linkedin_profile_job() ) : 
			?>
					var obj_linkedin_profile_job = $j("#jr_fx_linkedin_profile_job");
					$j("#jr_fx_linkedin_profile_job").remove();
					$j(".section_header p.meta").after( obj_linkedin_profile_job );
					$j("#jr_fx_linkedin_profile_job").show();
			<?php
				endif;
			?>	
			<?php
			/*******************************************************************
			* Feature: Company Logo / Company Logo Gravatar
			********************************************************************/
			?>
			<?php
				// Replace
				if( is_author() && function_exists('jr_fx_validate_feat_persistent_logo_gravatar') && jr_fx_validate_feat_persistent_logo_gravatar('jquery') ) : 

					$logo = get_user_meta( $post->post_author, 'company-logo', true );
					$logo = jr_fx_resize_logo( $logo );

					if ($logo):
			?>
						$j('.avatar').attr( {
							'src' : '<?php echo $logo['company-logo']; ?>',
							'width' : '<?php echo $logo['width']; ?>',
							'height' : '<?php echo $logo['height']; ?>'
						});
			<?php
					endif;
				endif;
			?>
			<?php
			/*******************************************************************
			* Feature: Persistent Logos
			********************************************************************/
			?>
			<?php
				if( is_page('profile') && function_exists('jr_fx_validate_feat_persistent_logo') && jr_fx_validate_feat_persistent_logo() ) :
			?>
					// add the enctype attribute dinamically to the form so we can upload images
					$j('#your-profile').attr('enctype','multipart/form-data');
			<?php
				endif;
			?>
			<?php
				// New/Edit Jobs
				if ( jr_fx_display_company_logo() ) :

					$logo = '';

					// check for already posted logo
					if ( get_query_var('job_id') ) {
						$job_id = intval( get_query_var('job_id') );
					}

					if ( ! empty($job_id) ):
						$logo = wp_get_attachment_image_src( get_post_thumbnail_id( $job_id ), array( JR_FX_COMPANY_LOGO_SIZE_W , JR_FX_COMPANY_LOGO_SIZE_H ) );
						if ( $logo && !empty($logo[1]) && !empty($logo[2]) ) {
							$logo = jr_fx_resize_logo( $logo, $logo[1], $logo[2] );
							$logo['company-logo'] = $logo[0];
						}
					endif;

					// get the logo from the user profile only if they are submitting new listings
					if ( ! $logo && ( is_page( jr_fx_get_page_id( 'submit_page_id' ) ) || is_page( jr_fx_get_page_id( 'edit_job_page_id' ) ) ) ):
						$logo = get_user_meta( $current_user->ID, 'company-logo', true );
						if ( ! jr_fx_check_logo_exists($logo) ) 
							$logo = '';
						else
							$logo = jr_fx_resize_logo( $logo );
					endif;
			?>
					var default_logo = "<?php echo (isset($_POST['jr_fx_def_company_logo'])?'yes':''); ?>";
					var logo = "<?php echo ($logo?'yes':''); ?>";

					var logo_w = "<?php echo (!empty($logo['width'])?$logo['width']:JR_FX_COMPANY_LOGO_SIZE_W); ?>";
					var logo_h = "<?php echo (!empty($logo['height'])?$logo['height']:JR_FX_COMPANY_LOGO_SIZE_H); ?>";

					if ( $j("input[name='job_submit']") ) {

						var def_thumb_w = "<?php echo JR_FX_COMPANY_LOGO_SIZE_W; ?>";
						var def_thumb_h = "<?php echo JR_FX_COMPANY_LOGO_SIZE_H; ?>";

						var thumb_w = def_thumb_w;
						var thumb_h = def_thumb_h;

						if ( parseInt(logo_w) <= parseInt(thumb_w) ) {
							thumb_w = logo_w;
							thumb_h = logo_h;
						}

						$j('#website').after("<legend class='jr_fx_logo_legend'><?php _e('Company Logo', JR_FX_i18N_DOMAIN); ?></legend><div class='jr_fx_def_company_logo_wrapper'><div class='jr_fx_company_logo_placeholder'><img class='jr_fx_company_logo_image' /></div></div>");
						$j('.jr_fx_company_logo_placeholder').after("<div class='jr_fx_def_company_logo'><input type='checkbox' name='jr_fx_def_company_logo' id='jr_fx_def_company_logo'/><span><?php echo __('Set as default Company Logo', JR_FX_i18N_DOMAIN) ?></span></div>");
						$j('.jr_fx_company_logo_placeholder').before('<div class="jr_fx_delete_logo_wrapper"><a class="jr_fx_delete_logo" href="#" title="<?php _e('Delete Logo',JR_FX_i18N_DOMAIN) ?>">x</a></div>');
						
						$j('.jr_fx_company_logo_placeholder').css( { 'width' : def_thumb_w, 'height' : def_thumb_h } );
						$j('.jr_fx_company_logo_image').attr({
							'width' : thumb_w,
							'height' : thumb_h,
							'src' : '<?php echo ( !empty($logo['company-logo']) ? esc_attr( $logo['company-logo'] ) : ''); ?>'
						});

						$j('.jr_fx_delete_logo_wrapper').css( {'margin-left' : <?php echo JR_FX_COMPANY_LOGO_SIZE_W + 20; ?> } );
						if (logo == 'yes') {
							$j('.jr_fx_company_logo_image, .jr_fx_delete_logo_wrapper, .jr_fx_def_company_logo').show();
						} else {
							$j('.jr_fx_company_logo_image, .jr_fx_delete_logo_wrapper, .jr_fx_def_company_logo').fadeOut();
						}

						if (default_logo == 'yes') {
							$j('#jr_fx_def_company_logo').attr('checked','checked');
						}

						// bind form using 'ajaxForm' 
						$j('#company-logo').change( function (event) {

							// jQuery forms options
							var options = { 
								url: jr_fx_ajax.ajaxurl,		// override for form's 'action' attribute 
								type: 'post',					// 'get' or 'post', override for form's 'method' attribute 
								dataType: 'json',
								success: 
									function(response) {
										$j('.jr_fx_logo_loading').hide();
										$j('.jr_fx_company_logo_image, .jr_fx_delete_logo_wrapper').show();
										$j('.jr_fx_company_logo_image').attr({ 
											'src' : response.url,
											'width': response.width,
											'height': response.height
										}).fadeIn('slow');
										$j('.jr_fx_delete_company_logo').remove();
										$j('.jr_fx_def_company_logo').show();
										$j('#submit_form').append("<input type='hidden' name='jr_fx_company_logo' id='jr_fx_company_logo' value=1 />");
									},
								error: 
									function(response) {
										alert(response.error);
									},
								// $.ajax options 
								data: {
										post_id : "<?php echo ( get_query_var('job_id') ? intval(get_query_var('job_id')) : 0 ); ?>",
										action : 'jr_fx_load_company_logo',
										security: jr_fx_ajax.nonce
								 }
							};

							var loading = "<?php echo JR_FX_PLUGIN_URL.'images/loading.gif'; ?>";
							$j('.jr_fx_logo_legend').append('<img class="jr_fx_logo_loading" src = "' + loading + '">');
							$j('#submit_form').ajaxSubmit(options);
							
						});

						$j(".jr_fx_delete_logo").click( function (event) {
						
							var loading = "<?php echo JR_FX_PLUGIN_URL.'images/loading.gif'; ?>";
							$j('.jr_fx_company_logo_image, .jr_fx_delete_logo_wrapper').fadeOut();
							$j('#submit_form').append("<input type='hidden' name='jr_fx_delete_company_logo' id='jr_fx_delete_company_logo' value='yes' />");
							$j('.jr_fx_def_company_logo').hide();
							$j('#company-logo').val('');
						
							event.preventDefault();
							return false;
						});

					}

					if ( default_logo == 'yes' && $j("#jr_fx_def_company_logo").html() == undefined ) {
						$j('#submit_form').append("<input type='hidden' name='jr_fx_def_company_logo' id='jr_fx_def_company_logo' value='on' />");
					}

			<?php
				endif;
			?>

			<?php
			/*******************************************************************
			* Feature: Job Applications Stats
			********************************************************************/
			?>
			<?php
				if ( is_page( jr_fx_get_page_id('dashboard_page_id') ) && ! current_user_can('can_submit_job') ):
					// trigger the selected tab
					 if ( get_query_var('tab') ):
			?>
						$j('ul.display_section li a[href="#<?php echo get_query_var('tab'); ?>"]').trigger('click');
			<?php 
					endif;
				endif;

				// ADD ONLY AFTER WORDPRESS 3.6
				if ( false && is_page( jr_fx_get_page_id('dashboard_page_id') ) && current_user_can('can_submit_job') ):
			?>
					$j(".myjobs_section .data_list th:nth-child(4)").after('<th>teste</th>').html();

					$j(".myjobs_section .data_list tbody tr").each( function() {
						permalink = $j("td:nth-child(1) a", this).attr('href');

						if ( permalink != undefined ) {

							// jQuery job applications
							$j.ajax( { 
								url: jr_fx_ajax.ajaxurl,		// override for form's 'action' attribute 
								type: 'post',					// 'get' or 'post', override for form's 'method' attribute 
								dataType: 'json',
								success: 
									function(response) {
										console.log(response);
									},
								error: 
									function(response) {
										alert(response.error);
									},
								data: {
									permalink : permalink,
									action : 'jr_fx_get_job_applications',
									security: jr_fx_ajax.nonce
								}
							} );

						}

					} );

			<?php
				endif;
			?>

			<?php
			/**************************************************************************
			* Feature: Monitor Job Applications
			* LinkedIn applications are monitored by checking the button state before 
			* and after the LinkedIn button is clicked
			***************************************************************************/
			?>
			<?php
				if ( current_theme_supports( 'jr-fx-job-applications' ) && is_singular(APP_POST_TYPE) ):

					if ( jr_fx_user_has_applied_to( $post ) ) {
						$already_applied = true;
						$text = '( ' . __( "You've Applied!", JR_FX_i18N_DOMAIN ) . ' )';
					} else 
						$already_applied = false;

					if ( $already_applied ):
			?>
						$j(".apply_online").text("<?php echo $text; ?>");
			<?php
					endif;
			?>

					function checkSubmittedApplication( original_button_state ) {

						var lkd_button_state = $j("#jr_fx_linkedin_apply span[id*='title-text']").text();

						if ( lkd_button_state != original_button_state ) {

							// jQuery job applications
							$j.ajax( { 
								url: jr_fx_ajax.ajaxurl,		// override for form's 'action' attribute 
								type: 'post',					// 'get' or 'post', override for form's 'method' attribute 
								dataType: 'json',
								success: 
									function(response) {
										// application submitted to Linkedin
									},
								error: 
									function(response) {
										alert(response.error);
									},
								data: {
									post_id: <?php echo $post->ID; ?>,
									action : 'jr_fx_handle_linkedin_job_applications',
									security: jr_fx_ajax.nonce
								}
							} );
						} else {
							console.log('not submmitted!!!');
						}
						
					}

					$j("#jr_fx_linkedin_apply").on( 'click', "span[id*='title-text']", function() {

							var lkd_button_state = $j(this).text();
							var iframe = $j('iframe')[1];

							$j(iframe).on( 'load', function() {

								var lkdn_apply_listener = window.setInterval( checkFocus, 3000 ); 

								function checkFocus() {
									if ( undefined == document.getElementsByTagName("iframe")[1] ) {
										window.clearInterval(lkdn_apply_listener);
									}

									if( document.activeElement == document.getElementsByTagName("iframe")[1] ) {
										// user has the LinkedIn iFrame open - wait
									} else {
										// user close the LinkedIn iFrame
										checkSubmittedApplication( lkd_button_state );
									}
								}

							});

					} );

			<?php
				endif;
			?>

		});

	/* ]]> */
	</script>
<?php
}
