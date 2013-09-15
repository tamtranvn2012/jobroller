<?php
header("Content-type: text/css");
$cssFile = simplexml_load_file("style.xml");

foreach ($cssFile->children() as $property) {
	
	$values=array();
	foreach ($property->children() as $tag) {
	   	$values[$tag->getName()] = $tag;
	}
	echo '.'.$property->getName()."{"."\n";
	foreach ($values AS $tag) {
		echo "\t".$tag->getName().":".$tag.";"."\n";
	}
	echo "} \n";
}


?>
