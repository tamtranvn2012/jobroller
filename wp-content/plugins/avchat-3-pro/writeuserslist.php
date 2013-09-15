<?php
//this file is called by the media server whenever a user joins/leaves a room or whenever a room is created/deleted/etc...
//some vars are sent to this script by the media server via POST: instance, nr_of_users and xml

//ip: String or Boolean(false)
//description: The IP of the FMS/Red5/Woowza server making calls to this script. This is a security feature so that this script is only called by your media server. 
//values: any ip as string:"127.0.0.1" OR false for disabled
//default: false
$ip = false;

//we check if the ip of the caller matches the ip set above
if($ip && ($_SERVER["REMOTE_ADDR"] && $ip != $_SERVER["REMOTE_ADDR"]) ){
	die();
}

//the instance name of the FMS/Red5/Wowza app that called the present script, this var is sent via POST
$instance=$_POST['instance']; 

//the number of users var is sent via POST
$nr_of_users = $_POST['nr_of_users'];

//the xml var containing the users/rooms data in xml format is sent via POST
if (get_magic_quotes_gpc()==1){
	$xml = stripslashes($_POST['xml']);
}else{
	$xml = $_POST['xml'];
}
$xml=str_replace("\r\n","",$xml);

//the file that will hold the number of users
$textfilename = 'users_'.$instance.'.txt';

//Writing the XML file containing the rooms and users in each room in xml format
$xmlfilename = 'users_'.$instance.'.xml';

//we make a small check
if (strlen($instance)<100 && $nr_of_users<100000){
	$result ="&result=";
	
	if (!$handle = fopen($textfilename, 'w+')) {
	    $result.="Cannot open file ($textfilename)";
	    exit;
	}
	if (fwrite($handle, $nr_of_users) === FALSE) {
	    $result.="Cannot write to file ($textfilename)";
	    exit;
	}
	$result.="Success, wrote ($nr_of_users) to file ($textfilename)";
	
	
	if (!$handle = fopen($xmlfilename, 'w+')) {
	   $result.="Cannot open file ($xmlfilename)";
	    exit;
	}
	if (fwrite($handle, $xml) === FALSE) {
	    $result.="Cannot write to file ($xmlfilename)";
	    exit;
	}
	$result.="Success, wrote the xml to file ($xmlfilename)";
	
	echo $result;
	
	fclose($handle);
}else{
	die ("instance=".$instance.", nr_of_users=".$nr_of_users);
}
?>