<?php
	require "RtmpClient.class.php";
	require "debug.php";
	require "connectionDetails.php";
	require "utils.php";
	@session_start(); 

	$host=HOST;
	$appName=APPNAME;
	$port=PORT;
	
	
	  if(empty($_SESSION["username"])){
			echo "Username is mandatory. Connection Rejected";
			return false;
		}
		
	if(isset($_SESSION["roomId"])){
		$client = new RtmpClient();
		$data = array($_SESSION["gender"],$_SESSION["username"],"","","user",0,0,$_SESSION["thumbnailUrl"],$_SESSION["theToken"],$_SESSION["profileUrl"],$_SESSION["profileCountryFlag"],$_SESSION["clientUniqueIdentifier"],$revision,$_COOKIE["userId"],getRealIpAddr());

		$client->connect($host,$appName,$port,$data);
		//checking to see if the client has been kicked to stop refreshing the userslist
		/*$amIKicked =$client->checkIfKicked();
		if($amIKicked == true){
			header('Location: error.php?kickedStatus=true');
			die();
		}*/
		$usersList = $client->getUsersList($_SESSION["roomId"]);
		//var_dump($usersList);
		//print_r($usersList);
		
		/*$theUsers = array();
		foreach($usersList as $i){
			array_push($theUsers, $i);
		}*/
		
		$genders = simplexml_load_file('genders.xml');
		
		
		//echo '<ul data-role="listview" data-inset="true" >';
		echo "<b>".$_SESSION["AVC_usersInRoom"]."</b> <br/>";
			foreach($usersList as $i){
				foreach ($genders as $genderInfo){
					if($genderInfo['id'] == $i["gender"]){
						$genderIconUrl = $genderInfo['iconUrl'];
					}
				}
				if($i["thumbnailUrl"] !=""){
					echo '<img src="'.$i["thumbnailUrl"].'" height="24" width="auto"/>';
				}
				echo '<img src="../'.$genderIconUrl.'"/ width="14" style="vertical-align: middle;" height="14">';
				if(strpos($i["agent"],'Android/iOS') || strpos($i["agent"],'HTML')){
					echo '<img style="vertical-align: middle;" src="img/mobile-icon.png"/> ';
				}else{
					echo '<img style="vertical-align: middle;" src="img/icon_computer.gif"/> ';
				}
				$ipPlaceHolder ="";
				if(isset($_SESSION["autoAddIpToUsername"])){
					if($_SESSION["autoAddIpToUsername"] == 1){
						$ipPlaceHolder = "(".$i["ip"].")";
					}
				}
				echo '<b>'.$i["username"].'</b>'.$ipPlaceHolder;
				if($i["cam"] > 0){
					echo '<img style="vertical-align: middle;" src="img/camera.gif"/>';
				}else if($i["numberOfCams"] > 0){
							echo '<img style="vertical-align: middle;" src="img/camera-off.png"/>';
						}
				if($i["mic"] > 0){
					echo '<img style="vertical-align: middle;" src="img/speaker.gif"/>';
				}else if($i["numberOfMics"] > 0){
							echo '<img style="vertical-align: middle;" src="img/speaker-off.png"/>';
						}
				echo'<br/>';
			}
			
		//echo '</ul>';
	}	
	
	/*function getIp($proper_address){
		if(count(explode(".",(string)$proper_address)) > 1){
			return (string)$proper_address;
		}
		
		$output="";
		if($proper_address && ($proper_address >=0 || $proper_address <= 4294967295)){
			$output = floor($proper_address / pow( 256, 3 ) ) . '.' .
	            floor( ( $proper_address % pow( 256, 3 ) ) / pow( 256, 2 ) ) . '.' .
	            floor( ( ( $proper_address % pow( 256, 3 ) )  % pow( 256, 2 ) ) / pow( 256, 1 ) ) . '.' . floor( ( ( ( $proper_address % pow( 256, 3 ) ) % pow( 256, 2 ) ) % pow( 256, 1 ) ) / pow( 256, 0 ) );
		}
		
		return $output;
	}*/
	
		
//$client->close();
?>