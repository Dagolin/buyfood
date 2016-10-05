<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'buyfood');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'u ,mk&,Yam}CppqQC^%U$Y,},P2n&sP|?+nOA&nb..0/dSA+Sl,?Pk=S@5g>?CEf');
define('SECURE_AUTH_KEY',  'z8I5;F1GQYq%^@{J+b-CNL-rxnH2%^w{I^VDZm<{X+d0_uvqVnE4j#A/o=&-(xl<');
define('LOGGED_IN_KEY',    '</I!lq9+l-:(pWpj+;Iw7|,7oWS$wE?4} R>xxe># L!+paYiPP2#nlY3JTsg(!M');
define('NONCE_KEY',        't5mlzVi=?fPu86rPK9{Su2ZqBYxjE^1@Fat5a/<`8@&>mXDf.U,@Xfd,N|<u>kU)');
define('AUTH_SALT',        '4*&7xM*gLbgVV313i=yFV.E;tZE[*.^k.FZtw`xX(Xwp>$CZP{!lT+@DxH4=_v<R');
define('SECURE_AUTH_SALT', 'ax058[w;y5}c?P* D%|)C2]kgluuVv|5hXG>F-iX#u$zD5}1hwN6<A,(9j;umF@`');
define('LOGGED_IN_SALT',   '`# AiEUq%)G51F:}E*ulaiGWO}Zwq,AM?0{Pn9F[`YY*5:6%O}wjy*YcUxpVIve$');
define('NONCE_SALT',       '%-*peA#nEA4F }_J:~_<$:C-kR70%GtY<9q`fVjlxG,-TA;`Q{i+L&V$tkxK+U3n');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
