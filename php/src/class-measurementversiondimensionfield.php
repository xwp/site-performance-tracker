<?php
/**
 * Create the Measurement Version Dimension field.
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Stores the Measurement Version Dimension.
 */
final class MeasurementVersionDimensionField extends DimensionFieldBase {
	/**
	 * Option measurementVersion name.
	 *
	 * @var string
	 */
	const OPTION_MEASUREMENT_VERSION = 'measurementVersion';

	/**
	 * Option measurement_version_dimension name.
	 *
	 * @var string
	 */
	const OPTION_MEASUREMENT_VERSION_DIMENSION = 'measurement_version_dimension';

	/**
	 * Get current field id.
	 */
	protected function get_id() {
		return self::OPTION_MEASUREMENT_VERSION_DIMENSION;
	}

	/**
	 * Get current field title.
	 */
	protected function get_title() {
		return __( 'Measurement Version Dimension', 'site-performance-tracker' );
	}

	/**
	 * Get option name.
	 */
	protected function get_option_name() {
		return self::OPTION_MEASUREMENT_VERSION;
	}
	/**
	 * Get field placeholder.
	 */
	protected function get_placeholder() {
		return __( 'dimension1', 'site-performance-tracker' );
	}

	/**
	 * Get field aria label.
	 */
	protected function get_aria_label() {
		return __( 'measurement version dimension', 'site-performance-tracker' );
	}
}
