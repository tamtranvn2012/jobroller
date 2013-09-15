<?php
/**
 * Veteranlogix.com  functions file
 *
 */
 
 add_action("adding_notes_box","notes_box_function",10,1);

function notes_box_function($post_id) {
	global $current_user;
	$user_id	=	$current_user->ID;
	$postid		=	$post_id;
	if(isset($_POST['post_id']) && !empty($_POST['post_id'])) {
		
		$notes_value 	=	$_POST["job_notes"];
		
		if(isset($_POST['add_notes']) && !empty($_POST['add_notes'])) {
		
			update_post_meta($postid,$postid."_".$user_id,$notes_value );
		
		}
		
		if(isset($_POST['modify_notes']) && !empty($_POST['modify_notes'])) {
		
			update_post_meta($postid,$postid."_".$user_id,$notes_value );
		
		}
		
		
		if(isset($_POST['delete_notes']) && !empty($_POST['delete_notes'])) {
		
			delete_post_meta($postid,$postid."_".$user_id);
		
		}
	
	}
	?>
	<div class="section_content">
		<h2><?php _e('Job Notes',APP_TD) ?></h2>
		<div style="text-align:justify"> <?php echo get_post_meta($postid,$postid."_".$user_id,true); ?></div>
		<style>
			#notes_add, #notes_modify,#notes_delete {
				background: url("<?php echo get_stylesheet_directory_uri().'/images/style-pro-blue/form.png'; ?>") repeat scroll 0 0 transparent;
			}
			#add_link,#modify_link,#del_link { text-decoration:none !important; font-size:18px; font-weight:bold;}
			#buttons{margin:15px 0px;}
		</style>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery("#apply_form").css("display","none");
				jQuery("#section_footer").css("display","block");
				jQuery("#add_link").click(function () {
					jQuery("#notes_add").toggle();
					jQuery("#notes_modify").css("display","none");
					jQuery("#notes_delete").css("display","none");
				});
				jQuery("#modify_link").click(function () {
					jQuery("#notes_add").css("display","none");
					jQuery("#notes_modify").toggle();
					jQuery("#notes_delete").css("display","none");
				});
				jQuery("#del_link").click(function () {
					jQuery("#notes_add").css("display","none");
					jQuery("#notes_modify").css("display","none");
					jQuery("#notes_delete").toggle();;
				});
			});
		</script>
		<div id="buttons">
			<?php if(get_post_meta($postid,$postid."_".$user_id,true) == '') { ?>
			<a href="#notes_add" id="add_link">Add Notes</a>
			<?php } ?>
			<?php if(get_post_meta($postid,$postid."_".$user_id,true) != '') { ?>
			<a href="#notes_modify" id="modify_link">Modify Notes</a>
			<a href="#notes_delete" id="del_link"> | Delete Notes</a>
			<?php } ?>
		</div>
		<div id="notes_add"  style="display:none;">
			<form style="padding: 16px 40px; height:250px;" class="main_form" name="notes_form" method="post" action="">
				<textarea name="job_notes" style="width:561px; height:183px;"><?php echo get_post_meta($postid,$postid."_".$user_id,true); ?></textarea>
				<input type="hidden" value="<?php echo $postid; ?>" name="post_id"><br />
				<input type="submit" style="float:right;" class="submit" name="add_notes"  value="Add Notes">
			</form>
		</div>
		<div id="notes_modify" style="display:none;">
			<form style="padding: 16px 40px; height:250px;" class="main_form" name="notes_form" method="post" action="">
				<textarea name="job_notes" style="width:561px; height:183px;"><?php echo get_post_meta($postid,$postid."_".$user_id,true); ?></textarea>
				<input type="hidden" value="<?php echo $postid; ?>" name="post_id"><br />
				<input type="submit" style="float:right;" class="submit" name="modify_notes"  value="Modify Notes">
			</form>
		</div>
		<div id="notes_delete" style="display:none;">
			<form style="padding: 16px 40px; height:280px;" class="main_form" name="notes_form" method="post" action="">
				<textarea name="job_notes" style="width:561px; height:183px;" ><?php echo get_post_meta($postid,$postid."_".$user_id,true); ?></textarea>
				<input type="hidden" value="<?php echo $postid; ?>" name="post_id">
				<p style="color:red;" >Are you sure you want to delete Job Notes ?</p>
				<input type="submit" style="float:right;" class="submit" name="delete_notes"  value="Delete Notes">
			</form>
		</div>
	</div>
	
	<?php
}

