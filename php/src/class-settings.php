<?php
/**
 * Create the Settings Page.
 *
 * This is where all the settings for the plugin can be added/edited
 * through an interface.
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
	 * Plugin Settings page ID.
	 *
	 * @var string
	 */
	const PAGE_ID = 'pluginPage';

	/**
	 * Plugin Settings section ID.
	 *
	 * @var string
	 */
	const SECTION_ID = 'spt_pluginPage_section';

	/**
	 * Settings option name.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'spt_settings';

	/**
	 * Hardcoded tracker config feature name.
	 *
	 * @var string
	 */
	const HARDCODED_TRACKER_CONFIG_FEATURE = 'site_performance_tracker_vitals';

	/**
	 * Option web_vitals_tracking_ratio name
	 *
	 * @var string
	 */
	const OPTION_WEB_VITALS_TRACKING_RATIO = 'web_vitals_tracking_ratio';

	/**
	 * All setting fields
	 *
	 * @var array
	 */
	public $fields;

	/**
	 * Settings constructor.
	 */
	public function __construct() {
		$this->fields = array(
			new AnalyticsTypesField(),
			new AnalyticsIdField(),
			new MeasurementVersionDimensionField(),
			new EventMetaDimensionField(),
			new EventDebugDimensionField(),
		);
	}

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
		add_action( 'after_setup_theme', array( $this, 'get_hardcoded_tracker_config' ), PHP_INT_MAX );

		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
	}

	/**
	 * Get options set via add_theme_support.
	 */
	public function get_hardcoded_tracker_config() {
		global $tracker_config;
		$tracker_config = isset( get_theme_support( self::HARDCODED_TRACKER_CONFIG_FEATURE )[0] ) ? get_theme_support( self::HARDCODED_TRACKER_CONFIG_FEATURE )[0] : array();
	}

	/**
	 * Add tracker as a settings menu item.
	 *
	 * @return string|false The option page's hook_suffix, or false if the user does not have the capability required.
	 */
	public function add_admin_menu() {
		return add_options_page(
			'Site Performance Tracker',
			'Site Performance Tracker',
			'manage_options',
			'site_performance_tracker',
			array(
				$this,
				'render_settings_page',
			)
		);
	}

	/**
	 * Initialize tracker settings by registering it and adding
	 * sections and fields.
	 */
	public function settings_init() {
		register_setting( self::PAGE_ID, self::OPTION_NAME );

		add_settings_section(
			self::SECTION_ID,
			null,
			array( $this, 'settings_section_callback' ),
			self::PAGE_ID
		);

		foreach ( $this->fields as $field ) {
			$field->init( $this, self::PAGE_ID, self::SECTION_ID );
		}

		add_settings_field(
			self::OPTION_WEB_VITALS_TRACKING_RATIO,
			__( 'Web Vitals Tracking Ratio', 'site-performance-tracker' ),
			array( $this, 'web_vitals_tracking_ratio_render' ),
			self::PAGE_ID,
			self::SECTION_ID
		);
	}

	/**
	 * Echo section callback text.
	 */
	public function settings_section_callback() {
		echo esc_html( __( 'Update Site Performance Tracker settings', 'site-performance-tracker' ) );
	}

	/**
	 * Render Tracking Ratio form input.
	 */
	public function web_vitals_tracking_ratio_render() {
		$options = $this->get_settings();
		global $tracker_config;
		$set = false;
		if ( isset( $tracker_config[ self::OPTION_WEB_VITALS_TRACKING_RATIO ] ) ) {
			$options[ self::OPTION_WEB_VITALS_TRACKING_RATIO ] = $tracker_config[ self::OPTION_WEB_VITALS_TRACKING_RATIO ];
			$set                                  = true;
		}
		if ( has_filter( 'site_performance_tracker_chance' ) ) {
			$options[ self::OPTION_WEB_VITALS_TRACKING_RATIO ] = apply_filters( 'site_performance_tracker_chance', 1 );
			$set                                  = true;
		}
		?>
		<input type='number' name='spt_settings[<?php echo esc_attr( self::OPTION_WEB_VITALS_TRACKING_RATIO ); ?>]' step='0.01' min='0.01' max='1'
			   value='<?php echo esc_attr( $options[ self::OPTION_WEB_VITALS_TRACKING_RATIO ] ); ?>'
			   placeholder="Enter between 0 > 1" aria-label="web vitals tracking ratio"
				<?php if ( $set ) { ?>
					readonly
				<?php } ?>>
		<?php

		$this->show_theme_warning( $set );
	}

	/**
	 * Create and Output the settings page.
	 */
	public function render_settings_page() {
		?>
		<form action='options.php' method='post'>
			<h1><?php echo esc_html( __( 'Site Performance Tracker Settings', 'site-performance-tracker' ) ); ?></h1>

			<?php
			settings_fields( self::PAGE_ID );
			do_settings_sections( self::PAGE_ID );
			submit_button();
			?>
		</form>

		<div class="content">
			<p>
				<?php _e( 'You can get the <a href="https://web-vitals-report.web.app/" target="_blank">Web Vitals Report here</a>. Ensure that the date range starts from when the Web Vitals data is being sent.', 'site-performance-tracker' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Get available trackers and print 'readonly' in the form inputs if the setting is defined in theme files
	 *
	 * @param string $prop_name The property name to be tested.
	 */
	public function print_readonly( $prop_name ) {
		global $tracker_config;
		if ( isset( $tracker_config[ $prop_name ] ) ) {
			echo esc_attr( 'readonly' );
		}
	}

	/**
	 * Returns the plugin settings.
	 */
	public function get_settings() {
		$options = get_option( self::OPTION_NAME, array() );

		return array_merge(
			array(
				AnalyticsTypesField::OPTION_ANALYTICS_TYPES                     => '',
				AnalyticsIdField::OPTION_TAG_ID                                 => '',
				MeasurementVersionDimensionField::OPTION_MEASUREMENT_VERSION    => '',
				EventMetaDimensionField::OPTION_EVENT_META                      => '',
				EventDebugDimensionField::OPTION_EVENT_DEBUG                    => '',
				self::OPTION_WEB_VITALS_TRACKING_RATIO                          => Plugin::TRACKING_DEFAULT_CHANCE,
			),
			$options
		);
	}

	/**
	 * Show warning that configured via theme files
	 *
	 * @param bool $show indicate if message should be displayed.
	 */
	public function show_theme_warning( $show ) {
		if ( $show ) {
			?>
			<br/><small><?php esc_html_e( 'Configured via theme files', 'site-performance-tracker' ); ?></small>
			<?php
		}
	}

	/**
	 * Output dimension option field
	 *
	 * @param string $option_name option name to render.
	 * @param string $placeholder input field placeholder.
	 * @param string $aria_label input field area-label.
	 */
	public function render_dimention_option( $option_name, $placeholder, $aria_label ) {
		$options = $this->get_settings();
		global $tracker_config;
		$set = false;
		if ( isset( $tracker_config[ $option_name ] ) ) {
			$options[ $option_name ] = $tracker_config[ $option_name ];
			$set                   = true;
		}
		?>
		<input type='text' name='spt_settings[<?php echo esc_attr( $option_name ); ?>]' pattern="[dimension]+[0-9]{1,2}"
			   value='<?php echo esc_attr( $options[ $option_name ] ); ?>' placeholder="<?php echo esc_attr( $placeholder ); ?>"
			   aria-label="<?php echo esc_attr( $aria_label ); ?>" <?php $this->print_readonly( $option_name ); ?>>
		<?php

		$this->show_theme_warning( $set );
	}
}
