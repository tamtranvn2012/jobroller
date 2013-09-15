<?php
	get_header('search');

	$tax = get_queried_object();

	do_action('jobs_will_display');

	do_action('before_jobs_taxonomy', $tax->taxonomy, $tax->slug);
?>

	<div class="section">

		<h1 class="pagetitle"><?php echo '<small class="rss"><a href="'.jr_get_current_url().'rss"><img src="'.get_bloginfo('template_url').'/images/feed.png" title="'.single_cat_title("", false).' '.__('Jobs RSS Feed',APP_TD).'" alt="'.single_cat_title("", false).' '.__('Jobs RSS Feed',APP_TD).'" /></a></small>'; ?> 

		<?php if ( in_array( $tax->taxonomy, array(APP_TAX_CAT, APP_TAX_TYPE) ) ) { ?>

			<?php echo wptexturize($tax->name); ?> <?php _e('Jobs',APP_TD); ?>
			<?php if ( APP_TAX_CAT == $tax->taxonomy && isset($_GET['action']) && $_GET['action']=='Filter') echo '<small>&mdash; <a href="'.jr_get_current_url().'">'.__('Remove Filters',APP_TD).'</a></small>'; ?>

		<?php } elseif (  APP_TAX_SALARY == $tax->taxonomy ) { ?>

			<?php _e('Jobs with a salary of',APP_TD); ?> <?php echo wptexturize($tax->name); ?>

		<?php } elseif ( APP_TAX_TAG == $tax->taxonomy ) { ?>

			<?php _e('Jobs tagged',APP_TD); ?> &ldquo;<?php echo wptexturize($tax->name); ?>&rdquo;

		<?php } ?>

		</h1>

		<?php
			$main_wp_query = $wp_query;

			$args = jr_filter_form();
			$args = array_merge(
				array(
					$tax->taxonomy 	=> $tax->slug,
					'post_type' => APP_POST_TYPE,
					'post_status' 	=> 'publish'
				),
				$args
			);
			$args = apply_filters('jr_taxonomy_filter', $args, $tax);

			query_posts($args);
		?>

		<?php appthemes_load_template( 'loop-job.php', array( 'main_wp_query' => $main_wp_query ) ); ?>

		<?php jr_paging(); ?>

		<div class="clear"></div>

	</div><!-- end section -->

	<?php do_action('after_jobs_taxonomy', $tax->taxonomy, $tax->slug); ?>

	<div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar(); ?>
