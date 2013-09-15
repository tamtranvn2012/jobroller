<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'jobs');

/** MySQL database username */
define('DB_USER', 'jobs');

/** MySQL database password */
define('DB_PASSWORD', 'h5f9p5h4');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '7@fO )IYg[iv}N-1zWk|[~WX @Z3H~/H;3*?aGS4Omx __%zi-f8T2#*WZefQW{&');
define('SECURE_AUTH_KEY',  'UJ0$bG:wbmG*TbQZtR{jl1Cx8.Vdih.C1;Pc5BO@QBFkqsv_JH+#nk`q6W^+QJ1Q');
define('LOGGED_IN_KEY',    '`|uey_E@Hnw}QN|up?/AM|qX#}T)VU5Y`{Tx,vQZ.`<>&fqcp[%9!$-!f|Kt14 ?');
define('NONCE_KEY',        'a<G1S$-q=g?[|ZN]5&T>=YHSVm!&}`[}sNOTTb:Xj}M-^M`zKY$zld;8E,U+:UT(');
define('AUTH_SALT',        'D@k.R_?mOM>=|`>-ad?{(R+-07Li<vjn+ehHZXLyui=V%B+-8t.?k_HjZk#ZE{`(');
define('SECURE_AUTH_SALT', 'zCv|(tACL:*d%ihumO@>~P5pDT]P@,kDWhNWHF_*-,woe]XR#vPIP(dB7iFNMG|F');
define('LOGGED_IN_SALT',   'yt$,E|_aq#`?.:++Ic84?Iv##rx3yipN|b,+c^j-rm7l? Y-x%=n/1bDxvvRv!c`');
define('NONCE_SALT',       ';5/G3.1+Q&:ZTOEG97^TH<+LyNp{.;k1/v1QoZt#<S}ftY#`9~F)(Rq+2~vFlg5S');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
