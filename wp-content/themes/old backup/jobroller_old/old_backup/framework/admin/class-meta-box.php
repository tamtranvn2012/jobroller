<?php
/**
 * Extend from APP_Meta_Box if you need to maintain backwards compatibility with older
 * AppThemes products.
 *
 * Otherwise, extend directly from scbPostMetabox.
 */
class APP_Meta_Box extends scbPostMetabox {

	public function __construct( $id, $title, $post_types = 'post', $context = 'advanced', $priority = 'default' ) {
		parent::__construct( $id, $title, array(
			'post_type' => $post_types,
			'context' => $context,
			'priority' => $priority
		) );
	}

	public function form_fields() {
		return $this->form();
	}

	public function form() {
		return array();
	}
}

