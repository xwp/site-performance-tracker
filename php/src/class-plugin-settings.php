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

	/**
	 * WordPress admin settings page ID.
	 *
	 * @var string
	 */
	const SETTINGS_PAGE_ID = 'site_performance_tracker';

	/**
	 * Option key for web vitals settings store.
	 *
	 * @var string
	 */
	const SETTINGS_VITALS_SECTION_ID = 'spt_settings';

	/**
	 * Key used for theme supports config.
	 *
	 * @var string
	 */
	const THEME_SUPPORTS_VITALS_KEY = 'site_performance_tracker_vitals';

	const SETTINGS_VITALS_FIELD_GTAG_ID = 'gtag_id';

	const SETTINGS_VITALS_FIELD_DIMENSION_MEASUREMENT_VERSION = 'measurementVersion';

	const SETTINGS_VITALS_FIELD_DIMENSION_EVENT_META = 'eventMeta';

	const SETTINGS_VITALS_FIELD_DIMENSION_EVENT_DEBUG = 'eventDebug';

	const SETTINGS_VITALS_FIELD_TRACKING_RATIO = 'web_vitals_tracking_ratio';

	/**
	 * Option page ID.
	 *
	 * @var string
	 */
	protected $option_page_id;

	/**
	 * Theme Support setting fetcher.
	 *
	 * @var Theme_Support_Setting
	 */
	protected $theme_settings;

	/**
	 * Settings page.
	 *
	 * @param Plugin $plugin Plugin instance.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		// Setup the get_theme_support() fetcher for our config.
		$this->theme_settings = new Theme_Support_Setting( self::THEME_SUPPORTS_VITALS_KEY );

		$this->web_vitals_settings = array(
			self::SETTINGS_VITALS_FIELD_GTAG_ID => array(
				'title' => __( 'Google Analytics ID', 'site-performance-tracker' ),
				'callback' => array( $this, 'render_web_vitals_field_gtag_id' ),
				'setting' => new Setting( 'sanitize_text_field' ),
			),
			self::SETTINGS_VITALS_FIELD_DIMENSION_MEASUREMENT_VERSION => array(
				'title' => __( 'Measurement Version Dimension', 'site-performance-tracker' ),
				'callback' => array( $this, 'render_web_vitals_field_dimension_measurement_version' ),
				'setting' => new Setting( 'sanitize_text_field' ),
			),
			self::SETTINGS_VITALS_FIELD_DIMENSION_EVENT_META => array(
				'title' => __( 'Event Meta Dimension', 'site-performance-tracker' ),
				'callback' => array( $this, 'render_web_vitals_field_dimension_event_meta' ),
				'setting' => new Setting( 'sanitize_text_field' ),
			),
			self::SETTINGS_VITALS_FIELD_DIMENSION_EVENT_DEBUG => array(
				'title' => __( 'Event Debug Dimension', 'site-performance-tracker' ),
				'callback' => array( $this, 'render_web_vitals_field_dimension_event_debug' ),
				'setting' => new Setting( 'sanitize_text_field' ),
			),
			self::SETTINGS_VITALS_FIELD_TRACKING_RATIO => array(
				'title' => __( 'Web Vitals Tracking Ratio', 'site-performance-tracker' ),
				'callback' => array( $this, 'render_web_vitals_field_tracking_ratio' ),
				'setting' => new Setting( array( $this, 'sanitize_web_vitals_field_tracking_ratio' ) ),
			),
		);
	}

	/**
	 * Register the hooks and fetch the data.
	 *
	 * @return void
	 */
	public function init() {
		$this->fetch_settings();

		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Populate the settings from the database.
	 *
	 * @return void
	 */
	protected function fetch_settings() {
		$options = (array) get_option( self::SETTINGS_VITALS_SECTION_ID, array() );

		foreach ( $this->web_vitals_settings as $key => $setting ) {
			// Set if the option has anything stored for it.
			if ( array_key_exists( $key, $options ) ) {
				$setting['setting']->set( $options[ $key ] );
			}
		}
	}

	/**
	 * Add tracker as a settings menu item.
	 */
	public function register_settings_page() {
		$this->option_page_id = add_options_page(
			__( 'Site Performance Tracker', 'site-performance-tracker' ),
			// Page head title.
			__( 'Site Performance Tracker', 'site-performance-tracker' ),
			// Menu title.
			'manage_options',
			// Capability.
			self::SETTINGS_PAGE_ID,
			// Menu slug.
			array( $this, 'render_settings_page' )
			// Callback.
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

	/**
	 * Display validation errors on the settings page.
	 *
	 * @return void
	 */
	public function admin_notices() {
		$messages = array();
		$current_screen = get_current_screen();

		if ( ! isset( $this->option_page_id ) || ! isset( $current_screen->id ) || $current_screen->id !== $this->option_page_id ) {
			return;
		}

		$gtag_id = $this->get_web_vitals_gtag_id();

		if ( empty( $gtag_id ) ) {
			$messages[] = __( 'Google Analytics ID must be specified for the Web Vitals tracking to work.', 'site-performance-tracker' );
		}

		if ( ! empty( $messages ) ) {
			?>
			<div class="notice notice-error">
				<p><?php echo esc_html( implode( ' ', $messages ) ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Callback for the WordPress settings API register_settings()
	 * definition that sanitizes and persists the data.
	 *
	 * Stores only the known settings.
	 *
	 * @param array $new_settings An array of settings.
	 *
	 * @return array
	 */
	public function save_settings( $new_settings ) {
		$settings = array();

		foreach ( $this->web_vitals_settings as $field_id => $field_settings ) {
			$setting = $field_settings['setting'];

			// Set the updated value, if received.
			if ( array_key_exists( $field_id, $new_settings ) ) {
				$setting->set( $new_settings[ $field_id ] );
			}

			$settings[ $field_id ] = $setting->get();
		}

		return $settings;
	}

	/**
	 * Generate the admin settings input field name
	 * for the Web Vitals settings section.
	 *
	 * @param string $key Field ID.
	 *
	 * @return string
	 */
	protected function get_web_vitals_setting_field_name( $key ) {
		return sprintf(
			'%s[%s]',
			self::SETTINGS_VITALS_SECTION_ID,
			$key
		);
	}

	/**
	 * Get setting instance by key.
	 *
	 * @param string $key Setting key.
	 *
	 * @return Setting
	 */
	protected function get_web_vitals_setting( $key ) {
		return $this->web_vitals_settings[ $key ]['setting'];
	}

	/**
	 * Get the Web Vitals setting from site options or the theme
	 * support override.
	 *
	 * @param string $key Setting key.
	 *
	 * @return mixed
	 */
	protected function get_web_vitals_setting_with_theme_support_override( $key ) {
		$setting = $this->get_web_vitals_setting( $key );
		$theme_support_override_value = $this->theme_settings->get( $key );

		if ( $theme_support_override_value ) {
			$setting->set( $theme_support_override_value );
		}

		return $setting->get();
	}

	/**
	 * Get the Google Analytics ID set via the add_theme_support().
	 *
	 * @return string|null
	 */
	protected function get_theme_support_gtag_id_setting() {
		// Collect all the possible theme support param values that reference the same setting.
		$gtag_id_values = array_filter(
			array(
				$this->theme_settings->get( 'ga_id' ),
				$this->theme_settings->get( 'gtag_id' ),
				$this->theme_settings->get( 'ga4_id' ),
			)
		);

		// Get the last non-empty value.
		return array_pop( $gtag_id_values );
	}

	/**
	 * Get the Google Analytics tracking ID.
	 *
	 * @return string|null
	 */
	public function get_web_vitals_gtag_id() {
		$setting = $this->get_web_vitals_setting( self::SETTINGS_VITALS_FIELD_GTAG_ID );
		$theme_support_value = $this->get_theme_support_gtag_id_setting();

		if ( ! empty( $theme_support_value ) ) {
			$setting->set( $theme_support_value );
		}

		return $setting->get();
	}

	/**
	 * Render Analytics ID form input.
	 */
	public function render_web_vitals_field_gtag_id() {
		$value = $this->get_web_vitals_gtag_id();
		$theme_setting_value = $this->get_theme_support_gtag_id_setting();

		if ( ! empty( $theme_setting_value ) ) {
			$value = $theme_setting_value;
		}

		?>
		<input
			type="text"
			class="regular-text"
			name="<?php echo esc_attr( $this->get_web_vitals_setting_field_name( self::SETTINGS_VITALS_FIELD_GTAG_ID ) ); ?>"
			pattern="[UA|GTM|G]\-[a-zA-Z0-9\-]"
			value="<?php echo esc_attr( $value ); ?>"
			placeholder="UA-XXXXXXXX-Y"
			<?php disabled( ! empty( $theme_setting_value ) ); ?>
		/>
		<p class="description">
			<?php esc_html_e( 'Specify the Google Universal Analytics (UA-XXXXXXX-X) or Google Analytics 4 (G-XXXXXXX) tracking ID.' ); ?>
		</p>
		<?php
	}

	/**
	 * Get the Web Vitals measurement version dimension.
	 *
	 * @return string|null
	 */
	public function get_web_vitals_dimension_measurement_version() {
		return $this->get_web_vitals_setting_with_theme_support_override( self::SETTINGS_VITALS_FIELD_DIMENSION_MEASUREMENT_VERSION );
	}

	/**
	 * Render the Web Vitals measurement version dimension setting.
	 *
	 * @return void
	 */
	public function render_web_vitals_field_dimension_measurement_version() {
		$value = $this->get_web_vitals_dimension_measurement_version();
		$theme_setting_value = $this->theme_settings->get( self::SETTINGS_VITALS_FIELD_DIMENSION_MEASUREMENT_VERSION );

		if ( ! empty( $theme_setting_value ) ) {
			$value = $theme_setting_value;
		}

		?>
		<input
			type="text"
			class="regular-text"
			name="<?php echo esc_attr( $this->get_web_vitals_setting_field_name( self::SETTINGS_VITALS_FIELD_DIMENSION_MEASUREMENT_VERSION ) ); ?>"
			pattern="dimension[0-9]{1,}"
			value="<?php echo esc_attr( $value ); ?>"
			placeholder="dimension1"
			<?php disabled( ! empty( $theme_setting_value ) ); ?>
		/>
		<p class="description">
			<?php esc_html_e( 'Analytics dimension for passing the measurement version. Defaults to dimension1.' ); ?>
		</p>
		<?php
	}

	/**
	 * Get the Web Vitals event meta dimension.
	 *
	 * @return string|null
	 */
	public function get_web_vitals_dimension_event_meta() {
		return $this->get_web_vitals_setting_with_theme_support_override( self::SETTINGS_VITALS_FIELD_DIMENSION_EVENT_META );
	}

	/**
	 * Render the Web Vitals event menta dimension setting field.
	 *
	 * @return void
	 */
	public function render_web_vitals_field_dimension_event_meta() {
		$value = $this->get_web_vitals_dimension_event_meta();
		$theme_setting_value = $this->theme_settings->get( self::SETTINGS_VITALS_FIELD_DIMENSION_EVENT_META );

		if ( ! empty( $theme_setting_value ) ) {
			$value = $theme_setting_value;
		}

		?>
		<input
			type="text"
			class="regular-text"
			name="<?php echo esc_attr( $this->get_web_vitals_setting_field_name( self::SETTINGS_VITALS_FIELD_DIMENSION_EVENT_META ) ); ?>"
			pattern="dimension[0-9]{1,}"
			value="<?php echo esc_attr( $value ); ?>"
			placeholder="dimension2"
			<?php disabled( ! empty( $theme_setting_value ) ); ?>
		/>
		<p class="description">
			<?php esc_html_e( 'Specify the custom dimension used for sending the event meta data. Defaults to dimension2.' ); ?>
		</p>
		<?php
	}

	/**
	 * Get the Web Vitals debug dimension.
	 *
	 * @return string|null
	 */
	public function get_web_vitals_dimension_event_debug() {
		return $this->get_web_vitals_setting_with_theme_support_override( self::SETTINGS_VITALS_FIELD_DIMENSION_EVENT_DEBUG );
	}

	/**
	 * Render the debug event dimension setting field.
	 *
	 * @return void
	 */
	public function render_web_vitals_field_dimension_event_debug() {
		$value = $this->get_web_vitals_dimension_event_debug();
		$theme_setting_value = $this->theme_settings->get( self::SETTINGS_VITALS_FIELD_DIMENSION_EVENT_DEBUG );

		if ( ! empty( $theme_setting_value ) ) {
			$value = $theme_setting_value;
		}

		?>
		<input
			type="text"
			class="regular-text"
			name="<?php echo esc_attr( $this->get_web_vitals_setting_field_name( self::SETTINGS_VITALS_FIELD_DIMENSION_EVENT_DEBUG ) ); ?>"
			pattern="dimension[0-9]{1,}"
			value="<?php echo esc_attr( $value ); ?>"
			placeholder="dimension3"
			<?php disabled( ! empty( $theme_setting_value ) ); ?>
		/>
		<p class="description">
			<?php esc_html_e( 'Analytics dimension for sending debug data. Defaults to dimension3.' ); ?>
		</p>
		<?php
	}

	/**
	 * Get the tracking ratio.
	 *
	 * @return float
	 */
	public function get_web_vitals_tracking_ratio() {
		$setting = $this->get_web_vitals_setting( self::SETTINGS_VITALS_FIELD_TRACKING_RATIO );

		if ( has_filter( 'site_performance_tracker_chance' ) ) {
			$setting->set(
				apply_filters( 'site_performance_tracker_chance', self::TRACKING_DEFAULT_CHANCE )
			);
		}

		return $setting->get();
	}

	/**
	 * Ensure the tracking ratio can be stored as empty to
	 * use the default value.
	 *
	 * @param mixed $value Value to be saved.
	 *
	 * @return float|null
	 */
	public function sanitize_web_vitals_field_tracking_ratio( $value ) {
		$value = trim( $value );

		if ( is_numeric( $value ) ) {
			return floatval( max( min( $value, 1 ), 0 ) );
		}

		return null;
	}

	/**
	 * Web vitals tracking ratio settings field.
	 *
	 * @see add_settings_field()
	 *
	 * @return void
	 */
	public function render_web_vitals_field_tracking_ratio() {
		$value = $this->get_web_vitals_tracking_ratio();

		?>
		<input
			type="number"
			class="small-text"
			min="0"
			max="1"
			step="0.01"
			placeholder="1"
			name="<?php echo esc_attr( $this->get_web_vitals_setting_field_name( self::SETTINGS_VITALS_FIELD_TRACKING_RATIO ) ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			<?php disabled( has_filter( 'site_performance_tracker_chance' ) ); ?>
		/>
		<p class="description">
			<?php esc_html_e( 'Specify the ratio of requests to enable the tracking. For example, set to 0.5 to enable tracking on 50% of the requests. Defaults to 1 or 100%.' ); ?>
		</p>
		<?php
	}

	/**
	 * Web vitals section header with useful information and links.
	 */
	public function web_vitals_section_header() {
		?>
		<p>
			<a class="button" href="https://web-vitals-report.web.app/" target="_blank"><?php esc_html_e( 'View Web Vitals Report', 'site-performance-tracker' ); ?></a>
			<?php _e( 'Ensure that the date range starts from when the Web Vitals data is being sent.', 'site-performance-tracker' ); ?>
		</p>
		<?php
	}

	/**
	 * Output the settings page.
	 *
	 * @return void
	 */
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

