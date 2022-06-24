<?php
/**
 * Create the Web Vitals Tracking Ratio field
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Class WebVitalsTrackingRatioField
 */
class WebVitalsTrackingRatioField {
	/**
	 * Setting that current fields belong to
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * Option web_vitals_tracking_ratio name
	 *
	 * @var string
	 */
	const OPTION_WEB_VITALS_TRACKING_RATIO = 'web_vitals_tracking_ratio';

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
			self::OPTION_WEB_VITALS_TRACKING_RATIO,
			__( 'Web Vitals Tracking Ratio', 'site-performance-tracker' ),
			array( $this, 'web_vitals_tracking_ratio_render' ),
			$page_id,
			$section_id
		);

		$this->settings = $settings;
	}

	/**
	 * Render Tracking Ratio form input.
	 */
	public function web_vitals_tracking_ratio_render() {
		$options = $this->settings->get_settings();
		global $tracker_config;
		$set = false;
		if ( isset( $tracker_config[ self::OPTION_WEB_VITALS_TRACKING_RATIO ] ) ) {
			$options[ self::OPTION_WEB_VITALS_TRACKING_RATIO ] = $tracker_config[ self::OPTION_WEB_VITALS_TRACKING_RATIO ];
			$set                                  = true;
		}
		if ( has_filter( 'site_performance_tracker_chance' ) ) {
			$options[ self::OPTION_WEB_VITALS_TRACKING_RATIO ] = apply_filters( 'site_performance_tracker_chance', 1 );
			$set                                  = true;
		}
		?>
		<input type='number' name='spt_settings[<?php echo esc_attr( self::OPTION_WEB_VITALS_TRACKING_RATIO ); ?>]' step='0.01' min='0.01' max='1'
			   value='<?php echo esc_attr( $options[ self::OPTION_WEB_VITALS_TRACKING_RATIO ] ); ?>'
			   placeholder="Enter between 0 > 1" aria-label="web vitals tracking ratio"
				<?php if ( $set ) { ?>
					readonly
				<?php } ?>>
		<?php

		$this->settings->show_theme_warning( $set );
	}
}
