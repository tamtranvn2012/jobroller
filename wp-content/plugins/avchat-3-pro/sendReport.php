<?php
error_reporting(E_ERROR | E_PARSE);

$ip = urldecode($_POST["ip"]);// the IP of the reported user
$siteId = urldecode($_POST["siteId"]);//the siteId of the reported user
$username = urldecode($_POST["username"]);//the username of the reported user
$reason = urldecode($_POST["reason"]);// the reason of the report
$description = urldecode($_POST["description"]);//additional description for the report (could be empty because it is optional)
$roomId = urldecode($_POST["roomId"]);//the id of the room from where the report was sent
$roomName = urldecode($_POST["roomName"]);// the name of the room from where the report was sent
$reporter = urldecode($_POST["reporter"]);// the name of the user who made the report
$webCamSnapURL = urldecode($_POST["webCamSnapURL"]);// the link to the web-cam snapshot of the reported user taken when the report was sent
$textChatSnapURL = urldecode($_POST["textChatSnapURL"]);// the link to the text-chat snapshot of the reported user taken when the report was sent

//Example of mail sending
/*
$to      = "webmaster@example.com";
$subject = "User ".$username." with the ID ".$siteId." has been reported";

$message .= "The following user has been reported \n";
$message .= "Username: ".$username."\n";
$message .= "IP: ".$ip."\n";
$message .= "siteId: ".$siteId."\n";
$message .= "\n";
$message .= "You can view the report screenshots here:\n";
$message .= "Camera snapshot: ".$webCamSnapURL."\n";
$message .= "Text-chat snapshot: ".$textChatSnapURL."\n";
$message .= "\n";
$message .= "You can also view a list of reported users at:\n";
$message .= "AVChat admin area -> Reports panel.";

$headers = "From: webmaster@example.com" . "\r\n" .
    "Reply-To: webmaster@example.com" . "\r\n" .
    "X-Mailer: PHP/" . phpversion();
	

mail($to, $subject, $message, $headers);
*/
$result ="&result=".$ip." ".$siteId." ".$username." ".$reason." ".$description." ".$roomId." ".$roomName." ".$reporter." ".$webCamSnapURL." ".$textChatSnapURL;
echo $result;



?>