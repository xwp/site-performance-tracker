<?php
/**
 * Generate the plugin settings UI.
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Class Plugin_Settings
 */
class Plugin_Settings {

	/**
	 * PerformanceObserver default chance of sending performance metrics to analytics.
	 *
	 * Set to 100% which sends analytics on every request.
	 *
	 * @var int
	 */
	const TRACKING_DEFAULT_CHANCE = 1;

	const SETTINGS_PAGE_ID = 'site_performance_tracker';

	/**
	 * Option key for web vitals settings store.
	 *
	 * @var string
	 */
	const SETTINGS_VITALS_SECTION_ID = 'spt_settings';

	const SETTINGS_VITALS_FIELD_GTAG_ID = 'gtag_id';

	const SETTINGS_VITALS_FIELD_MEASUREMENT_VERSION = 'measurementVersion';

	const SETTINGS_VITALS_FIELD_EVENT_META = 'eventMeta';

	const SETTINGS_VITALS_FIELD_EVENT_DEBUG = 'eventDebug';

	const SETTINGS_VITALS_FIELD_TRACKING_RATIO = 'web_vitals_tracking_ratio';

	/**
	 * Key used for theme supports config.
	 *
	 * @var string
	 */
	const THEME_SUPPORTS_VITALS_KEY = 'site_performance_tracker_vitals';

	protected $option_page_id;

	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		// Setup the get_theme_support() fetcher for our config.
		$this->theme_settings = new Theme_Support_Setting( self::THEME_SUPPORTS_VITALS_KEY );

		$this->web_vitals_settings = array(
			self::SETTINGS_VITALS_FIELD_GTAG_ID => array(
				'title' => __( 'Google Analytics ID', 'site-performance-tracker' ),
				'callback' => array( $this, 'render_web_vitals_field_gtag_id' ),
				'setting' => new Setting( 'sanitize_text_field', $this->get_theme_gtag_id_setting() )
			),
			self::SETTINGS_VITALS_FIELD_MEASUREMENT_VERSION => array(
				'title' => __( 'Measurement Version Dimension', 'site-performance-tracker' ),
				'callback' => array( $this, 'measurement_version_dimension_render' ),
				'setting' => new Setting( 'sanitize_text_field', $this->theme_settings->get( self::SETTINGS_VITALS_FIELD_MEASUREMENT_VERSION ) ),
			),
			self::SETTINGS_VITALS_FIELD_EVENT_META => array(
				'title' => __( 'Event Meta Dimension', 'site-performance-tracker' ),
				'callback' => array( $this, 'event_meta_dimension_render' ),
				'setting' => new Setting( 'sanitize_text_field', $this->theme_settings->get( self::SETTINGS_VITALS_FIELD_EVENT_META ) )
			),
			self::SETTINGS_VITALS_FIELD_EVENT_DEBUG => array(
				'title' => __( 'Event Debug Dimension', 'site-performance-tracker' ),
				'callback' => array( $this, 'event_debug_dimension_render' ),
				'setting' => new Setting( 'sanitize_text_field', $this->theme_settings->get( self::SETTINGS_VITALS_FIELD_EVENT_DEBUG ) ),
			),
			self::SETTINGS_VITALS_FIELD_TRACKING_RATIO => array(
				'title' => __( 'Web Vitals Tracking Ratio', 'site-performance-tracker' ),
				'callback' => array( $this, 'web_vitals_tracking_ratio_render' ),
				'setting' => new Setting( 'floatval', self::TRACKING_DEFAULT_CHANCE ),
			),
		);
	}

	public function init() {
		$this->fetch_settings();

		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_settings_styles' ) );
	}

	/**
	 * Populate the settings from the database.
	 *
	 * @return void
	 */
	protected function fetch_settings() {
		$options = (array) get_option( self::SETTINGS_VITALS_SECTION_ID, array() );

		foreach ( $this->web_vitals_settings as $key => $setting ) {
			if ( isset( $options[ $key ] ) ) {
				$setting['setting']->set( $options[ $key ] );
			}
		}
	}

	public function get_theme_gtag_id_setting() {
		// Collect all the possible theme support param values that reference the same setting.
		$gtag_id_values = array_filter(
			array(
				$this->theme_settings->get( 'ga_id' ),
				$this->theme_settings->get( 'gtag_id' ),
				$this->theme_settings->get( 'ga4_id' )
			)
		);

		// Get the last non-empty value.
		return array_pop( $gtag_id_values );
	}

	/**
	 * Enqueue styles for the UI.
	 */
	public function enqueue_settings_styles( $current_page_id ) {
		if ( $current_page_id !== $this->option_page_id ) {
			return;
		}

		wp_enqueue_style(
			'site-performance-tracker-styles',
			$this->plugin->uri_to( 'css/styles.css' ),
			array(),
			null,
			true
		);
	}

	/**
	 * Add tracker as a settings menu item.
	 */
	public function register_settings_page() {
		$this->option_page_id = add_options_page(
			__( 'Site Performance Tracker', 'site-performance-tracker' ), // Page head title.
			__( 'Site Performance Tracker', 'site-performance-tracker' ), // Menu title.
			'manage_options', // Capability.
			self::SETTINGS_PAGE_ID, // Menu slug.
			array( $this, 'render_settings_page' ) // Callback.
		);
	}

	/**
	 * Initialize tracker settings by registering it and adding
	 * sections and fields.
	 */
	public function register_settings() {
		// Each section needs it's own setting for automatic saving to work.
		register_setting(
			self::SETTINGS_PAGE_ID,
			self::SETTINGS_VITALS_SECTION_ID,
			array(
				'type' => 'array',
				'default' => array(),
				'sanitize_callback' => array( $this, 'save_settings' ),
			)
		);

		// Add the Web Vitals Section.
		add_settings_section(
			self::SETTINGS_VITALS_SECTION_ID,
			__( 'Web Vitals Tracking', 'site-performance-tracker' ),
			array( $this, 'web_vitals_section_header' ),
			self::SETTINGS_PAGE_ID
		);

		// Add fields.
		foreach ( $this->web_vitals_settings as $field_id => $setting ) {
			// Add only fields that need UI settings.
			if ( ! empty( $setting['title'] ) && ! empty( $setting['callback'] ) ) {
				add_settings_field(
					$field_id,
					$setting['title'],
					$setting['callback'],
					self::SETTINGS_PAGE_ID,
					self::SETTINGS_VITALS_SECTION_ID
				);
			}
		}
	}

	public function save_settings( $new_settings ) {
		$settings = array();

		foreach ( $this->web_vitals_settings as $field_id => $field_settings ) {
			// Set the updated value, if received.
			if ( isset( $new_settings[ $field_id ] ) ) {
				$field_settings['setting']->set( $new_settings[ $field_id ] );
			}

			$settings[ $field_id ] = $field_settings['setting']->get();
		}

		return $settings;
	}

	protected function get_web_vitals_setting_field_name( $key ) {
		return sprintf(
			'%s[%s]',
			self::SETTINGS_VITALS_SECTION_ID,
			$key
		);
	}

	protected function get_web_vitals_field_setting( $field_id ) {
		return $this->web_vitals_settings[ $field_id ][ 'setting' ];
	}

	public function get_web_vitals_gtag_id() {
		return $this->get_web_vitals_field_setting( self::SETTINGS_VITALS_FIELD_GTAG_ID )->get();
	}

	public function get_web_vitals_measurement_version() {
		return $this->get_web_vitals_field_setting( self::SETTINGS_VITALS_FIELD_MEASUREMENT_VERSION )->get();
	}

	/**
	 * Render Analytics ID form input.
	 */
	public function render_web_vitals_field_gtag_id() {
		$value = $this->get_web_vitals_gtag_id();
		$theme_setting_value = $this->get_theme_gtag_id_setting();

		if ( empty( $value ) ) {
			$value = $theme_setting_value;
		}

		?>
		<input
			type="text"
			name="<?php echo esc_attr( $this->get_web_vitals_setting_field_name( self::SETTINGS_VITALS_FIELD_GTAG_ID ) ) ?>"
			pattern="[UA|GTM|G]+-[A-Z|0-9]+.*"
			value="<?php echo esc_attr( $value ); ?>"
			placeholder="UA-XXXXXXXX-Y"
			required
			<?php disabled( ! empty( $theme_setting_value ) ); ?>
		/>
		<?php
	}

	/**
	 * Render Measurement Version Dimension form input.
	 */
	function measurement_version_dimension_render() {
		$value = $this->get_web_vitals_measurement_version();
		$theme_setting_value = $this->theme_settings->get( self::SETTINGS_VITALS_FIELD_MEASUREMENT_VERSION );

		if ( empty( $value ) ) {
			$value = $theme_setting_value;
		}
		?>
		<input
			type="text"
			name="<?php echo esc_attr( $this->get_web_vitals_setting_field_name( self::SETTINGS_VITALS_FIELD_MEASUREMENT_VERSION ) ) ?>"
			pattern="[dimension]+[0-9]{1,2}"
			value="<?php echo esc_attr( $value ); ?>"
			placeholder="dimension1"
			<?php disabled( ! empty( $theme_setting_value ) ); ?>
		/>
		<?php
	}

	/**
	 * Render Event Meta Dimension form input.
	 */
	function event_meta_dimension_render() {
		$value = $this->get_web_vitals_dimension_event_meta();
		$theme_setting_value = $this->theme_settings->get( self::SETTINGS_VITALS_FIELD_MEASUREMENT_VERSION );

		if ( empty( $value ) ) {
			$value = $theme_setting_value;
		}
		?>
		<input
			type="text"
			name="<?php echo esc_attr( $this->get_web_vitals_setting_field_name( self::SETTINGS_VITALS_FIELD_MEASUREMENT_VERSION ) ) ?>"
			pattern="[dimension]+[0-9]{1,2}"
			value="<?php echo esc_attr( $value ); ?>"
			placeholder="dimension2"
			<?php disabled( ! empty( $theme_setting_value ) ); ?>
		/>
		<?php

		$options = spt_get_settings();
		global $tracker_config;
		$set = false;
		if ( isset( $tracker_config['eventMeta'] ) ) {
			$options['eventMeta'] = $tracker_config['eventMeta'];
			$set = true;
		}
		?>
		<input type='text' name='spt_settings[eventMeta]' pattern="" value='<?php echo esc_attr( $options['eventMeta'] ); ?>' placeholder="dimension2" aria-label="event meta dimension" <?php print_readonly( 'eventMeta' ); ?>>
		<?php
		if ( $set ) {
			?>
			<br /><small><?php esc_html_e( 'Configured via theme files', 'site-performance-tracker' ); ?></small>
			<?php
		}
	}

	/**
	 * Render Event Debug Dimension form input.
	 */
	function event_debug_dimension_render() {
		$options = spt_get_settings();
		global $tracker_config;
		$set = false;
		if ( isset( $tracker_config['eventDebug'] ) ) {
			$options['eventDebug'] = $tracker_config['eventDebug'];
			$set = true;
		}
		?>
		<input type='text' name='spt_settings[eventDebug]' pattern="[dimension]+[0-9]{1,2}" value='<?php echo esc_attr( $options['eventDebug'] ); ?>' placeholder="dimension3" aria-label="event debug dimension" <?php print_readonly( 'eventDebug' ); ?>>
		<?php
		if ( $set ) {
			?>
			<br /><small><?php esc_html_e( 'Configured via theme files', 'site-performance-tracker' ); ?></small>
			<?php
		}
	}

	/**
	 * Render Tracking Ratio form input.
	 */
	function web_vitals_tracking_ratio_render() {
		$options = spt_get_settings();
		global $tracker_config;
		$set = false;
		if ( isset( $tracker_config['web_vitals_tracking_ratio'] ) ) {
			$options['web_vitals_tracking_ratio'] = $tracker_config['web_vitals_tracking_ratio'];
			$set = true;
		}
		?>
		<input type='number' name='spt_settings[web_vitals_tracking_ratio]' step='0.01' min='0.01' max='1' value='<?php echo esc_attr( $options['web_vitals_tracking_ratio'] ); ?>' placeholder="Enter between 0 > 1" aria-label="web vitals tracking ratio" <?php print_readonly( 'web_vitals_tracking_ratio' ); ?>>
		<?php
		if ( $set ) {
			?>
			<br /><small><?php esc_html_e( 'Configured via theme files', 'site-performance-tracker' ); ?></small>
			<?php
		}
	}

	/**
	 * Echo section callback text.
	 */
	public function web_vitals_section_header() {
		?>
		<p>
			<a class="button" href="https://web-vitals-report.web.app/" target="_blank"><?php esc_html_e( 'View Web Vitals Report', 'site-performance-tracker' ); ?></a>
			<?php _e( 'Ensure that the date range starts from when the Web Vitals data is being sent.', 'site-performance-tracker' ); ?>
		</p>
		<?php
	}

	public function render_settings_page() {
		?>
		<div class="wrap">
			<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
				<h1><?php esc_html_e( 'Site Performance Tracker', 'site-performance-tracker' ); ?></h1>
				<?php
				settings_fields( self::SETTINGS_PAGE_ID );
				do_settings_sections( self::SETTINGS_PAGE_ID );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

}

