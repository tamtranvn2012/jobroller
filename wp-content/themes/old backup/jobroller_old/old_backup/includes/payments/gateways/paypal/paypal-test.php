<?php

add_action( 'appthemes_payment_tests', array( 'APP_PayPal_Tests', 'run_static' ) );

class APP_PayPal_Tests{

	private $test_orders = array();
	
	public static function run_static(){
		$test = new APP_PayPal_Tests();
		$test->run();
	}

	public function run(){

		// Order Security Checks
		$tests['Normal Process'] = $this->find_order_success();
		$tests['Missing Item Number'] = $this->missing_item_number();
		$tests['Missing Currency'] = $this->missing_currency();
		$tests['Missing Payment Amount'] = $this->missing_amount();
		$tests['Missing Email'] = $this->missing_email();
		$tests['Bad Item Number'] = $this->bad_item_number();
		$tests['Bad Currency'] = $this->bad_currency();
		$tests['Bad Payment Amount'] = $this->bad_amount();
		$tests['Bad Email'] = $this->bad_email();

		// Order Processing Checks
		$tests['Completed Order'] = $this->complete_order();
		$tests['Completed Order Wrong Payment Type'] = $this->complete_order_wrong_type();
		$tests['Pending Order'] = $this->Pending_order();

		// Clean Up
		$this->clean_up_test_orders();

		foreach( $tests as $test_name => $test ){
			if( !$test ){
				echo 'FAIL ' . $test_name . ' </br>';
			}else{
				echo 'SUCCESS ' . $test_name . ' </br>';
			}
		}

	}

	public function complete_order(){

		$bridge = $this->create_bridge();
		$response = $this->create_response_array();

		$order = appthemes_get_order( $response['item_number'] );

		$response['txn_type'] = 'web_accept';
		$response['payment_status'] = 'Completed';

		remove_all_actions( 'appthemes_transaction_completed' );

		$bridge->handle_response( $response );

		return $order->get_status() == APPTHEMES_ORDER_COMPLETED;

	}

	public function complete_order_wrong_type(){

		$bridge = $this->create_bridge();
		$response = $this->create_response_array();

		$order = appthemes_get_order( $response['item_number'] );

		$response['txn_type'] = 'masspay';
		$response['payment_status'] = 'Completed';

		remove_all_actions( 'appthemes_transaction_completed' );

		$bridge->handle_response( $response );

		return $order->get_status() == APPTHEMES_ORDER_PENDING;

	}

	public function pending_order(){

		$bridge = $this->create_bridge();
		$response = $this->create_response_array();

		$order = appthemes_get_order( $response['item_number'] );

		$response['txn_type'] = 'web_accept';
		$response['payment_status'] = 'Pending';

		remove_all_actions( 'appthemes_transaction_completed' );

		$bridge->handle_response( $response );

		return $order->get_status() == APPTHEMES_ORDER_PENDING;

	}

	public function find_order_success(){

		$bridge = $this->create_bridge();
		$response = $this->create_response_array();
		$return_data =  $bridge->find_order( $response );

		if( ! ( $return_data instanceof APP_Order ) )
			return false;
		
		if( $return_data->get_id() != $response['item_number'] )
			return false;
		
		return true;

	}

	public function missing_item_number(){

		$bridge = $this->create_bridge();
		$response = $this->create_response_array();

		unset( $response['item_number'] );

		$return_data =  $bridge->find_order( $response );
		if( $return_data instanceof WP_Error ){
			$codes = $return_data->get_error_codes();
			if( $codes[0] == 'missing_item_number' )
				return true;
		}

	}

	public function bad_item_number(){

		$bridge = $this->create_bridge();
		$response = $this->create_response_array();

		$response['item_number'] = $response['item_number'] + 1;

		$return_data =  $bridge->find_order( $response );
		if( $return_data instanceof WP_Error ){
			$codes = $return_data->get_error_codes();
			if( $codes[0] == 'bad_order' )
				return true;
		}

	}

	public function missing_currency(){

		$bridge = $this->create_bridge();
		$response = $this->create_response_array();

		unset( $response['mc_currency'] );

		$return_data =  $bridge->find_order( $response );
		if( $return_data instanceof WP_Error ){
			$codes = $return_data->get_error_codes();
			if( $codes[0] == 'bad_currency' )
				return true;
		}

	}

	public function bad_currency(){

		$bridge = $this->create_bridge();
		$response = $this->create_response_array();

		$response['mc_currency'] = 'ABC';

		$return_data =  $bridge->find_order( $response );
		if( $return_data instanceof WP_Error ){
			$codes = $return_data->get_error_codes();
			if( $codes[0] == 'bad_currency' )
				return true;
		}

	}

	public function missing_amount(){

		$bridge = $this->create_bridge();
		$response = $this->create_response_array();

		unset( $response['payment_gross'] );

		$return_data =  $bridge->find_order( $response );
		if( $return_data instanceof WP_Error ){
			$codes = $return_data->get_error_codes();
			if( $codes[0] == 'bad_amount' )
				return true;
		}

	}

	public function bad_amount(){

		$bridge = $this->create_bridge();
		$response = $this->create_response_array();

		$response['payment_gross'] = 999;

		$return_data =  $bridge->find_order( $response );
		if( $return_data instanceof WP_Error ){
			$codes = $return_data->get_error_codes();
			if( $codes[0] == 'bad_amount' )
				return true;
		}

	}

	public function missing_email(){

		$bridge = $this->create_bridge();
		$response = $this->create_response_array();

		unset( $response['business'] );

		$return_data =  $bridge->find_order( $response );
		if( $return_data instanceof WP_Error ){
			$codes = $return_data->get_error_codes();
			if( $codes[0] == 'bad_email' )
				return true;
		}

	}

	public function bad_email(){

		$bridge = $this->create_bridge();
		$response = $this->create_response_array();

		$response['business'] = 'example@example.com';

		$return_data =  $bridge->find_order( $response );
		if( $return_data instanceof WP_Error ){
			$codes = $return_data->get_error_codes();
			if( $codes[0] == 'bad_email' )
				return true;
		}

	}

	public function create_response_array(){

		$options = $this->get_options();

		$order = appthemes_new_order();
		$order->add_item( 'paypal-test-item', 5, $order->get_id() );
		$order->set_gateway('paypal');

		$this->test_orders[] = $order->get_id();

		return array(
			'item_number' => $order->get_id(),
			'mc_currency' => $order->get_currency(),
			'payment_gross' => $order->get_total(),
			'business' => $options['email_address']
		);

	}

	public function clean_up_test_orders(){
		foreach( $this->test_orders as $id ){
			wp_delete_post( $id, true );
		}
	}

	public function get_options(){
		return APP_Gateway_Registry::get_gateway_options('paypal');
	}

	public function create_bridge(){
		return new APP_PayPal_Bridge;
	}

}
