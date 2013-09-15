<?php

require 'bt-emails.php';

if( is_admin() ){
	require 'bt-admin.php';
	new APP_Bank_Transfer_Queue;
}

/**
 * Payment Gateway for processing payments via Bank Transfer
 * or other manual method
 */
class APP_Bank_Transfer_Gateway extends APP_Gateway{

	/**
	 * Sets up the gateway
	 */
	public function __construct() {
		parent::__construct( 'bank-transfer', array(
			'dropdown' => __( 'Bank Transfer', APP_TD ),
			'admin' => __( 'Bank Transfer', APP_TD ),
		) );

	}

	/**
	 * Builds the administration settings form
	 * @return array scbForms style form
	 */
	public function form() {

		$form_values = array(

			array(
				'title' => __( 'Transfer Information', APP_TD ),
				'type' => 'textarea',
				'name' => 'message',
				'extra' => array(
					'style' => 'width:500px;height:100px'
				),
			),

		);

		$return_array = array(
			"title" => __( 'General Information', APP_TD ),
			"fields" => $form_values
		);

		return $return_array;

	}

	/**
	 * Processes a Bank Transfer Order to display
	 * instructions to the user
	 * @param  APP_Order $order   Order to display information for
	 * @param  array $options     User entered options
	 * @return void
	 */
	public function process( $order, $options ) {

		$sent = get_post_meta( $order->get_ID(), 'bt-sentemail', true );
		if( empty( $sent ) ){
			appthemes_bank_transfer_pending_email( get_post( $order->get_ID() ) );
			update_post_meta( $order->get_ID(), 'bt-sentemail', true );
		}

	?>
	<style type="text/css">
		#bank-transfer fieldset{
			margin-bottom: 20px;
		}
		#bank-transfer .content{
			width: auto;
			padding: 20px;
		}
		#bank-transfer pre{
			font-family: Arial, Helvetica, sans-serif;
			font-size: 12px;
			padding-top: 10px;
			white-space: pre-wrap;
		}
	</style>
	<div class="section-head"><h2><?php _e( 'Bank Transfer', APP_TD ); ?></h2></div>
	<form id="bank-transfer">
		<fieldset>
			<div class="featured-head"><h3><?php _e( 'Instructions:', APP_TD ); ?></h3></div>

			<div class="content">
				<pre><?php echo ( isset( $options['message'] ) ) ? $options['message'] : '' ; ?></pre>
			</div>
		</fieldset>
		<fieldset>
			<div class="featured-head"><h3><?php _e( 'Order Information:', APP_TD ); ?></h3></div>

			<div class="content">
<pre><?php _e( 'Order ID:', APP_TD ); ?> <?php echo $order->get_id(); ?> 
<?php _e( 'Order Total:', APP_TD ); ?> <?php echo appthemes_get_price( $order->get_total(), $order->get_currency() ); ?>


<?php _e( 'For questions or problems, please contact us directly at', APP_TD ) ?> <?php echo get_option('admin_email'); ?>
</pre>
			</div>
		</fieldset>
	</form>
	<div class="clear"></div>
	<?php
	}
}

appthemes_register_gateway( 'APP_Bank_Transfer_Gateway' );
