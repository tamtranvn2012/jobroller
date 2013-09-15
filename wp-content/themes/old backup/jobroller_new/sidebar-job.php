<div id="sidebar">

	<ul class="widgets">
	
		<?php appthemes_before_sidebar_widgets(); ?>
		
		<?php get_template_part('includes/sidebar-nav'); ?>
		
		<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar_job')) : else : ?>

			<!-- no dynamic sidebar so don't do anything -->

		<?php endif; ?>
		
		<?php appthemes_after_sidebar_widgets(); ?>

	</ul>

</div><!-- end sidebar -->
