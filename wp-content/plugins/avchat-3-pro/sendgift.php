
<?php
//By default AVChat 3 will send to this PHP file the following variables
$_GET["destinationSiteId"];
$_GET["destinationUsername"];
$_GET["senderSiteId"];
$_GET["senderUsername"];
//like this:sendgift.php?destinationSiteId=abc&destinationUsername=Nike&senderSiteId=123senderUsername=Nike
?>
<html>
<head>
<title>Send a gift to 
<?echo $_GET["destinationUsername"]?>
</title>
<style type="text/css">
body
{
background-color:#ffffff;
	font-family:Arial,Verdana,"Times New Roman",Georgia,Serif;
}

#maincontainer{
	border: 1px dashed;
	padding:5%;
	margin:5%;
	width:80%;
	height:70%
}
</style>
</head>
<body >
<div id="maincontainer">
<p><strong><?php echo $_GET["senderUsername"]?>, send a gift to <?php echo $_GET["destinationUsername"]?>!</strong></p>
<p>Use the Gifts API to plug into your existing gifts system!</p>
<img src="gift.png" width="80" height="80">
<p><input type="submit" value="..,,::::>>>>SEND<<<<::::,,.."></p>
</div>

</body>
</html>