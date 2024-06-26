<?php

use XWP\Site_Performance_Tracker\Settings;

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

		global $wp_settings_sections;
		if ( isset( $wp_settings_sections['pluginPage'] ) ) {
			unset( $wp_settings_sections['pluginPage'] );
		}

		global $wp_settings_fields;
		if ( isset( $wp_settings_fields['pluginPage'] ) ) {
			unset( $wp_settings_fields['pluginPage'] );
		}

		remove_theme_support( Settings::HARDCODED_TRACKER_CONFIG_FEATURE );
	}

	public function test_init_register_actions() {
		$this->assertFalse( has_action( 'admin_menu', array( $this->settings, 'add_admin_menu' ) ) );
		$this->assertFalse( has_action( 'admin_init', array( $this->settings, 'settings_init' ) ) );

		$this->settings->init();

		$this->assertNotFalse( has_action( 'admin_menu', array( $this->settings, 'add_admin_menu' ) ) );
		$this->assertNotFalse( has_action( 'admin_init', array( $this->settings, 'settings_init' ) ) );
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

	public function test_settings_init__register_setting() {
		global $wp_registered_settings;

		$this->settings->settings_init();

		$this->assertTrue( isset( $wp_registered_settings['spt_settings'] ) );

		$registered_settings = $wp_registered_settings['spt_settings'];

		$this->assertSame( 'pluginPage', $registered_settings['group'] );
	}

	public function test_settings_init__setting_section() {
		global $wp_settings_sections;

		$this->assertFalse( isset( $wp_settings_sections['pluginPage'] ) );

		$this->settings->settings_init();

		$this->assertTrue( isset( $wp_settings_sections['pluginPage']['spt_pluginPage_section'] ) );
		$section = $wp_settings_sections['pluginPage']['spt_pluginPage_section'];
		$this->assertSame( 'spt_pluginPage_section', $section['id'] );
	}

	public function test_settings_init__field_analytics_types() {
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

		$this->assertSame( array( $this->settings->fields[0], 'render' ), $field['callback'] );

		$this->assertSame(
			array(
				'class' => 'analytics_types',
			),
			$field['args']
		);
	}

	public function test_settings_init__field_web_vitals_tracking_ratio() {
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

		$this->assertSame( array( $this->settings->fields[5], 'render' ), $field['callback'] );

		$this->assertSame(
			array(
				'class' => 'web_vitals_tracking_ratio',
			),
			$field['args']
		);
	}

	public function test_settings_section__callback() {
		ob_start();
		$this->settings->settings_section_callback();
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertSame( 'Update Site Performance Tracker settings', $result );
	}

	public function test_analytics_types_render__empty_options() {
		$this->settings->settings_init();

		ob_start();
		$this->settings->fields[0]->render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
		<select name="spt_settings[analytics_types]" required>
				<option value="ga4" >
						GA4 Analytics
				</option>
		</select>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_analytics_types_render__set_ga4() {
		$this->settings->settings_init();
		add_option( 'spt_settings', array( 'analytics_types' => 'ga4' ) );

		ob_start();
		$this->settings->fields[0]->render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<select name="spt_settings[analytics_types]" required>
					<option value="ga4" selected='selected'>
							GA4 Analytics
					</option>
			</select>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_analytics_types_render__theme_ga4_id() {
		add_theme_support( Settings::HARDCODED_TRACKER_CONFIG_FEATURE, array( 'ga4_id' => 'test_ga4_id' ) );

		$this->settings->settings_init();

		ob_start();
		$this->settings->fields[0]->render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<select name="spt_settings[analytics_types]" disabled required>
					<option value="ga4" selected='selected'>
							GA4 Analytics
					</option>
			</select>
			<br/><small>Configured via theme files</small>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_analytics_id_render__empty_options() {
		$this->settings->settings_init();

		ob_start();
		$this->settings->fields[1]->render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='text' name='spt_settings[gtag_id]' pattern="[UA|GTM|G]-[A-Z0-9](.*)?"
				value='' placeholder="UA-XXX | GTM-XXX | G-XXX"
				aria-label="analytics id"  required>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_analytics_id_render__theme_ga4_id() {
		add_theme_support( Settings::HARDCODED_TRACKER_CONFIG_FEATURE, array( 'ga4_id' => 'test_ga4_id' ) );

		$this->settings->settings_init();

		ob_start();
		$this->settings->fields[1]->render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='text' name='spt_settings[gtag_id]' pattern="[UA|GTM|G]-[A-Z0-9](.*)?"
				value='test_ga4_id' placeholder="UA-XXX | GTM-XXX | G-XXX"
				aria-label="analytics id" readonly required>
			<br/><small>Configured via theme files</small>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_web_vitals_tracking_ratio_render__empty_options() {
		$this->settings->settings_init();

		ob_start();
		$this->settings->fields[5]->render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='number' name='spt_settings[web_vitals_tracking_ratio]' step='0.001' min='0.001' max='1'
				value='1'
				placeholder="Enter between 0 > 1" aria-label="web vitals tracking ratio"
			>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_web_vitals_tracking_ratio_render__set_web_vitals_tracking_ratio() {
		$this->settings->settings_init();

		add_option( 'spt_settings', array( 'web_vitals_tracking_ratio' => 0.05 ) );

		ob_start();
		$this->settings->fields[5]->render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='number' name='spt_settings[web_vitals_tracking_ratio]' step='0.001' min='0.001' max='1'
				value='0.05'
				placeholder="Enter between 0 > 1" aria-label="web vitals tracking ratio"
			>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_web_vitals_tracking_ratio_render__theme_web_vitals_tracking_ratio() {
		add_option( 'spt_settings', array( 'web_vitals_tracking_ratio' => 0.05 ) );
		add_theme_support( Settings::HARDCODED_TRACKER_CONFIG_FEATURE, array( 'web_vitals_tracking_ratio' => '0.33' ) );

		$this->settings->settings_init();

		ob_start();
		$this->settings->fields[5]->render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='number' name='spt_settings[web_vitals_tracking_ratio]' step='0.001' min='0.001' max='1'
				value='0.33'
				placeholder="Enter between 0 > 1" aria-label="web vitals tracking ratio"
				readonly
			>
			<br/><small>Configured via theme files</small>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_web_vitals_tracking_ratio_render__apply_filters() {
		$this->settings->settings_init();

		add_filter(
			'site_performance_tracker_chance',
			function () {
				return 0.77;
			}
		);

		ob_start();
		$this->settings->fields[5]->render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='number' name='spt_settings[web_vitals_tracking_ratio]' step='0.001' min='0.001' max='1'
				value='0.77'
				placeholder="Enter between 0 > 1" aria-label="web vitals tracking ratio"
				readonly
			>
			<br/><small>Configured via theme files</small>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_render_settings_page() {
		ob_start();
		$this->settings->render_settings_page();
		$result = ob_get_contents();
		ob_end_clean();

		// Confirm the form renders, without checking for an exact match.

		// replace nonce with a constant
		$result = preg_replace(
			'#name="_wpnonce" value=".*"#U',
			'name="_wpnonce" value="test nonce"',
			$result
		);

		$expected_html = <<<EOD
			<form action='options.php' method='post'>
EOD;

		$this->assertContains( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	private function normilize( $str ) {
		return trim( preg_replace( '/\s+/', ' ', $str ) );
	}
}
