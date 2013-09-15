<?php

/**
* Class to hold information about a notification
* 
* @author Vincent Prat
*/
class UM_Notification {
    
    /** The id of the notification */
    var $notification_id = 0;
    
    /** The id of the user to notify */
    var $user_id = 0;
    
    /** The id of the message related to this notification (if applicable) */
    var $message_id = 0;
    
    /** The type of notification */
    var $type = UM_NEW_MESSAGE_NOTIFICATION;
	
	/** The user to notify */
	var $user = null;
	
	/** The message related to this notification (if applicable) */
	var $message = null;
    
    /**
    * Constructor
    */
    function UM_Notification() {
		$user = null;
		$message = null;
    }
       
    /**
    * Get the author of the notification as a WP_User object              
    */
    function get_message() {
        if ($this->message==null) {
            $this->message = UM_MessageDAO::find_message_by_id($this->message_id);
        }
        return $this->message;
    }
       
    /**
    * Get the user to notify as a WP_User object              
    */
    function get_user() {
        if ($this->user==null) {
            $this->user = new WP_User($this->user_id);
        }
        return $this->user;
    }
}

?>
