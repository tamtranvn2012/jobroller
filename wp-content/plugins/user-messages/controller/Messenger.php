<?php

/**
 * The class managing the communications between the users
 */
class UM_Messenger {

	/**
	 * Constructor
	 */
	function UM_Messenger() {
	}

	/**
	 * Send a message from one user to the specified users
	 *
	 * @param $author_id The ID of the user sending the message
	 * @param $subject The subject of the message
	 * @param $content The content of the message
	 * @param $recipient_ids An array containing the IDs of the users to send the message to
	 * 
	 * @return See function "send_message()"
	 */
	function send_private_message($author_id, $subject, $content, $recipient_ids, $save_sent_message = false) {
		return $this->send_message(
			$author_id, UM_PRIVATE_MESSAGE_TYPE, 
			$subject, $content, $recipient_ids, $save_sent_message);
	}

	/**
	 * Send a message from one user to all the other users
	 *
	 * @param $author_id The ID of the user sending the message
	 * @param $subject The subject of the message
	 * @param $content The content of the message
	 *
	 * @return See function "send_message()"
	 */
	function send_public_message($author_id, $subject, $content, $save_sent_message = false) {
		return $this->send_message(
			$author_id, UM_PUBLIC_MESSAGE_TYPE, 
			$subject, $content, array(), $save_sent_message);
	}

	/**
	 * Send a message to one or more users.
	 *
	 * @param $author_id The ID of the user sending the message
	 * @param $subject The subject of the message
	 * @param $content The content of the message
	 * @param $recipient_ids This parameter can be ignored in the case of a public message. Else it should contain an array of user ids.
	 *
	 * @return an array with the following information
	 *   'delivered'     	a list of the users that got the message
	 *   'inbox_full_error' a list of the users whose inbox is full
	 * 	 'rejected_error'	a list of the users who do not accept the public messages
	 *   'general_errors'   a list of general errors
	 */
	private function send_message($author_id, $type, $subject, $content, $recipient_ids, $save_sent_message) {
		global $um_plugin, $wpdb, $um_notifier;
		 
		$result = array('delivered' => array(), 'quota_error' => array(),
        	'rejected_error' => array(), 'general_errors' => array());
		
		// Check the sender is allowed to send public messages
		//--
		if ($type==UM_PUBLIC_MESSAGE_TYPE && !current_user_can(UM_SEND_PUBLIC_MESSAGES_CAP)) {
			$result['general_errors'][] = __("You are not allowed to send public messages", "um");
			return $result;
		} else if ($type==UM_PRIVATE_MESSAGE_TYPE && !current_user_can(UM_SEND_PRIVATE_MESSAGES_CAP)) {
			$result['general_errors'][] = __("You are not allowed to send private messages", "um");
			return $result;
		} else if ($type!=UM_PRIVATE_MESSAGE_TYPE && $type!=UM_PUBLIC_MESSAGE_TYPE) {
			$result['general_errors'][] = __("Unknown message type", "um");
			return $result;
		}

		// For a public message, the users are all the users of the blog
		//--
		if ($type==UM_PUBLIC_MESSAGE_TYPE) {
			$recipient_ids = $wpdb->get_col("SELECT id FROM $wpdb->users");
		}
		
		// Prepare the message
		//--
		$msg = new UM_Message();
		$msg->type = $type;
		$msg->author_id = $author_id;
		$msg->subject = $subject;
		$msg->content = $content;
		$msg->timestamp = current_time('mysql', 1);
		$msg->recipient_ids = $recipient_ids;

		// Hook for eventual plugins (modify message)
		//--
		$msg = apply_filters('um-before-send-message', $msg);

		// For each recipient
		//--
		foreach ($recipient_ids as $recipient_id) {
		
			// Check the recipient agrees to receive messages
			//--
			if ($type==UM_PUBLIC_MESSAGE_TYPE && 'false'==get_usermeta($recipient_id, UM_ACCEPT_PUBLIC_MESSAGES_USER_META)) {
				$result['rejected_error'][] = $recipient_id;
				continue;
			} else if ($type==UM_PRIVATE_MESSAGE_TYPE && 'false'==get_usermeta($recipient_id, UM_ACCEPT_PRIVATE_MESSAGES_USER_META)) {
				$result['rejected_error'][] = $recipient_id;
				continue;
			} 

			// Check the recipient inbox is not full
			//--
			if ($this->is_user_exceeding_quota($recipient_id)) {
				$result['quota_error'][] = $recipient_id;
				$um_notifier->send_quota_exceeded_notification($recipient_id);
			} 

			// Check the recipient inbox is over quota
			//--
			if ($this->is_inbox_full($recipient_id)) {
				$result['inbox_full_error'][] = $recipient_id;
				$um_notifier->send_inbox_full_notification($recipient_id);
				continue;
			}

			// Set the message owner and folder
			//--
			$msg->folder_id = UM_INBOX_FOLDER;
			$msg->owner_id = $recipient_id;

			// Save the message in DB
			//--
			$msg_id = UM_MessageDAO::insert_message($msg);

			// Notify the recipient by email if he wants to
			//--
			if ('true'==get_usermeta($recipient_id, UM_NEW_MESSAGE_NOTIFICATION_USER_META)) {
				$um_notifier->send_new_message_notification($recipient_id, $msg_id);
			}

			// Hook for eventual plugins (message sent to recipient)
			//--
			do_action('um-message-sent', $msg, $recipient_id);

			$result['delivered'][] = $recipient;
			
			// Save a copy of the message in the sent folder if necessary
			//--
			if (true==$save_sent_message) {
				$msg->owner_id = $msg->author_id;
				$msg->folder_id = UM_SENT_FOLDER;
				UM_MessageDAO::insert_message($msg);
			}
		}
		
		return $result;
	}

	/**
	 * Send an email to a user.
	 *
	 * @param $author_id The ID of the user sending the message
	 * @param $subject The subject of the message
	 * @param $content The content of the message
	 * @param $recipient_id The user ID of the recipient of the email
	 *
	 * @return The result of the wp_mail function
	 */
	function send_email($author_id, $subject, $content, $recipient_id) {
		
		$recipient = new WP_User($recipient_id);
		$author = new WP_User($author_id);
		
		// Prepare the body for a plain text email send
		//--
		$body = preg_replace('|&[^a][^m][^p].{0,3};|', '', $body);
		$body = preg_replace('|&amp;|', '&', $body);
		$body = wordwrap(strip_tags($body), 80, "\n");
		
		$headers  = 'From: "' . $author->display_name . '" <' . $author->user_email . ">\n";
		$headers .= "Return-Path: <" . $author->user_email . ">\n";
		$headers .= "Reply-To: \"" . $author->display_name . "\" <" . $author->user_email . ">\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: text/plain; charset=\"". get_bloginfo('charset') . "\"\n";
		
		// Send email
		//--
		return wp_mail($recipient->user_email, $subject, $content, $headers);
	}
	
	/**
	 * Tell if the user exceeds his quota
	 */
	function is_inbox_full($user_id) {
		global $um_plugin;
		return UM_MessageDAO::count_user_messages($user_id) >= $um_plugin->options['default_inbox_capacity'];
	}
	
	/**
	 * Tell if the user is about to exceed his quota
	 */
	function is_user_exceeding_quota($user_id) {
		global $um_plugin;
		return UM_MessageDAO::count_user_messages($user_id) >= $um_plugin->options['user_quota'];
	}
}

?>