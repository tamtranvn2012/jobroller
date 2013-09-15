<?php
	require "RtmpClient.class.php";
	require "debug.php";
	require "connectionDetails.php";
	@session_start(); 

	$host=HOST;
	$appName=APPNAME;
	$port=PORT;
	
	
	  if(empty($_SESSION["username"])){
			echo "Username is mandatory. Connection Rejected";
			return false;
		}
		
		$client = new RtmpClient();
		$data = array($_SESSION["gender"],$_SESSION["username"],"","","user",0,0,$_SESSION["thumbnailUrl"],$_SESSION["theToken"],$_SESSION["profileUrl"],$_SESSION["profileCountryFlag"],$_SESSION["clientUniqueIdentifier"],$revision,$_COOKIE["userId"],getRealIpAddr());

		$client->connect($host,$appName,$port,$data);
		
		if(isset($_SESSION["roomId"])){
			$client->leaveRoom($_SESSION["roomId"]);
		}
	
//$client->close();
?>
