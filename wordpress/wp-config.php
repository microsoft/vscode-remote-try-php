<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
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
define( 'DB_NAME', 'test' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',         'I{I>&<c6/.tJT]eq},=Nw*3EP?<2[Op7II(A zY7WSTJ5q+Tb`0RM8;d|mJD=Nb!' );
define( 'SECURE_AUTH_KEY',  'bJBI#3_jl &2=;6PN?5O czw|2{RZdqTs~O)1Le~:Bh`2*mFI~>J[I32$&VG|E~0' );
define( 'LOGGED_IN_KEY',    '!Es1F]g%{qr=^@??/C_DMi_gZG-j{@yzf>)8T4wgVA<S;)SmJM g6xY:EB=ej+Yd' );
define( 'NONCE_KEY',        'du$OFGBTkMS>0ww^bfL>aV0$;j08V)7anI%_e7bR5B1prRwZaWeXo!cIPQnu2fD+' );
define( 'AUTH_SALT',        '61v`})tu<@&D>)+4g]CgP<Nh8PeiE=5Lhq|&9`u^dWcRQAqqj]ICJlDC9E!_WMo,' );
define( 'SECURE_AUTH_SALT', '5pH0,`}Wqg|M9;Pvf.0#,F^blKBU^gslMkXp)3Q?.1F%w2?~ vU=ce!V<V!FQ8$)' );
define( 'LOGGED_IN_SALT',   'c76)Jgx#Fus:j?U>Gv;I4uf,vZY3*0=Df0T)Tt%JcS|Ng@O0-*Hgc;LW5BCu+WAg' );
define( 'NONCE_SALT',       'BkVvP:cEbkw198TpjKQ/[!ZZ!Y^<@h%7YH[^_FY,bs C?X?BBv)ShAUo-<_[wP+2' );

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
