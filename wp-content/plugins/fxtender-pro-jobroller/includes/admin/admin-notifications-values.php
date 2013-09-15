<?php
/**
 *
 * Admin field data
 * Adapted from the original admin settings code by Appthemes
 * 
 */

//Extended features global vars
global $jr_fx_notification_settings, $jr_fx_info, $jr_fx_info_tab;

	$jr_fx_notification_settings = array(

		array( 'type' => 'tab', 'tabname' => __('Notices', JR_FX_i18N_DOMAIN) ),

		$jr_fx_info_tab,

		$jr_fx_info,

		array( 'name' => __('Notices', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),

		array(  'name' => 'Here you can customize the notification messages used by some of FXtender features. You need to activate the respective feature first.',
				'type' => 'free',
			 ),
/*
		array( 'name' => __('First # Job(s) Free', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),

		array(  
			'name' 		=> jr_fx_upgrade( '_text_notice_free_offer' ) . __('First # Job(s) Free Message', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('Used only if the option \'First # Job(s) Free\' is set.', JR_FX_i18N_DOMAIN).'<br/><br/>'. __('You can use the following variables:<br/>Total Jobs Published: <strong>%publishedjobs%</strong> <br/>Total Free Jobs: <strong>%totalfreejobs%</strong> <br/>Available Free Jobs: <strong>%freejobsleft%</strong><br/><br/>Each variable MUST have the percentage signs wrapped around it with no spaces.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __('The message you want to show to the user after he posts a job and is entitled to a free job.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_text_notice_free_offer',
			'css'  		=> 'width:550px;height:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'textarea',
			'std' 		=> __('Your job was submitted. You have %freejobsleft% job(s) left for free.',JR_FX_i18N_DOMAIN)
		),
*/
		array( 'name' => __('First # Job(s) Moderated', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),

		array(  
			'name' 		=> jr_fx_upgrade( '_text_notice_min_auto_pub' ) . __('First # Job(s) Moderated Message', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('Used only if the option \'First # Job(s) Moderated\' is set.', JR_FX_i18N_DOMAIN) .'<br/><br/>'.__('You can use the following variables:<p>Total Jobs Published: <strong>%publishedjobs%</strong> <br/>Minimum Jobs: <strong>%minpublishedjobs%</strong><br/><br/>Each variable MUST have the percentage signs wrapped around it with no spaces.', JR_FX_i18N_DOMAIN),
			'tip' 		=> __('The message you want to show to the user when they submit a job but the minimum published jobs needed is inferior to the specified value.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX . '_text_notice_min_auto_pub',
			'css'  		=> 'width:550px;height:100px;',
			'vis' 		=> '',
			'req' 		=> '',
			'min' 		=> '',
			'type' 		=> 'textarea',
			'std' 		=> __('You job was sent for approval. You have %publishedjobs% published jobs. You need a minimum of %minpublishedjobs% job(s) published to skip moderation.','jrf')
		),

		array( 'type' => 'tabend'),

		array( 'type' => 'tab', 'tabname' => __('Email', JR_FX_i18N_DOMAIN) ),

		$jr_fx_info_tab,

		$jr_fx_info,

		array( 'name' => __('Email Notices', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),

		array( 'name' => 'Here you can customize the email messages used by some of FXtender features. You need to activate the respective feature first.',
				'type' => 'free',
			),

		array( 'name' => __('Email Signature', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),	

		array( 'name' => jr_fx_upgrade( '_text_email_signature' ) . __('Email Signature', JR_FX_i18N_DOMAIN),
			   'desc' => __('Plain text only. You may use the following variables within the email body and/or subject line.<br/><br/><strong>%siteurl%</strong> - prints out your website url<br/><strong>%blogname%</strong> - prints out your site name<br/><br/>Each variable MUST have the percentage signs wrapped around it with no spaces.', JR_FX_i18N_DOMAIN),
			   'tip'  => __('Enter the email signature you want to use for all the emails (excluding Admin notifications) sent from your website, including job applications. Use it to promote your website when candidates apply for jobs.', JR_FX_i18N_DOMAIN),
			   'id'   => JR_FX_FIELDS_PREFIX.'_text_email_signature',
			   'css'  => 'width:550px;height:250px;',
			   'vis'  => '',
			   'type' => 'textarea',
			   'req'  => '',
			   'min'  => '',
			   'std'  => '',
		),
		
		array( 'type' => 'tabend')
		
	);

