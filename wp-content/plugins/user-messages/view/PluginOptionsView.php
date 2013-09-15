<?php

/**
* View to configure the plugin
* 
* @author Vincent Prat
*/
class UM_PluginOptionsView {

    /**
    * Constructor
    */
    function UM_PluginOptionsView() {
    }
    
    /**
    * Show contextual help
    */
    function show_help() {                                                                    
        return    "<h3>" . __("General", "um") . "</h3>" 
                . "<ul>"  
                . " <li><strong>" . __('Default inbox capacity', 'um') . "</strong>: " 
                . __("The number of messages that can be kept in each user's inbox. This is the real capacity of the inbox, messages will not be received when this box is full.", "um") . "</li>"
                . " <li><strong>" . __('User quota', 'um') . "</strong>: " 
                . __("When the user exceeds his quota (has more messages than this in his inbox), he cannot send messages anymore and a warning requests him to delete messages from his inbox. This number must be lower or equal than the inbox capacity.", "um") . "</li>"
                . " <li><strong>" . __('Plugin theme URL', 'um') . "</strong>: " 
                . __("The base address for the style sheet and icon set you want for the plugin. This should not end with /", "um") . "</li>"
                . "</ul>"
                . "<h3>" . __("Notifications", "um") . "</h3>" 
                . "<ul>"  
                . " <li><strong>" . __('Notify on public message', 'um') . "</strong>: " 
                . __("If the user wants, he can get an email notification when he receives a new message. However, if your mail server has a limitation on the number of emails sent by hour, you can at least deactivate notifications for public messages", "um") . "</li>"
                . " <li><strong>" . __('Notification batch size', 'um') . "</strong>: " 
                . __("To avoid sending too many notification emails at the same time, you can adjust here the number of notifications sent in each batch. You can increase this value if too many notifications get accumulated in the system.", "um") . "</li>"
                . " <li><strong>" . __('Notification task interval', 'um') . "</strong>: " 
                . __("Notifications are queued and processed by batch. This is the time between each task execution in minutes. You can try a lower value if too many notifications get accumulated in the system.", "um") . "</li>"
                . " <li><strong>" . __('New message notification', 'um') . "</strong>: " 
                . __("You can customize the generic notification email sent when a user receives a new message", "um") . "</li>"
                . " <li><strong>" . __('Inbox full notification', 'um') . "</strong>: " 
                . __("You can customize the generic notification email sent when the message box of a user is full", "um") . "</li>"
                . " <li><strong>" . __('Quota exceeded notification', 'um') . "</strong>: " 
                . __("You can customize the generic notification email sent when a user exceeds his quota and is thus forbidden to send messages", "um") . "</li>"
                . "</ul>"
                . "<p>" . __("Within the notification subject and body, you can use the tags %BLOG_NAME%, %BLOG_URL%, %USER_MESSAGES_URL%, %TOTAL_MESSAGE_COUNT%, %UNREAD_MESSAGE_COUNT%, %USER_QUOTA%, %RECIPIENT_NAME%, %MESSAGE_AUTHOR%, %MESSAGE_SUBJECT% and %MESSAGE_CONTENT%. They will be replaced by the appropriate value.");
    }
	
    /**
    * Show the view
    */
    function show_view() {
        global $um_plugin, $user_ID, $um_notifier;  
        
		echo '<div id="message" class="updated fade">';
        
        if (isset($_POST["submit"]) || isset($_POST["submit_test_notifications"]) || isset($_POST["submit_process_notifications"])) {
        	check_admin_referer("um_set_options");
        	
        	$um_plugin->options['default_inbox_capacity'] = $_POST['default_inbox_capacity'] < 1 ? $um_plugin->options['default_inbox_capacity'] : $_POST['default_inbox_capacity'];
        	$um_plugin->options['user_quota'] = $_POST['user_quota'] > $_POST['default_inbox_capacity'] ? $_POST['default_inbox_capacity'] : $_POST['user_quota'];
            $um_plugin->options['theme_url'] = $_POST['theme_url'];
            $um_plugin->options['notify_on_public_message'] = isset($_POST['notify_on_public_message']) ? 'true' : 'false';
            $um_plugin->options['notification_batch_size'] = $_POST['notification_batch_size'] < 1 ? $um_plugin->options['notification_batch_size'] : $_POST['notification_batch_size'];
            $um_plugin->options['notification_task_interval'] = $_POST['notification_task_interval'] < 1 ? $um_plugin->options['notification_task_interval'] : $_POST['notification_task_interval'];
            $um_plugin->options['new_message_notification_subject'] = $_POST['new_message_notification_subject'];
            $um_plugin->options['new_message_notification_body'] = stripslashes($_POST['new_message_notification_body']);
            $um_plugin->options['over_quota_notification_subject'] = $_POST['over_quota_notification_subject'];
            $um_plugin->options['over_quota_notification_body'] = $_POST['over_quota_notification_body'];
            $um_plugin->options['inbox_full_notification_subject'] = $_POST['inbox_full_notification_subject'];
            $um_plugin->options['inbox_full_notification_body'] = $_POST['inbox_full_notification_body'];
            
        	$um_plugin->save_options();
			$um_plugin->reschedule_cron();
			
			echo "<p><strong>" . __("Settings saved.", "um") . "</strong></p>";
        }
		
		if (isset($_POST["submit_test_notifications"])) {
			$notification_recipients = array($user_ID);
			
			$um_notifier->send_new_message_notification($notification_recipients, 1);
			$um_notifier->send_quota_exceeded_notification($notification_recipients);
			$um_notifier->send_inbox_full_notification($notification_recipients);
			
			echo "<p><strong>" . __("Sample notification messages have been queued in the system and should be sent to you by email shortly.", "um") . "</strong></p>";
		}
		
		if (isset($_POST["submit_process_notifications"])) {
			$um_notifier->process_pending_notifications();
			
			echo "<p><strong>" . __("Some pending notifications where processed.", "um") . "</strong></p>";
		}
		
		echo "</div>";
?>

<script type="text/javascript">
    jQuery(function() {
        jQuery('#slider').tabs({ fxFade: true, fxSpeed: 'fast' });   
<?php		
		if (isset($_POST["submit_process_notifications"])) {
			echo "jQuery('#slider').tabs('select', 'pending_notifications');";
		} else if (isset($_POST["submit_test_notifications"])) {
			echo "jQuery('#slider').tabs('select', 'notifications');";
		}
?>
    });
</script>

<div class="wrap">
    <h2><?php echo sprintf(__("User Messages %s configuration", "um"), $um_plugin->options['active_version']); ?></h2>
        
    <?php echo '<span style="display:block;border:1px solid red;color:red;font-weight:bold;margin:20px;padding:10px;">You are having the latest free version. However, since it has gone into commercial licensing, User Messages has plenty of bug fixes and lots of new cool features. Please visit <a href="http://user-messages.vincentprat.info">the plugin page</a> to know more about it!</span>'; ?>

    <div id="slider">    
        <ul id="tabs">
            <li><a href="#general"><?php _e('General', 'um') ;?></a></li>
            <li><a href="#notifications"><?php _e('Notifications', 'um') ;?></a></li>
            <li><a href="#capabilities"><?php _e('Roles &amp; capabilities', 'um') ;?></a></li>
            <li><a href="#pending_notifications"><?php _e('Pending Notifications', 'um') ;?></a></li>
        </ul>
        
        
        <form action="" method="post">
            <?php wp_nonce_field('um_set_options') ?> 
            <input type="hidden" name="option_names" value="default_inbox_capacity" />  
            
            <div id="general">        
                <h3><?php _e('General','um'); ?></h3>
                <table class="form-table um-msg">
                    <tr valign="top">
                        <th scope="row"><?php _e('Default inbox capacity', 'um') ?></th>
                        <td>                                                                            
                            <input type="text" name="default_inbox_capacity" value="<?php echo $um_plugin->options["default_inbox_capacity"]; ?>" class="small-text" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('User quota', 'um') ?></th>
                        <td>                                                                            
                            <input type="text" name="user_quota" value="<?php echo $um_plugin->options["user_quota"]; ?>" class="small-text" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Plugin theme URL', 'um') ?></th>
                        <td>                                                                            
                            <input type="text" name="theme_url" value="<?php echo $um_plugin->options["theme_url"]; ?>" class="wide" />
                        	<img src="<?php echo $um_plugin->options['theme_url'] . '/menu-icon.png'; ?>" alt="An image should be displayed instead of this text. Your url is probably incorrect" title="Your url seems to be correct" />
                        </td>
                    </tr>
                </table>
            </div>
            
            <div id="notifications">        
                <h3><?php _e('Notifications','um'); ?></h3>
                <table class="form-table um-msg">
                    <tr valign="top">
                        <th scope="row"><?php _e('Notify on public message', 'um') ?></th>
                        <td>                                                                            
                            <input type="checkbox" name="notify_on_public_message" value="true" "<?php if ('true'==$um_plugin->options["notify_on_public_message"]) echo 'checked="checked"'; ?>" class="small-text" /> 
                            <?php _e("Also notify users when they receive a public message (they will anyway be notified on new private messages)"); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Notification batch size', 'um') ?></th>
                        <td>                                                                            
                            <input type="text" name="notification_batch_size" value="<?php echo $um_plugin->options["notification_batch_size"]; ?>" class="small-text" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Notification task interval', 'um') ?></th>
                        <td>                                                                            
                            <input type="text" name="notification_task_interval" value="<?php echo $um_plugin->options["notification_task_interval"]; ?>" class="small-text" /> <?php _e("minutes","um"); ?>
                        </td>
                    </tr>
                </table>
				
				<table class="form-table um-msg">
					<tr valign="top">
						<th scope="row"></th>
						<td><input type="submit" name="submit_test_notifications" value="<?php _e("Save options and send sample notifications", "um"); ?>" class="button-primary"></input></td>
					</tr>
					<tr valign="top">
						<th scope="row"></th>
						<td></td>
					</tr>
				</table>
                
                <h3><?php _e('New message notification','um'); ?></h3>
                <table class="form-table um-msg">
                    <tr valign="top">
                        <th scope="row"><?php _e('Email subject', 'um') ?></th>
                        <td>                                                                            
                            <input type="text" name="new_message_notification_subject" value="<?php echo htmlspecialchars($um_plugin->options["new_message_notification_subject"]); ?>" class="wide subject" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Email body', 'um') ?></th>
                        <td>                            
							<textarea name="new_message_notification_body" rows="15" class="wide"><?php 
								echo htmlspecialchars(wordwrap($um_plugin->options["new_message_notification_body"], 80, "\n"));
							?></textarea>                                    
                        </td>
                    </tr>
                </table>
                
                <h3><?php _e('Quota exceeded notification','um'); ?></h3>
                <table class="form-table um-msg">
                    <tr valign="top">
                        <th scope="row"><?php _e('Email subject', 'um') ?></th>
                        <td>                                                                            
                            <input type="text" name="over_quota_notification_subject" value="<?php echo htmlspecialchars($um_plugin->options["over_quota_notification_subject"]); ?>" class="wide subject" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Email body', 'um') ?></th>
                        <td>                       
							<textarea name="over_quota_notification_body" rows="15" class="wide"><?php 
								echo htmlspecialchars(wordwrap($um_plugin->options["over_quota_notification_body"], 80, "\n"));
							?></textarea>                          
                        </td>
                    </tr>
                </table>
                
                <h3><?php _e('Inbox full notification','um'); ?></h3>
                <table class="form-table um-msg">
                    <tr valign="top">
                        <th scope="row"><?php _e('Email subject', 'um') ?></th>
                        <td>                                                                            
                            <input type="text" name="inbox_full_notification_subject" value="<?php echo htmlspecialchars($um_plugin->options["inbox_full_notification_subject"]); ?>" class="wide subject" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Email body', 'um') ?></th>
                        <td>                       
							<textarea name="inbox_full_notification_body" rows="15" class="wide"><?php 
								echo htmlspecialchars(wordwrap($um_plugin->options["inbox_full_notification_body"], 80, "\n"));
							?></textarea>                          
                        </td>
                    </tr>
                </table>
            </div>
            
            <div id="capabilities">                                 
                <h3><?php _e('Roles and capabilities','um'); ?></h3>
                <p><?php _e("This is the list of the capabilities used by the plugin, a short description of what it allows the users to do if they have it and the roles that currently have this capability.","um"); ?> 
                <?php _e("You should use the plugin named <strong>Role Manager</strong> (<a href='http://www.im-web-gefunden.de/wordpress-plugins/role-manager/' target='_blank'>link</a>) in order to configure the roles and the capabilities associated to them.", "um"); ?> </p>
                <?php 
                    global $wp_roles;
                    $caps = array(
                            UM_USE_PLUGIN_CAP => array(__("The user can use the User Messages plugin (basic capability to show the main plugign menu)", "um")),
                            UM_RECEIVE_MESSAGES_CAP => array(__("The user can receive messages from other users", "um")),
                            UM_SEND_PRIVATE_MESSAGES_CAP => array(__("The user can send private messages (the recipient is a single user)", "um")),
                            UM_SEND_PUBLIC_MESSAGES_CAP => array(__("The user can send public messages (the recipient is a selection of user groups)", "um")),
                            UM_SEND_EMAIL_MESSAGES_CAP => array(__("The user can send emails to the users (the message is sent to the address of the user instead of landing in his Inbox)", "um")),
                            UM_IGNORE_PUBLIC_MESSAGES_CAP => array(__("The user can choose whether to ignore public messages or not", "um")),
                            UM_REFUSE_PRIVATE_MESSAGES_CAP => array(__("The user can choose whether to allow other users to send him private messages or not", "um")),
                            UM_CONFIGURE_PLUGIN_CAP => array(__("The user can configure the plugin options", "um"))
                        );
                        
                    foreach ($caps as $capability => &$allowed_roles) {
                        foreach ($wp_roles->role_objects as $role) {
                            if (TRUE==$role->has_cap($capability)) {
                                $allowed_roles[] = $role->name;
                            }
                        }     
                    }               
                    
                    foreach ($caps as $capability => $allowed_roles) { ?>
                    <p><strong><?php echo $capability; ?></strong> <small>&raquo; <?php echo $allowed_roles[0]; ?></small>
                    <br/><blockquote>&bull; 
                        <?php                             
                            for ($i=1; $i<count($allowed_roles); $i++) { 
                                echo $allowed_roles[$i] . " &bull; ";
                            } 
                        ?>
                    </blockquote></p>
                    <?php } ?>                
                </ul>
            </div>
			
            <div id="pending_notifications">   
                <h3><?php _e('Pending Notifications','um'); ?></h3>
				
			<?php	
					$notifications = UM_NotificationDAO::find_next_notifications_to_send();
					if (count($notifications)>0) {
			?>
				<p><?php _e("This is the list of the notifications that have been queued in the system and that have still not been sent to the users.","um"); ?> 
				<?php _e("Next batch of notifications will be processed at: ","um"); echo date('Y-m-d H:i:s', wp_next_scheduled('um_process_notifications')); ?></p>
				
				<table class="form-table um-msg">
					<tr valign="top">
						<th scope="row"></th>
						<td><input type="submit" name="submit_process_notifications" value="<?php _e("Save options and process a batch of notifications", "um"); ?>" class="button-primary"></input></td>
					</tr>
					<tr valign="top">
						<th scope="row"></th>
						<td></td>
					</tr>
				</table>
				
				<table class="widefat">
					<tr>
						<th></th>
						<th><?php _e('Notification Type','um'); ?></th>
						<th><?php _e('User to Notify','um'); ?></th>
						<th><?php _e('Associated Message (if any)','um'); ?></th>
					</tr>
			<?php	
					$i = 1;
					foreach ($notifications as $n) {
			?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $n->type; ?></td>
						<td><?php echo $n->get_user()->display_name; ?></td>
						<td><?php echo $n->message_id!=0 ? $n->message_id :"-"; ?></td>
					</tr>
			<?php 		
						$i++;
					}	
			?>
				</table>
			<?php	
					} else {
			?>
				<p><?php _e("There are no pending notifications to anybody, everything has already been processed.","um"); ?></p>
			<?php 		
					}	
			?>
            </div>
            <table class="form-table um-msg">
                <tr valign="top">
                    <th scope="row"></th>
                    <td><input type="submit" name="submit" value="<?php _e("Save Options", "um"); ?>" class="button-primary"></input></td>
                </tr>
                <tr valign="top">
                    <th scope="row"></th>
                    <td></td>
                </tr>
            </table>
        </form>     
    </div>
</div>
<?php        
    }
}
  
?>
