Site Performance Tracker
=============

This WordPress plugin allows you to detect and track site performance metrics.
It is using a browser's [`PerformanceObserver`](https://developer.mozilla.org/en-US/docs/Web/API/PerformanceObserver)
API for measuring the metrics.

The plugin integrates with the Google Analytics and sends `timing` events on a few predefined events.
It is important though to identify the most significant elements on your page like a [hero element](https://developers.google.com/web/fundamentals/performance/user-centric-performance-metrics#first_meaningful_paint_and_hero_element_timing)
and add a custom performance marks in those locations in order to get the most meaningful metrics.

An inspiration for this plugin was a Google Developers guide around
[user-centric performance metrics](https://developers.google.com/web/fundamentals/performance/user-centric-performance-metrics).

---

**License:** [GPLv2](LICENSE)

**Contributors:** [delawski](https://github.com/delawski), [xwp](https://github.com/xwp)



## Installation

Copy the plugin files to the `wp-content/plugins` folder inside your WordPress directory.

## Usage

By default the plugin will report the following metrics:

* [`PerformancePaintTiming`](https://developer.mozilla.org/en-US/docs/Web/API/PerformancePaintTiming) events:
  * `first-paint` (FP)
  * `first-contentful-paint` [(FCP)](https://developers.google.com/web/tools/lighthouse/audits/first-contentful-paint)
* [`NavigationTiming`](https://developer.mozilla.org/en-US/docs/Web/API/PerformanceNavigationTiming) events:
  * `domContentLoadedEventEnd`
  * `domInteractive`
  * `domComplete`
* [`PerformanceMark`](https://developer.mozilla.org/en-US/docs/Web/API/PerformanceMark) injected in the WordPress hooks:
  * `mark_after_wp_head`
  * `mark_before_wp_footer`
  * `mark_after_wp_footer`

### Adding Custom Marks

You can add a mark in important locations on your page using the `the_site_performance_mark()` function like this:

```php
if ( function_exists( 'the_site_performance_mark' ) ) {
	the_site_performance_mark( 'after_hero' );
}
```

This will add a new performance mark called `mark_after_hero`.

### Hooks

There are the following hooks available to further customize the way the plugin works:

##### Disable the plugin

Programmatically disable the plugin.

```php
apply_filters( 'site_performance_tracker_disabled', boolean $is_disabled = false );
```

##### Disable default hooks

This will prevent adding custom performance marks for `wp_head` and `wp_footer` hooks.

```php
apply_filters( 'site_performance_tracker_disable_default_hooks', boolean $disable_default_hooks = false );
```

##### Category name

Change the category name used when sending events to Google Analytics.

```php
apply_filters( 'site_performance_tracker_category_name', string $category_name = 'Performance Metrics' );
```

##### Entry types

Filter the [entry types](https://developer.mozilla.org/en-US/docs/Web/API/PerformanceEntry/entryType) that are being measured byy PerformanceObserver.

```php
apply_filters( 'site_performance_tracker_event_types', array $entry_types = [ 'paint', 'navigation', 'mark' ] );
```

## Changelog

#### 0.1.0 - February 15, 2019

* Initial release.
