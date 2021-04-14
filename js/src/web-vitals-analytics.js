/*
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/* global ga, gtag, location, requestIdleCallback */

import { getCLS, getFCP, getFID, getLCP } from 'web-vitals';

const vitalThresholds = {
	CLS: [ 0.1, 0.25 ],
	FCP: [ 1800, 3000 ],
	FID: [ 100, 300 ],
	LCP: [ 2500, 4000 ],
};

const uaDimMeasurementVersion = window.webVitalsAnalyticsData.measurementVersion
	? window.webVitalsAnalyticsData.measurementVersion
	: 'dimension1';
const uaDimclientId = window.webVitalsAnalyticsData.clientId
	? window.webVitalsAnalyticsData.cliegantId
	: 'dimension2';
const uaDimSegments = window.webVitalsAnalyticsData.segments
	? window.webVitalsAnalyticsData.segments
	: 'dimension3';
const uaDimConfig = window.webVitalsAnalyticsData.config
	? window.webVitalsAnalyticsData.config
	: 'dimension4';
const uaDimEventMeta = window.webVitalsAnalyticsData.eventMeta
	? window.webVitalsAnalyticsData.eventMeta
	: 'dimension5';
const uaDimEventDebug = window.webVitalsAnalyticsData.eventDebug
	? window.webVitalsAnalyticsData.eventDebug
	: 'dimension6';

const getConfig = id => {
	const config = {
		page_path: location.pathname,
	};

	if ( 'gtag' === window.webVitalsAnalyticsData.delivery ) {
		Object.assign( config, {
			transport_type: 'beacon',
			measurement_version: '6',
		} );
	}

	if ( id.startsWith( 'UA-' ) ) {
		// Only gtag suports custom maps.
		if ( 'gtag' === window.webVitalsAnalyticsData.delivery ) {
			Object.assign( config, {
				custom_map: {
					[ uaDimMeasurementVersion ]: 'measurement_version',
					[ uaDimclientId ]: 'client_id',
					[ uaDimSegments ]: 'segments',
					[ uaDimConfig ]: 'config',
					[ uaDimEventMeta ]: 'event_meta',
					[ uaDimEventDebug ]: 'event_debug',
					metric1: 'report_size',
				},
			} );
		}

		if ( 'ga' === window.webVitalsAnalyticsData.delivery ) {
			Object.assign( config, {
				[ uaDimMeasurementVersion ]: '6',
			} );
		}
	}

	return [ 'config', id, config ];
};

function getRating( value, thresholds ) {
	if ( value > thresholds[ 1 ] ) {
		return 'poor';
	}
	if ( value > thresholds[ 0 ] ) {
		return 'ni';
	}
	return 'good';
}

function getNodePath( node ) {
	try {
		let name = node.nodeName.toLowerCase();
		if ( name === 'body' ) {
			return 'html>body';
		}
		if ( node.id ) {
			return `${ name }#${ node.id }`;
		}
		if ( node.className && node.className.length ) {
			name += `.${ [ ...node.classList.values() ].join( '.' ) }`;
		}
		return `${ getNodePath( node.parentElement ) }>${ name }`;
	} catch ( error ) {
		return '(error)';
	}
}

function getDebugInfo( metricName, entries = [] ) {
	const firstEntry = entries[ 0 ];
	const lastEntry = entries[ entries.length - 1 ];

	switch ( metricName ) {
		case 'LCP':
			if ( lastEntry ) {
				return getNodePath( lastEntry.element );
			}
			break;
		case 'FID':
			if ( firstEntry ) {
				const { name } = firstEntry;
				return `${ name }(${ getNodePath( firstEntry.target ) })`;
			}
			break;
		case 'CLS':
			if ( entries.length ) {
				const largestShift = entries.reduce( ( a, b ) => {
					return a && a.value > b.value ? a : b;
				} );
				if ( largestShift && largestShift.sources ) {
					const largestSource = largestShift.sources.reduce( ( a, b ) => {
						return a.node &&
							a.previousRect.width * a.previousRect.height >
								b.previousRect.width * b.previousRect.height
							? a
							: b;
					} );
					if ( largestSource ) {
						return getNodePath( largestSource.node );
					}
				}
			}
			break;
		default:
			return '(not set)';
	}
}

function sendToGoogleAnalytics( { name, value, delta, id, entries } ) {
	if ( 'undefined' !== typeof window.webVitalsAnalyticsData.gtag_id ) {
		gtag( 'event', name, {
			event_category: 'Web Vitals',
			event_label: id,
			value: Math.round( name === 'CLS' ? delta * 1000 : delta ),
			non_interaction: true,
			event_meta: getRating( value, vitalThresholds[ name ] ),
			metric_rating: getRating( value, vitalThresholds[ name ] ),
			event_debug: getDebugInfo( name, entries ),
		} );
	}
	if ( 'undefined' !== typeof window.webVitalsAnalyticsData.ga_id ) {
		ga( 'send', 'event', {
			eventCategory: 'Web Vitals',
			eventAction: name,
			eventLabel: id,
			eventValue: Math.round( name === 'CLS' ? delta * 1000 : delta ),
			nonInteraction: true,
			transport: 'beacon',
			[ uaDimEventMeta ]: getRating( value, vitalThresholds[ name ] ),
			[ uaDimEventDebug ]: getDebugInfo( name, entries ),
		} );
	}
}

export function measureWebVitals() {
	getCLS( sendToGoogleAnalytics );
	getFCP( sendToGoogleAnalytics );
	getFID( sendToGoogleAnalytics );
	getLCP( sendToGoogleAnalytics );
}

export function initAnalytics() {
	if ( 'undefined' === typeof window.webVitalsAnalyticsData ) {
		return false;
		// Do nothing without a config.
	}
	if ( 'undefined' !== typeof window.webVitalsAnalyticsData.gtag_id ) {
		window.webVitalsAnalyticsData.delivery = 'gtag';
	} else if ( 'undefined' !== typeof window.webVitalsAnalyticsData.ga_id ) {
		window.webVitalsAnalyticsData.delivery = 'ga';
	}

	if ( 'gtag' === window.webVitalsAnalyticsData.delivery ) {
		window.webVitalsAnalyticsData.type = 'gtag';
		if ( 'undefined' === typeof window.gtag ) {
			// eslint-disable-next-line no-console
			window.gtag = console.log;
		}
		gtag( 'js', new Date() );
		gtag( ...getConfig( window.webVitalsAnalyticsData.gtag_id ) );
	}

	if ( 'ga' === window.webVitalsAnalyticsData.delivery ) {
		if ( 'undefined' === typeof window.ga ) {
			// eslint-disable-next-line no-console
			window.ga = console.log;
		} else {
			ga( 'js', new Date() );
			ga( ...getConfig( window.webVitalsAnalyticsData.ga_id ) );
		}
	}

	measureWebVitals();
}

( function () {
	requestIdleCallback( initAnalytics );
} )();
