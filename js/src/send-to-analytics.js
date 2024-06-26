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

function getDeliveryFunction( type ) {
	// eslint-disable-next-line no-console
	return window[ type ] || console.log;
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

	switch ( name ) {
		case 'CLS':
			eventParams.debug_target = attribution.largestShiftTarget;
			break;
		case 'INP':
			const { processingDuration, presentationDelay, interactionTarget, interactionType } = attribution;
			const loaf = attribution.longAnimationFrameEntries.at( -1 );
			const script = loaf?.scripts?.sort( ( a, b ) => b.duration - a.duration )[ 0 ];

			eventParams.processingDuration = Math.round( processingDuration );
			eventParams.presentationDelay = Math.round( presentationDelay );
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
				eventParams.styleLayoutDuration = Math.round( styleLayoutDuration );
			}
			break;
		case 'LCP':
			eventParams.debug_target = attribution.element;
			break;
		default:
			return '(not set)';
	}

	if ( analyticsData && analyticsData.ga4_id ) {
		getDeliveryFunction( 'gtag' )( 'event', name, eventParams );
	}
}
