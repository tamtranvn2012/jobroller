<?php

define( 'APP_FRAMEWORK_DIR', dirname(__FILE__) );
if ( ! defined( 'APP_FRAMEWORK_DIR_NAME' ) )
	define( 'APP_FRAMEWORK_DIR_NAME', 'framework' );

if ( ! defined( 'APP_FRAMEWORK_URI' ) )
	define( 'APP_FRAMEWORK_URI', get_template_directory_uri() . '/' . APP_FRAMEWORK_DIR_NAME );

// scbFramework
require dirname( __FILE__ ) . '/scb/load.php';

require dirname( __FILE__ ) . '/kernel/functions.php';

appthemes_load_textdomain();

require dirname( __FILE__ ) . '/kernel/deprecated.php';
require dirname( __FILE__ ) . '/kernel/hooks.php';

require dirname( __FILE__ ) . '/kernel/view-types.php';
require dirname( __FILE__ ) . '/kernel/view-edit-profile.php';

require dirname( __FILE__ ) . '/kernel/mail-from.php';

// Breadcrumbs plugin
if ( !is_admin() && !function_exists( 'breadcrumb_trail' ) ) {
	require dirname( __FILE__ ) . '/kernel/breadcrumb-trail.php';
}

function _appthemes_after_scb_loaded() {
	if ( is_admin() ) {
		require dirname( __FILE__ ) . '/admin/functions.php';

		require dirname( __FILE__ ) . '/admin/class-dashboard.php';
		require dirname( __FILE__ ) . '/admin/class-tabs-page.php';

		if ( version_compare( $GLOBALS['wp_version'], '3.5-alpha', '<' ) ) {
			require dirname( __FILE__ ) . '/admin/taxonomy-columns.php';
		}
	}
}
scb_init( '_appthemes_after_scb_loaded' );

function _appthemes_load_features() {

	if ( current_theme_supports( 'app-wrapping' ) )
		require dirname( __FILE__ ) . '/includes/wrapping.php';

	if ( current_theme_supports( 'app-geo' ) )
		require dirname( __FILE__ ) . '/includes/geo.php';

	if ( current_theme_supports( 'app-login' ) ) {
		require dirname( __FILE__ ) . '/includes/views-login.php';

		list( $templates ) = get_theme_support( 'app-login' );

		new APP_Login( $templates['login'] );
		new APP_Registration( $templates['register'] );
		new APP_Password_Recovery( $templates['recover'] );
		new APP_Password_Reset( $templates['reset'] );
	}

	if ( current_theme_supports( 'app-feed' ) )
		add_filter( 'request', 'appthemes_modify_feed_content' );

	if ( current_theme_supports( 'app-stats' ) ) {
		list( $options ) = get_theme_support( 'app-stats' );

		scb_register_table( 'app_stats_daily', $options['table_daily'] );
		scb_register_table( 'app_stats_total', $options['table_total'] );

		add_action( 'appthemes_first_run', 'appthemes_install_stats_tables', 9 );
		add_action( 'wp_ajax_reset-stats', 'appthemes_reset_stats_ajax' );
	}

	if ( is_admin() && current_theme_supports( 'app-versions' ) )
		require dirname( __FILE__ ) . '/admin/versions.php';

	if ( current_theme_supports( 'app-term-counts' ) )
		require dirname( __FILE__ ) . '/includes/term-counts.php';

	if ( current_theme_supports( 'app-plupload' ) )
		require dirname( __FILE__ ) . '/app-plupload/app-plupload.php';
}
add_action( 'after_setup_theme', '_appthemes_load_features', 999 );

// Default filters
add_filter( 'wp_title', 'appthemes_title_tag', 9 );
add_action( 'wp_head', 'appthemes_favicon' );
add_action( 'admin_head', 'appthemes_favicon' );

