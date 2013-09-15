<?php

/**
 * Allow changing of values from user page
 */
function jr_profile_fields( $user ) {
	global $jr_options;

	$plans = jr_get_available_plans();
?>

	<?php if ($plans ) : ?>

	<h3><?php _e('Job Plans', APP_TD); ?></h3>
	
	<table class="form-table" >
 
 		<?php if ( 'pack' == $jr_options->plan_type ) : ?>
 
		<tr>
			<th><label><?php _e('Current User Packs', APP_TD); ?></label></th>
			<td>
				<?php
					$user_packs = jr_get_user_plan_packs( $user->ID );
					if ( sizeof($user_packs) > 0 ) :
					
						echo '
						<table class="job_packs">
							<thead>
								<tr>
								<th>'.__( 'Name', APP_TD ).'</th>
								<th>'.__( 'Jobs Remaining', APP_TD ).'</th>
								<th>'.__( 'Jobs Duration', APP_TD ).'</th>
								<th>'.__( 'Expire Date', APP_TD ).'</th>
								<th>'.__( 'Delete Pack?', APP_TD ).'</th>
								</tr>
							</thead>
							<tbody>';
						
						foreach ( $user_packs as $pack ) :
						
							if ( ! $pack['meta']['jobs_limit'] ) :
								$jobs_remain = __('Unlimited', APP_TD);
							else :
								$jobs_remain = jr_pack_jobs_remain( $pack );
							endif;
							
							if ( $pack['meta']['end_date'] > 0 ) $expire_date = appthemes_display_date( $pack['meta']['end_date'] ); else $expire_date = __( 'Endless', APP_TD );

							if ( $pack['meta']['jobs_duration'] > 0 ) $jobs_duration = sprintf( '%s %s', $pack['meta']['jobs_duration'], _n( 'Day', 'Days' , $pack['meta']['jobs_duration'] ) ) ; else $jobs_duration = __( 'Endless', APP_TD );

							echo '<tr>
								<td>'.$pack['plan_data']['title'].'</td>
								<td>'.$jobs_remain.'</td>
								<td>'.$jobs_duration.'</td>
								<td>'.$expire_date.'</td>
								<td><input type="checkbox" name="delete_pack[]" value="'.$pack['plan_ref_id'].'" /></td>
							</tr>';
							
						endforeach;
						
						echo '</tbody></table>';
					
					else :
						?><p><?php _e('No active Packs found.', APP_TD); ?></p><?php
					endif;
				?>
			</td>
		</tr>
		<tr>
			<th><label><?php _e('Assign Pack', APP_TD); ?></label></th>
			<td>
				<select name="give_job_pack" class="assign-plan"><option value=""><?php _e('Choose a Pack...', APP_TD); ?></option>
				<?php
					$plans = jr_get_available_plans();
					if ( sizeof($plans) > 0 ) foreach ( $plans as $key => $plan ) :
						
						echo '<option value="'.$plan['post_data']->ID.'">'.$plan['post_data']->post_title.'</option>';

					endforeach;
				?>
				</select>
			</td>
		</tr>

		<?php endif; ?>

		<tr>
			<th><label><?php _e( 'Reset Usage', APP_TD ); ?></label></th>
			<td>
				<input type="checkbox" name="reset_job_plans_usage" value="1">
				<?php echo html( 'em', __( 'This option resets the plans usage for this user. All \'Job Plans\' will be available for selection until the usage limit is reached again.', APP_TD ) ); ?>
			</td>
		</tr>

	</table>

	<?php endif; ?>

	<?php if ( jr_viewing_resumes_require_subscription() || jr_resume_valid_subscr( $user->ID ) ): ?>

	<h3><?php _e('Resumes Subscriptions', APP_TD); ?></h3>
 
	<table class="form-table">
 
 		<tr>
			<th><label><?php _e( 'Current Subscription', APP_TD ); ?></label></th>
			<td>
				<em>
				<?php 
					$plan_id = get_user_meta( $user->ID, '_valid_resume_subscription', true ); 
					if ( $plan_id ) {
						if ( $plan_id > 1 ) {
							$plan_data = get_post_custom( $plan_id );
							echo $plan_data['title'][0];
						} else {
							echo __( 'N/A', APP_TD );
						}
					} else {
						echo html( 'p', __( 'None', APP_TD  ) );
						$resumes_access = jr_user_resumes_access( $user->ID );
						if ( !empty($resumes_access['access']) ) {
							echo html( 'strong', __( 'Temporary access granted by purchased Plans: ', APP_TD ) );
							foreach ( $resumes_access['access'] as $key => $access ) {
								echo html( 'div',  sprintf( __( ' %s until <strong>%s</strong>', APP_TD ), $access['description'], $access['end_date'] ) );
							} 
						}
					}
				?>
				</em>
 			</td>
 		</tr>
		<?php if ( $plan_id ) : ?>
 		<tr>
			<th>&nbsp;</th>
			<td>
				<input type="checkbox" name="cancel_resume_subscription" value="1"> <?php _e( 'Cancel Subscription', APP_TD ); ?>
 			</td>
 		</tr>
		<?php endif; ?>
		<tr>
			<th><label><?php _e( 'Assign Subscription', APP_TD ); ?></label></th>
 
			<td>
				<select name="resumes_access" class="assign-plan"><option value=""><?php _e( 'Choose a Subscription...', APP_TD ); ?></option>
				<?php
					$plans = jr_get_available_plans( array( 'post_type' => APPTHEMES_RESUMES_PLAN_PTYPE ) );
					if ( sizeof($plans) > 0 ) foreach ( $plans as $key => $plan ) :
						if ( $plan_id == $plan['post_data']->ID ) continue;
						echo '<option value="'.$plan['post_data']->ID.'">'.$plan['post_data']->post_title.'</option>';
					endforeach;
				?>
				</select>
			</td>
		</tr>

		<tr>
			<th><label><?php _e( 'Reset Usage', APP_TD ); ?></label></th>
			<td>
				<input type="checkbox" name="reset_resume_plans_usage" value="1">
				<?php echo html( 'em', __( 'This option resets the plans usage for this user. All \'Resume Plans\' will be available for selection until the usage limit is reached again.', APP_TD ) ); ?>
			</td>
		</tr>

	</table>

	<?php endif; ?>
	
<?php }

if (current_user_can('manage_options')) :
	add_action( 'show_user_profile', 'jr_profile_fields', 10 );
	add_action( 'edit_user_profile', 'jr_profile_fields', 10 );
	add_action( 'personal_options_update', 'jr_save_profile_fields' );
	add_action( 'edit_user_profile_update', 'jr_save_profile_fields' );
endif;

function jr_save_profile_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) ) return false;

	if ( !empty($_POST['reset_job_plans_usage']) ) {
		jr_reset_user_plan_usage( $user_id, APPTHEMES_PRICE_PLAN_PTYPE );
	}

	if ( !empty($_POST['reset_resume_plans_usage']) ) {
		jr_reset_user_plan_usage( $user_id, APPTHEMES_RESUMES_PLAN_PTYPE );
	}

	if ( !empty($_POST['resumes_access']) ) {

		// start subscription
		$plan_id = intval($_POST['resumes_access']);
		$plan_data = get_post_custom( $plan_id );

		if ( !$plan_data ) return;

		do_action( 'user_resume_subscription_started', $user_id, $plan_id, $plan_data );
	} elseif ( !empty($_POST['cancel_resume_subscription']) ) {
		// end subscription
		do_action( 'user_resume_subscription_ended', $user_id );
	};

	if ( !empty($_POST['give_job_pack']) ) :
		$plan_id = (int) $_POST['give_job_pack'];

		jr_give_pack_to_user( $user_id, $plan_id );
	endif;

	if ( isset($_POST['delete_pack']) && is_array($_POST['delete_pack']) && sizeof($_POST['delete_pack']) > 0 ) {

		foreach ( $_POST['delete_pack'] as $plan_umeta_id ) {
			jr_expire_user_pack( $user_id, $plan_umeta_id );
		}

	};
}

/**
 * Track User Job Views
 */
function jr_viewed_jobs() {
	global $post;
	if( is_single() && is_user_logged_in() && get_post_type() == 'job_listing' ) :
		
		$_viewed_jobs = get_user_meta(get_current_user_id(), '_viewed_jobs', true);
		if (!is_array($_viewed_jobs)) $_viewed_jobs = array();
		
		if (!in_array($post->ID, $_viewed_jobs)) $_viewed_jobs[] = $post->ID;
		
		$_viewed_jobs = array_reverse($_viewed_jobs);
		$_viewed_jobs = array_slice($_viewed_jobs, 0, 5);
		$_viewed_jobs = array_reverse($_viewed_jobs);
		
		update_user_meta(get_current_user_id(), '_viewed_jobs', $_viewed_jobs);
	endif;
}

add_action('appthemes_before_post', 'jr_viewed_jobs');

/**
 * Star Jobs
 */
function jr_star_jobs() {
	global $post;
	if( isset($_GET['star']) && is_single() && is_user_logged_in() && get_post_type() == 'job_listing' ) :
		
		$_starred_jobs = get_user_meta(get_current_user_id(), '_starred_jobs', true);
		if (!is_array($_starred_jobs)) $_starred_jobs = array();
		
		if ($_GET['star']=='true') :
			if (!in_array($post->ID, $_starred_jobs)) : $_starred_jobs[] = $post->ID; endif;
		else :
			$_starred_jobs = array_diff($_starred_jobs, array($post->ID));
		endif;

		update_user_meta(get_current_user_id(), '_starred_jobs', $_starred_jobs);
	endif;
}

add_action('appthemes_before_post', 'jr_star_jobs');


/**
 * Get job seeker prefs table
 */
function jr_seeker_prefs( $user_id ) {
	
	$prefs = '<table cellspacing="0" class="user_prefs">';
	
	$availability_month 	= get_user_meta($user_id, 'availability_month', true);
	$availability_year 	= get_user_meta($user_id, 'availability_year', true);
	//$your_location			= get_user_meta($user_id, 'your_location', true);
	$career_status 			= get_user_meta($user_id, 'career_status', true);
	$willing_to_relocate 	= get_user_meta($user_id, 'willing_to_relocate', true);
	$willing_to_travel 		= get_user_meta($user_id, 'willing_to_travel', true);
	$where_you_can_work 	= get_user_meta($user_id, 'where_you_can_work', true);
	
	if ($career_status) :
		$prefs .= '<tr><th>' . __('Career Status:', APP_TD) . '</th><td>';
		switch ($career_status) :
			case "looking" :
				$prefs .= __('Actively looking', APP_TD);
			break;
			case "open" :
				$prefs .= __('Open to new opportunities', APP_TD);
			break;
			case "notlooking" :
				$prefs .= __('Not actively looking', APP_TD);
			break;
		endswitch;
		echo '</td></tr>';
	endif;
	
	//if ($your_location) $prefs .= '<tr><th>' . __('Location:', APP_TD) . '</th><td>' . wptexturize($your_location) . '</td></tr>';
	
	if ($availability_month && $availability_year) :
		$prefs .= '<tr><th>' . __('Availability:', APP_TD) . '</th><td>' .  jr_translate_months( date('F', mktime(0, 0, 0, $availability_month, 11, $availability_year)) ). ' ' . date('Y', mktime(0, 0, 0, $availability_month, 11, $availability_year)). '</td></tr>';
	else :
		$prefs .= '<tr><th>' . __('Availability:', APP_TD) . '</th><td>' .  __('Immediate', APP_TD) . '</td></tr>';
	endif;
	
	if ($willing_to_relocate=='yes') $prefs .= '<tr><th>' . __('Willing to relocate:', APP_TD) . '</th><td><img class="load" src="'.get_bloginfo('template_url').'/images/check.png" alt="yes" /></td></tr>';

	if ($willing_to_travel) :
		$prefs .= '<tr><th>' . __('Willingness to travel:', APP_TD) . '</th><td>';
		switch ($willing_to_travel) :
			case "100" :
				$prefs .= __('Willing to travel', APP_TD);
			break;
			case "75" :
				$prefs .= __('Fairly willing to travel', APP_TD);
			break;
			case "50" :
				$prefs .= __('Not very willing to travel', APP_TD);
			break;
			case "25" :
				$prefs .= __('Local opportunities only', APP_TD);
			break;
			case "0" :
				$prefs .= __('Not willing to travel/working from home', APP_TD);
			break;
		endswitch;
		$prefs .='</td></tr>';
	endif;
	
	$prefs .= '</table>';
	return $prefs;
}
