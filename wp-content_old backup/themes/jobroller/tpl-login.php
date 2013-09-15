<?php
// Template Name: Login

	$redirect = $action = $role = '';

	// set a redirect for after logging in
	if ( isset( $_REQUEST['redirect_to'] ) ) {
		$redirect = $_REQUEST['redirect_to'];
	}

	if ( 'yes' == get_option( 'jr_allow_recruiters' ) )
		$employer_recruiter = __( 'Employer/Recruiter', APP_TD );
	else
		$employer_recruiter = __( 'Employer', APP_TD );
?>

	<div class="section">

    	<div class="section_content">

			<h1><?php _e('Login/Register', APP_TD); ?></h1>

			<?php do_action( 'appthemes_notices' ); ?>

			<?php if (get_option('jr_allow_job_seekers')=='yes') { ?>

			<p><?php _e('You must login or create an account in order to post a job or submit your resume.', APP_TD); ?></p>

				<ul>
					<li><?php echo sprintf( __( 'As a <strong>%s</strong> you\'ll be able to submit your profile, post your resume, and be found by employers.', APP_TD ), __( 'Job Seeker', APP_TD ) ); ?></li>
					<li><?php echo sprintf( __( 'As an <strong>%s</strong> you will be able to submit, relist, view and remove your job listings.', APP_TD ), $employer_recruiter ); ?></li>
				</ul>

			<?php } else { ?>

				<p><?php _e('You must login or create an account in order to post a job &ndash; this will enable you to view, remove, or relist your listing in the future.', APP_TD); ?></p>

			<?php } ?>

		    <div class="col-1">

		        <?php jr_register_form( $redirect, $role ); ?>

		    </div>

		    <div class="col-2">

				<?php jr_login_form( $action, $redirect ); ?>

		    </div>

			<div class="clear"></div>

    	</div><!-- end section_content -->

		<div class="clear"></div>

	</div><!-- end section -->

    <div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar('page'); ?>
