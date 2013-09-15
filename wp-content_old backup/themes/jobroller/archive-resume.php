<?php
	jr_resume_page_auth(); 
	
	get_header('resume-search');
?>
    <div class="section">

		<?php do_action( 'appthemes_notices' ); ?>
		
		<?php if (jr_resume_is_visible()) : ?>
		
	        <h1 class="pagetitle"><?php 
	        	_e('Resumes', APP_TD); 
	
	        	if ( is_tax( 'resume_category' ) ) :
	        		
	        		$slug = get_query_var('resume_category');
			  		$term = get_term_by( 'slug', $slug, 'resume_category');
			  		echo sprintf( __(' in the %s category.', APP_TD), $term->name);
	        		
	        	elseif ( is_tax( 'resume_languages' ) ) :
	        	
	        		$slug = get_query_var('resume_languages');
			  		$term = get_term_by( 'slug', $slug, 'resume_languages');
			  		echo sprintf( __(' of people who speak %s.', APP_TD), $term->name);
	        	
	        	elseif ( is_tax( 'resume_interests' ) ) :
	        	
	        		$slug = get_query_var('resume_interests');
			  		$term = get_term_by( 'slug', $slug, 'resume_interests');
			  		echo sprintf( __(' of people interested in %s.', APP_TD), $term->name);
	        	
	        	elseif ( is_tax( 'resume_groups' ) ) :
	        	
	        		$slug = get_query_var('resume_groups');
			  		$term = get_term_by( 'slug', $slug, 'resume_groups');
			  		echo sprintf( __(' of members of %s.', APP_TD), $term->name);
	        	
	        	elseif ( is_tax( 'resume_specialities' ) ) :
	        	
	        		$slug = get_query_var('resume_specialities');
			  		$term = get_term_by( 'slug', $slug, 'resume_specialities');
			  		echo sprintf( __(' of people specialising in %s.', APP_TD), $term->name);
	        	
	        	elseif ( is_tax( 'resume_job_type' )) :
	        		
	        		$slug = get_query_var('resume_job_type');
			  		$term = get_term_by( 'slug', $slug, 'resume_job_type');
			  		echo sprintf( __(' of people wanting a %s job.', APP_TD), $term->name);
	        		
	        	endif;
	        ?></h1>
	
	        <?php get_template_part( 'loop', 'resume' ); ?>
	
	        <?php jr_paging(); ?>
        
        <?php else : ?>
        	
        	<h1 class="pagetitle"><?php _e('Resumes', APP_TD); ?></h1>

			<?php if ( jr_current_user_can_subscribe_for_resumes() ) :

        		if ( $notice = get_option('jr_resume_subscription_notice') ) echo '<p>'.wptexturize($notice).'</p>';

				the_resume_purchase_plan_link();

        	else :

				jr_no_access_permission( __('Sorry, you do not have permission to Browse or View Resumes.', APP_TD ) );

        	endif; ?>

        <?php endif; ?>

        <div class="clear"></div>

    </div><!-- end section -->

    <div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar('resume'); ?>
