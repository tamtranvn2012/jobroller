<?php
/**
 *
 * Admin field data
 * Adapted from the original admin settings code by Appthemes
 * 
 */

# Extended features global vars
global $jr_fx_options_settings, $jr_fx_info, $jr_fx_info_tab, $jr_fx_toggle_button;

	$jr_fx_info_tab = ( JR_FX_VERSION == JR_FX_VER_FREE ? array( 'name' => __('Info', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => '') : array( 'type' => 'dummy' ) );

	$jr_fx_info = ( 
	
		JR_FX_VERSION == JR_FX_VER_FREE ?

		array(
			'name' =>'<div class="jr_fx_admin_info"><img  class="jr_fx_admin_logo jr_fx_admin_me" src="'.JR_FX_PLUGIN_URL.'/images/logo.png" title= "'.JR_FX_PLUGIN_TITLE.'"/>'.
			(JR_FX_VERSION == JR_FX_VER_FREE?jr_fx_buy_me_url( $wrap = 'yes', $buynow = 'yes' ):'').
			'<strong>'.__('Version',JR_FX_i18N_DOMAIN).': </strong> ' . JR_FX_PLUGIN_VERSION . 
			' (' . (JR_FX_VERSION == JR_FX_VER_FREE?JR_FX_VER_FREE:JR_FX_VER_PLUS) . ') '.jr_fx_check_update_notice().
			'<br/>'.
			jr_fx_upgrade( $option='', sprintf(__('Upgrade to PRO now and unlock all the features! Only <em>%s</em>!' ,JR_FX_i18N_DOMAIN), JR_FX_PRICE), $show_ico  = 'yes' ).'</div>',
			'type' => 'free',
		) : array( 'type' => 'dummy' )

	);

	$jr_fx_options_settings = array(

		array( 'type' => 'tab', 'tabname' => __('General', JR_FX_i18N_DOMAIN) ),

//		$jr_fx_info_tab,

		$jr_fx_info_tab,
		
		$jr_fx_info,

		array( 'name' => __('Site', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),

		array(  
			'name' 		=> jr_fx_upgrade( '_opt_site_breadcrumbs' ) . __('Show Breadcrumbs', JR_FX_i18N_DOMAIN),
			'desc' 		=> __("If the search bar is disabled, breadcrumbs will show on top of the main content.", JR_FX_i18N_DOMAIN),
			'tip' 		=> __("Shows breadcrumbs throughout the website.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_site_breadcrumbs',
			'css' 		=> 'width:150px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'over' => __('Over Search Bar', JR_FX_i18N_DOMAIN),
				'under' => __('Under Search Bar',JR_FX_i18N_DOMAIN),
				'no'  => __('No',JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'no',	
		),			
		
		array(  
			'name' 		=> jr_fx_upgrade( '_opt_jobs_visitors_redirect' ) . __('Redirect Job Visitors', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('If active, non registered members will be redirected to the login page when trying to view jobs.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __('This option is recommended to convert visitors into registered users.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_jobs_visitors_redirect',
			'css' 		=> 'width:50px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'tzCheckbox',
			'options' => array(  
				'yes' => __('Yes',JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'no',
		),	
		
		array(  
			'name' 		=> jr_fx_upgrade( '_opt_jobs_apply_visitors_redirect' ) . __('Redirect Application Visitors', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('If active, non registered members will be redirected to the login page when trying to apply online.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __('This option is recommended to convert visitors into registered users.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_jobs_apply_visitors_redirect',
			'css' 		=> 'width:50px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'tzCheckbox',
			'options' => array(  
				'yes' => __('Yes',JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'no',
		),		

		array(  
			'name' =>  jr_fx_upgrade( '_opt_jobs_apply_registered_email' ) .  __('Apply with Registered Emails Only', JR_FX_i18N_DOMAIN),
			'desc' 		=> __("Enable this option to block applications from unknown email addresses.", JR_FX_i18N_DOMAIN),
			'tip' 		=> __('Enabling this option will block email addresses that are not in the database.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_jobs_apply_registered_email',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'tzCheckbox',
			//'type' 		=> 'select',
			'options' => array(  
				'yes' => __('Yes', JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN)
			),
			'std' 		=> 'no',
		),

		array( 'type' => 'tabend'),

		array( 'type' => 'tab', 'tabname' => __('Job Publishing', JR_FX_i18N_DOMAIN) ),		

		$jr_fx_info_tab,

		$jr_fx_info ,

		array( 'name' => jr_fx_upgrade( '_int_jobs_free_offer' ) . __('Job Offers', JR_FX_i18N_DOMAIN) , 'type' => 'title', 'desc' => '', 'id' => ''),

		array(  
			'name' 		=> __('First # Job(s) Free', JR_FX_i18N_DOMAIN),
			'desc' 		=> __( 'Used only if you charge for job posts. The customer will be able to submit this number of free jobs skipping the \'Select Plan\' step.', JR_FX_i18N_DOMAIN ),
			'tip' 		=> __("Promote you website offering some job postings for free by setting the number of free job postings you want to offer. You can show a special message for this offers and hide the pricing text on the <i>Notices</i> tab.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_int_jobs_free_offer',
			'css' 		=> 'width:50px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'text',
			'std' 		=> '',
		),

		
		array(  
			'name' 		=> __('Hide Pricing Text', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('Used only if you offer free jobs.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __("If you set a value on the <i>First # Job(s) Free</i> option you can hide the pricing text displayed bellow the 'Submit Job' button by setting this option to <i>Yes</i>.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_jobs_hide_pay_free_offer',
			'css' 		=> 'width:200px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'yes' => __('Yes (No Message)', JR_FX_i18N_DOMAIN),
				'text' => __('Yes (Custom Message)', JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'no',
		),
		
		array(  
			'name' 		=> __('Pricingt Replacement Text', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('Used only if you set \'Hide Pre-Payment Paragraph\' to \'Custom Message\'. <br><strong>Note:</strong> Message will ONLY be visible if NO fees apply for the current Job listing.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __('The pricing text will be replaced with this one for Free Job Offers.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_jobs_free_offer_text',
			'css' 		=> 'width:550px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'text',
			'std' 		=> 'This a FREE job post. It will not be charged.',
		),		
		
		array( 'name' => __('Job Moderation', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),
		
		array(  
			'name' 		=> jr_fx_upgrade( '_int_jobs_min_auto_pub' ) . __('First # Job(s) Moderated', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('Skipped if you have JobRoller <i>Moderate Jobs Listings</i> option set to <i>Yes</i> or with paid jobs.<br/><strong>Note: </strong>You can set a notification message for this feature on the <a href="admin.php?page=jr-fx-admin-notifications">notifications page</a>.',JR_FX_i18N_DOMAIN),
			'tip' 		=> __("If a value is set (>0), every job posted will need moderation until the minimum published jobs is reached by the publisher. You can show a special message for this jobs on the <i>Notices</i> tab.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_int_jobs_min_auto_pub',
			'css' 		=> 'width:50px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'text',
			'std' 		=> '',
		),	
		
		array(  
			'name' 		=> jr_fx_upgrade( '_opt_jobs_moderate' ) . __('Moderate Jobs Posted by Admins', JR_FX_i18N_DOMAIN),
			'desc' 		=> __("If set to <i>Yes</i>, this option will have priority over all the job status changes. If posted by an Admin, a new job will always be <i>Pending</i>. Skipped if you have jobroller <i>Moderate Jobs</i> option set to <i>Yes</i>.",JR_FX_i18N_DOMAIN),
			'tip' 		=> __('If you intend to publish jobs manually and add extra information before publishing you should enable this option.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_jobs_moderate',
			'css'  		=> 'width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'tzCheckbox',
			//'type' 		=> 'select',
			'options' => array(  
				'yes' => __('Yes', JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'no',											
		),
		
		array( 'type' => 'tabend'),					
		
		array( 'type' => 'tab', 'tabname' => __('Job Submit', JR_FX_i18N_DOMAIN) ),		
		
		$jr_fx_info_tab,
		
		$jr_fx_info ,	
		
		array( 'name' => jr_fx_upgrade( '_opt_jobs_duration_field' ). __('Job Duration (custom field)', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),
		
		array(  
			'name' =>  __('Job Duration Field', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('Overrides JobRoller\'s job expiry calculation and give the publisher the option to set job duration. Default is <strong>30 days</strong>.<br/>It should only be used if you don\'t charge for jobs as job plans have their own job duration.
								<br/><br/><strong>Note:</strong> The custom duration field is not displayed on offered jobs.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __('A new numeric field will be available for the publisher to set the number of days until job expires.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_jobs_duration_field',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'all' => __('Yes (All Publishers)',JR_FX_i18N_DOMAIN),
				'admin' => __('Yes (Only Admins)',JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN)
			),
			'std' 		=> 'no',
		),

		array(  
			'name' 		=>   __('Job Duration Caption', JR_FX_i18N_DOMAIN),
			'desc' 		=> __("Used only if the <i>Job duration field</i> option is used.",JR_FX_i18N_DOMAIN),
			'tip' 		=> __('This message will appear bellow the job duration field.',JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_text_jobs_duration_caption',
			'css' 		=> 'width:550px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'text',
			'std' 		=> __('Please choose the duration of this job (days).', JR_FX_FIELDS_PREFIX),
		),
		
		array(  
			'name' 		=>  __('Job Duration (min. days)', JR_FX_i18N_DOMAIN),
			'desc' 		=> __("Used only if the <i>Job duration field</i> option is used.",JR_FX_i18N_DOMAIN),
			'tip' 		=> __('Set the minimum days for the job duration field.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_int_jobs_min_duration',
			'css' 		=> 'width:50px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'text',
			'std' 		=> '1',																
		),		
		array(  
			'name' 		=> __('Job Duration (max. days)', JR_FX_i18N_DOMAIN),
			'desc' 		=> __("Used only if the <i>Job duration field</i> option is used.",JR_FX_i18N_DOMAIN),
			'tip' 		=> __('Set the maximum days for the job duration field.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_int_jobs_max_duration',
			'css' 		=> 'width:50px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'text',
			'std' 		=> '30',																			
		),
		
		array( 'name' =>  jr_fx_upgrade( '_opt_jobs_recipient_field' ) . __('Job Applications (custom field)', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),			
		
		array(  
			'name' 		=> __("Enable Applications Recipient Field", JR_FX_i18N_DOMAIN),
			'desc' 		=> __('<strong>Admins Only</strong> - A new field will be available on the <i>Submit Job</i> page that will allow Admins to add a recipient email address on their posted jobs. Aplications will be sent to this email address instead of the Admins email (the author).',JR_FX_i18N_DOMAIN),
			'tip' 		=> __("Set this option to <i>Yes</i> if you usually add jobs manually as an Admin using the frontend form. This custom field will appear on the job page admin panel and can be easily deleted or updated. If the field is deleted, all applications will be sent to the author email as usual. ",JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_jobs_recipient_field',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'tzCheckbox',	
			//'type' 		=> 'select',
			'options' => array(  
				'yes' => __('Yes', JR_FX_i18N_DOMAIN),
				'no'  => __('No',JR_FX_i18N_DOMAIN)
			)			,
			'std' 		=> 'no',	
		),	


		array( 'name' => jr_fx_upgrade( '_opt_jobs_optional_apply_online' ). __('Apply Online (custom field)', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),			
		
		array(  
			'name' =>  __('Optional Apply Online Field', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('Enabling this option will allow job listers to enable/disable online applications.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __('A checkbox will be available for the publishers to enable/disable online applications.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_jobs_optional_apply_online',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'no' => __('No',JR_FX_i18N_DOMAIN),
				'yes'  => __('Yes', JR_FX_i18N_DOMAIN)
			),
			'std' 		=> 'no',
		),

		array( 'name' => __('Job Lister / Recruiter', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),

		array(  
			'name' => jr_fx_upgrade( '_opt_jobs_lister_auto_complete' ) . __("Auto Fill Company Details", JR_FX_i18N_DOMAIN),
			'desc' 		=> __('Auto fills Job Lister company information with their profile information.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __("The job lister company information (Company Name and Website) will be auto completed when submiting jobs.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_jobs_lister_auto_complete',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'always'  	=> __('Always', JR_FX_i18N_DOMAIN),		
				'selectable'=> __('Selectable', JR_FX_i18N_DOMAIN),		
				'no'  		=> __('No', JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'no',
		),	

		array(  
			'name' => jr_fx_upgrade( '_opt_jobs_lister_persistent_logo' ) . __("Enable Persistent Company Logos", JR_FX_i18N_DOMAIN),
			'desc' 		=> __('Enabling this option will allow Job Listers to store a company logo on their Profile. The logo will be automatically loaded when submitting jobs.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __("Job Listers can store a company logo on their Profile to use on all new jobs or when selecting the 'Set as default Company Logo' option on the job submit page.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_jobs_lister_persistent_logo',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'tzCheckbox',
			'options' => array(  
				'yes'  	=> __('Yes', JR_FX_i18N_DOMAIN),
				'no'  	=> __('No', JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'no',
		),	
		
		array(  
			'name' => jr_fx_upgrade( '_opt_jobs_lister_persistent_logo_gravatar' ) . __("Replace Gravatar with Company Logo", JR_FX_i18N_DOMAIN),
			'desc' 		=> __('Enabling this option will replace job listers avatar with the company logo stored on their Profile.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __("The company logo will be visible on the job lister public Profile page. You need to enable 'Persistent Company Logos' for this feature to work.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_jobs_lister_persistent_logo_gravatar',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'tzCheckbox',
			'options' => array(  
				'yes'  	=> __('Yes', JR_FX_i18N_DOMAIN),
				'no'  		=> __('No', JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'no',
		),			
		
		array( 'name' => __('Google Maps', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),
		
		array(  
			'name' =>  jr_fx_upgrade( '_opt_jobs_gmaps' ) . __("Disable/Hide Google Maps",JR_FX_i18N_DOMAIN),
			'desc' 		=> __("If set to <i>Yes</i>, Google Maps will be disabled and the location filled by the job publisher will be saved as is, without the google maps filtering.",JR_FX_i18N_DOMAIN),
			'tip' 		=> __('Disables geolocation. Location search is done by matching locations instead of google maps geolocation. Radius will be hidden and ignored.'),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_jobs_gmaps',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'tzCheckbox',
			'options' => array(  
				'yes' => __('Yes', JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN)
			)			,
			'std' 		=> 'no',
		),

		array( 'type' => 'tabend'),
		
		array( 'type' => 'tab', 'tabname' => __('Job Page', JR_FX_i18N_DOMAIN) ),
		
		$jr_fx_info_tab,

		$jr_fx_info ,

		array( 'name' =>  jr_fx_upgrade( '_opt_jobs_company_logo' ) . __('Company Logo', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),

		array(  
			'name' =>  __('Hide Company Logo', JR_FX_i18N_DOMAIN),
			'desc' 		=> __("If you intend to use the <i>Company Logo Widget</i> you can hide the logo on the job page.", JR_FX_i18N_DOMAIN),
			'tip' 		=> __('Hides the company logo on the job page.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_jobs_company_logo',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'tzCheckbox',
			//'type' 		=> 'select',
			'options' => array(  
				'yes' => __('Yes', JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN)
			),
			'std' 		=> 'no',
		),

		array( 'name' => __('Buttons', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),
		
		array(  
			'name' => jr_fx_upgrade( '_opt_jobs_buttons' ) . __("Hide Action Buttons", JR_FX_i18N_DOMAIN),
			'desc' 		=> __('You can hide any of the buttons available on the job page footer.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __("Hides any or all action buttons available on the job page.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_jobs_buttons',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'all'  		=> __('All', JR_FX_i18N_DOMAIN),
				'apply' 		=> __('Apply Online', JR_FX_i18N_DOMAIN),
				'print' 	=> __('Print', JR_FX_i18N_DOMAIN),
				'no'  		=> __('None', JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'no',
		),

		array(  
			'name' => jr_fx_upgrade( '_opt_jobs_apply_button' ) . __("Show <i>Apply Online</i>", JR_FX_i18N_DOMAIN),
			'desc' 		=> __("This option will give your more control over the visibility of the <i>Apply Online</i> button. It will take precedence over the option you choose on the <i>Hide Buttons</i> feature. If you hide it, it will only be visible based on this option.", JR_FX_i18N_DOMAIN),
			'tip' 		=> __("Control the visibility of the <i>Apply Online</i> button by choosing when to show it.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_jobs_apply_button',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'always' 	=> __('Always', JR_FX_i18N_DOMAIN),
				'jobpack' 	=> __( 'Paid Job', JR_FX_i18N_DOMAIN ),
				'never'  	=> __('Never', JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'always',
		),

		array( 'name' => __('Job Seeker', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),
		
		array(  
			'name' => jr_fx_upgrade( '_opt_jobs_seeker_auto_complete' ) . __("Auto Fill User Details", JR_FX_i18N_DOMAIN),
			'desc' 		=> __('When applying to jobs, the Job Seeker name and email will be auto filled with their profile information.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __("The Job Seeker name and email will be auto filled when applying to jobs.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_jobs_seeker_auto_complete',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'always'  	=> __('Always', JR_FX_i18N_DOMAIN),
				'selectable'=> __('Selectable', JR_FX_i18N_DOMAIN),
				'no'  		=> __('No', JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'no',
		),

		array( 'name' => jr_fx_upgrade( '_opt_jobs_applications_monitor' ). __('Job Applications Monitor', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),

		array(
			'name' =>  __('Monitor Job Applications', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('Enabling this option will monitor job applications. Site owners will be able to track applications on a new meta box on the jobs page, or on each user profile.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __('Monitoring include appplications sent using \'Apply Online\' or LinkedIn, if you\'ve enabled it. Job seekers will see an \'Already Applied\' caption on the apply button on jobs already applied.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_jobs_applications_monitor',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'tzCheckbox',
			'options' => array(  
				'yes' => __('Yes', JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN)
			),
			'std' 		=> 'no',
		),

		array(
			'name' =>  __('Display \'Applications\' Tab', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('Enables a new Tab on the job seekers and job listers/recruiters dashboards, with job applications stats.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __('The new Tab allows listers/recruiters or seekers to track job applications. Job application monitoring must be enabled.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_jobs_applications_dashboard',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'tzCheckbox',
			'options' => array(  
				'yes' => __('Yes', JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN)
			),
			'std' 		=> 'no',
		),

		array( 'type' => 'tabend'),

		array( 'type' => 'tab', 'tabname' => __('Job Listings', JR_FX_i18N_DOMAIN) ),

		$jr_fx_info_tab,

		$jr_fx_info ,

		array( 'name' => __('Listings Info', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),

		array(  
			'name' => jr_fx_upgrade( '_opt_listings_days_left' ) . __("Replace Date with Days Left", JR_FX_i18N_DOMAIN),
			'desc' 		=> __('Replaces the <i>Date</i> on the selected page(s) with the number of days left.',JR_FX_i18N_DOMAIN),
			'tip' 		=> __("Show how many days the Job will remain valid instead of the listing Date.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_listings_days_left',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'all'  		=> __('Job Page + Job Listings', JR_FX_i18N_DOMAIN),
				'job' 		=> __('Job Page', JR_FX_i18N_DOMAIN),
				'listing' 	=> __('Job Listings', JR_FX_i18N_DOMAIN),
				'no'  		=> __('No', JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'no',
		),

		array( 'name' => jr_fx_upgrade( '_opt_listings_logo' ). __('Listings Thumbs', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),		

		array(  
			'name' =>  __('Display Company Logo Thumb', JR_FX_i18N_DOMAIN),
			'desc' 		=> sprintf(__('The company logo uses your %s. You can also customize the stylings via css.', JR_FX_i18N_DOMAIN),__('<a href="options-media.php">media thumbnail sizes</a>',JR_FX_i18N_DOMAIN)).
							'<br/><em>Example:</em><br/>'.
							'<img src="'.JR_FX_PLUGIN_URL.'images/feature-company-thumbs.png" class="jr_fx_feat_example">',
			'tip' 		=> __('Displays the company logo thumbnail on the job listings.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_listings_logo',
			'css' 		=> 'min-width:100px;',
			'std' 		=> 'all',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'all' => __('Yes (All Jobs)', JR_FX_i18N_DOMAIN),
				'featured' => __('Yes (Featured Jobs)', JR_FX_i18N_DOMAIN),
				'jobpack' => __( 'Yes (Paid Jobs)', JR_FX_i18N_DOMAIN ),
				'no'  => __('No', JR_FX_i18N_DOMAIN)
			),
			'std' 		=> 'no',
		),	
		
		array(  
			'name' =>  __('Company Logo Thumb Position', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('Used only if you use company thumbs on listings. Recommended dimensions for thumbnails: <strong>40px x 40px</strong>.
							  <br/> If you already have submitted jobs you might need to re-save them in order for the thumbnails to be correctly re-sized.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __('Company logo thumbnail position on the job listings.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_listings_logo_pos',
			'css' 		=> 'min-width:100px;',
			'std' 		=> 'all',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'left' => __('Float Left (resizes Title, Location and Date)', JR_FX_i18N_DOMAIN),
				'right' => __('Float Right (resizes Title, Location and Date)', JR_FX_i18N_DOMAIN),
				'collapse' => __('Float Left Collapse (collapses Location and Date)', JR_FX_i18N_DOMAIN),
				'under' => __('Float Left Under (left centered under Job type)', JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'right',
		),		

		array( 'name' => __('Show Job Preview', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),

		array(  
			'name' 		=> jr_fx_upgrade( '_opt_listings_preview' ) . __('Job Preview', JR_FX_i18N_DOMAIN),
			'desc' 		=> __("Let applicants preview jobs by hovering job listings.", JR_FX_i18N_DOMAIN),
			'tip' 		=> __("This option will allow users to preview jobs by hovering the job listings. You can choose the content you want to show. <i>Full</i> is not recommended.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_listings_preview',
			'css' 		=> 'width:200px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'custom' => __('Job description (Custom) ',JR_FX_i18N_DOMAIN),
				'excerpt' => __('Job description (Excerpt)', JR_FX_i18N_DOMAIN),
				'full' => __('Job Description (Full)',JR_FX_i18N_DOMAIN),				
				'no'  => __('No',JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'no',
		),

		array(  
			'name' 		=> jr_fx_upgrade( '_opt_listings_preview_size' ) . __('Job Preview Description Size', JR_FX_i18N_DOMAIN),
			'tip' 		=> __("Set an aproximate number of words to show on the Job description preview. The remaining text will be truncated.", JR_FX_i18N_DOMAIN),
			'desc' 		=> __("If you chose <i>Custom</i> on the previous option please set the aproximate number of words you want to show on the Job preview baloon. <strong>45</strong> is the minimum and recommended value.",JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_listings_preview_size',
			'css' 		=> 'width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'text',
			'std' 		=> '45',
		),
		
		array(  
			'name' 		=> jr_fx_upgrade( '_opt_listings_preview_thumb' ) . __('Job Preview Company Logo', JR_FX_i18N_DOMAIN),
			'desc' 		=> sprintf(__("The job preview window can be refined by showing the company thumbnail.<br/>Sizes can be edited using your %s.",JR_FX_i18N_DOMAIN),__('<a href="options-media.php">media settings</a>',JR_FX_i18N_DOMAIN)),			
			'tip' 		=> __("Shows the company thumbnail size for the job preview if exists. It uses your <i>Listings Thumbs</i> selection to show or hide the company logos on the preview window.", JR_FX_i18N_DOMAIN),			
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_listings_preview_thumb',
			'css' 		=> 'width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'large' => __('Large',JR_FX_i18N_DOMAIN),
				'medium' => __('Medium', JR_FX_i18N_DOMAIN),				
				'thumbnail' => __('Thumb',JR_FX_i18N_DOMAIN),
				'no'  => __('None',JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'no',
			'class'		=> (!jr_fx_has_perms_to( '_opt_listings_preview_size' )?'jr_fx_locked':''),																																							
		),		

		array(  
			'name' 		=> jr_fx_upgrade( '_opt_listings_preview_color' ) . __('Job Preview Color', JR_FX_i18N_DOMAIN),
			'desc' 		=> '',
			'tip' 		=> __("Sets the color of the <i>Job Preview</i> hovering baloon.",JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_listings_preview_color',
			'css' 		=> 'width:160px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => jr_fx_get_qtip_style( 'colors' ),
			'std' 		=> 'ui-tooltip-light',																																										
		),	
		
		array(  
			'name' 		=> jr_fx_upgrade( '_opt_listings_preview_style' ) . __('Job Preview Style', JR_FX_i18N_DOMAIN),
			'desc' 		=> '',
			'tip' 		=> __("Sets the style of the <i>Job Preview</i> hovering baloon.",JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_listings_preview_style',
			'css' 		=> 'width:160px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => jr_fx_get_qtip_style(),
			'std' 		=> 'ui-tooltip-shadow',
		),	

		array(  
			'name' 		=> jr_fx_upgrade( '_opt_listings_preview' ) . __('Job Preview Exclude Pages', JR_FX_i18N_DOMAIN),
			'desc' 		=> __("Comma separated list of pages to disable preview. Use any of the values bellow:
			 <br/><span class='jr_fx_highlight_value'>MAIN</span> (excludes preview from main listing and pagination)
			 <br/><span class='jr_fx_highlight_value'>TAXONOMY</span> (excludes preview from taxonomy pages: job category, job type, etc)
			 <br/><span class='jr_fx_highlight_value'>SEARCH</span> (excludes preview from search results)
			 <br/><span class='jr_fx_highlight_value'>AUTHOR</span> (excludes preview from author pages)
			<br/><br/>Example:<br/><span class='jr_fx_highlight_value'>MAIN, TAXOMOMY</span> would exclude preview from main and taxnomy pages
			 ", JR_FX_i18N_DOMAIN),
			'tip' 		=> __("This option will disable the job preview on the specified pages.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_listings_preview_exclude',
			'css' 		=> 'width:400px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'text',
			'std' 		=> '',
		),

		array( 'name' => __('Job Preview and Thumbs Cache', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),

		array(
			'name' 		=> jr_fx_upgrade( '_opt_listings_preview' ) . __('Cache Duration (in days)', JR_FX_i18N_DOMAIN),
			'desc' 		=> '',
			'tip' 		=> __( 'The number of day to cache job thumbs and job preview to help increase site performance.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_listings_preview_cache_time',
			'css' 		=> 'width:50px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'text',
			'std' 		=> 7,
		),

		array(
			'name' 		=> jr_fx_upgrade( '_opt_listings_preview' ) . __('Empty Cache', JR_FX_i18N_DOMAIN),
			'desc' 		=> __( 'Check the box and save changes to clear the job preview and thumbs cache.' ),
			'tip' 		=> __( 'Click the button to clear your job preview and thumbs cache manually.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_listings_preview_clear_cache',
			'css' 		=> '',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'checkbox',
			'std' 		=> '',
		),

		array( 'type' => 'tabend'),

	);

