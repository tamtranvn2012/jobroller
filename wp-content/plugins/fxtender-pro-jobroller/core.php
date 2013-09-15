<?php

// Core functions


# Default permissions 
if ( !defined('JR_FX_VERSION') ) define( 'JR_FX_VERSION', JR_FX_VER_FREE );

/************
 * UPDATER
 ************/

// only add the updater if the appthemes updater is not active
if ( is_admin() && ! class_exists('APP_Plugin_Upgrader') ):
	require 'updater/update-checker.php';
	$UpdateChecker = new PluginUpdateChecker(
		'http://bruno-carreco.com/wpuno/fxtender_jr/index.php', 
		basename(dirname(__FILE__)),
		basename(dirname(__FILE__))
	);

	if (!function_exists('jr_fx_updater_args')):
		function jr_fx_updater_args($query){
			$query['slug'] = basename(dirname(__FILE__));
			return $query;
		}
	endif;
	$UpdateChecker->addQueryArgFilter('jr_fx_updater_args');
endif;

/************
 * FUNCTIONS
 ************/
 
/**
 * INFO/UPGRADE
 */
 
 /**
 * Buy Now
 */
function  jr_fx_buy_me_url( $wrap = 'no', $buynow ='yes'  ) {
	$me = jr_fx_get_me();

	if ( $buynow == 'yes' )
		$url = 'http://www.appthemes.com/cp/go.php?r=4079&i=l20';
	else
		$url = $me['updates'];

	if ( $wrap == 'yes' ) {
		$url = '<a href="'.$url.'" class="jr_fx_buy_now" target="_new">
					<img src="'.JR_FX_PLUGIN_URL.'images/buynow.gif" alt="Buy Now" />
				</a>';
	}
	return $url;
}

/**
 * Description:			Shows upgrade text/graphic
 * @param $option		DB Option
 * @param $type			Type of upgrade notice: default is 'graphic' and returns an image. Any other value will be used as text link.
 * @returns  			True/False
 */ 
function jr_fx_upgrade( $option = '', $type = 'graphic', $ico = 'no' ) {

	$me = jr_fx_get_me();
	$result = '';

	if ( ( $option && !jr_fx_has_perms_to($option) ) || ( !$option && JR_FX_VERSION == JR_FX_VER_FREE ) ) :
	
		if ( $ico == 'yes' ) {
			$result  = "<img class='jr_fx_ver_free_ico' src='".JR_FX_PLUGIN_URL."/images/lite.png' title='FXtender ".JR_FX_VER_FREE."'>";
			$result .= "<img class='jr_fx_ver_free_ico' src='".JR_FX_PLUGIN_URL."/images/lock.png' title='This version have some locked features. You can see and change all the Pro features but they will not be active.'><br/>";
		}

		if  ($type == 'graphic' )
			$title = __("Feature(s) not available on the ".JR_FX_VER_FREE." version. Upgrade to FXtender " . JR_FX_VER_PLUS . " now and unlock the feature(s).", JR_FX_i18N_DOMAIN);
		else {
			$result .= '<u><a class="jr_fx_demo" href="'.$me['demosite'].'" title="Not sure about the Pro features? Visit the FXtender Demo website and see some of the Pro features in action." target="_new"> Visit FXtender Demo website </a></u>';
			$title = __("Upgrade to FXtender " . JR_FX_VER_PLUS . " now and unlock all the features for the limited time price of " . JR_FX_PRICE ."!", JR_FX_i18N_DOMAIN);
		}

		$result .= "<a href='".jr_fx_buy_me_url()."' title='".$title."' ".(!$option?"class='jr_fx_upgrade_url'":'')." >";

		if ( $ico == 'yes' ) $result .= "<img class='jr_fx_unlock_ico' src='".JR_FX_PLUGIN_URL."/images/unlock.png' title='".$title."'>";

		if ( $type == 'graphic' ) {
			$result .= "<img class='jr_fx_upgrade_ico' src='".JR_FX_PLUGIN_URL."/images/upgrade.png' title='".$title."' width='16px' height='16px'>";
		} else {
			$result .= $type;
		}
		$result .= "</a>";

	elseif( $ico == 'yes' ):
		$result = "<img class='jr_fx_ver_pro_ico' src='".JR_FX_PLUGIN_URL."/images/pro.png' title='FXtender ".JR_FX_VER_PLUS."'>";
	endif;

	return $result;
}

/**
 * Toggle Pro Features ON/OF
 */
function  jr_fx_feat_toggle_button( ) {
	$value = get_option(JR_FX_FIELDS_PREFIX.'_toggle_features');
	$button = '<div class="jr_fx_tzCheckbox jr_fx_toggle_features"><input class="tzCheckboxBt" type="checkbox" title="Shows/Hides the locked features" value="Hide" data-on="Show" data-off="Hide" '.($value?" checked='checked' ":"").' id="'.JR_FX_FIELDS_PREFIX . '_toggle_features" name="' . JR_FX_FIELDS_PREFIX . '_toggle_features"></div>';
	return $button;
}

/**
 * Main
 */
 
/**
 * Description:			Validates DB options and permissions
 * @param $option		DB Option
 * @param $value		Value to compare the $option. If no $value is set checks only if the option exists.
 * @returns  			The option value <=> True / False
 */ 
function jr_fx_validate ( $option, $value = '') {
	global $jr_fx_log, $jr_fx_options;

	# log message
	$message = '**** OPTION **** jr_fx_validate(' .$option .') | COMPARE TO = ' . $value . ' | jr_fx_has_perms_to('.$option.') = ' . (jr_fx_has_perms_to( $option )?'yes':'no') ;
	$from_global = '';
	
	if ( ! jr_fx_has_perms_to( $option )  ) {
		# log
		$jr_fx_log->write_log( $message .  ' | NO ACCESS' );

		return false;
	}

	// speed things up by getting the values from the global
	if ( isset( $jr_fx_options[JR_FX_FIELDS_PREFIX.$option] ) ) {
		$result = $jr_fx_options[JR_FX_FIELDS_PREFIX.$option];
		$from_global = " - FROM GLOBAL - ";
	} else {
		$result = get_option(JR_FX_FIELDS_PREFIX.$option);
		$jr_fx_options[JR_FX_FIELDS_PREFIX.$option] = $result;
	}

	# get option from DB
	if ( ( $value && $value == $result ) || ( !$value && $result ) ) {

		# log
		$jr_fx_log->write_log( "$message  | ACTIVE - value =  $result  -  $from_global" );

		return $result;

	} else {

		# log
		$jr_fx_log->write_log( "$message | COMPARISON RETURNED FALSE" );

		return false;
	}

}

/**
 * Description:			Check permissions
 * @param $option		DB Option
 * @returns  			True/False
 */  
function jr_fx_has_perms_to( $option ) {

	if (JR_FX_VERSION == JR_FX_VER_PLUS )
		return true;

	# Default perm is free
	$perms = JR_FX_VER_FREE;

	# Compare each option/feature against the version permissions
	switch ( $option ) {
		case '_opt_listings_all_cat_featured':
		case '_int_jobs_free_offer':
		case '_text_notice_free_offer':
		case '_opt_jobs_hide_pay_free_offer':
		case '_opt_jobs_free_offer_text':
		case '_opt_jobs_duration_field':
		case '_text_jobs_duration_caption':
		case '_int_jobs_min_duration':
		case '_int_jobs_max_duration':
		case '_opt_jobs_recipient_field':
		case '_opt_jobs_optional_apply_online':
		case '_opt_jobs_apply_button':
		case '_opt_jobs_apply_visitors_redirect':
		case  (preg_match('/_opt_listings_logo/i', $option) ? true : false):
		case '_opt_jobs_company_logo':
		case '_tex_notice_free_offer':
		case '_text_email_signature':
		case '_widget_company_logo':
		case '_opt_listings_preview_thumb':
		case '_opt_jobs_visitors_redirect':
		case '_opt_jobs_apply_visitors_redirect':
		case '_opt_jobs_apply_registered_email':
		case '_opt_jobs_lister_persistent_logo':
		case '_opt_integration_linkedin_apply':
		case '_opt_permalinks':
		case '_opt_jobs_applications_monitor':
		case '_opt_jobs_applications_dashboard':
		case (preg_match('/_persistent_logo*/i', $option) ? true : false):
		case (preg_match('/_opt_resume*/i', $option) ? true : false):
		case (preg_match('/_S2member*/i', $option) ? true : false):
			$perms = JR_FX_VER_PLUS;
			break;
		default:
			$perms = JR_FX_VER_FREE;
	}
	return ( $perms === JR_FX_VERSION );
}

 /**
 * Description: 		Counts and returns the total user posts based on the post type
 * @param $user_id		User ID
 * @param $post_type	Post Type
 * returns  			Total user published posts
 */
function jr_fx_count_user_posts ( $user_id, $post_status = array( 'publish', 'pending', 'expired' ) ) {

	$total = 0;
	$q_args = array(
		'post_type' => JR_FX_JR_POST_TYPE,
		'post_status' => ( $post_status != 'live' ? $post_status : 'publish' ) , // private status counts as a published post - it's usually an expired job
		'author' => $user_id
	);

	$posts = new WP_Query($q_args); 

	# total jobs published by user
	if ( $posts->found_posts ) $total = $posts->found_posts;

	return $total;
}

/*
 * Description:		Outputs a html notice using JobRoller notices styles
 * @param $message	The message notice
 * @param $type		The notice type. Default is 'errors'.
 * @return			Returns the html message 
 */
function jr_fx_format_notice( $message, $type = 'success' ) {

	$echo_message = "<div class='notice " . $type ."'>";
	$echo_message .= '<span>'.$message .'</span>';
	$echo_message .= '</div>';

	return '<span class=\'jr_fx_notice\'>'. $echo_message . '</span>';
}

/*
 * Description:				Parses custom notices to show to the current user
 * @param $notice			The message notice
 * @param $notice_option	The option that invoked this function
 * @return					Returns the parsed and formated notice
 */
function jr_fx_parse_notices( $notice, $notice_option ) {
	if ( $notice_option == 	'_text_notice_free_offer' ) {
		# for free offers check published and pending posts
		$total_posts = jr_fx_count_user_posts( get_current_user_id() );
	} else
		$total_posts = jr_fx_count_user_posts( get_current_user_id(), 'publish' );

	# validate features that use this function
	$min_job_post_val = jr_fx_validate( '_int_jobs_min_auto_pub' );
	$total_job_offers = jr_fx_validate( '_int_jobs_free_offer' );

	$total_posts = ($total_posts?$total_posts:0);
	$min_job_post_val = ($min_job_post_val?$min_job_post_val:0);
	$total_job_offers = ($total_job_offers?$total_job_offers:0);

	# variables that can be used by admin to dynamically fill in email content
	$find = array('/%publishedjobs%/i', '/%minpublishedjobs%/i', '/%totalfreejobs%/i', '/%freejobsleft%/i');
	$replace = array( $total_posts, $min_job_post_val, $total_job_offers, $total_job_offers - $total_posts);

	$msg = '';
	if ( $notice ) :
		$msg = stripslashes( $notice );
		$msg = strip_tags( $msg ); 
		$msg = preg_replace( $find, $replace, $msg );
		$msg = preg_replace( "/%.*%/", "", $msg );
		$msg = json_encode($msg);
		# remove json double quotes on start and end of string
		$msg = substr($msg,1,strlen(trim($msg))-2 );
	endif;

	return jr_fx_format_notice( $msg );
}

/*
 * Description:		Sets notices using Wordpress transients
 * @param $notice 	The notice message
 * @param $expire 	Expire time in seconds
 * @return			none
 */
function jr_fx_notices( $notice, $expire = 15 ) {
	global $jr_fx_log;

	if ( $notice ) {
		set_transient('jr-fx-notice', $notice, $expire);
		$jr_fx_log->write_log('jr_fx_notices() | Notice: ' . $notice . ' | Expire: ' . $expire);
	}
}

/*
 * Description:		String cleanup
 * @param $string 	The string to clean
 * @return			Cleaned up string
 */
function jr_fx_clean( $string ) {
	$string = stripslashes($string);
	$string = trim($string);
	return $string;
}

/*
 * Description:		Get the remaining days for a post
 * @param $post_id 	The post ID
 * @return			Remaining days
 */
function jr_fx_remaining_days( $post_id ) {
	$meta_key = jr_fx_get_duration_meta_key();

	$days = get_post_meta($post_id, $meta_key, true);

	if ($days==1) return $days.' '.__('day left', JR_FX_i18N_DOMAIN);
	if ($days<1) return __('Expired', JR_FX_i18N_DOMAIN);
	return $days.' '.__('days left', JR_FX_i18N_DOMAIN);

	return '-';
}

/*
 * Description:		Remove line breaks
 * @param $string 	The string
 * @return string	The string without the line breaks
 */
function jr_fx_remove_line_breaks($string) { 
	$string = nl2br($string); //add html line returns
	$string = str_replace(chr(10), " ", $string); //remove carriage returns
	$string = str_replace(chr(13), " ", $string); //remove carriage returns

	return $string;
}

/*
 * Description:			Shows frontend customized notices using JobRoller styles
 * @param $raw_notice 	The notice to show
 * @return notice		The formatted notice
 */
function jr_fx_show_notice( $raw_notice ) {

	$arr_notice = explode( '|', $raw_notice );

	if ( count( $arr_notice ) == 3 ) {
		$notice = "<strong>" . ucfirst($arr_notice[0]) .":</strong> ". $arr_notice[2] ."<br/><strong>Status: </strong> ". $arr_notice[1];
		$notice = "<div class='jr_fx_".$arr_notice[0]."'>" . $notice . "</div>";
		echo jr_fx_remove_line_breaks( $notice );
	}
}

/*
 * Description:				Create an excerpt
 * @param $post 			Post object
 * @param $excerpt_length 	Words to trim
 * @param $padding 			More chars
 * @return $output			The exceprt
 */
function jr_fx_preview_excerpt($post, $excerpt_length=45,  $padding="...") {
	$mycontent = $post->post_excerpt;

	$mycontent = $post->post_content;
	$mycontent = strip_shortcodes($mycontent);
	$mycontent = str_replace(']]>', ']]&gt;', $mycontent);
	$mycontent = strip_tags($mycontent);

	$words = explode(' ', $mycontent, $excerpt_length + 1);
	if(count($words) > $excerpt_length) :
		array_pop($words);
		array_push($words, '...');
		$mycontent = implode(' ', $words);
	endif;
	$mycontent = '<p>' . $mycontent . '</p>';

	// Make sure to return the content
	return $mycontent;
}

// clear any cached values for a specific post
function jr_fx_clear_cached_values( $post_id ) {
	delete_transient( JR_FX_FIELDS_PREFIX.'-thumb-' . $post_id );
	delete_transient( JR_FX_FIELDS_PREFIX.'-preview-' . $post_id );
}

function jr_fx_attach_features_to_post( $title, $post_id ) {
	global $featured_job_cat_id, $post;

	if ( is_feed() ) return $title;

	$output = '';
	if ( ! is_admin() && isset($post) && JR_FX_JR_POST_TYPE  == $post->post_type ) {

		$in_loop_featured = get_post_meta( $post_id, JR_ITEM_FEATURED_LISTINGS, true );
		if ( ! $in_loop_featured ) $in_loop_featured = get_post_meta( $post_id, JR_ITEM_FEATURED_CAT, true );

		if ( ! in_the_loop() && ! $in_loop_featured ) return $title;

		$output = _jr_fx_attach_features_to_post( $post );
	}

	return $title . $output;
}

function jr_fx_orders_get_connected( $user_id ){

	$args = array(
		'connected_type' => APPTHEMES_ORDER_CONNECTION,
		'connected_query' => array( 'post_status' => 'any' ),
		'post_status' => 'any',
		'nopaging' => true,
		'connected' => 'any',
		'author' => $user_id,
	);

	return new WP_Query( $args );
}

// check for paid jobs for a specific user
function jr_fx_paid_jobs_for_user( $user_id = 0 ) {

	if ( ! $user_id ) $user_id = get_current_user_id();

	if ( !current_theme_supports( 'app-payments' ) ) 
		return false;

	$connected = jr_fx_orders_get_connected( $user_id );
	if ( ! $connected->posts ) 
		return false;

	$total = 0;
	foreach( $connected->posts as $post ){
		$order = appthemes_get_order( $post->ID );
		if ( $order ) $total += $order->get_total();
	}
	return $total;
}

// check for paid jobs - supports JR 1.6.x and over
function jr_fx_is_paid_job( $post ) {
	$paid_job = false;
	if ( ! current_theme_supports( 'app-payments' ) )
		return false;

	$order = appthemes_get_order_connected_to( $post->ID );
	if ( $order ) {
		$paid_job = (bool) $order->get_total();
	}

	return $paid_job;
}

function _jr_fx_attach_features_to_post( $post ) {
	global $jr_fx_log;

	$current_hook = current_filter();
	$output = '';

	# log
	$jr_fx_log->write_log('**** HOOK CALLED ( ' . $current_hook .  '; post_id = ' . $post->ID . ' ) ****');

	// in_the_loop() would be the best approach but it does not work with featured jobs (uses WP_QUERY)
	if ( ! is_singular() && ! is_single() ) {

		if ( function_exists( 'jr_fx_validate_feat_job_list_thumbs' ) ) {
			### BEGIN FEATURE: Job Listings Thumbs
			if ( jr_fx_validate_feat_job_list_thumbs( $current_hook, 'add job list thumb' ) ) :

				$thumb = get_transient( JR_FX_FIELDS_PREFIX.'-thumb-' . $post->ID );

				#attach a hidden thumb to the title
				if ( ! $thumb && has_post_thumbnail( $post->ID ) ) {

					$paid_job = jr_fx_is_paid_job( $post );

					$thumb = "<div class='jr_fx_temp_thumb'><span class='jr_fx_job_listing_thumb ".($paid_job?'jr_fx_paid':'')."'>";
					$thumb .= get_the_post_thumbnail($post->ID, 'thumbnail', array ( 'class' => 'jr_fx_job_listing_thumb'));
					$thumb .= "</span></div>";
				} 

				if ( $thumb ) {

					$thumbs_cache = jr_fx_validate('_opt_listings_preview_cache_time');
					if ( $thumbs_cache ) {
						$thumbs_cache *= 86400;
					} else 
						$thumbs_cache = JR_FX_JOB_LISTINGS_THUMBS_CACHE;

					$output .= $thumb;
					// cache thumbs for a week
					set_transient( JR_FX_FIELDS_PREFIX.'-thumb-' . $post->ID, $thumb, $thumbs_cache );
				}

			endif;
			### END FEATURE
		}

		if ( function_exists( 'jr_fx_validate_feat_job_preview' ) ) {
			### BEGIN FEATURE: Job Preview
			if ( jr_fx_validate_feat_job_preview( $current_hook, 'Job Preview' ) ) :
				echo "<input type='hidden' class='jr_fx_preview_pid' value=".$post->ID.">";
				// get preview from cache
				$html_tip = get_transient( JR_FX_FIELDS_PREFIX.'-preview-'. $post->ID );
				if ( $html_tip ) $output .= $html_tip;
			endif;
			### END FEATURE
		}

	}

	if ( function_exists( 'jr_fx_validate_feat_days_left' ) ) {
		### BEGIN FEATURE: Days Left
		if ( jr_fx_validate_feat_days_left( $current_hook, 'Days Left' ) ) :
			$days = jr_fx_remaining_days( $post->ID );
			$output .= "<input type='hidden' class='jr_fx_days_left_pid' value='".$days."'>";
		endif;
		### END FEATURE
	}
	return $output;
}

function jr_fx_get_page_id( $page ) {

	$page_id = 0;
	switch( $page ){
		case 'submit_page_id' :
			$page_id = JR_Job_Submit_Page::get_id();
			break;
		case 'edit_job_page_id' :
			$page_id = JR_Job_Edit_Page::get_id();
			break;
		case 'dashboard_page_id':
			$page_id = JR_Dashboard_Page::get_id();
			break;
		case 'job_seeker_resume_page_id':
			$page_id = JR_Resume_Edit_Page::get_id();
			break;
		default:
			break;
	}
	return $page_id;
}

function jr_fx_set_custom_job_duration( $post_id, $pending_expire_days ) {
	update_post_meta( $post_id, JR_JOB_DURATION_META, $pending_expire_days );
	delete_post_meta( $post_id, '_jr_fx_expire_days' );
}

// TODO: MOVE THESE TO THE RELATED VALIDATE FUNCTIONS

// display the custom job duration field based on some criteria
function jr_fx_display_job_duration_field() {

	$custom_expire_field = jr_fx_validate( '_opt_jobs_duration_field' );
	if ( ! is_page( jr_fx_get_page_id( 'submit_page_id' ) ) || !$custom_expire_field || 'no' == $custom_expire_field )
		return false;

	if ( 'admin' == $custom_expire_field && ! current_user_can('manage_options') )
		return false;

	// don't display the duration to offered jobs
	if ( function_exists( 'jr_fx_validate_feat_jobs_free_offer' ) && jr_fx_validate_feat_jobs_free_offer() )
		return false;

	if ( ! current_theme_supports('app-payments') ) {
		 return (bool) ( sizeof( jr_get_user_job_packs() ) == 0 && !isset($_POST['job_pack']) );
	} else {
		// after JR 1.7, the custom job duration field can be displayed on any job plan (pack or single)
		return true;
	}

}

// display the custom application email field based on some criteria
function jr_fx_display_application_email_field() {

	$app_email_field = jr_fx_validate( '_opt_jobs_recipient_field' );
	if ( ! is_page( jr_fx_get_page_id( 'submit_page_id' ) ) || !$app_email_field || 'no' == $app_email_field )
		return false;

	if ( ! current_user_can('manage_options') )
		return false;

	return true;
}

function jr_fx_display_company_logo() {

	if ( ! is_page( jr_fx_get_page_id( 'submit_page_id' ) ) && ! is_page( jr_fx_get_page_id( 'edit_job_page_id' ) ) )
		return false;

	if ( ! function_exists('jr_fx_validate_feat_persistent_logo') || ! jr_fx_validate_feat_persistent_logo('jquery') )
		return false;

	return true;
}

function jr_fx_display_optional_apply_online_field() {

	$custom_optional_apply_field = jr_fx_validate( '_opt_jobs_optional_apply_online', 'yes' );
	if ( ! is_page( jr_fx_get_page_id( 'submit_page_id' ) ) || ! $custom_optional_apply_field )
		return false;

	if ( ! function_exists('jr_fx_validate_feat_persistent_logo') || ! jr_fx_validate_feat_persistent_logo('jquery') )
		return false;

	return true;
}

function jr_fx_display_optional_apply_linkedin_field() {

	$custom_optional_apply_ld_field = jr_fx_validate( '_opt_integration_linkedin_apply', 'optional' );
	if ( ! is_page( jr_fx_get_page_id( 'submit_page_id' ) ) || ! $custom_optional_apply_ld_field )
		return false;

	return true;
}

function jr_fx_get_duration_meta_key() {
	$meta_key = JR_JOB_DURATION_META;
	return $meta_key;
}

// set the job listing geo coordinates meta
function jr_fx_set_coordinates( $job_id ) {
	global $app_abbr;

	$data = array();
	foreach ( jr_get_geo_fields() as $field => $meta_name ) {
		$data[$field] = _jr_get_initial_field_value( $field );
	}

	if ( empty($data['jr_address']) )
		return;

	$latitude = '';
	$longitude = '';

	update_post_meta( $job_id, '_jr_address', $data['jr_address'] );
	update_post_meta( $job_id, '_jr_geo_latitude', $latitude );
	update_post_meta( $job_id, '_jr_geo_longitude', $longitude );

	update_post_meta( $job_id, 'geo_address', $data['jr_address']);
	update_post_meta( $job_id, 'geo_short_address', $data['jr_address']);

	$region = trim(strtoupper( get_option( $app_abbr.'_gmaps_region' ) ));

	update_post_meta( $job_id, 'geo_country', $region );
	update_post_meta( $job_id, 'geo_short_address_country', $region );
}

/********************
 * FEATURE FUNCTIONS
 ********************/

/*
 *********************************************************************************************************************
 * FEATURE: 			Show Breadcrumbs (free)
 *********************************************************************************************************************
 * Description:			Adds breadcrumbs
 * @param 				none
 * @returns  			The breadcrumb
 */  
function jr_fx_feat_breadcrumb() {
	global $wp_query, $post;

	if ( is_single() && $post->post_type != APP_POST_TYPE && $post->post_type != APP_POST_TYPE_RESUME )
		return;

	if ( is_home() ) 
		return;

	echo "<div class='jr_fx_breadcrumbs_wrap'><ul>";

	$terms = array( APP_TAX_TYPE, APP_TAX_CAT, APP_TAX_RESUME_JOB_TYPE, APP_TAX_RESUME_CATEGORY );
	foreach( $terms as $key => $term ) {
		$term = get_query_var( $term );
		if ( $term ) break;
	}

	$title = '';

	echo "<li><a href='";
	echo bloginfo('home');
	echo "'>";
	echo bloginfo('name');
	echo "</a></li>";
	echo "<li>></li>";
	//if ( is_archive() || is_single() || is_tax() ) {
	if ( $post->post_type == APP_POST_TYPE || $post->post_type == APP_POST_TYPE_RESUME || is_tax(APP_TAX_CAT) || isset($_GET['jobs_by_date']) ) {
		if ( $post->post_type == APP_POST_TYPE || is_tax(APP_TAX_CAT) || isset($_GET['jobs_by_date']) )
			$title = __( 'Jobs', JR_FX_i18N_DOMAIN );
		else
			$title = __( 'Resumes', JR_FX_i18N_DOMAIN );
		
		echo "<li><a href='";
		echo esc_url( get_post_type_archive_link( $post->post_type ) );
		echo "'>";
		echo $title;
		echo "</a></li>";
	}

	if ( is_category() || is_single() || $term ) {
		echo "<li>></li>";
		if ( is_category() ) {
			echo "<li>";
			the_category(" </li><li> ");
		}
		if ( is_single() ) {
			echo "<li>";
			the_title(" </li><li> ");
		}
		if ( $term ) {
			$object = $wp_query->get_queried_object();
			$taxonomy = $object->taxonomy;
			$label = get_taxonomy($taxonomy)->label;
			echo "</li><li>";
			echo $label;
			echo " ></li>";
			echo "</li><li>";
			echo $object->name; //get_term_by('slug', $term, $taxonomy )->name ;
			echo "</li>";
		}
	} elseif ( is_page() ) {
		echo "<li>";
		echo the_title(" </li><li> ");
	}
	elseif ( is_tag() ) {echo "<li>></li>"; echo "<li>></li>"; single_tag_title(); }
	elseif ( is_day() ) {echo "<li>></li>";echo "<li>". __("Archive for", JR_FX_i18N_DOMAIN); echo ' '; the_time('F jS, Y'); echo'</li>';}
	elseif ( is_month() ) { echo "<li>></li>";echo"<li>".__("Archive for ", JR_FX_i18N_DOMAIN); echo ' '; the_time('F, Y'); echo'</li>';}
	elseif ( is_year() ) {echo "<li>></li>";echo"<li>".__("Archive for ", JR_FX_i18N_DOMAIN); echo ' '; the_time('Y'); echo'</li>';}
	elseif ( is_author() ) {echo "<li>></li>";echo"<li>".__("Author Archive", JR_FX_i18N_DOMAIN); echo'</li>';}
	elseif ( isset($_GET['paged']) && !empty($_GET['paged']) ) {echo "<li>></li>";echo "<li>".__("Blog Archives", JR_FX_i18N_DOMAIN); echo'</li>';}
	elseif ( is_search() || isset($_GET['s']) || isset($_GET['location']) ) {echo "<li>></li>";echo"<li>".__("Search Results", JR_FX_i18N_DOMAIN); echo'</li>';}
	echo '</ul></div>';
}

/*
 *********************************************************************************************************************
 * FEATURE: 			Minimum Jobs for Auto Publish (free)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and return true if an action or filter is needed
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns True|False  If action or filter is needed, returns True, else, returs False
 */  
function jr_fx_validate_feat_jobs_min_auto_pub( $hook = '',  $action = 'no action' ) {
	global $jr_fx_log, $post;

	$log_message = '**** CHECKING RULES *** FEATURE [ Minimum Jobs for Auto Publish ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	# get the option
	$min_job_post_val = jr_fx_validate( '_int_jobs_min_auto_pub' );

	# check for the min job published rule
	if ( $min_job_post_val ) :

			# check if this call is made before inserting the job post to adjust posts total
			$plus_one = ( is_page( jr_fx_get_page_id( 'submit_page_id' ) ) && $hook != 'query' );

			# get total published jobs
			$total_posts =  jr_fx_count_user_posts( get_current_user_id() );

			# check total posts against total job offers
			if ( $total_posts + $plus_one < $min_job_post_val + 1 ) {

				# log
				$jr_fx_log->write_log($log_message . ' | Extra Info: total_posts('.$total_posts.') < Min. Job Pub. Rule ('.$min_job_post_val.')');

				### BEGIN FEATURE: First # job(s) are moderated - Show Message
				if ( jr_fx_validate( '_text_notice_min_auto_pub' ) ) jr_fx_notices( '_text_notice_min_auto_pub' );
				### END FEATURE

				# min value was not met. moderate the job.
				return true;

			} else {
				$jr_fx_log->write_log($log_message . ' | SKIPPED - RULES NOT MET [ total_posts('.$total_posts.'+1) > Min. Job Pub. Rule ('.$min_job_post_val.') ]');
			}

	endif;

	# no changes
	return false;
}

/*
 *********************************************************************************************************************
 * FEATURE: 			Moderate jobs posted by admins (free)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and return true if an action or filter is needed
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns True|False  If action or filter is needed, returns True, else, returs False
 */  
function jr_fx_validate_feat_jobs_moderate( $hook = '', $action = 'no action' ) {
	global $jr_fx_log;
	
	$log_message = '**** CHECKING RULES *** FEATURE [ Moderate jobs posted by admins ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';
	
	if ( jr_fx_validate('_opt_jobs_moderate','yes') && current_user_can('manage_options') ) {

		# log
		$jr_fx_log->write_log( $log_message );

		# min value was not met. moderate the job.
		return true;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
	}

	# no changes
	return false;
}

/*********************************************************************************************************************
 * FEATURE: 			Replace Date with Days Left - FREE 
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	False if the feature is set to 'no', with other values returns True 
 */ 
function jr_fx_validate_feat_days_left(  $hook = '', $action = 'no action' ){
	global $jr_fx_log;

	$log_message = '**** CHECKING RULES *** FEATURE [  Replace Date with Days Left ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	if ( !jr_fx_validate( '_opt_listings_days_left','no' ) ) {
		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_opt_listings_days_left = ' . jr_fx_validate( '_opt_listings_days_left' ) );
		return true;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}

/*********************************************************************************************************************
 * FEATURE: 			LinkedIn Profile
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	False if the feature is set to 'no', with other values returns True 
 */ 
function jr_fx_validate_feat_linkedin_profile(  $hook = '', $action = 'no action' ) {
	global $jr_fx_log;

	$log_message = '**** CHECKING RULES *** FEATURE [ LinkedIn Profile ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	if ( !is_user_logged_in() ) return false;

	$jr_fx_option = jr_fx_validate( '_opt_integration_linkedin_profile');

	if ( $jr_fx_option && 'no' != $jr_fx_option ) {
		# log
		if (JR_FX_LOG) $jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_opt_integration_linkedin_profile = ' . jr_fx_validate( '_opt_integration_linkedin_profile' ) );
		return true;
	} else {
		# log
		if (JR_FX_LOG) $jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}

/*********************************************************************************************************************
 * FEATURE: 			LinkedIn Profile Resume
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	False if the feature is set to 'no', with other values returns True 
 */ 
function jr_fx_validate_feat_linkedin_profile_resume(  $hook = '', $action = 'no action' ) {
	global $jr_fx_log;

	$log_message = '**** CHECKING RULES *** FEATURE [ LinkedIn Profile Resume ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	if ( !is_user_logged_in() ) return false;

	$jr_fx_option = jr_fx_validate( '_opt_integration_linkedin_profile');

	if ( $jr_fx_option && 'no' != $jr_fx_option ) {
		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_opt_integration_linkedin_profile_resume = ' . jr_fx_validate( '_opt_integration_linkedin_profile_resume' ) );
		return true;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}

/*********************************************************************************************************************
 * FEATURE: 			LinkedIn Profile Job
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	False if the feature is set to 'no', with other values returns True 
 */ 
function jr_fx_validate_feat_linkedin_profile_job(  $hook = '', $action = 'no action' ) {
	global $jr_fx_log;

	$log_message = '**** CHECKING RULES *** FEATURE [ LinkedIn Profile Job ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	$jr_fx_option = jr_fx_validate( '_opt_integration_linkedin_profile_job');

	if  ( $jr_fx_option && 'no' != $jr_fx_option ) {
		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_opt_integration_linkedin_profile_job = ' . jr_fx_validate( '_opt_integration_linkedin_profile_job' ) );
		return true;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}

/*********************************************************************************************************************
 * FEATURE: 			LinkedIn Company Profile
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	False if the feature is set to 'no', with other values returns True 
 */ 
function jr_fx_validate_feat_linkedin_company_profile(  $hook = '', $action = 'no action' ) {
	global $jr_fx_log;

	$log_message = '**** CHECKING RULES *** FEATURE [ LinkedIn Company Profile ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	$jr_fx_option = jr_fx_validate( '_opt_integration_linkedin_company_profile');

	if ( $jr_fx_option && 'no' != $jr_fx_option ) {
		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_opt_integration_linkedin_company_profile = ' . jr_fx_validate( '_opt_integration_linkedin_company_profile' ) );
		return true;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}

/*
 *********************************************************************************************************************
 * FEATURE: 			Job Preview - Styles/Colors (free)
 ********************************************************************************************************************* 
 * Description:			Reads the qTip styles/colors from a text file
 * @param $type			Values = colors|styles
 * @returns array		A List of the qTip styles/colors available
 */
function jr_fx_get_qtip_style( $type = 'styles' ) {
	$file = JR_FX_PLUGIN_DIR. 'js/jquery.qtip/jquery.qtip.'.$type.'.txt';

	if ( $type == 'colors' ) :
		$array = array( 'ui-tooltip-light' => __('Default (Light)',JR_FX_i18N_DOMAIN) );
	else:
		$array = array( 'ui-tooltip-shadow' => __('Default (Shadow)',JR_FX_i18N_DOMAIN) );
	endif;

	if (is_readable($file)) :

		if ( $file ) :
			$lines = file( $file );
			foreach ($lines as $line_num => $line) {
				$arr_line = explode( '|', $line );
				if ( $arr_line ) $array[htmlspecialchars($arr_line[0])] =  __( $arr_line[1], JR_FX_i18N_DOMAIN ) ;
			}
		endif;

	endif;
	return $array;
}

/*
 *********************************************************************************************************************
 * FEATURE: 			Job Preview Format (free)
 ********************************************************************************************************************* 
 * Description:			Formats the text on the job preview qTip
 * @param $curr_post	The post object
 * @param $size			The size (chars) for the job preview description
 * @returns html		The formatted/truncated content
 */
function jr_fx_preview_format( $curr_post, $size = 'excerpt' ) {

	# get the selected preview size
	$size = jr_fx_validate( '_opt_listings_preview');

	# default
	$content =  $curr_post->post_content; 

	switch ( $size ) {
		case 'excerpt':
			if ( $curr_post->post_excerpt != '' ):
				$content = $curr_post->post_excerpt;
				break;
			endif;
		case 'custom':
			# get the custom size 
			$custom_size = jr_fx_validate( '_opt_listings_preview_size' );
			$content = jr_fx_preview_excerpt($curr_post, $custom_size);
			break;
	}
	return $content;
}

/*
 *********************************************************************************************************************
 * FEATURE: 			Job Listings Preview (free)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and returns true or false
 * @param $hook			(optional) Hook name. Used for logging.
 * @param $action		(optional) Action needed. Used for logging.
 * @returns TRUE|FALSE 	Returns	False if the feature is set to 'no', with other values returns True 
 */ 
function jr_fx_validate_feat_job_preview(  $hook = '', $action = 'no action' ){
	global $jr_fx_log, $jr_fx_exclude;

	$log_message = '**** CHECKING RULES *** FEATURE [  Job List Preview ] *** HOOK [ '. $hook .'() ] *** ACTION [ ' . $action . ' ]';

	if ( ! $jr_fx_exclude && !jr_fx_validate( '_opt_listings_preview', 'no' ) ) {
			# log
			$jr_fx_log->write_log($log_message . ' | Extra Info: '. JR_FX_FIELDS_PREFIX . '_opt_listings_preview = ' . jr_fx_validate( '_opt_listings_preview' ) );
			return true;
	} else {
		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
		return false;
	}
}

/*
 *********************************************************************************************************************
 * FEATURE: 			Hide/Disable Google Maps (free)
 ********************************************************************************************************************* 
 * Description:			Validates the rules for this feature and updates the geolocation metadata on the DB
 * @param $post_id		The Post ID
 * @returns void  		
 */  
function jr_fx_validate_exec_feat_gmaps_location( $type = '', $hook = '', $action = 'no action', $post_id ) {
	global $posted, $jr_fx_log, $post;

	$log_message = 'type: '.$type.' | hook: '. $hook .'() | feature: Hide Google Maps | action: ' . $action;

	// don't let JR clear the geo address when saving on the admin edit page
	if ( jr_fx_validate('_opt_jobs_gmaps','yes') && is_admin() ) {

		remove_action( 'save_post', 'jr_save_meta_box' );

	} elseif( isset($_POST['jr_address']) && jr_fx_validate('_opt_jobs_gmaps','yes') ) {

		# if the hide google maps option is set to 'yes' associate the input location to the custom field 'geo_address'
		jr_fx_set_coordinates( $post_id );

		# log
		$jr_fx_log->write_log($log_message . ' | Extra Info: updated geo_address/geo_short_address = ' .  $posted['jr_address'] );

	} else {

		# log
		$jr_fx_log->write_log( $log_message . ' | SKIPPED - RULES NOT MET' );
	}

}

