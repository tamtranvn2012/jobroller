<?php
	// Template Name: Submit Job Template

	if ( empty($params) ) {
		 $params = array();
	}

	$job = jr_get_default_job_to_edit();

	if ( get_query_var('job_relist') ) {
		$title = sprintf( __( 'Relisting %s', APP_TD ), html_link( get_permalink( $job ), get_the_title( $job->ID ) ) );
	} else {
		$title = __( 'Submit a Job', APP_TD );
	}

	$default_params = array(
		'step' 			=> jr_get_next_step(),
		'job' 			=> $job,
		'order_id'		=> get_query_var('order_id'),
		'post_action'	=> get_query_var('job_relist') ? 'relist-job' : 'new-job',
		'form_action'	=> $_SERVER['REQUEST_URI'],
		'submit_text'	=> __( 'Next &rarr;', APP_TD ),
	);
	$params = wp_parse_args( $params, $default_params );

	$step = $params['step'];
?>
	<div class="section">

		<div class="section_content">

			<h1><?php echo $title; ?></h1>

			<?php do_action( 'appthemes_notices' );	?>

			<?php 
				$steps = jr_steps();
				echo '<ol class="steps ' . ( count($steps) > 4 ? 'more-steps' : '' ) . '">';
				foreach ( $steps as $i => $value ) {
					echo '<li class="';
					if ($step==$i) { 
						echo 'current ';
						$template = $value['template'];
					}
					if (($step-1)==$i) echo 'previous ';
					if ($i<$step) echo 'done';
					echo '"><span class="';
					if ($i==1) echo 'first';
					if ($i==count($steps)) echo 'last';
					echo '">';
					echo $value['description'];
					echo '</span></li>';
				}
				echo '</ol><div class="clear"></div>';

				do_action('jr_before_step', $step );

				if ( 1 == $step ) {
			?>
					<p><?php _e('You must login or create an account in order to post a job &mdash; this will enable you to view, remove, or relist your listing in the future.', APP_TD); ?></p>
					<div class="col-1">
						<?php do_action( 'jr_display_register_form', get_permalink( $post->ID ), 'job_lister' );?>
					</div>
					<div class="col-2">
						<?php do_action( 'jr_display_login_form', get_permalink( $post->ID ), get_permalink( $post->ID ) ); ?>
					</div>
					<div class="clear"></div>
			<?php

				} else {
					// retrieve template
					appthemes_load_template( $template, $params );
				}

				do_action('jr_after_step', $step );
			?>


		</div><!-- end section_content -->

	</div><!-- end section -->

	<div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar('submit'); ?>
