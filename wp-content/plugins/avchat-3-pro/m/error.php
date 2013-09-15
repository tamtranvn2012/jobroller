<?php
	//if(!defined('ERROR')){die('Direct access not permitted');}
	//echo 'you are here';
	
	echo '<html>
		  <head>
			  <link rel="stylesheet" href="css/userPanel.css" />
			  <link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.css" />
			  <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
			  <script src="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.js"></script>
			  
			  <title>Disconnected</title>
		  </head>
		   <body>';
	
	if(isset($_GET["invalidLicense"])){
		echo'<h4>Connection has been rejected. The cause may be:</h4>
			<ul>
				 <li>You are trying to connect from an unlicensed domain.</li>
				 <li>You have been banned by the chat administrator.</li>
				 <li>The username you are trying to connect with is already taken.</li>
			</ul>';	 
		echo'<a href="m.php" data-role="button" data-inline="true" data-theme="b" data-icon="arrow-l" data-transition="slide" data-direction="reverse">Go To Login Page</a>';
	}else if(isset($_GET["bannedStatus"])){
		echo'<h4>Connection rejected. You have been banned by the chat administrator</h4>';	
		echo'<a href="m.php" data-role="button" data-inline="true" data-theme="b" data-icon="arrow-l" data-transition="slide" data-direction="reverse" target="_self">Go To Login Page</a>';
	}else if(isset($_GET["kickedStatus"])){
		echo'<h4>You have been kicked by the chat administrator</h4>';
		echo'<a href="getRooms.php" data-role="button" data-inline="true" data-theme="b" data-icon="arrow-l" data-transition="slide" data-direction="reverse" target="_self" >Go To Login Page</a>';
	}else{
		echo'<h4>Connection lost, the server might be down or you may have lost connection to the internet.</h4>';
		echo'<a href="m.php" data-role="button" data-inline="true" data-theme="b" data-icon="arrow-l" data-transition="slide" data-direction="reverse">Go To Login Page</a>';
	}
			  
	echo'  <script>
			var goToIndexInterval;
			
			function refreshTimer(){
				goToIndexInterval = setInterval(function(){goToIndex()},1000);
			}
			
			function goToIndex(){
				clearInterval(goToIndexInterval);
				window.location.href = "m.php";
			}
			
		   </script>
		   </body>
		  </html>';
?>