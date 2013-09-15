<?php if ( JR_Job_Submit_Page::get_id() ) : ?>

	<li class="widget widget-submit">
		
		<?php if (!is_user_logged_in() || (is_user_logged_in() && current_user_can('can_submit_job'))) : ?>

			<div>
				<a href="<?php echo jr_get_listing_create_url() ?>" class="button"><span><?php _e('Submit a Job',APP_TD); ?></span></a>
				<?php echo jr_get_submit_footer_text(); ?>
			</div>

		<?php endif; ?>

		<?php if (is_user_logged_in() && current_user_can('can_submit_resume')) : ?>

			<?php if (get_option('jr_allow_job_seekers')=='yes') : ?>
				<div>
					<a href="<?php echo get_permalink( JR_Dashboard_Page::get_id() ); ?>" class="button"><span><?php _e('My Dashboard',APP_TD); ?></span></a>
					<?php if ($text = get_option('jr_my_profile_button_text')) echo wpautop(wptexturize($text)); ?>
				</div>
			<?php endif; ?>

		<?php endif; ?>

	</li>

<?php endif; ?>
