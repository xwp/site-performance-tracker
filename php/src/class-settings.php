<?php
/**
 * Plugin Settings file.
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Class Settings
 */
class Settings {
	/**
	 * Initialize settings.
	 */
	public function init() {
		$this->register_hooks();
	}

	/**
	 * Register hooks.
	 */
	protected function register_hooks() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
	}

	/**
	 * Add tracker as a settings menu item.
	 */
	public function add_admin_menu() {
		add_options_page( 'Site Performance Tracker', 'Site Performance Tracker', 'manage_options', 'site_performance_tracker', 'spt_options_page' );
	}

	/**
	 * Initialize tracker settings by registering it and adding
	 * sections and fields.
	 */
	public function settings_init() {
		register_setting( 'pluginPage', 'spt_settings' );

		add_settings_section(
			'spt_pluginPage_section',
			null,
			'spt_settings_section_callback',
			'pluginPage'
		);

		add_settings_field(
			'analytics_types',
			__( 'Analytics Types', 'site-performance-tracker' ),
			'analytics_types_render',
			'pluginPage',
			'spt_pluginPage_section'
		);

		add_settings_field(
			'analytics_id',
			__( 'Analytics ID', 'site-performance-tracker' ),
			'analytics_id_render',
			'pluginPage',
			'spt_pluginPage_section'
		);

		add_settings_field(
			'measurement_version_dimension',
			__( 'Measurement Version Dimension', 'site-performance-tracker' ),
			'measurement_version_dimension_render',
			'pluginPage',
			'spt_pluginPage_section'
		);

		add_settings_field(
			'event_meta_dimension',
			__( 'Event Meta Dimension', 'site-performance-tracker' ),
			'event_meta_dimension_render',
			'pluginPage',
			'spt_pluginPage_section'
		);

		add_settings_field(
			'event_debug_dimension',
			__( 'Event Debug Dimension', 'site-performance-tracker' ),
			'event_debug_dimension_render',
			'pluginPage',
			'spt_pluginPage_section'
		);

		add_settings_field(
			'web_vitals_tracking_ratio',
			__( 'Web Vitals Tracking Ratio', 'site-performance-tracker' ),
			'web_vitals_tracking_ratio_render',
			'pluginPage',
			'spt_pluginPage_section'
		);
	}
}
