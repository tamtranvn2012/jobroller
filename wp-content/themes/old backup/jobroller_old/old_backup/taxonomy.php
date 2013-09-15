<?php
	if ( 'resume' == get_post_type() || is_tax('resume_specialities') || is_tax('resume_groups') || is_tax('resume_languages') || is_tax('resume_category') || is_tax('resume_job_type') ) get_template_part('archive-resume');
	else get_template_part('archive');