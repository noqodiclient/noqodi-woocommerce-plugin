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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wpfive' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'PmEV+2R`8O0K(b7a=U(?6&_w-Bo5;^/];HP4`+CJc;LEye`dX)DxUpzWILNg,MoA' );
define( 'SECURE_AUTH_KEY',  'eKCo4CsP7So_*Z;iW[1Vnk~QX^iWt1|A-Kh^5g##^`%^o*%A?AZCb$uO5M|$SKhR' );
define( 'LOGGED_IN_KEY',    'P=rQKk H8eCx/ggq4yj([CK6Os/QF@JU=B98Q7Jn.3XS!LwQq+nJ|j)|rwg=A8(C' );
define( 'NONCE_KEY',        'U>yaoDunCC|zNSa}6}|hRXK!*lp[MIAp>=8e{J$pJ%h/;F|??hbX)T=e8ezOwGX/' );
define( 'AUTH_SALT',        ')(l`URH-bkQ$ec| *Z8e|zR@9I-odk/|kgp+XVF8STCxGctm%`nzy#Z Sl~jPxHN' );
define( 'SECURE_AUTH_SALT', '#?$O>9a7iuK14:T2Mg>[ao$kS%&LD$L[!mK5O[o.~wa;s?@~GE<ulv..?w=7y1T*' );
define( 'LOGGED_IN_SALT',   'f4G/V,j#sVT~`8~K@EI1MhS/=5j/2Yhuc3lqHE_nNp9U`6&j*V${`^f+Mo*AU]Q:' );
define( 'NONCE_SALT',       '{Q32@23u~xLiPD^l^dnqwJ:KW4hFx<`0W;J$!6EeJ93q0G$={Wl-&K)Upq#wP[rW' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
