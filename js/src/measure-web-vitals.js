import { sendToAnalytics } from './send-to-analytics';
import { onCLS, onFCP, onFID, onLCP, onTTFB, onINP } from 'web-vitals';

export function measureWebVitals() {
	onCLS( sendToAnalytics );
	onFCP( sendToAnalytics );
	onFID( sendToAnalytics );
	onLCP( sendToAnalytics );
	onTTFB( sendToAnalytics );
	onINP( sendToAnalytics );
}
