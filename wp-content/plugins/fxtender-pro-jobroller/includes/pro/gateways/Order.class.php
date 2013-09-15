<?php

/**
 * Extend Jobroller's Order Class with custom attributes and methods
 */

if ( !class_exists('jr_fx_order') ):
class jr_fx_order {

	var $gateway;
	var $jr_order;
	var $item_title;
	var $item_description;
	var $item_quantity = 1;
	var $return_page;

	var $options;

	/**
	 * Initialization constructor for an order parsed from a query or from an existing order
	 *
	 * @return void
	 */
	public function __construct( $order, $options, $return_url ) {
		global $wpdb, $app_abbr;

		$this->jr_order = $order;
		$this->options = $options;

		// set the item title
		$this->item_description = $order->get_description();

		// set the return page
		$this->return_page =  $return_url;
		if ( !$this->return_page ) $this->return_page = get_bloginfo('url');
	}
	
	# redirect the user to the selected gateway
	public function redirectGateway( $s_gateway ) {
		# redirect to the choosen gateway
		switch ( $s_gateway ) {
			case 'google':

				include_once ('Google.class.php');
				
				// Create an instance of Google
				$gateway = new jr_fx_gw_Google( $this );
				$gateway->button = JR_FX_PLUGIN_URL . "images/googlecheckout.gif";
				
				$gateway = $this->prepareGateway( $s_gateway, $gateway );
				$gateway->googleCheckout();
				break;

			case '2checkout':

				// Create an instance of 2Checkout
				$gateway = new JR_FX_2Checkout( $this );
				$gateway->process();
				break;

			case 'authorize':
				
				include_once ('Authorize.class.php');

				// Create an instance of Auhorize.net
				$gateway = new jr_fx_gw_Authorize( $this );
				$gateway->button = JR_FX_PLUGIN_URL . "images/authorize-redirect.jpg";
				
				$gateway = $this->prepareGateway( $s_gateway, $gateway );
				$gateway->Authorize();
				break;

			case 'manual':
				jr_new_order( $this->jr_order );
				wp_redirect( add_query_arg( array( 'mp_order_id' => $this->jr_order->id , 'mp_order_cost' => $this->jr_order->cost ), get_permalink( jr_fx_validate('_opt_gateway_redirect') ) ) );
				exit;
		}

	}

	# prepare the gateway bt setting all the necessary values
	protected function prepareGateway ( $s_gateway, $gateway ) {
		$gateway->setUserInfo( jr_fx_validate('_text_gateway_' . $s_gateway . '_id'),  jr_fx_validate('_text_gateway_' . $s_gateway . '_key') );				
		$gateway->currency = jr_fx_validate('_opt_gateway_' . $s_gateway . '_currency');
			
		// set server type: Live | Sandbox
		$gateway->serverType = jr_fx_validate('_opt_gateway_server');
	
		// check for sandbox mode	
		if ( jr_fx_validate('_opt_gateway_server', 'sandbox') )	:
			$gateway->enableTestMode();
		endif;	
		
		// set logging On/Off
		$gateway->logIpn = jr_fx_validate('_opt_gateway_log_ipn','yes');	

		return $gateway;
		
	}
	
	# inserts a new FXtenderorder on JobRoller's Orders table
	public function insertOrder( $use_ipn = 'yes' ) {
		global $wpdb;					
				
		# If IPN is not used Orders are inserted using the JobRoller's normal workflow. This can result in orphan Orders when users cancel the Order or press back on the browser on the gateway page, before pressing the payment button
		# JobRoller Orders workflow (success): 'pending_payment' (NOT CONFIRMED) -> 'completed'
		
		//$status = 'pending_payment';
		//$status = ($use_ipn == 'yes'?'pre_payment':'pending_payment');	//*(1) dropped the 'pre_payment' status to use JobRoller's normal workflow and ensure 100% compatibility 
	
		$wpdb->query( $wpdb->prepare( $this->jr_query ) );												
		$this->jr_order->id = $wpdb->insert_id;	
	
		//update the order to pre_payment. This way it will not show on the orders page until the user confirm the payment (pending_payment) 
		//$wpdb->update( $this->order_table, array( 'status' => $status ), array( 'id' => $this->jr_order->id ), array( '%s' ), array( '%d' ) );	 // *(1) dropped 							
				
	}
	
}
endif;
?>