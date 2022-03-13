<?php
/**
 * Main plugin file.
 *
 * @package XWP\Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace XWP\Site_Performance_Tracker;

/**
 * Class Plugin
 */
class Plugin {

	/**
	 * Asset handle for the JS script.
	 *
	 * @var string
	 */
	const JS_HANDLE_ANALYTICS = 'web-vitals-analytics';

	/**
	 * Plugin directory path.
	 *
	 * @var string
	 */
	protected $dir_path;

	/**
	 * Plugin directory URL.
	 *
	 * @var string
	 */
	protected $dir_url;

	/**
	 * List of all plugin settings.
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * Setup the plugin
	 *
	 * @param string $dir_path Absolute path to the plugin directory root.
	 */
	public function __construct( $dir_path ) {
		$this->dir_path = rtrim( $dir_path, '\\/' );
		$this->dir_url  = content_url( str_replace( WP_CONTENT_DIR, '', $this->dir_path ) );
		$this->settings = new Plugin_Settings( $this );
	}

	/**
	 * Initialize the plugin.
	 */
	public function init() {
		$this->settings->init();

		/**
		 * Check if the performance tracking should be disabled globally.
		 *
		 * @param boolean $is_disabled Is disabled flag.
		 */
		$is_disabled = apply_filters( 'site_performance_tracker_disabled', false );

		if ( ! $is_disabled ) {
			$this->register_hooks();
		}
	}

	/**
	 * Register hooks.
	 */
	protected function register_hooks() {
		/**
		 * Load web vitals analytics.
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/**
		 * Load only for modern browsers
		 */
		add_filter( 'script_loader_tag', array( $this, 'optimize_scripts' ), 10, 2 );
	}

	/**
	 * Get absolute path to a file relative to the plugin directory.
	 *
	 * @param string $path_relative Path relative to the plugin directory.
	 *
	 * @return string
	 */
	public function path_to( $path_relative ) {
		return sprintf(
			'%s/%s',
			$this->dir_path,
			ltrim( $path_relative, '\/' )
		);
	}

	/**
	 * Get URL of a file relative to the plugin directory.
	 *
	 * @param string $path_relative Path relative to the plugin directory.
	 *
	 * @return string
	 */
	public function uri_to( $path_relative ) {
		return sprintf(
			'%s/%s',
			$this->dir_url,
			ltrim( $path_relative, '\\/' )
		);
	}

	/**
	 * Enqueue javascript to trigger web vitals tracking.
	 */
	public function enqueue_scripts() {
		$vitals_theme_support = get_theme_support( 'site_performance_tracker_vitals' );
		$site_config          = $vitals_theme_support ? $vitals_theme_support : get_option( 'spt_settings' );
		$asset_meta_file      = $this->path_to( 'js/dist/module/web-vitals-analytics.asset.php' );

		if ( $site_config && file_exists( $asset_meta_file ) ) {
			$asset_meta = require $asset_meta_file;

			// Add to footer.
			wp_enqueue_script(
				self::JS_HANDLE_ANALYTICS,
				$this->uri_to( '/js/dist/module/web-vitals-analytics.js' ),
				array(),
				$asset_meta['version'],
				true
			);

			wp_localize_script(
				self::JS_HANDLE_ANALYTICS,
				'webVitalsAnalyticsData',
				$this->get_tracker_config()
			);

			/**
			 * Load the tracker JS file only when needed per chance setting.
			 */
			$web_vitals_init = "( function () {
	if ( 'requestIdleCallback' in window ) {
		var randNumber = Math.random();
		if ( randNumber <= parseFloat( window.webVitalsAnalyticsData.chance ) ) {
			requestIdleCallback( function() {
				webVitalsAnalyticsScript = document.querySelector( 'script[data-src*=\"web-vitals-analytics.js\"]' );
				webVitalsAnalyticsScript.src = webVitalsAnalyticsScript.dataset.src;
				delete webVitalsAnalyticsScript.dataset.src;
			} );
		}
	}
} )();";
			wp_add_inline_script( self::JS_HANDLE_ANALYTICS, $web_vitals_init );
		}//end if
	}

	/**
	 * Get tracker config to pass to JS.
	 *
	 * @return array
	 */
	public function get_tracker_config() {
		return array(
			'gtag_id' => $this->settings->get_web_vitals_gtag_id(),
			'measurementVersion' => $this->settings->get_web_vitals_dimension_measurement_version(),
			'eventMeta' => $this->settings->get_web_vitals_dimension_event_meta(),
			'eventDebug' => $this->settings->get_web_vitals_dimension_event_debug(),
			'chance' => floatval( $this->settings->get_web_vitals_tracking_ratio() ),
		);
	}

	/**
	 * Let the inline JS load our core logic when needed.
	 *
	 * @param string $tag Script tag mark-up.
	 * @param string $handle Script ID.
	 *
	 * @return $tag
	 */
	public function optimize_scripts( $tag, $handle ) {
		// Replaces only the first occurrence of src in the tag. Avoids replacing inside inline scripts.
		if ( self::JS_HANDLE_ANALYTICS === $handle && false !== strpos( $tag, ' src' ) ) {
			return substr_replace(
				$tag,
				' type="module" defer data-src',
				strpos( $tag, ' src' ),
				// Offset.
				strlen( ' src' )
				// Length.
			);
		}

		return $tag;
	}
}

