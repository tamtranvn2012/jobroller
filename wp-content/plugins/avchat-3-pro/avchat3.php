<?php
if(session_id() == ""){
		session_start();
	}
/**
 * @package AVChat Video Chat Plugin for WordPress
 * @author  AVChat Software
 * @version 1.3
 */
/*
Plugin Name: AVChat Video Chat Plugin PRO for WordPress
Plugin URI: http://avchat.net/integrations/wordpress
Description: This plugin integrates <a href="http://avchat.net" target="_blank">AVChat 3</a> into any WordPress website.
Author: AVChat Software
Version: build 2359 10th May 2013
Author URI: http://avchat.net/



Copyright (C) 2009-2012 AVChat Software, avchat.net

This WordPress Plugin is distributed under the terms of the GNU General Public License.
You can redistribute it and/or modify it under the terms of the GNU General Public License 
as published by the Free Software Foundation, either version 3 of the License, or any later version.

You should have received a copy of the GNU General Public License
along with this plugin.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once(ABSPATH . 'wp-content/plugins/avchat-3-pro/insert_tables.php');

function avchat3_pro_get_user_details(){
	global $current_user;
	global $wpdb;
	global $blog_id;
	get_currentuserinfo();
	
	$user_roles = array();
	$user_info = array();
	
	/*
	Commented on 7th of May 2013 because it's deprecated
	if(function_exists(is_site_admin)){
		$avchat3_is_on_wpmu = true;
	}else{
		$avchat3_is_on_wpmu = false;
	}*/

	if ( is_multisite() ) { 
		//this is a multisite WP installation
		if ($blog_id==1){
			//this is the main sub site in the multisite WP
			$av3_current_blog_capabilities = $wpdb->prefix.'capabilities';	
		}else{
			//these are other sub sites
			$av3_current_blog_capabilities = $wpdb->prefix.$blog_id.'_capabilities';
		}
	} else{
		$av3_current_blog_capabilities = $wpdb->prefix.'capabilities';
	}
	
	if($current_user->ID == null || $current_user->ID == ""){
		$user_info['user_id'] = '0';
	}else{
		
		$user_info['user_id'] = $current_user->ID;
		$user_info['user_login'] = $current_user->user_login;
		$user_info['user_display_name'] = $current_user->display_name;
		$user_info['user_level_id'] = $current_user->user_level;
		$user_info['email'] = $current_user->user_email;
		
		if ($current_user->$av3_current_blog_capabilities!=""){
			$user_roles = array_keys($current_user->$av3_current_blog_capabilities);
			$user_info['user_role'] = $user_roles[0];
		}else{
			//this is a user in a multisite environment that has no role in this site
			$user_info['user_role'] = "networkuser";
		}
		//we select the permissions for that specific user role
		$query = "SELECT * FROM ".$wpdb->prefix . "avchat3_permissions"." WHERE user_role = '".$user_info['user_role']."'";
		$user_permissions = $wpdb->get_results($query);
		
		unset($user_permissions[0]->id);
		unset($user_permissions[0]->user_role);
		
		foreach($user_permissions[0] as $key=>$value){
			$user_info[$key] = $value;
		}
	}
	return $user_info;
}

function get_avchat3_pro_visitor_permissions(){
	global $wpdb;
	
	$query = "SELECT * FROM ".$wpdb->prefix . "avchat3_permissions"." WHERE user_role = 'visitors'";
	$user_permissions = $wpdb->get_results($query);
	
	unset($user_permissions[0]->id);
	unset($user_permissions[0]->user_role);
	
	foreach($user_permissions[0] as $key=>$value){
		$user_info[$key] = $value;
	}
	$user_info['user_role'] = 'visitors';
	
	return $user_info;
}

function get_avchat3_pro_general_settings(){
	global $wpdb;
	
	$query = "SELECT * FROM ".$wpdb->prefix . "avchat3_general_settings";
	$general_settings = $wpdb->get_results($query);
	
	return $general_settings;
}

function get_avchat3_pro_general_setting($general_av_setting){
	global $wpdb;
	
	$query = "SELECT ".$general_av_setting." FROM ".$wpdb->prefix . "avchat3_general_settings";
	$result = $wpdb->get_results($query);
	
	
	return $result[0];
}

function set_avchat3_pro_general_settings_on_session(){
	if(session_id() == ""){
		session_start();
	}
	
	$general_settings = get_avchat3_pro_general_settings();
	
	foreach($general_settings[0] as $key=>$value){
		$_SESSION[$key] = $value;
	}
	
}

function set_avchat3_pro_buddy_details_on_session($buddy_details){
	if(session_id() == ""){
		session_start();
	}
	
	
	foreach($buddy_details as $key=>$value){
		$_SESSION[$key] = $value;
	}
}

function avchat3_pro_set_user_details_on_session($user_info){
	if(session_id() == ""){
		session_start();
	}
	
	
	if($user_info['user_id'] == "0"){
		$user_info = get_avchat3_pro_visitor_permissions();
	}else{
		$_SESSION['user_logged_in'] = true;
	}
	
	foreach($user_info as $key=>$val){
			$_SESSION[$key] = $val;
		}
}

function avchat3_pro_clear_session(){
	session_destroy();
}


function avchat3_pro_get_user_chat($content){
	$user_info = avchat3_pro_get_user_details();
	avchat3_pro_set_user_details_on_session($user_info);
	set_avchat3_pro_general_settings_on_session();
	
	require_once(ABSPATH . 'wp-content/plugins/avchat-3-pro/Mobile_Detect.php');
	
	if($user_info['can_access_admin_chat']){
		$movie_param = 'admin.swf';
		
	}else{
		$movie_param = 'index.swf';
	}
	
	//$_SESSION['raluca'] = 'administrator';
	
	$display_mode = get_avchat3_pro_general_setting('display_mode')->display_mode;
	
	
	//Check if buddypress is installed
	if(in_array( 'buddypress/bp-loader.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )){
		
		
		//Get buddypress member avatar
		$buddy_details['avatar'] = bp_core_fetch_avatar( array( 'item_id' => $user_info['user_id'], 'type' => 'thumb', 'alt' => '', 'css_id' => '', 'class' => 'avatar', 'width' => '40', 'height' => '40', 'email' => $user_info['user_email'], 'html' => 'false') ) ;
		$buddy_details['is_buddy'] = 1;
		
		
		set_avchat3_pro_buddy_details_on_session($buddy_details);	
	}	
	
	if($_SESSION['FB_appId'] != "") {
		$FB_appId = $_SESSION['FB_appId'];
	}
	else {
		$FB_appId = "";
	}
			
	$role = $_SESSION['user_role'];		
	
	

	

	if($_SESSION['can_access_chat'] != '1'){
				$embed = '<div id="av_message" style="color:#ff0000"> You do not have sufficient privileges to access this page. <a style="display:block;padding:5px 3px;width:200px;margin:5px 0;text-align:center;background:#f3f3f3;border:1px solid #ccc" href="wp-login.php" >Click to upgrade!</a></div>';	
	}else{
		if(!file_exists('./wp-content/plugins/avchat-3-pro/swfobject.js')){
			//the AVChat 3 files have not been copied to the installation folder
			$embed = '<p>Before the chat can work, you need to copy the <b>AVChat 3</b> files to the <b>/wp-content/plugins/avchat-3-pro/</b> folder.</p><p>To get <b>AVChat 3</b> you can request a 15 day trial from <a href="http://avchat.net">http://avchat.net</a> or you can purchase it from <a href="http://avchat.net/buy-now">http://avchat.net/buy-now</a>.</p>';
		}else{
		
			
			/* 
			In some cases when SESSION-s fail we might want to use this rudimentary save to file method of passing the permissions and settings to avc_settings.php
			You also need to uncomment the code in integration.php for this method to work 
				$getUserId=$user_info['user_role'].rand(10,100000000);
				$myFile = "wp-content/plugins/avchat-3-pro/sessions/$getUserId.txt";
				$fh = fopen($myFile, 'w') or die("can't open file");
				fwrite($fh, serialize($_SESSION));
				fclose($fh);
			*/
	
			$mobilecheck= new Mobile_Detect();
			if ($mobilecheck->isMobile() || $mobilecheck->isTablet()){
				$embed = '<a href="'.get_bloginfo('url').'/wp-content/plugins/avchat-3-pro/m/m.php" style="background:#f0f0f0;display:block;padding:10px 20px;width:200px;text-align:center;border:1px solid #ccc">Enter mobile version</a>';
			}else{
				if($display_mode == 'embed'){
					$embed = '
					<div id="myContent"><div id="av_message" style="color:#ff0000">You need to have JavaScript enabled and <a target="_blank" href="http://get2.adobe.com/flashplayer/">the latest version of Flash Player</a> for the chat to work.</div></div>
					<input type="hidden" name="FB_appId" id="FB_appId" value="'.$FB_appId.'" />
					<script type="text/javascript" src="'.get_bloginfo('url').'/wp-content/plugins/avchat-3-pro/tinycon.min.js"></script>
					<script type="text/javascript" src="'.get_bloginfo('url').'/wp-content/plugins/avchat-3-pro/facebook_integration.js"></script>
					<script type="text/javascript" src="'.get_bloginfo('url').'/wp-content/plugins/avchat-3-pro/swfobject.js"></script>
					<script type="text/javascript" src="'.get_bloginfo('url').'/wp-content/plugins/avchat-3-pro/new_message.js"></script>
					<script type="text/javascript">
					var plugin_path = "'.get_bloginfo('url').'/wp-content/plugins/avchat-3-pro/";
					</script>
					<script type="text/javascript">
						var flashvars = {
							lstext : "Loading Settings...",
							sscode : "php",
							userId : "'.$getUserId.'"
						};
						var params = {
							quality : "high",
							bgcolor : "#272727",
							play : "true",
							loop : "false",
							allowFullScreen : "true",
							base : "'.get_bloginfo("url").'/wp-content/plugins/avchat-3-pro/"
						};
						var attributes = {
							name : "index_embed",
							id :   "index_embed",
							align : "middle"
						};
					</script>
					<script type="text/javascript">
					swfobject.embedSWF("'.get_bloginfo('url').'/wp-content/plugins/avchat-3-pro/'.$movie_param.'", "myContent", "100%", "600", "11.1.0", "", flashvars, params, attributes);
					</script>';
				}else{
					$chat_window_url = get_bloginfo('url').'/wp-content/plugins/avchat-3-pro/index_popup.php?movie_param='.$movie_param."&FB_appId=".$FB_appId."&userId=".$getUserId;
					$chat_window_height = 600; 
					$chat_window_width = 800;
					$embed ='<a style="display:block;padding:5px 3px;width:200px;margin:5px 0;text-align:center;background:#f3f3f3;border:1px solid #ccc" href="#" onclick="javascript:window.open('.'&#39;'.$chat_window_url.'&#39;'.',\'_blank\',\'width='.$chat_window_width.',height='.$chat_window_height.'\')">Open chat in popup</a>';
				}
			}
		}
	}
	return str_replace('[chat]', $embed, $content);
}

function avchat3_pro_admin_config(){
	add_options_page('AVChat3 Permissions & Config', 'AVChat 3 Video Chat PRO',  'manage_options', 'avchat-3-pro/avchat3-settings.php');
}

register_activation_hook(__FILE__,'avchat3_pro_install');
add_action('wp_logout', 'avchat3_pro_clear_session');
add_action('admin_menu', 'avchat3_pro_admin_config');
add_filter('the_content', 'avchat3_pro_get_user_chat', 7);
?>