<?php

class JR_FX_Shortcodes {

	private $shortcode_id;
	private $description;

	function __construct( $shortcode_id = 0, $description = '' ) {
		$this->shortcode_id = $shortcode_id;
		$this->description = $description;
	}

	function get_shortcode() {
		switch( $this->shortcode_id ){
			case 'order_id':
				$shortcode = '[jr_fx_mp_order_id]';
				break;
			case 'order_total':
				$shortcode = '[jr_fx_mp_order_cost]';
				break;
			case 'order_return':
				$shortcode = '[jr_fx_mp_redirect title="Return to Dashboard"]';
				break;
			default:
				break;
		}
		return $this->render_shortcode( $shortcode );
	}

	function render_shortcode( $shortcode ){
		if ( $this->description ) html( 'p', $this->description );
		return html( 'label', array(), html( 'input', array(
			'type' => 'text',
			'class' => 'regular-text code',
			'value' => $shortcode,
			'size' => strlen( $shortcode ),
			'style' => 'width: 35em; background-color: #F8F8F8'
		))) . $this->description;
	}

	function notes() {
		return html( 'p', html( 'strong', __( 'Note:', JR_FX_i18N_DOMAIN ) ) . ' ' . __( 'The shortcodes bellow can only be used on the dynamic page.', JR_FX_i18N_DOMAIN ) );
	}
}

class JR_FX_Bank_Transfer_Gateway extends APP_Bank_Transfer_Gateway {

	public function __construct(){
		parent::__construct( 'bank-transfer', array(
			'admin' => __( 'Bank Transfer (FXtender)', JR_FX_i18N_DOMAIN ),
			'dropdown' => __( 'Bank Transfer', JR_FX_i18N_DOMAIN )
		));
	}

	public function form() {

		$form_values = array(

			array(
				'title' => __( 'Transfer Information', JR_FX_i18N_DOMAIN ),
				'type' => 'textarea',
				'name' => 'message',
				'extra' => array(
					'style' => 'width:500px;height:100px'
				),
			),

		);

		$return_array = array(
			'title' => __( 'Static Text', JR_FX_i18N_DOMAIN ),
			'desc' => __( 'The shortcodes bellow allow you to customize your manual payment instructions page. Using these shortcodes you will be able to show the Job Order ID to the Job Lister and set the page to redirect him after reading the instructions.', JR_FX_i18N_DOMAIN ),
			'fields' => $form_values
		);

		$form_redirect = array(

			array( 
				'title' => __( 'Instructions Page', JR_FX_i18N_DOMAIN ),
				'name' => 'jr_fx_page',
				'type' => 'select',
				'std' => 'none',
				'values' => $this->get_pages(),
				'tip' => __( "The page where your customers will be redirected after selecting 'Bank Transfer'. This page should contain details on how to complete the order. You can use the shortcodes bellow to output the Order ID and total price.", JR_FX_i18N_DOMAIN ),
			),

			array( 
				'title' => '',
				'name' => 'jr_fx_order_total_return',
				'type' => 'custom',
				'render' => array( new JR_FX_Shortcodes(), 'notes' ),
			),

		);

		$form_shortcodes = array(

			array( 
				'title' => __( 'Order ID Shortcode', JR_FX_i18N_DOMAIN ),
				'name' => 'jr_fx_order_id_shortcode',
				'type' => 'custom',
				'render' => array( 
					new JR_FX_Shortcodes('order_id',
						__( 'Paste this shortcode on your <em>Manual Payment Instructions Page</em>.
							<br/><br/><strong>Parameters:</strong><br/><span class="jr_fx_highlight_value code">class</span> <small>CSS class name for the Order ID text</small>
							<br/><br/>
							<strong>Example:</strong><br/>
							<span class="jr_fx_highlight_value">Please take note of your Order ID: [jr_fx_mp_order_id] it will indentify you as the buyer. This is also the ID we use to check for received payments.</span>
							', JR_FX_i18N_DOMAIN ) ), 
							'get_shortcode' ), 
				'tip' => __( 'This shortcode outputs the ID that identifies the customer Order. The customer must use this ID on whichever manual payment is used in order to indentify his job', JR_FX_i18N_DOMAIN ),
			),

			array( 
				'title' => __( 'Order Total Shortcode', JR_FX_i18N_DOMAIN ),
				'name' => 'jr_fx_order_total_shortcode',
				'type' => 'custom',
				'render' => array( 
					new JR_FX_Shortcodes('order_total',
						__( 'Paste this shortcode on your <em>Manual Payment Instructions Page</em>.
							<br/><br/><strong>Parameters:</strong><br/><span class="jr_fx_highlight_value code">class</span> <small>CSS class name for the Order Cost text</small>
							<br/><br/>
							<strong>Example:</strong><br/>
							<span class="jr_fx_highlight_value">Please take note of your Order Cost: [jr_fx_mp_order_cost].</span>
							', JR_FX_i18N_DOMAIN ) ), 
							'get_shortcode' ), 
				'tip' => __( 'This shortcode outputs the Order total price.', JR_FX_i18N_DOMAIN ),
			),

			array( 
				'title' => __( 'Return To Shortcode', JR_FX_i18N_DOMAIN ),
				'name' => 'jr_fx_order_total_return',
				'type' => 'custom',
				'render' => array( 
					new JR_FX_Shortcodes('order_return',
						__( 'Paste this shortcode on your <em>Manual Payment Instructions Page</em>.
							<br/><br/><strong>Parameters:</strong><br/><span class="jr_fx_highlight_value code">title</span> <small>the button title (optional)</small> <br/><span class="jr_fx_highlight_value code">page_id</span> <small>the page ID (optional)</small> <strong>OR</strong> <span class="jr_fx_highlight_value code">permalink</span> <small>the page permalink (optional)</small><br/><span class="jr_fx_highlight_value code">class</span> <small>CSS class name for the button styling</small></span>
							<br/><br/>
							<strong>Example:</strong><br/>
							<span class="jr_fx_highlight_value">[jr_fx_mp_redirect title="Continue" page_id="' . jr_fx_get_page_id('dashboard_page_id') . '" class="button"]</span>
							', JR_FX_i18N_DOMAIN ) ),
							'get_shortcode' ), 
				'tip' 		=> __( 'This shotrcode outputs a button that allows redirecting the customer to a specific page. All the parameters are optional. By default it will redirect customers to their dashboard.', JR_FX_i18N_DOMAIN ),
			),

		);

		$return_array_redirect = array(
			"title" => __( 'Dynamic Page', JR_FX_i18N_DOMAIN ),
			"fields" => $form_redirect
		);

		$return_array_sh = array(
			"title" => __( 'Shortcodes', JR_FX_i18N_DOMAIN ),
			"fields" => $form_shortcodes
		);

		return array( $return_array, $return_array_redirect, $return_array_sh );

	}

	function get_pages() {
		$pages['none'] = __( 'No Redirect', JR_FX_i18N_DOMAIN );
		$pages[''] = 'Frontpage';
		foreach( get_pages() as $page ) {
			$pages[ $page->ID ] = $page->post_title;
		}
		return $pages;
	}

	function get_options() {
		return APP_Gateway_Registry::get_gateway_options( $this->identifier() );
	}

}