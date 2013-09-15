<?php 
/* -----------------------------------------------------------------------------------*/
function amru_query( $args ) { /*  get all user data and attempt to extract out any object values into arrays for listing  */
global $wpdb;

// just do simply for now, as we have filtering later to chope out bits
	$_REQUEST['mem'] = true;  // to show memory

	if (is_multisite() and amr_is_network_admin()) {
		$where = ' INNER JOIN ' . $wpdb->usermeta .  
       ' ON      ' . $wpdb->users 
	   . '.ID = ' . $wpdb->usermeta . '.user_id 
        WHERE   ' . $wpdb->usermeta .'.meta_key =\'' . $wpdb->prefix . 'capabilities\'' ;
		
		$wheremeta = " WHERE ".$wpdb->usermeta.".user_id IN ".
		"(SELECT distinct user_id FROM ".$wpdb->usermeta
		." WHERE ".$wpdb->usermeta .".meta_key ='" . $wpdb->prefix . "capabilities')";
	}
	else {
		$where = '';
		$wheremeta = '';
	}	

	//track_progress('Start amr get users');	
	//$query = $wpdb->prepare( "SELECT * FROM $wpdb->usermeta".$where); // WHERE meta_key = %s", $meta_key );
	$query = "SELECT * FROM $wpdb->usermeta".$where; // we controlled the input so prepare not necessary
	$metalist = $wpdb->get_results($query, OBJECT_K);

	//track_progress('After get users meta');

// arghh - sometimes we need usrs that do not have the meta values, so does this mean we have to get all users ?	
	//$query = $wpdb->prepare( "SELECT ID, user_login, user_nicename, user_email, user_url, user_registered, display_name FROM $wpdb->users".$where); // WHERE meta_key = %s", $meta_key );
	$query = "SELECT ID, user_login, user_nicename, user_email, user_url, user_registered, display_name FROM $wpdb->users".$where; 
	$users = $wpdb->get_results($query, OBJECT_K);  // so returns id as key - NOT WORKING IN EVERY SITE
	
	//track_progress('After get users without meta');
	
	foreach ($users as $i => $u) {

		if (isset($metalist[$i])) {
			$users[$i] = (object) array_merge((array) $u, (array) $metalist[$i]);			
			unset($metalist[$i]);
		}
		
	}		
	//track_progress('After combining users with their meta');
	return ($users);

}

