<?php

abstract class APP_Upgrader_UI {

	abstract function init_page();
	abstract protected function can_set_key();
	abstract protected function get_admin_url();

	protected function maybe_set_key() {
		if ( !isset( $_POST['appthemes_submit'] ) )
			return;

		APP_Upgrader::set_key( trim( $_POST['appthemes_key'] ) );

		echo "
			<div class='updated fade'><p>"
			. __( 'Saved Changes.', 'app-updater' )
			. "</p></div>";
	}

	function show_notice() {
		self::maybe_set_key();

		if ( APP_Upgrader::get_key() )
			return;

		if ( !$this->can_set_key() )
			return;

		self::render( 'api-key-notice.php', array(
			'admin_url' => $this->get_admin_url()
		) );
	}

	function render_page() {
		self::render( 'admin-page.php' );

		if ( APP_Upgrader::get_key() )
			self::render( 'check-for-updates.php' );
	}

	private static function render( $path, $vars = array() ) {
		extract( $vars );

		include dirname(__FILE__) . "/templates/$path";
	}
}


class APP_Upgrader_Regular extends APP_Upgrader_UI {

	function __construct() {
		add_action( 'admin_notices', array( $this, 'show_notice' ) );

		add_action( 'admin_menu', array( $this, 'init_page' ) );
	}

	protected function can_set_key() {
		return current_user_can( 'manage_options' );
	}

	protected function get_admin_url() {
		return admin_url( 'admin.php?page=appthemes-key-config' );
	}

	function init_page() {
		add_submenu_page(
			'plugins.php',
			__( 'AppThemes Updater Configuration', 'appthemes-updater' ),
			__( 'AppThemes Updater', 'appthemes-updater' ),
			'manage_options',
			'appthemes-key-config',
			array( $this, 'render_page' )
		);
	}
}

class APP_Upgrader_Network extends APP_Upgrader_UI {

	function __construct() {
		add_action( 'all_admin_notices', array( $this, 'show_notice' ) );

		add_action( 'network_admin_menu', array( $this, 'init_page' ) );
	}

	protected function can_set_key() {
		return is_super_admin();
	}

	protected function get_admin_url() {
		return network_admin_url( 'settings.php?page=appthemes-key-config' );
	}

	function init_page() {
		add_submenu_page(
			'settings.php',
			__( 'AppThemes Updater Configuration', 'appthemes-updater' ),
			__( 'AppThemes Updater', 'appthemes-updater' ),
			'manage_options',
			'appthemes-key-config',
			array( $this, 'render_page' )
		);
	}
}

