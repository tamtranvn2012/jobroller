<?php
	### Disabled blog check
	if (get_option('jr_disable_blog')=='yes') :
		wp_redirect(get_bloginfo('url'));
		exit;
	endif;
?>

	<div class="section single">

	<?php appthemes_before_blog_loop(); ?>	
		
		<?php if(have_posts()) : ?>

			<?php while(have_posts()) : the_post() ?>
		
			<?php appthemes_before_blog_post(); ?>

			<?php appthemes_stats_update($post->ID); //records the page hit ?>        

            <div class="section_header">

                <div class="comment-bubble"><?php comments_popup_link('0', '1', '%'); ?></div>

                <?php appthemes_before_blog_post_title(); ?>

                <h1 class="title"><?php the_title(); ?></h1>

                <?php appthemes_after_blog_post_title(); ?>  

                <div class="clear"></div>

            </div><!-- end section_header -->
			

            <div class="section_content">
			
				<?php appthemes_before_blog_post_content(); ?>

                <?php the_content(); ?>
				
				<?php appthemes_after_blog_post_content(); ?>
				
                <div class="clear"></div>

            </div><!-- end section_content -->


			<div class="socialwrap">

				<div class="socialleft">

					<ul class="social-ico">

						<li class="rss"><a target="_blank" href="<?php if ( get_option('jr_feedburner_url') <> '' ) { echo get_option('jr_feedburner_url'); } else { echo get_bloginfo_rss('rss2_url').'?post_type=post'; } ?>"><?php _e('Subscribe to RSS Feed', APP_TD) ?></a></li>
						<li class="twitter"><a target="_blank" href="http://www.twitter.com/<?php echo get_option('jr_twitter_id'); ?>"><?php _e('Follow us on Twitter', APP_TD); ?></a></li>

					</ul>

				</div><!-- end social-ico -->

				<div class="socialright">
				
					<?php if (get_option('jr_sharethis_id')) { ?>
						<span class="st_twitter_vcount" displayText="Tweet"></span>
						<span class="st_facebook_vcount" displayText="Share"></span>
						<span class="st_email_vcount" displayText="Email"></span>
						<span class="st_sharethis_vcount" displayText="Share"></span>
					<?php } ?>
				
				</div><!-- end socialright -->
			
				<div class="clear"></div>

			</div><!-- end socialwrap -->    

        <?php comments_template('/comments-blog.php'); ?>

        <?php endwhile; ?>
		
			<?php appthemes_after_blog_endwhile(); ?>
		
		<?php else: ?>
		
			<?php appthemes_blog_loop_else(); ?>

			<p><?php _e('Sorry, no posts matched your criteria.', APP_TD); ?></p>

        <?php endif; ?>
		
		<?php appthemes_after_blog_loop(); ?>

        <div class="clear"></div>
		
	</div><!-- end section single -->	

</div><!-- end main content -->

<?php get_sidebar('blog'); ?>
