<?php

class APP_User_Profile extends APP_View_Page {

	private $error;

	function __construct() {
		parent::__construct( 'edit-profile.php', __( 'Edit Profile', APP_TD ) );
		add_action( 'init', array( $this, 'update' ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

	function update() {
		if ( !isset( $_POST['action'] ) || 'app-edit-profile' != $_POST['action'] )
			return;

		check_admin_referer( 'app-edit-profile' );

		require ABSPATH . '/wp-admin/includes/user.php';

		$r = edit_user( $_POST['user_id'] );

		if ( is_wp_error( $r ) ) {
			$this->error = $r->get_error_message();
		} else {
			$redirect_url = add_query_arg( array( 'updated' => 'true' ) );
			wp_redirect( $redirect_url );
			exit();
		}
	}

	function template_redirect() {
		// Prevent non-logged-in users from accessing the edit-profile.php page
		appthemes_auth_redirect_login();

		add_action( 'appthemes_notices', array( $this, 'show_notice' ) );
	}

	function show_notice() {
		if ( !empty( $this->error ) ) {
			appthemes_display_notice( 'error', $this->error );
		} elseif ( isset( $_GET['updated'] ) ) {
			appthemes_display_notice( 'success', __( 'Your profile has been updated.', APP_TD ) );
		}
	}
}

function appthemes_get_edit_profile_url() {
	if ( $page_id = APP_User_Profile::get_id() )
		return get_permalink( $page_id );

	return get_edit_profile_url( get_current_user_id() );
}

