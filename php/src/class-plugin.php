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
	 * PerformanceObserver default chance of sending performance metrics to analytics.
	 *
	 * Set to 100% which sends analytics on every request.
	 *
	 * @var float|int
	 */
	const TRACKING_DEFAULT_CHANCE = 1;

	/**
	 * Delay ms to execute requestIdleCallback.
	 *
	 * Set to 5000ms by default.
	 *
	 * @var int
	 */
	const WEB_VITALS_INIT_DELAY = 5000;

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
	 * Plugin settings.
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

		$this->settings = new Settings();
	}

	/**
	 * Initialize the plugin.
	 */
	public function init() {
		/**
		 * Check if the performance tracking should be disabled globally.
		 *
		 * @param boolean $is_disabled Is disabled flag.
		 */
		$is_disabled = apply_filters( 'site_performance_tracker_disabled', false );

		if ( ! $is_disabled ) {
			$this->register_hooks();
			$this->settings->init();
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

		/**
		 * Update and validate settings before updating
		 */
		add_filter( 'pre_update_option_spt_settings', array( $this, 'pre_update_option' ), 10, 1 );
	}

	/**
	 * Get absolute path to a file relative to the plugin directory.
	 *
	 * @param string $path_relative Path relative to the plugin directory.
	 *
	 * @return string
	 */
	protected function path_to( $path_relative ) {
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
	protected function uri_to( $path_relative ) {
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
			$asset_meta                   = require $asset_meta_file;
			$web_vitals_analytics_js_path = sprintf(
				'/js/dist/module/web-vitals-analytics.%s.js',
				$asset_meta['version']
			);

			// File name contains a calculated hash = no need to use the version parameter.
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			wp_enqueue_script(
				self::JS_HANDLE_ANALYTICS,
				$this->uri_to( $web_vitals_analytics_js_path ),
				array(),
				null,
				true
			);

			wp_localize_script(
				self::JS_HANDLE_ANALYTICS,
				'webVitalsAnalyticsData',
				$this->get_tracker_config()
			);

			$web_vitals_delay = (int) apply_filters( 'site_performance_tracker_web_vitals_delay', self::WEB_VITALS_INIT_DELAY );

			/**
			 * Load the tracker JS file only when needed per chance setting.
			 */
			$web_vitals_init = "( function () {
	if ( 'requestIdleCallback' in window ) {
		var randNumber = Math.random();
		if ( randNumber <= parseFloat( window.webVitalsAnalyticsData.chance ) ) {
			window.addEventListener( 'load', function() {
				setTimeout( function() {
					requestIdleCallback( function() {
						webVitalsAnalyticsScript = document.querySelector( 'script[data-src*=\"web-vitals-analytics.\"]' );
						webVitalsAnalyticsScript.src = webVitalsAnalyticsScript.dataset.src;
						delete webVitalsAnalyticsScript.dataset.src;
					} );
				}, $web_vitals_delay );
			});
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
		$options       = get_option( 'spt_settings' ) ? get_option( 'spt_settings' ) : array();
		$site_config   = isset( get_theme_support( 'site_performance_tracker_vitals' )[0] ) ? get_theme_support( 'site_performance_tracker_vitals' )[0] : array();
		$vitals_config = array( array_merge( $options, $site_config ) );

		if ( has_filter( 'site_performance_tracker_chance' ) ) {
			$chance                  = apply_filters( 'site_performance_tracker_chance', self::TRACKING_DEFAULT_CHANCE );
			$vitals_config['chance'] = floatval( $chance );
		} else {
			$vitals_config['chance'] = isset( $options['web_vitals_tracking_ratio'] ) ? floatval( $options['web_vitals_tracking_ratio'] ) : floatval( self::TRACKING_DEFAULT_CHANCE );
		}

		return $vitals_config;
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

	/**
	 * Filter the spt_settings options before updated
	 *
	 * @param array $value The new, unserialized option value.
	 *
	 * @return array $value
	 */
	public function pre_update_option( $value ) {
		if ( isset( $value['analytics_types'] ) && 'ga_id' == $value['analytics_types'] ) {
			$value['ga_id'] = $value['gtag_id'];
			unset( $value['gtag_id'] );
		}

		if ( isset( $value['analytics_types'] ) && 'ga4' == $value['analytics_types'] ) {
			$value['ga4_id'] = $value['gtag_id'];
			unset( $value['gtag_id'] );
		}

		return $value;
	}
}
