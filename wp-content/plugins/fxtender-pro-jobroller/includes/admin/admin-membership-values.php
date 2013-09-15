<?php
/**
 *
 * Admin field data
 * Adapted from the original admin settings code by Appthemes
 * 
 */

//Extended features global vars
global $jr_fx_membership_settings, $jr_fx_info, $jr_fx_info_tab;
	
	$s2member_logo = '<img src="' . JR_FX_PLUGIN_URL . '/images/s2member-logo.png">';
	
	$s2member_options_url = 'admin.php?page=ws-plugin--s2member-gen-ops';
	$s2member_paypal_url = 'admin.php?page=ws-plugin--s2member-paypal-ops';
	$s2member_paypal_buttons_url = 'admin.php?page=ws-plugin--s2member-paypal-buttons';
	
	$pages[''] = 'Frontpage';
	$pages_members[''] = 'None';
	foreach ( (array_merge ((array)get_pages ())) as $page ) {
		$pages[ $page->ID ] = $page->post_title;		
		$pages_members[ $page->ID ] = $page->post_title;
	}

	$jr_fx_membership_settings = array(			
	
		array( 'type' => 'tab', 'tabname' => __('S2Member Integration', JR_FX_i18N_DOMAIN) ),

		$jr_fx_info_tab,	
		
		$jr_fx_info ,	
				
		array( 'name' => __('Overview', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),	
		
		array(  'name' => '<div class="jr_fx_admin_container">
						   <div class="jr_fx_admin_logo_wrap pad">'. $s2member_logo . '</div> 
						   <p>FXtender now integrates with the <a href="http://wordpress.org/extend/plugins/s2member/" target="_new"><i>S2Member</i></a> plugin. If you install it you can restrict access to certain features for non paying Job Seekers. Every Job Seeker will continue to have access to their Dashboard but all the restricted features will be marked with the user level needed for access. These indicator will redirect Job Seekers to a Membership/Subscription page where they can buy access to the restricted features.</p>
						   <p>You need to have <i>S2Member</i> configured correctly for this feature to work properly. <i>S2Member</i> can be a very complex plugin but you need only to set a couple of options to integrate it with <i>FXtender</i> for <i>JobRoller</i>.
						   Only use the more advanced options of <i>S2Member</i> if you really know what you\'re doing as this can break your <i>JobRoller</i> theme functionality and restrict access to certain areas accidentally.</p>
						   <p><strong>Notes:</strong></p>
						   <p>
						   . Restrictions are currently limited to the <i>Resumes</i> features.
						   </p>
						   <p>. You may need to manually upgrade your current members levels if you want them to continue accessing Resumes features.</p>						   
						   <br/>
						   <strong>S2Member Status</strong>: '. (JR_FX_S2MEMBER_EXIST?'<span style="color:green">'.__('Installed - You can use this feature!',JR_FX_i18N_DOMAIN).'</span>':'<span style="color:red">'.__('Not installed/Activated!',JR_FX_i18N_DOMAIN). '</span> ' . __('<a href="http://wordpress.org/extend/plugins/s2member/" target="_new">Please install/activate <i>S2Member</i> to use this feature</a>.',JR_FX_i18N_DOMAIN)).'
						   </div>',
				'type' => 'free',
			 ),
			 
		array( 'name' => __('Instructions', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),	
		
		array(  'name' => '<div class="jr_fx_admin_container">
						   <p>Please read the <strong><i>How To</i></strong> to setup <i>S2Member</i>. This <strong><i>How To</i></strong> covers the basics for setting up <i>S2Member</i> to work with <i>JobRoller</i>. <i>FXtender</i> integrates with <i>S2Member</i> 
						   only to redirect or inform Free Job Seekers when trying to access restricted features. <i>FXtender</i> DOES NOT create Membership, Subscription or any other pages. These must be previously created by an Admin. Please read <a href="http://wordpress.org/extend/plugins/s2member/" target="_new"><i>S2Member</i></a> documentation for further information on 
						   how to setup this pages. You can read <i>S2Member</i> <a href="admin.php?page=ws-plugin--s2member-start">Quick-Start Guide</a> for a complete overview of all the options available.</p>
						   <p>The important thing to note when using <i>S2Member</i> is the way the current <i>Job Seeker</i> Role and the new Roles available work together. Resuming, a registered <i>Job Seeker</i> always starts as <i>Free</i> at <i>level #0</i>, with limited access to some features. To access restricted features, they will need to upgrade their account to a greater level <i>(#1 to #4)</i> using a Subscription/Membership page. 
							</p>		 						   
						   <p>Demoted Users <i>(level #0)</i> automatically become Free <i>Job Seekers</i>.</p>
						   </div>',
				'type' => 'free',
			 ),			 

		array( 'name' => jr_fx_upgrade( '_opt_s2member_restrict_access' ) . __('Settings', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),	
			 
		array(  
			'name' =>  __("Enable <i>S2Member</i> Integration", JR_FX_i18N_DOMAIN),
			'desc' 		=> __("If active, Job Seekers will not be able to access some features. Currently, only Resumes features (Add Resumes, Upload Profile Resumes, etc...). This option will be ignored if <i>S2Member</i> is no installed!<br>
							  <br/><strong>Note:</strong> Restricted features will continue to be VISIBLE to any Job Seeker on their Dashboard. An icon will appear next to each feature title heading indicating a restricted feature (you can change this icon by tweaking <i>FXtender</i> <i>styles.css</i>). This allows Free Job Seekers to see the features that become available after upgrading their account.
			", JR_FX_i18N_DOMAIN),
			'tip' 		=> __("Make money with Job Seekers by restricting access to certain features. ", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_s2member_restrict_access',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'tzCheckbox',
			'dis'		=> 	'',
			'options' => array(  
				'yes' => __('Yes', JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN)
			),
			'std' 		=> 'no',		
		),

		array( 'name' => jr_fx_upgrade( '_opt_s2member_restrict_access' ) . __('Apply Online', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),	
		
		array(
			'name' 		=> __("Apply to Jobs User Access Level", JR_FX_i18N_DOMAIN),
			'desc' 		=> __("The minimum <i>S2Member</i> user access level a Job Seeker must have to apply for jobs.<br><strong>Note:</strong> Free Job Seekers will still see the <i>Apply Online</i> button but will be redirect if they dont'\t have the minimum acccess level.", JR_FX_i18N_DOMAIN),
			'tip' 		=> __("Choose the minimum use access level for applying to jobs.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_jobs_apply_s2member_level',
			'css' 		=> 'min-width:150px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'dis'		=> '',
			'type' 		=> 'select',
			'options'   => array (
				'0' => "Level #0 (free)",
				'1' => "Level #1",
				'2' => "Level #2",
				'3' => "Level #3",
				'4' => "Level #4",
			),
			'std' 		=> '0',
		),

		array(
			'name' 		=> __("Apply with LinkedIn User Access Level", JR_FX_i18N_DOMAIN),
			'desc' 		=> __("The minimum <i>S2Member</i> user access level a Job Seeker must have to apply for jobs using the \'Apply with LinkedIn\' button.<br><strong>Note:</strong> Free Job Seekers will still see the <i>Aplpy with LinkeIn</i> button but will be redirect if they dont'\t have the minimum acccess level.", JR_FX_i18N_DOMAIN),
			'tip' 		=> __("Choose the minimum use access level for applying to jobs.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_jobs_apply_linkedin_s2member_level',
			'css' 		=> 'min-width:150px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'dis'		=> '',
			'type' 		=> 'select',
			'options'   => array (
				'0' => "Level #0 (free)",
				'1' => "Level #1",
				'2' => "Level #2",
				'3' => "Level #3",
				'4' => "Level #4",
			),
			'std' 		=> '0',
		),

		array( 'name' => jr_fx_upgrade( '_opt_s2member_restrict_access' ) . __('Resume Access', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),	
		
		array(  
			'name' 		=> __("Resume User Access Level", JR_FX_i18N_DOMAIN),
			'desc' 		=> __("The minimum <i>S2Member</i> user access level a Job Seeker must have to access the Resumes features.<br><strong>Note:</strong> Free Job Seekers will still see the <i>Add Resume</i> button but will be automatically redirected if they dont'\t have the minimum acccess level.", JR_FX_i18N_DOMAIN),
			'tip' 		=> __("Choose the minimum use access level for Resume access.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_resume_s2member_level',
			'css' 		=> 'min-width:150px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'dis'		=> '',
			'type' 		=> 'select',
			'options'   => array (
				'0' => "Level #0 (free)",
				'1' => "Level #1",
				'2' => "Level #2",
				'3' => "Level #3",
				'4' => "Level #4",
			),
			'std' 		=> '0',		
		),	
		
		array(  
			'name' 		=> __("Resume Upload Access Level", JR_FX_i18N_DOMAIN),
			'desc' 		=> __("The minimum <i>S2Member</i> user access level a Job Seeker must have to upload Resumes to their profile and use them to apply for Jobs. Only used if the option <i>FXtender</i> <a href='admin.php?page=jr-fx-admin-resumes'>Enable Profile Resumes</a> is enabled.
							   <br><strong>Note:</strong> Job Seekers will see the upload form but will not be able to upload CV/Resume files if not allowed access.
							  ", JR_FX_i18N_DOMAIN),
			'tip' 		=> __("Choose the minimum use access level for Profile Resume Upload. The level access for this option should be equal or superior to the previous option because a user can only upload CV/Resume files after adding an Online Resume.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_resume_upload_s2member_level',
			'css' 		=> 'min-width:150px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'dis'		=> '',
			'type' 		=> 'select',
			'options'   => array (
				'0' => "Level #0 (free)",
				'1' => "Level #1",
				'2' => "Level #2",
				'3' => "Level #3",
				'4' => "Level #4",
			),
			'std' 		=> '0',		
		),			

		array( 'name' => jr_fx_upgrade( '_opt_s2member_restrict_access' ) . __('Job Alerts', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),	
		
		array(  
			'name' 		=> __("Job Alerts User Access Level", JR_FX_i18N_DOMAIN),
			'desc' 		=> __("The minimum <i>S2Member</i> user access level a Job Seeker must have to access the Job Alerts features.<br><strong>Note:</strong> Free Job Seekers will still see the <i>Job Alerts</i> Tab but the subscribe option will be disabled if they dont'\t have the minimum acccess level.", JR_FX_i18N_DOMAIN),
			'tip' 		=> __("Choose the minimum use access level for Job Alerts access.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_job_alerts_s2member_level',
			'css' 		=> 'min-width:150px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'dis'		=> '',
			'type' 		=> 'select',
			'options'   => array (
				'0' => "Level #0 (free)",
				'1' => "Level #1",
				'2' => "Level #2",
				'3' => "Level #3",
				'4' => "Level #4",
			),
			'std' 		=> '0',
		),

		array( 'name' => jr_fx_upgrade( '_opt_s2member_restrict_access' ) . __('Pages', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),	
		
		array(  
			'name' 		=> __('Redirect Page', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('The page to redirect Job Seekers wihout the minimum access level (usually a Membership/Subscription page).', JR_FX_i18N_DOMAIN),
			'tip' 		=> __('The redirect page should be used to inform Job Seekers of the special conditions needed to access certain pages.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_resume_s2member_redirect',
			'css' 		=> 'width:150px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options'	=> $pages,
			'std' 		=> '',
		),	
		
		array(  
			'name' 		=> __('Subscription Page', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('The page where subscribed Job Seekers can view details about their subscription. If a page is selected a new link will be shown on the Dashboard, under <i>Account Options</i>.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __('Choose <i>None</i> if you don\'t want a link to the Subscribers detail page on the Job Seekers Dashboard.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_resume_s2member_member_page',
			'css' 		=> 'width:150px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options'	=> $pages_members,
			'std' 		=> '',
		),			

		array( 'type' => 'tabend'),		

		array( 'type' => 'tab', 'tabname' => __('How To', JR_FX_i18N_DOMAIN) ),
		
		array( 'name' => 'S2Member Integration', 'type' => 'title', 'desc' => '', 'id' => ''),
		
		array(  'name' => '<div class="jr_fx_admin_container">
						  <div class="jr_fx_admin_logo_wrap nopad">'. $s2member_logo . '</div> 		
						  <p>
						   The basics to have S2Member working with FXtender/JobRoller are setting up <a href="'.$s2member_paypal_url.'">PayPal Options</a>, <a href="'.$s2member_paypal_buttons_url.'">Paypal Buttons</a>, <a href="'.$s2member_options_url.'">Membership Levels/Labels</a>, <a href="'.$s2member_options_url.'">Membership Options Page</a>, <a href="'.$s2member_options_url.'">Open Registration</a> and creating your Membership/Subscription pages.
						   Configuring these options will get you started. 
						   </p>
						   </div>',
				 'type' => 'free',						   
			   ),
			   
		array( 'name' => 'General Options', 'type' => 'title', 'desc' => '', 'id' => ''),			   
		
		array(  'name' => '<div class="jr_fx_admin_container"> 
								This <a href="'.$s2member_options_url.'">General Options</a> page controls the most important features of <i>S2Member</i> and it\'s where you set your member levels. 
						   </div>',
				 'type' => 'free',						   
			   ),		
		
		array(  'name' => '<div class="jr_fx_admin_container"> 
						   <h4>. <a href="'.$s2member_options_url.'">Open Registration</a></h4>
							<p>
								Select <span class="jr_fx_highlight_value">Yes ( allow Open Registration; Free Subscribers at Level #0 ).
							</p>
							<p>	
								This option will keep the default <i>JobRoller</i> registration form for Job Listers / Job Seekers. If you choose <span class="jr_fx_highlight_value">No</span> users will not be able to register on your website. Choose <span class="jr_fx_highlight_value">No</span> ONLY if you know what you\'re doing.
							</p>
							<br/>		
							<h4>. <a href="'.$s2member_options_url.'">Membership Levels/Labels</a></h4>
							<p>
							    Here you can label your membership levels. <i>S2Member</i> (Free) allows 4 member levels. You can label <i>levels 1 to 4</i> anything you like but you should label <i>level #0</i> to something like <span class="jr_fx_highlight_value">Job Seeker (Free)</span>. This will 
								allow distinction between the default JobRoller <i>Job Seeker</i> Role. 
								<p><strong>Labels (this labels will replace the default <i>Job Seeker</i> label shown on the Dashboard sidebar):</strong></p>
								<p>- Level #0 - <span class="jr_fx_highlight_value">Job Seeker (Free)</span>
								<p>- Level #1 to 4 - anything you like (<i>examples:</i> <span class="jr_fx_highlight_value">Job Seeker (Pro), Job Seeker (Premium), etc...</span>)</p>
								<p>If you don\'t need all the levels you can leave the labels empty or with their original value.</p>
							</p>	
							<p>	
								A new registered Job Seeker will always start with a Free <i>Job Seeker</i> Role <i>(level #0)</i>.
								You can then control the minimum level needed to access each feature on <i>FXtender</i> <a href="admin.php?page=jr-fx-admin-membership">membership settings page</a>.
							</p>
							<br/>
							<h4>. <a href="'.$s2member_options_url.'">Membership Options Page</a></h4>
							<p>	
								Follow the instructions and choose the membership page where Job Seekers will be able to upgrade their account. You can create this page later and return to select it.
							</p>
							<br/>
							<h4>. <a href="'.$s2member_options_url.'">Member Profile Modifications</a></h4>
							<p>
								The recommended setting for the option <span class="jr_fx_highlight_value">Redirect Members away from the Default Profile Panel?</span> is <span class="jr_fx_highlight_value">No</span>. Choosing other option can mess your theme functionality. 
							</p>
							</div>',
				 'type' => 'free',						   
			   ),
			   		
		array( 'name' => 'PayPal Options', 'type' => 'title', 'desc' => '', 'id' => ''),			   
		
		array(  'name' => '<div class="jr_fx_admin_container"> 
								After setting up your member levels you need to configure <i>S2Member</i> <a href="'.$s2member_paypal_url.'">PayPal Options</a>. To have <i>S2Member</i> take care of all the payments, expiration, renovations, etc, you should fill all the 
								available options. The most important are listed bellow.								
						   </div>',
				 'type' => 'free',						   
			   ),		
			   
		array(  'name' => '<div class="jr_fx_admin_container"> 
							<h4>
								<p>. <a href="'.$s2member_paypal_url.'">PayPal Account Details</a></p>
								<p>. <a href="'.$s2member_paypal_url.'">PayPal IPN Integration</a></p>
								<p>. <a href="'.$s2member_paypal_url.'">PayPal PDT/Auto-Return Integration</a></p>
							</h4>
							<p>
							  Please follow S2Member instructions on how to configure each of these options. 
							  <br/><br/><strong>IMPORTANT:</strong> Please configure ALL these options to make sure that Job Seekers are promoted to the correct level after a successful payment.
							</p>  
						   </div>',
				'type' => 'free',
			 ),		

		array( 'name' => 'PayPal Buttons', 'type' => 'title', 'desc' => '', 'id' => ''),			   
		
		array(  'name' => '<div class="jr_fx_admin_container"> 
								This page is where you\'ll create your member subscription buttons. You\'ll need to create a button for each of your member levels. These buttons can then be used on your Subscription page 
								where Job Seekers will be able to upgrade their account level. Shortcodes and HTML code are automatically generated and just need to be pasted on your subscription page. These instructions cover only the subscription buttons. For 
								the other options available please read <i>S2Member</i> documentation.
						   </div>',
				 'type' => 'free',						   
			   ),		
			   
		array(  'name' => '<div class="jr_fx_admin_container"> 
							<h4>
								<p>. <a href="'.$s2member_paypal_buttons_url.'">PayPal Buttons For Level #1 to #4 Access</a></p>
							</h4>
							<p>
							  You can configure each level button in any way you like it. You just need to add the optional custom capability <span class="jr_fx_highlight_value">+job_seeker</span> on the <span class="jr_fx_highlight_value">Custom Capabilities ( comma-delimited )</span> field in order to avoid breaking <i>JobRoller\'s</i> Job Seeker statistics. 
							  These statistics control how many Job Seekers visit your site daily. If you want to keep this statistic you should add this custom capability.
							</p>  
							<p>
							  These buttons control the Job Seekers level access. After paying, Job Seekers will be automatically given the associated button level access.
							</p>
						   </div>',
				'type' => 'free',
			 ),					 
		
		array( 'name' => 'Subscription Page', 'type' => 'title', 'desc' => '', 'id' => ''),			   
		
		array(  'name' => '<div class="jr_fx_admin_container"> 
							<p>	
								Now that you have the member levels, paypal options and buttons configured you just need to create a <a href="post-new.php?post_type=page">new page</a> to use as the Subscription page where Job Seekers can upgrade their accounts. 
								Just add your previously created buttons shortcode or HTML to this page and explain your Job Seekers about the advantages of becoming a member. They will be redirected to this page each time they try to access a limited feature.
							</p>		
						   </div>',
				 'type' => 'free',						   
			   ),		
			   

		array( 'name' => 'Wraping Up', 'type' => 'title', 'desc' => '', 'id' => ''),			   
		
		array(  'name' => '<div class="jr_fx_admin_container"> 
							<p>
							These are only the basic settings need to have <i>S2Member</i> working with <i>JobRoller</i>. From here you can tweak <i>S2Member</i> to turn your site into a full featured member site.
							Just remember to read <i>S2Member</i> documentation carefully. Importantly, be carefull on the restrictions you make to content or pages, that are not covered by <i>FXtender</i>. <i>Fxtender</i> only controls 
							access to Resumes features. Any other restrictions must be carefully configured and controlled, by you.
							</p>						   		
						   </div>',
				 'type' => 'free',						   
			   ),		

		array( 'name' => 'Troubleshooting', 'type' => 'title', 'desc' => '', 'id' => ''),			   
		
		array(  'name' => '<div class="jr_fx_admin_container"> 
							<p>
							If you are having any errors related to <i>S2Member</i> please read <span class="jr_fx_highlight_value">s2member_fixes.txt</span> on <i>FXtender</i> folder as it contains some fixes to known problems. 
							For other problems related to <i>S2Member</i> please visit <a href="http://www.s2member.com">S2Member website</a> or read the <a href="admin.php?page=ws-plugin--s2member-start">Quick-Start Guide</a> to check you\'ve followed the basic instructions.
							</p>						   		
						   </div>',
				 'type' => 'free',						   
			   ),	
			 
		array( 'type' => 'tabend'),					 
	);
