<?php
/**
*
* Here is where all the admin field data is stored
* All the data is stored in arrays and then looped though
* @author AppThemes
* @version 1.2
*
*
*
*/

function jr_legacy_option_charge_jobs() {
	global $wpdb;
	
	$active_job_packs = false;

	$single_pricing = get_option('jr_jobs_listing_cost');

	if ( ! $single_pricing ) {
		$active_job_packs = (bool) $wpdb->get_results("SHOW TABLES LIKE '$wpdb->jr_job_packs' ") && (bool) $wpdb->get_var( "SELECT count(1) FROM $wpdb->jr_job_packs" );
	}
	return ( (bool)$active_job_packs || (bool)$single_pricing  ? 'yes' : 'no' );
}

global $options_settings, $options_pricing, $options_feeds, $options_emails,$options_alerts, $options_advertisments, $options_integration, $app_abbr;

$options_settings = array(

	array( 'type' => 'tab', 'tabname' => __('General', APP_TD) ),

	array( 'name' => __('Site Configuration', APP_TD), 'type' => 'title', 'desc' => '', 'id' => ''),

	array(  
		'name' 		=> __('Color Scheme', APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Select the color scheme you would like to use.',APP_TD),
		'id' 		=> $app_abbr.'_child_theme',
		'css' 		=> 'min-width:230px;',
		'std' 		=> 'style-default.css',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min'		=> '',
		'type' 		=> 'select',
		'options' 	=> jr_settings_theme_styles(),
	),
	
	array(  
		'name' 		=> __('Enable Logo',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('If you do not have a logo to use, select no and this will display the title and description of your web site instead.',APP_TD),
		'id' 		=> $app_abbr.'_use_logo',
		'css' 		=> 'min-width:100px;',
		'std' 		=> '',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' 	=> array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(  
		'name' 		=> __('Web Site Logo',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Paste the URL of your web site logo image here. It will replace the default JobRoller header logo.(i.e. http://www.yoursite.com/logo.jpg)',APP_TD),
		'id' 		=> $app_abbr.'_logo_url',
		'css' 		=> 'min-width:398px;',
		'vis' 		=> '',
		'req' 		=> '',
		'min' 		=> '',
		'type' 		=> 'upload',
		'std' 		=> ''
	),
	

	array(  
		'name' 		=> __('Disable Blog',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Turn this on to hide the blog pages.',APP_TD),
		'id' 		=> $app_abbr.'_disable_blog',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'no',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' 	=> array( 
			'no'  => __('No', APP_TD),
			'yes' => __('Yes', APP_TD)
		)
	),

	array(  
		'name' 		=> __('Feedburner URL',APP_TD),
		'desc' 		=> sprintf( '%s' . __("Sign up for a free <a target='_new' href='%s'>Feedburner account</a>.",APP_TD), '<div class="feedburnerico"></div>', 'http://feedburner.google.com' ),
		'tip' 		=> __('Paste your Feedburner address here. It will automatically redirect your default RSS feed to Feedburner. You must have a Google Feedburner account setup first.',APP_TD),
		'id' 		=> $app_abbr.'_feedburner_url',
		'css' 		=> 'min-width:500px;',
		'vis' 		=> '',
		'req' 		=> '',
		'min' 		=> '',
		'type' 		=> 'text',
		'std' 		=> ''
	),

	array(  
		'name' 		=> __('Twitter ID',APP_TD),
		'desc' 		=> sprintf( '%s' . __("Sign up for a free <a target='_new' href='%s'>Twitter account</a>.",APP_TD), '<div class="twitterico"></div>', 'http://twitter.com' ),
		'tip' 		=> __('Paste your Twitter ID here. It will be used in the Twitter sidebar widget. You must have a Twitter account setup first.',APP_TD),
		'id' 		=> $app_abbr.'_twitter_id',
		'css' 		=> 'min-width:500px;',
		'vis' 		=> '',
		'req' 		=> '',
		'min' 		=> '',
		'type' 		=> 'text',
		'std' 		=> ''
	),

	array(  
		'name' 		=> __('Facebook Page ID',APP_TD),
		'desc' 		=> sprintf( '%s' . __("Sign up for a free <a target='_new' href='%s'>Facebook account</a>.",APP_TD), '<div class="facebookico"></div>', 'http://www.facebook.com' ),
		'tip' 		=> __('Paste your Facebook Page ID here. It will be used in the Facebook Like Box sidebar widget. You must have a Facebook account and page setup first.',APP_TD),
		'id' 		=> $app_abbr.'_facebook_id',
		'css' 		=> 'min-width:500px;',
		'vis' 		=> '',
		'req'		=> '',
		'min' 		=> '',
		'type' 		=> 'text',
		'std' 		=> ''
	),
	
	array(  
		'name' 		=> __('ShareThis ID',APP_TD),
		'desc' 		=> sprintf( '%s' . __("Sign up for a free <a target='_new' href='%s'>ShareThis account</a>.",APP_TD), '<div class="sharethisico"></div>', 'http://sharethis.com' ),
		'tip' 		=> __('Paste your ShareThis publisher ID here. It will show the ShareThis buttons on the blog post and job listings. You must have a ShareThis account and page setup first.',APP_TD),
		'id' 		=> $app_abbr.'_sharethis_id',
		'css' 		=> 'min-width:500px;',
		'vis' 		=> '',
		'req' 		=> '',
		'min'		=> '',
		'type' 		=> 'text',
		'std' 		=> ''
	),

	array(  
		'name' 		=> __('Tracking Code',APP_TD),
		'desc' 		=> sprintf('%s' . __("Sign up for a free <a target='_new' href='%s'>Google Analytics account</a>.",APP_TD), '<div class="googleico"></div>', 'http://www.google.com/analytics/' ),
		'tip' 		=> __('Paste your analytics tracking code here. Google Analytics is free and the most popular but you can use other providers as well.',APP_TD),
		'id' 		=> $app_abbr.'_google_analytics',
		'css' 		=> 'width:500px;height:100px;',
		'vis' 		=> '',
		'req' 		=> '',
		'min' 		=> '',
		'type' 		=> 'textarea',
		'std' 		=> ''
	),

	array(	'name' => __('Google Maps Settings', APP_TD), 'type' => 'title', 'id' => '' ),

	array(  
		'name' 		=> __('Google Maps Language',APP_TD),
		'desc' 		=> sprintf( __("Find the list of supported language codes <a target='_new' href='%s'>here</a>.",APP_TD), 'http://spreadsheets.google.com/pub?key=p9pdwsai2hDMsLkXsoM05KQ&gid=1' ),
		'tip' 		=> __('The Google Maps API uses the browsers language setting when displaying textual info on the map. In most cases, this is preferable and you should not need to override this setting. However, if you wish to change the Maps API to ignore the browsers language setting and force it to display info in a particular language, enter your two character region code here (i.e. Japanese is ja).',APP_TD),
		'id' 		=> $app_abbr.'_gmaps_lang',
		'css' 		=> 'width:50px;',
		'vis' 		=> '',
		'req' 		=> '',
		'min' 		=> '',
		'type' 		=> 'text',
		'std' 		=> 'en'
	),

	array(  
		'name' 		=> __('Google Maps Region Biasing',APP_TD),
		'desc' 		=> sprintf( __("Find your two-letter ccTLD region code <a target='_new' href='%s'>here</a>.",APP_TD), 'http://en.wikipedia.org/wiki/CcTLD' ),
		'tip' 		=> __("Enter your country's two-letter region code here to properly display map locations. (i.e. Someone enters the location &quot;Toledo&quot;, it's based off the default region (US) and will display &quot;Toledo, Ohio&quot;. With the region code set to &quot;ES&quot; (Spain), the results will show &quot;Toledo, Spain.&quot;)",APP_TD),
		'id' 		=> $app_abbr.'_gmaps_region',
		'css' 		=> 'width:50px;',
		'vis' 		=> '',
		'req' 		=> '',
		'min' 		=> '',
		'type' 		=> 'text',
		'std' 		=> 'US'
	),

        array(  'name'          => __('Distance Unit',APP_TD),
                'desc'          => '',
                'tip'           => __('Defines the radius unit for search.',APP_TD),
                'id'            => $app_abbr.'_distance_unit',
                'css'           => 'width:100px;',
                'std'           => 'mi',
                'vis'           => '',
                'req'           => '',
                'js'            => '',
                'min'           => '',
                'type'          => 'select',
                'options'       => array(  
					'mi' => __( 'Miles', APP_TD ),
                    'km'  => __( 'Kilometers', APP_TD )
				),
		),

	array( 'name' => __('Registration Options', APP_TD), 'type' => 'title', 'desc' 		=> '' ),

	array(  
		'name' => __('Enable password fields on registration form',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Turning this off will send the user a password instead of letting them set it.',APP_TD),
		'id' 		=> $app_abbr.'_allow_registration_password',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'yes',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(  
		'name' 		=> __('Enable Terms & Conditions',APP_TD),
		'desc' 		=> sprintf ( __('Edit the <a href="%s">terms page</a> and add your terms & conditions; this will enable a checkbox on the registration page to confirm that the user accepts your terms and conditions.', APP_TD ), 'post.php?action=edit&post=' . JR_Terms_Conditions_Page::get_id() ),
		'tip' 		=> '',
		'id' 		=> $app_abbr.'_enable_terms_conditions',
		'css' 		=> 'min-width:100px;',
		'vis' 		=> '',
		'req' 		=> '',
		'min' 		=> '',
		'std' 		=> get_option( 'jr_terms_page_id' ) ? 'yes' : 'no',
		'type' 		=> 'select',
		'options' => array(
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array( 'name' => __('General Options', APP_TD), 'type' => 'title', 'desc' 		=> '' ),

	array(  
		'name' => __('Show Sidebar',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Turning off the sidebar will make the main content area wider and move the submit button for all main pages.',APP_TD),
		'id' 		=> $app_abbr.'_show_sidebar',
		'css' 		=> 'min-width:100px;',
		'std' 		=> '',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),
	
	array(  
		'name' => __('Show Search Bar',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Toggle the search bar on/off with this option.',APP_TD),
		'id' 		=> $app_abbr.'_show_searchbar',
		'css' 		=> 'min-width:100px;',
		'std' 		=> '',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(  
		'name' => __('Show Filter Bar',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Toggle the filter bar on/off with this option (shows checkboxes with Full-Time, Part-Time, etc.',APP_TD),
		'id' 		=> $app_abbr.'_show_filterbar',
		'css' 		=> 'min-width:100px;',
		'std' 		=> '',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),
	
	array(  
		'name' => __('Show Empty Categories?',APP_TD),
		'desc' 		=> __('By default, empty categories or job types are not visible and cannot be filtered by users, using the category and job type filter widget. If you are pulling jobs from external sources, this option will enable users to filter jobs from any category or job type.', APP_TD),
		'tip' 		=> __('This option should only be enabled if you pull jobs from external sources (Indeed, etc...). It may show empty listings otherwise.',APP_TD),
		'id' 		=> $app_abbr.'_show_empty_categories',
		'css' 		=> 'min-width:100px;',
		'std' 		=> '1',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'0' => __('Yes', APP_TD),
			'1'  => __('No', APP_TD),
		)
	),	

	array(  
		'name' => __('"Submit" Button Text',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('This text will appear below the Post a Job button. Leave it blank to automatically display pricing (if listings are paid).',APP_TD),
		'id' 		=> $app_abbr.'_jobs_submit_text',
		'css' 		=> 'width:500px;height:100px;',
		'vis' 		=> '',
		'req' 		=> '',
		'min' 		=> '',
		'type' 		=> 'textarea',
		'std' 		=> ''
	),
	
	array( 'type' => 'tabend'),
	
	array( 'type' => 'tab', 'tabname' => __('Jobs', APP_TD) ),

	array( 'name' => __('Job Options', APP_TD), 'type' => 'title', 'desc' 		=> '' ),

	array(
		'name' 		=> __( 'Default Expiration Days', APP_TD ),
		'desc' 		=> __( 'Default number of days until a job offer expires.', APP_TD ),
		'tip' 		=> __( 'Enter the default number of days until a job offer expires. When charging for job listings this duration will be ignored in favor of the job duration set on each price plan.', APP_TD ),
		'id' 		=> $app_abbr.'_jobs_default_expires',
		'css' 		=> 'width:50px;',
		'vis' 		=> '',
		'req' 		=> '',
		'min' 		=> '',
		'type' 		=> 'text',
		'std' 		=> '30'
	),

	array(  
		'name' => __('Charge for Job Listings',APP_TD),
		'desc' 		=> sprintf( __( 'Do you want to charge for creating a job listing on your site? You can manage your <a href="%s">Payments Settings</a> in the Payments Menu.', APP_TD ), 'admin.php?page=app-payments-settings'),
		'tip' 		=> __('Choose if you want to charge for jobs.',APP_TD),
		'id' 		=> $app_abbr.'_jobs_charge',
		'css' 		=> 'min-width:100px;',
		'std' 		=> jr_legacy_option_charge_jobs(),
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(  
		'name' => __('Allow Job Relisting',APP_TD),
		'desc' 		=> sprintf( __( 'If enabled, you can set a relisting fee on each price plan. You can manage <a href="%s">Plans</a> on the Payments Menu', APP_TD ), 'edit.php?post_type=' . APPTHEMES_PRICE_PLAN_PTYPE ),
		'tip' 		=> __('This enables an option for your customers to relist their job posting when it has expired.',APP_TD),
		'id' 		=> $app_abbr.'_allow_relist',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'yes',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD),
		)
	),

	array(  
		'name' => __('Allow Job Editing',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('This options allows you to control if job listings can be edited by the user.',APP_TD),
		'id' 		=> $app_abbr.'_allow_editing',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'yes',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(  
		'name' => __('Job Category Required',APP_TD),
		'tip' 		=> __('When submitting a job, is job category required? Make sure you have at least one job category before enabling this option. 
							If you charge for jobs and leave the category as optional, users that do not select a category will be presented with job plans from any job category. (Recommended)',APP_TD),
		'desc'		=> '',
		'id' 		=> $app_abbr.'_submit_cat_required',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'no',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(
		'name' => __('Job Category Editable',APP_TD),
		'tip' 		=> __('If you have mutliple job categories assigned to different price plans, it is recommended to disable this option. It ensures that users can\'t change job categories after purchasing a plan from a different category.',APP_TD),
		'desc'		=> '',
		'id' 		=> $app_abbr.'_submit_cat_editable',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'yes',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array( 'name' => __('Moderation', APP_TD), 'type' => 'title', 'desc' => '' ),

	array(  
		'name' => __('Moderate Job Listings',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('This options allows you to control if new free job listings should be manually approved before they go live. Note: paid jobs will automatically be published regardless of this setting.',APP_TD),
		'id' 		=> $app_abbr.'_jobs_require_moderation',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'yes',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(  
		'name' => __('Edited Job Requires Approval',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('This options allows you to define whether or not you want to moderate edited jobs. The job will stay \'pending\' and admin will be notified via email.',APP_TD),
		'id' 		=> $app_abbr.'_editing_needs_approval',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'yes',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array( 'name' => __('Appearance', APP_TD), 'type' => 'title', 'desc' => '' ),

	array(  
		'name' => __('Show Page Views Counter',APP_TD),
		'desc'		=> '',
		'tip'		=> __("This will show a 'total views' and 'today's views' at the bottom of each job listing and blog post.",APP_TD),
		'id'		=> $app_abbr.'_ad_stats_all',
		'css'		=> 'min-width:100px;',
		'std'		=> '',
		'vis'		=> '',
		'req'		=> '',
		'js'		=> '',
		'min'		=> '',
		'type'		=> 'select',
		'options'	=> array(
			'yes' => __('Yes', APP_TD),
			 'no'  => __('No', APP_TD)
		)
	),

	array(  
		'name' => __('Display "How to Apply" Field?',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('When submitting a job should the how to apply field be visible?',APP_TD),
		'id' 		=> $app_abbr.'_submit_how_to_apply_display',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'yes',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(  
		'name' => __('Enable Job Salary Field?',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Enable or disable the Salary field in the job submission form.',APP_TD),
		'id' 		=> $app_abbr.'_enable_salary_field',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'yes',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(  
		'name' => __('Allow HTML in Job Descriptions?',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('When submitting a job, is HTML allowed? Select no to have it automatically stripped out.',APP_TD),
		'id' 		=> $app_abbr.'_html_allowed',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'yes',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(  
		'name' => __('Expired Jobs Action',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Choose what to do with expired jobs. Selecting \'display message\' will keep the job visible and display a \'job expired\' notice on it. Selecting \'hide\' will hide all expired jobs.',APP_TD),
		'id' 		=> $app_abbr.'_expired_action',
		'css' 		=> 'min-width:150px;',
		'std' 		=> 'display_message',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'display_message' => __('Display Message', APP_TD),
			'hide'  => __('Hide', APP_TD)
		)
	),
	
	array( 'name' => __('Job Listings', APP_TD), 'type' => 'title', 'desc' => '' ),

	array(  
		'name' => __('Regular Jobs per Page',APP_TD),
		'desc'		=> '',
		'tip'		=> __( 'How many Regular (Non-Featured) jobs per page do you want shown? Regular jobs are displayed below Featured job listings, if any, on a list page.',APP_TD),
		'id'		=> $app_abbr.'_jobs_per_page',
		'css' 		=> 'width:50px;',
		'std'		=>  get_option('posts_per_page'),
		'vis'		=> '',
		'req'		=> '',
		'js'		=> '',
		'min'		=> '',
		'type'		=> 'text',
	),

	array(  
		'name' => __('Featured Jobs per Page',APP_TD),
		'desc'		=> __( 'How many Featured jobs per page do you want shown? Leave blank to display unlimited featured jobs.', APP_TD ),
		'tip'		=> __( 'If there are Featured jobs, they are displayed above the Regular jobs on a list page. Note that this option only affects the Featured jobs displayed over the job listings. 
							Setting a limit may hide featured jobs from your visitors.', APP_TD ),
		'id'		=> $app_abbr.'_featured_jobs_per_page',
		'css' 		=> 'width:50px;',
		'std'		=> '',
		'vis'		=> '',
		'req'		=> '',
		'js'		=> '',
		'min'		=> '',
		'type'		=> 'text',
	),

	array(  
		'name' => __('Featured Jobs Sort Method',APP_TD),
		'desc'		=> '',
		'tip'		=> __( 'How do you want to sort the Featured jobs that are displayed?',APP_TD),
		'id'		=> $app_abbr.'_featured_jobs_sort',
		'css' 		=> 'min-width:100px;',
		'std'		=> 'newest',
		'vis'		=> '',
		'req'		=> '',
		'js'		=> '',
		'min'		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'newest' => __('Newest First', APP_TD),
			'oldest'  => __('Oldest First', APP_TD),
			'random'  => __('Random', APP_TD)
		)
	),

	array( 'type' => 'tabend'),
	
	array( 'type' => 'tab', 'tabname' => __('Resumes', APP_TD) ),

	array( 'name' => __('Job Seeker Options', APP_TD), 'type' => 'title', 'desc' 		=> '' ),
	
	array(  
		'name' => __('Enable Job Seeker Registration',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Allows Job Seekers to signup. Job Seekers cannot post jobs; they can only find jobs and submit their resume.',APP_TD),
		'id' 		=> $app_abbr.'_allow_job_seekers',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'yes',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(  
		'name' => __('Enable Recruiters Registration',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Allows recruiters to signup. Recruiters can post posts and, if you don\'t have resumes subscriptions enabled, can also view resumes, unlike job listers who can only post jobs.
						If your site requires a subscription to view/browse resumes, access will always depend on your visibility settings.',APP_TD),
		'id' 		=> $app_abbr.'_allow_recruiters',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'no',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(  
		'name' => __('"My Profile" Button Text',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('This text will appear below the My Profile button.',APP_TD),
		'id' 		=> $app_abbr.'_my_profile_button_text',
		'css' 		=> 'width:500px;height:100px;',
		'vis' 		=> '',
		'req' 		=> '',
		'min' 		=> '',
		'type' 		=> 'textarea',
		'std' 		=> 'Submit your Resume, update your profile, and allow employers to find <em>you</em>!'
	),
	
	array(  
		'name' => __('"Submit your resume" Button Text',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('This text will appear below the Submit your resume button when browsing resumes.',APP_TD),
		'id' 		=> $app_abbr.'_submit_resume_button_text',
		'css' 		=> 'width:500px;height:100px;',
		'vis' 		=> '',
		'req' 		=> '',
		'min' 		=> '',
		'type' 		=> 'textarea',
		'std' 		=> 'Register as a Job Seeker to submit your Resume.'
	),
	
	array( 'name' => __('Resume Options', APP_TD), 'type' => 'title', 'desc' 		=> __('Control who can view resumes', APP_TD) ),
	
	array(  
		'name' => __('Resume Listings Visibility',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Lets you define who can browse through submitted resumes.',APP_TD),
		'id' 		=> $app_abbr.'_resume_listing_visibility',
		'css' 		=> 'min-width:250px;',
		'std' 		=> 'listers',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'public' 		 	=> __('Public', APP_TD),
			'members_listers'	=> __('Recruiters / Job Listers', APP_TD),
			'members' 			=> __('All Members (including Job Seekers)', APP_TD),
			'listers' 			=> __('Job listers', APP_TD),
			'recruiters'  		=> __('Recruiters', APP_TD)
		)
	),
	
	array(  
		'name' => __('Resume Visibility',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Lets you define who can view submitted resumes.',APP_TD),
		'id' 		=> $app_abbr.'_resume_visibility',
		'css' 		=> 'min-width:250px;',
		'std' 		=> 'listers',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'public' 		 	=> __('Public', APP_TD),
			'members_listers'	=> __('Recruiters / Job Listers', APP_TD),
			'members' 			=> __('All Members (including Job Seekers)', APP_TD),
			'listers' 			=> __('Job listers', APP_TD),
			'recruiters'  		=> __('Recruiters', APP_TD)
		)
	),

	array(  
		'name' => __('Resumes per Page',APP_TD),
		'desc'		=> '',
		'tip'		=> __( 'How many Resumes per page do you want shown?.',APP_TD),
		'id'		=> $app_abbr.'_resumes_per_page',
		'css' 		=> 'width:50px;',
		'std'		=> 15,
		'vis'		=> '',
		'req'		=> '',
		'js'		=> '',
		'min'		=> '',
		'type'		=> 'text',
	),

	array(	'name' => __('Subscription Options', APP_TD), 'type' 		=> 'title','desc' 		=> __('Control subscriptions for resume access', APP_TD), 'id' 		=> '' ),
	
	array(  
		'name' => __('Require active subscription to view resumes?',APP_TD),
		'desc' 		=>  sprintf( __( 'Enabling this option will block access to the resume section if the user does not have a subscription. 
						   Access will still be determined by your visibility settings, e.g. if set to \'Recruiters\', only recruiters will be able to subscribe. To subscribe the user must be logged in.
						   <br/><br/>
						   <strong>Note:</strong> If you set the visibility settings to \'Public\' this option will be ignored, and any user role, including visitors, will be able to Browse and/or View Resumes without a subscription.
						   <br/><br/>
						   You can create Resumes Plans that Employers/Recruiters or even Job Seekers can subscribe to, on the <a href="%s">Resumes Plans Page</a> in the Payments Menu.', APP_TD ), 
						   'edit.php?post_type=' . APPTHEMES_RESUMES_PLAN_PTYPE ),
		'tip' 		=> '',
		'id' 		=> $app_abbr.'_resume_require_subscription',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'no',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'no'  => __('No', APP_TD),
			'yes' => __('Yes', APP_TD),
		)
	),
	
	array(  
		'name' => __('Subscription notice', APP_TD),
		'desc' 		=> __('Notice to display above the subscription button.',APP_TD),
		'tip' 		=> '',
		'id' 		=> $app_abbr.'_resume_subscription_notice',
		'css' 		=> 'width:500px;height:50px;',
		'vis' 		=> '',
		'req' 		=> '',
		'min' 		=> '',
		'type' 		=> 'textarea',
		'std' 		=> 'Sorry, you do not have access to resumes. To access and/or view our resume database please subscribe.'
	),

	array( 'name' => __('Anti-Spam', APP_TD), 'type' => 'title', 'desc' 		=> __('Secure resumes contact details', APP_TD) ),
	
	array(  
		'name' => __('Enable Contact Form',APP_TD),
		'desc' 		=> __( 'Choose whether you want show a contact form instead of the resume author contact details (email, mobile and telephone).', APP_TD ),
		'tip' 		=> __( 'To avoid spammers you can hide the resumes contact details and let employers contact resume authors using a popup contact form.', APP_TD ),
		'id' 		=> $app_abbr.'_resume_show_contact_form',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'no',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'no' => __('No', APP_TD),
			'yes'  => __('Yes', APP_TD),
		)
	),
	
	array( 'type' => 'tabend'),
	
	array( 'type' => 'tab', 'tabname' => __('Security', APP_TD) ),

	array(	'name' => __('Security Settings', APP_TD), 'type' 		=> 'title', 'desc' 		=> '' ),

	array(  
		'name' => __('Back Office Access',APP_TD),
		'desc' 		=> sprintf( __("View the WordPress <a target='_new' href='%s'>Roles and Capabilities</a> for more information.",APP_TD), 'http://codex.wordpress.org/Roles_and_Capabilities' ),
		'tip' 		=> __('Allows you to restrict access to the WordPress Back Office (wp-admin) by specific role. Keeping this set to admins only is recommended. Select Disable if you have problems with this feature.',APP_TD),
		'id' 		=> $app_abbr.'_admin_security',
		'css' 		=> 'min-width:100px;',
		'std' 		=> '',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'manage_options' => __('Admins Only', APP_TD),
			'edit_others_posts' => __('Admins, Editors', APP_TD),
			'publish_posts' => __('Admins, Editors, Authors', APP_TD),
			'edit_posts' => __('Admins, Editors, Authors, Contributors', APP_TD),
			'read' => __('All Access', APP_TD),
			'disable' => __('Disable', APP_TD)
		)
	),

	array( 'name' => __('reCaptcha', APP_TD), 'type' 		=> 'title', 'desc' 		=> '' ),

	array(  
		'name' => __('Enable on Registration Form', APP_TD),
		'desc' 		=> sprintf(__("reCaptcha is a free anti-spam service provided by Google. Learn more about <a target='_new' href='%s'>reCaptcha</a>.", APP_TD), 'http://code.google.com/apis/recaptcha/'),
		'tip' 		=> __('Set this option to yes to enable the reCaptcha service that will protect your site against spam registrations. It will show a verification box on your registration page that requires a human to read and enter the words.',APP_TD),
		'id' 		=> $app_abbr.'_captcha_enable',
		'css' 		=> 'width:100px;',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'std' 		=> 'no',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(  
		'name' => __('Enable on Contact Forms', APP_TD),
		'desc' 		=> __( 'Enable reCaptcha on all contact forms.', APP_TD ),
		'tip' 		=> __('Set this option to yes to enable the reCaptcha service that will protect your site against spam contacts. It will show a verification box on your contact form page and on the online resumes contact form. It requires a human to read and enter the words.',APP_TD),
		'id' 		=> $app_abbr.'_captcha_contact_forms_enable',
		'css' 		=> 'width:100px;',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'std' 		=> 'yes',
		'type' 		=> 'select',
		'options' => array(  
			'visitors' => __('Visitors', APP_TD),
			'all'  => __('All', APP_TD),
			'no' => __('No', APP_TD)
		)
	),

	array(  
		'name' => __( 'Enable on Apply Online Form', APP_TD ),
		'desc' 		=> __( 'Enable reCaptcha on \'Apply Online\' form.', APP_TD ),
		'tip' 		=> __( 'Set this option to yes to enable the reCaptcha service that will protect your site against spam applications. It will show a verification box on the applications form that requires a human to read and enter the words.', APP_TD ),
		'id' 		=> $app_abbr.'_captcha_application_form_enable',
		'css' 		=> 'width:100px;',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'std' 		=> 'yes',
		'type' 		=> 'select',
		'options' => array(  
			'visitors' => __( 'Visitors', APP_TD ),
			'all'  => __('All', APP_TD ),
			'no' => __( 'No', APP_TD )
		)
	),

	array( 'name' => __('reCaptcha Settings', APP_TD), 'type' 		=> 'title', 'desc' 		=> '' ),

	array(  
		'name' => __('reCaptcha Public Key', APP_TD),
		'desc' 		=> sprintf( '%s' . __("Sign up for a free <a target='_new' href='%s'>Google reCaptcha</a> account.",APP_TD), '<div class="captchaico"></div>', 'https://www.google.com/recaptcha/admin/create' ),
		'tip' 		=> __('Enter your public key here to enable an anti-spam service on your new user registration page (requires a free Google reCaptcha account). Leave it blank if you do not wish to use this anti-spam feature.',APP_TD),
		'id' 		=> $app_abbr.'_captcha_public_key',
		'css' 		=> 'min-width:500px;',
		'vis' 		=> '',
		'req' 		=> '',
		'min' 		=> '',
		'type' 		=> 'text',
		'std' 		=> ''
	),
	
	array(  
		'name' => __('reCaptcha Private Key', APP_TD),
		'desc' 		=> sprintf( '%s' . __("Sign up for a free <a target='_new' href='%s'>Google reCaptcha</a> account.",APP_TD), '<div class="captchaico"></div>', 'https://www.google.com/recaptcha/admin/create' ),
		'tip' 		=> __('Enter your private key here to enable an anti-spam service on your new user registration page (requires a free Google reCaptcha account). Leave it blank if you do not wish to use this anti-spam feature.',APP_TD),
		'id' 		=> $app_abbr.'_captcha_private_key',
		'css' 		=> 'min-width:500px;',
		'vis' 		=> '',
		'req' 		=> '',
		'min' 		=> '',
		'type' 		=> 'text',
		'std' 		=> ''
	),

	array(  
		'name' => __('Choose Theme', APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Select the color scheme you wish to use for reCaptcha.', APP_TD),
		'id' 		=> $app_abbr.'_captcha_theme',
		'css' 		=> 'width:100px;',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'std' 		=> 'red',
		'type' 		=> 'select',
		'options' => array(  
			'red' => __('Red', APP_TD),
			'white' => __('White', APP_TD),
			'blackglass' => __('Black', APP_TD),
			'clean'  => __('Clean', APP_TD)
		)
	),

	array( 'name' => __('Anti-Spam Settings', APP_TD), 'type' 		=> 'title', 'desc' 		=> '' ),

	array(  
		'name' => __('Anti-Spam Question', APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Question asked before visitor can apply for a job.',APP_TD),
		'id' 		=> $app_abbr.'_antispam_question',
		'css' 		=> 'width:500px;',
		'vis' 		=> '',
		'type' 		=> 'text',
		'req' 		=> '',
		'min' 		=> '',
		'std' 		=> 'Is fire &ldquo;<em>hot</em>&rdquo; or &ldquo;<em>cold</em>&rdquo;?'
	),

	array(  
		'name' => __('Anti-Spam Answer', APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Enter the correct answer here.',APP_TD),
		'id' 		=> $app_abbr.'_antispam_answer',
		'css' 		=> 'width:50px;',
		'vis' 		=> '',
		'type' 		=> 'text',
		'req' 		=> '',
		'min' 		=> '',
		'std' 		=> 'hot'
	),

	array( 'type' => 'tabend' ),


	array( 'type' => 'tab', 'tabname' => __('Advertising', APP_TD) ),

	array(	'name' => __('Header banner (468x60)', APP_TD),
		'type' 		=> 'title',
		'desc' 		=> '',
		'id' 		=> ''
	),

	array(  
		'name' => __('Enable header banner spot?', APP_TD),
		'desc' 		=> __("Change this option to enable or disable the header banner spot.",APP_TD),
		'tip' 		=> __('This will replace the header navigation.',APP_TD),
		'id' 		=> $app_abbr.'_enable_header_banner',
		'css' 		=> 'width:100px;',
		'std' 		=> 'no',
		'js' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(  
		'name' => __('Banner Code', APP_TD),
		'desc' 		=> __('Image/Link HTML or JavaScript for the banner.',APP_TD),
		'tip' 		=> __('This can be what you like; javascript, an image and a link, text.',APP_TD),
		'id' 		=> $app_abbr.'_header_banner',
		'css' 		=> 'width:500px;height:150px;',
		'type' 		=> 'textarea',
		'req' 		=> '',
		'min' 		=> '',
		'std' 		=> '',
		'vis' 		=> ''
	),

	array(	'name' => __('Job Listing Banner (468x60)', APP_TD), 'type' 		=> 'title', 'desc' 		=> __( 'If you have the sidebar turned off you may fit in a 728x90 banner instead.', APP_TD ) ),

	array(  
		'name' => __('Enable job listing banner spot?', APP_TD),
		'desc' 		=> __("Change this option to enable or disable the job listing banner spot.",APP_TD),
		'tip' 		=> __('This banner appears in a job listing, usually between "Job description" and "How to Apply".',APP_TD),
		'id' 		=> $app_abbr.'_enable_listing_banner',
		'css' 		=> 'width:100px;',
		'std' 		=> 'no',
		'js' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(  
		'name' => __('Banner Code', APP_TD),
		'desc' 		=> __( 'Image/Link HTML or JavaScript for the banner.', APP_TD ),
		'tip' 		=> __('This can be what you like; javascript, an image and a link, text.',APP_TD),
		'id' 		=> $app_abbr.'_listing_banner',
		'css' 		=> 'width:500px;height:150px;',
		'type' 		=> 'textarea',
		'req' 		=> '',
		'min' 		=> '',
		'std' 		=> '',
		'vis' 		=> ''
	),

	array( 'type' => 'tabend'),


	array( 'type' => 'tab', 'tabname' => __('Advanced', APP_TD) ),

		array(	'name' => __('Advanced Options', APP_TD),
					'type' => 'title',
					'id' => ''),


				array(  'name' => __('Enable Debug Mode',APP_TD),
                        'desc' => '',
                        'tip' => __('This will print out the $wp_query->query_vars array at the top of your website. This should only be used for debugging.',APP_TD),
                        'id' => $app_abbr.'_debug_mode',
                        'css' => 'width:100px;',
                        'std' => 'no',
                        'vis' => '',
                        'req' => '',
                        'js' => '',
                        'min' => '',
                        'type' => 'select',
                        'options' => array(  'no'   => __('No', APP_TD),
                                             'yes'  => __('Yes', APP_TD))),

				array(  'name' => __('Enable Debug Log',APP_TD),
						'desc' => '',
						'tip' => __('Turn this on to log emails and transactions for debugging. Logs are stored in /themes/jobroller/log/. Delete them when you are finished since they contain info about jobs and transactions.',APP_TD),
						'id' => $app_abbr.'_enable_log',
						'css' => 'min-width:100px;',
						'std' => 'no',
						'vis' => '',
						'req' => '',
						'js' => '',
						'min' => '',
						'type' => 'select',
						'options' => array( 'no'  => __('No', APP_TD),
											'yes' => __('Yes', APP_TD))),

				array(  'name' => __('Use Google CDN jQuery',APP_TD),
                        'desc' => '',
                        'tip' => __("This will use Google's hosted jQuery files which are served from their global content delivery network. This will help your site load faster and save bandwidth.",APP_TD),
                        'id' => $app_abbr.'_google_jquery',
                        'css' => 'width:100px;',
                        'std' => 'no',
                        'vis' => '',
                        'req' => '',
                        'js' => '',
                        'min' => '',
                        'type' => 'select',
                        'options' => array(  'no'   => __('No', APP_TD),
                                             'yes'  => __('Yes', APP_TD))),

				array(  'name' => __('Disable WordPress Version Meta Tag',APP_TD),
                        'desc' => '',
                        'tip' => __("This will remove the WordPress generator meta tag in the source code of your site <code>< meta name='generator' content='WordPress 3.1' ></code>. It's an added security measure which prevents anyone from seeing what version of WordPress you are using. It also helps to deter hackers from taking advantage of vulnerabilities sometimes present in WordPress. (Yes is recommended)",APP_TD),
                        'id' => $app_abbr.'_remove_wp_generator',
                        'css' => 'width:100px;',
                        'std' => 'no',
                        'vis' => '',
                        'req' => '',
                        'js' => '',
                        'min' => '',
                        'type' => 'select',
                        'options' => array(  'no'   => __('No', APP_TD),
                                             'yes'  => __('Yes', APP_TD))),

				array(  'name' => __('Disable WordPress User Toolbar',APP_TD),
                        'desc' => '',
                        'tip' => __("This will remove the WordPress user toolbar at the top of your web site which is displayed for all logged in users. This feature was added in WordPress 3.1.",APP_TD),
                        'id' => $app_abbr.'_remove_admin_bar',
                        'css' => 'width:100px;',
                        'std' => 'no',
                        'vis' => '',
                        'req' => '',
                        'js' => '',
                        'min' => '',
                        'type' => 'select',
                        'options' => array(  'no'   => __('No', APP_TD),
                                             'yes'  => __('Yes', APP_TD))),

		array( 'name' => __('Custom Post Type & Taxonomy URLs', APP_TD),
                'type' => 'title',
                'id' => ''),

				array(  'name' => __('Job Listing Base URL', APP_TD),
                        'desc'=> sprintf( __("IMPORTANT: You must <a target='_blank' href='%s'>re-save your permalinks</a> for this change to take effect.",APP_TD), 'options-permalink.php' ),
                        'tip' => __('This controls the base name of your job listing urls. The default is jobs and will look like this: http://www.yoursite.com/jobs/ad-title-here/. Do not include any slashes. This should only be alpha and/or numeric values. You should not change this value once you have launched your site otherwise you risk breaking urls of other sites pointing to yours, etc.',APP_TD),
                        'id' => $app_abbr.'_job_permalink',
                        'css' => 'width:250px;',
                        'type' => 'text',
                        'req' => '',
                        'min' => '',
                        'std' => 'jobs',
                        'vis' => '',
                        'visid' => ''),

				array(  'name' => __('Job Category Base URL', APP_TD),
                        'desc'=> sprintf( __("IMPORTANT: You must <a target='_blank' href='%s'>re-save your permalinks</a> for this change to take effect.",APP_TD), 'options-permalink.php' ),
                        'tip' => __('This controls the base name of your job category urls. The default is job-category and will look like this: http://www.yoursite.com/job-category/category-name/. Do not include any slashes. This should only be alpha and/or numeric values. You should not change this value once you have launched your site otherwise you risk breaking urls of other sites pointing to yours, etc.',APP_TD),
                        'id' => $app_abbr.'_job_cat_tax_permalink',
                        'css' => 'width:250px;',
                        'type' => 'text',
                        'req' => '',
                        'min' => '',
                        'std' => 'job-category',
                        'vis' => '',
                        'visid' => ''),

				array(  'name' => __('Job Type Base URL', APP_TD),
                        'desc'=> sprintf( __("IMPORTANT: You must <a target='_blank' href='%s'>re-save your permalinks</a> for this change to take effect.",APP_TD), 'options-permalink.php' ),
                        'tip' => __('This controls the base name of your job type urls. The default is job-type and will look like this: http://www.yoursite.com/job-type/type-name/. Do not include any slashes. This should only be alpha and/or numeric values and different from the reserved word, \'type\'. You should not change this value once you have launched your site otherwise you risk breaking urls of other sites pointing to yours, etc.',APP_TD),
                        'id' => $app_abbr.'_job_type_tax_permalink',
                        'css' => 'width:250px;',
                        'type' => 'text',
                        'req' => '',
                        'min' => '',
                        'std' => 'job-type',
                        'vis' => '',
                        'visid' => ''),

				array(  'name' => __('Job Tag Base URL', APP_TD),
                        'desc'=> sprintf( __("IMPORTANT: You must <a target='_blank' href='%s'>re-save your permalinks</a> for this change to take effect.",APP_TD), 'options-permalink.php' ),
                        'tip' => __('This controls the base name of your job tag urls. The default is job-tag and will look like this: http://www.yoursite.com/job-tag/tag-name/. Do not include any slashes. This should only be alpha and/or numeric values. You should not change this value once you have launched your site otherwise you risk breaking urls of other sites pointing to yours, etc.',APP_TD),
                        'id' => $app_abbr.'_job_tag_tax_permalink',
                        'css' => 'width:250px;',
                        'type' => 'text',
                        'req' => '',
                        'min' => '',
                        'std' => 'job-tag',
                        'vis' => '',
                        'visid' => ''),

				array(  'name' => __('Job Salary Base URL', APP_TD),
                        'desc'=> sprintf( __("IMPORTANT: You must <a target='_blank' href='%s'>re-save your permalinks</a> for this change to take effect.",APP_TD), 'options-permalink.php' ),
                        'tip' => __('This controls the base name of your salary urls. The default is salary and will look like this: http://www.yoursite.com/salary/salary-value/. Do not include any slashes. This should only be alpha and/or numeric values. You should not change this value once you have launched your site otherwise you risk breaking urls of other sites pointing to yours, etc.',APP_TD),
                        'id' => $app_abbr.'_job_salary_tax_permalink',
                        'css' => 'width:250px;',
                        'type' => 'text',
                        'req' => '',
                        'min' => '',
                        'std' => 'salary',
                        'vis' => '',
                        'visid' => ''),
                        
               array(  'name' => __('Resume Base URL', APP_TD),
                        'desc'=> sprintf( __("IMPORTANT: You must <a target='_blank' href='%s'>re-save your permalinks</a> for this change to take effect.",APP_TD), 'options-permalink.php' ),
                        'tip' => __('This controls the base name of your resume urls. The default is resumes and will look like this: http://www.yoursite.com/resumes/resume-title-here/. Do not include any slashes. This should only be alpha and/or numeric values. You should not change this value once you have launched your site otherwise you risk breaking urls of other sites pointing to yours, etc.',APP_TD),
                        'id' => $app_abbr.'_resume_permalink',
                        'css' => 'width:250px;',
                        'type' => 'text',
                        'req' => '',
                        'min' => '',
                        'std' => 'resumes',
                        'vis' => '',
                        'visid' => ''),

	array( 'type' => 'tabend'),


);


$options_emails = array (

 	array( 'type' => 'tab', 'tabname' => __('General', APP_TD) ),

	array(	'name' => __('Email Notifications', APP_TD), 'type' 		=> 'title', 'desc' 		=> '', 'id' 		=> '' ),

	array(
		'name' => __('New Job Email',APP_TD),
		'desc' 		=> sprintf(__("Emails will be sent to: %s. (<a target='_new' href='%s'>Change email address</a>)", APP_TD), get_option('admin_email'), 'options-general.php'),
		'tip' 		=> __('Send me an email once a new job has been submitted.',APP_TD),
		'id' 		=> $app_abbr.'_new_ad_email',
		'css' 		=> 'width:100px;',
		'std' 		=> 'yes',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(
		'name' => __('Job Approved Email',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Send the job owner an email once their job has been approved either by you manually or after payment has been made (post status changes from pending to published).',APP_TD),
		'id' 		=> $app_abbr.'_new_job_email_owner',
		'css' 		=> 'width:100px;',
		'std' 		=> 'yes',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(
		'name' => __('Enable Reminder Emails',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Send the job owner an email 5/1 days before their job expires, and another once their job has expired (post status changes from published to draft).',APP_TD),
		'id' 		=> $app_abbr.'_expired_job_email_owner',
		'css' 		=> 'width:100px;',
		'std' 		=> 'yes',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(
		'name' => __('BCC on all Apply Emails',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Enable this option to receive a copy of application emails.',APP_TD),
		'id' 		=> $app_abbr.'_bcc_apply_emails',
		'css' 		=> 'width:100px;',
		'std' 		=> 'yes',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),


	array( 'type' => 'tabend'),


	array( 'type' => 'tab', 'tabname' => __('New User Email', APP_TD) ),

	array(	'name' => __('New User Registration Email', APP_TD), 'type' 		=> 'title' ),
	
		array(  
			'name' => __('Enable Custom Email',APP_TD),
			'desc' 		=> '',
			'tip' 		=> __('Sends a custom new user notification email to your customers by using the fields you complete below. If this is set to &quot;No&quot;, the default WordPress new user notification email will be sent. This is useful for debugging if your custom emails are not being sent.',APP_TD),
			'id' 		=> $app_abbr.'_nu_custom_email',
			'css' 		=> 'width:100px;',
			'std' 		=> 'no',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'yes' => __('Yes', APP_TD),
				'no'  => __('No', APP_TD)
			)
		),

		array(  
			'name' => __('From Name',APP_TD),
			'desc' 		=> '',
			'tip' 		=> __('This is what your customers will see as the &quot;from&quot; when they receive the new user registration email. Use plain text only',APP_TD),
			'id' 		=> $app_abbr.'_nu_from_name',
			'css' 		=> 'width:250px;',
			'vis' 		=> '',
			'type' 		=> 'text',
			'req' 		=> '',
			'min' 		=> '',
			'std' 		=> ''
		),

		array(  
			'name' => __('From Email',APP_TD),
			'desc' 		=> '',
			'tip' 		=> __('This is what your customers will see as the &quot;from&quot; email address (also the reply to) when they receive the new user registration email. Use only a valid and existing email address with no html or variables.',APP_TD),
			'id' 		=> $app_abbr.'_nu_from_email',
			'css' 		=> 'width:250px;',
			'vis' 		=> '',
			'type' 		=> 'text',
			'req' 		=> '',
			'min' 		=> '',
			'std' 		=> ''
		),

		array(  
			'name' => __('Email Subject',APP_TD),
			'desc' 		=> '',
			'tip' 		=> __('This is the subject line your customers will see when they receive the new user registration email. Use text and variables only.',APP_TD),
			'id' 		=> $app_abbr.'_nu_email_subject',
			'css' 		=> 'width:400px;',
			'vis' 		=> '',
			'type' 		=> 'text',
			'req' 		=> '',
			'min' 		=> '',
			'std' 		=> __('Thank you for registering, %username%', APP_TD),
		),

		array(  
			'name' => __('Allow HTML in Body', APP_TD),
			'desc' 		=> '',
			'tip' 		=> __('This option allows you to use html markup in the email body below. It is recommended to keep it set to &quot;No&quot; to avoid problems with delivery. If you turn it on, make sure to test it and make sure the formatting looks ok and gets delivered properly.',APP_TD),
			'id' 		=> $app_abbr.'_nu_email_type',
			'css' 		=> 'width:100px;',
			'vis' 		=> '',
			'std' 		=> 'text/plain',
			'js' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'text/html'   => __('Yes', APP_TD),
				'text/plain'  => __('No', APP_TD)
			)
		),

		array(  
			'name' => __('Email Body',APP_TD),
			'desc' 		=> __('You may use the following variables within the email body and/or subject line.<br/><br/><strong>%username%</strong> - prints out the username<br/><strong>%useremail%</strong> - prints out the users email address<br/><strong>%password%</strong> - prints out the users text password<br/><strong>%siteurl%</strong> - prints out your website url<br/><strong>%blogname%</strong> - prints out your site name<br/><strong>%loginurl%</strong> - prints out your sites login url<br/><br/>Each variable MUST have the percentage signs wrapped around it with no spaces.<br/>Always test your new email after making any changes (register) to make sure it is working and formatted correctly. If you do not receive an email, chances are something is wrong with your email body.',APP_TD),
			'tip' 		=> __('Enter the text you would like your customers to see in the new user registration email. Make sure to always at least include the %username% and %password% variables otherwise they might forget later.',APP_TD),
			'id' 		=> $app_abbr.'_nu_email_body',
			'css' 		=> 'width:550px;height:250px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'textarea',
			'std' 		=> ''
		),

	array( 'type' => 'tabend'),


);

$options_alerts = array (

 	array( 'type' => 'tab', 'tabname' => __('General', APP_TD) ),

	array(	'name' => __('Job Alerts', APP_TD), 'type' 		=> 'title', 'desc' 		=> '', 'id' 		=> '' ),

	array(
		'name' => __('Enable Job Alerts Email',APP_TD),
		'desc' 		=> __('Job Seekers will be able to set job alerts based on specific criteria.',APP_TD),
		'tip' 		=> __('A new area will be available on the Job Seeker\'s dashboard where they can configure their alerts criteria.',APP_TD),
		'id' 		=> $app_abbr.'_job_alerts',
		'css' 		=> 'width:100px;',
		'std' 		=> 'no',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),

	array(  
		'name' => __('Batch Size',APP_TD),
		'desc' 		=> __('Set the maximum allowed emails to be sent at a given time. A value between 1 and 100 is recommended.', APP_TD),
		'tip' 		=> __('This is the maximum number of emails that will be sent at a given time.',APP_TD),
		'id' 		=> $app_abbr.'_job_alerts_batch_size',
		'css' 		=> 'width:50px;',
		'vis' 		=> '',
		'type' 		=> 'text',
		'req' 		=> '',
		'min' 		=> '',
		'std' 		=> '100'
	),

	array(  
		'name' => __('Job Limit',APP_TD),
		'desc' 		=> __('Set the maximum number of jobs that should be sent on each email.', APP_TD),
		'tip' 		=> __('Email alerts can contain a list of jobs or individual jobs.',APP_TD),
		'id' 		=> $app_abbr.'_job_alerts_jobs_limit',
		'css' 		=> 'width:50px;',
		'vis' 		=> '',
		'type' 		=> 'text',
		'req' 		=> '',
		'min' 		=> '',
		'std' 		=> '5'
	),

	array(
		'name' => __('Recurrence',APP_TD),
		'desc' 		=> __('Set how often you want to trigger the job alerts.
						   <br/><br/>Emails are sent in batches every <code>n</code> minutes. If the batch size is smaller then the total emails to be sent at a given time, the remaining emails will be included on the next batch.
						   <br/><br/>Example:
						   <br/><code>Batch size = 100</code> <code>Jobs Limit = 5</code> <code>Recurrence = Once Hourly</code>
						   <br/>Each hour, JobRoller will pick 5 new jobs and look for matching user alerts. It will then split the mailing list in chunks of 100 users that will receive the jobs list.
						    The remaining users will be included on the batch that will run one hour later.
						   <br/><br/><strong>Important:</strong> It\'s strongly recommended that you contact your host provider for more information related with mass emailing limitations.        	 
							',APP_TD),
		'tip' 		=> __('This value should be set depending on how much activity you have on your site. If you have many jobs being posted, you should check for updates more frequently (lower value).',APP_TD),
		'id' 		=> $app_abbr.'_job_alerts_cron',
		'css' 		=> 'width:200px;',
		'std' 		=> 'hourly',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type'		=> 'select',
		'options' => array(  
			'ten_minutes'    => __('Every Ten Minutes', APP_TD),
			'twenty_minutes' => __('Every Twenty Minutes', APP_TD),
			'thirty_minutes' => __('Every Thirty Minutes', APP_TD),
			'hourly' 		 => __('Once Hourly', APP_TD),
			'daily' 		 => __('Once Daily', APP_TD),
		)
	),

	array(	'name' => __('Job Alerts Feed', APP_TD), 'type' 		=> 'title', 'desc' 		=> '', 'id' 		=> '' ),

	array(
		'name' => __('Enable Job Alerts RSS Feed',APP_TD),
		'desc' 		=> sprintf( __('Job Seekers will have access to a unique feed URL representing their alert criteria. This feed can be used by job seekers to subscribe to emails alerts using a 3d party service like <a target="_new" href="%s">FeedBurner</a> or <a target="_new" href="%s">FeedMyInbox</a>.',APP_TD), 'http://www.feedburner.com','http://www.feedmyinbox.com/'),
		'tip' 		=> __('Enable this option to allow job seekers to receive job alerts notifications using their unique RSS feed using a 3rd party service.',APP_TD),
		'id' 		=> $app_abbr.'_job_alerts_feed',
		'css' 		=> 'width:100px;',
		'std' 		=> 'no',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD)
		)
	),	

	array( 'type' => 'tabend'),

	
	array( 'type' => 'tab', 'tabname' => __('Email Format', APP_TD) ),

	array(	'name' => __('Job Alert Email', APP_TD), 'type' 		=> 'title' ),
	
		array(  
			'name' => __('From Name',APP_TD),
			'desc' 		=> '',
			'tip' 		=> __('This is what job seekers will see as the &quot;from&quot; when they receive email alerts. Use plain text only',APP_TD),
			'id' 		=> $app_abbr.'_job_alerts_from_name',
			'css' 		=> 'width:250px;',
			'vis' 		=> '',
			'type' 		=> 'text',
			'req' 		=> '',
			'min' 		=> '',
			'std' 		=> get_bloginfo('name'),
		),

		array(  
			'name' => __('From Email',APP_TD),
			'desc' 		=> '',
			'tip' 		=> __('This is what job seekers will see as the &quot;from&quot; email address (also the reply to) when they receive the email alerts. Use only a valid and existing email address with no html or variables.',APP_TD),
			'id' 		=> $app_abbr.'_job_alerts_from_email',
			'css' 		=> 'width:250px;',
			'vis' 		=> '',
			'type' 		=> 'text',
			'req' 		=> '',
			'min' 		=> '',
			'std' 		=> ''
		),

		array(  
			'name' => __('Email Subject',APP_TD),
			'desc' 		=> '',
			'tip' 		=> __('This is the subject line job seekers will see when they receive email alerts. Use text and variables only.',APP_TD),
			'id' 		=> $app_abbr.'_job_alerts_email_subject',
			'css' 		=> 'width:400px;',
			'vis' 		=> '',
			'type' 		=> 'text',
			'req' 		=> '',
			'min' 		=> '',
			'std' 		=> __('Job Alerts',APP_TD),
		),
		
		array(  
			'name' => __('Allow HTML in Body', APP_TD),
			'desc' 		=> '',
			'tip' 		=> __('This option allows you to use html markup in the email body below. If you\'re having proglems with email delivery it is recommended to set this option to &quot;No&quot;. Make sure to test it and that the formatting looks ok and gets delivered properly.',APP_TD),
			'id' 		=> $app_abbr.'_job_alerts_email_type',
			'css' 		=> 'width:100px;',
			'vis' 		=> '',
			'std' 		=> 'text/plain',
			'js' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'text/html'   => __('Yes', APP_TD),
				'text/plain'  => __('No', APP_TD)
			)
		),
		
		array(  
			'name' => __('Email Template', APP_TD),
			'desc' 		=> __('Choose how to send the alert emails. The <em>Standard</em> option will send the text as formatted on the <em>Email Body</em> and  <em>Job List Body</em> fields whereas <em>External</em> will use an external HTML file as the email template.',APP_TD),
			'tip' 		=> __('Email alerts can be formatted using the fields below or using an external HTML template. Both options use the variables presented below.',APP_TD),
			'id' 		=> $app_abbr.'_job_alerts_email_template',
			'css' 		=> 'min-width:400px;',
			'vis' 		=> '',
			'std' 		=> 'standard',
			'js' 		=> '',
			'type' 		=> 'select',
			'options' 	=> array_merge( array('standard' => __('Standard',APP_TD)), jr_job_alerts_get_templates() ),
		),		
	
	array(	'name' => __('Standard Email Format', APP_TD), 'type' 		=> 'title' ),
	
		array(  
			'name' => __('Email Body',APP_TD),
			'desc' 		=> __('You may use the following variables within the email body and/or subject line.<br/><br/><strong>%username%</strong> - prints out the username<br/><strong>%jobtitle%</strong> - prints out the job title for single job emails<br/><strong>%joblist%</strong> - prints out the jobs list<br/><strong>%siteurl%</strong> - prints out your website url<br/><strong>%blogname%</strong> - prints out your site name<br/><strong>%loginurl%</strong> - prints out your sites login url<br/><strong>%dashboardurl%</strong> - prints out the user dashboard url<br/><br/>Each variable MUST have the percentage signs wrapped around it with no spaces.<br/>Always test the email format after making any changes to make sure it is working and formatted correctly.',APP_TD),
			'tip' 		=> __('Enter the text you would like job seekers to see in the email alerts.',APP_TD),
			'id' 		=> $app_abbr.'_job_alerts_email_body',
			'css' 		=> 'width:550px;height:250px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'textarea',
			'std' 		=> ''
		),

		array(  
			'name' => __('Job List Body',APP_TD),
			'desc' 		=> __('You may use the following variables within the email job body.<br/><br/><strong>%jobtitle%</strong> - prints out the Job title<br/><strong>%jobtime%</strong> - prints out the Job time/date<br/><strong>%jobdetails%</strong> - prints out the full job details<br/><strong>%jobdetails_#%</strong> - prints out a cut version of the job details. Replace # for the aproximate lenght of the job details to display<br/><strong>%jobtype%</strong> - prints out the job type<br/><strong>%jobcat%</strong> - prints out the job category<br/><strong>%author%</strong> - prints out the job author<br/><strong>%company%</strong> - prints out the job company<br/><strong>%location%</strong> - prints out the job location<br/><strong>%permalink%</strong> - prints out the job permalink<br/><strong>%thumbnail%</strong> - prints out the job thumbnail<strong>%thumbnail_url%</strong> - prints only the job thumbnail url<br/><br/>Each variable MUST have the percentage signs wrapped around it with no spaces.<br/>Always test the email format after making any changes to make sure it is working and formatted correctly.',APP_TD),
			'tip' 		=> __('Enter the text you would like job seekers to see in the email job part.',APP_TD),
			'id' 		=> $app_abbr.'_job_alerts_job_body',
			'css' 		=> 'width:550px;height:250px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'textarea',
			'std' 		=> ''
		),						
		
	array( 'type' => 'tabend'),	
	
);

// admin options for the pricing page
$options_pricing = array (

	array(  'type' 		=> 'tab', 'tabname' => __('Browse Resumes Pricing', APP_TD)),
	
	array(  
		'name' => __('Recurring Payments',APP_TD),
		'desc' 		=> sprintf( __('Please note that the \'Automatic\' option will only work if you own a <a href="%s">Business or Premier PayPal account</a>. <br/>Please check your PayPal account type before setting this option.',APP_TD), 'https://www.paypal.com/pdn-recurring?bn_r=o' ),
		'tip' 		=> __('<strong>Automatic:</strong> Subscriptions are managed automatically by PayPal (requires a Business or Premier Account).<br/><strong>Manual:</strong> Users make timed payments for the trial or subscription period.',APP_TD),
		'id' 		=> $app_abbr.'_resume_subscr_recurr_type',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'manual',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'manual' => __('Manual (Standard Accounts)', APP_TD),
			'auto'  => __('Automatic (Business/Premier Accounts)', APP_TD),			
		)
	),		

	array(  
		'name' => __('Resume Access Subscription Price', APP_TD),
		'desc' 		=> __('Only enter numeric values or decimal points. Do not include a currency symbol or commas.', APP_TD),
		'tip' 		=> __('This is the amount you want to charge job listers access to the resume database.',APP_TD),
		'id' 		=> $app_abbr.'_resume_access_cost',
		'css' 		=> 'width:75px;',
		'vis' 		=> '',
		'type' 		=> 'text',
		'req' 		=> '',
		'min' 		=> '',
		'std' 		=> ''
	),
	
	array(  
		'name' => __('Subscription Length', APP_TD),
		'desc' 		=> __('Enter an integer. This length is also affected by the unit below.', APP_TD),
		'tip' 		=> '',
		'id' 		=> $app_abbr.'_resume_access_length',
		'css' 		=> 'width:75px;',
		'vis' 		=> '',
		'type' 		=> 'text',
		'req' 		=> '',
		'min' 		=> '',
		'std' 		=> '1'
	),
	
	array(  
		'name' => __('Subscription Unit', APP_TD),
		'desc' 		=> __("Select a unit for the subscription period.",APP_TD),
		'tip' 		=> '',
		'id' 		=> $app_abbr.'_resume_access_unit',
		'css' 		=> 'width:100px;',
		'std' 		=> 'M',
		'js' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'M' => __('Months', APP_TD),
			'D'  => __('Days', APP_TD),
			'W'  => __('Weeks', APP_TD),
			'Y'  => __('Years', APP_TD)
		)
	),
	
	array(  
		'name' => __('Allow trial?',APP_TD),
		'desc' 		=> __('Enabling a trial lets you charge more or less during the first billing period.',APP_TD),
		'tip' 		=> __('This option allows users to trial the subscription service before paying for a full subscription.',APP_TD),
		'id' 		=> $app_abbr.'_resume_allow_trial',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'no',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'no'  => __('No', APP_TD),
			'yes' => __('Yes', APP_TD),
		)
	),
	
	array(  
		'name' => __('Resume Access Trial Price', APP_TD),
		'desc' 		=> __('Only enter numeric values or decimal points. Do not include a currency symbol or commas.', APP_TD),
		'tip' 		=> __('This is the amount you want to charge job listers access to the resume database for their first billing term. Leave blank for free trial.',APP_TD),
		'id' 		=> $app_abbr.'_resume_trial_cost',
		'css' 		=> 'width:75px;',
		'vis' 		=> '',
		'type' 		=> 'text',
		'req' 		=> '',
		'min' 		=> '',
		'std' 		=> ''
	),
	
	array(  
		'name' => __('Trial Length', APP_TD),
		'desc' 		=> __('Enter an integer. This length is also affected by the unit below.', APP_TD),
		'tip' 		=> '',
		'id' 		=> $app_abbr.'_resume_trial_length',
		'css' 		=> 'width:75px;',
		'vis' 		=> '',
		'type' 		=> 'text',
		'req' 		=> '',
		'min' 		=> '',
		'std' 		=> '1'
	),
	
	array(  
		'name' => __('Trial Unit', APP_TD),
		'desc' 		=> __("Select a unit for the trial period.",APP_TD),
		'tip' 		=> '',
		'id' 		=> $app_abbr.'_resume_trial_unit',
		'css' 		=> 'width:100px;',
		'std' 		=> 'M',
		'js' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'M' => __('Months', APP_TD),
			'D'  => __('Days', APP_TD),
			'W'  => __('Weeks', APP_TD),
			'Y'  => __('Years', APP_TD)
		)
	),
	
	array( 'type' => 'tabend')

);

$options_integration = array (

	array(  'type' 		=> 'tab', 'tabname' => __('Indeed.com', APP_TD)),
	
	array(	'name' => __('Main Options', APP_TD), 'type' => 'title', 'desc' => '' ),
	
	array(  'name' => '<img src="'.get_bloginfo('template_directory').'/images/indeed-lg.png" />', 'type' 		=> 'logo' ),	
	
	array(  
		'name' => __('Publisher ID', APP_TD),
                'desc' 		=> sprintf( __("Sign up for a free <a target='_new' href='%s'>Indeed.com account</a> to get a publisher ID.",APP_TD), 'https://ads.indeed.com/jobroll/' ),
		'tip' 		=> __('Enter your Indeed publisher ID (i.e. 4247835648699281).',APP_TD),
		'id' 		=> $app_abbr.'_indeed_publisher_id',
		'css' 		=> 'min-width:350px;',
		'type' 		=> 'text',
		'req' 		=> '',
		'min' 		=> '',
		'std' 		=> '',
		'vis' 		=> ''
	),
	
	array(	'name' => __('Queries', APP_TD), 'type' => 'title', 'desc' => '' ),	
	
	array(  
		'name' => __('Pull x Indeed jobs', APP_TD),
		'desc' 		=> __('Enter the aproximate number of Indeed jobs you want to pull from Indeed.', APP_TD),
		'tip' 		=> '',
		'id' 		=> $app_abbr.'_indeed_front_page_count',
		'css' 		=> 'width:75px;',
		'std' 		=> '5',
		'req' 		=> '',
		'vis' 		=> '',
		'min' 		=> '',
		'type' 		=> 'text'
	),

	array(  
		'name' 		=> __('Site Type', APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Choose whether to pull jobs from job boards, direct employers or both.', APP_TD),
		'id' 		=> $app_abbr.'_indeed_site_type',		
		'css' 		=> 'width:150px;',
		'std' 		=> 'relevance',
		'req' 		=> '',
		'vis' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'all'		=> __('All', APP_TD),
			'jobsite'	=> __('Job Sites', APP_TD),
			'employer' 	=> __('Direct Employers', APP_TD),
		)		
	),
		
	array(  
		'name' 		=> __('Sort by', APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('The sort order of the organic jobs.', APP_TD),
		'id' 		=> $app_abbr.'_indeed_sort_order',
		'css' 		=> 'width:150px;',
		'std' 		=> 'relevance',
		'req' 		=> '',
		'vis' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'relevance' => __('Relevance (default)', APP_TD),
			'date' 		=> __('Date', APP_TD),
		)		
	),		
	
	array(  
		'name' => __('Job listings queries', APP_TD),
		'desc' 		=> sprintf( __("Setup your queries and category mappings to pull in Indeed.com job listings. Each query must be in the following format:<br/><code>keyword [ OR keyword... ]|country|job type|location (optional, post code or city)</code>.
									<br/><br/><strong>Examples:</strong>
									<br/><code>web designer|GB|fulltime</code> Retrieves Full-Time Web Design Jobs in the UK
									<br/><code>web designer OR web developer|GB|fulltime</code> Retrieves Full-Time Web Design OR Web Development Jobs in the UK									
									<br/><br/>One per line. By default all full-time and part-time jobs are shown from the US. For available country codes and other parameters, see the <a target='_new' href='%s'>Indeed.com XML Feed Guide</a>.
									<br/><br/>
									<strong>Note:</strong>
									<br/>For the best results, you should use the following job types: 
									<br/><code>fulltime, parttime, contract, internship, temporary</code>
									<br/><br/>Some job types may need to be mapped to match your JobRoller job types slugs. See more details on the <em>Mappings</em> option, below.																		
									",APP_TD), 'https://ads.indeed.com/jobroll/xmlfeed' ),
		'tip' 		=> __('These queries are used to retrieve relevant jobs to your website and are used differently for frontpage, search and filters: 
					   <br/><br/><strong>Frontpage:</strong> All your queries data will be used to pull relevant jobs as there are no user search of filters criteria. 
					   <br/><br/><strong>Search:</strong> Dynamically uses your queries data depending on the user search. For example, if the user is searching jobs by keyword, your queries keywords will be skipped in favour of the user\'s. It will use all the other queries information like job type or location. 
					   <br><br/><strong>Filters:</strong> Dynamically uses your queries data depending on the user filter. For example, when users filter jobs by job type, your queries job types will be skipped in favour of the user selected job type. This means that even if you only set queries for two job types
					   users can get results from any filterable job type.
					   <br/><br/>Each query will be ran and job listings will be merged together and displayed. Do not add too many queries since this will slow your site down significantly.',APP_TD),
		'id' 		=> $app_abbr.'_front_page_indeed_queries',
		'css' 		=> 'width:500px;height:150px;',
		'vis' 		=> '',
		'req' 		=> '',
		'min' 		=> '',
		'type' 		=> 'textarea',
		'std' 		=> ''
	),		

	
	array(	'name' => __('Mappings', APP_TD), 'type' => 'title', 'desc' => '' ),	
	
	array(  
		'name' => __('Job types mapping', APP_TD),
		'desc' 		=> __("Indeed reconizes the following job types (slugs): <code>fulltime, parttime, contract, internship, temporary</code>.
							</br><br/>If you use different slugs, map each one with the respective Indeed slug in the following format <code>your-slug|indeed-slug</code>.
							<br><br/>Examples: 
							<br/><code>freelance|contract</code>
							<br/><code>temps-partiel|parttime</code>
							<br/><code>tiempo-parcial|parttime</code>
							<br/><code>full-time|fulltime</code>
						",APP_TD),
		'tip' 		=> __('Mappings are used to allow JobRoller to relate your job types with Indeed\'s job types, on each job query, or when users browse jobs by job type, on the sidebar widget.', APP_TD),
		'id' 		=> $app_abbr.'_indeed_jtypes_other',
		'css' 		=> 'width:500px;height:150px;',
		'vis' 		=> '',
		'req' 		=> '',
		'min' 		=> '',
		'type' 		=> 'textarea',
		'std' 		=> ''
	),		
	
	array(	'name' => __('Styling', APP_TD), 'type' => 'title', 'desc' => '' ),

	array(  
		'name' 		=> __('Sponsored Jobs Class', APP_TD),
        'desc' 		=> __('Choose the CSS class that should be applied to sponsored jobs (these jobs generate revenue on a CPC basis).<br/>You can also style these types of jobs using the <code>ty_indeed_sponsored</code> class.',APP_TD),
		'tip' 		=> __('You can style these type of jobs to give them better visibility.',APP_TD),				
		'id' 		=> $app_abbr.'_indeed_job_type_sponsored',
		'css' 		=> 'min-width:150px;',
		'type' 		=> 'text',
		'req' 		=> '',
		'min' 		=> '',
		'std' 		=> 'job-featured',
		'vis' 		=> ''
	),	
	
	array(	'name' => __('Display', APP_TD), 'type' => 'title', 'desc' => '' ),
	
	array(  
		'name' => __('Show results on the front-page?',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('This option will dynamically pull in jobs from indeed on your front page.',APP_TD),
		'id' 		=> $app_abbr.'_indeed_front_page',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'yes',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD),
		)
	),
		
	array(  
		'name' => __('Show results when browsing?',APP_TD),

		'desc' 		=> sprintf (__("Only jobs matching your job listings queries are returned when browsing with the sidebar widget. <br/><br/>Adding <code>Design|GB|fulltime</code> to your job listings queries will return <code>fulltime</code> / <code>design</code> jobs when users browse <strong>Design</strong> jobs.
						<br/><br/><strong>Note:</strong> If you want to allow your visitors to browse jobs from any category (Job Type, Job Category, Job Salary, Date), without yet having published jobs on those categories (usually hidden), you can enable the <em>Show Empty Categories</em> option on the <a href='%s'>General Options</a> page.
						",APP_TD), 'admin.php?page=settings' ),									
		'tip' 		=> __('Enable this option to pull in jobs from Indeed when users browse jobs using the sidebar widget (Job Type, Job Category, Job Salary*, Post Date). <br/><br/><strong>(*) </strong> Indeed Job Salary browsing is ony available in some countries',APP_TD),
		'id' 		=> $app_abbr.'_indeed_all_listings',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'no',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' => __('Yes', APP_TD),
			'no'  => __('No', APP_TD),
		)
	),
		
	array(  
		'name' => __('Show results when searching',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('This option will dynamically pull in search results from indeed when your job board has no results.',APP_TD),
		'id' 		=> $app_abbr.'_dynamic_search_results',
		'css' 		=> 'min-width:100px;',
		'std' 		=> 'yes',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(  
			'yes' 		=> __('All the time', APP_TD),
			'noresults' => __('Only when no local results are found', APP_TD),
			'no' 		=> __('Never', APP_TD),
		)
	),

	array(
		'name' => __('Results position',APP_TD),
		'desc' 		=> '',
		'tip' 		=> __('Select whether to display indeed results before or after your site listings.',APP_TD),
		'id' 		=> $app_abbr.'_indeed_results_position',
		'css' 		=> 'min-width:150px;',
		'std' 		=> 'yes',
		'vis' 		=> '',
		'req' 		=> '',
		'js' 		=> '',
		'min' 		=> '',
		'type' 		=> 'select',
		'options' => array(
			'before' => __('Before Site Listings', APP_TD),
			'after'  => __('After Site Listings', APP_TD)
		)
	),

	array(	'name' => __('Caching', APP_TD), 'type' => 'title', 'desc' => '' ),

	array(  
		'name' 		=> __('Frontpage Results Duration',APP_TD),
		'desc' 		=> __('Only enter numeric values (in seconds).<code>i.e: 3600 = 1 hour</code>.<br/>Leave blank to disable caching.',APP_TD),
		'tip' 		=> __('To speed up Indeed frontpage loading, you can cache the results for a set period of time. Results will be refreshed when this period expires.',APP_TD),
		'id' 		=> $app_abbr.'_indeed_frontpage_cache',
		'css' 		=> 'width:75px;',
		'std' 		=> '3600',
		'vis' 		=> '',
		'req' 		=> '',
		'min' 		=> '',
		'type' 		=> 'text',
	),
	
	array( 'type' 		=> 'tabend')

);

// apply filters to allow add additional options 
$options_integration = apply_filters('jr_filter_integration_values', $options_integration );
