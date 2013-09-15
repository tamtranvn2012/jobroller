<?php

/**
* View to adjust personal preferences set in the user profile page
* 
* @author Vincent Prat
*/
class UM_ProfileView {
    
	/**
	 * Output the profile section to set User Messages personnal preferences 
	 */
	function on_show_user_profile() {
		global $user_ID;
?>
	<h3><?php _e('User Messages Preferences', 'um'); ?></h3>

	<table class="form-table">
	<tbody>
		<tr>
			<th></th>
			<td>
			<?php 
				if (current_user_can(UM_IGNORE_PUBLIC_MESSAGES_CAP)) {
					UM_ProfileView::echo_meta_checkbox(UM_ACCEPT_PUBLIC_MESSAGES_USER_META); 
					_e('Accept to receive public messages', 'um'); 
					echo "<br/>";
				}
				
				if (current_user_can(UM_REFUSE_PRIVATE_MESSAGES_CAP)) {
					UM_ProfileView::echo_meta_checkbox(UM_ACCEPT_PRIVATE_MESSAGES_USER_META); 
					_e('Allow other users to contact you by private message', 'um'); 
					echo "<br/>";
				}
				
				UM_ProfileView::echo_meta_checkbox(UM_ACCEPT_EMAIL_USER_META);
				_e('Allow other users to contact you by email (your address will be not be shown to them)', 'um'); 
				echo "<br/>";
				
				UM_ProfileView::echo_meta_checkbox(UM_NEW_MESSAGE_NOTIFICATION_USER_META);
				_e('Be notified by email when you receive a new message', 'um'); 
				echo "<br/>";
			?>
				<br/>
			</td>
		</tr>
	</tbody>
	</table>
<?php
	}
	
	/**
	 * Called when the user updates his profile
	 */
	function on_user_profile_update() {
		global $_POST, $user_ID;
		
		if (current_user_can(UM_IGNORE_PUBLIC_MESSAGES_CAP)) {
			update_usermeta($user_ID, 
				UM_ACCEPT_PUBLIC_MESSAGES_USER_META, 
				isset($_POST[UM_ACCEPT_PUBLIC_MESSAGES_USER_META]) ? 'true' : 'false');
		}
		if (current_user_can(UM_REFUSE_PRIVATE_MESSAGES_CAP)) {
			update_usermeta($user_ID, 
				UM_ACCEPT_PRIVATE_MESSAGES_USER_META, 
				isset($_POST[UM_ACCEPT_PRIVATE_MESSAGES_USER_META]) ? 'true' : 'false');
		}
		update_usermeta($user_ID, 
			UM_ACCEPT_EMAIL_USER_META, 
			isset($_POST[UM_ACCEPT_EMAIL_USER_META]) ? 'true' : 'false');
		update_usermeta($user_ID, 
			UM_NEW_MESSAGE_NOTIFICATION_USER_META, 
			isset($_POST[UM_NEW_MESSAGE_NOTIFICATION_USER_META]) ? 'true' : 'false');
	}
	
	/**
	 * Display a checkbox field for the given boolean user meta flag
	 */
	function echo_meta_checkbox($meta_flag) {
		global $user_ID;
		
		$output  = '<input type="checkbox" id="' . $meta_flag . '" name="' . $meta_flag . '" value="true"';
		if (get_usermeta($user_ID, $meta_flag)=="true") {
			$output .= ' checked="checked"';
		}
		$output .= ' /> ';
		
		echo $output;
	}
    
}
  
?>
