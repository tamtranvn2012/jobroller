<?php
/**
 * Main loop for displaying resumes
 *
 * @package JobRoller
 * @author AppThemes
 *
 */
 
 global $app_abbr;
?>

<?php appthemes_before_loop( 'resume' ); ?>

<?php if (have_posts()) : $alt = 1; ?>

    <ol class="resumes">

        <?php while (have_posts()) : the_post(); ?>
		
			<?php appthemes_before_post( 'resume' ); ?>

            <li class="resume" title="<?php echo htmlspecialchars(jr_seeker_prefs( get_the_author_meta('ID') ), ENT_QUOTES); ?>">

                <dl>

					<?php appthemes_before_post_title( 'resume' ); ?>
					
                    <dt><?php _e('Resume title', APP_TD); ?></dt>
					
                    <dd class="title">
					
						<strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>
						
						<?php 
						if ( get_option($app_abbr.'_resume_listing_visibility') != 'public' )
							echo __('Resume posted by ',APP_TD) . wptexturize(get_the_author_meta('display_name'));
						
						$terms = wp_get_post_terms($post->ID, 'resume_category');
						if ($terms) :
							_e(' in ',APP_TD);
							echo '<a href="'.get_term_link($terms[0]->slug, 'resume_category').'">' . $terms[0]->name .'</a>';
						endif;
						?>
						
                    </dd>
					
					<?php appthemes_after_post_title( 'resume' ); ?>

					<dt><?php _e('Photo',APP_TD); ?></dt>
                    <dd class="photo"><a href="<?php the_permalink(); ?>"><?php if (has_post_thumbnail()) the_post_thumbnail('listing-thumbnail'); ?></a></dd>
                    
                    <dt><?php _e('Location', APP_TD); ?></dt>
					<dd class="location"><?php jr_location(); ?></dd>
					
                    <dt><?php _e('Date Posted', APP_TD); ?></dt>
                    <dd class="date"><strong><?php echo date_i18n('j M', strtotime($post->post_date)); ?></strong> <span class="year"><?php echo date_i18n('Y', strtotime($post->post_date)); ?></span></dd>

                </dl>

            </li>
			
			<?php appthemes_after_post( 'resume' ); ?>

        <?php endwhile; ?>
		
		<?php appthemes_after_endwhile( 'resume' ); ?>

    </ol>

<?php else: ?>

	<?php appthemes_loop_else( 'resume' ); ?>
	
<?php endif; ?>

<?php appthemes_after_loop( 'resume' ); ?>
