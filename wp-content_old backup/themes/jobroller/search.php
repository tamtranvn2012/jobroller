<?php if (isset($_GET['resume_search']) && $_GET['resume_search']) : get_template_part('search-resume'); return; endif; ?>

<?php 
	$location = '';
	if ( isset($_GET['location']) ){
		$location = wp_strip_all_tags($_GET['location']);
		$location = urldecode(utf8_uri_encode( trim($location) ));
	}
?>

<?php get_header('search'); ?>
	
<?php do_action('jobs_will_display'); ?>

	<div class="section">

		<?php
		// Global so we can pass it on to the filter-process.php file
		global $find_posts_in, $search_result_ids, $wp_query, $query_string;

		$term_heading = '';
		$location_heading = '';

		$search = get_search_query();

		$radius = isset( $_GET['radius'] ) ? absint( $_GET['radius'] ) : 0;

		if ($search) :
			$search_result_ids[] = 0;

			if ($wp_query->posts) foreach ($wp_query->posts as $p) : $search_result_ids[] = $p->ID.''; endforeach;

			$term_heading = __('Searching for ',APP_TD).'&ldquo;'.$search.'&rdquo; ';
		endif;

		if ($location) :
			// Get address from post data
			$address_array = '';
			
			if (isset($_REQUEST['latitude']) && $_REQUEST['latitude'] && $_REQUEST['longitude'] && $_REQUEST['full_address']) :
				$address_array = array(
					'north_east_lng' 	=> trim(stripslashes( $_REQUEST['north_east_lng'] )),
					'south_west_lng'	=> trim(stripslashes( $_REQUEST['south_west_lng'] )),
					'north_east_lat' 	=> trim(stripslashes( $_REQUEST['north_east_lat'] )),
					'south_west_lat'	=> trim(stripslashes( $_REQUEST['south_west_lat'] )),
					'longitude' 	=> trim(stripslashes( $_REQUEST['longitude'] )),
					'latitude' 		=> trim(stripslashes( $_REQUEST['latitude'] )),
					'full_address' 	=> trim(stripslashes( $_REQUEST['full_address'] ))
				);
			endif;

			// Do radial search
			$radial_result = jr_radial_search($location, $radius, $address_array);
			if ( is_array($radial_result) ) :
				if ( $radial_result['address'] ) $location = $radial_result['address'];
				$find_posts_in = $radial_result['posts'];
				$radius = $radial_result['radius'];
			endif;

			if ( !$radius )
				$radius = 50;
			
			if ( get_option('jr_distance_unit') == 'km' )
				$format = __( 'Jobs within %s kilometers of %s', APP_TD );
			else
				$format = __( 'Jobs within %s miles of %s', APP_TD );

			$location_heading = sprintf( $format, $radius, $location );
		
		endif;

		if ( !$term_heading && !$location_heading )
			$term_heading = __('Search Results', APP_TD);
		?>

		<h1 class="pagetitle"><?php echo $term_heading.$location_heading; ?> <?php if ( $paged>1 ) : ?>(<?php _e('page',APP_TD); ?> <?php echo number_format_i18n( $paged ); ?>)<?php endif; ?></h1>

		<?php
		$main_wp_query = $wp_query;

		$args = jr_filter_form();
		query_posts($args);
		?>

		<?php appthemes_load_template( 'loop-job.php', array( 'main_wp_query' => $main_wp_query ) ); ?>

		<?php jr_paging(); ?>

		<div class="clear"></div>

	</div><!-- end section -->
	
	<?php do_action('after_search_results'); ?>

	<div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar(); ?>
