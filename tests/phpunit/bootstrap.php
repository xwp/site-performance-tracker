<?php
/**
 * Bootstrap the WP testing environment.
 *
 * @package XWP\Site_Performance_Tracker
 */

require_once dirname( dirname( __DIR__ ) ) . '/vendor/autoload.php';

// Load WP unit test helper library.
require_once getenv( 'WP_PHPUNIT__DIR' ) . '/includes/functions.php';

// Enable our plugin.
tests_add_filter(
	'muplugins_loaded',
	function() {
		require dirname( __DIR__ ) . '/site-performance-tracker.php';
	}
);

// Start up the WP testing environment.
require getenv( 'WP_PHPUNIT__DIR' ) . '/includes/bootstrap.php';
