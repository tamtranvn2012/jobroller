<?php
  

    $user_id=$_GET['user_id'];
    $post_ids=$_GET['post_id'];
    
    $created=time();
    $messages=$_GET['message'];
    $emails=$_GET['email'];
    $names=$_GET['names'];
    $is_Read=0;
    $authors=$_GET['authors'];
    
    $queryInsert = "INSERT INTO message(user_id,post_id, created, message,email,name,isread,author) VALUES('$user_id','$post_ids','$created','$messages','$emails','$names','$is_Read','$authors')";
    
    $wpdb->get_results($queryInsert);

    echo 'user:'.$user_id.' mess'.$messages;
 
?>
</div>