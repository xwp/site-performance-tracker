# Site Performance Tracker

[![Build Status](https://travis-ci.com/xwp/site-performance-tracker.svg?branch=master)](https://travis-ci.com/xwp/site-performance-tracker)

This WordPress plugin sends [Core Web Vitals](https://web.dev/vitals/) data to Google Analytics. It is compatible with [Web Vitals Report](https://github.com/GoogleChromeLabs/web-vitals-report)

---

**License:** [GPLv2](LICENSE)

**Contributors:** [delawski](https://github.com/delawski), [kasparsd](https://github.com/kasparsd), [xwp](https://github.com/xwp)



## Installation

This plugin can be installed as [a Composer dependency](https://packagist.org/packages/xwp/site-performance-tracker):

```
composer require xwp/site-performance-tracker
```

It relies on [PSR-4 autoloading](https://getcomposer.org/doc/04-schema.md#psr-4) as defined in the [`composer.json` file](composer.json).


## Usage

The following hooks can be added to a theme or a custom plugin. To confirm they were applied look for the "webVitalsAnalyticsData" in the page source. Collected data will be available in [Web Vitals Report](https://web-vitals-report.web.app/) in a few days.

### Limit the number of events sent

Using the following filter you can send the events for a limited percentage of your traffic, this limits the performance metrics to be sent only for 5% of the traffic:

```php
add_filter( 'site_performance_tracker_chance', function() {
	return 0.05;
} );
```

### Hooks

There are the following hooks available to further customize the way the plugin works:

##### Disable the plugin

Programmatically disable the plugin.

```php
apply_filters( 'site_performance_tracker_disabled', boolean $is_disabled = false );
```

##### Enable web vitals tracking

To send web vitals to Google Analytics in a format compatible with the [Web Vitals Report](https://web-vitals-report.web.app/), enable the following theme support and passing in the ID, both UA- and G- ID formats are supported:

Analytics is supported, requires passing the ID using `ga_id`:
```php
add_theme_support( 'site_performance_tracker_vitals', array(
	'ga_id' => 'UA-XXXXXXXX-Y',
) );
```
Gtag is supported, requires passing the Analytics ID (not GTM-) using `gtag_id`:
```php
add_theme_support( 'site_performance_tracker_vitals', array(
	'gtag_id' => 'UA-XXXXXXXX-Y',
) );
```

Analytics v4 is supported, requires passing the ID using `ga4_id`:
```php
add_theme_support( 'site_performance_tracker_vitals', array(
	'ga4_id' => 'G-XXXXXXXXXX',
) );
```

If you need to override the Google Analytics dimensions (defaults to dimensions1 through 3) to store these under, pass them along on the add theme support initialisation:
```php
add_theme_support( 'site_performance_tracker_vitals', array(
	'gtag_id'            => 'UA-XXXXXXXX-Y',
	'measurementVersion' => 'dimension7',
	'eventMeta'          => 'dimension8',
	'eventDebug'         => 'dimension9',
) );
```

## Changelog

#### 0.9.1 - July 9, 2021

* Fix `configureGtag` call

#### 0.9 - June 16, 2021

* Update web vitals JS library to 2.0.1

#### 0.8 - May 28, 2021

* Remove Performance Observer functionality
* Code cleanup

#### 0.7 - May 26, 2021

* Add support for Google Analytics 4.

#### 0.6 - May 25, 2021

* Fix Google Analytics support.
* Code cleanup - remove unused metric and dimension.

#### 0.5 - April 13, 2021

* Feature: Add support for sending data in the web vitals report format.

#### 0.3.1 - March 11, 2020

* Feature: Add support to Analytics added through Google Tag Managere.

#### 0.3.0 - March 11, 2020

* Feature: Track 'first-delay' of over 100ms.

#### 0.2.0 - February 22, 2019

* Make autoload.php optional to support project-wide autoload.
* Add an action `xwp/performance_tracker/render_mark` as an alternative way for adding
performance marks in the front-end.
* Bugfix: Use proper JS escaping (as per WordPress VIP review).

#### 0.1.1 - February 18, 2019

* The plugin is no longer using a singleton pattern. Instead it is just
a regular class that is being instantiated in the main plugin file.
* Namespace has been added.
* The PHP version check has been added (>= 5.3).
* The helper functions are extracted to a separate file and they are now
using static functions inside the class.
* The `$default_entry_types` array is no longer defined as static.

#### 0.1.0 - February 15, 2019

* Initial release.
