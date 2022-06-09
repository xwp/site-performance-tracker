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
	 * Plugin Settings page ID.
	 *
	 * @var string
	 */
	const PAGE_ID = 'pluginPage';

	/**
	 * Settings option name.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'spt_settings';

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
		$tracker_config = isset( get_theme_support( 'site_performance_tracker_vitals' )[0] ) ? get_theme_support( 'site_performance_tracker_vitals' )[0] : array();
	}

	/**
	 * Add tracker as a settings menu item.
	 */
	public function add_admin_menu() {
		add_options_page(
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
			'spt_pluginPage_section',
			null,
			array( $this, 'settings_section_callback' ),
			self::PAGE_ID
		);

		add_settings_field(
			'analytics_types',
			__( 'Analytics Types', 'site-performance-tracker' ),
			array( $this, 'analytics_types_render' ),
			self::PAGE_ID,
			'spt_pluginPage_section'
		);

		add_settings_field(
			'analytics_id',
			__( 'Analytics ID', 'site-performance-tracker' ),
			array( $this, 'analytics_id_render' ),
			self::PAGE_ID,
			'spt_pluginPage_section'
		);

		add_settings_field(
			'measurement_version_dimension',
			__( 'Measurement Version Dimension', 'site-performance-tracker' ),
			array( $this, 'measurement_version_dimension_render' ),
			self::PAGE_ID,
			'spt_pluginPage_section'
		);

		add_settings_field(
			'event_meta_dimension',
			__( 'Event Meta Dimension', 'site-performance-tracker' ),
			array( $this, 'event_meta_dimension_render' ),
			self::PAGE_ID,
			'spt_pluginPage_section'
		);

		add_settings_field(
			'event_debug_dimension',
			__( 'Event Debug Dimension', 'site-performance-tracker' ),
			array( $this, 'event_debug_dimension_render' ),
			self::PAGE_ID,
			'spt_pluginPage_section'
		);

		add_settings_field(
			'web_vitals_tracking_ratio',
			__( 'Web Vitals Tracking Ratio', 'site-performance-tracker' ),
			array( $this, 'web_vitals_tracking_ratio_render' ),
			self::PAGE_ID,
			'spt_pluginPage_section'
		);
	}

	/**
	 * Echo section callback text.
	 */
	public function settings_section_callback() {
		echo esc_html( __( 'Update Site Performance Tracker settings', 'site-performance-tracker' ) );
	}

	/**
	 * Render Analytics Types form dropdown.
	 */
	public function analytics_types_render() {
		$options = $this->get_settings();
		global $tracker_config;
		$set = false;
		if ( isset( $tracker_config['ga_id'] ) ) {
			$options['analytics_types'] = 'ga_id';
			$set                        = true;
		} elseif ( isset( $tracker_config['gtag_id'] ) ) {
			$options['analytics_types'] = 'gtm';
			$set                        = true;
		} elseif ( isset( $tracker_config['ga4_id'] ) ) {
			$options['analytics_types'] = 'ga4';
			$set                        = true;
		}
		?>
		<select name='spt_settings[analytics_types]' <?php echo ( $set ) ? esc_attr( 'disabled' ) : ''; ?> required>
			<option value="ga_id" <?php selected( $options['analytics_types'], 'ga_id' ); ?>>
				<?php esc_html_e( 'Google Analytics', 'site-performance-tracker' ); ?>
			</option>
			<option value="gtm" <?php selected( $options['analytics_types'], 'gtm' ); ?>>
				<?php esc_html_e( 'Global Site Tag', 'site-performance-tracker' ); ?>
			</option>
			<option value="ga4" <?php selected( $options['analytics_types'], 'ga4' ); ?>>
				<?php esc_html_e( 'GA4 Analytics', 'site-performance-tracker' ); ?>
			</option>
		</select>
		<?php
		if ( $set ) {
			?>
			<br/><small><?php esc_html_e( 'Configured via theme files', 'site-performance-tracker' ); ?></small>
			<?php
		}
	}

	/**
	 * Render Analytics ID form input.
	 */
	public function analytics_id_render() {
		$options = $this->get_settings();
		global $tracker_config;
		$set  = false;
		$prop = 'gtag_id';

		if ( isset( $options['ga_id'] ) ) {
			$options['gtag_id'] = $options['ga_id'];
		}

		if ( isset( $tracker_config['ga_id'] ) ) {
			$options['gtag_id'] = $tracker_config['ga_id'];
			$prop               = 'ga_id';
			$set                = true;
		} elseif ( isset( $tracker_config['gtag_id'] ) ) {
			$options['gtag_id'] = $tracker_config['gtag_id'];
			$set                = true;
		} elseif ( isset( $tracker_config['ga4_id'] ) ) {
			$options['gtag_id'] = $tracker_config['ga4_id'];
			$prop               = 'ga4_id';
			$set                = true;
		}
		?>
		<input type='text' name='spt_settings[gtag_id]' pattern="[UA|GTM|G]+-[A-Z|0-9]+.*"
			   value='<?php echo esc_attr( $options['gtag_id'] ); ?>' placeholder="UA-XXXXXXXX-Y"
			   aria-label="analytics id" <?php $this->print_readonly( $prop ); ?> required>
		<?php
		if ( $set ) {
			?>
			<br/><small><?php esc_html_e( 'Configured via theme files', 'site-performance-tracker' ); ?></small>
			<?php
		}
	}

	/**
	 * Render Measurement Version Dimension form input.
	 */
	public function measurement_version_dimension_render() {
		$options = $this->get_settings();
		global $tracker_config;
		$set = false;
		if ( isset( $tracker_config['measurementVersion'] ) ) {
			$options['measurementVersion'] = $tracker_config['measurementVersion'];
			$set                           = true;
		}
		?>
		<input type='text' name='spt_settings[measurementVersion]' pattern="[dimension]+[0-9]{1,2}"
			   value='<?php echo esc_attr( $options['measurementVersion'] ); ?>' placeholder="dimension1"
			   aria-label="measurement version dimension" <?php $this->print_readonly( 'measurementVersion' ); ?>>
		<?php
		if ( $set ) {
			?>
			<br/><small><?php esc_html_e( 'Configured via theme files', 'site-performance-tracker' ); ?></small>
			<?php
		}
	}

	/**
	 * Render Event Meta Dimension form input.
	 */
	public function event_meta_dimension_render() {
		$options = $this->get_settings();
		global $tracker_config;
		$set = false;
		if ( isset( $tracker_config['eventMeta'] ) ) {
			$options['eventMeta'] = $tracker_config['eventMeta'];
			$set                  = true;
		}
		?>
		<input type='text' name='spt_settings[eventMeta]' pattern="[dimension]+[0-9]{1,2}"
			   value='<?php echo esc_attr( $options['eventMeta'] ); ?>' placeholder="dimension2"
			   aria-label="event meta dimension" <?php $this->print_readonly( 'eventMeta' ); ?>>
		<?php
		if ( $set ) {
			?>
			<br/><small><?php esc_html_e( 'Configured via theme files', 'site-performance-tracker' ); ?></small>
			<?php
		}
	}

	/**
	 * Render Event Debug Dimension form input.
	 */
	public function event_debug_dimension_render() {
		$options = $this->get_settings();
		global $tracker_config;
		$set = false;
		if ( isset( $tracker_config['eventDebug'] ) ) {
			$options['eventDebug'] = $tracker_config['eventDebug'];
			$set                   = true;
		}
		?>
		<input type='text' name='spt_settings[eventDebug]' pattern="[dimension]+[0-9]{1,2}"
			   value='<?php echo esc_attr( $options['eventDebug'] ); ?>' placeholder="dimension3"
			   aria-label="event debug dimension" <?php $this->print_readonly( 'eventDebug' ); ?>>
		<?php
		if ( $set ) {
			?>
			<br/><small><?php esc_html_e( 'Configured via theme files', 'site-performance-tracker' ); ?></small>
			<?php
		}
	}

	/**
	 * Render Tracking Ratio form input.
	 */
	public function web_vitals_tracking_ratio_render() {
		$options = $this->get_settings();
		global $tracker_config;
		$set = false;
		if ( isset( $tracker_config['web_vitals_tracking_ratio'] ) ) {
			$options['web_vitals_tracking_ratio'] = $tracker_config['web_vitals_tracking_ratio'];
			$set                                  = true;
		}
		if ( has_filter( 'site_performance_tracker_chance' ) ) {
			$options['web_vitals_tracking_ratio'] = apply_filters( 'site_performance_tracker_chance', 1 );
			$set                                  = true;
		}
		?>
		<input type='number' name='spt_settings[web_vitals_tracking_ratio]' step='0.01' min='0.01' max='1'
			   value='<?php echo esc_attr( $options['web_vitals_tracking_ratio'] ); ?>'
			   placeholder="Enter between 0 > 1" aria-label="web vitals tracking ratio"
				<?php if ( $set ) { ?>
					readonly
				<?php } ?>>
		<?php
		if ( $set ) {
			?>
			<br/><small><?php esc_html_e( 'Configured via theme files', 'site-performance-tracker' ); ?></small>
			<?php
		}
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
	private function print_readonly( $prop_name ) {
		global $tracker_config;
		if ( isset( $tracker_config[ $prop_name ] ) ) {
			echo esc_attr( 'readonly' );
		}
	}

	/**
	 * Returns the plugin settings.
	 */
	private function get_settings() {
		$options = get_option( self::OPTION_NAME, array() );

		return array_merge(
			array(
				'gtag_id'                   => '',
				'measurementVersion'        => '',
				'eventMeta'                 => '',
				'eventDebug'                => '',
				'web_vitals_tracking_ratio' => Plugin::TRACKING_DEFAULT_CHANCE,
			),
			$options
		);
	}
}
