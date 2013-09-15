<?php

/**
 * The class managing the notifications sent to the users
 */
class UM_Notifier {

	/**
	 * Constructor
	 */
	function UM_Notifier() {
	}
	
	/**
	 * Notify a set of user that he has received a new message
	 */
	function send_new_message_notification($user_id, $message_id) {
		return $this->send_notification(UM_NEW_MESSAGE_NOTIFICATION, $user_id, $message_id);
	}
	
	/**
	 * Notify a set of user that his inbox is full and he cannot receive the message somebody wanted to send him.
	 */
	function send_inbox_full_notification($user_id) {
		return $this->send_notification(UM_INBOX_FULL_NOTIFICATION, $user_id);
	}
	
	/**
	 * Notify a set of user that he is exceeding his inbox quota and that he should delete some messages soon.
	 */
	function send_quota_exceeded_notification($user_id) {
		return $this->send_notification(UM_QUOTA_EXCEEDED_NOTIFICATION, $user_id);
	}
	
	/**
	 * Send a notification to a set of users. The notifications will first be put in the queue to avoid 
	 * overloading the mail server. They are then processed by the CRON task.
	 */
	function send_notification($type, $user_id, $message_id = 0) {
		UM_NotificationDAO::insert_notification($type, $user_id, $message_id);
	}
	
	/**
	 * Immediately send a notification to a user.
	 */
	function do_notify_user($notification) {	
		global $um_plugin;
	
		$recipient = $notification->get_user();
		$message = $notification->get_message();
			
		// Get the mail content according to the notification type
		//--
		switch ($notification->type) {
			case UM_NEW_MESSAGE_NOTIFICATION:
				$base_subject = $um_plugin->options['new_message_notification_subject'];
				$base_body = $um_plugin->options['new_message_notification_body'];
				break;
			case UM_INBOX_FULL_NOTIFICATION:
				$base_subject = $um_plugin->options['inbox_full_notification_subject'];
				$base_body = $um_plugin->options['inbox_full_notification_body'];
				break;
			case UM_QUOTA_EXCEEDED_NOTIFICATION:
				$base_subject = $um_plugin->options['over_quota_notification_subject'];
				$base_body = $um_plugin->options['over_quota_notification_body'];
				break;
			default:
				$base_subject = 'Unknown notification type';
				$base_body = 'Unknown notification type';
		}
		
		// Replace variables in the body and subject
		//--
		$subject = $this->replace_notification_variables( $base_subject, $recipient, $message );
		$body = $this->replace_notification_variables( $base_body, $recipient, $message );		
		
		// Prepare the body for a plain text email send
		//--
		$body = preg_replace('|&[^a][^m][^p].{0,3};|', '', $body);
		$body = preg_replace('|&amp;|', '&', $body);
		$body = wordwrap(strip_tags($body), 80, "\n");
		
		// Send email
		//--
		wp_mail($recipient->user_email, $subject, $body);
	}

	/**
	* Replace the notification variables in the given string
	*/
	private function replace_notification_variables($in, $recipient=null, $message=null) {
		global $user_ID, $um_plugin;
	
		$out = preg_replace( '/%BLOG_NAME%/', get_option('blogname'), $in );
		$out = preg_replace( '/%BLOG_URL%/', get_option('siteurl'), $out );
		$out = preg_replace( '/%USER_MESSAGES_URL%/', get_option('siteurl') . "/wp-admin/admin.php?page=user-messages", $out );
		
		$out = preg_replace( '/%TOTAL_MESSAGE_COUNT%/', UM_MessageDAO::count_user_messages($user_ID), $out );
		$out = preg_replace( '/%UNREAD_MESSAGE_COUNT%/', UM_MessageDAO::count_unread_user_messages($user_ID), $out );
		$out = preg_replace( '/%USER_QUOTA%/', $um_plugin->options['user_quota'], $out );
		
		if (isset($recipient)) {
			$out = preg_replace( '/%RECIPIENT_NAME%/', $recipient->display_name, $out );
		}
		
		if (isset($message) && $message->message_id!=0) {
			$out = preg_replace( '/%MESSAGE_AUTHOR%/', $message->get_author()->display_name, $out );
			$out = preg_replace( '/%MESSAGE_SUBJECT%/', $message->get_raw_subject(), $out );
			$out = preg_replace( '/%MESSAGE_CONTENT%/', $message->get_raw_content(), $out );
		}
		
		return $out;
	}

	/**
	 * Function to fetch the pending notifications from the database and processes a batch of them.
	 */
	function process_pending_notifications() {
		global $um_plugin, $um_notifier;
		
		// Get a batch of notifications
		//--
		$notification_ids = array();
		$notifications = UM_NotificationDAO::find_next_notifications_to_send($um_plugin->options['notification_batch_size']);
		
		// Process each notification 
		//--
		foreach ($notifications as $notification) {
			$um_notifier->do_notify_user($notification);
			$notification_ids[] = $notification->notification_id;
		}
		
		// Remove the entries from the database
		//-- 
		UM_NotificationDAO::delete_notifications($notification_ids);
	}
}

?>