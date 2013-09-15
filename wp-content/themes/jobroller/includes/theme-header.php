<?php

/**
 * Add header elements via the a hook
 *
 * Anything you add to this file will be dynamically
 * inserted in the header of your theme
 *
 * @since 1.0
 * @uses wp_head or appthemes_header
 *
 */

add_action( 'wp_before_admin_bar_render', 'jr_remove_admin_bar_links' );
add_action( 'admin_bar_menu', 'jr_add_admin_bar_links', 25 );
add_action( 'wp_head', 'jr_version' );

// adds version number in the header for troubleshooting
function jr_version($app_version) {
    global $app_version;

    echo "\n\t" . '<!-- start wp_head -->' . "\n";
    echo "\n\t" .'<meta name="version" content="JobRoller '.$app_version.'" />' . "\n";
    echo "\n\t" . '<!-- end wp_head -->' . "\n\n";
}

// enables the share buttons on job and blog posts
function jr_sharethis_head() {

    //fba9432a-d597-4509-800d-999395ce552a
    $pub_id = get_option('jr_sharethis_id');
    
    $http = (is_ssl()) ? 'https' : 'http';

    echo "\n\t" . '<script type="text/javascript" src="'.$http.'://w.sharethis.com/button/buttons.js"></script>' . "\n";
    echo "\n\t" . '<script type="text/javascript">stLight.options({publisher:"'.$pub_id.'"});</script>' . "\n";

}

// only enable sharethis if pub id is detected
if (get_option('jr_sharethis_id'))
    add_action('wp_head', 'jr_sharethis_head');
	
	
// remove the WordPress version meta tag
if (get_option('jr_remove_wp_generator') == 'yes')
	remove_action('wp_head', 'wp_generator');	
	
	
// remove the new 3.1 admin header toolbar visible on the website if logged in	
if (get_option('jr_remove_admin_bar') == 'yes')	
	add_filter('show_admin_bar', '__return_false');	

function default_primary_nav() {
	global $wp_query;
	echo '<ul>';
	echo '<li class="page_item ';
	if (is_front_page() && !isset($_GET['submit']) && !isset($_GET['myjobs'])) echo 'current_page_item';
	echo '"><a href="'.get_bloginfo('url').'">'.__('Latest Jobs', APP_TD).'</a></li>';
	
	$args = array(
	    'hierarchical'       => false,
	    'parent'               => 0
	);
	$terms = get_terms( 'job_type', $args );
	if ($terms) foreach($terms as $term) :
		echo '<li class="page_item ';
		if ( isset($wp_query->queried_object->slug) && $wp_query->queried_object->slug==$term->slug ) echo 'current_page_item';
		echo '"><a href="'.get_term_link( $term->slug, 'job_type' ).'">'.$term->name.'</a></li>';
	endforeach;
	
	echo '</ul>';
}

function default_top_nav() {
	echo '<ul id="menu-top" class="menu">';
	
	$exclude_pages = array();

	$exclude_pages[] = get_option('page_on_front');
	$exclude_pages[] = JR_Dashboard_Page::get_id();
	$exclude_pages[] = JR_Resume_Plans_Purchase_Page::get_id();
	$exclude_pages[] = JR_Packs_Purchase_Page::get_id();
	$exclude_pages[] = JR_Resume_Edit_Page::get_id();
	$exclude_pages[] = JR_Job_Submit_Page::get_id();
	$exclude_pages[] = JR_User_Profile_Page::get_id();
	$exclude_pages[] = JR_Job_Edit_Page::get_id();
	$exclude_pages[] = JR_Date_Archive_Page::get_id();

	if ( current_theme_supports ('app-login') ) {
		$exclude_pages[] = APP_Registration::get_id();
		$exclude_pages[] = APP_Login::get_id();
		$exclude_pages[] = APP_Password_Recovery::get_id();
		$exclude_pages[] = APP_Password_Reset::get_id();
	}

	if ( 'yes' == get_option('jr_disable_blog') ) $exclude_pages[] = JR_Blog_Page::get_id();
	
	$exclude_pages = implode(',', $exclude_pages);
	echo wp_list_pages('sort_column=menu_order&title_li=&echo=0&link_before=&link_after=&depth=1&exclude='.$exclude_pages);
	echo jr_top_nav_links();
	echo '</ul>';
}

// Add items to top nav
function jr_top_nav_links( $items = '', $menu = null) {

	if( !empty($menu) && $menu->theme_location != 'top')
		return $items;

	if (is_user_logged_in()) {
		$items .= '<li class="right logout"><a href="'.wp_logout_url( home_url() ).'">'.__('Logout', APP_TD).'</a></li>';

		
		
	} else {
		global $pagenow;
		if(isset($_GET['action'])) $theaction = $_GET['action']; else $theaction ='';
		$items .= '<li class="right login ';
		if ($pagenow == 'wp-login.php' && $theaction !=='lostpassword' && !isset($_GET['key'])) $items .= 'current_page_item login';
		$items .= '"><a href="'.site_url('wp-login.php').'">'.__('Login/Register', APP_TD).'</a></li>';
	}
	
	if ( jr_resume_is_visible() || jr_user_resume_visibility() ) :
		$items .= '<li class="right ';
		if (is_post_type_archive('resume')) $items .= 'current_page_item';	
		$items .= '"><a href="'.get_post_type_archive_link('resume').'">'.__('Browse Resumes', APP_TD).'</a></li>';
	endif;
	
	
		if ( JR_Job_Submit_Page::get_id() && (!is_user_logged_in() || (is_user_logged_in() && current_user_can('can_submit_job'))) ) :
	
		$items .= '<li class="right submitjob';
		if ( is_page( JR_Job_Submit_Page::get_id() ) ) $items .= 'current_page_item submitjob';	
		$items .= '"><a href="'.get_permalink( JR_Job_Submit_Page::get_id() ).'">'.__('Submit a Job', APP_TD).'</a></li>';
		
	endif;
	
	
	

	
	
	$items .= '</ul>';
	
	return $items;
	
}

function jr_add_admin_bar_links( $wp_admin_bar ) {

	if ( !is_user_logged_in() )
		return;

	$wp_admin_bar->add_node( array(
		'id'     => 'jr-dashboard',
		'parent' => false,
		'meta' => array( 'class' => 'opposite' ),
		'title'  => __( 'Dashboard', APP_TD ),
		'href'   => get_permalink(JR_Dashboard_Page::get_id())
	) );
}

function jr_remove_admin_bar_links() {
	global $wp_admin_bar;

	if ( !current_user_can( 'manage_options' ) ) {
		$wp_admin_bar->remove_node('my-sites');
		$wp_admin_bar->remove_node('site-name');
	}

	$wp_admin_bar->remove_node('wp-logo');
	$wp_admin_bar->remove_node('new-content');
	$wp_admin_bar->remove_node('search');
}