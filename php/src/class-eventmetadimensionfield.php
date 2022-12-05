<?php
/**
 * Create the Event Meta Dimension field.
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Defines behaviour for event meta dimension field.
 */
final class EventMetaDimensionField extends DimensionFieldBase {
	/**
	 * Option eventMeta name.
	 *
	 * @var string
	 */
	const OPTION_EVENT_META = 'eventMeta';

	/**
	 * Option event_meta_dimension name.
	 *
	 * @var string
	 */
	const OPTION_EVENT_META_DIMENSION = 'event_meta_dimension';

	/**
	 * Get current field id.
	 */
	protected function get_id() {
		return self::OPTION_EVENT_META_DIMENSION;
	}

	/**
	 * Get current field title.
	 */
	protected function get_title() {
		return __( 'Event Meta Dimension', 'site-performance-tracker' );
	}

	/**
	 * Get option name
	 */
	protected function get_option_name() {
		return self::OPTION_EVENT_META;
	}
	/**
	 * Get field placeholder.
	 */
	protected function get_placeholder() {
		return __( 'dimension2', 'site-performance-tracker' );
	}

	/**
	 * Get field aria label.
	 */
	protected function get_aria_label() {
		return __( 'event meta dimension', 'site-performance-tracker' );
	}
}
