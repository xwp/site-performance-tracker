<?php
/**
 * Create the Event Meta Dimension field
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Class EventMetaDimensionField
 */
class EventMetaDimensionField {
	/**
	 * Setting that current fields belong to
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * Option eventMeta  name
	 *
	 * @var string
	 */
	const OPTION_EVENT_META = 'eventMeta';

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
			'event_meta_dimension',
			__( 'Event Meta Dimension', 'site-performance-tracker' ),
			array( $this, 'render' ),
			$page_id,
			$section_id
		);

		$this->settings = $settings;
	}

	/**
	 * Render Event Meta Dimension form input.
	 */
	public function render() {
		$this->settings->render_dimention_option( self::OPTION_EVENT_META, 'dimension2', 'event meta dimension' );
	}
}
