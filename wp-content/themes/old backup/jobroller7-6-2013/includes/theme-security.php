<?php
/**
 * Function to prevent visitors without admin permissions
 * to access the wordpress backend. If you wish to permit
 * others besides admins acces, change the user_level
 * to a different number.
 *
 * http://codex.wordpress.org/Roles_and_Capabilities#level_8
 *
 * @global <type> $user_level
 *
 * in order to use this for wpmu, you need to follow the comment
 * instructions below in all locations and make the changes
 */

function jr_security_check() {

	// secure the backend for non ajax calls
	if (isset($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) != 'admin-ajax.php'):
		$jr_access_level = get_option('jr_admin_security');
	endif;	

    if (!isset($jr_access_level) || $jr_access_level=='') $jr_access_level = 'read'; // if there's no value then give everyone access

    if ( is_user_logged_in() && !current_user_can($jr_access_level) ) {

    // comment out the above two lines and uncomment this line if you are using
    // wpmu and want to block back office access to everyone except admins
    // if (!is_site_admin()) {

		status_header(404);
		nocache_headers();
		include( get_404_template() );
		exit;

    }

}

// if people are having trouble with this option, they can disable it
if (get_option('jr_admin_security') != 'disable') {

    // comment out the below line to work with wpmu
    if ( !defined('DOING_AJAX') ) add_action('admin_init', 'jr_security_check', 1);

}
?>