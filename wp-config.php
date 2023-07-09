<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'classiads' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'D0NQkX!%Dp3~Zq4ozT(YdJOZ/`:@UgOv0x!|`)W_GN%;z04/9G^jU.S`N:W4{Gj}' );
define( 'SECURE_AUTH_KEY',  'YIM6%YOWG>QOH)CT{!D%&}PSfeR3WqMo61vnBb[n uBr-*iJMCjE~$Ecc>fGMQ!=' );
define( 'LOGGED_IN_KEY',    '.0T9SNdYVWy%dHs)v:`_lvMmGE1f?!p*z{4L_YR??nh!i@/K<;#%)=3Do:rR)Qvz' );
define( 'NONCE_KEY',        'pz}GrV*A; zesftyqZwm4d!KwfL~=up9Wq]>;/M!0eAjp<,wm;{]!vT}G;qvM@8j' );
define( 'AUTH_SALT',        '71`r~~QURw3~3D6}aSnRsd@tLrLzmvRRYW}&JX~[K<+35I!_)`(NlQ`K<LV!R,fu' );
define( 'SECURE_AUTH_SALT', 'yK/hF(o S$Du5pEUBA(r --XMhx&)N>i9^bP{oSisf*B/Yk12:T#*l7s)h.^1pJ-' );
define( 'LOGGED_IN_SALT',   ';|YmN=2xfSA7m{;#Oo-0na1i=}ml<E8W5>Vc/c6t8C~I,I5v+Rpp1glJs~vLK=RJ' );
define( 'NONCE_SALT',       '_R~])Q|)c3Gh!f4DZyr!kA~Aol15T/@%QUk{b~BJNKbc*3a8)_@#V3mv:z*#6};%' );

/**#@-*/

/**
 * WordPress database table prefix.
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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
