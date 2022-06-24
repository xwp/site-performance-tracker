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
 * Version: 1.1.6
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

/**
 * Use includes to simplify the plugin distribution and usage on
 * platforms on platforms that don't use Composer autoloader.
 *
 * @todo Consider supporting Composer classmap autoload (to match
 * the filename requirements per PHPCS) after figuring out
 * how to handle built JS and the presence of `vendor` directory.
 */
require_once __DIR__ . '/php/src/class-plugin.php';
require_once __DIR__ . '/php/src/class-settings.php';
require_once __DIR__ . '/php/src/class-fieldbase.php';
require_once __DIR__ . '/php/src/class-dimensionfieldbase.php';
require_once __DIR__ . '/php/src/class-analyticstypesfield.php';
require_once __DIR__ . '/php/src/class-analyticsidfield.php';
require_once __DIR__ . '/php/src/class-measurementversiondimensionfield.php';
require_once __DIR__ . '/php/src/class-eventmetadimensionfield.php';
require_once __DIR__ . '/php/src/class-eventdebugdimensionfield.php';
require_once __DIR__ . '/php/src/class-webvitalstrackingratiofield.php';
require_once __DIR__ . '/php/helpers.php';

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

// Initialize the plugin.
add_action( 'init', array( xwp_site_performance_tracker(), 'init' ) );
