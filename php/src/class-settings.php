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
 * Main class for performance Settings
 */
final class Settings {
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
			new WebVitalsTrackingRatioField(),
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
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
	}

	/**
	 * Add tracker as a settings menu item.
	 *
	 * @return string|false The option page's hook_suffix, or false if the user does not have the capability required.
	 */
	public function add_admin_menu() {
		return add_options_page(
			__( 'Site Performance Tracker', 'site-performance-tracker' ),
			__( 'Site Performance Tracker', 'site-performance-tracker' ),
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
	}

	/**
	 * Echo section callback text.
	 */
	public function settings_section_callback() {
		echo esc_html( __( 'Update Site Performance Tracker settings', 'site-performance-tracker' ) );
	}

	/**
	 * Create and Output the settings page.
	 */
	public function render_settings_page() {
		?>
		<!-- Toggle dimension fields when value is changed in the analyticstype select field. -->
		<script>
			document.addEventListener( "DOMContentLoaded", function() {
				var analytics_types_field = document.getElementsByName( "spt_settings[analytics_types]" )[0];

				// Toggle visibility on page load.
				toggleDimensionsFields( analytics_types_field.value );

				// Handle change event.
				analytics_types_field.addEventListener( "change", function() {
					toggleDimensionsFields( this.value );
				});

				// Toggle visibility of dimension fields.
				function toggleDimensionsFields( field_value ) {
					if( 'ga4' === field_value ) {
						document.querySelectorAll( '.dimension' ).forEach( function( elem ) {
							elem.style.display = 'none';
						});
					} else {
						document.querySelectorAll( '.dimension' ).forEach( function( elem ) {
							elem.style.display = 'table-row';
						});
					}
				}
			});
		</script>

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
				<?php
					echo sprintf(
						// translators: 1 - link open tag, 2 - link close tag.
						esc_html__( 'You can get the %1$sWeb Vitals Report here%2$s. Ensure that the date range starts from when the Web Vitals data is being sent.', 'site-performance-tracker' ),
						'<a href="https://web-vitals-report.web.app/" target="_blank">',
						'</a>'
					);
				?>
			</p>
		</div>
		<?php
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
				WebVitalsTrackingRatioField::OPTION_WEB_VITALS_TRACKING_RATIO   => Plugin::TRACKING_DEFAULT_CHANCE,
			),
			$options
		);
	}

	/**
	 * Returns options set via add_theme_support.
	 */
	public function get_hardcoded_tracker_config() {
		return isset( get_theme_support( self::HARDCODED_TRACKER_CONFIG_FEATURE )[0] ) ? get_theme_support( self::HARDCODED_TRACKER_CONFIG_FEATURE )[0] : array();
	}
}
