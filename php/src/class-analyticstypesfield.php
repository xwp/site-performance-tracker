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
			array( $this, 'analytics_types_render' ),
			$page_id,
			$section_id
		);

		$this->settings = $settings;
	}

	/**
	 * Render Analytics Types form dropdown.
	 */
	public function analytics_types_render() {
		$options = $this->settings->get_settings();
		global $tracker_config;
		$set = false;
		if ( isset( $tracker_config['ga_id'] ) ) {
			$options[ self::OPTION_ANALYTICS_TYPES ] = 'ga_id';
			$set                                     = true;
		} elseif ( isset( $tracker_config[ Settings::OPTION_TAG_ID ] ) ) {
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
