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
	 * PerformanceObserver default entry types being tracked.
	 *
	 * @var array
	 */
	protected $default_entry_types = array( 'paint', 'navigation', 'mark', 'first-input' );

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

		// Add PerformanceObserver to the HEAD with the highest priority.
		add_action( 'wp_head', array( $this, 'inject_performance_observer' ), - PHP_INT_MAX );

		/**
		 * Add action to render a performance mark.
		 *
		 * @param string $mark_slug Mark slug.
		 */
		add_action( 'xwp/performance_tracker/render_mark', array( $this, 'the_performance_mark' ) );

		/**
		 * Check if performance marks should be added to default actions.
		 *
		 * @param boolean $disable_default_hooks Disable default hooks flag.
		 */
		$disable_default_hooks = apply_filters( 'site_performance_tracker_disable_default_hooks', false );
		if ( ! $disable_default_hooks ) {

			// Hook up to default actions and add performance marks.
			add_action( 'wp_head', array( $this, 'add_after_action_mark' ), PHP_INT_MAX );
			add_action( 'wp_footer', array( $this, 'add_before_action_mark' ), - PHP_INT_MAX );
			add_action( 'wp_footer', array( $this, 'add_after_action_mark' ), PHP_INT_MAX );
		}

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
	 * Inject performance observer JS code to HEAD.
	 *
	 * @return void
	 */
	public function inject_performance_observer() {

		/**
		 * Filters the category name used in Google Analytics.
		 *
		 * @param string $category_name Category name.
		 */
		$category_name = apply_filters( 'site_performance_tracker_category_name', __( 'Performance Metrics', 'site-performance-tracker' ) );

		/**
		 * Filters the list of entry types that should be tracked.
		 *
		 * @param array $entry_types List of entry types.
		 */
		$entry_types = apply_filters( 'site_performance_tracker_entry_types', $this->default_entry_types );

		/**
		 * Limits the percentage of traffic chance of sending events to analytics.
		 *
		 * @param number $entry_chance Chance - a number between 0 and 1.
		 */
		$chance = apply_filters( 'site_performance_tracker_chance', $this->default_chance );

		// Options object passed to JS.
		$options = array(
			'categoryName' => $category_name,
			'entryTypes'   => $entry_types,
			'chance'       => $chance,
		);

		?>
		<script>
			if ( window.PerformanceObserver ) {
				window.sitePerformanceObserver = <?php echo wp_json_encode( $options ); ?>;
				window.sitePerformanceObserver.send = function( name, startTime, duration ) {
					if ( 'undefined' !== typeof( window.ga ) ) {
						var trackerName = '';
						if ( 'undefined' !== typeof( window.ga.getAll ) ) {
							trackerName = window.ga.getAll()[0].get('name') + '.';
						}
						window.ga(trackerName + 'send', 'event', {
							eventCategory: window.sitePerformanceObserver.categoryName,
							eventAction: name,
							eventValue: Math.round( startTime + duration ),
							eventLabel: Math.round( startTime + duration ),
						} );
					}
				};
				window.sitePerformanceObserver.instance = new PerformanceObserver( function( list ) {
					for ( var entry of list.getEntries() ) {
						if ( 'first-input' === entry.entryType ) {
							const fid = entry.processingStart - entry.startTime;
							if ( fid > 100 ) {
								// Only track first-input delay of over 100ms:
								// https://developers.google.com/web/fundamentals/performance/rail#ux
								// > 100ms Users experience perceptible delay.
								window.sitePerformanceObserver.send( entry.entryType, entry.startTime, fid );
							}
						} else if ( 'navigation' === entry.entryType ) {
							for ( var metric of [ 'domContentLoadedEventEnd', 'domComplete', 'domInteractive' ] ) {
								window.sitePerformanceObserver.send( entry.entryType + '-' + metric, entry.startTime, entry[ metric ] );
							}
						} else {
							window.sitePerformanceObserver.send( entry.name, entry.startTime, entry.duration );
						}
					}
				} );
				var randNumber = Math.random();
				if ( randNumber <= window.sitePerformanceObserver.chance ) {
					window.sitePerformanceObserver.instance.observe( {
						entryTypes: window.sitePerformanceObserver.entryTypes
					} );
				}

			}
		</script>
		<?php
	}

	/**
	 * Add performance mark in an action.
	 *
	 * @return void
	 */
	public function add_action_mark() {
		$this->the_performance_mark( current_action() );
	}

	/**
	 * Add performance mark before an action.
	 *
	 * @return void
	 */
	public function add_before_action_mark() {
		$this->the_performance_mark( 'before_' . current_action() );
	}

	/**
	 * Add performance mark after an action.
	 *
	 * @return void
	 */
	public function add_after_action_mark() {
		$this->the_performance_mark( 'after_' . current_action() );
	}

	/**
	 * Echo the performance mark snippet.
	 *
	 * @param string $mark_slug Mark slug.
	 *
	 * @return void
	 */
	public static function the_performance_mark( $mark_slug ) {
		$mark = self::get_the_performance_mark( $mark_slug );

		if ( ! $mark ) {
			return;
		}

		echo '<script>' . $mark . '</script>' . PHP_EOL; // XSS ok.
	}

	/**
	 * Get performance mark JS code.
	 *
	 * @param string $mark_slug Mark slug.
	 *
	 * @return string
	 */
	public static function get_the_performance_mark( $mark_slug ) {
		if ( ! $mark_slug ) {
			return '';
		}

		return sprintf( 'performance && performance.mark( %s );', wp_json_encode( 'mark_' . $mark_slug ) );
	}

	/**
	 * Enqueue javascript to trigger web vitals tracking.
	 */
	public function enqueue_scripts() {
		$asset = include plugin_dir_path( __DIR__ ) . '/js/dist/module/web-vitals-analytics.asset.php';
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

			$analytics_data = $vitals_theme_support[0]['gtag_id'];
			$chance = apply_filters( 'site_performance_tracker_chance', $this->default_chance );
			$web_vitals_analytics_data = array(
				'chance' => htmlspecialchars( $chance ),
			);
			if ( isset( $vitals_theme_support[0] ) && isset( $vitals_theme_support[0]['gtag_id'] ) ) {
				$web_vitals_analytics_data['gtag_id'] = htmlspecialchars( $vitals_theme_support[0]['gtag_id'] );
			}

			wp_localize_script( 'web-vitals-analytics', 'webVitalsAnalyticsData', $web_vitals_analytics_data );
		}
	}

	/**
	 * Optimize script tag attributes.
	 *
	 * @param string $tag    Tag mark-up.
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
			return substr_replace( $tag, ' type="module" src', strpos( $tag, ' src' ), strlen( ' src' ) );
		}
		return $tag;
	}
}

