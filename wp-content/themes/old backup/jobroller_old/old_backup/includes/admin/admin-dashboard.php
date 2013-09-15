<?php

class JR_Admin_Dashboard extends APP_DashBoard{

	const SUPPORT_FORUM = 'http://forums.appthemes.com/external.php?type=RSS2';

	public function __construct(){

		parent::__construct( array(
			'page_title' => __( 'JobRoller Dashboard', APP_TD ),
			'menu_title' => __( 'JobRoller', APP_TD )
		) );

		add_filter( 'post_clauses', array( $this, 'filter_past_days' ), 10, 2 );

		$this->boxes[] = array( 'stats_30_days', $this->box_icon( 'chart-bar.png' ) . __( 'Stats - Last 30 Days', APP_TD ), 'side', 'high' );
		$this->boxes[] = array( 'support_forum', $this->box_icon( 'comments.png' ) . __( 'Support Forum', APP_TD ), 'normal', 'low' );

		$stats_icon = $this->box_icon( 'chart-bar.png' );
		$stats = array( 'stats', $stats_icon .  __( 'JobRoller Info', APP_TD ), 'normal' );
		array_unshift( $this->boxes, $stats );

	}

	public function stats_box(){
		
		$users = array();
		$users_stats = $this->get_user_counts();

		$totals[ __( 'Total Users', APP_TD ) ] = array(
			'text' => $users_stats['total_users'],
			'url' => 'users.php'
		);
?>
		<div class="stats_overview">
			<h3><?php _e('New Registrations', APP_TD) ?></h3>
			<div class="overview_today">
				<p class="overview_day"><?php _e('Today', APP_TD) ?></p>
				<p class="overview_count"><?php echo $users_stats['job_listers_today']; ?></p>
				<p class="overview_type"><em><?php _e('Job Listers', APP_TD); ?></em></p>
				<p class="overview_count"><?php echo $users_stats['job_seekers_today']; ?></p>
				<p class="overview_type_seek"><em><?php _e('Job Seekers', APP_TD) ?></em></p>
				<?php if ( 'yes' == get_option( 'jr_allow_recruiters' ) ): ?>
					<p class="overview_count"><?php echo $users_stats['recruiters_today']; ?></p>
					<p class="overview_type_recruiter"><em><?php _e('Recruiters', APP_TD) ?></em></p>
				<?php endif; ?>
			</div>

			<div class="overview_previous">
				<p class="overview_day"><?php _e('Yesterday', APP_TD) ?></p>
				<p class="overview_count"><?php echo $users_stats['job_listers_yesterday']; ?></p>
				<p class="overview_type"><em><?php _e('Job Listers', APP_TD); ?></em></p>
				<p class="overview_count"><?php echo $users_stats['job_listers_yesterday']; ?></p>
				<p class="overview_type_seek"><em><?php _e('Job Seekers', APP_TD) ?></em></p>
				<?php if ( 'yes' == get_option( 'jr_allow_recruiters' ) ): ?>
					<p class="overview_count"><?php echo $users_stats['recruiters_yesterday']; ?></p>
					<p class="overview_type_recruiter"><em><?php _e('Recruiters', APP_TD) ?></em></p>
				<?php endif; ?>
			</div>
		</div>
<?php

		$stats = array();

		$listings = $this->get_listing_counts();

		$totals[ __( 'Total Jobs', APP_TD ) ] = array(
			'text' => $listings['all'],
			'url' => 'edit.php?post_type='.APP_POST_TYPE
		);

		$this->output_list( $totals );

		$stats[ __( 'New Jobs (last 7 days)', APP_TD ) ] = $listings['new'];
		if( isset( $listings['publish'] ) ){
			$stats[ __( 'Total Live Jobs', APP_TD ) ] = array(
				'text' => $listings['publish'],
				'url' => 'edit.php?post_type='.APP_POST_TYPE.'&post_status=publish'
			);
		} else {
			$stats[ __( 'Total Live Jobs', APP_TD ) ] = 0;
		}
		if( isset( $listings['pending'] ) ){
			$stats[ __( 'Total Pending Jobs', APP_TD ) ] = array(
				'text' => $listings['pending'],
				'url' => 'edit.php?post_type='.APP_POST_TYPE.'&post_status=pending'
			);
		} else {
			$stats[ __( 'Total Pending Jobs', APP_TD ) ] = 0;
		}

		$resumes = $this->get_resumes_counts();
		
		if( isset( $resumes['publish'] ) ){
			$stats[ __( 'Total Live Resumes', APP_TD ) ] = array(
				'text' => $resumes['publish'],
				'url' => 'edit.php?post_type=' . APP_POST_TYPE_RESUME
			);
		} else {
			$stats[ __( 'Total Live Resumes', APP_TD ) ] = 0;
		}

		if ( current_theme_supports( 'app-payments' ) ){
			$orders = $this->get_order_counts();
			$stats[ __( 'Revenue (7 days)', APP_TD ) ] = appthemes_get_price( $orders['revenue'] );
			$stats[ __( 'Total Revenue', APP_TD ) ] = appthemes_get_price( array_sum( jr_daily_orders_sales() ) );
		}

		$stats[ __( 'Product Version', APP_TD ) ] = JR_VERSION;
		$stats[ __( 'Product Support', APP_TD ) ] = html( 'a', array( 'href' => 'http://forums.appthemes.com' ), __( 'Forum', APP_TD ) );
		$stats[ __( 'Product Support', APP_TD ) ] .= ' | ' . html( 'a', array( 'href' => 'http://www.appthemes.com/support/docs' ), __( 'Documentation', APP_TD ) );

		$this->output_list( $stats );

	}

	function stats_30_days_box() {
		echo '<div class="statsico">';
		jr_dashboard_charts();
		echo '</div>';
	}

	function support_forum_box() {
		global $app_forum_rss_feed;
		echo '<div class="forumico">';
		wp_widget_rss_output( self::SUPPORT_FORUM, array( 'items' => 5, 'show_author' => 0, 'show_date' => 1, 'show_summary' => 1 ) );
		echo '</div>';
	}

	private function output_list( $array, $begin = '<ul>', $end = '</ul>', $echo = true ){
		
		$html = '';
		foreach( $array as $title => $value ){
			if( is_array( $value ) ){
				$html .= '<li>' . $title . ': <a href="' . $value['url'] . '">' . $value['text'] . '</a></li>';
			}else{
				$html .= '<li>' . $title . ': ' . $value . '</li>';
			}
		}

		$html = $begin . $html . $end;

		$html = html( 'div', array( 'class' => 'stats-info' ), $html );

		if( $echo ) 
			echo $html;
		else
			return $html;

	}

	private function get_user_counts(){

		$users = (array) count_users();
		
		global $wpdb;
		$capabilities_meta = $wpdb->prefix . 'capabilities';
		$date_today = date( 'Y-m-d' );
		$date_yesterday = date( 'Y-m-d', strtotime('-1 days') );

		$users['job_listers_today'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->users INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key = %s AND ($wpdb->usermeta.meta_value NOT LIKE %s) AND $wpdb->usermeta.meta_value LIKE %s AND $wpdb->users.user_registered >= %s", $capabilities_meta, '%administrator%', '%job_lister%', $date_today ) );
		$users['job_listers_yesterday'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->users INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key = %s AND ($wpdb->usermeta.meta_value NOT LIKE %s) AND $wpdb->usermeta.meta_value LIKE %s AND $wpdb->users.user_registered BETWEEN %s AND %s", $capabilities_meta, '%administrator%', '%job_lister%', $date_yesterday, $date_today ) );

		$users['recruiters_today'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->users INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key = %s AND ($wpdb->usermeta.meta_value NOT LIKE %s) AND $wpdb->usermeta.meta_value LIKE %s AND $wpdb->users.user_registered >= %s", $capabilities_meta, '%administrator%', '%recruiter%', $date_today ) );
		$users['recruiters_yesterday'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->users INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key = %s AND ($wpdb->usermeta.meta_value NOT LIKE %s) AND $wpdb->usermeta.meta_value LIKE %s AND $wpdb->users.user_registered BETWEEN %s AND %s", $capabilities_meta, '%administrator%', '%recruiter%', $date_yesterday, $date_today ) );

		$users['job_seekers_today'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->users INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key = %s AND ($wpdb->usermeta.meta_value NOT LIKE %s) AND $wpdb->usermeta.meta_value LIKE %s AND $wpdb->users.user_registered >= %s", $capabilities_meta, '%administrator%', '%job_seeker%', $date_today ) );
		$users['job_seekers_yesterday'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->users INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key = %s AND ($wpdb->usermeta.meta_value NOT LIKE %s) AND $wpdb->usermeta.meta_value LIKE %s AND $wpdb->users.user_registered BETWEEN %s AND %s", $capabilities_meta, '%administrator%', '%job_seeker%', $date_yesterday, $date_today ) );

		return $users;
	}

	private function get_listing_counts(){

		$listings = (array) wp_count_posts( APP_POST_TYPE );

		$all = 0;
		foreach( (array) $listings as $type => $count ){
			$all += $count;
		}
		$listings['all'] = $all;

		$yesterday_posts = new WP_Query( array(
			'post_type' => APP_POST_TYPE,
			'past_days' => 7
		) );
		$listings['new'] = $yesterday_posts->post_count;

		return $listings;

	}

	private function get_resumes_counts(){

		$resumes = (array) wp_count_posts( APP_POST_TYPE_RESUME );

		$all = 0;
		foreach( (array) $resumes as $type => $count ){
			$all += $count;
		}
		$resumes['all'] = $all;

		return $resumes;

	}

	private function get_order_counts( $args = array() ){

		$orders = (array) wp_count_posts( APPTHEMES_ORDER_PTYPE );

		$week_orders = new WP_Query( array(
			'post_type' => APPTHEMES_ORDER_PTYPE,
			'post_status' => array( APPTHEMES_ORDER_COMPLETED, APPTHEMES_ORDER_ACTIVATED ),
			'past_days' => 7,
		) );

		$revenue = 0;
		foreach( $week_orders->posts as $post ){
			// payments framework meta key
			$revenue += (float) get_post_meta( $post->ID, 'total_price', true );
		}

		$orders['revenue'] = $revenue;
		return $orders;

	}

	public function filter_past_days( $clauses, $wp_query ){
		global $wp_query;

		$past_days = intval( $wp_query->get( 'past_days' ) );
		if( $past_days ){
			$clauses['where'] .= ' AND post_data > \'' . date( 'Y-m-d', strtotime( '-' . $past_days .' days' ) ) . '\'';
		}

		return $clauses;

	}


}

new JR_Admin_Dashboard;
