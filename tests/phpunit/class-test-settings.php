<?php

use XWP\Site_Performance_Tracker\Settings;

class Test_Settings extends WP_UnitTestCase {

	public $settings;

	function setUp(): void {
		parent::setUp();

		$this->settings = new Settings();
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
}
