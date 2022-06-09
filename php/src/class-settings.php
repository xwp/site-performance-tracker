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
	}

	/**
	 * Add tracker as a settings menu item.
	 */
	public function add_admin_menu() {
		add_options_page( 'Site Performance Tracker', 'Site Performance Tracker', 'manage_options', 'site_performance_tracker', 'spt_options_page' );
	}
}
