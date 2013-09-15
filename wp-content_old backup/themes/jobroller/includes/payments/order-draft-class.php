<?php

/**
 * Represents an order that has not been written to the database yet.
 * All methods used this class will not write to the database
 */
class APP_Draft_Order extends APP_Order{

	protected $modified = false;

	/**
	 * Upgrrades a Draft Order to a Main Order
	 * @param  APP_Draft_Order $draft_order The draft order to Upgrade
	 * @return APP_Order              		An upgraded order
	 */
	public static function upgrade( $draft_order ){

		if( !( $draft_order instanceof APP_Draft_Order ) )
			trigger_error( 'Invalid draft order given. Must be instance of APP_Draft_Order' );

		$order = APP_Order_Factory::create();

		$order->set_description( $draft_order->get_description() );
		$order->set_gateway( $draft_order->get_gateway() );
		$order->set_currency( $draft_order->get_currency() );

		foreach( $draft_order->get_items() as $item)
			$order->add_item( $item['type'], $item['price'], $item['post_id'] );

		return $order;

	}

	/**
	 * Records the current IP Address of the user
	 */
	public function __construct() {

		$args = appthemes_price_format_get_args();

		// Set defaults
		if( isset( $_SERVER['REMOTE_ADDR'] ) )
			$this->creator['ip_address'] = $_SERVER['REMOTE_ADDR'];

		$this->description = __( 'Transaction', APP_TD );
		$this->payment['currency'] = $args['currency_default'];
	}

	public function set_description( $description ){

		if( ! is_string( $description ) )
			trigger_error( 'Description must be a string', E_USER_WARNING );

		$this->description = $description;
		$this->modified = true;
	}

	/**
	 * See APP_Order::add_item() for more information
	 * @param string $type    A string representing the type of item being added
	 * @param int $price  	  The price of the item
	 * @param int $post_id    The post that this item affects
	 */
	public function add_item( $type, $price, $post_id = '', $unique = false ) {

		if( ! is_numeric( $post_id ) )
			trigger_error( 'Post ID must be an integer', E_USER_WARNING );

		if( ! is_numeric( $price ) )
			trigger_error( 'Price must be numeric', E_USER_WARNING );

		if( ! is_string( $type ) && ! is_int( $type ) )
			trigger_error( 'Item Type must be a string or integer', E_USER_WARNING );

		$item = array(
			'type' => $type,
			'price' => $price,
			'post_id' => $post_id,
			'post' => get_post( $post_id )
		);
		$this->items[] = $item;
		$this->refresh_total();
		$this->modified = true;
	}
	
	/**
	 * See APP_Order::set_gateway() for more information
	 * @param string $gateway_id The Gateway Identifier. See APP_gateway
	 */
	public function set_gateway( $gateway_id ) {

		if( ! is_string( $gateway_id ) )
			trigger_error( 'Gateway ID must be a string', E_USER_WARNING );

		if ( $gateway = APP_Gateway_Registry::get_gateway( $gateway_id ) ){
			$this->payment['gateway'] = $gateway->identifier();
			$this->modified = true;
			return true;
		}

		return false;

	}
	
	/**
	 * See APP_Order::clear_gateway()
	 * @return boolean True
	 */
	public function clear_gateway(){
		$this->payment['gateway'] = '';
		$this->modified = true;
		return true;		
	}
	
	/**
	 * See APP_Order::refresh_total()
	 * @return void
	 */
	protected function refresh_total() {
		$this->payment['total'] = 0;
		foreach( $this->items as $item )
			$this->payment['total'] += (float) $item['price'];
	}


	/**
	 * Overrides parent method to remove logging.
	 */
	public function pending(){
		$this->set_status( APPTHEMES_ORDER_PENDING );
	}

	/**
	 * Overrides parent method to remove logging.
	 */
	public function failed(){
		$this->set_status( APPTHEMES_ORDER_FAILED );
	}

	/**
	 * Overrides parent method to remove logging.
	 */
	public function complete(){
		$this->set_status( APPTHEMES_ORDER_COMPLETED );
	}

	/**
	 * Overrides parent method to remove ability for new order to be
	 * spawned from draft order activations and logging.
	 */
	public function activate(){
		$this->set_status( APPTHEMES_ORDER_ACTIVATED );
	}

	/**
	 * See APP_Order::set_status()
	 * @param string $status Valid status for order. See order-functions.php
	 * 							for valid statuses
	 */	
	protected function set_status( $state ) {
	    
		if ( $this->state == $state )
			return;

		$this->state = $state;
		$this->modified = true;
	}

	public function is_modified(){
		return (bool) $this->modified;
	}
}
