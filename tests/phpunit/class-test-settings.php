<?php

use XWP\Site_Performance_Tracker\Settings;
use Sunra\PhpSimple\HtmlDomParser;

class Test_Settings extends WP_UnitTestCase {

	public $settings;

	public function setUp() {

		parent::setUp();

		$this->settings = new Settings();

		// configure test to run udner administrator
		global $current_user;
		$current_user->add_role( 'administrator' );
		$current_user->get_role_caps();
	}

	public function tearDown() {
		parent::tearDown();

		global $tracker_config;
		$tracker_config = null;

		global $wp_settings_sections;
		if ( isset( $wp_settings_sections['pluginPage'] ) ) {
			unset( $wp_settings_sections['pluginPage'] );
		}

		global $wp_settings_fields;
		if ( isset( $wp_settings_fields['pluginPage'] ) ) {
			unset( $wp_settings_fields['pluginPage'] );
		}
	}

	public function test_init_register_actions() {
		$this->assertFalse( has_action( 'after_setup_theme', array( $this->settings, 'get_hardcoded_tracker_config' ) ) );
		$this->assertFalse( has_action( 'admin_menu', array( $this->settings, 'add_admin_menu' ) ) );
		$this->assertFalse( has_action( 'admin_init', array( $this->settings, 'settings_init' ) ) );

		$this->settings->init();

		$this->assertEquals( PHP_INT_MAX, has_action( 'after_setup_theme', array( $this->settings, 'get_hardcoded_tracker_config' ) ) );
		$this->assertNotFalse( has_action( 'admin_menu', array( $this->settings, 'add_admin_menu' ) ) );
		$this->assertNotFalse( has_action( 'admin_init', array( $this->settings, 'settings_init' ) ) );
	}

	public function test_get_hardcoded_tracker_config__with_empty_theme() {
		global $tracker_config;

		$this->assertNull( $tracker_config );

		$this->settings->get_hardcoded_tracker_config();

		$this->assertIsArray( $tracker_config );
		$this->assertTrue( empty( $tracker_config ) );
	}

	public function test_add_admin_menu() {
		global $submenu;

		$this->assertFalse( isset( $submenu['options-general.php'] ) );

		$hook_name = $this->settings->add_admin_menu();

		$this->assertTrue( isset( $submenu['options-general.php'] ) );

		$options = $submenu['options-general.php'];
		$performance_options_menu = end( $options );

		$this->assertSame( 'Site Performance Tracker', $performance_options_menu[0] );
		$this->assertSame( 'manage_options', $performance_options_menu[1] );
		$this->assertSame( 'site_performance_tracker', $performance_options_menu[2] );
		$this->assertSame( 'Site Performance Tracker', $performance_options_menu[3] );

		$this->assertNotFalse( has_action( $hook_name, array( $this->settings, 'render_settings_page' ) ) );
	}

	public function test_settings_init_register_setting() {
		global $wp_registered_settings;

		$this->settings->settings_init();

		$this->assertTrue( isset( $wp_registered_settings['spt_settings'] ) );

		$registered_settings = $wp_registered_settings['spt_settings'];

		$this->assertSame( 'pluginPage', $registered_settings['group'] );
	}

	public function test_settings_init_setting_section() {
		global $wp_settings_sections;

		$this->assertFalse( isset( $wp_settings_sections['pluginPage'] ) );

		$this->settings->settings_init();

		$this->assertTrue( isset( $wp_settings_sections['pluginPage']['spt_pluginPage_section'] ) );
		$section = $wp_settings_sections['pluginPage']['spt_pluginPage_section'];
		$this->assertSame( 'spt_pluginPage_section', $section['id'] );
	}

	public function test_settings_init_field_analytics_types() {
		global $wp_settings_fields;

		$this->assertFalse(
			isset( $wp_settings_fields['pluginPage'] )
		);

		$this->settings->settings_init();

		$field_name = 'analytics_types';
		$this->assertTrue( isset( $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ] ) );
		$field = $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ];

		$this->assertSame( $field_name, $field['id'] );
		$this->assertSame( 'Analytics Types', $field['title'] );

		$this->assertSame( array( $this->settings, 'analytics_types_render' ), $field['callback'] );

		$this->assertSame( array(), $field['args'] );
	}

	public function test_settings_init_field_gtag_id() {
		global $wp_settings_fields;

		$this->assertFalse(
			isset( $wp_settings_fields['pluginPage'] )
		);

		$this->settings->settings_init();

		$field_name = 'gtag_id';
		$this->assertTrue( isset( $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ] ) );
		$field = $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ];

		$this->assertSame( $field_name, $field['id'] );
		$this->assertSame( 'Analytics ID', $field['title'] );

		$this->assertSame( array( $this->settings, 'analytics_id_render' ), $field['callback'] );

		$this->assertSame( array(), $field['args'] );
	}

	public function test_settings_init_field_measurementVersion() {
		global $wp_settings_fields;

		$this->assertFalse(
			isset( $wp_settings_fields['pluginPage'] )
		);

		$this->settings->settings_init();

		$field_name = 'measurementVersion';
		$this->assertTrue( isset( $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ] ) );
		$field = $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ];

		$this->assertSame( $field_name, $field['id'] );
		$this->assertSame( 'Measurement Version Dimension', $field['title'] );

		$this->assertSame( array( $this->settings, 'measurement_version_dimension_render' ), $field['callback'] );

		$this->assertSame( array(), $field['args'] );
	}

	public function test_settings_init_field_eventMeta() {
		global $wp_settings_fields;

		$this->assertFalse(
			isset( $wp_settings_fields['pluginPage'] )
		);

		$this->settings->settings_init();

		$field_name = 'eventMeta';
		$this->assertTrue( isset( $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ] ) );
		$field = $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ];

		$this->assertSame( $field_name, $field['id'] );
		$this->assertSame( 'Event Meta Dimension', $field['title'] );

		$this->assertSame( array( $this->settings, 'event_meta_dimension_render' ), $field['callback'] );

		$this->assertSame( array(), $field['args'] );
	}

	public function test_settings_init_field_eventDebug() {
		global $wp_settings_fields;

		$this->assertFalse(
			isset( $wp_settings_fields['pluginPage'] )
		);

		$this->settings->settings_init();

		$field_name = 'eventDebug';
		$this->assertTrue( isset( $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ] ) );
		$field = $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ];

		$this->assertSame( $field_name, $field['id'] );
		$this->assertSame( 'Event Debug Dimension', $field['title'] );

		$this->assertSame( array( $this->settings, 'event_debug_dimension_render' ), $field['callback'] );

		$this->assertSame( array(), $field['args'] );
	}

	public function test_settings_init_field_web_vitals_tracking_ratio() {
		global $wp_settings_fields;

		$this->assertFalse(
			isset( $wp_settings_fields['pluginPage'] )
		);

		$this->settings->settings_init();

		$field_name = 'web_vitals_tracking_ratio';
		$this->assertTrue( isset( $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ] ) );
		$field = $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ];

		$this->assertSame( $field_name, $field['id'] );
		$this->assertSame( 'Web Vitals Tracking Ratio', $field['title'] );

		$this->assertSame( array( $this->settings, 'web_vitals_tracking_ratio_render' ), $field['callback'] );

		$this->assertSame( array(), $field['args'] );
	}

	public function test_settings_section_callback() {
		ob_start();
		$this->settings->settings_section_callback();
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertSame( 'Update Site Performance Tracker settings', $result );
	}

	public function test_analytics_types_render_empty_options() {
		ob_start();
		$this->settings->analytics_types_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
		<select name="spt_settings[analytics_types]" required>
				<option value="ga_id" >
						Google Analytics
				</option>
				<option value="gtm" >
						Global Site Tag
				</option>
				<option value="ga4" >
						GA4 Analytics
				</option>
		</select>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_analytics_types_render_with_theme_ga_id() {
		global $tracker_config;
		$tracker_config['ga_id'] = 'test_ga_id';

		ob_start();
		$this->settings->analytics_types_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<select name="spt_settings[analytics_types]" disabled required>
					<option value="ga_id" selected='selected'>
							Google Analytics
					</option>
					<option value="gtm" >
							Global Site Tag
					</option>
					<option value="ga4" >
							GA4 Analytics
					</option>
			</select>
			<br/><small>Configured via theme files</small>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_analytics_types_render_set_ga_id() {
		add_option( 'spt_settings', array( 'analytics_types' => 'ga_id' ) );

		ob_start();
		$this->settings->analytics_types_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<select name="spt_settings[analytics_types]" required>
					<option value="ga_id" selected='selected'>
							Google Analytics
					</option>
					<option value="gtm" >
							Global Site Tag
					</option>
					<option value="ga4" >
							GA4 Analytics
					</option>
			</select>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_analytics_types_render_set_gtm() {
		add_option( 'spt_settings', array( 'analytics_types' => 'gtm' ) );

		ob_start();
		$this->settings->analytics_types_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<select name="spt_settings[analytics_types]" required>
					<option value="ga_id" >
							Google Analytics
					</option>
					<option value="gtm" selected='selected'>
							Global Site Tag
					</option>
					<option value="ga4" >
							GA4 Analytics
					</option>
			</select>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_analytics_types_render_set_ga4() {
		add_option( 'spt_settings', array( 'analytics_types' => 'ga4' ) );

		ob_start();
		$this->settings->analytics_types_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<select name="spt_settings[analytics_types]" required>
					<option value="ga_id" >
							Google Analytics
					</option>
					<option value="gtm" >
							Global Site Tag
					</option>
					<option value="ga4" selected='selected'>
							GA4 Analytics
					</option>
			</select>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_analytics_types_render_with_theme_gtag_id() {
		global $tracker_config;
		$tracker_config['gtag_id'] = 'test_gtag_id';

		ob_start();
		$this->settings->analytics_types_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<select name="spt_settings[analytics_types]" disabled required>
					<option value="ga_id" >
							Google Analytics
					</option>
					<option value="gtm" selected='selected'>
							Global Site Tag
					</option>
					<option value="ga4" >
							GA4 Analytics
					</option>
			</select>
			<br/><small>Configured via theme files</small>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_analytics_types_render_with_theme_ga4_id() {
		global $tracker_config;
		$tracker_config['ga4_id'] = 'test_ga4_id';

		ob_start();
		$this->settings->analytics_types_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<select name="spt_settings[analytics_types]" disabled required>
					<option value="ga_id" >
							Google Analytics
					</option>
					<option value="gtm" >
							Global Site Tag
					</option>
					<option value="ga4" selected='selected'>
							GA4 Analytics
					</option>
			</select>
			<br/><small>Configured via theme files</small>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	private function normilize( $str ) {
		return trim( preg_replace( '/\s+/', ' ', $str ) );
	}
}
