<?php
/**
 * JobRoller Theme Support
 * This file defines 'theme support' so wordpress knows what new features it can handle.
 *
 *
 * @version 1.0
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */

add_theme_support( 'menus' );
add_theme_support( 'post-thumbnails', array( 'post', 'job_listing', 'resume' ) );

add_theme_support( 'app-wrapping' );

add_theme_support( 'app-versions', array(
	'update_page' 		=> 'admin.php?page=settings&firstrun=1',
	'current_version' 	=> JR_VERSION,
	'option_key' 		=> 'jobroller_version',
) );

add_theme_support( 'app-login', array(
	'login' 	=> 'tpl-login.php',
	'register' 	=> 'tpl-registration.php',
	'recover' 	=> 'tpl-password-recovery.php',
	'reset' 	=> 'tpl-password-reset.php',
) );

add_theme_support( 'app-stats', array(
	'cache' => 'today',
	'table_daily' => 'jr_counter_daily',
	'table_total' => 'jr_counter_total',
	'meta_daily' => 'jr_daily_count',
	'meta_total' => 'jr_total_count',
) );

add_theme_support( 'app-payments', array(
	'items' => array(
		array(
			'type' => JR_ITEM_FEATURED_LISTINGS,
			'title' => __( 'Feature on Homepage and Listings', APP_TD ),
			'meta' => array(
				'price' => $jr_options->addons[ JR_ITEM_FEATURED_LISTINGS]['price']
			)
		),
		array(
			'type' => JR_ITEM_FEATURED_CAT,
			'title' => __( 'Feature on Category', APP_TD ),
			'meta' => array(
				'price' => $jr_options->addons[ JR_ITEM_FEATURED_CAT ]['price']
			)
		),
		// Resumes
		array(
			'type' => JR_ITEM_BROWSE_RESUMES,
			'title' => __( 'Browse Resumes', APP_TD ),
			'meta' => array(
				'price' => $jr_options->addons[ JR_ITEM_BROWSE_RESUMES ]['price']
			)
		),
		array(
			'type' => JR_ITEM_VIEW_RESUMES,
			'title' => __( 'View Resumes', APP_TD ),
			'meta' => array(
				'price' => $jr_options->addons[ JR_ITEM_VIEW_RESUMES ]['price']
			)
		)
	),
	'items_post_types' => array( APP_POST_TYPE ),
	'options' => $jr_options,
) );

add_theme_support( 'app-price-format', array(
	'currency_default' => $jr_options->currency_code,
	'currency_identifier' => $jr_options->currency_identifier,
	'currency_position' => $jr_options->currency_position,
	'thousands_separator' => $jr_options->thousands_separator,
	'decimal_separator' => $jr_options->decimal_separator,
	'hide_decimals' => (bool) ( ! $jr_options->decimal_separator ),
) );

add_theme_support( 'app-form-builder', array(
	'show_in_menu' => 'edit.php?post_type=' . APP_POST_TYPE
) );

set_post_thumbnail_size( 250, 250, false );
add_image_size('blog-thumbnail', 150, 150, true); // blog post thumbnail size, box resize mode
add_image_size('sidebar-thumbnail', 48, 48, true); // sidebar blog thumbnail size, box resize mode
add_image_size('listing-thumbnail', 28, 28, true);

// Actions

add_action( 'init', 'jr_recaptcha_support' );

add_filter( 'wp_nav_menu_items', 'jr_top_nav_links', 2, 10 );


// Views
new JR_Contact_Page;
new JR_Blog_Page;
new JR_Dashboard_Page;
new JR_User_Profile_Page;
new JR_Date_Archive_Page;
new JR_Terms_Conditions_Page;
new JR_Resume_Edit_Page;
new JR_Job_Edit_Page;
new JR_Job_Submit_Page;
new JR_Packs_Purchase_Page;
new JR_Resume_Plans_Purchase_Page;

new JR_Date_Archive;
new JR_Job_Single;
new JR_Contact;
new JR_Job_Edit;
new JR_Job_Relist;
new JR_Job_Submit;
new JR_Packs_Purchase;
new JR_Resumes_Plans_Purchase;
new JR_Order_Go_Back;
new JR_Search;
new JR_Lister_Dashboard;

function jr_recaptcha_support() {
	global $app_abbr;

	if ( ! get_option($app_abbr.'_captcha_public_key') || ! get_option($app_abbr.'_captcha_private_key') )
		return;

	if ( 'yes' == get_option($app_abbr.'_captcha_enable') ) {
		$support_name = 'app-recaptcha';
		$support[] = $support_name;
		$display_rule[$support_name] = get_option($app_abbr.'_captcha_enable');
	}

	if ( 'no' != get_option($app_abbr.'_captcha_contact_forms_enable') ) {
		$support_name = 'app-recaptcha-contact';
		$support[] = $support_name;
		$display_rule[$support_name] = get_option($app_abbr.'_captcha_contact_forms_enable');
	}

	if ( 'no' != get_option($app_abbr.'_captcha_application_form_enable') ) {
		$support_name = 'app-recaptcha-application';
		$support[] = $support_name;
		$display_rule[$support_name] = get_option($app_abbr.'_captcha_application_form_enable');
	}

	if ( !empty($support) ) {
		foreach ( $support as $name ) {
			add_theme_support( $name, array(
				'file' => get_template_directory() . '/includes/lib/recaptchalib.php',
				'theme' => get_option($app_abbr.'_captcha_theme'),
				'public_key' => get_option($app_abbr.'_captcha_public_key'),
				'private_key' => get_option($app_abbr.'_captcha_private_key'),
				'display_rule' => $display_rule[$name]
			) );
		}
	}
}

