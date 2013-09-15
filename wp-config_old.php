<?php
/** 
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information by
 * visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
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
define('DB_NAME', 'wordpress_f');

/** MySQL database username */
define('DB_USER', 'wordpress_7');

/** MySQL database password */
define('DB_PASSWORD', '918dwY!RBm');

/** MySQL hostname */
define('DB_HOST', 'localhost:3306');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link http://api.wordpress.org/secret-key/1.1/ WordPress.org secret-key service}
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'gpV@8$6506tqBtko*R%B0ind6&xy1M#w$7k)aG8%VxRgYcrtX)8s6zTYmC$KbQuT');
define('SECURE_AUTH_KEY',  'hV^$zqoTy3CG*y1uht^G$v0(13aO&osGebHXMUxW!B)bOh2m^9C3%1jc4Q&yzG89');
define('LOGGED_IN_KEY',    'kRMr6HYZcSUl2r7P&#UxOB9LFOudfl%o7(30@#zvHcrkZBqNnPE^7&W4iUJKfxgP');
define('NONCE_KEY',        'zH#s3^^IS$2TCLxfF88)!5E9IPjDBC*L)#1!48ZoevTAMjdCEkfiwICPQ2OVCD29');
define('AUTH_SALT',        '7PiBH&)53QmrPpNS0VMaZwYM$XaLXXf3*hfGZf5s)3c%wmWteTj9IZ1LDkLVKcH8');
define('SECURE_AUTH_SALT', 'A&9sW(uwm%$V(Gn6ts1^ZyLwgD*x7aaQuAD)DdrjtJ2MMNY$$d3KguzByNQxA$1O');
define('LOGGED_IN_SALT',   ')V2Xyj5N#(4AEbsNBk^M(VWbOJzA!NTC@$Wmtmi&$A%8HOxqhn2j4CwKn$0YNuAC');
define('NONCE_SALT',       '$hK$3jaIZ^n@IFzH6th^Jao8zNi4EPFs9w^O9mCSFH(#9S7oTHZdqRzTX6*h%JoS');
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
 * Change this to localize WordPress.  A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de.mo to wp-content/languages and set WPLANG to 'de' to enable German
 * language support.
 */
define ('WPLANG', 'en_US');

define ('FS_METHOD', 'direct');

define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

?>
