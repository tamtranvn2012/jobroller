<?php
/*
Template Name: Contact Page
*/
?>
<?php 
		if ( isset($_POST['submit-form']) && ! get_query_var('contact_success') ) {
			$loc_keys = array( 
				'your_name',
				'email',
				'message',
				'honeypot',
			);

			foreach( $loc_keys as $key ) 
				$posted[$key] = $_POST[$key];

			$posted = stripslashes_deep( $posted );
		}
?>

	<div class="section">

		<div class="section_content">

			<?php if (have_posts()) : ?>

				<?php while (have_posts()) : the_post(); ?>

					<h1><?php the_title(); ?></h1>

					<?php the_content(); ?>

					<?php do_action( 'appthemes_notices' ); ?>

					<?php if ( ! get_query_var('contact_success') ): ?>

						<!-- Contact Form -->
						<form method="post" action="<?php echo get_permalink($post->ID); ?>" class="main_form">

							<p><label for="your_name"><?php _e('Your Name/Company Name', APP_TD); ?> <span title="required">*</span></label> <input type="text" class="text" name="your_name" id="your_name" value="<?php if (isset($posted['your_name'])) echo esc_attr($posted['your_name']); ?>" /></p>
							<p><label for="email"><?php _e('Your email', APP_TD); ?> <span title="required">*</span></label> <input type="text" class="text" name="email" id="email" value="<?php if (isset($posted['email'])) echo esc_attr($posted['email']); ?>" /></p>

							<p><label for="message"><?php _e('Message', APP_TD); ?> <span title="required">*</span></label> <textarea name="message" id="message" cols="60" rows="8"><?php if (isset($posted['message'])) echo esc_textarea($posted['message']); ?></textarea></p>

						<?php
							// include the spam checker if enabled();
							if ( jr_display_recaptcha('app-recaptcha-contact') ) {
								appthemes_recaptcha();
							}
						?>

							<p class="button">
								<input type="submit" name="submit-form" class="submit" id="submit-form" value="<?php _e('Submit', APP_TD); ?>" />
								<input type="text" name="honeypot" value="" style="position: absolute; left: -999em;" title="" />
								<input type="hidden" name="contact_form" value="1">
							</p>
						</form>

					<?php else: ?>

						<?php echo html( 'a', array( 'href' => home_url() ), __( '&nbsp;&larr; Return to main page', APP_TD ) ); ?>

					<?php endif; ?>

			<?php endwhile; ?>

			<?php endif; ?>

			<div class="clear"></div>

		</div><!-- end section_content -->

	</div><!-- end section -->

	<div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar('page'); ?>
