<?php
/*
Template Name: Jobs by date
*/
?>

<?php
$show = '';
if ( isset($_GET['show']) ) $show = $_GET['show'];
if ( ! $show ) wp_redirect( get_bloginfo('url') );
?>

<?php 
	do_action( 'jobs_will_display' );

	do_action( 'before_jobs_by_date', 'date', $show );
?>

	<div class="section">

		<?php
		$dateargs = array();
		$title = '';
		$today = getdate();
		
		switch ($show) :
			case "week" :
				$title = __('This weeks Jobs',APP_TD);
				$dateargs = array( 'year' => $today["year"], 'w' => date('W') );
			break;
			case "lastweek" :
				$title = __('Last weeks Jobs',APP_TD);
				$week = date('W');
				$year = $today["year"];
				if ($week==0) :
					$week = 53;
					$year = $year-1;
				else :
					$week = $week-1;
				endif;
				$dateargs = array( 'year' => $year, 'w' => $week );
			break;
			case "today" :
				$title = __('Todays Jobs',APP_TD);
				$dateargs = array( 'year' => $today["year"], 'monthnum' => $today["mon"], 'day' => $today["mday"] );
			break;
			case "month" :
				$title = __('This Months Jobs',APP_TD);
				$dateargs = array( 'year' => $today["year"], 'monthnum' => $today["mon"] );
			break;
		endswitch;

		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

		$main_wp_query = $wp_query;

		$args = array(
			'post_type'	=> APP_POST_TYPE,
			'post_status' => 'publish',
			'ignore_sticky_posts'	=> 1,
			'paged'=>$paged
		);
		$args = array_merge($dateargs, $args);
		query_posts($args);
		?>

		<h1 class="pagetitle"><?php echo $title; ?></h1>

		<?php appthemes_load_template( 'loop-job.php', array( 'main_wp_query' => $main_wp_query ) );  ?>

		<?php jr_paging(); ?>
		
		<div class="clear"></div>

	</div><!-- end section -->

	<?php do_action( 'after_jobs_by_date', 'date',  $show ); ?>

	<div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar(); ?>
