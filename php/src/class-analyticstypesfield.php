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
class AnalyticsTypesField extends FieldBase {
	/**
	 * Setting that current fields belong to
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * Option analytics_types name
	 *
	 * @var string
	 */
	const OPTION_ANALYTICS_TYPES = 'analytics_types';

	/**
	 * Get current field id
	 */
	protected function get_id() {
		return self::OPTION_ANALYTICS_TYPES;
	}

	/**
	 * Get current field title
	 */
	protected function get_title() {
		return __( 'Analytics Types', 'site-performance-tracker' );
	}

	/**
	 * Render Analytics Types form dropdown.
	 */
	public function render() {
		$options = $this->settings->get_settings();
		global $tracker_config;
		$set = false;
		if ( isset( $tracker_config['ga_id'] ) ) {
			$options[ self::OPTION_ANALYTICS_TYPES ] = 'ga_id';
			$set                                     = true;
		} elseif ( isset( $tracker_config[ AnalyticsIdField::OPTION_TAG_ID ] ) ) {
			$options[ self::OPTION_ANALYTICS_TYPES ] = 'gtm';
			$set                                     = true;
		} elseif ( isset( $tracker_config['ga4_id'] ) ) {
			$options[ self::OPTION_ANALYTICS_TYPES ] = 'ga4';
			$set                                     = true;
		}
		?>
		<select name="spt_settings[<?php echo esc_attr( self::OPTION_ANALYTICS_TYPES ); ?>]" <?php echo ( $set ) ? esc_attr( 'disabled' ) : ''; ?> required>
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

		$this->settings->show_theme_warning( $set );
	}
}
