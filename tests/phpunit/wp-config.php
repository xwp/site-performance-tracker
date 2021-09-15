<?php
/**
 * WP config file used during the unit tests.
 *
 * @package XWP\Site_Performance_Tracker
 */

// Use our local instance of WP core.
define( 'ABSPATH', dirname( dirname( __DIR__ ) ) . '/wordpress/' );

define( 'WP_DEFAULT_THEME', 'default' );

// Test with WordPress debug mode (default).
define( 'WP_DEBUG', true );

define( 'DB_NAME', getenv( 'WORDPRESS_DB_NAME' ) ? getenv( 'WORDPRESS_DB_NAME' ) : 'wp_phpunit_tests' );
define( 'DB_USER', getenv( 'WORDPRESS_DB_USER' ) ? getenv( 'WORDPRESS_DB_USER' ) : 'root' );
define( 'DB_PASSWORD', getenv( 'WORDPRESS_DB_PASSWORD' ) ? getenv( 'WORDPRESS_DB_PASSWORD' ) : '' );
define( 'DB_HOST', getenv( 'WORDPRESS_DB_HOST' ) ? getenv( 'WORDPRESS_DB_HOST' ) : 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

$table_prefix = 'wpphpunittests_'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );

define( 'WP_PHP_BINARY', 'php' );

define( 'WPLANG', '' );
