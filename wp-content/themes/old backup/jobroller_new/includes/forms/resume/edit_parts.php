<?php
add_action('jr_resume_footer', 'jr_edit_resume_parts');

function jr_edit_resume_parts($post) {
	$resume = $post->ID;
	?>
	<div style="display:none">
		
		<form id="websites" action="<?php echo get_permalink($resume); ?>" class="submit_form main_form modal_form" method="post">
			<h2><?php _e('Add Website', APP_TD); ?></h2>
			<p><?php _e('Add a website below to add it to your resume e.g. your portfolio or a Twitter account.', APP_TD); ?></p>
			
			<p><label for="website_name"><?php _e('Website Name', APP_TD); ?></label> <input type="text" class="text" name="website_name" id="website_name" /></p>
			<p><label for="website_url"><?php _e('Website URL', APP_TD); ?></label> <input type="text" class="text" name="website_url" id="website_url" /></p>
			
			<p><input type="submit" class="submit" name="save_website" value="<?php _e('Add', APP_TD); ?>" /></p>
		</form>
		
		<script type="text/javascript">
		/* <![CDATA[ */
			
			// Validation
			jQuery('input[name=save_website]').click(function(){
				var web_name = jQuery('#website_name').val();
				var web_url = jQuery('#website_url').val();
				jQuery('#fancybox-content form ul.errors').remove();
				if (!web_name || !web_url) {
					jQuery('#fancybox-content form h2').after('<ul class="errors"><li><?php _e('Name and URL are required fields.', APP_TD); ?></li></ul>');
					return false;
				}
			});
				
		/* ]]> */
		</script>
		
	</div>
	<?php
}

add_action('jr_resume_header', 'jr_process_resume_parts');

function jr_process_resume_parts() {
	
	global $post, $message;
	
	if (get_the_author_meta('ID')!=get_current_user_id()) return;
	
	$resume = $post->ID;
	
	if (isset($_POST['save_website'])) :
		
		$websites = get_post_meta($resume, '_resume_websites', true);
		if (!is_array($websites)) $websites = array();
		
		$website_name = $_POST['website_name'];
		$website_url = $_POST['website_url'];
		
		if ($website_name && $website_url) :
			$websites[] = array( 'name' => $website_name, 'url' => $website_url );
			sort($websites);
			update_post_meta($resume, '_resume_websites', $websites);
		endif;
		
		$message = __('Website Added', APP_TD);
		
	elseif (isset($_GET['delete_website']) && is_numeric($_GET['delete_website'])) :
	
		$site_index = $_GET['delete_website'];
		
		$websites = get_post_meta($resume, '_resume_websites', true);
		if (!is_array($websites)) $websites = array();
		$new_websites = array();
		
		$loop = 0;
		foreach($websites as $website) :
			if ($site_index!=$loop) $new_websites[] = $website;
			$loop++;
		endforeach;
		
		update_post_meta($resume, '_resume_websites', $new_websites);
		
		$message = __('Website successfully deleted', APP_TD);
	
	endif;
}
