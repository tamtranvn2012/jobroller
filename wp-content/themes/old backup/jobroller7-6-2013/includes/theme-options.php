<?php

// retrieve default options based on legacy settings
function _jr_options_defaults( $option ) {

	$sep_relation = array( 'comma' => ',', 'decimal' => '.' );
	$featured_price = get_option( 'jr_cost_to_feature', 0 );
	$plan_type = get_transient( 'jr_plan_type' );

	$gateways = array();

	if ( $paypal_email = get_option( 'jr_jobs_paypal_email' ) ) {
		$gateways = array(
			'enabled' => array( 'paypal' => 'yes' ),
			'paypal' => array (
				'email_address'		=> $paypal_email,
				'ipn_enabled' 		=> 'no' == get_option( 'jr_enable_paypal_ipn', 'no' ) ? '' : 'on',
				'sandbox_enabled'	=> 'no' == get_option( 'jr_use_paypal_sandbox', 'no' ) ? '': 'on',
				'business_account'	=> 'manual' == get_option( 'jr_resume_subscr_recurr_type' ) ? '' : 'on',
			),
		);
	} else {
		$gateways = array(
			'enabled' => array(),
		);
	}

	$legacy_options = array(
		'job_duration' 			=> get_option( 'jr_jobs_default_expires', 30 ),
		'featured_price' 		=> $featured_price,
		'featured_enabled' 		=> (bool) $featured_price,
		'separate_packs'		=> get_option( 'jr_packs_dashboard_buy', 'no' ),
		'plan_type' 			=> $plan_type ? $plan_type : 'single',
		'currency_decimal_sep' 	=> $sep_relation[ get_option( 'jr_curr_decimal_separator', 'comma' ) ],
		'currency_thousands_sep'=> $sep_relation[ get_option( 'jr_curr_thousands_separator', 'comma' ) ],
		'currency_position'		=> get_option( 'jr_curr_symbol_pos', 'left' ),
		'currency_code'			=> get_option( 'jr_jobs_paypal_currency', 'USD' ),
		'gateways'				=> $gateways,
		'plan_display_cats'		=> get_option( 'jr_packs_job_categories', 'no' ),
	);
	return $legacy_options[$option];
}

$GLOBALS['jr_options'] = new scbOptions( 'jr_options', false, array(
	'currency_code' 		=> _jr_options_defaults( 'currency_code' ),
	'currency_identifier' 	=> 'symbol',
	'currency_position' 	=> _jr_options_defaults( 'currency_position' ),
	'thousands_separator' 	=> _jr_options_defaults( 'currency_thousands_sep' ),
	'decimal_separator' 	=> _jr_options_defaults( 'currency_decimal_sep' ),

	'tax_charge' => 0,

	'plan_type' => _jr_options_defaults( 'plan_type' ),

	'separate_packs' => _jr_options_defaults( 'separate_packs' ),
	'plan_display_cats' => _jr_options_defaults( 'plan_display_cats' ),

	// Featured Listings
	'addons' => array(
		JR_ITEM_FEATURED_LISTINGS => array(
			'enabled' 	=> _jr_options_defaults( 'featured_enabled' ),
			'price' 	=> _jr_options_defaults( 'featured_price' ),
			'duration' 	=> _jr_options_defaults( 'job_duration' ),
		),

		JR_ITEM_FEATURED_CAT => array(
			'enabled' 	=> '',
			'price' 	=> _jr_options_defaults( 'featured_price' ),
			'duration' 	=> _jr_options_defaults( 'job_duration' ),
		),

		JR_ITEM_BROWSE_RESUMES => array(
			'enabled' 	=> '',
			'price'		=> 0,
			'duration' 	=> 30,
		),

		JR_ITEM_VIEW_RESUMES => array(
			'enabled' 	=> '',
			'price' 	=> 0,
			'duration' 	=> 30,
		),
	),

	// Gateways
	'gateways' => _jr_options_defaults( 'gateways' ),
) );
