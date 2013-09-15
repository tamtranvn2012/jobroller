<?php
/**
 * JobRoller Preferences Process
 * Processes Preferences form.
 *
 *
 * @version 1.4
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */

function jr_process_job_seeker_prefs_form() {
	
	global $post, $posted, $message;
	
	$errors = new WP_Error();
	if (isset($_POST['save_prefs']) && $_POST['save_prefs']) :
	
		// Get (and clean) data
		$fields = array(
			'career_status',
			'willing_to_relocate',
			'willing_to_travel',
			//'where_you_can_work',
			'keywords',
			'search_location',
			//'your_location',
			//'your_job_title',
			'availability_month',
			'availability_year'
		);
		
		foreach ($fields as $field) {
			if (isset($_POST[$field])) $posted[$field] = stripslashes(trim($_POST[$field])); else $posted[$field] = '';
		}
		
		$job_types = array();
		if (isset($_POST['prefs_job_types'])) $prefs_job_types = $_POST['prefs_job_types']; else $prefs_job_types = '';
		if (is_array($prefs_job_types)) :
			foreach ($prefs_job_types as $key => $value) :
				$job_types[] = $key;
			endforeach;
		endif;
		
		/* Save Prefs */

		update_user_meta(get_current_user_id(), 'availability_month', $posted['availability_month']);
		update_user_meta(get_current_user_id(), 'availability_year', (int) $posted['availability_year']);
		
		update_user_meta(get_current_user_id(), 'career_status', $posted['career_status']);
		update_user_meta(get_current_user_id(), 'willing_to_relocate', $posted['willing_to_relocate']);
		update_user_meta(get_current_user_id(), 'willing_to_travel', $posted['willing_to_travel']);
		//update_user_meta(get_current_user_id(), 'where_you_can_work', $posted['where_you_can_work']);
		update_user_meta(get_current_user_id(), 'keywords', $posted['keywords']);
		update_user_meta(get_current_user_id(), 'search_location', $posted['search_location']);
		update_user_meta(get_current_user_id(), 'job_types', $job_types);
		
		//update_user_meta(get_current_user_id(), 'your_location', $posted['your_location']);
		//update_user_meta(get_current_user_id(), 'your_job_title', $posted['your_job_title']);
		
		$message = __('Preferences Saved', APP_TD);
		
	endif;

}

add_action('jr_process_job_seeker_form', 'jr_process_job_seeker_prefs_form');
