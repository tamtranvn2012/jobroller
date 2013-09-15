<?php

/**
 * Helper class for controlling all aspects of a view.
 *
 * Supported methods (automatically hooked):
 *  - init() - for registering post types, taxonomies, rewrite rules etc.
 *  - parse_query() - for correcting query flags
 *  - pre_get_posts() - for altering the query, without affecting the query flags
 *  - posts_search(), posts_clauses(), posts_request() - for direct SQL manipulation
 *  - the_posts() - for various other manipulations
 *  - template_redirect() - for enqueuing scripts etc.
 *  - template_include( $path ) - for loading a different template file
 *  - title_parts( $parts ) - for changing the title
 *  - breadcrumbs( $trail ) - for changing the breadcrumbs
 *  - notices() - for displaying notices
 */
abstract class APP_View {

	/**
	 * Test if this class should handle the current view.
	 *
	 * Use is_*() conditional tags and get_query_var()
	 *
	 * @return bool
	 */
	abstract function condition();


	function __construct() {
		// 'init' hook (always ran)
		if ( method_exists( $this, 'init' ) )
			add_action( 'init', array( $this, 'init' ) );

		// $wp_query hooks
		$actions = array( 'parse_query', 'pre_get_posts' );
		$filters = array( 'posts_search', 'posts_clauses', 'posts_request', 'the_posts' );

		foreach ( $actions as $method ) {
			if ( method_exists( $this, $method ) )
				add_action( $method, array( $this, '_action' ) );
		}

		foreach ( $filters as $method ) {
			if ( method_exists( $this, $method ) )
				add_filter( $method, array( $this, '_filter' ), 10, 2 );
		}

		// other hooks
		add_action( 'template_redirect', array( $this, '_template_redirect' ), 9 );
	}

	final function _action( $wp_query ) {
		if ( $wp_query->is_main_query() && $this->condition() ) {
			$method = current_filter();

//			debug( get_class( $this ) . '->' . $method . '()' );

			$this->$method( $wp_query );
		}
	}

	final function _filter( $value, $wp_query ) {
		if ( $wp_query->is_main_query() && $this->condition() ) {
			$method = current_filter();

//			debug( get_class( $this ) . '->' . $method . '()' );

			$value = $this->$method( $value, $wp_query );
		}

		return $value;
	}

	final function _template_redirect() {
		if ( !$this->condition() )
			return;

		if ( method_exists( $this, 'template_redirect' ) )
			$this->template_redirect();

		$filters = array(
			'template_include' => 'template_include',
			'appthemes_title_parts' => 'title_parts',
			'appthemes_notices' => 'notices',
			'breadcrumb_trail_items' => 'breadcrumbs',
		);

		foreach ( $filters as $filter => $method ) {
			if ( method_exists( $this, $method ) )
				add_filter( $filter, array( $this, $method ) );
		}
	}
}


/**
 * Class for handling special pages that have a specific template file.
 */
class APP_View_Page extends APP_View {

	private $template;
	private $default_title;

	// List of instances
	private static $instances = array();

	// Page ID cache
	private static $page_ids = array();

	function __construct( $template, $default_title ) {
		$this->template = $template;
		$this->default_title = $default_title;

		self::$instances[ get_class( $this ) ] = $this;

		parent::__construct();
	}

	function condition() {
		if ( is_page_template( $this->template ) )
			return true;

		$page_id = (int) get_query_var( 'page_id' );

		return $page_id && $page_id == self::_get_id( get_class( $this ) ); // for 'page_on_front'
	}

	static function _get_id( $class ) {
		$template = self::$instances[ $class ]->template;

		if ( isset( self::$page_ids[ $template ] ) )
			return self::$page_ids[ $template ];

		// don't use 'fields' => 'ids' because it skips caching
		$page_q = new WP_Query( array(
			'post_type' => 'page',
			'meta_key' => '_wp_page_template',
			'meta_value' => $template,
			'posts_per_page' => 1,
			'suppress_filters' => true
		) );

		if ( empty( $page_q->posts ) )
			$page_id = 0;
		else
			$page_id = $page_q->posts[0]->ID;

		$page_id = apply_filters( 'appthemes_page_id_for_template', $page_id, $template );

		self::$page_ids[$template] = $page_id;

		return $page_id;
	}

	static function install() {
		foreach ( self::$instances as $class => $instance ) {
			if ( self::_get_id( $class ) )
				continue;

			$page_id = wp_insert_post( array(
				'post_type' => 'page',
				'post_status' => 'publish',
				'post_title' => $instance->default_title
			) );

			// Cache will have been set to 0, so update it
			self::$page_ids[ $instance->template ] = $page_id;

			add_post_meta( $page_id, '_wp_page_template', $instance->template );
		}
	}

	static function uninstall() {
		foreach ( self::$instances as $class => $instance ) {
			$page_id = self::_get_id( $class );

			if ( !$page_id )
				continue;

			wp_delete_post( $page_id, true );

			self::$page_ids[ $instance->template ] = 0;
		}
	}
}

add_action( 'appthemes_first_run', array( 'APP_View_Page', 'install' ), 9 );

