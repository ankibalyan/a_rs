<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wp_roadshow');

/** MySQL database username */
define('DB_USER', 'arvindadmin');

/** MySQL database password */
define('DB_PASSWORD', 'Passarvind');

/** MySQL hostname */
define('DB_HOST', 'arvindecom.c268nehlpjb4.ap-southeast-1.rds.amazonaws.com');

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
define('AUTH_KEY',         '31s^j*xkB->Om%eg2f9j_cha*CHAG]w[a03vw;q6t)|-1/Axy&E,+*E*OSPJqS5&');
define('SECURE_AUTH_KEY',  '%w&23t)rbZ`9 Hln#hW<F.761*qHis|k)Op`_I2FUan|. ?oOu}g}=>v>l48xu=u');
define('LOGGED_IN_KEY',    'YLFOsVZ}bCQ,N^g+?p$)f3=xeIB({a^5#W@9>|Kf9Fb!_%p|_]k2dA|ytGUBi@US');
define('NONCE_KEY',        'CWJ5QC2F-`hg#C4DZP0z-mVB^xbb:si_{>j+3p$ff[_+h{QC&YqT-U6QKM0D-/Ux');
define('AUTH_SALT',        '/BZL!3Aup8*c?`vO?0fOz0 g4qJBj++3zS!La{5?i4DU4Z`v~19W2Qd<YW$}o_=|');
define('SECURE_AUTH_SALT', 's[>H#5mO)K0f>L[r(I[9f:FKI_]}renWSB#x|%$(Uj?gF-=h{z+=zl4=v%*T[s&Z');
define('LOGGED_IN_SALT',   '6-B^+`}=lwnN,<@zUk >Y@DFy~x_908(-9sFHva#K(F${y.>NCQiJE-G#S%bv0G^');
define('NONCE_SALT',       'y_)sesqh+[q[z!sCM]EP[`WNxSz_B-,-.UD8?y3p?XaG.aL&<Ams#&yutks/g!A5');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
//define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG', FALSE);
define('WP_DEBUG_LOG', true);
define('SCRIPT_DEBUG', true);
define('SAVEQUERIES', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

//define( 'WP_CACHE', true );

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
