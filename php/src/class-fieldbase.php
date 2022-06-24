<?php
/**
 * Define Field Base interfase
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Class FieldBase
 */
abstract class FieldBase {
	/**
	 * Setting that current fields belong to
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * Initialize field.
	 *
	 * @param Settings $settings settings that current fields belong to.
	 * @param string   $page_id field page id.
	 * @param string   $section_id field section id.
	 */
	public function init( $settings, $page_id, $section_id ) {
		add_settings_field(
			$this->get_id(),
			$this->get_title(),
			array( $this, 'render' ),
			$page_id,
			$section_id
		);

		$this->settings = $settings;
	}

	/**
	 * Get current field id
	 */
	abstract protected function get_id();

	/**
	 * Get current field title
	 */
	abstract protected function get_title();

	/**
	 * Render form input
	 */
	abstract public function render();
}
