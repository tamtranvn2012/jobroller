<?php
$type = 'image/'.$_GET['type'];
$url = urldecode($_GET['url']);
$file = file_get_contents($url);

header('Content-Type:'.$type);

echo $file;
?>