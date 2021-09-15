<?php
/**
 * Helper functions to be used inside templates.
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

/**
 * Helper function that renders the site performance mark.
 *
 * @param string $mark_slug Mark slug.
 *
 * @return void
 */
function the_site_performance_mark( $mark_slug ) {
	xwp_site_performance_tracker()->the_performance_mark( $mark_slug );
}

/**
 * Helper function that returns the site performance mark code.
 *
 * @param string $mark_slug Mark slug.
 *
 * @return string
 */
function get_the_site_performance_mark( $mark_slug ) {
	return xwp_site_performance_tracker()->get_the_performance_mark( $mark_slug );
}
