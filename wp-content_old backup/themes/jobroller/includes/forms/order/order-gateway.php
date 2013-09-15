<?php
	process_the_order(); 

	$order_post = get_queried_object();
	$order = appthemes_get_order( $order_post->ID );

	if ( in_array( $order->get_status(), array( APPTHEMES_ORDER_COMPLETED, APPTHEMES_ORDER_ACTIVATED ) ) ) {
		// redirect the user to the order page to display the order summary
		wp_redirect( $order->get_return_url() );
		exit();
	} else {
		// notify admin and author about the new order
		jr_new_order_notify_admin( $order );
		jr_new_order_notify_owner( $order );
	}
