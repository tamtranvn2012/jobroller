<form action="<?php  echo $_SERVER['REQUEST_URI']; ?>" method="post">
<?php
   
	$user_ID = get_current_user_id();
    if($_POST){
    $mess_id=intval($_POST['mess_id']);
    $queryUpdate = "update message set isread=1 where mess_id=$mess_id and author=$user_ID";
    $resulSel =$wpdb->get_results($queryUpdate);
    }
    
	$querycount = "select count(mess_id) as total from message where author=$user_ID and isread=0";
	$resultcount = $wpdb->get_results($querycount);
    
    echo '<input id="show_a" value="1" type="hidden"/>';	
    
    //hi?n các mess chua d?c
    $querySel = "select * from message where author=$user_ID and isread=0";
    $resulSel =$wpdb->get_results($querySel);
    $str_new_msg='';
    
    foreach($resulSel as $perresult){
     $str_new_msg.='<input type="submit" id="'.$perresult->mess_id.'" class="alert_msg" value="'.$perresult->message.'" /><br/>';
    }
?>
<div class="icon_alert" id="alert_icon" title="Notifications">
    <?php 
	if($resultcount[0]->total>0){ 
		echo '<div class="number">'.$resultcount[0]->total.'</div>';
	}
	?>
    <img src="<?php echo get_stylesheet_directory_uri().'/images/alert.png';?>" />
</div>


<?php
    if($_POST){
    $mess_id=intval($_POST['mess_id']);
    echo 'mess id:'.$post_id.'<br/>';
    $querySel = "select * from message where mess_id=$mess_id and author=$user_ID";
    $resulSel =$wpdb->get_results($querySel);
    echo 'msg:'.$resulSel[0]->message;
    
    }

?>
<div id="alert_txt">
    <?php echo $str_new_msg; ?>
</div>


<?php
	global $wpdb;

	$user_ID = get_current_user_id();
	$querycount = "select count(mess_id) as total from message where author=$user_ID and isread=0";
	$resultcount = $wpdb->get_results($querycount);			
	$newmessage = '<div class="totalunreadmess">You have '.$resultcount[0]->total.' unread messages</div>';
	echo $newmessage;
	$query = "select post_id from message where author=$user_ID  and isread=0 group by post_id";
	$results = $wpdb->get_results($query);
    

    $ia_1;$ia_2;$ia_3;
	foreach($results as $perresult){
		$post_id = intval($perresult->post_id);
		$postobj = get_post($post_id); 
        $ia_1=$post_id; 
        echo '<div class="msg_root" id="root'.$ia_1.'_">';
		echo '<div class="msg_title_root" id="root'.$ia_1.'">'.$postobj->post_title.'</div>';
        echo '<div class="msg_body_root" >';
        
        $querypermess = "select * from message where author=$user_ID and isread=0 and post_id=$post_id group by user_id";
		$resultspermess = $wpdb->get_results($querypermess);
		foreach($resultspermess as $permess){
            $ia_2=$permess->mess_id;
            echo '<div class="msg_root" id="root2'.$ia_2.'_">';
    			echo '<div class="msg_title" id="root2'.$ia_2.'">'.$permess->message.'</div>';
                echo '<div class="msg_body">';
                    //nhom user id
                    $queryLevel2_post_id=$permess->post_id;
                    $queryLevel2_user_id=$permess->user_id;
                    $queryLevel2= "select * from message where isread=0 and post_id=$queryLevel2_post_id and  user_id=$queryLevel2_user_id";
                    $resulLevel2 = $wpdb->get_results($queryLevel2);
                    
                    foreach($resulLevel2 as $permessLevel2){
                        $ia_3=$permessLevel2->mess_id;
                        echo '<div class="msg_root" id="root3'.$ia_3.'_2">';
                        echo '<div class="msg_title_2" id="root3'.$ia_3.'">'.$permessLevel2->name.'</div>';
                        echo '<div class="msg_body_2">';
                        echo $permessLevel2->message.'<br/><br/>';
                        echo '<i>'.$permessLevel2->email.'</i>';
                        echo '</div>';
                        echo '</div>';
                    }
                echo '</div>';
            echo '</div>';
            
		}
        echo '</div>';
        echo '</div>';


	}

?>


 <input id="mess_id" name="mess_id" value="" type="hidden" />
</form>


</div><!-- end main content -->


  <script>
  $(function() {
    
    
    $(".msg_root .msg_body").hide();
    $(".msg_root .msg_body_root").hide();
    
    $(".msg_title").click(function(){
            $("#"+this.id+"_ .msg_body").toggle(500);
    });
    
        $(".msg_title_2").click(function(){
            $("#"+this.id+"_2 .msg_body_2").toggle(500);
    });
    
    
    $(".msg_title_root").click(function(){
            $("#"+this.id+"_ .msg_body_root").toggle(500);
    });
    
    
    
    
    $("#alert_txt").hide();
     
    $("#alert_icon").click(function(){
            $("#alert_txt").toggle(500);
    }); 
    
    $(".alert_msg").click(function(){
            $("#mess_id").val(this.id);
    });
      
  });
  </script>
  
  