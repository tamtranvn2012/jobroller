<?php
/*
Template Name: My Jobs Template
*/
?>

<?php
### Prevent Caching
nocache_headers();

appthemes_auth_redirect_login();
if ( !current_user_can('can_submit_job') ) 
	redirect_profile();

global $userdata, $user_ID, $message, $jr_options;

$myjobsID = JR_Dashboard_Page::get_id();

$pending_payment_jobs = _jr_pending_payment_jobs_for_user( $user_ID );

$can_subscribe = jr_current_user_can_subscribe_for_resumes();
?>
	<div class="section myjobs">

		<div class="section_content">
			<div class="divbg"><ul><li><span class="setting1"><a href="">Catapulture</a> </span></li><li><span class="setting2">My Deshboard</span></li></div>
		<div class="user"><h1><?php printf(__("%s ", APP_TD), ucwords($userdata->user_login)); ?></h1></div>
<p>&nbsp;</p>
<div class="clear"></div>
		<?php do_action( 'appthemes_notices' ); ?>

		<ul class="display_section">

			<?php do_action( 'jr_dashboard_tab_before', 'job_lister' ); ?>

			<li><a href="#live" class="noscroll"><?php _e('Dashboard', APP_TD); ?></a></li>
			<li><a href="#pending" class="noscroll"><?php _e('Resume / Attachments ', APP_TD); ?></a></li>
			<!--<li><a href="#ended" class="noscroll"><?php _e('Job Alerts', APP_TD); ?></a></li>
            <li><a href="#applications" class="noscroll"><?php _e('Applications', APP_TD); ?></a></li> -->
			<li><a href="#preferences" class="noscroll"><?php _e('Preferences', APP_TD); ?></a></li>
            <li><a href="#events" class="noscroll"><?php _e('Events', APP_TD); ?></a></li>
			<?php if ( 'pack' == $jr_options->plan_type && jr_charge_job_listings() ) : ?><li><a href="#packs" class="noscroll"><?php _e('Job Packs', APP_TD); ?></a></li><?php endif; ?>
			<?php if ( $can_subscribe ) : ?><li><a href="#subscriptions" class="noscroll"><?php _e('Subscriptions', APP_TD); ?></a>
			</li><?php endif; ?>
			<?php if ( jr_charge_job_listings() || $can_subscribe || jr_get_user_orders_count() > 0 ) : ?><li><a href="#orders" class="noscroll"><?php _e('Orders', APP_TD); ?></a></li><?php endif; ?>

			<?php do_action( 'jr_dashboard_tab_after', 'job_lister' ); ?>

		</ul>

		<div id="live" class="myjobs_section">

			<h2><?php _e('Starred Jobs', APP_TD); ?></h2>

			<?php
				global $user_ID;

				if ( get_query_var('tab') && 'live' == get_query_var('tab') ) {
					$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
				} else {
					$paged = 1;
				}

				$args = array(
						'ignore_sticky_posts'	=> true,
						'author' 				=> $user_ID,
						'post_type' 			=> APP_POST_TYPE,
						'post_status' 			=> 'publish',
						'posts_per_page' 		=> jr_get_jobs_per_page(),
						'paged' 				=> $paged,
				);
				$my_query = new WP_Query($args);
			?>
			<?php if ($my_query->have_posts()) : ?>

		    <?php while ($my_query->have_posts()) : ?>

					<?php $my_query->the_post(); ?>

					<?php if (get_post_meta($my_query->post->ID, 'jr_total_count', true)) $job_views = number_format(get_post_meta($my_query->post->ID, 'jr_total_count', true)); else $job_views = '-'; ?>

					<?php if (jr_check_expired($post)) continue; ?>
            
            
            
            <div class="dashboard">
            	<ul>
                	<li><div class="thumb"><?php if (has_post_thumbnail()) the_post_thumbnail(); ?></div><div class="plink"><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>

						</strong></div>
                        
                        <div class="excerpt"><?php the_excerpt(); ?></div>
                        
                        <div class="date">Posted <span><?php the_time(__('j M',APP_TD)); ?> <?php the_time(__('Y',APP_TD)); ?></span></div>
                
                		<div class="editjob">        
						<?php the_job_edit_link( $my_query->post->ID ); ?>
						<?php the_job_end_link( $my_query->post->ID ); ?>
						</div>
                        </li>
                </ul>
            </div>
            
            
            		
				<?php endwhile; ?>
            
            

			<?php jr_paging( $my_query, 'paged', array ( 'add_args' => array( 'tab' => 'live' ) ) ); ?>

			<?php else: ?>
				<p><?php _e('No live jobs found.',APP_TD); ?></p>
			<?php endif; ?>

		</div>

		<?php
		if ( 'pack' == $jr_options->plan_type && jr_charge_job_listings() ) :
			get_template_part( '/includes/dashboard-packs' );
		endif; 
		?>

		<div id="pending" class="myjobs_section">

			<h2><?php _e('Pending Jobs', APP_TD); ?></h2>

			<?php
				global $user_ID;

				if ( get_query_var('tab') && 'pending' == get_query_var('tab') ) {
					$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
				} else {
					$paged = 1;
				}

				$args = array(
					'ignore_sticky_posts'	=> true,
					'author' 				=> $user_ID,
					'post_type' 			=> APP_POST_TYPE,
					'post_status' 			=> array( 'pending', 'draft' ),
					'posts_per_page' 		=> jr_get_jobs_per_page(),
					'paged' 				=> $paged,
				);
				$my_query = new WP_Query($args);
			?>
			<?php if ($my_query->have_posts()) : ?>

			<p><?php _e('The following jobs are pending and are not visible to users.', APP_TD); ?></p>
			
			<table cellpadding="0" cellspacing="0" class="data_list footable">
				<thead>
					<tr>
						<th data-class="expand"><?php _e('Job Title',APP_TD); ?></th>
						<th class="center" data-hide="phone"><?php _e('Date Posted',APP_TD); ?></th>
						<th class="center" data-hide="phone"><?php _e('Status',APP_TD); ?></th>
						<th class="right" data-hide="phone"><?php _e('Actions',APP_TD); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
						<tr>
							<td>
							<?php
								// only users with 'edit_jobs' capability can preview pending jobs
								if ( current_user_can( 'edit_jobs', $post->ID ) ) { ?>
									<strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>
							<?php } else { ?>
									<strong><?php the_title(); ?></strong>
							<?php } ?>
							</td>
							<td class="date"><strong><?php the_time(__('j M',APP_TD)); ?></strong> <span class="year"><?php the_time(__('Y',APP_TD)); ?></span></td>
							<td class="center"><?php

								$can_edit = jr_allow_editing();

								$job_status = jr_get_job_status( $my_query->post, $pending_payment_jobs );

								if ( $order_status = jr_get_job_order_status( $my_query->post, $pending_payment_jobs ) )
									echo sprintf( ' %s', $order_status  );
								else
									echo sprintf( ' %s', $job_status );

							?></td>
							<td class="actions"><?php the_job_actions( $my_query->post, $pending_payment_jobs ); ?>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
			
			<?php jr_paging( $my_query, 'paged', array ( 'add_args' => array( 'tab' => 'pending' ) ) ); ?>

			<?php else : ?>
				<p><?php _e('No pending jobs found.', APP_TD); ?></p>
			<?php endif; ?>

		</div>
			
		<div id="ended" class="myjobs_section">

			<h2><?php _e('Ended/Expired Jobs', APP_TD); ?></h2>

			<?php
				global $user_ID;

				if ( get_query_var('tab') && 'ended' == get_query_var('tab') ) {
					$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
				} else {
					$paged = 1;
				}

				$args = array(
					'ignore_sticky_posts'	=> true,
					'author' 				=> $user_ID,
					'post_type' 			=> APP_POST_TYPE,
					'post_status' 			=> 'expired',
					'posts_per_page' 		=> jr_get_jobs_per_page(),
					'paged' 				=> $paged,
				);

				$my_query = new WP_Query($args);
			?>

			<?php if ( $my_query->have_posts() ): ?>

			<p><?php _e('The following jobs have expired or have been ended and are not visible to users.', APP_TD); ?></p>
			
			<table cellpadding="0" cellspacing="0" class="data_list footable">
				<thead>
					<tr>
						<th data-class="expand"><?php _e('Job Title',APP_TD); ?></th>
						<th class="center" data-hide="phone"><?php _e('Date Posted',APP_TD); ?></th>
						<th class="center" data-hide="phone"><?php _e('Status',APP_TD); ?></th>
						<th class="center" data-hide="phone"><?php _e('Views',APP_TD); ?></th>
						<th class="right" data-hide="phone"><?php _e('Actions',APP_TD); ?></th>
					</tr>
				</thead>
				<tbody>

				<?php while ( $my_query->have_posts() ) : $my_query->the_post(); ?>

					<?php if (get_post_meta($my_query->post->ID, 'jr_total_count', true)) $job_views = number_format(get_post_meta($my_query->post->ID, 'jr_total_count', true)); else $job_views = '-'; ?>

					<tr>
						<td><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong></td>
						<td class="date"><strong><?php the_time(__('j M',APP_TD)); ?></strong> <span class="year"><?php the_time(__('Y',APP_TD)); ?></span></td>
						<td class="center"><?php
					
							$job_status = jr_get_job_status( $my_query->post );

							if ( $order_status = jr_get_job_order_status( $my_query->post, $pending_payment_jobs ) )
								echo sprintf( ' %s', $order_status  );
							else
								echo sprintf( ' %s', $job_status );

						?>
						<td class="center"><?php echo $job_views; ?></td>
						<td class="actions"><?php the_job_actions( $my_query->post, $pending_payment_jobs ); ?></td>
					</tr>

				<?php endwhile; ?>

				</tbody>
			</table>

			<?php jr_paging( $my_query, 'paged', array ( 'add_args' => array( 'tab' => 'ended' ) ) ); ?>

			<?php else: ?>
				<p><?php _e('No expired jobs found.', APP_TD); ?></p>
			<?php endif; ?>

		</div>
        
        
        <div id="applications" class="myjobs_section">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut eget odio at mauris interdum rhoncus. Sed pellentesque lectus nec enim fringilla in semper lacus malesuada. Donec non enim diam, ut aliquam massa. Nam nisl risus, convallis in feugiat eu,</div>
        <div id="preferences" class="myjobs_section"> auctor non elit. Sed auctor ligula tempus nisl scelerisque facilisis. Proin tortor tortor, pellentesque euismod pharetra vel, pellentesque nec massa. Sed feugiat consequat mauris, eget rhoncus lorem dignissim sed. Suspendisse dictum, arcu at rhoncus venenatis, arcu nulla viverra lectus, et tristique mi tellus vel est. Ut feugiat sapien urna. Ut id nulla lorem. </div>
        
        
        
        
        
        
        

		<div id="subscriptions" class="myjobs_section">
			<h2><?php _e('Resume Subscriptions ', APP_TD); ?></h2>
			<?php get_template_part( 'includes/dashboard-resumes' ); ?>
		</div>

		<div id="orders" class="myjobs_section">
			<h2><?php _e('Orders', APP_TD); ?></h2>
			<?php get_template_part( 'includes/dashboard-orders' ); ?>
		</div>

		<?php do_action( 'jr_dashboard_tab_content', 'job_lister' ); ?>

		</div><!-- end section_content -->

	</div><!-- end section -->

	<div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar('user'); ?>
