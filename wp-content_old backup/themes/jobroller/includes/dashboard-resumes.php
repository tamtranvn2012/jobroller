<?php
	$active_subscription = jr_resume_valid_subscr();
	$resumes_access = jr_user_resumes_access();

	$valid_trial = jr_resume_valid_trial();
?>

<?php if ( $active_subscription || $valid_trial ): ?>
	<p><?php echo sprintf ( __( 'Your <em>%s</em> Resume Subscription is active:', APP_TD ), ( $valid_trial ? __( 'Trial', APP_TD ) : '' ) ); ?></p>
<?php else: ?>
	<p><?php _e( 'No active Resume subscriptions', APP_TD );?></p>

	<?php the_resume_purchase_plan_link(); ?>

<?php endif;?>

<?php if ( ! empty($resumes_access) ) : ?>

	<?php if ( 'temporary' == $resumes_access['level'] ) : ?>
		<div class="dashboard-resumes-temp-access">
			<h3><?php echo __('Temporary Access to Resumes',APP_TD); ?></h3>
			<p><?php echo __('Your purchased Plans give you temporary access to Resumes:',APP_TD); ?></p>
		</div>
	<?php endif; ?>

	<p>
	<?php foreach ( $resumes_access['access'] as $key => $access ) { ?>
		<?php echo sprintf( __( ' %s until <strong>%s</strong>', APP_TD ), $access['description'], $access['end_date'] ); ?><br/>
	<?php } ?>
	</p>

	<?php
		if ( $active_subscription && appthemes_recurring_available() ) {
			$resume_plan_id = intval( get_user_meta( get_current_user_id(), '_valid_resume_subscription',  true ) );
			$resume_plan_meta = get_post_meta( $resume_plan_id );
			if ( !empty($resume_plan_meta[JR_FIELD_PREFIX.'recurring'][0]) ) {
				$recur_text = html( 'p', sprintf( __( 'Recurs every <strong>%d</strong> %s', APP_TD ), $resume_plan_meta[JR_FIELD_PREFIX.'duration'][0] , _n( 'day', 'days', $resume_plan_meta[JR_FIELD_PREFIX.'duration'][0] ) ) );

				echo html( 'div', array( 'class' => 'recurring-subscription' ), $recur_text );
			}
		}
	?>

<?php endif; ?>

<div class="clear"></div>
