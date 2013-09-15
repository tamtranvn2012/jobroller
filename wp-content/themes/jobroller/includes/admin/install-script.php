<?php
/**
* Install script to insert default data.
* Only run if theme is being activated
* for the first time.
*
*/
global $app_theme, $app_version, $jr_log, $wp_rewrite;

// install JobRoller content on theme activation
add_action( 'appthemes_first_run', 'install_jobroller', 10 );
add_action( 'appthemes_first_run', 'jr_init_reading_settings', 15 );
add_action( 'appthemes_first_run', 'jr_init_price_plans', 17 );

/**
 * Init User Roles
 */
function jr_init_roles() {
	global $wp_roles;

	if (class_exists('WP_Roles'))
		if ( ! isset( $wp_roles ) )
			$wp_roles = new WP_Roles();

	$wp_roles->add_role( 'job_seeker', __('Job Seeker', APP_TD), array(
		'read' => true,
		'edit_posts' => false,
		'delete_posts' => false,
		'can_submit_resume' => true
	));

	$wp_roles->add_role( 'job_lister', __('Job Lister', APP_TD), array(
		'read' => true,
		'edit_posts' => false,
		'delete_posts' => false,
		'can_submit_job' => true,
	));

	$wp_roles->add_role( 'recruiter', __('Recruiter', APP_TD), array(
		'read' => true,
		'edit_posts' => false,
		'delete_posts' => false,
		'can_submit_job' => true,
		'can_view_resumes' => true,
	));

	if (is_object($wp_roles)) :
		$wp_roles->add_cap( 'administrator', 'can_submit_job' );
		$wp_roles->add_cap( 'administrator', 'can_view_resumes' );
		$wp_roles->add_cap( 'administrator', 'edit_jobs' );

		$wp_roles->add_cap( 'editor', 'can_submit_job' );
		$wp_roles->add_cap( 'editor', 'edit_jobs' );

		$wp_roles->add_cap( 'contributor', 'can_submit_job' );
		$wp_roles->add_cap( 'contributor', 'edit_jobs' );

		$wp_roles->add_cap( 'author', 'can_submit_job' );
		$wp_roles->add_cap( 'author', 'edit_jobs' );

		$wp_roles->add_cap( 'job_lister', 'edit_jobs' );
		$wp_roles->add_cap( 'recruiter', 'edit_jobs' );
	endif;
}

function install_jobroller() {

	// define vars
	global $app_theme, $app_version, $jr_log;

	$jr_log->clear_log();

	// Clear cron
	wp_clear_scheduled_hook('jr_check_jobs_expired');
	wp_clear_scheduled_hook('jr_prune_expired_featured');
	wp_clear_scheduled_hook('jr_job_alerts');
	wp_clear_scheduled_hook('appthemes_update_check');

	// insert the default values
	jr_default_options();

	// insert additional default values
	jr_default_values();

	// run the table install script
	jr_tables_install();

	// insert the default job types
	jr_create_cats();

	// clear cached locations
	_jr_clear_geolocation_cache();

	if ( function_exists('delete_site_transient') ) {
		$theme_name = strtolower($app_theme);
		delete_site_transient($theme_name.'_update_theme');
	}

	set_transient( 'jr_flush_rewrite_rules', 1, 300 );

}

function jr_init_reading_settings() {
	if ( !get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
}


// Set option and default values
function jr_default_options() {
	global $options_settings, $options_pricing, $options_integration, $options_emails, $options_alerts;

	$option_group = array(
		$options_settings,
		$options_pricing,
		$options_integration,
		$options_emails,
		$options_alerts,
	);

	foreach ($option_group as $option ) {
		foreach ($option as $value) {
			if (isset($value['std'])) {
				add_option($value['id'], $value['std']);
			}
		}
	}

}

// Create the JobRoller db tables
function jr_tables_install() {

	// create the job alerts table
	scb_register_table( 'jr_alerts' );

	$sql = "
			post_id bigint(20) NOT NULL,
			alert_type varchar(1024) NOT NULL,
			last_user_id bigint(20) DEFAULT NULL,
			last_activity timestamp NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (post_id),
			KEY alert_type_idx (alert_type(255))
		";

	scb_install_table( 'jr_alerts', $sql );
}

// create job cats
function jr_create_cats() {
	global $wpdb;

	$listings = get_posts( array(
		'post_type' => APP_POST_TYPE,
		'posts_per_page' => 1
	) );

	if ( !empty( $listings ) )
		return;

	// Default categories
	$job_cats = array(
		'WordPress',
	);
	if ( $job_cats ) foreach( $job_cats as $job_cat )
		wp_insert_term( $job_cat, APP_TAX_CAT );

	$job_types = array(
		'Full-Time',
		'Part-Time',
		'Freelance',
		'Temporary',
		'Internship'
	);

	if ( $job_types ) foreach( $job_types as $type )
		$ins_id = wp_insert_term( $type, APP_TAX_TYPE );

	// Default Salaries
	$salaries = array(
		'Less than 20,000',
		'20,000 - 40,000',
		'40,000 - 60,000',
		'60,000 - 80,000',
		'80,000 - 100,000',
		'100,000 and above'
	);
	if ( $salaries ) foreach( $salaries as $salary )
		$ins_id = wp_insert_term( $salary, APP_TAX_SALARY );

	// Default Resume Languages
	$languages = array(
		'Mandarin',
		'English',
		'Spanish',
		'Arabic',
		'Hindi/Urdu',
		'Bengali',
		'Portuguese',
		'Russian',
		'Japanese',
		'German',
		'French',
		'Italian'
	);
	if ( $languages ) foreach( $languages as $lang )
		$ins_id = wp_insert_term( $lang, APP_TAX_RESUME_LANGUAGES );

	// Default resume categories for job industry
	$resume_category = array(
		'Admin', 'Accounting', 'Agriculture', 'Aviation', 'Automotive', 'Architecture', 'Advertising', 'Banking', 'Building', 'Construction', 'Catering', 'Charity', 'Childcare', 'Customer Service', 'Driving', 'Design', 'Defence', 'Engineering', 'Executive', 'Education', 'Electronics', 'Environmental', 'Finance', 'Government', 'Hospitality', 'Health', 'IT', 'Industrial', 'Insurance', 'Leisure', 'Law', 'Logistics', 'Marketing', 'Medical', 'Manufacturing', 'Media', 'Mechanical', 'Nursing', 'Public Sector', 'Pharmaceutical', 'Retail', 'Recruitment', 'Social Care', 'Security', 'Secretarial', 'Scientific', 'Sports', 'Surveying', 'Travel', 'Telecommunications', 'Tourism', 'Other'
	);
	if ( $resume_category ) foreach( $resume_category as $cat )
		wp_insert_term( $cat, APP_TAX_RESUME_CATEGORY );

	// Default desired job types for resumes
	$resume_job_types = array(
		'Full-Time',
		'Part-Time',
		'Freelance',
		'Temporary',
		'Internship'
	);
	if ( $resume_job_types ) foreach( $resume_job_types as $resume_job_type )
		wp_insert_term( $resume_job_type, APP_TAX_RESUME_JOB_TYPE );

}

function jr_init_price_plans() {

	// create default single price plans if there are no plans

	if ( jr_get_available_plans( array( 'post_status' => 'any' ) ) )
		return;

	// Trial plan

	$title =  __( 'Trial', APP_TD );

	$plan = array(
		'post_title' 		=> $title,
		'post_name' 		=> $title,
		'post_type' 		=> APPTHEMES_PRICE_PLAN_PTYPE,
		'post_status'		=> 'publish',
		'tax_input' => array(
				APP_TAX_CAT => wp_list_pluck( get_terms( APP_TAX_CAT, array( 'hide_empty' => false ) ), 'term_id' ),
		)
	);
	$plan_id = wp_insert_post( $plan, true );

	$data = array(
		'title' => $title,
		'description' => __( 'Try before you buy. Submit one job for Free, for 15 days.', APP_TD ),
		'duration' => 15,
		'price' => 0,
		'relist_price' => 15,
		'limit' => 1,
	);

	foreach ( $data as $key => $value ) {
		if ( 'title' != $key ) $key = JR_FIELD_PREFIX.$key;
		add_post_meta( $plan_id, $key, $value );
	}


	// Starter plan

	$title =  __( 'Starter', APP_TD );

	$plan = array(
		'post_title' 		=> $title,
		'post_name' 		=> $title,
		'post_type' 		=> APPTHEMES_PRICE_PLAN_PTYPE,
		'post_status'		=> 'publish',
		'tax_input' => array(
				APP_TAX_CAT => wp_list_pluck( get_terms( APP_TAX_CAT, array( 'hide_empty' => false ) ), 'term_id' ),
		)
	);
	$plan_id = wp_insert_post( $plan, true );

	$data = array(
		'title' => $title,
		'description' => __( 'Post your job listing for 30 days with our Starter plan. No frills, no fuss.', APP_TD ),
		'duration' => 30,
		'price' => 15,
		'relist_price' => 15,
		'limit' => 0,
	);

	foreach ( $data as $key => $value ) {
		if ( 'title' != $key ) $key = JR_FIELD_PREFIX.$key;
		add_post_meta( $plan_id, $key, $value );
	}
}

// set additional default values
function jr_default_values() {
	global $wpdb, $app_abbr, $app_version;

	// set the default new WP user role only if it's currently subscriber
	if (get_option('default_role') == 'subscriber') update_option('default_role', 'contributor');

	// check the "membership" box to enable wordpress registration
	if (get_option('users_can_register') == 0) update_option('users_can_register', 1);

	if (get_option($app_abbr.'_nu_admin_email') == false) update_option($app_abbr.'_nu_admin_email', 'yes');

	// set default new user registration email values
	if (get_option($app_abbr.'_nu_from_name') == false) update_option($app_abbr.'_nu_from_name', wp_specialchars_decode(get_option('blogname'), ENT_QUOTES));
	if (get_option($app_abbr.'_nu_from_email') == false) update_option($app_abbr.'_nu_from_email', get_option('admin_email'));

	if(get_option($app_abbr.'_nu_email_body') == false) update_option($app_abbr.'_nu_email_body', '
Hi %username%,

Welcome to %blogname%!

Below you will find your username and password which allows you to login to your user account.

--------------------------
Username: %username%
Password: %password%

%loginurl%
--------------------------

If you have any questions, please just let us know.

Best regards,


Your %blogname% Team
%siteurl%
');

	if(get_option($app_abbr.'_job_alerts_from_name') == false) update_option($app_abbr.'_job_alerts_from_name', wp_specialchars_decode(get_option('blogname'), ENT_QUOTES));
	if(get_option($app_abbr.'_job_alerts_from_email') == false) update_option($app_abbr.'_job_alerts_from_email', get_option('admin_email'));
	
	if(get_option($app_abbr.'_job_alerts_email_body') == false) update_option($app_abbr.'_job_alerts_email_body', "

%joblist%

You're receiving this email because you're subscribed to %blogname% job alerts. To unsubscribe please change the alert settings on your dashboard.

Best regards,
Your %blogname% Team
%siteurl%

");	

if(get_option($app_abbr.'_job_alerts_job_body') == false) update_option($app_abbr.'_job_alerts_job_body', "

%jobtitle%
by: %author% @ %jobtime%
Job Type: %jobtype% | Job Category: %jobcat% | Company: %company% | Location: %location%

%jobdetails%

%permalink%

<hr/>

");

}

