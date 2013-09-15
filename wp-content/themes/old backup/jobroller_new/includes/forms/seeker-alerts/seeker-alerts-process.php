<?php
/**
 * JobRoller Alerts Process
 * Process the current user job alert settings on 'Save' and stores the meta keys on the user meta:
 * . Stores the user alert query string on the 'jr_alert' meta_key as a query string
 * . Stores keywords on a separate key as a regular expression to use on the alert SQL querys
 * . Stores each user criteria on a separate 'jr_alert_meta_{criteria}' key
 *
 * Alert querys are later evaluated against 'jr_alert' and 'jr_alert_keywords'
 *
 * @version 1.6
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */

add_action('jr_process_job_seeker_form', 'jr_process_job_seeker_alerts_form');

function jr_process_job_seeker_alerts_form() {
	global $posted, $message, $wpdb, $user_ID, $app_abbr, $errors;

	if ( !empty($_POST['save_alerts']) ) :

		$job_types = $job_cats = array();
		$alert = '';

		$errors = new WP_Error();

		// Get (and clean) data
		$fields = array(
			'alert_keywords',
			'alert_location',
			'alert_status'
		);

		$required = array(
			'alert_keywords',
			'alert_location',
			'alert_job_type',
			'alert_job_cat',
		);

		$valid_alert = false;
		foreach ($fields as $field) 
			if (isset($_POST[$field])) $posted[$field] = stripslashes(trim(strtolower($_POST[$field]))); else $posted[$field] = '';
				
		foreach ($required as $field) 
			if ( !empty($_POST[$field]) ) $valid_alert = true;

		// get (and clean) job types
		if ( isset($_POST['alert_job_type']) ) foreach ( $_POST['alert_job_type'] as $term )   
			if ( term_exists( (int)$term, APP_TAX_TYPE ) ) $job_types[] = $term;

		// get (and clean) job categories
		if ( isset($_POST['alert_job_cat']) ) foreach ( $_POST['alert_job_cat'] as $term )  
			if ( term_exists( (int)$term, APP_TAX_CAT ) ) $job_cats[] = $term;

		// keywords
		if ( !empty($posted['alert_keywords']) ):	
			
			$keywords = explode(',', $posted['alert_keywords']);
			$keywords_regexp = array_map('trim',$keywords);
			$keywords_regexp = implode('|', $keywords_regexp);

			// store keywords as as a regular expression '(keyword1|keyword2)'
			update_user_meta( $user_ID, $app_abbr.'_alert_keywords', '(' . $keywords_regexp . ')');
			update_user_meta( $user_ID, $app_abbr.'_alert_meta_keyword', $keywords);

		else:

			delete_user_meta( $user_ID, $app_abbr.'_alert_keywords');
			delete_user_meta( $user_ID, $app_abbr.'_alert_meta_keyword');

		endif;

		// locations
		if ( !empty($posted['alert_location']) ) :
			
			$i = 0;
			$locations = explode(',', $posted['alert_location']);
			foreach ( $locations as $location ):
				if ($i++ > 0) $alert .= '|?';
				$alert .= 'location=' . trim(strtolower($location));
			endforeach;
			
			update_user_meta( $user_ID, $app_abbr.'_alert_meta_location', $locations);

		else:

			$alert = 'location=anywhere';
			delete_user_meta( $user_ID, $app_abbr.'_alert_meta_location');
			
		endif;

		if ($alert) $alert .= '&';
		
		// job types
		if ( !empty($job_types) ) :

			$i = 0;
			foreach ( $job_types as $job_type ):
				if ($i++ > 0) $alert .= '|?';
				$alert .= 'job_type=' . trim(strtolower($job_type));
			endforeach;

			update_user_meta( $user_ID, $app_abbr.'_alert_meta_job_type', $job_types );
			
		else:

			$alert .= 'job_type=all';
			delete_user_meta( $user_ID, $app_abbr.'_alert_meta_job_type');	
			
		endif;

		if ($alert) $alert .= '&';

		// job categories
		if ( !empty($job_cats) ) :

			$i = 0;
			foreach ( $job_cats as $job_cat ):
				if ($i++ > 0) $alert .= '|?';
				$alert .= 'job_cat=' . trim(strtolower($job_cat));
			endforeach;

			update_user_meta( $user_ID, $app_abbr.'_alert_meta_job_cat', $job_cats );
			
		else:

			$alert .= 'job_cat=all';
			delete_user_meta( $user_ID, $app_abbr.'_alert_meta_job_cat');

		endif;

		if ( !$valid_alert ):
			$errors->add('submit_error', __('<strong>ERROR</strong>: ', APP_TD).__('You haven\'t specified any jobs alerts criteria.', APP_TD));
			update_user_meta( $user_ID, $app_abbr.'_alert_status', 'inactive');
		endif;

		if ($errors && sizeof($errors)>0 && $errors->get_error_code()) { $posted['errors'] = $errors; } else {

			$alert = '?' . $alert;
			$alert_status = empty($posted['alert_status']) ? 'active' : $posted['alert_status']; // if site is only using feeds for job alerts always set alert status to 'active'

			// store the user alert and the status subscribed/unsubscribed
			update_user_meta( $user_ID, $app_abbr.'_alert', $alert );
			update_user_meta( $user_ID, $app_abbr.'_alert_status', $alert_status );

			if ( 'yes' == get_option($app_abbr.'_job_alerts_feed') ) {

				global $wp_rewrite;
				$user_feed_key = get_user_meta($user_ID, $app_abbr.'_alert_feed_key', true);

				if ( 'active' == $alert_status ) {
					add_feed($user_feed_key, 'jr_job_alerts_do_feed');
				} else {
					$feed_key = array_search($user_feed_key, $wp_rewrite->feeds);
					unset($wp_rewrite->feeds[$feed_key]);
				}

				// flush rewrite rules if User subscribes/unsubscribes the RSS feed
				flush_rewrite_rules();

			}

			$message = __('Job Alerts Preferences Saved', APP_TD);
		}
		
	endif;
}
