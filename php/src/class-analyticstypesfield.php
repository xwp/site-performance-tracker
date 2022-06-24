<?php
/**
 * Create the Analytics Types field
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Class AnalyticsTypesField
 */
class AnalyticsTypesField {
	/**
	 * Option analytics_types name
	 *
	 * @var string
	 */
	const OPTION_ANALYTICS_TYPES = 'analytics_types';

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
			self::OPTION_ANALYTICS_TYPES,
			__( 'Analytics Types', 'site-performance-tracker' ),
			array( $settings, 'analytics_types_render' ),
			$page_id,
			$section_id
		);
	}
}
