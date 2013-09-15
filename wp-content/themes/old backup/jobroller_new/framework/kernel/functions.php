<?php

/**
 * Loads the appropriate .mo file from a pre-defined location
 *
 * Done in load_theme_textdomain() since WP 3.5: http://core.trac.wordpress.org/changeset/22346
 */
function appthemes_load_textdomain() {
	$locale = apply_filters( 'theme_locale', get_locale(), APP_TD );

	$base = basename( get_template_directory() );

	load_textdomain( APP_TD, WP_LANG_DIR . "/themes/$base-$locale.mo" );
}

/**
 * A version of load_template() with support for passing arbitrary values.
 *
 * @param string|array Template name(s) to pass to locate_template()
 * @param array Additional data
 */
function appthemes_load_template( $templates, $data = array() ) {
	$located = locate_template( $templates );

	if ( ! $located ) {
		$framework_parent_path = rtrim( APP_FRAMEWORK_DIR, APP_FRAMEWORK_DIR_NAME );
		foreach ( (array) $templates as $template_name ) {
			if ( empty( $template_name ) )
				continue;
			if ( file_exists( $framework_parent_path . $template_name ) ) {
				$located = $framework_parent_path . $template_name;
				break;
			}
		}
	}

	if ( ! $located )
		return;

	global $posts, $post, $wp_query, $wp_rewrite, $wpdb, $comment;

	extract( $data, EXTR_SKIP );

	if ( is_array( $wp_query->query_vars ) )
		extract( $wp_query->query_vars, EXTR_SKIP );

	require $located;
}

/**
 * Checks if a user is logged in, if not redirect them to the login page.
 */
function appthemes_auth_redirect_login() {
	if ( !is_user_logged_in() ) {
		nocache_headers();
		wp_redirect( wp_login_url( scbUtil::get_current_url() ) );
		exit();
	}
}

/**
 * Sets the favicon to the default location.
 */
function appthemes_favicon() {
	$uri = appthemes_locate_template_uri( 'images/favicon.ico' );

	if ( !$uri )
		return;

?>
<link rel="shortcut icon" href="<?php echo $uri; ?>" />
<?php
}

/**
 * Generates a better title tag than wp_title().
 */
function appthemes_title_tag( $title ) {
	global $page, $paged;

	$parts = array();

	if ( !empty( $title ) )
		$parts[] = $title;

	if ( is_home() || is_front_page() ) {
		$blog_title = get_bloginfo( 'name' );

		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && !is_paged() )
			$blog_title .= ' - ' . $site_description;

		$parts[] = $blog_title;
	}

	if ( !is_404() && ( $paged >= 2 || $page >= 2 ) )
		$parts[] = sprintf( __( 'Page %s', APP_TD ), max( $paged, $page ) );

	$parts = apply_filters( 'appthemes_title_parts', $parts );

	return implode( " - ", $parts );
}

/**
 * Generates a login form that goes in the admin bar.
 */
function appthemes_admin_bar_login_form( $wp_admin_bar ) {
	if ( is_user_logged_in() )
		return;

	$form = wp_login_form( array(
		'form_id' => 'adminloginform',
		'echo' => false,
		'value_remember' => true
	) );

	$wp_admin_bar->add_menu( array(
		'id'     => 'login',
		'title'  => $form,
	) );

	$wp_admin_bar->add_menu( array(
		'id'     => 'lostpassword',
		'title'  => __( 'Lost password?', APP_TD ),
		'href' => wp_lostpassword_url()
	) );

	if ( get_option( 'users_can_register' ) ) {
		$wp_admin_bar->add_menu( array(
			'id'     => 'register',
			'title'  => __( 'Register', APP_TD ),
			'href' => site_url( 'wp-login.php?action=register', 'login' )
		) );
	}
}

/**
 * Generates pagination links.
 */
function appthemes_pagenavi( $wp_query = null, $query_var = 'paged', $args = array() ) {
	if ( is_null( $wp_query ) )
		$wp_query = $GLOBALS['wp_query'];

	if ( is_object( $wp_query ) ) {
		$params = array(
			'total' => $wp_query->max_num_pages,
			'current' => $wp_query->get( $query_var )
		);
	} else {
		$params = $wp_query;
	}

	$big = 999999999;
	$base = str_replace( $big, '%#%', get_pagenum_link( $big ) );
	$paginate_links = '';

	$default_args = array(
		'base' => $base,
		'format' => '?' . $query_var . '=%#%',
		'current' => max( 1, $params['current'] ),
		'total' => $params['total'],
		'echo' => true,
		'pages_text' => false,
	);
	$args = wp_parse_args( $args, $default_args );

	$args = apply_filters( 'appthemes_pagenavi_args', $args );

	if ( $args['total'] < 2 )
		return false;

	if ( $args['pages_text'] )
		$paginate_links .= '<span class="total">' . sprintf( __( 'Page %s of %s', APP_TD ), $args['current'], $args['total'] ) . '</span>';

	$paginate_links .= paginate_links( $args );

	if ( $args['echo'] )
		echo $paginate_links;
	else
		return $paginate_links;
}

/**
 * Generates and prints pagination links.
 *
 * @param string $before HTML code to be added before output
 * @param string $after HTML code to be added after output
 */
function appthemes_pagination( $before = '', $after = '' ) {
	if ( is_single() )
		return;

	$args = array(
		'echo' => false,
		'pages_text' => true,
		'prev_text' => '&lsaquo;&lsaquo;',
		'next_text' => '&rsaquo;&rsaquo;',
	);

	$paginate_links = appthemes_pagenavi( null, 'paged', $args );

	if ( $paginate_links ) {
		echo $before . '<div class="paging"><div class="pages">';
		echo $paginate_links;
		echo '</div><div class="clr"></div></div>' . $after;
	}
}

/**
 * See http://core.trac.wordpress.org/attachment/ticket/18302/18302.2.2.patch
 */
function appthemes_locate_template_uri( $template_names ) {
	$located = '';
	foreach ( (array) $template_names as $template_name ) {
		if ( !$template_name )
			continue;
		if ( file_exists(get_stylesheet_directory() . '/' . $template_name)) {
			$located = get_stylesheet_directory_uri() . '/' . $template_name;
			break;
		} else if ( file_exists(get_template_directory() . '/' . $template_name) ) {
			$located = get_template_directory_uri() . '/' . $template_name;
			break;
		}
	}

	return $located;
}

/**
 * Simple wrapper for adding straight rewrite rules,
 * but with the matched rule as an associative array.
 *
 * @see http://core.trac.wordpress.org/ticket/16840
 *
 * @param string $regex The rewrite regex
 * @param array $args The mapped args
 * @param string $position Where to stick this rule in the rules array. Can be 'top' or 'bottom'
 */
function appthemes_add_rewrite_rule( $regex, $args, $position = 'top' ) {
	add_rewrite_rule( $regex, add_query_arg( $args, 'index.php' ), $position );
}

/**
 * Utility to create an auto-draft post, to be used on front-end forms.
 *
 * @param string $post_type
 * @return object
 */
function appthemes_get_draft_post( $post_type ) {
	$key = 'draft_' . $post_type . '_id';

	$draft_post_id = (int) get_user_option( $key );

	if ( $draft_post_id ) {
		$draft = get_post( $draft_post_id );

		if ( !empty( $draft ) && $draft->post_status == 'auto-draft' )
			return $draft;
	}

	require_once ABSPATH . '/wp-admin/includes/post.php';

	$draft = get_default_post_to_edit( $post_type, true );

	update_user_option( get_current_user_id(), $key, $draft->ID );

	return $draft;
}

function appthemes_display_notice( $class, $msg ) {
?>
	<div class="notice <?php echo esc_attr( $class ); ?>">
		<span><?php echo $msg; ?></span>
	</div>
<?php
}

/**
 * Create categories list.
 *
 * @param array $args
 * @param array $terms_args
 *
 * @return string
 */
function appthemes_categories_list( $args, $terms_args = array() ) {

	$defaults = array(
		'menu_cols' => 2,
		'menu_depth' => 3,
		'menu_sub_num' => 3,
		'cat_parent_count' => false,
		'cat_child_count' => false,
		'cat_hide_empty' => false,
		'cat_nocatstext' => true,
		'taxonomy' => 'category',
	);

	$options = wp_parse_args( (array)$args, $defaults );

	$terms_defaults = array(
		'hide_empty' => false,
		'hierarchical' => true,
		'pad_counts' => true,
		'show_count' => true,
		'orderby' => 'name',
		'order' => 'ASC',
	);

	$terms_args = wp_parse_args( (array)$terms_args, $terms_defaults );

	// get all terms for the taxonomy
	$terms = get_terms( $options['taxonomy'], $terms_args );
	$cats = array();
	$subcats = array();
	$cat_menu = '';

	if ( !empty( $terms ) ) {
		// separate into cats and subcats arrays
		foreach ( $terms as $key => $value ) {
			if ( $value->parent == 0 )
				$cats[$key] = $terms[$key];
			else
				$subcats[$key] = $terms[$key];
			unset( $terms[$key] );
		}

		$i = 0;
		$cat_cols = $options['menu_cols']; // menu columns
		$total_main_cats = count( $cats ); // total number of parent cats
		$cats_per_col = ceil( $total_main_cats / $cat_cols ); // parent cats per column

		// loop through all the cats
		foreach ( $cats as $cat ) :

			if ( ( $i == 0 ) || ( $i == $cats_per_col ) || ( $i == ( $cats_per_col * 2 ) ) || ( $i == ( $cats_per_col * 3 ) ) ) {
				if ( $i == 0 ) $first = ' first'; else $first = '';
				$cat_menu .= '<div class="catcol '. $first .'">';
				$cat_menu .= '<ul class="maincat-list">';
			}

		// only show the total count if option is set
		$show_count = $options['cat_parent_count'] ? '('. $cat->count .')' : '';

		$cat_menu .= '<li class="maincat cat-item-'. $cat->term_id .'"><a href="'. get_term_link( $cat, $options['taxonomy'] ) .'" title="'. esc_attr( $cat->description ) .'">'. $cat->name .'</a> '.$show_count.' ';
		if ( $options['menu_sub_num'] > 0 ) {
			// create child tree
			$temp_menu = appthemes_create_child_list( $subcats, $options['taxonomy'], $cat->term_id, 0, $options['menu_depth'], $options['menu_sub_num'], $options['cat_child_count'], $options['cat_hide_empty'] );
			if ( $temp_menu )
				$cat_menu .= $temp_menu;
			if ( !$temp_menu && !$options['cat_nocatstext'] )
				$cat_menu .= '<ul class="subcat-list"><li class="cat-item">'.__( 'No categories', APP_TD ).'</li></ul>';
		}
		$cat_menu .= '</li>';

		if ( ( $i == ( $cats_per_col - 1 ) ) || ( $i == ( ( $cats_per_col * 2 ) - 1 ) ) || ( $i == ( ( $cats_per_col * 3 ) - 1 ) ) || ( $i == ( $total_main_cats - 1 ) ) ) {
			$cat_menu .= '</ul>';
			$cat_menu .= '</div><!-- /catcol -->';
		}
		$i++;

		endforeach;

	}

	return $cat_menu;

}


/**
 * Creates child list, helper function for appthemes_categories_list().
 *
 * @param array $subcats
 * @param string $taxonomy
 * @param int $parent
 * @param int $curr_depth
 * @param int $max_depth
 * @param int $max_subcats
 * @param bool $child_count
 * @param bool $hide_empty
 *
 * @return string|bool
 */
function appthemes_create_child_list( $subcats = array(), $taxonomy = 'category', $parent = 0, $curr_depth = 0, $max_depth = 3, $max_subcats = 3, $child_count = true , $hide_empty = false ) {
	$child_menu = '';
	$curr_subcats = 0;

	// limit depth of subcategories
	if ( $curr_depth >= $max_depth )
		return false;
	$curr_depth++;

	foreach ( $subcats as $subcat ) {
		if ( $subcat->parent == $parent ) {
			// hide empty sub cats if option is set
			if ( $hide_empty && $subcat->count == 0 )
				continue;
			// limit quantity of subcategories
			if ( $curr_subcats >= $max_subcats )
				continue;
			$curr_subcats++;

			// only show the total count if option is set
			$show_count = $child_count ? '<span class="cat-item-count">('. $subcat->count .')</span>' : '';

			$child_menu .= '<li class="cat-item cat-item-'. $subcat->term_id .'"><a href="'. get_term_link( $subcat, $taxonomy ) .'" title="'. esc_attr( $subcat->description ) .'">'. $subcat->name .'</a> '.$show_count.' ';
			$temp_menu = appthemes_create_child_list( $subcats, $taxonomy, $subcat->term_id, $curr_depth, $max_depth, $max_subcats, $child_count, $hide_empty );
			if ( $temp_menu )
				$child_menu .= $temp_menu;
			$child_menu .= '</li>';

		}
	}

	if ( !empty( $child_menu ) )
		return '<ul class="subcat-list">' . $child_menu . '</ul>';
	else
		return false;
}

/**
 * Insert a term if it doesn't already exist
 *
 * @param string $name The term name
 * @param string $tax The taxonomy
 *
 * @return int/WP_Error The term id
 */
function appthemes_maybe_insert_term( $name, $tax ) {
	$term_id = term_exists( $name, $tax );
	if ( !$term_id )
		$term_id = wp_insert_term( $name, $tax );

	return $term_id;
}

/**
 * Returns term data specified in arguments
 *
 * @param int $post_id Post ID
 * @param string $taxonomy The taxonomy
 * @param string $tax_arg The term data to retrieve
 *
 * @return string|bool The term data specified by $tax_arg or bool false if post has no terms
 */
function appthemes_get_custom_taxonomy( $post_id, $taxonomy, $tax_arg ) {
	$tax_array = get_terms( $taxonomy, array( 'hide_empty' => '0' ) );

	if ( empty( $tax_array ) )
		return false;

	if ( ! is_object_in_term( $post_id, $taxonomy ) )
		return false;

	foreach ( $tax_array as $tax_val ) {
		if ( ! is_object_in_term( $post_id, $taxonomy, array( $tax_val->term_id ) ) )
			continue;

		switch ( $tax_arg ) {
			case 'slug':
				$link = get_term_link($tax_val, $taxonomy);
				return $link;
				break;
			case 'slug_name':
				return $tax_val->slug;
				break;
			case 'name':
				return $tax_val->name;
				break;
			case 'term_id':
				return $tax_val->term_id;
				break;
			default:
				return false;
				break;
		}
	}

}

/**
 * Prints random terms for specified taxonomy
 *
 * @param string $taxonomy The taxonomy
 * @param int $limit The limit of results
 */
function appthemes_get_rand_taxonomy( $taxonomy, $limit ) {
	global $wpdb;

	$sql = "SELECT t.name, t.slug FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = %s AND tt.count > 0 ORDER BY RAND() LIMIT %d";
	$tax_array = $wpdb->get_results( $wpdb->prepare( $sql, $taxonomy, $limit ) );

	if ( empty( $tax_array ) )
		return;

	foreach ( $tax_array as $tax_val ) {
		$link = get_term_link( $tax_val->slug, $taxonomy );
		echo '<a class="tax-link" href="' . $link . '">' . $tax_val->name . '</a>';
	}

}

/**
 * Prints most popular terms for specified taxonomy
 *
 * @param string $taxonomy The taxonomy
 * @param int $limit The limit of results
 */
function appthemes_get_pop_taxonomy( $taxonomy, $limit ) {
	global $wpdb;

	$sql = "SELECT t.name, t.slug, tt.count FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = %s AND tt.count > 0 GROUP BY tt.count DESC ORDER BY RAND() LIMIT %d";
	$tax_array = $wpdb->get_results( $wpdb->prepare( $sql, $taxonomy, $limit ) );

	if ( empty( $tax_array ) )
		return;

	foreach ( $tax_array as $tax_val ) {
		$link = get_term_link( $tax_val->slug, $taxonomy );
		echo '<a class="tax-link" href="' . $link . '">' . $tax_val->name . '</a>';
	}

}

/**
 * Returns terms list for specified taxonomy
 *
 * @param int $id The term ID
 * @param string $taxonomy The taxonomy
 * @param string $before HTML code to be added before list
 * @param string $sep Separator between terms
 * @param string $after HTML code to be added after list
 * @return string|bool|WP_Error Formatted tems list, false if no terms, WP_Error if taxonomy does not exist
 */
function appthemes_get_all_taxonomy( $id = 0, $taxonomy, $before = '', $sep = '', $after = '' ) {
	$terms = get_the_terms( $id, $taxonomy );

	if ( is_wp_error( $terms ) )
		return $terms;

	if ( empty( $terms ) )
		return false;

	foreach ( $terms as $term ) {
		$link = get_term_link( $term, $taxonomy );
		if ( is_wp_error( $link ) )
			return $link;
		$term_links[] = $term->name;
	}

	$term_links = apply_filters( "term_links-$taxonomy", $term_links );

	return $before . join( $sep, $term_links ) . $after;
}

function appthemes_get_registration_url( $context = 'display' ) {
	if ( current_theme_supports( 'app-login' ) && ( $page_id = APP_Registration::get_id() ) ) {
		$url = get_permalink( $page_id );
	} else {
		$url = site_url( 'wp-login.php?action=register' );
	}

	return esc_url( $url, null, $context );
}

function appthemes_get_password_recovery_url( $context = 'display' ) {
	if ( current_theme_supports( 'app-login' ) && ( $page_id = APP_Password_Recovery::get_id() ) ) {
		$url = get_permalink( $page_id );
	} else {
		$url = site_url( 'wp-login.php' );
	}

	if ( !empty($_GET['action']) && empty($_GET['key']) ) {
		$url = add_query_arg( 'action', $_GET['action'], $url );
	}

	return esc_url( $url, null, $context );
}

function appthemes_framework_image( $name ) {
	return APP_FRAMEWORK_URI . '/images/' . $name;
}

/**
 * Includes custom post types into main feed, hook to 'request' filter
 *
 * @param array $query_vars
 *
 * @return array
 */
function appthemes_modify_feed_content( $query_vars ) {

	if ( !current_theme_supports( 'app-feed' ) )
		return $query_vars;

	list( $options ) = get_theme_support( 'app-feed' );

	if ( isset($query_vars['feed']) && !isset($query_vars['post_type']) )
		$query_vars['post_type'] = array( 'post', $options['post_type'] );

	return $query_vars;
}

/**
 * Return feed url related to currently browsed page
 *
 * @return string
 */
function appthemes_get_feed_url() {

	if ( !current_theme_supports( 'app-feed' ) )
		return get_bloginfo_rss('rss2_url');

	list( $options ) = get_theme_support( 'app-feed' );

	if ( _appthemes_is_post_page( $options['blog_template'] ) )
		return add_query_arg( 'post_type', 'post', get_bloginfo_rss('rss2_url') );

	if ( empty($options['alternate_feed_url']) )
		return add_query_arg( 'post_type', $options['post_type'], get_bloginfo_rss('rss2_url') );

	return $options['alternate_feed_url'];
}

function _appthemes_is_post_page( $blog_template ) {
	if ( is_singular('post') || is_category() || is_tag() )
		return true;

	if ( is_page_template( $blog_template ) )
		return true;

	if ( get_queried_object_id() == get_option('page_for_posts') && in_array( $blog_template, array( 'home.php', 'index.php' ) ) )
		return true;

	return false;
}

function appthemes_absfloat( $maybefloat ){
	return abs( floatval( $maybefloat ) );
}

/**
 * Preserve a REQUEST variable by generating a hidden input for it
 */
function appthemes_pass_request_var( $keys ) {
	foreach ( (array) $keys as $key ) {
		if ( isset( $_REQUEST[ $key ] ) )
			_appthemes_form_serialize( $_REQUEST[ $key ], array( $key ) );
	}
}

function _appthemes_form_serialize( $data, $name ) {
	if ( !is_array( $data ) ) {
		echo html( 'input', array(
			'type' => 'hidden',
			'name' => scbForms::get_name( $name ),
			'value' => $data
		) ) . "\n";
		return;
	}

	foreach ( $data as $key => $value ) {
		_appthemes_form_serialize( $value, array_merge( $name, array( $key ) ) );
	}

}

/**
 * Check state of app-plupload
 *
 * @return bool
 */
function appthemes_plupload_is_enabled() {
	if ( isset( $_REQUEST['app-plupload'] ) && $_REQUEST['app-plupload'] == 'disable' )
		return false;

	if ( !current_theme_supports( 'app-plupload' ) )
		return false;

	return true;
}

/**
 * Sends email with standardized headers
 *
 */
function appthemes_send_email( $address, $subject, $content ){

	// Strip 'www.' from URL
	$domain = preg_replace( '#^www\.#', '', strtolower( $_SERVER['SERVER_NAME'] ) );

	$headers = array(
		'from' => sprintf( 'From: %1$s <%2$s', get_bloginfo( 'name' ), "wordpress@$domain" ),
		'mime' => 'MIME-Version: 1.0',
		'type' => 'Content-Type: text/html; charset="' . get_bloginfo( 'charset' ) . '"',
		'reply_to' => "Reply-To: noreply@$domain",
	);

	ob_start();
	appthemes_load_template( APP_FRAMEWORK_DIR_NAME . '/templates/email-template.php', array( 'address' => $address, 'subject' => $subject, 'content' => $content ) );
	$body = ob_get_clean();

	wp_mail( $address, $subject, $body, implode( "\n", $headers ) );

}

/**
 * Creates URL to Facebook profile by ID or username
 *
 * @param int|string A Facebook user id, a username or a full URL
 * @return string A full Facebook URL
 */
function appthemes_make_fb_profile_url( $id, $context = 'display' ) {

	$base_url = 'http://www.facebook.com/';

	if ( empty( $id ) ) {
		$url = $base_url;
	} elseif ( is_numeric( $id ) ) {
		$base_url = $base_url . 'profile.php';
		$url = add_query_arg( array( 'id' => $id ), $base_url );
	} elseif ( preg_match( '/^(http|https):\/\/(.*?)$/i', $id ) ) {
		$url = $id;
	} else {
		$url = $base_url . $id;
	}

	return esc_url( $url, null, $context );
}

/**
 * Checks whether string begins with given string
 *
 * @param string $string String to search in
 * @param string $search String to search for
 * @return bool
 */
function appthemes_str_starts_with( $string, $search ) {
	return ( strncmp( $string, $search, strlen( $search ) ) == 0 );
}

/**
 * Strips out everything except numbers
 *
 * @param string
 * @return string
 */
function appthemes_numbers_only( $string ) {
	$string = preg_replace( '/[^0-9]/', '', $string );
	return $string;
}

/**
 * Strips out everything except letters
 *
 * @param string
 * @return string
 */
function appthemes_letters_only( $string ) {
	$string = preg_replace( '/[^a-z]/i', '', $string );
	return $string;
}

/**
 * Strips out everything except numbers and letters
 *
 * @param string
 * @return string
 */
function appthemes_numbers_letters_only( $string ) {
	$string = preg_replace( '/[^a-z0-9]/i', '', $string );
	return $string;
}

/**
 * Cleanes string from slashes and whitespaces
 *
 * @param string
 * @return string
 */
function appthemes_clean( $string ) {
	$string = stripslashes( $string );
	$string = trim( $string );
	return $string;
}

/**
 * Removes any invalid characters from tags
 *
 * @param string
 * @return string
 */
function appthemes_clean_tags( $string ) {
	$string = preg_replace( '/\s*,\s*/', ',', rtrim( trim( $string ), ' ,' ) );
	return $string;
}

/**
 * Strips tags and limit characters to 5,000
 *
 * @param string
 * @return string
 */
function appthemes_filter( $string ) {
	$string = strip_tags( $string );
	$string = trim( $string );
	$char_limit = 5000;
	if ( strlen( $string ) > $char_limit )
		$string = substr( $string, 0, $char_limit );

	return $string;
}

/**
 * Returns extension of passed file name
 *
 * @param string A file name
 * @return string Extension of file
 */
function appthemes_find_ext( $filename ) {
	$filename = strtolower( $filename );
	$exts = split( "[/\\.]", $filename );
	$n = count( $exts ) - 1;
	$exts = $exts[ $n ];
	return $exts;
}

/**
 * Returns visitor IP address
 *
 * @return string
 */
function appthemes_get_ip() {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$ip = $_SERVER['HTTP_CLIENT_IP']; // ip from share internet
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR']; // ip from proxy
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

/**
 * Returns total count of posts based on post type and status
 *
 * @param string $post_type Post type
 * @param string|array $post_status Post status
 * @return int Total count of posts
 */
function appthemes_count_posts( $post_type = 'post', $post_status = 'publish' ) {
	$count_total = 0;
	$count_posts = wp_count_posts( $post_type );
	foreach ( (array) $post_status as $status )
		$count_total += $count_posts->$status;

	return (int) $count_total;
}

/**
 * Returns translated date in format specified by user in WP options.
 *
 * @param string|int $date_time Date in standarized format or unix timestamp
 * @param string $format Date parts to return, date with time, date, or just time
 * @return string Localized date
 */
function appthemes_display_date( $date_time, $format = 'datetime' ) {
	if ( is_string( $date_time ) )
		$date_time = strtotime( $date_time );

	if ( $format == 'date' ) {
		$date_format = get_option( 'date_format' );
	} elseif ( $format == 'time' ) {
		$date_format = get_option( 'time_format' );
	} else {
		$date_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
	}

	return date_i18n( $date_format, $date_time );
}

/**
 * Prints date of post or time ago if less than 24h, use in loop
 *
 * @param string Date in standarized format
 * @return string Localized date or time ago text
 */
function appthemes_date_posted( $date ) {
	$time = get_post_time( 'G', true );
	$time_diff = time() - $time;

	if ( $time_diff > 0 && $time_diff < 24*60*60 )
		printf( __( '%s ago', APP_TD ), human_time_diff( $time ) );
	else
		echo mysql2date( get_option('date_format'), $date );

}

/**
 * Convert date to mysql date format, to add/remove days from date use second parameter.
 *
 * @param string $date Date in standarized format
 * @param int $days Days to add or remove
 * @return string Date in mysql format
 */
function appthemes_mysql_date( $date, $days = 0 ) {
	$seconds = 60 * 60 * 24 * $days;
	$unix_time = strtotime( $date ) + $seconds;
	$mysqldate = date( 'Y-m-d H:i:s', $unix_time );

	return $mysqldate;
}

/**
 * Convert seconds to quantity of days.
 *
 * @param int Quantity of seconds
 * @return float Quantity of days
 */
function appthemes_seconds_to_days( $seconds ) {
	$days = $seconds / 24 / 60 / 60;
	return $days;
}

/**
 * Count days between passed dates.
 *
 * @param string A date for compare
 * @param string A date for compare
 * @param int Precision of results
 * @return float|bool Quantity of days or false if passed incorrect dates
 */
function appthemes_days_between_dates( $date1, $date2 = '', $precision = 1 ) {
	if ( empty( $date2 ) )
		$date2 = current_time('mysql');

	if ( ! is_string( $date1 ) || ! is_string( $date2 ) )
		return false;

	$date1 = strtotime( $date1 );
	$date2 = strtotime( $date2 );

	$days = round( appthemes_seconds_to_days( $date1 - $date2 ), $precision );
	return $days;
}

/**
 * Convert plaintext URI to HTML links.
 *
 * @param string $text Content to convert URIs
 * @return string Content with converted URIs
 */
function appthemes_make_clickable( $text ) {
	$text = make_clickable( $text );
	// open links in new window
	$text = preg_replace( '/(<a href=[\'|\"](http|https|ftp)[^<>]+)>/is', '\\1 target="_blank">', $text );
	return $text;
}

