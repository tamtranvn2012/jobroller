<?php

error_reporting(E_ERROR);


header("Connection: close\r\n");
header("Content-Encoding: none\r\n");


//this file is called by the media server (FMIS,Red5, Wowza) when a user connects to the media server

//this file is called only when token authentication is turned on in the avchat30 app config file on the media server

//a token var is sent via GET



//ip: String or Boolean(false)

//description: The IP of the FMS/Red5/Woowza server making calls to this script. This is a security feature so that this script is only called by your media server. 

//values: any ip as string:"127.0.0.1" OR false for disabled

//default: false

$ip = false;



//storage: String

//description: path from where to get the tokens, must be the same as in token_request.php

//values: any path to a existing or non existing folder

//default: tokens

$storage = 'tokens';



//cache_life: Number

//description: how many seconds after creation a token is considered valid

//values: any number in seconds

//default: 30

$cache_life = 30;



//if the ip var is set and the value is different than the ip of the caller we don't eecute anything

if($ip && ($_SERVER["REMOTE_ADDR"] && $ip != $_SERVER["REMOTE_ADDR"]) ){

	die();

}



if(isset($_GET["token"])){

	//if the token var is sent via GET

	

	//get the token from the get var

	$token = filter_input(INPUT_GET, "token",FILTER_SANITIZE_STRING);

	

	if(!is_file($storage."/".$token)){

		//if the token file does not exist

		 if(isset($_GET["fms"])){

		 	die("&res=false");

		 }else{

		 	die("false");

		 }

	}else {

		//if the file exists

		

		$ourFileHandle = fopen($storage."/".$token, 'rb') or die("false");

		

		//we read the cration time for the file

		$ttime = fread($ourFileHandle, filesize($storage."/".$token));

		

		if(time() - $ttime >= $cache_life){
		
			if(strpos($token,"_m")){
				fclose($ourFileHandle);
				if(isset($_GET["fms"])){ 
					die("&res=true");
					}else{
						die("true");
					}
			}

			//if this token is expired

			 fclose($ourFileHandle);

			 //we delete the token
			if(strpos($token,"_m") == false){
				unlink($storage."/".$token);
			}
			 
			 //and report the failiure to verify this token

			 if(isset($_GET["fms"])){

			 	die("&res=false");

			 }else{

			 	die("false");

			 }

		}else{

			//if this token is NOT expired

			fclose($ourFileHandle);

			if(isset($_GET["fms"])){ 

			 	die("&res=true");

			}else{

			 	die("true");

			}

		}

	}

}

if(isset($_GET["fms"])){

	die("&res=false");

}else{

	die("false");

}

?>