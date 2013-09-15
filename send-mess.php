<?php
include "wp-load.php";
$user_email=$_GET['user_email'];
$mess=$_GET['mess'];
$user_email_cur=$_GET['user_email_cur'];
$d_name=$_GET['d_name'];
$created=time();
$post_ids=0;
$user_id=$_GET['user_id_cur'];

$querySel = "select * from wp_users where user_email='$user_email'";
$rsd = mysql_query($querySel);
while($rs = mysql_fetch_array($rsd)) {
    $cname = $rs['display_name'];
    $authors=$rs['ID'];
}


$queryInsert = "INSERT INTO message(user_id,post_id, created, message,email,name,isread,author) VALUES('$user_id','$post_ids','$created','$mess','$user_email_cur','$d_name','0','$authors')";
    
$wpdb->get_results($queryInsert);
    
echo 'Send message to '.$cname.' success!!!';
?>