<?php
/**
 * JobRoller Application Process
 * Processes a job application sent via the form in a post.
 *
 *
 * @version 1.0
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */

function jr_process_application_form() {
	
	global $post, $app_form_results, $errors, $app_abbr;
	
	$apply_form_class = "";
	$posted = array();
	$errors = new WP_Error();
	if (isset($_POST['apply_to_job'])) :
	
		$apply_form_class = 'open';
		$linked_resume = '';
		
		// Get (and clean) data
		$fields = array(
			'your_name',
			'your_email',
			'your_message',
			'antispam_answer',
			'your_online_cv'
		);

		foreach ($fields as $field) {
			if (isset($_POST[$field])) $posted[$field] = stripslashes(trim($_POST[$field])); else $posted[$field] = '';
		}
		
		// Check required fields
		$required = array(
			'your_name' => __('Name', APP_TD),
			'your_email' => __('Email', APP_TD),
			'your_message' => __('Message', APP_TD),
		);
		
		foreach ($required as $field=>$name) {
			if (empty($posted[$field])) {
				$errors->add('submit_error_' . $name, __('<strong>ERROR</strong>: &ldquo;', APP_TD).$name.__('&rdquo; is a required field.', APP_TD));
			}
		}

		// Check the e-mail address
		if ( !empty($posted['your_email']) && !is_email( $posted['your_email'] ) ) {
			$errors->add('invalid_email', __('<strong>ERROR</strong>: The email address isn&#8217;t correct.', APP_TD));
			$posted['your_email'] = '';
		}
		
		// Check linked CV
		if ($posted['your_online_cv'] && $posted['your_online_cv']>0) :
			
			$posted['your_online_cv'] = (int) $posted['your_online_cv'];
			
			$linked_resume = get_post( $posted['your_online_cv'] );
			
			if (!is_wp_error($linked_resume) && $linked_resume->post_author == get_current_user_id() && $linked_resume->post_status == 'publish') :
			
				// Resume is okay :)
			
			else :
				$errors->add('invalid_resume', __('<strong>ERROR</strong>: Invalid resume.', APP_TD));
			endif;

		endif;
		
		// Check file extensions
		$allowed = array(
			'pdf',
			'doc',
			'docx',
			'zip',
			'txt',
			'rtf'
		);
		if (isset($_FILES['your_cv']) && !empty($_FILES['your_cv']['name'])) {
			
			//$extension = strtolower(pathinfo($_FILES['your_cv']['name'], PATHINFO_EXTENSION));
			$extension = strtolower(substr(strrchr($_FILES['your_cv']['name'], "."), 1));
			if (!in_array($extension, $allowed)) $errors->add('submit_error', __('<strong>ERROR</strong>: Only pdf, zip, doc, txt and rtf files are allowed.', APP_TD));
		}
		if (isset($_FILES['your_coverletter']) && !empty($_FILES['your_coverletter']['name'])) {
			//$extension = strtolower(pathinfo($_FILES['your_coverletter']['name'], PATHINFO_EXTENSION));
			$extension = strtolower(substr(strrchr($_FILES['your_coverletter']['name'], "."), 1));
			if (!in_array($extension, $allowed)) $errors->add('submit_error', __('<strong>ERROR</strong>: Only pdf, zip, doc, txt and rtf files are allowed.', APP_TD));
		}

		// Check AntiSpam Field
		if ( !is_user_logged_in() && ! jr_display_recaptcha('app-recaptcha-application') ):
			$ans = strtolower(trim(get_option('jr_antispam_answer')));
			if ( empty($posted['antispam_answer']) || strtolower(trim($posted['antispam_answer'])) !== $ans) {
				$errors->add('submit_error_antispam', __('<strong>ERROR</strong>: Incorrect anti-spam answer. The correct answer is ', APP_TD).'"'.$ans.'".');
			}
		endif;

		// process the reCaptcha request if it's been enabled
		$errors_recaptcha = jr_validate_recaptcha('app-recaptcha-application');
		if ( $errors_recaptcha && sizeof($errors_recaptcha)>0 ) {
			$errors->errors = array_merge( $errors->errors, $errors_recaptcha->errors);
		}

		if ($errors && sizeof($errors)>0 && $errors->get_error_code()) {
			// There are errors!
		} else {
			$attachments = array();
			$attachment_urls = array();
			// Continue, upload files
			if ((isset($_FILES['your_cv']) && !empty($_FILES['your_cv']['name'])) || (isset($_FILES['your_coverletter']) && !empty($_FILES['your_coverletter']['name']))) {
				
				// Find max filesize in bytes - we say 10mb becasue the file will be attached to an email, also checks system variables in case they are lower
				$max_sizes = array('10485760');
				if ((ini_get('post_max_size'))) $max_sizes[] = let_to_num(ini_get('post_max_size'));
				if ((ini_get('upload_max_filesize'))) $max_sizes[] = let_to_num(ini_get('upload_max_filesize'));
				if ((WP_MEMORY_LIMIT)) $max_sizes[] = let_to_num(WP_MEMORY_LIMIT);
				
				$max_filesize = min( $max_sizes );
				
				if (($_FILES["your_cv"]["size"]+$_FILES["your_coverletter"]["size"]) > $max_filesize) :
					$errors->add('submit_error', __('<strong>ERROR</strong>: ', APP_TD).'Attachments too large. Maximum file size for all attachments is '.($max_filesize/(1024*1024)).'MB');
				else :
				
					/** WordPress Administration File API */
					include_once(ABSPATH . 'wp-admin/includes/file.php');
					/** WordPress Media Administration API */
					include_once(ABSPATH . 'wp-admin/includes/media.php');
		
					add_filter('upload_dir', 'cv_upload_dir');
					
					$time = current_time('mysql');
					$overrides = array('test_form'=>false);
	
					if (isset($_FILES['your_cv']) && !empty($_FILES['your_cv']['name'])) {
						$file = wp_handle_upload($_FILES['your_cv'], $overrides, $time);
						if ( !isset($file['error']) ) {
							$attachments[] = $file['file'];
							$attachment_urls[] = $file['url'];
						} 
						else {
							$errors->add('submit_error', __('<strong>ERROR</strong>: ', APP_TD).$file['error'].'');
						}
					}
					if (isset($_FILES['your_coverletter']) && !empty($_FILES['your_coverletter']['name'])) {
						$file = wp_handle_upload($_FILES['your_coverletter'], $overrides, $time);
						if ( !isset($file['error']) ) {
							$attachments[] = $file['file'];
							$attachment_urls[] = $file['url'];
						} 
						else {
							$errors->add('submit_error', __('<strong>ERROR</strong>: ', APP_TD).$file['error'].'');
						}
					}
				
				endif;
				
				remove_filter('upload_dir', 'company_logo_upload_dir');
			
			}

			if ($errors && sizeof($errors)>0 && $errors->get_error_code()) {} else {
				
				$headers = 'From: '.$posted['your_name'].' <'.$posted['your_email'].'>' . "\r\n\\";
				$message = __("Applicant Name: ", APP_TD).$posted['your_name']."\n".__("Applicant Email: ", APP_TD).$posted['your_email'];
				
				if ($posted['your_online_cv'] && $linked_resume) :
					
					// Load or generate a key so that the recipient can view the resume without being logged in
					$view_key = get_post_meta( $linked_resume->ID, '_view_key', true );
					
					if (!$view_key) :
						
						$view_key = uniqid();
						update_post_meta( $linked_resume->ID, '_view_key', $view_key );
						
					endif;
					
					$link 	= add_query_arg('key', $view_key, get_permalink( $linked_resume->ID ));
					$title 	= $linked_resume->post_title;
					
					$message .= "\n" . __("Applicant's online resume: ", APP_TD) . $title . ' - ' . $link;
						
				endif;
				
				$message .= "\n\n".$posted['your_message'];
			
				wp_mail( get_the_author_meta('user_email'), __('Application for job "', APP_TD).$post->post_title.'"', $message, $headers, $attachments );
				
				// CC
				wp_mail( $posted['your_email'], __('[copy] Application for job "', APP_TD).$post->post_title.'"', $message, '', $attachments );
				
				// CC Admin
				if (get_option('jr_bcc_apply_emails')=='yes') :
					wp_mail( get_option('admin_email'), __('[copy] Application for job "', APP_TD).$post->post_title.'"', $message, '', $attachments );
				endif;
				
				if (sizeof($attachments)>0) {
					foreach ($attachments as $attach) {
						@unlink($attach);
					}
				}
				appthemes_display_notice( 'success', __('Your application has been sent successfully.', APP_TD) );

				$apply_form_class = "";
				
				$posted = array();
				
			}
			
		}
		
		
	endif;
	
	$app_form_results = array(
		'class' => $apply_form_class,
		'errors' => $errors,
		'posted' => $posted
	);

}

function cv_upload_dir( $pathdata ) {
	$subdir = '/uploaded_cvs'.$pathdata['subdir'];
 	$pathdata['path'] = str_replace($pathdata['subdir'], $subdir, $pathdata['path']);
 	$pathdata['url'] = str_replace($pathdata['subdir'], $subdir, $pathdata['url']);
	$pathdata['subdir'] = str_replace($pathdata['subdir'], $subdir, $pathdata['subdir']);
	return $pathdata;
}
