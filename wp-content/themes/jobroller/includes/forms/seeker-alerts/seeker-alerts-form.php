<?php
/**
 * Job Seeker Job Alerts form
 * Function outputs the Job Seeker Job Alerts form
 *
 *
 * @version 1.6
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */

function jr_job_seeker_alerts_form() {	
	global $post, $user_ID, $app_abbr;
		
	$keywords = get_user_meta($user_ID, $app_abbr.'_alert_meta_keyword', true);
	$locations = get_user_meta($user_ID, $app_abbr.'_alert_meta_location', true);
	$job_types = get_user_meta($user_ID, $app_abbr.'_alert_meta_job_type', true);
	$job_cats = get_user_meta($user_ID, $app_abbr.'_alert_meta_job_cat', true);
	$alert_status = get_user_meta($user_ID, $app_abbr.'_alert_status', true);
	
	?>
	<form action="<?php echo get_permalink( $post->ID ); ?>" method="post" id="submit_form" class="submit_form main_form">

		<p><?php _e('You can receive tailored job alerts directly on your email. Control alerts by selecting the options that best suit the job you are looking for.', APP_TD); ?></p>

		<fieldset>
	
			<legend><?php _e('Job Criteria', APP_TD); ?></legend>
			<p><?php _e('All the options are <em>inclusive</em>. This means that jobs will be matched against all your criteria. For example, adding keywords and a location will limit job results to those having that location <em>and</em> at least one of the keywords.', APP_TD); ?></p>			
			<p><?php _e('Leave the job types or job categories empty to receive alerts from all job types or job categories',APP_TD); ?></p>
			<p><label for="alert_keywords"><?php _e('Key Words <small>(comma separated)</small>', APP_TD); ?></label> <input type="text" class="tags text" name="alert_keywords" id="alert_keywords" placeholder="<?php _e('e.g. Web Design, Designer', APP_TD); ?>" value="<?php if (!empty($keywords)) echo implode(',',$keywords); ?>" /></p>
			
			<p><label for="alert_location"><?php _e('Locations <small>(comma separated)</small>', APP_TD); ?></label> 
			<input type="text" class="tags text" name="alert_location" id="alert_location" placeholder="<?php _e('e.g. London, United Kingdom', APP_TD); ?>" value="<?php if (!empty($locations)) echo implode(',',$locations); ?>" /></p>			
	
		</fieldset>
		
		<fieldset>
					
			<div class="optional alerts prefs_job_types"><label><?php _e('Types of Job<br/><small>(Leave empty to receive alerts from any job type)</small>', APP_TD); ?></label>
				<ul>
				<?php
				$all_job_types = jr_get_taxonomy_terms( APP_TAX_TYPE );
				if ($all_job_types && sizeof($all_job_types) > 0) {
					jr_output_alert_terms( $all_job_types, $job_types );						
				}
				?>
				</ul>
			</div>
			
		</fieldset>		
		
		<fieldset>
		
			<div class="optional alerts prefs_job_categories"><label><?php _e('Categories of Job<br/><small>(Leave empty to receive alerts from any job category)</small>', APP_TD); ?></label>
				<ul>
				<?php
				$all_job_cats = jr_get_taxonomy_terms( APP_TAX_CAT );
				if ($all_job_cats && sizeof($all_job_cats) > 0) {
					jr_output_alert_terms( $all_job_cats, $job_cats );						
				}
				?>
				</ul>
			</div>
			
		</fieldset>				

		<?php if ( get_option($app_abbr.'_job_alerts_feed') == 'yes' && $feed_key = get_user_meta($user_ID,$app_abbr.'_alert_feed_key',true) ): ?>
		
			<?php $alert_feed = trailingslashit(get_bloginfo('rss2_url')) . $feed_key; ?>

			<fieldset>

				<legend><?php _e('Alert Feed', APP_TD); ?></legend>

				<p><?php _e('This is your unique RSS feed representing your alert settings. You can use it to subscribe to <em>Google Reader</em> or any other service to keep you updated of new jobs
							fitting your criteria, as they are published.', APP_TD); ?></p>

				<p><label for="alert_feed"></label>
				<a href="<?php echo $alert_feed; ?>" title="<?php _e('Your Job Alert RSS Feed',APP_TD); ?>" alt="<?php _e('Your Job Alert RSS Feed',APP_TD); ?>" /><small class="alert_rss"></small></a>
				<input type="text" class="text"  style="width: 550px; color: #ADADAD;" readonly value="<?php echo $alert_feed; ?>"></input>
				</p>

			</fieldset>

		<?php endif; ?>

		<?php if ( get_option($app_abbr.'_job_alerts') == 'yes' ): ?>

			<fieldset>

				<legend><?php _e('Alerts Status', APP_TD); ?></legend>

				<p><?php _e('You can subscribe/unsubscribe to job alerts at any time.', APP_TD); ?></p>

				<p><label for="alert_status"></label>
				<select name="alert_status" id="alert_status"/>
					<option value="active" <?php selected( $alert_status, 'active' ); ?>><?php _e('Subscribed',APP_TD); ?></option>
					<option value="inactive" <?php selected( !$alert_status || $alert_status == 'inactive',1); ?>><?php _e('Unsubscribed',APP_TD); ?></option>
				</select>
				</p>

			</fieldset>

		<?php endif; ?>

		<p><input type="submit" class="submit" name="save_alerts" value="<?php _e('Save &rarr;', APP_TD); ?>" /></p>

		<div class="clear"></div>

	</form>

	<?php
}

### return list of terms from taxonomy (ADICIONAR AO THEME-FUNCTIONS.php)
function jr_get_taxonomy_terms( $taxonomy ) {		

	$args = array(
	  'hide_empty'	=> 0,
	  'taxonomy' 	=> $taxonomy,
	);									

	return get_terms( $taxonomy, $args );
	  
}	


### outputs terms for the job alerts
function jr_output_alert_terms( $terms, $user_options = array() ) {

	$output = '';
	foreach ($terms as $term )
		$output .= '<li><label for="' . esc_attr($term->slug) . '"><input type="checkbox" name="alert_'  . esc_attr($term->taxonomy) . '[' . esc_attr($term->slug) . ']" id="' . esc_attr($term->slug) . '"' .
					checked(is_array($user_options) && in_array($term->term_id, $user_options), 1, FALSE) . ' value="'.esc_attr($term->term_id).'" /> ' . $term->name . '</label></li>';

	echo $output;
}
