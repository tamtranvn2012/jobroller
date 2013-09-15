<?php
	// Template Name: Edit Job Template

	// retrieve the job details for new / edit / relist
	$job = jr_get_default_job_to_edit();

	if ( ! $job->ID ) {
		wp_redirect( home_url() );
		exit();
	}

	$params = array(
		'step' => 1,
		'job' => $job,
		'order_id'	=> 0,
		'post_action' => 'edit-job',
		'form_action'	=> $_SERVER['REQUEST_URI'],
		'submit_text' 	=> __( 'Save &rarr;' , APP_TD ),
	);

?>
	<div class="section">
	
		<div class="section_content">
			<h1><?php echo sprintf( __( 'Editing %s', APP_TD ), html_link( get_permalink( $job->ID ), get_the_title( $job->ID ) ) ); ?></h1>

			<?php do_action( 'appthemes_notices' ); ?>

			<?php appthemes_load_template( '/includes/forms/submit-job/submit-job-form.php', $params ); ?>

		</div><!-- end section_content -->

	</div><!-- end section -->

	<div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar('submit'); ?>