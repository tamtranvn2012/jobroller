<?php

class APP_PayPal_IPN_Listener{

	const QUERY_VAR = 'paypal_ipn';

	private $callback;

	public function __construct( $callback ){
		$this->callback = $callback;
		$this->listen();
	}

	public function listen(){

		if( !isset( $_GET[ self::QUERY_VAR ] ) )
			return;

		$passphrase = get_option( 'paypal_listener_passphrase', false );
		if( ! $passphrase ){
	    		return;
		}

		if( $_GET[ self::QUERY_VAR ] != $passphrase ){
	        	return;
		}

		if( ! self::validate_request() )
			return;

		call_user_func( $this->callback, $_POST );

		die;

	}

	public function validate_request(){

		$post_string = '';    
		foreach ($_POST as $field=>$value) { 
			$post_string .= $field.'='.urlencode(stripslashes($value)).'&'; 
		}
		$post_string.="cmd=_notify-validate";

		$fsock_url = APP_PayPal::get_ssl_url();
		$fp = fsockopen ( $fsock_url, "443", $err_num, $err_str, 60 );

		$ipn_response = '';
		if(!$fp) {
			return false;
		} else { 

			$request_url = APP_PayPal::get_request_url();
			$url_parsed = parse_url( $request_url );

			fputs($fp, "POST {$url_parsed['path']} HTTP/1.1\r\n"); 
			fputs($fp, "Host: {$url_parsed['host']}\r\n"); 
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
			fputs($fp, "Content-length: ".strlen($post_string)."\r\n"); 
			fputs($fp, "Connection: close\r\n\r\n"); 
			fputs($fp, $post_string . "\r\n\r\n"); 

			while(!feof($fp)) { 
				$ipn_response .= fgets($fp, 1024); 
			} 
			fclose($fp); 

		}    

		if (!preg_match("/VERIFIED/s", $ipn_response)) {
			return false;
		} else {
			return true;
		}

	}

	public static function get_listener_url(){

		$passphrase = get_option( 'paypal_listener_passphrase', false );
		if( ! $passphrase ){
	        	$passphrase = md5( site_url() . time() );
		        update_option( 'paypal_listener_passphrase', $passphrase );
		}

		return add_query_arg( self::QUERY_VAR, $passphrase, site_url( '/' ) );
	}

}
