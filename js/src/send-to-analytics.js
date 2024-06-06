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

export function sendToAnalytics( { name, value, delta, id, attribution, rating } ) {
	const analyticsData = window.webVitalsAnalyticsData[ 0 ];
	const eventParams = {
		value: delta,
		metric_id: id,
		metric_value: value,
		metric_delta: Math.round( name === 'CLS' ? delta * 1000 : delta ),
		metric_rating: rating,
	};

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
		case 'INP':
			const { processingDuration, presentationDelay, interactionTarget, interactionType } = attribution;
			const loaf = attribution.longAnimationFrameEntries.at( -1 );
			const script = loaf?.scripts?.sort( ( a, b ) => b.duration - a.duration )[0];

			eventParams.processingDuration = processingDuration;
			eventParams.presentationDelay = presentationDelay;
			eventParams.debug_target = interactionTarget;
			eventParams.interactionType = interactionType;

			if ( script ) {
				const { invokerType, invoker, sourceURL, sourceCharPosition, sourceFunctionName } = script;
				const { startTime, duration, styleAndLayoutStart } = loaf;
				const endTime = startTime	+ duration;
				const styleLayoutDuration = endTime - styleAndLayoutStart;

				eventParams.invokerType = invokerType;
				eventParams.invoker = invoker;
				eventParams.sourceURL = sourceURL;
				eventParams.sourceCharPosition = sourceCharPosition;
				eventParams.sourceFunctionName = sourceFunctionName;
				eventParams.styleLayoutDuration = styleLayoutDuration;
			}
			break;
		case 'LCP':
			eventParams.debug_target = attribution.element;
			break;
		default:
			return '(not set)';
	}

	if ( analyticsData && analyticsData.gtag_id ) {
		if ( ! gtagConfigured ) {
			configureGtag( analyticsData.gtag_id );
			gtagConfigured = true;
		}

		eventParams.event_category = 'Web Vitals';
		eventParams.event_label = id;
		eventParams.non_interaction = true;

		getDeliveryFunction( 'gtag' )( 'event', name, eventParams );
	}

	if ( analyticsData && analyticsData.ga4_id ) {
		getDeliveryFunction( 'gtag' )( 'event', name, eventParams );
	}
}
