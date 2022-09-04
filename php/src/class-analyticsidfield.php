<?php
/**
 * Create the Analytics ID field.
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Stores the Google Analytics ID.
 */
final class AnalyticsIdField extends FieldBase {
	/**
	 * Option gtag_id name.
	 *
	 * @var string
	 */
	const OPTION_TAG_ID = 'gtag_id';


	/**
	 * Option gtag_id name.
	 *
	 * @var string
	 */
	const OPTION_ANALYTICS_ID = 'analytics_id';

	/**
	 * Get current field id.
	 */
	protected function get_id() {
		return self::OPTION_ANALYTICS_ID;
	}

	/**
	 * Get current field title.
	 */
	protected function get_title() {
		return __( 'Analytics ID', 'site-performance-tracker' );
	}

	/**
	 * Render Analytics ID form input.
	 */
	public function render() {
		$options = $this->settings->get_settings();
		$hardcoded_tracker_config = $this->settings->get_hardcoded_tracker_config();
		$display_theme_override_warning  = false;
		$property_name = self::OPTION_TAG_ID;

		if ( isset( $options['ga_id'] ) ) {
			$options[ self::OPTION_TAG_ID ] = $options['ga_id'];
		}

		if ( isset( $hardcoded_tracker_config['ga_id'] ) ) {
			$options[ self::OPTION_TAG_ID ] = $hardcoded_tracker_config['ga_id'];
			$property_name                  = 'ga_id';
			$display_theme_override_warning = true;
		} elseif ( isset( $hardcoded_tracker_config[ self::OPTION_TAG_ID ] ) ) {
			$options[ self::OPTION_TAG_ID ] = $hardcoded_tracker_config[ self::OPTION_TAG_ID ];
			$display_theme_override_warning                            = true;
		} elseif ( isset( $hardcoded_tracker_config['ga4_id'] ) ) {
			$options[ self::OPTION_TAG_ID ] = $hardcoded_tracker_config['ga4_id'];
			$property_name                  = 'ga4_id';
			$display_theme_override_warning = true;
		}
		?>
		<input type='text' name='spt_settings[<?php echo esc_attr( self::OPTION_TAG_ID ); ?>]' pattern="[UA|GTM|G]+-[A-Z|0-9]+.*"
			   value='<?php echo esc_attr( $options[ self::OPTION_TAG_ID ] ); ?>' placeholder="UA-XXXXXXXX-Y"
			   aria-label="<?php echo esc_attr( __( 'analytics id', 'site-performance-tracker' ) ); ?>" <?php $this->print_readonly( $property_name ); ?> required>
		<?php

		$this->show_theme_warning( $display_theme_override_warning );
	}
}
