<?php

function appthemes_bank_transfer_pending_email( $post ) {

	$content = '';

	$content .= html( 'p', __( 'A new order is waiting to be processed. Once you recieve payment, you should mark the order as completed.', APP_TD ) );

	$order_link = sprintf( __('<a href="%s">Review this order</a>', APP_TD ), get_edit_post_link( $post ) );

	$all_orders = html_link(
		admin_url( 'edit.php?post_status=tr_pending&post_type=transaction' ),
		__( 'review all pending orders', APP_TD ) );

	// translators: <Single Order Link> or <Link to All Orders>
	$content .= html( 'p',  sprintf( __( '%s or %s', APP_TD ), $order_link, $all_orders ) );

	$subject = sprintf( __( '[%s] Pending Order #%s', APP_TD ), get_bloginfo( 'name' ), $post->ID );

	if( ! function_exists( 'appthemes_send_email' ) )
		return false;

	appthemes_send_email( get_option( 'admin_email' ), $subject, $content );
}
