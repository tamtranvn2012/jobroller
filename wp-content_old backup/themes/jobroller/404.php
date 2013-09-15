
		<div class="section">

			<div class="section_content">

				<?php if ( is_admin() ) : ?>
				
					<h1><?php _e('Access Denied.', APP_TD); ?></h1>
					<p><?php _e('Your site administrator has blocked your access to the WordPress back-office.', APP_TD) ?></p>
					
				<?php elseif ($wp_query->query_vars['post_type']=='resume') : ?>
					
					<h1><?php _e('No resumes exist', APP_TD); ?></h1>
					<p><?php _e('No resumes have been submitted yet. When a resume is submitted it will appear here.', APP_TD) ?></p>
				
				<?php else : ?>
				
					<h1><?php _e('Sorry, Nothing was Found', APP_TD); ?></h1>
					<p><?php _e('The page, job, or post may have been moved or no longer exists.', APP_TD); ?></p>
					
				<?php endif; ?>

			</div>

		</div><!-- end section -->

		<div class="clear"></div>

	</div><!-- end main content -->

<?php if ( ! is_admin() && get_option('jr_show_sidebar') !== 'no' ) get_sidebar(); ?>
