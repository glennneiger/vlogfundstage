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
define('DB_NAME', 'vlogfundDB6q6wt');

/** MySQL database username */
define('DB_USER', 'vlogfundDB6q6wt');

/** MySQL database password */
define('DB_PASSWORD', 'oUgduATTl1');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');

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
define('AUTH_KEY',         'Ns>}VzCh[o1Z!Go1Z_h[S-Dl;S+a_Lt6e#LtT+DLu6fIqb.Iq3bAj{U$Fnv7g>Q');
define('SECURE_AUTH_KEY',  '>Bc,Nr4cBk}V@Fo0v8g[1Z!K[S-Cl:W~d#Kt5e#OxW*Hp2e<L]T+EmbA+Dm;T+Em');
define('LOGGED_IN_KEY',    'l_Dl:S-D_Lt2e#LT+a.Lu6e<PX*Iq3b,j{QyBjY7f>QzBk}r4ck>RzCkNs4d|Ow');
define('NONCE_KEY',        ':[Os1Z8l:SZ_KtS-Dl;W*H#Pt6e.LxA*Emb.Iu3yAj{Uy^IrQzBj}U@BkJr4c|Nv');
define('AUTH_SALT',        'IBFj}U3c,Jr4g|N}V@Go0Z!g[O-8dww:KHde#Lt6+9m]T+Dm;T2b.Lu6f<mX^Ir3');
define('SECURE_AUTH_SALT', '|v0V@Go:V~d|:S-Dl:W5d#Ox9i]P;W*Hq2a.H<PyAi{EbbXrr,FYcz3Qro>Fg@@4R');
define('LOGGED_IN_SALT',   'xt;S+Dm]t2ei{T+E.Lu6f<QyX^Inb,IrQzBj}U@F,Nv8g>}V@g|Nw8h[S1Z_Ks5d');
define('NONCE_SALT',       'Crv4zBk}V@FoNs4d[N-C!Gs1Z_Ks5-Dl:W~HO;W*Hm;W*H<PxAi{PyA*Iq3b,MuU$');

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
define('WP_DEBUG', false);define('FS_METHOD', 'direct');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
