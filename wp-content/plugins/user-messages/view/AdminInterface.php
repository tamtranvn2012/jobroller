<?php

/**
* Class that manages the admin panel for messages
* 
* @author Vincent Prat
*/

class UM_AdminInterface {
    
    /**
    * Constructor
    */
    function UM_AdminInterface() {
        add_action('admin_menu', array(&$this, 'add_menu'));  
        add_action('admin_print_scripts', array(&$this, 'load_scripts') );
        add_action('admin_print_styles', array(&$this, 'load_styles') );
		add_action('show_user_profile', array('UM_ProfileView', 'on_show_user_profile'));
		add_action('personal_options_update', array('UM_ProfileView', 'on_user_profile_update'));
        add_action('wp_dashboard_setup', array(&$this, 'dashboard_setup'));
        add_filter('contextual_help', array(&$this, 'help_callback'), 10, 2);  
		
        if (IS_PROFILE_PAGE) {
        	include_once(dirname(__FILE__) . '/ProfileView.php' );
        }
    }
    
    /**
    * Load the scripts we need for the administration interface
    */
    function load_scripts() {
    	$view = $this->get_view_parameter();
        switch ($view) {
            case "um-write-message-view":
            case "um-write-private-message-view":
            case "um-write-public-message-view":
            case "um-plugin-options-view":
                wp_enqueue_script( 'jquery-ui-tabs' );
                break;
        }
    }
    
    /**
    * Load the scripts we need for the administration interface
    */
    function load_styles() {
    	$view = $this->get_view_parameter();
        switch ($view) {
            case "um-write-message-view":
            case "um-write-private-message-view":
            case "um-write-public-message-view":
            case "um-plugin-options-view":
                wp_enqueue_style('umtabs', UM_THEMEURL .'/jquery.ui.tabs.css', false, '2.5.0', 'screen');
				wp_enqueue_style('um', UM_THEMEURL .'/style.css', false, '2.5.0', 'screen');
                break;
            default:
				wp_enqueue_style('um', UM_THEMEURL .'/style.css', false, '2.5.0', 'screen');
                break;
        }
    }
    
    /**
    * Add the menu to the administration interface
    */
    function add_menu() {                
    	global $user_ID;

		$unread_msg_count = UM_MessageDAO::count_unread_user_messages($user_ID);
	
        add_menu_page( 
			sprintf(__('Messages (%s new)', 'um'), number_format_i18n($unread_msg_count)),
            sprintf(__('Messages %s', 'um'), "<span class='update-plugins count-$unread_msg_count'><span class='plugin-count'>" . number_format_i18n($unread_msg_count) . "</span></span>" ), 
            UM_USE_PLUGIN_CAP,
            UM_FOLDER, 
            array(&$this, 'menu_callback'), 
            UM_THEMEURL .'/menu-icon.png' );
        
        add_submenu_page( 
            UM_FOLDER, 
            __('My Messages', 'um'), __('My Messages', 'um'), 
            UM_RECEIVE_MESSAGES_CAP, 
            UM_FOLDER, 
            array (&$this, 'menu_callback'));    
        
		if (current_user_can(UM_SEND_PUBLIC_MESSAGES_CAP) || current_user_can(UM_SEND_PRIVATE_MESSAGES_CAP)) {		
			add_submenu_page( 
				UM_FOLDER, 
				__('Write Message', 'um'), __('Write Message', 'um'), 
				0, 
				'um-write-message-view', 
				array (&$this, 'menu_callback'));   
        }
		
        add_submenu_page( 
            UM_FOLDER, 
            __('Write Email', 'um'), __('Write Email', 'um'), 
            UM_SEND_EMAIL_MESSAGES_CAP, 
            'um-write-email-view', 
            array (&$this, 'menu_callback'));   
                                    
        add_options_page(
            __('User Messages', 'um'), __('User Messages', 'um'), 
            UM_CONFIGURE_PLUGIN_CAP,
            'um-plugin-options-view', 
            array (&$this, 'menu_callback'));  
	}

	/**
	* Setup the dashboard features
	*/
	function dashboard_setup() {
		include_once(dirname(__FILE__) . '/DashboardFeatures.php' );
		$widget = new UM_DashboardFeatures();
		$widget->setup();
    }
    
    /**
    * Function called on a menu click to display the appropriate view
    */
    function menu_callback() {   
    	$view = $this->get_view();
        $view->show_view();
    }
    
    /**
    * Show help according to the current view 
    */
    function help_callback($help, $screen) {   
    	$view = $this->get_view();
        return $view->show_help();
    }
    
    /**
    * Get the view object corresponding to the page we want to show
    */
    private function get_view() {
    	$view = $this->get_view_parameter();
        $chosen_view = $this;
            
        switch ($view){
            case "um-write-message-view":
                include_once(dirname(__FILE__) . '/WriteMessageView.php' );
                $chosen_view = new UM_NewMessageView();
                break;
            case "um-write-email-view":
                include_once(dirname(__FILE__) . '/WriteEmailView.php' );
                $chosen_view = new UM_WriteEmailView();
                break;
            case "um-show-message-view":
                include_once(dirname(__FILE__) . '/ShowMessageView.php' );
                $chosen_view = new UM_ShowMessageView();
                break;
            case "um-plugin-options-view":
                include_once(dirname(__FILE__) . '/PluginOptionsView.php' );
                $chosen_view = new UM_PluginOptionsView();
                break;
            default:
                if (current_user_can(UM_RECEIVE_MESSAGES_CAP)) {
                    include_once(dirname(__FILE__) . '/InboxView.php' );       
                    $chosen_view = new UM_InboxView($action);
                } else if (current_user_can(UM_SEND_PRIVATE_MESSAGES_CAP)) {
                    include_once(dirname(__FILE__) . '/WriteMessageView.php' );
                    $chosen_view = new UM_NewMessageView(UM_PRIVATE_MESSAGE_TYPE);
                } else if (current_user_can(UM_SEND_PUBLIC_MESSAGES_CAP)) {                        
                    include_once(dirname(__FILE__) . '/WriteMessageView.php' );
                    $chosen_view = new UM_NewMessageView(UM_PUBLIC_MESSAGE_TYPE);
                } else {
                    $chosen_view = $this;  
                }
                break;
        }
        
        return $chosen_view;
    }
    
    /**
     * Get a link to write to a user
     */
    function get_write_to_link($user) {
    	global $user_ID;
    	
   		$output = '';
   		if ($user_ID!=$user->ID
   				&& current_user_can(UM_SEND_PRIVATE_MESSAGES_CAP) 
   				&& 'true'==get_usermeta($user->ID, UM_ACCEPT_PRIVATE_MESSAGES_USER_META)) {
   			$link_text = sprintf(__("Write a message to %s", "um"), $user->display_name);
    		$output .= '<a href="admin.php?page=um-write-message-view&type=' . UM_PRIVATE_MESSAGE_TYPE . '&recipients=' . $user->ID . '" title="' . $link_text . '" >';
			$output .= '<img src="' . UM_THEMEURL . '/send-message-to-link-icon.png" title="' . $link_text . '" alt="' . $link_text . '" class="link-icon" />';
    		$output .= '</a>';
   		}
   		if ($user_ID!=$user->ID
   				&& current_user_can(UM_SEND_EMAIL_MESSAGES_CAP) 
   				&& 'true'==get_usermeta($user->ID, UM_ACCEPT_EMAIL_USER_META)) {
   			$link_text = sprintf(__("Write an email to %s", "um"), $user->display_name);
    		$output .= '<a href="admin.php?page=um-write-email-view&recipients=' . $user->ID . '" title="' . $link_text . '" >';
			$output .= '<img src="' . UM_THEMEURL . '/send-email-to-link-icon.png" title="' . $link_text . '" alt="' . $link_text . '" class="link-icon" />';
    		$output .= '</a>';
   		}
		$output .= '<span class="um-user-name">' . $user->display_name . '</span>';
    	
		return $output;
    }
    
    /**
     * Get a link to view a message
     */
    function get_show_message_link($msg, $link_text='') {
		$output = '<a href="admin.php?page=user-messages&view=um-show-message-view&message_id=' . $msg->message_id . '" title="' . __("Click to view the message", "um") . '" class="um-show-message">';
		$output .= $link_text=='' ? htmlspecialchars($msg->get_raw_subject()) : $link_text;
		$output .= '</a>';
		return $output;
    }
    
	/**
	* Get the proper icon according to the message type
	*/
	function get_message_type_icon($type, $recipient_count=0) {
		$file = UM_THEMEURL . '/';
		switch ($type) {
			case UM_PUBLIC_MESSAGE_TYPE:
				$file .= 'msg-type-public.png';
				$text = __("Public message", "um");
				break;
            case UM_PRIVATE_MESSAGE_TYPE:
            	if ($recipient_count<=1) {
					$file .= 'msg-type-private.png';
					$text = __("Private message", "um");
            	} else {
					$file .= 'msg-type-private-multi.png';
					$text = __("Private message to multiple users", "um");
            	}
				break;
            default:
				$file .= 'msg-type-unknown.png';
				$text = __("Unknown message type", "um");
		}
		
		return '<img src="' . $file . '" title="' . $text . '" alt="' . $text . '" class="um-msg-type" />';
	}
    
	/**
	* Get the proper icon according to the message status
	*/
	function get_message_status_icon($status) {
		$file = UM_THEMEURL . '/';
		switch ($status) {
			case UM_READ_MESSAGE_STATUS:
				$file .= 'msg-status-read.png';
				$text = __("Message already read", "um");
				break;
            case UM_UNREAD_MESSAGE_STATUS:
            	$file .= 'msg-status-unread.png';
				$text = __("New message", "um");
				break;
            default:
				$file .= 'msg-status-unknown.png';
				$text = __("Unknown message status", "um");
		}
		
		return '<img src="' . $file . '" title="' . $text . '" alt="' . $text . '" class="um-msg-status" />';
	}
	
	/**
	 * Get the page from the GET or POST values
	 */
	private function get_view_parameter() {
		if (isset($_GET['page']) && $_GET['page']=='user-messages' && isset($_GET['view'])) {
			return $_GET['view'];
		} else if (isset($_GET['page'])) {
			return $_GET['page'];
		} else {
			return $_POST['page'];
		}
	}
	
	/**
	* Show the message requesting to delete some messages. Returns true if user has a quota problem (either inbox full or over quota).
	*/
	function check_quota_and_warn($user_id) {
		global $um_plugin;
		$msg_count = UM_MessageDAO::count_user_messages($user_id);
		$quota = $um_plugin->options['user_quota'];
		$capacity = $um_plugin->options['default_inbox_capacity'];
		
		if ($msg_count>=$capacity) {
			echo '<div class="um-error">';
			echo '<p>' 
				. sprintf(__("Warning: You have exceeded your inbox capacity (you can store at most %d messages).", "um"), $msg_count, $capacity)  
				. " "
				. __("You are not able to recieve new messages anymore. Additionally, you cannot send messages until you delete some from your inbox.", "um") 
				. '</p>';
			echo "</div>";
			
			return true;
		} else if ($msg_count>=$quota) {
			echo '<div class="um-error">';
			echo '<p>' 
				. sprintf(__("Error: you currently have %d messages in your inbox whereas the number of messages you can keep is %d.", "um"), $msg_count, $quota)  
				. " "
				. __("You will not be able to recieve new messages soon. Additionally, you cannot send any messages until you delete some from your inbox.", "um") 
				. '</p>';
			echo "</div>";
			
			return true;
		}

		return false;
	}
    
    /**
    * Default view to show 
    */
    function show_view() {
?>   
<div class="wrap">
    <h2><?php _e('User Messages', 'um') ?></h2>
    <p><?php _e('You are not allowed to use the User Messages feature on this site, you should not even have landed on this page. Sorry.', 'um') ?></p>
</div>
<?php        
    }
    
    /**
    * Default contextual help to show
    */
    function show_help() {
        return "<p>" . __('No help for this page.', 'um') . "</p>";
    }
}

?>
