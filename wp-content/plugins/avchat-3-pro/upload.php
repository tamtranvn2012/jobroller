<?php
if(!empty($_FILES)){
	//set error reporting so that no warnings are returned to the swf
	error_reporting(E_ERROR | E_PARSE);

	//allowed extensions
	
	$extensions = array();
	$fileTypesFile = simplexml_load_file("file_types.xml");
	foreach($fileTypesFile->children() as $child) {
		array_push($extensions, (string)$child[0]);
	}
	
	//$extensions = array("tiff","ico","swf","jpg", "jpeg", "bmp", "pdf","psd","flv", "png", "gif", "mp3", "aac","avi", "mp4","txt","log","xml","wav","doc","docx","rar","zip","7z","xls","doc","docx","ppt","pps");

	//helper function
	function EndsWith($FullStr, $EndStr)
	{
		// Get the length of the end string
		$StrLen = strlen($EndStr);
		// Look at the end of FullStr for the substring the size of EndStr
		$FullStrEnd = strtolower(substr($FullStr, strlen($FullStr) - $StrLen));
		//$FullStrEnd = substr($FullStr, strlen($FullStr) - $StrLen);
		// If it matches, it does end with EndStr
		return $FullStrEnd == $EndStr;
	}

	//some variables are sent using GET:
	$uploaderUsername=utf8_decode($_GET["uploaderUsername"]);
	$uploaderSiteId=$_GET["uploaderSiteId"];
	$destinationType=$_GET["destinationType"]; //"user" when the file is sent to a user, "room" when the file is sent to a room
	$destUID=$_GET["destUID"]; //the id of the user to which this file is sent
	$destRID=$_GET["destRID"]; //the id of the room to which this file is sent OR the id of the room the user to which this file is destined is in
	$destUSN=$_GET["destUSN"]; //the username of the user to which this file is sent or the name of the room to which the file is sent
	$SWFFolderURL=$_GET["pathToSWFFolder"]; //the url to the folder that contains the swf as detected by the swf, without the http:// OR https:// part

	//path to where to put the file
	$storage = 'uploadedFiles';

	//make the folder where to put the file
	if(!file_exists($storage)){
		mkdir($storage);
	}

	//try chomding the folders
	//...

	//full URL for uploads folder
	$UploadFolderURL="http://".$SWFFolderURL."/".$storage;


	//relative path TO THE UPLOADED FILE
	$uploadfile = $storage."/" .$uploaderUsername."_". basename( $_FILES['Filedata']['name'] );

	//index.swf and admin.swf are strict about the values it expects back from upload.php so do not modify the output in the following lines
	
	//we check for strange/fishy extensions not allowed in the above list 
	$allowed = false;
	foreach ($extensions as $i => $extension) {
		if (EndsWith($_FILES['Filedata']['name'],$extension)){
			$allowed=true;
			break;
		}
	}

	if (!$allowed){
		//we tell the swf this extension is not allowed
		echo( '?result=notallowed');
	}else {
		if ((!file_exists($uploadfile) || (file_exists($uploadfile) AND filesize($uploadfile) == filesize($_FILES["Filedata"]["tmp_name"]))) ) {
			//if the file does not exists or if it exists and its the same file
			if (move_uploaded_file( $_FILES['Filedata']['tmp_name'] , $uploadfile ) ) {
				//if the file is moved successfully we return a success message
				echo( '?result=success&fileurl='. $UploadFolderURL."/".$uploaderUsername."_".$_FILES['Filedata']['name']);
			}else{
				//file failed to move
				echo( '?result=fail');
			}
		}else{
			//if a file with the same name exists, we rename the one that is being uploaded
			$uploadfile = $storage."/" .$uploaderUsername."_". basename( $_FILES["Filedata"]["name"] );
			$path_parts = pathinfo($uploadfile);
			$i = 0;
			do { 
				$i++; 
			}while (file_exists($storage."/".$path_parts["filename"]." (".$i.")".".".$path_parts["extension"]) 
					AND 
					filesize($storage."/".$path_parts["filename"]." (".$i.")".".".$path_parts["extension"]) != filesize($_FILES["Filedata"]["tmp_name"]) );
			
			$uploadfile = $storage."/".$path_parts["filename"]." (".$i.")".".".$path_parts["extension"];
			
			if(move_uploaded_file( $_FILES["Filedata"]["tmp_name"] , $uploadfile)){
				echo( '?result=success&fileurl='. $UploadFolderURL."/".$path_parts["filename"]." (".$i.")".".".$path_parts["extension"]);
			}else{
				//file failed to move
				echo( '?result=fail');
			}
		}
	}
}
?>