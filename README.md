# Site Performance Tracker

[![Test and Build](https://github.com/xwp/site-performance-tracker/actions/workflows/ci.yml/badge.svg)](https://github.com/xwp/site-performance-tracker/actions/workflows/ci.yml)

This WordPress plugin sends [Core Web Vitals](https://web.dev/vitals/) data to Google Analytics. It is compatible with [Web Vitals Report](https://github.com/GoogleChromeLabs/web-vitals-report)

## Installation

This plugin can be installed as [a Composer dependency](https://packagist.org/packages/xwp/site-performance-tracker):

```bash
composer require xwp/site-performance-tracker
```

or by downloading a plugin ZIP file from the [releases page](https://github.com/xwp/site-performance-tracker/releases).

## Usage

The plugin must be configured by setting the `site_performance_tracker_vitals` theme feature with your Analytics IDs. Collected data will be available in [Web Vitals Report](https://web-vitals-report.web.app) in a few days.

To send Web Vitals metrics to Google Analytics in a format compatible with the [Web Vitals Report](https://web-vitals-report.web.app), enable the following theme support and passing in the ID:

[GA4 Analytics](https://support.google.com/analytics/answer/9304153) is supported, requires passing the ID using `ga4_id`:

```php
add_theme_support( 'site_performance_tracker_vitals', array(
  'ga4_id' => 'G-XXXXXXXXXX',
) );
```

The following hooks can be added to a theme or a custom plugin to configure the plugin, alternatively you can configure the plugin through the settings screen, in case of duplication, plugin will take programmatically set settings. To confirm they were applied look for the `webVitalsAnalyticsData` global variable in the page source.

### Limit the number of events sent

The following filter can be used to limit the number of tracking events to a percentage of your traffic. For example, to limit the tracking events to 5% of requests, use the following logic:

```php
add_filter( 'site_performance_tracker_chance', function() {
  return 0.05;
} );
```

### Disable the tracking

Programmatically disable the plugin.

```php
add_filter( 'site_performance_tracker_disabled', '__return_true' );
```

### Delay script loading

Programmatically delay web vitals tracking to minimise impact on interactivity. By default, an idle callback request is postponed by 5000ms, a value which can be adjusted via a filter:

```php
add_filter( 'site_performance_tracker_web_vitals_delay', function() {
  return 1000;
} );
```

## Contribute

All contributions are welcome! Please create [an issue](https://github.com/xwp/site-performance-tracker/issues) for bugs and feature requests, and use [pull requests](https://github.com/xwp/site-performance-tracker/pulls) for code contributions.

### Project Setup  

- We use [`wp-env`](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/) for local development environment. See all the `env:*` scripts in `package.json` for supported commands and helpers.

- `webpack.config.js` configures how `@wordpress/scripts` transforms JS and CSS assets during packaging.

- We use the `@wordpress/eslint-plugin/recommended-with-formatting` ruleset for JS linting since the Prettier integration is [currently unreliable in `@wordpress/scripts`](https://github.com/WordPress/gutenberg/issues/21872).

### Changelog

#### 1.3.6 - Jan 14th, 2025

- Made events non_interactive, such that they don't interfere with user events.

#### 1.3.5 - Jul 28th, 2023

- Add tracking support for Google Tag Manager setup GA4 with window.dataLayer.

#### 1.3.4 - Jul 15th, 2023

- Fix bug in INP reporting when some attributions were empty.

#### 1.3.3 - Jun 26th, 2023

- Deprecated GA3 support.
- Used attribution build of web vitals.

#### 1.3.2 - October 30th, 2023

- Improved admin interface for GA4.
- Bumped up build process to use Node 16.

#### 1.3.1 - July 18th, 2023

- Updated GA4 Settings.
- Added WP-Rocket exclusion to web-vitals-analytics.
- Cleaned up/refactored settings code.

#### 1.3 - February 24th, 2023

- Added INP metric.
- Support for smaller web vitals ratio.

#### 1.2 - December 5, 2022

- Clean up settings code.

#### 1.1.7 - July 5, 2022

- Fix GA delivery bug.

#### 1.1.6 - May 18, 2022

- Fix GA not tracking bug when using the UI.

#### 1.1.5 - March 17, 2022

- Adding TTFB.

#### 1.1.4 - March 10, 2022

- Improve performance by loading and executing the script in quiter periods.
- Make chunk hash a part of the output filename.

#### 1.1.3 - March 9, 2022

- Fix UI to prefill configured data.

#### 1.1.2 - March 4, 2022

- Fix duplicated page view when using gtag.

#### 1.1.1 - March 3, 2022

- Fix duplicitous page views when using gtag.
- Fix PHP notices.

#### 1.1.0 - January 6, 2022

- Introduces an UI in WordPress Admin for easier configuration. If any config parameters are set in the theme files, the UI will not allow changing those parameters.

#### 1.0.0 - October 4, 2021

- Update docs to start the "Usage" section with the required configuration for the plugin to do anything.
- Switch to basic PHP includes for loading the PHP files instead of Composer autoload. 
- Introduce helper methods for working with asset paths and URLs.
- Introduce VIP Go coding standards.
- Introduce PHP unit testing.

#### 0.9.1 - July 9, 2021

- Fix `configureGtag` call

#### 0.9 - June 16, 2021

- Update web vitals JS library to 2.0.1

#### 0.8 - May 28, 2021

- Remove Performance Observer functionality
- Code cleanup

#### 0.7 - May 26, 2021

- Add support for Google Analytics 4.

#### 0.6 - May 25, 2021

- Fix Google Analytics support.
- Code cleanup - remove unused metric and dimension.

#### 0.5 - April 13, 2021

- Feature: Add support for sending data in the web vitals report format.

#### 0.3.1 - March 11, 2020

- Feature: Add support to Analytics added through Google Tag Managere.

#### 0.3.0 - March 11, 2020

- Feature: Track 'first-delay' of over 100ms.

#### 0.2.0 - February 22, 2019

- Make autoload.php optional to support project-wide autoload.
- Add an action `xwp/performance_tracker/render_mark` as an alternative way for adding
performance marks in the front-end.
- Bugfix: Use proper JS escaping (as per WordPress VIP review).

#### 0.1.1 - February 18, 2019

- The plugin is no longer using a singleton pattern. Instead it is just
a regular class that is being instantiated in the main plugin file.
- Namespace has been added.
- The PHP version check has been added (>= 5.3).
- The helper functions are extracted to a separate file and they are now
using static functions inside the class.
- The `$default_entry_types` array is no longer defined as static.

#### 0.1.0 - February 15, 2019

- Initial release.

## Contribute

Please follow the [contribution guide](CONTRIBUTE.md).

## Credits

Created by [XWP](https://xwp.co) and [contributors](https://github.com/xwp/site-performance-tracker/graphs/contributors). Licensed under [GNU General Public License v2.0 or later](LICENSE).
