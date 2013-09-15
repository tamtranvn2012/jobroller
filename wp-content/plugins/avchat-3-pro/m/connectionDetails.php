<?php

	$doNotEcho = true;
	chdir('../');
	include("avc_settings.php");
	
	$revision = "2768";
	
	if(isset($avconfig['connectionstring']) && $avconfig['connectionstring'] != ''){
	
	$domain = parse_url($avconfig['connectionstring'], PHP_URL_HOST);
	$siteUrl = explode('/', $avconfig['connectionstring']);
	$appName = $siteUrl[3]."/".$siteUrl[4];
	
	//echo $domain.' '.$appName;
	
		define("HOST", $domain);
		define("APPNAME", $appName);
		define("PORT", "1935");
	}else{
		die ("No connection string");
	}
	
	
?>
