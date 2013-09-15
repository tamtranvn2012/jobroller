<?php
/**
 * JobRoller Preview Job form
 * Function outputs the job preview form
 *
 *
 * @version 1.0
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */
 ?>
	<form action="<?php echo esc_url( $form_action ) ?>" method="post" enctype="multipart/form-data" id="submit_form" class="submit_form main_form">
		<?php wp_nonce_field('submit_job', 'nonce') ?>
		<p><?php _e('Below is a preview of what your job listing will look like when published:', APP_TD); ?></p>
		
		<ol class="jobs">
			<li class="job" style="padding-left:0; padding-right:0;"><dl>
				<dt><?php _e('Type',APP_TD); ?></dt>
				<dd class="type"><?php
					$job_type = get_term_by( 'id', (int)$job->type, APP_TAX_TYPE );
					echo '<span class="'.$job_type->slug.'">'.wptexturize($job_type->name).'</span>';
				?>&nbsp;</dd>
				<dt><?php _e('Job', APP_TD); ?></dt>
				<dd class="title"><strong><?php echo $job->post_title; ?> </strong><?php
					
					$author = get_user_by('id', get_current_user_id());
					
					if ( $job->your_name ) :
						echo $job->your_name;
						if ( $author && $link = get_author_posts_url( $author->ID, $author->user_nicename ) ) :
							echo sprintf( __(' &ndash; Posted by <a href="%s">%s</a>', APP_TD), $link, $author->display_name );
						endif;
					else :
						if ($author && $link = get_author_posts_url( $author->ID, $author->user_nicename )) :
							echo sprintf( __('<a href="%s">%s</a>', APP_TD), $link, $author->display_name );
						endif;
					endif;
					
					?>
				</dd>
				<dt><?php _e('_Location', APP_TD); ?></dt>
				<dd class="location"><?php
				
					$latitude = jr_clean_coordinate( $job->jr_geo_latitude );
					$longitude = jr_clean_coordinate( $job->jr_geo_longitude );

					if ( $job->jr_address && $latitude && $longitude ) :
						$address = jr_reverse_geocode($latitude, $longitude);
						
						echo '<strong>'.wptexturize($address['short_address']).'</strong> '.wptexturize($address['short_address_country']).'';
					else :
						echo '<strong>'.__('Anywhere',APP_TD).'</strong>';
					endif;
				?></dd>
				<dt><?php _e('Date Posted', APP_TD); ?></dt>
				<dd class="date"><strong><?php echo date_i18n(__('j M',APP_TD)); ?></strong> <span class="year"><?php echo date_i18n(__('Y',APP_TD)); ?></span></dd>
			</dl></li>
		</ol>
		
		<p><?php _e('The job listing&rsquo;s page will contain the following information:', APP_TD); ?></p>
		
		<blockquote>
			<h2><?php _e('Job description',APP_TD); ?></h2>
			<?php echo wpautop(wptexturize($job->post_content)); ?>
			<?php if (get_option('jr_submit_how_to_apply_display')=='yes') : ?>
				<h2><?php _e('How to apply',APP_TD); ?></h2>
				<?php echo wpautop(wptexturize($job->apply)); ?>
			<?php endif; ?>
		</blockquote>

		<?php do_action( 'jr_after_preview_job_form' ); ?>

		<input type="hidden" name="action" value="<?php echo esc_attr($post_action); ?>" />
		<input type="hidden" name="ID" value="<?php echo esc_attr($job->ID); ?>">
		<input type="hidden" name="order_id" value="<?php echo esc_attr($order_id); ?>">
		<input type="hidden" name="step" value="<?php echo esc_attr($step); ?>"/>

		<p>
			<input type="submit" class="goback" name="goback" value="<?php esc_attr_e( 'Go Back',APP_TD ); ?>"  /> 
			<input type="submit" class="submit" name="preview_submit" value="<?php esc_attr_e( 'Next &rarr;', APP_TD ); ?>" />
		</p>

		<div class="clear"></div>
	</form>
<?php
