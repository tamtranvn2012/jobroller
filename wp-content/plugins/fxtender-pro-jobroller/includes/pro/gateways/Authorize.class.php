<?php

/**
 * Authorize.net Class
 *
 * Integrate the Authorize.net payment gateway in your site using this
 * easy to use library. Just see the example code to know how you should
 * proceed. Also, remember to read the readme file for this class.
 *
 * @package     Payment Gateway
 * @category	Library
 * @author      Md Emran Hasan <phpfour@gmail.com>
 * @modified by Bruno Carreço
 * @link        http://www.phpfour.com
 * @adapted by 	Bruno Carreço
 * @link        http://www.bruno-carreco.com 
 */

include_once ('PaymentGateway.class.php');

class JR_FX_Authorize_APP extends APP_Boomerang {

	protected $options;

	public function __construct(){
		parent::__construct( 'authorize-net', array(
			'admin' => __( 'Authorize.Net (FXtender)', JR_FX_i18N_DOMAIN ),
			'dropdown' => __( 'Authorize.Net', JR_FX_i18N_DOMAIN )
		));
	}

	public function create_form( $order, $options ) {
		$gateway = new JR_FX_Authorize( $order, $options, $this->get_return_url( $order ) );
		$gateway->process();
	}

	public function form(){

		$general = array(
			'title' => __( 'Authorize', JR_FX_i18N_DOMAIN ),
			'fields' => array (
					array( 'title' => __( 'API Login ID', JR_FX_i18N_DOMAIN ),
						'name' => 'jr_fx_login_id',
						'type' => 'text',
						'tip' => sprintf( __( "Your Authorize.Net Login ID. You must have a <a target='_new' href='%s'>Authorize.Net</a> account.", JR_FX_i18N_DOMAIN ), JR_FX_GATEWAY_INFO_AUTHORIZE_URL ),
					),
					array( 'title' => __( 'Transaction Key', JR_FX_i18N_DOMAIN ),
						'name' => 'jr_fx_transaction_key',
						'type' => 'text',
					),
					array( 'title' => __( 'Sandbox Mode', JR_FX_i18N_DOMAIN ),
						'name' => 'jr_fx_enable_sandbox',
						'type' => 'checkbox',
					),
				)
		);

		return array( $general );
	}

	protected function is_valid( $order, $options ){

		$ipn_log = new JR_FX_IPN_Log( 'Authorize.net' );

		$ipn_data = array();
		foreach ( $_GET as $field => $value ) {
			$ipn_data[$field] = $value;
		}

		// Authorize.net in sandbox box always returns Transaction ID = 0
		// We generate a random number just to test the IPN
		if ( !empty( $options['enable_sandbox'] ) && $ipn_data['x_trans_id'] == 0) {
			$ipn_data['x_trans_id'] = rand(1,99999);
		}

		//$invoice	= intval($ipn_data['x_invoice_num']);
		$invoice	= $ipn_data['x_invoice_num'];
		$pnref		= $ipn_data['x_trans_id'];
		$amount		= doubleval($ipn_data['x_amount']);
		$result		= intval($ipn_data['x_response_code']);
		$respmsg	= $ipn_data['x_response_reason_text'];

		$md5source	= $options['jr_fx_transaction_key'] . $options['jr_fx_login_id'] . $ipn_data['x_trans_id'] . $ipn_data['x_amount'];
		$md5		= md5($md5source);

		if ( $result == '1' ) {
		 	// Valid IPN transaction
			$ipn_log->logResults( $ipn_data, 'Valid IPN!' );
			return true;
		} elseif ( $result != '1' ) {
			$ipn_log->logResults( $ipn_data, 'Invalid IPN!' );
			return false;
		} elseif ( strtoupper($md5) != $ipn_data['x_MD5_Hash'] ) {
			$ipn_log->logResults( $ipn_data, 'Invalid HASH!' );
			return false;
		}
	}

}

class JR_FX_Authorize extends JR_FX_Payment_Gateway {

	protected $login;
	protected $secret;

	/**
	 * Initialize the Authorize.net gateway
	 *
	 * @param none
	 * @return void
	 */
	public function __construct( $order, $options ) {
		global $jr_options;

		$this->order = $order;

		$this->button = JR_FX_PLUGIN_URL . "images/authorizenet.gif";

		// Some default values of the class
		$this->gatewayUrl = 'https://secure.authorize.net/gateway/transact.dll';
		$this->gateway = 'Authorize.net';

		$this->currency = $jr_options->currency_code;
		$this->serverType = $options['jr_fx_enable_sandbox']; // set server type: Live | Sandbox

		$this->setUserInfo( $options['jr_fx_login_id'], $options['jr_fx_transaction_key'] );

		// check for sandbox mode
		if ( $options['jr_fx_enable_sandbox'] ):
			$this->enableTestMode();
		endif;
	}

	/**
	 * Enables the test mode
	 *
	 * @param none
	 * @return none
	 */
	public function enableTestMode() {
		$this->testMode = true;
		$this->addField('x_Test_Request', 'TRUE');
		$this->gatewayUrl = 'https://test.authorize.net/gateway/transact.dll';
	}

	/**
	 * Set login and secret key
	 *
	 * @param string user login
	 * @param string secret key
	 * @return void
	 */
	public function setUserInfo( $login, $key ) {
		$this->login  = $login;
		$this->secret = $key;
	}

	/**
	 * Prepare a few payment information
	 *
	 * @param none
	 * @return void
	 */
	public function prepareSubmit() {
		$this->addField('x_Login', $this->login);
		$this->addField('x_fp_sequence', $this->fields['x_Invoice_num']);
		$this->addField('x_fp_timestamp', time());

		$data = $this->fields['x_Login'] . '^' . $this->fields['x_Invoice_num'] . '^' . $this->fields['x_fp_timestamp'] . '^' . $this->fields['x_Amount'] . '^';

		$this->addField('x_fp_hash', $this->hmac($this->secret, $data));
	}

	public function process() {

		// Populate $fields array with a few default
		$this->addField('x_Version','3.1');
		$this->addField('x_Show_Form','PAYMENT_FORM');

		// Specify the url where authorize.net will send the user on success/failure
		$this->addField('x_receipt_link_method', 'GET');
		$this->addField('x_Receipt_Link_URL', $this->order->get_return_url());
		$this->addField('x_Receipt_Link_text', sprintf( __( 'Return to %s', JR_FX_i18N_DOMAIN ), get_bloginfo('name') ));

		$this->addField('x_cancel_url', $this->order->get_cancel_url());
		$this->addField('x_cancel_url_text', __( 'Cancel', JR_FX_i18N_DOMAIN ));

		// Specify the product information
		$this->addField('x_Description', htmlentities( $this->order->get_description(),  null, $this->charSet ));
		$this->addField('x_Amount', $this->order->get_total());
		$this->addField('x_Invoice_num', $this->order->get_id());
		
		$this->addField('_wpnonce', wp_create_nonce( 'authorize-net' ));

		$this->prepareSubmit();

		// Let's start the train!
		$this->submitPayment();
	}

	/**
	 * RFC 2104 HMAC implementation for php.
	 *
	 * @author Lance Rushing
	 * @param string key
	 * @param string date
	 * @return string encoded hash
	 */
	private function hmac ($key, $data) {
		$b = 64; // byte length for md5

		if (strlen($key) > $b) {
			$key = pack("H*",md5($key));
		}

		$key  = str_pad($key, $b, chr(0x00));
		$ipad = str_pad('', $b, chr(0x36));
		$opad = str_pad('', $b, chr(0x5c));
		$k_ipad = $key ^ $ipad ;
		$k_opad = $key ^ $opad;

		return md5($k_opad  . pack("H*", md5($k_ipad . $data)));
	}
}
