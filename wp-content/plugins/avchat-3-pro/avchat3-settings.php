<?php
/*

Copyright (C) 2009-2012 AVChat Software, avchat.net

This WordPress Plugin is distributed under the terms of the GNU General Public License.
You can redistribute it and/or modify it under the terms of the GNU General Public License 
as published by the Free Software Foundation, either version 3 of the License, or any later version.

You should have received a copy of the GNU General Public License
along with this plugin.  If not, see <http://www.gnu.org/licenses/>.
*/

global $wp_roles;
global $wpdb;

require_once(ABSPATH . 'wp-content/plugins/avchat-3-pro/insert_tables.php');
//in a multisite scenario we might have to create the tables when this page is acessed because when the plugin is NETWORK ACTIVATED the tables are not created for all websitess
if( is_multisite()){
	avchat3_pro_install(true);
}

$table_permissions = $wpdb->prefix . "avchat3_permissions";
$table_general_settings = $wpdb->prefix . "avchat3_general_settings";

$permissions = array(
				'can_access_chat' => 'Can access chat',
				'can_access_admin_chat' => 'Can access AVChat admin',
				'can_publish_audio_video' =>  'Can publish audio & video stream',
				'can_stream_private' => 'Can stream private ',
				'can_send_files_to_rooms' => 'Can send files to rooms ',
				'can_send_files_to_users' => 'Can send files to users ',
				'can_pm' => 'Can send private messages',
				'can_create_rooms' => 'Can create rooms',
				'can_watch_other_people_streams' => 'Can watch other people streams ',
				'can_join_other_rooms' => 'Can join other rooms ',
				'show_users_online_stay' => 'Show users how much they stayed online ',
				'view_who_is_watching_me' => 'Ability for the users to see who is watching them ',
				'can_block_other_users' => 'Can block other users ',
				'can_buzz' => 'Can buzz ',
				'can_stop_viewer' => 'Can stop viewer ',
				'can_ignore_pm' => 'Can ignore private messages ',
				'typing_enabled' => 'Typing enabled ',
				'admin_can_kick' => 'AVChat admins can kick?',
				'admin_can_ban' => 'AVChat admins can ban?',
				'admin_can_view_ips' => 'AVChat admins can view IPs?',
				'admin_can_silence' => 'AVChat admins can silence users?',
				'admin_can_view_pms' => 'AVChat admins can view PMs ?',
				'admin_can_access_sett' => 'AVChat admins can access the [Settings] panel?',
				'admin_can_hide' => 'AVChat admins can login as hidden?',
				'admin_can_view_hiden_admins' => 'AVChat admins can view hidden admins?'
);

$settings = array(
				'free_video_time' => 'Free video time ',
				'drop_in_room' => 'Auto drop in room with id: ',
				'max_streams' => 'Max streams a user can watch',
				'max_rooms' => 'Max rooms a user can join',
				'username_prefix' => 'Auto add this prefix to username: ',
);

$general_settings = array(
				'connection_string' => 'Connection string*',
				'invite_link' => 'Invite URL ',
				'disconnect_link' => 'Disconnect button URL ',
				'login_page_url' => 'Login page URL',
				'register_page_url' => 'Register page URL' ,
				'text_char_limit' => 'Text chat character limit ',
				'history_lenght' => 'History length ',
				'flip_tab_menu' => 'Position of rooms tabbed menu',
				'hide_left_side' => 'Hide left side of chat',
				'p2t_default' => 'Push 2 talk used by default ',
				'display_mode' => 'Where the chat is embedded ',
				'allow_facebook_login' => 'Allow Facebook login ',
				'FB_appId' => 'Facebook application ID*',
);


if(isset($_POST) && !empty($_POST)){
	foreach ($_POST as $key=>$avconfs){
		if (strpos($key, "-")){
			$avconf_arrtemp = explode("-", $key);
			if($avconfs == 'on')$avconfs = '1';
			$avconf_arr[$avconf_arrtemp[0]][substr($avconf_arrtemp[1],4)] = $avconfs;
		}else{
			$av_general_confs[substr($key,11)] = $avconfs;
		}
	}
	
	
	
	foreach ($avconf_arr as $key=>$vals){
		$updateString = "";
		foreach($permissions as $pkey => $pvalue){
			if($vals[$pkey] == ""){
				$vals[$pkey] = 0;
			}
			$updateString.= $pkey." = '".$vals[$pkey]."', ";
		}
		
		$i=1;
		foreach($settings as $skey=>$svalue){
			$updateString.= $skey." = '".stripslashes(trim($vals[$skey]))."'";
			if(count($settings) != $i) $updateString.= ', ';
			$i++;
		}
		
		
		$query = "UPDATE ".$table_permissions." SET ".$updateString." WHERE user_role = '".$key."'";
		$wpdb->query($query);
	}
	
	$updateString="";
	$p=1;
	foreach($av_general_confs as $gkey=>$gvalue){
		$updateString.= $gkey." = '".stripslashes(trim($gvalue))."'";
		if(count($av_general_confs) != $p) $updateString.= ', ';
		$p++;
	}
	
	$query = "UPDATE ".$table_general_settings." SET ".$updateString;
	//var_dump($query);
	$wpdb->query($query);
}


$location = get_option('siteurl') . '/wp-admin/admin.php?page=avchat-3-pro/avchat3-settings.php'; 
$user_roles = array();

foreach($wp_roles->roles as $role => $details){
	$user_roles[$role] = $details["name"];
}

//unset($user_roles['administrator']);

$user_roles['visitors'] = "Visitors";
if (is_multisite()){
	$user_roles['networkuser'] = "Network user";
}
//var_dump($user_roles);
	
?>

<div class="wrap">
	<h2>AVChat 3 Settings & Permissions</h2>
</div>
<form name="form1" method="post" action="<?php echo $location; ?>">
	<table style="text-align:center">
		
		<tr>
			<th></th>
			<?php foreach($user_roles as $role => $name){?>
				<th style="padding:0 1px !important"><?php echo $name;?></th>
			<?php } ?>
		</tr>
		
		<tr><td colspan="5" style="text-align:left"><h3>Permissions</h3></td>
		<?php foreach($permissions as $key=>$value){ ?>
			
			<tr>
				<td style="text-align:left"><?php echo $value;?></td>
				<?php 
					foreach ($user_roles as $user_role => $name){
						$user_permissions = $wpdb->get_results( "SELECT can_access_chat, can_access_admin_chat, can_publish_audio_video, can_stream_private, can_send_files_to_rooms, can_send_files_to_users, can_pm, can_create_rooms, can_watch_other_people_streams, can_join_other_rooms, show_users_online_stay, view_who_is_watching_me, can_block_other_users, can_buzz, can_stop_viewer, can_ignore_pm, typing_enabled,admin_can_kick, admin_can_ban,admin_can_view_ips,admin_can_silence,admin_can_view_pms,admin_can_access_sett,admin_can_hide,admin_can_view_hiden_admins FROM ".$wpdb->prefix . "avchat3_permissions WHERE user_role = '".$user_role."'" );
				?>
					<td style="padding:0 1px !important">
					<input type="checkbox" 
						<?php 
							if($user_permissions[0]->$key){ echo 'checked="checked"';}
						?> 
						name="<?php echo strtolower($user_role);?>-avp_<?php echo $key;?>" />
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						</td>
				<?php }?>
			</tr>
		<?php }?>
		
		<tr><td colspan="5" style="text-align:left"><h3>Settings</h3></td>
		<?php foreach($settings as $key=>$value){?>
		<tr>
			<td style="text-align:left"><?php echo $value;?></td>
			<?php 
				foreach ($user_roles as $user_role => $name){
					$user_settings = $wpdb->get_results( "SELECT free_video_time, drop_in_room, max_streams, max_rooms, username_prefix FROM ".$wpdb->prefix . "avchat3_permissions WHERE user_role = '".$user_role."'" );
			?>
				<td style="padding:0 1px !important"><input type="text" name="<?php echo strtolower($user_role);?>-avs_<?php echo $key;?>" style="width:80px" value="<?php echo $user_settings[0]->$key;?>" /></td>
			
			
			
			
			
			
			<?php }?>
		</tr>
		<?php }?>
		
		<tr><td colspan="5" style="text-align:left"><h3>General settings</h3></td>
		<?php 
			foreach($general_settings as $key=>$value){
				$av_general_settings = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix . "avchat3_general_settings" );	
		?>
		<tr>
			<td style="text-align:left"><?php echo $value;?></td>
			<td style="padding:0 1px !important;text-align:left;" colspan="4">
				<?php 
				switch ($key) {
					case 'display_mode':
					?>
						<select name="avgsetting_<?php echo $key?>" >
							<option <?php if ($av_general_settings[0]->$key == 'popup') {echo 'selected="selected"';}?> value="popup">Popup</option>
							<option <?php if ($av_general_settings[0]->$key == 'embed') {echo 'selected="selected"';}?> value="embed">Embed</option>
						</select>
					<?php
						break;
					case ($key == 'allow_facebook_login' || $key == 'hide_left_side' || $key == 'p2t_default'):
					?>
						<select name="avgsetting_<?php echo $key?>" >
							<option <?php if ($av_general_settings[0]->$key == 'yes') {echo 'selected="selected"';}?> value="yes">Yes</option>
							<option <?php if ($av_general_settings[0]->$key == 'no') {echo 'selected="selected"';}?> value="no">No</option>
						</select> 
					<?php
						break;
					case 'flip_tab_menu':
					?>
						<select name="avgsetting_<?php echo $key?>" >
							<option <?php if ($av_general_settings[0]->$key == 'top') {echo 'selected="selected"';}?> value="top">Top</option>
							<option <?php if ($av_general_settings[0]->$key == 'bottom') {echo 'selected="selected"';}?> value="bottom">Bottom</option>
						</select> 
					<?php
						break;
						case ($key == 'history_lenght' || $key == 'text_char_limit' || $key == 'invite_link' || $key == 'disconnect_link'):
					?>
						<input size="50" type="text" name="avgsetting_<?php echo $key;?>" value="<?php echo $av_general_settings[0]->$key; ?>" />
					<?php
						break;
					default :
					?>
						<input size="50" type="text" name="avgsetting_<?php echo $key;?>" value="<?php echo $av_general_settings[0]->$key; ?>" />
				<?php }?>
			</td>
		</tr>
		<?php } ?>
	</table>
	<p class="submit"><input type="submit" value="Update Options" class="button-primary" /></p>
</form>