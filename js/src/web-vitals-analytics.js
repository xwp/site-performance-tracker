import { measureWebVitals } from './measure-web-vitals';

( function () {
	if (
		'requestIdleCallback' in window &&
		'object' === typeof window.webVitalsAnalyticsData
	) {
		requestIdleCallback( measureWebVitals );
	}
} )();
