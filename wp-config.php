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
define('DB_NAME', 'wordpress');

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
define('AUTH_KEY',         '2tNaSW<QzL)y6>H0/{IB2vp<~Ckv_D.!5.4TC.zlY|hD,Msj{ *JU^K:.C^3g-Ih');
define('SECURE_AUTH_KEY',  '=%@ J+`TM~*;v,>)*PiDn4?hswyD#r<D}`&@ux48<GPs_(LTN&w@3*mE)au#xtiO');
define('LOGGED_IN_KEY',    'E]9tuBJ+WumKqymo~Il>4G2MwM=?@.~WzoOQf=Ss{ywemiDvG[.c6~,d%QBsyw^Z');
define('NONCE_KEY',        'hR~1o{uVq|]X]go_IP>u(d|r6f`,jue;zov6xM/An~OwIMMc-VKF3j?>*>uDD5J4');
define('AUTH_SALT',        'vF<Oc=W9My~GOu-sXYeyk_H#9)WOh %y3mk+P(oNd|3!y7wFLK!c*()8fnv?jOo&');
define('SECURE_AUTH_SALT', 'RurfWg>s}pr}Y#XLh_sMSSs5PTnj}SZ0U5yblMx8uV6rZ5;=o(Oa>%,Eg6O O-[A');
define('LOGGED_IN_SALT',   'x@P>XORN1gX|QL zQi)F[$hzx;M;Qbt-UTO dpN)!`21pAa5o.+T #T*%G~+12 )');
define('NONCE_SALT',       '&Pr6J/Bb+#0BhqW:;*h>w*sTOj.sR?0mHFD:h0LVuy8r/ADQy1k<Oi0_1YnH5~ym');

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
