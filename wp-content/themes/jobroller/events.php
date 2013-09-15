<?php
/*
Template Name: events
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
			
			

	
		<div class="opendiv">

<h1>Events</h1>

<?php

query_posts('page_id=24');

while(have_posts()): the_post();

echo the_content();

endwhile;

wp_reset_query();

?>

</div> <!– close div –>








		</div><!-- end section_content -->

	</div><!-- end section -->

	<div class="clear"></div>
    </div>

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar(); ?>

