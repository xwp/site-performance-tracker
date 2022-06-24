<?php
/**
 * Create the Analytics ID field
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Class AnalyticsTypesField
 */
class AnalyticsIdField {
	/**
	 * Setting that current fields belong to
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * Option gtag_id name
	 *
	 * @var string
	 */
	const OPTION_TAG_ID = 'gtag_id';

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
			'analytics_id',
			__( 'Analytics ID', 'site-performance-tracker' ),
			array( $this, 'analytics_id_render' ),
			$page_id,
			$section_id
		);

		$this->settings = $settings;
	}

	/**
	 * Render Analytics ID form input.
	 */
	public function analytics_id_render() {
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
			   aria-label="analytics id" <?php $this->settings->print_readonly( $prop ); ?> required>
		<?php

		$this->settings->show_theme_warning( $set );
	}
}
