<?php
/**
 * These are scripts used within the AppThemes admin pages
 *
 * @package AppThemes
 *
 */

add_action( 'admin_enqueue_scripts', 'appthemes_load_admin_scripts' );
add_action( 'admin_print_styles', 'jr_admin_styles' );

// correctly load all the scripts so they don't conflict with plugins
function appthemes_load_admin_scripts( $hook ) {
	global $is_IE;

	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('media-upload'); // needed for image upload

	wp_enqueue_script('thickbox'); // needed for image upload
	wp_enqueue_style('thickbox'); // needed for image upload

	wp_enqueue_script('easytooltip', get_template_directory_uri().'/includes/js/easyTooltip.js', array('jquery'), '1.0');

	if ($is_IE) // only load this support js when browser is IE
		wp_enqueue_script('excanvas', get_template_directory_uri().'/includes/js/flot/excanvas.min.js', array('jquery'), '1.2');

	wp_enqueue_script('flot', get_template_directory_uri().'/includes/js/flot/jquery.flot.min.js', array('jquery'), '1.2');

	$admin_pages = array( 
		'toplevel_page_app-dashboard',
		'jobroller_page_settings',
		'jobroller_page_emails',
		'jobroller_page_alerts',
		'jobroller_page_integration',
		'jobroller_page_sysinfo',
		'edit-pricing-plan',
		'edit-resumes-pricing-plan',
		'payments_page_app-payments-settings',
	);

	if( ! in_array( get_current_screen()->id, $admin_pages ) )
		return;

	 wp_enqueue_script('admin-scripts', get_template_directory_uri().'/includes/admin/admin-scripts.js', array('jquery','media-upload','thickbox'), '1.2');

}

function jr_admin_styles() {
	appthemes_menu_sprite_css( array(
		'#toplevel_page_app-dashboard',
		'#adminmenu #menu-posts-job_listing',
	) );
}