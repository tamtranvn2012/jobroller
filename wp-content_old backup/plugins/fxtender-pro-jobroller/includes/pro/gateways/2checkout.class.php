<?php

/**
 * 2Checkout Class
 *
 * Integrate the 2CheckOut payment gateway in your site using this easy
 * to use library. Just see the example code to know how you should
 * proceed. Btw, this library does not support the recurring payment
 * system. If you need that, drop me a note and I will send to you.
 *
 * @package     Payment Gateway
 * @category    Library
 * @author      Md Emran Hasan <phpfour@gmail.com>
 * @link        http://www.phpfour.com
 * @adapted by 	Bruno Carreço
 * @link        http://www.bruno-carreco.com
 */

add_action( 'init', 'jr_fx_init_2checkout_currencies', 105 );

/**
* JobRoller Payments API (JR 1.7.x) - Integration
*/

class JR_FX_2Checkout_APP extends APP_Boomerang {

	protected $options;

	public function __construct(){
		parent::__construct( '2checkout', array(
			'admin' => __( '2Checkout (FXtender)', JR_FX_i18N_DOMAIN ),
			'dropdown' => __( '2Checkout', JR_FX_i18N_DOMAIN )
		));
	}

	public function create_form( $order, $options ) {
		$gateway = new JR_FX_2Checkout( $order, $options, $this->get_return_url( $order ) );
		$gateway->process();
	}

	public function form(){

		$general = array(
			'title' => __( '2Checkout', JR_FX_i18N_DOMAIN ),
			'fields' => array (
					array( 'title' => __( 'Account Number', JR_FX_i18N_DOMAIN ),
						'name' => 'jr_fx_account_number',
						'type' => 'text',
						'tip' => sprintf( __( "Your 2Checkout Account Number. You must have a <a target='_new' href='%s'>2Checkout</a> account.", JR_FX_i18N_DOMAIN ), JR_FX_GATEWAY_INFO_2CO_URL ),
					),
					array( 'title' => __( 'Secret Word', JR_FX_i18N_DOMAIN ),
						'name' => 'jr_fx_secret_word',
						'type' => 'text',
						'default' => 'tango',
					),
					array( 'title' => __( 'Demo Mode', JR_FX_i18N_DOMAIN ),
						'name' => 'jr_fx_enable_sandbox',
						'type' => 'checkbox',
						'desc' => __( 'Enable', JR_FX_i18N_DOMAIN )
					),
				)
		);

		return array( $general );
	}

	protected function is_valid( $order, $options ){

		$ipn_log = new JR_FX_IPN_Log( '2Checkout' );

		$ipn_data = array();
		foreach ( $_POST as $field => $value ) {
			$ipn_data[$field] = $value;
		}

		$ipn_data['transaction_id'] = $ipn_data["order_number"];

		$vendorNumber = ! empty( $ipn_data["vendor_number"] ) ? $ipn_data["vendor_number"] : $ipn_data["sid"];
		$orderNumber = $ipn_data["order_number"];
		$orderTotal = $ipn_data["total"];

		// If demo mode, the order number must be forced to 1
		if( !empty( $options['enable_sandbox'] ) || isset($ipn_data['demo']) && 'Y' == strtoupper($ipn_data['demo']) ) {
			$orderNumber = "1";
		}

		// Calculate md5 hash as 2co formula: md5(secret_word + vendor_number + order_number + total)
		$key = strtoupper(md5($options['jr_fx_secret_word'] . $vendorNumber . $orderNumber . $orderTotal));

		// verify if the key is accurate
		if ( $ipn_data["key"] == $key || $ipn_data["x_MD5_Hash"] == $key || $orderNumber == "1" ) {
			$ipn_log->logResults( $ipn_data, 'Valid HASH!' );
			return true;
		} else {
			$ipn_log->logResults( $ipn_data, 'Invalid HASH!' );
			return false;
		}

	}

}

/**
* FXtender 2Checkout class
*/

class JR_FX_2Checkout extends JR_FX_Payment_Gateway {

	protected $vendor_id;
	protected $secret_key;

	public function __construct( $order, $options, $return_url ) {
		global $jr_options;

		$this->order = $order;
		$this->return_url = $return_url;

		$this->gateway = '2Checkout';

		$this->button = JR_FX_PLUGIN_URL . "images/2co.gif";
		$this->gatewayUrl = 'https://www.2checkout.com/checkout/spurchase';
		$this->currency = $jr_options->currency_code;
		//$this->serverType = $options['jr_fx_enable_sandbox']; // set server type: Live | Sandbox

		$this->setUserInfo( $options['jr_fx_account_number'], $options['jr_fx_secret_word'] );

		// check for sandbox mode
		if ( $options['jr_fx_enable_sandbox'] ):
			$this->enableTestMode();
		endif;
	}

	public function setUserInfo( $vendor_id, $word ) {
		$this->vendor_id = $vendor_id;
		$this->secret_key = $word;
	}

	public function enableTestMode() {
		$this->testMode = true;
	}

	public function process() {
		$args = array(
			'sid' 				=> $this->vendor_id,
			'tco_currency' 		=> $this->currency,
			'x_invoice_num' 	=> $this->order->get_id(),
			'cart_order_id' 	=> $this->order->get_id(),
			'merchant_order_id' => $this->order->get_id(),
			'id_type' 			=> 1,
			'total' 			=> $this->order->get_total(),
			'c_prod' 			=> $this->order->get_id(),
			'c_name' 			=> urlencode($this->order->get_description()),
			'c_description' 	=> urlencode($this->order->get_description()),
			'c_price' 			=> $this->order->get_total(),
			'return_url' 		=> $this->return_url,
			'x_receipt_link_url'=> $this->return_url,
		);
		
		if ( $this->testMode ) $args['demo'] = 'Y';

		// redirect user to the gateway payment page
		wp_redirect( add_query_arg( $args, $this->gatewayUrl ) );
		exit;

	}

}

// register 2Checkout currencies into JobRoller
function jr_fx_init_2checkout_currencies(){

	$currencies = array(
		'AED' => array(
			'name' => __( 'United Arab Emirates Dirham', JR_FX_i18N_DOMAIN ),
		),
		'ARS' => array(
			'symbol' => '$',
			'name' => __( 'Argentine Peso', JR_FX_i18N_DOMAIN ),
		),
		'AUD' => array(
			'name' => __('Australian Dollars',JR_FX_i18N_DOMAIN),
		),
		'BGN' => array(
			'name' => __('Bulgarian Lev',JR_FX_i18N_DOMAIN),
		),
		'BRL' => array(
			'name' => __('Brazilian Real',JR_FX_i18N_DOMAIN),
		),
		'CAD' => array(
			'name' => __('Canadian Dollars',JR_FX_i18N_DOMAIN),
		),
		'CHF' => array(
			'name' => __('Swiss Francs',JR_FX_i18N_DOMAIN),
		),
		'CLP' => array(
			'name' => __('Chilean Peso',JR_FX_i18N_DOMAIN),
		),
		'DKK' => array(
			'name' => __('Danish Kroner',JR_FX_i18N_DOMAIN),
		),
		'EUR' => array(
			'name' => __('Euros',JR_FX_i18N_DOMAIN),
		),
		'GBP' => array(
			'name' => __('British Pounds Sterling',JR_FX_i18N_DOMAIN),
		),
		'HKD' => array(
			'name' => __('Hong Kong Dollars',JR_FX_i18N_DOMAIN),
		),
		'IDR' => array(
			'name' => __('Indonesian Rupiah',JR_FX_i18N_DOMAIN),
		),
		'ILS' => array(
			'name' => __('Israeli New Shekel',JR_FX_i18N_DOMAIN),
		),
		'JPY' => array(
			'name' => __('Japanese Yen',JR_FX_i18N_DOMAIN),
		),
		'MXN' => array(
			'name' => __('Mexican Peso',JR_FX_i18N_DOMAIN),
		),
		'MYR' => array(
			'name' => __('Malaysian Ringgit',JR_FX_i18N_DOMAIN),
		),
		'NOK' => array(
			'name' => __('Norwegian Kroner',JR_FX_i18N_DOMAIN),
			),
		'NZD' => array(
			'name' => __('New Zealand Dollars',JR_FX_i18N_DOMAIN),
		),
		'PHP' => array(
			'name' => __('Philippine Peso',JR_FX_i18N_DOMAIN),
		),
		'SEK' => array(
			'name' => __('Swedish Kronor',JR_FX_i18N_DOMAIN),
		),
		'SGD' => array(
			'name' => __('Singapore Dollar',JR_FX_i18N_DOMAIN),
		),
		'TRY' => array(
			'name' => __('Turkish Lira',JR_FX_i18N_DOMAIN),
		),
		'UAH' => array(
			'name' => __('Ukrainian Hryvnia',JR_FX_i18N_DOMAIN),
		),
		'USD' => array(
			'name' => __('US Dollars',JR_FX_i18N_DOMAIN),
		),
		'INR' => array(
			'name' => __( 'Indian Rupee', JR_FX_i18N_DOMAIN ),
		),
		'LTL' => array(
			'symbol' => 'Lt',
			'name' => __( 'Lithuanian Litas', JR_FX_i18N_DOMAIN ),
		),
		'RON' => array(
			'name' => __( 'Romanian Leu', JR_FX_i18N_DOMAIN ),
			'display' => '{price} {symbol}'
		),
		'RUB' => array(
			'name' => __( 'Russian Ruble', JR_FX_i18N_DOMAIN ),
		),
		'ZAR' => array(
			'symbol' => 'R',
			'name' => __( 'South African Rand', JR_FX_i18N_DOMAIN ),
		),
	);

	foreach( $currencies as $currency_code => $args )
		APP_Currencies::add_currency( $currency_code, $args );
}
