<?php
/**
 * Create the Analytics Types field.
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Stores the Google Analytics type.
 */
class AnalyticsTypesField extends FieldBase {
	/**
	 * Option analytics_types name.
	 *
	 * @var string
	 */
	const OPTION_ANALYTICS_TYPES = 'analytics_types';

	/**
	 * Get current field id.
	 */
	protected function get_id() {
		return self::OPTION_ANALYTICS_TYPES;
	}

	/**
	 * Get current field title.
	 */
	protected function get_title() {
		return __( 'Analytics Types', 'site-performance-tracker' );
	}

	/**
	 * Render Analytics Types form dropdown.
	 */
	public function render() {
		$options = $this->settings->get_settings();
		$hardcoded_tracker_config = $this->settings->get_hardcoded_tracker_config();
		$display_theme_override_warning = false;
		if ( isset( $hardcoded_tracker_config[ AnalyticsIdField::OPTION_ANALYTICS_ID ] ) ) {
			$options[ self::OPTION_ANALYTICS_TYPES ] = AnalyticsIdField::OPTION_ANALYTICS_ID;
			$display_theme_override_warning          = true;
		} elseif ( isset( $hardcoded_tracker_config[ AnalyticsIdField::OPTION_TAG_ID ] ) ) {
			$options[ self::OPTION_ANALYTICS_TYPES ] = 'gtm';
			$display_theme_override_warning          = true;
		} elseif ( isset( $hardcoded_tracker_config[ AnalyticsIdField::OPTION_GA4_ID ] ) ) {
			$options[ self::OPTION_ANALYTICS_TYPES ] = 'ga4';
			$display_theme_override_warning          = true;
		}
		?>
		<select name="spt_settings[<?php echo esc_attr( self::OPTION_ANALYTICS_TYPES ); ?>]" <?php echo ( $display_theme_override_warning ) ? esc_attr( 'disabled' ) : ''; ?> required>
			<option value="ga_id" <?php selected( $options[ self::OPTION_ANALYTICS_TYPES ], 'ga_id' ); ?>>
				<?php esc_html_e( 'Google Analytics', 'site-performance-tracker' ); ?>
			</option>
			<option value="gtm" <?php selected( $options[ self::OPTION_ANALYTICS_TYPES ], 'gtm' ); ?>>
				<?php esc_html_e( 'Global Site Tag', 'site-performance-tracker' ); ?>
			</option>
			<option value="ga4" <?php selected( $options[ self::OPTION_ANALYTICS_TYPES ], 'ga4' ); ?>>
				<?php esc_html_e( 'GA4 Analytics', 'site-performance-tracker' ); ?>
			</option>
		</select>
		<?php

		$this->show_theme_warning( $display_theme_override_warning );
	}
}
