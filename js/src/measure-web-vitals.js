import { sendToAnalytics } from './send-to-analytics';
import { getCLS, getFCP, getFID, getLCP } from 'web-vitals';

export function measureWebVitals() {
	getCLS( sendToAnalytics );
	getFCP( sendToAnalytics );
	getFID( sendToAnalytics );
	getLCP( sendToAnalytics );
}
