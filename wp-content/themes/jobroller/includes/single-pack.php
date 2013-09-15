<?php
	extract( $pack['output'] );

	$pack_selection_text = 'user' == $type ? __( 'Choose This Pack!', APP_TD ) : __( 'Buy This Pack!', APP_TD );
?>
	<div class="job-pack <?php echo esc_attr( implode( ' ', $display_options['class'] ) ); ?>" >

		<div class="job-pack-title">
			<h2><?php echo $pack['plan_data']['title']; ?> : <span class="job-pack-price"> <?php echo $cost ?></span></h2>
			<?php if ( 'user' == $type ) : ?>
				<div class="pack-activation-date"><?php echo $activation_date; ?></div>
			<?php endif; ?>
		</div>

		<?php if ( 'yes' == $display_options['selectable'] ): ?>
			<div class="job-pack-choose">
				<label><?php echo $pack_selection_text; ?>
					<input type="radio" name="plan" value="<?php echo esc_attr( $pack['plan_ref_id'] ); ?>" <?php checked( $default, 1 ); ?> />
				</label>
				<?php if ( 'user' != $type && ! empty($pack['plan_data'][JR_FIELD_PREFIX.'limit']) ) : ?>
					<?php $remain_uses = jr_plan_remain_usage( $pack['plan_id'], $pack['plan_data'] ); ?>
					<div class="pack-limit"><?php echo sprintf ( __( 'Limited Availability (%d %s)', APP_TD ), $remain_uses, __( 'remaining', APP_TD ) ); ?></div>
				<?php endif; ?>
		 	</div>
		<?php endif; ?>
		<?php if ( 'user' != $type ) : ?>
			<p class="job-pack-description"> <?php echo  $pack['plan_data'][JR_FIELD_PREFIX.'description']; ?> </p>
		<?php else: ?>
			<p>&nbsp;</p>
		<?php endif; ?>
		<ul class="job-pack-details">
			<li class="job-pack-duration"><strong><?php _e('Duration:',APP_TD); ?></strong> <?php echo $expiration; ?></li>
			<li class="job-pack-jobs-duration"><strong><?php _e('Jobs:',APP_TD); ?></strong> <?php echo $jobs; ?></li>
		</ul>
		<ul class="job-pack-details">
			<li class="job-pack-offers"><strong><?php _e('Offers:',APP_TD); ?></strong> <?php echo $offers; ?></li>
			<li class="job-pack-resume"><strong><?php _e('Access:',APP_TD); ?></strong> <?php echo $access; ?></li>
		</ul>

		<?php if ( 'no' != $display_options['categories'] ) : ?>
			<ul class="job-pack-details categories-list">
				<li class="job-pack-categories"><strong><?php _e('Categories:',APP_TD); ?></strong> <?php echo $categories; ?></li>
			</ul>
		<?php endif; ?>

		<?php if ( ! is_page( JR_Dashboard_Page::get_id() ) ) : ?>

			<?php if ( ! _jr_no_addons_available( $pack['plan_data'], ( is_page( JR_Packs_Purchase_Page::get_id() ) ? jr_get_addons('user') : '' ) ) ): ?>
				<div class="option-header">
					<?php _e( 'No additional options available for this plan.', APP_TD ); ?>
				</div>
			<?php else: ?>
				<div class="job-pack-additional-options">
					<p><strong><?php _e( 'Additional options:', APP_TD ); ?></strong></p>
					<?php
						$args = array( 
							'plan_id' => $pack['plan_ref_id'], 
							'plan_data' => $pack['plan_data'], 
							'job' => $job, 
							'pack' => $pack
						);
					?>
					<?php do_action( 'jr_plan_additional_options', $args ); ?>
				</div>
			<?php endif; ?>

		<?php endif; ?>

	</div><!-- job-pack -->
