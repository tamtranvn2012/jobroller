<?php

/**
* View to write a new public message and send it
* 
* @author Vincent Prat
*/
class UM_NewMessageView {

	var $default_tab = UM_PRIVATE_MESSAGE_TYPE;
	
    /**
    * Constructor
    */
    function UM_NewMessageView($default_tab = -1) {
		if (isset($_POST['type']) && $_POST['type']==UM_PRIVATE_MESSAGE_TYPE) {
			$this->default_tab = UM_PRIVATE_MESSAGE_TYPE;
		} else if (isset($_POST['type']) && $_POST['type']==UM_PUBLIC_MESSAGE_TYPE) {
			$this->default_tab = UM_PUBLIC_MESSAGE_TYPE;
		} else if ($default_tab!=-1) {
			$this->default_tab = $default_tab;
		} else if ($default_tab==-1 && current_user_can(UM_SEND_PRIVATE_MESSAGES_CAP)) {
			$this->default_tab = UM_PRIVATE_MESSAGE_TYPE;
		} else if ($default_tab==-1 && current_user_can(UM_SEND_PUBLIC_MESSAGES_CAP)) {
			$this->default_tab = UM_PUBLIC_MESSAGE_TYPE;
		} else {
			$this->default_tab = UM_PRIVATE_MESSAGE_TYPE; 
		}
    }
    
    /**
    * Show contextual help
    */
    function show_help() {                                                 
        return "<p>" . __('This page allows you to send messages to the users of the blog. Choose private message if you want to send to a selection of users or choose public message if you want to send to every registered user on the website.', 'um') . "</p>";
    }
    
    /**
    * Show the view
    */
    function show_view() {
		global $um_plugin, $um_messenger, $user_ID;
		
		// Get the current user and check that he can send messages
		//--
		$current_user = wp_get_current_user();
						
		if (!current_user_can(UM_SEND_PUBLIC_MESSAGES_CAP) && !current_user_can(UM_SEND_PRIVATE_MESSAGES_CAP)) {
			die(__("You are not allowed to send messages", "um"));
		}
?>
<div class="wrap">
    <h2><?php _e("Write Message", "um"); ?></h2>
<?php        
		
	// Display a warning if the user exceeds his quota
	//--
	if (UM_AdminInterface::check_quota_and_warn($user_ID)) {
		return;
	}
		
	// Send the message if the form was submitted
	//--
	if ($_POST['dosend']) {
		check_admin_referer($_POST['type']==UM_PUBLIC_MESSAGE_TYPE ? 'um_send_public_message' : 'um_send_private_message');
		
		if ($this->check_post_params()) {
			$this->perform_send_message();
		}
	}
	
	// Prepare the form if we are replying to or fowarding a message
	//--
	if ($_GET['action']=='reply') {
		$this->prepare_reply(false);
	} else if ($_GET['action']=='reply-all') {
		$this->prepare_reply(true);
	} else if ($_GET['action']=='forward') {
		$this->prepare_forward();
	}
	
	// Output the forms to send a message according to the message type
	//--
	
?>
<script type="text/javascript">
    jQuery(function() {
        jQuery('#slider').tabs({ fxFade: true, fxSpeed: 'fast' });    
<?php if ($this->default_tab==UM_PUBLIC_MESSAGE_TYPE && current_user_can(UM_SEND_PUBLIC_MESSAGES_CAP)) { ?>
		jQuery('#slider').tabs('select', 'public');
<?php } else if ($this->default_tab==UM_PRIVATE_MESSAGE_TYPE && current_user_can(UM_SEND_PRIVATE_MESSAGES_CAP)) { ?>
		jQuery('#slider').tabs('select', 'private');
<?php } ?>
    });
</script>

<div id="slider">    
	<ul id="tabs">
<?php if (current_user_can(UM_SEND_PRIVATE_MESSAGES_CAP)) { ?>
		<li><a href="#private"><?php _e('Private Message', 'um') ;?></a></li>
<?php } 
	  if (current_user_can(UM_SEND_PUBLIC_MESSAGES_CAP)) { ?>
		<li><a href="#public"><?php _e('Public Message', 'um') ;?></a></li>
<?php } ?>
	</ul>

<?php if (current_user_can(UM_SEND_PRIVATE_MESSAGES_CAP)) { ?>
<div id="private">
<form id="private-msg-form" method="post" action="admin.php?page=<?php echo $_GET['page']; ?>">
	<input type="hidden" name="dosend" value="yes" />	
	<input type="hidden" name="type" value="<?php echo UM_PRIVATE_MESSAGE_TYPE; ?>" />	
	<?php wp_nonce_field('um_send_private_message'); ?>
	
	<div class="clear"></div>
	
	<table class="um-msg">
	<tbody>	
		<tr>
			<th scope="row"><?php _e("Recipients", "um")?></th>
			<td class="um-msg-recipients">
				<select class="wide" name="recipients[]" size="8" multiple="multiple" style="height: 7em;">
			<?php 
				if (isset($_POST['recipients'])) {
					$selected_ids = $_POST['recipients'];
				} else if (isset($_GET['recipients'])) {
					$selected_ids = $_GET['recipients'];
				} else {
					$selected_ids = array();
				}
				
				if (!is_array($selected_ids)) {
					$selected_ids = array($selected_ids);
				}				
				
				// By default, allow to send to all users
				//--
				$recipients = get_users_of_blog();
				
				// Sort the list by display_name
				//--
				usort($recipients, create_function('$a,$b', 'if ($a->display_name== $b->display_name) return 0; return ($a->display_name < $b->display_name) ? -1 : 1;'));
				
				// A separate plugin could define a filter on this user list in order to eliminate some of them
				//--
				$recipients = apply_filters('um_select_available_private_message_recipients', $recipients);

				$recipient_count = 0;
				foreach ($recipients as $recipient) {
					if ('false'==get_usermeta($recipient->user_id, UM_ACCEPT_PRIVATE_MESSAGES_USER_META)) continue;
			?>
					<option value="<?php echo $recipient->user_id; ?>" <?php if (in_array(''.$recipient->user_id, $selected_ids)) echo ' selected="yes"';?>><?php 
						echo $recipient->display_name; 
					?></option>
			<?php
					$recipient_count++;
				}
				
				if ($recipient_count==0) {
					echo '<option value="0">' . __("Nobody has agreed to receive private messages!", "um") . "</option>";
				} 
			?>
				</select>
			</td>
		</tr>	
		<?php if ($recipient_count>0) { ?>
		<tr>
			<th scope="row"><?php _e("Subject", "um")?></th>
			<td class="um-msg-subject">
				<input type="text" class="wide" name="subject" value="<?php if (isset($_POST['subject'])) { echo htmlspecialchars(stripslashes($_POST['subject'])); } ?>" />
			</td>
		</tr>	
		<tr>
			<th scope="row"><?php _e("Message", "um")?></th>
			<td class="um-msg-content">
				<textarea name="content" rows="15" cols="80" class="wide"><?php 
					if (isset($_POST['content'])) { echo htmlspecialchars(stripslashes($_POST['content'])); }
				?></textarea>
			</td>
		</tr>	
		<tr>
			<th scope="row"></th>
			<td class="um-submit">
				<input type="submit" name="submit" value="<?php _e("Send Message To Selected User(s)", "um"); ?>" class="button-primary um-send-message-button"></input>
			</td>
		</tr>	
		<?php } ?>
	</tbody>
	</table>
</form>
</div>

<?php } 
	  if (current_user_can(UM_SEND_PUBLIC_MESSAGES_CAP)) { ?>
	  
<div id="public">
<form id="public-msg-form" method="post" action="admin.php?page=<?php echo $_GET['page']; ?>">
	<input type="hidden" name="dosend" value="yes" />	
	<input type="hidden" name="type" value="<?php echo UM_PUBLIC_MESSAGE_TYPE; ?>" />	
	<?php wp_nonce_field('um_send_public_message'); ?>
	
	<div class="clear"></div>
	
	<table class="um-msg">
	<tbody>	
		<tr>
			<th scope="row"><?php _e("Subject", "um")?></th>
			<td class="um-msg-subject">
				<input type="text" class="wide" name="subject" value="<?php if (isset($_POST['subject'])) { echo htmlspecialchars(stripslashes($_POST['subject'])); } ?>" />
			</td>
		</tr>	
		<tr>
			<th scope="row"><?php _e("Message", "um")?></th>
			<td class="um-msg-content">
				<textarea name="content" rows="15" cols="80" class="wide"><?php 
					if (isset($_POST['content'])) { echo htmlspecialchars(stripslashes($_POST['content'])); }
				?></textarea>
			</td>
		</tr>	
		<tr>
			<th scope="row"></th>
			<td class="um-submit">
				<input type="submit" name="submit" value="<?php _e("Send Message To Everybody", "um"); ?>" class="button-primary um-send-message-button"></input>
			</td>
		</tr>	
	</tbody>
	</table>
</form>
</div>

<?php } ?>

</div>

<script type="text/javascript">
// <![[CDATA
	jQuery("#um-submit").click(function() {
		return confirm('<?php addslashes(_e("Are you sure that you want to send this message?", "um")); ?>');
	});
// ]]>
</script>
<?php 
    }
    
    /**
     * Check the $_POST variables before actually sending the message and output errors if needed
     * @return true if the form is valid and message can be sent
     */
    private function check_post_params() {
    	$has_errors = false;
    	$output = '<div id="error" class="updated fade">';			
			
		if (!isset($_POST['type']) || ($_POST['type']!=UM_PRIVATE_MESSAGE_TYPE && $_POST['type']!=UM_PUBLIC_MESSAGE_TYPE)) {
			$has_errors = true;
			$output .= '<p>' . __("The type of the message must be provided and known.", "um") . '</p>';
		} else if ($_POST['type']==UM_PRIVATE_MESSAGE_TYPE && !current_user_can(UM_SEND_PRIVATE_MESSAGES_CAP)) {
			$has_errors = true;
			$output .= '<p>' . __("You are not allowed to send private messages.", "um") . '</p>';
		} else if ($_POST['type']==UM_PUBLIC_MESSAGE_TYPE && !current_user_can(UM_SEND_PUBLIC_MESSAGES_CAP)) {
			$has_errors = true;
			$output .= '<p>' . __("You are not allowed to send public messages.", "um") . '</p>';
		}
			
		if (empty($_POST['subject']) || empty($_POST['content'])) {
			$has_errors = true;
			$output .= '<p>' . __("The subject and the content of the message must be provided.", "um") . '</p>';
		}
		
		if ($_POST['type']==UM_PRIVATE_MESSAGE_TYPE
				&& (!isset($_POST['recipients']) 
						|| !is_array($_POST['recipients']) 
						|| empty($_POST['recipients']))) {
			$has_errors = true;
			$output .= '<p>' . __("You must select at least one recipient.", "um") . '</p>';
		}
		
    	$output .= '</div>';
    	
    	if ($has_errors) {
    		echo $output;
    	}
    		
    	return $has_errors ? false : true;
    }
    
    /**
     * Send the message using the $_POST variables. No check is performed in this function, the function 
     * check_post_params() should be called to perform this check. This function will print the result of
     * our action.
     */
    private function perform_send_message() {
		global $um_messenger;
		
    	$result = null;
    	$current_user = wp_get_current_user();
    	$subject = $_POST['subject'];
    	$content = $_POST['content'];
		$type = $_POST['type'];
    	
    	if ($type==UM_PUBLIC_MESSAGE_TYPE) {
			$result = $um_messenger->send_public_message($current_user->ID, $subject, $content);
    	} else if ($type==UM_PRIVATE_MESSAGE_TYPE) {
    		$selected_ids = isset($_POST['recipients']) ? $_POST['recipients'] : array();	
			$result = $um_messenger->send_private_message($current_user->ID, $subject, $content, $selected_ids);
    	} 
    	
    	// Output result 
    	//--
    	if (!empty($result)) {
    		if (!empty($result['delivered']) || !empty($result['quota_error']) || !empty($result['rejected_error'])) {	  			
		   		echo '<div id="message" class="updated fade"><ul>';
    			
		   		if (!empty($result['delivered'])) {
					echo '<li>' . sprintf(__("The message has been delivered to %d users.", "um"), count($result['delivered'])) . '</li>';
		   		}
				
		   		if (!empty($result['inbox_full'])) {
					echo '<li>' . sprintf(__("%d users have exceeded their inbox capacity and thus have not received the message.", "um"), count($result['inbox_full'])) . '</li>';
				}
				
				if (!empty($result['rejected_error'])) {
					echo '<li>' . sprintf(__("%d users have chosen not to receive public messages.", "um"), count($result['rejected_error'])) . '</li>';
				}
		   		
				echo "</ul></div>";
			}
			
			if (!empty($result['general_errors'])) {
		   		echo '<div id="error" class="updated fade"><ul>';
		   		foreach ($result['general_errors'] as $error) {
					echo '<li>' . $error . '</li>';
		   		}
				echo "</ul></div>";
			}
    	}
    }
    
    /**
     * Prepare the form for a reply to a message
     */
    private function prepare_reply($is_reply_all=false) {
    	global $user_ID;
    	
		// Find the message we are replying to
		//--
		if (!isset($_GET['message_id'])) {
			echo '<div id="error" class="updated fade">';
		   	echo '<p>' . __("You want to reply to a message but you did not give its id", "um") . '</p>';
			echo "</div>";
			return;
		}
		
		$reply_to_msg = UM_MessageDAO::find_message_by_id($_GET['message_id']);
		if (!isset($reply_to_msg)) {
			echo '<div id="error" class="updated fade">';
		   	echo '<p>' . __("The message you want to reply to does not exists", "um") . '</p>';
			echo "</div>";
			return;
		}
		
		// Check we own that message
		//--
		if ($reply_to_msg->owner_id!=$user_ID) {
			echo '<div id="error" class="updated fade">';
		   	echo '<p>' . __("You cannot reply to a message that does not belong to you", "um") . '</p>';
			echo "</div>";
			return;
		}
		
		// Prepare some of the form parameters according to the message
		//--
		if ($is_reply_all) {
			$_POST['recipients'] = $reply_to_msg->recipient_ids;
			$_POST['recipients'][] = $reply_to_msg->author_id;
			$_POST['recipients'] = array_unique($_POST['recipients']);
		} else {
			$_POST['recipients'] = array($reply_to_msg->author_id);
		}
		
		$subject_prefix = __("Re: ", "um");
		$original_subject = $reply_to_msg->get_raw_subject();
		$_POST['subject'] = substr_compare($original_subject, $subject_prefix, 0, count($subject_prefix)) ? $subject_prefix . $original_subject : $original_subject;

		$content_prefix = sprintf(__("Original message from %s:", "um"), $reply_to_msg->get_author()->display_name);
		$_POST['content'] = "\n" . $content_prefix . "\n" . wordwrap("> " . $reply_to_msg->get_raw_content(), 78, "\n> ");
    }
    
    /**
     * Prepare the form for a forward of a message
     */
    private function prepare_forward() {
    	global $user_ID;
    	
		// Find the message we are replying to
		//--
		if (!isset($_GET['message_id'])) {
			echo '<div id="error" class="updated fade">';
		   	echo '<p>' . __("You want to forward a message but you did not give its id", "um") . '</p>';
			echo "</div>";
			return;
		}
		
		$forward_msg = UM_MessageDAO::find_message_by_id($_GET['message_id']);
		if (!isset($forward_msg)) {
			echo '<div id="error" class="updated fade">';
		   	echo '<p>' . __("The message you want to forward does not exists", "um") . '</p>';
			echo "</div>";
			return;
		}
		
		// Check we own that message
		//--
		if ($forward_msg->owner_id!=$user_ID) {
			echo '<div id="error" class="updated fade">';
		   	echo '<p>' . __("You cannot forward a message that does not belong to you", "um") . '</p>';
			echo "</div>";
			return;
		}
		
		// Prepare some of the form parameters according to the message
		//--
		$subject_prefix = __("Fw: ", "um");
		$original_subject = $forward_msg->get_raw_subject();
		$_POST['subject'] = substr_compare($original_subject, $subject_prefix, 0, count($subject_prefix)) ? $subject_prefix . $original_subject : $original_subject;

		$content_prefix = sprintf(__("Original message from %s:", "um"), $forward_msg->get_author()->display_name);
		$_POST['content'] = "\n" . $content_prefix . "\n" . wordwrap("> " . $forward_msg->get_raw_content(), 78, "\n> ");
    }
}
  
?>
