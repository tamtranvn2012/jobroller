<?php
/*

Copyright (C) 2009-2012 AVChat Software, avchat.net

This WordPress Plugin is distributed under the terms of the GNU General Public License.
You can redistribute it and/or modify it under the terms of the GNU General Public License 
as published by the Free Software Foundation, either version 3 of the License, or any later version.

You should have received a copy of the GNU General Public License
along with this plugin.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
This function is called in 3 situations:
1) In a standard WP website, when the admin activates the plugin.
2) In a multisite WP website when the plugin is NOT network activated and a user activates the plugin individually for each of it's own websites
3) In a multisite WP website when the plugin is network activated the function is not called automatically so the websites on the nework will not have the apprpriate tables. In this case we placed a manuall call to the function when the admin acesses the Settings > AVChat 3 Video Chat PRO page (inside avchat3-settings.php).
*/

function avchat3_pro_install($calledfromsettingspage=true){
   global $wpdb;
   global $wp_roles;

   $table_name = $wpdb->prefix . "avchat3_permissions";
   $table2_name = $wpdb->prefix . "avchat3_general_settings";
   
    //$calledfromsettingspage=true;
    //$file = 'people.txt';
	//file_put_contents($file, $calledfromsettingspage, FILE_APPEND);

   //we remove the tables if they exist, when this function is executed. In a multisite setup when executed from avchat3-settings.php the tables are not droped ($droptables=false)
   if ($calledfromsettingspage){
   }else{
	//	$sql = "DROP TABLE  $table_name";
	//	$results = $wpdb->query( $sql );
	//	$sql = "DROP TABLE  $table2_name";
	//  $results = $wpdb->query( $sql );
   }
    //keep in mind if the tables were present\
	$tables_were_present=true;
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name && $wpdb->get_var("SHOW TABLES LIKE '$table2_name'") != $table2_name) {
		$tables_were_present=false;
	}
   		$sql = "CREATE TABLE " . $table_name . " (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  user_role varchar(50) DEFAULT '0' NOT NULL,
			  can_access_chat tinyint(1) NOT NULL,
			  can_access_admin_chat tinyint(1) NOT NULL,
			  can_publish_audio_video tinyint(1) NOT NULL,
			  can_stream_private tinyint(1) NOT NULL,
			  can_send_files_to_rooms tinyint(1) NOT NULL,
			  can_send_files_to_users tinyint(1) NOT NULL,
			  can_pm tinyint(1) NOT NULL,
			  can_create_rooms tinyint(1) NOT NULL,
			  can_watch_other_people_streams tinyint(1) NOT NULL,
			  can_join_other_rooms tinyint(1) NOT NULL,
			  show_users_online_stay tinyint(1) NOT NULL,
			  view_who_is_watching_me tinyint(1) NOT NULL,
			  can_block_other_users tinyint(1) NOT NULL,
			  can_buzz tinyint(1) NOT NULL,
			  can_stop_viewer tinyint(1) NOT NULL,
			  can_ignore_pm tinyint(1) NOT NULL,
			  typing_enabled tinyint(1) NOT NULL,
			  free_video_time mediumint(5) NOT NULL,
			  drop_in_room varchar(5) NOT NULL,
			  max_streams mediumint(2) NOT NULL,
			  max_rooms mediumint(2) NOT NULL,
			  username_prefix varchar(10) NOT NULL,
			  admin_can_kick tinyint(1) NOT NULL,
			  admin_can_ban tinyint(1) NOT NULL,
			  admin_can_view_ips tinyint(1) NOT NULL,
			  admin_can_silence tinyint(1) NOT NULL,
			  admin_can_view_pms tinyint(1) NOT NULL,
			  admin_can_access_sett tinyint(1) NOT NULL,
			  admin_can_hide tinyint(1) NOT NULL,
			  admin_can_view_hiden_admins tinyint(1) NOT NULL,
			  UNIQUE KEY id (id)
			);
				CREATE TABLE " . $table2_name . " (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				connection_string TEXT NOT NULL,
				invite_link TEXT NOT NULL,
				disconnect_link TEXT NOT NULL,
				login_page_url TEXT NOT NULL,
				register_page_url TEXT NOT NULL,
				text_char_limit mediumint(2) NOT NULL,
				history_lenght mediumint(3) NOT NULL,
				hide_left_side ENUM ('yes', 'no') NOT NULL,
				p2t_default ENUM ('yes', 'no') NOT NULL,
				flip_tab_menu ENUM ('top', 'bottom') NOT NULL,
				display_mode ENUM ('embed', 'popup') NOT NULL,
				allow_facebook_login ENUM ('yes', 'no') NOT NULL,
				FB_appId TEXT NOT NULL,
				UNIQUE KEY id (id)
				);
		";		
   		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      	dbDelta($sql);
      	
		
		if (!$tables_were_present){
			//if there were no tables we insert the data in them, otherwise we keep the existing data
			foreach($wp_roles->roles as $role => $details){
				$user_roles[$role] = $details["name"];
			}
			
			//we add these 2 roles to the array so that default values are also inserted for them
			$user_roles['visitors'] = "Visitors";
			
			//Network users are users that have signed up on the main site of a Multisite enabled WP instalation, they have no role on the main site but are admin in their own websites (part of the WP Multisite network)
			$user_roles['networkuser'] = "Network user";
			
			//settings for each user role
			
			
			
			foreach($user_roles as $key=>$value){
				$canAccessAdmin=0;
				if ($key=="administrator"){
					$canAccessAdmin=1;
				}  
				$insert = "INSERT INTO " . $table_name .
						  " (user_role, can_access_chat, can_access_admin_chat, can_publish_audio_video, can_stream_private, can_send_files_to_rooms, can_send_files_to_users, can_pm, can_create_rooms, can_watch_other_people_streams, can_join_other_rooms, show_users_online_stay, view_who_is_watching_me, can_block_other_users, can_buzz, can_stop_viewer, can_ignore_pm, typing_enabled, free_video_time, drop_in_room, max_streams, max_rooms,admin_can_kick,admin_can_ban,admin_can_view_ips,admin_can_silence,admin_can_view_pms,admin_can_access_sett,admin_can_hide,admin_can_view_hiden_admins) " .
						  "VALUES ('" . $key . "','1','$canAccessAdmin', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '3600', '', '4', '4','1', '1', '1', '1', '1', '1', '1', '1')";
				$results = $wpdb->query( $insert );
			}
			
			//settings for the entire website
			$insert = "INSERT INTO " . $table2_name .
						  " (connection_string, invite_link, disconnect_link, login_page_url, register_page_url, text_char_limit, history_lenght, hide_left_side, p2t_default, flip_tab_menu, display_mode, allow_facebook_login, FB_appId) " .
						  "VALUES ('rtmp://','','/','/', '/', '200', '20', 'no', 'yes', 'top', 'embed', 'yes', '')";
			$results = $wpdb->query( $insert );
		}
  //s }	
}

?>