<? ob_start(); ?>
<?php
	$doNotEcho = true;
	chdir('../');
	include("avc_settings.php");
		
	if($avconfig['enableHtmlClientForDesktopBrowser'] != 1 && (strpos($_SERVER['HTTP_USER_AGENT'],'Windows') || strpos($_SERVER['HTTP_USER_AGENT'],'Macintosh'))){
		die("The HTML 5 client is disabled for desktop browsers");
	}
	
	@session_start();
	
	//loading the translation file
	
	if(isset($avconfig['languagefile']) && $avconfig['languagefile'] != ''){
		$_SESSION["languagefile"] = $avconfig['languagefile'];
	}else{
		$_SESSION["languagefile"] = "translations/en.xml";
	}
	
	$trans = simplexml_load_file($_SESSION["languagefile"]);
	$theUnits = $trans->file->body->{'trans-unit'};
	
	echo'<!DOCTYPE html>
		<html>
			<head>
				<meta charset="utf-8">
				<meta name="viewport" content="height=device-height,width=device-width,initial-scale=1.0,maximum-scale=1.0">
				
				<link rel="stylesheet" href="css/userPanel.css" />
				<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.css" />
				<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
				<script src="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.js"></script>
				
				<title>AVChat</title>
				</head>
			<body>
			<div data-role="header">
			<h1>'.(string)$theUnits[236]->source.'</h1>
			</div>';
		
		//setting the cookie
		if(empty($_COOKIE["userId"])){
			setcookie("userId", "u".rand(10000,999999));
		}
	
		if(isset($avconfig['showLoginError']) && $avconfig['showLoginError'] == 1){
			
			$disableLogin ="";
			$disableRegister ="";
			
			if(isset($avconfig['loginPageURL']) && isset($avconfig['registerPageURL'])){
				
				echo '<div style="margin:20px;"><b>'.(string)$theUnits[219]->source.'</b> <br/>
				  <b>'.(string)$theUnits[220]->source.'</b><br/>';
				  if($avconfig['registerPageURL'] ==""){
					echo '<a class="ui-disabled" href="'.$avconfig['registerPageURL'].'" data-role="button"  data-inline="true" data-theme="b" rel="external">'.(string)$theUnits[221]->source.'</a><br/><br/>';
				  }else{
						echo '<a href="'.$avconfig['registerPageURL'].'" data-role="button"  data-inline="true" data-theme="b" rel="external">'.(string)$theUnits[221]->source.'</a><br/><br/>';
				  }
				 echo' <b>'.(string)$theUnits[222]->source.'</b><br/>
				  <b>'.(string)$theUnits[223]->source.'</b><br/>';
				  if($avconfig['loginPageURL'] ==""){
						echo '<a class="ui-disabled" href="'.$avconfig['loginPageURL'].'" data-role="button"  data-inline="true" data-theme="b" rel="external">'.(string)$theUnits[224]->source.'</a><br/><br/>';
					}else{
						echo '<a href="'.$avconfig['loginPageURL'].'" data-role="button"  data-inline="true" data-theme="b" rel="external">'.(string)$theUnits[224]->source.'</a><br/><br/>';
					}
				 echo  '</div>';
				  
			return;
			}	  
		}
		
		//loading the genders
		$genders = simplexml_load_file('genders.xml');
		$_username="";
		$_gender="";
		$disabled = "";
		
		if(isset($avconfig['username']) && $avconfig['username'] != ''){
			$_username = $avconfig['username'];
		}
		
		if(isset($_SESSION["username"]) && $avconfig['username'] == ''){
			$_username = $_SESSION["username"];
		}
		
		if(isset($avconfig['gender']) && $avconfig['gender'] != ''){
			$_gender = $avconfig['gender'];
		}
		
		if(isset($avconfig['changegender']) && $avconfig['changegender'] == 0){
			$disabled = "disabled";
		}
		
		if( $avconfig['changegender'] == 0 &&  $avconfig['changeuser'] == 0 && !empty($avconfig['username'])){
			$_POST['username'] = $avconfig['username'];
			$_POST['gender'] = $avconfig['gender'];
			header("Location: getRooms.php");
		}
		
		if(isset($avconfig['connectionstring']) && $avconfig['connectionstring'] != ''){
			echo'<div style="margin:20px; position:relative">
				<form id="login" action="getRooms.php" method="post" accept-charset="UTF-8">
				 <br/><label for="username" >'.(string)$theUnits[8]->source.':</label>';
				 
				 if(isset($avconfig['changeuser']) && $avconfig['changeuser'] == 0){
					echo '<input type="text" name="username2" id="username2" value="'.$_username.'" maxlength="50" disabled="true"/>';
					echo '<input type="hidden" name="username" id="username" value="'.$_username.'"/>';		
				 }else{
					echo '<input type="text" name="username" id="username" value="'.$_username.'" maxlength="50"/>';
				 }
				 
				 echo '<div data-role="fieldcontain">
					   <legend>'.(string)$theUnits[237]->source.'</legend>
					 
					     <fieldset data-role="controlgroup" >
						   <input type="radio" name="gender" value="'.$genders->gender[0]['id'].'" id="'.$genders->gender[0]['id'].'" checked="checked" '.$disabled.'/>
						   <label for="'.$genders->gender[0]['id'].'"><img src="../'.$genders->gender[0]['iconUrl'].'" width="18px" style="vertical-align:text-top;" height="18px"/> '.$genders->gender[0]['label'].'</label>';
						
					 for($i=1; $i<count($genders); $i++){
						if($genders->gender[$i]['adminOnly'] == "false"){
							if($_gender != "" && $genders->gender[$i]['id'] == $_gender){
								echo '<input type="radio" name="gender" value="'.$genders->gender[$i]['id'].'" id="'.$genders->gender[$i]['id'].'" checked="checked" '.$disabled.'/>
								  <label for="'.$genders->gender[$i]['id'].'"><img src="../'.$genders->gender[$i]['iconUrl'].'" width="18px" style="vertical-align:text-top;" height="18px"/> '.$genders->gender[$i]['label'].'</label>';
								  echo '<input type="hidden" name="genderFromHidden" value="'.$genders->gender[$i]['id'].'" id="'.$genders->gender[$i]['id'].'"/>';	
							}else{
								echo '<input type="radio" name="gender" value="'.$genders->gender[$i]['id'].'" id="'.$genders->gender[$i]['id'].'" '.$disabled.'/>
								  <label for="'.$genders->gender[$i]['id'].'"><img src="../'.$genders->gender[$i]['iconUrl'].'" width="18px" style="vertical-align:text-top;" height="18px"/> '.$genders->gender[$i]['label'].'</label>';
								  echo '<input type="hidden" name="genderFromHidden" value="'.$genders->gender[$i]['id'].'" id="'.$genders->gender[$i]['id'].'"/>';
							}
						}
					 }
					echo '</fieldset>
				</div>
				<input id="connectBtn" type="submit" name="Submit" value="'.(string)$theUnits[9]->source.'"/>
				</form>
				</div>';
		}else{
			echo '<br/><b>The connection string is empty</b><br/><br/>';
		}
		
	if(isset($avconfig['dropInRoom']) && $avconfig['dropInRoom'] != ''){	
		$_SESSION["dropInRoom"] = $avconfig['dropInRoom'];
		$_SESSION["firstTimeConnected"] = true;
	}
	
	if(isset($avconfig['typingEnabled'])){
		$_SESSION["typingEnabled"] = $avconfig['typingEnabled'];
	}
	
	if(isset($avconfig['disconnectButtonLink']) && $avconfig['disconnectButtonLink'] != ''){
		$_SESSION["disconnectButtonLink"] = $avconfig['disconnectButtonLink'];
	}else{
		$_SESSION["disconnectButtonLink"] = "m.php";
	}
	
	if(isset($avconfig['emoticonsurl']) && $avconfig['emoticonsurl'] != ''){
		$_SESSION["emoticonsurl"] = $avconfig['emoticonsurl'];
	}else{
		$_SESSION["emoticonsurl"] = "emoticons/squarePack/emoticons.xml";
	}
	
	if(isset($avconfig['autoAddIpToUsername'])){
		$_SESSION["autoAddIpToUsername"] = $avconfig['autoAddIpToUsername'];
	}
	
	if(isset($avconfig['thumbnailUrl']) && $avconfig['thumbnailUrl'] != ''){
		$_SESSION["thumbnailUrl"] = $avconfig['thumbnailUrl'];
	}else{
		$_SESSION["thumbnailUrl"]="";
	}
	
	if(isset($avconfig['profileUrl']) && $avconfig['profileUrl'] != ''){
		$_SESSION["profileUrl"] = $avconfig['profileUrl'];
	}else{
		$_SESSION["profileUrl"]="";
	}
	
	if(isset($avconfig['profileCountryFlag']) && $avconfig['profileCountryFlag'] != ''){
		$_SESSION["profileCountryFlag"] = $avconfig['profileCountryFlag'];
	}else{
		$_SESSION["profileCountryFlag"]="";
	}
	
	if(isset($avconfig['clientUniqueIdentifier']) && $avconfig['clientUniqueIdentifier'] != ''){
		$_SESSION["clientUniqueIdentifier"] = $avconfig['clientUniqueIdentifier'];
	}else{
		$_SESSION["clientUniqueIdentifier"]="";
	}
	
	if(isset($avconfig['showMobileChatHistory'])){
		$_SESSION["showMobileChatHistory"] = $avconfig['showMobileChatHistory'];
	}else{
		$_SESSION['showMobileChatHistory'] = 1;
	}
	
	
	//adding translations to the session variables for the other pages
	$_SESSION["AVC_welcomeMessage"] = (string)$theUnits[235]->source;
	$_SESSION["AVC_btnloginArea"] = (string)$theUnits[238]->source;
	$_SESSION["AVC_welcomeUser"] = (string)$theUnits[239]->source;
	$_SESSION["AVC_inRoom"] = (string)$theUnits[240]->source;
	$_SESSION["AVC_owner"] = (string)$theUnits[241]->source;
	$_SESSION["AVC_isPrivate"] = (string)$theUnits[242]->source;
	$_SESSION["AVC_isPrivateYes"] = (string)$theUnits[48]->source;
	$_SESSION["AVC_isPrivateNo"] = (string)$theUnits[49]->source;
	$_SESSION["AVC_roomUsers"] = (string)$theUnits[51]->source;
	$_SESSION["AVC_btnRooms"] = (string)$theUnits[18]->source;
	$_SESSION["AVC_welcomeToRoom"] = (string)$theUnits[246]->source;
	$_SESSION["AVC_btnLeaveChat"] = (string)$theUnits[17]->source;
	$_SESSION["AVC_clickToType"] = (string)$theUnits[245]->source;
	$_SESSION["AVC_btnSend"] = (string)$theUnits[137]->source;
	$_SESSION["AVC_btnUsers"] = (string)$theUnits[243]->source;
	$_SESSION["AVC_usersInRoom"] = (string)$theUnits[244]->source;
	
	//loading the emoticons
	$emotes = simplexml_load_file($_SESSION["emoticonsurl"]);
	$emoticonsArray = array();
	
	foreach($emotes as $e){
		//echo $e["text"]." -> ".$e["url"]."<br/>";
		$emoticonsArray[(string)$e["text"]] =  (string)$e["url"];
	}
	
	$_SESSION["AVC_emoteIcons"] = $emoticonsArray;
	
	/*foreach($_SESSION["AVC_emoteIcons"] as $key => $value){
		echo $value;
	}*/
	
?>


	
<div data-role="footer" data-position="fixed">
	<h4>AVChat</h4>
</div>
<!--script-->
<script>
$(document).bind("pageinit", function(){
	 $('[type="submit"]').button('disable');
	 if($('input[type="text"]').val().length > 2 ){
			$('[type="submit"]').button('enable');
		}
     $('input[type="text"]').keyup(function() {
        if($(this).val() != '' && $(this).val().length > 2){
		   $('[type="submit"]').button('enable');
		   $('[type="submit"]').button('refresh');
        }else{
		   $('[type="submit"]').button('disable');
		   $('[type="submit"]').button('refresh');
		}
     });
 });
 
 $('#login').submit(function() {
	if($("input:first").val().length < 3){
		return false;
	}
});
 
</script>	
	
</body>
</html>
<? ob_start(); ?>