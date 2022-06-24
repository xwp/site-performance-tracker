<?php
/**
 * Define Dimension Field Base interfase
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Class DimenisonFieldBase
 */
abstract class DimenisonFieldBase extends FieldBase {
	/**
	 * Get option name
	 */
	abstract protected function get_option_name();

	/**
	 * Get field placeholder
	 */
	abstract protected function get_placeholder();

	/**
	 * Get field aria label
	 */
	abstract protected function get_aria_label();

	/**
	 * Render field.
	 */
	public function render() {
		$this->settings->render_dimention_option( $this->get_option_name(), $this->get_placeholder(), $this->get_aria_label() );
	}
}
