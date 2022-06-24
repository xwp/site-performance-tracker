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

	public function test_get_hardcoded_tracker_config__empty_theme() {
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

		$this->assertSame( array(), $field['args'] );
	}

	public function test_settings_init__field_gtag_id() {
		global $wp_settings_fields;

		$this->assertFalse(
			isset( $wp_settings_fields['pluginPage'] )
		);

		$this->settings->settings_init();

		$field_name = 'analytics_id';
		$this->assertTrue( isset( $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ] ) );
		$field = $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ];

		$this->assertSame( $field_name, $field['id'] );
		$this->assertSame( 'Analytics ID', $field['title'] );

		$this->assertSame( array( $this->settings->fields[1], 'analytics_id_render' ), $field['callback'] );

		$this->assertSame( array(), $field['args'] );
	}

	public function test_settings_init__field_measurementVersion() {
		global $wp_settings_fields;

		$this->assertFalse(
			isset( $wp_settings_fields['pluginPage'] )
		);

		$this->settings->settings_init();

		$field_name = 'measurement_version_dimension';
		$this->assertTrue( isset( $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ] ) );
		$field = $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ];

		$this->assertSame( $field_name, $field['id'] );
		$this->assertSame( 'Measurement Version Dimension', $field['title'] );

		$this->assertSame( array( $this->settings, 'measurement_version_dimension_render' ), $field['callback'] );

		$this->assertSame( array(), $field['args'] );
	}

	public function test_settings_init__field_eventMeta() {
		global $wp_settings_fields;

		$this->assertFalse(
			isset( $wp_settings_fields['pluginPage'] )
		);

		$this->settings->settings_init();

		$field_name = 'event_meta_dimension';
		$this->assertTrue( isset( $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ] ) );
		$field = $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ];

		$this->assertSame( $field_name, $field['id'] );
		$this->assertSame( 'Event Meta Dimension', $field['title'] );

		$this->assertSame( array( $this->settings, 'event_meta_dimension_render' ), $field['callback'] );

		$this->assertSame( array(), $field['args'] );
	}

	public function test_settings_init__field_eventDebug() {
		global $wp_settings_fields;

		$this->assertFalse(
			isset( $wp_settings_fields['pluginPage'] )
		);

		$this->settings->settings_init();

		$field_name = 'event_debug_dimension';
		$this->assertTrue( isset( $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ] ) );
		$field = $wp_settings_fields['pluginPage']['spt_pluginPage_section'][ $field_name ];

		$this->assertSame( $field_name, $field['id'] );
		$this->assertSame( 'Event Debug Dimension', $field['title'] );

		$this->assertSame( array( $this->settings, 'event_debug_dimension_render' ), $field['callback'] );

		$this->assertSame( array(), $field['args'] );
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

		$this->assertSame( array( $this->settings, 'web_vitals_tracking_ratio_render' ), $field['callback'] );

		$this->assertSame( array(), $field['args'] );
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

	public function test_analytics_types_render__set_ga_id() {
		$this->settings->settings_init();

		add_option( 'spt_settings', array( 'analytics_types' => 'ga_id' ) );

		ob_start();
		$this->settings->fields[0]->render();
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

	public function test_analytics_types_render__set_gtm() {
		$this->settings->settings_init();
		add_option( 'spt_settings', array( 'analytics_types' => 'gtm' ) );

		ob_start();
		$this->settings->fields[0]->render();
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

	public function test_analytics_types_render__set_ga4() {
		$this->settings->settings_init();
		add_option( 'spt_settings', array( 'analytics_types' => 'ga4' ) );

		ob_start();
		$this->settings->fields[0]->render();
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

	public function test_analytics_types_render__theme_ga_id() {
		global $tracker_config;
		$tracker_config['ga_id'] = 'test_ga_id';

		$this->settings->settings_init();

		ob_start();
		$this->settings->fields[0]->render();
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

	public function test_analytics_types_render__theme_gtag_id() {
		global $tracker_config;
		$tracker_config['gtag_id'] = 'test_gtag_id';

		$this->settings->settings_init();

		ob_start();
		$this->settings->fields[0]->render();
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

	public function test_analytics_types_render__theme_ga4_id() {
		global $tracker_config;
		$tracker_config['ga4_id'] = 'test_ga4_id';

		$this->settings->settings_init();

		ob_start();
		$this->settings->fields[0]->render();
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

	public function test_analytics_id_render__empty_options() {
		$this->settings->settings_init();

		ob_start();
		$this->settings->fields[1]->analytics_id_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='text' name='spt_settings[gtag_id]' pattern="[UA|GTM|G]+-[A-Z|0-9]+.*"
				value='' placeholder="UA-XXXXXXXX-Y"
				aria-label="analytics id"  required>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_analytics_id_render__set_ga_id() {
		$this->settings->settings_init();
		add_option( 'spt_settings', array( 'ga_id' => 'test_ga_id' ) );

		ob_start();
		$this->settings->fields[1]->analytics_id_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='text' name='spt_settings[gtag_id]' pattern="[UA|GTM|G]+-[A-Z|0-9]+.*"
				value='test_ga_id' placeholder="UA-XXXXXXXX-Y"
				aria-label="analytics id"  required>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_analytics_id_render__set_gtag_id() {
		$this->settings->settings_init();
		add_option( 'spt_settings', array( 'gtag_id' => 'test_gtag_id' ) );

		ob_start();
		$this->settings->fields[1]->analytics_id_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='text' name='spt_settings[gtag_id]' pattern="[UA|GTM|G]+-[A-Z|0-9]+.*"
				value='test_gtag_id' placeholder="UA-XXXXXXXX-Y"
				aria-label="analytics id"  required>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_analytics_id_render__theme_ga_id() {
		global $tracker_config;
		$tracker_config['ga_id'] = 'test_ga_id';

		$this->settings->settings_init();

		ob_start();
		$this->settings->fields[1]->analytics_id_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='text' name='spt_settings[gtag_id]' pattern="[UA|GTM|G]+-[A-Z|0-9]+.*"
				value='test_ga_id' placeholder="UA-XXXXXXXX-Y"
				aria-label="analytics id" readonly required>
			<br/><small>Configured via theme files</small>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_analytics_id_render__theme_gtag_id() {
		global $tracker_config;
		$tracker_config['gtag_id'] = 'test_gtag_id';

		$this->settings->settings_init();

		ob_start();
		$this->settings->fields[1]->analytics_id_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='text' name='spt_settings[gtag_id]' pattern="[UA|GTM|G]+-[A-Z|0-9]+.*"
				value='test_gtag_id' placeholder="UA-XXXXXXXX-Y"
				aria-label="analytics id" readonly required>
			<br/><small>Configured via theme files</small>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_analytics_id_render__theme_ga4_id() {
		global $tracker_config;
		$tracker_config['ga4_id'] = 'test_ga4_id';

		$this->settings->settings_init();

		ob_start();
		$this->settings->fields[1]->analytics_id_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='text' name='spt_settings[gtag_id]' pattern="[UA|GTM|G]+-[A-Z|0-9]+.*"
				value='test_ga4_id' placeholder="UA-XXXXXXXX-Y"
				aria-label="analytics id" readonly required>
			<br/><small>Configured via theme files</small>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_measurement_version_dimension_render__empty_options() {
		ob_start();
		$this->settings->measurement_version_dimension_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='text' name='spt_settings[measurementVersion]' pattern="[dimension]+[0-9]{1,2}"
				value='' placeholder="dimension1"
				aria-label="measurement version dimension" >
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_measurement_version_dimension_render__set_measurementVersion() {
		add_option( 'spt_settings', array( 'measurementVersion' => 'dimension1' ) );

		ob_start();
		$this->settings->measurement_version_dimension_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='text' name='spt_settings[measurementVersion]' pattern="[dimension]+[0-9]{1,2}"
				value='dimension1' placeholder="dimension1"
				aria-label="measurement version dimension" >
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_measurement_version_dimension_render__theme_measurementVersion() {
		global $tracker_config;
		$tracker_config['measurementVersion'] = 'dimension11';

		ob_start();
		$this->settings->measurement_version_dimension_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='text' name='spt_settings[measurementVersion]' pattern="[dimension]+[0-9]{1,2}"
				value='dimension11' placeholder="dimension1"
				aria-label="measurement version dimension" readonly>
			<br/><small>Configured via theme files</small>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_event_meta_dimension_render__empty_options() {
		ob_start();
		$this->settings->event_meta_dimension_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='text' name='spt_settings[eventMeta]' pattern="[dimension]+[0-9]{1,2}"
				value='' placeholder="dimension2"
				aria-label="event meta dimension" >
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_event_meta_dimension_render__set_eventMeta() {
		add_option( 'spt_settings', array( 'eventMeta' => 'dimension2' ) );

		ob_start();
		$this->settings->event_meta_dimension_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='text' name='spt_settings[eventMeta]' pattern="[dimension]+[0-9]{1,2}"
				value='dimension2' placeholder="dimension2"
				aria-label="event meta dimension" >
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_event_meta_dimension_render__theme_eventMeta() {
		global $tracker_config;
		$tracker_config['eventMeta'] = 'dimension22';

		ob_start();
		$this->settings->event_meta_dimension_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='text' name='spt_settings[eventMeta]' pattern="[dimension]+[0-9]{1,2}"
				value='dimension22' placeholder="dimension2"
				aria-label="event meta dimension" readonly>
			<br/><small>Configured via theme files</small>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_event_debug_dimension_render__empty_options() {
		ob_start();
		$this->settings->event_debug_dimension_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='text' name='spt_settings[eventDebug]' pattern="[dimension]+[0-9]{1,2}"
				value='' placeholder="dimension3"
				aria-label="event debug dimension" >
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_event_debug_dimension_render__set_eventDebug() {
		add_option( 'spt_settings', array( 'eventDebug' => 'dimension3' ) );

		ob_start();
		$this->settings->event_debug_dimension_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='text' name='spt_settings[eventDebug]' pattern="[dimension]+[0-9]{1,2}"
				value='dimension3' placeholder="dimension3"
				aria-label="event debug dimension" >
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_event_debug_dimension_render__theme_eventDebug() {
		global $tracker_config;
		$tracker_config['eventDebug'] = 'dimension33';

		ob_start();
		$this->settings->event_debug_dimension_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='text' name='spt_settings[eventDebug]' pattern="[dimension]+[0-9]{1,2}"
				value='dimension33' placeholder="dimension3"
				aria-label="event debug dimension" readonly>
			<br/><small>Configured via theme files</small>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_web_vitals_tracking_ratio_render__empty_options() {
		ob_start();
		$this->settings->web_vitals_tracking_ratio_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='number' name='spt_settings[web_vitals_tracking_ratio]' step='0.01' min='0.01' max='1'
				value='1'
				placeholder="Enter between 0 > 1" aria-label="web vitals tracking ratio"
			>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_web_vitals_tracking_ratio_render__set_web_vitals_tracking_ratio() {
		add_option( 'spt_settings', array( 'web_vitals_tracking_ratio' => 0.05 ) );

		ob_start();
		$this->settings->web_vitals_tracking_ratio_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='number' name='spt_settings[web_vitals_tracking_ratio]' step='0.01' min='0.01' max='1'
				value='0.05'
				placeholder="Enter between 0 > 1" aria-label="web vitals tracking ratio"
			>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_web_vitals_tracking_ratio_render__theme_web_vitals_tracking_ratio() {
		add_option( 'spt_settings', array( 'web_vitals_tracking_ratio' => 0.05 ) );
		global $tracker_config;
		$tracker_config['web_vitals_tracking_ratio'] = '0.33';

		ob_start();
		$this->settings->web_vitals_tracking_ratio_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='number' name='spt_settings[web_vitals_tracking_ratio]' step='0.01' min='0.01' max='1'
				value='0.33'
				placeholder="Enter between 0 > 1" aria-label="web vitals tracking ratio"
				readonly
			>
			<br/><small>Configured via theme files</small>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	public function test_web_vitals_tracking_ratio_render__apply_filters() {
		add_filter(
			'site_performance_tracker_chance',
			function () {
				return 0.77;
			}
		);

		ob_start();
		$this->settings->web_vitals_tracking_ratio_render();
		$result = ob_get_contents();
		ob_end_clean();

		$expected_html = <<<EOD
			<input type='number' name='spt_settings[web_vitals_tracking_ratio]' step='0.01' min='0.01' max='1'
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

		// replace nonce with a constant
		$result = preg_replace(
			'#name="_wpnonce" value=".*"#U',
			'name="_wpnonce" value="test nonce"',
			$result
		);

		$expected_html = <<<EOD
			<form action='options.php' method='post'>
				<h1>Site Performance Tracker Settings</h1>

				<input type='hidden' name='option_page' value='pluginPage' /><input type="hidden" name="action" value="update" /><input type="hidden" id="_wpnonce" name="_wpnonce" value="test nonce" /><input type="hidden" name="_wp_http_referer" value="" /><p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  /></p>          </form>

				<div class="content">
					<p>
						You can get the <a href="https://web-vitals-report.web.app/" target="_blank">Web Vitals Report here</a>. Ensure that the date range starts from when the Web Vitals data is being sent.                 </p>
				</div>
EOD;

		$this->assertSameIgnoreEOL( $this->normilize( $expected_html ), $this->normilize( $result ) );
	}

	private function normilize( $str ) {
		return trim( preg_replace( '/\s+/', ' ', $str ) );
	}
}
