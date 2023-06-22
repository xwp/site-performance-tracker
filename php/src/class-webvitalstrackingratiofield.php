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
class WebVitalsTrackingRatioField extends FieldBase {
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
	 * Get current field id
	 */
	protected function get_id() {
		return self::OPTION_WEB_VITALS_TRACKING_RATIO;
	}

	/**
	 * Get current field title
	 */
	protected function get_title() {
		return __( 'Web Vitals Tracking Ratio', 'site-performance-tracker' );
	}

	/**
	 * Render Tracking Ratio form input.
	 */
	public function render() {
		$options = $this->settings->get_settings();
		$hardcoded_tracker_config = $this->settings->get_hardcoded_tracker_config();
		$display_theme_override_warning = false;
		if ( isset( $hardcoded_tracker_config[ self::OPTION_WEB_VITALS_TRACKING_RATIO ] ) ) {
			$options[ self::OPTION_WEB_VITALS_TRACKING_RATIO ] = $hardcoded_tracker_config[ self::OPTION_WEB_VITALS_TRACKING_RATIO ];
			$display_theme_override_warning                    = true;
		}
		if ( has_filter( 'site_performance_tracker_chance' ) ) {
			$options[ self::OPTION_WEB_VITALS_TRACKING_RATIO ] = apply_filters( 'site_performance_tracker_chance', 1 );
			$display_theme_override_warning                    = true;
		}
		?>
		<input
			type="number"
			name="spt_settings[<?php echo esc_attr( self::OPTION_WEB_VITALS_TRACKING_RATIO ); ?>]"
			step="0.001"
			min="0.001"
			max="1"
			value="<?php echo esc_attr( $options[ self::OPTION_WEB_VITALS_TRACKING_RATIO ] ); ?>"
			placeholder="<?php _e( 'Enter between 0 > 1', 'site-performance-tracker' ); ?>"
			aria-label="<?php _e( 'web vitals tracking ratio', 'site-performance-tracker' ); ?>"
			<?php if_print( $display_theme_override_warning, 'readonly' ); ?>
		>
		<?php

		$this->show_theme_warning( $display_theme_override_warning );
	}
}
