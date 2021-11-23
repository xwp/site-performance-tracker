<?php
/**
 * Site Performance Tracker settings page.
 *
 * @package XWP\Site_Performance_Tracker
 */

use XWP\Site_Performance_Tracker\Plugin;

/**
 * Get available trackers and print 'readonly' in the form inputs.
 *
 * @return string|null "readonly" or null.
 */
function print_readonly() {
	$web_vitals_plugin = new Plugin( __DIR__ );
	$tracker_config    = $web_vitals_plugin->get_tracker_config();
	$trackers          = isset( $tracker_config[0] ) ? array_keys( $tracker_config[0] ) : null;

	if ( isset( $trackers ) ) {
		$type = get_option( 'spt_settings' )['analytics_types'] ?? '';

		foreach ( $trackers as $tracker ) {
			return print( $tracker === $type ) ? 'readonly' : null;
		}
	}
}

add_action( 'admin_menu', 'spt_add_admin_menu' );
add_action( 'admin_init', 'spt_settings_init' );

/**
 * Add tracker as a settings menu item.
 */
function spt_add_admin_menu() {
	add_options_page( 'Site Performance Tracker', 'Site Performance Tracker', 'manage_options', 'site_performance_tracker', 'spt_options_page' );
}

/**
 * Initialize tracker settings by registering it and adding
 * sections and fields.
 */
function spt_settings_init() {
	register_setting( 'pluginPage', 'spt_settings' );

	add_settings_section(
		'spt_pluginPage_section',
		null,
		'spt_settings_section_callback',
		'pluginPage'
	);

	add_settings_field(
		'analytics_types',
		__( 'Analytics Types', 'site-performance-tracker' ),
		'analytics_types_render',
		'pluginPage',
		'spt_pluginPage_section'
	);

	add_settings_field(
		'analytics_id',
		__( 'Analytics ID', 'site-performance-tracker' ),
		'analytics_id_render',
		'pluginPage',
		'spt_pluginPage_section'
	);

	add_settings_field(
		'measurement_version_dimension',
		__( 'Measurement Version Dimension', 'site-performance-tracker' ),
		'measurement_version_dimension_render',
		'pluginPage',
		'spt_pluginPage_section'
	);

	add_settings_field(
		'event_meta_dimension',
		__( 'Event Meta Dimension', 'site-performance-tracker' ),
		'event_meta_dimension_render',
		'pluginPage',
		'spt_pluginPage_section'
	);

	add_settings_field(
		'event_debug_dimension',
		__( 'Event Debug Dimension', 'site-performance-tracker' ),
		'event_debug_dimension_render',
		'pluginPage',
		'spt_pluginPage_section'
	);

	add_settings_field(
		'web_vitals_tracking_ratio',
		__( 'Web Vitals Tracking Ratio', 'site-performance-tracker' ),
		'web_vitals_tracking_ratio_render',
		'pluginPage',
		'spt_pluginPage_section'
	);
}

/**
 * Render Analytics Types form dropdown.
 */
function analytics_types_render() {
	$options = get_option( 'spt_settings' );
	?>
	<select name='spt_settings[analytics_types]' required>
		<option value="ga_id" <?php selected( $options['analytics_types'], 1 ); ?>>Google Analytics</option>
		<option value="gtm" <?php selected( $options['analytics_types'], 2 ); ?>>Global Site Tag</option>
		<option value="ga4" <?php selected( $options['analytics_types'], 3 ); ?>>GA4 Analytics</option>
	</select>
	<?php
}

/**
 * Render Analytics ID form input.
 */
function analytics_id_render() {
	$options = get_option( 'spt_settings' );
	?>
	<input type='text' name='spt_settings[analytics_id]' pattern="[UA|GTM|G]+-[A-Z|0-9]+.*" value='<?php echo esc_attr( $options['analytics_id'] ); ?>' placeholder="UA-XXXXXXXX-Y" aria-label="analytics id" <?php print_readonly(); ?> required>
	<?php
}

/**
 * Render Measurement Version Dimension form input.
 */
function measurement_version_dimension_render() {
	$options = get_option( 'spt_settings' );
	?>
	<input type='text' name='spt_settings[measurement_version_dimension]' pattern="[dimension]+[0-9]{1,2}" value='<?php echo esc_attr( $options['measurement_version_dimension'] ); ?>' placeholder="dimension1" aria-label="measurement version dimension" <?php print_readonly(); ?> required>
	<?php
}

/**
 * Render Event Meta Dimension form input.
 */
function event_meta_dimension_render() {
	$options = get_option( 'spt_settings' );
	?>
	<input type='text' name='spt_settings[event_meta_dimension]' pattern="[dimension]+[0-9]{1,2}" value='<?php echo esc_attr( $options['event_meta_dimension'] ); ?>' placeholder="dimension2" aria-label="event meta dimension" <?php print_readonly(); ?> required>
	<?php
}

/**
 * Render Event Debug Dimension form input.
 */
function event_debug_dimension_render() {
	$options = get_option( 'spt_settings' );
	?>
	<input type='text' name='spt_settings[event_debug_dimension]' pattern="[dimension]+[0-9]{1,2}" value='<?php echo esc_attr( $options['event_debug_dimension'] ); ?>' placeholder="dimension3" aria-label="event debug dimension" <?php print_readonly(); ?> required>
	<?php
}

/**
 * Render Tracking Ratio form input.
 */
function web_vitals_tracking_ratio_render() {
	$options = get_option( 'spt_settings' );
	?>
	<input type='number' name='spt_settings[web_vitals_tracking_ratio]' step='0.01' value='0.00' min='0.01' max='1' value='<?php echo esc_attr( $options['web_vitals_tracking_ratio'] ); ?>' placeholder="Enter between 0 > 1" aria-label="web vitals tracking ratio" <?php print_readonly(); ?> required>
	<?php
}

/**
 * Echo section callback text.
 */
function spt_settings_section_callback() {
	echo esc_html( __( 'Update Site Performance Tracker settings', 'site-performance-tracker' ) );
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
		<p>You can get the <a href="https://web-vitals-report.web.app/" target="_blank">Web Vitals Report here</a>. Ensure that the date range starts from when the Web Vitals data is being sent.</p>
	</div>
	<?php
}
