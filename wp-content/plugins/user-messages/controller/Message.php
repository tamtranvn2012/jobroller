<?php

/**
* Class to hold information about a user message
* 
* @author Vincent Prat
*/
class UM_Message {
    
    /** The id of the message */
    var $message_id = 0;
    
    /** The id of the author of the message */
    var $author_id = 0;
    
    /** The id of the owner of the message */
    var $owner_id = 0;
    
    /** The type of message */
    var $type = UM_PUBLIC_MESSAGE_TYPE;
    
    /** The folder in which the message is put */
    var $folder_id = UM_INBOX_FOLDER;
    
    /** The status of the message */
    var $status = UM_UNREAD_MESSAGE_STATUS;
    
    /** 
    * The ids of the recipient(s) of the message. 
    * - In the case of a public message, this field is not used. 
    * - In the case of a private message, this is an array of user ids.
    */
    var $recipient_ids = array();           
    
    /** The subject of the message */
    var $subject = "";           
    
    /** The content of the message */
    var $content = "";
    
    /** The time the message was sent (GMT) */
    var $timestamp = "";
    
    /** Author of the message */
    var $author = null;
    
    /** Owner of the message */
    var $owner = null;
    
    /** 
    * The recipient(s) of the message. 
    * - In the case of a public message, this field is not used. 
    * - In the case of a private message, this is an array of user ids.
    */
    var $recipients = null;           
    
    /**
    * Constructor
    */
    function UM_Message() {
        $author = null;
        $recipients = null;
    }
       
    /**
    * Get the author of the message as a WP_User object              
    */
    function get_author() {
        if ($this->author==null) {
            $this->author = new WP_User($this->author_id);
        }
        return $this->author;
    }
       
    /**
    * Get the owner of the message as a WP_User object              
    */
    function get_owner() {
        if ($this->owner==null) {
            $this->owner = new WP_User($this->owner_id);
        }
        return $this->owner;
    }
    
    /**
    * Get the recipients of the message as an array of WP_User objects
    */
    function get_recipients() {
        if ($this->recipients==null) {
            $this->recipients = array();
            foreach ($this->recipient_ids as $id) {
                $this->recipients[] = new WP_User($id);
            }
        }
        return $this->recipients;
    }
	
	/**
	 * Get the raw subject of the message (applies the filter 'um_msg_get_raw_content')
	 */
	function get_raw_subject() {
		return apply_filters('um_msg_get_raw_subject', $this->subject);
	}
	
	/**
	 * Get the raw content of the message (applies the filter 'um_msg_get_raw_content')
	 */
	function get_raw_content() {
		return apply_filters('um_msg_get_raw_content', $this->content);
	}
       
    /**
    * Get the subject of the message              
    */
    function get_subject_html() {
        return htmlspecialchars($this->get_raw_subject());
    }
       
    /**
    * Get the content of the message              
    */
    function get_content_html() {
        return str_replace("\n", "<br/>", htmlspecialchars(wordwrap($this->get_raw_content(), 80, "\n")));
    }
       
    /**
    * Get a preview of the content of the message              
    */
    function get_content_preview_html() {
        return wp_html_excerpt($this->get_raw_content(), 150);
    }
}

?>
