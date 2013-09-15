<?php

/**
* Class to show the dashboard features
* 
* @author Vincent Prat
*/

class UM_DashboardFeatures {
	
	/**
	* Constructor
	*/
	function UM_DashboardFeatures() {
	}
	
	/**
	 * Function to add the widget to the dashboard 
	 */
	function setup() {
		if (current_user_can(UM_RECEIVE_MESSAGES_CAP)) {
			wp_add_dashboard_widget('user-messages', __('Messages', 'um'), array(&$this, 'render_widget'));
			add_action('right_now_table_end', array(&$this, 'add_right_now_table_row'));
		}
	}
	
	/**
	* Function to render the widget control panel
	*/
	function render_widget() {
		global $user_ID, $um_plugin, $um_messenger;
		
		// Display a warning if the user exceeds his quota
		//--
		UM_AdminInterface::check_quota_and_warn($user_id);
		
		$unread_message_count = UM_MessageDAO::count_unread_user_messages($user_ID);
		
		if ($unread_message_count==0) {
?>			
			<div class="um-dashboard-table">
			<table>
				<tr><td class="first"><?php _e("You don't have any new message", "um"); ?></td></tr>
			</table>
			</div>
<?php
		} else {
			echo '<p>' . sprintf(_n("You have %s new message", "You have %s new messages", $unread_message_count, "um"), $unread_message_count) . '</p>';
			
			$new_messages = UM_MessageDAO::find_user_messages($user_ID, UM_UNREAD_MESSAGE_STATUS, UM_ALL_MESSAGE_TYPES);
?>			
			<div class="um-dashboard-table">
			<table>
				<?php 
					$i = 0;
					foreach ($new_messages as $msg) { 
				    	$format = 'd-m-Y H:i';
						$date = date($format, mysql2date('G', $msg->timestamp));
				?>
				<tr>
					<td class="um-dashboard-date <?php if ($i==0) echo "first"; ?>"><?php echo UM_AdminInterface::get_show_message_link($msg, $date); ?></td>
					<td class="um-dashboard-author <?php if ($i==0) echo "first"; ?>"><?php echo UM_AdminInterface::get_show_message_link($msg, $msg->get_author()->display_name); ?></td>
					<td class="um-dashboard-subject <?php if ($i==0) echo "first"; ?>"><?php echo UM_AdminInterface::get_show_message_link($msg); ?></td>
				</tr>
				<?php 
						$i++;
					} ?>
			</table>
			</div>
<?php
		}
?>

			
			<div class="um-dashboard-actions">
<?php 	if (current_user_can(UM_RECEIVE_MESSAGES_CAP)) { ?>
				<a id="um-back-button" class="button" href="admin.php?page=user-messages">
					<img class="um-button-icon" src="<?php echo UM_THEMEURL; ?>/inbox-icon.png" title="<?php _e("Inbox", "um")?>" alt="<?php _e("Inbox", "um")?>" /></a>
<?php 	} 
		if (current_user_can(UM_SEND_PRIVATE_MESSAGES_CAP) || current_user_can(UM_SEND_PUBLIC_MESSAGES_CAP)) { ?>
				<a id="um-write-msg-button" class="button" href="admin.php?page=um-write-message-view">
					<img class="um-button-icon" src="<?php echo UM_THEMEURL; ?>/write-msg-icon.png" title="<?php _e("Write Message", "um")?>" alt="<?php _e("Write Message", "um")?>" /></a>
<?php 	} ?>
			</div>

<?php
	}
	
	/**
	* Add a row to the table in the "Right Now" widget 
	*/
	function add_right_now_table_row() {
		global $user_ID;
	
		$message_count = UM_MessageDAO::count_user_messages($user_ID);
		$unread_message_count = UM_MessageDAO::count_unread_user_messages($user_ID);
		
		echo '<tr>';
		echo '<td class="first b"><a href="admin.php?page=user-messages">' . $message_count . '</a></td>';
		echo '<td class="t"><a href="admin.php?page=user-messages">' . _n('Message', 'Messages', $message_count, 'um') . '</a></td>';
		echo '<td class="b"><a href="admin.php?page=user-messages&filter_type=unread">' . $unread_message_count . '</a></td>';
		echo '<td class="last t"><a href="admin.php?page=user-messages&filter_type=unread" style="color: #FF00E2;">' . __('New', 'um') . '</a></td>';
		echo '</tr>';
	}
} 

?>