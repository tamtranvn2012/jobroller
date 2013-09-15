<li class="widget widget-submit">
	<?php if (!is_user_logged_in()) : ?>
			<div>
				<a href="<?php echo site_url('wp-login.php'); ?>" class="button"><span><?php _e('Submit your Resume',APP_TD); ?></span></a>
				<?php if ($text = get_option('jr_submit_resume_button_text')) echo wpautop(wptexturize($text)); ?>
			</div>
	<?php else : ?>
			<div>
				<a href="<?php echo get_permalink( JR_Dashboard_Page::get_id() ); ?>" class="button"><span><?php _e('My Dashboard',APP_TD); ?></span></a>
				<?php if (is_user_logged_in() && current_user_can('can_submit_resume')) : ?><?php if ($text = get_option('jr_my_profile_button_text')) echo wpautop(wptexturize($text)); ?><?php endif; ?>
			</div>
	<?php endif; ?>
</li> 