<form action="<?php  echo $_SERVER['REQUEST_URI']; ?>" method="post">
<?php

	$user_ID = get_current_user_id();
    $user_email=get_the_author_meta('user_email', $user_ID );
    $user_names=get_the_author_meta('display_name',$user_ID);
?>
<div class="msg_other" >
<input type="button" value="Send Message Other" class="bnt_blue_mss" id="bnt_show_mess_other" />
<div class="box_msg_other">
<table>
<tr>
    <td><label for="tags_email">Email:</label></td>
    <td><input id="tags_email" style="width: 96%;" /></td>
</tr>
<tr>
    <td><label for="msg_mess_other">Message:</label></td>
    <td><textarea id="msg_mess_other"></textarea></td>
</tr>
<tr>
    <td>
        <input type="hidden" value="<?php echo $user_email;?>" id="txt_email_cur" />
        <input type="hidden" value="<?php echo $user_names;?>" id="txt_display_name" />
        <input type="hidden" value="<?php echo $user_ID;?>" id="txt_user_id" />
    </td>
    <td><input type="button" value="Send Message" class="bnt_blue_mss" id="bnt_send_mess" /></td>
</tr>
</table>

</div>
</div>

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
    
    //show mess isread=0
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
    echo 'Msg mail:'.$_POST['msg_rel'];
    }
?>
<div id="alert_txt">
    <?php echo $str_new_msg; ?>
</div>




<?php
	global $wpdb;


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
        if($post_id==0){ //mess other
            echo '<div class="msg_root" id="root'.$ia_1.'_">';
    		echo '<div class="msg_title_root" id="root'.$ia_1.'">'.$postobj->post_title.'</div>';
            echo '<div class="msg_body_root" >';
            
            $querypermess = "select * from message where author=$user_ID and isread=0 and post_id=$post_id group by user_id";
    		$resultspermess = $wpdb->get_results($querypermess);
    		foreach($resultspermess as $permess){
                $ia_2=$permess->mess_id;
                echo '<div class="msg_root" id="root2'.$ia_2.'_">';
        			echo '<div class="msg_title" id="root2'.$ia_2.'">Message Other</div>';
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
                            echo '<div id="'.$ia_3.'_rel_msg_alert" class="msg_alert"></div></br>';
                            echo '<i>'.$permessLevel2->email.'</i>';
                            echo '<div class="msg_reply bnt_blue_mss"  id="box_mail_'.$ia_3.'" >Reply</div>';
                                echo '<div class="msg_mail" id="box_mail_'.$ia_3.'_rel">';
                                echo '<textarea cols="50" rows="4"  id="'.$ia_3.'_rel_msg"></textarea><br/>';
                                echo '<input  id="'.$ia_3.'_rel" type="button" class="button_rel bnt_reply" value="Send Reply"/>';
                                echo '<input type="hidden" id="'.$ia_3.'_rel_user_id" value="'.$permessLevel2->author.'" />';
                                echo '<input type="hidden" id="'.$ia_3.'_rel_post_id" value="'.$permessLevel2->post_id.'" />';
                                echo '<input type="hidden" id="'.$ia_3.'_rel_user_email" value="'.$user_email.'" />';
                                echo '<input type="hidden" id="'.$ia_3.'_rel_user_name" value="'.$user_names.'" />';
                                echo '<input type="hidden" id="'.$ia_3.'_rel_author" value="'.$permessLevel2->user_id.'" />';
                                echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    echo '</div>';
                echo '</div>';
                
    		}
            echo '</div>';
            echo '</div>';
        }else{ //mess not other
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
                            echo '<div id="'.$ia_3.'_rel_msg_alert" class="msg_alert"></div></br>';
                            echo '<i>'.$permessLevel2->email.'</i>';
                            echo '<div class="msg_reply bnt_reply"  id="box_mail_'.$ia_3.'" >Reply</div>';
                                echo '<div class="msg_mail" id="box_mail_'.$ia_3.'_rel">';
                                echo '<textarea cols="50" rows="4"  id="'.$ia_3.'_rel_msg"></textarea><br/>';
                                echo '<input  id="'.$ia_3.'_rel" type="button" class="button_rel bnt_blue_mss" value="Send Reply"/>';
                                echo '<input type="hidden" id="'.$ia_3.'_rel_user_id" value="'.$permessLevel2->author.'" />';
                                echo '<input type="hidden" id="'.$ia_3.'_rel_post_id" value="'.$permessLevel2->post_id.'" />';
                                echo '<input type="hidden" id="'.$ia_3.'_rel_user_email" value="'.$user_email.'" />';
                                echo '<input type="hidden" id="'.$ia_3.'_rel_user_name" value="'.$user_names.'" />';
                                echo '<input type="hidden" id="'.$ia_3.'_rel_author" value="'.$permessLevel2->user_id.'" />';
                                echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    echo '</div>';
                echo '</div>';
                
    		}
            echo '</div>';
            echo '</div>';        
            
        }
	}

?>


 <input id="mess_id" name="mess_id" value="" type="hidden" />
 
</form>


</div><!-- end main content -->


  <script>
  $(function() {
    
    //set open/hide hide
    $(".msg_root .msg_body").hide();
    $(".msg_root .msg_body_root").hide();
    $(".msg_root .msg_mail").hide();
    
    //open/hide box level1
    $(".msg_title").click(function(){
            $("#"+this.id+"_ .msg_body").toggle(500);
    });
    
    //open/hide box level2
    $(".msg_title_2").click(function(){
            $("#"+this.id+"_2 .msg_body_2").toggle(500);
    });
    
    //open/hide box roots
    $(".msg_title_root").click(function(){
            $("#"+this.id+"_ .msg_body_root").toggle(500);
    });
    
    
    //show/hide box mess reply
     $(".msg_root .msg_reply").click(function(){
           $("#"+this.id+"_rel").toggle(500);
    });   
    
    //click button reply
    $(".msg_root input").click(function(){
           var idss=this.id;
           var msg=$("#"+this.id+"_msg").val();
           var str_user_id=$("#"+this.id+"_user_id").val();
           var str_post_id=$("#"+this.id+"_post_id").val();
           var str_user_email=$("#"+this.id+"_user_email").val();
           var str_user_name=$("#"+this.id+"_user_name").val();
           var str_author=$("#"+this.id+"_author").val();
           
           var str_data="user_id="+str_user_id+"&post_id="+str_post_id+"&message="+msg+"&email="+str_user_email+"&names="+str_user_name+"&authors="+str_author;
           
           // prompt('thien thanh',str_data);
           
           if(msg.length==0){
            alert("Reply not null and greater than 5 characters!!!");
            return;
           }
                $.ajax({
                              type:"get",
                              url:"/jobs/insert-mymessage/?"+str_data,
                              success:function(data){
                                $("#"+idss+"_msg_alert").html("Reply Success!!!");
                                $("#"+idss+"_msg").val('');
                                $("#"+idss+"_msg_alert").fadeIn(1000);
                                $("#"+idss+"_msg_alert").fadeOut(5000);
                              }
                });    
    }); 
    
    $( "#tags_email" ).autocomplete({
      source: '/jobs/list-Auto-Complete.php'
    });
    
    
    //box send mess other
    $(".box_msg_other").hide();
    
    $("#bnt_show_mess_other").click(function(){
        $(".box_msg_other").toggle(500);
    });
    
    
    $("#bnt_send_mess").click(function(){
        var txt_email=$("#tags_email").val();
        var txt_msg_other=$("#msg_mess_other").val();
        var txt_email_cur=$("#txt_email_cur").val();
        var txt_display_name=$("#txt_display_name").val();
        var txt_user_id=$("#txt_user_id").val();
        
        if((txt_email.length==0)){
            alert("Email and Message not null");
            return;
        }
        
                
        if((txt_msg_other.length==0)){
            alert("Email and Message not null");
            return;
        }
                $.ajax({
                              type:"get",
                              url:"/jobs/send-mess.php/?user_email="+txt_email+"&mess="+txt_msg_other+"&user_email_cur="+txt_email_cur+"&d_name="+txt_display_name+"&user_id_cur="+txt_user_id,
                              success:function(data){
                                alert(data);
                                $("#msg_mess_other").val('');
                              }
                });
    });
    //----------end send mess-----------
    
    $("#alert_txt").hide();
     
    $("#alert_icon").click(function(){
            $("#alert_txt").toggle(500);
    }); 
    
    $(".alert_msg").click(function(){
            $("#mess_id").val(this.id);
    });
    
    
    
    
      
  });
  </script>
  
  