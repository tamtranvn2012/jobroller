<?php
//this file is called by index.swf and admin.swf when the user pressses the [Connect] button, the username var is sent via POST
//storage: String
//description: path from where to get the tokens, must be the same as in token_request.php
//values: any path to a existing or non existing folder
//default: tokens
$storage = 'tokens';

//make the folder where to put the tokens
if(!file_exists($storage)){
	mkdir($storage);
}

//we start the session to get a session idlater on
session_start();
//$_POST["username"] = "aa";
//if the username is sent via POST
if(isset($_POST["username"])){

	//we create a string that we consider the token
	$token = md5(session_id().$_POST["username"]);
	
	if(isset($mobileClient) && $mobileClient == true){
		$token = $token."_m";
	}
	
	//we store the token for the being able to accest it from the mobile client
	$_SESSION["theToken"] = $token;
	/*if(empty($_SESSION["theToken"])){
		$_SESSION["theToken"] = $token;
	}*/
	
	//we write the token to disk
	if ($ourFileHandle = fopen($storage."/".$token, 'w')){
		@fwrite($ourFileHandle, time());
		fclose($ourFileHandle);
		echo "token=".$token."&writesuccess=true";
	}else{
		echo "token=".$token."&writesuccess=false";
	}
	
	//we create a file called leanup_handle to help us decide when to clean up the OLD tokens
	if(!file_exists($storage."/cleanup_handle")){
		$cleanFileHandle = fopen($storage."/cleanup_handle", 'w') ;
		fclose($cleanFileHandle);
	}
	
	//if the cleanup handle is older than 300 seconds we start cleaning the tokens
	if(file_exists($storage."/cleanup_handle") && (filemtime($storage."/cleanup_handle") < (time() - 300 ))){	 
		$handle =  opendir($storage);
		try{
			while($filename = readdir($handle)){
				//we go trough the tokens in the token folder and remove any token older than 300 seconds
				if(is_file($storage."/".$filename) && (filemtime($storage."/".$filename) < (time() - 300 ))){
					if(strpos($filename,"_m") == false){
						unlink($storage."/".$filename);
						//echo "<br/>".$filename;	
					}
				}
			}
		}catch (Exception $e) {
		
		}
		closedir($handle);
	 }
}
?>