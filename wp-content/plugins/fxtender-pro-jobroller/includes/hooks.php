<?php
// Contains the features attached to WordPress or JobRoller hooks

// JR Filters
add_filter( 'jr_job_submit_steps', 'jr_fx_change_submit_steps' );
add_filter( 'jr_submit_footer_text', 'jr_fx_submit_footer_text' );

// WP Filters
add_filter( 'post_thumbnail_html', 'jr_fx_filter_content', 1, 4 );
add_filter( 'wp_mail', 'jr_fx_filter_mail', 99 );
add_filter( 'query','jr_fx_simple_location_search', 99 ); 

// JR Actions
add_action( 'jr_after_insert_job', 'jr_fx_action_after_insert_job', 99 );
add_action( 'jr_resume_header', 'jr_fx_linkedin_profile_resume' );
add_action( 'appthemes_after_post_title', 'jr_fx_linkedin_profile_job' );

// WP Actions
add_action( 'init', 'jr_fx_init_preview_and_thumbs' );
add_action( 'init', 'jr_fx_application_stats_init' );

add_action( 'admin_menu', 'jr_fx_maybe_remove_mini_map', 15 );

add_action( 'wp_insert_post_data', 'jr_fx_maybe_moderate', 99 );

add_action( 'jr_activate_plan_job_addons', 'jr_fx_update_post_meta_paid_job', 15, 2 );
add_action( 'wp_redirect', 'jr_fx_update_post_meta', 15 );

add_action( 'wp_head', 'jr_fx_action_wp_head' );
add_action( 'pre_get_posts', 'jr_fx_selective_preview_exclude' );
add_action( 'pre_post_update', 'jr_fx_action_pre_post_update', 1 );
add_action( 'get_footer', 'jr_fx_linkedin_profile' );
add_action( 'save_post', 'jr_fx_action_save_post', 95 );

if ( isset( $_REQUEST['action'] ) && 'jr_fx_qtip_callback' == $_REQUEST['action'] ) {
	# fired if the current viewer is not logged in
	do_action( 'wp_ajax_nopriv_' .  $_REQUEST['action'] );
	add_action( 'wp_ajax_nopriv_jr_fx_qtip_callback', 'jr_fx_qtip_callback' );

	# fired if the current viewer is logged in
	do_action( 'wp_ajax_' . $_POST['action'] );
	add_action( 'wp_ajax_jr_fx_qtip_callback', 'jr_fx_qtip_callback' );
}

# this security check was returning 404 errors on simple ajax calls for the job preview when a user was not logged in
# its disabled temporarly on ajax calls
if ( !is_user_logged_in() ) {
	add_action( 'init', create_function( '$a', "remove_action('admin_init', 'jr_security_check', 1);" ) );
}

/**
* ADD SUPPORT FOR FEATURES
*/

if ( jr_fx_validate( '_opt_jobs_applications_monitor', 'yes' ) ) {
	add_theme_support( 'jr-fx-job-applications' );
}


/*********************************************************************************************************************
 * FEATURE: 			Track job applications
 * HOOKS INTO:			init
 ********************************************************************************************************************* 
 * Description:			Track job applications
 */
// register new p2p connection for job applications
function jr_fx_application_stats_init() {

	if ( ! current_theme_supports( 'jr-fx-job-applications' ) )
		return;

	p2p_register_connection_type( array(
		'name' => JR_FX_JOB_APPLICATION,
		'from' => APP_POST_TYPE,
		'to' => 'user',
		'sortable' => true,
	) );

}

/*********************************************************************************************************************
 * FEATURE: 			Custom Field - Job Duration (pro)
 * HOOKS INTO: 			wp_redirect
 *********************************************************************************************************************
 * Description:			Hooks to wp_redirect to execute features that modify meta values
 *
 * TODO: Find better alternative then to hook to wp_redirect()
 */
function jr_fx_update_post_meta( $location ) {
	global $jr_fx_log;

	if ( empty($_POST['job_confirm']) )
		return $location; 

	if ( ! $post_id = intval($_POST['ID']) )
		return $location;

	$current_hook = current_filter();
	$action = 'wp_redirect';

	$log_message = '**** CHECKING RULES *** FEATURE [  Expire Days ] *** HOOK [ '. $current_hook .'() ] *** ACTION [ ' . $action . ' ]';

	# check for pending expire days updates (after payed jobs)
	if ( !jr_fx_validate('_opt_jobs_duration_field', 'no') ) :

		# log
		$jr_fx_log->write_log('**** HOOK CALLED ( ' . $current_hook . ' ) ****');

		### BEGIN FEATURE: Custom Field - Expire Days
		$pending_expire_days = get_post_meta( $post_id, '_jr_fx_expire_days', true );
		if ( $pending_expire_days ) {

			jr_fx_set_custom_job_duration( $post_id, $pending_expire_days );

			# log
			$jr_fx_log->write_log($log_message . ' | Extra Info: Updated duration meta data - new duration = ' . $pending_expire_days );
		}
		### END FEATURE

	endif;

	return $location;
}

/*********************************************************************************************************************
 * FEATURE: 			Custom Field - Job Duration (pro)
 * HOOKS INTO: 			jr_activate_plan_job_addons
 *********************************************************************************************************************
 * Description:			Hooks to wp_redirect to execute features that modify meta values
 *
 * TODO: Find better alternative then to hook to wp_redirect()
 */
function jr_fx_update_post_meta_paid_job( $order, $plan_data ) {
	global $jr_fx_log;

	$post_id = jr_get_order_job_id( $order );
	if ( ! $post_id )
		return;

	$current_hook = current_filter();
	$action = 'jr_activate_plan_job_addons';

	$log_message = '**** CHECKING RULES *** FEATURE [  Expire Days ] *** HOOK [ '. $current_hook .'() ] *** ACTION [ ' . $action . ' ]';

	# check for pending expire days updates (after payed jobs)
	if ( !jr_fx_validate('_opt_jobs_duration_field', 'no') ) :

		# log
		$jr_fx_log->write_log('**** HOOK CALLED ( ' . $current_hook . ' ) ****');

		### BEGIN FEATURE: Custom Field - Expire Days
		$pending_expire_days = get_post_meta( $post_id, '_jr_fx_expire_days', true );
		if ( $pending_expire_days ) {

			jr_fx_set_custom_job_duration( $post_id, $pending_expire_days );

			# log
			$jr_fx_log->write_log($log_message . ' | Extra Info: Updated duration meta data - new duration = ' . $pending_expire_days );
		}
		### END FEATURE

	endif;
}

/*********************************************************************************************************************
 * FEATURE: 		First # job(s) are free (pro)
 * HOOKS INTO: 		jr_job_submit_steps
 ********************************************************************************************************************* 
 */
 function jr_fx_change_submit_steps( $steps ) {

	global $jr_fx_log;

	$current_hook = current_filter();

	$jr_fx_log->write_log('**** HOOK CALLED ( ' . $current_hook . ' ) ****');

	### BEGIN FEATURE: First # job(s) are free - check for an insert into jr_orders table
	if ( function_exists( 'jr_fx_validate_feat_jobs_free_offer' ) ) {

		# validates the feature rules. If all rules are met, ignores all fees except featured jobs
		if ( jr_fx_validate_feat_jobs_free_offer( 'query', 'ignore costs' ) ):

			# job offer is entitled; skip pricing
			$steps = _jr_job_submit_steps();
			$description = __('Confirm', APP_TD);

			$steps[] = _jr_confirm_step( $description );

		endif;

	}
	### END FEATURE
	return $steps;
}


/*********************************************************************************************************************
 * FEATURE: 		First # job(s) are free (pro)
 * HOOKS INTO: 		jr_submit_footer_text
 ********************************************************************************************************************* 
 * Display message for Free jobs offer on the job submit button footer 
 *
 */
function jr_fx_submit_footer_text( $text ) {
	global $jr_fx_log;

	$current_hook = current_filter();

	$jr_fx_log->write_log('**** HOOK CALLED ( ' . $current_hook . ' ) ****');

	### BEGIN FEATURE: First # job(s) are free - display footer message
	if ( function_exists( 'jr_fx_validate_feat_jobs_free_offer' ) ) {

			# validates the feature rules. If all rules are met, ignores all fees except featured jobs
			if ( jr_fx_validate_feat_jobs_free_offer( $current_hook, 'ignore costs' ) && 'no' != jr_fx_validate('_opt_jobs_hide_pay_free_offer') ):

				if ( 'text' == jr_fx_validate('_opt_jobs_hide_pay_free_offer') ) 
					$text_free = jr_fx_validate('_opt_jobs_free_offer_text');
				else
					$text_free = '';

				# job offer is entitled; display submit job footer message 
				$text = sprintf( '<p class=\'pricing\'>%s</p>', esc_html( $text_free ) );

			endif;
	}
	### END FEATURE
	return $text;
}

/*********************************************************************************************************************
 * FEATURE: 			Hide/Disable Google Maps (free)
 * HOOKS INTO: 			pre_post_update()
 ********************************************************************************************************************* 
 * Description:			Hooks to pre_post_update() for updating data before saving to the database
 * @returns 			none
 */
function jr_fx_action_pre_post_update( $post_id ) {
	global $posted, $jr_fx_log, $post;

	$current_hook = current_filter();

	# runs only on admin edit job
	if ( isset($post) && is_admin() && $post->post_type == JR_FX_JR_POST_TYPE ) :

		# log
		$jr_fx_log->write_log('**** HOOK CALLED ( ' . $current_hook . ' ) ****');

		if ( function_exists( 'jr_fx_validate_exec_feat_gmaps_location' ) ) {
			### BEGIN FEATURE: Hide "Google Map"
			jr_fx_validate_exec_feat_gmaps_location( 'action', $current_hook, 'update_post_meta()', $post_id );
			### END FEATURE
		}

	endif;
}

/*********************************************************************************************************************
 * FEATURE: 			Custom Field - Job Duration (pro)
 * 						Custom Field - Applications Recipient - updates metadata (pro)
 * 						Custom Field - Optional Apply Online - updates user metadata (pro) 
 * 						Custom Field - Optional Apply With LinkedIn - updates user metadata (pro)
 * 						Hide/Disable Google Maps (free)
 * HOOKS INTO: 			jr_after_insert_job
 ********************************************************************************************************************* 
 * Description:			Hooks to jr_after_insert_job for updating data after inserting a job on the database
 * @returns 			none
 */
function jr_fx_action_after_insert_job ( $post_id ) {
	global $current_user, $jr_fx_log;

	get_currentuserinfo();

	$current_hook = current_filter();

		# log
		$jr_fx_log->write_log('**** HOOK CALLED ( ' . $current_hook . ' ) ****');

		if ( function_exists( 'jr_fx_validate_exec_feat_field_expire_days' ) ) {
			### BEGIN FEATURE: Custom Field - Expire Days
			jr_fx_validate_exec_feat_field_expire_days( $current_hook, 'update_post_meta()', $post_id, $current_user );
			### END FEATURE
		}

		if ( function_exists( 'jr_fx_validate_exec_feat_field_apps_recipient' ) ) {
			### BEGIN FEATURE: Custom Field - Applications Recipient - update metadata
			jr_fx_validate_exec_feat_field_apps_recipient( $current_hook, 'update_post_meta()', $post_id, $current_user );
			### END FEATURE
		}

		if ( function_exists( 'jr_fx_validate_exec_feat_field_optional_apply' ) ) {
			### BEGIN FEATURE: Custom Field - Optional Apply - update user metadata
			jr_fx_validate_exec_feat_field_optional_apply( $current_hook, 'update_user_meta()', $post_id, $current_user );
			### END FEATURE
		}

		if ( function_exists( 'jr_fx_validate_exec_feat_field_optional_apply_ld' ) ) {
			### BEGIN FEATURE: Custom Field - Optional Apply LinkedIn - update metadata
			jr_fx_validate_exec_feat_field_optional_apply_ld( $current_hook, 'update_user_meta()', $post_id, $current_user );
			### END FEATURE
		}

		if ( function_exists( 'jr_fx_validate_exec_feat_gmaps_location' ) ) {
			### BEGIN FEATURE: Hide "Google Map"
			jr_fx_validate_exec_feat_gmaps_location( 'action', $current_hook, 'update_post_meta()', $post_id, $current_user );
			### END FEATURE
		}
}

/*********************************************************************************************************************
 * FEATURE: 			Job Listings Preview (free)
 * HOOKS INTO: 			save_post
 ********************************************************************************************************************* 
 * Description:			Hooks to save_post() for updating data after saving posts on the database
 * @returns 			none
 */
function jr_fx_action_save_post ( $post_id ) {
	global $posted, $current_user, $jr_fx_log, $post, $app_abbr;

	get_currentuserinfo();

	$current_hook = current_filter();

	# clear any existing cached values for this post
	jr_fx_clear_cached_values( $post_id );
}

/*********************************************************************************************************************
 * FEATURE: 			Job Listings Preview (free)
 * HOOKS INTO: 			pre_get_posts
 *********************************************************************************************************************
 * Description:			Exclude preview from specific pages
 */
function jr_fx_selective_preview_exclude( $query ) {
	global $jr_fx_exclude;

	if ( $query->is_main_query() ){
		$exclude = jr_fx_validate( '_opt_listings_preview_exclude' );
		if ( $exclude ) {
			$exclude_pages = explode( ',', $exclude );

			foreach ( $exclude_pages as $page ) {

				switch ( trim(strtoupper( $page )) ) {
					case 'MAIN':
						if (  $query->is_home ) {
							$jr_fx_exclude = 1;
							return;
						}
						break;
					case 'TAXONOMY':
						if ( $query->is_tax ) {
							$jr_fx_exclude = 1;
							return;
						}
						break;
					case 'AUTHOR':
						if ( $query->is_author ) { 
							$jr_fx_exclude = 1; 
							return;
						}
						break;
					case 'SEARCH':
						if ( $query->is_search ) { 
							$jr_fx_exclude = 1;
							return;
						}
						break;
				}
			};
		}
	}
}

/*********************************************************************************************************************
 * FEATURE: 			Hide Company Logo (pro)
 * HOOKS INTO: 			post_thumbnail_html()
 ********************************************************************************************************************* 
 * Description:			Hooks to post_thumbnail_html() to filter the post content
 * @returns $attach_id	Returns the $attach_id
 */
function jr_fx_filter_content( $attach_id , $post_id, $size, $attr ) {
	global $jr_fx_log;

	$current_hook = current_filter();

	if ( is_singular(JR_FX_JR_POST_TYPE) && $attr == 'post-thumbnail' ) :

		# log
		if (JR_FX_LOG) $jr_fx_log->write_log('**** HOOK CALLED ( ' . $current_hook . ' ) ****');

		if ( function_exists( 'jr_fx_validate_feat_hide_company_logo' ) ) {
			### BEGIN FEATURE: Hide Company Logo
			if (jr_fx_validate_feat_hide_company_logo( 'filter', $current_hook, 'hide company logo' ) ) :
				# clears the attachment
				$attach_id = '';
			endif;
			### END FEATURE
		}

	endif;
	return $attach_id;
}

/*
 *********************************************************************************************************************
 * FEATURE: 			Browse Resume Lists for Job Posts / Paid Jobs (PRO)
 * 						Browse Single Resumes for Job Posts / Paid Joba (PRO)
 * 						S2Member Integration (pro)
 * HOOKS INTO: 			wp_head
 ********************************************************************************************************************* 
 * Description:			Hooks to wp_head to trigger some features
 * @returns location
 */
function jr_fx_action_wp_head() {
	global $jr_fx_log, $post, $posted, $app_abbr, $user_ID, $wpdb;	

	$current_hook = current_filter();

	# check main rules for processing the features - RESUMES
	if ( isset($post) && $post->post_type == JR_FX_JR_RESUME ) :

		# log
		$jr_fx_log->write_log('**** HOOK CALLED ( ' . $current_hook . ' ) ****');
		
		if ( function_exists( 'jr_fx_validate_feat_resume_list_browse' ) && !is_singular() && !is_single() ) {

			### BEGIN FEATURE: Resume list browsing
			if ( jr_fx_validate_feat_resume_list_browse( $current_hook, 'browse resume listings' )  ) :

				$redirect_url = jr_fx_validate( '_opt_resumes_noaccess_redirect' );

				if ( !$redirect_url ) 
					$redirect_url = get_bloginfo('url');
				else
					$redirect_url = get_permalink( $redirect_url );

				wp_redirect( $redirect_url );
				exit;

			endif;
			### END FEATURE
		}

		if ( function_exists( 'jr_fx_validate_feat_resume_browse' ) && ( is_singular() || is_single() ) ) {

			### BEGIN FEATURE: Resume resume viewing
			if ( jr_fx_validate_feat_resume_browse( $current_hook, 'view resumes' )  ) :

				$redirect_url = jr_fx_validate( '_opt_resumes_noaccess_redirect' );
				
				if ( !$redirect_url ) 
					$redirect_url = get_bloginfo('url');
				else
					$redirect_url = get_permalink( $redirect_url );

				wp_redirect( $redirect_url );

			endif;
			### END FEATURE
		}
	endif;

	if ( is_page( jr_fx_get_page_id('job_seeker_resume_page_id') ) ) :

		if ( function_exists( 'jr_fx_validate_feat_s2member' ) ) {
			### BEGIN FEATURE: S2Member
			if ( function_exists('jr_fx_validate_feat_s2member') && jr_fx_validate_feat_s2member( '_opt_resume_s2member_level', $current_hook, 's2member' ) ) {

				$redirect_url = jr_fx_validate( '_opt_resume_s2member_redirect' );

				if ( !$redirect_url ) 
					$redirect_url = get_bloginfo('url');
				else
					$redirect_url = get_permalink( $redirect_url );

				wp_redirect( $redirect_url );
			}
			### END FEATURE
		}

	endif;

	if ( is_page( jr_fx_get_page_id('dashboard_page_id') ) && isset($_POST['save_alerts'] ) ):

		if ( function_exists( 'jr_fx_validate_feat_s2member' ) ) {
			### BEGIN FEATURE: S2Member Alerts
			if ( function_exists('jr_fx_validate_feat_s2member') && jr_fx_validate_feat_s2member( '_opt_job_alerts_s2member_level', $current_hook, 's2member' ) ) {

				$redirect_url = jr_fx_validate( '_opt_resume_s2member_redirect' );

				if ( !$redirect_url ) 
					$redirect_url = get_bloginfo('url');
				else
					$redirect_url = get_permalink( $redirect_url );

				wp_redirect( $redirect_url );
			}
			### END FEATURE
		}

	endif;

	if( !is_user_logged_in() && is_singular(JR_FX_JR_POST_TYPE) && jr_fx_validate('_opt_jobs_visitors_redirect', 'yes') ) :

			### BEGIN FEATURE: Redirect Application Visitors
			$redirect_url = wp_login_url( $_SERVER['REQUEST_URI'] );

			wp_redirect( $redirect_url );
			### END FEATURE

	endif;
}

/*
 *********************************************************************************************************************
 * FEATURES: 			Custom Field - Applications Recipient - gets metadata (pro)
 *						Email Signature (pro)
 *						Attach Preset CV (pro)
 * HOOKS INTO: 			wp_mail
 ********************************************************************************************************************* 
 * Description:			Hooks to wp_mail() for updating $email data before sending emails
 * @returns $mail		Returns the modified $mail variable as needed
 */
function jr_fx_filter_mail( $mail ) {
	global $post, $jr_fx_log;

	$current_hook = current_filter();

	if( isset($post) ) :

		# log
		if (JR_FX_LOG) $jr_fx_log->write_log('**** HOOK CALLED ( ' . $current_hook . ' ) ****');

		if ( function_exists( 'jr_fx_validate_feat_field_apps_recipient' ) ) {
			### BEGIN FEATURE: Custom Field - Applications Recipient - get metadata
			$new_mail_address = jr_fx_validate_feat_field_apps_recipient( $current_hook, 'get recipient emaill address', $mail, $post->ID );
			### END FEATURE
		}

		if ( function_exists( 'jr_fx_validate_feat_email_signature' ) ) {
			### BEGIN FEATURE: Email Signature
			$mail_signature = jr_fx_validate_feat_email_signature(  $current_hook, 'get email signature' );
			### END FEATURE
		}

		if ( function_exists('jr_fx_validate_feat_attach_preset_cv') ) {
			### BEGIN FEATURE: Upload CVs
			$mail['attachments'][] = jr_fx_validate_feat_attach_preset_cv(  $current_hook, 'attach preset CV' );
			### END FEATURE
		}

	endif;

	if ( !empty($new_mail_address) ) $mail['to'] =  $new_mail_address ;

	if ( !empty($mail_signature) ) $mail['message'] .=  $mail_signature;

	return $mail;
}

/*
 *********************************************************************************************************************
 * FEATURES:			Job Listings Thumbs (pro)
 * 						Job Listings Preview (free)
 * 						Replace Date with Days Left - FREE 
 * HOOKS INTO: 			the_title
 ********************************************************************************************************************* 
 * Description:			Hooks to the_title() for some features
 * @returns $mail		Returns the modified $title variable as needed
 */
function jr_fx_init_preview_and_thumbs() {global $wp_query;

	// add action only if one of these features is Enabled
	if (
			/* ### BEGIN FEATURE: Job Listings Thumbs */( function_exists( 'jr_fx_validate_feat_job_list_thumbs' ) && jr_fx_validate_feat_job_list_thumbs( 'the_title() - pre_check', 'add job list thumb' ) ) ||
			/* ### BEGIN FEATURE: Job Preview */		( function_exists( 'jr_fx_validate_feat_job_preview' ) && jr_fx_validate_feat_job_preview( 'the_title() - pre_check', 'Job Preview' ) ) ||
			/* ### BEGIN FEATURE: Days Left */			( function_exists( 'jr_fx_validate_feat_days_left' ) && jr_fx_validate_feat_days_left( 'the_title() - pre_check', 'Days Left' ) )
		 ) :
		add_action( 'the_title', 'jr_fx_attach_features_to_post', 10, 2 ); 
	endif;
}

/*
 *********************************************************************************************************************
 * FEATURES: 			First # job(s) are free (pro)
 * 						Minimum Jobs for Auto Publish (free)
 * 						Moderate jobs posted by admins (free)
 * HOOKS INTO: 			wp_insert_post_data
 ********************************************************************************************************************* 
 */
function jr_fx_maybe_moderate ( $data ) {
	global $app_abbr, $jr_fx_log, $wp_query;

	if ( APP_POST_TYPE !=  $data['post_type'] || is_admin() || $data['post_status'] != 'publish' )
		return $data;

	$current_hook = current_filter();
	$skip_min_auto_pub = false;

	# start by getting jobroller 'require moderation' option to quicky filter out non compatible features
	$jr_jobs_moderate = get_option($app_abbr.'_jobs_require_moderation');

	# log
	$jr_fx_log->write_log('**** HOOK CALLED ( ' . $current_hook . ' ) ****');
/*
	if ( function_exists( 'jr_fx_validate_feat_jobs_free_offer' ) ) {
		### BEGIN FEATURE: First # job(s) are free | validates the feature rules. If all rules are met, sets the status to published.
		if ( jr_fx_validate_feat_jobs_free_offer() ):

			# job offer entitled
			if ( jr_fx_validate( '_text_notice_free_offer' ) )	jr_fx_notices( '_text_notice_free_offer' );

		endif; 
		### END FEATURE
	}
*/
	if ( function_exists( 'jr_fx_validate_feat_jobs_min_auto_pub' ) && !$skip_min_auto_pub ) {
		### BEGIN FEATURE: Minimum Jobs for Auto Publish - validates the feature rules. If all rules are met, sets the status to pending.		
		if ( $jr_jobs_moderate == 'no' && jr_fx_validate_feat_jobs_min_auto_pub( $current_hook, 'status change = ' . ( !isset($status)?'pending':'')  ) ):

			# minimum jobs published rule not met - set status to pending
			if ( !isset($status) )	$status = 'pending';

		endif;
		### END FEATURE
	}

	if ( function_exists( 'jr_fx_validate_feat_jobs_moderate' ) ) {
		### BEGIN FEATURE: Moderate jobs posted by admins - validates the feature rules. If all rules are met, sets the status to pending.
		if ( $jr_jobs_moderate == 'no' && jr_fx_validate_feat_jobs_moderate( $current_hook, 'status change = ' . ( !isset($status)?'pending':'')  ) ):

			# current user is admin - set status to pending
			if ( !isset($status) )	$status = 'pending';

		endif;
		### END FEATURE
	}

	# update job satus if there are changes
	if ( isset($status) ) $data['post_status'] = $status;

	return $data;
}

/*********************************************************************************************************************
 * FEATURE: 			LinkedIn Profile
 * HOOKS INTO: 			get_footer
 ********************************************************************************************************************* 
 * Description:			Output LinkedIn Javascript User Profile
 * @param none			
 * @returns none		Echo's the LinkedIn javascript User profile
 */
 function jr_fx_linkedin_profile() {

	if ( is_author() && function_exists('jr_fx_validate_feat_linkedin_profile') && jr_fx_validate_feat_linkedin_profile() ) :

		$curauth = get_query_var('author_name') ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );

		$linkedin_url = get_user_meta( $curauth->ID, 'linkedin_profile', true);	

		$pos = strpos($linkedin_url, '/company/');
		$profile_type = ($pos === FALSE?'Member':'Company');
?>
		<span id="jr_fx_linkedin_profile"  style="display: block">
				<script
					type="IN/<?php echo $profile_type; ?>Profile"
					data-id="<?php echo $linkedin_url; ?>" 
					data-format="inline" 
					data-related="false">
				</script>
		</span>
<?php
	endif;
}

/*********************************************************************************************************************
 * FEATURE: 			LinkedIn Profile Resume
 * HOOKS INTO: 			jr_resume_header
 ********************************************************************************************************************* 
 * Description:			Output LinkedIn Javascript User Profile on Resumes
 * @param none			
 * @returns none		Echo's the LinkedIn javascript User profile on Resumes
 */
 function jr_fx_linkedin_profile_resume() {
	global $post;

	if ( isset($post) && is_singular( JR_FX_JR_RESUME ) && function_exists('jr_fx_validate_feat_linkedin_profile_resume') && jr_fx_validate_feat_linkedin_profile_resume() ) :

		$linkedin_url = get_user_meta( $post->post_author, 'linkedin_profile', true);

		$pos = strpos($linkedin_url, '/company/');
		$profile_type = ($pos === FALSE?'Member':'Company');
?>
		<div id="jr_fx_linkedin_profile_resume" style="display: none">
			<script 
				type="IN/<?php echo $profile_type; ?>Profile"
				data-id="<?php echo $linkedin_url; ?>" 
				data-format="inline" 
				data-related="false">
			</script>
		</div>
<?php
	endif;
}

/*********************************************************************************************************************
 * FEATURE: 			LinkedIn Profile Job
 * HOOKS INTO: 			appthemes_after_post_title
 ********************************************************************************************************************* 
 * Description:			Output LinkedIn Javascript User Profile on Jobs
 * @param none			
 * @returns none		Echo's the LinkedIn javascript User profile on Jobs
 */
function jr_fx_linkedin_profile_job() {
	global $post;

	if ( isset($post) && is_singular( JR_FX_JR_POST_TYPE ) && function_exists('jr_fx_validate_feat_linkedin_profile_job') && jr_fx_validate_feat_linkedin_profile_job() ) :

		$option = jr_fx_validate( '_opt_integration_linkedin_profile_job' );

		if ( $option == 'company' ) {
			$company_name = wptexturize(strip_tags(get_post_meta($post->ID, '_Company', true)));

			if ($company_name)	$linkedin_url = 'http://www.linkedin.com/company/' . $company_name;
			$profile_type = 'Company';
		} else {
			$linkedin_url = get_user_meta( $post->post_author, 'linkedin_profile', true);

			$pos = strpos($linkedin_url, '/company/');
			$profile_type = ($pos === FALSE?'Member':'Company');
		}

		if ( empty($linkedin_url) )
			return;
?>
		<div id="jr_fx_linkedin_profile_job" style="display: none;">
			<script
				type="IN/<?php echo $profile_type; ?>Profile"
				data-id="<?php echo $linkedin_url; ?>" 
				data-format="inline" 
				data-related="false">
			</script>
		</div>
<?php
	endif;
}

/*********************************************************************************************************************
 * FEATURE: 			(Ajax) Job Preview (free)
 * HOOKS INTO:			wp_ajax_jr_fx_qtip_callback / wp_ajax_nopriv_jr_fx_qtip_callback
 ********************************************************************************************************************* 
 * Description:				Validates the rules for this feature and show the job preview using the qTip library
 * @param $_POST['jobID']	The job/post ID
 * @returns html			qTip Baloon
 */
function jr_fx_qtip_callback() {
	global $app_version;

	$html_tip = '';

	//********************************************************************
	// reset previously disabled JR security check - if used
	//********************************************************************
	if ( version_compare( $app_version, '1.5.2', '<' ) ) {
		if ( get_option('jr_admin_security') != 'disable' ) {
			// comment out the below line to work with wpmu
			if (!appthemes_is_wpmu()) add_action('admin_init', 'jr_security_check', 1);
		}
	}
	// ********************************************************************	

	# generate the response
	if ( empty($_POST['jobID']) ) exit;

	# get the job id
	$job_id = $_POST['jobID'] ;

	# check for thumb
	$allow_thumb = $_POST['hasThumb'];

	$args = array ( 
		'post_type' => JR_FX_JR_POST_TYPE,
		'p' => $job_id
	);

	$WP_Query_object = new WP_Query();
	$WP_Query_object->query( $args );
	foreach ( $WP_Query_object->posts as $result ) {
		$img_size = ($allow_thumb?jr_fx_validate( '_opt_listings_preview_thumb'):'') ;
		$img_size = ( $img_size && $img_size != 'no'?$img_size:'');
		if ( $img_size && $img_size != 'no' ) :
			$img_attr = wp_get_attachment_image_src( get_post_thumbnail_id($result->ID), $img_size );
		endif;

		$html_tip_cached = '<div id="jr_fx_preview_cached_'.$job_id.'" class="jr_fx_preview_wrap" style="display: none">';

		$html_tip_no_cache = '<div class="jr_fx_preview_wrap">';
		if ( $img_size && function_exists( 'jr_fx_get_preview_thumbs' ) ) :
			$html_tip = jr_fx_get_preview_thumbs( $result->ID );
		endif;
		$html_tip .= '<div class="jr_fx_preview_content" ' . ($img_size?'style="margin-left:'.($img_attr[1] + 20).'px"':"") .'>
						<h1><a href="'.$result->guid.'">'.$result->post_title.'</a></h1>
						<p class="note">'.$_POST['jobType'].'</p>
						<br/>
						<p>'. jr_fx_preview_format($result).'</p>
						<p class="note">
							'.__('Location',JR_FX_i18N_DOMAIN).': ' . $_POST['jobLocation'].
						'</p>
					</div>
					<div class="clear">&nbsp;</div>
				</div>';

		$preview_cache = jr_fx_validate('_opt_listings_preview_cache_time');
		if ( $preview_cache ) {
			$preview_cache *= 86400;
		} else
			$preview_cache = JR_FX_JOB_LISTINGS_PREVIEW_CACHE;

		// cache preview for a week
		set_transient( JR_FX_FIELDS_PREFIX.'-preview-'. $job_id, $html_tip_cached . $html_tip, $preview_cache );
	}
	
	if( $html_tip ) echo $html_tip_no_cache . $html_tip;
	exit;
}

/*********************************************************************************************************************
 * FEATURE: 			Disable Google Maps (free)
 * HOOKS INTO:			query
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and show the job preview using the qTip library
  */
function jr_fx_maybe_remove_mini_map() {
	if ( jr_fx_validate('_opt_jobs_gmaps','yes') ) {
		remove_meta_box( 'location-meta-boxes', APP_POST_TYPE, 'side' );
	}
}

// replace geolocation search with simple location search
function jr_fx_simple_location_search( $query ) {
	global $jr_fx_disable_gmaps;

	if ( isset($_GET['s']) && !empty($_GET['location']) && $jr_fx_disable_gmaps ) {

		$coordinate_keys = array('_jr_geo_latitude', '_jr_geo_longitude');

		### BEGIN FEATURE: Disable Google Maps	
		$geo_meta_key = stripos($query, "_jr_geo_longitude");
		if ( ! $geo_meta_key ) $geo_meta_key = stripos($query, "_jr_geo_longitude");
		
		if ( ! $geo_meta_key ) return $query;

		# change the db query to get jobs by location and not by geolocation
		global $wpdb;

		$location = mysql_real_escape_string( $_GET['location'] );

		// custom query for simple location search
		$query = " 
			SELECT ID FROM (
				SELECT ID, 1 as weight, post_date as date 
				FROM $wpdb->posts 
				WHERE ID IN ( 
					SELECT $wpdb->postmeta.post_id 
					FROM $wpdb->postmeta 
					WHERE meta_key = 'geo_short_address'  AND LCASE(meta_value) like LCASE('%".$location."%')
				) 
				AND $wpdb->posts.post_status = 'publish'
				AND $wpdb->posts.post_type = '" . JR_FX_JR_POST_TYPE . "'
				UNION
				( 
					SELECT ID, -1 as weight, post_date as date 
					FROM $wpdb->posts 
					WHERE ID NOT IN ( 
						SELECT $wpdb->postmeta.post_id 
						FROM $wpdb->postmeta 
						WHERE meta_key = 'geo_short_address' 
					) 
					AND $wpdb->posts.post_status = 'publish' 
					AND $wpdb->posts.post_type = '" . JR_FX_JR_POST_TYPE . "'
					ORDER BY post_date DESC ) 
				) AS radial_search 
				ORDER BY weight DESC, date DESC
		";
		### END FEATURE		
	}
	return $query;
}