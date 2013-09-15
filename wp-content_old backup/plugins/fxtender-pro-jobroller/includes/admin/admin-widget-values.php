<?php
/**
 *
 * Admin field data
 * Adapted from the original admin settings code by Appthemes
 * 
 */

//Extended features global vars
global $jr_fx_widget_settings,  $jr_fx_info, $jr_fx_info_tab;

	$jr_fx_widget_settings = array(

		array( 'type' => 'tab', 'tabname' => __('General', JR_FX_i18N_DOMAIN) ),

		$jr_fx_info_tab,
		
		$jr_fx_info ,	

		array( 'name' => __('New Widgets', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),	

		array(  'name' => "<p>New Widgets are available on the <a href='widgets.php'>Widgets</a> page. You can configure them there.</p>",
				'type' => 'free',
			 ),	
		
		array( 'name' => __('Navigation Widget', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),	
		
		array(  
			'name' 		=>  jr_fx_upgrade( '_opt_widget_nav' ) . __('Hide Job Page Nav. Widget',JR_FX_i18N_DOMAIN),
			'desc' 		=> '',
			'tip' 		=> __('Hides the navigation widget (browse by, tags...) on the job page sidebar.',JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_widget_nav',
			'css' 		=> 'min-width:100px;',
			'vis' 		=> '',
			'req'		=> '',
			'min' 		=> '',
			'type' 		=> 'tzCheckbox',
			//'type' 		=> 'select',
			'options' => array(  
				'yes' => __('Yes', JR_FX_i18N_DOMAIN),
				'no'  => __('No', JR_FX_i18N_DOMAIN)
			),
			'std' 		=> 'no',	
		),	
			
		array( 'name' => __('Social Widget', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),	
			
		array(  
			'name' 		=>  jr_fx_upgrade( '_opt_widget_google_plus_id' ) . __('Google+ Profile ID',JR_FX_i18N_DOMAIN),
			'desc' 		=> sprintf( '%s' . __("Sign up for a free <a target='_new' href='%s'>Google+ account</a>.",JR_FX_i18N_DOMAIN), '<div class="googleico"></div>', 'https://plus.google.com' ),
			'tip' 		=> __('Paste your Google+ Profile ID here. It will be used within your website. You must have a Gooogle+ account setup first.',JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_widget_google_plus_id',
			'css' 		=> 'min-width:500px;',
			'vis' 		=> '',
			'req'		=> '',
			'min' 		=> '',
			'type' 		=> 'text',
			'std' 		=> ''
		),	
		

		array(  
			'name' => jr_fx_upgrade( '_opt_widget_youtube_id' ) .  __('YouTube Channel ID', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('You need to set your YoutTube channel ID.',JR_FX_i18N_DOMAIN),
			'tip' 		=> __('Shows the YouTube balloon on JobRoller\'s social widget.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_widget_youtube_id',
			'css' 		=> 'min-width:300px;',
			'vis' 		=> '',
			'req' 		=> '',
			'js' 		=> '',
			'min' 		=> '',
			'type' 		=> 'text',
			'std' 		=> '',
		),

		array(  
			'name' =>  jr_fx_upgrade( '_opt_widget_facebook' ) .  __('Display Facebook on Social Widget', JR_FX_i18N_DOMAIN),
			'desc' 		=> __('You need to set your Facebook Page ID on the JobRoller theme settings.',JR_FX_i18N_DOMAIN),
			'tip' 		=> __('Shows the Facebook balloon on JobRoller\'s social widget.', JR_FX_i18N_DOMAIN),
			'id' 		=> JR_FX_FIELDS_PREFIX.'_opt_widget_facebook',
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

		array( 'type' => 'tabend' ),

);

