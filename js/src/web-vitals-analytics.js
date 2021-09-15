import { sendToAnalytics } from './send-to-analytics';
import { getCLS, getFCP, getFID, getLCP } from 'web-vitals';

function measureWebVitals() {
	getCLS( sendToAnalytics );
	getFCP( sendToAnalytics );
	getFID( sendToAnalytics );
	getLCP( sendToAnalytics );
}

( function () {
	if (
		'requestIdleCallback' in window &&
		'object' === typeof window.webVitalsAnalyticsData
	) {
		requestIdleCallback( measureWebVitals );
	}
} )();
