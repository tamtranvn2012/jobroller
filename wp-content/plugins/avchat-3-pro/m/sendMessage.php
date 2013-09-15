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
		
		$client = new RtmpClient();
		$data = array($_SESSION["gender"],$_SESSION["username"],"","","user",0,0,$_SESSION["thumbnailUrl"],$_SESSION["theToken"],$_SESSION["profileUrl"],$_SESSION["profileCountryFlag"],$_SESSION["clientUniqueIdentifier"],$revision,$_COOKIE["userId"],getRealIpAddr());

		$client->connect($host,$appName,$port,$data);
		
		//checking to see if the client has been banned in room to stop refreshing the messages
		/*$bannedInRoom = $client->checkBanInRoom();
			if($bannedInRoom == true){
				echo 'You have been banned from this room. Try to enter again later.';
				return;
			}
		*/
		if(isset($_POST["text"])){
			$_SESSION["textMessage"] = $_POST["text"];
			$client->distributeTextMessage($_SESSION["roomId"],"all","all",$_POST["text"],"");
			$theChat = $client->getMessages($_SESSION["roomId"],$_COOKIE["userId"]);			
		}		
		
		$history = array();
		
		$welcomeMessage = array();
		$welcomeMessage["time"] = $_COOKIE["timeOfLastMessage"] + 1;
		$welcomeMessage["senderusername"] = "";
		$welcomeMessage["textmessage"] = $_SESSION["AVC_welcomeMessage"];
		
		foreach($theChat as $j){
			if(@strcmp($j["senderusername"],"r") != 0){
				array_push($history, $j);
			}
		}
		//adding the welcome message
		array_push($history, $welcomeMessage);
		
		// sorting the array
		function sortChatOnSend($a, $b){
			return $a['time'] - $b['time'];
		}
		usort($history, "sortChatOnSend"); 
		
		$pattern ="#(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$#i";
		$ytPattern="#http:\/\/w{0,3}\.youtube[^' '\n\r]+#i";
		$ytIDPattern="#(?<=v(\=|\/))([-a-zA-Z0-9_]+)|(?<=youtu\.be\/)([-a-zA-Z0-9_]+)#i";
		
		
		if(count($history) >= 40){
			  for ($i=count($history)-40; $i<count($history); $i++){
				if($history[$i]["time"] > $_COOKIE["timeOfLastMessage"]){
				  if(preg_match($pattern, $history[$i]["textmessage"]) || preg_match($ytPattern, $history[$i]["textmessage"])){
					echo "<div><b>".$history[$i]["senderusername"]."</b>: <a href='".$history[$i]["textmessage"]."' target='_blank'>".$history[$i]["textmessage"]."</a></div>";
						//if(preg_match($ytIDPattern, $history[$i]["textmessage"])){
							//echo'<iframe width="560" height="315" src="http://www.youtube.com/embed/UsvgYXtQB48" frameborder="0" allowfullscreen></iframe>';
						//}
				  }else{
					foreach($_SESSION["AVC_emoteIcons"] as $key => $value){
						$history[$i]["textmessage"] = str_replace($key, "<img src='../".$value."' width='18' height='18'/>", $history[$i]["textmessage"]);
					}
					 if($history[$i]["senderusername"] != ""){
						echo "<div><b>".$history[$i]["senderusername"]."</b>: ".$history[$i]["textmessage"]."</div>";
					 }else{
						echo "<div style='color:#548eba'><b>".$history[$i]["textmessage"]."</b></div>";
					 }
				  }
				}else if((int)$_SESSION["showMobileChatHistory"] == 1){
				  if(preg_match($pattern, $history[$i]["textmessage"]) || preg_match($ytPattern, $history[$i]["textmessage"])){
					echo "<div style='color:#b4b4b4'><b>".$history[$i]["senderusername"]."</b>: <a href='".$history[$i]["textmessage"]."' target='_blank'>".$history[$i]["textmessage"]."</a></div>";
				  }else{
					foreach($_SESSION["AVC_emoteIcons"] as $key => $value){
						$history[$i]["textmessage"] = str_replace($key, "<img src='../".$value."' width='18' height='18'/>", $history[$i]["textmessage"]);
					}
					echo "<div style='color:#b4b4b4'><b>".$history[$i]["senderusername"]."</b>: ".$history[$i]["textmessage"]."</div>";
				  }
				}	
			  }
		}else{
			  for ($i=0; $i<count($history); $i++){
				if($history[$i]["time"] > $_COOKIE["timeOfLastMessage"]){
				  if(preg_match($pattern, $history[$i]["textmessage"]) || preg_match($ytPattern, $history[$i]["textmessage"])){
					echo "<div><b>".$history[$i]["senderusername"]."</b>: <a href='".$history[$i]["textmessage"]."' target='_blank'>".$history[$i]["textmessage"]."</a></div>";
						/*if(preg_match($ytIDPattern, $history[$i]["textmessage"])){
							echo'<iframe width="560" height="315" src="http://www.youtube.com/embed/UsvgYXtQB48" frameborder="0" allowfullscreen></iframe>';
						}*/
				  }else{
					foreach($_SESSION["AVC_emoteIcons"] as $key => $value){
						$history[$i]["textmessage"] = str_replace($key, "<img src='../".$value."' width='18' height='18'/>", $history[$i]["textmessage"]);
					}
					 if($history[$i]["senderusername"] != ""){
						echo "<div><b>".$history[$i]["senderusername"]."</b>: ".$history[$i]["textmessage"]."</div>";
					 }else{
						echo "<div style='color:#548eba'><b>".$history[$i]["textmessage"]."</b></div>";
					 }
				  }
				}else if((int)$_SESSION["showMobileChatHistory"] == 1){
				  if(preg_match($pattern, $history[$i]["textmessage"]) || preg_match($ytPattern, $history[$i]["textmessage"])){
					echo "<div style='color:#b4b4b4'><b>".$history[$i]["senderusername"]."</b>: <a href='".$history[$i]["textmessage"]."' target='_blank'>".$history[$i]["textmessage"]."</a></div>";
				  }else{
					foreach($_SESSION["AVC_emoteIcons"] as $key => $value){
						$history[$i]["textmessage"] = str_replace($key, "<img src='../".$value."' width='18' height='18'/>", $history[$i]["textmessage"]);
					}
					echo "<div style='color:#b4b4b4'><b>".$history[$i]["senderusername"]."</b>: ".$history[$i]["textmessage"]."</div>";
				  }
				}	
			  }	
		}

//$client->close();
?>