<?php
/**
 * Theme functions file
 *
 * DO NOT MODIFY THIS FILE. Make a child theme instead: http://codex.wordpress.org/Child_Themes
 *
 * @package JobRoller
 * @author AppThemes
 */

// Define vars and globals
global $app_version, $app_form_results, $jr_log, $app_abbr;

// current version
$app_theme = 'JobRoller';
$app_abbr = 'jr';
$app_version = '1.7.1';

define( 'APP_TD', 'jobroller' );
define( 'JR_VERSION' , $app_version );
define( 'JR_FIELD_PREFIX', '_' . $app_abbr . '_' );

// Framework
require( dirname(__FILE__) . '/framework/load.php' );

// Payments Framework
require dirname( __FILE__ ) . '/includes/payments/load.php';

scb_register_table( 'app_pop_daily', $app_abbr . '_counter_daily' );
scb_register_table( 'app_pop_total', $app_abbr . '_counter_total' );

require( dirname(__FILE__) . '/framework/includes/stats.php' );

// Custom forms
require dirname( __FILE__ ) . '/includes/custom-forms/form-builder.php';

// Theme-specific files
require( dirname(__FILE__) . '/includes/theme-functions.php' );
