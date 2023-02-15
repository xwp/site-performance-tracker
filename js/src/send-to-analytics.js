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

/* global gtag */

const vitalThresholds = {
	CLS:  [ 0.1, 0.25 ],
	FCP:  [ 1800, 3000 ],
	FID:  [ 100, 300 ],
	LCP:  [ 2500, 4000 ],
	INP:  [ 200, 500 ],
	TTFB: [ 501, 1500 ],
};

const uaDimMeasurementVersion = window.webVitalsAnalyticsData[ 0 ].measurementVersion
	? window.webVitalsAnalyticsData[ 0 ].measurementVersion
	: 'dimension1';
const uaDimEventMeta = window.webVitalsAnalyticsData[ 0 ].eventMeta
	? window.webVitalsAnalyticsData[ 0 ].eventMeta
	: 'dimension2';
const uaDimEventDebug = window.webVitalsAnalyticsData[ 0 ].eventDebug
	? window.webVitalsAnalyticsData[ 0 ].eventDebug
	: 'dimension3';

const measurementVersion = '6';

let gtagConfigured = false;

function getDeliveryFunction( type ) {
	// eslint-disable-next-line no-console
	return window[ type ] || console.log;
}

function configureGtag( id ) {
	if ( 'gtag' in window ) {
		gtag( 'config', id, {
			send_page_view: false,
			transport_type: 'beacon',
			measurement_version: measurementVersion,
			custom_map: {
				[ uaDimMeasurementVersion ]: 'measurement_version',
				[ uaDimEventMeta ]: 'event_meta',
				[ uaDimEventDebug ]: 'event_debug',
			},
		} );
	}
}

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
					const largestSource = largestShift.sources.reduce(
						( a, b ) => {
							return a.node &&
								a.previousRect.width * a.previousRect.height >
									b.previousRect.width * b.previousRect.height
								? a
								: b;
						}
					);
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

export function sendToAnalytics( { name, value, delta, id, entries } ) {
	const analyticsData = window.webVitalsAnalyticsData[ 0 ];

	if ( analyticsData && analyticsData.gtag_id ) {
		if ( ! gtagConfigured ) {
			configureGtag( analyticsData.gtag_id );
			gtagConfigured = true;
		}

		getDeliveryFunction( 'gtag' )( 'event', name, {
			event_category: 'Web Vitals',
			event_label: id,
			value: Math.round( name === 'CLS' ? delta * 1000 : delta ),
			non_interaction: true,
			event_meta: getRating( value, vitalThresholds[ name ] ),
			event_debug: getDebugInfo( name, entries ),
		} );
	}
	if ( analyticsData && analyticsData.ga_id ) {
		getDeliveryFunction( 'ga' )( 'create', analyticsData.ga_id, 'auto' );
		getDeliveryFunction( 'ga' )( 'send', 'event', {
			eventCategory: 'Web Vitals',
			eventAction: name,
			eventLabel: id,
			eventValue: Math.round( name === 'CLS' ? delta * 1000 : delta ),
			nonInteraction: true,
			transport: 'beacon',
			[ uaDimEventMeta ]: getRating( value, vitalThresholds[ name ] ),
			[ uaDimEventDebug ]: getDebugInfo( name, entries ),
			[ uaDimMeasurementVersion ]: measurementVersion,
		} );
	}
	if ( analyticsData && analyticsData.ga4_id ) {
		getDeliveryFunction( 'gtag' )( 'event', name, {
			value: delta,
			metric_id: id,
			metric_value: Math.round( name === 'CLS' ? delta * 1000 : delta ),
			event_meta: getRating( value, vitalThresholds[ name ] ),
			event_debug: getDebugInfo( name, entries ),
			measurement_version: measurementVersion,
		} );
	}
}
