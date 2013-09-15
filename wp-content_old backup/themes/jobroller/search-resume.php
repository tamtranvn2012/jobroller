<?php 
	jr_resume_page_auth(); 
	
	if (!jr_resume_is_visible()) :
		wp_redirect( get_post_type_archive_link('resume') ); 
		exit;
	endif;

	$location = '';
	if ( isset($_GET['location']) ){
		$location = wp_strip_all_tags($_GET['location']);
		$location = urldecode(utf8_uri_encode( trim($location) ));
	}

?>
	<?php get_header('resume-search'); ?>

	<div class="section">

		<?php
		global $wp_query, $query_string, $find_posts_in;

		if ( get_query_var('paged') )
			$paged = get_query_var('paged');
		elseif ( get_query_var('page') )
			$paged = get_query_var('page');
		else
			$paged = 1;

		$term_heading = '';
		$find_posts_in = '';

		$search = get_search_query();
		$radius = isset( $_GET['radius'] ) ? absint( $_GET['radius'] ) : 0;
		
		if ($search) :
			$term_heading = sprintf( __('Searching resumes for &ldquo;%s&rdquo; ', APP_TD), get_search_query());
		else :
			$term_heading = __('Searching resumes ', APP_TD);
		endif;
		
		if ($location) :
			
			$radial_result = jr_radial_search($location, $radius);
			if (is_array($radial_result)) :
				if ($radial_result['address']) $location = $radial_result['address'];
				$find_posts_in = $radial_result['posts'];
				$radius = $radial_result['radius'];
			endif;

			if ( !$radius )
				$radius = 50;

			$term_heading .= __('within ',APP_TD).' '.$radius;
			if (get_option('jr_distance_unit')=='km') $term_heading .= 'km '; else $term_heading .= ' Miles '; 
			$term_heading .= __('of',APP_TD).' '.ucwords($location);
			
			$find_posts_in[] = 0;
			
		endif;
		
 		if (is_array($find_posts_in)) :
	 		$args = array_merge( $wp_query->query,
		 		array(
					'post_type' => APP_POST_TYPE_RESUME,
					'post__in' => $find_posts_in
				)
			);
		else :
			 $args = array_merge( $wp_query->query,
		 		array(
					'post_type' => APP_POST_TYPE_RESUME
				)
			);
		endif;

		$args['posts_per_page'] = jr_get_resumes_per_page();

		query_posts( $args );
		?>
		
		<h1 class="pagetitle"><?php echo $term_heading; ?> <?php if ($paged>1) : ?>(<?php _e('page',APP_TD); ?> <?php echo number_format_i18n( $paged ); ?>)<?php endif; ?></h1>

		<?php get_template_part( 'loop', 'resume' ); ?>

        <?php jr_paging(); ?>

        <div class="clear"></div>

    </div><!-- end section -->

    <div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar('resume'); ?>
