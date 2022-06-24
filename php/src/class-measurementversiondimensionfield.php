<?php
/**
 * Create the Measurement Version Dimension field
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Class MeasurementVersionDimensionField
 */
class MeasurementVersionDimensionField {
	/**
	 * Setting that current fields belong to
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * Option measurementVersion name
	 *
	 * @var string
	 */
	const OPTION_MEASUREMENT_VERSION = 'measurementVersion';

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
			'measurement_version_dimension',
			__( 'Measurement Version Dimension', 'site-performance-tracker' ),
			array( $this, 'render' ),
			$page_id,
			$section_id
		);

		$this->settings = $settings;
	}

	/**
	 * Render Measurement Version Dimension form input.
	 */
	public function render() {
		$this->settings->render_dimention_option( self::OPTION_MEASUREMENT_VERSION, 'dimension1', 'measurement version dimension' );
	}
}
