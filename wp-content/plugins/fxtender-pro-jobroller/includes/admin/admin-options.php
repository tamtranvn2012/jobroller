<?php

# admin menu
define('JR_FX_MENU_ORDER', '4.1');
define('JR_FX_FAVICON', JR_FX_PLUGIN_URL . 'images/job_icon.png');

add_action( 'admin_menu','jr_fx_admin', 15 );
add_action( 'admin_init','jr_fx_maybe_clear_cache', 15 );

/**
 *  FXtender menu
 */
function jr_fx_admin() {
	add_menu_page( 'FXtender', 'FXtender ' . JR_FX_VERSION, 'manage_options','jr-fx-admin-settings' , 'jr_fx_settings', JR_FX_FAVICON, JR_FX_MENU_ORDER);
	add_submenu_page('jr-fx-admin-settings', __('General',JR_FX_i18N_DOMAIN), __('General',JR_FX_i18N_DOMAIN), 'manage_options', 'jr-fx-admin-settings', 'jr_fx_settings' );
	add_submenu_page('jr-fx-admin-settings', __('Resumes',JR_FX_i18N_DOMAIN), __('Resumes',JR_FX_i18N_DOMAIN), 'manage_options', 'jr-fx-admin-resumes', 'jr_fx_resumes' );
	add_submenu_page('jr-fx-admin-settings', __('Membership',JR_FX_i18N_DOMAIN), __('Membership',JR_FX_i18N_DOMAIN), 'manage_options', 'jr-fx-admin-membership', 'jr_fx_membership' );
	add_submenu_page('jr-fx-admin-settings', __('Integration',JR_FX_i18N_DOMAIN), __('Integration',JR_FX_i18N_DOMAIN), 'manage_options', 'jr-fx-admin-integration', 'jr_fx_integration' );
	add_submenu_page('jr-fx-admin-settings', __('Notifications',JR_FX_i18N_DOMAIN), __('Notifications',JR_FX_i18N_DOMAIN), 'manage_options', 'jr-fx-admin-notifications', 'jr_fx_notifications' );
	add_submenu_page('jr-fx-admin-settings', __('Widgets',JR_FX_i18N_DOMAIN), __('Widgets',JR_FX_i18N_DOMAIN), 'manage_options', 'jr-fx-admin-widgets', 'jr_fx_widgets' );
	add_submenu_page('jr-fx-admin-settings', __('More',JR_FX_i18N_DOMAIN), __('More',JR_FX_i18N_DOMAIN), 'manage_options', 'jr-fx-admin-more', 'jr_fx_more' );
	add_submenu_page('jr-fx-admin-settings', __('System Info',JR_FX_i18N_DOMAIN), __('System Info',JR_FX_i18N_DOMAIN), 'manage_options', 'jr-fx-admin-sys-info', 'jr_fx_system_info');
}

function jr_fx_maybe_clear_cache() {
	global $wpdb;

	if ( isset($_POST['jr_fx_opt_listings_preview_clear_cache']) ) {

		$trans_1 = '_transient_' . JR_FX_FIELDS_PREFIX.'-thumb-';
		$trans_2 = '_transient_' . JR_FX_FIELDS_PREFIX.'-preview-';

		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%".$trans_1."%' OR option_name LIKE '%".$trans_2."%' " );

		$_POST['jr_fx_opt_listings_preview_clear_cache'] = '';
	}
}

function jr_fx_admin_settings_form( $title, $settings ) {
	jr_fx_update_options( $settings );
?>
	<div class="wrap jobroller">
			<div class="icon32" id="icon-tools"><br/></div>
			<h2><?php echo 'FXtender ' . __($title, JR_FX_i18N_DOMAIN); ?></h2>

				<form method="post" id="mainform" action="">

					<?php jr_fx_admin_fields($settings); ?>

					<p class="submit bbot"><input name="save" class="button-primary" type="submit" value="<?php _e('Save changes',JR_FX_i18N_DOMAIN) ?>" /></p>
					<input name="submitted" type="hidden" value="yes" />
		</form>
	</div>
<?php
}

// main settings admin page
function jr_fx_settings() {
	//JobRoller Extended features global vars
	global $jr_fx_options_settings;
	
	jr_fx_admin_settings_form( 'General Settings', $jr_fx_options_settings );

}

// resumes admin page
function jr_fx_resumes() {
	//JobRoller Extended features global vars
	global $jr_fx_resume_settings;
	
	jr_fx_admin_settings_form( 'Resume Options', $jr_fx_resume_settings );

}

// membership features admin page
function jr_fx_membership() {
	//JobRoller Extended features global vars
	global $jr_fx_membership_settings;

	jr_fx_admin_settings_form( 'Membership Settings', $jr_fx_membership_settings );
}

// notification features admin page
function jr_fx_notifications() {
	//JobRoller Extended features global vars
	global $jr_fx_notification_settings;
	
	jr_fx_admin_settings_form( 'Notifications Settings', $jr_fx_notification_settings );

}

// integration features admin page
function jr_fx_integration() {
	//JobRoller Extended features global vars
	global $jr_fx_integration_settings;
	
	jr_fx_admin_settings_form( 'Integration Settings', $jr_fx_integration_settings );
}

// widget features admin page
function jr_fx_widgets() {
	//JobRoller Extended features global vars
	global $jr_fx_widget_settings;
	
	jr_fx_admin_settings_form( 'Widget Settings', $jr_fx_widget_settings );	
}

// system information page
function jr_fx_system_info() {
?>

	<div class="wrap jobroller">
		<div class="icon32" id="icon-options-general"><br/></div>
		<h2><?php _e('System Info', JR_FX_i18N_DOMAIN) ?></h2>

		<script type="text/javascript">
		jQuery(function() {
		    jQuery("#tabs-wrap").tabs({
		        fx: {
		            opacity: 'toggle',
		            duration: 200
		        }
		    });
		});
		</script>
        <div id="tabs-wrap">
            <ul>
            	<li><a href="#tab1">PHP Info</a></li>
            </ul>
			<div id="tab1">
                <table class="widefat fixed" style="width:850px;">

                    <thead>
                        <tr>
                            <th scope="col" width="200px"><?php _e('PHP Info',JR_FX_i18N_DOMAIN)?></th>
                            <th scope="col">&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="titledesc"><?php _e('PHP CURL<br/>(used by some gateways)',JR_FX_i18N_DOMAIN)?></td>
                            <td class="forminp">
							<?php
								if  ( in_array  ('curl', get_loaded_extensions())) :
									 echo '<span style="color:green">' . __('CURL extension is loaded.', JR_FX_i18N_DOMAIN). '</span>';
								else :
									echo '<span style="color:red">' . __('CURL extension is not loaded!<br/>Some gateways need CURL to work properly. If you experience problems, please try to load this extension. Check you <strong>PHP.ini</strong> file.', JR_FX_i18N_DOMAIN). '</span>';
								endif;
							?>			
							</td>
                        </tr>
                        <tr>
                            <td class="titledesc"><?php _e('File Permissions',JR_FX_i18N_DOMAIN)?></td>
                            <td class="forminp">
							<?php
								$errors = jr_fx_check_file_perms( $echo = FALSE );
								
								if ( !$errors ) :
									 echo '<span style="color:green">' . __('File permissions are good.', JR_FX_i18N_DOMAIN). '</span>';
								else :
									echo '<span style="color:red">' . $errors . '</span>';
								endif;
							?>
							</td>
                        </tr>
                        <tr>
                            <td class="titledesc" colspan=2><?php _e('Please also check all other system information using JobRoller\'s <a href="admin.php?page=sysinfo">' . __('System Info', JR_FX_i18N_DOMAIN) . '</a> page.' ,JR_FX_i18N_DOMAIN)?></td>
                        </tr>
						
                    <thead>
                        <tr>
                            <th scope="col" width="200px"><?php _e('Security',JR_FX_i18N_DOMAIN)?></th>
                            <th scope="col">&nbsp;</th>
                        </tr>
                    </thead>
					<tr>
						<td class="titledesc"><?php _e('Robots.txt',JR_FX_i18N_DOMAIN)?></td>
						<td class="forminp">
						<?php
							$errors = jr_fx_check_robots();

							if ( !$errors ) :
								 echo '<span style="color:green">' . __('<strong>Robots.txt</strong> was found. Please check that you are using the <strong>\'Disallow: /wp-content/\'</strong> option to block search engines from indexing your Resume/CV files.', JR_FX_i18N_DOMAIN). '</span>';
							else :
								echo '<span style="color:red">' . $errors . '<br/>' . 
									 __('Your \wp-content folder may be visible to search engine crawlers. If you\'re using the \'Resume Upload\' feature, you should have a <strong>robots.txt</strong> file on your root folder to block indexing. 
										You should create a <strong>robots.txt</strong> file or copy the <strong>/extras/robots.txt</strong> file bundled with <i>FXtender</i> to your root folder.</span>',JR_FX_i18N_DOMAIN);								
							endif;
						?>			
						</td>
					</tr>
				</tbody>

				</table>
			</div>
<?php
}

// more features admin page
function jr_fx_more() {
	//JobRoller Extended features global vars
	global $jr_fx_more_settings;

	jr_fx_admin_settings_form( 'More Settings', $jr_fx_more_settings );
}

// s2member instuctions admin page
function jr_fx_s2member_help() {
	//JobRoller Extended features global vars
	global $jr_fx_s2member_help;

	jr_fx_admin_settings_form( 'S2Member/JobRoller Integration', $jr_fx_s2member_help );
}

// update all the admin options on save
function jr_fx_update_options($options) {

    if(isset($_POST['submitted']) && $_POST['submitted'] == 'yes') {

        foreach ($options as $value) {

            if(isset($value['id']) && isset($_POST[$value['id']])) {
                update_option($value['id'], jr_fx_clean($_POST[$value['id']]));
            } else {
                @delete_option($value['id']);
            }
        }

        echo '<div id="message" class="updated fade"><p><strong>'.__('Your settings have been saved.', JR_FX_i18N_DOMAIN).'</strong></p></div>';

    }
}

// add options to JR options global object
function jr_fx_update_options_legacy() {
	global $jr_options;

	$jr_options->set( 'gateways', _jr_fx_options_legacy('gateways') );
}

// retrieve default options based on legacy settings
function _jr_fx_options_legacy( $option ) {

	$gateways = $gateway = array();

	if ( 'yes' == get_option( 'jr_fx_opt_gateway_google' ) ) {
		$gateway['google-wallet'] = array(
			'jr_fx_merchant_id' 	=> get_option( 'jr_fx_text_gateway_google_id' ),
			'jr_fx_merchant_key' 	=> get_option( 'jr_fx_text_gateway_google_key' ),
			'jr_fx_ipn' 			=> 'yes' == get_option( 'jr_fx_opt_gateway_google_ipn' ) ? 'on' : '',
			'jr_fx_enable_sandbox' 	=> 'sandbox' == get_option( 'jr_fx_opt_gateway_server' ) ? 'on' : '',
		);
	}

	if ( 'yes' == get_option( 'jr_fx_opt_gateway_2checkout' ) ) {
		$gateway['2checkout'] = array(
			'jr_fx_account_number' 	=> get_option( 'jr_fx_text_gateway_2checkout_id' ),
			'jr_fx_secret_word' 	=> get_option( 'jr_fx_text_gateway_2checkout_key' ),
			'jr_fx_enable_sandbox' 	=> 'sandbox' == get_option( 'jr_fx_opt_gateway_server' ) ? 'on' : '',
		);
	}

	if ( 'yes' == get_option( 'jr_fx_opt_gateway_authorize' ) ) {
		$gateway['authorize-net'] = array(
			'jr_fx_login_id' 	=> get_option( 'jr_fx_text_gateway_authorize_id' ),
			'jr_fx_transaction_key' 	=> get_option( 'jr_fx_text_gateway_authorize_key' ),
			'jr_fx_enable_sandbox' 	=> 'sandbox' == get_option( 'jr_fx_opt_gateway_server' ) ? 'on' : '',
		);
	}

	$gateways['enabled'] = array( 'bank-transfer' => 'yes' );

	$gateway['bank-transfer'] = array(
		'jr_fx_page' => 'none',
	);

	foreach( $gateway as $key => $value ) {
		$gateways['enabled'] = array_merge( $gateways['enabled'], array( $key => 'yes' ) );
		$gateways[$key] = $value;
	}

	$legacy_options = array(
		'gateways'	=> $gateways,
	);
	return $legacy_options[$option];
}

// generates a list of FXtender features
function jr_fx_feature_list($options) {

	foreach ($options as $value) {
		if ( $value['type']	 != 'title' and $value['type']	 != 'tab' and $value['type'] != 'free'  and $value['type'] != 'logo' and $value['type'] != 'tabend'  ) {
			if ( $value['name'] ) 
				echo strip_tags($value['name'] . " | " . (jr_fx_upgrade(str_replace(JR_FX_FIELDS_PREFIX, '',$value['id']))?"PRO":"FREE")).'<br/>';
		}	
		
	}

}

// generates admin fields based on array params passed in
function jr_fx_admin_fields($options) {
    global $shortname;
	
	//echoes features table list
	//jr_fx_feature_list( $options );
?>

<script type="text/javascript">
jQuery(function() {
    jQuery("#tabs-wrap").tabs({
        fx: {
            opacity: 'toggle',
            duration: 200
        }
    });
});
</script>


<div id="tabs-wrap">

<?php

    // first generate the page tabs
    $counter = 1;

    echo '<ul>'. "\n";
    foreach ($options as $value) {

        if (in_array('tab', $value)) :
            echo '<li '.(isset($value['tabname']) && $value['tabname']== 'About'?'style="float: right"':'').'><a href="#'.$value['type'].$counter.'">'.$value['tabname'].'</a></li>'. "\n";
            $counter = $counter + 1;
        endif;

    }
    echo '</ul>'. "\n\n";


     // now loop through all the options
    $counter = 1;
    foreach ($options as $value ) {

        switch($value['type']) {

            case 'tab':

                echo '<div id="'.$value['type'].$counter.'">'. "\n\n";
                echo '<table class="widefat fixed" style="width:850px; margin-bottom:20px;">'. "\n\n";

            break;

            case 'title':
            ?>

                <thead><tr><th scope="col" width="250px"><?php echo $value['name'] ?></th><th scope="col"><?php if (isset($value['desc'])) echo $value['desc'] ?>&nbsp;</th></tr></thead>

            <?php
            break;

            case 'text':
            ?>

                <tr class="<?php if ( isset($value['class']) ) echo esc_attr($value['class']); ?>" <?php if ($value['vis'] == '0') { ?>id="<?php if ($value['visid']) { echo $value['visid']; } else { echo 'drop-down'; } ?>" style="display:none;"<?php } ?>>
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo $value['tip'] ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp"><input <?php echo ( isset($value['dis']) && $value['dis'] == '1'? 'readonly':'') ?> name="<?php echo $value['id'] ?>" id="<?php echo $value['id'] ?>" type="<?php echo $value['type'] ?>" style="<?php echo $value['css'] ?>" value="<?php if (get_option( $value['id'])) echo get_option( $value['id'] ); else echo $value['std'] ?>"<?php if ($value['req']) { ?> class="required" <?php } ?> <?php if ($value['min']) { ?> minlength="<?php echo $value['min'] ?>"<?php } ?> /><br /><small><?php echo $value['desc'] ?></small></td>
                </tr>


            <?php
            break;

            case 'select':
            ?>
                <tr class="<?php if ( isset($value['class']) ) echo esc_attr($value['class']); ?>">
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo $value['tip'] ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp"><select <?php if (isset($value['js']) && $value['js']) echo $value['js']; ?> name="<?php echo $value['id'] ?>" id="<?php echo $value['id'] ?>" style="<?php echo $value['css'] ?>"<?php if ($value['req']) { ?> class="required"<?php } ?>>

                        <?php
                        foreach ($value['options'] as $key => $val) {
                        ?>

                            <option value="<?php echo $key ?>" <?php if ( (!get_option($value['id'])?$value['std']:get_option($value['id'])) == $key ) { ?> selected="selected" <?php } ?>><?php echo ucfirst($val) ?></option>

                        <?php
                        }
                        ?>

                       </select><br /><small><?php echo $value['desc'] ?></small>
                    </td>
                </tr>

            <?php
            break;

            case 'checkbox':
            ?>
                <tr class="<?php if ( isset($value['class']) ) echo esc_attr($value['class']); ?>">
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo $value['tip'] ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp"><input type="checkbox" name="<?php echo $value['id'] ?>" id="<?php echo $value['id'] ?>" value="true" style="<?php echo $value['css']?>" <?php if(get_option($value['id'])) { ?>checked="checked"<?php } ?> />
                        <br /><small><?php echo $value['desc'] ?></small>
                    </td>
                </tr>
            <?php
            break;
				
			case 'tzCheckbox':
			?>
			<tr class="<?php if ( isset($value['class']) ) echo esc_attr($value['class']); ?>">
				<td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo $value['tip'] ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>		
                    <td class="forminp"><div class="jr_fx_tzCheckbox"><input class="tzCheckboxBt" type="checkbox" name="<?php echo $value['id'] ?>" id="<?php echo $value['id'] ?>" value="yes" data-on="Yes" data-off="No"  <?php if(get_option($value['id'])) { ?>checked="checked"<?php } ?> /></div>					
                        <small><?php echo $value['desc'] ?></small>
                    </td>			
				</td>
			</tr>
			
            <?php
            break;

            case 'textarea':
            ?>
                <tr class="<?php if ( isset($value['class']) ) echo esc_attr($value['class']); ?>">
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo $value['tip'] ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp">
                        <textarea <?php echo ( isset($value['dis']) &&  $value['dis'] == '1'? 'readonly':'') ?> name="<?php echo $value['id'] ?>" id="<?php echo $value['id'] ?>" style="<?php echo $value['css'] ?>" <?php if ($value['req']) { ?> class="required" <?php } ?><?php if ($value['min']) { ?> minlength="<?php echo $value['min'] ?>"<?php } ?>><?php if (get_option($value['id'])) echo stripslashes(get_option($value['id'])); else echo $value['std']; ?></textarea>
                        <br /><small><?php echo $value['desc'] ?></small>
                    </td>
                </tr>

            <?php
            break;

            case 'logo':
            ?>
                <tr>
                    <td class="titledesc" colspan=2 ><?php echo $value['name'] ?></td>
                    <!--<td class="forminp">&nbsp;</td>-->
                </tr>

            <?php
            break;
            case 'free':
            ?>
                <tr>					
                    <td colspan=2><?php echo $value['name'] ?></td>
                </tr>

            <?php
            break;			

            case 'tabend':

                echo '</table>'. "\n\n";
                echo '</div> <!-- #tab'.$counter.' -->'. "\n\n";
                $counter = $counter + 1;

            break;


        } // end switch

    } // end foreach
?>

</div> <!-- #tabs-wrap -->

<?php
}
?>