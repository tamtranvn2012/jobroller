<div id="sidebar">

	<ul class="widgets">
	
		<?php appthemes_before_sidebar_widgets(); ?>
		
		

		<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar_main')) : else : ?>

			<!-- no dynamic sidebar setup -->

		<?php endif; ?>
		
		<?php appthemes_after_sidebar_widgets(); ?>

	</ul>

</div><!-- end sidebar -->