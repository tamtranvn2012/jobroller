<?php

$arr=array();
include "wp-load.php";

$querySel = "select * from wp_users";
$rsd = mysql_query($querySel);

while($rs = mysql_fetch_array($rsd)) {
    $cname = $rs['user_email'];
    array_push($arr,$cname);
}

echo json_encode($arr);

?>