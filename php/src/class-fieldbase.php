<?php
/**
 * Define Field Base interfase.
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Defines base class for fields.
 */
abstract class FieldBase {
	/**
	 * Setting that current fields belong to.
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
			$section_id,
			[
				'class' => implode( ' ', $this->get_classes() ),
			]
		);

		$this->settings = $settings;
	}

	/**
	 * Get current field id.
	 */
	abstract protected function get_id();

	/**
	 * Get current field title.
	 */
	abstract protected function get_title();

	/**
	 * Get current field classes.
	 *
	 * @return array
	 */
	protected function get_classes() {
		return [
			$this->get_id(),
		];
	}

	/**
	 * Render form input.
	 */
	abstract public function render();

	/**
	 * Get available trackers and print 'readonly' in the form inputs if the setting is defined in theme files.
	 *
	 * @param string $prop_name The property name to be tested.
	 */
	protected function print_readonly( $prop_name ) {
		$hardcoded_tracker_config = $this->settings->get_hardcoded_tracker_config();

		if ( isset( $hardcoded_tracker_config[ $prop_name ] ) ) {
			echo esc_attr( 'readonly' );
		}
	}

	/**
	 * Show warning that configured via theme files.
	 *
	 * @param bool $show indicate if message should be displayed.
	 */
	protected function show_theme_warning( $show ) {
		if ( $show ) {
			?>
			<br/><small><?php esc_html_e( 'Configured via theme files', 'site-performance-tracker' ); ?></small>
			<?php
		}
	}
}
