<?php
/**
 * Hold deprecated functions and hooks
 *
 * DO NOT UPDATE WITHOUT UPDATING ALL OTHER THEMES!
 * This is a shared file so changes need to be propagated to insure sync
 */



/**
 * deprecated
 *
 */
function appthemes_page_comment() {
	_deprecated_function( __FUNCTION__, '2012-11-30' );

	do_action( 'appthemes_page_comment' );
}


/**
 * deprecated
 *
 */
function appthemes_blog_comment() {
	_deprecated_function( __FUNCTION__, '2012-11-30' );

	do_action( 'appthemes_blog_comment' );
}


/**
 * deprecated
 *
 */
function appthemes_comment() {
	_deprecated_function( __FUNCTION__, '2012-11-30' );

	do_action( 'appthemes_comment' );
}


/**
 * invokes the 468x60 advertise section, called in header
 *
 * @deprecated Use appthemes_advertise_header()
 * @see appthemes_advertise_header()
 */
function appthemes_header_ad_468x60() {
	_deprecated_function( __FUNCTION__, '2012-11-30', 'appthemes_advertise_header()' );

	return appthemes_advertise_header();
}


/**
 * invokes the 336x280 advertise section, called in content
 *
 * @deprecated Use appthemes_advertise_content()
 * @see appthemes_advertise_content()
 */
function appthemes_single_ad_336x280() {
	_deprecated_function( __FUNCTION__, '2012-11-30', 'appthemes_advertise_content()' );

	return appthemes_advertise_content();
}


/**
 * determines whether multisite support is enabled
 *
 * @deprecated Use is_multisite()
 * @see is_multisite()
 */
function appthemes_is_wpmu() {
	_deprecated_function( __FUNCTION__, '2013-01-22', 'is_multisite()' );

	return is_multisite();
}


/**
 * inserts line breaks before new lines
 *
 * @deprecated Use nl2br()
 * @see nl2br()
 */
function appthemes_nl2br( $string ) {
	_deprecated_function( __FUNCTION__, '2013-01-22', 'nl2br()' );

	return nl2br( $string );
}


