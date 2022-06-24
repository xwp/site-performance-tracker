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
class EventMetaDimensionField extends FieldBase {
	/**
	 * Option eventMeta  name
	 *
	 * @var string
	 */
	const OPTION_EVENT_META = 'eventMeta';

	/**
	 * Get current field id
	 */
	protected function get_id() {
		return 'event_meta_dimension';
	}

	/**
	 * Get current field title
	 */
	protected function get_title() {
		return __( 'Event Meta Dimension', 'site-performance-tracker' );
	}

	/**
	 * Render Event Meta Dimension form input.
	 */
	public function render() {
		$this->settings->render_dimention_option( self::OPTION_EVENT_META, 'dimension2', 'event meta dimension' );
	}
}
