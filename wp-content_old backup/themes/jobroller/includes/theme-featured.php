<?php

add_action( 'jobs_will_display','jr_display_featured_jobs' );

add_action( 'jr_prune_expired_featured', 'jr_prune_expired_featured' );
add_action( 'jr_addon_featured_ended', 'jr_remove_featured', 10, 2 );

add_filter( 'posts_clauses', 'jr_expired_featured_sql', 10, 2 );
add_filter( 'jr_featured_jobs_args', 'jr_sort_featured_jobs' );

add_filter( 'pre_get_posts', 'jr_handle_featured_rss_feed' );

function jr_is_listing_featured( $post_id, $main_wp_query = '' ) {

	$condition = true;
	foreach( _jr_featured_addons() as $addon ){

		if ( JR_ITEM_FEATURED_CAT == $addon && ! $main_wp_query->is_tax( APP_TAX_CAT ) )
			continue;

		if ( JR_ITEM_FEATURED_LISTINGS == $addon && ( $main_wp_query->is_tax( APP_TAX_CAT ) || ( ! $main_wp_query->is_search( APP_POST_TYPE ) && ! $main_wp_query->is_archive( APP_POST_TYPE ) && ! $main_wp_query->is_front_page() ) ) )
			continue;

		$condition = _jr_is_active_featured( $post_id, $addon );
	}
	return apply_filters( 'jr_job_is_featured', $condition, $post_id, $main_wp_query );

}

function _jr_is_active_featured( $post_id, $addon ) {
	$featured = get_post_meta( $post_id, $addon, true );
	$featured_start_date = get_post_meta( $post_id, $addon . '_start_date', true );
	if ( $featured && $featured_start_date && appthemes_days_between_dates( $featured_start_date ) <= 0 ) 
		return true;
	return false;
}

function jr_add_featured( $job_id, $addon, $duration, $start_date = '' ){
	if ( ! $start_date ) $start_date = current_time( 'mysql' );

	update_post_meta( $job_id, $addon , true );
	update_post_meta( $job_id, $addon .'_start_date', $start_date );
	update_post_meta( $job_id, $addon .'_duration', $duration );
}

function jr_remove_featured( $post_id, $addon ){
	delete_post_meta( $post_id, $addon );
	delete_post_meta( $post_id, $addon .'_start_date' );
	delete_post_meta( $post_id, $addon .'_duration' );
}

function jr_prune_expired_featured() {

	foreach( _jr_featured_addons() as $addon ){
		$expired_posts = new WP_Query( array(
			'post_type' => APP_POST_TYPE,
			'expired' => $addon,
			'nopaging' => true,
		) );
	
		foreach ( $expired_posts->posts as $post ) {
			do_action( 'jr_addon_featured_ended', $post->ID, $addon );
		}
	}
	
}

function jr_expired_featured_sql( $clauses, $wp_query ) {
	if ( $wp_query->get( 'expired' ) ) {
		foreach( _jr_featured_addons() as $addon ){
			if ( $addon == $wp_query->get( 'expired' ) ) {
				$clauses['join'] .= _jr_get_expired_sql_join( $addon );
				$clauses['where'] = _jr_get_expired_sql_where( $addon );
			}
		}
	}
	return $clauses;
}

function _jr_get_expired_sql_join( $addon ){
	global $wpdb;

	$output = '';
	$output .= " INNER JOIN " . $wpdb->postmeta ." AS duration ON (" . $wpdb->posts .".ID = duration.post_id)";
	$output .= " INNER JOIN " . $wpdb->postmeta ." AS start ON (" . $wpdb->posts .".ID = start.post_id)";

	return $output;
}

function _jr_get_expired_sql_where( $addon ){
	$where = 'AND (';
		$where .= 'duration.meta_key = \'' . $addon . '_duration\' AND ';
		$where .= 'start.meta_key = \'' . $addon . '_start_date\'';
		$where .= ' AND ';
		$where .= ' DATE_ADD( start.meta_value, INTERVAL duration.meta_value DAY ) < \'' . current_time( 'mysql' ) . '\'';
		$where .= ' AND duration.meta_value > 0 ';
	$where .= ") ";

	return $where;
}

function jr_sort_featured_jobs( $args ){
	global $app_abbr;

	$sort_method = get_option( $app_abbr . '_featured_jobs_sort' );

	switch( $sort_method ){
		case 'oldest':
			$args['orderby'] = 'date';
			$args['order'] = 'ASC';
			break;
		case 'random':
			$args['orderby'] = 'rand';
			break;
	}
	return $args;
}

/**
 * @return WP_Query instance, WP_Query args if displaying a RSS Feed, or null
 */
function jr_get_featured_jobs() {

	if ( ! _jr_display_featured_jobs() )
		return;

	$rss_feed = 0;

	// check for a feed
	if ( is_feed() && get_query_var('rss_featured') ) {
	
		if ( get_query_var('rss_job_cat') )
			$rss_feed = 2; // category feed
		else
			$rss_feed = 1; // frontpage/archive feed

	}

	$args = array(
		'post_type' 	=> APP_POST_TYPE,
		'paged' 		=> get_query_var( 'page' ),
		'post_status' 	=> 'publish',
		'posts_per_page'=> jr_get_featured_jobs_per_page(),
	);

	if ( is_tax( APP_TAX_CAT ) || 2 == $rss_feed ) {

		if ( $rss_feed )
			$terms = intval( get_query_var('rss_job_cat') );
		else
			$terms = get_queried_object_id();

		$meta_key = JR_ITEM_FEATURED_CAT;
		$args['tax_query'] = array(
			array(
				'taxonomy' => APP_TAX_CAT,
				'terms' => array( $terms ),
			)
		);
	} elseif ( ( is_front_page() || is_archive( APP_POST_TYPE ) ) || 1 == $rss_feed ) {
		$meta_key = JR_ITEM_FEATURED_LISTINGS;
	} 
	else {
		return;
	}

	// make sure to check the addon start date
	$args['meta_query'] = array(
		'relation' => 'AND',
		array(
			'key' => $meta_key,
			'value' => 1,
		),
		array(
			'key' => $meta_key . '_start_date',
			'value' => current_time( 'mysql' ),
			'type' => 'datetime',
			'compare' => '<='
		),
	);

	$args = apply_filters( 'jr_featured_jobs_args', $args );

	if ( $rss_feed ) return $args;

	$query = new WP_Query( $args );

	if ( !$query->have_posts() )
		return;

	return $query;
}


function _jr_display_featured_jobs() {
	// don't display top featured jobs on searches by default
	$condition = ! is_search( APP_POST_TYPE ) && ( is_front_page() || is_archive( APP_POST_TYPE ) || is_tax() || is_feed() );

	if ( get_query_var('paged') )
		$paged = get_query_var('paged');
	elseif ( get_query_var('page') )
		$paged = get_query_var('page');
	else
		$paged = 1;

	if ( $paged > 1 ) $condition = FALSE;

	return apply_filters( 'jr_display_featured_jobs', $condition );

}

function jr_display_featured_jobs() {

	if ( _jr_display_featured_jobs() ) {
		get_template_part('includes/featured-jobs');
	}

}

function jr_handle_featured_rss_feed( $wp_query ) {

	if ( $wp_query->is_feed && isset($_GET['rss_featured']) ) {
		$wp_query->set( 'rss_featured', intval($_GET['rss_featured']) );

		if ( isset($_GET['rss_job_cat']) )
			$wp_query->set( 'rss_job_cat', intval($_GET['rss_job_cat']) );

		$args = jr_get_featured_jobs();
		foreach ( $args as $arg => $value ) {
			$wp_query->set( $arg, $value );
		}

	}
	return $wp_query;
}
