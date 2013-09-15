<? ob_start(); ?>
<?php
	require "RtmpClient.class.php";
	require "debug.php";
	require "connectionDetails.php";
	require "utils.php";
	@session_start(); 

	$host=HOST;
	$appName=APPNAME;
	$port=PORT;
	
	echo '<!DOCTYPE html>
		  <html>
		  <head>
			  <meta name="viewport" content="height=device-height,width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable = no">		  
			  <meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />

			  <link rel="stylesheet" href="css/userPanel.css" />
			  <link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.css" />
			  <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
			  <script src="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.js"></script>
		
		 <title>The Chat</title>

		 </head>
		 <body>';
	
	
	  if(empty($_SESSION["username"])){
			echo "Username is mandatory. Connection Rejected";
			return false;
		}
		
		if(isset($_GET["rid"]) && isset($_GET["roomName"])){
			$_SESSION["roomId"] = $_GET["rid"];
			$_SESSION["roomName"] = $_GET["roomName"];
		}
		
		$typingEnabled = "";
		if(isset($_SESSION["typingEnabled"]) && $_SESSION["typingEnabled"] == 0){
			$typingEnabled = "disabled";
		}

		$client = new RtmpClient();
		$data = array($_SESSION["gender"],$_SESSION["username"],"","","user",0,0,$_SESSION["thumbnailUrl"],$_SESSION["theToken"],$_SESSION["profileUrl"],$_SESSION["profileCountryFlag"],$_SESSION["clientUniqueIdentifier"],$revision,$_COOKIE["userId"],getRealIpAddr());

		$client->connect($host,$appName,$port,$data);
		
		if(isset($_GET["rid"])){
			$client->joinRoom($_SESSION["roomId"],"",1);
			/*$bannedInRoom = $client->checkBanInRoom();
			if($bannedInRoom == "true"){
				echo'<script>
				bannedFromRoom();
				</script>';
			}*/
		}
		
		//setting the last message
		$theChat = $client->getMessages($_SESSION["roomId"],$_COOKIE["userId"]);
		$history = array();
		
		foreach($theChat as $j){
			if(@strcmp($j["senderusername"],"r") != 0){
				array_push($history, $j);
			}
		}
		// sorting the array
		
		function sortByTime($a, $b){
			return $a['time'] - $b['time'];
		}
		
		usort($history,"sortByTime"); 
		
		if(count($history) > 0){
			setcookie("timeOfLastMessage",$history[count($history)-1]["time"]);
		}else{
			setcookie("timeOfLastMessage",0);
		}
		//last message set	
		
			
		echo "<div data-role='header' data-position='fixed' data-fullscreen='false' data-tap-toggle='false'>
				<a href='#' class='ui-btn-left' data-theme='b' data-icon='arrow-l' onclick='leaveRoom();'>".$_SESSION["AVC_btnRooms"]."</a>
				<h1>".$_SESSION["AVC_welcomeUser"]." <b>".$_SESSION["username"]." </b>".$_SESSION["AVC_welcomeToRoom"]." <b>".$_SESSION["roomName"]."</b></h1>
				<a href='".$_SESSION["disconnectButtonLink"]."' class='ui-btn-right' data-theme='e' data-icon='arrow-r' data-iconpos='right' onclick='leaveChat();'>".$_SESSION["AVC_btnLeaveChat"]."</a>
			 </div>";
			 
?>	

	
	<div data-role="content">
		<div id="chatArea"  style="margin-left:-5px; margin-top:-10px; position:relative;"></div>
	 </div>
	<!--ul data-role="listview" data-inset="true" >
	</ul-->
	
	<div data-role="footer" data-position="fixed" data-fullscreen="false" data-tap-toggle="false">
		<div data-role="fieldcontain" style="margin-left:10px; position:relative;">
			<input type='text' id='text' name='text' placeholder='<?php echo $_SESSION["AVC_clickToType"];?>' <?php echo $typingEnabled; ?> data-theme="b"  data-mini="true" onkeydown="if (event.keyCode == 13) sendText();" />
			<a href="#" data-role="button" data-mini="true" data-inline="true" data-theme="b" onclick="sendText();" style="margin-bottom:5px;"><?php echo $_SESSION["AVC_btnSend"];?></a>
			<a href="#popupPanel" data-position-to="window" data-role="button" data-rel="popup" data-transition="slide" data-icon="grid" data-mini="true" data-inline="true" style="margin-bottom:5px;" ><?php echo $_SESSION["AVC_btnUsers"];?></a>
		</div>	
	</div>	
	
	<div data-role="popup" id="popupPanel" data-corners="false" data-theme="a" data-shadow="false" data-tolerance="0,0">
		<div id="usersList" ></div>
	</div> 

<!--script-->
<script>

var leaveRoomInterval;
var leaveChatInterval;
	
  var refreshTextInterval;
  var refreshUsersInterval;
  var loadingWidgetInterval;
  
  //var colors = ['red', 'purple', 'blue', 'yellow'];
  $(document).delegate(".ui-page", "pagebeforeshow", function () {
		$(this).css('background', '#f9f9f9');
	});
	 
   $(document).bind("pageinit", function() {
      refreshTextInterval = setInterval(function(){refreshText()},2000);
	  refreshUsersInterval = setInterval(function(){refreshUserList()},4000);
	  setTimeout("loadWidget()", 50);
	  $.mobile.pageLoadErrorMessage = 'Loading page';
	  
	});
	
	function loadWidget(){
		//$.mobile.loading( 'show', { theme: "a", text: "Textchat is loading", textVisible: true});
		$.mobile.pageLoadErrorMessage = 'Loading page';
		$.mobile.loading( 'show' );
		setTimeout("hideWidget()", 1950);
	}
	
	function hideWidget(){
		$.mobile.loading( 'hide' );
		
		//scrolling to the last message sent
		$("html, body").animate({ scrollTop: $(document).height() }, "slow");
	}
	
	$('input[type="text"]').bind('blur', function(e) {
       // Keyboard disappeared
	   //alert($('meta[name=viewport]').attr('content'));
	});
	
	/*$(document).bind( "pagechange", function( e, data ) {
		clearInterval(refreshTextInterval);
		clearInterval(refreshUsersInterval);
		
		e.preventDefault();
	}*/
	
	function sendText()
	{
		var xmlhttp;
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();
		  }else{
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		{
		  if (xmlhttp.readyState==4 && xmlhttp.status==200){
				document.getElementById("chatArea").innerHTML=xmlhttp.responseText;
			}
		}
		
		xmlhttp.open("POST","sendMessage.php",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		if(document.getElementById('text').value!=""){
			var theMessage = document.getElementById('text').value;
			theMessage = encodeURIComponent(theMessage);
			xmlhttp.send("text="+theMessage);
			document.getElementById('text').value = "";
		}
	}
	
	function refreshText()
	{
		var xmlhttp;
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();
		  }else{
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		{
		  if (xmlhttp.readyState==4 && xmlhttp.status==200){
				document.getElementById("chatArea").innerHTML=xmlhttp.responseText;
			}
		}
		
		xmlhttp.open("GET","refreshChat.php",true);
		xmlhttp.send();
		
	}
	
	function refreshUserList()
	{
		var xmlhttp;
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();
		  }else{
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		{
		  if (xmlhttp.readyState==4 && xmlhttp.status==200){
				document.getElementById("usersList").innerHTML=xmlhttp.responseText;
			}
		}
		
		xmlhttp.open("GET","getUsers.php",true);
		xmlhttp.send();
	}
	
	function leaveRoom(){
	
		/*var xmlhttp;
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();
		  }else{
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		{
		  if (xmlhttp.readyState==4 && xmlhttp.status==200){
				leaveRoomInterval = timerForLeaveRoom();
			}
		}
		xmlhttp.open("GET","leaveRoom.php",true);
		xmlhttp.send();*/
		
		$.mobile.changePage("getRooms.php",{ transition: "slide", reverse: true});
		leaveRoomInterval = timerForLeaveRoom();
	}
	
	function timerForLeaveRoom(){
		var leaveInterval = setInterval(function(){leaveRoom2()},500);
		return leaveInterval;
	}
	
	function leaveRoom2(){
		clearInterval(leaveRoomInterval);
		window.location.href = 'getRooms.php';
	}
	
		
	function leaveChat(){
		/*var xmlhttp;
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();
		  }else{
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		{
		  if (xmlhttp.readyState==4 && xmlhttp.status==200){
				leaveChatInterval = timerForLeaveChat();
			}
		}
		xmlhttp.open("GET","leaveRoom.php",true);
		xmlhttp.send();*/
		
		$.mobile.changePage("m.php",{ transition: "slide", reverse: false});
		leaveChatInterval = timerForLeaveChat();
	}
	
	function timerForLeaveChat(){
		var leaveInterval = setInterval(function(){leaveChat2()},500);
		return leaveInterval;
	}
	
	function leaveChat2(){
		clearInterval(leaveChatInterval);
		window.location.href = '/';
	}

	function bannedFromRoom(){
		if(window.location.href.indexOf("getRooms") > -1){
			alert("YOU ARE BANNED IN THIS ROOM");
		}else{
			window.location.href = 'getRooms.php';
		}
	}
	
	$("#popupPanel" ).on({
		popupbeforeposition: function() {
			var h = $( window ).height();

			$( "#popupPanel" ).css( "height", h );
		}
	});

	/*$('#text').blur(function(){
		console.log('blur');
		if ($(this).val()=='')
		$(this).val("Click here to type a message");
	});

	$('#text').focus(function(){
		console.log('focus');
		if ($(this).val()=='Click here to type a message')
		$(this).val("");
	});*/
	
	/*window.setInterval(function() {
		var elem = document.getElementById('data');
		elem.scrollTop = elem.scrollHeight;
	}, 5000);*/

// Timer for leaveRoom if the user is inactive for more that 20 seconds - if mobile device enters standby
	
	 var intTime = new Date().getTime();
	 var getTime = function() {
        var intNow = new Date().getTime();
        if (intNow - intTime > 20000) {
            //console.log("I JUST WOKE UP")
			leaveRoom();
        }
        intTime = intNow;
        setTimeout(getTime,2000);
    };
    getTime();	
</script>	

</body>
</html>
<? ob_flush(); ?>