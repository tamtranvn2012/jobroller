<?php
/*
 * PRO exclusive features
 */

//include_once( 'legacy.php' );

define('JR_FX_VERSION',JR_FX_VER_PLUS);

// WP Actions
add_action( 'wp_head', 'jr_fx_mask_download_cvs' );
add_action( 'wp_head', 'jr_fx_check_registered_email' );

add_action( 'init', 'jr_fx_process_cv_upload' );
add_action( 'init', 'jr_fx_register_gateways', 100 );
add_action( 'init', 'jr_maybe_redirect_bank_transfer', 101 );

add_action( 'get_footer', 'jr_fx_show_cv_list' );
add_action( 'get_footer', 'jr_fx_cv_html_list' );
add_action( 'get_footer', 'jr_fx_get_cv_files' );

add_action( 'user_edit_form_tag', 'jr_fx_user_edit_form_tag' );

add_action( 'show_user_profile', 'jr_fx_extra_user_profile_fields' );
add_action( 'edit_user_profile', 'jr_fx_extra_user_profile_fields' );
add_action( 'personal_options_update', 'jr_fx_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'jr_fx_save_extra_user_profile_fields' );

add_action( 'wp_ajax_nopriv_jr_fx_load_company_logo', 'jr_fx_load_company_logo' );
add_action( 'wp_ajax_jr_fx_load_company_logo', 'jr_fx_load_company_logo' );

add_action( 'wp_ajax_jr_fx_get_job_applications', 'jr_fx_get_job_applications' );
add_action( 'wp_ajax_jr_fx_handle_linkedin_job_applications', 'jr_fx_handle_linkedin_job_applications' );

add_action( 'wp_head', 'jr_fx_handle_job_applications', 14 );

add_action( 'add_meta_boxes', 'jr_fx_register_job_applications_meta_box' );
add_action( 'edit_user_profile', 'jr_fx_profile_jobs_applied_list' );

add_action( 'manage_' . APP_POST_TYPE . '_posts_custom_column', 'jr_fx_jobs_custom_columns' );
add_filter( 'manage_edit-' . APP_POST_TYPE . '_sortable_columns', 'jr_fx_jobs_columns_sort' );
add_filter( 'manage_edit-' . APP_POST_TYPE . '_columns', 'jr_fx_edit_jobs_columns' );

add_filter( 'request', 'jr_fx_applications_column_orderby' );

// JR Actions
add_action( 'job_footer', 'jr_fx_linkedin_apply' );
add_action( 'jr_after_insert_job', 'jr_fx_process_company_logo' );

add_action( 'jr_dashboard_tab_after', 'jr_fx_seeker_dash_applications_tab' ) ;
add_action( 'jr_dashboard_tab_content', 'jr_fx_seeker_dash_applications_content' ) ;

add_action( 'jr_dashboard_tab_after', 'jr_fx_lister_dash_applications_tab' ) ;
add_action( 'jr_dashboard_tab_content', 'jr_fx_lister_dash_applications_content' ) ;

// Shortcodes
add_shortcode( 'jr_fx_mp_order_id', 'jr_fx_order_id_sc' );
add_shortcode( 'jr_fx_mp_order_cost', 'jr_fx_order_cost_sc' );
add_shortcode( 'jr_fx_mp_redirect', 'jr_fx_manual_payment_redirect_sc' );

/*********************************************************************************************************************
 * FUNCTIONS (PRO ONLY)
 *********************************************************************************************************************/

function jr_fx_register_gateways() {
	global $jr_fx_log;

	if ( ! current_theme_supports('app-payments') )
		return;

	# Include gateway classes

	$jr_fx_log->write_log( 'HTTP_USER_AGENT = ' . $_SERVER['HTTP_USER_AGENT'] );

	$jr_fx_log->write_log( '_GET = ' . print_r($_GET, true) );

	require_once ('gateways/PaymentGateway.class.php');

	if ( ! APP_Gateway_Registry::is_gateway_registered( '2checkout' ) ) {
		require_once ( 'gateways/2checkout.class.php' );
		appthemes_register_gateway( 'JR_FX_2Checkout_APP' );

		$jr_fx_log->write_log( 'Registered 2Checkout' );
	}

	if ( ! APP_Gateway_Registry::is_gateway_registered( 'authorize-net' ) ) {
		require_once ( 'gateways/Authorize.class.php' );
		appthemes_register_gateway( 'JR_FX_Authorize_APP' );

		$jr_fx_log->write_log( 'Registered Authorize.net' );
	}

	if ( ! APP_Gateway_Registry::is_gateway_registered( 'google-wallet' ) ) {
		require_once ( 'gateways/Google.class.php' );
		appthemes_register_gateway( 'JR_FX_Google_Wallet_APP' );

		$jr_fx_log->write_log( 'Registered Google Wallet' );
	}

	require_once ( 'gateways/BankTransfer.class.php' );
	appthemes_register_gateway( 'JR_FX_Bank_Transfer_Gateway' );
}


/*
 * Description:			Thumbs for the Job Listings Preview
 * @param $posID		The post ID
 * @returns image 		Returns	the html image
 */ 
function jr_fx_get_preview_thumbs ( $postID ) {
	return get_the_post_thumbnail(  $postID, jr_fx_validate( '_opt_listings_preview_thumb' ), array ( 'class' => 'jr_fx_img_preview_thumb jr_fx_left'));
}

/*
 * Description:	 Mask downloaded CV's
 * @returns File
 */
 function jr_fx_mask_download_cvs () {

	if ( isset($_POST) && isset($_POST['jr_fx_download_cv_id']) && $_POST['jr_fx_download_cv_id'] ):

		$cv_id = $_POST['jr_fx_download_cv_id']; 
		$filename = wp_get_attachment_url($cv_id);	
		$mm_type = get_post_mime_type($cv_id);  
		
		header('Content-Type: ' . $mm_type); //Stream as a binary file! So it would force browser to download
		header('Content-Disposition: attachment; filename="'.basename($filename).'"'); //Tell the filename to the browser
		ob_clean();
		flush();
		readfile($filename); //Read and stream the file
		exit;

	endif;
}

/*
 * Description:	 Check for unregistered emails
 * @returns File
 */
function jr_fx_check_registered_email() {
	global $jr_fx_email_error, $jr_fx_invalid_email;

	# get the email
	if (isset($_POST['apply_to_job']) && isset($_POST['your_email']) && jr_fx_validate('_opt_jobs_apply_registered_email', 'yes') ):

		$email = $_POST['your_email'] ;

		if ( $email && !email_exists( $email ) ):

			$jr_fx_invalid_email = $email;
			$_POST['your_email'] = __('[UNREGISTERED EMAIL]', JR_FX_i18N_DOMAIN) ;
			$jr_fx_email_error = __('<strong>ERROR</strong>: The email address is not registered on the database.', JR_FX_i18N_DOMAIN);

		endif;
	endif;
}

// selectively disable options (ie: Apply with LinkedIn, Apply Online)
function jr_fx_selective_disable( $meta_key, $post_id ) {
	$args = array(
		'meta_query' => array(
			array(
				'key' => $meta_key . '-' . $post_id,
				'value' => '1',
			),
		)
	 );
	$user_query = new WP_User_Query( $args );

	// get value from user meta for older FX-RC versions
	$total = $user_query->get_total();
	$total += get_post_meta( $post_id, $meta_key, true );
	return $total;
}

/*************
 * SHORTCODES
 ************/

/*
 * Description:	Shortcode for the manual payment page return button
 * @returns HTML button
 */
function jr_fx_manual_payment_redirect_sc( $atts ){
	global $app_abbr;

	$default_page_id = jr_fx_get_page_id('dashboard_page_id');

	// parameters
	extract( shortcode_atts( array(
		'title' => __( "Return to 'My Dashboard'", JR_FX_i18N_DOMAIN ),
		'page_id' => $default_page_id,
		'permalink' => get_permalink($default_page_id),
		'class' => 'button'
	), $atts ) );

	return "<p class='{$class}'><a href='{$permalink}' rel='nofollow' >{$title}</a></p>";
}


/*
 * Description:	Shortcode to show the Order ID
 * @returns HTML text
 */  
function jr_fx_order_id_sc( $atts ){

	// parameters
	extract( shortcode_atts( array(
		'class' => ''
	), $atts ) );

	$order_id = ! empty($_GET['mp_order_id']) ? intval($_GET['mp_order_id']) : __( '(please contact our support team to be informed of your Order ID)', JR_FX_i18N_DOMAIN );
	return "<span class='{$class}'>#$order_id</span>";
}

 
/*
 * Description:	Shortcode to show the Order Cost
 * @returns HTML text
 */  
function jr_fx_order_cost_sc( $atts ){

	// parameters
	extract( shortcode_atts( array(
		'class' => ''
	), $atts ) );

	if ( intval($_GET['mp_order_cost']) == 0 )
		$cost = 0;
	else 
		$cost = $_GET['mp_order_cost'];

	$order_cost = ! empty($_GET['mp_order_cost']) ? appthemes_get_price($cost): __( '(please contact our support team to be informed about your Order Cost)', JR_FX_i18N_DOMAIN );
	return "<span class='{$class}'>".$order_cost."</span>";
}



/*************
 * FEATURES
 ************/

/*
 *********************************************************************************************************************
 * FEATURE: 			First # job(s) are free (pro)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and return true if an action or filter is needed
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns True|False  If action or filter is needed, returns True, else, returs False
 */
function jr_fx_validate_feat_jobs_free_offer( $hook = '',  $action = 'no action' ) {
	global $app_abbr, $jr_fx_log, $post, $posted, $current_user;

	get_currentuserinfo();

	$log_message = '**** CHECKING RULES *** FEATURE [ First # Job(s) Free ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	if ( ! jr_charge_job_listings() )
		return false;

	# get total job offers option
	$total_job_offers = jr_fx_validate( '_int_jobs_free_offer' );

	$payment_due = jr_fx_payment_due();

	// only give offer if there are fees associated with the listing
	// and charge only featured costs to the offer for JR 1.5.2 or Older
	if ( $total_job_offers && $payment_due ):

			# check if this call is made before inserting the job post to adjust posts total
			//$plus_one = is_page( jr_fx_get_page_id( 'submit_page_id' ) );
	
			# check total pending+published posts against job offers
			$total_posts =  jr_fx_count_user_posts( $current_user->ID, array( 'publish', 'pending', 'expired' ) );

			# check total posts against total job offers
			if ( $total_posts < $total_job_offers ) {

				# log
				$jr_fx_log->write_log( $log_message . ' | Extra Info: total_posts('.$total_posts.'+1) <= total_offers ('.$total_job_offers.')');

				### BEGIN FEATURE: First # job(s) are free - Message
				# set notice message only after job is sumbited
				//if ( $hook == 'wp_insert_post_data' || $action == 'redirect_myjobs()' ) {
					//if ( jr_fx_validate( '_text_notice_free_offer' ) )	jr_fx_notices( '_text_notice_free_offer' );
				//}
				### END FEATURE

				# an offer is entitled
				return true;

			} else {
				$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET [ total_posts('.$total_posts.' + 1) > total_offers ('.$total_job_offers.') ]');
			}

	endif;

	# no offers here
	return false;
}


/*
 *********************************************************************************************************************
 * FEATURE: 			Custom Field - Job Duration (pro)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and updates the expire metadata (_expires) on the DB
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @param $post_id		The Post ID
 * @param $user			The User Object from get_currentuserinfo();
 * @returns none  		
 */  
function jr_fx_validate_exec_feat_field_expire_days ( $hook = '', $action = 'no action', $post_id, $user ) {
	global $jr_fx_log;

	$log_message = '**** CHECKING RULES *** FEATURE [ Custom Field - Job Duration ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';	

	# get the selected option for this feature
	$field = jr_fx_validate('_opt_jobs_duration_field') ;

	# get min and max days for this feature
	$min_days = jr_fx_validate( '_int_jobs_min_duration' );
	$max_days = jr_fx_validate( '_int_jobs_max_duration' );

	$min_days = ($min_days?$min_days:0);
	$max_days = ($max_days?$max_days:($min_days?30:0));

	if ( $field != 'no' && isset($_POST['jr_fx_job_duration_field']) && $_POST['jr_fx_job_duration_field'] >= $min_days  && $_POST['jr_fx_job_duration_field'] <= $max_days ) {
		if  ( $field != 'admin' || ( $field == 'admin' && user_can($user->ID, 'manage_options') ) ):

			# checks if the expire date already exists and updates it
			# if a payment is due stores the days on the meta data table to update later
			update_post_meta( $post_id, '_jr_fx_expire_days', intval($_POST['jr_fx_job_duration_field']) );

			# log
			$jr_fx_log->write_log($log_message . ' | Extra Info: _expires = ' .  (isset($date)?date("Y-m-d", $date):'-') );

		endif;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
	}
}

/*
 *********************************************************************************************************************
 * FEATURE: 			Custom Field - Applications Recipient - updates metadata (pro)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and adds new metadata (_apps_recipient_address) on the DB
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @param $post_id		The Post ID
 * @param $user			The User Object from get_currentuserinfo();
 * @returns none 
 */
function jr_fx_validate_exec_feat_field_apps_recipient ( $hook = '', $action = 'no action', $post_id, $user ) {
	global $jr_fx_log;

	$log_message = '**** CHECKING RULES *** FEATURE [ Custom Field - Applications Recipient - Set Email] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	# get the selected option for this feature
	$field = jr_fx_validate( '_opt_jobs_recipient_field' );

	if ( $field == 'yes' && isset($_POST['jr_fx_field_email_applications']) ) {

			# checks if the custom recipient email address already exists and updates it
			if ( get_post_meta( $post_id, JR_FX_FIELDS_PREFIX . '_apps_recipient_address', true ) ) {
				update_post_meta( $post_id, JR_FX_FIELDS_PREFIX . '_apps_recipient_address', $_POST['jr_fx_field_email_applications'] );
			}else {
				add_post_meta( $post_id, JR_FX_FIELDS_PREFIX . '_apps_recipient_address', $_POST['jr_fx_field_email_applications'], true );
			}
			# log
			$jr_fx_log->write_log($log_message . ' | Extra Info: '.JR_FX_FIELDS_PREFIX.'_apps_recipient_address = ' .  $_POST['jr_fx_field_email_applications'] );
	} else
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
}

/*
 *********************************************************************************************************************
 * FEATURE: 			Custom Field - Applications Recipient - gets metadata (pro)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns the new recipient email address
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @param $post_id		The Post ID
 * @returns $email  	Returns	the new email address
 */
function jr_fx_validate_feat_field_apps_recipient( $hook = '', $action = 'no action', $mail, $post_id  ) {
	global $jr_fx_log;

	$log_message = '**** CHECKING RULES *** FEATURE [ Custom Field - Applications Recipient - Get Email ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	$email = '';
	#check for email signature
	#check for email recipient field. Only add the recipient field to the first email(the one with Headers)	
	if ( jr_fx_validate('_opt_jobs_recipient_field', 'yes') && $mail['headers'] ) {

		$email = get_post_meta($post_id, JR_FX_FIELDS_PREFIX . '_apps_recipient_address', true);

		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_apps_recipient_address = ' .$email );
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
	}
	return $email;
}

/*
 *********************************************************************************************************************
 * FEATURE: 			Custom Field - Optional Apply Online - updates user metadata (pro)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and adds new metadata (_apps_recipient_address) on the DB
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @param $post_id		The Post ID
 * @param $user			The User Object from get_currentuserinfo();
 * @returns none
 */ 
function jr_fx_validate_exec_feat_field_optional_apply ( $hook = '', $action = 'no action', $post_id, $user ) {
	global $jr_fx_log;

	$log_message = '**** CHECKING RULES *** FEATURE [ Custom Field - Optional Apply ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	# get the selected option for this feature
	$field = jr_fx_validate( '_opt_jobs_optional_apply_online' );

	if ( $field == 'yes' && isset($_POST['jr_fx_disable_apply_field']) ) {
		update_post_meta( $post_id, 'jr_fx_disable_apply_online', 1);

		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: '.JR_FX_FIELDS_PREFIX.'_disable_apply_field = ' .  $_POST['jr_fx_disable_apply_field'] );
	} else 
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
}

/*
 *********************************************************************************************************************
 * FEATURE: 			Custom Field - Optional Apply With LinkedIn - updates user metadata (pro)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and adds new metadata (_apps_recipient_address) on the DB
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @param $post_id		The Post ID
 * @param $user			The User Object from get_currentuserinfo();
 * @returns none
 */ 
function jr_fx_validate_exec_feat_field_optional_apply_ld ( $hook = '', $action = 'no action', $post_id, $user ) {
	global $jr_fx_log;

	$log_message = '**** CHECKING RULES *** FEATURE [ Custom Field - Optional Apply LinkeIn ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	# get the selected option for this feature
	$field = jr_fx_validate( '_opt_jobs_optional_apply_online' );

	if ( $field == 'yes' && isset($_POST['jr_fx_disable_apply_ld_field']) ) {
		update_post_meta( $post_id, 'jr_fx_disable_apply_linkedin', 1);

		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: '.JR_FX_FIELDS_PREFIX.'_disable_apply_ld_field = ' .  $_POST['jr_fx_disable_apply_ld_field'] );
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
	}
}

/*
 *********************************************************************************************************************
 * FEATURE: 			Email Signature (pro)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns the email signature to use
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns $signature 	Returns	the email signature
 */
function jr_fx_validate_feat_email_signature( $hook = '', $action = 'no action' ) {
	global $jr_fx_log;

	$log_message = '**** CHECKING RULES *** FEATURE [  Email Signature ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	$mail_signature = jr_fx_validate( '_text_email_signature') ;
	$signature = '';

	# check for email signature
	if ( $mail_signature ) {

		# variables that can be used by admin to dynamically fill in email content
		$find = array('/%blogname%/i', '/%siteurl%/i');	
		$replace = array(get_option('blogname'), get_option('siteurl'));

		# search and replace any user added variable fields in the body
		$signature = stripslashes( $mail_signature );
		$signature = preg_replace( $find, $replace, $signature );
		$signature = preg_replace( "/%.*%/", "", $signature );
		$signature = "\n\n" . $signature ;

		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_text_email_signature = ' .$signature );
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
	}
	return $signature;
}

/*
 *********************************************************************************************************************
 * FEATURE: 			Job Listings Thumbs (pro)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	False if the feature is set to 'no', with other values returns True 
 */
function jr_fx_validate_feat_job_list_thumbs(  $hook = '', $action = 'no action' ){
	global $jr_fx_log;

	$log_message = '**** CHECKING RULES *** FEATURE [  Job List Thumbs ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	if ( !jr_fx_validate( '_opt_listings_logo','no' ) ) {
		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_opt_listings_logo = ' . jr_fx_validate( '_opt_listings_logo' ) );
		return true;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}

/*
 *********************************************************************************************************************
 * FEATURE: 			Hide Company Logo (pro)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	False if the feature is set to 'no', with other values returns True 
 */ 
function jr_fx_validate_feat_hide_company_logo( $hook = '', $action = 'no action' ) {
	global $jr_fx_log;

	$log_message = '**** CHECKING RULES *** FEATURE [  Hide Company Logo ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	if ( jr_fx_validate( '_opt_jobs_company_logo', 'yes' ) ) {
		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_opt_jobs_company_logo = ' . jr_fx_validate( '_opt_jobs_company_logo' ) );
		return true;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}

/*********************************************************************************************************************
 * FEATURE: 			Other Gateways - PRO
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	False if the feature is set to 'no', with other values returns True 
 */ 
function jr_fx_validate_feat_other_gateway(  $hook = '', $action = 'no action' ){
	global $jr_fx_log;

	$log_message = '**** CHECKING RULES *** FEATURE [ Other Gateways ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	if ( jr_fx_validate( '_opt_gateway_google' ,'yes' ) || jr_fx_validate( '_opt_gateway_2checkout' ,'yes' ) ||  jr_fx_validate( '_opt_gateway_authorize' ,'yes' ) ||  jr_fx_validate( '_opt_gateway_manual' ,'yes' ) ) {
		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_opt_gateway = ' . jr_fx_validate( '_opt_gateway' ) );
		return true;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}

/*********************************************************************************************************************
 * FEATURE: 			Other Gateways - ACTIVE IPNS - PRO
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	False if the feature is set to 'no', with other values returns True 
 */
function jr_fx_validate_feat_other_gateway_ipn(  $hook = '', $action = 'no action' ){
	global $jr_fx_log;

	$log_message = '**** CHECKING RULES *** FEATURE [ Other Gateways IPNS ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';
		
	if ( jr_fx_validate( '_opt_gateway_google_ipn' ,'yes' )  || jr_fx_validate( '_opt_gateway_2checkout_ipn' ,'yes' ) ||  jr_fx_validate( '_opt_gateway_authorize_ipn' ,'yes' ) ) {
		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: ' );
		return true;
	} else {
		# log
		 $jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}
/*********************************************************************************************************************
 * FEATURE: 			Browse Resume Lists for JobPacks / Paid Listings (PRO)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	False if the feature is set to 'no', with other values returns True 
 */
function jr_fx_validate_feat_resume_list_browse(  $hook = '', $action = 'no action' ) {
	global $jr_fx_log, $current_user, $app_abbr;

	get_currentuserinfo();

	$log_message = '**** CHECKING RULES *** FEATURE [ Browse Resume Lists for JobPacks/Paid Listings ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	if ( !is_user_logged_in() ) return false;

	# validate Resume browsing only if the option 'Job Listers Only' is selected 
	$resume_list_browse_opt =  get_option( $app_abbr . '_resume_listing_visibility');
	$can_submit_job = current_user_can('can_submit_job');

	if ( $resume_list_browse_opt != 'listers' || ($resume_list_browse_opt == 'listers' && !$can_submit_job) || jr_fx_validate( '_opt_resume_list_visibility', 'jr' ) ) return false;

	if ( jr_fx_validate( '_opt_resume_list_visibility' ,'jobpack' ) ) {
		$user_packs = jr_fx_paid_jobs_for_user( $current_user->ID );
	}

	if ( ( jr_fx_validate( '_opt_resume_list_visibility' ,'jobpack' ) && empty($user_packs) ) ||
		  ( jr_fx_validate( '_opt_resume_list_visibility', 'paid_all' ) && ! jr_fx_count_user_posts( $current_user->ID ) ) ||
		  ( jr_fx_validate( '_opt_resume_list_visibility', 'paid_live' ) && ! jr_fx_count_user_posts( $current_user->ID, 'live' ) ) ) {

		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_opt_resume_list_visibility = ' . jr_fx_validate( '_opt_resume_list_visibility' ) );
		return true;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}

/*********************************************************************************************************************
 * FEATURE: 			Browse Single Resumes for JobPacks/Paid Listings (PRO)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	False if the feature is set to 'no', with other values returns True 
 */
function jr_fx_validate_feat_resume_browse(  $hook = '', $action = 'no action' ) {
	global $jr_fx_log,  $current_user, $app_abbr;

	$log_message = '**** CHECKING RULES *** FEATURE [ Browse Single Resumes for JobPacks/Paid Listings ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	if ( !is_user_logged_in() ) return false;

	# validate Resume browsing only if the option 'Job Listers Only' is selected 
	$resume_browse_opt =  get_option( $app_abbr . '_resume_visibility');
	$can_submit_job = current_user_can('can_submit_job');

	if ( $resume_browse_opt != 'listers' || ($resume_browse_opt == 'listers' && !$can_submit_job) || jr_fx_validate( '_opt_resume_visibility', 'jr' ) ) return false;

	if ( jr_fx_validate( '_opt_resume_visibility' ,'jobpack' ) ) {
		$user_packs = jr_fx_paid_jobs_for_user( $current_user->ID );
	}

	if  ( ( jr_fx_validate( '_opt_resume_visibility' ,'jobpack' ) && empty($user_packs) ) || 
		 ( jr_fx_validate( '_opt_resume_visibility', 'paid_all' ) && ! jr_fx_count_user_posts( $current_user->ID ) ) ||
		 ( jr_fx_validate( '_opt_resume_visibility', 'paid_live' ) && ! jr_fx_count_user_posts( $current_user->ID, 'live' ) ) ) {
		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_opt_resume_visibility = ' . jr_fx_validate( '_opt_resume_visibility' ) );
		return true;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}

/*********************************************************************************************************************
 * FEATURE: 			Upload CV's (PRO)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	False if the feature is set to 'no', with other values returns True 
 */ 
function jr_fx_validate_feat_cvs_upload(  $hook = '', $action = 'no action' ) {
	global $jr_fx_log, $userdata;

	$log_message = '**** CHECKING RULES *** FEATURE [ Upload CV\'s ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	if ( !is_user_logged_in() ) return false;

	if ( jr_fx_validate( '_opt_resume_upload_cvs', 'yes' ) ) {

			# check for S2Member restrictions - if the validation returns TRUE then user is NOT allowed access
			/*
			if ( function_exists('jr_fx_validate_feat_s2member') && jr_fx_validate_feat_s2member( '_opt_resume_upload_s2member_level', $hook, 's2member - resume upload' ) ) {
				return false; // block user access to CV uplaod
			}*/

			# log
			$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_opt_resume_upload_cvs = ' . jr_fx_validate( '_opt_resume_upload_cvs' ) );
			return true;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}

/*
 *********************************************************************************************************************
 * FEATURE: 			Attach Preset CV (pro)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns the preset CV
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns $preset_cv 	Preset CV attachment
 */ 
function jr_fx_validate_feat_attach_preset_cv( $hook = '', $action = 'no action' ) {
	global $jr_fx_log;

	$log_message = '**** CHECKING RULES *** FEATURE [  Attach Preset CV ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	$jr_fx_uploaded_cv = '';

	# check if the upload CV 's feature is valid and active
	if ( function_exists('jr_fx_validate_feat_cvs_upload') && jr_fx_validate_feat_cvs_upload() && jr_fx_validate( '_opt_resume_upload_attach', 'yes' ) ) {

		$jr_fx_uploaded_cv = '';
		if ( isset($_POST['jr_fx_sel_uploaded_cv']) ) :
			$jr_fx_cv_id = (int) $_POST['jr_fx_sel_uploaded_cv'];
			
			$jr_fx_uploaded_cv = get_attached_file( $jr_fx_cv_id );		
		endif;

		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: Preset CV = ' . $jr_fx_uploaded_cv );
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
	}
	return $jr_fx_uploaded_cv;
}

/*
 *********************************************************************************************************************
 * FEATURE: 			S2Member Integration (pro)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $feature		(required) The feature to check for the correct access level.
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	FALSE if the user HAS the right access level. TRUE if the user DOES NOT have access. 
 * 						Defaults to FALSE (do not block access) to avoid blocking when feature is not in use.
 */
function jr_fx_validate_feat_s2member( $feature,  $hook = '', $action = 'no action' ) {
	global $jr_fx_log;

	$log_message = '**** CHECKING RULES *** FEATURE [ S2Member ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	if ( JR_FX_S2MEMBER_EXIST && jr_fx_validate( '_opt_s2member_restrict_access','yes' ) ) {

			$access_level = jr_fx_validate( $feature );

			# access level #0 => FREE - do not block acess
			if ( $access_level == 0 ) return false;

			if ( defined ("S2MEMBER_CURRENT_USER_ACCESS_LEVEL" ) && S2MEMBER_CURRENT_USER_ACCESS_LEVEL >= $access_level ) {
				// do not block access
				return false;
			} else {
				// block access
			}

			# log
			$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_opt_resume_visibility = ' . jr_fx_validate( '_opt_resume_visibility' ) );
			return true;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}


/*********************************************************************************************************************
 * FEATURE: 			Download CV's (PRO)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	False if the feature is set to 'no', with other values returns True 
 */
function jr_fx_validate_feat_cvs_download(  $hook = '', $action = 'no action' ) {
	global $jr_fx_log, $userdata;

	$log_message = '**** CHECKING RULES *** FEATURE [ Download CV\'s ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	if ( !is_user_logged_in() ) return false;

	if ( jr_fx_validate( '_opt_resume_cv_download') && !jr_fx_validate( '_opt_resume_cv_download', 'no' ) ) {
		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_opt_resume_cv_download = ' . jr_fx_validate( '_opt_resume_cv_download' ) );
		return true;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}


/*********************************************************************************************************************
 * FEATURE: 			Apply with LinkedIn (PRO)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	False if the feature is set to 'no', with other values returns True 
 */
function jr_fx_validate_feat_linkedin_apply(  $hook = '', $action = 'no action' ) {
	global $jr_fx_log, $post;

	$log_message = '**** CHECKING RULES *** FEATURE [ LinkedIn Apply ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	$ld_option = jr_fx_validate( '_opt_integration_linkedin_apply');

	if ( 'members' == $ld_option && !is_user_logged_in() ) return false;

	if ( 'optional' == $ld_option ) { 
		//$disable_apply_ld = get_user_meta( get_current_user_id(), 'jr_fx_disable_apply_linkedin-' . $post->ID, true);
		$disable_apply_ld = jr_fx_selective_disable('jr_fx_disable_apply_linkedin', $post->ID);

		if ( $disable_apply_ld ) return false;
	}

	if ( $ld_option && 'no' != $ld_option )  {
		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_opt_integration_linkedin_apply = ' . $ld_option );
		return true;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}

/*********************************************************************************************************************
 * FEATURE: 			Persistent Logos (PRO)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	False if the feature is set to 'no', with other values returns True 
 */ 
function jr_fx_validate_feat_persistent_logo(  $hook = '', $action = 'no action' ) {
	global $jr_fx_log, $userdata;	

	$log_message = '**** CHECKING RULES *** FEATURE [ Persistent Logos ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	if ( !current_user_can('can_submit_job') ) return false; 

	$jr_fx_option = jr_fx_validate( '_opt_jobs_lister_persistent_logo');

	if ( $jr_fx_option &&  'no' != $jr_fx_option ) {
		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_opt_jobs_lister_persistent_logo = ' . jr_fx_validate( '_opt_jobs_lister_persistent_logo' ) );
		return true;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}

/*********************************************************************************************************************
 * FEATURE: 			Persistent Logos Replace Gravatar (PRO)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	False if the feature is set to 'no', with other values returns True 
 */
function jr_fx_validate_feat_persistent_logo_gravatar(  $hook = '', $action = 'no action' ) {
	global $jr_fx_log, $userdata;

	$log_message = '**** CHECKING RULES *** FEATURE [ Persistent Logos Replace Gravatar ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	$jr_fx_option = jr_fx_validate( '_opt_jobs_lister_persistent_logo');

	if ( $jr_fx_option && 'no' != $jr_fx_option && jr_fx_validate( '_opt_jobs_lister_persistent_logo_gravatar', 'yes' ) ) {
		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_opt_jobs_lister_persistent_logo_gravatar = ' . jr_fx_validate( '_opt_jobs_lister_persistent_logo_gravatar' ) );
		return true;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}

/*********************************************************************************************************************
 * FEATURE: 			Upload CV (PRO)
 ********************************************************************************************************************* 
 * Description:			Process uploaded CV's by checking for CV keys on $_FILES and $_POST
 * @param none
 * @returns none
 */
function jr_fx_process_cv_upload() {

	# check for uploaded CV's and process them
	if ( isset($_POST['jr_fx_resume_post_id']) && isset($_FILES['jr_fx_upload_cv_file']) && function_exists('jr_fx_validate_feat_cvs_upload') && jr_fx_validate_feat_cvs_upload() ) {
		jr_fx_process_cv_file( (int)$_POST['jr_fx_resume_post_id'], $_FILES );
	}

	# check for deleted CV's and process them
	if ( isset($_POST['jr_fx_delete_cv']) && isset($_POST['jr_fx_cv_uid']) && isset($_POST['jr_fx_cv_filename']) ) {
		jr_fx_delete_cv( (int)$_POST['jr_fx_delete_cv'], (int)$_POST['jr_fx_cv_uid'] );
	}
}

/*********************************************************************************************************************
 * FEATURE: 			Upload CV (PRO)
 ********************************************************************************************************************* 
 * Description:			Shows the list of Uplodaded CV's on the user Dashboard hidden on the footer
 * @param none
 * @returns HTML		CV's listings 
 */
function jr_fx_show_cv_list () {
	global $post, $app_abbr, $user_ID;

	if ( isset($post) && is_page( jr_fx_get_page_id( 'dashboard_page_id' ) ) && function_exists('jr_fx_validate_feat_cvs_upload') && jr_fx_validate_feat_cvs_upload() ):
?>
	<div id="jr_fx_dashboard_cv_list_temp" style="display: none">
	 <div id="jr_fx_dashboard_cv_list">
		<div class="clear"></div>
		<br/>
		<h2><?php _e('My Uploaded Resumes', JR_FX_i18N_DOMAIN); ?></h2>
		<form name="jr_fx_list_cv_form" class="jr_fx_list_cv_form" method="post" action="<?php echo get_permalink(); ?>#resumes" >
		<table cellpadding="0" cellspacing="0" class="data_list">
			<thead>
				<tr>
					<th colspan="3"><?php _e('Resume Filename',JR_FX_i18N_DOMAIN); ?></th>
					<th class="center"><?php _e('Date Created',JR_FX_i18N_DOMAIN); ?></th>
					<th class="right"><?php _e('Actions',JR_FX_i18N_DOMAIN); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
					$uploaded_cvs_list = jr_fx_uploaded_cvs_list($user_ID);
				?>
				<?php if ( $uploaded_cvs_list ) : ?>
				
					<?php foreach ($uploaded_cvs_list as $uploaded_cv ) { ?>
						<tr>
							<td valign="top"><div class="jr_fx_cv_ico">&nbsp;</div></td>
							<td colspan=2><strong><a href="<?php echo $uploaded_cv['url_filename']; ?>"><?php echo $uploaded_cv['name']; ?></a></strong><br/><span class="jr_fx_cv_parent"><strong><?php _e('Parent: ',JR_FX_i18N_DOMAIN) ?>:</strong><a href="<?php echo $uploaded_cv['parent_url']; ?>"> <?php echo $uploaded_cv['parent_title']; ?></a></td>
							
							<td class="date"><strong><?php echo date('j M', strtotime($uploaded_cv['date'])); ?></strong> <span class="year"><?php echo date('Y', strtotime($uploaded_cv['date'])); ?></span></td>

							<td class="actions"><a href="#" id="jr_fx_cv_list_action_<?php echo $uploaded_cv['id']; ?>" class="delete delete_cv"><?php _e('Delete',JR_FX_i18N_DOMAIN); ?></a></td>
							<input type="hidden" name="jr_fx_delete_cv" value="<?php echo $uploaded_cv['id'] ?>">
							<input type="hidden" name="jr_fx_cv_filename" value="<?php echo $uploaded_cv['name'] ?>">
							<input type="hidden" name="jr_fx_cv_uid" value="<?php echo $user_ID ?>">
						</tr>
					<?php 
						};
				else :
					?><tr><td colspan="6"><?php _e('No uploaded files found.',JR_FX_i18N_DOMAIN); ?></td></tr><?php
				endif; 
				
				wp_reset_query();
				
				?>
			</tbody>
		</table>
		</form>
		<?php
				# max CV's allowed
				$jr_fx_max_cvs = jr_fx_validate('_opt_resume_upload_cvs_max'); 
				$args = array(
						'ignore_sticky_posts' => 1,
						'posts_per_page' => -1,
						'author' => $user_ID,
						'post_type' => 'resume'
				);
				$my_query = new WP_Query($args);

				if ($my_query->have_posts()) :
					$count = 0;
					$count_attachments = 0;
		?>
					<form class="jr_fx_submit_cv_form" method="post" action="<?php echo get_permalink(); ?>#resumes" enctype="multipart/form-data">				
						<p><strong><?php _e('Online Resumes without attachments:' , JR_FX_i18N_DOMAIN);?></strong></p>
						<select class="jr_fx_select_cv_upload" name="jr_fx_resume_post_id">
							<?php while ($my_query->have_posts()) : ?>
								<?php $my_query->the_post(); ?>
								<?php if ( !jr_fx_cv_attachments( $my_query->post->ID ) ) { ?>
									<option  value="<?php echo $my_query->post->ID; ?>"><?php echo the_title(); ?></option>
								<?php 
									$count++;
									}
								?>
								<?php
								endwhile;
								
								?>
								<?php if ( $count == 0 ) { ?>
										<option  value=""><?php echo _e('< No more Resumes to attach >', JR_FX_i18N_DOMAIN); ?></option>
								<?php } ?>
						</select> 
						<?php
							$count_attachments = count(jr_fx_cv_attachments( $my_query->post->ID ));
							if ( $count > 0 && ( !$jr_fx_max_cvs || (  $count_attachments < $jr_fx_max_cvs ) ) &&
							    ( function_exists('jr_fx_validate_feat_s2member') && !jr_fx_validate_feat_s2member( '_opt_resume_upload_s2member_level', 'get_footer', 's2member' ) ) ) { 
						?>
								<input type="file" class="text" name="jr_fx_upload_cv_file" id="jr_fx_upload_cv_file">
								<p>
								<?php 
									global $jr_fx_cv_allowed_ext_list;

									$allowed_ext = '';
									foreach ($jr_fx_cv_allowed_ext_list as $cv_ext) 
											$allowed_ext .= $cv_ext . ' ';

									echo sprintf(__('Allowed extensions: %s', JR_FX_i18N_DOMAIN), '<i>'. str_replace( ' ',', ', trim($allowed_ext)) .'</i>' ); 
								?>
								</p>
						<?php } ?>
						<p class="jr_fx_upload_cv_note"><?php echo sprintf(__("You can attach CV/Resumes files to your Online Resumes%s. These will be available when applying for jobs.", JR_FX_i18N_DOMAIN), ($jr_fx_max_cvs?" (max: $jr_fx_max_cvs)":"") ); ?></p>				
					</form>
		<?php
				endif;

		?>
		</div>
	</div>
	<?php
	endif;
}

/*********************************************************************************************************************
 * FEATURE: 			Upload CV (PRO)
 ********************************************************************************************************************* 
 * Description:			Check for uploaded CV
 * @param post_id		The parent post id
 * @returns array		Attachments array
 */ 
function jr_fx_cv_attachments( $post_id = '' ) {
	$args = array( 'post_parent' => $post_id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'order' => 'ASC', 'numberposts' => -1, 'post_mime_type' => 'application') ;
	return get_children( $args );
}

/*********************************************************************************************************************
 * FEATURE: 			Upload CV (PRO)
 ********************************************************************************************************************* 
 * Description:			Get the uploaded CV's list
 * @param user_id		Resume ID
 * @returns array		Array list of uploaded CV's
 */
function jr_fx_uploaded_cvs_list( $user_id, $resume_id = '' ) {
	global $wpdb;

	$args = array(
			'ignore_sticky_posts' => 1,
			'posts_per_page' => -1,
			'author' => ($user_id?$user_id:''),
			'post_type' => 'resume',
			'p' => ($resume_id!=''?$resume_id:''),
	);
	$my_query = new WP_Query($args);

	$count = 0;
	$jr_fx_cv_list = '';
	if ($my_query->have_posts()) :
		while ($my_query->have_posts()) : 
			 $my_query->the_post(); 
			if ( $files = get_children( array('post_parent' => $my_query->post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => array('application','text/plain'), 'order' => 'ASC', 'numberposts' => 1) ) ) {

				foreach ( $files as $file ) {
					# get the upload dir
					$upload_dir = wp_upload_dir();
					$upload_dir = jr_fx_cv_upload_dir( $upload_dir );
				
					$filename = get_post_meta( $file->ID , '_wp_attached_file', true );
					$cv['id'] = $file->ID;
					$cv['date'] = $file->post_date;
					$cv['url'] = $upload_dir['url'];
					$cv['path_filename'] = $upload_dir['path'] . '/' . basename($filename);

					// check if upload paths are valid
					if ( !file_exists($cv['path_filename']) ):
						$upload_dir['path'] = str_replace($upload_dir['subdir'], '', $upload_dir['path']);
						$upload_dir['url'] = str_replace($upload_dir['subdir'],'',$upload_dir['url']);
						$cv['path_filename'] = $upload_dir['path'] . '/' . basename($filename);
					endif;

					$cv['path_filename'] = $upload_dir['path'] . '/' . basename($filename);
					$cv['url_filename'] = $upload_dir['url'] . '/' . basename($filename);
					
					$cv['name'] = basename($filename);
					$cv['parent_title'] = get_the_title( $file->post_parent );
					$cv['parent_url'] = get_permalink( $file->post_parent );
					$jr_fx_cv_list[] = $cv;		
					
				}
			} else
				$count++;
		endwhile;

	endif;
	return $jr_fx_cv_list;
}

/*********************************************************************************************************************
 * FEATURE: 			Upload CV (PRO)
 ********************************************************************************************************************* 
 * Description:			CV's upload dir filter
 * @param pathdata		The pathdata returned by the upload_dir filter
 * @returns pathdata	the filtered pathdata
 */
function jr_fx_cv_upload_dir( $pathdata ) {

	# set the uploaded CV's path 
	$current_user = wp_get_current_user();

	if ( !$pathdata['subdir'] ) {
		$pathdata['subdir'] = '/uploads';
	}
	$subdir = $pathdata['subdir'].'/'.JR_FX_DIR_UPLOADED_CVS . '/'.$current_user->user_login;
	$pathdata['path'] = str_replace($pathdata['subdir'], $subdir, $pathdata['path']);
	$pathdata['url'] = str_replace($pathdata['subdir'], $subdir, $pathdata['url']);
	$pathdata['subdir'] = str_replace($pathdata['subdir'], $subdir, $pathdata['subdir']);
		
	return $pathdata;
}

/*********************************************************************************************************************
 * FEATURE: 			Upload CV (PRO)
 ********************************************************************************************************************* 
 * Description:			Process uploaded CV
 * @param post_id		The parent post_id
 * @param files			File info array
 * @returns none		
 */
function jr_fx_process_cv_file( $post_id ) {
	global $post, $app_abbr, $jr_fx_cv_allowed_ext_list;

	$errors = new WP_Error();

	// Check file extensions
	$allowed = $jr_fx_cv_allowed_ext_list;

	if (isset($_FILES['jr_fx_upload_cv_file']) && !empty($_FILES['jr_fx_upload_cv_file']['name'])) {
		$extension = strtolower(substr(strrchr($_FILES['jr_fx_upload_cv_file']['name'], "."), 1));
		if (!in_array($extension, $allowed)) $errors->add('submit_error', __('Only pdf, zip, doc, txt and rtf files are allowed.', JR_FX_i18N_DOMAIN));
	}

	if ($errors && sizeof($errors)>0 && $errors->get_error_code()) {
		// There are errors!
	} else {
		$attachments = array();
		$attachment_urls = array();
		// Continue, upload files
		if ((isset($_FILES['jr_fx_upload_cv_file']) && !empty($_FILES['jr_fx_upload_cv_file']['name'])) ) {

			// Find max filesize in bytes - we say 10mb becasue the file will be attached to an email, also checks system variables in case they are lower
			$max_sizes = array('10485760');
			if ((ini_get('post_max_size'))) $max_sizes[] = let_to_num(ini_get('post_max_size'));
			if ((ini_get('upload_max_filesize'))) $max_sizes[] = let_to_num(ini_get('upload_max_filesize'));
			if ((WP_MEMORY_LIMIT)) $max_sizes[] = let_to_num(WP_MEMORY_LIMIT);

			$max_filesize = min( $max_sizes );

			if (($_FILES["jr_fx_upload_cv_file"]["size"]) > $max_filesize) :
				$errors->add('submit_error', 'Attachments too large. Maximum file size for all attachments is '.($max_filesize/(1024*1024)).'MB');
			else :

				/** WordPress Administration File API */
				include_once(ABSPATH . 'wp-admin/includes/file.php');
				/** WordPress Media Administration API */
				include_once(ABSPATH . 'wp-admin/includes/media.php');
	
				add_filter('upload_dir', 'jr_fx_cv_upload_dir');
				
				$time = current_time('mysql');
				$overrides = array('test_form'=>false);

				if (isset($_FILES['jr_fx_upload_cv_file']) && !empty($_FILES['jr_fx_upload_cv_file']['name'])) {
				
					if ( !jr_fx_cv_attachments( $post_id ) ) :
						$file = wp_handle_upload($_FILES['jr_fx_upload_cv_file'], $overrides, $time);
						if ( !isset($file['error']) ) {
							$attachments[] = $file['file'];
							$attachment_urls[] = $file['url'];
						} else {
							$errors->add('submit_error', $file['error'] );
						}
					else:
						$errors->add('submit_error', __('Resume already uploaded!', JR_FX_i18N_DOMAIN));
					endif;
				}
			endif;

		}

	}

	if ($errors && sizeof($errors)>0 && $errors->get_error_code()) {}
	else {
		// save file to database	
		$errors = jr_fx_db_save_cv( $post_id, $file['file'] );
	}
	
	if ($errors && sizeof($errors)>0 && $errors->get_error_code()) {
		$message = $errors->errors['submit_error'][0];
		$message = jr_fx_format_notice( $message, 'error' );
	} else {
		$message = __('Your Resume was successfully uploaded.', JR_FX_i18N_DOMAIN);
		$message = jr_fx_format_notice( $message );
	}
	jr_fx_notices( '+' . $message );
}

/*********************************************************************************************************************
 * FEATURE: 			Upload CV (PRO)
 ********************************************************************************************************************* 
 * Description:			Save CV's to database
 * @param post_id		The parent post_id
 * @param filename		CV filename
 * @returns boolean		Database operation result
 */
function jr_fx_db_save_cv( $post_id, $filename ) {
	global $wpdb, $user_ID ;

	$errors = new WP_Error();

	$wp_filetype = wp_check_filetype(basename($filename), null );
	$attachment = array(
		 'post_mime_type' => $wp_filetype['type'],
		 'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
		 'post_content' => '',
		 'post_status' => 'inherit'
	);

	$attach_id = wp_insert_attachment( $attachment, $filename, $post_id );
	// you must first include the image.php file
	// for the function wp_generate_attachment_metadata() to work
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );

	if ( !wp_update_attachment_metadata( $attach_id, $attach_data ) ) :
		// do nothing
		//$errors->add('submit_error', __('Could not save file info to the database!', JR_FX_i18N_DOMAIN));
	endif;

	return $errors;
}

/*********************************************************************************************************************
 * FEATURE: 			Upload CV (PRO)
 ********************************************************************************************************************* 
 * Description:			Delete Resumes attachment
 * @param cv_attach_id	The CV ID
 * @param post_user_id	Posted User ID
 * @returns none
 */
function jr_fx_delete_cv( $cv_attach_id, $post_user_id) {
	global $user_ID;

	$errors = new WP_Error();

	$attachment = get_post( $cv_attach_id );
	if ( $attachment ) :
		if ( $attachment->post_author == $user_ID && $post_user_id == $attachment->post_author ) {
			wp_delete_attachment( $cv_attach_id );
		} else {
			$errors->add('delete_error', __('You dont\'t have permissions to delete this Resume!', JR_FX_i18N_DOMAIN));
		}
	else:
		$errors->add('delete_error', __('Resume already deleted!', JR_FX_i18N_DOMAIN));
	endif;

	if ($errors && sizeof($errors)>0 && $errors->get_error_code()) {
		$message = $errors->errors['delete_error'][0];
		$message = jr_fx_format_notice( $message, 'error' );
	} else {
		$message = __('Your Resume was successfully deleted.', JR_FX_i18N_DOMAIN);
		$message = jr_fx_format_notice( $message );
	}

	jr_fx_notices( '+' . $message );
}

/*********************************************************************************************************************
 * FEATURE: 			Attach CV (PRO)
 ********************************************************************************************************************* 
 * Description:			Output uploaded CV list HTML if user has the right access level
 * @param User ID		Current User ID
 * @returns none
 */
function jr_fx_cv_html_list () {
	global $user_ID, $post;

	if ( isset($post) && $post->post_type == JR_FX_JR_POST_TYPE && function_exists('jr_fx_validate_feat_cvs_upload') && jr_fx_validate_feat_cvs_upload() && jr_fx_validate( '_opt_resume_upload_attach', 'yes' ) ):
		$uploaded_cvs_list = jr_fx_uploaded_cvs_list( $user_ID );
		if ( $uploaded_cvs_list ) : 
	?>	
		<div id="jr_fx_preset_cv_list" style="display: none"> 
		  <div class="jr_fx_uploaded_cvs_wrap">
			<div class="jr_fx_uploaded_cvs_note"><?php _e('Choose uploaded Resumes:',JR_FX_i18N_DOMAIN); ?></div>
			<?php
				$i = 0;
				foreach ($uploaded_cvs_list as $uploaded_cv ) { 
			?>
					<input <?php echo ($i==0?"checked":""); ?> type="radio" class="jr_fx_sel_uploaded_cv" name="jr_fx_sel_uploaded_cv" value="<?php echo $uploaded_cv['id'] ?>"><a href="<?php echo $uploaded_cv['url_filename']; ?>"> <?php echo $uploaded_cv['name']; ?></a>
					<br/><span class="jr_fx_cv_parent"><strong><?php _e('Parent: ',JR_FX_i18N_DOMAIN) ?></strong><a href="<?php echo $uploaded_cv['parent_url']; ?>"> <?php echo $uploaded_cv['parent_title']; ?></a>
					<br/>
			<?php		
					$i++;
				}		
			?>
			<input type="radio" class="jr_fx_sel_uploaded_cv" name="jr_fx_sel_uploaded_cv" value="none"> <?php echo _e('None', JR_FX_i18N_DOMAIN); ?><br/>
		  </div>	
		</div>
	<?php	
	   endif;
	endif;
}

/*********************************************************************************************************************
 * FEATURE: 			Download CV's (PRO)
 ********************************************************************************************************************* 
 * Description:			Output uploaded CV list HTML to download if user has the right access level
 * @param Resume ID		Resume ID
 * @returns none		Echo's HTML resume attachments list
 */
 function jr_fx_get_cv_files () {
	global $user_ID, $post;

	if ( isset($post) && is_singular(JR_FX_JR_RESUME) && function_exists('jr_fx_validate_feat_cvs_download') && jr_fx_validate_feat_cvs_download() ):
		$resume_id = $post->ID;

		$uploaded_cvs_list = jr_fx_uploaded_cvs_list( '', $resume_id );

		if ( $uploaded_cvs_list ) : 
	?>
	<div id="jr_fx_download_cv_list" style="display: none"> 
		<div class="jr_fx_download_cvs_wrap"> 
			<div class="jr_fx_uploaded_cvs_note"><?php _e('Related CV Attachments:',JR_FX_i18N_DOMAIN); ?></div>
			<?php
				$i = 0;
				foreach ($uploaded_cvs_list as $uploaded_cv ) { 
					$cv_attach = get_post($uploaded_cv['id']); 

					if ( $cv_attach->post_parent == $resume_id	) :
			?>
						<form name="jr_fx_frm_cv_download" method="post">
							<input type="hidden" name="jr_fx_download_cv_id" value="<?php echo $uploaded_cv['id']; ?>">
							<a class="jr_fx_cv_file" rel="nofollow" onclick="javascript:document.jr_fx_frm_cv_download.submit();" href="#<?php echo $uploaded_cv['name']; ?>"><?php echo $uploaded_cv['name']; ?></a>
						</form>
			<?php
						$i++;
					endif;
				}
			?>
		</div>	
	</div> 
	<?php	
		endif;
	endif;
}

/*********************************************************************************************************************
 * FEATURE: 			Apply with LinkedIn (PRO)
 ********************************************************************************************************************* 
 * Description:			Output LinkedIn Javascript Apply With
 * @param none			
 * @returns none		Echo's the LinkedIn javascript button
 */
 function jr_fx_linkedin_apply() {
	global $post;

	if( isset($post) && is_singular(JR_FX_JR_POST_TYPE) && function_exists('jr_fx_validate_feat_linkedin_apply') && jr_fx_validate_feat_linkedin_apply() ) :
		$company_name = wptexturize(strip_tags(get_post_meta($post->ID, '_Company', true)));
		$address = get_post_meta($post->ID, 'geo_short_address', true);

		//allow use of the recipient address for admins posting jobs
		$other_recipient_address = get_post_meta($post->ID, JR_FX_FIELDS_PREFIX . '_apps_recipient_address', true);
		if ( current_user_can('manage_options') && $other_recipient_address )
			$email = $other_recipient_address;
		else
			$email = get_the_author_meta('user_email');
?>	
		<li id="jr_fx_linkedin_apply" style="display: none">
			<script
				type="IN/Apply"
				data-companyname="<?php echo $company_name ?>" 
				data-jobtitle="<?php echo $post->post_title; ?>" 
				data-joblocation="<?php echo $address ?>" 
				data-email="<?php echo $email; ?>"
				data-themecolor="<?php echo jr_fx_validate( '_text_integration_linkedin_color' ); ?>" 
				data-size="<?php echo jr_fx_validate( '_opt_integration_linkedin_apply_size' ); ?>" 
				data-showText="<?php echo (jr_fx_validate( '_opt_integration_linkedin_apply_text' ) == 'yes'?'true':'false'); ?>" >
			</script>
		</li>
<?php
	endif;
}


 // HELPER FUNCTIONS
 
 // handle profiles company logo upload
function jr_fx_company_logo_upload_dir( $pathdata ) {
	$subdir = '/company_logos'.$pathdata['subdir'];
	$pathdata['path'] = str_replace($pathdata['subdir'], $subdir, $pathdata['path']);
	$pathdata['url'] = str_replace($pathdata['subdir'], $subdir, $pathdata['url']);
	$pathdata['subdir'] = str_replace($pathdata['subdir'], $subdir, $pathdata['subdir']);
	return $pathdata;
}


/*********************************************************************************************************************
 * FEATURE: 			Track job applications
 * HOOKS INTO:			wp_head, init, add_meta_boxes, jr_dashboard_tab_after, jr_dashboard_tab_content
 						edit_user_profile, wp_ajax_jr_fx_get_job_applications
 ********************************************************************************************************************* 
 * Description:			Track job applications
 */
 
// handle 'Apply Online' job applications
function jr_fx_handle_job_applications() {
	global $jr_fx_log;

	if ( ! current_theme_supports( 'jr-fx-job-applications' ) )
		return;

	$post = get_queried_object();

	if ( ! isset($_POST['apply_to_job']) || ! isset($post) || APP_POST_TYPE != $post->post_type || ! is_user_logged_in() )
		return;

	if ( !empty($_POST['your_online_cv']) )
		$meta['linked_resume'] = intval( $_POST['your_online_cv'] );

	if ( !empty($_POST['jr_fx_sel_uploaded_cv']) )
		$meta['attached_resume'] = intval( $_POST['jr_fx_sel_uploaded_cv'] );

	if ( !empty($_FILES['your_cv']) )
		$meta['uploaded_resume'] = $_FILES['your_cv']['name'];

	if ( !empty($_FILES['your_coverletter']) )
		$meta['uploaded_cover_letter'] = $_FILES['your_coverletter']['name'];

	jr_fx_set_p2p_application_connection( $post->ID, get_current_user_id(), $meta );
}

// handle 'LinkedIn' job applications
function jr_fx_handle_linkedin_job_applications() {
	global $jr_fx_log;

	if ( ! current_theme_supports( 'jr-fx-job-applications' ) )
		exit;

	check_ajax_referer( JR_FX_PLUGIN_BASE_NAME, 'security' );

	$post_id = intval($_POST['post_id']);

	$jr_fx_log->write_log( 'jr_fx_handle_linkedin_job_applications() - post_id = ' . $post_id );

	jr_fx_set_p2p_application_connection( $post_id, get_current_user_id(), array( 'application_type' => array( 'linkedin' ) ) );

	$data['success'] = 1;
	echo json_encode($data);
	exit;
}

function jr_fx_set_p2p_application_connection( $post_id, $user_id = 0, $meta = array() ) {
	global $jr_fx_log;

	if ( ! $user_id ) $user_id = get_current_user_id();

	$default_meta = array( 
		'date' 					=> current_time('mysql'),
		'application_type' 		=> array( 'online_form' ),
		'attached_resume' 		=> '',
		'linked_resume' 		=> '',
		'uploaded_resume' 		=> '',
		'uploaded_cover_letter' => '',
	);
	$meta = wp_parse_args( $meta, $default_meta );

	$p2p = p2p_type( JR_FX_JOB_APPLICATION )->connect( $post_id, $user_id, $meta );

	// if a connection already exists update the connection meta
	if ( is_wp_error( $p2p ) ) {

		$jr_fx_log->write_log( sprintf( "Could not store job application to '%s (#%d)' from user '#%d'. Error - %s" , get_the_title( $post_id ), $post_id, $user_id, $p2p->get_error_message() ) );

		$p2p_id = p2p_type( JR_FX_JOB_APPLICATION )->get_p2p_id( $post_id, $user_id );
		if ( ! $p2p_id )
			return;

		$jr_fx_log->write_log( sprintf( 'Looking for new meta for p2p ID #%d', $p2p_id ) );

		$application_types = p2p_get_meta( $p2p_id, 'application_type', true );
		if ( ! in_array( $meta['application_type'], $application_types ) )  {
			$application_types = array_merge( $application_types, $meta['application_type'] );

			p2p_update_meta( $p2p_id, 'application_type', $application_types );
		}
		// update the new job application date and meta
		p2p_update_meta( $p2p_id, 'date', $meta['date'] );

		if ( !empty($meta['attached_resume']) ) p2p_add_meta( $p2p_id, 'attached_resume', $meta['attached_resume'], true );

		if ( !empty($meta['linked_resume']) ) p2p_add_meta( $p2p_id, 'linked_resume', $meta['linked_resume'], true );

		if ( !empty($meta['uploaded_resume']) ) p2p_add_meta( $p2p_id, 'uploaded_resume', $meta['uploaded_resume'], true );

		if ( !empty($meta['uploaded_cover_letter']) ) p2p_add_meta( $p2p_id, 'uploaded_cover_letter', $meta['uploaded_cover_letter'], true );

	} else {
		jr_fx_update_job_applications_meta( $post_id );

		$jr_fx_log->write_log( sprintf( "Succesfully stored job application to '%s (#%d)' from user '#%d'.", get_the_title( $post_id ), $post_id, $user_id ) );
	}
}

function jr_fx_update_job_applications_meta( $post_id ) {
	$applications = intval( get_post_meta( $post_id, JR_FX_JOB_APPLICATION_META, true ) ); 
	$applications++;
	update_post_meta( $post_id, $meta_key, $applications );
}

// display additional tab on the seekers dashboard for the job applications
function jr_fx_seeker_dash_applications_tab( $role ) {

	if ( ! current_theme_supports( 'jr-fx-job-applications' ) || ! jr_fx_validate('_opt_jobs_applications_dashboard', 'yes') )
		return;

	if ( 'job_seeker' != $role )
		return;
?>
	<li><a href="#jr_fx_applications" class="noscroll"><?php _e( 'Applications', JR_FX_i18N_DOMAIN ); ?></a></li>
<?php
}

// display additional tab cotent on the seekers dashboard for the job applications
function jr_fx_seeker_dash_applications_content( $role ) {

	if ( ! current_theme_supports( 'jr-fx-job-applications' ) || ! jr_fx_validate('_opt_jobs_applications_dashboard', 'yes') )
		return;

	if ( 'job_seeker' != $role )
		return;
?>
	<div id="jr_fx_applications" class="myprofile_section">
		<h2><?php _e( 'My Job Applications', JR_FX_i18N_DOMAIN ); ?></h2>
		<?php jr_fx_job_applications_list_for_user(); ?>
	</div>
<?php
}

// display additional tab on the lister/recruiter dashboard for the job applications
function jr_fx_lister_dash_applications_tab( $role = '' ) {

	if ( ! current_theme_supports( 'jr-fx-job-applications' ) || ! jr_fx_validate('_opt_jobs_applications_dashboard', 'yes') )
		return;

	if ( 'job_lister' != $role )
		return;
?>
	<li><a href="#jr_fx_applications" class="noscroll"><?php _e( 'Applications', JR_FX_i18N_DOMAIN ); ?></a></li>
<?php
}

// display additional tab content on the lister/recruiter dashboard for the job applications
function jr_fx_lister_dash_applications_content( $role = '' ) {

	if ( ! current_theme_supports( 'jr-fx-job-applications' ) || ! jr_fx_validate('_opt_jobs_applications_dashboard', 'yes') )
		return;

	if ( 'job_lister' != $role )
		return;
?>
	<div id="jr_fx_applications" class="myjobs_section">
		<h2><?php _e( 'Job Applications', JR_FX_i18N_DOMAIN ); ?></h2>
		<?php jr_fx_job_applications_users(); ?>
	</div>
<?php
}

// display the job applications for jobs posted by a specific user
function jr_fx_job_applications_users( $user_id = 0 ) {
	global $post;

	if ( !$user_id ) $user_id = get_current_user_id();

	$applied_posts = new WP_Query( array(
		'connected_type' => JR_FX_JOB_APPLICATION,
		'suppress_filters' => false,
		'nopaging' => true
	) );
	$applied_posts = wp_list_pluck( $applied_posts->posts, 'ID' );

	if ( $applied_posts ) {

		$user_posts = new WP_Query( array(
			'post_type' => APP_POST_TYPE,
			'nopaging' => true,
			'author' => $user_id
		) );
		$user_posts = wp_list_pluck( $user_posts->posts, 'ID' );

		$user_applied_posts = array_unique( array_intersect( $applied_posts, $user_posts ) );
		if ( empty( $user_applied_posts ) )
				$user_applied_posts = array( -1 );

	} else {
		$user_applied_posts = array( -1 );
	}

	if ( get_query_var('tab') && 'jr_fx_applications' == get_query_var('tab') ) {
		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	} else {
		$paged = 1;
	}

	$jobs = new WP_Query( array(
		'post_type' => APP_POST_TYPE,
		'post_status' => 'publish',
		'posts_per_page' => jt_get_jobs_per_page(),
		'paged' => $paged,
		'post__in' => $user_applied_posts,
	) );

?>
	<?php if ( $jobs->have_posts() ) : ?>

	<table cellpadding="0" cellspacing="0" class="data_list">
		<thead>
			<tr>
				<th><?php _e('Job Title',JR_FX_i18N_DOMAIN); ?></th>
				<th class="center"><?php _e('Online Form',JR_FX_i18N_DOMAIN); ?></th>
				<?php if ( !jr_fx_validate( 'jr_fx_opt_integration_linkedin_apply', 'no' ) ) : ?>
					<th class="center"><?php _e('LinkedIn Form',JR_FX_i18N_DOMAIN); ?></th>
				<?php endif; ?>
				<th class="center"><?php _e( 'Total Applications', JR_FX_i18N_DOMAIN ); ?></th>
				<th class="center"><?php _e( 'Last Application Date', JR_FX_i18N_DOMAIN ); ?></th>
			</tr>
		</thead>
		<tbody>

		<?php while ( $jobs->have_posts() ) : $jobs->the_post(); ?>

		<?php
			$connected_users = p2p_type( JR_FX_JOB_APPLICATION )->get_connected( $post );

			$latest_date = 0;
			$total['online_form'] = $total['linkedin'] = 0;

			foreach ( $connected_users->results as $user ) {
				$p2p_id = $user->data->p2p_id;

				$meta_date = p2p_get_meta( $p2p_id, 'date', true );
				$latest_date = ( $meta_date > $latest_date ? $meta_date : $latest_date );
				
				$meta_app_type = p2p_get_meta( $p2p_id, 'application_type', true );

				if ( in_array( 'online_form', $meta_app_type ) )
					$total['online_form']++;

				if ( in_array( 'linkedin', $meta_app_type ) )
					$total['linkedin']++;
			}

		?>

		<tr>
			<td><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
			<td class="center" style="line-height: 40px;"><?php echo $total['online_form']; ?></td>
			<?php if ( !jr_fx_validate( 'jr_fx_opt_integration_linkedin_apply', 'no' ) ) : ?>
				<td class="center" style="line-height: 40px;"><?php echo $total['linkedin']; ?></td>
			<?php endif; ?>
			<td class="center"><?php echo $connected_users->total_users; ?></td>
			<td class="date"><strong><?php echo date( 'j M', strtotime($latest_date) ); ?></strong> <span class="year"><?php echo date( 'Y', strtotime($latest_date) ); ?></span></td>
		</tr>

		<?php endwhile; ?>
	
		</tbody>
	</table>

	<p><strong>:: <?php _e( 'Live Jobs', JR_FX_i18N_DOMAIN ); ?> ::</strong></p>
	<?php if ( !jr_fx_validate( 'jr_fx_opt_integration_linkedin_apply', 'no' ) ) : ?>
		<p><strong>Note: </strong><?php _e( 'A user that applies for the same job using the Online form, or the LinkedIn form, counts as a single application only.', JR_FX_i18N_DOMAIN ); ?></p>
	<?php endif; ?>

	<?php jr_paging( $jobs, 'paged', array ( 'add_args' => array( 'tab' => 'jr_fx_applications' ) ) ); ?>

	<?php else: ?>
		<p><?php _e( 'No job applications monitored yet.', JR_FX_i18N_DOMAIN ); ?></p>
	<?php endif; ?>

<?php
}

// retrieves applied jobs for a specific user
function jr_fx_get_applications_for_user( $user_id = 0, $args = array() ) {

	if ( !$user_id ) $user_id = get_current_user_id();

	$default_args = array(
		'connected_type' => JR_FX_JOB_APPLICATION,
		'connected_items' => $user_id,
		'suppress_filters' => false,
		'nopaging' => true
	);
	$args = wp_parse_args( $args, $default_args );

	$posts = new WP_Query( $args );

	return $posts;
}

// display the job applications list for a specific user
function jr_fx_job_applications_list_for_user( $user_id = 0 ) {
	global $post;

	if ( !$user_id ) $user_id = get_current_user_id();

	if ( is_admin() ) {
		$css = 'form-table';
	}

	if ( get_query_var('tab') && 'jr_fx_applications' == get_query_var('tab') ) {
		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	} else {
		$paged = 1;
	}

	$args = array(
		'nopaging' => false,
		'paged' => $paged,
		'posts_per_page' => jt_get_jobs_per_page(),
	);

	$posts = jr_fx_get_applications_for_user( $user_id, $args );
?>
	<?php if ( $posts->have_posts() ) : ?>

	<table cellpadding="0" cellspacing="0" class="data_list <?php echo $css; ?>">
		<thead>
			<tr>
				<th><?php _e('Job Title',JR_FX_i18N_DOMAIN); ?></th>
				<th class="center"><?php _e( 'Applied With', JR_FX_i18N_DOMAIN ); ?></th>
				
				<th class="center"><?php _e( 'Resume', JR_FX_i18N_DOMAIN ); ?></th>
				<th class="center"><?php _e( 'Uploads', JR_FX_i18N_DOMAIN ); ?></th>
				<th class="center"><?php _e( 'Last Applied', JR_FX_i18N_DOMAIN ); ?></th>
			</tr>
		</thead>
		<tbody>

		<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>

		<?php
			$meta = p2p_get_meta( $post->p2p_id );

			$meta_date = $meta['date'][0];
			$meta_app_type = maybe_unserialize( $meta['application_type'][0] );

			$meta_app_type = array_flip( $meta_app_type );

			$application_types = array(
				'online_form' 	=> __( 'Online Form', JR_FX_i18N_DOMAIN ),
				'linkedin' 		=> __( 'LinkedIn', JR_FX_i18N_DOMAIN ),
			);

			$meta_attached_resume = @$meta['attached_resume'][0];
			$meta_linked_resume = @$meta['linked_resume'][0];
			$meta_uploaded_cv = @$meta['uploaded_resume'][0];
			$meta_uploaded_cl = @$meta['uploaded_cover_letter'][0];

			$sep = html( 'span', array ( 'class' => 'jr_fx_sep' ), ' | ' );

			if ( isset($meta_app_type['online_form']) ) $app_types['online_form'] = $application_types['online_form'];
			if ( isset($meta_app_type['linkedin']) ) $app_types['linkedin'] = $application_types['linkedin'];

			if ( empty($app_types) ) $app_types = array( '-' );

			$resumes = array();
			if ( $meta_attached_resume ) $resumes[] = __( 'Attached', JR_FX_i18N_DOMAIN );
			if ( $meta_linked_resume ) $resumes[] = __( 'Linked', JR_FX_i18N_DOMAIN );

			if ( empty($resumes) ) $resumes = array( '-' );

			$uploads = array();
			if ( $meta_uploaded_cv ) $uploads[] = __( 'CV/Resume', JR_FX_i18N_DOMAIN );
			if ( $meta_uploaded_cl ) $uploads[] = __( 'Cover Letter', JR_FX_i18N_DOMAIN );

			if ( empty($uploads) ) $uploads = array( '-' );
		?>

		<tr>
			<td><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
			<td class="center"><?php echo ( implode( $sep, array_values($app_types) ) ); ?></td>
			<td class="center"><?php echo ( implode( $sep, $resumes ) ); ?></td>
			<td class="center"><?php echo ( implode( $sep, $uploads ) ); ?></td>
			<td class="date"><strong><?php echo date( 'j M', strtotime($meta_date) ); ?></strong> <span class="year"><?php echo date( 'Y', strtotime($meta_date) ); ?></span></td>
		</tr>

		<?php endwhile; ?>
	
		</tbody>
	</table>

	<?php jr_paging( $posts, 'paged', array ( 'add_args' => array( 'tab' => 'jr_fx_applications' ) ) ); ?>

	<?php else: ?>
		<p><?php _e( 'No job applications monitored yet.', JR_FX_i18N_DOMAIN ); ?></p>
	<?php endif; ?>

<?php
}

// register job applications meta box
function jr_fx_register_job_applications_meta_box() {

	if ( ! current_theme_supports( 'jr-fx-job-applications' ) )
		return;

	add_meta_box( 'jr-fx-job-applications', __( 'Job Applications', JR_FX_i18N_DOMAIN ), 'jr_fx_job_applications_mbox', APP_POST_TYPE, 'side' );
}

// display the job applications meta box on the backend job edit page
function jr_fx_job_applications_mbox( $post ) {

	if ( ! current_theme_supports( 'jr-fx-job-applications' ) )
		return;

	$visible_users = 15;

	$total = 0;
	$display = 'block';
	$connected_users = p2p_type( JR_FX_JOB_APPLICATION )->get_connected( $post );

	echo html( 'p', sprintf( __( 'Total Applications: %s', JR_FX_i18N_DOMAIN ), sprintf( '<strong><code>%d</code></strong>', $connected_users->total_users ) ) );
?>
<ul>
<?php
	 foreach( $connected_users->results as $user ) :
		$total++;
		$user_info = get_userdata( $user->data->ID );
		if ( $total > $visible_users ) $display = 'none';
		echo sprintf( '<li class="jr_fx_meta_application" style="display: %s"><a href="%s">%s</a></li>', $display, esc_url($user_info->user_url), $user->display_name );
	endforeach;

	if ( $connected_users->total_users && $total > $visible_users ) {
		echo "<br/>";
		echo html( 'p', sprintf( '<a href="#" class="jr_fx_meta_application_show_all">%s</a>', __( 'Show All',JR_FX_i18N_DOMAIN ) ) );
	}
?>
</ul>
<?php
}

// display the job applications for a specific user
function jr_fx_profile_jobs_applied_list( $user ) {

	if ( ! current_theme_supports( 'jr-fx-job-applications' ) && is_admin() )
		return;

	?><h3><?php _e('Job Applications', APP_TD); ?></h3><?php
	jr_fx_job_applications_list_for_user( $user->ID );
}

/**
* NOTE: 
* url_to_postid() for custom post types is scheduled for WP 3.6.
* After this fix, I will add support to enable job applications to be displayed on a new column on the Live dashboard
* 
*/
// dynamically retrieve the job applications for a specific permalink
function jr_fx_get_job_applications() {
	global $jr_fx_log;

	if ( ! current_theme_supports( 'jr-fx-job-applications' ) )
		exit;

	check_ajax_referer( JR_FX_PLUGIN_BASE_NAME, 'security' );

	$permalink = $_POST['permalink'];
	$post_id = url_to_postid( $permalink );

	$post = get_post( $post_id );

	$jr_fx_log->write_log( 'jr_fx_get_job_applications() - permalink = ' . $permalink );

	$connected_users = p2p_type( JR_FX_JOB_APPLICATION )->get_connected( $post );
	$data['applications'] = $connected_users->total_users;

	$jr_fx_log->write_log( 'jr_fx_get_job_applications() - data = ' . print_r( $data, true ) );

	echo json_encode($data);
	exit;
}


function jr_fx_jobs_custom_columns( $column ){
	global $post;

	if ( ! current_theme_supports( 'jr-fx-job-applications' ) )
		return;

	switch ($column) {
		case JR_FX_JOB_APPLICATION:
			$connected_users = p2p_type( JR_FX_JOB_APPLICATION )->get_connected( $post );
			echo intval($connected_users->total_users);
			break;
	}
}

function jr_fx_jobs_columns_sort( $columns ) {

	if ( ! current_theme_supports( 'jr-fx-job-applications' ) )
		return $columns;

	$columns[JR_FX_JOB_APPLICATION] = JR_FX_JOB_APPLICATION;
	return $columns;
}

function jr_fx_edit_jobs_columns( $columns ){

	if ( ! current_theme_supports( 'jr-fx-job-applications' ) )
		return $columns;

	foreach ( $columns as $key => $column ) {
		if ( 'date' == $key ) {
			$columns_reorder[JR_FX_JOB_APPLICATION] = __( 'Applied', APP_TD );
		}
		$columns_reorder[$key] = $column;
	}
	return $columns_reorder;
}

function jr_fx_applications_column_orderby( $vars ) {

	if ( ! current_theme_supports( 'jr-fx-job-applications' ) )
		return $vars;

	if ( isset( $vars['orderby'] ) && JR_FX_JOB_APPLICATION == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'meta_key' => JR_FX_JOB_APPLICATION_META,
			'orderby' => 'meta_value_num'
		) );
	}
	return $vars;
}

function jr_fx_user_has_applied_to( $post, $user_id = 0, $extra_qv = array() ) {

	if ( ! $user_id ) $user_id = get_current_user_id();

	$users = p2p_type( JR_FX_JOB_APPLICATION )->get_connected( $post, $extra_qv, 'abstract' );
	$users = wp_list_pluck( $users->items, 'ID' );

	return in_array( $user_id, $users );
}

/**
* END FEATURE: Monitor job listings
*************************************************************************************************************/


/**
* 
* HELPER FUNCTIONS
* 
*/

// create uploaded company logo attachments for better image management
// returns the attachment_id
function jr_fx_attach_logo_to_user( $current_user, $file/*$file, $mime_type*/ ) {

	// if the resize fails the attachment was deleted - insert as new attachment
	$existing_logo = get_user_meta( $current_user->ID, 'company-logo', true );
	$logo = jr_fx_resize_logo( $existing_logo );

	if ( ! empty($logo['attach_id']) ) {
		wp_delete_attachment($logo['attach_id']);
	}

	// create separate logo copy by concatenating a logo sufix
	$filename = basename($file['company-logo-file']);
	$logo_filename = str_replace(".", "_" . JR_FX_COMPANY_LOGO_SUFIX . ".", $filename);

	$new_file = str_replace( $filename, $logo_filename, $file['company-logo-file'] );
	if ( copy( $file['company-logo-file'], $new_file) ) {
		// copy successfull - use the copy to generate attachments meta
		$file['company-logo'] = str_replace( $filename, $logo_filename, $file['company-logo'] );
		$file['company-logo-file'] = addslashes($new_file);
	}

	$attachment = array(
		'guid' => $file['company-logo'],
		'post_title' => sprintf( __('Company Logo for User #%d (%s)', JR_FX_i18N_DOMAIN), $current_user->ID, $current_user->user_login ),
		'post_content' => 'Company Logo',
		'post_type' => 'attachment',
		'post_mime_type' => $file['company-logo-type'],//$mime_type,
		'post_status' => 'inherit'
	);

	$attach_id = wp_insert_attachment( $attachment, $file['company-logo-file'] );
	$file['attach_id'] = $attach_id;

	// include the image.php file for the function wp_generate_attachment_metadata() to work
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $file['company-logo-file'] ) );

	//return $attach_id;
	return $file;
}

// handle the logo uploads 
// return the populated upload file array
function jr_fx_handle_logo_upload ( $attach = TRUE ) {
	global $posted, $current_user;

	get_currentuserinfo();

	if ( isset($_FILES['company-logo']) && !empty($_FILES['company-logo']['name']) ) {

		$posted['company-logo-name'] = $_FILES['company-logo']['name'];

		// Check valid extension
		$allowed = array(
			'png',
			'gif',
			'jpg',
			'jpeg'
		);

		$extension = strtolower(substr(strrchr($_FILES['company-logo']['name'], "."), 1));
		if (!in_array($extension, $allowed)) {
			$_FILES['error'] = __( 'Only jpg, gif, and png images are allowed.', JR_FX_i18N_DOMAIN );
		} else {

			/** WordPress Administration File API */
			include_once(ABSPATH . 'wp-admin/includes/file.php');
			/** WordPress Media Administration API */
			include_once(ABSPATH . 'wp-admin/includes/media.php');
			
			add_filter( 'upload_dir', 'jr_fx_company_logo_upload_dir' );
			
			$time = current_time('mysql');
			$overrides = array('test_form'=>false);

			$file = wp_handle_upload( $_FILES['company-logo'], $overrides, $time );

			remove_filter( 'upload_dir', 'jr_fx_company_logo_upload_dir' );
			
			if ( isset($file['error']) ) :
				$file['error'] = __('ERROR: ', JR_FX_i18N_DOMAIN).$file['error'];
			else:

				$file['company-logo'] = $file['url'];
				$file['company-logo-type'] = $file['type'];
				$file['company-logo-file'] = addslashes($file['file']);
				$file['file'] = addslashes($file['file']);

				if ( $attach ) {
					$file = jr_fx_attach_logo_to_user( $current_user, $file );
				}

			endif;

		}
	}
	return $file;
}

// attach the company logo to the job post
function jr_fx_attach_company_logo ( $post_id, $file ) {

		## Load APIs and Link to company image

		$title = '';

		include_once(ABSPATH . 'wp-admin/includes/file.php');
		include_once(ABSPATH . 'wp-admin/includes/image.php');
		include_once(ABSPATH . 'wp-admin/includes/media.php');

		if (empty($file)) return;
		
		if ( isset( $file['company-logo-name'] ) ) {
			$name_parts = pathinfo($file['company-logo-name']);
			$name = trim( substr( $name, 0, -(1 + strlen($name_parts['extension'])) ) );
			$title = $file['company-logo-name'];
		}

		$url = $file['company-logo'];
		$type = $file['company-logo-type'];
		$logo = $file['company-logo-file'];

		$content = '';

		if ($logo) :
		
			// use image exif/iptc data for title and caption defaults if possible
			if ( $image_meta = @wp_read_image_metadata($logo) ) {
				if ( trim($image_meta['title']) )
					$title = $image_meta['title'];
				if ( trim($image_meta['caption']) )
					$content = $image_meta['caption'];
			}

			// create separate logo copy for the post
			$filename = basename($logo);
			$logo_filename = str_replace("_" . JR_FX_COMPANY_LOGO_SUFIX, "_$post_id", $filename);

			$new_file = str_replace( $filename, $logo_filename, $logo );
			if ( copy( $logo, $new_file) ) {
				// copy successfull - use the copy to generate attachments meta
				$url = str_replace( $filename, $logo_filename, $url );
				$logo = $new_file;
			}

			// Construct the attachment array
			$attachment = array_merge( array(
				'post_mime_type' => $type,
				'guid' => $url,
				'post_parent' => $post_id,
				'post_title' => $title,
				'post_content' => $content,
			), array() );

			// Save the data
			$id = wp_insert_attachment($attachment, $logo, $post_id);
			if ( !is_wp_error($id) ) {
				wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $logo ) );
			} 

			update_post_meta( $post_id, '_thumbnail_id', $id );
		
		endif;
}

 // add extra profile fields
function jr_fx_get_extra_profile_list() {
	return array(
		//	Custom fields: 'slug_of_the_field_here' => 'Field name for display',
		'company-logo' => 'Company Logo',
		'delete-logo' => 'Delete'	
	);
}

// add enctype to the profile form so we can upload images (only available on the backend)
function jr_fx_user_edit_form_tag() {
	if ( jr_fx_validate_feat_persistent_logo() ) :
		echo ' enctype="multipart/form-data"';
	endif;
}

// return company logo correctly resized using WP functions
function jr_fx_resize_logo( $logo, $thumbs_width = JR_FX_COMPANY_LOGO_SIZE_W, $thumbs_height = JR_FX_COMPANY_LOGO_SIZE_H ) {

	if ( ! empty($logo['attach_id']) ) {
		$src = wp_get_attachment_image_src($logo['attach_id'], array( $thumbs_width, $thumbs_height ) );
		if ( $src ) {
			$logo['company-logo'] = $src[0];
			$thumbs_width =  $src[1];
			$thumbs_height = $src[2];
		} else {
			unset($logo['attach_id']);
		}
	}

	list( $logo['width'], $logo['height'] ) = jr_fx_image_proportional_size( $thumbs_width, $thumbs_height );

	return $logo;
}

function jr_fx_check_logo_exists( $logo ) {
	return (bool) isset($logo['company-logo-file']) && file_exists($logo['company-logo-file']);
}

// add extra profile fields
function jr_fx_extra_user_profile_fields( $user ) { 
	$has_logo = false;
?>
	<?php if ( jr_fx_validate_feat_persistent_logo() ): ?>

		<h3><?php _e("Company Logo", "blank"); ?></h3>

		<table class="form-table">
		<?php
			foreach(jr_fx_get_extra_profile_list() as $key => $value) {
		?>
			<tr>
				<th><!--<label for="<?php echo $key; ?>"><?php _e($value); ?></label>--></th>
				<td>
					<?php if ( $key == 'company-logo' ): ?>
						<label for="<?php echo $key; ?>"><?php _e('Logo (.jpg, .gif or .png)', 'appthemes'); ?></label> <input type="file" class="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>" />
						<?php
							$logo = get_user_meta( $user->ID, $key, true );
							$has_logo = (bool) !empty($logo['company-logo-file']) && jr_fx_check_logo_exists($logo);
							
							// try to properly resize the image
							 $logo = jr_fx_resize_logo( $logo );
						?>
						<?php if ( $has_logo ): ?> <br/><div class="jr_fx_company_logo_placeholder"><img class="jr_fx_company_logo_image" width="<?php echo $logo['width']; ?>" height="<?php echo $logo['height']; ?>" src="<?php echo esc_attr( $logo['company-logo'] ); ?>"/></div> <?php endif; ?>
					<?php endif; ?>
					
					 <?php if ( $key == 'delete-logo' && $has_logo ): ?>
						<div class="jr_fx_profile_delete_logo"><input type="checkbox" name="jr_fx_profile_delete_logo"/> <?php _e($value,JR_FX_i18N_DOMAIN); ?></div> 
					<?php endif; ?>
				</td>
			</tr>
		<?php
			}
		?>
		</table>
		
	<?php endif; ?>
<?php }

// save extra profile fields to the database
function jr_fx_save_extra_user_profile_fields( $user_id ) {
	global $jr_fx_upload_error; echo "user = $user_id";

 	if ( !current_user_can( 'edit_user', $user_id ) || !jr_fx_validate_feat_persistent_logo('jr_fx_save_extra_user_profile_fields') ) { return false; }

	if ( !isset($jr_fx_upload_error) && !$jr_fx_upload_error  ):

		$logo = get_user_meta( $user_id, 'company-logo', true );

		foreach( jr_fx_get_extra_profile_list() as $key => $value) {

				if ( ! isset( $_POST['jr_fx_profile_delete_logo'] ) ) {

					if ( $key == 'company-logo' ):
						$value = jr_fx_handle_logo_upload();
					else:
						$value = $_POST[$key];
					endif;

				}

				if ( $key == 'delete-logo' && isset( $_POST['jr_fx_profile_delete_logo'] ) ) {
					// don't delete the meta value if the attach id exists
					// delete the file attributes but keep the attachment id
					if ( ! empty($logo['attach_id']) ) {
						wp_delete_attachment($logo['attach_id']);
					}
					delete_user_meta( $user_id, 'company-logo');
				} elseif ( !empty( $_FILES ) && $_FILES['company-logo']['size'] > 0 ) {
					update_user_meta( $user_id, $key, $value);
				}

		}

	endif;
}

function jr_fx_load_company_logo() {
	global $jr_fx_log;

	check_ajax_referer( JR_FX_PLUGIN_BASE_NAME, 'security' );

	$post_id = $_POST['post_id'];
	$file = jr_fx_handle_logo_upload( $attach = FALSE );

	$jr_fx_log->write_log( 'jr_fx_load_company_logo() - file = ' . print_r( $file, true ) );

	if ( $file ):

		if ( array_key_exists( 'error', $file ) ) {
			echo json_encode($file);
		}
		else {
			list( $width, $height ) = getimagesize( $file['company-logo-file'] );

			$file = jr_fx_resize_logo( $file, $width, $height );
			$file['url'] = $file['company-logo'];

			echo json_encode($file);
		}

	endif;
	exit;
}

function jr_fx_process_company_logo( $post_id ) {
	global $current_user, $post, $posted, $app_abbr;

	if ( ! jr_fx_validate_feat_persistent_logo('jr_fx_process_company_logo') )
		return;

	get_currentuserinfo();

	// look for already attached image
	$attach_id = get_post_thumbnail_id($post_id);

	// try to retrieve the logo from the user meta
	$logo = get_user_meta( $current_user->ID, 'company-logo', true );
	if ( $logo && array_key_exists('company-logo-file', $logo) && !empty($logo['company-logo-file']) && ! $attach_id  ):
		// process new upload to keep files stored separately
		jr_fx_attach_company_logo( $post_id, $logo );

	else:

		if ($attach_id) {

			$attached['company-logo'] = wp_get_attachment_image_src( $attach_id, array( JR_FX_COMPANY_LOGO_SIZE_W, JR_FX_COMPANY_LOGO_SIZE_H ) );
			if ($attached['company-logo']) 
				$attached['company-logo'] = $attached['company-logo'][0];
			$attached['company-logo-file'] = addslashes(get_attached_file($attach_id));
			$attached['company-logo-type'] = get_post_mime_type($attach_id);
			$attached['attach_id'] = $attach_id;

			if ( isset($_POST['jr_fx_delete_company_logo']) ):
				wp_delete_attachment($attach_id);
				return;
			endif;
			
			if ( $attached && array_key_exists( 'company-logo-file', $attached ) ):
				$logo = $attached;
			endif;
			
		}

	endif;

	# store the logo on user profile as default
	if ( !empty($logo) && isset($_POST['jr_fx_def_company_logo']) ):
		$logo = jr_fx_attach_logo_to_user( $current_user, $logo );
		update_user_meta( $current_user->ID, 'company-logo', $logo );
	endif;

}

function jr_fx_payment_due() {
	global $app_abbr;

	if ( ! current_theme_supports('app-payments') ) {
		$payment_due = jr_fx_payment_due_16();
	} else {
		$payment_due = (bool) ( 'yes' === get_option( $app_abbr . '_jobs_charge' ) );
	}
	return $payment_due;
}

/*
 * UPDATER
 */
 
//
function jr_fx_updater_args($query){
	$query['slug'] = basename(dirname(__FILE__));
	$query['secret'] = urlencode('jr_fx_uPd$a%t&e.R=');	
	return $query;
}

function jr_maybe_redirect_bank_transfer() {

	if ( empty($_POST['payment_gateway']) || empty($_POST['order_id']) )
		return;

	$gateway = new JR_FX_Bank_Transfer_Gateway();

	if ( $gateway->identifier() != $_POST['payment_gateway'] )
		return;

	$order_id = intval($_POST['order_id']);
	$order = appthemes_get_order( $order_id );

	$options = $gateway->get_options();

	if ( 'none' != $options['jr_fx_page'] ) {
		if ( empty($options['jr_fx_page']) )
			$url = home_url();
		else
			$url = get_permalink( $options['jr_fx_page'] );

		wp_redirect( add_query_arg( array( 'mp_order_id' => $order->get_id(), 'mp_order_cost' => $order->get_total() ), $url ) );
		exit;
	}

}

function jr_fx_image_proportional_size( $width, $height, $target = '150' ) {

	if ( $width > $height ) {
		$percentage = ( $target / $width );
	} else {
		$percentage = ( $target / $height );
	}
	//gets the new value and applies the percentage, then rounds the value
	$width = round($width * $percentage);
	$height = round($height * $percentage);

	return array( $width, $height );
}