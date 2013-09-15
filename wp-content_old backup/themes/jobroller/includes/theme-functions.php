<?php
/**
 * JobRoller core theme functions
 * This file is the backbone and includes all the core functions
 * Modifying this will void your warranty and could cause
 * problems with your instance of JR. Proceed at your own risk!
 *
 *
 * @package JobRoller
 * @author AppThemes
 * @url http://www.appthemes.com
 *
 */

define('THE_POSITION', 3);

// setup the custom post types and taxonomies as constants
// do not modify this after installing or it will break your theme!
// started using in places in 1.4. slowly migrate over with the next version
define('APP_POST_TYPE', 'job_listing');
define('APP_POST_TYPE_RESUME', 'resume');
define('APP_TAX_CAT', 'job_cat');
define('APP_TAX_TAG', 'job_tag');
define('APP_TAX_TYPE', 'job_type');
define('APP_TAX_SALARY', 'job_salary');
define('APP_TAX_RESUME_SPECIALITIES', 'resume_specialities');
define('APP_TAX_RESUME_GROUPS', 'resume_groups');
define('APP_TAX_RESUME_LANGUAGES', 'resume_languages');
define('APP_TAX_RESUME_CATEGORY', 'resume_category');
define('APP_TAX_RESUME_JOB_TYPE', 'resume_job_type');

// meta keys
define( 'JR_JOB_DURATION_META',  JR_FIELD_PREFIX . 'job_duration' );

define( 'JR_ITEM_FEATURED_LISTINGS', JR_FIELD_PREFIX . 'featured-listings' );
define( 'JR_ITEM_FEATURED_CAT', JR_FIELD_PREFIX . 'featured-cat' );

define( 'JR_ITEM_BROWSE_RESUMES', JR_FIELD_PREFIX . 'browse_resumes' );
define( 'JR_ITEM_VIEW_RESUMES', JR_FIELD_PREFIX . 'view_resumes' );

// Actions

add_action( 'init', 'buffer_the_output' );
add_action( 'init', 'jr_check_rewrite_rules_transient', 9999 );
add_action( 'admin_notices', 'jr_first_run', 9999 );

add_action( 'appthemes_notices', 'jr_notices' );
add_action( 'pre_get_posts', 'custom_post_author_archive' );
add_action( 'admin_notices', 'check_jr_environment' );

add_action( 'wp_ajax_jr_ajax_validate_recaptcha', 'jr_ajax_validate_recaptcha' );
add_action( 'wp_ajax_nopriv_jr_ajax_validate_recaptcha', 'jr_ajax_validate_recaptcha' );


// Filters

add_filter( 'get_pagenum_link', 'location_query_arg' );
add_filter( 'request', 'jr_rss_request' );
add_filter( 'pre_get_posts', 'jr_rss_pre_get_posts' );
add_filter( 'the_excerpt', 'custom_excerpt' );
add_filter( 'get_search_query', 'jr_search_query' );

if ( !is_admin() ) {
	// search on custom fields
	add_filter( 'posts_join', 'custom_search_join' );
	add_filter( 'posts_where', 'custom_search_where' );
	add_filter( 'posts_groupby', 'custom_search_groupby' );
}

// Tables
$jr_db_tables = array( 'jr_alerts' );

foreach ( $jr_db_tables as $jr_db_table )
	scb_register_table($jr_db_table);

// Legacy Tables
$jr_legacy_db_tables = array( 'jr_orders', 'jr_job_packs', 'jr_customer_packs' );

foreach ( $jr_legacy_db_tables as $jr_db_table )
	scb_register_table($jr_db_table);
	
// execute theme actions on theme activation
function jr_first_run() {
	if ( isset( $_GET['firstrun'] ) ) do_action( 'appthemes_first_run' );
}

// Include functions

// Logging
require( get_template_directory() .'/includes/theme-log.php' );
$jr_log = new jrLog();

// Framework functions
require( get_template_directory() . '/includes/theme-hooks.php' );
require( get_template_directory() . '/includes/appthemes-functions.php' );

// include the new custom post type and taxonomy declarations.
// must be included on all pages to work with site functions
require( get_template_directory() . '/includes/admin/admin-post-types.php' );

// Theme views
require( get_template_directory() . '/includes/views.php' );

// Theme functions
require( get_template_directory() . '/includes/theme-options.php' );
require( get_template_directory() . '/includes/theme-security.php' );
require( get_template_directory() . '/includes/theme-sidebars.php' );
require( get_template_directory() . '/includes/theme-comments.php' );
require( get_template_directory() . '/includes/theme-header.php' );
require( get_template_directory() . '/includes/theme-footer.php' );
require( get_template_directory() . '/includes/theme-widgets.php' );
require( get_template_directory() . '/includes/theme-emails.php' );
require( get_template_directory() . '/includes/theme-geolocation.php' );
require( get_template_directory() . '/includes/theme-actions.php' );
require( get_template_directory() . '/includes/theme-alerts.php' );
require( get_template_directory() . '/includes/theme-enqueue.php' );
require( get_template_directory() . '/includes/theme-stats.php' );
require( get_template_directory() . '/includes/theme-users.php' );
require( get_template_directory() . '/includes/theme-resumes.php');
require( get_template_directory() . '/includes/theme-packs.php' );
require( get_template_directory() . '/includes/theme-featured.php' );
require( get_template_directory() . '/includes/theme-support.php' );

// custom-forms
require dirname( __FILE__ ) . '/custom-forms.php';

// file manager
require( get_template_directory() . '/includes/uploads.php');

// theme payments
require( get_template_directory() . '/includes/theme-payments.php' );

// plans purchase and job submit handling functions
require( get_template_directory() . '/includes/plan-purchase.php' );
require( get_template_directory() . '/includes/plan-activate.php' );
require( get_template_directory() . '/includes/job-status.php' );
require( get_template_directory() . '/includes/job-form.php' );

// loop functions
require( get_template_directory() . '/includes/template-tags.php' );

// 3d party integration
require( get_template_directory() . '/includes/indeed/theme-indeed.php');

require( get_template_directory() . '/includes/theme-cron.php' );

// Front-end includes
if ( !is_admin() ) {

	get_template_part( 'includes/forms/register/register-form' );
	require( get_template_directory() . '/includes/forms/register/register-process.php' );

	require( get_template_directory() . '/includes/countries.php' );

	get_template_part( 'includes/forms/application/application-form' );
	require( get_template_directory() . '/includes/forms/application/application-process.php');

	get_template_part( 'includes/forms/filter/filter-form' );
	require( get_template_directory() . '/includes/forms/filter/filter-process.php' );

	get_template_part( 'includes/forms/share/share-form' );
	get_template_part( 'includes/forms/login/login-form' );

	get_template_part( 'includes/forms/submit-resume/submit-resume-form' );
	require( get_template_directory() . '/includes/forms/submit-resume/submit-resume-process.php' );

	get_template_part( 'includes/forms/resume/edit_parts' );
	get_template_part( 'includes/forms/resume/contact_parts' );

	get_template_part( 'includes/forms/seeker-prefs/seeker-prefs-form' );
	require( get_template_directory() . '/includes/forms/seeker-prefs/seeker-prefs-process.php' );

	get_template_part( 'includes/forms/seeker-alerts/seeker-alerts-form' );
	require( get_template_directory() . '/includes/forms/seeker-alerts/seeker-alerts-process.php' );

} else {

	// Admin Only Functions
	require( get_template_directory() . '/includes/admin/admin-dashboard.php' );
	require( get_template_directory() . '/includes/admin/admin-enqueue.php' );
	require( get_template_directory() . '/includes/admin/admin-options.php' );
	require( get_template_directory() . '/framework/admin/class-meta-box.php' );
	require( get_template_directory() . '/includes/admin/write-panel.php' );
	require( get_template_directory() . '/includes/admin/admin-payments-settings.php' );
	require( get_template_directory() . '/includes/admin/admin-payments-pricing.php' );
	require( get_template_directory() . '/includes/admin/install-script.php' );
	require( get_template_directory() . '/includes/admin/theme-upgrade.php' );
};

// use a transient to flush the rewrite rules
function jr_check_rewrite_rules_transient() {

	if ( get_transient('jr_flush_rewrite_rules') ) {
		delete_transient('jr_flush_rewrite_rules');
		// files required for hard reset of rewrite rules
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/misc.php');
		flush_rewrite_rules();
	}

}

// return the translated role display name
function jr_translate_role( $role ) {
	global $wp_roles;

	$roles = $wp_roles->get_names();

	$translated_roles = array(
		'job_lister' => __('Job Lister',APP_TD),
		'job_seeker' => __('Job Seeker',APP_TD),
		'recruiter'  => __('Recruiter',APP_TD),
	);

	if ( !array_key_exists($role, $translated_roles) ) return $roles[ $role ];

	return $translated_roles[ $role ];

}

################################################################################
// Fix paging on author page
################################################################################

function custom_post_author_archive( &$query ) {
	if ( $query->is_author )
		$query->set( 'post_type', array('post', 'resume', 'job_listing') );
	remove_action( 'pre_get_posts', 'custom_post_author_archive' );
}

################################################################################
// Fix location encoding in urls
################################################################################

function location_query_arg( $link ) {

	if (isset($_GET['location']) && $_GET['location']) :

		$location = wp_strip_all_tags($_GET['location']);
		$link = add_query_arg('location', urlencode( utf8_uri_encode( $location ) ), $link);

	endif;

	return $link;
}

################################################################################
// Check theme is installed correctly
################################################################################

function check_jr_environment() {
	$errors = array();

	$files = array(
		'includes/theme-cron.php',
		'includes/theme-sidebars.php',
		'includes/theme-support.php',
		'includes/theme-comments.php',
		'includes/forms/application/application-process.php',
		'includes/forms/application/application-form.php',
		'includes/forms/filter/filter-process.php',
		'includes/forms/filter/filter-form.php',
		'includes/forms/share/share-form.php',
		'includes/theme-actions.php',
		'includes/admin/admin-options.php',
		'includes/admin/write-panel.php'
	);

	foreach ($files as $file) {
		if (!is_readable(TEMPLATEPATH.'/'.$file)) $errors[] = $file.__(' is not readable or does not exist - check file permissions.',APP_TD);
	}

	if (isset($errors) && sizeof($errors)>0) {
		echo '<div class="error" style="padding:10px"><strong>'.__('JobRoller theme errors:',APP_TD).'</strong>';
		foreach ($errors as $error) {
			echo '<p>'.$error.'</p>';
		}
		echo '</div>';
	}
}

// Buffer the output so headers work correctly
function buffer_the_output() {
	ob_start();
}

// count taxonomy terms
function jr_tax_has_terms( $taxonomy ) {
	return (int) get_terms( $taxonomy, array( 'hide_empty' => false, 'fields' => 'count' ) );
}

// Add custom post types to the Main RSS feed
function jr_rss_request($qv) {
	if (isset($qv['feed']) && !isset($qv['post_type'])) :
		$qv['post_type'] = array('post', 'job_listing');
	endif;
	return $qv;
}

function jr_rss_pre_get_posts($query) {
	if ($query->is_feed) $query->set('post_status','publish');
	return $query;
}

// get the custom taxonomy array and loop through the values
function jr_get_custom_taxonomy($post_id, $tax_name, $tax_class) {
    $tax_array = get_terms( $tax_name, array( 'hide_empty' => '0' ) );
    if ($tax_array && sizeof($tax_array) > 0) {
        foreach ($tax_array as $tax_val) {
            if ( is_object_in_term( $post_id, $tax_name, array( $tax_val->term_id ) ) ) {
                echo '<span class="'.$tax_class . ' '. $tax_val->slug.'">'.$tax_val->name.'</span>';
                break;
            }
        }
    }
}

// deletes all the database tables
function jr_delete_db_tables() {
	global $wpdb, $jr_db_tables;

	$db_tables = array_merge( $jr_db_tables, $jr_legacy_db_tables );

	foreach ( $db_tables as $key => $value ) :
		scb_uninstall_table($value);

		printf(__("Table '%s' has been deleted.", APP_TD), $value);
		echo '<br/>';
	endforeach;

	scb_uninstall_table('app_pop_daily');
	_e("Table 'app_pop_daily' has been deleted.", APP_TD);

	scb_uninstall_table('app_pop_total');
	_e("Table 'app_pop_total' has been deleted.", APP_TD);

}

// deletes all the theme options from wp_options
function jr_delete_all_options() {
	global $wpdb;

	$sql = "DELETE FROM ". $wpdb->options
		." WHERE option_name like 'jr_%'";
	$wpdb->query($sql);

	echo __("All JobRoller options have been deleted.", APP_TD);
}

// Define Nav Bar Locations
register_nav_menus( array(
	'primary' => __( 'Primary Navigation', APP_TD ),
	'top' => __( 'Top Bar Navigation', APP_TD ),
) );

// Sets or/and retrieves the current tab for pagination
function jr_dashboard_curr_page_tab( $new_tab = '' ) {
	static $tab;

	if ( $new_tab ) $tab = $new_tab;
	return $tab;
}

// Applies filters necessary for wp-pagenavi pagination to work properly on the dashboard tabs
function jr_wp_pagenavi_tab_pagination( $args ) {

	if ( empty($args['add_args']['tab']) )
		return;

	jr_dashboard_curr_page_tab( $args['add_args'] );

	add_filter( 'get_pagenum_link', 'jr_wp_pagenavi_add_tab' );
}

// Adds a 'tab' query var to the pagination link to allows pagination on the dashboard tabs
function jr_wp_pagenavi_add_tab( $result ) {
	$tab = jr_dashboard_curr_page_tab();
	return add_query_arg( $tab, $result );
}

// Function to output pagination
if (!function_exists('jr_paging')) {
function jr_paging( $new_wp_query = null, $query_var = 'paged', $args = array() ) {
	global $wp_query;
?>
		<div class="clear"></div>
		<div class="paging">
<?php
		if ( ! $new_wp_query ) $new_wp_query = $wp_query;

		if ( $new_wp_query->max_num_pages > 1 ) {

			if ( function_exists('wp_pagenavi') ) {
				$args['query'] = $new_wp_query;
				jr_wp_pagenavi_tab_pagination( $args );
				wp_pagenavi( $args );
			} else {
				appthemes_pagenavi( $new_wp_query, $query_var, $args ); 
			}

		}
?>
		<div class="top"><a href="#top" title="<?php _e( 'Back to top', APP_TD ); ?>"><?php _e( 'Top &uarr;', APP_TD ); ?></a></div>
	</div>
<?php
}
}

// Remaining days function
function jr_remaining_days( $post ) {
	$remain_days = __( 'Endless', APP_TD );

	$days = get_post_meta($post->ID, JR_JOB_DURATION_META, true);
	if ( $days ) {
		if ( $days >= 1 ) {
			$expire_date = strtotime( $post->post_date . '+' . $days . ' days' );
			$days = appthemes_days_between_dates( date('y-m-d', $expire_date ), date( 'y-m-d', strtotime('NOW') ), $precision = 0 ); 
			if ( $days >= 1 )
				$remain_days = $days . ' ' . _n( 'day', 'days', $days, APP_TD );
			else
				$remain_days = human_time_diff( strtotime('NOW'), $expire_date );
		} else
			$remain_days = __('Expired', APP_TD);
	}

	return apply_filters( 'jr_remaining_days', $remain_days );
}

// Expiry check function
if (!function_exists('jr_check_expired')) {
function jr_check_expired($post) {
	return ( 'expired' == $post->post_status );
}
}

function jr_get_job_expiration_date( $job_id = '' ) {
	global $post;
	
	$job_id = !empty( $job_id ) ? $job_id : get_the_ID();
	
	$duration = get_post_meta( $job_id, JR_JOB_DURATION_META, true );
	if( empty( $duration ) ){
		return 0;
	}
	
	return jr_get_expiration_date( $post->post_date, $duration );
}

function jr_get_expiration_date( $start_date, $duration ){
	$expiration_date = strtotime( $start_date .' + ' . $duration . 'days' );
	return appthemes_display_date( $expiration_date, 'date' );
}

// Expired Message
if (!function_exists('jr_expired_message')) {
function jr_expired_message($post) {
	$expired = jr_check_expired($post);
	if ($expired) :
		?><p class="expired"><?php _e('<strong>NOTE:</strong> This job listing has expired and may no longer be relevant!',APP_TD); ?></p><?php
	endif;
}
}

// Get Page URL
if ( !function_exists('jr_get_current_url') ) {
function jr_get_current_url($url = '') {

	if ( is_front_page() || is_search() ) :
		return trailingslashit(get_bloginfo('wpurl'));
	elseif ( is_category() ) :
		return trailingslashit(get_category_link(get_cat_id(single_cat_title("", false))));
	elseif ( is_tax() ) :
		$job_cat = get_query_var('job_cat');
		$job_type = get_query_var('job_type');

		if (isset($job_cat) && $job_cat) :
			$slug = $job_cat;
			return trailingslashit(get_term_link( $slug, 'job_cat' ));
		elseif (isset($job_type) && $job_type) :
			$slug = $job_type;
			return trailingslashit(get_term_link( $job_type, 'job_type' ));
		endif;

	endif;
	return trailingslashit($url);
}
}

// get the visitor IP so we can include it with the job submission
if (!function_exists('jr_getIP')) {
function jr_getIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {  //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

	$ip = ( $ip == '::1' ? '127.0.0.1' : $ip );

	// avoid multiple IP's
	$ip = explode( ',', $ip );
	$ip = reset( $ip );

    return $ip;
}
}

// tinyMCE text editor
if (!function_exists('jr_tinymce')) {
function jr_tinymce($width='', $height='') {
?>
<script type="text/javascript">
    <!--

	tinyMCEPreInit = {
		base : "<?php echo includes_url('js/tinymce'); ?>",
		suffix : "",
		mceInit : {
			mode : "specific_textareas",
			editor_selector : "mceEditor",
			theme : "advanced",
			plugins: "paste",
			skin : "default",
	        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",
	        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,cleanup,code,|,forecolor,backcolor,|,media",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,
			theme_advanced_resize_horizontal : false,
			content_css : "<?php echo get_stylesheet_directory_uri(); ?>/style.css",
			languages : 'en',
			disk_cache : true,
			width : "<?php echo $width; ?>",
			height : "<?php echo $height; ?>",
			language : 'en'
		},
		load_ext : function(url,lang){var sl=tinymce.ScriptLoader;sl.markDone(url+'/langs/'+lang+'.js');sl.markDone(url+'/langs/'+lang+'_dlg.js');}
	};

	(function(){var t=tinyMCEPreInit,sl=tinymce.ScriptLoader,ln=t.mceInit.language,th=t.mceInit.theme;sl.markDone(t.base+'/langs/'+ln+'.js');sl.markDone(t.base+'/themes/'+th+'/langs/'+ln+'.js');sl.markDone(t.base+'/themes/'+th+'/langs/'+ln+'_dlg.js');})();
	tinyMCE.init(tinyMCEPreInit.mceInit);

    -->
</script>
<?php
}
}

// get the date/time of the post
if (!function_exists('jr_ad_posted')) {
function jr_ad_posted($m_time) {
    //$t_time = get_the_time(__('Y/m/d g:i:s A'));
    $time = get_post_time('G', true);
    $time_diff = time() - $time;

    if ( $time_diff > 0 && $time_diff < 24*60*60 )
            $h_time = sprintf( __('%s ago', APP_TD), human_time_diff( $time ) );
    else
            $h_time = mysql2date(get_option('date_format'), $m_time);
    echo $h_time;
}
}

// Filters
function custom_excerpt($text) {
	global $post;
	return str_replace(' [...]', '&hellip; <a href="'. get_permalink($post->ID) . '" class="more">' . __('read more',APP_TD) . '</a>', $text);
}

// search on custom fields
function custom_search_join($join) {
    if ( is_search() && isset($_GET['s'])) {
        global $wpdb;
       $join = " LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id ";
    }
    return($join);
}
// search on custom fields
function custom_search_groupby($groupby) {
    if ( is_search() && isset($_GET['s'])) {
        global $wpdb;
        $groupby = " $wpdb->posts.ID ";
    }
    return($groupby);
}
// search on custom fields
function custom_search_where($where) {
    global $wpdb;
    $old_where = $where;
    if (is_search() && isset($_GET['s']) && !isset($_GET['resume_search'])) {
		// add additional custom fields here to include them in search results
        $customs = array('_Company', 'geo_address', '_CompanyURL', 'geo_short_address', 'geo_country', 'geo_short_address_country');
        $query = '';
        $var_q = stripslashes($_GET['s']);
        preg_match_all('/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $var_q, $matches);
        $search_terms = array_map(create_function('$a', 'return trim($a, "\\"\'\\n\\r ");'), $matches[0]);

        $n = '%';
        $searchand = '';
        foreach((array)$search_terms as $term) {
            $term = addslashes_gpc($term);
            $query .= "{$searchand}(";
            $query .= "($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
            $query .= " OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}')";
            foreach($customs as $custom) {
                $query .= " OR (";
                $query .= "($wpdb->postmeta.meta_key = '$custom')";
                $query .= " AND ($wpdb->postmeta.meta_value  LIKE '{$n}{$term}{$n}')";
                $query .= ")";
            }
            $query .= ")";
            $searchand = ' AND ';
        }
        $term = $wpdb->escape($var_q);
        $where .= " OR ($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
        $where .= " OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}')";

        if (!empty($query)) {
            $where = " AND ({$query}) AND ($wpdb->posts.post_status = 'publish') AND ($wpdb->posts.post_type = 'job_listing')";
        }
    } else if (is_search() && isset($_GET['s'])) {
    	// add additional custom fields here to include them in search results
        $customs = array(
        	'_desired_position',
        	'_resume_websites',
        	'_experience',
        	'_education',
        	'_skills',
        	'_desired_salary',
        	'_email_address',
        	'geo_address',
        	'geo_country'
        );
        $query = '';
        $var_q = stripslashes($_GET['s']);
        preg_match_all('/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $var_q, $matches);
        $search_terms = array_map(create_function('$a', 'return trim($a, "\\"\'\\n\\r ");'), $matches[0]);

        $n = '%';
        $searchand = '';
        foreach((array)$search_terms as $term) {
            $term = addslashes_gpc($term);
            $query .= "{$searchand}(";
            $query .= "($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
            $query .= " OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}')";
            foreach($customs as $custom) {
                $query .= " OR (";
                $query .= "($wpdb->postmeta.meta_key = '$custom')";
                $query .= " AND ($wpdb->postmeta.meta_value  LIKE '{$n}{$term}{$n}')";
                $query .= ")";
            }
            $query .= ")";
            $searchand = ' AND ';
        }
        $term = $wpdb->escape($var_q);
        $where .= " OR ($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
        $where .= " OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}')";

        if (!empty($query)) {
            $where = " {$old_where} AND ({$query}) AND ($wpdb->posts.post_status = 'publish') AND ($wpdb->posts.post_type = 'resume')";
        }
    }
    return($where);
}

function jr_search_query( $query ) {
	if ( empty( $query ) && ! empty( $_GET['s']) ) {
		$query = wp_strip_all_tags( $_GET['s'] );
	}
	return $query;
}

// redirects a user to my jobs
if (!function_exists('redirect_myjobs')) {
function redirect_myjobs( $query_string = '' ) {
	$url = get_permalink( JR_Dashboard_Page::get_id() );
	if (is_array($query_string)) $url = add_query_arg( $query_string, $url );
    wp_redirect($url);
    exit();
}
}

// redirects a user to my profile
if (!function_exists('redirect_profile')) {
function redirect_profile( $query_string = '' ) {
	$url = get_permalink( JR_User_Profile_Page::get_id() );
	if (is_array($query_string)) $url = add_query_arg( $query_string, $url );
    wp_redirect($url);
    exit();
}
}

// Output errors
if (!function_exists('jr_show_errors')) {
function jr_show_errors( $errors, $id = '' ) {
	$error_msg = '';
	if ($errors && sizeof($errors)>0 && $errors->get_error_code()) :
		foreach ($errors->errors as $error) {
			$error_msg .= html( 'div', $error[0] );
		}
		appthemes_display_notice('error', $error_msg );
	endif;
}
}

if (!function_exists('let_to_num')) {
	function let_to_num($v){
		$l = substr($v, -1);
	    $ret = substr($v, 0, -1);
	    switch(strtoupper($l)){
	    case 'P':
	        $ret *= 1024;
	    case 'T':
	        $ret *= 1024;
	    case 'G':
	        $ret *= 1024;
	    case 'M':
	        $ret *= 1024;
	    case 'K':
	        $ret *= 1024;
	        break;
	    }
	    return $ret;
	}
}

function jr_job_author() {
	global $post;

	$company_name = wptexturize(strip_tags(get_post_meta($post->ID, '_Company', true)));

	if ( $company_name ) {
		if ( $company_url = esc_url( get_post_meta( $post->ID, '_CompanyURL', true ) ) ) {
?>
			<a href="<?php echo $company_url; ?>" rel="nofollow"><?php echo $company_name; ?></a>
<?php
		} else {
			echo $company_name;
		}
		$format = __(' &ndash; Posted by <a href="%s">%s</a>', APP_TD);
	} else {
		$format = '<a href="%s">%s</a>';
	}

	$author = get_user_by('id', $post->post_author);
	if ( $author && $link = get_author_posts_url( $author->ID, $author->user_nicename ) )
		echo sprintf( $format, $link, $author->display_name );
}

function jr_location( $with_comma = false ) {
	global $post;

	$address = get_post_meta($post->ID, 'geo_short_address', true);

	if ( !$address )
		$address = __( 'Anywhere', APP_TD );

	echo "<strong>$address</strong>";

	$country = strip_tags(get_post_meta($post->ID, 'geo_short_address_country', true));

	if ( $country ) {
		if ( $with_comma )
			echo ', ';

		echo $country;
	}
}

// calculate and return the week start date
function jr_week_start_date($week, $year, $format = "d-m-Y") {

	$first_day_year = date("N", mktime(0,0,0,1,1,$year));
	if ($first_day_year < 5)
		$shift =-($first_day_year-1)*86400;
	else
		$shift=(8-$first_day_year)*86400;
	if ($week > 1) $week_seconds = ($week-1)*604800; else $week_seconds = 0;
	$timestamp = mktime(0,0,0,1,1,$year) + $week_seconds + $shift;

	return date($format, $timestamp);
}

// get the server country
function jr_get_server_country() {

	// Get user country
	if(isset($_SERVER['HTTP_X_FORWARD_FOR'])) $ip = $_SERVER['HTTP_X_FORWARD_FOR']; else $ip = $_SERVER['REMOTE_ADDR'];

	$ip = strip_tags($ip);
	$country = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

	$result = wp_remote_get('http://api.hostip.info/country.php?ip='.$ip);
	if (!is_wp_error($result) && strtolower($result['body']) != 'xx') $country = $result['body'];

	return strtolower($country);
}

// returns the translated month
function jr_translate_months( $month ) {

	$translated_months = array (
		'january'  	=> __('January', APP_TD),
		'february' 	=> __('February', APP_TD),
		'march' 	=> __('March', APP_TD),
		'april' 	=> __('April', APP_TD),
		'may' 		=> __('May', APP_TD),
		'june' 		=> __('June', APP_TD),
		'july' 		=> __('July', APP_TD),
		'august' 	=> __('August', APP_TD),
		'september' => __('September', APP_TD),
		'october' 	=> __('October', APP_TD),
		'november' 	=> __('November', APP_TD),
		'december' 	=> __('December', APP_TD),
	);

	return $translated_months[ strtolower(trim($month)) ];

}

// output sponsored listings
if ( !function_exists('jr_display_sponsored_results') ):
function jr_display_sponsored_results( $search_results, $params, $is_ajax = false, $page = 1 ) {

	$defaults = array (
		'link_class'  => array('more_sponsored_results', 'front_page'),
		'tax'		  => '',
		'term'		  => ''
	);
	$params = wp_parse_args( $params, $defaults );

	$alt = 1;
	$first = true;

	if (!$is_ajax) :
		echo sprintf('<div class="section"><h2 class="pagetitle">%s</h2>', esc_html($params['title']));
   		echo sprintf('<ol class="jobs sponsored_results" source="%s">', esc_attr($params['source']));
	endif;

	foreach ($search_results as $job) :

		$job_defaults = array (
			'onmousedown' => '',
		);
		$job = wp_parse_args( $job, $job_defaults );

		$post_class = array('job');
		if ($alt==1) $post_class[] = 'job-alt';

		// check for the special sponsored job types (i.e: paid, sponsored or organic) and add them as classes
		if ( isset($job['type']) && $job['type'] ) $post_class[] = 'ty_' . strtolower( $params['source'] ) . '_' . $job['type'];

		// check for the additional classes to add
		if ( isset($job['class']) && $job['class'] ) $post_class[] = $job['class'];

		?>

		<li class="<?php echo esc_attr( implode(' ', $post_class) ); ?>" <?php if ($is_ajax && $first) echo 'id="more-'.$page.'"'; ?>><dl>

	            <dt><?php _e('Type',APP_TD); ?></dt>
	            <dd class="type"><span class="ftype <?php echo esc_attr($job['jobtype']); ?>"><?php echo ucwords(esc_html($job['jobtype_name'])); ?></span></dd>

	            <dt><?php _e('Job', APP_TD); ?></dt>
	            <dd class="title">
				<strong><a href="<?php echo esc_url($job['url']); ?>" target="_blank" rel="nofollow" onmousedown="<?php echo esc_attr($job['onmousedown']); ?>"><?php echo esc_html($job['jobtitle']); ?></a></strong>
				<?php echo wptexturize($job['company']); ?>
	            </dd>

	            <dt><?php _e('Location', APP_TD); ?></dt>
	            <dd class="location"><strong><?php echo esc_html($job['location']); ?></strong> <?php echo esc_html($job['country']); ?></dd>

	            <dt><?php _e('Date Posted', APP_TD); ?></dt>
	            <dd class="date"><strong><?php echo date_i18n('j M', strtotime($job['date'])); ?></strong> <span class="year"><?php echo date_i18n('Y', strtotime($job['date'])); ?></span></dd>

	    </dl></li>

		<?php
	endforeach;

	if (!$is_ajax) :

		echo '</ol>
		<div class="paging sponsored_results_paging">
	        <div style="float:left;"><a href="#more" source="'. esc_attr($params['source']) .'" callback="' . esc_attr($params['callback']) . '" class="'.esc_attr(implode(' ', $params['link_class'])).'" tax="'.esc_attr($params['tax']).'" term="'.esc_attr($params['term']).'" rel="2" >Load More &raquo;</a></div>
			<p class="attribution"><a href="'.esc_url($params['url']).'">jobs</a> by <a href="'.esc_url($params['url']).'" title="Job Search" target="_new"><img src="' . esc_attr($params['jobs_by_img']) . '" alt="' . esc_attr($params['source']) . ' job search" /></a></p>
	    </div></div>';

    endif;

}
endif;

// displays notices
if ( !function_exists('jr_notices') ):
function jr_notices() {
	global $post, $message, $errors;


	if ( $err_obj = jr_get_listing_error_obj() ) {
		if( $err_obj->get_error_codes() ){
			$errors = $err_obj;
		}
	}

	if ( $errors && sizeof($errors)>0 && $errors->get_error_code() ) { jr_show_errors($errors); return; }
	elseif ( !empty($message) )	{ appthemes_display_notice( 'success', strip_tags(stripslashes($message)) ); return; }

	if (isset($post)):

		// dashboard notices
		if ( $post->ID == JR_Dashboard_Page::get_id() ) {

				if ( isset($_GET['relist_success']) && is_numeric($_GET['relist_success']) )
					appthemes_display_notice( 'success', __('Job relisted successfully',APP_TD) );
				else
					if ( isset($_GET['edit_success']) && is_numeric($_GET['edit_success']) )
						appthemes_display_notice( 'success', __('Job edited successfully',APP_TD) );
				else
					if ( isset($_POST['payment_status']) && strtolower($_POST['payment_status'])=='completed' )
						appthemes_display_notice( 'success', __('Thank you for your Order!',APP_TD) );

		// single resume notices
		} else {

			if ( is_singular(APP_POST_TYPE_RESUME) ):

				if ( isset($_GET['resume_contact']) && is_numeric($_GET['resume_contact']) )
					if ( $_GET['resume_contact']>0 )
						appthemes_display_notice( 'success', __('Your message was sent', APP_TD) );
					else
						appthemes_display_notice( 'error', __('Could not send message at this time. Please try again later', APP_TD) );

			endif;

		}

	endif;

}
endif;

// allow post status translations
function jr_post_statuses_i18n( $status ) {
	$statuses = array(
		'draft'			=> __('Draft', APP_TD),
		'pending'		=> __('Pending Review', APP_TD),
		'private'		=> __('Private', APP_TD),
		'publish'		=> __('Published', APP_TD),
		'expired'		=> __('Expired', APP_TD)
	);

	if ( isset($statuses[$status]) ) {
		$i18n_status = $statuses[$status];
	} else {
		$i18n_status = $status;
	}
	return $i18n_status;
}

function jr_prefix_field( $field, $prefix = '' ) {
	global $app_abbr;

	if  ( ! $prefix ) $prefix = $app_abbr;

	if ( is_array($field) ) {
		$key = key($field);
		$field_prefixed[$app_abbr . '_' . $key] = $field;
		return $field_prefixed;
	} else {
		return $app_abbr . '_' . $field;
	}
}

function jr_get_jobs_per_page() {
	global $app_abbr;
	return intval( get_option( $app_abbr . '_jobs_per_page', 10 ) );
}

function jr_get_featured_jobs_per_page() {
	global $app_abbr;
	return intval( get_option( $app_abbr . '_featured_jobs_per_page', -1 ) );
}

function jr_allow_relist() {
	global $app_abbr;
	return ( 'yes' == get_option( $app_abbr . '_allow_relist' ) );
}

function jr_allow_editing() {
	global $app_abbr;
	return ( 'yes' == get_option( $app_abbr . '_allow_editing' ) );
}

function jr_get_resumes_per_page() {
	global $app_abbr;
	return intval( get_option( $app_abbr . '_resumes_per_page', 10 ) );
}

/**
* Resets an multi-dimensional array of single values and returns a one dimensional array
* @param array $data The data array
* 
* @return array A one dimensional array
*/
function jr_reset_data( $data ) {

	$new_data = array();
	foreach ( $data as $key => $value )
		$new_data[$key] = reset($value);

	return $new_data;
}

function _jr_prefix_fields( $fields ) {
	foreach( $fields as $key => $field ) {
		if ( 'title' != $field['name'] ) {
			$field['name'] = JR_FIELD_PREFIX . $field['name'];
		}
		$prefixed_fields[] = $field;
	}
	return $prefixed_fields;
}

function _jr_unprefix_fields( $fields ) {
	foreach( $fields as $field => $value ) {
		$unprefixed_fields[str_replace( JR_FIELD_PREFIX, '', $field )] = $value;
	}
	return $unprefixed_fields;
}

function jr_display_recaptcha($support) {
	global $app_abbr;

	if ( ! current_theme_supports($support) ) return false;

	list( $options ) = get_theme_support($support);

	if ( ( 'visitors' == $options['display_rule']  && ! is_user_logged_in() ) ||
		 ( 'all' == $options['display_rule'] ) ) {
		 	return true;
		} else {
		 	return false;
		 }
}

function jr_ajax_validate_recaptcha() {

	$support = strval( $_POST['support'] );
	$nonce = strval( $_POST['nonce'] );

	if ( !wp_verify_nonce( $nonce, 'jr-nonce' ) )
		die ( 'Busted!' );

	$errors = jr_validate_recaptcha($support);
	if ( $errors && sizeof($errors)>0 && $errors->get_error_code() ) {
		echo $errors->get_error_message();
	} else {
		echo "1";
	}
	die();
}

function jr_validate_recaptcha($support) {
	global $app_abbr;

	if ( ! jr_display_recaptcha($support) ) return FALSE;

	$errors = new WP_Error();

	if ( current_theme_supports($support) ) {
		list( $options ) = get_theme_support($support);
		require_once ( get_template_directory() . '/includes/lib/recaptchalib.php' );

		// check and make sure the reCaptcha values match
		$resp = recaptcha_check_answer( $options['private_key'], $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field'] );

		if ( !$resp->is_valid ) {
			$errors->add( 'invalid_recaptcha', __('<strong>ERROR</strong>: The reCaptcha anti-spam response was incorrect.', APP_TD ) );
		}
	}
	return $errors;
}

function jr_no_access_permission( $message ) {
	$login = '';
	if ( ! is_user_logged_in() ) {
		$login = sprintf( __( ' Please <a href="%s">login or register</a>.', APP_TD ), wp_login_url( get_permalink() ) );
	}
	echo html( 'p', $message. $login );

	echo html( 'a', array( 'href' => home_url() ), __( '&nbsp;&larr; Return to main page', APP_TD ) ); 
}

// temporary
function jt_get_jobs_per_page() {
	return jr_get_jobs_per_page();
}

// run the appthemes_init() action hook
appthemes_init();
