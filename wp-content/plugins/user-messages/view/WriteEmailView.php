<?php

/**
* View to write a new email message and send it
* 
* @author Vincent Prat
*/
class UM_WriteEmailView {

    /**
    * Constructor
    */
    function UM_WriteEmailView() {
    }
    
    /**
    * Show contextual help
    */
    function show_help() {                                                   
        return "<p>" . __('No help for this page.', 'um') . "</p>";
    }
    
    /**
    * Show the view
    */
    function show_view() {              
		global $um_plugin, $um_messenger, $user_ID;
		
		// Get the current user and check that he can send messages
		//--
		$current_user = wp_get_current_user();
						
		if (!current_user_can(UM_SEND_EMAIL_MESSAGES_CAP)) {
			die(__("You are not allowed to send emails", "um"));
		}
?>
<div class="wrap">
    <h2><?php _e("Write Email", "um"); ?></h2>
<?php      
		
	// Send the message if the form was submitted
	//--
	if ($_POST['dosend']) {
		check_admin_referer('um_send_email_message');
		
		if ($this->check_post_params()) {
			$this->perform_send_message();
		}
	}
	
	// Output the forms to send an email
	//--	
?>
<form id="email-form" method="post" action="admin.php?page=um-write-email-view">
	<input type="hidden" name="dosend" value="yes" />	
	<?php wp_nonce_field('um_send_email_message'); ?>
	
	<div class="clear"></div>
	
	<table class="um-msg">
	<tbody>	
		<tr>
			<th scope="row"><?php _e("Recipient", "um")?></th>
			<td class="um-msg-recipients">
				<select class="wide" name="recipient">
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
				$recipients = apply_filters('um_select_available_email_recipients', $recipients);
				
				$recipient_count = 0;
				foreach ($recipients as $recipient) {
					if ('false'==get_usermeta($recipient->user_id, UM_ACCEPT_EMAIL_USER_META)) continue;
			?>
					<option value="<?php echo $recipient->user_id; ?>" <?php if (in_array(''.$recipient->user_id, $selected_ids)) echo ' selected="yes"';?>><?php 
						echo $recipient->display_name; 
					?></option>
			<?php
					$recipient_count++;
				}
				
				if ($recipient_count==0) {
					echo '<option value="0">' . __("Nobody has agreed to be contacted by email!", "um") . "</option>";
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
				<input type="submit" name="submit" value="<?php _e("Send Email To Selected User", "um"); ?>" class="button-primary um-send-message-button"></input>
			</td>
		</tr>	
		<?php } ?>
	</tbody>
	</table>
</form>
</div>
<?php        
    }
	
    /**
     * Check the $_POST variables before actually sending the message and output errors if needed
     * @return true if the form is valid and message can be sent
     */
    private function check_post_params() {
    	$has_errors = false;
    	$output = '<div id="error" class="updated fade">';			
			
		if (empty($_POST['subject']) || empty($_POST['content'])) {
			$has_errors = true;
			$output .= '<p>' . __("The subject and the content of the message must be provided.", "um") . '</p>';
		}
		
		if (!isset($_POST['recipient']) ||$_POST['recipient']==0) {
			$has_errors = true;
			$output .= '<p>' . __("You must select a recipient.", "um") . '</p>';
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
		global $um_messenger, $user_ID;
		
    	$subject = $_POST['subject'];
    	$content = $_POST['content'];
    	$recipient = $_POST['recipient']; 
		
    	$result = $um_messenger->send_email($user_ID, $subject, $content, $recipient);
    	
    	// Output result 
    	//--
    	if (true==$result) {
    		echo '<div id="message" class="updated fade">' . __("The message has been sent.", "um") . "</div>";
		} else {
    		echo '<div id="error" class="updated fade">' . __("The system could not send the message correctly.", "um") . "</div>";
			var_dump($result);
    	}
    }

}
  
?>
