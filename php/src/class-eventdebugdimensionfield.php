<?php
/**
 * Create the Event Debug Dimension field
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Class EventDebugDimensionField
 */
class EventDebugDimensionField extends DimensionFieldBase {

	/**
	 * Option eventDebug name
	 *
	 * @var string
	 */
	const OPTION_EVENT_DEBUG = 'eventDebug';

	/**
	 * Get current field id
	 */
	protected function get_id() {
		return 'event_debug_dimension';
	}

	/**
	 * Get current field title
	 */
	protected function get_title() {
		return __( 'Event Debug Dimension', 'site-performance-tracker' );
	}

	/**
	 * Get option name
	 */
	protected function get_option_name() {
		return self::OPTION_EVENT_DEBUG;
	}
	/**
	 * Get field placeholder
	 */
	protected function get_placeholder() {
		return 'dimension3';
	}

	/**
	 * Get field aria label
	 */
	protected function get_aria_label() {
		return 'event debug dimension';
	}
}
