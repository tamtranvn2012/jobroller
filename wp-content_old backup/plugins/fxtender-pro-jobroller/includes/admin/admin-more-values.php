<?php
/**
 *
 * Admin field data
 * Adapted from the original admin settings code by Appthemes
 * 
 */

//Extended features global vars
global $jr_fx_more_settings, $jr_fx_info, $jr_fx_info_tab;
	
	$me = jr_fx_get_me();

	$jr_fx_more_settings = array(
		
		array( 'type' => 'tab', 'tabname' => __('More', JR_FX_i18N_DOMAIN) ),

		$jr_fx_info_tab,

		$jr_fx_info,

		(JR_FX_VERSION == JR_FX_VER_FREE?
			array( 'name' => __('Upgrade to PRO', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => '') 
		:array( 'name' =>'','type'=>'')),
			
		(JR_FX_VERSION == JR_FX_VER_FREE?		
			array(  'name' => '<div class="jr_fx_admin_container">
							   <h4>Why Upgrade to PRO?</h4> 
							   <p>	- One time payment</br>
									- Exclusive Features<br/>
									- Exclusive Widgets
									<p>+ Unlimited updates!</p>
							   </div>',
					'type' => 'free',
				 ) 
		:array( 'name' =>'','type'=>'' )),
		
		array( 'name' => __('More Features', JR_FX_i18N_DOMAIN), 'type' => 'title', 'desc' => '', 'id' => ''),
		
		array(  'name' => sprintf( '<div class="jr_fx_admin_container">
						   <h4>Want more features?</h4>'.
						   '<p>You can post your suggestions or questions on <a href="%s">AppThemes FXtender Pro support forum</a>. Popular suggestions will eventually be added. I\'ll also try to come up with my own new ideas and features.</p>
						   <h4><strong>Important</strong></h4>
						   <p>Some of the features rely heavily on JobRoller\'s theme styles. If you radically changed your theme structure or CSS style class names, some of the features might not work properly.</p>
						   <h4><strong>Notes on child themes</strong></h4>
						   <p>If you\'re having trouble with a specific feature and you\'re using a 3d party child theme, you should try using one of the default JR themes. If the problems persists with the default theme please post the problems on the support forum. Otherwise, please contact the child theme author.</p>
						   </div>', 'http://forums.appthemes.com/fxtender-pro/' ),
				'type' => 'free',
			 ),

		array( 'type' => 'tabend'),
		
		array( 'type' => 'tab', 'tabname' => __('About', JR_FX_i18N_DOMAIN) ),
		
		array( 'name' => 'About the Author', 'type' => 'title', 'desc' => '', 'id' => ''),
		
		array(  'name' => '<div class="jr_fx_admin_container">' . 
						  '<div class="jr_fx_admin_thumb">'. $me['avatar'] . '</div> 
						  <br/>
						   <p>Hello, I\'m Bruno! A freelance Web Developer and Wordpress Ninja :)</>	
						   <p>After developing <a href="'.$me['elocriativo'] .'" title="'.$me['elocriativo'] .'">Elo Criativo</a> using the <a href="'.JR_FX_JOBROLLER_URL.'" title="'.JR_FX_JOBROLLER.'">'.JR_FX_JOBROLLER.'</a> theme I had several requests to
							 develop similar or new features from other users. During this time and while I was working on 
							 some hand made exclusive features for <a href="'.$me['elocriativo'] .'" title="'.$me['elocriativo'] .'">Elo Criativo</a> I came up with the ideia of pluggable features, and <strong>FXtender</strong> was born.</p> 
							 <p>Seeing the need that most users have to add extra functionality and the time it can take for new releases to come out I	decided to release it to the public.
							 With <strong>FXtender</strong> you can now plugg features that otherwise would need a child theme or hacking into the theme core.</p> 
							 <p>I\'ve decided to release 2 versions of <strong>FXtender</strong>. A <strong>Pro</strong> and a <strong>Lite</strong> version. Both add new features and options to <a href="'.JR_FX_JOBROLLER_URL.'" title="'.JR_FX_JOBROLLER.'">'.JR_FX_JOBROLLER.'</a>. 
							 While the <strong>Lite</strong> version is more limited on the number of features, the <strong>Pro</strong> version will give you plenty of options to personalize even more your favourite job portal.</p>					 
							 <br/><p>I\'ll try to keep both versions updated with regular releases and new features. Enjoy!</p>
							 </p>
						   </div>',
				'type' => 'free',
			 ),	
		
		array( 'name' => 'My Other Plugins', 'type' => 'title', 'desc' => '', 'id' => ''),
		
		array(  'name' => '<div class="jr_fx_admin_container">' . 
						  '	<p><a href="'.$me['plugins']['custom_posts']['url'] .'" title="'.$me['plugins']['custom_posts']['name'] .'">'.$me['plugins']['custom_posts']['name'].'</a></p> 
							<p>'.$me['plugins']['custom_posts']['desc'] .'</p>
						   </div>',
				'type' => 'free',
			 ),

		array( 'type' => 'tabend'),

	);

