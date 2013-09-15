<?php
/**
 * Module for gathering post view statistics.
 */



/**
 * Prints total and daily page views for a post.
 *
 * @param int The post ID
 */
function appthemes_stats_counter( $post_id ) {

	if ( ! current_theme_supports( 'app-stats' ) )
		return false;

	$today = appthemes_get_stats_by( $post_id, 'today' );
	$total = appthemes_get_stats_by( $post_id, 'total' );

	if ( $total > 0 )
		printf( __( '%d total views, %d today', APP_TD ), $total, $today );
	else
		_e( 'No views yet', APP_TD );

}


/**
 * Records page views for a post.
 *
 * @param int The post ID
 * @return bool Whether the stats get updated
 */
function appthemes_stats_update( $post_id ) {
	global $wpdb;

	if ( ! current_theme_supports( 'app-stats' ) )
		return false;

	list( $options ) = get_theme_support( 'app-stats' );

	$today_date = date( 'Y-m-d', current_time( 'timestamp' ) );

	$post = get_post( $post_id );
	if ( ! $post || $post->post_author == get_current_user_id() )
		return false;

	// first try and update the existing total post counter
	$update = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->app_stats_total SET postcount = postcount+1 WHERE postnum = %d LIMIT 1", $post_id ) );

	// if it doesn't exist, then insert two new records
	// one in the total views, another in today's views
	if ( $update == 0 ) {
		$wpdb->insert( $wpdb->app_stats_total, array(
			'postnum' => $post_id,
			'postcount' => 1
		) );
		$wpdb->insert( $wpdb->app_stats_daily, array(
			'time' => $today_date,
			'postnum' => $post_id,
			'postcount' => 1
		) );
		// post exists so let's just update the counter
	} else {
		$update = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->app_stats_daily SET postcount = postcount+1 WHERE time = %s AND postnum = %s LIMIT 1", $today_date, $post_id ) );

		// insert a new record since one hasn't been created for current day
		if ( $update == 0 ) {
			$wpdb->insert( $wpdb->app_stats_daily, array(
				'time' => $today_date,
				'postnum' => $post_id,
				'postcount' => 1
			) );
		}
	}

	// get all the post view info so we can update meta fields
	$sql = $wpdb->prepare( "SELECT t.postcount AS total, d.postcount AS today FROM $wpdb->app_stats_total AS t INNER JOIN $wpdb->app_stats_daily AS d ON t.postnum = d.postnum WHERE t.postnum = %d AND d.time = %s ", $post_id, $today_date );
	$stats = $wpdb->get_row( $sql );

	// add the counters to temp values on the post so it's easy to call from the loop
	update_post_meta( $post_id, $options['meta_daily'], $stats->today );
	update_post_meta( $post_id, $options['meta_total'], $stats->total );
	
	return true;
}


/**
 * Collects statistical data for displayed posts.
 */
function appthemes_collect_stats() {
	global $wpdb, $posts, $pageposts, $wp_query, $stats_data;

	if ( ! current_theme_supports( 'app-stats' ) )
		return false;

	list( $options ) = get_theme_support( 'app-stats' );

	$today_date = date( 'Y-m-d', current_time( 'timestamp' ) );

	if ( isset( $posts ) && is_array( $posts ) )
		foreach ( $posts as $post )
			$post_ids[] = $post->ID;

	if ( isset( $pageposts ) && is_array( $pageposts ) )
		foreach ( $pageposts as $post )
			$post_ids[] = $post->ID;

	if ( isset( $wp_query->posts ) && is_array( $wp_query->posts ) )
		foreach ( $wp_query->posts as $post )
			$post_ids[] = $post->ID;

	if ( isset( $post_ids ) && is_array( $post_ids ) ) {
		$post_ids = array_unique( $post_ids );
		$post_list = implode( ",", $post_ids );
		if ( in_array( $options['cache'], array( 'today', 'both' ) ) )
			$todays = $wpdb->get_results( $wpdb->prepare( "SELECT postcount, postnum FROM $wpdb->app_stats_daily WHERE postnum IN ($post_list) AND time = %s", $today_date ) );
		if ( in_array( $options['cache'], array( 'total', 'both' ) ) )
			$totals = $wpdb->get_results( "SELECT postcount, postnum FROM $wpdb->app_stats_total WHERE postnum IN ($post_list)" );
	}

	if ( isset( $todays ) && is_array( $todays ) )
		foreach ( $todays as $today )
			$stats_data[ $today->postnum ]['today'] = $today->postcount;

	if ( isset( $totals ) && is_array( $totals ) )
		foreach ( $totals as $total )
			$stats_data[ $total->postnum ]['total'] = $total->postcount;

	if ( isset( $post_ids ) && is_array( $post_ids ) ) {
		foreach ( $post_ids as $post_id ) {
			if ( in_array( $options['cache'], array( 'today', 'both' ) ) )
				if ( ! isset( $stats_data[ $post_id ]['today'] ) )
					$stats_data[ $post_id ]['today'] = 0;
			if ( in_array( $options['cache'], array( 'total', 'both' ) ) )
				if ( ! isset( $stats_data[ $post_id ]['total'] ) )
					$stats_data[ $post_id ]['total'] = 0;
		}
	}

}


/**
 * Returns page views for a post.
 *
 * @param int $post_id The post ID
 * @param string $type Return total or daily page views
 * @return int Quantity of post views
 */
function appthemes_get_stats_by( $post_id, $type = 'total' ) {
	global $wpdb, $stats_data;

	if ( ! current_theme_supports( 'app-stats' ) )
		return false;

	list( $options ) = get_theme_support( 'app-stats' );

	$today_date = date( 'Y-m-d', current_time( 'timestamp' ) );

	if ( $type == 'today' ) {

		if ( isset( $stats_data[ $post_id ]['today'] ) )
			$counter = $stats_data[ $post_id ]['today'];
		else
			$counter = $wpdb->get_var( $wpdb->prepare( "SELECT postcount FROM $wpdb->app_stats_daily WHERE postnum = %d AND time = %s", $post_id, $today_date ) );

	} else {

		if ( isset( $stats_data[ $post_id ]['total'] ) )
			$counter = $stats_data[ $post_id ]['total'];
		elseif ( get_post_meta( $post_id, $options['meta_total'], true ) )
			$counter = get_post_meta( $post_id, $options['meta_total'], true );
		else
			$counter = $wpdb->get_var( $wpdb->prepare( "SELECT postcount FROM $wpdb->app_stats_total WHERE postnum = %d", $post_id ) );
	}

	if ( empty( $counter ) || ! is_numeric( $counter ) )
		$counter = 0;

	return $counter;
}


/**
 * Installs stats tables.
 */
function appthemes_install_stats_tables() {

	if ( ! current_theme_supports( 'app-stats' ) )
		return false;

	list( $options ) = get_theme_support( 'app-stats' );

	// create the daily page view counter table
	$sql = "
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time date NOT NULL DEFAULT '0000-00-00',
		postnum int(11) NOT NULL,
		postcount int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY  (id)";

	scb_install_table( 'app_stats_daily', $sql );


	// create the all-time page view counter table
	$sql = "
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		postnum int(11) NOT NULL,
		postcount int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY  (id)";

	scb_install_table( 'app_stats_total', $sql );

}


/**
 * Deletes all stats when the admin option has been selected.
 *
 * @return bool Whether the stats get reseted
 */
function appthemes_reset_stats() {
	global $wpdb;

	if ( ! current_theme_supports( 'app-stats' ) || ! current_user_can( 'manage_options' ) )
		return false;

	list( $options ) = get_theme_support( 'app-stats' );

	// empty both stats tables
	$wpdb->query( "TRUNCATE $wpdb->app_stats_daily ;" );
	$wpdb->query( "TRUNCATE $wpdb->app_stats_total ;" );

	// update post meta mirrors to 0 views
	$sql = $wpdb->prepare( "UPDATE $wpdb->postmeta SET meta_value = '0' WHERE meta_key = %s OR meta_key = %s", $options['meta_daily'], $options['meta_total'] );
	$wpdb->query( $sql );

	return true;
}


/**
 * Deletes all stats for individual listing,
 * called via ajax reset-stats action
 */
function appthemes_reset_stats_ajax() {
	global $wpdb;

	if ( ! current_theme_supports( 'app-stats' ) || ! current_user_can( 'manage_options' ) || ! isset( $_GET['post_id'] ) ) {
		$response = array( 'success' => false );
		die( json_encode( $response ) );
	}

	$post_id  = appthemes_numbers_only( $_GET['post_id'] );

	list( $options ) = get_theme_support( 'app-stats' );

	// empty stats from both tables
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->app_stats_daily WHERE postnum = '%d'", $post_id ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->app_stats_total WHERE postnum = '%d'", $post_id ) );

	// update post meta mirrors to 0 views
	update_post_meta( $post_id, $options['meta_daily'], '0' );
	update_post_meta( $post_id, $options['meta_total'], '0' );

	$response = array(
		'success' => true,
		'html' => html( 'span', array( 'class' => 'text' ), __( 'Stats has been reseted!', APP_TD ) ),
	);

	die( json_encode( $response ) );
}


/**
 * Prints reset listing stats link for admin, use only in loop.
 */
function appthemes_reset_stats_link() {
	global $post;

	if ( ! current_theme_supports( 'app-stats' ) || ! current_user_can( 'manage_options' ) || ! in_the_loop() )
		return;

?>
	<script type="text/javascript">
	// <![CDATA[
		jQuery(document).ready(function() {
			jQuery('a.reset-stats-link').click(function(){
				jQuery.ajax({
					url: '<?php echo esc_js( admin_url( 'admin-ajax.php', 'relative' ) ); ?>',
					beforeSend: function() {
						jQuery(this).parent().fadeOut('slow');
					},
					context: this,
					dataType: "json",
					data: {
						action: 'reset-stats',
						post_id: jQuery(this).data('rel')
					},
					success: function( data ) {
						if ( data.success ) {
							jQuery(this).parent().html(data.html).fadeIn('slow');
						}
					}
				});
				return false;
			});
		});
	// ]]>
	</script>
<?php
	$link = html( 'a', array( 'class' => 'reset-stats-link', 'href' => '#', 'data-rel' => $post->ID, 'title' => __( 'Reset listing statistics', APP_TD ) ), __( 'Reset stats', APP_TD ) );
	echo html( 'p', array( 'class' => 'edit' ), $link );
}

