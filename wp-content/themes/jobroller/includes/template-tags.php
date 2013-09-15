<?php
/**
 * JobRoller Template Tags
 * This file defines functions to be used in the Loop and helper functions
 *
 *
 * @version 1.7
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */

function the_job_relist_link( $job_id = 0, $text = '' ) {

	if ( ! jr_allow_relist() ) 
		return;

	$job_id = $job_id ? $job_id : get_the_ID();

	if ( ! jr_is_job_author( $job_id ) )
		return;

	if( empty( $text ) )
		$text = __( 'Relist', APP_TD );

	echo html( 'a', array(
		'class' => 'job-relist-link',
		'href' => jr_get_job_relist_url( $job_id ),
	), $text );
}

function jr_get_job_relist_url( $job_id ) {
	$query_args = array(
		'job_relist' => $job_id,
	);
	return add_query_arg( $query_args, get_permalink( JR_Job_Submit_Page::get_id() ) );
}

function the_job_cancel_link( $job_id = 0, $text = '' ) {

	$job_id = $job_id ? $job_id : get_the_ID();

	if ( ! jr_is_job_author( $job_id ) )
		return;

	if( empty( $text ) )
		$text = __( 'Cancel', APP_TD );

	echo html( 'a', array(
		'class' => 'delete',
		'href' => jr_get_job_cancel_url( $job_id, $cancel = true ),
	), $text );
}

function jr_get_job_cancel_url( $job_id, $cancel = false ) {
	$args = array( 'job_end' => $job_id, 'cancel' => 1 );
	return add_query_arg( $args, get_permalink( JR_Dashboard_Page::get_id() ) );
}

function the_job_end_link( $job_id = 0, $text = '' ) {

	$job_id = $job_id ? $job_id : get_the_ID();

	if ( ! jr_is_job_author( $job_id ) )
		return;

	if( empty( $text ) )
		$text = __( 'End', APP_TD );

	echo html( 'a', array(
		'class' => 'end',
		'href' => jr_get_job_end_url( $job_id ),
	), $text );
}

function jr_get_job_end_url( $job_id ) {
	$args = array( 'job_end' => $job_id );
	return add_query_arg( $args, get_permalink( JR_Dashboard_Page::get_id() ) );
}

function the_job_edit_link( $job_id = 0, $text = '' ) {

	if ( ! jr_allow_editing() )
		return;

	$job_id = $job_id ? $job_id : get_the_ID();

	if ( ! jr_is_job_author( $job_id ) )
		return;

	if( empty( $text ) )
		$text = __( 'Edit&nbsp;&rarr;', APP_TD );

	echo html( 'a', array(
		'class' => 'job-edit-link',
		'href' => jr_get_job_edit_url( $job_id ),
	), $text );
}

function jr_get_job_edit_url( $job_id ) {
	return add_query_arg( array( 'job_edit' => $job_id ), get_permalink( JR_Job_Edit_Page::get_id() ) );
}

function the_job_continue_link( $job_id = 0, $text = '' ) {
	$job_id = $job_id ? $job_id : get_the_ID();

	if ( ! jr_is_job_author( $job_id ) )
		return;

	if( empty( $text ) )
		$text = __( 'Continue&nbsp;&rarr;', APP_TD );

	echo html( 'a', array(
		'class' => 'job-continue-link',
		'href' => jr_get_job_continue_url( $job_id ),
	), $text );
}

function jr_get_job_continue_url( $job_id ) {
	return add_query_arg( array( 'job_id' => $job_id ), get_permalink( JR_Job_Submit_Page::get_id() ) );
}

function the_order_purchase_link( $text, $order ) {

	if( empty( $text ) )
		$text = __( 'Pay', APP_TD );

	$order_data = _jr_get_order_job_info( $order );
	if ( $order_data && APP_POST_TYPE == $order_data['post']->post_type ) {
		$job_id = $order_data['post_id'];
	}

	if ( empty($job_id) || ! $order_link = jr_get_the_order_purchase_url( $order, $job_id ) )
		return;

	echo html( 'a', array(
		'class' => 'order-purchase-link',
		'href' => jr_get_the_order_purchase_url( $order, $job_id ),
	), $text );

}

function jr_get_the_order_purchase_url( $order, $job_id = 0 ) {
	$args = array (
		'order_id' 	=> $order->get_id(),
	);

	$plan = jr_plan_for_order( $order );
	if ( ! $plan )
		return;

	if ( $job_id ) {
		$plan_type = 'job-plan';
		
		$args['job_id'] = $job_id;
	} else {
		$plan_type = $plan->post_type;
	}

	if ( $step = jr_get_step_by_name('select_plan') ) {
		$args['step'] = $step;
	}

	switch( $plan_type ){
		case APPTHEMES_RESUMES_PLAN_PTYPE:
			$url = jr_get_purchase_resume_plans_url( $args );
			break;
		case APPTHEMES_PRICE_PLAN_PTYPE:
			$url = jr_get_purchase_packs_url( $args );
			break;
		default:
			$url = jr_get_listing_create_url( $args );
	}

	return $url;
}

function the_order_cancel_link( $text, $order, $job_id = 0 ) {

	if( empty( $text ) )
		$text = __( 'Cancel', APP_TD );

	echo html( 'a', array(
		'class' => 'order-cancel-link',
		'href' => jr_get_the_order_cancel_url( $order->get_id() ),
	), $text );

}

function jr_get_the_order_cancel_url( $order_id ) {
	return add_query_arg( array( 'order_cancel' => $order_id ), get_permalink( JR_Dashboard_Page::get_id() ) );
}

function the_purchase_pack_link( $text = '' ) {

	if( empty( $text ) )
		$text = __( 'Buy Packs &rarr;', APP_TD );

	$aref = html( 'a', array(
		'class' => 'buy-pack button',
		'href' => jr_get_purchase_packs_url(),
	), html( 'span', $text ) );

	echo html ( 'p', $aref );
}

function the_resume_purchase_plan_link( $text = '' ) {

	if ( ! jr_current_user_can_subscribe_for_resumes() )
		return;

	if( empty( $text ) )
		$text = __( 'Subscribe &rarr;', APP_TD );

	$aref = html( 'a', array(
		'class' => 'subscribe-resumes button',
		'href' => jr_get_purchase_resume_plans_url(),
	), html( 'span', $text ) );

	echo html ( 'p', $aref );
}

function jr_get_the_job_tax( $job_id, $taxonomy ) {
	$terms = get_the_terms( $job_id, $taxonomy );
	if ( !$terms )
		return;

	return reset( $terms );
}

function get_the_job_terms( $job_id, $taxonomy, $fields = 'all' ) {
	$params = array( 'fields' => $fields );
	$terms = get_the_terms( $job_id, $taxonomy, $params );
	if ( !$terms )
		return;

	return reset( $terms );
}

function jr_get_single_term( $listing_id, $taxonomy ) {
	$terms = get_the_terms( $listing_id, $taxonomy );
	if ( !$terms )
		return;

	return reset( $terms );
}

function jr_get_listing_create_url( $args = '' ) {
	$default_args = array();
	$args = wp_parse_args( $args, $default_args );

	return add_query_arg( $args, get_permalink( JR_Job_Submit_Page::get_id() ) );
}

function jr_get_purchase_packs_url( $args = '' ) {
	$default_args = array();
	$args = wp_parse_args( $args, $default_args );

	return add_query_arg( $args, get_permalink( JR_Packs_Purchase_Page::get_id() ) );
}

function jr_get_purchase_resume_plans_url( $args = '' ) {
	$default_args = array();
	$args = wp_parse_args( $args, $default_args );

	return add_query_arg( $args, get_permalink( JR_Resume_Plans_Purchase_Page::get_id() ) );
}

function jr_get_dashboard_url() {
	return get_permalink( JR_Dashboard_Page::get_id() ) ;
}

function get_the_job_listing_category( $job_id ) {
	$terms = get_the_terms( $job_id, APP_TAX_CAT );
	if ( !$terms )
		return;

	return reset( $terms );
}

function the_job_listing_fields( $job_id = 0 ) {

	$job_id = $job_id ? $job_id : get_the_ID();

	$cat = get_the_job_listing_category( $job_id );
	if ( !$cat )
		return;

	echo '<section id="listing-fields">';

	foreach ( jr_get_fields_for_cat( (int) $cat->term_id ) as $field ) {
		if ( 'checkbox' == $field['type'] ) {
			$value = implode( ', ', get_post_meta( $job_id, $field['name'] ) );
		} else {
			$value = get_post_meta( $job_id, $field['name'], true );
		}

		if ( !$value )
			continue;

		$field['id_tag'] = jr_make_custom_field_id_tag( $field['desc'] );

		echo html( 'p', array('class' => 'job-listing-custom-field', 'id' => $field['id_tag']),
			 html('span', array('class' => 'custom-field-label'), $field['desc'] ). html('span', array('class' => 'custom-field-sep'), ': ' ) . html('span', array('class' => 'custom-field-value'), $value ) );
	}

	echo '</section>';
}

function jr_make_custom_field_id_tag( $desc, $prefix = 'job-listing-custom-field-' ) {
	$id_tag = $desc;
	$id_tag = strtolower( $id_tag );
	$id_tag = str_ireplace( ' ', '-', $id_tag );
	$id_tag = $prefix.$id_tag;
	return $id_tag;
}

function the_job_addons( $job_id = 0 ) {
	$job_id = $job_id ? $job_id : get_the_ID();

	$job_meta = get_post_custom( $job_id );
	foreach ( jr_get_addons( 'job' ) as $addon ) {
		if ( !empty( $job_meta[$addon][0] ) && _jr_is_active_featured( $job_id, $addon ) ) {
			$days = get_post_meta( $job_id, $addon . '_duration', true );
			$expire_date = '';
			if ( $days >= 1 ) {
				$featured_time = get_post_meta( $job_id, $addon . '_start_date', true );
				$expire_date = strtotime( $featured_time . '+' . $days . ' days' );
				$expire_date = __( ' :: ends ', APP_TD ) . appthemes_display_date( $expire_date, 'date' );
			}
			echo html( 'div', array( 'class' => 'job-addon ' . $addon ), html( 'span', jr_get_addon_title( $addon ) . $expire_date ) );
		}
	}

}

function jr_get_addon_title( $addon ) {
	if ( ! list($app_payments) = get_theme_support( 'app-payments' ) )
		 return '';

	foreach ( $app_payments['items'] as $item ) {
		if ( $addon == $item['type'] )
			return $item['title'];
	}

}

function the_orders_history_job( $order ) {

	$job_id = jr_get_order_job_id( $order );
	if ( ! $job_id ) {
		return;
	}

	$title = get_the_title( $job_id );

	$html = html( 'a', array( 'href' => get_permalink( $job_id ) ), $title );
	echo $html;
}

function jr_get_the_order_summary( $order, $output = 'plain' ) {

	$order_items = '';

	$items = $order->get_items();

	$plan = jr_plan_for_order( $order );

	foreach( $items as $item ) {
		if ( ! APP_Item_Registry::is_registered( $item['type'] ) ) {
			$item_title = __( 'Unknown', APP_TD );
		} else {
			$item_title = APP_Item_Registry::get_title( $item['type'] );
		}
		$item_html = ( 'html' == $output ? html( 'div', $item_title ) : ( $order_items ? ' / ' . $item_title : $item_title ) );
		$order_items .= $item_html;
	}

	if ( ! $order_items ) $order_items = '-';

	return $order_items;
}

function the_orders_history_payment( $order ) {
	$gateway_id = $order->get_gateway();

	if ( !empty( $gateway_id ) ) {
		$gateway = APP_Gateway_Registry::get_gateway( $gateway_id );
		if( $gateway ){
			$gateway = $gateway->display_name( 'admin' );
		} else {
			$gateway = __( 'Unknown', APP_TD );
		}
	} else {
		$gateway = __( 'Undecided', APP_TD );
	}

	$gateway = html( 'div', array( 'class' => 'order-history-gateway' ), $gateway );
	$status = html( 'div', array( 'class' => 'order-history-status' ), $order->get_display_status() );

	echo $gateway . $status;

}

function jr_is_job_author( $job_id ) {
	$post = get_post( $job_id );
	return ( get_current_user_id() == $post->post_author );
}

function jr_get_job_order_status( $job, $pending_payment = '' ) {

	$order_status = '';

	if ( isset( $pending_payment[ $job->ID ] ) ) {
		$order = appthemes_get_order($pending_payment[$job->ID]['order_id']);
		if ( $order ) {
			if ( APPTHEMES_ORDER_FAILED == $order->get_status() )
				$order_status = __( 'Payment Failed', APP_TD );
			elseif ( 'undecided' == $pending_payment[$job->ID]['status'] )
				$order_status = __( 'Pending Payment', APP_TD );
			else
				$order_status = __( 'Pending Payment Approval', APP_TD );
		}
	}
	return $order_status;

}

function jr_get_job_status( $job, $pending_payment = '' ) {

	switch(  $job->post_status ) {
		case 'pending':
			if ( ! $pending_payment || ! jr_get_job_order_status( $job, $pending_payment ) )
				$status = __( 'Pending Approval', APP_TD );
			else
				$status = __( 'Pending', APP_TD );
			break;
		case 'draft':
			$status = __( 'Incomplete Draft', APP_TD);
			break;
		case 'expired':
			$canceled_job = get_post_meta( $job->ID, '_jr_canceled_job', true );
			if ( $canceled_job )
				$status = __( 'Canceled', APP_TD );
			else
				$status = __( 'Expired', APP_TD );
			break;
		default:
			$status = '';
			break;
	}

	return $status;

}

function the_job_actions( $job, $pending_payment = '' ) {

	if ( ! isset( $pending_payment[ $job->ID ] ) ) {

		if ( 'pending' == $job->post_status ) :
			 the_job_edit_link( $job->ID );
		elseif ( 'draft' == $job->post_status ) :
			the_job_continue_link( $job->ID );
		elseif ( 'expired' == $job->post_status ) :
			$canceled_job = get_post_meta( $job->ID, '_jr_canceled_job', true );
			if ( $canceled_job )
				the_job_continue_link( $job->ID, __( 'Continue', APP_TD ) );
			else
				the_job_relist_link( $job->ID );
		endif;

	} elseif ( ! empty( $pending_payment[ $job->ID ]) && 'undecided' == $pending_payment[ $job->ID ]['status'] ) {
		$order = appthemes_get_order($pending_payment[$job->ID]['order_id']);
		the_order_purchase_link( __( 'Pay&nbsp;&rarr; ',APP_TD ), $order ); 
	}

	if ( 'pending' == $job->post_status || 'draft' == $job->post_status ) {
		the_job_cancel_link( $job->ID ); 
	}
}

function jr_get_submit_footer_text() {

	$text = get_option( 'jr_jobs_submit_text' );
	if ( ! $text && jr_charge_job_listings() && $plans = jr_get_plans_prices_duration() ) {
		if ( sizeof($plans) > 0 ) {
			$text = __( 'Starting at ', APP_TD );
		}
		reset($plans);

		// display standard pricing
		$amount = $plans[0]['price'];
		$jobs_last = $plans[0]['duration'];
		if ( ! $jobs_last ) $jobs_last = 30; // 30 day default
		if ( $amount && $amount > 0 ) { 
			$text = sprintf( '<p class=\'pricing\'>%s <em>%s</em> %s <em>%s %s</em></p>', $text, appthemes_get_price($amount), __( 'for',APP_TD ), $jobs_last, _n( 'day', 'days', $jobs_last, APP_TD ) );
		}
	}
	$text = apply_filters( 'jr_submit_footer_text', wpautop( wptexturize($text) ) );

	return $text;
}

function jr_get_featured_jobs_rss_url() {

	if ( ! is_tax( APP_TAX_CAT ) && ! is_front_page() && ! is_archive( APP_POST_TYPE ) ) 
		return add_query_arg( 'post_type', APP_POST_TYPE, get_bloginfo('rss2_url') );

	$args = array(
		'rss_featured' => 1
	);
	if ( is_tax( APP_TAX_CAT ) ) {
		$args['rss_job_cat'] = get_queried_object_id();
	}
	
	return add_query_arg( $args, get_bloginfo('rss2_url') );

}
