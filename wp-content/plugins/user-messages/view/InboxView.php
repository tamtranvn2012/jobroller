<?php

/**
* View to list the user's messages
* 
* @author Vincent Prat
*/
class UM_InboxView {

    /**
    * Constructor
    */
    function UM_InboxView() {
    }
    
    /**
    * Show contextual help
    */
    function show_help() {                                                      
        $help  = "<p>" . __('This view presents the messages you have recieved. Icons will help you to quickly identify the message status (icons in the first column) and type (icons in the second column). With the button on the top-left of the message list you can toggle the display of the message excerpt.', 'um') . "</p>";		
		$help .= "<h5>" . __('Message Types', 'um') . "</h5>";
		$help .= "<ul>";
		$help .= "<li>" . UM_AdminInterface::get_message_type_icon(UM_PUBLIC_MESSAGE_TYPE) . " " . __('indicates a public message (that was sent to everybody).', 'um') . "</li>";
		$help .= "<li>" . UM_AdminInterface::get_message_type_icon(UM_PRIVATE_MESSAGE_TYPE, 1) . " " . __('indicates a message that was sent only to you.', 'um') . "</li>";
		$help .= "<li>" . UM_AdminInterface::get_message_type_icon(UM_PRIVATE_MESSAGE_TYPE, 2) . " " . __('indicates a message that was sent to you and a selection of other users.', 'um') . "</li>";
		$help .= "</ul>";
		$help .= "<h5>" . __('Message Status', 'um') . "</h5>";
		$help .= "<ul>";
		$help .= "<li>" . UM_AdminInterface::get_message_status_icon(UM_UNREAD_MESSAGE_STATUS) . " " . __('indicates a message that has not yet been opened (a new message).', 'um') . "</li>";
		$help .= "<li>" . UM_AdminInterface::get_message_status_icon(UM_READ_MESSAGE_STATUS) . " " . __('indicates a message that has already been opened.', 'um') . "</li>";
		$help .= "</ul>";
		return $help;
    }
    
    /**
    * Show the view
    */
    function show_view() {
    	global $um_plugin, $um_messenger, $user_ID;
    	 	
		// Get the current user and check he can view his inbox
		//--
		$current_user = wp_get_current_user();
		$user_ID = $current_user->ID;			
				
		if (!current_user_can(UM_RECEIVE_MESSAGES_CAP)) {
			die(__("You are not allowed to recieve messages", "um"));
		}
		
    	// Get the filter type from request
		//--
    	if (!isset($_GET['filter_type']) || $_GET['filter_type']=='') {
			$filter_type = 'all';
		} else {
			$filter_type = $_GET['filter_type'];
		}
		
		// Get the display mode
		//--
    	if (!isset($_GET['display_mode']) || $_GET['display_mode']=='') {
			$display_mode = 'subject';
		} else {
			$display_mode = $_GET['display_mode'];
		}
		
		// Get the folder
		//--
		$folder_id = UM_INBOX_FOLDER;
?>
<div class="wrap">
    <h2><?php _e("Inbox", "um"); ?></h2>
		
<?php 
	// TODO: Do any required action on the selected messages
	//--
	if (isset($_GET['action']) && isset($_GET['doaction'])) {
		check_admin_referer('um_inbox_action');
		
		$action = $_GET['action'];
		$message_ids = $_GET['message_ids'];
		
		$this->do_action($action, $message_ids);
	}
   	
	// Get the messages 
	//--
	$messages = UM_MessageDAO::find_user_messages($user_ID);
	$unread_message_num = $this->count_unread_messages($messages);
	$unread_private_message_num = $this->count_unread_messages($messages, UM_PRIVATE_MESSAGE_TYPE);
	$unread_public_message_num = $this->count_unread_messages($messages, UM_PUBLIC_MESSAGE_TYPE);
	$total_message_num = count($messages);
	
	// Filter the messages
	//--
	$messages = $this->filter_messages($filter_type, $messages);
    
	// Display a warning if the user exceeds his quota
	//--
	UM_AdminInterface::check_quota_and_warn($user_ID);
?>

	<ul class="subsubsub">
		<li><a href='admin.php?page=user-messages&filter_type=all&display_mode=<?php echo $display_mode; ?>' <?php if ($filter_type=='all') echo 'class="current"'; ?>>
			<?php _e("All", "um"); ?></a>
		</li>
		<li>| <a href='admin.php?page=user-messages&filter_type=private&display_mode=<?php echo $display_mode; ?>' <?php if ($filter_type=='private') echo 'class="current"'; ?>>
			<?php _e("Private", "um"); ?> <span class="count">(<?php echo $unread_private_message_num; ?>)</span></a>
		</li>
		<li>| <a href='admin.php?page=user-messages&filter_type=public&display_mode=<?php echo $display_mode; ?>' <?php if ($filter_type=='public') echo 'class="current"'; ?>>
			<?php _e("Public", "um"); ?> <span class="count">(<?php echo $unread_public_message_num; ?>)</span></a>
		</li>
<?php if ($unread_message_num>0) { ?>
		<li>| <a href='admin.php?page=user-messages&filter_type=unread&display_mode=<?php echo $display_mode; ?>' <?php if ($filter_type=='unread') echo 'class="current"'; ?>>
			<?php _e("Unread", "um"); ?> <span class="count">(<?php echo $unread_message_num; ?>)</span></a>
		</li>
<?php } ?>
	</ul>

<form id="inbox-form" method="get" action="admin.php">
	<input type="hidden" name="page" value="user-messages" />	
	<input type="hidden" name="view" value="user-messages" />
	<?php wp_nonce_field('um_inbox_action'); ?>
	
	<div class="tablenav">
		<div class="alignleft actions">
			<select id="action" name="action">
				<option value="-1"><?php _e("Actions", "um"); ?></option>
				<option value="delete"><?php _e("Delete", "um"); ?></option>
				<option value="mark-read"><?php _e("Mark As Read", "um"); ?></option>
				<option value="mark-unread"><?php _e("Mark As Unread", "um"); ?></option>
			</select>
			<input type="submit" value="<?php _e("Apply", "um"); ?>" name="doaction" id="doaction" class="button-secondary action" />
		</div>
			
		<div class="view-switch">
			<a href="admin.php?page=user-messages&display_mode=subject&filter_type=<?php echo $filter_type; ?>">
				<img <?php if ($display_mode=='subject') echo 'class="current"'; ?> id="view-switch-list" src="../wp-includes/images/blank.gif" width="20" height="20" title="<?php _e("Show only subject","um"); ?>" alt="<?php _e("Show only subject","um"); ?>" /></a>
			<a href="admin.php?page=user-messages&display_mode=preview&filter_type=<?php echo $filter_type; ?>">
				<img <?php if ($display_mode=='subject') echo 'class="current"'; ?> id="view-switch-excerpt" src="../wp-includes/images/blank.gif" width="20" height="20" title="<?php _e("Show subject and content preview","um"); ?>" alt="<?php _e("Show subject and content preview","um"); ?>" /></a>
		</div>
	
		<div class="clear"></div>
	</div>
	
	<div class="clear"></div>
		
	<table class="widefat fixed" cellspacing="0">
		<thead>
		<tr>
			<th class="column-cb check-column"><input type="checkbox" /></th>
			<th class="icon"></th>
			<th class="icon"></th>
			<th class="um-time-column"><?php _e("Date", "um"); ?></th>
			<th class="um-from-column"><?php _e("From", "um"); ?></th>
			<th class="um-subject-colum"><?php if ($display_mode=="subject") _e("Subject", "um"); else _e("Message", "um"); ?></th>
		</tr>
		</thead>
	
		<tfoot>
		<tr>
			<th class="column-cb check-column"><input type="checkbox" /></th>
			<th class="icon"></th>
			<th class="icon"></th>
			<th class="um-time-column"><?php _e("Date", "um"); ?></th>
			<th class="um-from-column"><?php _e("From", "um"); ?></th>
			<th class="um-subject-colum"><?php if ($display_mode=="subject") _e("Subject", "um"); else _e("Message", "um"); ?></th>
		</tr>
		</tfoot>
	
		<tbody>
			<?php 		
				if (empty($messages)) {
			?>
		<tr>
			<td colspan="6"><?php _e("There are no messages to show."); ?></td>
		</tr>
			<?php 	
				}			
				foreach ($messages as $msg) {
			?>
		<tr <?php if ($msg->status==UM_UNREAD_MESSAGE_STATUS) echo 'class="msg-unread"'; ?>>
			<th scope="row" class="check-column"><input type="checkbox" name="message_ids[]" value="<?php echo $msg->message_id; ?>" /></th>
			<td class="icon"><?php echo $this->get_message_status_text($msg); ?></td>
			<td class="icon"><?php echo $this->get_message_type_text($msg); ?></td>
			<td><?php echo $this->get_message_date_text($msg); ?></td>
			<td><?php echo $this->get_message_author_text($msg); ?></td>
			<td><?php echo $this->get_message_subject_text($msg, $display_mode); ?></td>
		</tr>
			<?php
				}
			?>
		</tbody>
	</table>
</form>

<script type="text/javascript">
// <![[CDATA
	jQuery("#doaction").click(function() {
		if (jQuery("#action option:selected").val() == -1) {
			alert('<?php addslashes(_e("You have to select an action", "um")); ?>');
			return false;
		}
		
		return confirm('<?php addslashes(_e("Are you sure that you want to execute this action on the selected messages?", "um")); ?>');
	});
// ]]>
</script>

<?php        
    }
    
    /**
     * Filter the messages according to the filter type
     */
    private function filter_messages($filter_type, $messages) {
    	$result = array();
    	foreach ($messages as $msg) {
    		if ($filter_type=='all') {
    			$result[] = $msg;
    		} else if ($filter_type=='unread' && $msg->status==UM_UNREAD_MESSAGE_STATUS) {
    			$result[] = $msg;
    		} else if ($filter_type=='private' && $msg->type==UM_PRIVATE_MESSAGE_TYPE) {
    			$result[] = $msg;
    		} else if ($filter_type=='public' && $msg->type==UM_PUBLIC_MESSAGE_TYPE) {
    			$result[] = $msg;
    		}
    	}
    	return $result;
    }
    
    /**
     * Get the link when showing the author of the message
     */
    private function get_message_author_text($msg) {
    	return UM_AdminInterface::get_write_to_link($msg->get_author());
    }
    
    /**
     * Get the link when showing the subject of the message
     */
    private function get_message_subject_text($msg, $display_mode="subject") {    	
    	if ($display_mode=="subject") {
    		$output = UM_AdminInterface::get_show_message_link($msg);
    	} else {
			$output = '<div class="um-msg-subject">' . UM_AdminInterface::get_show_message_link($msg) . '</div>';
			$output .= '<hr class="um-content-separator" />';
			$output .= '<div class="um-msg-content">' . $msg->get_content_preview_html() . ' ' . UM_AdminInterface::get_show_message_link($msg, "[...]") . '</div>';
    	}
    	
    	return $output;
    }
    
    /**
     * Get the text for the message date
     */
    private function get_message_date_text($msg) {
    	$format = get_option('date_format') . ' ' . get_option('time_format');
		$output = date($format, mysql2date('G', $msg->timestamp));
		return $output;
    }
    
    /**
     * Count the messages that have an unread status
	 * $message_type can be used to filter the messages according to their type (defaults to -1 to cancel filtering)
     */
    private function count_unread_messages($messages, $message_type=UM_ALL_MESSAGE_TYPES) {
    	$acc = 0;
    	foreach ($messages as $msg) {
    		if ($msg->status==UM_UNREAD_MESSAGE_STATUS) {
				if ($message_type==UM_ALL_MESSAGE_TYPES) {
					$acc++;
				} else {
					if ($msg->type==$message_type) {
						$acc++;
					}
				}
			}
    	}
    	return $acc;
    }
    
	/**
	* Get the proper icon according to the message type
	*/
	private function get_message_type_text($msg) {
		return UM_AdminInterface::get_message_type_icon($msg->type, count($msg->recipient_ids));
	}
	
	/**
	* Get the proper icon according to the message status
	*/
	private function get_message_status_text($msg) {
		return UM_AdminInterface::get_message_status_icon($msg->status);
	}
	
	/**
	 * Perform bulk actions on the messages
	 */
	private function do_action($action, $message_ids) {
		global $user_ID;
		$count = 0;
		
		echo '<div id="message" class="updated fade">';
				
		if (empty($message_ids)) {
			echo '<p>' . __("No messages were selected", "um") . '</p>';
			echo "</div>";
			return;
		}
		
		echo "<ul>";
		foreach ($message_ids as $message_id) {
			$msg = UM_MessageDAO::find_message_by_id($message_id);

			// Check the message exists
			//--
			if (!isset($msg)) {
				echo '<li>' . sprintf(__("The message with ID %d does not exist", "um"), $message_id) . '</li>';
				continue;
			}
		
			// Check that our user is indeed owner of this message
			//--
			if ($msg->owner_id!=$user_ID) {
				echo '<li>' . sprintf(__("The message with ID %d does not belong to you", "um"), $message_id) . '</li>';
				continue;
			}
				
			// Execute action on the message
			//--
			if ($action=="delete") {
				UM_MessageDAO::delete_messages($message_id);
			} else if ($action=="mark-read") {
				UM_MessageDAO::update_message_status($message_id, UM_READ_MESSAGE_STATUS);
			} else if ($action=="mark-unread") {
				UM_MessageDAO::update_message_status($message_id, UM_UNREAD_MESSAGE_STATUS);
			}
			
			$count++;
		}
		
		echo "</ul>";
		echo "<p>" . sprintf(__("The selected action has been performed on %d messages", "um"), $count) . "</p>";
		echo "</div>";
	}
}
  
?>