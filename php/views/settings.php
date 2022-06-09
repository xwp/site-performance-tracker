<?php
/**
 * Site Performance Tracker settings page.
 *
 * @package XWP\Site_Performance_Tracker
 */

use XWP\Site_Performance_Tracker\Plugin;

/**
 * Get options set via add_theme_support.
 */
function get_hardcoded_tracker_config() {
	global $tracker_config;
	$tracker_config = isset( get_theme_support( 'site_performance_tracker_vitals' )[0] ) ? get_theme_support( 'site_performance_tracker_vitals' )[0] : array();
}
add_action( 'after_setup_theme', 'get_hardcoded_tracker_config', PHP_INT_MAX );

/**
 * Get available trackers and print 'readonly' in the form inputs if the setting is defined in theme files
 *
 * @param  string $prop_name The property name to be tested.
 */
function print_readonly( $prop_name ) {
	global $tracker_config;
	if ( isset( $tracker_config[ $prop_name ] ) ) {
		echo esc_attr( 'readonly' );
	}
}

/**
 * Create and Output the form.
 */
function spt_options_page() {
	?>
	<form action='options.php' method='post'>
		<h1><?php echo esc_html( __( 'Site Performance Tracker Settings', 'site-performance-tracker' ) ); ?></h1>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
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
 * Returns the plugin settings.
 */
function spt_get_settings() {
	$options = get_option( 'spt_settings', array() );

	return array_merge(
		array(
			'gtag_id' => '',
			'measurementVersion' => '',
			'eventMeta' => '',
			'eventDebug' => '',
			'web_vitals_tracking_ratio' => Plugin::TRACKING_DEFAULT_CHANCE,
		),
		$options
	);
}
