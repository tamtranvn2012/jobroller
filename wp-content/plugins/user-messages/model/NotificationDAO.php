<?php

/**
* Class to access the pending notifications
* 
* @author Vincent Prat        
*/
class UM_NotificationDAO {
    
    /**
    * Constructor
    */
    function UM_NotificationDAO() {
    }
    
    /**
    * Fetch the oldest $count pending notifications. If $count is 0, it returns all the notifications.
    * @return an array of UM_Notification objects
    */
    function find_next_notifications_to_send($count = 0) {
        global $wpdb;
        
        $sql  = "SELECT * FROM {$wpdb->um_notification} ORDER BY ID ASC";
		if ($count>0) $sql .= " LIMIT {$count}";
        $results = $wpdb->get_results($wpdb->prepare($sql));
        
        // Convert result to an array of objects
        //--
        $notifications = array();
        foreach ($results as $result) {
            $notifications[] = UM_NotificationDAO::get_notification_from_row($result);
        }
        
        return $notifications;
    }
    
    /**
    * Fetch the notifications given their type (use 'all' to get all the notifications).
    * @return an array of UM_Notification objects
    */
    function find_notifications_by_type( $type='all', $order_by = 'timestamp_gmt', $order = 'desc' ) {
        global $wpdb;
        
        $sql  = "SELECT * FROM {$wpdb->um_notification} WHERE 1=1";
		if ($type!='all') $sql .= " AND type='" . $type . "'";
		$sql .= " ORDER BY {$order_by} {$order}";
        $results = $wpdb->get_results($wpdb->prepare($sql));
        
        // Convert result to an array of objects
        //--
        $notifications = array();
        foreach ($results as $result) {
            $notifications[] = UM_NotificationDAO::get_notification_from_row($result);
        }
        
        return $notifications;
    }
    
    /**
    * Fetch the notification with the given ID
    * @param $notification_id The id of the notification
    * @return a UserNotification object or null
    */
    function find_notification_by_id($notification_id) {
    	global $wpdb;
    	
   		$sql = "SELECT * FROM {$wpdb->um_notification} WHERE id=%d";
   		$result = $wpdb->get_row($wpdb->prepare($sql, $notification_id));
   		
   		// Convert result to an object
   		//--
   		$notification = isset($result) ? UM_NotificationDAO::get_notification_from_row($result) : null;
   		
   		return $notification;
    }
    
    /**
    * Delete one or more notifications
    * 
    * @param an array of integers or a single integer representing the id(s) of the notification(s) to delete
    */
    function delete_notifications( $ids ) {
        global $wpdb;
        
        if (!is_array($ids)) {
            $ids = array($ids);
        }
		
		if ( empty($ids) ) {
			return;
		}
        
        $id_list = implode(",", $ids);
        
        $sql = "DELETE FROM {$wpdb->um_notification} WHERE id in ($id_list)";
        $wpdb->query($sql);
    }
    
    /**
    * Insert the given notification into the database
    * @return The new notification ID
    */
    function insert_notification($type, $user_id, $message_id = 0) {
        global $wpdb;
        
        // Insert notification details
        //--
        $sql = "INSERT INTO {$wpdb->um_notification} (type, user_id, message_id) VALUES ("
            . "  '" . $type . "'"
            . ",  " . $user_id 
            . ",  " . $message_id
            . " )";            
        $wpdb->query($sql);
        
        // Get new notification ID
        //--
        $new_id = $wpdb->get_var("SELECT max(id) FROM {$wpdb->um_notification}");        
        return $new_id;
    }
    
    /**
    * Convert a database row into a notification
    */
    private function get_notification_from_row($row) {
        $notification = new UM_Notification();
        $notification->notification_id = $row->id;
        $notification->type = $row->type;
        $notification->user_id = $row->user_id;
        $notification->message_id = $row->message_id;
		
        return $notification;
    }
}

?>