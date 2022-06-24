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
class EventDebugDimensionField {
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
	 * Initialize settings.
	 */
	/**
	 * Initialize field.
	 *
	 * @param Settings $settings settings that current fields belong to.
	 * @param string   $page_id field page id.
	 * @param string   $section_id field section id.
	 */
	public function init( $settings, $page_id, $section_id ) {
		add_settings_field(
			'event_debug_dimension',
			__( 'Event Debug Dimension', 'site-performance-tracker' ),
			array( $this, 'render' ),
			$page_id,
			$section_id
		);

		$this->settings = $settings;
	}

	/**
	 * Render Event Debug Dimension form input.
	 */
	public function render() {
		$this->settings->render_dimention_option( self::OPTION_EVENT_DEBUG, 'dimension3', 'event debug dimension' );
	}
}
