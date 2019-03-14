<?php
/**
 * Site Performance Tracker
 *
 * @package Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 *
 * Plugin Name: Site Performance Tracker
 * Plugin URI: https://github.com/xwp/site-performance-tracker
 * Description: Allows you to detect and track site performance metrics.
 * Version: 0.2.0
 * Author: XWP.co
 * Author URI: https://xwp.co
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin notice for incompatible versions of PHP.
 */
function site_performance_tracker_php_version_error() {
	printf( '<div class="error"><p>%s</p></div>', esc_html( site_performance_tracker_php_version_text() ) );
}

/**
 * String describing the minimum PHP version.
 *
 * "Namespace" is a PHP 5.3 introduced feature. This is a hard requirement
 * for the plugin structure.
 *
 * @return string
 */
function site_performance_tracker_php_version_text() {
	return __( 'Site Performance Tracker plugin error: Your version of PHP is too old to run this plugin. You must be running PHP 5.3 or higher.', 'site-performance-tracker' );
}

// If the PHP version is too low, show warning and return.
if ( version_compare( phpversion(), '5.3', '<' ) ) {
	if ( defined( 'WP_CLI' ) ) {
		WP_CLI::warning( site_performance_tracker_php_version_text() );
	} else {
		add_action( 'admin_notices', 'site_performance_tracker_php_version_error' );
	}

	return;
}

// Setup the Composer auto loader for classes.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// Load helper functions manually.
require_once __DIR__ . '/php/helpers.php';

// Initialize the plugin.
require_once __DIR__ . '/php/Plugin.php';
add_action( 'init', array( new \Site_Performance_Tracker\Plugin(), 'init' ) );
