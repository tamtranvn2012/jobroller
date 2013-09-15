<?php
/**
 * JobRoller Application form
 * Function outputs the job application form
 *
 *
 * @version 1.0
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */

function jr_application_form() {
	global $post, $app_form_results, $app_abbr;

	$errors = $app_form_results['errors'];
	$posted = $app_form_results['posted'];

	if (!$posted && is_user_logged_in()) :
		$current_user = wp_get_current_user();
		$posted['your_name'] = $current_user->user_firstname . ' ' . $current_user->user_lastname;;
		$posted['your_email'] = $current_user->user_email;
	endif;
	?>

	<div id="apply_form" class="section_content <?php echo $app_form_results['class']; ?>">
		<h2><?php _e('Apply for this Job', APP_TD); ?></h2>
		<?php jr_show_errors( $errors, 'apply_form_result' ); ?>
		<form action="<?php echo get_permalink($post->ID); ?>#apply_form_result" method="post" class="main_form" enctype="multipart/form-data">
			
			<p><label for="your_name"><?php _e('Name', APP_TD); ?> <span title="required">*</span></label> <input type="text" class="text" name="your_name" id="your_name" value="<?php if (isset($posted['your_name'])) echo $posted['your_name']; ?>" /> </p>
			<p><label for="your_email"><?php _e('Email', APP_TD); ?> <span title="required">*</span></label> <input type="text" class="text" name="your_email" id="your_email" value="<?php if (isset($posted['your_email'])) echo $posted['your_email']; ?>" /></p>
			<p><label for="your_message"><?php _e('Message', APP_TD); ?> <span title="required">*</span></label> <textarea rows="5" cols="30" name="your_message" id="your_message"><?php if (isset($posted['your_message'])) echo $posted['your_message']; ?></textarea></p>
			
			<?php if (is_user_logged_in() && !current_user_can('can_submit_job')) : ?>
				<p class="optional"><label for="your_online_cv"><?php _e('Link to Resum&eacute;', APP_TD); ?></label> <select name="your_online_cv" id="your_online_cv">
					<option value=""><?php _e('None', APP_TD); ?></option>
					<?php
						$args = array(
								'ignore_sticky_posts'	=> 1,
								'posts_per_page' => -1,
								'author' => get_current_user_id(),
								'post_type' => 'resume',
								'post_status' => 'publish'
						);
						$my_query = new WP_Query($args);
						
						if ($my_query->have_posts()) : while ($my_query->have_posts()) : $my_query->the_post();
							
							echo '<option ';
							
							if (isset($posted['your_online_cv']) && $posted['your_online_cv']==$my_query->post->ID) echo 'selected="selected"';
							
							echo ' value="'.$my_query->post->ID.'">'.$my_query->post->post_title.'</option>';
							
						endwhile; endif;

						wp_reset_query();
					?>
				</select></p>
			<?php endif; ?>

			<p class="optional"><label for="your_cv"><?php _e('Upload resum&eacute; (zip, pdf, doc, txt, rtf)', APP_TD); ?></label> <input type="file" class="text" name="your_cv" id="your_cv" /></p>
			<p class="optional"><label for="your_coverletter"><?php _e('Upload cover letter (zip, pdf, doc, txt, rtf)', APP_TD); ?></label> <input type="file" class="text" name="your_coverletter" id="your_coverletter" /></p>

			<?php
				// include the spam checker if enabled();
				 if ( !is_user_logged_in() && ! current_theme_supports('app-recaptcha-application') ) { 
			?>
					<p><label for="antispam_answer" title="<?php _e( 'This is to prevent spam', APP_TD ); ?>"><?php echo get_option('jr_antispam_question'); ?> <span title="required">*</span></label> <input type="text" class="text small" name="antispam_answer" id="antispam_answer" value="<?php if (isset($posted['antispam_answer'])) echo $posted['antispam_answer']; ?>" /></p>
			<?php } elseif ( jr_display_recaptcha('app-recaptcha-application') ) {
					// or include reCaptcha if enabled();
					appthemes_recaptcha();
				}
			?>

			<p><input type="submit" class="submit" name="apply_to_job" value="<?php _e('Apply for Job', APP_TD); ?>" /></p>
			
		</form>
	</div>
	
	<?php
}
