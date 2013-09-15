<?php
/*
Plugin Name: 	FXtender Pro (for JobRoller)
Plugin URI: 	http://www.bruno-carreco.com/wpuno/fxtender_jr
Description:  	Unofficial plugin for the premium JobRoller theme (by AppThemes) that adds new features, options and widgets. Developed for the power users who want more features for their favourite job portal theme.
Version: 		1.4.1.2
Author: 		Bruno Carre&ccedil;o
Author URI: 	http://www.bruno-carreco.com
AppThemes ID: 	fxtender-pro
License: 		GPL v2
*/

register_activation_hook( __FILE__, 'jr_fx_install' );

define( 'JR_FX_PLUGIN_VERSION', '1.4.1' );
define( 'JR_FX_JR_CURR_VERSION', '1.7' );

define( 'JR_FX_JR_MIN_VERSION', '1.7' );

define( 'JR_FX_JOB_APPLICATION', 'jr_job_application' );
define( 'JR_FX_JOB_APPLICATION_META', '_'.JR_FX_JOB_APPLICATION );
define( 'JR_FX_JOB_APPLICATION_LKND_META', '_'.JR_FX_JOB_APPLICATION.'_lkdn' );

global $jr_fx_options;

# init google maps var so we can use it inside the query() hook ( get_option will loop )
global $jr_fx_disable_gmaps;

### allowed CV file extensions 
global $jr_fx_cv_allowed_ext_list;
$jr_fx_cv_allowed_ext_list = array( 'pdf', 'doc','docx','zip' ,'txt', 'rtf');

# LOGGING
global $jr_fx_log;

define('JR_FX_LOG','');	// <-- turn debug log to ON by replacing '' with 'yes'

require_once( ABSPATH . '/wp-includes/pluggable.php' );

### error reporting  >>>> 
# uncomment these lines if you need low level debugging. Be carefull uncommenting this as it may show sensitive data to your site visitors. Use only on Dev sites.
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
### error reporting <<<< 

# caching - increase caching values to speed up your site
define('JR_FX_JOB_LISTINGS_PREVIEW_CACHE', 60 * 60 * 120); // cache job preview for a week
define('JR_FX_JOB_LISTINGS_THUMBS_CACHE', 60 * 60 * 120); // cache thumbs displayed on job listings for a week

# constant declaration
define('JR_FX_PLUGIN_BASE_NAME','fxtender_jr');
define('JR_FX_PLUGIN_NAME',plugin_basename(__FILE__));
define('JR_FX_PLUGIN_TITLE','FXtender for JobRoller');
define('JR_FX_PRICE','$39,99');

define('JR_FX_VER_FREE','Lite');
define('JR_FX_VER_PLUS','Pro');

define('JR_FX_FIELDS_PREFIX','jr_fx');
define('JR_FX_PLUGIN_DIR',plugin_dir_path( __FILE__ ));
define("JR_FX_PLUGIN_URL",plugin_dir_url(__FILE__)); 
define('JR_FX_i18N_DOMAIN','jr_fx');

# post types used by JobRoller 
define('JR_FX_JR_POST_TYPE', 'job_listing');
define('JR_FX_JR_JOB_TYPE_TAX', 'job_type'); 
define('JR_FX_JR_JOB_CAT_TAX', 'job_cat'); 
define('JR_FX_JR_RESUME', 'resume'); // since JR 1.4	

# JR featured job category  
define('JR_FX_JR_JOB_CAT_FEATURED_ID', get_option('jr_featured_category_id') ); 

# info Constants
define('JR_FX_JOBROLLER','JobRoller');
define('JR_FX_JOBROLLER_URL','http://www.appthemes.com/themes/jobroller/');
define('JR_FX_CLASSIPRESS','Classipress');
define('JR_FX_CLASSIPRESS_URL','http://www.appthemes.com/themes/classipress/');
define('JR_FX_IDEAS_URL','http://ideas.appthemes.com'); 

### upload cvs dir - warning - this path is always appended to /wp-content/
define('JR_FX_DB_TABLE_UPLOADED_CVS', 'jr_fx_uploaded_cvs' ); 
define('JR_FX_DIR_UPLOADED_CVS','uploaded_cvs');

# S2Member
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
define('JR_FX_S2MEMBER_EXIST', is_plugin_active( 's2member/s2member.php') );

# gateway values

# define default google API Callback URL used for IPN Responses
define('JR_FX_GW_RESPONSE_URL', site_url());

# define gateways info url
define('JR_FX_GATEWAY_INFO_GOOGLE_URL','https://checkout.google.com/sell/');
define('JR_FX_GATEWAY_INFO_2CO_URL','https://www.2checkout.com/va/');
define('JR_FX_GATEWAY_INFO_AUTHORIZE_URL','https://account.authorize.net/');

# nonce
define('JR_FX_NONCE', wp_create_nonce(JR_FX_PLUGIN_BASE_NAME) );

define('JR_FX_COMPANY_LOGO_SIZE_W', 150 );
define('JR_FX_COMPANY_LOGO_SIZE_H', 150 );
define('JR_FX_COMPANY_LOGO_SUFIX', 'company_logo' );

add_action( 'admin_init', 'jr_fx_theme_installed', 1 );
add_action( 'init','jr_fx_includes', 1 );

add_action( 'after_setup_theme', 'jr_fx_init_fxtender', 5 );
add_action( 'admin_notices', 'jr_fx_admin_notices' );
add_action( 'admin_notices', 'jr_fx_check_file_perms' );
add_action( 'admin_footer', 'jr_fx_ini_admin_functions' );

add_action( 'admin_enqueue_scripts', 'jr_fx_load_admin_scripts' );
add_action( 'wp_enqueue_scripts', 'jr_fx_load_frontend_scripts' );

add_action( 'wp_footer', 'jr_fx_ini_functions' );

require_once( dirname(__FILE__) . '/includes/log.php' );
$jr_fx_log = new jr_fx_log();

### include the main files only after the JobRoller theme has finished initializing
function jr_fx_init_fxtender() {
	global $jr_fx_disable_gmaps;

	if ( !jr_fx_theme_installed() ) 
		return;

	# include all the core frontend files
	if ( file_exists( JR_FX_PLUGIN_DIR . '/includes/pro/pro.php' ) ) { include_once ('includes/pro/pro.php'); }
	include_once ('core.php');

	$jr_fx_disable_gmaps = jr_fx_validate('_opt_jobs_gmaps','yes');

	# include other important files
	include_once ('includes/hooks.php');
	include_once ('includes/widgetz.php');
	include_once ('includes/geoscripts.php');

	if ( !is_admin() ):
		include_once ('includes/jscripts.php');
	endif;

	# include classes
	if ( JR_FX_VERSION != JR_FX_VER_FREE ) :
		include_once ('includes/pro/gateways/PaymentGateway.class.php');
	endif;
	
}

# include some dependent files as needed
function jr_fx_includes() {

	if ( !jr_fx_theme_installed() ) 
		return;

	# initialize locale translations
	jr_fx_init_language();

	if ( is_admin() ) {
		include_once ('includes/admin/admin-main-values.php');
		include_once ('includes/admin/admin-options.php');

		$_current_page = isset($_GET['page']) ? $_GET['page'] : '';

		switch ($_current_page):
			case 'jr-fx-admin-resumes':
				include_once ('includes/admin/admin-resume-values.php');
				break;
			case 'jr-fx-admin-pricing':
				include_once ('includes/admin/admin-pricing-values.php');
				break;
			case 'jr-fx-admin-membership':
				include_once ('includes/admin/admin-membership-values.php');
				break;
			case 'jr-fx-admin-notifications':
				include_once ('includes/admin/admin-notifications-values.php');
				break;
			case 'jr-fx-admin-integration':
				include_once ('includes/admin/admin-integration-values.php');
				break;
			case 'jr-fx-admin-widgets':
				include_once ('includes/admin/admin-widget-values.php');
				break;
			case 'jr-fx-admin-more':
				include_once ('includes/admin/admin-more-values.php');
				break;
			default:
		endswitch;

	}

}

/**
 * Localization
 */

function jr_fx_init_language(){
	$currentLocale = get_locale();
				
	if(!empty($currentLocale)) {
		$langPath = '/lang/';
		$moFile = dirname( __FILE__ ) . $langPath . JR_FX_i18N_DOMAIN . '-' . $currentLocale . '.mo';
				
		if(@file_exists($moFile) && is_readable($moFile)):
			// USES load_plugin_textdomain() - translated files should be named ../lang/jr_fx-en_US.mo (for english)
			$moPath = dirname( plugin_basename( __FILE__ ) ) . $langPath ;
			load_plugin_textdomain( JR_FX_i18N_DOMAIN,  false, $moPath );
		else:
			// USES load_theme_textdomain() - translated files should be named .../lang/en_US.mo (for english)
			$moFile = dirname( __FILE__ ) . $langPath . $currentLocale . '.mo';
			$moPath = dirname( __FILE__ ) . $langPath ;
			if(@file_exists($moFile) && is_readable($moFile)) 
				load_theme_textdomain( JR_FX_i18N_DOMAIN,  $moPath );
		endif;	
	}
}

/**
 * Install and register plugin options
 */
function jr_fx_install() {
	global $jr_fx_options_settings, $jr_fx_resume_settings, $jr_fx_notification_settings, $jr_fx_integration_settings, $jr_fx_widget_settings, $jr_fx_more_settings, $jr_fx_membership_settings;

	if ( !jr_fx_theme_installed() ) return;
	
	# include dependent files
	include_once ('includes/admin/admin-options.php');
	
	# update the plugin with the default options
	jr_fx_update_options($jr_fx_options_settings);
	jr_fx_update_options($jr_fx_resume_settings);
	jr_fx_update_options($jr_fx_notification_settings);
	jr_fx_update_options($jr_fx_integration_settings);
	jr_fx_update_options($jr_fx_widget_settings);
	jr_fx_update_options($jr_fx_more_settings);
	jr_fx_update_options($jr_fx_membership_settings);

	// update legacy options
	jr_fx_update_options_legacy();

	# install dependant table
	jr_fx_db_install();
	
	# clear old cron update check
	wp_clear_scheduled_hook('jr_fx_update_check');
	
	delete_site_transient( JR_FX_PLUGIN_BASE_NAME . '_update_theme' );	
	
}

/**
 * Uninstall and unregister plugin options
 */
register_deactivation_hook( __FILE__, 'jr_fx_uninstall' );
function jr_fx_uninstall() {

	# clear cron jobs
	wp_clear_scheduled_hook('jr_fx_update_check');
	wp_clear_scheduled_hook('check_plugin_updates-fxtender-pro-jobroller');

}

/**
 * Check if theme is installed
 */
function jr_fx_theme_installed () {

	$min_requirements = defined('JR_VERSION') && version_compare( JR_VERSION, JR_FX_JR_MIN_VERSION ) >= 0;

	# check if JobRoller theme is installed by checking the global $app_theme
	if ( !defined('JR_VERSION') || ! $min_requirements ) {

		if ( !defined('JR_VERSION') )
			set_transient('jr-fx-admin-notice', 'jr-fx-no-theme', '10');
		elseif( ! $min_requirements )
			set_transient('jr-fx-admin-notice', 'jr-fx-no-min_req', '10');

		deactivate_plugins( __FILE__ );
		return false;
	}
	return true;
}

/**
 * Install any necessary tables
 */
function jr_fx_db_install() {
	global $wpdb;
	
	$db_version = '1.0';
	
	# table for the uploaded CV's
	$table_name =  $wpdb->prefix . JR_FX_DB_TABLE_UPLOADED_CVS;
	
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	
		$updatedb = "CREATE TABLE $table_name (
					id int(12) NOT NULL auto_increment,
					user_id MEDIUMINT NOT NULL, 
					filename VARCHAR(155) NOT NULL,
					url VARCHAR(155) NOT NULL, 
					date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`)
				)";
			
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($updatedb);	
			
		add_option( JR_FX_FIELDS_PREFIX . "_db_version", $db_version);
		
	}
}

/**
 * Init User Roles for S2Member integration
 */
 if ( JR_FX_S2MEMBER_EXIST ) add_action('ws_plugin__s2member_after_loaded', 'jr_fx_init_roles');
 
function jr_fx_init_roles() {
	global $wp_roles;

	if (class_exists('WP_Roles')) 	
		if ( ! isset( $wp_roles ) )
			$wp_roles = new WP_Roles();	
	
	if (is_object($wp_roles)) :
		# add 'can_submit_resume' capability to all member levels to ensure compatibility between S2Member and JR
		# FXtender will check if the user has the right member level before accessing this capability
		for ($n = 1; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++) {		
			$wp_roles->add_cap( 's2member_level' . $n, 'can_submit_resume' );
		}				
		$wp_roles->add_cap( 'job_seeker' , 'access_s2member_level0' );
		$wp_roles->add_cap( 'job_seeker' , 'level_0' );
		
	endif;

	# filter S2Member default and demoted roles
	add_filter('ws_plugin__s2member_force_default_role','jr_fx_s2member_default_role');
	add_filter('ws_plugin__s2member_force_demotion_role','jr_fx_s2member_default_role');
	
}

/**
 * Filter S2Member default and demote roles to 'job_seeker' - default is 'subscriber'
 */
function jr_fx_s2member_default_role( $role = FALSE){

	$role = 'job_seeker';
	
	return $role;
}

/**
 * Confirm/update the user Role after returning from S2Member payment URL
 */
if ( JR_FX_S2MEMBER_EXIST ) {
	add_action("ws_plugin__s2member_after_paypal_return","jr_fx_update_role");
	add_action("ws_plugin__s2member_during_paypal_return","jr_fx_update_role");
}	

function jr_fx_update_role( $def_vars ) {
	global $current_user, $app_abbr;
	get_currentuserinfo();

	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);

	$user_id = $def_vars['user_id'];
	$user = new WP_User ($user_id);

	if ( $user_role ) $user->set_role( $user_role );

	$dashboard = jr_fx_get_page_id('dashboard_page_id');
	
	echo "<a href='". get_permalink($dashboard) ."'>".__('If you\'re not automatically redirected please click here.',JR_FX_i18N_DOMAIN)."</a>";

}

/**
 * Custom admin notices
 */
function jr_fx_admin_notices() {

	$trans_admin_notice = get_transient('jr-fx-admin-notice');

	# check if JobRoller is installed
	if( 'jr-fx-no-theme' == $trans_admin_notice ) {
		echo sprintf( '<div id="my-custom-warning" class="error fade"><p>It seems you don\'t have the <strong>JobRoller Theme</strong> active/installed. <strong>FXtender</strong> cannot be installed alone. <p>Please install/activate <strong>JobRoller v.%s or later</strong> to use FXtender.</p></div>', JR_FX_JR_MIN_VERSION ); 
		deactivate_plugins( JR_FX_PLUGIN_NAME );
	} elseif ( 'jr-fx-no-min_req' == $trans_admin_notice ) {
		echo sprintf( '<div id="my-custom-warning" class="error fade"><p>FXtender can only be used with <strong>JobRoller v.%s</strong>, or later (v.%s installed). <p>Please upgrade or install a compatible FXtender Pro version.</p></div>', JR_FX_JR_MIN_VERSION, JR_VERSION ); 
	}
	
	delete_transient( 'jr-fx-admin-notice' );
	
}

/**
 * Add css/javascript on admin side
 */
 function jr_fx_load_admin_scripts( $hook ) {
	global $post;

	# register stylesheets
	wp_register_style('jr-fx-admin-style', JR_FX_PLUGIN_URL.'admin-styles.css', false, '3.0');
	wp_enqueue_style('jr-fx-admin-style');

	if ( isset($_GET['page']) && (stripos($_GET['page'], 'jr-fx') === 0) ) {

		wp_register_style('jr-tzCheckbox-style', JR_FX_PLUGIN_URL. 'js/jquery.tzCheckbox/jquery.tzCheckbox.css', false, '3.0');
		wp_enqueue_style('jr-tzCheckbox-style');

		# register scripts
		wp_register_script('jr-tzCheckbox-js', JR_FX_PLUGIN_URL. 'js/jquery.tzCheckbox/jquery.tzCheckbox.js' , array( 'jquery' ) );
		wp_enqueue_script('jr-tzCheckbox-js');

		# after JR 1.4 some scripts are loaded contextually - this will load some needed scripts
		wp_enqueue_script('admin-scripts', get_bloginfo('template_directory').'/includes/admin/admin-scripts.js', array('jquery','media-upload','thickbox'), '1.2');

	}

	if( 'post.php' == $hook && APP_POST_TYPE == $post->post_type ) {
		wp_enqueue_script( 'jr-fx-admin-scripts', JR_FX_PLUGIN_URL . 'js/admin.js', array('jquery'), '1.2' );
	}

}

/**
 * Add css/javascript on client side
 */
 function jr_fx_load_frontend_scripts() {
	global $post, $app_abbr;
	
	# register stylesheets
	wp_register_style('jr-fx-frontend-style', JR_FX_PLUGIN_URL.'styles.css', false, '3.0');
	wp_enqueue_style('jr-fx-frontend-style');

	# register ajax scripts
	wp_enqueue_script( 'jr-fx-ajax-request', JR_FX_PLUGIN_URL. 'js/ajax.js' , array( 'jquery' ) );

	# declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
	wp_localize_script( 'jr-fx-ajax-request', 'jr_fx_ajax', 
		array( 
				'ajaxurl' 	   => admin_url( 'admin-ajax.php' ),
				'nonce'		   => JR_FX_NONCE,
				'qTipThrobber' => JR_FX_PLUGIN_URL . 'js/jquery.qtip/images/loading.gif',
				'qTipColor'    => jr_fx_validate( '_opt_listings_preview_color' ),
				'qTipStyle'    => jr_fx_validate( '_opt_listings_preview_style' )
		)
	);

	if ( !is_single() && !is_admin() && isset($post) && $post->post_type <> JR_FX_JR_RESUME ) :

		# deregister older jobroller qtip version
		wp_deregister_script('qtip');
		
		# register new bundled qtip version
		wp_register_script('qtip', JR_FX_PLUGIN_URL. 'js/jquery.qtip/jquery.qtip.min.js', array( 'jquery' ) );

		# register stylesheets - qtip
		wp_register_style('jr-fx-qtip-style', JR_FX_PLUGIN_URL.'js/jquery.qtip/jquery.qtip.min.css', false, '3.0');
		wp_enqueue_style('jr-fx-qtip-style');
		
	endif;

	if ( is_page( jr_fx_get_page_id( 'submit_page_id' ) ) || is_page( jr_fx_get_page_id( 'edit_job_page_id' ) ) ):
		# enqueue jquery form for uploading company logos
		wp_enqueue_script('jquery-form');
	endif;
}

/**
 * Add client/server side jQuery functions
 */
function jr_fx_ini_functions() {

	# reset post data
	wp_reset_postdata();

	# adds jquery functions
	jr_fx_add_jquery();
}

/**
 * Add client/server side jQuery functions (admin) to the footer for better performance
 */
function jr_fx_ini_admin_functions() {
?>
	<script type="text/javascript">
	/* <![CDATA[ */
		var $j = jQuery.noConflict();

		//JobRoller Extended Features Scripts
		$j(function() {
			$j(".jr_fx_toggle_features").click( function () { 
				$j(".jr_fx_locked").toggle();
			});
		});
	/* ]]> */
	</script>
<?php	
}

/**
 * Show upgrade message to user
 */
function jr_fx_check_update_notice() {

	$plugin_name = JR_FX_PLUGIN_BASE_NAME;
	$plugins_page = 'plugins.php';
	$plugin_version = JR_FX_PLUGIN_VERSION;

	$me = jr_fx_get_me();
	
	if ( !current_user_can('update_core') || JR_FX_VERSION != JR_FX_VER_FREE )
		return false;
		
	$cur = get_site_transient( $plugin_name.'_update' );

	if ( empty($cur) )
		return false;
	
	if ( !isset($cur->version_checked) || !isset($cur->latest_version) )
		return false;
	
	if ( version_compare($cur->version_checked, $cur->latest_version, '>=' ) )	// 1.0 to 2.0
		return false;

	if ( current_user_can('update_core') ) {
		$msg = sprintf( __(' - <a href="%3$s" title="Update FXtender %2$s now!">v.%2$s is available. Please update to the new version directly from the plugins page.'), $plugin_name, $cur->latest_version, $plugins_page );
	} else {
		$msg = sprintf( __(' - v.%2$s is now available! Please notify the site administrator.'), $plugin_name, $cur->latest_version );
	}
	
	return $msg;
}


// Check file permissions
function jr_fx_check_file_perms( $echo = TRUE ) {
	$errors = array();
	$output_errors = '';
	
	$files = array(
		'js/jquery.qtip/jquery.qtip.colors.txt',
		'js/jquery.qtip/jquery.qtip.styles.txt'
	);
	
	foreach ($files as $file) {
		if (!is_readable(JR_FX_PLUGIN_DIR.'/'.$file)) $errors[] = JR_FX_PLUGIN_DIR.'/'.$file.__(' is not readable or does not exist - check file permissions.',JR_FX_i18N_DOMAIN);
	}
		
	if (isset($errors) && sizeof($errors)>0) {
		
			if ( $echo == TRUE ) echo '<div class="error" style="padding:10px"><strong>'.__('FXtender file permissions error:',JR_FX_i18N_DOMAIN).'</strong>';
			foreach ($errors as $error) {
				if ( $echo == TRUE ) 
					echo '<p>'.$error.'</p>';
				else
					$output_errors .= $error.'<br/><br/>';
			}
			if ( $echo == TRUE ) echo '</div>';
		
	}
	if ( !$echo ) return $output_errors;	
}

// Check for robots.txt
function jr_fx_check_robots() {
	$errors = array();
	$output_errors = '';

	$files = array(
		ABSPATH.'robots.txt'
	);

	foreach ($files as $file) {
		if (!file_exists($file) || !is_readable($file)) $errors[] = __('Robots.txt file not found.',JR_FX_i18N_DOMAIN);
	}

	if (isset($errors) && sizeof($errors)>0) {

			foreach ($errors as $error) {
				$output_errors .= $error.'<br/><br/>';
			}

	}
	return $output_errors;
}

/**
 * check for older JR versions to maintain features between versions
 */
function jr_fx_is_legacy_jr() {
	global $app_version;

	if ( version_compare($app_version, JR_FX_JR_CURR_VERSION, '<') ) {
		return true;
	}
	return false;
	
}

 /**
 * About me
 */
 function jr_fx_get_me() {
	$author_email = "bruno.carreco@gmail.com";
	$me['avatar'] = get_avatar( $author_email, 50 );
	$me['website'] = "http://www.bruno-carreco.com";
	$me['elocriativo'] = "http://www.elocriativo.com";
	$me['contact'] = "http://www.bruno-carreco.com/contact";
	$me['updates'] = "http://bruno-carreco.com/wpuno/fxtender_jr/blog";
	$me['demosite'] = "http://bruno-carreco.com/wpuno/fxtender_jr";
	$me['member'] = "http://bruno-carreco.com/wpuno/fxtender_jr/member";
	$me['democontact'] = "http://bruno-carreco.com/wpuno/fxtender_jr/contact";
	$me['plugins']['custom_posts']['name'] = "Users Custom Posts Count";
	$me['plugins']['custom_posts']['url'] = "http://wordpress.org/extend/plugins/users-custom-posts-counts/";
	$me['plugins']['custom_posts']['desc'] = 'Very simple plugin that adds a new column showing custom type posts counts on the users list. You can configure with any custom type. You can use it with <a href="'.JR_FX_JOBROLLER_URL.'" title="'.JR_FX_JOBROLLER.'">'.JR_FX_JOBROLLER.'</a> or <a href="'.JR_FX_CLASSIPRESS_URL.'" title="'.JR_FX_CLASSIPRESS.'">'.JR_FX_CLASSIPRESS.'</a> where it will show the Total Jobs, or Total Ads depending on the theme.';
	return $me;
}
