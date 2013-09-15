<?php
/*
Template Name: Blog Template
*/
?>

<?php
	### Disabled blog check
	if (get_option('jr_disable_blog')=='yes') :
		wp_redirect(get_bloginfo('url'));
		exit;
	endif;
?>

	<div class="section">

	    <?php $args = array( 'paged'=> $paged ); query_posts($args); ?>

		<?php get_template_part( 'loop' ); ?>

		<?php jr_paging(); ?>

		<div class="clear"></div>

	</div><!-- End section -->

	<div class="clear"></div>

</div><!-- end main content -->

<?php get_sidebar('blog'); ?>
