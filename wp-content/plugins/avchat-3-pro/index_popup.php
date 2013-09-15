<?php if(session_id() == ""){session_start();}?>
<html>
<head>
<title>Video Chat</title>
</head>
<body style="padding:0px;margin:0px">
<?php
$movie_param = "index.swf";
if(isset($_GET['movie_param'])){
	$movie_param = $_GET['movie_param'];
}
?>
<input type="hidden" name="FB_appId" id="FB_appId" value="<?=$_GET['FB_appId']?>" />
<script type="text/javascript" src="tinycon.min.js"></script>
<script type="text/javascript" src="facebook_integration.js"></script>
<script type="text/javascript" src="swfobject.js"></script>
<script type="text/javascript">
//document.getElementById("av_message").innerHTML = "You do not have the proper Flash Player installed. Click below to download the newest version of Flash Player: <p><a href=\"http://www.adobe.com/go/getflashplayer\"><img src=\"http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif\" alt=\"Get Adobe Flash player\" /></a></p>";
var flashvars = {
	lstext : "Loading Settings...",
	sscode : "php",
	userId : ""
};
var params = {
	quality : "high",
	bgcolor : "#272727",
	play : "true",
	loop : "false",
	allowFullScreen : "true"
};
var attributes = {
	name : "index_embed",
	id :   "index_embed",
	align : "middle"
};
var embed = "embed";
swfobject.embedSWF("<?=$movie_param;?>", "myContent", "100%", "600", "11.1.0", "", flashvars, params, attributes);
</script>
<script type="text/javascript" src="new_message.js"></script>
<div id="myContent">
	<div id="av_message" style="color:#ff0000"><p>You need to have JavaScript enabled and <a target="_blank" href="http://get2.adobe.com/flashplayer/">the latest version of Flash Player</a> for the chat to work.</p></div>
</div>
</body>
</html>