<?php
/**
 * Resumes Related Functions
 *
 * @version 1.7
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */

add_action( 'wp_loaded', 'jr_handle_resume_plan_order', 11 );

add_action( 'appthemes_transaction_activated', '_jr_activate_resume_plan', 10 );

add_action( 'user_resume_subscription_started', 'jr_user_resume_subscription_start_meta', 10, 3 );
add_action( 'user_resume_subscription_ended', 'jr_user_resume_subscription_end_meta' );

add_action( 'jr_addon_resume_ended', 'jr_resume_addon_remove', 10, 2 );

add_action( 'pre_get_posts', 'jr_resumes_posts_per_page' );

// set resumes per page on all resumes pages
function jr_resumes_posts_per_page( $query ) {

	if ( ( ! $query->is_main_query() && ! $query->is_search() ) || is_admin() )
		return;

	$taxonomies = array( APP_TAX_RESUME_SPECIALITIES, APP_TAX_RESUME_GROUPS, APP_TAX_RESUME_LANGUAGES, APP_TAX_RESUME_CATEGORY, APP_TAX_RESUME_JOB_TYPE );

	if ( $query->is_post_type_archive(APP_POST_TYPE_RESUME) || $query->is_tax( $taxonomies ) || get_query_var('resume_search') ) {
		$query->set( 'posts_per_page' , jr_get_resumes_per_page() );
	}
}

/**
 * Handle resumes subscriptions
 */
function jr_handle_resume_plan_order() {

	if ( !isset( $_POST['action'] ) || 'purchase-resume-plan' != $_POST['action'] || !empty($_POST['goback']) )
		return;

	$errors = apply_filters( 'appthemes_validate_purchase_fields', jr_get_listing_error_obj() );
	if( $errors->get_error_codes() ){
		return false;
	}

	$plan_id = intval($_POST['plan']);
	$plan = get_post( $plan_id );
	$plan_data = get_post_custom( $plan_id );

	$new_order = true;

	if ( empty($_POST['order_id']) ) {
 		$order = _jr_create_order( $plan_id );
	} else {
		$order = appthemes_get_order( intval($_POST['order_id']) );
		$order->remove_item();

		$new_order = false;
	}

	$attach = get_post( $order->get_id() );

	do_action( 'appthemes_create_order', $order, $plan, $plan_data, $attach );

	// action hook executed only on each new order
	if ( $new_order ) jr_after_insert_order( $order );

	_jr_order_redirect_user( $order, $plan );
}

/**
 * Activate the resumes subscription and/or trial
 */
function _jr_activate_resume_plan( $order ){

	$order_data = _jr_get_order_job_info( $order );
	if ( ! $order_data )
		return;

	extract( $order_data );

	if ( $plan->post_type != APPTHEMES_RESUMES_PLAN_PTYPE )
		return;

	do_action( 'user_resume_subscription_started', $order->get_author(), $plan->ID, $plan_data );
}

/**
 * Check if resumes are enabled or not
 */
function jr_resumes_are_disabled() {
	if (get_option('jr_allow_job_seekers')=='no') return true;
	return false;
}

/**
 * Check if resumes are visible or not
 */
function jr_resume_is_visible( $single = '' ) {

	if ( ! $single )
		$visibility_option = get_option('jr_resume_listing_visibility');
	else
		$visibility_option = get_option('jr_resume_visibility');

	/* Support keys so logged out users can view a resume if they are sent the link via email (apply form) */
	if ( is_single() ) :

		if (isset($_GET['key']) && $_GET['key']) :
			global $post;
			$key = get_post_meta( $post->ID, '_view_key', true );
			if ($key==$_GET['key']) :
				return true;
			endif;
			
		endif;

	endif;

	// if a subscriptions is required and the current user has a subscription or an active resume addon it will always have priority over the visibility settings
	// subscription check is skipped if the visibility setting is set to "Public"
	if ( jr_viewing_resumes_require_subscription() && 'public' != $visibility_option ) {

		// check for specific resumes access
		$resume_access = jr_user_resumes_access();
		if ( ( $single && empty($resume_access['access']['view']) ) || ( !$single && empty($resume_access['access']['browse']) ) )
			return false;
		else
			// user was given access
			return true;

	}

	return jr_user_resume_visibility( $single );
}

/**
 * Check if current user can actually subscribe
 */
function jr_current_user_can_subscribe_for_resumes() {

	if ( ! is_user_logged_in() ) return false;

	if ( ! jr_viewing_resumes_require_subscription() ) return false;

	return jr_user_resume_visibility();
}

function jr_user_resume_visibility( $visibility_option = '' ) {
	if ( ! $visibility_option )
		$visibility_option = get_option('jr_resume_listing_visibility');
	else
		$visibility_option = get_option('jr_resume_visibility');

	switch ($visibility_option) :
		case "public" :
			return true;
		break;
		case "members" :
			if ( is_user_logged_in() ) :
				return true;
			endif;
		break;
		case "recruiters" :
		case "members_listers":
			if ( current_user_can('can_view_resumes') && current_user_can('can_submit_job') ) :
				return true;
			endif;
			// skip the break if checking for all listers members (can submit jobs)
			if ( 'members_listers' != $visibility_option ) {
				break;
			}
		case "listers" :
		case "members_listers":
			if ( ( current_user_can('can_submit_job') && !current_user_can('can_view_resumes') ) || current_user_can('manage_options') ) :
				return true;
			endif;
		break;
	endswitch;
	return false;
}
 
/**
 * Check if resumes require subscription
 */
function jr_viewing_resumes_require_subscription() {
	return ( 'yes' == get_option('jr_resume_require_subscription') );
}

/**
 * Check if resumes are disabled/visible and redirect
 */
function jr_resume_page_auth() {
	
	## Enabled/Disabled
	if (jr_resumes_are_disabled()) :
		wp_redirect(get_bloginfo('url'));
		exit;
	endif;
	
}

/**
 * Checks for valid subscriptions (auto/manual) and ends expired manual subscriptions
 */
function jr_resume_valid_subscr( $user_id = 0 ) {
	$user_id = $user_id ? $user_id : get_current_user_id();

	$valid = false;

	$active_subscr = get_user_meta( $user_id, '_valid_resume_subscription', true );
	if ( ! $active_subscr )
		return false;

	// Grab the stored subscription end date
	$end_date = get_user_meta( $user_id, '_valid_resume_subscription_end', true );
	if ( $end_date && $active_subscr ) :

		$days = ceil( ( $end_date-strtotime('NOW') ) / 86400 );
		//subscription ended
		if ( $days < 1 ):
			// end subscription
			do_action( 'user_resume_subscription_ended', $user_id );
		else:
			$valid = true;
		endif;
		
	endif;

	return apply_filters( 'jr_resume_valid_subscr', $valid, $user_id );
}

function jr_resume_addon_view_access( $valid, $user_id ) {
	if ( _jr_resume_addon_valid_access( $user_id, JR_ITEM_VIEW_RESUMES ) && is_single() ) {
		$valid = true;
	}
	return $valid;
}

function jr_resume_addon_browse_access( $valid, $user_id ) {
	if ( _jr_resume_addon_valid_access( $user_id, JR_ITEM_BROWSE_RESUMES ) && ! is_single() ) {
		$valid = true;
	}
	return $valid;
}

/**
 * Checks for valid temporary resumes access (auto/manual) and expires access if ended
 */
function _jr_resume_addon_valid_access( $user_id, $addon ) {

	$temp_access = get_user_meta( $user_id, $addon, true );
	if ( $temp_access ) :
		$start_date = get_user_meta( $user_id, $addon . '_start_date', true );
		$duration = get_user_meta( $user_id, $addon . '_duration', true );

		$end_date = strtotime( '+'.$duration.' DAYS', strtotime($start_date) );
		//subscription ended
		if ( $end_date < strtotime('NOW') ):
			// end subscription
			do_action( 'jr_addon_resume_ended', $user_id, $addon );
		else:
			return $end_date;
		endif;
	endif;
	return false;
}

function jr_resume_addon_remove( $user_id, $addon ){
	delete_user_meta( $user_id, $addon );
	delete_user_meta( $user_id, $addon .'_start_date' );
	delete_user_meta( $user_id, $addon .'_duration' );
}

function jr_add_resumes_access( $user_id, $addon, $duration ){
	update_user_meta( $user_id, $addon , true );

	$curr_duration = intval( get_user_meta( $user_id, $addon .'_duration', true ) );
	if ( $duration >= $curr_duration ) {
		update_user_meta( $user_id, $addon .'_start_date', current_time( 'mysql' ) );
		update_user_meta( $user_id, $addon .'_duration', $duration );
	}
}

/**
 * Update resume subscriptions user meta for ending subscriptions
 */
function jr_user_resume_subscription_end_meta( $user_id ) {
	delete_user_meta( $user_id, '_valid_resume_subscription' );
	delete_user_meta( $user_id, '_valid_resume_subscription_order' );
	delete_user_meta( $user_id, '_valid_resume_trial' );
}


/**
 * Update resume subscriptions user meta for new subscriptions
 */
function jr_user_resume_subscription_start_meta( $user_id, $plan_id, $plan_data ) {
	if ( !empty($plan_data[JR_FIELD_PREFIX.'trial'][0]) ) {
		update_user_meta( $user_id, '_valid_resume_trial', 1 );
	} else {
		delete_user_meta( $user_id, '_valid_resume_trial' );
	}
	update_user_meta( $user_id, '_valid_resume_subscription', $plan_id );
	update_user_meta( $user_id, '_valid_resume_subscription_start', strtotime("now") );
	update_user_meta( $user_id, '_valid_resume_subscription_end', jr_resume_calc_end_date( $plan_data[JR_FIELD_PREFIX.'duration'][0] ) );
}

/**
 * Calculate and return new resume subcription dates
 */
function jr_resume_calc_end_date( $length ) {
	$date = strtotime( '+'.$length.' days', current_time('timestamp') );
	return $date;
	
}

function jr_resume_valid_trial( $user_id = 0 ) {
	$user_id = $user_id ? $user_id : get_current_user_id();

	return (bool) ( get_user_meta( $user_id, '_valid_resume_trial', true ) );
}

function jr_user_resumes_access( $user_id = 0 ) {
	$user_id = $user_id ? $user_id : get_current_user_id();

	$valid_subscription = get_user_meta( $user_id, '_valid_resume_subscription', true );
	$access_end_date = get_user_meta( $user_id, '_valid_resume_subscription_end', true );
	$active_subscription = ( ( $valid_subscription || jr_resume_valid_trial( $user_id ) ) && $access_end_date );

	// if no valid subscriptions or trials look for temporary Resumes access
	if ( ! $active_subscription ) {

		$view_resumes = _jr_resume_addon_valid_access( $user_id, JR_ITEM_VIEW_RESUMES );
		$browse_resumes = _jr_resume_addon_valid_access( $user_id, JR_ITEM_BROWSE_RESUMES );
		$level = 'temporary';

	} else {
		$level = 'full';
		$view_resumes = $browse_resumes = (int)$access_end_date;
	}

	if ( ! $browse_resumes && ! $view_resumes )
		return;

	$resumes_access = array( 
		'level' => $level,
		'access' => array(),
	);

	if ( $browse_resumes ) {
		$resumes_access['access']['browse'] = array (
			'description' => __('Browse Resumes',APP_TD),
			'end_date' => appthemes_display_date( $browse_resumes ),
		);
	}

	if ( $view_resumes ) {
		$resumes_access['access']['view'] = array (
			'description' => __('View Resumes',APP_TD),
			'end_date' => appthemes_display_date( $view_resumes ),
		);
	}

	return $resumes_access;
}

function jr_get_resume_subscribers( $show = 'active', $args = array() ) {
	$default_args = array(
		'meta_query' => array(
			array(
				'key' => '_valid_resume_subscription',
				'value' => 0,
				'compare' => '>',
			),
		),
	);
	$args = wp_parse_args( $args, $default_args );

	if ( 'inactive' == $show ) {

		$active_users = new WP_User_Query( $args );

		$active_users = wp_list_pluck( $active_users->get_results(), 'ID' );

		$args['exclude'] = $active_users;
		$args['meta_query'] = array();

	}
	return new WP_User_Query( $args );
}

// expire subscriptions on the pre-set date
function jr_check_expired_subscriptions() {

	$expired_subscr = new WP_User_Query( array(
		'meta_query' => array(
				'relation' => 'AND',
				array(
						'key' => '_valid_resume_subscription',
						'value' => 0,
						'compare' => '>',
				),
				array(
						'key' => '_valid_resume_subscription_end',
						'value' => current_time( 'timestamp' ),
						'compare' => '<'
				),
		),
	) );

	foreach ( $expired_subscr->results as $user ) {
		do_action( 'user_resume_subscription_ended', $user->ID );
	}

}
