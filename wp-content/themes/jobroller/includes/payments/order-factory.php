<?php

class APP_Order_Factory{

	static public function create( $description = '' ){
		$order = self::make( $description );

		do_action( 'appthemes_create_order_original', $order );
		return $order;
	}

	/**
	 * Prepares and returns a new Order
	 * @return APP_Order New Order object
	 */
	static protected function make( $description = '' ) {

		if( empty( $description ) )
			$description = __( 'Transaction', APP_TD );

		$id = wp_insert_post( array(
			'post_title' => $description,
			'post_content' => __( 'Transaction Data', APP_TD ),
			'post_type' => APPTHEMES_ORDER_PTYPE,
			'post_status' => APPTHEMES_ORDER_PENDING,
		) );

		add_post_meta( $id, 'currency', appthemes_price_format_get_args( 'currency_default' ), true );
		if ( isset( $_SERVER['REMOTE_ADDR'] ) )
			add_post_meta( $id, 'ip_address', $_SERVER['REMOTE_ADDR'], true );

		wp_update_post( array(
			'ID' => $id,
			'post_name' => $id
		) );

		$order = self::retrieve( $id );
		$order->log( 'Order Created', 'major' );

		return $order;
	}

	/**
	 * Retrieves an existing order by ID
	 * @param  int 	$order_id Order ID
	 * @return APP_Order Object representing the order
	 */
	static public function retrieve( $order_id ) {

		if( !is_numeric( $order_id ) )
			trigger_error( 'Invalid order id given. Must be an integer', E_USER_WARNING );

		$order_data = get_post( $order_id );
		if ( !$order_data || $order_data->post_type != APPTHEMES_ORDER_PTYPE )
			return false;

		$order = new APP_Order( $order_data, self::get_order_items( $order_id ) );
		return $order;
	}

	static public function duplicate( $original ){

		$duplicate = self::make( $original->get_description() );

		$duplicate->set_gateway( $original->get_gateway() );
		$duplicate->set_currency( $original->get_currency() );
		$duplicate->set_author( $original->get_author() );

		if( $original->is_recurring() )
			$duplicate->set_recurring_period( $original->get_recurring_period() );		

		foreach( $original->get_items() as $item )
			$duplicate->add_item( $item['type'], $item['price'], $item['post_id'] );

		if( $original->get_id() != 0 ){
			$original->log( 'Order Duplicated. Created Order #' . $duplicate->get_id(), 'info' );
			$duplicate->log( 'Order Duplicated from Order #' . $original->get_id(), 'info' );
		}

		do_action( 'appthemes_create_order_duplicate', $duplicate, $original );

		return $duplicate;
	}

	/**
	 * Retrieves an array of an order's items
	 * @param int $order_id The Order ID
	 * @return array
	 */
	static protected function get_order_items( $order_id ){

		$items = array();
		foreach ( _appthemes_orders_get_connected( $order_id )->posts as $post ) {
			$meta = p2p_get_meta( $post->p2p_id );
			$items[] = array(
				'type' => $meta['type'][0],
				'price' => $meta['price'][0],
				'post_id' => $post->ID,
				'post' => $post,
				'unique_id' => $post->p2p_id,
			);
		}

		return $items;
	}

}
