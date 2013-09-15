<?php

function jr_alerts_subscribers() {
    global $wpdb, $message;
    
    $message = '';
            
	if (isset($_GET['export'])) :
    	
    	ob_end_clean();
    	header("Content-type: text/plain; charset=" . get_bloginfo('charset'));
		header("Content-Disposition: attachment; filename=jobroller_alerts_export_".date('Ymd').".csv");

    	$csv = array();
    	
    	$row = array("User ID","Name","Email","Keywords","Locations","Job Types","Job Categories");
    	
    	$csv[] = '"'.implode('","', $row).'"';
	            
	    $row = array();

        $subscribers_list = jr_list_alerts_subscribers();
	    
        if (sizeof($subscribers_list) > 0) :
        
            foreach( $subscribers_list as $subscriber) :

				$user_info = array();
            	if ($subscriber['user_id']) $user_info = get_userdata($subscriber['user_id']);
            
				$job_type = array();
                if ( !empty($subscriber['criteria']['job_type']) )
                	$job_type = jr_get_multiple_term_names_by('id', $subscriber['criteria']['job_type'], APP_TAX_TYPE );
             
			 	$job_cat = array();
				if ( !empty($subscriber['criteria']['job_cat']) )
                	$job_cat = jr_get_multiple_term_names_by('id', $subscriber['criteria']['job_cat'], APP_TAX_CAT);         
                	
                $row[] = '#'.$user_info->ID;
                $row[] = $user_info->first_name.' '.$user_info->last_name;
                $row[] = $user_info->user_email;
                 	            
				$row[] = !empty($subscriber['criteria']['keyword']) ? implode(',',$subscriber['criteria']['keyword']) :  '';                    						
                $row[] = !empty($subscriber['criteria']['location']) ? implode(',',$subscriber['criteria']['location']) :  '';
                
				$row[] =  !empty($job_type) ? $job_type : ''; 
				$row[] =  !empty($job_cat) ? $job_cat: '';
					                
	            $row = array_map('trim', $row);
	            $row = array_map('html_entity_decode', $row);
	            $row = array_map('addslashes', $row);
	            
	            $csv[] = '"'.implode('","', $row).'"';
	            
	            $row = array();
                    
			endforeach;
              
		endif;
		
		echo implode("\n", $csv);
		exit;
    	
    endif;
	
?>
<div class="wrap jobroller">  
	<h2><?php _e('Job Alerts Subscribers',APP_TD) ?> <a href="admin.php?page=alerts_subscribers&amp;export=true" class="button" title=""><?php _e('Export CSV', APP_TD); ?></a></h2>    
    <p><?php _e('Below is a list of all the current job alerts subscribers.',APP_TD);?></p>

	<?php do_action( 'appthemes_notices' );	?>

	<?php		
		if (isset($_GET['p'])) $page = $_GET['p']; else $page = 1;
		
		$dir = 'ASC';
		$sort = 'ID';
		
		$per_page = 20;
		$total_pages = 1;
			
		$totals = jr_get_count_subscribers();
		$total_pages = ceil($totals['total']/$per_page);
		
		$show = 'all';
		
		if (isset($_GET['show'])) :
		
			switch ($_GET['show']) :
				default :
					$total_pages = ceil($totals['total']/$per_page);
				break;
			endswitch;
			
		else :
			$_GET['show'] = '';
		endif;	
		
		if (isset($_GET['dir'])) $posteddir = $_GET['dir']; else $posteddir = '';
		if (isset($_GET['sort'])) $postedsort = $_GET['sort']; else $postedsort = '';	

		$alerts_subscribers = jr_list_alerts_subscribers ($show, $per_page*($page-1), $per_page, $postedsort, $posteddir);

	?>	
	<div class="tablenav">
		<div class="tablenav-pages alignright">
			<?php
				if ($total_pages>1) {
					echo paginate_links( array(
						'base' => 'admin.php?page=alerts_subscribers&show='.$_GET['show'].'%_%&sort='.$postedsort.'&dir='.$posteddir,
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
	</div>

	<div class="clear"></div>

    <table class="widefat fixed">

        <thead>
            <tr>
                <th scope="col" style="width:20%;"><a href="<?php echo jr_echo_subscribers_link('user_id', 'ASC'); ?>"><?php _e('User',APP_TD) ?></a></th>                
				<th scope="col"><?php _e('Keywords',APP_TD) ?></a></th>
                <th scope="col"><?php _e('Location',APP_TD) ?></a></th>
	            <th scope="col"><?php _e('Job Types',APP_TD) ?></a></th>
	            <th scope="col"><?php _e('Job Categories',APP_TD) ?></a></th>
            </tr>
        </thead>
		
	<?php if (sizeof($alerts_subscribers) > 0) :
            $rowclass = '';
            ?>
            <tbody id="list">
            <?php
    		  
    		foreach( $alerts_subscribers as $subscriber) :
    	
                $rowclass = 'even' == $rowclass ? 'alt' : 'even';
                
				$user_info = array();
                if ($subscriber['user_id']) $user_info = get_userdata($subscriber['user_id']);

				$job_type = array();
                if ( !empty($subscriber['criteria']['job_type']) )
                	$job_type = jr_get_multiple_term_names_by('id', $subscriber['criteria']['job_type'], APP_TAX_TYPE );
             
			 	$job_cat = array();
				if ( !empty($subscriber['criteria']['job_cat']) )
                	$job_cat = jr_get_multiple_term_names_by('id', $subscriber['criteria']['job_cat'], APP_TAX_CAT);
               		
				?>
                <tr class="<?php echo $rowclass ?>">
                    <td><?php if (!empty($user_info)) : ?>#<?php echo $user_info->ID; ?> &ndash; <strong><?php echo $user_info->first_name ?> <?php echo $user_info->last_name ?></strong> (<?php echo $user_info->display_name ?>)<br/><a href="mailto:<?php echo $user_info->user_email ?>"><?php echo $user_info->user_email ?></a><?php endif; ?></td>                    
					<td><?php if (!empty($subscriber['criteria']['keyword'])) echo implode(',',$subscriber['criteria']['keyword']); else echo __('-', APP_TD);  ?></td>                    						
                    <td><?php if (!empty($subscriber['criteria']['location'])) echo implode(',',$subscriber['criteria']['location']); else echo __('Anywhere', APP_TD);  ?></td>
					<td><?php if (!empty($job_type)) echo $job_type; else echo __('All', APP_TD);  ?></td>
					<td><?php if (!empty($job_cat)) echo $job_cat; else echo __('All', APP_TD);  ?></td>
                </tr>
              <?php endforeach; ?>

              </tbody>
		<?php else : ?>
            <tr><td colspan=5> <?php _e('No subscribers found.',APP_TD) ?></td></tr>
        <?php endif; ?>
    </table>
    <?php $last_activity = get_transient( 'jr_job_alerts_last_activity'); ?>
    <p><em><?php echo __( 'Last Alert Activity: ', APP_TD ) . ( $last_activity ? human_time_diff( time(), $last_activity ) . ' ago' : __( 'None', APP_TD ) ) ; ?></em></p>
    
</div><!-- end wrap -->

<?php
}

// Returns the subscriptions list
function jr_list_alerts_subscribers ( $show = '', $offset = 0, $limit = 20, $orderby = 'user_id', $order = 'ASC' ) {
	global $wpdb, $app_abbr;

	if ( ! $orderby || 'user_id' != $orderby ) $orderby = 'user_id';

	$sort_vals = array(
		'ASC',
		'DESC'
	);

	// sanitize sort column
	if ( ! $order || ( $order && ! in_array($order, $sort_vals) ) ) {
		$order = 'ASC';
	}

	$sql = "
		SELECT user_id, 
		 (SELECT meta_value FROM $wpdb->usermeta as jtype WHERE meta_key = '{$app_abbr}_alert_meta_keyword' AND user_id = user_meta.user_id)     as alert_keyword,
		 (SELECT meta_value FROM $wpdb->usermeta as jtype WHERE meta_key = '{$app_abbr}_alert_meta_job_type' AND user_id = user_meta.user_id)    as alert_jtype,
		 (SELECT meta_value FROM $wpdb->usermeta as jcat WHERE meta_key = '{$app_abbr}_alert_meta_job_cat' AND user_id = user_meta.user_id)      as alert_jcat,
		 (SELECT meta_value FROM $wpdb->usermeta as location WHERE meta_key = '{$app_abbr}_alert_meta_location' AND user_id = user_meta.user_id) as alert_location
		FROM $wpdb->usermeta as user_meta
		WHERE meta_key = '{$app_abbr}_alert_status' AND meta_value = 'active' ";
			
	$subscribers = $wpdb->get_results( $sql . " ORDER BY ".$orderby." ".$order.($limit>0?" LIMIT $offset, $limit":"") );

	if ( $subscribers ):
	
		$alerts = array();
		foreach( $subscribers as $subscriber) :
	
			$user_id = $subscriber->user_id;
			$alert = array (
						'keyword'  => maybe_unserialize($subscriber->alert_keyword),
						'location' => maybe_unserialize($subscriber->alert_location),
						'job_type' => maybe_unserialize($subscriber->alert_jtype),
						'job_cat'  => maybe_unserialize($subscriber->alert_jcat),
			);			

			$alerts[] = array (
				'user_id'  => $user_id,
				'criteria' => $alert
			);

		endforeach;
		$subscribers = $alerts;
	endif;

	return $subscribers;
}

function jr_echo_subscribers_link( $sort = 'id', $dir = 'ASC' ) {

	if (isset($_GET['show'])) $show = $_GET['show']; else $show = 'all';
	if (isset($_GET['p'])) $page = $_GET['p']; else $page = 1;
	if (isset($_GET['dir'])) $posteddir = $_GET['dir']; else $posteddir = '';
	if (isset($_GET['sort'])) $postedsort = $_GET['sort']; else $postedsort = '';

	echo 'admin.php?page=alerts_subscribers&amp;show='.$show.'&amp;p='. $page .'&amp;sort='.$sort.'&amp;dir=';

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
function jr_get_count_subscribers () {
	global $wpdb, $app_abbr;

	$query = "SELECT distinct count(distinct user_id) total FROM $wpdb->usermeta WHERE meta_key = '{$app_abbr}_alert' ";
	$totals = $wpdb->get_row($query, ARRAY_A);

	return $totals;
}

// loop through an array of terms and returns the corresponding term names
function jr_get_multiple_term_names_by( $field = 'id', $terms, $taxonomy, $delimiter = ',' ) {

	$names = array();

	foreach ( $terms as $term ) {
		$term = get_term_by( $field, $term, $taxonomy );
		if ( $term ) $names[] = $term->name;
	}

	return implode( $delimiter, $names );
}
