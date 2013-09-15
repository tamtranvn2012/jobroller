<?php 

class APP_PayPal_Form{

	const TYPE = 'cmd';

	const BUY_NOW = '_xclick';
	const AMOUNT = 'amount';

	const SUBSCRIBE = '_xclick-subscriptions';
	const RECURRING_AMOUNT = 'a3';
	const RECURRING_PERIOD = 'p3';
	const RECURRING_PERIOD_TYPE = 't3';
	const RECURR_BILLING = 'src';

	const RECUR_BY_DAYS = 'D';
	const RECUR_BY_WEEKS = 'W';
	const RECUR_BY_MONTHS = 'M';
	const RECUR_BY_YEARS = 'Y';

	const SELLER_EMAIL = 'business';
	const ITEM_NAME = 'item_name';
	const ITEM_NUMBER = 'item_number';
	const CURRENCY_CODE = 'currency_code';

	const RETURN_METHOD = 'rm';
	const RETURN_BY_GET = 0;
	const RETURN_BY_GET_NO_QUERY = 1;
	const RETURN_BY_POST = 2;

	const RETURN_TEXT = 'cbt';
	const RETURN_URL = 'return';
	const CANCEL_URL = 'cancel_return';

	const NO_SHIPPING = 'noshipping';
	const NO_NOTE = 'no_note';
	const CHARSET = 'charset';
	const INVOICE = 'invoice';

	/**
	 * Displays the form for user redirection
	 * @param  APP_Order $order   Order to process
	 * @param  array $options     User inputted options
	 * @return void  
	 */
	public static function create_form( $order, $options, $return_url, $cancel_url ) {
		
		$options = wp_parse_args( $options, array(
	       		'email_address' => ''
		) );

		$fields = array(
			self::SELLER_EMAIL => $options['email_address'],
			
			self::ITEM_NAME => $order->get_description(),
			self::ITEM_NUMBER => $order->get_id(),
			self::CURRENCY_CODE => $order->get_currency(),

			self::RETURN_TEXT => sprintf( __( 'Continue to %s', APP_TD ), get_bloginfo( 'name' ) ),
			self::RETURN_URL => $return_url,
			self::CANCEL_URL => $cancel_url,
			self::NO_SHIPPING => 1,
			self::NO_NOTE => 1,

			self::RETURN_METHOD => self::RETURN_BY_GET,
			self::CHARSET => 'utf-8',
		);

		if( $order->is_recurring() ){

			if( get_post_meta( $order->get_id(), 'paypal_subscription_id', true ) ){
				self::print_processing_script( $order );
				return array();
			}

			$fields[ self::TYPE ] = self::SUBSCRIBE;
			$fields[ self::RECURR_BILLING ] = 1;

			$subscription_id = $order->get_id() . mt_rand(0, 1000);
			$fields[ self::INVOICE ] = $subscription_id;
			update_post_meta( $order->get_id(), 'paypal_subscription_id', $subscription_id );

			$fields[ self::RECURRING_AMOUNT ] = $order->get_total();
			$fields[ self::RECURRING_PERIOD ] = $order->get_recurring_period();
			$fields[ self::RECURRING_PERIOD_TYPE ] = self::RECUR_BY_DAYS;

		}else{

			$fields[ self::TYPE ] = self::BUY_NOW;
			$fields[ self::AMOUNT ] = $order->get_total();

		}
		
		$form = array(
			'action' => APP_PayPal::get_request_url(),
			'name' => 'paypal_payform',
			'id' => 'create_listing',
		);

		return array( $form, $fields );

	}

	public static function print_processing_script( $order ){
		echo html( 'p', __( 'Your Order is strill being processed. Please wait a few seconds...', APP_TD ) );
		echo html( 'p', sprintf( __( 'If your Order does not complete soon, please contact us and refer to your Order ID - #%d.', APP_TD ), $order->get_id() ) );

		$page = $_SERVER['REQUEST_URI'];
		header( "Refresh:5; url=$page");
	}

}
