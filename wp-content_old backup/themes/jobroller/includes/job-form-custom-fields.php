<div id="job-form-custom-fields">
<?php
	if ( $job->category ) {
		the_listing_files_editor( $job->ID );

		jr_render_job_form( (int) $job->category, $job->ID );
	}
?>
</div>