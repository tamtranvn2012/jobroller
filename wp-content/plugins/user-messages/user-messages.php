<?php

/*
Plugin Name: User Messages
Plugin URI: http://user-messages.vincentprat.info
Description: Allow you users to communicate with each other in a flexible way. They can send private messages, emails, public messages, ... You can configure who is allowed to do what with the role manager plugin. <span style="display:block;border:1px solid red;color:red;font-weight:bold;">You are having the latest free version. However, since it has gone into commercial licensing, User Messages has plenty of bug fixes and lots of new cool features. Please visit <a href="http://user-messages.vincentprat.info">the plugin page</a> to know more about it!</span>
Author: Vincent Prat
Version: 1.2.4
Author URI: http://www.vincentprat.info
 
Copyright (c) Vincent Prat 2009
*/

if (!class_exists('UM_UserMessagesPlugin')) {

    /**
    * Main plugin class
    */
    class UM_UserMessagesPlugin {
        
        /** The version of the plugin */
        public $version = "1.2.4";
        
        /** The version of the db structure */
        public $db_version = "0";
        
        /** The options of the plugin */
        public $options = array();
        
        /**
        * Constructor
        */
        function UM_UserMessagesPlugin() {        		
            $this->load_textdomain();     
            $this->load_options();        
            $this->define_constants();
            $this->define_tables();  
            $this->load_dependencies();   
            $this->register_hooks();           
			$this->one_time_actions();   
        }
        
        /**
        * Load the options
        */
        function load_options() {
            $this->options = get_option('um_options'); 
        }
        
        /**
        * Save the options
        */
        function save_options() {
            update_option('um_options', $this->options);
        }
        
        /**
         * Called each time a user registers
         */
        function on_user_registration($user_id) {
        	update_usermeta($user_id, UM_ACCEPT_PUBLIC_MESSAGES_USER_META, 'true');
			update_usermeta($user_id, UM_ACCEPT_PRIVATE_MESSAGES_USER_META, 'true');
			update_usermeta($user_id, UM_ACCEPT_EMAIL_USER_META, 'true');
			update_usermeta($user_id, UM_NEW_MESSAGE_NOTIFICATION_USER_META, 'true');
        }
        
        /**
        * Called on plugin activation event
        */
        function on_activate() {
            // Look at the active plugin version before we activate this one
            //--
            $active_version = $this->options['active_version'];
            $active_db_version = $this->options['active_db_version'];
						
            if ($active_version==$this->version) {
                // do nothing
            } else {             
                if (empty($active_version)) {   
                    // The plugin has never been installed
                    //--         
                    $this->create_tables();
                    $this->add_default_capabilities();
                    $this->set_default_options();
                    $this->insert_sample_data();
                    $this->set_default_user_meta();
                    
                    add_option(
                        'um_options', 
                        $this->options, 
                        'User Messages plugin options');
			
					// Schedule the CRON events
					//--
					wp_schedule_event(time(), 'um-notifications-schedule', 'um_process_notifications'); 
				} else {                    
                    // We already have a version of the plugin installed, update
                    //--					
					if ($active_version<'1.2.0') {
						// Remove old capabilities that had a long name
						//--
						$old_caps = array('UM - Can Ignore Public Messages', 'UM - Can Refuse Private Messages', 
							'UM - Send Private Messages', 'UM - Send Public Messages', 'UM - Send Email Messages', 
							'UM - Recieve Messages', 'UM - Configure Plugin', 'UM - Use Plugin', 'UM-Ignore Public Msg',
							'UM-Refuse Private Msg', 'UM-Send Private Msg', 'UM-Send Public Msg', 'UM-Send Email Msg',
							'UM-Recieve Msg', 'UM Recieve Msg', 'UM-Configure Plugin', 'UM-Use Plugin');
			
						$roles = array("subscriber", "contributor", "author", "editor", "administrator");
						foreach ($roles as $role) {
							$role = get_role($role);
							if (!empty($role)) {
								foreach ($old_caps as $cap) {
									$role->remove_cap($cap);
								}									
							}
						}
						
						// add new capabilities again
						//--
						$this->add_default_capabilities();
					}
                }
            }          
            
            // Save the new active version 
            //--
            $this->options['active_version'] = $this->version;
            $this->options['active_db_version'] = $this->db_version;
            
            // Save options
            //--                                                                                        
            $this->save_options();
        }
        
        /**
        * Called on plugin deactivation event
        */
        function on_deactivate() {
			wp_clear_scheduled_hook('um_process_notifications');
        }
        
        /**
        * Uninstall the plugin (removes DB tables, ...)
        */
        function on_uninstall() {
            global $wpdb;
            
            // Drop the plugin tables 
            //--
            $wpdb->query("DROP TABLE IF EXISTS {$wpdb->um_message}");
            
            // Remove plugin options
            //--
            delete_option('um_options');
            
            // Remove unused capabilities
            //--
            $roles = array("subscriber", "contributor", "author", "editor", "administrator");
            foreach ($roles as $role) {
                $role = get_role($role);
                if (!empty($role)) {
                    $role->remove_cap(UM_IGNORE_PUBLIC_MESSAGES_CAP);
                    $role->remove_cap(UM_REFUSE_PRIVATE_MESSAGES_CAP);
                    $role->remove_cap(UM_SEND_PRIVATE_MESSAGES_CAP);
                    $role->remove_cap(UM_SEND_PUBLIC_MESSAGES_CAP); 
                    $role->remove_cap(UM_SEND_EMAIL_MESSAGES_CAP); 
                    $role->remove_cap(UM_RECEIVE_MESSAGES_CAP); 
                    $role->remove_cap(UM_CONFIGURE_PLUGIN_CAP); 
                    $role->remove_cap(UM_USE_PLUGIN_CAP);                           
                }
            }
            
            // Remove user flags
            //--
			$user_ids = $wpdb->get_col("SELECT id FROM $wpdb->users");
			foreach ($user_ids as $id) {
				delete_usermeta($id, UM_ACCEPT_PUBLIC_MESSAGES_USER_META);
				delete_usermeta($id, UM_ACCEPT_PRIVATE_MESSAGES_USER_META);
				delete_usermeta($id, UM_ACCEPT_EMAIL_USER_META);
				delete_usermeta($id, UM_NEW_MESSAGE_NOTIFICATION_USER_META);
	        }
        }       
		
		/**
		* Send a POST request from the code
		*/
		function send_post_request($url, $referer, $_data) {	 
			// convert variables array to string:
			$data = array();    
			while(list($n,$v) = each($_data)){
				$data[] = "$n=$v";
			}    
			$data = implode('&', $data);
			// format --> test1=a&test2=b etc.
		 
			// parse the given URL
			$url = parse_url($url);
			if ($url['scheme'] != 'http') { 
				return array("", "false");
			}
		 
			// extract host and path:
			$host = $url['host'];
			$path = $url['path'];
		 
			// open a socket connection on port 80
			$fp = fsockopen($host, 80);
			if (!$fp) { 
				return array("", "false");
			}
				
			// send the request headers:
			fputs($fp, "POST $path HTTP/1.1\r\n");
			fputs($fp, "Host: $host\r\n");
			fputs($fp, "Referer: $referer\r\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ". strlen($data) ."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $data);
		 
			$result = ''; 
			while(!feof($fp)) {
				// receive the results of the request
				$result .= fgets($fp, 128);
			}
		 
			// close the socket connection:
			fclose($fp);
		 
			// split the result header from the content
			$result = explode("\r\n\r\n", $result, 2);
		 
			$header = isset($result[0]) ? $result[0] : '';
			$content = isset($result[1]) ? $result[1] : '';
		 
			// return as array:
			return array($header, $content);
		}
	 
		/**
		* Do all one time actions that need to be done
		*/
		function one_time_actions() {
            $current_time = time();
            $last_register_attempt = empty( $this->options['last_register_attempt'] ) ? 0 : (int) $this->options['last_register_attempt'];
            
			if ( (string) $this->options['registered']!=$this->version && ( $current_time - $last_register_attempt > 600 ) ) {				
				$host = "http://www.vincentprat.info/wp_plugins_register.php";
				$params = array(
					'plugin_name' 		=> 'user-messages',
					'plugin_version' 	=> $this->version,
					'host' 				=> get_option('siteurl'),
                    'valid' 			=> 'true'
				);
				
				$old_err_level = error_reporting(E_ERROR);
				list($header, $content) = $this->send_post_request($host, get_option('siteurl'), $params);
				error_reporting($old_err_level);
				
				if ($content=='true') {
					$this->options['registered'] = $this->version;
				}
                $this->options['last_register_attempt'] = time();
				$this->save_options();
			}
		} 
                                               
        /**
        * Register the WordPress hooks used by the plugin
        */
        private function register_hooks() {
			global $um_notifier;
			
            register_activation_hook(dirname(__FILE__) . '/user-messages.php', array(&$this, 'on_activate'));
            register_deactivation_hook(dirname(__FILE__) . '/user-messages.php', array(&$this, 'on_deactivate'));    

            add_action('user_register', array(&$this, 'on_user_registration'));
			
			add_action('um_process_notifications', array(&$um_notifier, 'process_pending_notifications'));
			add_filter('cron_schedules', array(&$this, 'cron_schedules_filter'));	
            
            if (function_exists('register_uninstall_hook')) {                                              
                register_uninstall_hook(dirname(__FILE__) . '/user-messages.php', array(&$this, 'on_uninstall'));
            }
        }
        
        /**
        * Define the tables that will be used in the plugin
        */
        private function define_tables() {        
            global $wpdb;
                                          
            // add our database tables to the global $wpdb
            //--
            $wpdb->um_message = $wpdb->prefix . 'um_message';  
            $wpdb->um_notification = $wpdb->prefix . 'um_notification';  
        }
        
        /**
        * Define a few constants
        */
        private function define_constants() {
            
            // name, URL and absolute directory of the plugin
            //--
            define('UM_FOLDER', plugin_basename(dirname(__FILE__)));
            define('UM_ABSPATH', str_replace("\\", "/", WP_PLUGIN_DIR . '/' . plugin_basename(dirname(__FILE__)) . '/'));
            define('UM_BASEURL', WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)) . '/');        
            define('UM_THEMEURL', $this->options['theme_url']);        
            
            // Capabilities used by the plugin
            //--
            define('UM_IGNORE_PUBLIC_MESSAGES_CAP', 'UM Ignore Public Msg');
            define('UM_REFUSE_PRIVATE_MESSAGES_CAP', 'UM Refuse Private Msg');
            define('UM_SEND_PRIVATE_MESSAGES_CAP', 'UM Send Private Msg');
            define('UM_SEND_PUBLIC_MESSAGES_CAP', 'UM Send Public Msg');
            define('UM_SEND_EMAIL_MESSAGES_CAP', 'UM Send Email Msg');
            define('UM_RECEIVE_MESSAGES_CAP', 'UM Receive Msg');
            define('UM_CONFIGURE_PLUGIN_CAP', 'UM Configure Plugin');
            define('UM_USE_PLUGIN_CAP', 'UM Use Plugin');
            
            // The possible types of messages
            //--
            define('UM_ALL_MESSAGE_TYPES', -1);
            define('UM_PUBLIC_MESSAGE_TYPE', 0);
            define('UM_PRIVATE_MESSAGE_TYPE', 1);
            
            // The possible statuses of a message
            //--
            define('UM_ALL_MESSAGE_STATUS', -1);
            define('UM_UNREAD_MESSAGE_STATUS', 0);
            define('UM_READ_MESSAGE_STATUS', 1);  
            
            // The default inbox folders
            //--
            define('UM_INBOX_FOLDER', 0);
            define('UM_SENT_FOLDER', 1);  
			
			// The type of notification the system sends to users
			//--
            define('UM_NEW_MESSAGE_NOTIFICATION', 'new-message');  
            define('UM_QUOTA_EXCEEDED_NOTIFICATION', 'quota-exceeded');  
            define('UM_INBOX_FULL_NOTIFICATION', 'inbox-full');  
            
            // User flags
            //--
            define('UM_ACCEPT_PUBLIC_MESSAGES_USER_META', 'um_accept_public_messages');
            define('UM_ACCEPT_PRIVATE_MESSAGES_USER_META', 'um_accept_private_messages');       
            define('UM_ACCEPT_EMAIL_USER_META', 'um_accept_email');            
            define('UM_NEW_MESSAGE_NOTIFICATION_USER_META', 'um_new_message_notification');     
        }
        
        /**
        * Include all the files we need for the plugin
        */
        private function load_dependencies() {
			require_once (dirname (__FILE__) . '/controller/Messenger.php');
			require_once (dirname (__FILE__) . '/controller/Message.php');
			require_once (dirname (__FILE__) . '/controller/Notifier.php');
			require_once (dirname (__FILE__) . '/controller/Notification.php');
			require_once (dirname (__FILE__) . '/model/MessageDAO.php');
			require_once (dirname (__FILE__) . '/model/NotificationDAO.php');
            if (is_admin()) {
                require_once (dirname (__FILE__) . '/view/AdminInterface.php');
            }
        }
        
        /**
        * Load the plugin text domain for internationalization
        */        
        private function load_textdomain() {                                                                        
            load_plugin_textdomain('um', false, dirname(plugin_basename(__FILE__)) . '/lang');
        }       
        
        /**
         * Set the default values of user flags for every blog user
         */
		private function set_default_user_meta() {
			global $wpdb;
			$user_ids = $wpdb->get_col("SELECT id FROM $wpdb->users");
			foreach ($user_ids as $id) {
				$this->on_user_registration($id);
	        }
		}
        
        /**
        * Add default capabilities to the roles
        */
        private function add_default_capabilities() {                                
            $role = get_role('administrator');
            if (!empty($role)) {
                $role->add_cap(UM_SEND_PUBLIC_MESSAGES_CAP);
                $role->add_cap(UM_SEND_PRIVATE_MESSAGES_CAP);
                $role->add_cap(UM_SEND_EMAIL_MESSAGES_CAP);
                $role->add_cap(UM_IGNORE_PUBLIC_MESSAGES_CAP);   
                $role->add_cap(UM_REFUSE_PRIVATE_MESSAGES_CAP);  
                $role->add_cap(UM_RECEIVE_MESSAGES_CAP);   
                $role->add_cap(UM_CONFIGURE_PLUGIN_CAP);   
                $role->add_cap(UM_USE_PLUGIN_CAP);   
            }
            
            $role = get_role('editor');
            if (!empty($role)) {
                $role->add_cap(UM_SEND_PRIVATE_MESSAGES_CAP);    
                $role->add_cap(UM_RECEIVE_MESSAGES_CAP);    
                $role->add_cap(UM_USE_PLUGIN_CAP);       
            }
            
            $role = get_role('author');
            if (!empty($role)) {
                $role->add_cap(UM_SEND_PRIVATE_MESSAGES_CAP);
                $role->add_cap(UM_RECEIVE_MESSAGES_CAP);      
                $role->add_cap(UM_USE_PLUGIN_CAP);       
            }
            
            $role = get_role('contributor');
            if (!empty($role)) {
                $role->add_cap(UM_SEND_PRIVATE_MESSAGES_CAP);
                $role->add_cap(UM_RECEIVE_MESSAGES_CAP);    
                $role->add_cap(UM_USE_PLUGIN_CAP);         
            }
            
            $role = get_role('subscriber');
            if (!empty($role)) {                                
                $role->add_cap(UM_RECEIVE_MESSAGES_CAP);    
                $role->add_cap(UM_USE_PLUGIN_CAP);         
            }           
        }
        
        /**
        * Set default values for the options
        */
        private function set_default_options() {
            $this->options['default_inbox_capacity'] = 30;
            $this->options['user_quota'] = 20;
            $this->options['theme_url'] = UM_BASEURL . 'themes/default';
            $this->options['notify_on_public_message'] = 'true';
            $this->options['notification_batch_size'] = 20;
            $this->options['notification_task_interval'] = 20;
            $this->options['new_message_notification_subject'] = __("New message received on %BLOG_NAME%", "um");
            $this->options['new_message_notification_body'] = __("Hi %RECIPIENT_NAME%,\n\nYou have received a new message from %MESSAGE_AUTHOR% on %BLOG_NAME%. You currently have %UNREAD_MESSAGE_COUNT% unread messages. Please log on the website (%USER_MESSAGES_URL%) to view it.\n\n", "um") . __("This is an automatic email, please do not reply to it.", "um");
            $this->options['over_quota_notification_subject'] = __("Problem with your message box on %BLOG_NAME%", "um");
            $this->options['over_quota_notification_body'] = __("Hi %RECIPIENT_NAME%,\n\nYour message box on %BLOG_NAME% has exceed the maximum number of messages it can hold: you have %TOTAL_MESSAGE_COUNT% messages for a limit of %USER_QUOTA%. Please log on the website (%USER_MESSAGES_URL%) to delete some of those messages.\n\n", "um") . __("This is an automatic email, please do not reply to it.", "um");
            $this->options['inbox_full_notification_subject'] = __("Problem with your message box on %BLOG_NAME%", "um");
            $this->options['inbox_full_notification_body'] = __("Hi %RECIPIENT_NAME%,\n\nSomebody has tried to send you a message on %BLOG_NAME% but your message box is full. The message has not been delivered. Please log on the website (%USER_MESSAGES_URL%) to delete some of those messages.\n\n", "um") . __("This is an automatic email, please do not reply to it.", "um");
        }
        
        /**
        * Put some sample data into the database
        */
        private function insert_sample_data() {
            $current_user = wp_get_current_user();
            $user_id = $current_user->ID;
            
            $msg = new UM_Message();
            $msg->status = UM_UNREAD_MESSAGE_STATUS;
            $msg->type = UM_PRIVATE_MESSAGE_TYPE;
            $msg->folder_id = UM_INBOX_FOLDER;
            $msg->author_id = $user_id;
            $msg->owner_id = $user_id;
            $msg->subject = __("User Messages plugin is installed.", "um");
            $msg->content = __("This is a sample message confirming that the User Messages plugin is installed.", "um");
            $msg->recipient_ids = array($user_id);
            $msg->timestamp = current_time('mysql', 1);
            
            UM_MessageDAO::insert_message($msg);
        }
        
        /**
        * Create the plugin database tables
        */
        private function create_tables() {
            global $wpdb;
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); 
            
            if ($wpdb->get_var("show tables like '{$wpdb->um_message}'") != $wpdb->um_message) {
                
                // Create the message table
                //--
                $sql = "CREATE TABLE {$wpdb->um_message} (
                    id BIGINT(20) NOT NULL AUTO_INCREMENT, 
                    type SMALLINT NOT NULL, 
                    status SMALLINT NOT NULL DEFAULT 0,      
                    subject TEXT NOT NULL, 
                    content TEXT NOT NULL, 
                    author_id BIGINT(20) NOT NULL, 
                    owner_id BIGINT(20) NOT NULL,   
                    recipient_ids TEXT NOT NULL,      
                    timestamp_gmt DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                    folder_id SMALLINT NOT NULL DEFAULT 0,  
                    PRIMARY KEY  (id)  
                ); ";                          
                
                dbDelta($sql);                                            
            }
            
            if ($wpdb->get_var("show tables like '{$wpdb->um_notification}'") != $wpdb->um_notification) {
                
                // Create the notification table
                //--
                $sql = "CREATE TABLE {$wpdb->um_notification} (
                    id BIGINT(20) NOT NULL AUTO_INCREMENT, 
                    type TEXT NOT NULL, 
                    user_id BIGINT(20) NOT NULL,
                    message_id BIGINT(20) DEFAULT 0,
                    PRIMARY KEY  (id)  
                );";                          
                
                dbDelta($sql);                                            
            }
        }
        
        /**
        * Update the tables necessary to the plugin
        */
        function update_tables($active_db_version) {
            global $wpdb;
            
            if ($active_db_version<1) {                
            }
        }
		
		/**
		 * Use this function when the tasks have to be rescheduled due to options update for example
		 */
		function reschedule_cron() {
			wp_clear_scheduled_hook('um_process_notifications');
			wp_schedule_event(time(), 'um-notifications-schedule', 'um_process_notifications'); 
		}
		
		/**
		 * Add a schedule interval to the WordPress array of schedules
		 */
		function cron_schedules_filter($schedules) {
			$schedules['um-notifications-schedule'] = array(
				'interval' => 60 * $this->options['notification_task_interval'],
				'display'  => sprintf(__('Once Every %s Minute(s)', 'um'), $this->options['notification_task_interval'])
			);

			return $schedules;			
		}
    }

    // Start the plugin only if we are in the admin interface
    //--               
	global $um_plugin, $um_admin_interface, $um_messenger, $um_notifier;
	$um_plugin = new UM_UserMessagesPlugin();        
	$um_messenger = new UM_Messenger();       
	$um_notifier = new UM_Notifier();      
	
	if (is_admin()) {
		$um_admin_interface = new UM_AdminInterface();
    }
}

?>
