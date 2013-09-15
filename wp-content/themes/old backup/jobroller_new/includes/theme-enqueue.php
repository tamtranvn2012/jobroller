<?php
/**
 * These are scripts used within the JobRoller theme
 * To increase speed and performance, we only want to
 * load them when needed
 *
 * @package JobRoller
 * @version 1.0
 *
 */

// to speed things up, don't load these scripts in the WP back-end (which is the default)
if( ! is_admin() ) {
    add_action( 'wp_enqueue_scripts', 'jr_load_scripts' );
}

function jr_load_scripts() {
	global $app_abbr;

	$http = (is_ssl()) ? 'https' : 'http';

	// load google cdn hosted scripts if enabled
	if (get_option($app_abbr.'_google_jquery') == 'yes') {

		wp_deregister_script('jquery');
		wp_register_script('jquery', (''.$http.'://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js'), false, '1.8.3');

		wp_register_script('jquery-ui-custom', ''.$http.'://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js', false, '1.8');
		wp_enqueue_script('jquery-ui-custom');

	} else {

		wp_enqueue_script('jquery-ui-core');

	}

	// enqueue the strength meter script
	if ( ( ! is_user_logged_in() && is_page( JR_Job_Submit_Page::get_id() ) ) || is_page_template('tpl-login.php') || is_page_template('tpl-profile.php') ) {
		wp_enqueue_script('password-strength-meter');
		wp_enqueue_script('user-profile');
	}

	// used for the fields placeholders to display default values
    wp_enqueue_script('defaultvalue', get_template_directory_uri().'/includes/js/jquery.defaultvalue.js', array('jquery'), '');
	// adds fancy tags to tag fields
    wp_enqueue_script('jquery-tag', get_template_directory_uri().'/includes/js/jquery.tag.js', array('jquery'), '');
	// adds smooth scroll to top
    wp_enqueue_script('smoothscroll', get_template_directory_uri().'/includes/js/smoothscroll.js', array('jquery'), '');
	// delays loading of images in long web pages
    wp_enqueue_script('lazyload', get_template_directory_uri().'/includes/js/jquery.lazyload.mini.js', array('jquery'), '1.5.0');
	// makes textareas grow and shrink to fit it’s content; inspired by the auto growing textareas on Facebook
    wp_enqueue_script('elastic', get_template_directory_uri().'/includes/js/jquery.elastic.js', array('jquery'), '1.0');
	// displays images, html content and multi-media in a Mac-style "lightbox" that floats overtop of web page
    wp_enqueue_script('fancybox', get_template_directory_uri().'/includes/js/jquery.fancybox-1.3.4.pack.js', array('jquery'), '1.3.4');
	// used to display small balloon tooltips
    wp_enqueue_script('qtip', get_template_directory_uri().'/includes/js/jquery.qtip.min.js', array('jquery'), '1.0');
	// appthemes javascript functions
    wp_enqueue_script('general', get_template_directory_uri().'/includes/js/theme-scripts.js', array('jquery'), '3.0');
    // used to transform tables on mobile devices
	wp_enqueue_script( 'footable', get_template_directory_uri() . '/includes/js/jquery.footable.js', array( 'jquery' ), '0.1' );
	
	// script must be enqueued on header to work properly
	wp_deregister_script( 'jquery-ui-autocomplete' );
	wp_enqueue_script( 
		'jquery-ui-autocomplete',
		site_url( '/wp-includes/js/jquery/ui/jquery.ui.autocomplete.min.js' ),
		array( 'jquery', 'jquery-ui-widget', 'jquery-ui-position' )
	);
	wp_enqueue_script('googlemaps', $http.'://www.google.com/jsapi');

	/* Script variables */
	$params = array(
		'lazyload_placeholder' 			=> get_template_directory_uri().'/images/grey.gif',
		'ajax_url' 						=> admin_url('admin-ajax.php'),
		'get_sponsored_results_nonce' 	=> wp_create_nonce("get-sponsored-results"),
		'nonce'							=> wp_create_nonce('jr-nonce'),

		'si_empty'						=> __( 'Strength indicator',APP_TD ),
		'si_short' 						=> __( 'Very weak',APP_TD ),
		'si_bad' 						=> __( 'Weak',APP_TD ),
		'si_good' 						=> __( 'Medium',APP_TD ),
		'si_strong'						=> __( 'Strong',APP_TD ),
		'si_mismatch'					=> __( 'Mismatch',APP_TD ),

		'msg_invalid_email'				=> __( 'Invalid email address.', APP_TD ),
		'msg_fields_required'			=> __( 'All fields are required.', APP_TD ),
	);

	wp_localize_script( 'general', 'jobroller_params', $params );
}

