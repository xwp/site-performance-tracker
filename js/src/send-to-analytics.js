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

export function sendToAnalytics( { name, value, delta, id, attribution, rating } ) {
	const analyticsData = window.webVitalsAnalyticsData?.[ 0 ] ?? null;
	const eventParams = {
		value: delta,
		metric_id: id,
		metric_value: value,
		metric_delta: Math.round( name === 'CLS' ? delta * 1000 : delta ),
		metric_rating: rating,
		non_interaction: true,
	};

	switch ( name ) {
		case 'CLS':
			eventParams.debug_target = attribution?.largestShiftTarget || '(not set)';
			break;
		case 'INP':
			const { processingDuration = 0, presentationDelay = 0, interactionTarget = '(not set)', interactionType = '(not set)', inputDelay = 0 } = attribution || {};
			const loaf = attribution?.longAnimationFrameEntries?.at( -1 );
			const script = loaf?.scripts?.sort( ( a, b ) => b.duration - a.duration )[ 0 ];
			const delays = {
				inputDelay,
				processingDuration,
				presentationDelay,
			};

			eventParams.processingDuration = Math.round( processingDuration );
			eventParams.presentationDelay = Math.round( presentationDelay );
			eventParams.debug_target = interactionTarget;
			eventParams.interactionType = interactionType;
			// Return the name of the biggest contributor to INP.
			const maxDelay = Object.keys( delays ).reduce( ( a, b ) => ( delays[ a ] > delays[ b ] ? a : b ) );
			// Return the name of the biggest contributor to INP if it's significant.
			if ( delays[ maxDelay ] > 50 ) {
				eventParams.maxDelay = maxDelay;
			}

			if ( script ) {
				const { invokerType = '(not set)', invoker = '(not set)', sourceURL = '(not set)', sourceCharPosition = 0, sourceFunctionName = '(not set)' } = script;
				const { startTime = 0, duration = 0, styleAndLayoutStart = 0 } = loaf || {};
				const endTime = startTime + duration;
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
			eventParams.debug_target = attribution?.element || '(not set)';
			break;
		default:
			return '(not set)';
	}

	if ( analyticsData && analyticsData.ga4_id ) {
		if ( typeof window.gtag !== 'function' && window.dataLayer && typeof window.dataLayer.push === 'function' ) {
			window.gtag = function() {
				window.dataLayer.push( arguments );
			};
			// We need gtag to be initialized before sending events.
			window.gtag( 'config', analyticsData.ga4_id, {
				send_page_view: false,
			} );
		}

		if ( typeof window.gtag === 'function' ) {
			window.gtag( 'event', name, eventParams );
		} else {
			// eslint-disable-next-line no-console
			console.log( 'Event:', name, eventParams );
		}
	}
}
