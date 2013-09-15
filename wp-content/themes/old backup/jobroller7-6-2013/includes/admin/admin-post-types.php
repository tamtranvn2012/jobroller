<?php
/**
 * Custom post types and taxonomies
 *
 *
 * @version 1.2
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */

add_action( 'init', 'jr_post_type', 10 );

// add this option to the edit post submit box
add_action( 'post_submitbox_misc_actions', 'jr_post_type_changer' );

// activate this function to load in the admin head
add_action( 'admin_head', 'jr_post_type_changer_head' );
add_action( 'admin_head', 'jr_custom_icons' );

//Display the custom column data for each user
add_action( 'manage_users_columns', 'jr_manage_users_columns' );
add_action( 'manage_users_custom_column', 'jr_manage_users_custom_column', 10, 3 );

add_filter( 'manage_post_posts_columns', 'jr_post_thumbnail_column' );
add_action( 'manage_posts_custom_column', 'jr_custom_thumbnail_column' );

add_action( 'manage_' . APP_POST_TYPE . '_posts_custom_column', 'jr_jobs_custom_columns' );
add_filter( 'manage_edit-' . APP_POST_TYPE . '_sortable_columns', 'jr_listing_columns_sort' );
add_filter( 'manage_edit-' . APP_POST_TYPE . '_columns', 'jr_edit_jobs_columns' );

add_action( 'manage_' . APP_POST_TYPE_RESUME . '_posts_custom_column', 'jr_resumes_custom_columns' );
add_filter( 'manage_edit-' . APP_POST_TYPE_RESUME . '_columns', 'jr_edit_resumes_columns' );
add_filter( 'manage_edit-' . APP_POST_TYPE_RESUME . '_sortable_columns', 'jr_resumes_columns_sort' );

// create the custom post type and category taxonomy for ad listings
// Define the custom post types
function jr_post_type() {
	global $app_abbr;

	// make sure the new roles are added to the DB before registering the post types
	if ( isset( $_GET['firstrun'] ) ) {
		jr_init_roles();
	}

	// get the slug value for the ad custom post type & taxonomies
	if(get_option($app_abbr.'_job_permalink')) $post_type_base_url = get_option($app_abbr.'_job_permalink'); else $post_type_base_url = 'jobs';
	if(get_option($app_abbr.'_job_cat_tax_permalink')) $cat_tax_base_url = get_option($app_abbr.'_job_cat_tax_permalink'); else $cat_tax_base_url = 'job-category';
	if(get_option($app_abbr.'_job_type_tax_permalink')) $type_tax_base_url = get_option($app_abbr.'_job_type_tax_permalink'); else $type_tax_base_url = 'job-type';
	if(get_option($app_abbr.'_job_tag_tax_permalink')) $tag_tax_base_url = get_option($app_abbr.'_job_tag_tax_permalink'); else $tag_tax_base_url = 'job-tag';
	if(get_option($app_abbr.'_job_salary_tax_permalink')) $sal_tax_base_url = get_option($app_abbr.'_job_salary_tax_permalink'); else $sal_tax_base_url = 'salary';	
	if(get_option($app_abbr.'_resume_permalink')) $resume_post_type_base_url = get_option($app_abbr.'_resume_permalink'); else $resume_post_type_base_url = 'resumes';

	// register the new job category taxonomy
	register_taxonomy( 
		APP_TAX_CAT,
		array( APP_POST_TYPE ),
		array(
			'hierarchical' => true,
			'labels' => array(
				'name' => __( 'Job Categories', APP_TD),
				'singular_name' => __( 'Job Category', APP_TD),
				'search_items' =>  __( 'Search Job Categories', APP_TD),
				'all_items' => __( 'All Job Categories', APP_TD),
				'parent_item' => __( 'Parent Job Category', APP_TD),
				'parent_item_colon' => __( 'Parent Job Category:', APP_TD),
				'edit_item' => __( 'Edit Job Category', APP_TD),
				'update_item' => __( 'Update Job Category', APP_TD),
				'add_new_item' => __( 'Add New Job Category', APP_TD),
				'new_item_name' => __( 'New Job Category Name', APP_TD)
			),
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'update_count_callback' => '_update_post_term_count',
			'rewrite' => array( 'slug' => $cat_tax_base_url, 'hierarchical' => true ),
		)
	);

	// register the new job type taxonomy
	register_taxonomy( 
		APP_TAX_TYPE,
		array( APP_POST_TYPE ),
		array(
			'hierarchical' => true,
			'labels' => array(
				'name' => __( 'Job Types', APP_TD),
				'singular_name' => __( 'Job Type', APP_TD),
				'search_items' =>  __( 'Search Job Types', APP_TD),
				'all_items' => __( 'All Job Types', APP_TD),
				'parent_item' => __( 'Parent Job Type', APP_TD),
				'parent_item_colon' => __( 'Parent Job Type:', APP_TD),
				'edit_item' => __( 'Edit Job Type', APP_TD),
				'update_item' => __( 'Update Job Type', APP_TD),
				'add_new_item' => __( 'Add New Job Type', APP_TD),
				'new_item_name' => __( 'New Job Type Name', APP_TD)
			),
			'show_ui' => true,
			'query_var' => true,
			'update_count_callback' => '_update_post_term_count',
			'rewrite' => array( 'slug' => $type_tax_base_url, 'hierarchical' => true ),
		)
	);

	// register the new job tag taxonomy
	register_taxonomy( 
		APP_TAX_TAG,
		array( APP_POST_TYPE ),
		array(
			'hierarchical' => false,
			'labels' => array(
				'name' => __( 'Job Tags', APP_TD),
				'singular_name' => __( 'Job Tag', APP_TD),
				'search_items' =>  __( 'Search Job Tags', APP_TD),
				'all_items' => __( 'All Job Tags', APP_TD),
				'parent_item' => __( 'Parent Job Tag', APP_TD),
				'parent_item_colon' => __( 'Parent Job Tag:', APP_TD),
				'edit_item' => __( 'Edit Job Tag', APP_TD),
				'update_item' => __( 'Update Job Tag', APP_TD),
				'add_new_item' => __( 'Add New Job Tag', APP_TD),
				'new_item_name' => __( 'New Job Tag Name', APP_TD)
			),
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => $tag_tax_base_url ),
			'update_count_callback' => '_update_post_term_count'
		)
	);

	// register the salary taxonomy
	register_taxonomy( 
		APP_TAX_SALARY,
		array( APP_POST_TYPE ),
		array(
			'hierarchical' => true,
			'labels' => array(
				'name' => __( 'Salary', APP_TD),
				'singular_name' => __( 'Salary', APP_TD),
				'search_items' =>  __( 'Search Salaries', APP_TD),
				'all_items' => __( 'All Salaries', APP_TD),
				'parent_item' => __( 'Parent Salary', APP_TD),
				'parent_item_colon' => __( 'Parent Salary:', APP_TD),
				'edit_item' => __( 'Edit Salary', APP_TD),
				'update_item' => __( 'Update Salary', APP_TD),
				'add_new_item' => __( 'Add New Salary', APP_TD),
				'new_item_name' => __( 'New Salary', APP_TD)
			),
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => $sal_tax_base_url ),
		)
	);

	$custom_caps = array(
		'edit_posts' => 'edit_jobs', // enables job listers to view pending jobs
	);

	// create the custom post type and category taxonomy for job listings
	register_post_type( 
			APP_POST_TYPE,
			array( 'labels' => array(
					'name' => __( 'Jobs', APP_TD ),
					'singular_name' => __( 'Jobs', APP_TD ),
					'add_new' => __( 'Add New', APP_TD ),
					'add_new_item' => __( 'Add New Job', APP_TD ),
					'edit' => __( 'Edit', APP_TD ),
					'edit_item' => __( 'Edit Job', APP_TD ),
					'new_item' => __( 'New Job', APP_TD ),
					'view' => __( 'View Jobs', APP_TD ),
					'view_item' => __( 'View Job', APP_TD ),
					'search_items' => __( 'Search Jobs', APP_TD ),
					'not_found' => __( 'No jobs found', APP_TD ),
					'not_found_in_trash' => __( 'No jobs found in trash', APP_TD ),
					'parent' => __( 'Parent Job', APP_TD ),
				),
				'description' => __( 'This is where you can create new job listings on your site.', APP_TD ),
				'public' => true,
				'show_ui' => true,
				'capabilities' => $custom_caps,
				'map_meta_cap' => true,
				'publicly_queryable' => true,
				'exclude_from_search' => false,
				'menu_position' => 8,
				'has_archive' => true,
				'menu_icon' => get_template_directory_uri() . '/images/job_icon.png',
				'hierarchical' => false,
				'rewrite' => array( 'slug' => $post_type_base_url, 'with_front' => false ), /* Slug set so that permalinks work when just showing post name */
				'query_var' => true,
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'sticky' ),
		)
	);

	if (get_option('jr_allow_job_seekers')=='yes') $show_ui = true; else $show_ui = false;

	register_taxonomy( 
		APP_TAX_RESUME_SPECIALITIES,
		array( APP_POST_TYPE_RESUME ),
		array(
			'hierarchical' => false,
			'labels' => array(
				'name' => __( 'Resume Specialties', APP_TD),
				'singular_name' => __( 'Resume Specialty', APP_TD),
				'search_items' =>  __( 'Search Resume Specialties', APP_TD),
				'all_items' => __( 'All Resume Specialties', APP_TD),
				'parent_item' => __( 'Parent Resume Specialty', APP_TD),
				'parent_item_colon' => __( 'Parent Resume Specialty:', APP_TD),
				'edit_item' => __( 'Edit Resume Specialty', APP_TD),
				'update_item' => __( 'Update Resume Specialty', APP_TD),
				'add_new_item' => __( 'Add New Resume Specialty', APP_TD),
				'new_item_name' => __( 'New Resume Specialty Name', APP_TD)
			),
			'show_ui' => $show_ui,
			'rewrite' => array( 'slug' => 'resume/speciality', 'with_front' => false ),
			'query_var' => true,
			'update_count_callback' => '_update_post_term_count'
		)
	);

	register_taxonomy( 
		APP_TAX_RESUME_GROUPS,
		array( APP_POST_TYPE_RESUME ),
		array(
			'hierarchical' => false,
			'labels' => array(
				'name' => __( 'Groups/Associations', APP_TD),
				'singular_name' => __( 'Resume Group/Association', APP_TD),
				'search_items' =>  __( 'Search Groups/Associations', APP_TD),
				'all_items' => __( 'All Groups/Associations', APP_TD),
				'parent_item' => __( 'Parent Group/Association', APP_TD),
				'parent_item_colon' => __( 'Parent Group/Association:', APP_TD),
				'edit_item' => __( 'Edit Group/Association', APP_TD),
				'update_item' => __( 'Update Group/Association', APP_TD),
				'add_new_item' => __( 'Add New Group/Association', APP_TD),
				'new_item_name' => __( 'New Group/Association Name', APP_TD)
			),
			'show_ui' => $show_ui,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'resume/group', 'with_front' => false ),
			'update_count_callback' => '_update_post_term_count'
		)
	);

	register_taxonomy( 
		APP_TAX_RESUME_LANGUAGES,
		array( APP_POST_TYPE_RESUME ),
		array(
			'hierarchical' => false,
			'labels' => array(
				'name' => __( 'Spoken Languages', APP_TD),
				'singular_name' => __( 'Spoken Langauge', APP_TD),
				'search_items' =>  __( 'Search Spoken Languages', APP_TD),
				'all_items' => __( 'All Spoken Languages', APP_TD),
				'parent_item' => __( 'Parent Spoken Language', APP_TD),
				'parent_item_colon' => __( 'Parent Spoken Language:', APP_TD),
				'edit_item' => __( 'Edit Spoken Language', APP_TD),
				'update_item' => __( 'Update Spoken Language', APP_TD),
				'add_new_item' => __( 'Add New Spoken Language', APP_TD),
				'new_item_name' => __( 'New Spoken Language Name', APP_TD)
			),
			'show_ui' => $show_ui,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'resume/language', 'with_front' => false ),
			'update_count_callback' => '_update_post_term_count'
		)
	);

	register_taxonomy( 
		APP_TAX_RESUME_CATEGORY,
		array( APP_POST_TYPE_RESUME ),
		array(
			'hierarchical' => true,
			'labels' => array(
				'name' => __( 'Resume Categories', APP_TD),
				'singular_name' => __( 'Resume Category', APP_TD),
				'search_items' =>  __( 'Search Resume Categories', APP_TD),
				'all_items' => __( 'All Resume Categories', APP_TD),
				'parent_item' => __( 'Parent Resume Category', APP_TD),
				'parent_item_colon' => __( 'Parent Resume Category:', APP_TD),
				'edit_item' => __( 'Edit Resume Category', APP_TD),
				'update_item' => __( 'Update Resume Category', APP_TD),
				'add_new_item' => __( 'Add New Resume Category', APP_TD),
				'new_item_name' => __( 'New Resume Category Name', APP_TD)
			),
			'show_ui' => $show_ui,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'resume/category', 'with_front' => false ), 
			'update_count_callback' => '_update_post_term_count'
		)
	);

	register_taxonomy( 
		APP_TAX_RESUME_JOB_TYPE,
		array( APP_POST_TYPE_RESUME ),
		array(
			'hierarchical' => false,
			'labels' => array(
				'name' => __( 'Resume Job Types', APP_TD),
				'singular_name' => __( 'Resume Job Type', APP_TD),
				'search_items' =>  __( 'Search Resume Job Types', APP_TD),
				'all_items' => __( 'All Resume Job Types', APP_TD),
				'parent_item' => __( 'Parent Resume Job Type', APP_TD),
				'parent_item_colon' => __( 'Parent Resume Job Type:', APP_TD),
				'edit_item' => __( 'Edit Resume Job Type', APP_TD),
				'update_item' => __( 'Update Resume Job Type', APP_TD),
				'add_new_item' => __( 'Add New Resume Job Type', APP_TD),
				'new_item_name' => __( 'New Resume Job Type Name', APP_TD)
			),
			'show_ui' => $show_ui,
			'rewrite' => array( 'slug' => 'resume/job-type', 'with_front' => false ),
			'query_var' => true,
			'update_count_callback' => '_update_post_term_count'
		)
	);

	register_post_type( 
		APP_POST_TYPE_RESUME,
		array(
			'labels' => array(
				'name' => __( 'Resumes', APP_TD ),
				'singular_name' => __( 'Resume', APP_TD ),
				'add_new' => __( 'Add New', APP_TD ),
				'add_new_item' => __( 'Add New Resume', APP_TD ),
				'edit' => __( 'Edit', APP_TD ),
				'edit_item' => __( 'Edit Resume', APP_TD ),
				'new_item' => __( 'New Resume', APP_TD ),
				'view' => __( 'View Resumes', APP_TD ),
				'view_item' => __( 'View Resume', APP_TD ),
				'search_items' => __( 'Search Resumes', APP_TD ),
				'not_found' => __( 'No Resumes found', APP_TD ),
				'not_found_in_trash' => __( 'No Resumes found in trash', APP_TD ),
				'parent' => __( 'Parent Resume', APP_TD ),
			),
			'description' => __( 'Resumes are created and edited by job_seekers.', APP_TD ),
			'public' => true,
			'show_ui' => $show_ui,
			'capability_type' => 'post',
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'menu_position' => 8,
			'menu_icon' => get_template_directory_uri() . '/images/resume_icon.png',
			'hierarchical' => false,
			'rewrite' => array( 'slug' => $resume_post_type_base_url, 'with_front' => false ), /* Slug set so that permalinks work when just showing post name */
			'query_var' => true,
			'has_archive' => $resume_post_type_base_url,
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'custom-fields' ),
			)
	);

}

function jr_custom_thumbnail_column( $column ){
	global $post;
	$custom = get_post_custom();
	switch ($column) {
		case 'thumbnail' :
			if (has_post_thumbnail($post->ID)) 
				echo get_the_post_thumbnail($post->ID, 'sidebar-thumbnail');
			break;
	}
}

function jr_jobs_custom_columns( $column ){
	global $post;
	$custom = get_post_custom();
	switch ($column) {
		case 'company':
			if ( isset($custom['_Company'][0]) && !empty($custom['_Company'][0]) ) :
				if ( isset($custom['_CompanyURL'][0]) && !empty($custom['_CompanyURL'][0]) ) echo '<a href="'.$custom['_CompanyURL'][0].'">'.$custom['_Company'][0].'</a>';
				else echo $custom['_Company'][0];
			endif;
			break;
		case 'location':
			if ( isset($custom['geo_address'][0]) && !empty($custom['geo_address'][0]) ) :
				echo $custom['geo_address'][0];
			else :
				_e('Anywhere', APP_TD);
			endif;
			break;
		case APP_TAX_TYPE :
		case APP_TAX_SALARY :
		case APP_TAX_CAT :
			echo get_the_term_list( $post->ID, $column, '', ', ','' );
			break;
		case 'expire_date' :
			$expiration_date = jr_get_job_expiration_date( $post->ID );
			if ( $expiration_date ) {
				echo $expiration_date;
				if ( jr_check_expired( $post ) ) {
					echo html( 'p', array( 'class' => 'admin-job-expired' ), __( 'Expired', APP_TD ) );
				}
			} else 
				echo __( 'Endless', APP_TD );
			break;
		case 'logo':
			if (has_post_thumbnail($post->ID)) 
				echo get_the_post_thumbnail($post->ID, 'sidebar-thumbnail');
			break;
	}
}

function jr_resumes_custom_columns( $column ){
	global $post;
	$custom = get_post_custom();
	switch ($column) {
		case 'location':
			if ( isset($custom['geo_address'][0]) && !empty($custom['geo_address'][0]) ) :
				echo $custom['geo_address'][0];
			else :
				_e('Anywhere', APP_TD);
			endif;
			break;
		case APP_TAX_RESUME_JOB_TYPE :
		case APP_TAX_RESUME_CATEGORY :
		case APP_TAX_RESUME_SPECIALITIES :
		case APP_TAX_RESUME_LANGUAGES :
			echo get_the_term_list( $post->ID, $column, '', ', ','' );
			break;
		case 'logo':
			if (has_post_thumbnail($post->ID)) 
				echo get_the_post_thumbnail($post->ID, 'sidebar-thumbnail');
			break;
	}
}

function jr_edit_jobs_columns( $columns ){
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __('Job Name', APP_TD),
		'author' => __('Job Author', APP_TD),
		'job_cat' => __('Job Category', APP_TD),
		'job_type' => __('Job Type', APP_TD),
		'job_salary' => __('Salary', APP_TD),
		'company' => __('Company', APP_TD),
		'location' => __('Location', APP_TD),
		'expire_date' => __('Expire Date', APP_TD),
		'date' => __('Date', APP_TD),
		'logo' => __('Logo', APP_TD),
	);
	return $columns;
}

function jr_edit_resumes_columns( $columns ){

	foreach ( $columns as $key => $column ) {
		if ( 'date' == $key ) {
			$columns_reorder[APP_TAX_RESUME_JOB_TYPE] = __( 'Job Types', APP_TD );
			$columns_reorder[APP_TAX_RESUME_CATEGORY] = __( 'Job Categories', APP_TD );
			$columns_reorder[APP_TAX_RESUME_SPECIALITIES] = __( 'Job Specialties', APP_TD );
			$columns_reorder[APP_TAX_RESUME_LANGUAGES] = __( 'Spoken Languages', APP_TD );
			$columns_reorder['location'] = __( 'Location', APP_TD );
		}
		$columns_reorder[$key] = $column;
	}
	$columns_reorder['thumbnail'] = __( 'Photo', APP_TD );
	return $columns_reorder;
}

// add a thumbnail column to the edit posts screen
function jr_post_thumbnail_column( $cols ) {
	$cols['thumbnail'] = __('Thumbnail', APP_TD);
	return $cols;
}

function jr_listing_columns_sort( $columns ) {
	$columns['expire_date'] = 'expire_date';
	return $columns;
}

function jr_resumes_columns_sort( $columns ) {
	$columns[APP_TAX_RESUME_JOB_TYPE] = APP_TAX_RESUME_JOB_TYPE;
	$columns[APP_TAX_RESUME_CATEGORY] = APP_TAX_RESUME_CATEGORY;
	$columns[APP_TAX_RESUME_SPECIALITIES] = APP_TAX_RESUME_SPECIALITIES;
	$columns[APP_TAX_RESUME_LANGUAGES] = APP_TAX_RESUME_LANGUAGES;
	return $columns;
}

// add a drop-down post type selector to the edit post/ads admin pages
function jr_post_type_changer() {
	global $post;

	// disallow things like attachments, revisions, etc
	$safe_filter = array('public' => true, 'show_ui' => true);

	// allow this to be filtered
	$args = apply_filters('jr_post_type_changer', $safe_filter);

	// get the post types
	$post_types = get_post_types((array)$args);

	// get the post_type values
	$cur_post_type_object = get_post_type_object($post->post_type);

	$cur_post_type = $cur_post_type_object->name;

	// make sure the logged in user has perms
	$can_publish = current_user_can($cur_post_type_object->cap->publish_posts);
?>

<?php if ( $can_publish ) : ?>

<div class="misc-pub-section misc-pub-section-last post-type-switcher">

	<label for="pts_post_type"><?php _e('Post Type:', APP_TD); ?></label>

	<span id="post-type-display"><?php echo $cur_post_type_object->label; ?></span>

	<a href="#pts_post_type" class="edit-post-type hide-if-no-js"><?php _e('Edit', APP_TD); ?></a>
	<div id="post-type-select" class="hide-if-js">

		<select name="pts_post_type" id="pts_post_type">
            <?php foreach ( $post_types as $post_type ) {
			$pt = get_post_type_object( $post_type );

			if ( current_user_can( $pt->cap->publish_posts ) ) : ?>

				<option value="<?php echo $pt->name; ?>"<?php if ( $cur_post_type == $post_type ) : ?>selected="selected"<?php endif; ?>><?php echo $pt->label; ?></option>

			<?php
			endif;
		}
			?>
		</select>

		<input type="hidden" name="hidden_post_type" id="hidden_post_type" value="<?php echo $cur_post_type; ?>" />

		<a href="#pts_post_type" class="save-post-type hide-if-no-js button"><?php _e('OK', APP_TD); ?></a>
		<a href="#pts_post_type" class="cancel-post-type hide-if-no-js"><?php _e('Cancel', APP_TD); ?></a>
	</div>	
	
</div>

<?php
	endif;
}


// jquery and css for the post type changer
function jr_post_type_changer_head() {
?>

<script type='text/javascript'>
    jQuery(document).ready(function(){
        jQuery('#post-type-select').siblings('a.edit-post-type').click(function() {
            if (jQuery('#post-type-select').is(":hidden")) {
                jQuery('#post-type-select').slideDown("normal");
                jQuery(this).hide();
            }
            return false;
        });

        jQuery('.save-post-type', '#post-type-select').click(function() {
            jQuery('#post-type-select').slideUp("normal");
            jQuery('#post-type-select').siblings('a.edit-post-type').show();
            pts_updateText();
            return false;
        });

        jQuery('.cancel-post-type', '#post-type-select').click(function() {
            jQuery('#post-type-select').slideUp("normal");
            jQuery('#pts_post_type').val(jQuery('#hidden_post_type').val());
            jQuery('#post-type-select').siblings('a.edit-post-type').show();
            pts_updateText();
            return false;
        });

        function pts_updateText() {
            jQuery('#post-type-display').html( jQuery('#pts_post_type :selected').text() );
            jQuery('#hidden_post_type').val(jQuery('#pts_post_type').val());
            jQuery('#post_type').val(jQuery('#pts_post_type').val());
            return true;
        }
    });
</script>

<style type="text/css">
    #post-type-select { line-height: 2.5em; margin-top: 3px; }
    #post-type-display { font-weight: bold; }
    div.post-type-switcher { border-top: 1px solid #eee; }
</style>

<?php
}

// custom user page columns
function jr_manage_users_columns( $columns ) {
	$columns['jr_jobs_count'] = __('Jobs', APP_TD);
	$columns['jr_resumes_count'] = __('Resumes', APP_TD);
	$columns['jr_resume_subscription'] = __('Resume Subscription', APP_TD);
	$columns['last_login'] = __('Last Login', APP_TD);
	$columns['registered'] = __('Registered', APP_TD);
	return $columns;
}

// display the coumn values for each user
function jr_manage_users_custom_column( $r, $column_name, $user_id ) {

	// count the total jobs for the user
	if ( 'jr_jobs_count' == $column_name ) {
		global $jobs_counts;

		if ( !isset( $jobs_counts ) )
			$jobs_counts = jr_count_custom_post_types( APP_POST_TYPE );

		if ( !array_key_exists( $user_id, $jobs_counts ) )
			$jobs_counts = jr_count_custom_post_types( APP_POST_TYPE );

		if ( $jobs_counts[$user_id] > 0 ) {
			$r .= "<a href='edit.php?post_type=" . APP_POST_TYPE . "&author=$user_id' title='" . esc_attr__( 'View jobs by this author', APP_TD ) . "' class='edit'>";
			$r .= $jobs_counts[$user_id];
			$r .= '</a>';
		} else {
			$r .= 0;
		}
	}
	
	// count the total resumes for the user
	if ( 'jr_resumes_count' == $column_name ) {
		global $resumes_counts;

		if ( !isset( $resumes_counts ) )
			$resumes_counts = jr_count_custom_post_types( APP_POST_TYPE_RESUME );

		if ( !array_key_exists( $user_id, $resumes_counts ) )
			$resumes_counts = jr_count_custom_post_types( APP_POST_TYPE_RESUME );

		if ( $resumes_counts[$user_id] > 0 ) {
			$r .= "<a href='edit.php?post_type=" . APP_POST_TYPE_RESUME . "&author=$user_id' title='" . esc_attr__( 'View resumes by this author', APP_TD ) . "' class='edit'>";
			$r .= $resumes_counts[$user_id];
			$r .= '</a>';
		} else {
			$r .= 0;
		}
	}
	
	// get the user last login date
	if ('last_login' == $column_name)
		$r = get_user_meta($user_id, 'last_login', true);
	
	// get the user registration date	
	if ('registered' == $column_name) {
		$user_info = get_userdata($user_id);
		$r = $user_info->user_registered;
		//$r = appthemes_get_reg_date($reg_date);
	}
	
	if ('jr_resume_subscription' == $column_name) {
		$status = (int) get_user_meta($user_id, '_valid_resume_subscription', true);
		if ( $status > 0 ) {
			$r = __( 'Yes', APP_TD );
		} else {
			$r = '&ndash;';
		}
	}

	return $r;
}


// count the number of job listings & resumes for the user
function jr_count_custom_post_types( $post_type ) {
	global $wpdb, $wp_list_table;

	$users = array_keys( $wp_list_table->items );
	$userlist = implode( ',', $users );
	$result = $wpdb->get_results( "SELECT post_author, COUNT(*) FROM $wpdb->posts WHERE post_type = '$post_type' AND post_author IN ($userlist) GROUP BY post_author", ARRAY_N );
	foreach ( $result as $row ) {
		$count[ $row[0] ] = $row[1];
	}

	foreach ( $users as $id ) {
		if ( ! isset( $count[ $id ] ) )
			$count[ $id ] = 0;
	}

	return $count;
}

// Define icon styles for the custom post type
function jr_custom_icons() {
?>
<style type="text/css" media="screen">
	#icon-edit.icon32-posts-<?php echo APP_POST_TYPE; ?> {background: url(<?php echo get_template_directory_uri(); ?>/images/admin-icon-jobs-32.png) no-repeat;}
	#icon-edit.icon32-posts-<?php echo APP_POST_TYPE_RESUME; ?> {background: url(<?php echo get_template_directory_uri(); ?>/images/admin-icon-resumes-32.png) no-repeat;}
</style>
<?php
}
