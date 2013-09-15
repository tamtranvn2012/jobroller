<?php

/**
* Class to access the messages for a user
* 
* @author Vincent Prat        
*/
class UM_MessageDAO {
    
    /**
    * Constructor
    */
    function UM_MessageDAO() {
    }
    
    /**
    * Fetch the messages belonging to the given user
    * @param $user_id The id of the user
    * @return an array of UserMessage objects
    */
    function find_user_messages( $user_id, $status=UM_ALL_MESSAGE_STATUS, $type=UM_ALL_MESSAGE_TYPES, $order_by = 'timestamp_gmt', $order = 'desc' ) {
        global $wpdb;
        
        $sql  = "SELECT * FROM {$wpdb->um_message} WHERE owner_id = %d";
		if ($type!=UM_ALL_MESSAGE_TYPES) $sql .= " AND type=" . $type;
		if ($status!=UM_ALL_MESSAGE_STATUS) $sql .= " AND status=" . $status;
		$sql .= " ORDER BY {$order_by} {$order}";
        $results = $wpdb->get_results($wpdb->prepare($sql, $user_id));
        
        // Convert result to an array of objects
        //--
        $messages = array();
        foreach ($results as $result) {
            $messages[] = UM_MessageDAO::get_message_from_row($result);
        }
        
        return $messages;
    }
    
    /**
    * Count the unread messages belonging to the given user
    * @param $user_id The id of the user
    * @return The number of messages
    */
    function count_unread_user_messages( $user_id ) {
        global $wpdb;
        
        $sql = "SELECT count(id) FROM {$wpdb->um_message} WHERE owner_id = %d AND status=" . UM_UNREAD_MESSAGE_STATUS;
        $result = $wpdb->get_var($wpdb->prepare($sql, $user_id));
        
        return $result;
    }
    
    /**
    * Count the messages belonging to the given user
    * @param $user_id The id of the user
    * @return The number of messages
    */
    function count_user_messages( $user_id ) {
        global $wpdb;
        
        $sql = "SELECT count(id) FROM {$wpdb->um_message} WHERE owner_id = %d";
        $result = $wpdb->get_var($wpdb->prepare($sql, $user_id));
        
        return $result;
    }
    
    /**
    * Fetch the message with the given ID
    * @param $message_id The id of the message
    * @return a UserMessage object or null
    */
    function find_message_by_id($message_id) {
    	global $wpdb;
    	
   		$sql = "SELECT * FROM {$wpdb->um_message} WHERE id=%d";
   		$result = $wpdb->get_row($wpdb->prepare($sql, $message_id));
   		
   		// Convert result to an object
   		//--
   		$message = isset($result) ? UM_MessageDAO::get_message_from_row($result) : null;
   		
   		return $message;
    }
    
    /**
     * Update the status of a message
     */
    function update_message_status($message_id, $new_status) {
    	global $wpdb;
    	$wpdb->query("UPDATE {$wpdb->um_message} SET status={$new_status} WHERE id={$message_id}");
    }
    
    /**
    * Delete one or more messages
    * 
    * @param an array of integers or a single integer representing the id(s) of the message(s) to delete
    */
    function delete_messages( $ids ) {
        global $wpdb;
        
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        
        $id_list = implode(",", $ids);
        
        $sql = "DELETE FROM {$wpdb->um_message} WHERE id in ($id_list)";
        $wpdb->query($sql);
    }
    
    /**
    * Insert the given message into the database
    * @return The new message ID
    */
    function insert_message($msg) {
        global $wpdb;
        
        // Insert message details
        //--
        $sql = "INSERT INTO {$wpdb->um_message} (type, status, subject, content, author_id, owner_id, recipient_ids, timestamp_gmt) VALUES ("
            . "   " . $msg->type 
            . ",  " . $msg->status 
            . ", '" . $wpdb->escape($msg->subject) . "'"
            . ", '" . $wpdb->escape($msg->content) . "'"
            . ",  " . $msg->author_id
            . ",  " . $msg->owner_id
            . ", '" . implode(",", $msg->recipient_ids) . "'"
            . ", '" . $msg->timestamp . "'"
            . " )";            
        $wpdb->query($sql);
        
        // Get new message ID
        //--
        $new_id = $wpdb->get_var("SELECT max(id) FROM {$wpdb->um_message}");        
        return $new_id;
    }
    
    /**
    * Convert a database row into a message
    */
    private function get_message_from_row($row) {
        $msg = new UM_Message();
        $msg->message_id = $row->id;
        $msg->subject = stripslashes($row->subject);
        $msg->content = stripslashes($row->content);
        $msg->status = $row->status;
        $msg->type = $row->type;
        $msg->author_id = $row->author_id;
        $msg->owner_id = $row->owner_id;
        $msg->recipient_ids = explode(",", $row->recipient_ids);
        $msg->timestamp = $row->timestamp_gmt;
        return $msg;
    }
}

?>