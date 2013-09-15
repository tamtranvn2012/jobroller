<?php
function jr_subscriptions() {
?>
<div class="wrap jobroller">
	<div class="icon32" id="icon-themes"><br/></div>
	<h2><?php _e('Subscriptions',APP_TD) ?></h2>

	<?php do_action( 'appthemes_notices' );	?>

	<?php
		if (isset($_GET['p'])) $page = $_GET['p']; else $page = 1;
		
		$dir = 'ASC';
		$sort = 'ID';
		
		$per_page = 20;
		$total_pages = 1;
			
		$show = 'active';
		
		$totals = jr_get_count_subscriptions();

		if (isset($_GET['show'])) :
		
			switch ($_GET['show']) :
				case "inactive" :
					$show = 'inactive';
					$total_pages = ceil($totals['inactive']/$per_page);
				break;
				default :
					$show = 'active';
					$total_pages = ceil($totals['active']/$per_page);
			endswitch;
			
		else :
			$_GET['show'] = '';
		endif;	
		
		if (isset($_GET['dir'])) $posteddir = $_GET['dir']; else $posteddir = '';
		if (isset($_GET['sort'])) $postedsort = $_GET['sort']; else $postedsort = '';	

		$subscribers = jr_list_subscriptions($show, $per_page*($page-1), $per_page, $postedsort, $posteddir);			
	?>
	<div class="tablenav">
		<div class="tablenav-pages alignright">
			<?php
				if ($total_pages>1) {
				
					echo paginate_links( array(
						'base' => 'admin.php?page=subscriptions&show='.$_GET['show'].'%_%&sort='.$postedsort.'&dir='.$posteddir,
						'format' => '&p=%#%',
						'prev_text' => __( '&laquo; Previous', APP_TD ),
						'next_text' => __( 'Next &raquo;', APP_TD ),
						'total' => $total_pages,
						'current' => $page,
						'end_size' => 1,
						'mid_size' => 5,
					));
				}
			?>	
		</div> 

		<ul class="subsubsub">
			<li><a href="admin.php?page=subscriptions&show=active" <?php if ($show == 'active') echo 'class="current"'; ?>><?php _e('Active' ,APP_TD); ?> <span class="count">(<?php echo $totals['active']; ?>)</span></a> |</li>
			<li><a href="admin.php?page=subscriptions&show=inactive" <?php if ($show == 'inactive') echo 'class="current"'; ?>><?php _e('Inactive' ,APP_TD); ?> <span class="count">(<?php echo $totals['inactive']; ?>)</span></a></li>
		</ul>
	</div>
	
	<div class="clear"></div>

	<table class="widefat fixed">

		<thead>
			<tr>
				<th scope="col"><a href="<?php echo jr_echo_subscription_link('user_id', 'ASC'); ?>"><?php _e('User',APP_TD) ?></a></th>
				<?php if ( 'inactive' != $show ) : ?>
					<th scope="col"><a href="<?php echo jr_echo_subscription_link('name', 'ASC'); ?>"><?php _e('Plan Name',APP_TD) ?></a></th>
					<th scope="col"><a href="<?php echo jr_echo_subscription_link('trial', 'ASC'); ?>"><?php _e('Trial?',APP_TD) ?></a></th>
					<th scope="col"><a href="<?php echo jr_echo_subscription_link('start_date', 'DESC'); ?>"><?php _e('Start Date',APP_TD) ?></a></th>
					<th scope="col"><a href="<?php echo jr_echo_subscription_link('end_date', 'ASC'); ?>"><?php _e('End Date',APP_TD) ?></a></th>
					<th scope="col"><?php _e('Recurs',APP_TD) ?></th>
				<?php endif; ?>
				<th scope="col"  style="width:15%;"><?php _e('Actions',APP_TD) ?></th>
			</tr>
		</thead>
	<?php if (sizeof($subscribers) > 0) :
			$rowclass = '';
	?>
		<tbody id="list">
		<?php
			foreach( $subscribers as $subscriber ) :

					$rowclass = 'even' == $rowclass ? 'alt' : 'even';

					$user_info = get_userdata($subscriber->ID);

					// get meta data
					$plan_id = get_user_meta( $subscriber->ID, '_valid_resume_subscription', true );
					if ( $plan_id ) {
						$plan_data = get_post_custom( $plan_id );
					}

					$trial = get_user_meta( $subscriber->ID, '_valid_resume_trial', true );
					$start_date = (int) get_user_meta( $subscriber->ID, '_valid_resume_subscription_start', true );
					$end_date = (int) get_user_meta( $subscriber->ID, '_valid_resume_subscription_end', true );

					$manage_link = add_query_arg( 'user_id', $subscriber->ID, self_admin_url( 'user-edit.php' ) );
					?>
					<tr class="<?php echo $rowclass ?>">
						<td><?php if ($user_info) : ?>#<?php echo $user_info->ID; ?> &ndash; <strong><?php echo $user_info->first_name ?> <?php echo $user_info->last_name ?></strong> (<?php echo $user_info->display_name ?>)<br/><a href="mailto:<?php echo $user_info->user_email ?>"><?php echo $user_info->user_email ?></a><?php endif; ?></td>
						<?php if ( 'inactive' != $show ) : ?>
							<td><?php echo ( !empty($plan_data) ? $plan_data['title'][0] : __( 'N/A', APP_TD ) ); ?></td>
							<td><?php if ( $trial ) echo __( 'Yes' , APP_TD ); else echo __( 'No', APP_TD ); ?></td>
							<td><?php if ( $start_date ) echo appthemes_display_date( $start_date ); else echo __( 'N/A',APP_TD ); ?></td>
							<td><?php if ( $end_date ) echo appthemes_display_date( $end_date ); else echo __( 'N/A', APP_TD ); ?></td>
							<td><?php echo ( !empty($plan_data) && !$trial  ? sprintf( _n( 'Every %s day', 'Every %s days', $plan_data[JR_FIELD_PREFIX.'duration'][0], APP_TD ), $plan_data[JR_FIELD_PREFIX.'duration'][0] ) : __( 'N/A', APP_TD ) ); ?></td>
						<?php endif; ?>
						<td>
							<a href="<?php echo $manage_link; ?>" class="button button-primary manage-resume-subscription"><?php _e('Manage Subscriptions',APP_TD); ?></a>
						</td>
					</tr>

			<?php endforeach; ?>

		</tbody>

		<?php else : ?>
			<tr><td colspan="<?php if ( 'inactive' != $show ) : ?>6<?php else : ?>3<?php endif; ?>"><?php _e( 'No subscriptions found.', APP_TD ); ?></td></tr>
		<?php endif; ?>
	</table>
	<br />
	<script type="text/javascript">
	/* <![CDATA[ */
		jQuery('a.end-subscription').click(function(){
		var answer = confirm ("<?php _e('Are you sure you want to end this subscription?', APP_TD); ?>");
			if (answer) return true;
			return false;
		})
	/* ]]> */
	</script>
</div><!-- end wrap -->
<?php
}

function jr_echo_subscription_link( $sort = 'id', $dir = 'ASC' ) {
	
	if (isset($_GET['show'])) $show = $_GET['show']; else $show = 'active';
	if (isset($_GET['p'])) $page = $_GET['p']; else $page = 1;
	if (isset($_GET['dir'])) $posteddir = $_GET['dir']; else $posteddir = '';
	if (isset($_GET['sort'])) $postedsort = $_GET['sort']; else $postedsort = '';
	
	echo 'admin.php?page=subscriptions&amp;show='.$show.'&amp;p='. $page .'&amp;sort='.$sort.'&amp;dir=';
	
	if ($sort==$postedsort) :
		if ($posteddir==$dir) :
			if ($posteddir=='ASC') echo 'DESC';
			else echo 'ASC';
		else :
			echo $dir;
		endif;
	else :
		echo $dir;
	endif;
}

// Returns the subscriptions list
function jr_list_subscriptions ( $show = 'active', $offset = 0, $limit = 20, $orderby = 'user_id', $order = 'ASC' ) {

	$order_cols = array(
		'user_id',
		'name',
		'trial',
		'start_date',
		'end_date',
	);

	// sanitize order columns
	if ( ! $orderby || ( $orderby && ! in_array($orderby, $order_cols) ) ) {
		$orderby = 'user_id';
	}

	$sort_vals = array(
		'ASC',
		'DESC'
	);

	// sanitize sort column
	if ( ! $order || ( $order && ! in_array($order, $sort_vals) ) ) {
		$order = 'ASC';
	}

	$args = array (
		'number ' 	=> $limit,
		'offset' 	=> $offset,
		'orderby' 	=> $orderby,
		'order' 	=> $order,
	);
	$subscribers = jr_get_resume_subscribers( $show, $args );

	return $subscribers->get_results();

}

// Returns the subscriptions list
function jr_get_count_subscriptions () {

	$totals = array( 
		'active' => jr_get_resume_subscribers( 'active' )->get_total(),
		'inactive' => jr_get_resume_subscribers( 'inactive' )->get_total(),
	);

	return $totals;
}

