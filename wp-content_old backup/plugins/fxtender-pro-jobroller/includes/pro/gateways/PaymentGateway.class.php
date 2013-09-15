<?php

/**
 * Payment Gateway
 *
 * This library provides generic payment gateway handling functionlity
 * to the other payment gateway classes in an uniform way. Please have
 * a look on them for the implementation details.
 *
 * @package     Payment Gateway
 * @category    Library
 * @author      Md Emran Hasan <phpfour@gmail.com>
 * @link        http://www.phpfour.com
 *
 * @adapted by 	Bruno Carreço
 */

abstract class JR_FX_Payment_Gateway {

	public $fields = array();

	public $gateway;
	public $gatewayUrl;

	public $currency;
	public $serverType;
	public $button;
	public $charSet;

	public $testMode;

	public $order;
	public $return_url;

	/**
	 * Initialization constructor
	 *
	 * @param none
	 * @return void
	 */
	public function __construct() {
		$this->testMode = false;
		$this->charSet = get_settings('blog_charset');
	}

	/**
	 * Adds a key=>value pair to the fields array
	 *
	 * @param string key of field
	 * @param string value of field
	 * @return
	 */
	public function addField($field, $value) {
		$this->fields[$field] = $value;
	}

	/**
	 * Submit Payment Request
	 *
	 * Generates a form with hidden elements from the fields array
	 * and submits it to the payment gateway URL. The user is presented
	 * a redirecting message along with a button to click.
	 *
	 * @param none
	 * @return void
	 */
	public function submitPayment() {

			$this->prepareSubmit();

			echo "You're now being redirected to the gateway...";

			echo "<body onLoad=\"document.forms['gateway_form'].submit();\">\n";
			echo '<form method="post" name="gateway_form" action="' . esc_url($this->gatewayUrl) . '" />';

			foreach ($this->fields as $name => $value) {
				 echo '<input type="hidden" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '"/>';
			}

			echo "</form>";
	}

	/**
	 * Perform any pre-posting actions
	 *
	 * @param none
	 * @return none
	 */
	protected function prepareSubmit() {}

	/**
	 * Enables the test mode
	 *
	 * @param none
	 * @return none
	 */
	abstract protected function enableTestMode();

}

class JR_FX_IPN_Log {

	public $gateway;

	public $logIpn;
	public $ipnLogFile;

	/**
	 * Initialization constructor
	 *
	 * @param none
	 * @return void
	 */
	public function __construct( $gateway ) {

		$this->logIpn = ( 'yes' == JR_FX_LOG );

		$this->gateway = $gateway;
		$this->ipnResponse = '';

		// log files
		$this->ipnLogFile	= JR_FX_PLUGIN_DIR . 'log/ipn_log.txt';
	}

	/**
	 * Logs the IPN results
	 *
	 * @param boolean IPN result
	 * @return void
	 */
	public function logResults( $data, $message = '' ) {

		if ( !$this->logIpn )
			return;

		// Timestamp
		//$text = '[' . date('m/d/Y H:i:s').'] - ';
		$text = '';

		if ( ! empty( $data ) ) {
			// Log the POST variables
			$text .= "IPN POST/GET Vars from gateway (".$this->gateway.") ";
		}

		$text .= print_r( $message, true );
		$text .= ' :: ';

		if ( is_array( $data ) ) {

			foreach ( $data as $key => $value ) {
				$text .= "$key=$value, ";
			}

		} else {
			$text .= print_r( $data, true );
		}

		$text = sprintf( "%s:- %s\n", date("D M j G:i:s T Y"), $text );

		// Write to log
		$fp = fopen( $this->ipnLogFile, 'a+' );
		fwrite( $fp, $text . "\n" );
		fclose( $fp );
	}

}
