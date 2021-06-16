<?php
/**
 * Main plugin file.
 *
 * @package Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Site_Performance_Tracker;

/**
 * Class Plugin
 */
class Plugin {

	/**
	 * PerformanceObserver default chance of sending performance metrics to analytics.
	 *
	 * @var array
	 */
	protected $default_chance = 1;

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
	 * Enqueue javascript to trigger web vitals tracking.
	 */
	public function enqueue_scripts() {
		$asset                = include plugin_dir_path( __DIR__ ) . '/js/dist/module/web-vitals-analytics.asset.php';
		$vitals_theme_support = get_theme_support( 'site_performance_tracker_vitals' );

		if ( $vitals_theme_support ) {
			// Add to footer.
			wp_enqueue_script(
				'web-vitals-analytics',
				plugin_dir_url( __DIR__ ) . 'js/dist/module/web-vitals-analytics.js',
				array(),
				$asset['version'],
				true
			);

			$chance                    = apply_filters( 'site_performance_tracker_chance', $this->default_chance );
			$web_vitals_analytics_data = array();
			if ( isset( $vitals_theme_support[0] ) ) {
				$web_vitals_analytics_data = $vitals_theme_support[0];
			}
			$web_vitals_analytics_data['chance'] = htmlspecialchars( $chance );

			wp_localize_script( 'web-vitals-analytics', 'webVitalsAnalyticsData', $web_vitals_analytics_data );

			$web_vitals_init = "( function () {
	if ( 'requestIdleCallback' in window ) {
		var randNumber = Math.random();
		if ( randNumber <= parseFloat(window.webVitalsAnalyticsData.chance) ) {
			requestIdleCallback( function() {
				webVitalsAnalyticsScript = document.querySelector( 'script[data-src*=\"web-vitals-analytics.js\"]' );
				webVitalsAnalyticsScript.src = webVitalsAnalyticsScript.dataset.src;
				delete webVitalsAnalyticsScript.dataset.src;
			} );
		}
	}
} )();";
			wp_add_inline_script( 'web-vitals-analytics', $web_vitals_init );
		}
	}

	/**
	 * Optimize script tag attributes.
	 *
	 * @param string $tag Tag mark-up.
	 * @param string $handle Script ID.
	 *
	 * @return $tag
	 */
	public function optimize_scripts( $tag, $handle ) {
		if ( 'web-vitals-analytics' !== $handle ) {
			return $tag;
		}

		// Replaces only the first occurrence of src in the tag. Avoids replacing inside inline scripts.
		if ( false !== strpos( $tag, ' src' ) ) {
			return substr_replace( $tag, ' type="module" defer data-src', strpos( $tag, ' src' ), strlen( ' src' ) );
		}

		return $tag;
	}
}

