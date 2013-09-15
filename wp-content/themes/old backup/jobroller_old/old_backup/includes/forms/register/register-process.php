<?php
/**
 * AppThemes Register Process
 * Processes JobRoller registration specific data
 *
 *
 * @version 1.6.3
 * @author AppThemes
 * @copyright 2010 all rights reserved
 *
 */

add_filter( 'user_register', 'jr_user_register_role' );
add_action( 'register_post', 'jr_process_register_form', 10, 3 );

// update the user role
function jr_user_register_role( $user_id ) {
	global $posted;

	if ( !empty( $posted['role'] ) )
		$user_role = $posted['role'];
	else
		$user_role = 'job_lister';

    wp_update_user( array ( 'ID' => $user_id, 'role' => $user_role ) );
}

// validate additional registration fields
function jr_process_register_form( $login, $email, $errors ) {
	global $posted;

	// Check terms acceptance
	if ( get_option('jr_terms_page_id') > 0 || 'yes' == get_option('jr_enable_terms_conditions') ) {
		if ( !isset( $_POST['terms'] ) ) $errors->add('empty_terms', __('<strong>Notice</strong>: You must accept our terms and conditions in order to register.', APP_TD));
	};

	// validate the  user role

	if ( 'yes' == get_option('jr_allow_job_seekers') ) {

		if ( !isset($_POST['role']) ) {

			$errors->add( 'empty_role', __('<strong>Notice</strong>: Please select a role.', APP_TD) );

		} else {

			if ( ! in_array( $_POST['role'], array( 'job_lister', 'job_seeker', 'recruiter' ) ) )
				$errors->add( 'empty_role', __( '<strong>Notice</strong>: Invalid Role!', APP_TD ) );
			else
				$posted['role'] = stripslashes(trim($_POST['role']));

		}

	}

}
