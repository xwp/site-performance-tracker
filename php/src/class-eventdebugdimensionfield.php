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
class EventDebugDimensionField extends FieldBase {
	/**
	 * Setting that current fields belong to
	 *
	 * @var Settings
	 */
	protected $settings;

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
	 * Render Event Debug Dimension form input.
	 */
	public function render() {
		$this->settings->render_dimention_option( self::OPTION_EVENT_DEBUG, 'dimension3', 'event debug dimension' );
	}
}
