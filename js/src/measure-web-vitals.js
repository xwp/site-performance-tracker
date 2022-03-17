import { sendToAnalytics } from './send-to-analytics';
import { getCLS, getFCP, getFID, getLCP, getTTFB } from 'web-vitals';

export function measureWebVitals() {
	getCLS( sendToAnalytics );
	getFCP( sendToAnalytics );
	getFID( sendToAnalytics );
	getLCP( sendToAnalytics );
	getTTFB( sendToAnalytics );
}
