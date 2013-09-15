<? ob_start(); ?>
<?php
	require "RtmpClient.class.php";
	require "debug.php";
	require "connectionDetails.php";
	require "utils.php";
	
	$mobileClient = true;
	
	include("token_request.php");
	//session_start();
	
	$host=HOST;
	$appName=APPNAME;
	$port=PORT;	
	echo '<!DOCTYPE html>
			  <html>
			  <head>
			  <meta name="viewport" content="height=device-height,width=device-width,initial-scale=1.0,maximum-scale=1.0">
			  <meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
			  
			  <link rel="stylesheet" href="css/userPanel.css" />
			  <link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.css" />
			  <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
			  <script src="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.js"></script>
			  
			  <title>RoomsList</title>
			  </head>
			  <body>';

	  if(empty($_POST['username']) && empty($_SESSION["username"]))
		{
			echo '<b>Username is mandatory. Connection Rejected</b>
				 <a href="m.php" data-role="button" data-inline="true" data-theme="b" data-icon="arrow-l" data-transition="slide" data-direction="reverse">Go To Login Page</a>';
			return false;
		}

		if (isset($_POST['username'])){
			$userName = trim($_POST['username']);
			if(isset($_POST["gender"])){
				$gender = $_POST["gender"];
			}else{
				$gender = $_POST["genderFromHidden"];
			}
			
			$_SESSION["username"] = $userName;
			$_SESSION["gender"] =  $gender;
		
		}
	
		$data = array($_SESSION["gender"],$_SESSION["username"],"","","user",0,0,$_SESSION["thumbnailUrl"],$_SESSION["theToken"],$_SESSION["profileUrl"],$_SESSION["profileCountryFlag"],$_SESSION["clientUniqueIdentifier"],$revision,$_COOKIE["userId"],getRealIpAddr());
		
		//echo "Inainte de conexiune<br/>";
		$client = new RtmpClient();
		$client->connect($host,$appName,$port,$data);
		//die("Connected");
		
		//echo "Dupa conexiune";
		//checking if the client is licensed
		$licenseKey = $client->checkIfLicensed();
		//checking if the client is banned
		//$bannedStatus = $client->checkIfBanned();
		
		if($licenseKey == false){
			//die ('<br/><b>You do not have a license key entered or you are not connecting from a licensed domain.</b><br/><br/>');
			echo '<b>You do not have a license key entered or you are not connecting from a licensed domain.</b>
				 <a href="m.php" data-role="button" data-inline="true" data-theme="b" data-icon="arrow-l" data-transition="slide" data-direction="reverse">Go To Login Page</a>';
			return false;
		}/*else if($bannedStatus == true){
			header('Location: error.php?bannedStatus=true');
			return false;	
		}*/else{
			//Creating the rooms list 
			$roomsList = $client->getRoomsList($_COOKIE["userId"]);
			//var_dump($roomsList);
			//die();
			
			
		//sorting the roomList by number of users
		$rlArray = array();
				
		foreach($roomsList as $j){
				array_push($rlArray, $j);
		}
		
		function sortRooms($a, $b){
			return $b["users"] - $a["users"];
		}
		
		usort($rlArray, "sortRooms");
			
		//setting the auto join room
		$roomToJoin = "";
		if(isset($_SESSION["dropInRoom"])){
			if(strpos ($_SESSION["dropInRoom"],"[")){
				$firstPart= explode("[",$_SESSION["dropInRoom"]);
				$secondPart = explode(",",$firstPart[1]);
				$roomToJoin = $secondPart[0];
			}else{
				$roomToJoin = $_SESSION["dropInRoom"];
			}
		}
		
		if($roomToJoin != "" && isset($_SESSION["firstTimeConnected"]) && $_SESSION["firstTimeConnected"] == true){
			foreach($roomsList as $r){
			  if($r["id"] == $roomToJoin ){
				$roomNameToJoin = $r["name"];
				//echo $roomNameToJoin;
				break;
			  }
			}
			$_SESSION["firstTimeConnected"] = false;
			header("Location: joinRoom.php?rid=".$roomToJoin."&roomName=".$roomNameToJoin);
		}else{
			
			echo "<div data-role='header' data-position='fixed' data-fullscreen='false' data-tap-toggle='false'>
					<a href='m.php' class='ui-btn-left' data-theme='b' data-icon='arrow-l' data-transition='slide' data-direction='reverse'>".$_SESSION["AVC_btnloginArea"]."</a>
					<h1>".$_SESSION["AVC_welcomeUser"]." <b>".$_SESSION["username"]."</b></h1>
				 </div>";
			//echo "the COOKIE ".$_COOKIE["userId"]."<br/>";
			echo "<ul data-role='listview'>";
					
			for ($i=0; $i<count($rlArray); $i++){
				if($rlArray[$i]["password"] != ""){
					echo"<li  data-theme='b'>
						<div style='margin-top:-10px;'><img src='img/lock.png' class='ui-li-icon'/></div>
						<div style='margin-left:10px;'><h1>".$rlArray[$i]["name"]."</h1></div>
						<p>".$_SESSION["AVC_inRoom"]." <b>".$rlArray[$i]["users"]."</b> ".$_SESSION["AVC_roomUsers"]."</p>
						<p>".$_SESSION["AVC_owner"]." <b>".$rlArray[$i]["ownerName"]."</b></p>
						<p>".$_SESSION["AVC_isPrivate"]." <b>".$_SESSION["AVC_isPrivateYes"]."</b></p>
						
					</li>";
				}else{
					echo"<li>
					 <a href='joinRoom.php?rid=".$rlArray[$i]["id"]."&roomName=".$rlArray[$i]["name"]."' data-transition='slide'>
						<h1>".$rlArray[$i]["name"]."</h1>
						<p>".$_SESSION["AVC_inRoom"]." <b>".$rlArray[$i]["users"]."</b> ".$_SESSION["AVC_roomUsers"]."</p>
						<p>".$_SESSION["AVC_owner"]." <b>".$rlArray[$i]["ownerName"]."</b></p>
						<p>".$_SESSION["AVC_isPrivate"]." <b>".$_SESSION["AVC_isPrivateNo"]."</b></p>
					 </a>
					</li>";
				}
				
			}
			echo "</ul>
				<div data-role='footer' data-position='fixed' data-fullscreen='false' data-tap-toggle='false'>
					<h4>AVChat</h4>
				</div>";
		}
			
			echo "<script>
					/*var refreshPageInterval;
			
					$(document).bind('pageinit', function() {
						refreshPageInterval = setInterval(function(){refreshPage()},10000);
					});
					
					function refreshPage(){
						//clearInterval(refreshPageInterval);
						window.location.reload();
					}*/
					$(document).delegate('.ui-page', 'pagebeforeshow', function () {
						$(this).css('background', '#f9f9f9');
					});				
				</script>	
			  </body>
			  </html>";
			//$client->close();
		}
				
		
?>
<? ob_flush(); ?>