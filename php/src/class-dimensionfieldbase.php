<?php
/**
 * Define Dimension Field Base interfase.
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Base class for Dimention field.
 */
abstract class DimensionFieldBase extends FieldBase {
	/**
	 * Get option name.
	 */
	abstract protected function get_option_name();

	/**
	 * Get field placeholder.
	 */
	abstract protected function get_placeholder();

	/**
	 * Get field aria label.
	 */
	abstract protected function get_aria_label();

	/**
	 * Render field.
	 */
	public function render() {
		$option_name = $this->get_option_name();

		$options = $this->settings->get_settings();
		$hardcoded_tracker_config = $this->settings->get_hardcoded_tracker_config();
		$display_theme_override_warning = false;
		if ( isset( $hardcoded_tracker_config[ $option_name ] ) ) {
			$options[ $option_name ] = $hardcoded_tracker_config[ $option_name ];
			$display_theme_override_warning                   = true;
		}
		?>
		<input type='text' name='spt_settings[<?php echo esc_attr( $option_name ); ?>]' pattern="[dimension]+[0-9]{1,2}"
			   value='<?php echo esc_attr( $options[ $option_name ] ); ?>' placeholder="<?php echo esc_attr( $this->get_placeholder() ); ?>"
			   aria-label="<?php echo esc_attr( $this->get_aria_label() ); ?>" <?php $this->print_readonly( $option_name ); ?>>
		<?php

		$this->show_theme_warning( $display_theme_override_warning );
	}

	/**
	 * Get current field classes.
	 */
	protected function get_classes() {
		return array(
			'dimension',
			$this->get_id(),
		);
	}
}
