<?php
/**
 * Main loop for displaying jobs
 *
 * @package JobRoller
 * @author AppThemes
 *
 */
?>

<?php appthemes_before_loop( 'job_listing' ); ?>

<?php if (have_posts()) : $alt = 1; ?>

    <ol class="jobs">

        <?php while (have_posts()) : the_post(); ?>
		
			<?php appthemes_before_post( 'job_listing' ); ?>

            <?php
				$post_class = array('job');
				$expired = jr_check_expired( $post );

				if ( $expired ) {
					$post_class[] = 'job-expired';
				}
				$alt=$alt*-1;

				if ($alt==1) $post_class[] = 'job-alt';

				if ( !empty($main_wp_query) && jr_is_listing_featured( $post->ID, $main_wp_query ) ) $post_class[] = 'job-featured';
				
            ?>

            <li class="<?php echo implode(' ', $post_class); ?>">

                <dl>

                    <dt class="viewthumb type"><?php jr_get_custom_taxonomy($post->ID, 'job_type', 'jtype'); ?><?php if (has_post_thumbnail()) the_post_thumbnail(); ?><?php _e('',APP_TD); ?></dt>
                    <dd class="type"></dd>

                    <dt><?php _e('Job', APP_TD); ?></dt>
					
					<?php appthemes_before_post_title( 'job_listing' ); ?>

                    <dd class="title">
						<strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>
                       <div class="excerpt"><?php the_excerpt(); ?></div>

						<?php jr_job_author(); ?>
                    </dd>

					<?php appthemes_after_post_title( 'job_listing' ); ?>

                    <dt><?php _e('Location', APP_TD); ?></dt>
					<dd class="location"><?php jr_location(); ?></dd>

                    <dt><?php _e('Date Posted', APP_TD); ?></dt>
                    <dd class="date"><strong><?php echo date_i18n(__('j M',APP_TD), strtotime($post->post_date)); ?></strong> <span class="year"><?php echo date_i18n(__('Y',APP_TD), strtotime($post->post_date)); ?></span>
                    
                    <div class="sticker">
						<?php  
							global $current_user; 
							$user_id	=	$current_user->ID;
							$postid 	=	$post->ID;

							if(get_post_meta($postid,$postid."_".$user_id,true) != '') { ?>
							<img src="<?php bloginfo('template_directory') ?>/images/sticker_tick.png" >
						<?php } else { ?>
						<img src="<?php bloginfo('template_directory') ?>/images/sticker.png" >
						
						<?php } ?>
					
					</div>
                    </dd>

                </dl>

            </li>
			
			<?php appthemes_after_post( 'job_listing' ); ?>

        <?php endwhile; ?>
		
		<?php appthemes_after_endwhile( 'job_listing' ); ?>

    </ol>

<?php else: ?>

	<?php appthemes_loop_else( 'job_listing' ); ?>        
	
<?php endif; ?>

<?php appthemes_after_loop( 'job_listing' ); ?>
