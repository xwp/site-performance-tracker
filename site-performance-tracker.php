<?php
/**
 * Site Performance Tracker
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 *
 * Plugin Name: Site Performance Tracker
 * Plugin URI: https://github.com/xwp/site-performance-tracker
 * Description: Allows you to detect and track site performance metrics.
 * Version: 1.3
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
function xwp_site_performance_tracker_php_version_error() {
	printf( '<div class="error"><p>%s</p></div>', esc_html( xwp_site_performance_tracker_php_version_text() ) );
}

/**
 * String describing the minimum PHP version.
 *
 * "Namespace" is a PHP 5.3 introduced feature. This is a hard requirement
 * for the plugin structure.
 *
 * @return string
 */
function xwp_site_performance_tracker_php_version_text() {
	return __( 'Site Performance Tracker plugin error: Your version of PHP is too old to run this plugin. You must be running PHP 5.3 or higher.', 'site-performance-tracker' );
}

/**
 * Global function to provide access to the plugin APIs.
 *
 * @return XWP\Site_Performance_Tracker\Plugin
 */
function xwp_site_performance_tracker() {
	static $plugin;

	if ( ! isset( $plugin ) ) {
		$plugin = new XWP\Site_Performance_Tracker\Plugin( __DIR__ );
	}

	return $plugin;
}

/**
 * Load Site Performance Tracker classes.
 *
 * @param class-string $class_name The fully-qualified class name.
 *
 * @return void
 */
function xwp_site_performance_autoloader( $class_name ) {
	$project_namespace = 'XWP\\Site_Performance_Tracker\\';
	$length = strlen( $project_namespace );

	// Class is not in our namespace.
	if ( 0 !== strncmp( $project_namespace, $class_name, $length ) ) {
		return;
	}

	$relative_class_name = substr( $class_name, $length );
	$name_parts = explode( '\\', strtolower( str_replace( '_', '-', $relative_class_name ) ) );
	$last_part  = array_pop( $name_parts );

	$file = sprintf(
		'%1$s/php/src%2$s/class-%3$s.php',
		__DIR__,
		array() === $name_parts ? '' : '/' . implode( '/', $name_parts ),
		$last_part
	);

	if ( ! is_file( $file ) ) {
		return;
	}

	require $file;
}

// If the PHP version is too low, show warning and return.
if ( version_compare( phpversion(), '5.3', '<' ) ) {
	if ( defined( 'WP_CLI' ) ) {
		WP_CLI::warning( xwp_site_performance_tracker_php_version_text() );
	} else {
		add_action( 'admin_notices', 'xwp_site_performance_tracker_php_version_error' );
	}

	return;
}

// Register autoloader and load helpers.
spl_autoload_register( 'xwp_site_performance_autoloader' );
require_once __DIR__ . '/php/helpers.php';

// Initialize the plugin.
add_action( 'init', array( xwp_site_performance_tracker(), 'init' ) );
