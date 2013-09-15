<?php
/**
 *
 * Admin field data
 * Adapted from the original admin settings code by Appthemes
 * 
 */

//Extended features global vars
global $jr_fx_integration_settings, $jr_fx_info, $jr_fx_info_tab;
	
	$jr_fx_integration_settings = array(
		
		array( 'type' => 'tab', 'tabname' => __('LinkedIn', JR_FX_i18N_DOMAIN) ),

		$jr_fx_info_tab,
		
		$jr_fx_info ,
		
		array(  'name' => '<img src="'.JR_FX_PLUGIN_URL.'images/linkedin-logo.jpg" />', 'type' 		=> 'logo' ),
		
		array( 'name' => __('Overview', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),	
		
		array(  'name' => '<div class="jr_fx_admin_container">		
						   <p><i>FXtender</i> integrates some of <i>LinkedIn</i> Javascript API features. You\'ll need an API Key and to set your domain name on <a href="https://www.linkedin.com/secure/developer" target="_blank"><i>LinkedIn</i></a> to use these features. The domain name can be set to <strong>localhost</strong> <span class="jr_fx_highlight_value">e.g: http://localhost</span> for local test environments.
						   <p>LinkedIn Developer Application details, example:</p>
						   <p><span class="jr_fx_highlight_value"><strong>API Key:</strong> my_API_Key_123</span></p>
						   <p><span class="jr_fx_highlight_value"><strong>JavaScript API Domain:</strong> http://www.my-website.com</span> or <span class="jr_fx_highlight_value">http://localhost</span></p>
						   <p>If you\'re having problems integrating this features please make sure you\'ve configured your domain correctly and your API Key is correct.</p>
						   <p>More info about the <i>LinkedIn</i> API can be found <a href="https://developer.linkedin.com/documents/getting-started-javascript-api" target="_blank">here</a>.</p>
						   </div>',
				'type' => 'free',
			 ),

		array( 'name' => __('Settings', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),	
			
				
		array(    'name' => __('API Key', JR_FX_i18N_DOMAIN),
				'desc' => __('Your <i>LinkedIn</i> API key. You need an API Key <a href="https://www.linkedin.com/secure/developer" target="_blank">LinkedIn</a>.',JR_FX_i18N_DOMAIN),
				'tip' => __('Your <i>LinkedIn</i> API Key gives you access to the <i>LinkedIn</i> API features.',JR_FX_i18N_DOMAIN),
				'id' => JR_FX_FIELDS_PREFIX.'_text_integration_linkedin_key',
				'css' => 'min-width:350px;',
				'type' => 'text',
				'req' => '',
				'min' => '',
				'std' => '',
				'vis' => ''),
				
		array(    'name' => __('Theme Color', JR_FX_i18N_DOMAIN),
				'desc' => __('A hexadecimal CSS color value to theme the <i>LinkedIn</i> features. You should prefix the color value with #. <p><span class="jr_fx_highlight_value">Example: #438ccb</span></p>',JR_FX_i18N_DOMAIN),
				'tip' => __('Choose the color that best suits your website to visually integrate <i>LinkedIn</i> features.',JR_FX_i18N_DOMAIN),
				'id' => JR_FX_FIELDS_PREFIX.'_text_integration_linkedin_color',
				'css' => 'min-width:150px;',
				'type' => 'text',
				'req' => '',
				'min' => '',
				'std' => '',
				'vis' => ''),	
				
			 
		array( 'name' =>  jr_fx_upgrade( '_opt_integration_linkedin_apply' ) . __('Apply with <i>LinkedIn</i>', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),	
				
		array(  'name' => '<div class="jr_fx_admin_container">		
						   <p>This feature works by submitting job applications to the Job Lister email using <i>LinkedIn</i> profiles. When aplying using this button, Job Seekers can instantly sign in to <i>LinkedIn</i> on a popup window and submit their applications. 
							The Job Lister will instantly receive the application and get access to the Applicant <i>LinkedIn</i> Profile. Applicants also receive a copy of the application.
						   <p>More info about this feature can be found <a href="https://developer.linkedin.com/plugins/apply" target="_blank">here</a>.</p>
						   </div>',
				'type' => 'free',
			 ),
			 
		array(  
			'name' => __("Enable 'Apply with <i>LinkedIn</i>'", JR_FX_i18N_DOMAIN),
			'desc' 		=> __("If enabled, Job Seekers will be able to apply using <i>LinkedIn</i>.", JR_FX_i18N_DOMAIN),
			'tip' 		=> __("The button <i>Apply with LinkedIn</i> will be visible on the Job page footer for members and/or visitors. Selecting 'Optional' will display a checkbox field that allows job listers to enable/disable the button.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_integration_linkedin_apply',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'optional' => __('Optional (by Job)', JR_FX_i18N_DOMAIN),
				'members' => __('Members', JR_FX_i18N_DOMAIN),
				'all' => __('All', JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN)
			),
			'std' 		=> 'no',		
		),
				

		array(  
				'name' => __("Button Size", JR_FX_i18N_DOMAIN),
				'desc' 		=> __("<i>Apply with LinkedIn</i> button size.", JR_FX_i18N_DOMAIN),
				'tip' 		=> __("Select the button size that best suits your theme.", JR_FX_i18N_DOMAIN),
				'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_integration_linkedin_apply_size',
				'css' 		=> 'min-width:100px;',
				'vis' 		=> '',
				'req' 		=> '',
				'js' 		=> '',
				'min' 		=> '',
				'type' 		=> 'select',
				'options' => array(  
					'normal' => __('Normal', JR_FX_i18N_DOMAIN),
					'medium'  => __('Medium', JR_FX_i18N_DOMAIN)
				),
				'std' 		=> 'medium',		
			),	
			
		array(  
				'name' => __("Show Button Text", JR_FX_i18N_DOMAIN),
				'desc' 		=> __("Show the call to action <i>LinkedIn</i> text.", JR_FX_i18N_DOMAIN),
				'tip' 		=> __("To increase the number of people who apply for a position, <i>LinkedIn</i> adds a call to action below the 'Apply with <i>LinkedIn</i>' button. You can hide this text.", JR_FX_i18N_DOMAIN),
				'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_integration_linkedin_apply_text',
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

		array( 'name' =>  jr_fx_upgrade( '_opt_jobs_linkedin_profile' ) . __('Inline <i>LinkedIn</i> Profiles', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),	
							 
		array(  
			'name' => __("Enable on Profiles", JR_FX_i18N_DOMAIN),
			'desc' 		=> __("Show Job Seeker's <i>LinkedIn</i> profile inline, on their Profile page. A valid <i>LinkedIn</i> URL Profile is required for this feature to work.", JR_FX_i18N_DOMAIN),
			'tip' 		=> __("If enabled, <i>LinkedIn</i> profiles will be shown inline below the <i>LinkedIn</i> URL link on members Profile pages.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_integration_linkedin_profile',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'members' => __('Members', JR_FX_i18N_DOMAIN),			
				'job_seeker' => __('Job Seekers', JR_FX_i18N_DOMAIN),
				'job_lister' => __('Job Listers', JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN)
			),
			'std' 		=> 'no',		
		),	

		array(  
			'name' => __("Enable on Resumes", JR_FX_i18N_DOMAIN),
			'desc' 		=> __("Show Job Seeker's <i>LinkedIn</i> profile inline on their Resume pages. A valid <i>LinkedIn</i> URL Profile is required for this feature to work.", JR_FX_i18N_DOMAIN),
			'tip' 		=> __("If enabled, Job Seeker's <i>LinkedIn</i> profiles will be shown inline on Resume pages headers.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_integration_linkedin_profile_resume',
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
			'name' => __("Enable on Job Pages", JR_FX_i18N_DOMAIN),
			'desc' 		=> __("Show Job Lister's <i>LinkedIn</i> profile inline on their Job pages. A valid <i>LinkedIn</i> URL Profile or job listing Company name are required for this feature to work.", JR_FX_i18N_DOMAIN),
			'tip' 		=> __("If enabled, Job Lister's <i>LinkedIn</i> profiles will be shown inline on Job pages headers. You can use the job Company name to retrieve it's LinkedIn profile or use the existing job lister's profile LinkedIn URL. Please note that the company name must match the LinkedIn Company name for this feature to work.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_integration_linkedin_profile_job',
			'css' 		=> 'min-width:150px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'company' => __('Yes (Using Company Name)', JR_FX_i18N_DOMAIN),
				'profile' => __('Yes (Using Profile LinkedIn URL)', JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN)
			),
			'std' 		=> 'no',		
		),			
						
		array( 'type' => 'tabend'),			
		
		array( 'type' => 'tab', 'tabname' => __('Indeed (JR 1.4.x or less)', JR_FX_i18N_DOMAIN) ),

		$jr_fx_info_tab,	
		
		$jr_fx_info ,	
		
		array(  'name' => '<img src="'.get_bloginfo('template_directory').'/images/indeed-lg.png" />', 'type' 		=> 'logo' ),
		
		array( 'name' => __('Job Page', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),	
		
		array(  
			'name' => jr_fx_upgrade( '_opt_jobs_indeed_apply_button' ) . __("Hide 'Apply Online' on Indeed Jobs", JR_FX_i18N_DOMAIN),
			'desc' 		=> __("This option has priority over any Show/Hide <i>Apply Online</i> button options because users cannot apply for Indeed jobs directly.", JR_FX_i18N_DOMAIN),
			'tip' 		=> __("Hides the <i>Apply Online</i> button for Indeed Jobs.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_jobs_indeed_apply_button',
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
	);
