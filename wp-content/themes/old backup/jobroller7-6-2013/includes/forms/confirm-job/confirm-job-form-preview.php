<?php
	$status = ( 'no' != _jr_moderate_jobs() ?  __( 'for approval', APP_TD ) : '' );
?>
	<p><?php echo sprintf( __( 'Your job is ready to be submitted, please confirm the details are correct and then click \'Confirm\' to submit your listing %s.', APP_TD ), $status ); ?></p>

	<blockquote>

		<h2><?php
			$job_type_name = jr_get_single_term( $job->ID, APP_TAX_TYPE )->name;
			echo wptexturize( $job_type_name ).' &ndash; '; 
			echo wptexturize( $job->post_title ); 
		?></h2>
		<?php if ($job->your_name) : ?>
		<h3><?php _e('Company/Poster',APP_TD); ?></h3>
		<p><?php
			if ( $job->website )
				echo '<a href="'. strip_tags( $job->website ).'">';
			echo strip_tags( $job->your_name );
			if ( $job->website )
				echo '</a>';
		?></p>
		<?php endif; ?>
		<h3><?php _e('Job description',APP_TD); ?></h3>
		<?php echo wpautop( wptexturize( $job->post_content ) ); ?>
		<?php if ( 'yes' == get_option('jr_submit_how_to_apply_display') ) : ?>
			<h3><?php _e( 'How to apply', APP_TD ); ?></h3>
			<?php echo wpautop( wptexturize( $job->apply ) ); ?>
		<?php endif; ?>

	</blockquote>
