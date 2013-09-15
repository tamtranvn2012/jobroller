<?php get_header('search'); ?>

	<div class="section single">

	<?php do_action( 'appthemes_notices' ); ?>

	<?php appthemes_before_loop(); ?>
		
		<?php if (have_posts()) : ?>

			<?php while (have_posts()) : the_post(); ?>
			
				<?php appthemes_before_post(); ?>

				<?php appthemes_stats_update($post->ID); //records the page hit ?>

				<div class="section_header">

					<div class="date"><strong><?php the_time(__('j M',APP_TD)); ?></strong> <span class="year"><?php the_time(__('Y',APP_TD)); ?></span></div>

					<?php appthemes_before_post_title(); ?>
					
					<h1 class="title">

						<span class="type"><?php jr_get_custom_taxonomy($post->ID, 'job_type', 'jtype'); ?></span>

							<?php the_title(); ?>

					</h1>
					
					<?php appthemes_after_post_title(); ?>

					<p class="meta">
						<?php jr_job_author(); ?>

						&ndash;

						<?php jr_location( true ); ?>
					</p>

					<div class="clear"></div>

				</div><!-- end section_header -->

				<div class="section_content">

					<?php do_action('job_main_section', $post); ?>

					<?php if (get_option('jr_sharethis_id')) { ?>
						<p class="sharethis">
							<span class="st_twitter_hcount" displayText="Tweet"></span>
							<span class="st_facebook_hcount" displayText="Share"></span>
						</p>
					<?php } ?>

					<?php if (has_post_thumbnail()) the_post_thumbnail(); ?>

					<h2><?php _e('Job Description', APP_TD); ?></h2>

					<?php appthemes_before_post_content(); ?>
					
					<?php the_content(); ?>

					<?php the_job_listing_fields(); ?>

					<?php the_listing_files(); ?>

					<?php appthemes_after_post_content(); ?>

					<?php if (get_option('jr_enable_listing_banner')=='yes') : ?><div id="listingAd"><?php echo stripslashes(get_option('jr_listing_banner')); ?></div><?php endif; ?>

					<?php if (get_option('jr_submit_how_to_apply_display')=='yes' && get_post_meta($post->ID, '_how_to_apply', true)) { ?>

						<h2><?php _e('How to Apply',APP_TD) ?></h2>
						<?php echo apply_filters('jr_how_to_apply_content', get_post_meta($post->ID, '_how_to_apply', true)); ?>

					<?php } ?>

					<p class="meta"><em><?php the_taxonomies(); ?> <?php if (!jr_check_expired($post) && jr_remaining_days($post)!='-') : ?><?php _e('Job expires in', APP_TD) ?> <strong><?php echo jr_remaining_days($post); ?></strong>.<?php endif; ?></em></p>

					<?php if ( get_option('jr_ad_stats_all') == 'yes' && current_theme_supports( 'app-stats' ) ) { ?><p class="stats"><?php appthemes_stats_counter($post->ID); ?></p> <?php } ?>

					<div class="clear"></div>

				</div><!-- end section_content -->

				<?php
				// load up theme-actions.php and display the apply form
				do_action('job_footer');
				?>
				<ul class="section_footer" style="display:none;">

					<?php if ($url = get_post_meta($post->ID, 'job_url', true)) : ?>
						<li class="apply"><a href="<?php echo $url; ?>" <?php
							 if ($onmousedown = get_post_meta($post->ID, 'onmousedown', true)) :
							 	echo 'onmousedown="'.$onmousedown.'"';
							 endif;
						?> target="_blank" rel="nofollow"><?php _e('View &amp; Apply Online',APP_TD); ?></a></li>
					<?php else :?>
						<li class="apply"><a href="#apply_form" class="apply_online"><?php _e('Apply Online',APP_TD); ?></a></li>
					<?php endif; ?>
					
					<?php if (is_user_logged_in() && current_user_can('can_submit_resume')) : $starred = (array) get_user_meta(get_current_user_id(), '_starred_jobs', true); ?>
						<?php if (!in_array($post->ID, $starred)) : ?>
							<li class="star"><a href="<?php echo add_query_arg( 'star', 'true', get_permalink() ); ?>" class="star"><?php _e('Star Job',APP_TD); ?></a></li>
						<?php else : ?>
							<li class="star"><a href="<?php echo add_query_arg( 'star', 'false', get_permalink() ); ?>" class="star"><?php _e('Un-star Job',APP_TD); ?></a></li>
						<?php endif; ?>
					<?php endif; ?>
					
					<li class="print"><a href="javascript:window.print();"><?php _e('Print Job',APP_TD); ?></a></li>
					
					<?php if (get_post_meta($post->ID, '_jr_geo_longitude', true) && get_post_meta($post->ID, '_jr_geo_latitude', true)) : ?><li class="map"><a href="#map" class="toggle_map"><?php _e('View Map',APP_TD); ?></a></li><?php endif; ?>
					
					<?php if(function_exists('selfserv_sexy')) { ?><li class="sexy share"><a href="#share_form" class="share"><?php _e('Share Job',APP_TD); ?></a></li><?php } ?>

					<li class="edit-job"><?php the_job_edit_link(); ?></li>

				</ul>

				<?php comments_template(); ?>
				
				<?php appthemes_after_post(); ?>

			<?php endwhile; ?>

				<?php appthemes_after_endwhile(); ?>

		<?php else: ?>

			<?php appthemes_loop_else(); ?>

		<?php endif; ?>	

		<?php appthemes_after_loop(); ?>

	</div><!-- end section -->	

	<div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar('job'); ?>
