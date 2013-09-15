<?php
/**
 *
 * Admin field data
 * Adapted from the original admin settings code by Appthemes
 * 
 */

//Extended features global vars
global $jr_fx_resume_settings, $jr_fx_info, $jr_fx_info_tab;

	$pages[''] = 'Frontpage';
	foreach ( (array_merge ((array)get_pages ())) as $page ) {
		$pages[ $page->ID ] = $page->post_title;
	}

	$jr_fx_resume_settings = array(

		array( 'type' => 'tab', 'tabname' => __('Resumes', JR_FX_i18N_DOMAIN) ),

		$jr_fx_info_tab,
		
		$jr_fx_info ,

		array( 'name' => jr_fx_upgrade( '_opt_resume_list_visibility' ). __('Resume Options', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),			
		
		array(  
			'name' 		=> __('Resume Listings Visibility', JR_FX_i18N_DOMAIN),
			'desc' 		=> sprintf(" 
							  <strong>Paid Job</strong> Visible only to Job Listers with paid jobs<br/>
							  <strong>Posted Job (Any Status)</strong> Visible to Job Listers with at least one submitted job listing (published, pending or expired)<br/>
							  <strong>Posted Job (Published)</strong> Visible only to Job Listers with active job listings (live).<br/><br/>".
							  __("<strong>Important: </strong> You must set the <i>Resume Listings Visibility</i> option to <strong>Job Listers Only</strong> on your JobRoller %s for this feature to work."),
							  __('<a href="admin.php?page=settings#tab3">Resume Options</a>
							',JR_FX_i18N_DOMAIN)),
			'tip' 		=> __( 'Lets you define who can browse through submitted resumes. If the user trying to browse the resumes does not meet the criteria set here it will be redirect to pre-set page.', JR_FX_i18N_DOMAIN ),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_resume_list_visibility',
			'css' 		=> 'width:200px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'paid_all' 		=> __('Posted Job (Any Status)', JR_FX_i18N_DOMAIN),
				'paid_live'	=> __('Posted Job (Published)', JR_FX_i18N_DOMAIN),
				'jobpack' 	=> __('Paid Job', JR_FX_i18N_DOMAIN),
				'jr' => __('JobRoller settings',JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'jr',	
			'class'		=> (!jr_fx_has_perms_to( '_opt_resume_list_visibility' )?'jr_fx_locked':''),
		),					
		
		array(  
			'name' 		=> __('Resume Visibility', JR_FX_i18N_DOMAIN),
			'desc' 		=> sprintf(" 
							  <strong>Paid Job</strong> Visible only to Job Listers with active Job Packs<br/>
							  <strong>Posted Job (Any Status)</strong> Visible to Job Listers with at least one submitted job listing (published or expired)<br/>
							  <strong>Posted Job (Published)</strong> Visible only to Job Listers with active job listings (live).<br/><br/>".
							  __("<strong>Important: </strong> You must set the <i>Resume Visibility</i> option to <strong>Job Listers Only</strong> on your JobRoller %s for this feature to work."),
							  __('<a href="admin.php?page=settings#tab3">Resume Options</a>				
							',JR_FX_i18N_DOMAIN)),
			'tip' 		=> __( 'Lets you define who can view through submitted resumes. If the user trying to view the resumes does not meet the criteria set here it will be redirect to pre-set page.', JR_FX_i18N_DOMAIN ),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_resume_visibility',
			'css' 		=> 'width:200px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'paid_all' 		=> __('Posted Job (Any Status)', JR_FX_i18N_DOMAIN),
				'paid_live'	=> __('Posted Job (Published)', JR_FX_i18N_DOMAIN),
				'jobpack' 	=> __('Paid Job', JR_FX_i18N_DOMAIN),
				'jr' => __('JobRoller settings',JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'jr',	
			'class'		=> (!jr_fx_has_perms_to( '_opt_resume_visibility' )?'jr_fx_locked':''),
		),		

		array(  
			'name' 		=> __('Call to action Page', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('The page to redirect Job Listers that try to browse or View Resumes without permissions.<br/><strong>Note: </strong> Page redirection will only work for any of the options above if you DON\'T select <strong><i>JobRoller Settings</i></strong>.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __('Select a page that informs Job Listers of the special conditions needed to access resumes.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_resumes_noaccess_redirect',
			'css' 		=> 'width:150px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options'	=> $pages,
			'std' 		=> '',
		),			
		
		array( 'name' => jr_fx_upgrade( '_opt_resume_list_visibility' ). __('Profile Resumes', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),					
		
		array(  
			'name' 		=> __('Enable Profile Resumes', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('Job Seekers can attach files to their Online Resumes on their Dashboard and use them to apply for Jobs. Only available for Job Seekers with at least one Online Resume.<br>
							   <br/><strong>Important:</strong> You should use a <strong>robots.txt</strong> file to block search engine crawlers from indexing Resume/CV files. For more information check the <a href="admin.php?page=jr-fx-admin-sys-info">System Info</a> page.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __("If set to <i>Yes</i> Job Seekers can upload Resumes and keep them stored on their profile to apply for Jobs.", JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_resume_upload_cvs',
			'css' 		=> 'width:50px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'tzCheckbox',
			'options' => array(  
				'yes' => __('Yes', JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'no',
		),	
		
		array(  
			'name' 		=> __('Max. Number Profile Resumes', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('The maximum number of Resume files a Job Seeker can upload. Leave empty to allow Job Seekers to upload one Resume file for each Online Resume they add.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __('This will limit the number of Resumes a user can upload on their profile.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_resume_upload_cvs_max',
			'css' 		=> 'width:50px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'text',
			'std' 		=> '',
		),		
		
		array(  
			'name' 		=> __('Enable Selectable Profile Resumes', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('If active, Job Seekers have the option to apply for Jobs with their uploaded Profile Resumes.<br/><strong>Note: </strong>Only used if you enable <i>Profile Resumes</i>.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __('Profile Uploaded Resumes will be listed on the <i>Apply Job</i> form as a new option.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_resume_upload_attach', 
			'css' 		=> 'width:50px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'tzCheckbox',
			'options' => array(  
				'yes' => __('Yes', JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'no',
		),			
		
		### NEW!!!
		
		array(  
			'name' 		=> __('Enable Resumes Attachments Download', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('If active, Job Listers will be able to download the related CV (if attached) on single Resume pages. <br/><strong>Note: </strong> Any Job Lister with <i>Resume View</i> access will be able to download CV file attachments.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __('A list with the attached CV\'s files will appear at the top or bottom of each Resume.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_opt_resume_cv_download', 
			'css' 		=> 'width:250px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'select',
			'options' => array(  
				'header' => __('Yes (Show on Resume Header)', JR_FX_i18N_DOMAIN),
				'footer' => __('Yes (Show on Resume Footer)', JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN),
			),
			'std' 		=> 'no',
		),					
		
		
		array( 'type' => 'tabend'),					
		
	);	
		
