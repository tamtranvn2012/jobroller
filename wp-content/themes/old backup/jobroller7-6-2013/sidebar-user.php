<div id="sidebar">

	<ul class="widgets">
	
		<?php appthemes_before_sidebar_widgets(); ?>
		
		<?php get_template_part( 'includes/sidebar-user' ); ?>

		<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar_user')) : else : ?>

			<!-- no dynamic sidebar setup -->

		<?php endif; ?>
		
		<?php appthemes_after_sidebar_widgets(); ?>

	</ul>

</div><!-- end sidebar -->