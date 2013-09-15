<?php
/**
 * Processes order pages and locates templates for the page being displayed
 */
class APP_Order_Summary extends APP_View {

	function condition() {

		if( apply_filters( 'appthemes_disable_order_summary_template', false ) )
			return false;

		return is_singular( APPTHEMES_ORDER_PTYPE );
	}

	function template_include( $template ) {
		
		$order = get_order();

		$currentuser = wp_get_current_user();
		if ( $order->get_author() != $currentuser->ID ) {
			return locate_template( '404.php' );
		}

		if( apply_filters( 'appthemes_order_summary_skip_checks', false ) == true ){
			return locate_template( 'order-checkout.php' );
		}

		if ( $order->get_total() == 0 ) {

			if( count( $order->get_items() ) > 0 )
				$order->complete();

			return locate_template( 'order-summary.php' );
		}

		if( ! in_array( $order->get_status(), array( APPTHEMES_ORDER_PENDING, APPTHEMES_ORDER_FAILED ) ) ){
			return locate_template( 'order-summary.php' );
		}

		$gateway = $this->resolve_gateway( $order );
		if( empty( $gateway ) ){
			return locate_template( 'order-checkout.php' );
		}else{
			return locate_template( 'order-gateway.php' );
		}

	}

	function resolve_gateway( $order ){

		if( isset( $_GET['cancel'] ) ){
			$order->clear_gateway();
		}

		$gateway = $order->get_gateway();
		if ( !empty( $_POST['payment_gateway'] ) && empty( $gateway ) ) {
			$order->set_gateway( $_POST['payment_gateway'] );
		}

		return $order->get_gateway();

	}

}

/**
 * Returns an order object
 * @param $order_id (optional) If given, returns the specified order, otherwise returns
 * 			the order currently being queried
 * @return APP_Order
 */
function get_order( $order_id = null ){

	if( empty( $order_id ) ){
		$post =  get_queried_object();
		$order_id = $post->ID;
	}

	return appthemes_get_order( $order_id );
}

/**
 * Retrieves the current order object
 * @param $order_id (optional) If given, uses the specified order id, otherwise uses
 * 			the order currently being queried
 * @deprecated Use get_order
 * @return APP_Order
 */
function get_the_order( $order_id = null ){
	_deprecated_function( __FUNCTION__, '12/10/12', 'get_order()' );
	return get_order( $order_id  );
}

/**
 * Displays the current order ID
 * @return void
 */
function the_order_id( $order_id = null ){
	echo get_the_order_id( $order_id );
}

/**
 * Returns the current order ID
 * @param $order_id (optional) If given, uses the specified order id, otherwise uses
 * 			the order currently being queried
 * @return int
 */
function get_the_order_id( $order_id = null ){
	return get_order( $order_id )->get_id();
}

/**
 * Displays the current order description
 * @return void
 */ 
function the_order_description( $order_id = null ){
	echo get_the_order_description( $order_id );
}

/**
 * Returns the current order description
 * @param $order_id (optional) If given, uses the specified order id, otherwise uses
 * 			the order currently being queried
 * @return strng
 */
function get_the_order_description( $order_id = null ){

	return get_order( $order_id )->get_description();
}

/**
 * Displays the current order human readable status
 * Uses APP_Order::get_display_status()
 * @return void
 */
function the_order_status( $order_id = null ){
	echo get_the_order_status( $order_id );
}

/**
 * Returns the current order human readable status
 * Uses APP_Order::get_display_status()
 * @param $order_id (optional) If given, uses the specified order id, otherwise uses
 * 			the order currently being queried
 * @return string
 */
function get_the_order_status( $order_id = null  ){

	return get_order( $order_id )->get_display_status();
}

/**
 * Displays the current order total in a human readable format
 * Uses appthemes_get_price() for formatting
 * @return void
 */
function the_order_total( $order_id ){
	echo get_the_order_total( $order_id );
}

/**
 * Returns the current order total in a human readable format
 * Uses appthemes_get_price() for formatting
 * @param $order_id (optional) If given, uses the specified order id, otherwise uses
 * 			the order currently being queried
 * @return string
 */
function get_the_order_total( $order_id = null ){

	$order = get_order( $order_id );
	return appthemes_get_price( $order->get_total(), $order->get_currency() );
}

/**
 * Displays the name of the current order's currency
 * @return void
 */
function the_order_currency( $order_id = null ){
	echo get_the_order_currency_name( $order_id );
}

/**
 * Returns the name of the current order's currency
 * @param $order_id (optional) If given, uses the specified order id, otherwise uses
 * 			the order currently being queried
 * @return string
 */
function get_the_order_currency_name( $order_id = null ){

	$order = get_order( $order_id );
	return APP_Currencies::get_name( $order->get_currency() );
}

/**
 * Returns the three-letter currency code for the
 * current order's currency
 * @param $order_id (optional) If given, uses the specified order id, otherwise uses
 * 			the order currently being queried
 * @return string 
 */
function get_the_order_currency_code( $order_id = null ){
	
	return get_order( $order_id )->get_currency();
}

/**
 * Displays the current order's return url
 * @return void
 */
function the_order_return_url( $order_id = null ){
	echo get_the_order_return_url( $order_id );
}

/**
 * Returns the current order's return url
 * @param $order_id (optional) If given, uses the specified order id, otherwise uses
 * 			the order currently being queried
 * @return strng
 */
function get_the_order_return_url( $order_id = null ){

	return get_order( $order_id )->get_return_url();
}

/**
 * Display the current order's cancel url
 * @return void
 */
function the_order_cancel_url( $order_id = null ){
	echo get_the_order_cancel_url( $order_id );
}

/**
 * Return the current order's cancel url
 * @param $order_id (optional) If given, uses the specified order id, otherwise uses
 * 			the order currently being queried
 * @return string
 */
function get_the_order_cancel_url( $order_id = null ){

	return get_order( $order_id )->get_cancel_url();
}

/**
 * Displays the order summary table for the current order
 * @uses APP_Order_Summary_Table
 * @return void
 */
function the_order_summary(){
	$order = get_order();
	$table = new APP_Order_Summary_Table( $order );
	$table->show();
}

/**
 * Allows the gateway for the current order to process the order
 * The gateway may output content during this call
 * @return void
 */
function process_the_order(){
	$order = get_order();
	appthemes_process_gateway( $order->get_gateway(), $order );
}

/**
 * Used to construct and display an order summary table for an order
 */
class APP_Order_Summary_Table extends APP_Table{

	protected $order, $currency;

	public function __construct( $order, $args = array() ){

		$this->order = $order;
		$this->currency = $order->get_currency();

		$this->args = wp_parse_args( $args, array(
			'wrapper_html' => 'table',
			'header_wrapper' => 'thead',
			'body_wrapper' => 'tbody',
			'footer_wrapper' => 'tfoot',
			'row_html' => 'tr',
			'cell_html' => 'td',
		) );

	}

	public function show( $attributes = array() ){

		$items = $this->order->get_items();

		$sorted = array();
		foreach( $items as $item ){
			$priority = APP_Item_Registry::get_priority( $item['type'] );
			if( ! isset( $sorted[ $priority ] ) )
				$sorted[ $priority ] = array( $item );
			else
				$sorted[ $priority ][] = $item;
		}
		
		ksort( $sorted );
		$final = array();
		foreach( $sorted as $sorted_items ){
			$final = array_merge( $final, $sorted_items );
		}
		
		echo $this->table( $final, $attributes, $this->args );
	}

	protected function footer( $items ){

		$cells = array(
			__( 'Total', APP_TD ),
			appthemes_get_price( $this->order->get_total(), $this->currency )
		);

		return html( $this->args['row_html'], array(), $this->cells( $cells, $this->args['cell_html'] ) );

	}

	protected function row( $item ){

		if( ! APP_Item_Registry::is_registered( $item['type'] ) ){
			return '';
		}

		$cells = array(
			APP_Item_Registry::get_title( $item['type'] ),
			appthemes_get_price( $item['price'], $this->currency )
		);

		return html( $this->args['row_html'], array(), $this->cells( $cells ) );

	}

}
?>
