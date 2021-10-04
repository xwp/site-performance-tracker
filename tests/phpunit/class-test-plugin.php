<?php

class Test_Plugin extends WP_UnitTestCase {

	public function test_wordpress_and_plugin_are_loaded() {
		$this->assertTrue( function_exists( 'do_action' ), 'WP is present' );
		$this->assertTrue( function_exists( 'xwp_site_performance_tracker' ), 'Plugin bootstrap function is present' );
	}

}
