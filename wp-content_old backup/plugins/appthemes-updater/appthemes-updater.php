<?php
/*
Plugin Name: AppThemes Updater
Description: Allows customers to automatically update AppThemes Products.
Version: 1.2.1
Author: AppThemes
Author URI: http://appthemes.com
AppThemes ID: appthemes-updater
Network: true
Text Domain: appthemes-updater
*/

function is_app_updater_network_activated() {
	if ( !is_multisite() )
		return false;

	$plugins = get_site_option( 'active_sitewide_plugins' );

	return isset( $plugins[ plugin_basename( __FILE__ ) ] );
}

function app_updater_activate() {
	app_refresh_themes();
	app_refresh_plugins();
}

function app_refresh_themes() {
	delete_site_transient( 'update_themes' );
	wp_update_themes();
}

function app_refresh_plugins() {
	delete_site_transient( 'update_plugins' );
	wp_update_plugins();
}

function app_extra_headers( $headers ) {
	$headers['AppThemes ID'] = 'AppThemes ID';

	return $headers;
}

function app_updater_init() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'appthemes-updater' );
	load_textdomain( 'appthemes-updater', WP_LANG_DIR . "/plugins/appthemes-updater-$locale.mo" );

	require dirname( __FILE__ ) . '/updater-class.php';
	require dirname( __FILE__ ) . '/updater-ui.php';

	new APP_Theme_Upgrader;
	new APP_Plugin_Upgrader;

	if ( is_app_updater_network_activated() )
		$app_updater = new APP_Upgrader_Network;
	else
		$app_updater = new APP_Upgrader_Regular;
}

if ( is_admin() ) {
	app_updater_init();

	add_filter( 'extra_plugin_headers', 'app_extra_headers' );
	add_filter( 'extra_theme_headers', 'app_extra_headers' );

	add_action( 'load-update-core.php', 'app_updater_activate' );
	remove_action( 'load-update-core.php', 'wp_update_plugins' );
	remove_action( 'load-update-core.php', 'wp_update_themes' );

	register_activation_hook( __FILE__, 'app_updater_activate' );
}

