<?php

/**
* View to read a message
* 
* @author Vincent Prat
*/
class UM_ShowMessageView {

    /**
    * Constructor
    */
    function UM_ShowMessageView() {
    }
    
    /**
    * Show contextual help
    */
    function show_help() {      
    	$help = "<p>";
    	$help .= __('You are currently viewing a message. ', "um");
    	$help .= __('If you are allowed to send private messages, you can do so by clicking on any user name. ', 'um');
    	$help .= __('Above the message details, you can see buttons to do a few actions (if you have the capability):', "um");
    	$help .= "</p>";                  
    	$help .= "<ul>";   
    	$help .= '<li><b>' . __("Go Back To Inbox", "um") . '</b>: ' . __('takes you back to the inbox view.', "um") . "</li>";    
    	$help .= '<li><b>' . __("Reply", "um") . '</b>: ' . __('allows you to reply to the sender of the message.', "um") . "</li>";    
    	$help .= '<li><b>' . __("Reply All", "um") . '</b>: ' . __('allows you to reply to the sender and to the other recipients of the message.', "um") . "</li>";    
    	$help .= '<li><b>' . __("Forward", "um") . '</b>: ' . __('allows you to send this same message to other users.', "um") . "</li>";    
    	$help .= '<li><b>' . __("Delete", "um") . '</b>: ' . __('deletes the message from your inbox.', "um") . "</li>";   
    	$help .= "</ul>";                  
    	
        return $help;
    }
    
    /**
    * Show the view
    */
    function show_view() {    
		global $um_messenger;
?>
<div class="wrap">
    <h2><?php _e("Show Message", "um"); ?></h2>
    
<?php   
   	// Get the message
   	//--
   	$message_id = $_GET['message_id'];
   	if (!isset($message_id)) {
   		echo '<div id="error" class="updated fade">';
		echo '<p>' . __("No message was selected", "um") . '</p>';
		echo "</div>";
		return;
  	} 
  	
  	$msg = UM_MessageDAO::find_message_by_id($message_id);
   	if (!isset($msg)) {
   		echo '<div id="error" class="updated fade">';
		echo '<p>' . __("The message does not exist", "um") . '</p>';
		echo "</div>";
		return;
  	} 

   	// Check the user is the owner of this message (else that means someone else 
   	// is trying to read other's messages)
   	//--
	$current_user = wp_get_current_user();
	if ($msg->owner_id!=$current_user->ID) {
   		echo '<div id="error" class="updated fade">';
		echo '<p>' . __("You are not the owner of this message, please respect other's privacy", "um") . '</p>';
		echo "</div>";
		return;
	}
	
	// Display a warning if the user exceeds his quota
	//--
	UM_AdminInterface::check_quota_and_warn($user_id);
	
	// Mark the message as read
	//--
	$msg->status = UM_READ_MESSAGE_STATUS;
	UM_MessageDAO::update_message_status($msg->message_id, $msg->status);
	
	// Display the message
	//--
?>
	<div class="clear"></div>
	
	<table class="um-msg">
	<tbody>	
		<tr>
			<th scope="row"><?php _e("Actions", "um")?></th>
			<td class="um-msg-actions">
				<a id="um-back-button" class="button" href="admin.php?page=user-messages">
					<img class="um-button-icon" src="<?php echo UM_THEMEURL; ?>/inbox-icon.png" title="<?php _e("Go Back To Inbox", "um")?>" alt="<?php _e("Go Back To Inbox", "um")?>" /></a>
<?php if (current_user_can(UM_SEND_PRIVATE_MESSAGES_CAP)) { ?>
				<a id="um-reply-button" class="button" href="admin.php?page=um-write-message-view&type=<?php echo UM_PRIVATE_MESSAGE_TYPE?>&action=reply&message_id=<?php echo $msg->message_id; ?>">
					<img class="um-button-icon" src="<?php echo UM_THEMEURL; ?>/msg-reply-icon.png" title="<?php _e("Reply To Sender", "um")?>" alt="<?php _e("Reply", "um")?>" /></a>
				<a id="um-reply-all-button" class="button" href="admin.php?page=um-write-message-view&type=<?php echo UM_PRIVATE_MESSAGE_TYPE?>&action=reply-all&message_id=<?php echo $msg->message_id; ?>">
					<img class="um-button-icon" src="<?php echo UM_THEMEURL; ?>/msg-reply-all-icon.png" title="<?php _e("Reply To All Recipients", "um")?>" alt="<?php _e("Reply All", "um")?>" /></a>
				<a id="um-forward-button" class="button" href="admin.php?page=um-write-message-view&type=<?php echo UM_PRIVATE_MESSAGE_TYPE?>&action=forward&message_id=<?php echo $msg->message_id; ?>">
					<img class="um-button-icon" src="<?php echo UM_THEMEURL; ?>/msg-forward-icon.png" title="<?php _e("Forward Message", "um")?>" alt="<?php _e("Forward", "um")?>" /></a>
<?php } ?>
				<a id="um-delete-button" class="button" href="admin.php?page=user-messages&action=delete&doaction=doaction&message_ids[]=<?php echo $msg->message_id; ?>&_wpnonce=<?php echo wp_create_nonce("um_inbox_action"); ?>">
					<img class="um-button-icon" src="<?php echo UM_THEMEURL; ?>/msg-delete-icon.png" title="<?php _e("Delete Message", "um")?>" alt="<?php _e("Delete", "um")?>" /></a> 
			</td>
		</tr>	
		<tr>
			<th scope="row"><?php _e("Date", "um")?></th>
			<td class="um-msg-date"><?php 
		    	$format = get_option('date_format') . ' ' . get_option('time_format');
				echo date($format, mysql2date('G', $msg->timestamp));
			?></td>
		</tr>	
		<tr>
			<th scope="row"><?php _e("From", "um")?></th>
			<td class="um-msg-from"><?php 
				echo UM_AdminInterface::get_write_to_link($msg->get_author());
			?></td>
		</tr>	
		<tr>
			<th scope="row"><?php _e("To", "um")?></th>
			<td class="um-msg-to"><?php 
				if ($msg->type==UM_PUBLIC_MESSAGE_TYPE) {
					echo '<i>' . __("This public message was sent to every registered user", "um") . '</i>';
				} else {
					$recipients = $msg->get_recipients();
					for ($i=0;$i<count($recipients);$i++) {
						echo UM_AdminInterface::get_write_to_link($recipients[$i]);
						if ($i!=count($recipients)-1) {
							echo ", ";
						}
					}
				}
			?></td>
		</tr>	
		<tr>
			<th scope="row"><?php _e("Message", "um")?></th>
			<td class="um-msg-message">
				<div class="wide um-msg-subject"><?php echo $msg->get_subject_html(); ?></div><hr/>
				<div class="wide um-msg-content"><?php echo $msg->get_content_html(); ?></div>
			</td>
		</tr>	
	</tbody>
	</table>
</div>

<script type="text/javascript">
// <![[CDATA
	jQuery("#um-delete-button").click(function() {
		return confirm('<?php addslashes(_e("Are you sure that you want to delete this message?", "um")); ?>');
	});
// ]]>
</script>

<?php        
    }
}
  
?>
