<?php

/**
 * Google Checkout Class
 *
 * Integrate Google Checkout
 *
 * @package     Payment Gateway
 * @category    Library
 * @author      Bruno Carreço
 * @link        http://www.bruno-carreco.com
 */

// Include all the required files

require_once ('PaymentGateway.class.php');

require_once('library/googlecart.php');
require_once('library/googleitem.php');

require_once('library/googleresponse.php');
require_once('library/googlerequest.php');

require_once('library/googlemerchantcalculations.php');
require_once('library/googlenotificationhistory.php');

class JR_FX_Google_Wallet_APP extends APP_Boomerang{

	public function __construct(){
		parent::__construct( 'google-wallet', array(
			'admin' => __( 'Google Wallet (FXtender)', JR_FX_i18N_DOMAIN ),
			'dropdown' => __( 'Google Wallet', JR_FX_i18N_DOMAIN )
		));

		add_action( 'init', array( $this, 'listener' ), 1000 );
	}

	public function create_form( $order, $options ) {

		$gateway = new JR_FX_Google_Wallet( $order, $options, $this->get_return_url( $order ) );
		$gateway->process();

	}

	public function form(){

		$general = array(
			'title' => __( 'Google Wallet', JR_FX_i18N_DOMAIN ),
			'fields' => array (
					array( 'title' => __( 'Merchant ID', JR_FX_i18N_DOMAIN ),
						'name' => 'jr_fx_merchant_id',
						'type' => 'text',
						'tip' => sprintf( __( "Your Google Wallet Merchant ID. Please note that Merchant ID's for Sandbox and Live mode are different. You must have a <a target='_new' href='%s'>Google Wallet</a> account.", JR_FX_i18N_DOMAIN ), JR_FX_GATEWAY_INFO_GOOGLE_URL ),
					),
					array( 'title' => __( 'Merchant Key', JR_FX_i18N_DOMAIN ),
						'name' => 'jr_fx_merchant_key',
						'type' => 'text',
					),
					array( 'title' => __( 'Sandbox Mode', JR_FX_i18N_DOMAIN ),
						'name' => 'jr_fx_enable_sandbox',
						'type' => 'checkbox',
					),
				)
		);

		$ipn = array(
			'title' => __( 'Gateway Responses (IPN)', JR_FX_i18N_DOMAIN ),
			'fields' => array (
					array( 'title' => __( 'Enable IPN', JR_FX_i18N_DOMAIN ),
						'name' => 'jr_fx_ipn',
						'type' => 'checkbox',
						'tip' => __( 'Turning off IPN means that you will need to manually complete orders after each payment. Disable it if IPN does not work for you.', JR_FX_i18N_DOMAIN ),
					),
					array( 'title' => __( 'Approved URL', JR_FX_i18N_DOMAIN ),
						'name' => 'jr_fx_ipn_url',
						'type' => 'custom',
						'render' => array( $this, 'listener_url' ),
						'name' => '_blank',
						'tip' => sprintf( __( '<h3>Instructions:</h3> 
								<br/>Under your account go to, <em>Settings > Integration</em>:
								<br/>1. Check <strong>My company will only post digitally signed carts</strong> (if not already checked)
								<br/>2. Paste this URL on the API Callback URL field
								<br/>3. Check the option <strong>Notification Serial Number</strong>
								<br/>4. Choose API Version 2.5
								<br/><br/>This will enable receiving Google Order responses (IPN) automatically. Please read <a href="%s">Google Checkout Merchant Help</a> if you need more help.
								<br/><br/><strong>Note:</strong> Make sure you have WP_DEBUG disabled on your \'wp-config.php\' file or the IPN listner may fail.
								', JR_FX_i18N_DOMAIN ), 'http://support.google.com/checkout/sell' ),
					),
				)
		);

		return array( $general, $ipn );
	}

	function listener(){
		global $jr_options;

		$ipn_log = new JR_FX_IPN_Log( 'Google Wallet' );

		if ( ! stristr( $_SERVER['HTTP_USER_AGENT'], "Google Checkout Notification Agent" ) )
			return;

		if ( ! isset($_GET['ipn_response']) || $_GET['ipn_response'] != 'gwallet' )
			return;

		$options = APP_Gateway_Registry::get_gateway_options( $this->identifier() );

		if ( ! $options['jr_fx_ipn'] )
			return;

		$merchant_id = $options['jr_fx_merchant_id'];
		$merchant_key = $options['jr_fx_merchant_key'];
		$server_type = $options['jr_fx_enable_sandbox'] ? 'sandbox' : 'live';

		$currency = $jr_options->currency_code;

		//log data
		$ipn_data["merchant_id"] = $merchant_id;
		$ipn_data["server_type"] = $server_type;
		$ipn_data["currency"] = $currency;
		$ipn_data["auto_charge"] = $auto_charge;

		//Create the response object
		$Gresponse = new GoogleResponse( $merchant_id, $merchant_key );

		$Gresponse->SetLogFiles( $ipn_log->ipnLogFile, $ipn_log->ipnLogFile, ( $ipn_log->logIpn ? L_ALL : L_OFF ) );

		$ipn_log->logResults( $ipn_data, 'IPN DATA' );

		$ipn_log->logResults( $Gresponse, 'Response' );

		//Retrieve the XML sent in the HTTP POST request to the ResponseHandler
		$xml_response = isset( $HTTP_RAW_POST_DATA )? $HTTP_RAW_POST_DATA : file_get_contents( "php://input" );

		$ipn_log->logResults( $xml_response, 'XML Response');

		//If serial-number-notification pull serial number and request xml
		if ( strpos( $xml_response, 'xml' ) == FALSE ) {

			$ipn_log->logResults( array(), 'Using Serial Number Authentication' );

			//Find serial-number ack notification
			$serial_array = array();

			parse_str( $xml_response, $serial_array );

			$serial_number = $serial_array['serial-number'];
			$ipn_log->logResults( $serial_number, 'Serial Number' );

			//Request XML notification
			$Grequest = new GoogleNotificationHistoryRequest( $merchant_id, $merchant_key, $server_type );

			$raw_xml_array = $Grequest->SendNotificationHistoryRequest( $serial_number );
			if ( $raw_xml_array[0] != 200 ){
				//Add code here to retry with exponential backoff
			} else {
				$raw_xml = $raw_xml_array[1];
			}

			$Gresponse->SendAck( $serial_number, false );

		} else {

			$ipn_log->logResults( array(), 'Using Basic Authentication' );

			//Else assume pre 2.5 XML notification
			//Check Basic Authentication
			$Gresponse->SetMerchantAuthentication($merchant_id, $merchant_key);
			$status = $Gresponse->HttpAuthentication();
			if( ! $status ) {
				$ipn_log->logResults( $status, 'Basic Authentication Failed' );
				die('authentication failed');
			}
			$raw_xml = $xml_response;

			$Gresponse->SendAck( null, false );

		}

		$ipn_log->logResults( $Gresponse, 'Google Response' );

		if ( get_magic_quotes_gpc() ) {
			$raw_xml = stripslashes($raw_xml);
		}

		//Parse XML to array
		list( $root, $data ) = $Gresponse->GetParsedXML( $raw_xml );

		$ipn_log->logResults( $root, 'Notification Type' );

		$ipn_log->logResults( $data[$root], 'Root Data' );

		$order = $this->get_order( $data[$root] );
		if( !$order ) {
			$ipn_log->logResults( $order, 'Unable to retrieve order' );
			return false;
		}

		$ipn_log->logResults( $order->get_status(), 'AppThemes Order Status' );

		if ( $order->get_status() == APPTHEMES_ORDER_COMPLETED )
			return;

		switch( $root ){

			case "error": {
				// Could not open the connection, log error if enabled
				$ipn_log->logResults( $ipn_data, 'Could not open the connection' );
				break;
			}

			case "authorization-amount-notification":
				$Grequest = new GoogleRequest($merchant_id, $merchant_key, $server_type, $currency);

				$google_order_number = $data[$root]['google-order-number']['VALUE'];
				$status = $Grequest->SendChargeOrder( $google_order_number );

				ob_start();
				if( $status[0] == 200 ){
					$order->complete();
				}
				else{
					$order->failed();
				}
				ob_end_clean();

				$ipn_log->logResults( $status, 'Auto Charge Status' );
				break;

			default:
				break;
		}
		exit();
	}

	private function get_order( $data ){

		$ipn_log = new JR_FX_IPN_Log( 'Google Wallet :: Get Order' );

		$ipn_log->logResults( $order_id, 'Retrieving Order #ID' );

		if ( empty( $data['order-summary']['shopping-cart']['items']['item'] ) )
			return false;
	
		$item = $data['order-summary']['shopping-cart']['items']['item'];
		$order_id = intval( str_ireplace( 'Order#', '', $item['item-name']['VALUE'] ) );

		$ipn_log->logResults( $order_id, 'Order #ID' );

		$order = appthemes_get_order( $order_id );
		if( !$order )
			return false;

		if( $order->get_total() != $item['unit-price']['VALUE'] )
			return false;

		if( $order->get_currency() != $item['unit-price']['currency'] )
			return false;

		return $order;
	}

	function listener_url(){
		$listener_url = add_query_arg( array( 'ipn_response' => 'gwallet' ), JR_FX_GW_RESPONSE_URL );
		return html( 'label', array(), html( 'input', array(
			'type' => 'text',
			'class' => 'regular-text',
			'value' => $listener_url,
			'size' => strlen( $listener_url ),
			'style' => 'width: 35em; background-color: #EEE'
		)));
	}

}


class JR_FX_Google_Wallet extends JR_FX_Payment_Gateway {

	var $merchant_id;
	var $merchant_key;

	/**
	 * Initialize the 2CheckOut gateway
	 *
	 * @param none
	 * @return void
	 */
	public function __construct( $order, $options, $return_url ) {
		global $jr_options;

		$this->order = $order;
		$this->return_url = $return_url;

		// Some default values of the class
		$this->gatewayUrl = 'https://checkout.google.com/checkout/api/checkout/v2/checkout/Merchant/' . $this->merchant_id;
		$this->gateway = 'Google';

		$this->currency = $jr_options->currency_code;
		$this->serverType = $options['jr_fx_enable_sandbox'] ? 'sandbox' : 'live'; // set server type: Live | Sandbox

		$this->setUserInfo( $options['jr_fx_merchant_id'], $options['jr_fx_merchant_key'] );

		// check for sandbox mode
		if ( $options['jr_fx_enable_sandbox'] ):
			$this->enableTestMode();
		endif;
	}

	/**
	 * Set user info
	 *
	 * @param string user login
	 * @param string secret key
	 * @return void
	 */
	public function setUserInfo( $merchant_id, $merchant_key ) {
		$this->merchant_id = $merchant_id;
		$this->merchant_key = $merchant_key;
	}

	/**
	 * Enables the test mode
	 *
	 * @param none
	 * @return none
	 */
	public function enableTestMode() {
		$this->testMode = TRUE;
		$this->gatewayUrl = 'https://sandbox.google.com/checkout/api/checkout/v2/checkout/Merchant/'.  $this->merchant_id;
	}

	public function process() {

		# create a Cart instance
		$cart = new GoogleCart( $this->merchant_id, $this->merchant_key, $this->serverType, $this->currency );

		$item = new GoogleItem( 'Order#'.$this->order->get_id(),										// Item name
								htmlentities( $this->order->get_description(), null, $this->charSet ),	// Item Description
								1, 																		// Quantity
								$this->order->get_total() 												// Item price
		); 

		$item->SetMerchantItemId( $this->order->get_id() );

		$cart->AddItem( $item );

		$cart->SetContinueShoppingUrl( $this->return_url );

		// run any custom code before the final redirect
		$this->prepareSubmit();

		// This will do a server-2-server cart post and send an HTTP 302 redirect status
		list( $status, $error ) = $cart->CheckoutServer2Server();

		// if i reach this point, something was wrong - output the error
		// hide sensitive data

		echo "ERROR=" . $status . ", " . $error;
		exit;
	}

}

