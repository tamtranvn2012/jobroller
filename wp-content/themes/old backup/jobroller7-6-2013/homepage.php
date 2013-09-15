<?php
/*
Template Name: home
*/
?>


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

		<div class="section_content">
			<h2><span>Power of your job search </span></h2>
<div class="powerSearch">
<ul>
	<li><div class="searchMail">&nbsp;</div>
<h3>Get Job By Email</h3>
Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque</li>
	<li><div class="searchProfile">&nbsp;</div>
<h3>Create Your Profile</h3>
Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque</li>
	<li><div class="searchCv">&nbsp;</div>
<h3>Upload your CV</h3>
Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque</li>
</ul>
<div class="clear"></div>
</div>


<div class="otherpages">
<div class="w49 fl">
<h3>Take Control of Your Career</h3>
<p>Find thousands of relevant articles on resume advvic, job-hunting guidance, caree planning tips and more. </p>
<ul>
<li><a href="" tilte="">Resume Critique Checklist</a></li>
<li><a href="" tilte="">Six Steps to Handling Money Questions</a></li>
<li><a href="" tilte="">High-Paying Jobs for Gen Y</a></li>
</ul>

</div>

<div class="w49 fr">

<h3>Take Control of Your Career</h3>
<p>Questions & Answers powerd by Yahoo! Answers</p>
<ul>
<li><a href="" tilte="">Is this normal behavior for an interviewer?</a></li>
<li><a href="" tilte="">How can i get a better job with little exp ?</a></li>
<li><a href="" tilte="">What should i wer to interview ?</a></li>
<li><a href="" tilte="">10 Tips to Rehearse for a Job Interview</a></li>
</ul>

</div>

</div>




		</div><!-- end section_content -->

	</div><!-- end section -->

	<div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar(); ?>

