<?php
error_reporting(E_ALL);

//this file is called by index.swf when a report is made
$siteId  = $_GET["siteId"];//user's internal specified ID
$type = $_GET["type"];//snapshot type (text-chat snapshot or camera snapshot);

//[+]!!!!!!!!!!!! do not modify

if(!file_exists("report_snaps")){
		mkdir("report_snaps",0777);
	}

$image = fopen("report_snaps/".$siteId."_".$type,"wb");
if ($image){
	if (fwrite($image , file_get_contents("php://input"))){
		fclose($image);
		echo "save=ok";
	}else{
		echo "save=failed";
	}
}else{
	echo "save=failed";
}
//[-]!!!!!!!!!!!! end do not modify

//echo "save=ok"; //to tell the swf saving the snapshot succeeded
//echo "save=failed";// to tell the swf saving the snapshot failed
?>