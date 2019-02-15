<?php
/**
 * Site Performance Tracker
 *
 * @package Site_Performance_Tracker
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 *
 * Plugin Name: Site Performance Tracker
 * Plugin URI: https://github.com/xwp/site-performance-tracker
 * Description: Allows you to detect and track site performance metrics.
 * Version: 0.1.0
 * Author: XWP.co
 * Author URI: https://xwp.co
 */

add_action( 'init', array( 'Site_Performance_Tracker', 'get_instance' ) );

/**
 * Class Site_Performance_Tracker
 */
class Site_Performance_Tracker {

	/**
	 * Plugin instance.
	 *
	 * @var Site_Performance_Tracker
	 */
	protected static $instance;

	/**
	 * PerformanceObserver default entry types being tracked.
	 *
	 * @var array
	 */
	protected static $default_entry_types = array( 'paint', 'navigation', 'mark' );

	/**
	 * Get the plugin instance.
	 *
	 * @return Site_Performance_Tracker
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Site_Performance_Tracker constructor.
	 */
	public function __construct() {

		/**
		 * Check if the performance tracking should be disabled globally.
		 *
		 * @param boolean $is_disabled Is disabled flag.
		 */
		$is_disabled = apply_filters( 'site_performance_tracker_disabled', false );
		if ( $is_disabled ) {
			return;
		}

		// Add PerformanceObserver to the HEAD with the highest priority.
		add_action( 'wp_head', array( $this, 'inject_performance_observer' ), - PHP_INT_MAX );

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
		$entry_types = apply_filters( 'site_performance_tracker_entry_types', self::$default_entry_types );

		// Options object passed to JS.
		$options = array(
			'categoryName' => $category_name,
			'entryTypes'   => $entry_types,
		);

		?>
		<script>
			if ( window.PerformanceObserver ) {
				window.sitePerformanceObserver = <?php echo wp_json_encode( $options ); ?>;
				window.sitePerformanceObserver.send = function( name, startTime, duration ) {
					window.ga && window.ga( 'send', 'timing', {
						timingCategory: window.sitePerformanceObserver.categoryName,
						timingVar: name,
						timingValue: Math.round( startTime + duration ),
					} );
				};
				window.sitePerformanceObserver.instance = new PerformanceObserver( function( list ) {
					for ( var entry of list.getEntries() ) {
						if ( 'navigation' === entry.entryType ) {
							for ( var metric of [ 'domContentLoadedEventEnd', 'domComplete', 'domInteractive' ] ) {
								window.sitePerformanceObserver.send( entry.entryType + '-' + metric, entry.startTime, entry[ metric ] );
							}
						} else {
							window.sitePerformanceObserver.send( entry.name, entry.startTime, entry.duration );
						}
					}
				} );
				window.sitePerformanceObserver.instance.observe( { entryTypes: window.sitePerformanceObserver.entryTypes } );
			}
		</script>
		<?php
	}

	/**
	 * Echo the performance mark snippet.
	 *
	 * @param string $mark_slug Mark slug.
	 */
	public function the_performance_mark( $mark_slug ) {
		echo $this->get_the_performance_mark( $mark_slug ); // XSS ok.
	}

	/**
	 * Get performance mark JS code.
	 *
	 * @param string $mark_slug Mark slug.
	 *
	 * @return string
	 */
	public function get_the_performance_mark( $mark_slug ) {
		if ( ! $mark_slug ) {
			return '';
		}

		return sprintf( "<script>performance && performance.mark( 'mark_%s' );</script>\n", esc_js( $mark_slug ) );
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
}

/**
 * Helper function that renders the site performance mark.
 *
 * @param string $mark_slug Mark slug.
 *
 * @return void
 */
function the_site_performance_mark( $mark_slug ) {
	Site_Performance_Tracker::get_instance()->the_performance_mark( $mark_slug );
}

/**
 * Helper function that returns the site performance mark code.
 *
 * @param string $mark_slug Mark slug.
 *
 * @return string
 */
function get_the_site_performance_mark( $mark_slug ) {
	return Site_Performance_Tracker::get_instance()->get_the_performance_mark( $mark_slug );
}
