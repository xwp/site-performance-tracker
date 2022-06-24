<?php
/**
 * Create the Analytics ID field
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Class AnalyticsIdField
 */
class AnalyticsIdField extends FieldBase {
	/**
	 * Option gtag_id name
	 *
	 * @var string
	 */
	const OPTION_TAG_ID = 'gtag_id';

	/**
	 * Get current field id
	 */
	protected function get_id() {
		return 'analytics_id';
	}

	/**
	 * Get current field title
	 */
	protected function get_title() {
		return __( 'Analytics ID', 'site-performance-tracker' );
	}

	/**
	 * Render Analytics ID form input.
	 */
	public function render() {
		$options = $this->settings->get_settings();
		global $tracker_config;
		$set  = false;
		$prop = self::OPTION_TAG_ID;

		if ( isset( $options['ga_id'] ) ) {
			$options[ self::OPTION_TAG_ID ] = $options['ga_id'];
		}

		if ( isset( $tracker_config['ga_id'] ) ) {
			$options[ self::OPTION_TAG_ID ] = $tracker_config['ga_id'];
			$prop                           = 'ga_id';
			$set                            = true;
		} elseif ( isset( $tracker_config[ self::OPTION_TAG_ID ] ) ) {
			$options[ self::OPTION_TAG_ID ] = $tracker_config[ self::OPTION_TAG_ID ];
			$set                            = true;
		} elseif ( isset( $tracker_config['ga4_id'] ) ) {
			$options[ self::OPTION_TAG_ID ] = $tracker_config['ga4_id'];
			$prop                           = 'ga4_id';
			$set                            = true;
		}
		?>
		<input type='text' name='spt_settings[<?php echo esc_attr( self::OPTION_TAG_ID ); ?>]' pattern="[UA|GTM|G]+-[A-Z|0-9]+.*"
			   value='<?php echo esc_attr( $options[ self::OPTION_TAG_ID ] ); ?>' placeholder="UA-XXXXXXXX-Y"
			   aria-label="analytics id" <?php $this->print_readonly( $prop ); ?> required>
		<?php

		$this->show_theme_warning( $set );
	}
}
