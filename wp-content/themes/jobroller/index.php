<?php
	// Empty search fixes
	if ( isset($_GET['resume_search']) && $_GET['resume_search'] ) : 
		if (isset($_GET['s']) && isset($_GET['location']) && !empty($_GET['location'])) : get_template_part('search-resume'); return; endif;
		wp_safe_redirect(get_post_type_archive_link('resume'));
		exit;
	endif;
	if (isset($_GET['s']) && isset($_GET['location']) && !empty($_GET['location'])) : get_template_part('search'); return; endif;
?>

<?php get_header('search'); ?>

<?php do_action('jobs_will_display'); ?>

<?php
	if ( get_query_var('paged') )
		$paged = get_query_var('paged');
	elseif ( get_query_var('page') )
		$paged = get_query_var('page');
	else
		$paged = 1;
?>

	<?php do_action('before_front_page_jobs'); ?>
	
	<div class="section">

		<h2 class="pagetitle">

			<small class="rss"><a href="<?php echo add_query_arg('post_type', 'job_listing', get_bloginfo('rss2_url')); ?>"><img src="<?php bloginfo('template_url'); ?>/images/feed.png" title="<?php _e('Latest Jobs RSS Feed',APP_TD); ?>" alt="<?php _e('Latest Jobs RSS Feed',APP_TD); ?>" /></a></small>

			<?php _e('Latest Jobs',APP_TD); ?> <?php if ($paged>1) { ?>(<?php _e('page', APP_TD ) ?> <?php echo $paged; ?>)<?php } ?>

			<?php if (isset($_GET['action']) && $_GET['action'] == 'Filter') { ?>
				<small> &mdash; <a href="<?php echo jr_get_current_url(); ?>"><?php _e('Remove Filters',APP_TD); ?></a></small>
			<?php } ?>

		</h2>

		<?php
			$main_wp_query = $wp_query;

			 $args = jr_filter_form();
			 query_posts( $args );

			// call the main loop-job.php file
			appthemes_load_template( 'loop-job.php', array( 'main_wp_query' => $main_wp_query ) );
		?>

		<?php jr_paging(); ?>
		
		<?php wp_reset_query(); ?>

		<div class="clear"></div>

	</div><!-- end section -->
	
	<?php do_action('after_front_page_jobs'); ?>

    <div class="clear"></div>
    
    
<h2><span>Power your job search </span></h2>
<div class="powerSearch">
<ul>
	<li><a href="http://catapulture.com.au/my-dashboard/#prefs"><div class="searchMail">&nbsp;</div></a>
<h3>Get Jobs By Email</h3>
Get the latest jobs sent straight to your inbox</li>
	<li><div class="searchProfile">&nbsp;</div>
<h3>Create Your Profile</h3>
Be in the best position to move quickly.</li>
	<li><div class="searchCv">&nbsp;</div>
<h3>Upload your CV</h3>
Save time and let the best employers find you.</li>
</ul>
<div class="clear"></div>
</div>


<div class="otherpages">
<div class="w49 fl">
<h3>Catapult Your Career</h3>
<p>Find thousands of relevant articles on resume advice, job-hunting guidance, career planning tips and more. </p>
<ul>
<li><a href="http://catapulture.com.au/career/resume-critique-checklist-3/" tilte="">Resume Critique Checklist</a></li>
<li><a href="http://catapulture.com.au/career/six-steps-to-handling-money-questions/" tilte="">Six Steps to Handling Money Questions</a></li>
<li><a href="http://catapulture.com.au/career/high-paying-jobs-for-gen-y/" tilte="">High-Paying Jobs for Gen Y</a></li>
</ul>

</div>

<div class="w49 fr">

<h3>Questions & Answers</h3>
<p>Questions & Answers powered by Yahoo! Answers</p>
<ul>
<li><a href="http://catapulture.com.au/questions-answers/is-this-normal-behaviour-for-an-interviewer/" tilte="">Is this Normal Behaviour for an Interviewer?</a></li>
<li><a href="http://catapulture.com.au/questions-answers/how-can-i-get-a-better-job-with-little-experience/" tilte="">How can I get a Better Job with Little Exp?</a></li>
<li><a href="http://catapulture.com.au/questions-answers/what-should-i-wear-to-an-interview/" tilte="">What Should I Wear to an Interview?</a></li>
<li><a href="http://catapulture.com.au/questions-answers/10-tips-to-rehearse-for-a-job-interview/" tilte="">10 Tips to Rehearse for a Job Interview</a></li>
</ul>

</div>

</div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar(); ?>
